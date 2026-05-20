<?php
// FIX_B_004 — ClinicDashboard.php data writer.
// JS contract preserved: see ClinicDashboard.php line 184-222.
require_once(__DIR__ . "/../../config/functions.php");
require_once(__DIR__ . "/../../include/auth_guard.php");
requireLogin();
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$fromDate = $_POST['fromDate'] ?? date('Y-m-01');
$toDate   = $_POST['toDate']   ?? date('Y-m-d');
$fromDate = mysqli_real_escape_string($conn, $fromDate);
$toDate   = mysqli_real_escape_string($conn, $toDate);
$orgId    = mysqli_real_escape_string($conn, (string)$SessionOrgId);

$orgFilter = $orgId !== '' ? "AND a.org_id='$orgId'" : "";
// FIX_B_1903: doctor-scope filter — applied via the existing $orgFilter so every appointment_online query inherits it.
$orgFilter .= currentDoctorScopeSql('a.doctor_name');

// Appointment counts by status
$apptQ = mysqli_query($conn, "
    SELECT
        SUM(CASE WHEN visitor_status='1' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN visitor_status='2' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN visitor_status='0' THEN 1 ELSE 0 END) AS done,
        SUM(CASE WHEN visitor_status='3' THEN 1 ELSE 0 END) AS no_show,
        COUNT(*) AS total
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
");
$apptRow = $apptQ ? mysqli_fetch_assoc($apptQ) : null;
$appointments = [
    'total'   => (int)($apptRow['total']   ?? 0),
    'pending' => (int)($apptRow['pending'] ?? 0),
    'active'  => (int)($apptRow['active']  ?? 0),
    'done'    => (int)($apptRow['done']    ?? 0),
    'no_show' => (int)($apptRow['no_show'] ?? 0),
];

// Revenue aggregates (best-effort against appointment_online amount/final_amount)
$revQ = mysqli_query($conn, "
    SELECT
        COALESCE(SUM(a.amount),0)                                AS gross,
        COALESCE(SUM(a.amount - COALESCE(a.final_amount,a.amount)),0) AS discount,
        0                                                         AS tax,
        COALESCE(SUM(COALESCE(a.final_amount,a.amount)),0)       AS net
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
");
$revRow = $revQ ? mysqli_fetch_assoc($revQ) : null;
$revenue = [
    'gross'    => (float)($revRow['gross']    ?? 0),
    'discount' => (float)($revRow['discount'] ?? 0),
    'tax'      => (float)($revRow['tax']      ?? 0),
    'net'      => (float)($revRow['net']      ?? 0),
];

// Average consultation/waiting times (minutes) via doctor_patient_duration
$avg = ['consultation' => 0, 'waiting' => 0];
$avgQ = @mysqli_query($conn, "
    SELECT
        AVG(TIMESTAMPDIFF(MINUTE, dpd.check_in, dpd.check_out)) AS consultation
    FROM doctor_patient_duration dpd
    LEFT JOIN appointment_online a ON dpd.appointment_id = a.appoint_register_id
    WHERE dpd.check_out IS NOT NULL
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
");
if ($avgQ && ($r = mysqli_fetch_assoc($avgQ))) {
    $avg['consultation'] = (float)($r['consultation'] ?? 0);
}

// Charts: per-day stacked appointment counts
$apptChart = ['categories' => [], 'pending' => [], 'active' => [], 'done' => [], 'no_show' => []];
$cQ = mysqli_query($conn, "
    SELECT DATE(a.appoint_date) AS d,
           SUM(CASE WHEN visitor_status='1' THEN 1 ELSE 0 END) AS pending,
           SUM(CASE WHEN visitor_status='2' THEN 1 ELSE 0 END) AS active,
           SUM(CASE WHEN visitor_status='0' THEN 1 ELSE 0 END) AS done,
           SUM(CASE WHEN visitor_status='3' THEN 1 ELSE 0 END) AS no_show
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
    GROUP BY DATE(a.appoint_date)
    ORDER BY d ASC
");
while ($cQ && ($r = mysqli_fetch_assoc($cQ))) {
    $apptChart['categories'][] = $r['d'];
    $apptChart['pending'][]    = (int)$r['pending'];
    $apptChart['active'][]     = (int)$r['active'];
    $apptChart['done'][]       = (int)$r['done'];
    $apptChart['no_show'][]    = (int)$r['no_show'];
}

// Revenue trend
$revChart = ['categories' => [], 'data' => []];
$rQ = mysqli_query($conn, "
    SELECT DATE(a.appoint_date) AS d,
           COALESCE(SUM(COALESCE(a.final_amount,a.amount)),0) AS revenue
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
    GROUP BY DATE(a.appoint_date)
    ORDER BY d ASC
");
while ($rQ && ($r = mysqli_fetch_assoc($rQ))) {
    $revChart['categories'][] = $r['d'];
    $revChart['data'][]       = (float)$r['revenue'];
}

// Top doctors by appointments
$topDoctors = ['labels' => [], 'data' => []];
$dQ = mysqli_query($conn, "
    SELECT d.doctor_name AS name, COUNT(*) AS cnt
    FROM appointment_online a
    LEFT JOIN doctors d ON a.doctor_name = d.doc_id
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
    GROUP BY a.doctor_name
    ORDER BY cnt DESC
    LIMIT 5
");
while ($dQ && ($r = mysqli_fetch_assoc($dQ))) {
    $topDoctors['labels'][] = $r['name'] ?: 'Unknown';
    $topDoctors['data'][]   = (int)$r['cnt'];
}

// Top services — best-effort (services table; falls back to empty)
$topServices = ['labels' => [], 'data' => []];
$sQ = @mysqli_query($conn, "
    SELECT s.service_name AS name, COUNT(*) AS cnt
    FROM appointment_online a
    JOIN doctors d  ON a.doctor_name = d.doc_id
    JOIN services s ON FIND_IN_SET(s.service_id, d.doctor_services) > 0
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'
      $orgFilter
    GROUP BY s.service_id
    ORDER BY cnt DESC
    LIMIT 5
");
while ($sQ && ($r = mysqli_fetch_assoc($sQ))) {
    $topServices['labels'][] = $r['name'] ?: 'Unknown';
    $topServices['data'][]   = (int)$r['cnt'];
}

echo json_encode([
    'appointments' => $appointments,
    'revenue'      => $revenue,
    'avg'          => $avg,
    'charts'       => [
        'appointments' => $apptChart,
        'revenue'      => $revChart,
        'top_doctors'  => $topDoctors,
        'top_services' => $topServices,
    ],
]);
