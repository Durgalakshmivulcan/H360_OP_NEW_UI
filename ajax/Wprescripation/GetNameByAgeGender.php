<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$result = [];
$customValue = $_POST['customValue'];
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$uniqueCombinations = array();

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppointOnline = mysqli_query($conn, "SELECT mobile_number, appoint_unicode, appoint_register_id, gender, age, dob FROM appointment_online WHERE appoint_status='1' AND appoint_id='$customValue' AND appoint_date='$currentDate'") or die(mysqli_error($conn));

    $getAppointExisting = mysqli_query($conn, "SELECT mobile_number, appoint_unicode, appoint_register_id, gender, age, dob FROM appointment_existing WHERE appoint_status='1' AND appoint_id='$customValue' AND appoint_date='$currentDate'") or die(mysqli_error($conn));
} else{
    $getAppointOnline = mysqli_query($conn, "SELECT mobile_number, appoint_unicode, appoint_register_id, gender, age FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_id='$customValue' AND appoint_date='$currentDate'") or die(mysqli_error($conn));

    $getAppointExisting = mysqli_query($conn, "SELECT mobile_number, appoint_unicode, appoint_register_id, gender, age FROM appointment_existing WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_id='$customValue' AND appoint_date='$currentDate'") or die(mysqli_error($conn));
}

while ($resAppoint = mysqli_fetch_object($getAppointOnline)) {
    $combination = $resAppoint->gender . "_" . $resAppoint->age . "_" . $resAppoint->mobile_number . "_" . $resAppoint->appoint_unicode . "_" . $resAppoint->appoint_register_id;

    if (!in_array($combination, $uniqueCombinations)) {
        $uniqueCombinations[] = $combination; 
        $result[] = $resAppoint;
    }
}

while ($resAppoint = mysqli_fetch_object($getAppointExisting)) {
    $combination = $resAppoint->gender . "_" . $resAppoint->age . "_" . $resAppoint->mobile_number . "_" . $resAppoint->appoint_unicode . "_" . $resAppoint->appoint_register_id;

    if (!in_array($combination, $uniqueCombinations)) {
        $uniqueCombinations[] = $combination; 
        $result[] = $resAppoint;
    }
}

echo json_encode($result);

?>
