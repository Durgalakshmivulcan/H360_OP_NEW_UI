<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');



$result = [];

$patient_name = $_POST['patient_name'];


    $getAppoint=mysqli_query($conn, "SELECT appoint_register_id FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_unicode='$patient_name'") or die(mysqli_error($conn));
    while ($resAppoint=mysqli_fetch_object($getAppoint)){
        $result[] = $resAppoint;
    }
echo json_encode($result);


?>