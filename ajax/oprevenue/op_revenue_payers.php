<?php
// FIX_B_425 / FIX_B_700: empty-but-valid 200 stub.
// Schema-level fix tracked in B-085 — NEEDS-HUMAN (no writer for invoices).
// JS consumer (op_revenue.js) expects a bare JSON array for Array.isArray() check;
// returning [] keeps the dropdown populator on its happy path.
require_once(__DIR__ . "/../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if ($SessionUserId === '' || $SessionOrgId === '') {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

// stub — needs visits/invoices table writer (B-085)
echo json_encode([]);
exit;
