<?php
require_once("../../config/functions.php");

// FIX_B_026: require login and pull SessionOrgId from session.
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
$SessionOrgId = $_SESSION['org_id'] ?? '';
header('Content-Type: application/json');

$id = (int)($_POST['echo_report_id'] ?? 0);
if (!$id) { echo json_encode(['success' => false]); exit; }

$qry = mysqli_query($conn, "SELECT * FROM echo_reports WHERE echo_report_id='$id' AND status='1' AND org_id='$SessionOrgId' LIMIT 1");
$row = mysqli_fetch_assoc($qry);
if ($row) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not found']);
}
