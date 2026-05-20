<?php
// B-2050 Accountant dashboard: today's payment-method donut breakdown
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$org   = (int)$SessionOrgId;
$today = date('Y-m-d');

$series = [];

// Invoice payments
$q = mysqli_query($conn,
    "SELECT COALESCE(NULLIF(payment_method,''),'Other') AS pm,
            COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS amt
     FROM invoice
     WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'
     GROUP BY pm");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $pm = ucfirst(strtolower($row['pm']));
        $series[$pm] = $series[$pm] ?? ['count' => 0, 'amount' => 0.0];
        $series[$pm]['count']  += (int)$row['cnt'];
        $series[$pm]['amount'] += (float)$row['amt'];
    }
}

// Test billing payments
$q2 = mysqli_query($conn,
    "SELECT COALESCE(NULLIF(payment_method,''),'Other') AS pm,
            COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS amt
     FROM patient_test_billing
     WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'
     GROUP BY pm");
if ($q2) {
    while ($row = mysqli_fetch_assoc($q2)) {
        $pm = ucfirst(strtolower($row['pm']));
        $series[$pm] = $series[$pm] ?? ['count' => 0, 'amount' => 0.0];
        $series[$pm]['count']  += (int)$row['cnt'];
        $series[$pm]['amount'] += (float)$row['amt'];
    }
}

$labels  = [];
$amounts = [];
$counts  = [];
$total   = 0.0;
foreach ($series as $pm => $vals) {
    $labels[]  = $pm;
    $amounts[] = round($vals['amount'], 2);
    $counts[]  = $vals['count'];
    $total    += $vals['amount'];
}

echo json_encode([
    'labels'  => $labels,
    'amounts' => $amounts,
    'counts'  => $counts,
    'total'   => round($total, 2),
]);
