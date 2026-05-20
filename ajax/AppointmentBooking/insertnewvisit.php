<?php
require_once("../../config/functions.php");
// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('add', 'AppointmentOnline.php', 'ajax');
$id=$_session['doctors_time_id'];

$result = [];


    $get=mysqli_query($conn,"SELECT doctors_time_id,doctorName_registrationNumber FROM doctors_time_slot WHERE status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    while ($res = mysqli_fetch_object($get)) {
        $res1=getDoctorById($conn,$res->doctorName_registrationNumber);
        // $res2=$res->doctors_time_id;
        $result[] = array(
            // "doctors_time_id"=>$res2,
            "doctorName_registrationNumber"=>$res1
        );
    }


echo json_encode($result);
// echo $res1;



?>