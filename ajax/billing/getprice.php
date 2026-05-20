<?php
// FIX_B_205: stub replacing legacy 500-throwing body.
// Per decisions_needed.md ANSWER (2026-05-10), product-owner intent on
// getprice.php response shape (per dept×service vs per appointment_id)
// is unresolved. This stub returns an empty 200 so the page stops 500'ing
// while we gather usage data via error_log. Original body preserved in
// getprice.php.b205-legacy.bak for the eventual real rewrite.
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../include/auth_guard.php");

session_start();
if (function_exists('requireLogin')) { requireLogin(); }

// Soft org assertion — degrade gracefully if helper missing in this branch.
$orgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;
if ($orgId <= 0) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'data' => [],
        'note' => 'stub — implement business logic',
        'bug'  => 'B-205',
        'error' => 'session org_id required',
    ]);
    exit;
}

error_log(sprintf(
    "[B-205 stub] getprice.php called by user_id=%s org_id=%d uri=%s — implement business logic",
    isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'unknown',
    $orgId,
    isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-'
));

header('Content-Type: application/json');
echo json_encode([
    'data' => [],
    'note' => 'stub — implement business logic',
    'bug'  => 'B-205',
]);
