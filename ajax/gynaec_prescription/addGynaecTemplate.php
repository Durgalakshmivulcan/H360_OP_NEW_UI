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

$fieldType    = mysqli_real_escape_string($conn, $_POST['field_type']    ?? '');
$templateName = mysqli_real_escape_string($conn, $_POST['template_name'] ?? '');
$templateData = mysqli_real_escape_string($conn, $_POST['template_data'] ?? '');
$orgId        = mysqli_real_escape_string($conn, $_POST['org_id']        ?? $SessionOrgId);

// Non-admin must use their own org_id
if ($_SESSION['security_id'] != '1') $orgId = $SessionOrgId;

if (!$fieldType || !$templateName || !$templateData) {
    echo json_encode(['success'=>false,'error'=>'All fields required']); exit;
}

$editId = (int)($_POST['edit_id'] ?? 0);
if ($editId > 0) {
    $sql = "UPDATE gynaec_field_templates SET template_name='$templateName', template_data='$templateData'
            WHERE id='$editId' AND org_id='$orgId'";
} else {
    $sql = "INSERT INTO gynaec_field_templates (field_type, template_name, template_data, org_id, status)
            VALUES ('$fieldType','$templateName','$templateData','$orgId','1')";
}

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>mysqli_error($conn)]);
}
