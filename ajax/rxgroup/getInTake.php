<?php

require_once "../../config/functions.php";


$result=[];
$getfreq=mysqli_query($conn, "SELECT intake_id,intake_name FROM in_take_period  WHERE status='1' ") or die(mysqli_error($conn));
while($getfrequen=mysqli_fetch_object($getfreq)){
    $result[]=$getfrequen;
}

echo json_encode($result);

?>