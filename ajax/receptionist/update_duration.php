<?php
// IDOR_FIXED B-578
require_once("../../config/functions.php");

$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('edit', 'receptionist.php', 'ajax');

header('Content-Type: application/json');

// Read incoming POST parameters
$appointmentId = $_POST['appointment_id'] ?? null;
$action        = isset($_POST['action']) ? trim($_POST['action']) : '';
$now           = date('Y-m-d H:i:s');

// Validate input
if (!$appointmentId || !in_array($action, ['start', 'done', 'lapsed'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    exit;
}



try {
    if ($action === 'start') {
        // Insert a new duration record with check_in time
        $query = "INSERT INTO doctor_patient_duration (appointment_id, check_in) 
                  VALUES ('$appointmentId', '$now')";
        mysqli_query($conn, $query);

        // Update visitor_status to 2 (in session)
        mysqli_query($conn, "UPDATE appointment_online 
                             SET visitor_status = '2' 
                             WHERE appoint_register_id = '$appointmentId' AND org_id='$SessionOrgId'");

        echo json_encode(['status' => 'ok', 'message' => 'Check-in recorded']);

    } elseif ($action === 'done') {
        // Close the most recent open session
        $query = "UPDATE doctor_patient_duration 
                  SET check_out = '$now' 
                  WHERE appointment_id = '$appointmentId' 
                    AND check_out IS NULL 
                  ORDER BY id DESC 
                  LIMIT 1";
        mysqli_query($conn, $query);

        // Update visitor_status to 3 (done/completed)
        mysqli_query($conn, "UPDATE appointment_online
                             SET visitor_status = '3'
                             WHERE appoint_register_id = '$appointmentId' AND org_id='$SessionOrgId'");

        echo json_encode(['status' => 'ok', 'message' => 'Visit marked as completed']);

    } elseif ($action === 'lapsed') {
        // Mark appointment as no-show / cancelled
        mysqli_query($conn, "UPDATE appointment_online
                             SET visitor_status = '0'
                             WHERE appoint_register_id = '$appointmentId' AND org_id='$SessionOrgId'");

        echo json_encode(['status' => 'ok', 'message' => 'Patient marked as lapsed']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
