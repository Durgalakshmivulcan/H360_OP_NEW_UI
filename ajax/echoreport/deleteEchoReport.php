<?php
require_once("../../config/functions.php");

// FIX_B_026: require login and pull SessionOrgId from session.
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
$SessionOrgId = $_SESSION['org_id'] ?? '';
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
if (!$SessionUserId) { echo json_encode(['success' => false]); exit; }

$id = (int)($_POST['echo_report_id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit; }

$result = mysqli_query($conn, "UPDATE echo_reports SET status='0' WHERE echo_report_id='$id' AND org_id='$SessionOrgId'");
if ($result) {
    audit_log($conn, 'EchoReport', 'delete', 'echo_reports', $id, null, null);
    echo json_encode(['success' => true, 'message' => 'Echo report deleted.']);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
}
