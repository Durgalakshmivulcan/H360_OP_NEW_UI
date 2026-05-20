<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
$currentDate   = date('Y-m-d');

$result = [];

$patient_name = $_POST['patient_name'];

if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $getAppoint = mysqli_query($conn, "
        SELECT DISTINCT appoint_unicode, mobile_number, appoint_register_id, age, gender, dob
        FROM appointment_online 
        WHERE appoint_status='1' 
          AND appoint_date = '$currentDate' 
          AND patient_name='$patient_name' 
        ORDER BY appoint_register_id DESC
    ") or die(mysqli_error($conn));
} else {
    $getAppoint = mysqli_query($conn, "
        SELECT DISTINCT appoint_unicode, mobile_number, appoint_register_id, age, gender, dob
        FROM appointment_online 
        WHERE appoint_status='1' 
          AND appoint_date = '$currentDate' 
          AND org_id='$SessionOrgId' 
          AND patient_name='$patient_name' 
        ORDER BY appoint_register_id DESC
    ") or die(mysqli_error($conn));
}

while ($resAppoint = mysqli_fetch_object($getAppoint)) {
    $result[] = array(
        'appoint_unicode'     => $resAppoint->appoint_unicode,
        'mobile_number'       => $resAppoint->mobile_number,
        'appoint_register_id' => $resAppoint->appoint_register_id,
        'age'                 => $resAppoint->age,
        'gender'              => $resAppoint->gender,
        'dob'                 => $resAppoint->dob
    );
}

echo json_encode($result);
?>