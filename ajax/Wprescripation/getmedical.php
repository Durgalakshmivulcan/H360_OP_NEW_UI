<?php
require_once "../../config/functions.php";
// $sql = mysqli_query($conn,"SELECT * FROM medicines");
// while($row=mysqli_fetch_array($sql))
// {
// echo '<option value="'.$row['medicine_id'].'">'.$row['medicine_name'].'</option>';
// } 

	
    $results = [];
	$sql = mysqli_query($conn,"SELECT medicine_id,medicine_name FROM medicines WHERE status='1' AND org_id='$SessionOrgId' ORDER BY medicine_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>
                    