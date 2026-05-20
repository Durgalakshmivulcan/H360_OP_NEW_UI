<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT route_id, routes FROM route WHERE status='1' ORDER BY route_id ASC") or die(mysqli_error($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>