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

$fieldType = mysqli_real_escape_string($conn, $_GET['field_type'] ?? '');
if (!$fieldType) { echo json_encode(['success'=>false,'error'=>'field_type required']); exit; }

if ($_SESSION['security_id'] == '1' && $_SESSION['role_id'] == '1') {
    $orgId = mysqli_real_escape_string($conn, $_GET['org_id'] ?? '');
    $orgFilter = $orgId ? "AND org_id='$orgId'" : '';
} else {
    $orgFilter = "AND org_id='$SessionOrgId'";
}

$sql = "SELECT id, template_name, template_data FROM gynaec_field_templates
        WHERE field_type='$fieldType' AND status='1' $orgFilter ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
if (!$result) { echo json_encode(['success'=>false,'error'=>mysqli_error($conn)]); exit; }

$templates = [];
while ($row = mysqli_fetch_assoc($result)) $templates[] = $row;
echo json_encode(['success'=>true,'templates'=>$templates]);
