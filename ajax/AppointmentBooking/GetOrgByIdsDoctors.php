<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$org_id = $_POST['org_id'];
$appoint_date = $_POST['appoint_date'];

$GetOrgIdsByDoctor = mysqli_query($conn, "SELECT DISTINCT(doctorName_registrationNumber) FROM doctors_time_slot WHERE status='1' AND org_id='$org_id' AND available_date='$appoint_date'") or die(mysqli_error($conn));
while($GetDoctor = mysqli_fetch_object($GetOrgIdsByDoctor)){
    $result[] = array(
        "doctor_name" => getDoctorById($conn, $GetDoctor->doctorName_registrationNumber),
        "doctorName_registrationNumber" => $GetDoctor->doctorName_registrationNumber
    );
}


echo json_encode($result);
?>