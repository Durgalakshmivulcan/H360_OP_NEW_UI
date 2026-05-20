<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan('delete', 'patienthistory.php', 'ajax');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$langDeleted = 0;

$vital_id= $_POST['vital_id'];

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $VitalQryDelete = mysqli_query($conn,"UPDATE vitals SET status='0' WHERE vital_id  ='$vital_id'") or die(mysqli_error($conn));
    if($VitalQryDelete) {
        $langDeleted = 1;
    }
} else{
    $VitalQryDelete = mysqli_query($conn,"UPDATE vitals SET status='0' WHERE vital_id  ='$vital_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    if($VitalQryDelete) {
        $langDeleted = 1;
    }
}

echo $langDeleted;
?>