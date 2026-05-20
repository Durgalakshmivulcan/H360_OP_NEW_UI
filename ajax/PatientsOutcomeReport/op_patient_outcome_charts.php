<?php
header('Content-Type: application/json');
require_once("../../config/functions.php"); // $mysqli must be defined

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// Get filters
$from       = trim($_POST['from'] ?? '');
$to         = trim($_POST['to'] ?? '');
$doctor     = trim($_POST['doctor'] ?? '');
$searchText = trim($_POST['search_text'] ?? '');

// Get user security type
$checkDoctor = $mysqli->query("SELECT security_type FROM security WHERE status='1' AND security_id='$SessionUserId'");
$securityType = $checkDoctor->fetch_assoc()['security_type'] ?? 'U';

// Build WHERE array
$where = ["ao.appoint_status='1'", "ao.org_id='" . $mysqli->real_escape_string($SessionOrgId) . "'"];

// Date filter
if ($from !== '' && $to !== '') {
    $from_s = $mysqli->real_escape_string($from) . ' 00:00:00';
    $to_s   = $mysqli->real_escape_string($to)   . ' 23:59:59';
    $where[] = "ao.appoint_date BETWEEN '$from_s' AND '$to_s'";
} elseif ($from !== '') {
    $where[] = "ao.appoint_date >= '" . $mysqli->real_escape_string($from) . " 00:00:00'";
} elseif ($to !== '') {
    $where[] = "ao.appoint_date <= '" . $mysqli->real_escape_string($to) . " 23:59:59'";
}

// Search filter
if ($searchText !== '') {
    $like = "%" . $mysqli->real_escape_string($searchText) . "%";
    $where[] = "(ao.appoint_unicode LIKE '$like' OR ao.appoint_register_id LIKE '$like')";
}

// Doctor / security filter
$doctorFilter = '';
if ($doctor !== '') {
    $doctorEsc = (int)$doctor;
    $doctorFilter = " AND d.doc_id = $doctorEsc";
} elseif ($securityType === 'U') {
    // Only show assigned doctors for normal users
    $doctorFilter = " AND (d.security_id = '$SessionUserId' 
                          OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id='$SessionUserId'))";
}

// Final WHERE
$whereSQL = implode(' AND ', $where);

// SQL: join doctors table on doc_id instead of name
$sql = "SELECT ao.visitor_status
        FROM appointment_online ao
        LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
        WHERE $whereSQL $doctorFilter";

$result = $mysqli->query($sql);
if (!$result) {
    echo json_encode(['error' => $mysqli->error, 'query' => $sql]);
    exit;
}

// Aggregate counts
$compCounts = ['Improved'=>0,'Declined'=>0,'Same'=>0,'Missed'=>0,'No Data'=>0];
while ($row = $result->fetch_assoc()) {
    $status = $row['visitor_status'];
    if ($status==='0') $key='Improved';
    elseif ($status==='1') $key='Missed';
    elseif ($status==='2') $key='Same';
    elseif ($status==='3') $key='Declined';
    else $key='No Data';
    $compCounts[$key]++;
}

$trendData = [
    'Improved'=>$compCounts['Improved'],
    'Declined'=>$compCounts['Declined'],
    'Same'=>$compCounts['Same'],
    'NoData'=>$compCounts['Missed']+$compCounts['No Data']
];

// Return JSON
echo json_encode([
    'trend'=>[
        'labels'=>['Total'],
        'improved'=>[$trendData['Improved']],
        'declined'=>[$trendData['Declined']],
        'same'=>[$trendData['Same']],
        'nodata'=>[$trendData['NoData']]
    ],
    'comp'=>[
        'labels'=>array_keys($compCounts),
        'data'=>array_values($compCounts)
    ]
]);
