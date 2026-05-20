<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ if (!isset($_RBAC_BODY)) { $_RBAC_RAW = file_get_contents('php://input'); $_RBAC_BODY = json_decode($_RBAC_RAW, true) ?: array(); } requireCan(empty($_RBAC_BODY['prescription_id']) ? 'add' : 'edit', 'prescription.php', 'ajax');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';


$input = file_get_contents('php://input');

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$msg = 0;

$variables = [
    'prescription_id', 'organizations', 'patientIdName', 'mobile_number', 'patientId',
    'appoint_register_id', 'age', 'Gender', 'reviewInput', 'reviewSelect', 'reviewCalculatedDate',
    'finalDiagnosis', 'chiefComplaint', 'pastHistory', 'bpSit_systolic', 'bpSit_diastolic',
    'bpStand_systolic', 'bpStand_diastolic', 'weight', 'height', 'bmi', 'grbs', 'heart_rate',
    'temperature', 'respiration_rate', 'spO2', 'patient_overview'
];

foreach ($variables as $var) {
    $$var = $data[$var] ?? '';
}

$medicine = $data['medicine'] ?? [];
$investigation = $data['investigation'] ?? [];

foreach ($medicine as &$item) {
    $medicine_id = trim($item['medicine_id'] ?? '');
    $medicine_name = trim($item['medicine_name'] ?? '');
    $type_id = trim($item['type_id'] ?? '');
    $type_text = trim($item['type_text'] ?? '');
    $unit_id = trim($item['unit_id'] ?? '');
    $unit_text = trim($item['unit_text'] ?? '');
    $dosage_id = trim($item['dosage_id'] ?? '');
    $when_id = trim($item['when_id'] ?? '');
    $time_id = trim($item['time_id'] ?? '');
    $duration_value = trim($item['duration_value'] ?? '');
    $duration = trim($item['duration'] ?? '');
    $notes = trim($item['notes'] ?? '');
    $med_status = trim($item['med_status'] ?? '');

    $duration = trim($item['timeText'] ?? '');
    $notes = trim($item['dosageText'] ?? '');
    $med_status = trim($item['whenText'] ?? '');

    if ($type_id === '') {
        $type_check_query = "SELECT type_id FROM madicine_type WHERE type_name = '$type_text' LIMIT 1";
        $type_check_result = mysqli_query($conn, $type_check_query);
        if ($type_check_result && mysqli_num_rows($type_check_result) > 0) {
            $type_row = mysqli_fetch_assoc($type_check_result);
            $type_id = $type_row['type_id'];
        } else {
            $insert_type_query = "INSERT INTO madicine_type (type_name, status, c_d_t) 
                                  VALUES ('$type_text', '1', NOW())";
            mysqli_query($conn, $insert_type_query) or die(mysqli_error($conn));
            $type_id = mysqli_insert_id($conn);
        }
        $item['type_id'] = $type_id;
    }

    if ($unit_id === '') {
        $unit_check_query = "SELECT unit_id FROM units WHERE unit_name = '$unit_text' AND org_id = '$SessionOrgId' LIMIT 1";
        $unit_check_result = mysqli_query($conn, $unit_check_query);
        if ($unit_check_result && mysqli_num_rows($unit_check_result) > 0) {
            $unit_row = mysqli_fetch_assoc($unit_check_result);
            $unit_id = $unit_row['unit_id'];
        } else {
            $insert_unit_query = "INSERT INTO units (unit_name, status, create_by, create_date_time, org_id) 
                                  VALUES ('$unit_text', '1', '$SessionUserId', NOW(), '$SessionOrgId')";
            mysqli_query($conn, $insert_unit_query) or die(mysqli_error($conn));
            $unit_id = mysqli_insert_id($conn);
        }
        $item['unit_id'] = $unit_id;
    }

    if ($medicine_id === '') {
        $medicine_check_query = "SELECT medicine_id FROM medicines WHERE medicine_name = '$medicine_name' AND org_id = '$SessionOrgId' LIMIT 1";
        $medicine_check_result = mysqli_query($conn, $medicine_check_query);
        if ($medicine_check_result && mysqli_num_rows($medicine_check_result) > 0) {
            $medicine_row = mysqli_fetch_assoc($medicine_check_result);
            $medicine_id = $medicine_row['medicine_id'];
        } else {
            $insert_medicine_query = "INSERT INTO medicines (org_id, medicine_type, medicine_name, scientific_name, dosage, gst, price, notes, status, created_by, modifeid_by, c_d_t) 
                                      VALUES ('$SessionOrgId', '$type_id', '$medicine_name', '$medicine_name', '$unit_text', '', 0, '', '1', '$SessionUserId', '$SessionUserId', NOW())";
            mysqli_query($conn, $insert_medicine_query) or die(mysqli_error($conn));
            $medicine_id = mysqli_insert_id($conn);
        }
        $item['medicine_id'] = $medicine_id;
    }
}

foreach ($investigation as &$item) {

    $test_id = trim($item['test_id'] ?? '');
    $test_name = trim($item['test_name'] ?? '');
    $instruction = trim($item['instruction'] ?? '');
    $doctor_price = trim($item['doctor_price'] ?? '');
    $standard_price = trim($item['standard_price'] ?? '');
    $test_status = trim($item['test_status'] ?? '');
    $test_group_id = trim($item['test_group_id'] ?? ''); 
    $test_group_name = trim($item['test_group_name'] ?? ''); 
    $test_group_price = trim($item['test_group_price'] ?? '');

    $test_id = '';
    $test_query = "SELECT test_id FROM tests WHERE test_name = '$test_name' AND org_id = '$SessionOrgId' LIMIT 1";
    $test_result = mysqli_query($conn, $test_query);
    if ($test_row = mysqli_fetch_assoc($test_result)) {
        $test_id = $test_row['test_id'];
    } else {
        $insert_test_query = "INSERT INTO tests (test_name, test_price, test_gst, status, created_by, modified_by, create_date_time, org_id) VALUES ('$test_name', '$doctor_price', '0', '1', '$SessionUserId', '$SessionUserId', NOW(), '$SessionOrgId')";
        mysqli_query($conn, $insert_test_query);
        $test_id = mysqli_insert_id($conn);
    }

    $item['test_id'] = $test_id;
}

$medicine_json = json_encode($medicine);
$investigation_json = json_encode($investigation);

$updateQuery = "
    UPDATE appointment_online
    SET
        bpSit_systolic = '$bpSit_systolic',
        bpSit_diastolic = '$bpSit_diastolic',
        bpStand_systolic = '$bpStand_systolic',
        bpStand_diastolic = '$bpStand_diastolic',
        weight = '$weight',
        height = '$height',
        bmi = '$bmi',
        grbs = '$grbs',
        heart_rate = '$heart_rate',
        temperature = '$temperature',
        respiration_rate = '$respiration_rate',
        spO2 = '$spO2',
        patient_overview = '$patient_overview'
    WHERE appoint_id = (
        SELECT appoint_id FROM (
            SELECT appoint_id FROM appointment_online
            WHERE appoint_register_id = '$appoint_register_id' 
              AND appoint_unicode = '$patientId' 
              AND org_id = '$organizations'
            ORDER BY create_date_time DESC
            LIMIT 1
        ) AS subquery
    )
";

$reviewafter = $reviewInput . ' ' . $reviewSelect;
if (!empty($prescription_id)) {
    $org_id = ($SessionUserId == "1") ? $organizations : $SessionOrgId;

    $update_query = "UPDATE prescripition SET
        patient_name = '$patientIdName',
        appoint_register_id = '$appoint_register_id',
        patient_uid = '$patientId',
        age = '$age',
        gender = '$Gender',
        rx_id = '',
        test_group_id = '',
        test_id = '$investigation_json',
        medicine_id = '$medicine_json',
        prescriptiondate = NOW(),
        patient_vitals = '$appoint_register_id',
        finalDiagnosis = '$finalDiagnosis',
        chiefcomplaint = '$chiefComplaint',
        pasthistory = '$pastHistory',
        reviewafter = '$reviewafter',
        reviewafterdate = '$reviewCalculatedDate',
        status = '1',
        modify_by = '$SessionUserId',
        prescription_status = 'R',
        org_id = '$org_id'
        WHERE prescription_id = '$prescription_id'";
    mysqli_query($conn, $update_query) or die(mysqli_error($conn));

    $msg = 2;
} else {
    $org_id = ($SessionUserId == "1") ? $organizations : $SessionOrgId;

    $insert_query = "INSERT INTO prescripition (
        patient_name, appoint_register_id, patient_uid, age, gender, rx_id,
        test_group_id, test_id, medicine_id, prescriptiondate, patient_vitals,
        finalDiagnosis, chiefcomplaint, pasthistory, reviewafter, reviewafterdate,
        status, create_date_time, create_by, modify_by, org_id, prescription_status
    ) VALUES (
        '$patientIdName', '$appoint_register_id', '$patientId', '$age', '$Gender', '',
        '', '$investigation_json', '$medicine_json', NOW(), '$appoint_register_id',
        '$finalDiagnosis', '$chiefComplaint', '$pastHistory', '$reviewafter', '$reviewCalculatedDate',
        '1', NOW(), '$SessionUserId', '$SessionUserId', '$org_id', 'R'
    )";
    mysqli_query($conn, $insert_query) or die(mysqli_error($conn));

    $msg = 1;
}




// echo json_encode($duration);
// if ($drugName !="") {
//     if($prescription_id != "") {
//         if($SessionUserId == "1"){
//             if($organizations != ""){
//                 $UpdatePrescData = mysqli_query($conn, "UPDATE prescripition SET patient_name='$patientIdName', patient_uid='$patientId',appoint_register_id='$appoint_register_id' ,age='$age', gender='$Gender', rx_id='$rx_id',test_group_id='$test_group_id', test_id='$tests', modify_by='$SessionUserId', org_id='$organizations' WHERE prescription_id='$prescription_id'") or die(mysqli_error($conn));
//                 $deleteOldPrescData = mysqli_query($conn, "DELETE FROM prescription_medicines WHERE prescription_id='$prescription_id'") or die(mysqli_error($conn));
//                 for ($i=0; $i < count($drugName); $i++) {
//                     $inTakestr = join(',', $inTake[$i]);
//                     // $teststr = join(',', $tests[$i]);
//                     $InserPrescSubData = mysqli_query($conn, "INSERT INTO prescription_medicines(prescription_id, medicine_id, type_id,unit_id, dosage_id, intake_id, time_id, frequency_ids, duration, quantity, note, status, created_by, create_date_time, modified_by, modified_date_time, org_id) VALUES ('$prescription_id', '$drugName[$i]','$Type[$i]','$unitName[$i]', '$dosage[$i]','$inTakestr','$Time[$i]','$frequency[$i]','$duration[$i]','$quantity[$i]','$note[$i]','1', '$SessionUserId', '$datetime','$SessionUserId', '$SessionUserId','$organizations')") or die(mysqli_error($conn));
//                 }
//             }
//             $msg = 2;
//         } else{
//             $UpdatePrescData = mysqli_query($conn, "UPDATE prescripition SET patient_name='$patientIdName', patient_uid='$patientId',appoint_register_id='$appoint_register_id', age='$age', gender='$Gender', rx_id='$rx_id',test_group_id='$test_group_id', test_id='$tests', modify_by='$SessionUserId' WHERE prescription_id='$prescription_id'") or die(mysqli_error($conn));
//             $deleteOldPrescData = mysqli_query($conn, "DELETE FROM prescription_medicines WHERE prescription_id='$prescription_id'") or die(mysqli_error($conn));
//             for ($i=0; $i < count($drugName); $i++) {
//                 $inTakestr = join(',', $inTake[$i]);
//                 // $teststr = join(',', $tests[$i]);
//                 $InserPrescSubData = mysqli_query($conn, "INSERT INTO prescription_medicines(prescription_id, medicine_id, type_id, unit_id, dosage_id, intake_id, time_id, frequency_ids, duration, quantity, note, status, created_by, create_date_time, modified_by, modified_date_time, org_id) VALUES ('$prescription_id', '$drugName[$i]','$Type[$i]','$unitName[$i]', '$dosage[$i]','$inTakestr','$Time[$i]','$frequency[$i]','$duration[$i]','$quantity[$i]','$note[$i]','1', '$SessionUserId', '$datetime','$SessionUserId', '$SessionUserId','$SessionOrgId')") or die(mysqli_error($conn));
//             }
//             $msg = 2;
//         }
//     } else {
//         if($SessionUserId == "1"){
//             if($organizations != ""){
//                 $InserPrescData = mysqli_query($conn, "INSERT INTO prescripition(patient_name, patient_uid, appoint_register_id, age, gender, rx_id,test_group_id, test_id, status, create_by, modify_by, org_id) VALUES ('$patientIdName', '$patientId', '$appoint_register_id', '$age', '$Gender', '$rx_id','$test_group_id','$tests', '1', '$SessionUserId', '$SessionUserId','$organizations')") or die(mysqli_error($conn));
//                 $prescription_id = mysqli_insert_id($conn);

//                 for ($i=0; $i < count($drugName); $i++) {
//                     $inTakestr = join(',', $inTake[$i]);
//                     // $teststr = join(',', $tests[$i]);
//                     $InserPrescSubData = mysqli_query($conn, "INSERT INTO prescription_medicines(prescription_id, medicine_id, type_id, unit_id, dosage_id, intake_id, time_id, frequency_ids, duration, quantity, note, status, created_by, create_date_time, modified_by, modified_date_time, org_id) VALUES ('$prescription_id', '$drugName[$i]','$Type[$i]','$unitName[$i]', '$dosage[$i]','$inTakestr','$Time[$i]','$frequency[$i]','$duration[$i]','$quantity[$i]','$note[$i]','1', '$SessionUserId', '$datetime','$SessionUserId', '$SessionUserId','$organizations')") or die(mysqli_error($conn));
//                 }
//             }
//             $msg = 1;
//         } else{
//             if($SessionOrgId != ""){
//                 $InserPrescData = mysqli_query($conn, "INSERT INTO prescripition(patient_name, patient_uid, appoint_register_id, age, gender, rx_id,test_group_id, test_id, status, create_by, modify_by, org_id) VALUES ('$patientIdName', '$patientId', '$appoint_register_id', '$age', '$Gender', '$rx_id','$test_group_id','$tests', '1', '$SessionUserId', '$SessionUserId','$SessionOrgId')") or die(mysqli_error($conn));
//                 $prescription_id = mysqli_insert_id($conn);
//                 for ($i=0; $i < count($drugName); $i++) {
//                     $inTakestr = join(',', $inTake[$i]);
//                     // $teststr = join(',', $tests[$i]);
//                     $InserPrescSubData = mysqli_query($conn, "INSERT INTO prescription_medicines(prescription_id, medicine_id, type_id, unit_id, dosage_id, intake_id, time_id, frequency_ids, duration, quantity, note, status, created_by, create_date_time, modified_by, modified_date_time, org_id) VALUES ('$prescription_id', '$drugName[$i]','$Type[$i]','$unitName[$i]', '$dosage[$i]','$inTakestr','$Time[$i]','$frequency[$i]','$duration[$i]','$quantity[$i]','$note[$i]','1', '$SessionUserId', '$datetime','$SessionUserId', '$SessionUserId','$SessionOrgId')") or die(mysqli_error($conn));
//                 }
//                 $msg = 1;
//             }
//         }
//     }
// }
echo $msg;
return;

