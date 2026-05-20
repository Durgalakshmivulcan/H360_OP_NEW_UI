<?php
session_start();
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? 0;
if (!$SessionUserId) {
    echo "User session invalid!";
    exit;
}

// FIX_B_1850: per-action RBAC on profile.php. Existing IDOR fix already binds
// the write to $_SESSION['security_id'], so a user can only edit their own
// signature even if 'edit' is granted. SA bypass preserved by userCan().
requireCan('edit', 'profile.php', 'ajax');

if (!isset($_FILES['signature_file']['name'])) {
    echo "No file received!";
    exit;
}

$signatureName = $_FILES["signature_file"]["name"];
$signatureSize = $_FILES["signature_file"]["size"];
$signatureTmp  = $_FILES["signature_file"]["tmp_name"];

$validExt = ['jpg', 'jpeg', 'png', 'gif'];
$signatureExt = strtolower(pathinfo($signatureName, PATHINFO_EXTENSION));

if (!in_array($signatureExt, $validExt)) {
    echo "Invalid file type!";
    exit;
}

if ($signatureSize > 30 * 1024 * 1024) {
    echo "File size exceeds 30MB!";
    exit;
}

$targetDir = "../../signature/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$newSignatureName = "signature_" . $SessionUserId . "_" . time() . "." . $signatureExt;
$destination = $targetDir . $newSignatureName;

if (!move_uploaded_file($signatureTmp, $destination)) {
    echo "Upload failed!";
    exit;
}

$update = mysqli_query($conn, "UPDATE security SET signature_url = '$newSignatureName' WHERE security_id = $SessionUserId");

if ($update) {
    echo $newSignatureName; // ✔️ return only filename
} else {
    echo "Database update failed!";
}
?>
