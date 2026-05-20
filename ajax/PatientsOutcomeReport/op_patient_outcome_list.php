<?php
header('Content-Type: application/json');
require_once("../../config/functions.php"); // must define $mysqli here

$org_id = isset($_SESSION['org_id']) ? (int)$_SESSION['org_id'] : 0;

// Filters
$from       = $_GET['from']   ?? null;
$to         = $_GET['to']     ?? null;
$doctor     = isset($_GET['doctor']) && $_GET['doctor'] !== '' ? trim($_GET['doctor']) : null;
$searchText = isset($_GET['search_text']) && $_GET['search_text'] !== '' ? trim($_GET['search_text']) : '';

// --- Build WHERE ---
$where  = "ao.appoint_status = '1' AND ao.org_id = '" . $mysqli->real_escape_string($org_id) . "'";

if ($from) {
    $where .= " AND ao.appoint_date >= '" . $mysqli->real_escape_string($from) . "'";
}
if ($to) {
    $where .= " AND ao.appoint_date <= '" . $mysqli->real_escape_string($to) . "'";
}
if ($doctor) {
    $where .= " AND d.doctor_name = '" . $mysqli->real_escape_string($doctor) . "'";
}
if ($searchText !== '') {
    $like = "%" . $mysqli->real_escape_string($searchText) . "%";
    $where .= " AND (ao.appoint_unicode LIKE '$like'
                     OR ao.appoint_register_id LIKE '$like'
                     OR ao.doctor_name LIKE '$like')";
}

// Build query
$sql = "SELECT ao.appoint_id,
               ao.appoint_register_id,
               ao.appoint_unicode,
               ao.appoint_date,
               ao.doctor_name,
               ao.visitor_status,
               ao.bpSit_systolic,
               ao.bpStand_diastolic,
               d.doc_id,
               p.reviewafterdate,
               p.reviewafter
        FROM appointment_online ao
        LEFT JOIN doctors d ON ao.doctor_name = d.doctor_name
        LEFT JOIN prescripition p ON ao.appoint_register_id = p.appoint_register_id
        WHERE $where";

$res = $mysqli->query($sql);
if (!$res) {
    header('Content-Type: text/plain');
    echo 'Error: ' . $mysqli->error;
    exit;
}

// CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=op_patient_outcomes_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
// CSV header (7 columns)
fputcsv($out, [
    'Patient',
    'Appointment Date',
    'Baseline BP',
    'Last Date',
    'Last BP',
    'Next Follow-Up',
    'Missed'
]);

while ($row = $res->fetch_assoc()) {
    $status = $row['visitor_status'];

    // Missed column: Yes if visitor_status = 1
    $missed = ($status === '1') ? 'Yes' : 'No';

    fputcsv($out, [
        $row['appoint_unicode'],    // Patient
        $row['appoint_date'],       // Appointment Date
        $row['bpSit_systolic'],     // Baseline BP
        $row['reviewafterdate'],    // Last Date
        $row['bpStand_diastolic'],  // Last BP
        $row['reviewafter'],        // Next Follow-Up
        $missed                     // Missed
    ]);
}

fclose($out);

