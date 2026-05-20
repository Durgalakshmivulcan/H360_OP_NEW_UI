<?php
// FIX_B_013_015: require login + allowlist upload extensions
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
if (!function_exists('h360_safe_upload_ext')) {
    function h360_safe_upload_ext($name) {
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg','jpeg','png','pdf'], true);
    }
}

require_once "../../config/config.php";
require_once __DIR__ . "/../../config/functions.php";
/* B-1830 RBAC */ requireCan(empty($_REQUEST['report_id']) ? 'add' : 'edit', 'TestReport.php', 'ajax');

$msg = 0;
$ImgArr = [];

$appoint_register_id = $_POST['appoint_register_id1'] ?? '';
$org_id = $_POST['org_id1'] ?? '';

// Prepare dynamic WHERE condition
$where = "status='1'";
if (!empty($appoint_register_id)) {
    $where .= " AND appoint_register_id='$appoint_register_id'";
}
if (!empty($org_id)) {
    $where .= " AND org_id='$org_id'";
}

// Fetch existing images
$GetData = mysqli_query($conn, "SELECT images FROM prescripition WHERE $where") or die(mysqli_error($conn));
$resData = mysqli_fetch_object($GetData);

$images = [];
if (!empty($resData->images)) {
    $images = explode(",", $resData->images);
}

$file_names = $_FILES["uploadfile"]["name"];
$absolute_destination = __DIR__ . "/../Testimages/";

$i = count($images) + 1;

for ($img = 0; $img < count($file_names); $img++) {
    if (!h360_safe_upload_ext($file_names[$img])) { continue; }
    $ext = pathinfo($file_names[$img], PATHINFO_EXTENSION);
    $id_prefix = !empty($appoint_register_id) ? $appoint_register_id : $org_id;
    // FIX_B_087: race-safe filename — micro + rand suffix avoids the
    // count(existing)+1 TOCTOU collision under concurrent uploads.
    $micro = (string) round(microtime(true) * 1000000);
    $rand  = bin2hex(random_bytes(4));
    $file_name = $id_prefix . "_" . $micro . "_" . $rand . "." . $ext;

    if (!empty($ext)) {
        move_uploaded_file($_FILES["uploadfile"]["tmp_name"][$img], $absolute_destination . $file_name);
        array_push($images, $file_name);
    }
}

$file_data_str = implode(',', $images);

// Only update if there are new images
if (!empty($images)) {
    $update_qry = mysqli_query($conn, "UPDATE prescripition SET images='$file_data_str' WHERE $where") or die(mysqli_error($conn));
    if ($update_qry) {
        $msg = 2; // Success
    }
} else {
    $msg = 3; // No file uploaded
}

echo $msg;
?>
