<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('delete', 'AppointmentOnline.php', 'ajax');


$langDeleted = 0;

$appoint_id= $_POST['appoint_id'];
$before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$appoint_id'"));
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $AppointmentQryDelete = mysqli_query($conn,"UPDATE appointment_online SET appoint_status='0' WHERE appoint_id  ='$appoint_id'") or die(mysqli_error($conn));
    if($AppointmentQryDelete) {
        $langDeleted = 1;
    }
} else{
    $AppointmentQryDelete = mysqli_query($conn,"UPDATE appointment_online SET appoint_status='0' WHERE appoint_id  ='$appoint_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    if($AppointmentQryDelete) {
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$appoint_id'"));

        
        audit_log($conn, "Appointments", "delete", "appointment_online", $appoint_id, $before, $after);
        $langDeleted = 1;
    }
}

echo $langDeleted;
?>