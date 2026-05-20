<?php
require_once("../../config/functions.php");
	
$results = [];
$sql = mysqli_query($conn,"SELECT rx_group_id,duration FROM rx_groups WHERE status='1' ORDER BY rx_group_id ASC") or die(mysqli_eroror($conn));
while($row = mysqli_fetch_object($sql)){
    $results[] = $row;
}

echo json_encode($results);


?>
