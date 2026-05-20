<?php
    require_once("../../config/functions.php");

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $current_time = date('H:i');

    $result = "";
    
    $doctor_name = $_POST['doctor_name'];
    $appoint_date = $_POST['appoint_date'];
    $appoint_unicode = $_POST['appoint_unicode'];

    $qry = mysqli_query($conn, "SELECT * FROM appointment_existing WHERE appoint_status='1' AND doctor_name='$doctor_name' AND  appoint_date='$appoint_date' AND appoint_unicode='$appoint_unicode'") or die(mysqli_error($conn));
    $checkExist = mysqli_num_rows($qry);
    if($checkExist) {
        while($res = mysqli_fetch_object($qry)){
            if($appoint_unicode > date('Y-m-d')) {
                $result = $res->start_time;
            } else {
                if (strtotime($res->start_time) >= strtotime($current_time)) {
                    $result = $res->start_time;
                }
            }
        }
        
    }

    if(!$result) {
        $qry = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND doctor_name='$doctor_name' AND  appoint_date='$appoint_date' AND appoint_unicode='$appoint_unicode'") or die(mysqli_error($conn));
        $checkExist = mysqli_num_rows($qry);
        if($checkExist) {
            while($res = mysqli_fetch_object($qry)){
                if($appoint_unicode > date('Y-m-d')) {
                    $result = $res->start_time;
                } else {
                    if (strtotime($res->start_time) >= strtotime($current_time)) {
                        $result = $res->start_time;
                    }
                }
            }
            
        }
    }

    echo $result;