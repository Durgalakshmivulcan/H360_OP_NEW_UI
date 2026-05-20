<?php
require_once("../../config/functions.php"); // must define $mysqli here

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$org_id = (int)($_SESSION['org_id'] ?? 0);
$export = $_GET['export'] ?? ''; // if ?export=csv → CSV mode

// Filters
$from       = $_GET['from']        ?? date('Y-m-d', strtotime('-30 days'));
$to         = $_GET['to']          ?? date('Y-m-d');
$doctor     = !empty($_GET['doctor']) ? trim($_GET['doctor']) : null;
$searchText = !empty($_GET['search_text']) ? trim($_GET['search_text']) : '';

// Pagination (for DataTables serverSide)
$start  = isset($_GET['start'])  ? (int)$_GET['start']  : 0;
$length = isset($_GET['length']) ? (int)$_GET['length'] : 10;

// --- Step 1: Get security type ---
$checkDoctorQuery = "SELECT security_type FROM security WHERE status='1' AND security_id = '" . $mysqli->real_escape_string($SessionUserId) . "'";
$checkDoctorRes   = $mysqli->query($checkDoctorQuery);
$securityType     = $checkDoctorRes->fetch_assoc()['security_type'] ?? 'U';

// --- Step 2: Build WHERE clause ---
$where  = "ao.appoint_status = '1' AND ao.org_id = " . (int)$org_id;
$where .= " AND ao.appoint_date BETWEEN '" . $mysqli->real_escape_string($from) . "' AND '" . $mysqli->real_escape_string($to) . "'";

// Doctor filter logic
if ($doctor) {
    $where .= " AND d.doctor_name = '" . $mysqli->real_escape_string($doctor) . "'";
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

// --- Count total ---
$totalSql = "SELECT COUNT(*) AS cnt 
             FROM appointment_online ao 
             LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
             WHERE $where";
$totalRes = $mysqli->query($totalSql);
$totalRow = $totalRes->fetch_assoc();
$recordsTotal = (int)$totalRow['cnt'];

// --- Main query ---
$sql = "SELECT 
            ao.appoint_unicode,
            ao.appoint_date,
            ao.bpSit_systolic,
            ao.bpStand_diastolic,
            ao.visitor_status,
            p.reviewafterdate,
            p.reviewafter
        FROM appointment_online ao
        LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
        LEFT JOIN prescripition p ON ao.appoint_register_id = p.appoint_register_id
        WHERE $where
        ORDER BY ao.appoint_date DESC";

if ($export !== 'csv') {
    $sql .= " LIMIT $start, $length";
}

$res = $mysqli->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    $baseline_bp = is_numeric($row['bpSit_systolic']) ? (int)$row['bpSit_systolic'] : null;
    $last_bp     = is_numeric($row['bpStand_diastolic']) ? (int)$row['bpStand_diastolic'] : null;

    // Compute diff
    $diff = null;
    if ($baseline_bp !== null && $last_bp !== null) {
        $diff = $last_bp - $baseline_bp;
    }

    // Compute status
    if ($baseline_bp === null || $last_bp === null) {
        $status = 'No Data';
    } elseif ($last_bp > $baseline_bp) {
        $status = 'Declined';
    } elseif ($last_bp < $baseline_bp) {
        $status = 'Improved';
    } else {
        $status = 'Same';
    }

    // Missed column
    $missed = ($row['visitor_status'] === '1') ? 'Yes' : 'No';

    $data[] = [
        'patient'        => $row['appoint_unicode'],
        'baseline_date'  => $row['appoint_date'],
        'baseline_bp'    => $baseline_bp,
        'last_date'      => $row['reviewafterdate'],
        'last_bp'        => $last_bp,
        'diff'           => $diff,
        'status'         => $status,
        'next_follow'    => $row['reviewafter'],
        'missed'         => $missed
    ];
}

// --- Output ---
if ($export === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=op_patient_outcomes_' . date('Ymd_His') . '.csv');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['Patient','Appointment Date','Baseline BP','Last Date','Last BP','Diff','Status','Next Follow-Up','Missed']);
    foreach ($data as $r) {
        fputcsv($out, [
            $r['patient'],
            $r['baseline_date'],
            $r['baseline_bp'],
            $r['last_date'],
            $r['last_bp'],
            $r['diff'],
            $r['status'],
            $r['next_follow'],
            $r['missed']
        ]);
    }
    fclose($out);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsTotal, // adjust if needed
        'data'            => $data
    ]);
    exit;
}
