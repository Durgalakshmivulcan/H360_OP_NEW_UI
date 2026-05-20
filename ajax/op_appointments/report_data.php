<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$fromDate  = isset($_POST['fromDate'])  ? mysqli_real_escape_string($conn, $_POST['fromDate'])  : '';
$toDate    = isset($_POST['toDate'])    ? mysqli_real_escape_string($conn, $_POST['toDate'])    : '';
$doctorId  = isset($_POST['doctor'])    ? mysqli_real_escape_string($conn, $_POST['doctor'])    : '';
$serviceId = isset($_POST['service'])   ? mysqli_real_escape_string($conn, $_POST['service'])   : '';

if (!$fromDate || !$toDate) {
  echo json_encode([]);
  exit;
}

// Get security type and allowed doctors
$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// SA_FATAL_FIXED_B_549: include SA so $sql is defined for super-admin
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

// Build query conditions
$conditions = [];
$conditions[] = "a.appoint_date BETWEEN '$fromDate' AND '$toDate'";

// Always restrict to allowed doctors
$allowedDoctorIds = array_column($doctors, 'doc_id');
$allowedDoctorIdsStr = implode(',', array_map('intval', $allowedDoctorIds));

if (!empty($allowedDoctorIdsStr)) {
    if ($doctorId !== '') {
        $conditions[] = "a.doctor_name = '$doctorId'";
    } else {
        $conditions[] = "a.doctor_name IN ($allowedDoctorIdsStr)";
    }
} else {
    // no allowed doctors → return empty
    echo json_encode([ 'tableData'=>[], 'totals'=>[], 'chartSeries'=>[] ]);
    exit;
}

// Service filter
if ($serviceId !== '') {
    $conditions[] = "s.service_id = '$serviceId'";
}

// FIX_B_1903: explicit doctor-scope filter (defense-in-depth alongside doctor_name IN ($allowedDoctorIdsStr) above).
// Helper returns " AND col='id' " or '' — strip the leading " AND " before adding to AND-joined conditions.
$_dscope = trim(currentDoctorScopeSql('a.doctor_name'));
if (strpos($_dscope, 'AND ') === 0) { $_dscope = trim(substr($_dscope, 4)); }
if ($_dscope !== '') $conditions[] = $_dscope;
$whereClause = implode(' AND ', $conditions);

// Compute appointment counts
$sql = "SELECT
          s.service_name AS service,
          d.doctor_name  AS doctor,
          SUM(CASE
                WHEN a.appoint_status = '0' THEN 1
                WHEN a.visitor_status = '1' THEN 1
                WHEN a.visitor_status = '2' THEN 1
                WHEN a.visitor_status = '0' THEN 1
                WHEN a.visitor_status = '3' THEN 1
                ELSE 0
              END) AS booked,
          SUM(CASE WHEN a.visitor_status = '0' THEN 1 ELSE 0 END) AS completed,
          SUM(CASE WHEN a.appoint_status = '0' THEN 1 ELSE 0 END) AS cancelled,
          SUM(CASE WHEN a.visitor_status = '3' THEN 1 ELSE 0 END) AS noShows
        FROM appointment_online a
        JOIN doctors d ON a.doctor_name = d.doc_id
        JOIN services s ON FIND_IN_SET(s.service_id, d.doctor_services) > 0
        WHERE $whereClause
        GROUP BY s.service_name, d.doctor_name";

$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$tableData = [];
$totals = [ 'booked'=>0, 'completed'=>0, 'cancelled'=>0, 'noShows'=>0 ];

while ($row = mysqli_fetch_assoc($res)) {
  $tableData[] = [
    'service'   => $row['service'] ?? 'Unknown',
    'doctor'    => $row['doctor']  ?? 'Unknown',
    'booked'    => (int)$row['booked'],
    'completed' => (int)$row['completed'],
    'cancelled' => (int)$row['cancelled'],
    'noShows'   => (int)$row['noShows']
  ];
  $totals['booked']    += (int)$row['booked'];
  $totals['completed'] += (int)$row['completed'];
  $totals['cancelled'] += (int)$row['cancelled'];
  $totals['noShows']   += (int)$row['noShows'];
}

$chartSeries = [ [ 'name' => 'Count', 'data' => [ $totals['booked'], $totals['completed'], $totals['cancelled'], $totals['noShows'] ] ] ];

echo json_encode([ 'tableData' => $tableData, 'totals' => $totals, 'chartSeries' => $chartSeries ]);
?>
