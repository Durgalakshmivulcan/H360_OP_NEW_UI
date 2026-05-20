<?php
// B-2050 Accountant dashboard: top services + top tests by ₹ this month
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$org = (int)$SessionOrgId;
$monthStart = date('Y-m-01');
$monthEnd   = date('Y-m-t');

// Top services (by invoice.category_type/bill_type as service label)
$services = [];
$q = mysqli_query($conn,
    "SELECT COALESCE(NULLIF(category_type,''), NULLIF(bill_type,''),'Other') AS label,
            COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS total
     FROM invoice
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$monthStart' AND '$monthEnd'
     GROUP BY label
     ORDER BY total DESC
     LIMIT 5");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $services[] = [
            'label' => $row['label'],
            'count' => (int)$row['cnt'],
            'total' => round((float)$row['total'], 2),
        ];
    }
}

// Top tests (by patient_test_billing — extract first test_name from JSON if present)
$tests = [];
$q2 = mysqli_query($conn,
    "SELECT test_details, total_amount, net_amount
     FROM patient_test_billing
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$monthStart' AND '$monthEnd'");
$bucket = [];
if ($q2) {
    while ($row = mysqli_fetch_assoc($q2)) {
        $details = $row['test_details'];
        $label = 'Test';
        if ($details) {
            $decoded = json_decode($details, true);
            if (is_array($decoded)) {
                $label = $decoded['test_name'] ?? ($decoded[0]['test_name'] ?? 'Test');
            } else {
                // raw string
                $label = mb_substr(strip_tags($details), 0, 40);
            }
        }
        $label = trim((string)$label) ?: 'Test';
        if (!isset($bucket[$label])) {
            $bucket[$label] = ['count' => 0, 'total' => 0.0];
        }
        $bucket[$label]['count']++;
        $bucket[$label]['total'] += (float)$row['net_amount'];
    }
}
uasort($bucket, fn($a, $b) => $b['total'] <=> $a['total']);
$bucket = array_slice($bucket, 0, 5, true);
foreach ($bucket as $label => $vals) {
    $tests[] = [
        'label' => $label,
        'count' => $vals['count'],
        'total' => round($vals['total'], 2),
    ];
}

echo json_encode([
    'services' => $services,
    'tests'    => $tests,
    'period'   => ['from' => $monthStart, 'to' => $monthEnd],
]);
