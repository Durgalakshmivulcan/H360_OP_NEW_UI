<?php
require_once("../../config/functions.php");

$result = [];


    $get=mysqli_query($conn,"SELECT mobile_number FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' ORDER BY appoint_id DESC") or die(mysqli_error($conn));
    while ($res = mysqli_fetch_object($get)) {
        $result[] = $res;
    }


echo json_encode($result);


// $results = [];
// $sql = mysqli_query($conn,"SELECT mobile_number FROM appointment_online WHERE appoint_status='1' ORDER BY appoint_id ASC") or die(mysqli_error($conn));
// while($row = mysqli_fetch_object($sql)){
//     $results[] = $row;
// }

// echo json_encode($results);


?>