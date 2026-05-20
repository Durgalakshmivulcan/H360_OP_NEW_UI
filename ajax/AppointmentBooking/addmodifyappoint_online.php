<?php
require_once("../../config/functions.php");

// FIX_B_2384: catch any uncaught DB exception and surface a useful body to
// the JS error handler. PHP's display_errors is OFF in production-style
// configs, so an uncaught mysqli_sql_exception used to return an empty
// 500 body — the modal-dismiss swal then showed "Server error." with no
// detail. With this handler the user (and the audit log) get the actual
// SQL/file/line.
set_exception_handler(function ($e) {
    error_log('B-2384 addmodifyappoint_online uncaught: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo 'Save failed: ' . preg_replace('/\s+/', ' ', $e->getMessage());
    exit;
});

ensureReferralColumns($conn);

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate. Empty appoint_id1 → 'add'; non-empty → 'edit'.
requireCan(empty($_POST['appoint_id1']) ? 'add' : 'edit', 'AppointmentOnline.php', 'ajax');


$msg = 0;

// FIX_B_022: scope non-SA UPDATE + audit SELECTs to caller's org_id

$appoint_id = $_POST['appoint_id1'];
$appointID = $_POST['appointID1'];
$patient_name = $_POST['patient_name1'];
$appoint_unicode = $_POST['appoint_unicode1'];
$gender = $_POST['gender1'];
$mobile_number = $_POST['mobile_number1'];
$organizations = $_POST['organizations'];
$patient_email = $_POST['patient_email1'];
$age = $_POST['age1'];
$bpSit_systolic = $_POST['bpSit_systolic1'];
$bpSit_diastolic = $_POST['bpSit_diastolic1'];
$bpStand_systolic = $_POST['bpStand_systolic1'];
$bpStand_diastolic = $_POST['bpStand_diastolic1'];
$weight = $_POST['weight1'];
$height = $_POST['height1'];
$bmi = $_POST['bmi1'];
$heart_rate = $_POST['heart_rate1'];
$temperature = $_POST['temperature1'];
$grbs = $_POST['grbs1'];
$spO2 = $_POST['spO21'];
$respiration_rate = $_POST['respiration_rate1'];
$patient_overview = $_POST['patient_overview1'];
$referred_by       = mysqli_real_escape_string($conn, $_POST['referred_by1']       ?? '');
$referral_hospital = mysqli_real_escape_string($conn, $_POST['referral_hospital1'] ?? '');
$referral_type     = mysqli_real_escape_string($conn, $_POST['referral_type1']     ?? '');
$referral_notes    = mysqli_real_escape_string($conn, $_POST['referral_notes1']    ?? '');
$dob = mysqli_real_escape_string($conn, $_POST['dob1'] ?? '');
// FIX_B_990: empty $dob fed to a `date NULL` column crashes under MariaDB strict mode
// ("Incorrect date value: ''"). Build a SQL fragment that is either a quoted date or
// the literal NULL, and use $dobSql in INSERT/UPDATE statements instead of '$dob'.
$dobSql = ($dob === '' ? 'NULL' : "'$dob'");
$appoint_date = $_POST['appoint_date1'];
$doctor_name = $_POST['doctor_name1'];
$start_time = $_POST['start_time1'];
$end_time = $_POST['end_time1'];
$amount_method = $_POST['amount_method1'];
$amount = $_POST['amount1'];
$bill_id = $_POST['billID1'];
$appointment_status = $_POST['vitals_status'];
$currDate = date('Y-m-d');
$valid_to_date = $_POST['validToDate'];
$transactionNumber1 = $_POST['transactionNumber1'];
$concessionName1 = $_POST['concessionName1'];
$concessionType1 = $_POST['concessionType1'];
$concessionValue1 = $_POST['concessionValue1'];

$final_amount = $amount;

if (!empty($concessionType1) && !empty($concessionValue1)) {
    $amount1 = (float)$amount;
    $concessionValue1 = (float)$concessionValue1;

    if ($concessionType1 === "amount") {
        $final_amount = $amount1 - $concessionValue1;
    } elseif ($concessionType1 === "percentage") {
        $final_amount = $amount - (($amount * $concessionValue1) / 100);
    }
}

$final_amount = max(0, $final_amount);


if($currDate > $valid_to_date) {
    $valid_from_date = new DateTime();
$valid_to_date = clone $valid_from_date;
$valid_to_date->modify('+13 days');

$valid_from_date = $valid_from_date->format('Y-m-d');
$valid_to_date = $valid_to_date->format('Y-m-d');
} else {
$valid_to_date = $_POST['validToDate'];
// FIX_B_056: default valid_from to the appointment date on the
// non-past-date branch so the INSERT does not bind an empty string.
$valid_from_date = !empty($appoint_date) ? $appoint_date : date('Y-m-d');

}

if($bpSit_diastolic || $bpSit_systolic || $bpStand_diastolic || $bpStand_systolic || $weight || $height || $bmi || $grbs || $heart_rate || $temperature || $respiration_rate || $spO2 || $patient_overview) {
    $appointmentStatus = 1;
} else {
    $appointmentStatus = 0;
}



// FIX_B_2383: gender column is ENUM('Male','Female','Others'). Pre-existing
// patient rows occasionally carry empty / NULL / typo'd gender values; when
// the JS autocomplete loaded one of those into the form, the prior weak
// guard ($gender != "") passed and the INSERT then 500'd with "Data
// truncated for column 'gender'" — leaving the user with the modal-blur
// stuck-screen. Reject upfront with a clean 200/"0" so the JS data=='0'
// branch handles it (FIX_B_2382a closes the modal + swals).
if (!in_array($gender, ['Male', 'Female', 'Others'], true)) {
    echo 0;
    exit;
}

if (
    $patient_name != "" && $appoint_unicode != "" && $gender != "" && $mobile_number != "" &&
    $appoint_date != "" && $doctor_name != "" && $start_time != "" && $end_time != "" && $amount != ""
) {
    if ($appoint_id != "") {
             $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$appoint_id'"));
        if ($SessionUserId == "1") {
            if ($organizations != "") {
                $UpdateAppointmentData = mysqli_query($conn, "
                    UPDATE appointment_online SET
                        bill_id='$bill_id',
                        patient_name='$patient_name',
                        appoint_unicode='$appoint_unicode',
                        amount_method='$amount_method',
                        amount='$amount',
                        appoint_register_id='$appointID',
                        gender='$gender',
                        dob=$dobSql,
                        bpSit_systolic='$bpSit_systolic',
                        bpSit_diastolic='$bpSit_diastolic',
                        bpStand_systolic='$bpStand_systolic',
                        bpStand_diastolic='$bpStand_diastolic',
                        weight='$weight',
                        height='$height',
                        bmi='$bmi',
                        heart_rate='$heart_rate',
                        temperature='$temperature',
                        grbs='$grbs',
                        spO2='$spO2',
                        respiration_rate='$respiration_rate',
                        patient_overview='$patient_overview',
                        age='$age',
                        mobile_number='$mobile_number',
                        patient_email='$patient_email',
                        appoint_date='$appoint_date',
                        doctor_name='$doctor_name',
                        start_time='$start_time',
                        end_time='$end_time',
                        modified_by='$SessionUserId',
                        transaction_number='$transactionNumber1',
                        concession_name='$concessionName1',
                        concession_type='$concessionType1',
                        concession_value='$concessionValue1',
                        final_amount='$final_amount',
                        org_id='$organizations',
                        appointment_status='$appointmentStatus',
                        referred_by='$referred_by',
                        referral_hospital='$referral_hospital',
                        referral_type='$referral_type',
                        referral_notes='$referral_notes'
                    WHERE appoint_id='$appoint_id'
                ") or die(mysqli_error($conn));
                if ($UpdateAppointmentData) {
                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$appoint_id'"));

                
                audit_log($conn, "Appointments", "update", "appointment_online", $appoint_id, $before, $after);
                    $msg = 2;
                }
            }
        } else {
            $UpdateAppointmentData = mysqli_query($conn, "
                UPDATE appointment_online SET
                    bill_id='$bill_id',
                    patient_name='$patient_name',
                    appoint_unicode='$appoint_unicode',
                    amount_method='$amount_method',
                    amount='$amount',
                    appoint_register_id='$appointID',
                    gender='$gender',
                    dob=$dobSql,
                    bpSit_systolic='$bpSit_systolic',
                    bpSit_diastolic='$bpSit_diastolic',
                    bpStand_systolic='$bpStand_systolic',
                    bpStand_diastolic='$bpStand_diastolic',
                    weight='$weight',
                    height='$height',
                    bmi='$bmi',
                    heart_rate='$heart_rate',
                    temperature='$temperature',
                    grbs='$grbs',
                    spO2='$spO2',
                    respiration_rate='$respiration_rate',
                    patient_overview='$patient_overview',
                    age='$age',
                    mobile_number='$mobile_number',
                    patient_email='$patient_email',
                    appoint_date='$appoint_date',
                    doctor_name='$doctor_name',
                    start_time='$start_time',
                    end_time='$end_time',
                    modified_by='$SessionUserId',
                    transaction_number='$transactionNumber1',
                    concession_name='$concessionName1',
                    concession_type='$concessionType1',
                    concession_value='$concessionValue1',
                    final_amount='$final_amount',
                    appointment_status='$appointmentStatus',
                    referred_by='$referred_by',
                    referral_hospital='$referral_hospital',
                    referral_type='$referral_type',
                    referral_notes='$referral_notes'
                WHERE appoint_id='$appoint_id' AND org_id='$SessionOrgId'
            ") or die(mysqli_error($conn));
            if ($UpdateAppointmentData) {
           
            $after  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$appoint_id' AND org_id='$SessionOrgId'"));
            audit_log($conn, "Appointments", "update", "appointment_online", $appoint_id, $before, $after);
                $msg = 2;
            }
        }
    } else {
        $getAppointment = mysqli_query($conn, "
            SELECT appoint_unicode, appoint_register_id 
            FROM appointment_online 
            WHERE appoint_status='1' AND 
                  mobile_number='$mobile_number' AND 
                  start_time='$start_time' AND 
                  end_time='$end_time' AND 
                  appoint_unicode='$appoint_unicode' AND 
                  org_id='$SessionOrgId'
        ") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAppointment);
        if ($result > 0) {
            $msg = 3;
        } else {
            $getAppointment = mysqli_query($conn, "
                SELECT mobile_number 
                FROM appointment_online 
                WHERE appoint_status='1' AND 
                      mobile_number='$mobile_number' AND 
                      org_id='$SessionOrgId'
            ") or die(mysqli_error($conn));
            $result = mysqli_num_rows($getAppointment);
            if ($result > 1000) {
                $msg = 3;
            } else {
                if ($SessionUserId == "1") {
                    if ($organizations != "") {
                        $InsertAppointmentData = mysqli_query($conn, "
                            INSERT INTO appointment_online(
                                bill_id, patient_name, appoint_unicode, amount_method, amount, appoint_register_id,
                                gender, dob, bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
                                weight, height, bmi, heart_rate, temperature, grbs, spO2, respiration_rate,
                                patient_overview, transaction_number, concession_name, concession_type, concession_value, final_amount,
                                age, mobile_number, patient_email, appoint_date, doctor_name,
                                start_time, end_time, appoint_status, org_id, created_by, modified_by, create_date_time, valid_from, valid_to, appointment_status,
                                referred_by, referral_hospital, referral_type, referral_notes
                            ) VALUES (
                                '$bill_id', '$patient_name', '$appoint_unicode', '$amount_method', '$amount', '$appointID',
                                '$gender', $dobSql, '$bpSit_systolic', '$bpSit_diastolic', '$bpStand_systolic', '$bpStand_diastolic',
                                '$weight', '$height', '$bmi', '$heart_rate', '$temperature', '$grbs', '$spO2', '$respiration_rate',
                                '$patient_overview', '$transactionNumber1', '$concessionName1', '$concessionType1', '$concessionValue1', '$final_amount',
                                '$age', '$mobile_number', '$patient_email', '$appoint_date', '$doctor_name',
                                '$start_time', '$end_time', '1', '$organizations', '$SessionUserId', '$SessionUserId', '$datetime', '$valid_from_date', '$valid_to_date', '$appointment_status',
                                '$referred_by', '$referral_hospital', '$referral_type', '$referral_notes'
                            )
                        ") or die(mysqli_error($conn));
                        if ($InsertAppointmentData) {
                            $newId = mysqli_insert_id($conn);

                            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$newId'"));

                            audit_log($conn, "Appointments", "create", "appointment_online", $newId, null, $after);
                            $msg = 1;
                        }
                    }
                } else {
                    if ($SessionOrgId != "") {
                        $InsertAppointmentData = mysqli_query($conn, "
                            INSERT INTO appointment_online(
                                bill_id, patient_name, appoint_unicode, amount_method, amount, appoint_register_id,
                                gender, dob, bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
                                weight, height, bmi, heart_rate, temperature, grbs, spO2, respiration_rate,
                                patient_overview, transaction_number, concession_name, concession_type, concession_value, final_amount,
                                age, mobile_number, patient_email, appoint_date, doctor_name,
                                start_time, end_time, appoint_status, org_id, created_by, modified_by, create_date_time, valid_from, valid_to, appointment_status,
                                referred_by, referral_hospital, referral_type, referral_notes
                            ) VALUES (
                                '$bill_id', '$patient_name', '$appoint_unicode', '$amount_method', '$amount', '$appointID',
                                '$gender', $dobSql, '$bpSit_systolic', '$bpSit_diastolic', '$bpStand_systolic', '$bpStand_diastolic',
                                '$weight', '$height', '$bmi', '$heart_rate', '$temperature', '$grbs', '$spO2', '$respiration_rate',
                                '$patient_overview', '$transactionNumber1', '$concessionName1', '$concessionType1', '$concessionValue1', '$final_amount',
                                '$age', '$mobile_number', '$patient_email', '$appoint_date', '$doctor_name',
                                '$start_time', '$end_time', '1', '$SessionOrgId', '$SessionUserId', '$SessionUserId', '$datetime', '$valid_from_date', '$valid_to_date', '$appointment_status',
                                '$referred_by', '$referral_hospital', '$referral_type', '$referral_notes'
                            )
                        ") or die(mysqli_error($conn));
                        if ($InsertAppointmentData) {
                            $newId = mysqli_insert_id($conn);

                            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_id='$newId'"));

                            audit_log($conn, "Appointments", "create", "appointment_online", $newId, null, $after);
                            $msg = 1;
                        }
                    }
                }
            }
        }
    }
}
// $pushMessage = null;
    $fp = @fsockopen('127.0.0.1', 9000, $errno, $errstr, 2);
    if ($fp) {
        $message = json_encode([
            'type' => 'appointment',
            'user' => $SessionUserId,
            'patient_name' => $patient_name,
            'doctor_name' => $doctor_name,
            'appoint_date' => $appoint_date,
            'message' => 'Appointment created or updated'
        ]);
        fwrite($fp, $message . "\n");
        fclose($fp);
    } // else: socket server not running, skip silently
echo $msg;
?>