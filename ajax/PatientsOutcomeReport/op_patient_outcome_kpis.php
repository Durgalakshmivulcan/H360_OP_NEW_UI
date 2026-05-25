<?php
header('Content-Type: application/json');
require_once("../../config/functions.php"); // must define $mysqli here

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// Filters
$org_id     = $_GET['orgId'] ?? $SessionOrgId;
$from       = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to         = $_GET['to']   ?? date('Y-m-d');
$doctor     = isset($_GET['doctor']) && $_GET['doctor'] !== '' ? trim($_GET['doctor']) : null;
$searchText = isset($_GET['search_text']) && $_GET['search_text'] !== '' ? trim($_GET['search_text']) : '';

// --- Step 1: Get security type ---
$checkDoctorQuery = "SELECT security_type FROM security WHERE status='1' AND security_id = '" . $mysqli->real_escape_string($SessionUserId) . "'";
$checkDoctorRes   = $mysqli->query($checkDoctorQuery);
$securityType     = $checkDoctorRes->fetch_assoc()['security_type'] ?? 'U';

// --- Step 2: Build WHERE clause ---
$where  = "ao.appoint_status = '1' AND ao.org_id = '" . $mysqli->real_escape_string($org_id) . "'";
$where .= " AND ao.appoint_date BETWEEN '" . $mysqli->real_escape_string($from) . "' AND '" . $mysqli->real_escape_string($to) . "'";

// Doctor filter logic — $doctor is a doc_id from the dropdown
if ($doctor) {
    $where .= " AND d.doc_id = " . (int)$doctor;
} elseif ($securityType === 'U') {
    // Restrict normal users to their own/assigned doctors
    $where .= " AND (
        d.security_id = '" . $mysqli->real_escape_string($SessionUserId) . "'
        OR d.doc_id IN (
            SELECT r.doc_id
            FROM receptionnist r
            WHERE r.security_id = '" . $mysqli->real_escape_string($SessionUserId) . "'
        )
    )";
}

// Search text
if ($searchText !== '') {
    $like = "%" . $mysqli->real_escape_string($searchText) . "%";
    $where .= " AND (ao.appoint_unicode LIKE '$like'
                     OR ao.appoint_register_id LIKE '$like'
                     OR ao.doctor_name LIKE '$like')";
}

// --- Step 3: SQL query ---
$sql = "SELECT ao.appoint_id,
               ao.appoint_register_id,
               ao.appoint_unicode,
               ao.doctor_name,
               ao.visitor_status,
               d.doc_id
        FROM appointment_online ao
        LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
        WHERE $where";

$result = $mysqli->query($sql);

if (!$result) {
    echo json_encode(['error' => $mysqli->error, 'query' => $sql]);
    exit;
}

// --- Step 4: Counters ---
$total = $improved = $declined = $missed = $same = $nodata = 0;

while ($row = $result->fetch_assoc()) {
    $status = $row['visitor_status'];
    if ($status === '0') {
        $improved++; $total++;
    } elseif ($status === '1') {
        $missed++; $total++;
    } elseif ($status === '3') {
        $declined++; $total++;
    } elseif ($status === '2') {
        $same++; $total++;
    } else {
        $nodata++;
    }
}
$result->free();

echo json_encode([
    'total'    => $total,
    'improved' => $improved,
    'declined' => $declined,
    'same'     => $same,
    'nodata'   => $nodata,
    'missed'   => $missed
]);
