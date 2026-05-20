<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');


// FIX_B_027: require login + load SessionOrgId.
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
$SessionOrgId = $_SESSION['org_id'] ?? '';
header('Content-Type: application/json');

$id = (int)($_POST['gynaec_rx_id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false]); exit; }

$qry = mysqli_query($conn, "SELECT * FROM gynaec_prescriptions WHERE gynaec_rx_id='$id' AND status='1' AND org_id='$SessionOrgId' LIMIT 1");
$row = mysqli_fetch_assoc($qry);
if (!$row) { echo json_encode(['success'=>false,'message'=>'Not found']); exit; }

$medicines      = json_decode($row['medicines_json']      ?? '[]', true) ?: [];
$investigations = json_decode($row['investigations_json'] ?? '[]', true) ?: [];

echo json_encode([
    'success'        => true,
    'data'           => $row,
    'medicines'      => $medicines,
    'investigations' => $investigations
]);
