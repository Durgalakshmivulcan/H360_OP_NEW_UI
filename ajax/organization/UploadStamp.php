<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionType   = $_SESSION['security_type'] ?? '';
if ($SessionUserId === '' || $SessionType !== 'SA') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$orgId = 0;
if (!empty($_POST['org_id'])) {
    $orgId = (int)$_POST['org_id'];
}
if (!$orgId) {
    echo json_encode(['success' => false, 'message' => 'Missing org_id']);
    exit;
}

if (empty($_FILES['stamp_file']['name'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$uploadDir = __DIR__ . '/../../organisation_stamp/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = $_FILES['stamp_file']['name'];
$imageSize = $_FILES['stamp_file']['size'];
$tmpName   = $_FILES['stamp_file']['tmp_name'];

$validExts = ['jpg', 'jpeg', 'png'];
$ext       = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

if (!in_array($ext, $validExts)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Use JPG or PNG.']);
    exit;
}
if ($imageSize > 1500000) {
    echo json_encode(['success' => false, 'message' => 'File too large (max 1.5 MB)']);
    exit;
}

$newName = $orgId . '_stamp.' . $ext;

// Remove old stamp files for this org
foreach (glob($uploadDir . $orgId . '_stamp.*') as $old) {
    unlink($old);
}

// Save (no resize — stamps need to be crisp)
if (!move_uploaded_file($tmpName, $uploadDir . $newName)) {
    echo json_encode(['success' => false, 'message' => 'File move failed']);
    exit;
}

$escaped = mysqli_real_escape_string($conn, $newName);
mysqli_query($conn, "UPDATE organization SET org_stamp='$escaped' WHERE org_id='$orgId'");

echo json_encode(['success' => true, 'filename' => $newName]);
