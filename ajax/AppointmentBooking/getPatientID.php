<?php
require_once("../../config/functions.php");


$result = [];

// $appoint_id = $_POST['appointId'];
$patient_name = $_POST['patient_name'];
$patient_number = $_POST['patient_number'];


// if($patient_name != "") {

    $getAppoint=mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND patient_name='$patient_name' AND mobile_number='$patient_number'") or die(mysqli_error($conn));
    $resAppoint=mysqli_fetch_object($getAppoint);
        $result[] = $resAppoint;
    // }
// }
echo json_encode($result);

// $getAppoint=mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND mobile_number='$patient_number'") or die(mysqli_error($conn));
// while ($resAppoint=mysqli_fetch_object($getAppoint)){
//     $result=$resAppoint->appoint_unicode;
// }
// echo $result;

?>

