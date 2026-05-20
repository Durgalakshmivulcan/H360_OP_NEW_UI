<?php
// IDOR_FIXED B-566
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

$SessionOrgId = $_SESSION['org_id'] ?? '';
header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$SessionUserId = $_SESSION['security_id'] ?? '';
$id = (int)($_POST['it_id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false,'error'=>'Invalid ID']); exit; }
$ok = mysqli_query($conn, "UPDATE instruction_template SET status='0', modified_by='$SessionUserId' WHERE it_id='$id' AND org_id='$SessionOrgId'");
echo json_encode(['success' => (bool)$ok]);
