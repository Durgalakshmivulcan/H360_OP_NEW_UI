<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$patient_number = $_POST['patient_number'];

if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $getAppoint = mysqli_query($conn, "
        SELECT DISTINCT appoint_unicode, patient_name, appoint_register_id, age, gender, dob
        FROM appointment_online 
        WHERE appoint_status='1' AND mobile_number='$patient_number'
    ") or die(mysqli_error($conn));
} else {
    $getAppoint = mysqli_query($conn, "
        SELECT DISTINCT appoint_unicode, patient_name, appoint_register_id, age, gender, dob
        FROM appointment_online 
        WHERE appoint_status='1' AND org_id='$SessionOrgId' AND mobile_number='$patient_number'
    ") or die(mysqli_error($conn));
}

while ($resAppoint = mysqli_fetch_object($getAppoint)) {
    $result[] = array(
        'appoint_unicode'     => $resAppoint->appoint_unicode,
        'patient_name'        => $resAppoint->patient_name,
        'appoint_register_id' => $resAppoint->appoint_register_id,
        'age'                 => $resAppoint->age,
        'gender'              => $resAppoint->gender,
        'dob'                 => $resAppoint->dob
    );
}

echo json_encode($result);
?>