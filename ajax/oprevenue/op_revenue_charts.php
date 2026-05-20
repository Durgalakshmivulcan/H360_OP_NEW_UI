<?php
// FIX_B_425 / FIX_B_700: empty-but-valid 200 stub.
// Schema-level fix tracked in B-085 — NEEDS-HUMAN (no writer for invoices/visits).
// JS reads resp.trend.{labels,data} and resp.composition.{labels,data}; provide both
// shapes plus the action-plan-requested {categories, series} shape for compatibility.
require_once(__DIR__ . "/../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if ($SessionUserId === '' || $SessionOrgId === '') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized',
        'trend' => ['labels' => [], 'data' => []],
        'composition' => ['labels' => [], 'data' => []],
        'categories' => [], 'series' => [],
        'note' => 'stub — needs visits table writer (B-085)'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'trend' => ['labels' => [], 'data' => []],
    'composition' => ['labels' => [], 'data' => []],
    'categories' => [],
    'series' => [],
    'note' => 'stub — needs visits table writer (B-085)'
]);
exit;
