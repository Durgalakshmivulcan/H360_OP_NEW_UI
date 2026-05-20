<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('add', 'prescription.php', 'ajax');
header("Content-Type: application/json");

session_start();
if (!isset($_SESSION['security_id'], $_SESSION['org_id'], $_POST['template_name'], $_POST['diagnosis_data'], $_POST['org_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request or session expired']);
    exit;
}

$userOrgId = $_SESSION['org_id'] ?? '';
$requestOrgId = $_POST['org_id'];

if ($_SESSION['role_id'] != 1 || $_SESSION['security_id'] != 1) {
    if ($userOrgId != $requestOrgId) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized organization access']);
        exit;
    }
}

$templateName = mysqli_real_escape_string($conn, $_POST['template_name']);
$templateData = mysqli_real_escape_string($conn, $_POST['diagnosis_data']);
$orgId = mysqli_real_escape_string($conn, $requestOrgId);

$sql = "INSERT INTO cheifcomplaint_template 
        (template_name, template_data, org_id, status) 
        VALUES ('$templateName', '$templateData', '$orgId', '1')";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    error_log("Database error: " . mysqli_error($conn));
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save template',
        'db_error' => (ENVIRONMENT === 'development') ? mysqli_error($conn) : null
    ]);
}
?>