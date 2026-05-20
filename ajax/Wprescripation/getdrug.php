<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

$rx_group_id=$_POST['rx_get_id'];
$rx_id=$_POST['drug_id'];
// $machine=$_POST['machine'];
$result=[];

$getrx=mysqli_query($conn, "SELECT drug_id, drug_names FROM drug_name  WHERE status='1'  AND rx_get_id='$rx_get_id'") or die(mysqli_error($conn));
while ($resrx=mysqli_fetch_object($getrx)){
    $result[]=$resrx;
}

echo json_encode($result);
?>

