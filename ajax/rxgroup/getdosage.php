<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT doseandtime_id,dose_schedule FROM dosageandtime WHERE status='1' ORDER BY doseandtime_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>