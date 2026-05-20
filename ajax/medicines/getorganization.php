<?php
	require_once ("../../config/functions.php");

    $results = [];

        $sql = mysqli_query($conn,"SELECT org_id,organization_name FROM organization WHERE status='1' ORDER BY  org_id ASC") or die(mysqli_eroror($conn));

	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}   

    echo json_encode($results); 
?>