<?php
require_once("../../config/functions.php");
header("Content-Type: application/json; charset=utf-8");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$patient_id = $_GET['patient_id'] ?? 'All';
$doctor_id  = $_GET['doctor_id']  ?? 'All';

$response = ["patients" => [], "doctors" => []];

// Get current user's security type
$securityType = '';
$checkUser = mysqli_query($conn, "
    SELECT security_type 
    FROM security 
    WHERE status='1' AND security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'
");
if ($checkUser && mysqli_num_rows($checkUser) > 0) {
    $row = mysqli_fetch_assoc($checkUser);
    $securityType = $row['security_type'] ?? '';
}

// Base WHERE clause
$where = " WHERE inv.status='1' AND inv.org_id='" . mysqli_real_escape_string($conn, $SessionOrgId) . "' ";

if ($patient_id !== 'All') $where .= " AND inv.patient_id='" . mysqli_real_escape_string($conn, $patient_id) . "' ";
if ($doctor_id !== 'All')  $where .= " AND d.doc_id='" . mysqli_real_escape_string($conn, $doctor_id) . "' ";

// Apply security filter for normal users
if ($securityType === 'U') {
    $where .= " AND (
        d.security_id='" . mysqli_real_escape_string($conn, $SessionUserId) . "' 
        OR d.doc_id IN (
            SELECT r.doc_id FROM receptionnist r WHERE r.security_id='" . mysqli_real_escape_string($conn, $SessionUserId) . "'
        )
    )";
}

// Query patients and doctors
$sql = "SELECT DISTINCT 
            ao.patient_name, 
            inv.patient_id, 
            d.doc_id, 
            d.doctor_name
        FROM invoice inv
        LEFT JOIN appointment_online ao ON ao.appoint_register_id = inv.appointment_id
        LEFT JOIN doctors d ON d.doc_id = ao.doctor_name
        $where
        ORDER BY ao.patient_name ASC";

$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Add patient
        if (!empty($row['patient_id']) && !empty($row['patient_name'])) {
            $response['patients'][$row['patient_id']] = [
                "id" => $row['patient_id'],
                "name" => $row['patient_id'],
                "doctor_id" => $row['doc_id']
            ];
        }

        // Add doctor
        if (!empty($row['doc_id'])) {
            $response['doctors'][$row['doc_id']] = [
                "id" => $row['doc_id'],
                "name" => $row['doctor_name']
            ];
        }
    }

    // Re-index arrays
    $response['patients'] = array_values($response['patients']);
    $response['doctors']  = array_values($response['doctors']);
}

// Return JSON
echo json_encode($response);
exit;
