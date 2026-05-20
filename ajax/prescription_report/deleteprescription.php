<?php
// IDOR_FIXED B-572
require_once("../../config/functions.php"); // FIX_B_1840 — brings config.php + session_start + RBAC helpers
// FIX_B_1840 — RBAC: per-action AJAX gate (delete) on prescriptionreports.php.
requireCan('delete', 'prescriptionreports.php', 'ajax');
$SessionOrgId = $_SESSION['org_id'] ?? '';
$langDeleted = 0;

$delete_id = $_POST['prescription_id'];
$qryDelete = mysqli_query($conn,"UPDATE prescripition SET status='0' WHERE prescription_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
if($qryDelete) {
    $langDeleted = 1;
}

echo $langDeleted;
?>