<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$results = [];
$sql = mysqli_query($conn,"SELECT rx_group_id,quantity FROM rx_groups WHERE status='1' AND org_id='$SessionOrgId' ORDER BY rx_group_id ASC") or die(mysqli_error($conn));
while($row = mysqli_fetch_object($sql)){
    $results[] = $row;
}

echo json_encode($results);


?>
