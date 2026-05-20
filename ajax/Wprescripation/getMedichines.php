<?php
	require_once "../../config/functions.php";
							
	$SessionUserId = $_SESSION['security_id'] ?? '';
	$SessionRoleId = $_SESSION['role_id'] ?? '';
	$SessionOrgId = $_SESSION['org_id'] ?? '';

	$results = [];

	$addorgid= "AND org_id='$SessionOrgId'";
	$org_id = $_POST['org_id'];

	if($SessionUserId == "1"){
		$addorgid="AND org_id='$org_id'";
	}

	$sql = mysqli_query($conn,"SELECT DISTINCT medicine_id,medicine_name,scientific_name FROM medicines WHERE status='1' $addorgid  ORDER BY medicine_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>