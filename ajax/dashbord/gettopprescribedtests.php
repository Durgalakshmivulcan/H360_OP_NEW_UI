<?php
header('Content-Type: application/json');
require_once('../../config/functions.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if (!$SessionUserId) { echo json_encode([]); exit; }

$year    = (int)(($_POST['year'] ?? date('Y')));
$orgFilt = ($SessionUserId == '1') ? (int)($_POST['org_id'] ?? 0) : (int)$SessionOrgId;

// Build org condition
$orgCond        = $orgFilt ? "AND org_id='$orgFilt'" : '';
$orgCondNoAlias = $orgFilt ? "AND org_id='$orgFilt'" : '';

// Load role map: security_id => role_name (null for admin-type users — excluded from breakdown)
$roleMap = [];
$rRes = mysqli_query($conn, "
    SELECT s.security_id, s.security_type, TRIM(r.role_name) AS role_name
    FROM security s
    LEFT JOIN roles r ON r.role_id = s.role_id
    WHERE s.status='1'
");
// Roles excluded from breakdown columns (counted in total only)
$excludedRoles = ['Doctor', 'doctor'];

while ($r = mysqli_fetch_assoc($rRes)) {
    if ($r['security_type'] === 'A') {
        $roleMap[$r['security_id']] = null; // admin — exclude
    } else {
        $roleName = $r['role_name'] ?: 'User';
        // Doctor role excluded from breakdown (prescriptions are inherently doctor-created)
        $roleMap[$r['security_id']] = in_array($roleName, $excludedRoles) ? null : $roleName;
    }
}

$counts = []; // test_name => [total, by_role => [roleName => count]]

function addTest(&$counts, $name, $role) {
    $name = trim($name);
    if ($name === '') return;
    if (!isset($counts[$name])) $counts[$name] = ['total' => 0, 'by_role' => []];
    $counts[$name]['total']++;
    if ($role !== null) {
        $counts[$name]['by_role'][$role] = ($counts[$name]['by_role'][$role] ?? 0) + 1;
    }
}

// --- prescripition.test_id ---
$pRes = mysqli_query($conn, "
    SELECT test_id, create_by
    FROM prescripition
    WHERE status='1' AND YEAR(prescriptiondate)='$year' $orgCondNoAlias
") or die(mysqli_error($conn));

while ($row = mysqli_fetch_assoc($pRes)) {
    if (empty($row['test_id'])) continue;
    $tests = json_decode($row['test_id'], true);
    if (!is_array($tests)) continue;
    $role = $roleMap[$row['create_by']] ?? null;
    foreach ($tests as $t) {
        $name = trim($t['test_name'] ?? '');
        addTest($counts, $name, $role);
    }
}

// --- gynaec_prescriptions.investigations_json ---
$gRes = mysqli_query($conn, "
    SELECT investigations_json, created_by
    FROM gynaec_prescriptions
    WHERE status='1' AND YEAR(rx_date)='$year' $orgCondNoAlias
") or die(mysqli_error($conn));

while ($row = mysqli_fetch_assoc($gRes)) {
    if (empty($row['investigations_json'])) continue;
    $invs = json_decode($row['investigations_json'], true);
    if (!is_array($invs)) continue;
    $role = $roleMap[$row['created_by']] ?? null;
    foreach ($invs as $inv) {
        $name = trim($inv['investigation_name'] ?? $inv['investigation'] ?? '');
        addTest($counts, $name, $role);
    }
}

// Sort by total desc, take top 5
uasort($counts, fn($a, $b) => $b['total'] <=> $a['total']);
$top5 = array_slice($counts, 0, 5, true);

// Collect all distinct roles present across top 5
$allRoles = [];
foreach ($top5 as $d) {
    foreach (array_keys($d['by_role']) as $r) {
        $allRoles[$r] = true;
    }
}
$allRoles = array_keys($allRoles);

$output = [];
foreach ($top5 as $testName => $data) {
    $output[] = [
        'test_name' => $testName,
        'total'     => $data['total'],
        'by_role'   => $data['by_role'],
    ];
}

echo json_encode(['tests' => $output, 'roles' => $allRoles]);
