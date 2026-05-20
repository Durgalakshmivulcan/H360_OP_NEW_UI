<?php

require_once "../../config/functions.php";


$result=[];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getfreq=mysqli_query($conn, "SELECT rx_id,duration FROM rx_groups WHERE status='1'") or die(mysqli_error($conn));
} else{
    $getfreq=mysqli_query($conn, "SELECT rx_id,duration FROM rx_groups WHERE status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
}
while($getfrequen=mysqli_fetch_object($getfreq)){

    $result[]=$getfrequen;  
}

echo json_encode($result);
?>