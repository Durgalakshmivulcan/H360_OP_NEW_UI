<?php
// ============================================================================
// ajax/patient_waiting/report_data.php
//
// Backend endpoint for the Patient Waiting Time report.  
// Accepts a date range and optional doctor/service filters, 
// then calculates the average consultation duration 
// (difference between doctor_patient_duration.check_in and check_out).  
// Results are aggregated per doctor and service, and overall averages 
// are provided for the summary tiles and chart.
// ============================================================================

header('Content-Type: application/json');

require_once("../../config/functions.php");

// Helper function to get POST parameter safely
function getParam($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$fromDate  = getParam('fromDate');
$toDate    = getParam('toDate');
$doctorId  = getParam('doctor');
$serviceId = getParam('service');

// Basic validation
if (empty($fromDate) || empty($toDate)) {
    echo json_encode([]);
    exit;
}

$esc_org = mysqli_real_escape_string($conn, $SessionOrgId);
$esc_uid = mysqli_real_escape_string($conn, $SessionUserId);

$checkDoctor  = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$esc_uid'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

if ($securityType === 'SA') {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
} elseif ($securityType === 'A') {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' AND org_id='$esc_org' ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status = '1'
            AND d.org_id = '$esc_org'
            AND (
                d.security_id = '$esc_uid'
                OR d.doc_id IN (
                    SELECT r.doc_id
                    FROM receptionnist r
                    WHERE r.security_id = '$esc_uid'
                )
            )
            ORDER BY d.doctor_name ASC";
} else {
    echo json_encode([]);
    exit;
}

$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

$doctors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $doctors[] = $row;
}

// Resolve service name for display (not used for filtering)
$serviceName = '';
if (!empty($serviceId)) {
    $resSrv = mysqli_query(
        $conn, 
        "SELECT service_name 
         FROM services 
         WHERE service_id = '" . mysqli_real_escape_string($conn, $serviceId) . "'"
    );
    if ($resSrv && mysqli_num_rows($resSrv) > 0) {
        $srvRow      = mysqli_fetch_assoc($resSrv);
        $serviceName = $srvRow['service_name'];
    }
}

// Build WHERE clauses
$whereParts   = [];
$whereParts[] = "a.appoint_date BETWEEN '" . mysqli_real_escape_string($conn, $fromDate) . "' AND '" . mysqli_real_escape_string($conn, $toDate) . "'";

if ($securityType !== 'SA' && !empty($SessionOrgId)) {
    $whereParts[] = "a.org_id = '$esc_org'";
}

if (!empty($doctorId)) {
    $whereParts[] = "a.doctor_name = '" . mysqli_real_escape_string($conn, $doctorId) . "'";
} else {
    $allowedDoctorIds = array_column($doctors, 'doc_id');
    if (!empty($allowedDoctorIds)) {
        $whereParts[] = "a.doctor_name IN (" . implode(',', array_map('intval', $allowedDoctorIds)) . ")";
    } else {
        echo json_encode([]);
        exit;
    }
}

if (!empty($serviceId)) {
    $whereParts[] = "FIND_IN_SET('" . mysqli_real_escape_string($conn, $serviceId) . "', d.doctor_services) > 0";
}

$whereSql = implode(' AND ', $whereParts);

// Group by doctor only; service displayed via scalar subquery to avoid row multiplication
$sql = "SELECT
    d.doctor_name,
    (SELECT GROUP_CONCAT(sv.service_name ORDER BY sv.service_name SEPARATOR ', ')
     FROM services sv
     WHERE FIND_IN_SET(sv.service_id, d.doctor_services) > 0 AND sv.status='1') AS service,
    COUNT(*) AS total_appointments,
    AVG(
        CASE
            WHEN a.start_time IS NOT NULL
                 AND dpd.check_in IS NOT NULL
            THEN TIMESTAMPDIFF(MINUTE, a.start_time, dpd.check_in)
            ELSE NULL
        END
    ) AS avg_wait,
    AVG(
        CASE
            WHEN dpd.check_in IS NOT NULL
                 AND dpd.check_out IS NOT NULL
            THEN TIMESTAMPDIFF(MINUTE, dpd.check_in, dpd.check_out)
            ELSE NULL
        END
    ) AS avg_duration
FROM appointment_online a
JOIN doctor_patient_duration dpd
    ON a.appoint_register_id = dpd.appointment_id
JOIN doctors d
    ON a.doctor_name = d.doc_id
WHERE $whereSql
GROUP BY d.doc_id, d.doctor_name";

$res = mysqli_query($conn, $sql);
if (!$res) {
    echo json_encode([]);
    exit;
}

$tableRows = [];
$sumDur    = 0;
$countRows = 0;

while ($r = mysqli_fetch_assoc($res)) {
    $total       = (int) $r['total_appointments'];
    $avgDuration = isset($r['avg_duration']) ? floatval($r['avg_duration']) : 0;
    $avgWait     = isset($r['avg_wait']) ? floatval($r['avg_wait']) : 0;

    if ($avgDuration > 0) {
        $sumDur += $avgDuration;
        $countRows++;
    }

    $tableRows[] = [
        'doctor'      => $r['doctor_name'],
        'service'     => empty($r['service']) ? 'Unknown' : $r['service'],
        'total'       => $total,
        'avgWait'     => $avgWait,
        'avgDuration' => $avgDuration
    ];
}

// Compute overall averages for summary and chart
$overallAvgDur = $countRows > 0 ? ($sumDur / $countRows) : 0;

$chartSeries = [
    [ 'name' => 'Avg Duration (mins)', 'data' => [ $overallAvgDur ] ]
];

echo json_encode([
    'tableData'   => $tableRows,
    'averages'    => [ 'avgVisit' => $overallAvgDur ],
    'chartSeries' => $chartSeries
]);
