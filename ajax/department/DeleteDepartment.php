<?php
require_once("../../config/functions.php");
requireCan('delete', 'department.php', 'ajax'); // FIX_B_1810
$langDeleted = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$delete_id = $_POST['dept_id'];

// fetch the row before deletion
$beforeQuery = mysqli_query($conn, "SELECT * FROM department WHERE dept_id ='$delete_id' LIMIT 1");
$beforeRow   = null;
if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
    $beforeRow = mysqli_fetch_assoc($beforeQuery);
}

if($SessionUserId == "1" && $SessionRoleId == "1"){
    $qryDelete = mysqli_query($conn,"UPDATE department SET status='0' WHERE dept_id ='$delete_id'") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
        // audit log the delete action with old data
        audit_log($conn, "Department", "delete", "department", $delete_id, $beforeRow, null);
    }
} else{
    $qryDelete = mysqli_query($conn,"UPDATE department SET status='0' WHERE org_id='$SessionOrgId' AND dept_id ='$delete_id'") or die(mysqli_error($conn));
    if($qryDelete) {
        $langDeleted = 1;
        // audit log the delete action with old data
    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM department WHERE dept_id='$delete_id'"));

     audit_log($conn, "Department", "delete", "department", $delete_id, $beforeRow, $after);
    }
}

echo $langDeleted;
?>
