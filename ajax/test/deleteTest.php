<?php
require_once("../../config/functions.php");
requireCan('delete', 'test.php', 'ajax'); // FIX_B_1810
$langDeleted = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$delete_id = $_POST['test_id'];

$addorgid= "AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
$addorgid="";
}
    $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tests WHERE test_id='$delete_id' $addorgid"));
    $qryDelete = mysqli_query($conn,"UPDATE tests SET status='0' WHERE test_id ='$delete_id' $addorgid") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tests WHERE test_id='$delete_id'"));

    
    audit_log($conn, "Tests", "delete", "tests", $delete_id, $before, $after);
    }


echo $langDeleted;
?>