<?php
// B-2050 Accountant dashboard: 30-day stacked revenue trend
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
$days = 30;
$start = date('Y-m-d', strtotime("-".($days - 1)." days"));
$end   = date('Y-m-d');

// Build skeleton: every day, zeroed
$buckets = [];
for ($i = 0; $i < $days; $i++) {
    $d = date('Y-m-d', strtotime("$start +$i days"));
    $buckets[$d] = ['consultation' => 0.0, 'tests' => 0.0, 'medicine' => 0.0];
}

// Consultation invoices (bill_type LIKE 'Consult%' OR 'Doctor%') and other invoices
$invQ = mysqli_query($conn,
    "SELECT DATE(created_at) AS d, bill_type, COALESCE(SUM(net_amount),0) AS total
     FROM invoice
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$start' AND '$end'
     GROUP BY DATE(created_at), bill_type");
if ($invQ) {
    while ($row = mysqli_fetch_assoc($invQ)) {
        $d = $row['d'];
        $bt = strtolower(trim((string)$row['bill_type']));
        $amt = (float)$row['total'];
        if (!isset($buckets[$d])) continue;
        if (str_contains($bt, 'consult') || str_contains($bt, 'doctor')) {
            $buckets[$d]['consultation'] += $amt;
        } elseif (str_contains($bt, 'medic') || str_contains($bt, 'pharm') || str_contains($bt, 'rx')) {
            $buckets[$d]['medicine'] += $amt;
        } else {
            // Lump non-test, non-consult invoices into consultation slot for now.
            $buckets[$d]['consultation'] += $amt;
        }
    }
}

// Lab tests
$testQ = mysqli_query($conn,
    "SELECT DATE(created_at) AS d, COALESCE(SUM(net_amount),0) AS total
     FROM patient_test_billing
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$start' AND '$end'
     GROUP BY DATE(created_at)");
if ($testQ) {
    while ($row = mysqli_fetch_assoc($testQ)) {
        $d = $row['d'];
        if (!isset($buckets[$d])) continue;
        $buckets[$d]['tests'] += (float)$row['total'];
    }
}

$categories = [];
$consultation = [];
$tests = [];
$medicine = [];
foreach ($buckets as $d => $vals) {
    $categories[]   = $d;
    $consultation[] = round($vals['consultation'], 2);
    $tests[]        = round($vals['tests'], 2);
    $medicine[]     = round($vals['medicine'], 2);
}

echo json_encode([
    'categories'   => $categories,
    'consultation' => $consultation,
    'tests'        => $tests,
    'medicine'     => $medicine,
]);
