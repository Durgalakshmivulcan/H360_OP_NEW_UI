<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');

header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? ''; // FIX_B_188
if ($SessionOrgId === '') { echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }
if (!$SessionUserId) { echo json_encode(['success'=>false]); exit; }

$id = (int)($_POST['gynaec_rx_id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit; }

$result = mysqli_query($conn, "UPDATE gynaec_prescriptions SET status='0' WHERE gynaec_rx_id='$id' AND org_id='$SessionOrgId'");
if ($result) {
    audit_log($conn,'GynaecRx','delete','gynaec_prescriptions',$id,null,null);
    echo json_encode(['success'=>true,'message'=>'Prescription deleted.']);
} else {
    echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]);
}
