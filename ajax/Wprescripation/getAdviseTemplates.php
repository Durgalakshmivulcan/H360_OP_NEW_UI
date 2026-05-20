<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$SessionUserId = $_SESSION['security_id'] ?? '';
$orgId = ($SessionUserId == 1) ? (int)($_GET['org_id'] ?? 0) : (int)($_SESSION['org_id'] ?? 0);
$cond  = $orgId ? "AND org_id='$orgId'" : '';
$res   = mysqli_query($conn, "SELECT at_id, template_name, template_data FROM advise_template WHERE status='1' $cond ORDER BY template_name ASC");
$templates = [];
while ($r = mysqli_fetch_assoc($res)) $templates[] = $r;
echo json_encode(['success' => true, 'templates' => $templates]);
