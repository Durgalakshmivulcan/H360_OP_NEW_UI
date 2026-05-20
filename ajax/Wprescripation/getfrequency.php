<?php

require_once "../../config/functions.php";


$result=[];
$getfreq=mysqli_query($conn, "SELECT freq_id,freq_name FROM frequencies WHERE status='1' ") or die(mysqli_error($conn));
while($getfrequen=mysqli_fetch_object($getfreq)){

    $result[]=$getfrequen;  
}

echo json_encode($result);
?>