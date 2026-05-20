<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan(empty($_REQUEST['timeslot_id']) ? 'add' : 'edit', 'doctorstimeslot.php', 'ajax');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
// FIX_B_075: initialise $datetime explicitly for INSERT writes.
$datetime = date('Y-m-d H:i:s');

$multi_id = $_POST['multi_id'];
$Timeslot_id = $_POST['Timeslot_id1'];
$doc_name = $_POST['doc_name1'];
$available_date = json_decode($_POST['weeks']);


$start_date1=$_POST['available_date1'];
$end_date1=$_POST['end_date1'];
$doctortime_type = $_POST['doctortime_type1'];


$available_start_time = $_POST['available_start_time1'];

$available_ending_time = $_POST['available_ending_time1'];
$organizations = $_POST['organizations'];

$weekends = array($_POST['weekdays']);
$weekends2 = implode(" ",$weekends);

$weekends3 = $_POST['selectedDaysIndex'];
   

$weekends1 = join(" ",$weekends);

if($organizations) {
    $organizations = $organizations;
} else {
    $organizations = $SessionOrgId;
}

$lastid=$_POST['lastid1'];  
$starttime= json_encode($available_start_time);
$starttime1 = trim($starttime, '[]"');
$endtime=json_encode($available_ending_time);
$endtime2 = trim($endtime, '[]"');
$available_date1= json_encode($available_date);
$available_date2 = trim($available_date1, '" "');


if($doc_name != "" && $available_date != "" && $available_start_time != "" ) {
    if($Timeslot_id != "" && $multi_id != "") {
        $error = 0;
        for($j=0;$j < count($available_date);$j++ ){ 
            for ($i=0; $i < count($available_start_time); $i++) {
                $getAdmin1 = mysqli_query($conn, " SELECT m.doctorName_registrationNumber, 
                    m.available_date, 
                    m.doctors_time_id, 
                    mr.doctors_time_id,
                    mr.starting_Time,
                    mr.ending_Time 
                    FROM doctors_time_slot m
                    INNER JOIN doctors_time_slot2 mr ON m.doctors_time_id = mr.doctors_time_id
                    WHERE m.status = '1'
                        AND m.doctorName_registrationNumber = '$doc_name'
                        AND m.modify_by = '$SessionUserId'
                        AND m.org_id = '$organizations'
                        AND m.available_date = '$available_date[$j]'
                        AND (
                            ('$available_start_time[$i]' BETWEEN mr.starting_Time AND mr.ending_Time)
                            OR ('$available_ending_time[$i]' BETWEEN mr.starting_Time AND mr.ending_Time)

                            OR (
                                ('$available_start_time[$i]' = mr.starting_Time AND '$available_ending_time[$i]' = mr.ending_Time)
                                OR ('$available_start_time[$i]' = mr.starting_Time AND '$available_ending_time[$i]' = mr.starting_Time)
                                OR ('$available_ending_time[$i]' = mr.starting_Time AND '$available_start_time[$i]' = mr.ending_Time)
                            )
                               OR (mr.starting_Time BETWEEN '$available_start_time[$i]' AND '$available_ending_time[$i]')
                            OR (mr.ending_Time BETWEEN '$available_start_time[$i]' AND '$available_ending_time[$i]')
                            AND m.doctors_time_id != $Timeslot_id
                        )
                ") or die(mysqli_error($conn));
                while($result = mysqli_fetch_object($getAdmin1)){
                }
                $result = mysqli_num_rows($getAdmin1);
                if($result > 0) {
                    $error = $result;
                }
            }
        }
        if ($error > 0){
            $msg= 3;
        } else{
            if($SessionUserId == "1"){
                $updatemulti=mysqli_query($conn,"UPDATE multi_doctortimeslots  SET doctorName_registrationNumber='$doc_name',from_date='$start_date1',to_date='$end_date1',selectedDays='$weekends1', modify_by='$SessionUserId',org_id='$organizations',multi_id='$multi_id' WHERE multi_id='$multi_id'") or die(mysqli_error($conn));
                
                $deleteOldmultiData = mysqli_query($conn, "DELETE FROM multi_doctortimeslots2 WHERE multi_id='$multi_id'") or die(mysqli_error($conn));         
                for ($k=0; $k < count($available_start_time); $k++) {
                    $InserPrescSubmulti = mysqli_query($conn, "INSERT INTO multi_doctortimeslots2(multi_id,start_time,end_time,created_by,modify_by,status,org_id) VALUES ('$multi_id', '$available_start_time[$k]', '$available_ending_time[$k]','$SessionUserId', '$SessionUserId','1','$organizations')") or die(mysqli_error($conn));
                }
                
                for($j=0;$j < count($available_date);$j++ ){ 

                    $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date[$i]',doctortime_type='$doctortime_type', modify_by='$SessionUserId',org_id='$organizations',multi_id='$multi_id' WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));

                    $getdoctor_time_id = mysqli_query($conn,"SELECT  * FROM doctors_time_slot WHERE available_date='$available_date[$j]' AND status='1' AND multi_id='$multi_id'") or die(mysqli_error($conn));

                }
                    
                    $resid=mysqli_fetch_object($getdoctor_time_id);
                    $doctordate_id=$resid->doctors_time_id;
                    
                    $selecte_days=$resid->selectedDays;


                        $deleteOldtimeData = mysqli_query($conn, "DELETE FROM doctors_time_slot WHERE multi_id='$multi_id'") or die(mysqli_error($conn));
                    
             
                    $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$doctordate_id'") or die(mysqli_error($conn));

                    for($j=0;$j < count($available_date);$j++ ){ 

                    $InserPrescSub = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,selectedDays,created_by,modify_by,status,org_id,multi_id) VALUES ('$doc_name','$available_date[$j]','$doctortime_type', '$weekends3[$j]','$SessionUserId', '$SessionUserId','1','$organizations','$multi_id')") or die(mysqli_error($conn));
                    $doctortime_id=mysqli_insert_id($conn);
                    
                    for ($i=0; $i < count($available_start_time); $i++) {
                        $InserPrescSub = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id,multi_id) VALUES ('$doctortime_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$organizations','$multi_id')") or die(mysqli_error($conn));
                        $msg = 2;
                    }
                }
            } else {
                $updatemulti=mysqli_query($conn,"UPDATE multi_doctortimeslots  SET doctorName_registrationNumber='$doc_name',from_date='$start_date1',to_date='$end_date1',selectedDays='$weekends1', modify_by='$SessionUserId',org_id='$SessionOrgId',multi_id='$multi_id' WHERE multi_id='$multi_id'") or die(mysqli_error($conn));
                
                $deleteOldmultiData = mysqli_query($conn, "DELETE FROM multi_doctortimeslots2 WHERE multi_id='$multi_id'") or die(mysqli_error($conn));         
                for ($k=0; $k < count($available_start_time); $k++) {
                    $InserPrescSubmulti = mysqli_query($conn, "INSERT INTO multi_doctortimeslots2(multi_id,start_time,end_time,created_by,modify_by,status,org_id) VALUES ('$multi_id', '$available_start_time[$k]', '$available_ending_time[$k]','$SessionUserId', '$SessionUserId','1','$SessionOrgId')") or die(mysqli_error($conn));
                }
                
                for($j=0;$j < count($available_date);$j++ ){ 

                    $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date[$i]',doctortime_type='$doctortime_type', modify_by='$SessionUserId',org_id='$SessionOrgId',multi_id='$multi_id' WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));

                    $getdoctor_time_id = mysqli_query($conn,"SELECT  * FROM doctors_time_slot WHERE available_date='$available_date[$j]' AND status='1' AND multi_id='$multi_id'") or die(mysqli_error($conn));

                }
                    
                    $resid=mysqli_fetch_object($getdoctor_time_id);
                    $doctordate_id=$resid->doctors_time_id;
                    
                    $selecte_days=$resid->selectedDays;


                        $deleteOldtimeData = mysqli_query($conn, "DELETE FROM doctors_time_slot WHERE multi_id='$multi_id'") or die(mysqli_error($conn));
                    
                    $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$doctordate_id'") or die(mysqli_error($conn));

                    for($j=0;$j < count($available_date);$j++ ){ 

                    $InserPrescSub = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,selectedDays,created_by,modify_by,status,org_id,multi_id) VALUES ('$doc_name','$available_date[$j]','$doctortime_type', '$weekends3[$j]','$SessionUserId', '$SessionUserId','1','$organizations','$multi_id')") or die(mysqli_error($conn));
                    $doctortime_id=mysqli_insert_id($conn);
                    
                    for ($i=0; $i < count($available_start_time); $i++) {
                        $InserPrescSub = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id,multi_id) VALUES ('$doctortime_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$organizations','$multi_id')") or die(mysqli_error($conn));
                        $msg = 2;
                    }
                }
            }
        }
    } else {
        $error = 0;
        for($j=0;$j < count($available_date);$j++ ){ 
            for ($i=0; $i < count($available_start_time); $i++) {

                $getAdmin1 = mysqli_query($conn, "SELECT m.doctorName_registrationNumber, 
                m.available_date, 
                m.doctors_time_id, 
                mr.doctors_time_id,
                mr.starting_Time, 
                mr.ending_Time 
                FROM doctors_time_slot m
                INNER JOIN doctors_time_slot2 mr ON m.doctors_time_id = mr.doctors_time_id
                WHERE m.status = '1'
                    AND m.doctorName_registrationNumber = '$doc_name'
                    AND m.modify_by = '$SessionUserId'
                    AND m.org_id = '$organizations'
                    AND m.available_date = '$available_date[$j]'
                    AND (
                        ('$available_start_time[$i]' BETWEEN mr.starting_Time AND mr.ending_Time)
                            OR ('$available_ending_time[$i]' BETWEEN mr.starting_Time AND mr.ending_Time)

                        OR (
                            ('$available_start_time[$i]' = mr.starting_Time AND '$available_ending_time[$i]' = mr.ending_Time) 
                            OR ('$available_start_time[$i]' = mr.starting_Time AND '$available_ending_time[$i]' = mr.starting_Time) 
                            OR ('$available_ending_time[$i]' = mr.starting_Time AND '$available_start_time[$i]' = mr.ending_Time)
                        )
                           OR (mr.starting_Time BETWEEN '$available_start_time[$i]' AND '$available_ending_time[$i]')
                            OR (mr.ending_Time BETWEEN '$available_start_time[$i]' AND '$available_ending_time[$i]')
                )") or die(mysqli_error($conn));
                 while($result = mysqli_fetch_object($getAdmin1)){
                }
                $result = mysqli_num_rows($getAdmin1);
                if($result > 0) {
                            $error = $result;
                }
            }
        }

        if ($error > 0){
            $msg= 3;
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                $insertmultidata= mysqli_query($conn, "INSERT INTO  multi_doctortimeslots(doctorName_registrationNumber,from_date,to_date,selectedDays,created_by,modify_by,status,create_date_time,org_id) VALUES('$doc_name','$start_date1','$end_date1','$weekends1','$SessionUserId','$SessionUserId','1','$datetime','$organizations')") or die(mysqli_error($conn));
                $multi_id1 = mysqli_insert_id($conn);
    
                for($j=0;$j < count($available_date);$j++ ){     
                $InserPrescData = mysqli_query($conn, "INSERT INTO  doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,selectedDays,modify_by,created_by,org_id,c_d_t,status,multi_id) VALUES ('$doc_name','$available_date[$j]','$doctortime_type','$weekends3[$j]','$SessionUserId','$SessionUserId','$organizations','$datetime','1','$multi_id1')") or die(mysqli_error($conn));
                $Timeslot_id1 = mysqli_insert_id($conn);
                for ($k=0; $k < count($available_start_time); $k++) {
                $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id,multi_id) VALUES ('$Timeslot_id1', '$available_start_time[$k]', '$available_ending_time[$k]','$SessionUserId','$SessionUserId','1','$organizations','$multi_id1')") or die(mysqli_error($conn));
                }
                }   
                for ($k=0; $k < count($available_start_time); $k++) {
    
                $insertmultidata1= mysqli_query($conn, "INSERT INTO  multi_doctortimeslots2(multi_id,start_time,end_time,created_by,modify_by,status,create_date_time,org_id)VALUES('$multi_id1','$available_start_time[$k]','$available_ending_time[$k]','$SessionUserId','$SessionUserId','1','$datetime','$organizations')") or die(mysqli_error($conn));
            
                }
                $msg=1;
            }
            }
            else{
                    if($SessionOrgId != ""){
                        $insertmultidata= mysqli_query($conn, "INSERT INTO  multi_doctortimeslots(doctorName_registrationNumber,from_date,to_date,selectedDays,created_by,modify_by,status,create_date_time,org_id) VALUES('$doc_name','$start_date1','$end_date1','$weekends1','$SessionUserId','$SessionUserId','1','$datetime','$SessionOrgId')") or die(mysqli_error($conn));
                        $multi_id1 = mysqli_insert_id($conn);

                        for($j=0;$j < count($available_date);$j++ ){     
                        $InserPrescData = mysqli_query($conn, "INSERT INTO  doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,selectedDays,modify_by,created_by,org_id,c_d_t,status,multi_id) VALUES ('$doc_name','$available_date[$j]','$doctortime_type','$weekends3[$j]','$SessionUserId','$SessionUserId','$organizations','$datetime','1','$multi_id1')") or die(mysqli_error($conn));
                        $Timeslot_id1 = mysqli_insert_id($conn);
                        for ($k=0; $k < count($available_start_time); $k++) {
                        $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id,multi_id) VALUES ('$Timeslot_id1', '$available_start_time[$k]', '$available_ending_time[$k]','$SessionUserId','$SessionUserId','1','$organizations','$multi_id1')") or die(mysqli_error($conn));
                        }
                        }   
                        for ($k=0; $k < count($available_start_time); $k++) {

                        $insertmultidata1= mysqli_query($conn, "INSERT INTO  multi_doctortimeslots2(multi_id,start_time,end_time,created_by,modify_by,status,create_date_time,org_id)VALUES('$multi_id1','$available_start_time[$k]','$available_ending_time[$k]','$SessionUserId','$SessionUserId','1','$datetime','$SessionOrgId')") or die(mysqli_error($conn));
                    
                        }
                        $msg=1;
                    }
                }
        }
    }
}

echo $msg;
return;  

?>