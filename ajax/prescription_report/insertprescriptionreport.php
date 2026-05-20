<?php

// IDOR_FIXED B-581
require_once("../../config/functions.php"); // FIX_B_1840 — brings config.php + session_start + RBAC helpers
// FIX_B_1840 — RBAC: per-action AJAX gate. Branch by mode: $_POST['id'] non-empty → edit, else → add.
$_rbacAction = (isset($_POST['id']) && $_POST['id'] !== '') ? 'edit' : 'add';
requireCan($_rbacAction, 'prescriptionreports.php', 'ajax');

$SessionOrgId = $_SESSION['org_id'] ?? '';
$msg = 0;

$emr_id = $_POST['emr_id'];
$dateRange = $_POST['date'];
$visit = $_POST['next_visit'];
$medicine_name = $_POST['medicine_name'];


    if(!empty($id)){
        $query = ")" or die(error($conn));
        $result = $conn->query($query);
        if($result){
            echo "Inserted Successfully!";
        }
    }

if($dateRange != "" && $visit != "" && $medicine_name != "") {
    if($id != "") {
        $UpdatereportData = mysqli_query($conn, "UPDATE prescription_emr SET emr_id='$emr_id',date='$dateRange', next_visit='$visit' WHERE emr_id='$emr_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        if($UpdatereportData) {
            $msg = 2;
        }
    } else {
        $InserreportData = mysqli_query($conn, "INSERT INTO prescription_emr(emr_id, date, next_visit, medicine_name, status, create_date_time, create_by, modify_by) VALUES ('$emr_id', '$dateRange','$visit','$medicine_name','1','$datetime','1','1');") or die(mysqli_error($conn));
        if($InserreportData) {
            $msg = 1;
        }
    }
}

echo $msg;
?>


