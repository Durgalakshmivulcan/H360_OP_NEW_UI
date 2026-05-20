<?php
// FIX_B_065 — deduplicated (file shipped pasted 2× with stray <?php).
require_once(__DIR__ . "/../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
ensureInstructionTemplateTable($conn);
if (!isset($_SESSION['security_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
$orgId = ($_SESSION['security_id'] == 1)
    ? (int)($_GET['org_id'] ?? 0)
    : (int)($_SESSION['org_id'] ?? 0);
$type  = mysqli_real_escape_string($conn, trim($_GET['type'] ?? ''));
$cond  = $orgId ? "AND org_id='$orgId'" : '';
$tCond = $type  ? "AND type='$type'" : '';
$res   = mysqli_query(
    $conn,
    "SELECT it_id, template_name, template_data FROM instruction_template WHERE status='1' $cond $tCond"
);
$templates = [];
while ($r = mysqli_fetch_assoc($res)) {
    $templates[] = $r;
}
echo json_encode(['success' => true, 'templates' => $templates]);
