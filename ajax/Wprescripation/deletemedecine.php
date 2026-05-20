<?php
// IDOR_FIXED B-570
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('delete', 'prescription.php', 'ajax');
$SessionOrgId = $_SESSION['org_id'] ?? '';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    error_log("Soft deleting medicine row ID: " . $id);

    $sql = "UPDATE inp_prescription_list SET status1 = '0' WHERE id = $id AND org_id='$SessionOrgId'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_affected_rows($conn) > 0) {
            $response['success'] = true;
            $response['message'] = 'Prescription deleted successfully';
        } else {
            $response['message'] = 'No record found with this ID.';
        }
    } else {
        $response['message'] = 'Execution error: ' . mysqli_error($conn);
    }
} else {
    $response['message'] = 'Invalid ID.';
}

echo json_encode($response);
exit();
