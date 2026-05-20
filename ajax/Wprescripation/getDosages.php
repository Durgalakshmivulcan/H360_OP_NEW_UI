
<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT dosage_id, dosages FROM dosage WHERE status='1' ORDER BY dosage_id ASC") or die(mysqli_error($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>