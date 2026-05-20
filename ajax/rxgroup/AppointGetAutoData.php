<?php
require_once("../../config/functions.php");


$result = [];

// $appoint_id = $_POST['appointId'];
$patient_name = $_POST['patient_name'];


// if($patient_name != "") {

    $getAppoint=mysqli_query($conn, "SELECT mobile_number FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name'") or die(mysqli_error($conn));
    while ($resAppoint=mysqli_fetch_object($getAppoint)){
        $result[] = $resAppoint;
    }
// }
echo json_encode($result);

// $getAppoint=mysqli_query($conn, "SELECT mobile_number FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name'") or die(mysqli_error($conn));
// while ($resAppoint=mysqli_fetch_object($getAppoint)){
//     $result=$resAppoint->mobile_number;
// }
// // }
// echo $result;

?>


