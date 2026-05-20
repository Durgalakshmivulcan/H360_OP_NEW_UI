<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$org_id = $_POST['organization_id'];
$fieldName = $_POST['fieldName'];
$fieldValue = $_POST['fieldValue'];

$fieldMap = [
    'patient_name' => 'appoint_id',
    'mobile_number' => 'appoint_id',
    'appoint_unicode' => 'appoint_id',
    'appoint_register_id' => 'appoint_id'
];

$where = "org_id = '$org_id'";

if (!empty($fieldName) && isset($fieldMap[$fieldName]) && !empty($fieldValue)) {
    $column = $fieldMap[$fieldName];
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
            'patient_name' => $row['patient_name'],
            'mobile_number' => $row['mobile_number'],
            'appoint_unicode' => $row['appoint_unicode'],
            'appoint_register_id' => $row['appoint_register_id'],
            'appoint_id' => $row['appoint_id']
        ]
    ]);
} else {
    echo json_encode(['success' => false]);
}

?>