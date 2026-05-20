<?php
require_once("../../config/functions.php");
header('Content-Type: text/plain');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate and process doctor picture upload
if (isset($_FILES["dpic"]["name"]) && $_FILES["dpic"]["error"] == UPLOAD_ERR_OK) {
    $imageName = $_FILES["dpic"]["name"];
    $imageSize = $_FILES["dpic"]["size"];
    $tmpName   = $_FILES["dpic"]["tmp_name"];
    $validExts = ['jpg', 'jpeg', 'png'];
    $ext       = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $maxSize   = 1500000; // 1.5MB

    // Validate file extension
    if (!in_array($ext, $validExts)) {
        echo "Invalid Image Extension";
        exit;
    }

    // Validate file size
    if ($imageSize > $maxSize) {
        echo "Image Size Is Too Large";
        exit;
    }

    // Generate unique image name using timestamp and random number
    $newImageName = 'doc_' . time() . '.' . $ext;

    // Prepare the upload directory (relative to this script)
    $uploadDir = __DIR__ . '/../../doctor_images/';

    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo "Failed to create upload directory";
            exit;
        }
    }

    // Verify directory is writable
    if (!is_writable($uploadDir)) {
        echo "Upload directory is not writable";
        exit;
    }

    // First, try to move the uploaded file directly
    $destination = $uploadDir . $newImageName;
    if (move_uploaded_file($tmpName, $destination)) {
        echo $newImageName;
        exit;
    }

    // If direct move fails, try image processing
    try {
        // Load the image based on its type
        if ($ext == 'jpg' || $ext == 'jpeg') {
            $image = imagecreatefromjpeg($tmpName);
        } elseif ($ext == 'png') {
            $image = imagecreatefrompng($tmpName);
        } else {
            echo "Unsupported image type";
            exit;
        }

        if (!$image) {
            echo "Failed to create image from upload";
            exit;
        }

        // Define the target dimensions
        $targetWidth = 300;
        $targetHeight = 300;

        // Get original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calculate aspect ratio preserving resize
        $ratio = $originalWidth / $originalHeight;
        if ($targetWidth / $targetHeight > $ratio) {
            $targetWidth = $targetHeight * $ratio;
        } else {
            $targetHeight = $targetWidth / $ratio;
        }

        // Create a new image
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG
        if ($ext == 'png') {
            imagecolortransparent($resizedImage, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
        }

        // Resize the image
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $originalWidth, $originalHeight);

        // Save the resized image
        if ($ext == 'jpg' || $ext == 'jpeg') {
            imagejpeg($resizedImage, $destination, 85);
        } elseif ($ext == 'png') {
            imagepng($resizedImage, $destination, 6);
        }

        // Free memory
        imagedestroy($image);
        imagedestroy($resizedImage);

        // Verify the file was created
        if (file_exists($destination)) {
            echo $newImageName;
        } else {
            echo "File creation failed";
        }
    } catch (Exception $e) {
        echo "Image processing error: " . $e->getMessage();
    }
} else {
    echo "Upload error: " . (isset($_FILES["dpic"]["error"]) ? $_FILES["dpic"]["error"] : "No file uploaded");
}
?>























