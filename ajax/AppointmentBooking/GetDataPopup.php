<?php
require_once("../../config/functions.php");

$TimeSlotInActive = 0;

$appoint_id = $_POST['appoint_id'];


$getTimeSlotInActive = mysqli_query($conn, "SELECT start_time FROM appointment_online WHERE appoint_status='1' And appoint_id='$appoint_id'") or die(mysqli_error($conn));

if($getTimeSlotInActive) {
    $TimeSlotInActive = 1;
}

echo $TimeSlotInActive;

?>