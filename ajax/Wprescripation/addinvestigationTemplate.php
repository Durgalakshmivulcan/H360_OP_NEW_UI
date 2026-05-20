<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('add', 'prescription.php', 'ajax');
header("Content-Type: application/json");

$input = json_decode(file_get_contents('php://input'), true);

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if (
    !isset($input['template_name']) ||
    !isset($input['total_price']) ||
    !isset($input['tests']) ||
    !is_array($input['tests'])
) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}


$template_name = mysqli_real_escape_string($conn, $input['template_name']);
$organizations = mysqli_real_escape_string($conn, $input['organizations']);
$template_data = mysqli_real_escape_string($conn, json_encode($input['tests']));
$total_price = floatval($input['total_price']);


$organizations = ($SessionUserId == "1") ? $organizations : $SessionOrgId;

$qry = mysqli_query($conn,"INSERT INTO test_group (test_group_name, test_id, test_group_price, status, created_by, modified_by, create_date_time,org_id) VALUES ('$template_name','$template_data', '$total_price', '1', '$SessionUserId', '$SessionUserId','$datetime','$organizations')") or die(mysqli_error($conn));

if ($qry) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>
