

<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT time_id,time FROM times WHERE status='1' ORDER BY time_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>