<?php
// FIX_B_1500: previously had NO authentication gate AND $_POST['org_id'] was
// concatenated directly into the SQL — any unauthenticated client could send
// `POST org_id=N` (or `org_id=1' OR '1'='1`) and soft-delete every
// organization. Now: session-gated to Super Admin (security_type='SA') only,
// org_id int-cast.
require_once("../../config/functions.php");
header('Content-Type: text/plain');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionType   = $_SESSION['security_type'] ?? '';
if ($SessionUserId === '' || $SessionType !== 'SA') {
    http_response_code(403);
    echo '0';
    exit;
}

$delete_id = isset($_POST['org_id']) ? (int) $_POST['org_id'] : 0;
if ($delete_id <= 0) { http_response_code(400); echo '0'; exit; }

$langDeleted = 0;
$qryDelete = mysqli_query($conn, "UPDATE organization SET status='0' WHERE org_id='$delete_id'") or die(mysqli_error($conn));
if ($qryDelete) { $langDeleted = 1; }

echo $langDeleted;
