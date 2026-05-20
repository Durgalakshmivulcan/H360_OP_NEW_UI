<?php
// FIX_B_2340: delete a TV ad. Scoped to filenames inside tv_ads/ — no
// path-traversal possible because we basename() and then re-resolve against
// the canonical folder.

require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/_constants.php';

header('Content-Type: application/json');
requireCan('delete', 'tv_ads.php', 'ajax');

$name = basename($_POST['file'] ?? '');
if ($name === '' || $name[0] === '.' || $name[0] === '_') {
    http_response_code(400); echo json_encode(['ok' => false, 'error' => 'invalid filename']); exit;
}
if (!preg_match('/\.(jpe?g|png|webp)$/i', $name)) {
    http_response_code(400); echo json_encode(['ok' => false, 'error' => 'not an image']); exit;
}

$dir = tvad_dir();
$path = $dir . '/' . $name;
$real = realpath($path);

// belt-and-braces: confirm the resolved path still lives inside tv_ads/
if (!$real || strpos($real, $dir . DIRECTORY_SEPARATOR) !== 0) {
    http_response_code(400); echo json_encode(['ok' => false, 'error' => 'invalid path']); exit;
}
if (!is_file($real)) {
    http_response_code(404); echo json_encode(['ok' => false, 'error' => 'not found']); exit;
}
if (!@unlink($real)) {
    http_response_code(500); echo json_encode(['ok' => false, 'error' => 'delete failed']); exit;
}
echo json_encode(['ok' => true]);
