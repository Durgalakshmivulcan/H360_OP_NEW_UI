<?php
require_once("../../config/functions.php");

// FIX_B_021: require login + scope every query to caller's org
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
assertOrgId();
$SessionOrgId = $_SESSION['org_id'];


$appoint_id = $_POST['appoint_id'];

// Get current visitor_status of the clicked appointment
$getCurrent = mysqli_query($conn, "SELECT visitor_status FROM appointment_online WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'");
$row = mysqli_fetch_assoc($getCurrent);
$currentStatus = $row['visitor_status'];

if ($currentStatus == 1) {
    // Step 1: If pending, make this one active
    mysqli_query($conn, "UPDATE appointment_online SET visitor_status = '2' WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'");

    // Step 2: Deactivate any previously active (in case of missed reset)
    mysqli_query($conn, "UPDATE appointment_online SET visitor_status = '1' WHERE appoint_id != '$appoint_id' AND visitor_status = '2' AND org_id='$SessionOrgId'");

    echo "activated";
} elseif ($currentStatus == 2) {
    // Step 3: If already active, mark it completed
    mysqli_query($conn, "UPDATE appointment_online SET visitor_status = '0' WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'");

    // Step 4: Activate next pending appointment
    // FIX_B_1903: doctor-scope filter so each doctor's queue advances independently
    $docScope = currentDoctorScopeSql('doctor_name');
    $getNext = mysqli_query($conn, "
        SELECT appoint_id FROM appointment_online
        WHERE appoint_status = '1' AND visitor_status = '1' AND appoint_id > '$appoint_id' AND org_id='$SessionOrgId' $docScope
        ORDER BY appoint_id ASC LIMIT 1
    ");

    if (mysqli_num_rows($getNext) > 0) {
        $next = mysqli_fetch_assoc($getNext);
        $nextId = $next['appoint_id'];
        mysqli_query($conn, "UPDATE appointment_online SET visitor_status = '2' WHERE appoint_id = '$nextId' AND org_id='$SessionOrgId'");
    }

    echo "completed";
} else {
    echo "already_completed";
}
?>
