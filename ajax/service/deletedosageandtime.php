<?php
require_once("../../config/functions.php");
requireCan('delete', 'services.php', 'ajax'); // FIX_B_1810

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$langDeleted = 0;

$delete_id = $_POST['doseandtime_id'];

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $qryDelete = mysqli_query($conn,"UPDATE dosageandtime SET status='0' WHERE doseandtime_id ='$delete_id' ") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
    }
} else{
    $qryDelete = mysqli_query($conn,"UPDATE dosageandtime SET status='0' WHERE doseandtime_id ='$delete_id' AND org_id='$SessionOrgId' ") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
    }
}

echo $langDeleted;
