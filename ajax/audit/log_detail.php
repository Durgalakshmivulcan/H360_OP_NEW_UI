<?php
// audit_feature/ajax/audit/log_detail.php
// Return before/after JSON for a single audit log row.

header('Content-Type: application/json');
require_once("../../config/functions.php");

$org_id = (int)($_SESSION['org_id'] ?? 0);
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    echo json_encode(['error' => 'Missing id']);
    exit;
}

// ------------------------------------------------------
// Step 1: Fetch audit log row
// ------------------------------------------------------
$sql = "SELECT before_json, after_json 
        FROM audit_log 
        WHERE id='$id' AND org_id='$org_id'";
$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
$row = mysqli_fetch_assoc($res);

$before = $row['before_json'] ?? null;
$after  = $row['after_json']  ?? null;

// ------------------------------------------------------
// Step 2: Decode JSON
// ------------------------------------------------------
$after_data  = $after  ? json_decode($after, true)  : null;
$before_data = $before ? json_decode($before, true) : null;

// ------------------------------------------------------
// Step 3: Post-process AFTER data
// ------------------------------------------------------
if ($after_data) {
    if (isset($after_data['org_id'])) {
        $after_data['organization_name'] = getOrganizationName($conn, (int)$after_data['org_id']);
    }
    if (isset($after_data['created_by'])) {
        $after_data['created_by_name'] = getAdminName($conn, (int)$after_data['created_by']);
    }
    if (isset($after_data['modified_by'])) {
        $after_data['modified_by_name'] = getAdminName($conn, (int)$after_data['modified_by']);
    }
    if (isset($after_data['status'])) {
        $after_data['status_name'] = (($after_data['status']=="1") ? "Active" : "Deleted");
    }
    if (isset($after_data['test_group_name'])) {
        $after_data['test_group_name'] = formatTestGroupName($after_data);
    }
    if (isset($after_data['departments'])) {
        $after_data['departments'] = getDepartmentNames($conn, $after_data['departments']);
    }
    if (isset($after_data['doctorName_registrationNumber'])) {
        $after_data['doctor_name'] = getDoctorName($conn, (int)$after_data['doctorName_registrationNumber']);
    }
    if (isset($after_data['doctors_time_id'])) {
        $after_data['time_slots'] = getDoctorTimeslotDetails($conn, (int)$after_data['doctors_time_id']);
    }
    if (isset($after_data['doctor_specialization'])) {
        $after_data['doctor_specialization'] = getSpecializationNames($conn, $after_data['doctor_specialization']);
    }
    if (isset($after_data['test_details'])) {
        $after_data['test_details'] = getTestDetails($conn, $after_data['test_details']);
    }
    $after_data = formatPriceAndPercentage($after_data);
}

// ------------------------------------------------------
// Step 4: Post-process BEFORE data
// ------------------------------------------------------
if ($before_data) {
    if (isset($before_data['org_id'])) {
        $before_data['organization_name'] = getOrganizationName($conn, (int)$before_data['org_id']);
    }
    if (isset($before_data['created_by'])) {
        $before_data['created_by_name'] = getAdminName($conn, (int)$before_data['created_by']);
    }
    if (isset($before_data['modified_by'])) {
        $before_data['modified_by_name'] = getAdminName($conn, (int)$before_data['modified_by']);
    }
    if (isset($before_data['status'])) {
        $before_data['status_name'] = (($before_data['status']=="1") ? "Active" : "In-Active");
    }
    if (isset($before_data['test_group_name'])) {
        $before_data['test_group_name'] = formatTestGroupName($before_data);
    }
    if (isset($before_data['departments'])) {
        $before_data['departments'] = getDepartmentNames($conn, $before_data['departments']);
    }
    if (isset($before_data['doctorName_registrationNumber'])) {
        $before_data['doctor_name'] = getDoctorName($conn, (int)$before_data['doctorName_registrationNumber']);
    }
    if (isset($before_data['doctors_time_id'])) {
        $before_data['time_slots'] = getDoctorTimeslotDetails($conn, (int)$before_data['doctors_time_id']);
    }
    if (isset($before_data['doctor_specialization'])) {
        $before_data['doctor_specialization'] = getSpecializationNames($conn, $before_data['doctor_specialization']);
    }
    if (isset($before_data['test_details'])) {
        $before_data['test_details'] = getTestDetails($conn, $before_data['test_details']);
    }
    $before_data = formatPriceAndPercentage($before_data);
}

// ------------------------------------------------------
// Step 5: Transform and return
// ------------------------------------------------------
$after_pretty  = $after_data  ? transformData($after_data, $displayMap)   : null;
$before_pretty = $before_data ? transformData($before_data, $displayMap)  : null;

echo json_encode([
    'before' => $before_pretty,
    'after'  => $after_pretty
]);
?>
