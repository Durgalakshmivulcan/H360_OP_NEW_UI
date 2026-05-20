<?php
require_once("../../config/functions.php");
requireCan('delete', 'rxgroup.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$langDeleted = 0;
$delete_id = $_POST['rx_group_id'];

if ($delete_id != "") {

    // Fetch old data for audit
    $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE rx_group_id='$delete_id'"));

    if ($SessionUserId == "1" && $SessionRoleId == "1") {
        $qryDelete = mysqli_query($conn, "UPDATE rx_groups_names SET status='0', modify_by='$SessionUserId' WHERE rx_group_id='$delete_id'") or die(mysqli_error($conn));
    } else {
        $qryDelete = mysqli_query($conn, "UPDATE rx_groups_names SET status='0', modify_by='$SessionUserId' WHERE rx_group_id='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    }

    if ($qryDelete) {
        $langDeleted = 1;

        // Fetch after data for audit (status=0)
        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE rx_group_id='$delete_id'"));

        // Audit log
        audit_log($conn, "Rx Groups", "delete", "rx_groups_names", $delete_id, $before, $after);
    }
}

echo $langDeleted;
?>
