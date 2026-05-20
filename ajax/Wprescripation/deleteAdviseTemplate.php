<?php
// IDOR_FIXED B-564
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

$SessionOrgId = $_SESSION['org_id'] ?? '';
header("Content-Type: application/json");
$id = (int)($_POST['id'] ?? 0);
$ok = mysqli_query($conn, "UPDATE advise_template SET status='0' WHERE at_id='$id' AND org_id='$SessionOrgId'");
echo json_encode(['success' => (bool)$ok]);
