<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';


function generateNextBillID($conn, $SessionOrgId) {
    $sql = "SELECT COUNT(1) FROM (
        SELECT 
            appoint_id, bill_id, bill_date, appoint_register_id, 
            appoint_unicode, patient_name, gender, systolic, diastolic, 
            temperature, glucose_level, age, mobile_number, patient_email, 
            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
            appoint_status, visitor_status, org_id, created_by, modified_by, 
            create_date_time, amount_method, amount 
        FROM appointment_online WHERE org_id='$SessionOrgId'

        UNION ALL

        SELECT 
            appoint_id, bill_id, bill_date, appoint_register_id, 
            appoint_unicode, patient_name, gender, systolic, diastolic, 
            temperature, glucose_level, age, mobile_number, patient_email, 
            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
            appoint_status, visitor_status, org_id, created_by, modified_by, 
            create_date_time, amount_method, amount 
        FROM appointment_existing WHERE org_id='$SessionOrgId'
    ) AS combined_table";



    $combinedQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));
    $resDateForPatientId = mysqli_fetch_array($combinedQuery);

        $count = $resDateForPatientId[0];

        $result = $count + 1;

        $patient = 'BID';
        $id = $patient . str_pad($result, 6, '0', STR_PAD_LEFT);

        return $id;
}
$id = generateNextBillID($conn, $SessionOrgId);

echo json_encode($id);
?>
