<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');

header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if (!$SessionUserId) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$id    = (int)($_POST['id'] ?? 0);
$orgId = mysqli_real_escape_string($conn, $SessionOrgId);

if (!$id) { echo json_encode(['success'=>false,'error'=>'Invalid ID']); exit; }

$orgCheck = ($_SESSION['security_id'] == '1') ? '' : "AND org_id='$orgId'";
$sql = "UPDATE gynaec_field_templates SET status='0' WHERE id='$id' $orgCheck";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>mysqli_error($conn)]);
}
