<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$prescriptionId = (int)($_POST['prescription_id'] ?? 0);
if (!$prescriptionId) { echo json_encode(['success'=>false,'error'=>'Prescription ID required']); exit; }

$res = mysqli_query($conn, "SELECT * FROM prescripition WHERE prescription_id='$prescriptionId' AND status='1' LIMIT 1");
if (!$res || mysqli_num_rows($res) === 0) { echo json_encode(['success'=>false,'error'=>'Prescription not found']); exit; }

$prescription = mysqli_fetch_assoc($res);

// Fetch latest appointment for the same patient to get vitals
$patient_uid = mysqli_real_escape_string($conn, $prescription['patient_uid']);
$org_id      = (int)$prescription['org_id'];
$apptRes = mysqli_query($conn,
    "SELECT * FROM appointment_online
     WHERE appoint_unicode='$patient_uid' AND org_id='$org_id' AND appointment_status='1'
     ORDER BY appoint_id DESC LIMIT 1"
);
$appointment = $apptRes ? mysqli_fetch_assoc($apptRes) : null;

echo json_encode(['success' => true, 'prescription' => $prescription, 'appointment' => $appointment]);
