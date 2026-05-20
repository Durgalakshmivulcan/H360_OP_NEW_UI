

<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT unit_id,unit_name FROM units WHERE status='1' ORDER BY unit_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>