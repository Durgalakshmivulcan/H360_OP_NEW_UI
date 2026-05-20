<?php
session_start(); // Start the session if not already started

require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// Sanitize the patientIdName input from POST
$patientIdName = isset($_POST['patientIdName']) ? $_POST['patientIdName'] : '';
$patientIdName = filter_var($patientIdName, FILTER_SANITIZE_STRING);

    $query1 = "SELECT DISTINCT(appoint_unicode),appoint_id,mobile_number FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND patient_name='$patientIdName'";
    $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

    $query2 = "SELECT DISTINCT(appoint_unicode),appoint_id,mobile_number FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' AND patient_name='$patientIdName'";
    $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));

$options = array();

while ($row1 = mysqli_fetch_assoc($result1)) {
    $options[] = $row1;
}

while ($row2 = mysqli_fetch_assoc($result2)) {
    $options[] = $row2;
}

$isDataIdentical = count($options) > 0 ? true : false;
$PatientNamesArray = array();

foreach ($options as $option) {
    $Keyvalue1 = $option['appoint_id'];
    $Keyvalue2 = $option['appoint_unicode'];
    $mobile_number = $option['mobile_number'];

    $PatientNamesArray[] = array(
        'Keyvalue1' => $Keyvalue1,
        'Keyvalue2' => $Keyvalue2,
        'mobile_number' => $mobile_number,
    );
}

$PatientNamesArray = array_unique($PatientNamesArray, SORT_REGULAR);



echo json_encode($PatientNamesArray);
?>
