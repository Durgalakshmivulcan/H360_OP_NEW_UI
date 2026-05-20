<?php
require_once("../../config/functions.php");


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];
$atmt_id = $_POST['atmt_id'];


if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppoint=mysqli_query($conn, "SELECT bill_id,appoint_register_id,appoint_unicode,patient_name FROM appointment_existing WHERE appoint_status='1' AND atmt_id='$atmt_id'") or die(mysqli_error($conn));
} else{
    $getAppoint=mysqli_query($conn, "SELECT bill_id,appoint_register_id,appoint_unicode,patient_name FROM appointment_existing WHERE appoint_status='1' AND org_id='$SessionOrgId' AND atmt_id='$atmt_id'") or die(mysqli_error($conn));
}

while ($resAppoint=mysqli_fetch_object($getAppoint)){
    $result[] = $resAppoint;
}

echo json_encode($result);


?>


