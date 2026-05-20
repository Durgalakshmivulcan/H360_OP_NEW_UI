<?php
// IDOR_FIXED B-567
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('delete', 'prescription.php', 'ajax');
$SessionOrgId = $_SESSION['org_id'] ?? '';
$langDeleted = 0;

$delete_id = $_POST['medicine_id'];
// FIX_B_1860: previously UPDATEd `inp_vitals` (an inpatient table that
// doesn't exist on this OP-only deployment) → fatal "Table 'h360_op.inp_vitals'
// doesn't exist" on every actual delete attempt. Switched to the canonical
// Rx-medicine table `prescription_medicines` (matches sibling delete.php).
$qryDelete = mysqli_query($conn,"UPDATE prescription_medicines SET status='0' WHERE medicine_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
if($qryDelete) {
    $langDeleted = 1;
}

echo $langDeleted;
