<?php
	require_once "../../config/functions.php";
	
    $test_name=$_POST['test_name'];

    $results = [];
	$sql = mysqli_query($conn,"SELECT test_id, test_name FROM tests WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_id='$test_name'") or die(mysqli_error($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row->test_name;
	}

    echo json_encode($results);
?>

