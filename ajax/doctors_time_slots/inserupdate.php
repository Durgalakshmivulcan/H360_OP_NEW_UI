<?php
// IDOR_FIXED B-597
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan(empty($_REQUEST['doctor_dates_id']) ? 'add' : 'edit', 'doctorstimeslot.php', 'ajax');
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
// $msg= '';

$Timeslot_id = $_POST['Timeslot_id'];
$doc_name = $_POST['doc_name'];
$available_date = $_POST['available_date'];
$doctortime_type = $_POST['doctortime_type'];
$organizations = $_POST['organizations'];
$available_start_time = $_POST['available_start_time'];
$available_ending_time = $_POST['available_ending_time'];
$lastid=$_POST['lastid'];
$avail_date= array($available_date);

$starttime= json_encode($available_start_time);
$starttime1 = trim($starttime, '[]"');
$endtime=json_encode($available_ending_time);
$endtime2 = trim($endtime, '[]"');

// echo $organizations;
if($organizations) {
    $organizations = $organizations;
} else {
    $organizations = $SessionOrgId;
}
// echo $organizations;

if($doc_name != "" && $available_date != "" && $available_start_time != "" ) {
    if($Timeslot_id != "") {
        $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$Timeslot_id' AND org_id='$organizations'"));
        for ($i=0; $i < count($available_start_time); $i++) {
            $getAdmin1 = mysqli_query($conn, "
                                    SELECT 
                                        m.doctorName_registrationNumber, 
                                        m.available_date, 
                                        m.doctors_time_id, 
                                        mr.doctors_time_id,
                                        mr.starting_Time,
                                        mr.ending_Time 
                                    FROM doctors_time_slot m
                                    INNER JOIN doctors_time_slot2 mr ON m.doctors_time_id = mr.doctors_time_id
                                    WHERE m.status = '1'
                                        AND m.doctors_time_id != '$Timeslot_id'
                                        AND m.doctorName_registrationNumber = '$doc_name'
                                        AND m.org_id = '$organizations'
                                        AND m.available_date = '$available_date'
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
                                        )
                                ") or die(mysqli_error($conn));
            
            $result=mysqli_num_rows($getAdmin1);
            if ($result > 0){
                $msg= 3;
            } else{
                if($SessionUserId == "1"){
                    if($available_start_time[$i]<$available_start_time[$i-1]){
                        $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date',doctortime_type='$doctortime_type', modify_by='$SessionUserId',org_id='$organizations' WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                        $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                        for ($i=0; $i < count($available_start_time); $i++) {
                            $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$organizations')") or die(mysqli_error($conn));
                            $msg= 2;
                            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$Timeslot_id' AND org_id='$organizations'"));

                // Audit log
                audit_log($conn, "Doctor Timeslot", "update", "doctors_time_slot", $Timeslot_id, $before, $after);
                        }
                    } else{
                        if( $available_ending_time[$i-1] >= $available_start_time[$i] ){
                            // FIX_B_073: skip the conflicting slot, do not exit the include.
                            $msg= 4;
                            continue;
                        } else{
                            $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date',doctortime_type='$doctortime_type', modify_by='$SessionUserId',org_id='$organizations' WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                            $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                            for ($i=0; $i < count($available_start_time); $i++) {
                                $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$organizations')") or die(mysqli_error($conn));
                                $msg= 2;
                                $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$Timeslot_id' AND org_id='$organizations'"));

                // Audit log
                audit_log($conn, "Doctor Timeslot", "update", "doctors_time_slot", $Timeslot_id, $before, $after);
                            }
                        }
                    }
                } else{
                    if($available_start_time[$i]<$available_start_time[$i-1]){
                        $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date',doctortime_type='$doctortime_type', modify_by='$SessionUserId' WHERE doctors_time_id='$Timeslot_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                        $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                        for ($i=0; $i < count($available_start_time); $i++) {
                            $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$SessionOrgId')") or die(mysqli_error($conn));
                            $msg= 2;
                        }
                    } else{
                        if( $available_ending_time[$i-1] >= $available_start_time[$i] ){
                            // FIX_B_073: skip the conflicting slot, do not exit the include.
                            $msg= 4;
                            continue;
                        } else{
                            $UpdatePrescData = mysqli_query($conn,"UPDATE doctors_time_slot SET doctorName_registrationNumber='$doc_name',available_date='$available_date',doctortime_type='$doctortime_type', modify_by='$SessionUserId' WHERE doctors_time_id='$Timeslot_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                            $deleteOldPrescData = mysqli_query($conn, "DELETE FROM doctors_time_slot2 WHERE doctors_time_id='$Timeslot_id'") or die(mysqli_error($conn));
                            for ($i=0; $i < count($available_start_time); $i++) {
                                $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$SessionOrgId')") or die(mysqli_error($conn));
                                $msg= 2;
                                $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$Timeslot_id' AND org_id='$organizations'"));

                // Audit log
                audit_log($conn, "Doctor Timeslot", "update", "doctors_time_slot", $Timeslot_id, $before, $after);
                            }
                        }
                    }
                }
            }
        }
    } else {
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
                AND m.available_date = '$available_date'
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
                )
        ") or die(mysqli_error($conn));
        
            $result=mysqli_num_rows($getAdmin1);

            if ($result > 0){
                $msg= 3;
            } else{
                if($SessionUserId == "1"){
                    if($organizations != ""){
                        if($available_start_time[$i]<$available_start_time[$i-1]){
                            $InserPrescData = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,modify_by,created_by,org_id,status) VALUES ('$doc_name','$available_date','$doctortime_type','$SessionUserId','$SessionUserId','$organizations','1')") or die(mysqli_error($conn));
                            $Timeslot_id = mysqli_insert_id($conn);
                            $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$organizations')") or die(mysqli_error($conn));
                                $msg= 1;
                        }else{
                            if($available_ending_time[$i-1] >= $available_start_time[$i]){ 
                                // FIX_B_073: skip the conflicting slot, do not exit the include.
                                $msg= 4;
                                continue;
                            }else{
                                $InserPrescData = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,modify_by,created_by,org_id,status) VALUES ('$doc_name','$available_date','$doctortime_type','$SessionUserId','$SessionUserId','$organizations','1')") or die(mysqli_error($conn));
                                $Timeslot_id = mysqli_insert_id($conn);
                                
                                for ($j=0; $j < count($available_start_time); $j++) { // FIX_B_074
                                    $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$j]', '$available_ending_time[$j]','$SessionUserId', '$SessionUserId','1','$organizations')") or die(mysqli_error($conn));
                                }
                                    $msg= 1;
                            }

                        }
                    }
                } else{
                    if($SessionOrgId != ""){
                        if($available_start_time[$i]<$available_start_time[$i-1]){
                            $InserPrescData = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,modify_by,created_by,org_id,status) VALUES ('$doc_name','$available_date','$doctortime_type','$SessionUserId','$SessionUserId','$SessionOrgId','1')") or die(mysqli_error($conn));
                            $Timeslot_id = mysqli_insert_id($conn);

                            $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$i]', '$available_ending_time[$i]','$SessionUserId', '$SessionUserId','1','$SessionOrgId')") or die(mysqli_error($conn));
                                $msg= 1;
                        }else{
                            if($available_ending_time[$i-1] >= $available_start_time[$i]){ 
                                // FIX_B_073: skip the conflicting slot, do not exit the include.
                                $msg= 4;
                                continue;
                            }else{
                                $InserPrescData = mysqli_query($conn, "INSERT INTO doctors_time_slot(doctorName_registrationNumber,available_date,doctortime_type,modify_by,created_by,org_id,status) VALUES ('$doc_name','$available_date','$doctortime_type','$SessionUserId','$SessionUserId','$SessionOrgId','1')") or die(mysqli_error($conn));
                                $Timeslot_id = mysqli_insert_id($conn);
                                for ($j=0; $j < count($available_start_time); $j++) { // FIX_B_074
                                    $InserPrescSubData = mysqli_query($conn, "INSERT INTO doctors_time_slot2(doctors_time_id,starting_Time,ending_Time,created_by,modify_by,status,org_id) VALUES ('$Timeslot_id', '$available_start_time[$j]', '$available_ending_time[$j]','$SessionUserId', '$SessionUserId','1','$SessionOrgId')") or die(mysqli_error($conn));
                                }
                                    $msg= 1;
                                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$Timeslot_id' AND org_id='$organizations'"));

                                    // Audit log
                                    audit_log($conn, "Doctor Timeslot", "create", "doctors_time_slot", $Timeslot_id, null, $after);
                            }
                    
                        }
                    }
                }
            }
        }
  
    }
}

echo $msg;

return;
?>