<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if(isset($_POST['org_id'])) {
    $orgId = $_POST['org_id'];

    // FIX_B_1903: doctor-scope filter
    $docScope = currentDoctorScopeSql('doctor_name');
    $query1 = "SELECT appoint_id, appoint_register_id FROM appointment_online WHERE appoint_status='1' AND org_id='$orgId' $docScope";
    $query2 = "SELECT appoint_id, appoint_register_id FROM appointment_existing WHERE appoint_status='1' AND org_id='$orgId' $docScope";

    $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));
    $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));

    echo '<option value="">Select Appointment ID</option>';

    while ($row = mysqli_fetch_assoc($result1)) {
        echo "<option value='" . $row['appoint_id'] . "'>" . $row['appoint_register_id'] . "</option>";
    }
    while ($row = mysqli_fetch_assoc($result2)) {
        echo "<option value='" . $row['appoint_id'] . "'>" . $row['appoint_register_id'] . "</option>";
    }
}
?>
