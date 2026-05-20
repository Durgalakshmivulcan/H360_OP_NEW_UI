<?php
// FIX_B_2311: tiny JSON lister for the TV-display ad rotator. Returns the
// images currently sitting in /tv_ads/, in stable order, so the front-end
// can preload + crossfade without any admin upload UI.

header('Content-Type: application/json');
header('Cache-Control: no-cache');

$dir = realpath(__DIR__ . '/../../tv_ads');
$out = ['ads' => [], 'count' => 0];

if ($dir && is_dir($dir)) {
    $files = scandir($dir) ?: [];
    foreach ($files as $f) {
        if ($f === '' || $f[0] === '.' || $f[0] === '_') continue;       // skip dotfiles + staged "_xxx"
        // FIX_B_2341b: keep this regex in lockstep with the admin uploader —
        // GIF / animation is explicitly disallowed (distracting on a public TV).
        if (!preg_match('/\.(jpe?g|png|webp)$/i', $f)) continue;         // images only, no GIF
        $out['ads'][] = 'tv_ads/' . rawurlencode($f);
    }
    sort($out['ads']);
    $out['count'] = count($out['ads']);
}

echo json_encode($out);
