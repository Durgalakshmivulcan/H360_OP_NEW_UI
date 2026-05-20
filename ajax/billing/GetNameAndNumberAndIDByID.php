<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$patient_name = $_POST['patient_name'];
$patient_number = $_POST['patient_number'];
$appoint_unicode = $_POST['appoint_unicode'];

if($SessionUserId == "1" && $SessionRoleId == "1"){
    $getAppoint=mysqli_query($conn, "SELECT appoint_register_id, bill_id FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND mobile_number='$patient_number' AND appoint_unicode='$appoint_unicode'") or die(mysqli_error($conn));
}else{
    $getAppoint=mysqli_query($conn, "SELECT appoint_register_id, bill_id FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND patient_name='$patient_name' AND mobile_number='$patient_number' AND appoint_unicode='$appoint_unicode'") or die(mysqli_error($conn));
}
    while ($resAppoint=mysqli_fetch_object($getAppoint)){
        $result[] = $resAppoint;
    }
echo json_encode($result);


?>
