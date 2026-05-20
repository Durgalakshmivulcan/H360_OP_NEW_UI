<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate. Empty atmt_id → 'add'; non-empty → 'edit'.
requireCan(empty($_POST['atmt_id']) ? 'add' : 'edit', 'AppointmentOnline.php', 'ajax');


$msg = 0;

$appointDate = $_POST['appointDate2'];
$atmt_id = $_POST['atmt_id'];
$AppointIDs = $_POST['AppointIDs'];
$patient_name = $_POST['patient_name2'];
$appoint_unicode = $_POST['appoint_unicode2'];
$appointID = $_POST['appointID2'];
$gender = $_POST['gender2'];
$temperature = $_POST['temperature2'];
$age = $_POST['age2'];
$mobile_number = $_POST['mobile_number2'];
$patient_email = $_POST['patient_email2'];
$appoint_date = $_POST['appoint_date2'];
$doctor_name = $_POST['doctor_name2'];
$start_time = $_POST['start_time2'];
$end_time = $_POST['end_time2'];
$organizations = $_POST['E_organizations2'];
$payment_method = $_POST['payment_method2'];
$payment = $_POST['payment2'];
$bill_id = $_POST['billId2'];

$bpSit_systolic = $_POST['bpSit_systolic2'];
$bpSit_diastolic = $_POST['bpSit_diastolic2'];
$bpStand_systolic = $_POST['bpStand_systolic2'];
$bpStand_diastolic = $_POST['bpStand_diastolic2'];
$weight = $_POST['weight2'];
$height = $_POST['height2'];
$bmi = $_POST['bmi2'];
$grbs = $_POST['grbs2'];
$heart_rate = $_POST['heart_rate2'];
$respiration_rate = $_POST['respiration_rate2'];
$spO2 = $_POST['spO22'];
$patient_overview = $_POST['patient_overview2'];
$valid_from_date = new DateTime();
$valid_to_date = clone $valid_from_date;
$valid_to_date->modify('+13 days');

$valid_from_date = $valid_from_date->format('Y-m-d');
$valid_to_date = $valid_to_date->format('Y-m-d');

if (
    $patient_name != "" && $appoint_unicode != "" && $gender != "" && $mobile_number != "" && $age != ""  &&
    $appoint_date != "" && $doctor_name != "" && $start_time != "" && $end_time != "" && $payment != ""
) {
    if ($atmt_id != "") {
        $UpdateAppointmentData = mysqli_query($conn, "
            UPDATE appointment_existing 
            SET appoint_id='$AppointIDs', bill_id='$bill_id', patient_name='$patient_name',
                appoint_unicode='$appoint_unicode', amount_method='$payment_method', amount='$payment',
                appoint_register_id='$appointID', gender='$gender', temperature='$temperature',
                age='$age', mobile_number='$mobile_number', patient_email='$patient_email',
                appoint_date='$appoint_date', doctor_name='$doctor_name', start_time='$start_time',
                end_time='$end_time', modified_by='$SessionUserId',
                bpSit_systolic='$bpSit_systolic', bpSit_diastolic='$bpSit_diastolic',
                bpStand_systolic='$bpStand_systolic', bpStand_diastolic='$bpStand_diastolic',
                weight='$weight', height='$height', bmi='$bmi', grbs='$grbs', heart_rate='$heart_rate',
                respiration_rate='$respiration_rate', spO2='$spO2', patient_overview='$patient_overview'
            WHERE atmt_id='$atmt_id'
        ") or die(mysqli_error($conn));

        if ($UpdateAppointmentData) {
            $msg = 2;
        }
    } else {
        $getAppointment = mysqli_query($conn, "
            SELECT * FROM appointment_existing 
            WHERE appoint_status='1' AND mobile_number='$mobile_number' 
                AND start_time='$start_time' AND end_time='$end_time' 
                AND appoint_unicode='$appoint_unicode' AND org_id='$SessionOrgId' 
                AND modified_by='$SessionUserId'
        ") or die(mysqli_error($conn));

        $result = mysqli_num_rows($getAppointment);
        if ($result > 0) {
            $msg = 3;
        } else {
            if ($SessionUserId == "1") {
                if ($AppointIDs != "") {
                    if ($organizations != "") {
                        $InsertAppointmentData = mysqli_query($conn, "
                            INSERT INTO appointment_existing (
                                bill_id, appoint_id, patient_name, appoint_unicode, amount_method, amount,
                                appoint_register_id, gender, temperature, age, mobile_number, patient_email,
                                appoint_date, doctor_name, start_time, end_time, appoint_status, org_id,
                                created_by, modified_by, create_date_time,
                                systolic, diastolic
                                
                            ) VALUES (
                                '$bill_id', '$AppointIDs', '$patient_name', '$appoint_unicode', '$payment_method', '$payment',
                                '$appointID', '$gender', '$temperature', '$age', '$mobile_number', '$patient_email',
                                '$appoint_date', '$doctor_name', '$start_time', '$end_time', '1', '$organizations',
                                '$SessionUserId', '$SessionUserId', '$datetime',
                                '$bpSit_systolic', '$bpSit_diastolic'
                            )
                        ") or die(mysqli_error($conn));

                        if ($InsertAppointmentData) {
                            $msg = 1;
                        }
                    } else {
                        $msg = 5;
                    }
                } else {
                    if ($organizations != "") {
                        $InsertAppointmentData = mysqli_query($conn, "
                            INSERT INTO appointment_online (
                                bill_id, patient_name, appoint_unicode, amount_method, amount, appoint_register_id,
                                gender, temperature, age, mobile_number, patient_email,
                                appoint_date, doctor_name, start_time, end_time, appoint_status, org_id,
                                created_by, modified_by, create_date_time,
                                bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
                                weight, height, bmi, grbs, heart_rate, respiration_rate, spO2, patient_overview ,valid_from ,valid_to
                            ) VALUES (
                                '$bill_id', '$patient_name', '$appoint_unicode', '$payment_method', '$payment', '$appointID',
                                '$gender', '$temperature', '$age', '$mobile_number', '$patient_email',
                                '$appoint_date', '$doctor_name', '$start_time', '$end_time', '1', '$organizations',
                                '$SessionUserId', '$SessionUserId', '$datetime',
                                '$bpSit_systolic', '$bpSit_diastolic', '$bpStand_systolic', '$bpStand_diastolic',
                                '$weight', '$height', '$bmi', '$grbs', '$heart_rate', '$respiration_rate', '$spO2', '$patient_overview' , '$valid_from_date' , '$valid_to_date'
                            )
                        ") or die(mysqli_error($conn));

                        if ($InsertAppointmentData) {
                            $msg = 1;
                        }
                    }
                }
            } else if ($AppointIDs != "") {
                if ($appoint_date != $appointDate) {
                    $InsertAppointmentData = mysqli_query($conn, "
                        INSERT INTO appointment_online (
                                bill_id, patient_name, appoint_unicode, amount_method, amount, appoint_register_id,
                                gender, temperature, age, mobile_number, patient_email,
                                appoint_date, doctor_name, start_time, end_time, appoint_status, org_id,
                                created_by, modified_by, create_date_time,
                                bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
                                weight, height, bmi, grbs, heart_rate, respiration_rate, spO2, patient_overview ,valid_from ,valid_to
                            ) VALUES (
                                '$bill_id', '$patient_name', '$appoint_unicode', '$payment_method', '$payment', '$appointID',
                                '$gender', '$temperature', '$age', '$mobile_number', '$patient_email',
                                '$appoint_date', '$doctor_name', '$start_time', '$end_time', '1', '$organizations',
                                '$SessionUserId', '$SessionUserId', '$datetime',
                                '$bpSit_systolic', '$bpSit_diastolic', '$bpStand_systolic', '$bpStand_diastolic',
                                '$weight', '$height', '$bmi', '$grbs', '$heart_rate', '$respiration_rate', '$spO2', '$patient_overview' , '$valid_from_date' , '$valid_to_date'
                            )
                    ") or die(mysqli_error($conn));

                    if ($InsertAppointmentData) {
                        $msg = 1;
                    }
                }
            } else {
                $InsertAppointmentData = mysqli_query($conn, "
                    INSERT INTO appointment_online (
                        bill_id, patient_name, appoint_unicode, amount_method, amount, appoint_register_id,
                        gender, temperature, age, mobile_number, patient_email,
                        appoint_date, doctor_name, start_time, end_time, appoint_status, org_id,
                        created_by, modified_by, create_date_time,
                        bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
                        weight, height, bmi, grbs, heart_rate, respiration_rate, spO2, patient_overview ,valid_from ,valid_to
                    ) VALUES (
                        '$bill_id', '$patient_name', '$appoint_unicode', '$payment_method', '$payment', '$appointID',
                        '$gender', '$temperature', '$age', '$mobile_number', '$patient_email',
                        '$appoint_date', '$doctor_name', '$start_time', '$end_time', '1', '$SessionOrgId',
                        '$SessionUserId', '$SessionUserId', '$datetime',
                        '$bpSit_systolic', '$bpSit_diastolic', '$bpStand_systolic', '$bpStand_diastolic',
                        '$weight', '$height', '$bmi', '$grbs', '$heart_rate', '$respiration_rate', '$spO2', '$patient_overview' , '$valid_from_date' , '$valid_to_date'
                    )
                ") or die(mysqli_error($conn));

                if ($InsertAppointmentData) {
                    $msg = 1;
                }
            }
        }
    }
}

echo $msg;
?>