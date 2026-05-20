<?php
require_once("../../config/functions.php");


$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

// $appoint_id = $_POST['appointId'];
$patient_name = $_POST['patient_name'];
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppoint=mysqli_query($conn, "SELECT mobile_number FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name'") or die(mysqli_error($conn));
} else{
    $getAppoint=mysqli_query($conn, "SELECT mobile_number FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND modified_by='$SessionUserId' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
}
    while ($resAppoint=mysqli_fetch_object($getAppoint)){
        $result[] = $resAppoint;
    }
echo json_encode($result);


?>


