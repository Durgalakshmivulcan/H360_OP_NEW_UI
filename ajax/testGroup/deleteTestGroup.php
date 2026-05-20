<?php
require_once("../../config/functions.php");
requireCan('delete', 'testGroup.php', 'ajax'); // FIX_B_1810
$langDeleted = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$delete_id=$_POST['test_group_id'];
$before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$delete_id'"));
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $qryDelete2 = mysqli_query($conn,"UPDATE test_group SET status='0' WHERE test_group_id ='$delete_id'") or die(mysqli_error($conn));
    if($qryDelete2) {
        $langDeleted = 1;
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$delete_id'"));
        audit_log($conn, "Test Group", "delete", "test_group", $delete_id, $before, null);
    }
} else{
    $qryDelete2 = mysqli_query($conn,"UPDATE test_group SET status='0' WHERE org_id='$SessionOrgId' AND test_group_id ='$delete_id'") or die(mysqli_error($conn));
    if($qryDelete2) {
        $langDeleted = 1;
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$delete_id'"));
        audit_log($conn, "Test Group", "delete", "test_group", $delete_id, $before, null);
    }
}

echo $langDeleted;
