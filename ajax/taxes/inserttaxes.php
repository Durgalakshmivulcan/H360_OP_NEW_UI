<?php

// IDOR_FIXED B-590
require_once("../../config/functions.php");
requireCan(empty($_POST['taxes_hid_id']) ? 'add' : 'edit', 'taxes.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;

if(!$SessionUserId) {
    $msg= 5;
    return;
}

$addorgid= " AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
$addorgid="";
}

$tax_id = $_POST['taxes_hid_id'];
$cgstNumber = $_POST['cgstNumber'];
$sgstNumber = $_POST['sgstNumber'];
$percentage = $_POST['percentage'];
$organizations = $_POST['organizations'];

if($cgstNumber != "" && $sgstNumber != "" && $percentage != "" ) {
    if($tax_id != "") {
        $beforeQuery = mysqli_query($conn, "SELECT * FROM taxes WHERE tax_id='$tax_id' LIMIT 1");
        $before      = null;
        if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
            $before = mysqli_fetch_assoc($beforeQuery);
        }
        $checkorgid = $SessionOrgId;
        if(!$SessionOrgId) {
            $checkorgid = $organizations;
        }
        $gettaxes = mysqli_query($conn,"SELECT percentage FROM taxes WHERE status='1' AND percentage='$percentage' AND tax_id!='$tax_id' AND modify_by='$SessionUserId' $addorgid") or die(mysqli_error($conn));
        $result=mysqli_num_rows($gettaxes);
        
        if ($result > 0) {     
                $msg = 3; 
        }else{
        if($SessionUserId == "1"){
            if($organizations != ""){
                $Updatetaxes = mysqli_query($conn, "UPDATE taxes SET cgstNumber ='$cgstNumber', sgstNumber ='$sgstNumber', percentage ='$percentage', org_id='$organizations' WHERE tax_id='$tax_id'") or die(mysqli_error($conn));
                if($Updatetaxes) {
                    $msg = 2;
                    $afterQuery = mysqli_query($conn, "SELECT * FROM taxes WHERE tax_id='$tax_id' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "Taxes", "update", "taxes", $tax_id, $before, $after);
                }
            } else {
                $msg = 4;
            }
        } else{
            $Updatetaxes = mysqli_query($conn, "UPDATE taxes SET cgstNumber ='$cgstNumber', sgstNumber ='$sgstNumber', percentage ='$percentage' WHERE tax_id='$tax_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            if($Updatetaxes) {
                $msg = 2;
                $afterQuery = mysqli_query($conn, "SELECT * FROM taxes WHERE tax_id='$tax_id' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "Taxes", "update", "taxes", $tax_id, $before, $after);
            }
        }
    }
    } else {
        $checkorgid = $SessionOrgId;
        if(!$SessionOrgId) {
            $checkorgid = $organizations;
        }
        $gettaxes = mysqli_query($conn,"SELECT percentage FROM taxes WHERE status='1' AND percentage='$percentage' AND tax_id!='$tax_id' AND modify_by='$SessionUserId' $addorgid") or die(mysqli_error($conn));
        $result=mysqli_num_rows($gettaxes);
        
        if ($result > 0) {     
                $msg = 3; 
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $Insertaxes = mysqli_query($conn, "INSERT INTO taxes (tax_id, cgstNumber, sgstNumber, percentage, org_id, status, create_date_time, created_by, modify_by) VALUES ('$tax_id','$cgstNumber','$sgstNumber','$percentage','$organizations','1','$datetime','$SessionUserId','$SessionUserId');") or die(mysqli_error($conn));
                    if($Insertaxes) {
                        $msg = 1;
                        $newId = mysqli_insert_id($conn);
                        // fetch after insert
                        $afterQuery = mysqli_query($conn, "SELECT * FROM taxes WHERE tax_id='$newId' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "Taxes", "create", "taxes", $newId, null, $after);
                    }
                } else {
                    $msg = 4;
                }
            } else {
                if($SessionOrgId != ""){
                    $Insertaxes = mysqli_query($conn, "INSERT INTO taxes (tax_id, cgstNumber, sgstNumber, percentage, org_id, status, create_date_time, created_by, modify_by) VALUES ('$tax_id','$cgstNumber','$sgstNumber','$percentage','$SessionOrgId','1','$datetime','$SessionUserId','$SessionUserId');") or die(mysqli_error($conn));
                    if($Insertaxes) {
                        $msg = 1;
                        $newId = mysqli_insert_id($conn);
                        // fetch after insert
                        $afterQuery = mysqli_query($conn, "SELECT * FROM taxes WHERE tax_id='$newId' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "Taxes", "create", "taxes", $newId, null, $after);
                    }
                } else {
                    $msg = 4;
                }
            }
        }
    }
}


echo $msg;


?>
