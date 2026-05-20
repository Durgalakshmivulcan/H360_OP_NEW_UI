<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$RXGroup = $_POST['RXGroup'];

if($RXGroup != "") {
    if($SessionUserId == "1" && $SessionRoleId=="1"){
        $getRx=mysqli_query($conn, "SELECT rx_id, rx_group_id, rx_group_name, medicine_name, medicine_type, dosage, unit, timing, in_time_period, frequency, duration, quantity, notes FROM rx_groups WHERE status='1' AND rx_group_id='$RXGroup'") or die(mysqli_error($conn));
    } else{
        $getRx=mysqli_query($conn, "SELECT rx_id, rx_group_id, rx_group_name, medicine_name, medicine_type, dosage, unit, timing, in_time_period, frequency, duration, quantity, notes FROM rx_groups WHERE status='1' AND org_id='$SessionOrgId' AND rx_group_id='$RXGroup'") or die(mysqli_error($conn));
    }
    while ($resRx=mysqli_fetch_object($getRx)){
        $result[] = $resRx;
    }
}
echo json_encode($result);

?>


