<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];
$orgid = $_POST['orgid'];
if($SessionUserId == "1"){
    
    $getnames = mysqli_query($conn, "SELECT DISTINCT(mobile_number),mobile_number, appoint_id, patient_name FROM appointment_online WHERE appoint_status='1' AND org_id='$orgid'") or die(mysqli_error($conn));
} else {
    
    $getnames = mysqli_query($conn, "SELECT DISTINCT(mobile_number),mobile_number, appoint_id, patient_name FROM appointment_online WHERE appoint_status='1' AND org_id='$orgid'") or die(mysqli_error($conn));
}


while($row = mysqli_fetch_assoc($getnames)) {
    $result[] = $row;
}


echo json_encode($result);

?>

