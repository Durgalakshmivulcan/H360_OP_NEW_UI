<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');

// FIX_B_027: org-scope predicate added to latest-Rx SELECTs.
header('Content-Type: application/json');

$SessionOrgId = $_SESSION['org_id'] ?? '';

// Try by appointment_id first, then by patient_id (appoint_unicode)
$appointId = mysqli_real_escape_string($conn, $_POST['appointment_id'] ?? '');
$patientId = mysqli_real_escape_string($conn, $_POST['patient_id']     ?? '');
$orgId     = mysqli_real_escape_string($conn, $_POST['org_id']         ?? $SessionOrgId);

$row = null;

// Prefer appointment-specific match
if ($appointId !== '') {
    $q = mysqli_query($conn,
        "SELECT * FROM gynaec_prescriptions
         WHERE appointment_id = '$appointId' AND status = '1' AND org_id = '$SessionOrgId'
         ORDER BY gynaec_rx_id DESC LIMIT 1");
    if ($q) $row = mysqli_fetch_assoc($q);
}

// Fall back to most recent for this patient
if (!$row && $patientId !== '') {
    $q = mysqli_query($conn,
        "SELECT * FROM gynaec_prescriptions
         WHERE patient_id = '$patientId' AND status = '1' AND org_id = '$SessionOrgId'
         ORDER BY gynaec_rx_id DESC LIMIT 1");
    if ($q) $row = mysqli_fetch_assoc($q);
}

if (!$row) {
    echo json_encode(['success' => false]);
    exit;
}

$medicines      = json_decode($row['medicines_json']      ?? '[]', true) ?: [];
$investigations = json_decode($row['investigations_json'] ?? '[]', true) ?: [];

echo json_encode([
    'success'        => true,
    'data'           => $row,
    'medicines'      => $medicines,
    'investigations' => $investigations
]);
