<?php
// FIX_B_2340: admin-side listing with metadata (dimensions + bytes) so the
// management page can show each ad's stats. Distinct from the public lister
// at ajax/AppointmentBooking/tv_ads_list.php which only returns URLs.

require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/_constants.php';

header('Content-Type: application/json');
requireCan('view', 'tv_ads.php', 'ajax');

$dir = tvad_dir();
$out = ['ads' => [], 'count' => 0];
if (!$dir || !is_dir($dir)) { echo json_encode($out); exit; }

foreach (scandir($dir) ?: [] as $f) {
    if (!tvad_is_visible($f)) continue;
    $full = $dir . '/' . $f;
    $info = @getimagesize($full);
    $out['ads'][] = [
        'name'     => $f,
        'url'      => tvad_url($f),
        'bytes'    => @filesize($full) ?: 0,
        'width'    => $info ? (int)$info[0] : 0,
        'height'   => $info ? (int)$info[1] : 0,
        'modified' => @filemtime($full) ?: 0,
    ];
}
usort($out['ads'], function ($a, $b) { return strcmp($a['name'], $b['name']); });
$out['count'] = count($out['ads']);
echo json_encode($out);
