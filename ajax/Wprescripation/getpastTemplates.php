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
    if (!isset($_SESSION['security_id'], $_SESSION['role_id'], $_SESSION['org_id'])) {
        throw new Exception("Session expired or invalid.");
    }

    // Prepare base query - REMOVED status filter to show all templates
    $sql = "SELECT ph_id, template_name, template_data, status FROM pasthistory_template";
    
    // Handle organization filtering
    if ($_SESSION['role_id'] == 1 && $_SESSION['security_id'] == 1) {
        // Admin can view any organization's templates
        $orgId = $_GET['org_id'] ?? null;
        if ($orgId) {
            $sql .= " WHERE org_id = '" . mysqli_real_escape_string($conn, $orgId) . "'";
        }
    } else {
        // Regular users only see their organization's templates
        $sql .= " WHERE org_id = '" . mysqli_real_escape_string($conn, $_SESSION['org_id']) . "'";
    }

    // Optional: Add status filter if needed
    if (isset($_GET['status'])) {
        $status = (int)$_GET['status'];
        $sql .= (strpos($sql, 'WHERE') !== false ? ' AND' : ' WHERE');
        $sql .= " status = $status";
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