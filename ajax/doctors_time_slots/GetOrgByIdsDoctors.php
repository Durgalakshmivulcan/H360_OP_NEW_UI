<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$org_id = $_POST['org_id'];

$getMenus = mysqli_query($conn,"SELECT  * FROM doctors  WHERE status='1' AND org_id='$org_id' ORDER BY doc_id DESC") or die(mysqli_error($conn));

while ($resMenus = mysqli_fetch_object($getMenus)) {


    $result[] = array(
        "doctor_id" => $resMenus->doc_id,
        "doctor_name" => $resMenus->doctor_name,
        "doctor_register" => $resMenus->doc_registration_number
    );

}

echo json_encode($result);
?>