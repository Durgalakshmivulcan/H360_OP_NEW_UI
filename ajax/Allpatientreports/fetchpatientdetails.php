<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$orgId = isset($_GET['org_id']) && !empty($_GET['org_id']) ? $_GET['org_id'] : $SessionOrgId;

$checkDoctor  = mysqli_query($conn, "SELECT s.security_type, r.role_name FROM security s LEFT JOIN roles r ON r.role_id = s.role_id WHERE s.status='1' AND s.security_id = '$SessionUserId'");
$secRow       = mysqli_fetch_assoc($checkDoctor);
$securityType = $secRow['security_type'] ?? '';
$currentRole  = strtolower(trim($secRow['role_name'] ?? ''));

// ---- Doctors list ----
if ($securityType === 'SA' || $securityType === 'A' || $currentRole === 'pharmacist') {
    $orgFilter = !empty($orgId) ? "AND org_id='$orgId'" : "";
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' $orgFilter ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status = '1'
            AND (
                d.security_id = '$SessionUserId'
                OR d.doc_id IN (
                        SELECT r.doc_id
                        FROM receptionnist r
                        WHERE r.security_id = '$SessionUserId'
                )
            )
            ORDER BY d.doctor_name ASC";
} else {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' AND 0";
}

$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

$doctors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $doctors[] = $row;
}

// --- Build doctor condition for appointments ---
$doctorIds = array_column($doctors, 'doc_id');
$doctorIdsStr = implode(',', array_map('intval', $doctorIds));
$doctorCondition = !empty($doctorIdsStr) ? "AND doctor_name IN ($doctorIdsStr)" : "AND 0";

// FIX_B_1903: explicit doctor-scope filter (defense-in-depth alongside doctorCondition above)
$docScope = currentDoctorScopeSql('doctor_name');

// 1. Get all patient names with their appoint_id (latest first)
$nameQuery = mysqli_query($conn, "
    SELECT appoint_id, patient_name
    FROM appointment_online
    WHERE appoint_status='1' AND org_id='$orgId' $doctorCondition $docScope
    ORDER BY appoint_id DESC
") or die(mysqli_error($conn));
$names = [];
while ($row = mysqli_fetch_assoc($nameQuery)) {
    $names[] = $row;
}

// 2. Get unique appointment IDs in descending order
$appointIdQuery = mysqli_query($conn, "
    SELECT appoint_register_id, appoint_id
    FROM appointment_online
    WHERE appoint_status='1' AND org_id='$orgId' $doctorCondition $docScope
    ORDER BY appoint_id DESC
") or die(mysqli_error($conn));
$appointIds = [];
while ($row = mysqli_fetch_assoc($appointIdQuery)) {
    $appointIds[] = $row;
}

// 3. Get unique mobile numbers with latest appoint_id per number
$mobileQuery = mysqli_query($conn, "
    SELECT mobile_number, appoint_id
    FROM appointment_online
    WHERE appoint_status = '1' AND org_id = '$orgId' $doctorCondition $docScope
    ORDER BY appoint_id DESC
") or die(mysqli_error($conn));
$mobiles = [];
while ($row = mysqli_fetch_assoc($mobileQuery)) {
    $mobiles[] = $row;
}

// 4. Get unique patient_ids (appoint_unicode) with latest appoint_id
$patientIdQuery = mysqli_query($conn, "
    SELECT appoint_unicode, appoint_id
    FROM appointment_online
    WHERE appoint_status = '1' AND org_id = '$orgId' $doctorCondition $docScope
    ORDER BY appoint_id DESC
") or die(mysqli_error($conn));
$patientIds = [];
while ($row = mysqli_fetch_assoc($patientIdQuery)) {
    $patientIds[] = $row;
}

$response = [
    "patient_names"   => $names,
    "appointment_ids" => $appointIds,
    "mobile_numbers"  => $mobiles,
    "patient_ids"     => $patientIds
];

echo json_encode($response);
exit;
?>
