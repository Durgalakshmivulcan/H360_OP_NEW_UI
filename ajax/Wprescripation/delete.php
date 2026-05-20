<?php
// IDOR_FIXED B-563
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('delete', 'prescription.php', 'ajax');
$SessionOrgId = $_SESSION['org_id'] ?? '';
$langDeleted = 0;

$delete_id = $_POST['prescription_id'];
$qryDelete = mysqli_query($conn,"UPDATE prescripition SET status='0' WHERE prescription_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
$qryDelete2 = mysqli_query($conn,"UPDATE prescription_medicines SET status='0' WHERE prescription_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
if($qryDelete&$qryDelete2) {
    $langDeleted = 1;
}

echo $langDeleted;
