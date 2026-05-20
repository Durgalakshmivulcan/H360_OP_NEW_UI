<?php
// FIX_B_2340: TV-ad upload endpoint with strict server-side validation. The
// client-side JS runs the same checks first (for instant feedback) but this
// is the authoritative gate — defends against bypass via direct POST.

require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/_constants.php';

header('Content-Type: application/json');
requireCan('add', 'tv_ads.php', 'ajax');

function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    fail('No file uploaded.');
}
$f = $_FILES['file'];

// --- size guard ---
if ($f['size'] < TVAD_MIN_BYTES) fail('File is too small (' . round($f['size']/1024) . ' KB). Minimum ' . (TVAD_MIN_BYTES/1024) . ' KB for sharp display on a 60-inch TV.');
if ($f['size'] > TVAD_MAX_BYTES) fail('File is too large (' . round($f['size']/1024/1024, 1) . ' MB). Max ' . (TVAD_MAX_BYTES/1024/1024) . ' MB.');

// --- mime + extension guard ---
$mime = mime_content_type($f['tmp_name']) ?: '';
if (!in_array($mime, TVAD_ALLOWED_MIME, true)) {
    fail('Unsupported file type (' . htmlspecialchars($mime) . '). Allowed: JPG, PNG, WebP.');
}
$origName = basename($f['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
if (!in_array($ext, TVAD_ALLOWED_EXT, true)) {
    fail('Unsupported file extension (.' . htmlspecialchars($ext) . '). Allowed: jpg, png, webp.');
}

// --- resolution guard ---
$info = @getimagesize($f['tmp_name']);
if (!$info || empty($info[0]) || empty($info[1])) fail('Could not read image dimensions — file may be corrupt.');
$w = (int)$info[0]; $h = (int)$info[1];

if ($w < TVAD_MIN_WIDTH || $h < TVAD_MIN_HEIGHT) {
    fail("Resolution too low ({$w}×{$h}). Minimum required for a 60-inch TV is " . TVAD_MIN_WIDTH . '×' . TVAD_MIN_HEIGHT . '. For best results use 3840×1200 (4K-ready).');
}
if ($w > TVAD_MAX_WIDTH || $h > TVAD_MAX_HEIGHT) {
    fail("Resolution too high ({$w}×{$h}). Maximum is " . TVAD_MAX_WIDTH . '×' . TVAD_MAX_HEIGHT . ' (4K).');
}
$aspect = $w / max(1, $h);
if ($aspect < TVAD_ASPECT_MIN || $aspect > TVAD_ASPECT_MAX) {
    fail('Aspect ratio is ' . number_format($aspect, 2) . ':1, outside the allowed band of ' . TVAD_ASPECT_MIN . ':1 to ' . TVAD_ASPECT_MAX . ':1. Target is 16:5 (3.20:1) — that\'s the shape of the bottom strip on the TV.');
}

// --- destination filename ---
$dir = tvad_dir();
if (!$dir || !is_dir($dir) || !is_writable($dir)) fail('Server folder is not writable.', 500);

// slug: timestamp-prefix + sanitized original name (no spaces / unicode pain)
$slug = preg_replace('/[^a-z0-9._-]+/i', '-', pathinfo($origName, PATHINFO_FILENAME));
$slug = trim($slug, '-') ?: 'ad';
$slug = substr($slug, 0, 64);
$dest = sprintf('%s/%s-%s.%s', $dir, date('YmdHis'), $slug, $ext);

if (!move_uploaded_file($f['tmp_name'], $dest)) fail('Could not save file.', 500);
@chmod($dest, 0644);

echo json_encode([
    'ok'        => true,
    'file'      => basename($dest),
    'url'       => tvad_url(basename($dest)),
    'width'     => $w,
    'height'    => $h,
    'bytes'     => filesize($dest),
    'aspect'    => round($aspect, 2),
]);
