<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

	
$results = [];
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $sql = mysqli_query($conn,"SELECT rx_group_id,notes FROM rx_groups WHERE status='1' ORDER BY rx_group_id ASC") or die(mysqli_error($conn));
} else{
    $sql = mysqli_query($conn,"SELECT rx_group_id,notes FROM rx_groups WHERE status='1' AND org_id='$SessionOrgId' ORDER BY rx_group_id ASC") or die(mysqli_error($conn));
}
while($row = mysqli_fetch_object($sql)){
    $results[] = $row;
}

echo json_encode($results);


?>
