<?php
// --- Config ---
$wsHost = '0.0.0.0';  // WebSocket server binds on all interfaces
$wsPort = 8080;       // WebSocket port for browsers
$pushHost = '127.0.0.1'; // Push port binds only locally
$pushPort = 9000;        // Control/push port for PHP to send JSON (no WS framing)

// --- Create listening sockets ---
$wsServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($wsServer, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($wsServer, $wsHost, $wsPort);
socket_listen($wsServer);

$pushServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($pushServer, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($pushServer, $pushHost, $pushPort);
socket_listen($pushServer);

echo "WS server:   ws://$wsHost:$wsPort\n";
echo "Push server: tcp://$pushHost:$pushPort\n";

$clients = []; // connected WS client sockets

// --- Helpers ---
function ws_handshake($client, $headers) {
    if (preg_match('/Sec-WebSocket-Key:\s*(.*)\r\n/i', $headers, $m)) {
        $key = trim($m[1]);
        $accept = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        $resp  = "HTTP/1.1 101 Switching Protocols\r\n";
        $resp .= "Upgrade: websocket\r\n";
        $resp .= "Connection: Upgrade\r\n";
        $resp .= "Sec-WebSocket-Accept: $accept\r\n\r\n";
        socket_write($client, $resp, strlen($resp));
    }
}

function ws_encode($msg) {
    $b1 = 0x81; // FIN + text
    $len = strlen($msg);
    if ($len <= 125) {
        return pack('CC', $b1, $len) . $msg;
    } elseif ($len <= 65535) {
        return pack('CCn', $b1, 126, $len) . $msg;
    } else {
        // 64-bit length (use 32-bit high zero, 32-bit low length)
        return pack('CCNN', $b1, 127, 0, $len) . $msg;
    }
}

function ws_decode($data) {
    if ($data === '' || $data === false) return '';
    $len = ord($data[1]) & 127;
    if ($len === 126) {
        $mask = substr($data, 4, 4);
        $payload = substr($data, 8);
    } elseif ($len === 127) {
        $mask = substr($data, 10, 4);
        $payload = substr($data, 14);
    } else {
        $mask = substr($data, 2, 4);
        $payload = substr($data, 6);
    }
    $out = '';
    $plen = strlen($payload);
    for ($i = 0; $i < $plen; $i++) {
        $out .= $payload[$i] ^ $mask[$i % 4];
    }
    return $out;
}

function broadcast($msg, array &$clients) {
    $frame = ws_encode($msg);
    foreach ($clients as $c) {
        @socket_write($c, $frame, strlen($frame));
    }
}

function drop_client($sock, array &$clients) {
    $i = array_search($sock, $clients, true);
    if ($i !== false) unset($clients[$i]);
    @socket_close($sock);
}

// --- Event loop ---
while (true) {
    // Build the read set: include BOTH listening sockets + all client sockets
    $read = array_merge([$wsServer, $pushServer], $clients);
    $write = $except = null;

    // Wait up to 1 second (prevents 100% CPU)
    $num = @socket_select($read, $write, $except, 1);
    if ($num === false) {
        // Ignore transient warnings; continue loop
        continue;
    }
    if ($num === 0) {
        // Timeout, loop again
        continue;
    }

    // 1) New WebSocket client?
    if (in_array($wsServer, $read, true)) {
        $client = @socket_accept($wsServer);
        if ($client instanceof Socket) {
            // Read HTTP headers for handshake
            $headers = @socket_read($client, 4096);
            ws_handshake($client, $headers ?: '');
            $clients[] = $client;
            echo "WS client connected (total: " . count($clients) . ")\n";
        }
        // Remove listener from read set so we don't treat it below
        $idx = array_search($wsServer, $read, true);
        unset($read[$idx]);
    }

    // 2) New push connection? (plain TCP sender, no WS framing)
    if (in_array($pushServer, $read, true)) {
        $push = @socket_accept($pushServer);
        if ($push instanceof Socket) {
            // Read whatever the pusher sent (expect JSON)
            $all = '';
            while (true) {
                $bytes = @socket_recv($push, $buf, 8192, 0);
                if ($bytes === false || $bytes === 0) break;
                $all .= $buf;
                if ($bytes < 8192) break; // likely done
            }
            @socket_close($push);

            $msg = trim($all);
            if ($msg !== '') {
                echo "PUSH: $msg\n";
                // Optionally validate JSON:
                // $j = json_decode($msg, true);
                // if (json_last_error() === JSON_ERROR_NONE) { ... }
                broadcast($msg, $clients);
            }
        }
        $idx = array_search($pushServer, $read, true);
        unset($read[$idx]);
    }

    // 3) Handle messages from existing WS clients (browser -> server)
    foreach ($read as $sock) {
        // $read now only has WS clients
        $bytes = @socket_recv($sock, $buffer, 8192, 0);
        if ($bytes === false || $bytes === 0) {
            drop_client($sock, $clients);
            echo "WS client disconnected (total: " . count($clients) . ")\n";
            continue;
        }

        // Browser frames are masked; decode then re-broadcast (encoded)
        $decoded = ws_decode($buffer);
        if ($decoded !== '') {
            echo "WS recv: $decoded\n";
            broadcast($decoded, $clients);
        }
    }
}
