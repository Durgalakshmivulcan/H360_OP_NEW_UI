<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('delete', 'AppointmentOnline.php', 'ajax');


$langDeleted = 0;

$atmt_id= $_POST['atmt_id'];

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $AppointmentQryDelete = mysqli_query($conn,"UPDATE appointment_existing SET appoint_status='0' WHERE atmt_id  ='$atmt_id'") or die(mysqli_error($conn));
    if($AppointmentQryDelete) {
        $langDeleted = 1;
    }
} else{
    $AppointmentQryDelete = mysqli_query($conn,"UPDATE appointment_existing SET appoint_status='0' WHERE atmt_id  ='$atmt_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    if($AppointmentQryDelete) {
        $langDeleted = 1;
    }
}

echo $langDeleted;
?>