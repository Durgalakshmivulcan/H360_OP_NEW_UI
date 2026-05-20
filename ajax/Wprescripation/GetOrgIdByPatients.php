<?php

require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$rx_group_array = [];
$test_group_array = [];
$tests_array = [];
$medicines_array = [];

$org_id = isset($_POST['org_id']) ? $_POST['org_id'] : '';
// $org_id = filter_var($org_id, FILTER_SANITIZE_STRING);

$org_qry = "";
if($org_id) {
    $org_qry = " AND org_id='$org_id'";
}

// if ($SessionUserId == "1" && $SessionRoleId == "1") {
    $query1 = "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' $org_qry";
    $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

    $query2 = "SELECT DISTINCT(patient_name) FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' $org_qry";
    $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
// } else {
//     $query1 = "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
//     $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

//     $query2 = "SELECT DISTINCT(patient_name) FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
//     $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
// }

$options = array();

while ($row1 = mysqli_fetch_object($result1)) {
    $options[] = $row1->patient_name;
}

while ($row2 = mysqli_fetch_object($result2)) {
    $options[] = $row2->patient_name;
}

$options = array_unique($options, SORT_REGULAR);


$getRXGroups = mysqli_query($conn, "SELECT rx_group_id, rx_group_name FROM rx_groups_names WHERE status='1' $org_qry") or die(mysqli_error($conn));
while ($resRXGroups = mysqli_fetch_object($getRXGroups)) {
    $rx_group_array[] = $resRXGroups;
}


$getTestGroups = mysqli_query($conn, "SELECT test_group_id, test_group_name FROM test_group WHERE status='1' $org_qry") or die(mysqli_error($conn));
while ($resTestGroups = mysqli_fetch_object($getTestGroups)) {
    $test_group_array[] = $resTestGroups;
}


$getTests = mysqli_query($conn, "SELECT test_id, test_name FROM tests WHERE status='1' $org_qry") or die(mysqli_error($conn));
while ($resTests = mysqli_fetch_object($getTests)) {
    $tests_array[] = $resTests;
}


$GetMedicines = mysqli_query($conn, "SELECT medicine_id, medicine_name FROM medicines WHERE status='1' $org_qry") or die(mysqli_error($conn));
while ($resMedicines = mysqli_fetch_object($GetMedicines)) {
    $medicines_array[] = $resMedicines;
}

$PatientNamesArray[] = array(
    'patients' => $options,
    'rx_groups' => $rx_group_array,
    'test_groups' => $test_group_array,
    'tests' => $tests_array,
    'medicines' => $medicines_array,
    'org_id' => $org_id
);


echo json_encode($PatientNamesArray);
?>
