<?php
require_once "../../config/functions.php";


$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';
	
$results = [];
$sql = mysqli_query($conn,"SELECT * FROM dosage WHERE status='1'  AND modify_by='$SessionUserId' ORDER BY dosage_id ASC") or die(mysqli_eroror($conn));
while($row = mysqli_fetch_object($sql)){
	$results[] = $row;
}

echo json_encode($results);

?>