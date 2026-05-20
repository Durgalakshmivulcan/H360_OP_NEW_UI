<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
$currentDate   = date('Y-m-d');

// FIX_B_1903: doctor-scope filter (empty for SA / non-doctor sessions)
$docScope = currentDoctorScopeSql('a.doctor_name');
$docScopeNoAlias = currentDoctorScopeSql('doctor_name');

if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $appointmentsQuery = "
        SELECT
            a.appoint_id,
            a.patient_name,
            a.appoint_unicode,
            a.appoint_register_id,
            a.start_time,
            a.visitor_status,
            a.appoint_date,
            d.doctor_name
        FROM appointment_online a
        LEFT JOIN doctors d ON d.doc_id = a.doctor_name
        WHERE a.appoint_status='1'
          AND a.appoint_date='$currentDate'
          $docScope
        ORDER BY a.appoint_id ASC
    ";

    $countQuery = "
        SELECT
            visitor_status,
            COUNT(*) as count
        FROM appointment_online
        WHERE appoint_status='1'
          AND appoint_date='$currentDate'
          $docScopeNoAlias
        GROUP BY visitor_status
    ";
} else {
    $appointmentsQuery = "
        SELECT
            a.appoint_id,
            a.patient_name,
            a.appoint_unicode,
            a.appoint_register_id,
            a.start_time,
            a.visitor_status,
            a.appoint_date,
            d.doctor_name
        FROM appointment_online a
        LEFT JOIN doctors d ON d.doc_id = a.doctor_name
        WHERE a.appoint_status='1'
          AND a.org_id='$SessionOrgId'
          AND a.appoint_date='$currentDate'
          $docScope
        ORDER BY a.appoint_id ASC
    ";

    $countQuery = "
        SELECT
            visitor_status,
            COUNT(*) as count
        FROM appointment_online
        WHERE appoint_status='1'
          AND org_id='$SessionOrgId'
          AND appoint_date='$currentDate'
          $docScopeNoAlias
        GROUP BY visitor_status
    ";
}

$appointmentsResult = mysqli_query($conn, $appointmentsQuery);
$appointmentsData = [];
while ($row = mysqli_fetch_assoc($appointmentsResult)) {
    $appointmentsData[] = $row;
}
$countResult = mysqli_query($conn, $countQuery);
$countData = [
    '0' => 0, // Done
    '1' => 0, // Pending
    '2' => 0, // Walked In
    '3' => 0  // Missed
];
while ($row = mysqli_fetch_assoc($countResult)) {
    $countData[$row['visitor_status']] = (int)$row['count'];
}

$response = [
    'appointments' => $appointmentsData,
    'countData'    => $countData
];

echo json_encode($response);
?>
