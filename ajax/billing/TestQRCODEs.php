<?php
require_once("../../config/functions.php");


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];
$id = $_POST['id'];
// echo $id;
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppoint=mysqli_query($conn, "SELECT bill_id,appoint_register_id,appoint_unicode,patient_name FROM appointment_online WHERE appoint_status='1' AND appoint_id='$id'") or die(mysqli_error($conn));
} else{
    $getAppoint=mysqli_query($conn, "SELECT bill_id,appoint_register_id,appoint_unicode,patient_name FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_id='$id'") or die(mysqli_error($conn));
}
    while ($resAppoint=mysqli_fetch_object($getAppoint)){
        $result[] = $resAppoint;
    }
echo json_encode($result);


?>


