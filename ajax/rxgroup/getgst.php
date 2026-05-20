<?php
require_once "../../config/functions.php";
	
    $result = [];
	$sql = mysqli_query($conn,"SELECT tax_id,percentage FROM taxes WHERE status='1' ORDER BY tax_id ASC") or die(mysqli_eroror($conn));
	while($row = mysqli_fetch_object($sql)){
		$result[] = $row;
	}

    echo json_encode($result);

?>