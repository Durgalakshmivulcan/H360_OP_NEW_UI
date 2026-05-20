<?php
require_once("../../config/functions.php");

// FIX_B_029: require login + scope every UPDATE to caller's org
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
assertOrgId();
$SessionOrgId = $_SESSION['org_id'];

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('edit', 'AppointmentOnline.php', 'ajax');



header('Content-Type: application/json');

$appoint_id = $_POST['appoint_id'] ?? '';
$newStatus  = $_POST['status'] ?? '';

$response = ['success' => false, 'message' => 'Invalid input'];

if (!empty($appoint_id) && $newStatus !== '') {
    $dateTimeNow = date('Y-m-d H:i:s'); // current date and time

    if ($newStatus === "2") {
        // Set check_in to current datetime
        $updateQuery = "UPDATE appointment_online 
                        SET visitor_status = '$newStatus', 
                            check_in = '$dateTimeNow'
                        WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'";
    } elseif ($newStatus === "0") {
        // Set check_out to current datetime
        $updateQuery = "UPDATE appointment_online 
                        SET visitor_status = '$newStatus', 
                            check_out = '$dateTimeNow'
                        WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'";
    } else {
        // Just update visitor_status for other statuses
        $updateQuery = "UPDATE appointment_online 
                        SET visitor_status = '$newStatus' 
                        WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'";
    }

    if (mysqli_query($conn, $updateQuery)) {
        $response = ['success' => true, 'message' => 'Status updated successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Database update failed: ' . mysqli_error($conn)];
    }
}

// Push message to socket

$fp = @fsockopen('127.0.0.1', 9000, $errno, $errstr, 2);
if (!$fp) {
    // FIX_B_007: never break the AJAX response when the WS daemon is down.
    error_log("[UpdateVisitorStatus] WS push connect failed: $errstr ($errno)");
    echo json_encode($response);
    return;
}
$message = json_encode([
    'type' => 'patients',
    'user' => 'pushmsg',
    'message' => 'new patient!'
]);
fwrite($fp, $message);
fclose($fp);

echo json_encode($response);
exit;
?>
