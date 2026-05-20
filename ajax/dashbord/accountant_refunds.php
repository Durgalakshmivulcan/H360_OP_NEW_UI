<?php
// B-2050 Accountant dashboard: refunds today + this week + recent list
// Refund table not present in current schema; we proxy refunds as invoices
// whose status was flipped to '0' (cancelled/refunded) within the period.
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$org        = (int)$SessionOrgId;
$today      = date('Y-m-d');
$weekStart  = date('Y-m-d', strtotime('monday this week'));
$weekEnd    = date('Y-m-d', strtotime('sunday this week'));

function rfScalar(mysqli $conn, string $sql): float {
    $r = mysqli_query($conn, $sql);
    if (!$r) return 0.0;
    $row = mysqli_fetch_array($r);
    return (float)($row[0] ?? 0);
}

$todayCount  = (int)rfScalar($conn,
    "SELECT COUNT(*) FROM invoice
     WHERE org_id='$org' AND status='0' AND DATE(modified_at)='$today'");
$todayAmount = rfScalar($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='0' AND DATE(modified_at)='$today'");

$weekCount  = (int)rfScalar($conn,
    "SELECT COUNT(*) FROM invoice
     WHERE org_id='$org' AND status='0'
     AND DATE(modified_at) BETWEEN '$weekStart' AND '$weekEnd'");
$weekAmount = rfScalar($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='0'
     AND DATE(modified_at) BETWEEN '$weekStart' AND '$weekEnd'");

$rows = [];
$q = mysqli_query($conn,
    "SELECT invoice_id, patient_id, bill_type, net_amount, modified_at
     FROM invoice
     WHERE org_id='$org' AND status='0'
     ORDER BY modified_at DESC
     LIMIT 5");
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = [
            'invoice_id' => (int)$r['invoice_id'],
            'patient_id' => $r['patient_id'],
            'bill_type'  => $r['bill_type'],
            'amount'     => (float)$r['net_amount'],
            'when'       => $r['modified_at'],
        ];
    }
}

echo json_encode([
    'today' => ['count' => $todayCount, 'amount' => round($todayAmount, 2)],
    'week'  => ['count' => $weekCount,  'amount' => round($weekAmount, 2)],
    'recent'=> $rows,
]);
