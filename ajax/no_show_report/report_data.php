<?php
// ============================================================================
// ajax/no_show_report/report_data.php
// ============================================================================

header('Content-Type: application/json');

require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// ---- Doctors list ----
// SA_FATAL_FIXED_B_391: include SA so $sql is defined for super-admin (was B-391)
if ($securityType === 'A' || $securityType === 'SA') {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status = '1'
            AND (
                d.security_id = '$SessionUserId'
                OR d.doc_id IN (
                        SELECT r.doc_id 
                        FROM receptionnist r 
                        WHERE r.security_id = '$SessionUserId'
                )
            )
            ORDER BY d.doctor_name ASC";
}

$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

$doctors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $doctors[] = $row;
}

// Helper to safely fetch POST parameters
function postParam($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$fromDate  = postParam('fromDate');
$toDate    = postParam('toDate');
$gender    = postParam('gender');
$ageGroup  = postParam('ageGroup');
$serviceId = postParam('service');
// New: doctor filter from dropdown
$doctorId  = postParam('doctor');

// Validate dates
if (empty($fromDate) || empty($toDate)) {
    echo json_encode([]);
    exit;
}

// Resolve service name if service ID provided
$serviceName = '';
if (!empty($serviceId)) {
    $sRes = mysqli_query($conn, "SELECT service_name FROM services WHERE service_id = '" . mysqli_real_escape_string($conn, $serviceId) . "'");
    if ($sRes && mysqli_num_rows($sRes) > 0) {
        $sRow = mysqli_fetch_assoc($sRes);
        $serviceName = $sRow['service_name'];
    }
}

// Build dynamic WHERE clause
$whereParts = [];
$whereParts[] = "a.appoint_date BETWEEN '" . mysqli_real_escape_string($conn, $fromDate) . "' AND '" . mysqli_real_escape_string($conn, $toDate) . "'";

if (!empty($gender)) {
    $whereParts[] = "a.gender = '" . mysqli_real_escape_string($conn, $gender) . "'";
}

if (!empty($serviceId)) {
    $whereParts[] = "s.service_id = '" . mysqli_real_escape_string($conn, $serviceId) . "'";
}

// --- Always restrict to allowed doctors ---
$doctorIds = array_column($doctors, 'doc_id');
$doctorIdsStr = implode(',', array_map('intval', $doctorIds));

if (!empty($doctorIdsStr)) {
    $whereParts[] = "a.doctor_name IN ($doctorIdsStr)";
} else {
    // No allowed doctors → return empty
    echo json_encode(['tableData' => [], 'chartSeries' => []]);
    exit;
}

// If a specific doctor is selected, override list filter
if (!empty($doctorId)) {
    $whereParts[] = "a.doctor_name = '" . mysqli_real_escape_string($conn, $doctorId) . "'";
}

$whereSql = implode(' AND ', $whereParts);

// Main query: group appointments by gender, age bucket and doctor_services
$query = "SELECT
            a.gender,
            CASE
              WHEN a.age < 18 THEN '<18'
              WHEN a.age BETWEEN 18 AND 30 THEN '18-30'
              WHEN a.age BETWEEN 31 AND 50 THEN '31-50'
              ELSE '>50'
            END AS age_group,
            s.service_name AS service,
            COUNT(*) AS total_appointments,
            SUM(CASE WHEN a.visitor_status = '3' OR a.visitor_status = '0' THEN 1 ELSE 0 END) AS no_shows,
            SUM(CASE WHEN a.appoint_status = '0' THEN 1 ELSE 0 END) AS cancellations,
            AVG(DATEDIFF(a.appoint_date, DATE(a.create_date_time))) AS avg_lead_time
          FROM appointment_online a
          LEFT JOIN doctors d ON a.doctor_name = d.doc_id
          LEFT JOIN services s ON FIND_IN_SET(s.service_id, d.doctor_services) > 0
          WHERE $whereSql
          GROUP BY a.gender, age_group, s.service_name";

$result = mysqli_query($conn, $query);
if (!$result) {
    echo json_encode([]);
    exit;
}

$tableData   = [];
$totalCount  = 0;
$totalNoShow = 0;
$totalCancel = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $total      = (int) $row['total_appointments'];
    $noShow     = (int) $row['no_shows'];
    $cancel     = (int) $row['cancellations'];
    $lead       = (float) $row['avg_lead_time'];

    if (!empty($ageGroup)) {
        if ($ageGroup === '<18' && $row['age_group'] !== '<18') continue;
        if ($ageGroup === '18-30' && $row['age_group'] !== '18-30') continue;
        if ($ageGroup === '31-50' && $row['age_group'] !== '31-50') continue;
        if ($ageGroup === '>50' && $row['age_group'] !== '>50') continue;
    }

    $totalCount  += $total;
    $totalNoShow += $noShow;
    $totalCancel += $cancel;

    $tableData[] = [
        'gender'           => $row['gender'],
        'ageGroup'         => $row['age_group'],
        'service'          => empty($row['service']) ? '-' : $row['service'],
        'total'            => $total,
        'noShow'           => $noShow,
        'cancelled'        => $cancel,
        'noShowRate'       => $total > 0 ? ($noShow / $total) : 0,
        'cancellationRate' => $total > 0 ? ($cancel / $total) : 0,
        'avgLeadTime'      => $lead
    ];
}

// Prepare chart series data
$chartSeries = [];
if ($totalCount > 0) {
    $overallNoShowRate   = $totalNoShow / $totalCount;
    $overallCancelRate   = $totalCancel / $totalCount;
    $chartSeries = [
        [ 'name' => 'Rate', 'data' => [ $overallNoShowRate, $overallCancelRate ] ]
    ];
}

echo json_encode([
    'tableData'  => $tableData,
    'chartSeries' => $chartSeries
]);
