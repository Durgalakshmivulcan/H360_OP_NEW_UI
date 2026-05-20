<?php
require_once("../../config/functions.php");
require_once __DIR__ . '/../../include/auth_guard.php';
requireLogin();
assertRole([1]); // AUTH_GUARD_B020_WIRED — SA-only (FIX_B_920)

header('Content-Type: application/json; charset=UTF-8');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

if (empty($SessionUserId)) {
    $response['message'] = "Session expired or user not logged in.";
    echo json_encode($response);
    exit;
}

// Fetch security type
$securityQuery = mysqli_query($conn, "SELECT security_type FROM security WHERE security_id = '$SessionUserId' LIMIT 1");
if (!$securityQuery || mysqli_num_rows($securityQuery) == 0) {
    $response['message'] = "User not found in security table.";
    echo json_encode($response);
    exit;
}

$securityRow = mysqli_fetch_assoc($securityQuery);
$securityType = $securityRow['security_type'];

// FIX_B_920: Only Superadmin (SA) — tightened from SA+A to SA-only (no destructive DB ops for tenant Admins)
if (!in_array($securityType, ['SA'])) {
    $response['message'] = "Unauthorized action. Only Superadmin can truncate tables.";
    echo json_encode($response);
    exit;
}

// Validate table name input
if (!isset($_POST['table_name']) || empty(trim($_POST['table_name']))) {
    $response['message'] = "No table specified.";
    echo json_encode($response);
    exit;
}

$table = trim($_POST['table_name']);
if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
    $response['message'] = "Invalid table name format.";
    echo json_encode($response);
    exit;
}

// Check if table exists
$checkTable = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
if (!$checkTable || mysqli_num_rows($checkTable) == 0) {
    $response['message'] = "Table '$table' does not exist.";
    echo json_encode($response);
    exit;
}

// ---------------- EXPORT DATA BEFORE TRUNCATE ----------------
$exportFile = __DIR__ . '/../../database/' . $table . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
$fp = fopen($exportFile, 'w');
if ($fp) {
    $result = mysqli_query($conn, "SELECT * FROM `$table`");
    while ($row = mysqli_fetch_assoc($result)) {
        $columns = array_map(function($col){ return "`$col`"; }, array_keys($row));
        $values  = array_map(function($val) use ($conn){ return "'" . mysqli_real_escape_string($conn, $val) . "'"; }, array_values($row));
        $sqlLine = "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
        fwrite($fp, $sqlLine);
    }
    fclose($fp);
}

// Disable foreign key checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Truncate the table
$query = "TRUNCATE TABLE `$table`";
if (mysqli_query($conn, $query)) {

    // Handle truncate_logs table
    $logTableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'truncate_logs'");
    if (!$logTableCheck || mysqli_num_rows($logTableCheck) == 0) {
        $createLogTable = "
            CREATE TABLE `truncate_logs` (
                `truncate_id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `org_id` INT(11) DEFAULT NULL,
                `action` TEXT NOT NULL,
                `log_time` DATETIME NOT NULL,
                PRIMARY KEY (`truncate_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        mysqli_query($conn, $createLogTable);
    }

    $logMessage = "Table '$table' truncated by User ID $SessionUserId (Type: $securityType, Org ID: $SessionOrgId)";
    mysqli_query($conn, "INSERT INTO truncate_logs (user_id, org_id, action, log_time) 
                         VALUES ('$SessionUserId', '$SessionOrgId', '" . mysqli_real_escape_string($conn, $logMessage) . "', NOW())");

    $response['status'] = 'success';
    $response['message'] = "Table '$table' truncated successfully. Backup saved at: " . basename($exportFile);

} else {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
    $response['message'] = "Error truncating '$table': " . mysqli_error($conn) . " (Query: $query)";
}

echo json_encode($response);
exit;
?>
