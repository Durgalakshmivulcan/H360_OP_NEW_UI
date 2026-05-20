<?php
// Disable errors and notices for SSE
error_reporting(0);
@ini_set('zlib.output_compression', 0);
@ini_set('output_buffering', 0);
@ini_set('implicit_flush', 1);
while (ob_get_level()) ob_end_flush();
ob_implicit_flush(true);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Include functions and database
require("../../config/functions.php");
global $conn;

// Session info
$SessionOrgId  = $_SESSION['org_id'] ?? 1;
$SessionUserId = $_SESSION['security_id'] ?? 0;

// Close session to avoid blocking
session_write_close();

// Filter parameters
$fromDate  = $_GET['from'] ?? date('Y-m-d');
$toDate    = $_GET['to'] ?? date('Y-m-d');
$doctorId  = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : null;
$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;

// Function to fetch receptionist appointments
function getReceptionistAppointments($conn, $fromDate, $toDate, $doctorId, $serviceId, $SessionOrgId, $SessionUserId) {
    $where = [];
    $where[] = "DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'";
    if ($doctorId)  $where[] = "d.doc_id = $doctorId";
    if ($serviceId) $where[] = "FIND_IN_SET($serviceId, d.doctor_services) > 0";

    $whereClause = implode(' AND ', $where);

    $sql = "SELECT a.appoint_register_id, a.appoint_unicode, a.patient_name, a.appoint_date,
                   a.start_time, a.end_time, d.doctor_name, s.service_name, a.visitor_status
            FROM appointment_online a
            JOIN doctors d ON a.doctor_name = d.doc_id
            JOIN services s ON FIND_IN_SET(s.service_id, d.doctor_services) > 0
            WHERE a.org_id = $SessionOrgId
              AND (d.security_id = $SessionUserId 
                   OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id = $SessionUserId))
              AND $whereClause
            ORDER BY a.appoint_date ASC";

    $result = mysqli_query($conn, $sql);
    $appointments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // FIX_B_068 — align producer status names to the consumer (ReceptionistBoard.php)
        $statusText = ['0'=>'Completed','1'=>'Pending','2'=>'In Session','3'=>'Lapsed'][$row['visitor_status']] ?? 'Pending';
        $appointments[] = [
            'appoint_register_id' => $row['appoint_register_id'],
            'patient_id'         => $row['appoint_unicode'],
            'patient_name'       => $row['patient_name'],
            'doctor_name'        => $row['doctor_name'],
            'service_name'       => $row['service_name'],
            'appoint_date'       => $row['appoint_date'],
            'start_time'         => $row['start_time'],
            'end_time'           => $row['end_time'],
            'status'             => $statusText,
            'visitor_status'     => $row['visitor_status']
        ];
    }
    return $appointments;
}

// FIX_B_067: cap the loop with a wall-clock deadline so PHP-FPM workers
// are not held forever. Browser EventSource auto-reconnects per spec,
// so the client sees a continuous stream while server-side workers cycle.
$deadline = time() + 30;
while (time() < $deadline) {
    $appointments = getReceptionistAppointments($conn, $fromDate, $toDate, $doctorId, $serviceId, $SessionOrgId, $SessionUserId);

    // Always send valid JSON (even empty array)
    echo "data: " . json_encode($appointments, JSON_UNESCAPED_UNICODE) . "\n\n";

    ob_flush();
    flush();
    sleep(20); // repeat every 20 seconds
}
