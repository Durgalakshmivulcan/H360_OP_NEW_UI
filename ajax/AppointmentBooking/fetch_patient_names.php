<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
$orgId = isset($_GET['org_id']) && !empty($_GET['org_id']) ? $_GET['org_id'] : $SessionOrgId;

$patients = [];

// $query = mysqli_query($conn, "SELECT * FROM appointment_online GROUP BY appoint_register_id");
$query = mysqli_query($conn, "SELECT DISTINCT patient_name, mobile_number FROM appointment_online WHERE patient_name IS NOT NULL AND mobile_number IS NOT NULL AND org_id='$orgId'");

while ($row = mysqli_fetch_assoc($query)) {
    $patients[] = [
        'name' => $row['patient_name'],
        'mobile' => $row['mobile_number']
    ];
}

echo json_encode($patients);
?>
