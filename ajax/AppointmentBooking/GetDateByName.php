<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
$appoint_date  = $_POST['appoint_date'] ?? date('Y-m-d');
$result = [];

$SessionUserIdEsc = $conn->real_escape_string($SessionUserId);
$SessionOrgIdEsc = $conn->real_escape_string($SessionOrgId);

$sql = "SELECT security_type FROM security 
        WHERE status='1' 
        AND security_id='$SessionUserIdEsc' 
        AND org_id='$SessionOrgIdEsc'";

$sec_info = $conn->query($sql)->fetch_assoc();
// SA_FATAL_FIXED_B_556: SA also gets admin scope
$isAdmin = isset($sec_info['security_type']) && ($sec_info['security_type'] === 'A' || $sec_info['security_type'] === 'SA');

$sql = "SELECT doc_id, doctor_name FROM doctors 
        WHERE status='1' AND security_id='$SessionUserIdEsc'";
$doctor_info = $conn->query($sql)->fetch_assoc();

$sql = "SELECT doc_id FROM receptionnist 
        WHERE status='1' AND security_id='$SessionUserIdEsc'";
$res = $conn->query($sql);
$receptionist_docs = [];
while ($row = $res->fetch_assoc()) {
    $receptionist_docs[] = $row['doc_id'];
}

$appoint_dateEsc = $conn->real_escape_string($appoint_date);
if ($isAdmin) {
    $sql = "SELECT DISTINCT doctorName_registrationNumber 
            FROM doctors_time_slot 
            WHERE status='1' 
            AND available_date='$appoint_dateEsc' 
            AND org_id='$SessionOrgIdEsc'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $result[] = [
            "doctor_name" => getDoctorById($conn, $row['doctorName_registrationNumber']),
            "doctorName_registrationNumber" => $row['doctorName_registrationNumber'],
            "readonly" => false,
            "role" => "admin"
        ];
    }

} elseif ($doctor_info) {
    $doc_idEsc = $conn->real_escape_string($doctor_info['doc_id']);
    $sql = "SELECT 1 FROM doctors_time_slot 
            WHERE status='1' 
            AND available_date='$appoint_dateEsc' 
            AND org_id='$SessionOrgIdEsc' 
            AND doctorName_registrationNumber='$doc_idEsc' LIMIT 1";
    $slot_check = $conn->query($sql)->fetch_assoc();

    if ($slot_check) {
        $result[] = [
            "doctor_name" => $doctor_info['doctor_name'],
            "doctorName_registrationNumber" => $doctor_info['doc_id'],
            "readonly" => true,
            "role" => "doctor"
        ];
    }

} elseif (!empty($receptionist_docs)) {
    $in = implode(',', array_map('intval', $receptionist_docs));
    $sql = "SELECT DISTINCT doctorName_registrationNumber 
            FROM doctors_time_slot 
            WHERE status='1' 
            AND available_date='$appoint_dateEsc' 
            AND org_id='$SessionOrgIdEsc' 
            AND doctorName_registrationNumber IN ($in)";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $result[] = [
            "doctor_name" => getDoctorById($conn, $row['doctorName_registrationNumber']),
            "doctorName_registrationNumber" => $row['doctorName_registrationNumber'],
            "readonly" => false,
            "role" => "receptionist"
        ];
    }
}

echo json_encode($result);
?>