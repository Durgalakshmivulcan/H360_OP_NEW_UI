<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('edit', 'prescription.php', 'ajax');
header("Content-Type: application/json");

session_start();
if (!isset($_SESSION['security_id'], $_SESSION['org_id'], $_POST['ph_id'], $_POST['template_name'], $_POST['diagnosis_data'], $_POST['org_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request or session expired']);
    exit;
}

$id = intval($_POST['ph_id']);
$templateName = mysqli_real_escape_string($conn, $_POST['template_name']);
$templateData = mysqli_real_escape_string($conn, $_POST['diagnosis_data']);
$requestOrgId = intval($_POST['org_id']);
$userOrgId = intval($_SESSION['org_id']);

$orgCondition = "";
if ($_SESSION['role_id'] != 1 || $_SESSION['security_id'] != 1) {
    if ($userOrgId != $requestOrgId) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized organization access']);
        exit;
    }
    $orgCondition = " AND org_id = $userOrgId";
} else {
    $orgCondition = " AND org_id = $requestOrgId";
}

$sql = "UPDATE pasthistory_template 
        SET template_name = '$templateName', 
            template_data = '$templateData'
        WHERE ph_id = $id
        $orgCondition";

$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No changes made - template not found or already has these values'
        ]);
    }
} else {
    error_log("Template update failed: " . mysqli_error($conn));
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'db_error' => (ENVIRONMENT === 'development') ? mysqli_error($conn) : null
    ]);
}
?>