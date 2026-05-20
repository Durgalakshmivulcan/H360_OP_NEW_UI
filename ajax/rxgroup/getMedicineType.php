<?php
	require_once "../../config/functions.php";
	
	$SessionUserId = $_SESSION['security_id'] ?? '';
	$SessionRoleId = $_SESSION['role_id'] ?? '';
	$SessionOrgId = $_SESSION['org_id'] ?? '';


    $results = [];


	$sql = mysqli_query($conn,"SELECT type_id,type_name FROM madicine_type WHERE status='1'") or die(mysqli_eroror($conn));

	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>