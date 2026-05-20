<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$existing_appoint_date = $_POST['existing_appoint_date'];
$org_id = $_POST['org_id'];

$result = [];

if($SessionUserId == "1"){
    $get = mysqli_query($conn, "SELECT DISTINCT(doctorName_registrationNumber) FROM doctors_time_slot WHERE status='1' AND available_date='$existing_appoint_date' AND org_id='$org_id'") or die(mysqli_error($conn));
} else{
    $get = mysqli_query($conn, "SELECT DISTINCT(doctorName_registrationNumber) FROM doctors_time_slot WHERE status='1' AND available_date='$existing_appoint_date' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
}
while ($res = mysqli_fetch_object($get)) {
    $result[] = array(
        "doctor_name" => getDoctorById($conn, $res->doctorName_registrationNumber),
        "doctorName_registrationNumber" => $res->doctorName_registrationNumber
    );
}

echo json_encode($result);
?>

