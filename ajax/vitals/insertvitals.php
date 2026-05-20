<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan(empty($_POST['vital_id']) ? 'add' : 'edit', 'patienthistory.php', 'ajax');

$msg = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$vital_id = $_POST['vital_id']; 
$patient_id = $_POST['modalAppointId'];

$bpSitPre = !empty($_POST['bpSitpre']) ? $_POST['bpSitpre'] : '-';
$bpSitPost = !empty($_POST['bpSitpost']) ? $_POST['bpSitpost'] : '-';
$bpSit = ($bpSitPre === '-' && $bpSitPost === '-') ? '' : $bpSitPre . "/" . $bpSitPost;

$bpStandPre = !empty($_POST['bpStandpre']) ? $_POST['bpStandpre'] : '-';
$bpStandPost = !empty($_POST['bpStandpost']) ? $_POST['bpStandpost'] : '-';
$bpStand = ($bpStandPre === '-' && $bpStandPost === '-') ? '' : $bpStandPre . "/" . $bpStandPost;

$bmiVal1 = !empty($_POST['bmi']) ? $_POST['bmi'] : '-';
$bmiVal2 = !empty($_POST['bmi1']) ? $_POST['bmi1'] : '-';
$bmi = ($bmiVal1 === '-' && $bmiVal2 === '-') ? '' : $bmiVal1 . "-" . $bmiVal2;


$weight = $_POST['weight'];
$height = $_POST['height'];
$grbs = $_POST['grbs'];
$heartRate = $_POST['heartRate'];
$temperature = $_POST['temperature'];
$respiration = $_POST['respiration'];
$spo2 = $_POST['spo2'];
$bloodGroup = $_POST['bloodGroup'];
$cpap = $_POST['cpap'];
$hfnc = $_POST['hfnc'];
$vo2 = $_POST['vo2'];
$overview = $_POST['overview'];

$datetime = date("Y-m-d H:i:s"); 

$addorgid = "AND org_id='$SessionOrgId'";
$org_id = $SessionOrgId;
if ($SessionUserId == "1") {
    $addorgid = "AND org_id='{$_POST['organizations']}'";
    $org_id = $_POST['organizations'];

}

if ($patient_id != "") {
    if ($vital_id != "") {
        
        $checkVital = mysqli_query($conn, "SELECT * FROM vitals WHERE status='1' AND appointment_id='$patient_id' AND vital_id!='$vital_id' $addorgid");
        if (mysqli_num_rows($checkVital) > 0) {
            $msg = 3; 
        } else {
            $updateVitals = mysqli_query($conn, "
                UPDATE vitals SET 
                    BPsit = '$bpSit', 
                    BPstand = '$bpStand', 
                    BMIvalue = '$bmi', 
                    weight = '$weight', 
                    height = '$height', 
                    GRBS = '$grbs', 
                    heartrate = '$heartRate', 
                    temperature = '$temperature', 
                    resp = '$respiration', 
                    sp02percent = '$spo2', 
                    bloodgroup = '$bloodGroup', 
                    CPAP = '$cpap', 
                    HFNC = '$hfnc', 
                    VO2 = '$vo2', 
                    Overviewofpatient = '$overview', 
                    modified_by = '$SessionUserId',
                    modifydatetime = '$datetime'
                WHERE vital_id = '$vital_id'
            ") or die(mysqli_error($conn));

            if ($updateVitals) {
                $msg = 2; 
            }
        }
    } else {
        
        $checkVital = mysqli_query($conn, "SELECT * FROM vitals WHERE status='1' AND appointment_id='$patient_id' $addorgid");
        if (mysqli_num_rows($checkVital) > 0) {
            $msg = 3; 
        } else {
            $insertVitals = mysqli_query($conn, "
                INSERT INTO vitals (
                    appointment_id, BPsit, BPstand, BMIvalue, weight, height, GRBS, heartrate, temperature, resp, sp02percent, bloodgroup, CPAP, HFNC, VO2, Overviewofpatient, status, created_by, modified_by, createdatetime, org_id
                ) VALUES (
                    '$patient_id' ,'$bpSit', '$bpStand', '$bmi', '$weight', '$height', '$grbs', '$heartRate', '$temperature', '$respiration', '$spo2', '$bloodGroup', '$cpap', '$hfnc', '$vo2', '$overview', '1', '$SessionUserId', '$SessionUserId', '$datetime', '$org_id'
                )
            ") or die(mysqli_error($conn));

            if ($insertVitals) {
                $msg = 1; 
            }
        }
    }
} else {
    $msg = 4; 
}

echo $msg;
?>
