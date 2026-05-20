<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$unicode = mysqli_real_escape_string($conn, $_POST['appoint_unicode'] ?? '');

// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $q = mysqli_query($conn, "SELECT patient_name, mobile_number, appoint_register_id, appoint_unicode, appoint_id, age, gender, dob
        FROM appointment_online WHERE appoint_status='1' AND appoint_unicode='$unicode' $docScope
        ORDER BY appoint_id DESC LIMIT 1") or die(mysqli_error($conn));
} else {
    $q = mysqli_query($conn, "SELECT patient_name, mobile_number, appoint_register_id, appoint_unicode, appoint_id, age, gender, dob
        FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_unicode='$unicode' $docScope
        ORDER BY appoint_id DESC LIMIT 1") or die(mysqli_error($conn));
}

if ($r = mysqli_fetch_assoc($q)) echo json_encode(['success' => true, 'data' => $r]);
else echo json_encode(['success' => false]);
?>
