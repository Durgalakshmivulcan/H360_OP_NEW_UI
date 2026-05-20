<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$patient_name = $_POST['patient_name'];
$patient_number = $_POST['patient_number'];
// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
if($SessionUserId == "1" && $SessionRoleId == "1"){
    $getAppoint = mysqli_query($conn, "SELECT DISTINCT(appoint_unicode) FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' AND mobile_number='$patient_number' $docScope") or die(mysqli_error($conn));

}else{
    $getAppoint = mysqli_query($conn, "SELECT DISTINCT(appoint_unicode) FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND patient_name='$patient_name' AND mobile_number='$patient_number' $docScope") or die(mysqli_error($conn));

}

$uniqueAppointIds = array(); // Create an array to store unique appoint_register_ids

while ($resAppoint = mysqli_fetch_object($getAppoint)) {
    $result[] = array(
        // 'appoint_register_id' => $resAppoint->appoint_register_id,
        'appoint_unicode' => $resAppoint->appoint_unicode,
        // 'org_id' => $resAppoint->org_id,
        // 'org_name' => getUserNameByOrgId($conn, $resAppoint->org_id)
    );
}

echo json_encode($result);
?>
