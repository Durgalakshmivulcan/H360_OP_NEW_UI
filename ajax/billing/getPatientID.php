<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

// $appoint_id = $_POST['appointId'];
$patient_name = $_POST['patient_name'];
$patient_number = $_POST['patient_number'];


if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppoint=mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND mobile_number='$patient_number'") or die(mysqli_error($conn));
    $resAppoint=mysqli_fetch_object($getAppoint);
        $result[] = $resAppoint;
} else{
    $getAppoint=mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND mobile_number='$patient_number'AND modified_by='$SessionUserId' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    $resAppoint=mysqli_fetch_object($getAppoint);
        $result[] = $resAppoint;
}
// }
echo json_encode($result);

?>

