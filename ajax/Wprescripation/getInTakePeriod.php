
<?php
	require_once "../../config/functions.php";
	
    $results = [];
	$sql = mysqli_query($conn,"SELECT intake_id, intake_name FROM in_take_period WHERE status='1' ORDER BY intake_id ASC") or die(mysqli_error($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>