<?php
require_once("../../config/functions.php");
requireCan(empty($_POST['doseandtime_id']) ? 'add' : 'edit', 'services.php', 'ajax'); // FIX_B_1810
    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;
$doseandtime_id = $_POST['doseandtime_id'];
$dosage = $_POST['dosage'];
$Intake_period = $_POST['Intake_period'];
$time = $_POST['time'];
$dosage_combination = $_POST['dosage_combination'];
$organizations = $_POST['organizations'];

function getFrequency($dose_schedule) {
    $pattern = explode('|', $dose_schedule)[0];
    $values = explode('-', $pattern); 

    $count = array_sum(array_map('intval', $values)); 
    return $count;
}

$frequency = getFrequency($dosage_combination);



if($dosage != "" && $Intake_period != "" && $time != "") {

    if($doseandtime_id !="") {
        if($SessionUserId == "1"){
            $getAdminDoctor = mysqli_query($conn, "SELECT /* FIX_B_081: drop modified_by from dup-check (cross-user collisions in same org must be rejected) */ dose_id,intake_time_id FROM dosageandtime WHERE dose_id='$dosage' AND  intake_time_id='$Intake_period' AND status='1' AND doseandtime_id!='$doseandtime_id' AND org_id='$organizations'") or die(mysqli_error($conn));
        }else{
            $getAdminDoctor = mysqli_query($conn, "SELECT /* FIX_B_081 */ dose_id,intake_time_id FROM dosageandtime WHERE dose_id='$dosage' AND  intake_time_id='$Intake_period' AND  status='1' AND org_id='$SessionOrgId' AND doseandtime_id!='$doseandtime_id'") or die(mysqli_error($conn));
        }

        $result=mysqli_num_rows($getAdminDoctor);
        if ($result > 0) {  
            $msg = 3;
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $UpdateMenuData = mysqli_query($conn, "UPDATE dosageandtime SET frequency = '$frequency', dose_id='$dosage', intake_time_id='$Intake_period', dose_schedule='$dosage_combination',modified_by='$SessionUserId',org_id='$organizations' WHERE doseandtime_id='$doseandtime_id'") or die(mysqli_error($conn));
                    if($UpdateMenuData) {
                        $msg = 2;
                    }
                }
            } else{
                $UpdateMenuData = mysqli_query($conn, "UPDATE dosageandtime SET frequency = '$frequency', dose_id='$dosage', intake_time_id='$Intake_period', dose_schedule='$dosage_combination',modified_by='$SessionUserId' WHERE doseandtime_id='$doseandtime_id'") or die(mysqli_error($conn));
                if($UpdateMenuData) {
                    $msg = 2;
                }
            }
        }
    } else {
        if($SessionUserId == "1"){
            $getAdminDoctor = mysqli_query($conn, "SELECT dose_id,intake_time_id FROM dosageandtime WHERE dose_id='$dosage' AND  intake_time_id='$Intake_period' AND status='1' AND org_id='$organizations' ") or die(mysqli_error($conn));
            $result=mysqli_num_rows($getAdminDoctor);
        }else{
            $getAdminDoctor = mysqli_query($conn, "SELECT dose_id,intake_time_id FROM dosageandtime WHERE dose_id='$dosage' AND  intake_time_id='$Intake_period' AND status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            $result=mysqli_num_rows($getAdminDoctor);
        }
       
        if ($result > 0) {
            $msg = 3;
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $InserMenuData = mysqli_query($conn, "INSERT INTO dosageandtime(dose_id, intake_time_id, frequency, dose_schedule,org_id, status,created_by, modified_by) VALUES ('$dosage', '$Intake_period', '$frequency' ,'$dosage_combination','$organizations','1','$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $msg = 1;
                    }
                }
            } else{
                if($SessionOrgId != ""){
                    $InserMenuData = mysqli_query($conn, "INSERT INTO dosageandtime(dose_id, intake_time_id, frequency, dose_schedule,org_id, status,created_by, modified_by) VALUES ('$dosage', '$Intake_period', '$frequency' ,'$dosage_combination','$SessionOrgId','1','$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $msg = 1;
                    }
                }
            }
        }
    }
}else{
    echo "0";
}
echo $msg;