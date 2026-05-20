<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
$msg = 0;

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$doctor = $_POST['doctor'];

$checkPatientExists = mysqli_query($conn, "SELECT * FROM appointment_online WHERE mobile_number = '$mobile' AND patient_name='$name' AND doctor_name='$doctor' AND org_id='$SessionOrgId' ORDER BY appoint_id DESC LIMIT 1") or die(mysqli_error($conn));

$rows = mysqli_num_rows($checkPatientExists);

if ($rows > 0) {    
    $row = mysqli_fetch_assoc($checkPatientExists);
    
    // You can echo whatever field you want from $row
    // Example: echoing the entire row as JSON
    echo json_encode($row);
} else {
    echo 1;
}
?>
