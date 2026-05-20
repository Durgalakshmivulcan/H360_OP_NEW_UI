<?php
require_once("../../config/functions.php");
header("Content-Type: application/json; charset=utf-8");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// ---------- Step 1: Inputs ----------
$orgId  = $_GET['orgId']  ?? $SessionOrgId;
$from   = $_GET['from']   ?? date('Y-m-d', strtotime('-30 days'));
$to     = $_GET['to']     ?? date('Y-m-d');
$dx     = $_GET['dx']     ?? '';
$class  = $_GET['class']  ?? '';
$doctor = $_GET['doctor'] ?? '';

// FIX_B_204: hoist security_type lookup so $securityType is defined
// before the WHERE-builder consumes it (was use-before-assignment → 500).
// ---------- Step 3: Get security type ----------
$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// ---------- Step 2: Build WHERE ----------
// ---------- Step 2: Build WHERE ----------
$where = "p.org_id = '$orgId' AND DATE(p.prescriptiondate) BETWEEN '$from' AND '$to'";
if ($dx) {
    $dxEsc = mysqli_real_escape_string($conn, $dx);
    $where .= " AND p.finalDiagnosis LIKE '%$dxEsc%'";
}

// Doctor filter logic
if ($doctor) {
    $doctorEsc = mysqli_real_escape_string($conn, $doctor);
    $where .= " AND d.doc_id = '$doctorEsc'";
} elseif ($securityType === 'U') {
    // If user is not admin and no doctor filter given, restrict to their own/assigned doctors
    $where .= " AND (
        d.security_id = '$SessionUserId'
        OR d.doc_id IN (
            SELECT r.doc_id 
            FROM receptionnist r 
            WHERE r.security_id = '$SessionUserId'
        )
    )";
}

if ($class) {
    $classEsc = mysqli_real_escape_string($conn, $class);
    $where .= " AND p.medicine_id LIKE '%$classEsc%'";
}

// ---------- Step 4: Build query ----------
// SA_FATAL_FIXED_B_551: include SA so $sql is defined for super-admin
if ($securityType === 'A' || $securityType === 'SA') {
    // Admin/Super-Admin → see all prescriptions
    $sql = "
        SELECT p.prescription_id, 
               p.patient_uid, 
               p.appoint_register_id, 
               p.finalDiagnosis,
               p.prescriptiondate, 
               p.medicine_id,
               d.doctor_name,
               d.doc_id
        FROM prescripition p
        LEFT JOIN appointment_online ao 
               ON ao.appoint_register_id = p.appoint_register_id
        LEFT JOIN doctors d 
               ON d.doc_id = ao.doctor_name
        WHERE p.status = '1'
          AND $where
        ORDER BY p.prescriptiondate ASC
    ";
} elseif ($securityType === 'U') {
    // User/Doctor → restricted prescriptions
    $sql = "
        SELECT p.prescription_id, 
               p.patient_uid, 
               p.appoint_register_id, 
               p.finalDiagnosis,
               p.prescriptiondate, 
               p.medicine_id,
               d.doctor_name,
               d.doc_id
        FROM prescripition p
        LEFT JOIN appointment_online ao 
               ON ao.appoint_register_id = p.appoint_register_id
        LEFT JOIN doctors d 
               ON d.doc_id = ao.doctor_name
        WHERE p.status = '1'
          AND $where
          AND (
              d.security_id = '$SessionUserId'
              OR d.doc_id IN (
                  SELECT r.doc_id 
                  FROM receptionnist r 
                  WHERE r.security_id = '$SessionUserId'
              )
          )
        ORDER BY p.prescriptiondate ASC
    ";
}

// ---------- Step 5: Execute query ----------
$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

if (!$res) {
    echo json_encode(['error' => mysqli_error($conn), 'sql' => $sql]);
    exit;
}

// ---------- Step 6: Initialize ----------
$totalScripts   = 0;
$uniqueDrugs    = [];
$drugUsage      = [];
$diagCounts     = [];
$trend          = [];
$doctorList     = [];

// ---------- Step 7: Process results ----------
while ($row = mysqli_fetch_assoc($res)) {
    $totalScripts++;
    $patient    = $row['patient_uid'] ?: $row['appoint_register_id'];
    $diagnosis  = $row['finalDiagnosis'] ?: '--';
    $doctorName = $row['doctor_name'] ?: '--';
    $doctorId   = $row['doc_id'] ?: null;
    $day        = date('Y-m-d', strtotime($row['prescriptiondate']));
    $trend[$day] = ($trend[$day] ?? 0) + 1;

    if ($doctorId) {
        $doctorList[$doctorId] = $doctorName;
    }

    $medicines = json_decode($row['medicine_id'], true);
    if (!is_array($medicines)) continue;

    foreach ($medicines as $m) {
        $name      = $m['medicine_name'] ?? null;
        $drugClass = $m['drug_class'] ?? null;
        if (!$name) continue;

        if ($class && $drugClass && strtolower($drugClass) !== strtolower($class)) continue;

        $uniqueDrugs[$name] = true;

        if (!isset($drugUsage[$name])) {
            $drugUsage[$name] = [
                'patients'  => [],   // track unique patients
                'scripts'   => 0,    // total prescriptions
                'diagnoses' => [],
                'drug_class'=> $drugClass
            ];
        }

        // Count total prescriptions
        $drugUsage[$name]['scripts']++;

        // Track unique patients
        $drugUsage[$name]['patients'][$patient] = true;

        // Track diagnoses
        $drugUsage[$name]['diagnoses'][$diagnosis] = ($drugUsage[$name]['diagnoses'][$diagnosis] ?? 0) + 1;
    }

    $diagCounts[$diagnosis] = ($diagCounts[$diagnosis] ?? 0) + 1;
}

// ---------- Step 8: Build doctor array ----------
$doctorArr = [];
foreach ($doctorList as $id => $name) {
    $doctorArr[] = [
        'doc_id' => $id,
        'doctor_name' => $name
    ];
}

// ---------- Step 9: Build drug_usage ----------
$drugList = [];
$flagThreshold = 1;

foreach ($drugUsage as $name => $info) {
    arsort($info['diagnoses']);
    $topDiag = array_keys($info['diagnoses'])[0] ?? '--';

    $drugList[] = [
        'drug_name'  => $name,
        'drug_class' => $info['drug_class'],
        'dx_name'    => $topDiag,
        'scripts'    => $info['scripts'],         // total prescriptions
        'patients'   => count($info['patients']), // unique patients
        'share_pct'  => round(($info['scripts'] / $totalScripts) * 100, 1),
        'flag'       => ($info['scripts'] > $flagThreshold),
        'flag_number'=> $info['scripts'] 
    ];
}

// Sort by total prescriptions descending
usort($drugList, fn($a, $b) => $b['scripts'] <=> $a['scripts']);

// ---------- Step 10: Top diagnoses ----------
arsort($diagCounts);
$topDiagnoses = [];
foreach ($diagCounts as $d => $c) {
    $topDiagnoses[] = ['diagnosis' => $d, 'count' => $c];
}

// ---------- Step 11: Trend ----------
ksort($trend);
$trendArr = [];
foreach ($trend as $d => $c) {
    $trendArr[] = ['date' => $d, 'scripts' => $c];
}

// ---------- Step 12: Output JSON ----------
echo json_encode([
    'meta' => [
        'date_from' => $from,
        'date_to'   => $to,
        'total_scripts' => $totalScripts,
        'unique_drug_count' => count($uniqueDrugs)
    ],
    'drug_usage'    => $drugList,
    'top_diagnoses' => $topDiagnoses,
    'doctorArr'     => $doctorArr,
    'script_trend'  => $trendArr
], JSON_PRETTY_PRINT);
