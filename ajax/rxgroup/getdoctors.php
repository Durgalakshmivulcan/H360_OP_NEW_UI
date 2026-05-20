<?php
require_once("../../config/functions.php");
$id=$_session['doctors_time_id'];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];


    // $get1=mysqli_query($conn,"SELECT *  FROM doctors_time_slot2 WHERE doctors_time_id ") or die(mysqli_error($conn));
    $get=mysqli_query($conn,"SELECT *  FROM doctors_time_slot WHERE doctors_time_id AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    while ($res = mysqli_fetch_object($get)) {
        $result[] = $res;
    }


echo json_encode($result);



// $result1 = [];

// $get1=mysqli_query($conn,"SELECT *  FROM doctors_time_slot2 WHERE doctors_time_id ") or die(mysqli_error($conn));
// while ($res1 = mysqli_fetch_object($get1)) {
//     $result1[] = $res1;
// }


// echo json_encode($result1);

?>