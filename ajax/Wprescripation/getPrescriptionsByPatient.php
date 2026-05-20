<?php
// FIX_B_065 — deduplicated (file shipped pasted 4× with stray <?php).
require_once(__DIR__ . "/../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);
$organizations = (int)($_POST['organizations'] ?? 0);
$org_id = ($SessionUserId == "1") ? ($organizations ?: $SessionOrgId) : $SessionOrgId;

$patient_id = mysqli_real_escape_string($conn, trim($_POST['patient_id'] ?? ''));
if (!$patient_id) {
    echo json_encode(['success' => false, 'error' => 'Patient ID required']);
    exit;
}

$orgCond = $org_id ? "AND org_id='$org_id'" : '';

$res = mysqli_query(
    $conn,
    "SELECT prescription_id, prescriptiondate, finalDiagnosis, create_date_time
     FROM prescripition
     WHERE patient_uid='$patient_id' AND status='1' $orgCond
     ORDER BY prescription_id DESC LIMIT 20"
);

if (!$res) {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit;
}

$list = [];
while ($r = mysqli_fetch_assoc($res)) {
    $list[] = $r;
}
echo json_encode(['success' => true, 'prescriptions' => $list]);
