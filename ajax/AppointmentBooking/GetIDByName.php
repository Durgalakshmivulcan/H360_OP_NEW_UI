<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$doctor_name = $_POST['doctor_name'];

$result = [];

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $get = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE status='1' AND doc_id='$doctor_name'") or die(mysqli_error($conn));
} else{
    $get = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE status='1' AND doc_id='$doctor_name' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
}

while ($res = mysqli_fetch_object($get)) {
    $result[] = array(
        "doctorName_registrationNumber" => $res->doctor_name
    );
}

echo json_encode($result);
?>

