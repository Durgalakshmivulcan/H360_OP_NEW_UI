<?php
require_once("../../config/functions.php");
header("Content-Type: application/json; charset=utf-8");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$doctors = [];

// --- Get current user's security type ---
$securityType = '';
$checkDoctor = mysqli_query($conn, "
    SELECT security_type 
    FROM security 
    WHERE status='1' AND security_id = '$SessionUserId'
");
if ($checkDoctor && mysqli_num_rows($checkDoctor) > 0) {
    $row = mysqli_fetch_assoc($checkDoctor);
    $securityType = $row['security_type'] ?? '';
}

// --- Build query based on role ---
if ($securityType === 'A') {
    // Super Admin: all doctors
    $sql = "SELECT doc_id, doctor_name 
            FROM doctors 
            WHERE status='1' 
            ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    // Receptionist: only own + assigned doctors
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status='1'
              AND (
                  d.security_id = '$SessionUserId'
                  OR d.doc_id IN (
                      SELECT r.doc_id 
                      FROM receptionnist r 
                      WHERE r.security_id = '$SessionUserId'
                  )
              )
            ORDER BY d.doctor_name ASC";
} else {
    // Default: return all doctors
    $sql = "SELECT doc_id, doctor_name 
            FROM doctors 
            WHERE status='1' 
            ORDER BY doctor_name ASC";
}

// --- Execute query ---
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $doctors[] = $row;
    }
} else {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

// --- Return JSON ---
echo json_encode(["doctors" => $doctors]);
exit;
