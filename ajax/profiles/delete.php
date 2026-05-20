<?php
session_start();
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? 0;
if (!$SessionUserId) {
    echo "User session invalid!";
    exit;
}

// FIX_B_1850: per-action RBAC on profile.php. The query below is bound to
// $_SESSION['security_id'], so a user can only delete their own signature.
// SA bypass preserved by userCan().
requireCan('edit', 'profile.php', 'ajax');

$query = mysqli_query($conn, "SELECT signature_url FROM security WHERE security_id = $SessionUserId");
if (!$query || mysqli_num_rows($query) == 0) {
    echo "User not found!";
    exit;
}

$row = mysqli_fetch_assoc($query);
$currentFile = $row['signature_url'] ?? '';

if (!empty($currentFile)) {
    $filePath = "../../signature/" . $currentFile;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

$update = mysqli_query($conn, "UPDATE security SET signature_url = '' WHERE security_id = $SessionUserId");

if ($update) {
    echo "success";
} else {
    echo "Database update failed!";
}
?>
