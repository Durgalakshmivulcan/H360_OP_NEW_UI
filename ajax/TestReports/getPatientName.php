<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];
$patient_name = $_POST['patient_name'];
// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
if($SessionUserId == "1"){
    $getAppoint = mysqli_query($conn, "SELECT DISTINCT(mobile_number) FROM appointment_online WHERE appoint_status='1' AND patient_name='$patient_name' $docScope") or die(mysqli_error($conn));

}else{
    $getAppoint = mysqli_query($conn, "SELECT DISTINCT(mobile_number) FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND patient_name='$patient_name' $docScope") or die(mysqli_error($conn));
}

while ($resAppoint = mysqli_fetch_object($getAppoint)) {
    $appointId = $resAppoint->appoint_register_id;

    $result[] = array(
        // 'appoint_register_id' => $resAppoint->appoint_register_id,
        // 'appoint_unicode' => $resAppoint->appoint_unicode,
        // 'org_id' => $resAppoint->org_id,
        'mobile_number' => $resAppoint->mobile_number,
        // 'org_name' => getUserNameByOrgId($conn, $resAppoint->org_id)
    );
}

echo json_encode($result);
?>
