<?php
require_once("../../config/functions.php");

$result = [];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$Timeslot_id = $_POST['Timeslot_id'];

if($SessionUserId == '1' && $SessionRoleId == '1') /* FIX_B_003 */{
    if($Timeslot_id != "") {
        $getdoctorstime=mysqli_query($conn, "SELECT starting_Time,ending_Time FROM doctors_time_slot2 WHERE status='1' AND doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
        while ($resdoctorstime=mysqli_fetch_object($getdoctorstime)){
            $result[] = $resdoctorstime;
        }
    }

}else{

    if($Timeslot_id != "") {
        $getdoctorstime=mysqli_query($conn, "SELECT starting_Time,ending_Time FROM doctors_time_slot2 WHERE status='1' AND org_id='$SessionOrgId' AND doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
        while ($resdoctorstime=mysqli_fetch_object($getdoctorstime)){
            $result[] = $resdoctorstime;
        }
    }

}


echo json_encode($result);

?>


