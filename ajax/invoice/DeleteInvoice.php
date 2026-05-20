<?php
// IDOR_FIXED B-574
require_once("../../config/functions.php");
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('delete', 'bill.php', 'ajax');

$langDeleted = 0;

$delete_id = $_POST['invoice_id'];
$qryDelete = mysqli_query($conn,"UPDATE invoice SET status='0' WHERE invoice_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
if($qryDelete) {
    $langDeleted = 1;
}

echo $langDeleted;
