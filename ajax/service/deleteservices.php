<?php
require_once("../../config/functions.php");
requireCan('delete', 'services.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$langDeleted = 0;

$delete_id = $_POST['service_id'];
$before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$delete_id'"));

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $qryDelete = mysqli_query($conn,"UPDATE services SET status='0' WHERE service_id ='$delete_id' ") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$delete_id'"));

         audit_log($conn, "Services", "delete", "services", $delete_id, $before, $after);
    }
} else{
    $qryDelete = mysqli_query($conn,"UPDATE services SET status='0' WHERE service_id ='$delete_id' AND org_id='$SessionOrgId' ") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$delete_id'"));

         audit_log($conn, "Services", "delete", "services", $delete_id, $before, $after);
    }
}

echo $langDeleted;
