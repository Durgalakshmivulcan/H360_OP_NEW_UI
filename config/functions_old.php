<?php
require_once("config.php");
session_start();

$SessionUserId = $_SESSION['security_id'] ?? '';
function getUserNameById($conn, $security_id)
{
    $qry = mysqli_query($conn, "SELECT admin_name FROM security WHERE security_id='$security_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->admin_name;
}


$SessionOrgId = $_SESSION['org_id'] ?? '';
function getUserNameByOrgId($conn, $org_id)
{
    $qry = mysqli_query($conn, "SELECT organization_name FROM organization WHERE org_id='$org_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->organization_name;
}

$SessionOrgId = $_SESSION['org_id'] ?? '';
function GetUserNameByOrg_Id($conn, $org_id)
{
    $qry = mysqli_query($conn, "SELECT organization_name FROM organization WHERE org_id='$org_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->organization_name;
}

function getMenuNameById($conn, $menu_id)
{
    $qry = mysqli_query($conn,"SELECT menu_name FROM menus WHERE menu_id='$menu_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->menu_name;
}

function getCareerTitleById($conn, $career_id)
{
    $qry = mysqli_query($conn, "SELECT career_title FROM career WHERE career_id='$career_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->career_title;
}

function getLanguageNameById($conn, $language_id)
{
    $qry = mysqli_query($conn, "SELECT language_name FROM languages WHERE language_id='$language_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->language_name;
}

function getUIMenuNameByLanguageIdAndMenuId($conn, $language_id, $menu_id) {
    $qry = mysqli_query($conn, "SELECT menu_name FROM ui_menus_languages WHERE language_id='$language_id' AND ui_menu_id='$menu_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->menu_name;
}

function getRoleNameById($conn, $role_id) {
    $qry = mysqli_query($conn, "SELECT role_name FROM roles WHERE role_id='$role_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->role_name;
}

function getRXGroupById($conn, $rx_id) {
    $qry = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups WHERE rx_id='$rx_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->rx_group_name;
}

function getRXGroupNameById($conn, $rx_group_id) {
    $qry = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE rx_group_id='$rx_group_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->rx_group_name;
}

function getAppointmentById($conn,$appoint_unicode,$org_id) {
    $qry = mysqli_query($conn, "SELECT patient_name FROM appointment_online WHERE appoint_unicode='$appoint_unicode' AND org_id='$org_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->patient_name;
}

function getDoctorNameById($conn,$doctors_time_id) {
    $qry = mysqli_query($conn, "SELECT doctorName_registrationNumber FROM doctors_time_slot WHERE doctors_time_id='$doctors_time_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->doctorName_registrationNumber;
}

function getTestById($conn,$test_id) {
    $qry = mysqli_query($conn, "SELECT test_name FROM tests WHERE test_id='$test_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->test_name;
}

function getTestGroupById($conn,$test_group_id) {
    $qry = mysqli_query($conn, "SELECT test_group_name FROM test_group WHERE test_group_id='$test_group_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->test_group_name;
}


function getDepartmentById($conn,$dept_id) {
    $qry = mysqli_query($conn, "SELECT departmentName FROM department WHERE dept_id='$dept_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->departmentName;
}

function getsarivesById($conn,$service_id) {
    $qry = mysqli_query($conn, "SELECT service_name FROM services WHERE service_id='$service_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->service_name;

}

function getDoctorById($conn,$doc_id){
    $qry = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE doc_id='$doc_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->doctor_name;
}


function getMedicineById($conn, $medicine_id){
    $qry = mysqli_query($conn, "SELECT medicine_name,medicine_type FROM medicines WHERE medicine_id='$medicine_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->medicine_name;
    return $res->medicine_type;
}

function gettypeById($conn, $type_id){
    $qry = mysqli_query($conn, "SELECT type_name FROM madicine_type WHERE type_id='$type_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->type_name;
}

function getUniteById($conn, $unit_id){
    $qry = mysqli_query($conn, "SELECT unit_name FROM units WHERE unit_id='$unit_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->unit_name;
}

function getDosageById($conn, $dosage_id){
    $qry = mysqli_query($conn, "SELECT dosages FROM dosage WHERE dosage_id='$dosage_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->dosages;
}

function getInTakeById($conn, $intake_id){
    $qry = mysqli_query($conn, "SELECT intake_name FROM in_take_period WHERE intake_id='$intake_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->intake_name;
}

function getFrequencyById($conn, $freq_id){
    $qry = mysqli_query($conn, "SELECT freq_name FROM frequency WHERE freq_id='$freq_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->freq_name;
}

function getTimesById($conn, $time_id){
    $qry = mysqli_query($conn, "SELECT time FROM times WHERE time_id='$time_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->time;
}


// numbers to word convert

function numberConvert($number)
{
    if ($number === '') {
        return '';
    }

    $no = floor($number);
    $decimal = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $decimal_words = $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10];
    $paise = ($decimal > 0) ? " and " . $decimal_words . ' Paise' : '';

    $result = "";
    if ($Rupees) {
        $result .= $Rupees . " Rupees";
        if ($paise) {
            $result .= $paise;
        }
    } else {
        $result .= "Zero Rupees";
    }

    $result .= " Only";
    return $result;
}


function convertNumber($number)
{
    // Validate the input
    if (!is_numeric($number)) {
        return 'N/A';
    }
    if ($number === '') {
        return '';
    }

    $no = floor($number);
    $decimal = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $decimal_words = $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10];
    $paise = ($decimal > 0) ? " and " . $decimal_words . ' Paise' : '';

    $result = "";
    if ($Rupees) {
        $result .= $Rupees . " Rupees";
        if ($paise) {
            $result .= $paise;
        }
    } else {
        $result .= "Zero Rupees";
    }

    $result .= " Only";
    return $result;
}

function isTrueValue($value) {
    return $value != null && isset($value) && $value != '';
}


function getTimeSlot($interval, $start_time, $end_time, $date){
    // $start = new DateTime($start_time);
    // $end = new DateTime($end_time);
    // $startTime = $start->format('H:i');
    // $endTime = $end->format('H:i');
    // $i = 0;
    // $time = [];
    // while (strtotime($startTime) <= strtotime($endTime)) {
    //     $start = $startTime;
    //     $end = date('H:i', strtotime('+'.$interval.' minutes', strtotime($startTime)));
    //     $startTime = date('H:i', strtotime('+'.$interval.' minutes', strtotime($startTime)));
    //     if (strtotime($startTime) <= strtotime($endTime)) {
    //         $time[$i]['slot_start_time'] = $start;
    //         $time[$i]['slot_end_time'] = $end;
    //     }
    //     $i++;
    // }
    // return $time;

    $ReturnArray =[];// Define output
    $StartTime    = strtotime ($date ." " . $start_time); //Get Timestamp
    $EndTime      = strtotime ($date ." " . $end_time); //Get Timestamp

    $AddMins  = $interval * 60;

    $i = 0;
    while ($StartTime < $EndTime) {
        $ReturnArray[$i]['slot_start_time'] = date ("G:i", $StartTime);
        $StartTime += $AddMins; //Endtime check
        $ReturnArray[$i]['slot_end_time'] = date ("G:i", $StartTime);
        $i++;
    }
    return $ReturnArray;
}


function generateNextDocID($conn, $SessionOrgId, $SessionUserId, $currentDate)
{
    if ($SessionUserId =="1") {
        $sql =  "SELECT COUNT(1) FROM doctors WHERE doc_id";
    } else {
        $sql = "SELECT COUNT(1) FROM doctors WHERE org_id='$SessionOrgId' ORDER BY doc_id";
    }

        $combinedQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $resDateForPatientId = mysqli_fetch_array($combinedQuery);
        $count = $resDateForPatientId[0];


        $result = $count + 1;

        $today = date('d-m-Y');
        $dateComponents = explode('-', $today);
        $date = $dateComponents[0];
        $month = $dateComponents[1];
        $year = $dateComponents[2];
        $patient = 'D';
        $id = $patient . $year . $month . $date . str_pad($result, 4, '0', STR_PAD_LEFT);

        return $id;

}


//  // Function to generate a new patient ID based on the maximum existing patient ID and appointment unicode

function generateNewPatientId($conn, $orgId) {
    $getMaxPatientId = mysqli_query($conn, "SELECT patient_id FROM inp_patient_registration WHERE org_id = '$orgId' ORDER BY patient_id DESC LIMIT 1;");
    $getMaxAppointUnicode = mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE org_id = '$orgId' ORDER BY appoint_unicode DESC LIMIT 1;");

    if (!$getMaxPatientId || !$getMaxAppointUnicode) {
        return false; // or throw exception based on your preference
    }

    $maxPatientId = mysqli_fetch_assoc($getMaxPatientId)['patient_id'] ?? '';
    $maxAppointUnicode = mysqli_fetch_assoc($getMaxAppointUnicode)['appoint_unicode'] ?? '';

    if (!empty($maxPatientId) && !empty($maxAppointUnicode)) {
        $maxPatUnicode = ($maxPatientId > $maxAppointUnicode) ? $maxPatientId : $maxAppointUnicode;
    } elseif (!empty($maxPatientId)) {
        $maxPatUnicode = $maxPatientId;
    } elseif (!empty($maxAppointUnicode)) {
        $maxPatUnicode = $maxAppointUnicode;
    } else {
        $maxPatUnicode = '0';
    }

    $nextCountVal = ($maxPatUnicode === '0') ? 1 : ((int)preg_replace('/\D/', '', $maxPatUnicode) + 1);
    return "PAT" . str_pad($nextCountVal, 4, '0', STR_PAD_LEFT);
}

function generateNewAppointmentId($conn, $orgId) {
    $query = mysqli_query($conn, "SELECT appoint_id_val FROM inp_patient_registration WHERE org_id = '$orgId' ORDER BY created_at DESC LIMIT 1;");
    
    if (!$query) {
        return false; // or throw exception
    }

    $row = mysqli_fetch_assoc($query);
    $maxVal = $row['appoint_id_val'] ?? '';

    $nextCountVal = empty($maxVal) ? 1 : ((int)preg_replace('/\D/', '', $maxVal) + 1);

    $currentDate = date('ymd'); // e.g., 250618
    $prefix = "I" . $currentDate; // I250618
    $appointmentId = $prefix . str_pad($nextCountVal, 4, '0', STR_PAD_LEFT); // I2506180001

    return [
        'appointmentId' => $appointmentId,
        'appointVal' => $nextCountVal
    ];
}

function escape($value) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($value ?? ''));
}
function audit_log($mysqli, $module, $action, $entity, $entity_id, $before=null, $after=null) {
  $sql = "INSERT INTO audit_log (org_id,user_id,module,action,entity,entity_id,ts,ip,before_json,after_json)
          VALUES (?,?,?,?,?,?,?,?,?,?)";
  $stmt = $mysqli->prepare($sql);
  $org_id  = $_SESSION['org_id'] ?? '';
  $user_id = $_SESSION['security_id'] ?? '';
  $ts      = date('Y-m-d H:i:s');
  $ip      = $_SERVER['REMOTE_ADDR'] ?? null;
  $before_json = $before ? json_encode($before, JSON_UNESCAPED_UNICODE) : null;
  $after_json  = $after  ? json_encode($after,  JSON_UNESCAPED_UNICODE) : null;
  $stmt->bind_param('iisssissss', $org_id, $user_id, $module, $action,
    $entity, $entity_id, $ts, $ip, $before_json, $after_json);
  $stmt->execute();
}
// function log_action($conn, $org_id, $user_id, $module, $action, $entity, $entity_id, $before = null, $after = null) {
//     $ip = $_SERVER['REMOTE_ADDR'] ?? null;

//     // Convert arrays to JSON safely
//     $before_json = $before ? mysqli_real_escape_string($conn, json_encode($before, JSON_UNESCAPED_UNICODE)) : null;
//     $after_json  = $after ? mysqli_real_escape_string($conn, json_encode($after, JSON_UNESCAPED_UNICODE)) : null;

//     // Escape all other string inputs
//     $module  = mysqli_real_escape_string($conn, $module);
//     $action  = mysqli_real_escape_string($conn, $action);
//     $entity  = mysqli_real_escape_string($conn, $entity);
//     $ip      = mysqli_real_escape_string($conn, $ip);

//     // Build query (use NULL if no before/after JSON)
//     $sql = "INSERT INTO audit_log 
//                 (org_id, user_id, module, action, entity, entity_id, ip, before_json, after_json) 
//             VALUES 
//                 ('$org_id', '$user_id', '$module', '$action', '$entity', '$entity_id', '$ip', " .
//                 ($before_json ? "'$before_json'" : "NULL") . ", " .
//                 ($after_json ? "'$after_json'" : "NULL") . ")";

//     $res = mysqli_query($conn, $sql);
//     if (!$res) {
//         error_log("Audit Log Insert Failed: " . mysqli_error($conn));
//     }
// }
function getOrganizationName($mysqli, $org_id) {
    $sql = "SELECT organization_name FROM organization WHERE org_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $org_id);
    $stmt->execute();
    $stmt->bind_result($organization_name);
    $stmt->fetch();
    $stmt->close();
    return $organization_name ?: null;
}

function getAdminName($mysqli, $security_id) {
    $sql = "SELECT admin_name FROM security WHERE security_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $security_id);
    $stmt->execute();
    $stmt->bind_result($admin_name);
    $stmt->fetch();
    $stmt->close();
    return $admin_name ?: null;
}

function formatTestGroupName($row) {
    $groupName = $row['test_group_name'] ?? '';

    if (!empty($row['test_id'])) {
        $decoded = json_decode($row['test_id'], true);
        if (is_array($decoded)) {
            // Get unique investigation names
            $investigations = array_unique(array_column($decoded, 'investigation'));
            if (!empty($investigations)) {
                $groupName .= ' [' . implode(', ', $investigations) . ']';
            }
        }
    }

    return $groupName;
}



function formatPriceAndPercentage(array $data, array $priceFields = [], array $percentFields = []) {
    // Default fields if not provided
    if (empty($priceFields)) {
        $priceFields = ['price', 'test_price', 'doctor_fee', 'amount', 'final_amount', 'test_group_price'];
    }
    if (empty($percentFields)) {
        $percentFields = ['service_GST', 'test_gst', 'gst', 'concession_value'];
    }

    foreach ($priceFields as $field) {
        if (isset($data[$field]) && $data[$field] !== '') {
            $data[$field] = '₹' . $data[$field];
        }
    }

    foreach ($percentFields as $field) {
        if (isset($data[$field]) && $data[$field] !== '') {
            $data[$field] = $data[$field] . '%';
        }
    }

    return $data;
}

function transformData($data) {
  $displayMap = [
    // Services table
    'service_name'       => 'Service Name',
    'price'              => 'Price (₹)',
    'service_GST'        => 'GST (%)',

    // Tests table
    'test_name'          => 'Test Name',
    'test_price'         => 'Test Price (₹)',
    'test_gst'           => 'Test GST (%)',
    'normal_range'       => 'Normal Range',

    // Appointment / Patient fields
    'patient_name'       => 'Patient Name',
    'gender'             => 'Gender',
    'age'                => 'Age',
    'mobile_number'      => 'Mobile Number',
    'patient_email'      => 'Patient Email',
    'appoint_date'       => 'Appointment Date',
    'doctor_name'        => 'Doctor Name',
    'start_time'         => 'Start Time',
    'end_time'           => 'End Time',
    'doctor_fee'         => 'Doctor Fee (₹)',
    'amount'             => 'Amount (₹)',
    'concession_name'    => 'Concession Name',
    'concession_type'    => 'Concession Type',
    'concession_value'   => 'Concession (%)',
    'final_amount'       => 'Final Amount (₹)',
    'amount_method'      => 'Payment Method',
    'transaction_number' => 'Transaction Number',
    'appointment_status' => 'Appointment Status',
    'patient_history'    => 'Patient History',
    'patient_overview'   => 'Patient Overview',

    // Vitals
    'systolic'           => 'BP Systolic',
    'diastolic'          => 'BP Diastolic',
    'bpSit_systolic'     => 'BP Sitting Systolic',
    'bpSit_diastolic'    => 'BP Sitting Diastolic',
    'bpStand_systolic'   => 'BP Standing Systolic',
    'bpStand_diastolic'  => 'BP Standing Diastolic',
    'temperature'        => 'Temperature',
    'glucose_level'      => 'Glucose Level',
    'weight'             => 'Weight',
    'height'             => 'Height',
    'bmi'                => 'BMI',
    'heart_rate'         => 'Heart Rate',
    'grbs'               => 'GRBS',
    'spO2'               => 'SpO2',
    'respiration_rate'   => 'Respiration Rate',

      // test group table
    'test_group_name'    => 'Test Group Name',
    'test_group_price'   => 'Group Price (₹)',

     // Medicines table
    'medicine_name'      => 'Medicine Name',
    'scientific_name'    => 'Scientific Name',
    'dosage'             => 'Dosage',
    'gst'                => 'GST',
    'price'              => 'Price',
    'notes'              => 'Notes',

     //Security table
    'admin_name'        => 'Admin Name',
    'email'             => 'Email',
    'contact'           => 'Contact Number',
    'image_url'         => 'Profile Image',
    'signature_url'     => 'Signature',
    'role_name'         => 'Role',


    // Organization
    'organization_name'  => 'Organization Name',

    // Common fields
    'created_by_name'    => 'Created By',
    'modified_by_name'   => 'Modified By',
    'status_name'        => 'Status',
    'c_d_t'              => 'Created Date',
    'm_d_t'              => 'Modified Date',
];

    $result = [];
    foreach ($displayMap as $key => $label) {
        if (isset($data[$key])) {
            $result[$label] = $data[$key];
        }
    }
    return $result;
}

?>
