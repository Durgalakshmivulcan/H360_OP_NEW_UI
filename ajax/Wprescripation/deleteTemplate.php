<?php
// IDOR_FIXED B-568
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('delete', 'prescription.php', 'ajax');

$SessionOrgId = $_SESSION['org_id'] ?? '';
$id = intval($_POST['id']);

$qry = mysqli_query($conn, "UPDATE finaldiagnosis_template SET status = '0' WHERE fd_id = '$id' AND org_id='$SessionOrgId'");

echo json_encode(['success' => $qry ? true : false]);
