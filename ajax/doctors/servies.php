<?php
	require_once "../../config/functions.php";
	
    // $service_id=$_POST['doc_services'];
// echo $service_id;
    $results = [];
	$sql = mysqli_query($conn,"SELECT service_id, service_name FROM services WHERE status='1' ORDER BY service_id") or die(mysqli_error($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row->service_name;
	}

    echo json_encode($results);
?>

