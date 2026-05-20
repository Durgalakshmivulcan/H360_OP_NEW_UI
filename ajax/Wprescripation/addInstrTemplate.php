<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
ensureInstructionTemplateTable($conn);
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$orgId = (int)($_SESSION['org_id'] ?? 0);
$name  = mysqli_real_escape_string($conn, trim($_POST['template_name'] ?? ''));
$data  = mysqli_real_escape_string($conn, trim($_POST['instr_data']    ?? ''));
$type  = mysqli_real_escape_string($conn, trim($_POST['type']          ?? ''));
if (!$name || !$data || !in_array($type, ['medicine','investigation'])) {
    echo json_encode(['success'=>false,'error'=>'Name, text and type required']); exit;
}
$ok = mysqli_query($conn, "INSERT INTO instruction_template (template_name, template_data, type, status, org_id) VALUES ('$name','$data','$type','1','$orgId')");
echo json_encode(['success' => (bool)$ok]);
