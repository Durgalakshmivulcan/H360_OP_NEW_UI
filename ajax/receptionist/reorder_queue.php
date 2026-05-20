<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionOrgId = $_SESSION['org_id'] ?? 1;
$order = $_POST['order'] ?? [];

if (empty($order) || !is_array($order)) {
    echo json_encode(['status' => 'error', 'message' => 'No order data']);
    exit;
}

// Add queue_order column if it does not exist yet
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM appointment_online LIKE 'queue_order'");
if (mysqli_num_rows($checkCol) == 0) {
    mysqli_query($conn, "ALTER TABLE appointment_online ADD COLUMN queue_order INT(11) DEFAULT NULL");
}

foreach ($order as $i => $appoint_register_id) {
    $appoint_register_id = mysqli_real_escape_string($conn, $appoint_register_id);
    $pos = (int)$i + 1;
    mysqli_query($conn, "UPDATE appointment_online SET queue_order = $pos WHERE appoint_register_id = '$appoint_register_id' AND org_id = '$SessionOrgId'");
}

echo json_encode(['status' => 'ok']);
?>
