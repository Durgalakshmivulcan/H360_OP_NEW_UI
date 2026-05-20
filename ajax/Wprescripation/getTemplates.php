<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");

// Debug mode
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Validate session
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['security_id'])) {
        throw new Exception("Session expired or invalid.");
    }

    // Build query
    $sql = "SELECT fd_id, template_name, template_data FROM finaldiagnosis_template WHERE status = '1'";
    
    // Add org filter (if needed)
    if ($_SESSION['role_id'] == 1 && $_SESSION['security_id'] == 1) {
        $orgId = $_GET['org_id'] ?? null;
        if ($orgId) {
            $sql .= " AND org_id = '" . mysqli_real_escape_string($conn, $orgId) . "'";
        }
    } else {
        $sql .= " AND org_id = '" . mysqli_real_escape_string($conn, $_SESSION['org_id']) . "'";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    $templates = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode([
        'success'   => true,
        'templates' => $templates
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}