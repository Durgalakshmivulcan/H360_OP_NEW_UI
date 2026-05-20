<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_online WHERE visitor_status='2' $docScope ORDER BY appoint_id ASC") or die(mysqli_error($conn));
    $resAppointmentData = mysqli_fetch_object($getAppointmentData);
} else{
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_online WHERE visitor_status='2' AND org_id='$SessionOrgId' $docScope ORDER BY appoint_id ASC") or die(mysqli_error($conn));
    $resAppointmentData = mysqli_fetch_object($getAppointmentData);
}
?>

<div class="card" >
    <div class="card-body" style="background-color: blue; color:white;">
        <p> <b><?=$resAppointmentData->appoint_unicode?></b> </p>
        <p> <b><?=$resAppointmentData->patient_name?></b> </p>
        <p> <b></b> </p>
    </div>
</div>
</div>
