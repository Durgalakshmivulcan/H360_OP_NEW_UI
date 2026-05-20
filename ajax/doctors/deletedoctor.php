<?php
require_once("../../config/functions.php");
requireCan('delete', 'doctor.php', 'ajax'); // FIX_B_1810
$langDeleted = 0;

$delete_id = $_POST['doc_id'];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// fetch row before delete for audit log
$beforeQuery = mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id ='$delete_id' LIMIT 1");
$before      = null;
if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
    $before = mysqli_fetch_assoc($beforeQuery);
}

if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $qryDelete = mysqli_query($conn, "UPDATE doctors SET status='0' WHERE doc_id ='$delete_id'") or die(mysqli_error($conn));
    if ($qryDelete) {
        $langDeleted = 1;

        // audit log
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id='$delete_id'"));

        audit_log($conn, "Doctors", "delete", "doctors", $delete_id, $before, $after);
    }
} else {
    $qryDelete = mysqli_query($conn, "UPDATE doctors SET status='0' WHERE doc_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    if ($qryDelete) {
        $langDeleted = 1;

        // audit log
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id='$delete_id'"));

        audit_log($conn, "Doctors", "delete", "doctors", $delete_id, $before, $after);
    }
}

echo $langDeleted;
?>
