<?php
require_once "../../config/functions.php";
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$results = [];

$organizations = $_POST['org_id'];

if (isset($_POST['appointment_id'])) {

    $org_id = ($SessionUserId == "1") ? $organizations : $SessionOrgId;
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);

    $query = "SELECT * FROM appointment_online 
              WHERE appoint_register_id='$appointment_id' AND org_id='$org_id' AND appoint_status='1'
              ORDER BY appoint_id DESC LIMIT 1";
    error_log("Executing Query: " . $query);

    $sql = mysqli_query($conn, $query);

    if (!$sql) {
        die(json_encode(["error" => mysqli_error($conn)]));
    }

    $appointment = mysqli_fetch_object($sql);

    if ($appointment) {
        $results['appointment'] = $appointment;

        $prescriptionQuery = "SELECT * FROM prescripition 
                              WHERE appoint_register_id	='$appointment_id' AND status='1'
                              AND org_id='$org_id' 
                              ORDER BY prescription_id DESC LIMIT 1";

        $prescriptionSql = mysqli_query($conn, $prescriptionQuery);

        if ($prescriptionSql && mysqli_num_rows($prescriptionSql) > 0) {
            $results['prescription'] = mysqli_fetch_object($prescriptionSql);
        } else {
            $results['prescription'] = null; 
        }
    } else {
        $results['error'] = "No appointment found.";
    }

} else {
    error_log("appointment_id is missing");
    echo json_encode(["error" => "No appointment_id provided"]);
    exit;
}

echo json_encode($results);
?>
