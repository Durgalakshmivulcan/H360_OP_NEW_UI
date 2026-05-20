<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];
$customValue = $_POST['customValue'];
$appointId = $_POST['appointId'];

$selectFields = "
    DISTINCT(mobile_number),
    appoint_unicode,
    appoint_register_id,
    gender,
    appoint_date,
    systolic,
    diastolic,
    temperature,
    glucose_level,
    age,
    dob,
    patient_email,
    amount_method,
    appoint_id,
    org_id,
    bpSit_systolic,
    bpSit_diastolic,
    bpStand_systolic,
    bpStand_diastolic,
    weight,
    height,
    bmi,
    heart_rate,
    grbs,
    spO2,
    patient_overview,
    respiration_rate
";

if ($SessionUserId == "1") {
    $getAppoint = mysqli_query($conn, "SELECT $selectFields FROM appointment_online WHERE appoint_status='1' AND appoint_id='$customValue'") or die(mysqli_error($conn));
} else {
    $getAppoint = mysqli_query($conn, "SELECT $selectFields FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_id='$customValue'") or die(mysqli_error($conn));
}

$uniqueCombinations = array(); 
while ($resAppoint = mysqli_fetch_object($getAppoint)) {
    $old_date = $resAppoint->appoint_date;
    $currentDate;
    $diff = strtotime($currentDate) - strtotime($old_date);
    $daysDifference = $diff / (60 * 60 * 24);
    $Key = $resAppoint->appoint_id;

    if ($daysDifference > 10) {
        $Id = $appointId;
        $Keyvalue = "";
    } else {
        $Id = $resAppoint->appoint_register_id;
        $Keyvalue = $Key;
    }

    $combination = $resAppoint->mobile_number . "_" . $resAppoint->appoint_unicode;

    if (!in_array($combination, $uniqueCombinations)) {
        $uniqueCombinations[] = $combination; 
        $result[] = array(
            'appoint_id' => $Keyvalue,
            'mobile_number' => $resAppoint->mobile_number,
            'appoint_unicode' => $resAppoint->appoint_unicode,
            'appoint_register_id' => $Id,
            'gender' => $resAppoint->gender,
            'appoint_date' => $old_date,
            'systolic' => $resAppoint->systolic,
            'diastolic' => $resAppoint->diastolic,
            'temperature' => $resAppoint->temperature,
            'glucose_level' => $resAppoint->glucose_level,
            'patient_age' => $resAppoint->age,
            'patient_dob' => $resAppoint->dob,
            'patient_email' => $resAppoint->patient_email,
            'payment_type' => $resAppoint->amount_method,
            'daysDifference' => $daysDifference,
            'org_id' => $resAppoint->org_id,
            'org_name' => getUserNameByOrgId($conn, $resAppoint->org_id),

            'bpSit_systolic' => $resAppoint->bpSit_systolic,
            'bpSit_diastolic' => $resAppoint->bpSit_diastolic,
            'bpStand_systolic' => $resAppoint->bpStand_systolic,
            'bpStand_diastolic' => $resAppoint->bpStand_diastolic,
            'weight' => $resAppoint->weight,
            'height' => $resAppoint->height,
            'bmi' => $resAppoint->bmi,
            'heart_rate' => $resAppoint->heart_rate,
            'grbs' => $resAppoint->grbs,
            'spO2' => $resAppoint->spO2,
            'patient_overview' => $resAppoint->patient_overview,
            'respiration_rate' => $resAppoint->respiration_rate
        );
    }
}

echo json_encode($result);
?>
