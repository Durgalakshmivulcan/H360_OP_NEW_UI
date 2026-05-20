<?php
	require_once ("../../config/functions.php");

    $results = [];

        $sql = mysqli_query($conn,"SELECT type_id,type_name FROM madicine_type WHERE status='1' ORDER BY  type_id ASC") or die(mysqli_eroror($conn));

	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results); 
?>