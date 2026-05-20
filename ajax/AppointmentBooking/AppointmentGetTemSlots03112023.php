<?php
include 'config/functions.php';
$result = getTimeSlot(15, '1:00', '23:50', '2023-11-04', 1);

echo json_encode($result);



require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$doctorName_registrationNumber = $_POST['doctorName_registrationNumber'];
$appoint_date = $_POST['available_date'];
$organizations = $_POST['organizations'];

if($SessionOrgId) {
    $organizations = $SessionOrgId;
}

$i = $_POST['TimeSlot_id1'];
$interval = 15;

$result = [];

// if ($SessionUserId == "1" && $SessionRoleId == "1") {
//     $getDoctorTime = mysqli_query($conn, "SELECT m.doctors_time_id, mr.starting_Time, mr.ending_Time FROM doctors_time_slot m, doctors_time_slot2 mr  WHERE m.status='1' AND m.doctors_time_id=mr.doctors_time_id AND m.doctorName_registrationNumber='$doctorName_registrationNumber' AND m.available_date='$appoint_date'") or die(mysqli_error($conn));
// } else {
//     $getDoctorTime = mysqli_query($conn, "SELECT m.doctors_time_id, mr.starting_Time, mr.ending_Time FROM doctors_time_slot m, doctors_time_slot2 mr  WHERE m.status='1'AND m.org_id='$SessionOrgId' AND mr.org_id='$SessionOrgId' AND m.doctors_time_id=mr.doctors_time_id AND m.doctorName_registrationNumber='$doctorName_registrationNumber' AND m.available_date='$appoint_date'") or die(mysqli_error($conn));
// }
$getDoctorTime = mysqli_query($conn, "SELECT m.doctors_time_id, mr.starting_Time, mr.ending_Time FROM doctors_time_slot m, doctors_time_slot2 mr WHERE m.status='1' AND m.doctors_time_id=mr.doctors_time_id AND m.doctorName_registrationNumber='$doctorName_registrationNumber' AND m.available_date='$appoint_date' AND mr.org_id='$organizations' AND m.org_id=mr.org_id ORDER BY m.doctors_time_id DESC LIMIT 1") or die(mysqli_error($conn));

$count = 1;

$current_time = date('H:i'); 
$tomorrow_date = date('Y-m-d');
if ($doctorName_registrationNumber =="") {
    ?>
    <div class="col-lg-12 col-sm-12">
        <p>Please select a doctor.</p>
    </div>
    <?php
} else {
    $availableSlots = false;

    $FinalData = [];
    while ($resDoctorTime = mysqli_fetch_object($getDoctorTime)) {
        $result = getTimeSlot($interval, $resDoctorTime->starting_Time, $resDoctorTime->ending_Time, $appoint_date, $doctorName_registrationNumber);

        
        foreach ($result as $slot) {
            $res_starting_Time = $slot['slot_start_time'];
            $res_ending_Time = $slot['slot_end_time'];

            $FinalData[] = $slot;
        }
    }

    $FinalData = array_unique($FinalData, SORT_REGULAR);
    // echo json_encode($FinalData);
    // return;
    $FinalData1 = sort($FinalData);

    foreach ($FinalData as $FinalSlot) {
        $res_starting_Time = $FinalSlot['slot_start_time'];
        $res_ending_Time = $FinalSlot['slot_end_time'];
        
        
        // $date1 = strtotime($res_starting_Time);
        // $date2 = strtotime($resDoctorTime->ending_Time);

        // $mins = ($date2 - $date1) / 60;
        // if($mins < $interval) {
        //     break;
        // }

        // Check if the slot start time is in the future or the current time
        if ($appoint_date != $tomorrow_date || strtotime($res_starting_Time) >= strtotime($current_time)) {
            if ($SessionUserId == "1" && $SessionRoleId == "1") {
                $sql = "SELECT 
                            appoint_id, bill_id, bill_date, appoint_register_id, 
                            appoint_unicode, patient_name, gender, systolic, diastolic, 
                            temperature, glucose_level, age, mobile_number, patient_email, 
                            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
                            appoint_status, visitor_status, org_id, created_by, modified_by, 
                            create_date_time, amount_method, amount 
                        FROM appointment_online 
                        WHERE appoint_status='1' 
                          AND start_time='$res_starting_Time' 
                          AND end_time='$res_ending_Time' 
                          AND appoint_date='$appoint_date' 
                          AND doctor_name='$doctorName_registrationNumber'
            
                        UNION
            
                        SELECT 
                            appoint_id, bill_id, bill_date, appoint_register_id, 
                            appoint_unicode, patient_name, gender, systolic, diastolic, 
                            temperature, glucose_level, age, mobile_number, patient_email, 
                            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
                            appoint_status, visitor_status, org_id, created_by, modified_by, 
                            create_date_time, amount_method, amount 
                        FROM appointment_existing 
                        WHERE appoint_status='1' 
                          AND start_time='$res_starting_Time' 
                          AND end_time='$res_ending_Time' 
                          AND appoint_date='$appoint_date' 
                          AND doctor_name='$doctorName_registrationNumber'";
            } else {
                $sql = "SELECT 
                            appoint_id, bill_id, bill_date, appoint_register_id, 
                            appoint_unicode, patient_name, gender, systolic, diastolic, 
                            temperature, glucose_level, age, mobile_number, patient_email, 
                            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
                            appoint_status, visitor_status, org_id, created_by, modified_by, 
                            create_date_time, amount_method, amount 
                        FROM appointment_online 
                        WHERE appoint_status='1' 
                          AND start_time='$res_starting_Time' 
                          AND end_time='$res_ending_Time' 
                          AND appoint_date='$appoint_date' 
                          AND doctor_name='$doctorName_registrationNumber' 
                          AND org_id='$SessionOrgId'
            
                        UNION
            
                        SELECT 
                            appoint_id, bill_id, bill_date, appoint_register_id, 
                            appoint_unicode, patient_name, gender, systolic, diastolic, 
                            temperature, glucose_level, age, mobile_number, patient_email, 
                            appoint_date, doctor_name, start_time, end_time, doctor_fee, 
                            appoint_status, visitor_status, org_id, created_by, modified_by, 
                            create_date_time, amount_method, amount 
                        FROM appointment_existing 
                        WHERE appoint_status='1' 
                          AND start_time='$res_starting_Time' 
                          AND end_time='$res_ending_Time' 
                          AND appoint_date='$appoint_date' 
                          AND doctor_name='$doctorName_registrationNumber' 
                          AND org_id='$SessionOrgId'";
            }
            

            $combinedQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));
            $rowCount = mysqli_num_rows($combinedQuery);

            if ($rowCount == 0) {
                $onclk = 'onclick="move1(`' . $res_starting_Time . '`,`' . $res_ending_Time . '`,`' . $appoint_date . '`,`' . $count . '`)"';
                $bukcolor = "";
                $availableSlots = true;
            } else {
                $onclk = "";
                $bukcolor = " background-color: gray;";
            }

            ?>
            <div class='col-lg-1 col-sm-12 TimeSlot' id="myDIV<?= $count ?>" <?= $onclk ?> style="<?= $bukcolor ?>">
                <span><?= $FinalSlot['slot_start_time'] ?></span>
            </div>
            <?php

            $count++;
        }
    }

    if (!$availableSlots) {
        ?>
        <div class="col-lg-12 col-sm-12">
            <p>No Time Slots for the selected doctor.</p>
        </div>
        <?php
    }
}


?>