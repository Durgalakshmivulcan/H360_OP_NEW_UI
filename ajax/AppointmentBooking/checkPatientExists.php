<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
$msg = 0;

$name = $_POST['name'];
$mobile = $_POST['mobile'];

$orgId = isset($_POST['org_id']) && !empty($_POST['org_id']) ? $_POST['org_id'] : $SessionOrgId;

$checkPatientExists = mysqli_query($conn, "SELECT * FROM appointment_online WHERE mobile_number = '$mobile' AND patient_name='$name' AND org_id='$orgId' ORDER BY appoint_id DESC LIMIT 1") or die(mysqli_error($conn));

$rows = mysqli_num_rows($checkPatientExists);

if ($rows > 0) {    
    $row = mysqli_fetch_assoc($checkPatientExists);
        
    echo json_encode($row);
} else {
    echo json_encode(1);
    exit;
}
?>
