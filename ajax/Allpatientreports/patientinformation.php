<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$org_id    = mysqli_real_escape_string($conn, $_POST['organization_id'] ?? '');
$fieldName = $_POST['fieldName']  ?? '';
$fieldValue = mysqli_real_escape_string($conn, $_POST['fieldValue'] ?? '');

// All select options across every page use appoint_id as their value,
// so any fieldName maps to the appoint_id column.
$fieldMap = [
    'patientName'        => 'appoint_id',
    'mobileNumber'       => 'appoint_id',
    'appointUnicode'     => 'appoint_id',
    'appointRegisterId'  => 'appoint_id',
];

$where = "org_id = '$org_id'";

if (!empty($fieldValue)) {
    // Use the mapped column or fall back to appoint_id for callers that pass HTML element IDs
    $column = $fieldMap[$fieldName] ?? 'appoint_id';
    $where .= " AND $column = '$fieldValue'";
}
// FIX_B_1903: doctor-scope filter
$where .= currentDoctorScopeSql('doctor_name');

$query = mysqli_query($conn, 
        "SELECT 
            patient_name, 
            mobile_number, 
            appoint_unicode, 
            appoint_register_id,
            appoint_id 
        FROM appointment_online 
        WHERE $where LIMIT 1"
        
);

if ($row = mysqli_fetch_assoc($query)) {
    echo json_encode([
        'success' => true,
        'data' => [
            'patientName' => $row['patient_name'],
            'mobileNumber' => $row['mobile_number'],
            'appointUnicode' => $row['appoint_unicode'],
            'appointRegisterId' => $row['appoint_register_id'],
            'appoint_id' => $row['appoint_id']
        ]
    ]);
} else {
    echo json_encode(['success' => false]);
}

?>