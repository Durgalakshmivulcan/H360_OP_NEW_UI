<?php
require_once("config.php");
session_start();

$SessionUserId = $_SESSION['security_id'];
function getUserNameById($conn, $security_id)
{
    $qry = mysqli_query($conn, "SELECT admin_name FROM security WHERE security_id='$security_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->admin_name;
}


$SessionOrgId = $_SESSION['org_id'];
function getUserNameByOrgId($conn, $org_id)
{
    $qry = mysqli_query($conn, "SELECT organization_name FROM organization WHERE org_id='$org_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->organization_name;
}

$SessionOrgId = $_SESSION['org_id'];
function GetUserNameByOrg_Id($conn, $org_id)
{
    $qry = mysqli_query($conn, "SELECT organization_name FROM organization WHERE org_id='$org_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->organization_name;
}

function getMenuNameById($conn, $menu_id)
{
    $qry = mysqli_query($conn, "SELECT menu_name FROM menus WHERE menu_id='$menu_id'") or die(mysqli_error($conn));
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

function getUIMenuNameByLanguageIdAndMenuId($conn, $language_id, $menu_id)
{
    $qry = mysqli_query($conn, "SELECT menu_name FROM ui_menus_languages WHERE language_id='$language_id' AND ui_menu_id='$menu_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->menu_name;
}

function getRoleNameById($conn, $role_id)
{
    $qry = mysqli_query($conn, "SELECT role_name FROM roles WHERE role_id='$role_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->role_name;
}

function getCurrentRoleName($conn)
{
    $role_id = $_SESSION['role_id'] ?? '';
    if (!$role_id) return '';
    return getRoleNameById($conn, $role_id);
}

function ensureUserCodeColumn($conn)
{
    $chk = mysqli_query($conn, "SHOW COLUMNS FROM `security` LIKE 'user_code'");
    if (mysqli_num_rows($chk) === 0) {
        mysqli_query($conn, "ALTER TABLE `security` ADD COLUMN `user_code` VARCHAR(20) DEFAULT NULL");
    }
}

function generateUserCode($conn, $prefix)
{
    $prefix = strtoupper(trim($prefix));
    if ($prefix === '') $prefix = 'U';
    $esc = mysqli_real_escape_string($conn, $prefix);
    $res = mysqli_query($conn, "SELECT user_code FROM security WHERE user_code LIKE '{$esc}%'");
    $maxNum = 0;
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $numPart = substr($row['user_code'], strlen($prefix));
            if (ctype_digit($numPart) && (int)$numPart > $maxNum) $maxNum = (int)$numPart;
        }
    }
    return $prefix . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
}

function ensureRefundColumns($conn)
{
    $columns = [
        'refund_type'   => "VARCHAR(20) DEFAULT NULL",
        'refund_amount' => "DECIMAL(10,2) DEFAULT NULL",
        'refund_reason' => "TEXT DEFAULT NULL",
        'refunded_by'   => "INT(11) DEFAULT NULL",
        'refunded_at'   => "DATETIME DEFAULT NULL",
    ];
    foreach ($columns as $col => $definition) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `invoice` LIKE '$col'");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE `invoice` ADD COLUMN `$col` $definition");
        }
    }
}

function ensureReferralColumns($conn)
{
    $columns = [
        'referred_by'       => "VARCHAR(255) DEFAULT NULL",
        'referral_hospital' => "VARCHAR(255) DEFAULT NULL",
        'referral_type'     => "VARCHAR(20) DEFAULT NULL",
        'referral_notes'    => "TEXT DEFAULT NULL",
    ];
    foreach ($columns as $col => $definition) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `appointment_online` LIKE '$col'");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE `appointment_online` ADD COLUMN `$col` $definition");
        }
    }
}

function getRXGroupById($conn, $rx_id)
{
    $qry = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups WHERE rx_id='$rx_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->rx_group_name;
}

function getRXGroupNameById($conn, $rx_group_id)
{
    $qry = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE rx_group_id='$rx_group_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->rx_group_name;
}

function getAppointmentById($conn, $appoint_unicode, $org_id)
{
    $qry = mysqli_query($conn, "SELECT patient_name FROM appointment_online WHERE appoint_unicode='$appoint_unicode' AND org_id='$org_id'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->patient_name;
}

function getDoctorNameById($conn, $doctors_time_id)
{
    $qry = mysqli_query($conn, "SELECT doctorName_registrationNumber FROM doctors_time_slot WHERE doctors_time_id='$doctors_time_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->doctorName_registrationNumber;
}

function getTestById($conn, $test_id)
{
    $qry = mysqli_query($conn, "SELECT test_name FROM tests WHERE test_id='$test_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->test_name;
}

function getTestGroupById($conn, $test_group_id)
{
    $qry = mysqli_query($conn, "SELECT test_group_name FROM test_group WHERE test_group_id='$test_group_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->test_group_name;
}


function getDepartmentById($conn, $dept_id)
{
    $qry = mysqli_query($conn, "SELECT departmentName FROM department WHERE dept_id='$dept_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->departmentName;
}

function getsarivesById($conn, $service_id)
{
    $qry = mysqli_query($conn, "SELECT service_name FROM services WHERE service_id='$service_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->service_name;
}

function getDoctorById($conn, $doc_id)
{
    $qry = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE doc_id='$doc_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->doctor_name;
}


function getMedicineById($conn, $medicine_id)
{
    $qry = mysqli_query($conn, "SELECT medicine_name,medicine_type FROM medicines WHERE medicine_id='$medicine_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->medicine_name;
    return $res->medicine_type;
}

function gettypeById($conn, $type_id)
{
    $qry = mysqli_query($conn, "SELECT type_name FROM madicine_type WHERE type_id='$type_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->type_name;
}

function getUniteById($conn, $unit_id)
{
    $qry = mysqli_query($conn, "SELECT unit_name FROM units WHERE unit_id='$unit_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->unit_name;
}

function getDosageById($conn, $dosage_id)
{
    $qry = mysqli_query($conn, "SELECT dosages FROM dosage WHERE dosage_id='$dosage_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->dosages;
}

function getInTakeById($conn, $intake_id)
{
    $qry = mysqli_query($conn, "SELECT intake_name FROM in_take_period WHERE intake_id='$intake_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->intake_name;
}

function getFrequencyById($conn, $freq_id)
{
    $qry = mysqli_query($conn, "SELECT freq_name FROM frequency WHERE freq_id='$freq_id' AND status='1'") or die(mysqli_error($conn));
    $res = mysqli_fetch_object($qry);
    return $res->freq_name;
}

function getTimesById($conn, $time_id)
{
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
    $words = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
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
    $words = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
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

function isTrueValue($value)
{
    return $value != null && isset($value) && $value != '';
}


function getTimeSlot($interval, $start_time, $end_time, $date)
{
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

    $ReturnArray = []; // Define output
    $StartTime    = strtotime($date . " " . $start_time); //Get Timestamp
    $EndTime      = strtotime($date . " " . $end_time); //Get Timestamp

    $AddMins  = $interval * 60;

    $i = 0;
    while ($StartTime < $EndTime) {
        $ReturnArray[$i]['slot_start_time'] = date("G:i", $StartTime);
        $StartTime += $AddMins; //Endtime check
        $ReturnArray[$i]['slot_end_time'] = date("G:i", $StartTime);
        $i++;
    }
    // FIX_B_076: drop the trailing slot if its end-time runs past the
    // doctor's stated availability window (over-window slot).
    if (!empty($ReturnArray)) {
        $last = end($ReturnArray);
        if (strtotime($date . ' ' . $last['slot_end_time']) > $EndTime) {
            array_pop($ReturnArray);
        }
    }
    return $ReturnArray;
}

function generateNextDocID($conn, $SessionOrgId, $SessionUserId, $currentDate)
{
    if ($SessionUserId == "1") {
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


function getOrganizationName($mysqli, $org_id)
{
    $org_id = (int)$org_id; // sanitize
    $sql = "SELECT organization_name FROM organization WHERE org_id = $org_id LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['organization_name'];
    }
    return null;
}

function getAdminName($mysqli, $security_id)
{
    $security_id = (int)$security_id; // sanitize
    $sql = "SELECT admin_name FROM security WHERE security_id = $security_id LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['admin_name'];
    }
    return null;
}

function getDoctorName($mysqli, $doc_id)
{
    $doc_id = (int)$doc_id; // sanitize
    $sql = "SELECT doctor_name FROM doctors WHERE doc_id = $doc_id LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return $row['doctor_name'];
    }
    return null;
}

function getDoctorTimeslotDetails($mysqli, $doctors_time_id)
{
    $doctors_time_id = (int)$doctors_time_id; // sanitize
    $sql = "SELECT starting_Time, ending_Time FROM doctors_time_slot2 WHERE doctors_time_id = $doctors_time_id";
    $result = $mysqli->query($sql);

    $time_slots = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $time_slots[] = $row['starting_Time'] . ' - ' . $row['ending_Time'];
        }
    }
    return !empty($time_slots) ? implode(', ', $time_slots) : null;
}




function formatTestGroupName($row)
{
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



function formatPriceAndPercentage($data, array $priceFields = [], array $percentFields = [])
{

    if (is_string($data)) {
        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $data = $decoded;
        } else {
            return $data; // return original if not valid JSON
        }
    }


    if (!is_array($data) || empty($data)) {
        return $data;
    }


    if (empty($priceFields)) {
        $priceFields = ['price', 'test_price', 'doctor_fee', 'amount', 'final_amount', 'test_group_price'];
    }
    if (empty($percentFields)) {

        $percentFields = ['service_GST', 'test_gst', 'gst', 'cgstNumber', 'sgstNumber'];
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


    if (isset($data['concessionName'], $data['concessionValue'], $data['concessionType'])) {
        if (strtolower($data['concessionType']) === 'amount' || strtolower($data['concessionType']) === 'fixed') {
            $data['concession'] = $data['concessionName'] . " (₹" . $data['concessionValue'] . ")";
        } elseif (strtolower($data['concessionType']) === 'percentage' || $data['concessionType'] === '%') {
            $data['concession'] = $data['concessionName'] . " (" . $data['concessionValue'] . "%)";
        } else {
            $data['concession'] = $data['concessionName'];
        }
        unset($data['concessionName'], $data['concessionValue'], $data['concessionType']);
    }

    return $data;
}



function getDepartmentNames($mysqli, $dept_ids)
{
    if (!$dept_ids) return null;

    // Convert to array and sanitize
    $ids = is_array($dept_ids) ? $dept_ids : explode(',', $dept_ids);
    $ids = array_map('intval', $ids);
    $ids = array_filter($ids); // remove 0/invalids

    if (empty($ids)) return null;

    $idList = implode(',', $ids);
    $sql = "SELECT departmentName FROM department WHERE dept_id IN ($idList)";
    $result = $mysqli->query($sql);

    $names = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $names[] = $row['departmentName'];
        }
    }
    return !empty($names) ? implode(', ', $names) : null;
}

function getSpecializationNames($mysqli, $spec_ids)
{
    if (!$spec_ids) return null;

    // Convert to array and sanitize
    $ids = is_array($spec_ids) ? $spec_ids : explode(',', $spec_ids);
    $ids = array_map('intval', $ids);
    $ids = array_filter($ids); // remove 0/invalids

    if (empty($ids)) return null;

    $idList = implode(',', $ids);
    $sql = "SELECT specialtisname FROM specialtis WHERE specialtis_id IN ($idList)";
    $result = $mysqli->query($sql);

    $names = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $names[] = $row['specialtisname'];
        }
    }
    return !empty($names) ? implode(', ', $names) : null;
}



function getTestDetails($mysqli, $testDetails)
{
    if (!is_array($testDetails) || empty($testDetails)) {
        return null;
    }

    $formatted = [];
    foreach ($testDetails as $test) {
        $testId   = $test['test_id'] ?? '';
        $testName = $test['test_name'] ?? '';
        $instruction = $test['instruction'] ?? '';
        $doctorPrice = isset($test['doctor_price']) ? number_format($test['doctor_price'], 2) : '0.00';
        $standardPrice = isset($test['standard_price']) ? number_format($test['standard_price'], 2) : '0.00';

        $formatted[] = sprintf(
            "%s (ID: %s) - Doctor Price: %s, Standard Price: %s%s",
            $testName,
            $testId,
            $doctorPrice,
            $standardPrice,
            $instruction ? ", Instruction: {$instruction}" : ""
        );
    }

    return implode("; ", $formatted);
}
function audit_log($mysqli, $module, $action, $entity, $entity_id, $before = null, $after = null)
{
    $org_id  = (int)($_SESSION['org_id'] ?? 0);
    $user_id = (int)($_SESSION['security_id'] ?? 0);
    $ts      = $mysqli->real_escape_string(date('Y-m-d H:i:s'));
    $ip      = $mysqli->real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');

    // Escape dynamic values
    $module  = $mysqli->real_escape_string($module);
    $action  = $mysqli->real_escape_string($action);
    $entity  = $mysqli->real_escape_string($entity);
    $entity_id = (int)$entity_id;

    $before_json = $before ? $mysqli->real_escape_string(json_encode($before, JSON_UNESCAPED_UNICODE)) : null;
    $after_json  = $after  ? $mysqli->real_escape_string(json_encode($after,  JSON_UNESCAPED_UNICODE)) : null;

    // Build query with proper NULL handling
    $sql = "INSERT INTO audit_log (org_id, user_id, module, action, entity, entity_id, ts, ip, before_json, after_json)
            VALUES ($org_id, $user_id, '$module', '$action', '$entity', $entity_id, '$ts', '$ip', " .
        ($before_json !== null ? "'$before_json'" : "NULL") . ", " .
        ($after_json !== null ? "'$after_json'" : "NULL") . ")";

    $mysqli->query($sql);
}


function transformData($data)
{
    $displayMap = [
        // Services table
        'service_name'       => 'Service Name',
        'price'              => 'Price(₹)',
        'service_GST'        => 'GST(%)',

        // Tests table
        'test_name'          => 'Test Name',
        'test_price'         => 'Test Price(₹)',
        'test_gst'           => 'Test GST(%)',
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
        'doctor_fee'         => 'Doctor Fee(₹)',
        'amount'             => 'Amount(₹)',
        'concession_name'    => 'Concession Name',
        'concession_type'    => 'Concession Type',
        'concession_value'   => 'Concession(%)',
        'final_amount'       => 'Final Amount(₹)',
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
        'test_group_price'   => 'Group Price(₹)',

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

        'departmentName'    => 'Department Name',
        'description'       => 'Description',
        'departmentStatus'  => 'Department Status',
        'type'              => 'Department Type',

        'specialtisname'    => 'Specialization',


        'doc_registration_number' => 'Registration Number',
        'doctor_type'    => 'Doctor Type',
        'phone_number'    => 'Mobile Number',
        'departments'    => 'Department',
        'doctor_specialization' => 'Specialization',
        'time_slot_duration' => 'Time Slot Duration',
        'details'            => 'Details',


        'cgstNumber'  => 'CGST',
        'sgstNumber'  => 'SGST',
        'percentage'  => 'Percentage',

        'concession_name'  => 'Concession Given To',
        'concession_type'  => 'Concession Type',
        'concession_value'  => 'Concession Value',

        'prescriptiondate'  => 'Prescription Date',
        'finalDiagnosis'  => 'Final Diagnosis',
        'chiefcomplaint'  => 'Cheif Complaint',
        'pasthistory'  => 'Past History',
        'personal_note'  => 'Personal Note',
        'reviewafter'  => 'Review After',
        'reviewafterdate'  => 'Review After Date',
        'prescription_status'  => 'Prescription Status',

        'doctor_name' => 'Doctor Name',
        'doctortime_type'  => 'Available Type',
        'available_date'  => 'Available Date',
        'starting_Time' => 'Starting Time',
        'ending_Time' => 'Ending Time',

        'test_details' => 'Test Details',




        // Organization
        'organization_name'  => 'Organization Name',

        // Common fields
        'created_by_name'    => 'Created By',
        'modified_by_name'   => 'Modified By',
        'status_name'        => 'Status',
        // 'c_d_t'              => 'Created Date',
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

function ensureGynaecVitalsColumns($conn) // FIX_B_183
{
    $columns = [
        'bpSit_systolic'    => "VARCHAR(10) DEFAULT NULL",
        'bpSit_diastolic'   => "VARCHAR(10) DEFAULT NULL",
        'bpStand_systolic'  => "VARCHAR(10) DEFAULT NULL",
        'bpStand_diastolic' => "VARCHAR(10) DEFAULT NULL",
        'weight'            => "VARCHAR(10) DEFAULT NULL",
        'height'            => "VARCHAR(10) DEFAULT NULL",
        'bmi'               => "VARCHAR(10) DEFAULT NULL",
        'grbs'              => "VARCHAR(10) DEFAULT NULL",
        'heart_rate'        => "VARCHAR(10) DEFAULT NULL",
        'temperature'       => "VARCHAR(10) DEFAULT NULL",
        'respiration_rate'  => "VARCHAR(10) DEFAULT NULL",
        'spO2'              => "VARCHAR(10) DEFAULT NULL",
        'patient_overview'  => "TEXT DEFAULT NULL",
    ];
    $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'gynaec_prescriptions'");
    if (!$tableCheck || mysqli_num_rows($tableCheck) === 0) {
        return; // table absent — phantom; skip silently
    }
    foreach ($columns as $col => $definition) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM `gynaec_prescriptions` LIKE '$col'");
        if ($check && mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE `gynaec_prescriptions` ADD COLUMN `$col` $definition");
        }
    }
}

/**
 * FIX_B_1801 / FIX_B_1810 — per-action RBAC.
 *
 * Returns true if the current session-bearing user has the given action
 * permission on the menu identified by $menu_url (e.g. 'doctor.php').
 *
 * Super Admin (security_id=1 OR role_id=1) is always allowed — bootstrap
 * + lockout safety. Otherwise we look up the role's per-menu permissions
 * SET column and ask FIND_IN_SET($action, permissions) > 0.
 *
 * Use at three layers:
 *   - Page top (after require_once header.php): requireCan('view', basename(__FILE__))
 *   - AJAX endpoint top (POST/GET handlers):  requireCan('add'|'edit'|'delete', $menu_url, 'ajax')
 *   - PHP-rendered Add/Edit/Delete buttons:   <?php if (userCan('edit', $menu_url)) { ?> ... <?php } ?>
 *
 * Action must be one of: view, add, edit, delete. Anything else returns false.
 *
 * @param string  $action   one of view|add|edit|delete
 * @param string  $menuUrl  basename of the page (e.g. 'doctor.php')
 * @param mysqli  $conn     optional override; falls back to global $conn
 * @return bool
 */
function userCan($action, $menuUrl, $conn = null) {
    if ($conn === null) { global $conn; }
    static $cache = [];

    $action  = strtolower(trim((string) $action));
    $menuUrl = trim((string) $menuUrl);
    if (!in_array($action, ['view', 'add', 'edit', 'delete'], true)) return false;
    if ($menuUrl === '') return false;

    $sessionUserId = $_SESSION['security_id'] ?? '';
    $sessionRoleId = $_SESSION['role_id'] ?? '';
    if ($sessionUserId === '' || $sessionRoleId === '') return false;

    // Super-admin override — bootstrap + lockout safety.
    if ((string) $sessionUserId === '1' || (string) $sessionRoleId === '1') return true;

    $key = $sessionRoleId . '|' . $action . '|' . $menuUrl;
    if (isset($cache[$key])) return $cache[$key];

    if (!$conn) { return false; }
    $a = mysqli_real_escape_string($conn, $action);
    $u = mysqli_real_escape_string($conn, $menuUrl);
    $r = mysqli_real_escape_string($conn, (string) $sessionRoleId);

    $sql = "
        SELECT 1
          FROM role_menus rm
          JOIN menus m ON m.menu_id = rm.menu_id
         WHERE rm.role_id = '$r'
           AND m.menu_web_url = '$u'
           AND FIND_IN_SET('$a', rm.permissions) > 0
         LIMIT 1
    ";
    $res = @mysqli_query($conn, $sql);
    $cache[$key] = ($res && mysqli_num_rows($res) > 0);
    return $cache[$key];
}

/**
 * Ergonomic helper: redirect to dashboard.php (or send 403 JSON on AJAX) when
 * the current user lacks the action permission. Use at the top of pages /
 * write endpoints to enforce in one line.
 *
 * FIX_B_1810: handle the case where output has already been emitted by
 * upstream includes (e.g. ajax/header.php prints <!DOCTYPE html> before
 * the page body even loads). When headers_sent() we fall back to a JS
 * redirect (page mode) or skip the now-noop header() call (ajax mode).
 *
 * @param string $action
 * @param string $menuUrl
 * @param string $mode  'page' (default) issues a Location redirect; 'ajax'
 *                      sends 403 + a JSON {error: 'forbidden'} body and exits.
 */
function requireCan($action, $menuUrl, $mode = 'page') {
    if (userCan($action, $menuUrl)) return;
    if ($mode === 'ajax') {
        if (!headers_sent()) {
            http_response_code(403);
            header('Content-Type: application/json');
        }
        echo json_encode(['error' => 'forbidden', 'action' => $action, 'menu' => $menuUrl]);
        exit;
    }
    if (!headers_sent()) {
        header('Location: dashboard.php');
    } else {
        echo '<script>window.location="dashboard.php";</script>';
    }
    exit;
}

/**
 * FIX_B_1901: per-doctor specialization access check.
 *
 * Returns true if the current session-bearing user is allowed to access the
 * given menu_url given menus.restricted_to_specializations and
 * menus.excluded_specializations. SA + non-doctor sessions are always allowed.
 *
 * Use at the top of pages whose menu carries a specialization restriction
 * (currently: prescription.php, gynaec_prescription.php). Pairs with the
 * sidebar filter in ajax/header.php so the visibility model is consistent.
 *
 * @param string $menuUrl basename of the page (e.g. 'gynaec_prescription.php')
 * @return bool
 */
function userMaySeeBySpecialization($menuUrl) {
    global $conn;
    $sessionUserId = $_SESSION['security_id'] ?? '';
    $sessionRoleId = $_SESSION['role_id'] ?? '';
    if ($sessionUserId === '' || $sessionRoleId === '') return false;
    if ((string) $sessionUserId === '1' || (string) $sessionRoleId === '1') return true;

    $u = mysqli_real_escape_string($conn, $menuUrl);
    $r = mysqli_real_escape_string($conn, (string) $sessionUserId);

    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT m.restricted_to_specializations AS r, m.excluded_specializations AS e,
                (SELECT doctor_specialization FROM doctors WHERE security_id='$r' LIMIT 1) AS spec,
                (SELECT COUNT(*) FROM doctors WHERE security_id='$r') AS is_doctor
           FROM menus m WHERE m.menu_web_url='$u' AND m.status='1' LIMIT 1"));
    if (!$row) return true; // unknown menu — fall through to other gates
    if ((int) $row['is_doctor'] === 0) return true; // not a doctor → no spec gating
    $spec = $row['spec'];
    $r_ok = empty($row['r']) || in_array($spec, array_map('trim', explode(',', $row['r'])), true);
    $e_ok = empty($row['e']) || !in_array($spec, array_map('trim', explode(',', $row['e'])), true);
    return $r_ok && $e_ok;
}

/**
 * Companion: redirect-or-403 if specialization gate fails.
 */
function requireSpecializationFor($menuUrl, $mode = 'page') {
    if (userMaySeeBySpecialization($menuUrl)) return;
    if ($mode === 'ajax') {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'forbidden_by_specialization', 'menu' => $menuUrl]);
        exit;
    }
    header('Location: dashboard.php');
    if (!headers_sent()) exit;
    echo "<script>location.replace('dashboard.php');</script>"; exit;
}

/**
 * FIX_B_1902: per-doctor patient/appointment scope.
 *
 * Returns a SQL fragment to AND into queries against `appointment_online`,
 * `prescripition`, `gynaec_prescriptions`, `patient_test_billing`, etc. so a
 * doctor only sees their own patients/appointments/Rx.
 *
 * Resolves the current session's doc_id from the doctors table. If the user
 * is NOT a doctor (SA, plain Admin without a doctors row, Receptionist,
 * Pharmacist, Accountant), returns an empty string — they see everything
 * within their org_id scope. SA security_id=1 always sees everything.
 *
 * @param string $column The column on the target table that holds the doc_id
 *                       (commonly `doctor_name` on appointment_online; some
 *                       tables use `doc_id` or `doctor_id`).
 * @return string SQL fragment (' AND <column>=...') or '' for non-doctors.
 */
/* FIX_B_23420: read security.can_switch_doctor LIVE on every request rather
 * than caching in $_SESSION['_can_switch_doctor']. The cache caused two bugs:
 *   (a) stale-after-flip — admin un-checks the flag for a receptionist who is
 *       already logged in; her existing session keeps the switcher visible
 *       AND setdoctorfilter.php keeps accepting writes (until she logs out).
 *   (b) prime-order — direct AJAX hits (e.g. setdoctorfilter.php) before any
 *       page that includes ajax/header.php returned 403 even for valid
 *       opted-in receptionists, because the cache had never been populated.
 * One indexed PK lookup per request is negligible; correctness > microseconds.
 * Returns 1 or 0. SA bypasses (caller checks role first).
 */
function canSwitchDoctorLive($conn) {
    $sid = (int) ($_SESSION['security_id'] ?? 0);
    if ($sid <= 0) return 0;
    $r = mysqli_query($conn, "SELECT can_switch_doctor FROM security WHERE security_id='$sid' LIMIT 1");
    if (!$r) return 0;
    $row = mysqli_fetch_assoc($r);
    return (int) ($row['can_switch_doctor'] ?? 0);
}

function currentDoctorScopeSql($column = 'doctor_name') {
    global $conn;
    $sessionUserId = $_SESSION['security_id'] ?? '';
    $sessionRoleId = $_SESSION['role_id'] ?? '';
    if ((string) $sessionUserId === '1' || (string) $sessionRoleId === '1') return ''; // SA bypass
    if ($sessionUserId === '') return '';
    $col = preg_replace('/[^a-zA-Z0-9_.]/', '', $column); // whitelist column name

    // FIX_B_2002 + FIX_B_2320: clinic-wide multi-doctor roles can OPTIONALLY
    // narrow the view to one doctor via the header doctor-switcher widget.
    //   Admin       (role_id=6, e.g. Dinesh)  — original consumer (FIX_B_2002).
    //   Receptionist (role_id=3)              — single receptionist handling
    //     two OP rooms; switcher lets her focus on one doctor's queue when
    //     one side is busy (FIX_B_2320).
    // Choice persists in $_SESSION['admin_doctor_filter']:
    //   'all' | '' | '0' → see everything (default)
    //   '<doc_id>'       → scope to that doctor only
    if ((string) $sessionRoleId === '6') {
        $f = $_SESSION['admin_doctor_filter'] ?? 'all';
        if ($f === 'all' || $f === '' || $f === '0') return '';
        return " AND $col='" . (int) $f . "' ";
    }
    // FIX_B_2320: Receptionist (role_id=3) only consumes the filter when the
    // admin has flipped security.can_switch_doctor=1 on her account. Without
    // the opt-in flag a receptionist sees the merged queue across both doctors.
    if ((string) $sessionRoleId === '3' && canSwitchDoctorLive($conn)) {
        $f = $_SESSION['admin_doctor_filter'] ?? 'all';
        if ($f === 'all' || $f === '' || $f === '0') return '';
        return " AND $col='" . (int) $f . "' ";
    }

    // Doctor self-scope (existing behavior — security_id ↔ doctors.security_id).
    $sUid = mysqli_real_escape_string($conn, (string) $sessionUserId);
    $row = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT doc_id FROM doctors WHERE security_id='$sUid' AND status='1' LIMIT 1"));
    if (!$row || empty($row['doc_id'])) return ''; // not a doctor
    $doc = (int) $row['doc_id'];
    return " AND $col='$doc' ";
}

// FIX_B_2352: every existing row in `doctors` already has a "Dr." (or "Dr ")
// prefix on doctor_name. UI sites that naively prepended "Dr. " produced
// "Dr. Dr.Ashwin Kumar Panda". Normalise here so callers never double-prefix.
// Returns the name with exactly one canonical "Dr. " prefix.
function formatDoctorName($name) {
    $n = trim((string) $name);
    if ($n === '') return '';
    // strip any leading "Dr." / "Dr " / "DR." / "doctor " then re-add canonical
    $stripped = preg_replace('/^(dr\.?|doctor)\s*/i', '', $n);
    if ($stripped === '') $stripped = $n;   // belt + braces
    return 'Dr. ' . $stripped;
}
