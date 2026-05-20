<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$doctors_time_id = $_POST['doctors_time_id'];
$mobile = trim($_POST['mobile'] ?? ''); 
$result = [];

if (empty($mobile)) {
    if ($SessionUserId == "1") {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    } else {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND org_id = '$SessionOrgId' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    }

    while ($resDoctorFee = mysqli_fetch_object($getDoctorFee)) {
        $result = $resDoctorFee->doctor_fee;
    }

    echo json_encode($result);
    exit;
}

$checkDateQuery = "SELECT valid_to, appointment_status 
                   FROM appointment_online 
                   WHERE mobile_number = '$mobile' 
                   AND doctor_name = '$doctors_time_id' 
                   ORDER BY appoint_id DESC 
                   LIMIT 1";

$checkDateResult = mysqli_query($conn, $checkDateQuery) or die(mysqli_error($conn));
$data = mysqli_fetch_assoc($checkDateResult);
$appointment_status = $data['appointment_status'];
$valid_to = $data['valid_to'];
// echo 'status:'.$appointment_status;


if ($appointment_status == '1' && strtotime($valid_to) >= strtotime(date("Y-m-d"))) {
    $result = 0;
    echo json_encode($result);
    exit;
} elseif ($appointment_status == 0 && strtotime($valid_to) >= strtotime(date("Y-m-d"))) {
    // Charge the actual doctor fee
    if ($SessionUserId == "1") {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    } else {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND org_id = '$SessionOrgId' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    }

    while ($resDoctorFee = mysqli_fetch_object($getDoctorFee)) {
        $result = $resDoctorFee->doctor_fee;
    }

    echo json_encode($result);
    exit;
} elseif (($appointment_status == 0 || $appointment_status == 1) && strtotime($valid_to) < strtotime(date("Y-m-d"))) {
    // Appointment expired, get doctor fee
    if ($SessionUserId == "1") {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    } else {
        $getDoctorFee = mysqli_query($conn, "SELECT * FROM doctors WHERE status = '1' AND org_id = '$SessionOrgId' AND doc_id = '$doctors_time_id'") or die(mysqli_error($conn));
    }

    while($resDoctorFee = mysqli_fetch_object($getDoctorFee)) {
        $result = $resDoctorFee->doctor_fee;
    }

    echo json_encode($result);
    exit;
}

// 🔴 If no condition matched, return 0
$result = 0;
echo json_encode($result);
?>
