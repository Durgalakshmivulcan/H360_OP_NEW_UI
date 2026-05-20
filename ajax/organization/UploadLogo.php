<?php
// FIX_B_1502: previously had NO authentication gate. Anonymous file upload was
// possible to /organisation_images/. Now: session-gated to Super Admin (since
// only SA edits org records). org_id int-cast was already in place.
require_once("../../config/functions.php");
header('Content-Type: text/plain');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionType   = $_SESSION['security_type'] ?? '';
if ($SessionUserId === '' || $SessionType !== 'SA') {
    http_response_code(403);
    echo '';
    exit;
}

$id = 0;
if (!empty($_POST['org_id'])) {
    $id = intval($_POST['org_id']);
} elseif (!empty($_POST['lastorg_id'])) {
    $id = intval($_POST['lastorg_id']);
}

$uploadDir = __DIR__ . '/../../organisation_images/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function processLogo($fileInput, $id, $uploadDir, $conn) {
    if (!isset($_FILES[$fileInput]["name"])) return null;

    $imageName = $_FILES[$fileInput]["name"];
    $imageSize = $_FILES[$fileInput]["size"];
    $tmpName   = $_FILES[$fileInput]["tmp_name"];
    
    $validExts = ['jpg', 'jpeg', 'png'];
    $ext       = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $maxSize   = 1500000;

    if (!in_array($ext, $validExts)) {
        echo "Invalid Image Extension for $fileInput";
        exit;
    }

    if ($imageSize > $maxSize) {
        echo "Image Size Is Too Large for $fileInput";
        exit;
    }

    $newImageName = $id . '_' . $fileInput . '.' . $ext; // unique filename per input

    // Remove old file if exists
    foreach (glob($uploadDir . $id . '_' . $fileInput . '.*') as $oldFile) {
        unlink($oldFile);
    }

    // Resize image
    if ($ext === 'jpg' || $ext === 'jpeg') {
        $image = imagecreatefromjpeg($tmpName);
    } elseif ($ext === 'png') {
        $image = imagecreatefrompng($tmpName);
    }

    $targetWidth = 125;
    $targetHeight = 77;
    $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled(
        $resizedImage,
        $image,
        0, 0, 0, 0,
        $targetWidth,
        $targetHeight,
        imagesx($image),
        imagesy($image)
    );

    // Save resized image
    if ($ext === 'jpg' || $ext === 'jpeg') {
        imagejpeg($resizedImage, $uploadDir . $newImageName);
    } elseif ($ext === 'png') {
        imagepng($resizedImage, $uploadDir . $newImageName);
    }

    imagedestroy($image);
    imagedestroy($resizedImage);

    // Update DB
    $escapedImageName = mysqli_real_escape_string($conn, $newImageName);
    $column = $fileInput === 'logoFile1' ? 'logo' : 'logo_without_text'; // adjust column names
    $update = "UPDATE organization SET $column = '$escapedImageName' WHERE org_id = '$id'";
    mysqli_query($conn, $update);

    return $newImageName;
}

// Process both logos
$uploaded1 = processLogo('logoFile1', $id, $uploadDir, $conn);
$uploaded2 = processLogo('logoFile2', $id, $uploadDir, $conn);

// Return uploaded filenames as comma-separated
if ($uploaded1 || $uploaded2) {
    echo trim(($uploaded1 ?: '') . ',' . ($uploaded2 ?: ''));
} else {
    echo "no_id_or_file";
}
