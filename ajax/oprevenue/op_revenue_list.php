<?php
// FIX_B_425 / FIX_B_700: empty-but-valid 200 stub.
// Schema-level fix (visits/invoices/invoice_items writers) tracked in B-085 — NEEDS-HUMAN.
// Until a writer exists, return DataTables-compatible empty payload so the page renders.
require_once(__DIR__ . "/../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if ($SessionUserId === '' || $SessionOrgId === '') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized',
        'data' => [],
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'note' => 'stub — needs visits table writer (B-085)'
    ]);
    exit;
}

// DataTables-shaped empty stub (server-side mode).
$draw = isset($_GET['draw']) ? (int) $_GET['draw'] : 0;
echo json_encode([
    'success' => true,
    'draw' => $draw,
    'data' => [],
    'recordsTotal' => 0,
    'recordsFiltered' => 0,
    'total' => 0,
    'note' => 'stub — needs visits table writer (B-085)'
]);
exit;
