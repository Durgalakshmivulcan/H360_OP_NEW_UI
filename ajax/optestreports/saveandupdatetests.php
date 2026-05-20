<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan(empty($_REQUEST['optestreport_id']) ? 'add' : 'edit', 'TestReport.php', 'ajax');


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

$msg = 0;

$org_id         = $_POST['org_id'] ?? '';
$patient_id     = $_POST['appoint_unicode'] ?? '';
$appointment_id = $_POST['appoint_register_id'] ?? '';
$performed_at   = $_POST['test_performed_at'] ?? ''; // mapped to table column 'performed_at'
$observations   = $_POST['observations'] ?? '';
$status         = '1';
$created_by     = $SessionUserId ?? 0;

$allowed_performed_at = ['Within the Hospital'];
$performed_at = $_POST['test_performed_at'] ?? '';
if (!in_array($performed_at, $allowed_performed_at)) {
    $performed_at = 'Within the Hospital';
}

// Upload directory
$upload_dir = __DIR__ . "/../Testimages/";
$relative_dir = "Testimages/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle multiple files
$file_urls = [];
if (!empty($_FILES['file_upload']['name'][0])) {
    foreach ($_FILES['file_upload']['name'] as $i => $original_name) {
        if ($_FILES['file_upload']['error'][$i] === UPLOAD_ERR_OK && h360_safe_upload_ext($original_name)) {
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $new_filename = $patient_id . "_" . time() . "_$i." . $ext;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], $destination)) {
                $file_urls[] = $relative_dir . $new_filename;
            }
        }
    }
}

// Get per-file fields
$file_types = $_POST['file_type'] ?? [];
$test_names = $_POST['test_name'] ?? [];
$uploaded_dates = $_POST['test_date'] ?? []; // mapped to table column 'uploaded_date'

// FIX_B_060: detect existing rows so we can emit msg=2 (Updated) vs msg=1 (Inserted),
// then DELETE the prior rows for idempotent re-save semantics that the JS expects.
$priorCheck = mysqli_query($conn,
    "SELECT 1 FROM patient_tests_history
       WHERE org_id='$org_id' AND patient_id='$patient_id' AND appointment_id='$appointment_id' AND status='1'
       LIMIT 1");
$hadPrior = ($priorCheck && mysqli_num_rows($priorCheck) > 0) ? 1 : 0;
mysqli_query($conn, "DELETE FROM patient_tests_history
    WHERE org_id='$org_id' AND patient_id='$patient_id' AND appointment_id='$appointment_id'");

// Insert each file record
foreach ($file_urls as $i => $file_url) {
    $type = $file_types[$i] ?? '';
    $test_name = $test_names[$i] ?? '';
    $uploaded_date = $uploaded_dates[$i] ?? '';

    if ($file_url && $type && $uploaded_date) {
        $InsertData = mysqli_query($conn, "INSERT INTO patient_tests_history 
            (patient_id, appointment_id, performed_at, file_type, test_name, uploaded_date, file_url, observations, status, org_id, created_by)
            VALUES 
            ('$patient_id', '$appointment_id', '$performed_at', '$type', '$test_name', '$uploaded_date', '$file_url', '$observations', '$status', '$org_id', '$created_by')")
            or die(mysqli_error($conn));

        if ($InsertData) {
            // FIX_B_060: report Updated (2) when caller is re-saving, else Inserted (1).
            $msg = $hadPrior ? 2 : 1;
        }
    }
}

echo $msg;
