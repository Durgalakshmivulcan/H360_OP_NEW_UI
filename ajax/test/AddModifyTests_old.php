<?php
// IDOR_FIXED B-586
require_once("../../config/functions.php");

$msg = 0;

$SessionUserId   = $_SESSION['security_id'] ?? '';
$SessionRoleId   = $_SESSION['role_id'] ?? '';
$SessionOrgId    = $_SESSION['org_id'] ?? '';

$test_id         = $_POST['test_id'];
$test_name       = $_POST['test_name'];
$normal_range         = $_POST['normal_range'];
$test_price      = $_POST['test_price'];
$test_gst        = $_POST['test_gst'];
$organizations   = $_POST['organizations'];

$addorgid= "AND org_id='$SessionOrgId'";
if($SessionUserId == "1"){
    $addorgid=" AND org_id='$organizations'";
}

if($test_name != "" && $test_price != "" && $test_gst != "" ) {
    if($test_id != "") {
        // —— UPDATED DUPLICATE CHECK —— exclude current record
        $getAdminDepartment = mysqli_query($conn, "SELECT test_name FROM tests WHERE status='1' AND test_name='$test_name' AND test_id!='$test_id' $addorgid ") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminDepartment);
        if ($result > 0) {
            $msg = 3;
        } else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $UpdateTestData = mysqli_query($conn, "UPDATE tests SET test_name='$test_name',test_price='$test_price',test_gst='$test_gst',normal_range='$normal_range',modified_by='$SessionUserId', org_id='$organizations' WHERE test_id='$test_id'") or die(mysqli_error($conn));
                    if($UpdateTestData) {
                        $msg = 2;
                    }
                }
            } else{
                $UpdateTestData = mysqli_query($conn, "UPDATE tests SET test_name='$test_name',test_price='$test_price',test_gst='$test_gst',normal_range='$normal_range',modified_by='$SessionUserId' WHERE test_id='$test_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                if($UpdateTestData) {
                    $msg = 2;
                }
            }
        }
    } else {
        $getAdminDepartment = mysqli_query($conn, "SELECT test_name FROM tests WHERE status='1' AND test_name='$test_name' $addorgid ") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminDepartment);
        if ($result > 0) {
            $msg = 3;
        } else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $InsertTestData = mysqli_query($conn, "INSERT INTO tests(test_name, test_price, test_gst,normal_range, status, created_by, modified_by, create_date_time, org_id) VALUES ('$test_name','$test_price','$test_gst','$normal_range','1','$SessionUserId','$SessionUserId','$datetime','$organizations')") or die(mysqli_error($conn));
                    if($InsertTestData) {
                        $msg = 1;
                    }
                }
            } else{
                if($SessionOrgId != ""){
                    $InsertTestData = mysqli_query($conn, "INSERT INTO tests(test_name, test_price, test_gst,normal_range, status, created_by, modified_by, create_date_time, org_id) VALUES ('$test_name','$test_price','$test_gst','$normal_range','1','$SessionUserId','$SessionUserId','$datetime','$SessionOrgId')") or die(mysqli_error($conn));
                    if($InsertTestData) {
                        $msg = 1;
                    }
                }
            }
        }
    }
}

echo $msg;
?>