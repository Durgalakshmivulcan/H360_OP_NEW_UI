<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$SessionUserId = $_SESSION['security_id'] ?? '';
$orgId = ($SessionUserId == 1) ? (int)($_POST['org_id'] ?? 0) : (int)($_SESSION['org_id'] ?? 0);
$name  = mysqli_real_escape_string($conn, trim($_POST['template_name'] ?? ''));
$data  = mysqli_real_escape_string($conn, trim($_POST['template_data'] ?? ''));
$type  = in_array($_POST['type'] ?? '', ['medicine','investigation']) ? $_POST['type'] : 'medicine';
if (!$name || !$data) { echo json_encode(['success'=>false,'error'=>'Name and data required']); exit; }
$ok = mysqli_query($conn, "INSERT INTO instruction_template (template_name, template_data, type, org_id, created_by) VALUES ('$name','$data','$type','$orgId','$SessionUserId')");
echo json_encode(['success' => (bool)$ok, 'it_id' => mysqli_insert_id($conn)]);
