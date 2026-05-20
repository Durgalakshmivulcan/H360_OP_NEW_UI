<?php
// IDOR_FIXED B-580
require_once("../../config/functions.php");

$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate. Empty invoice_id → 'add'; non-empty → 'edit'.
requireCan(empty($_POST['invoice_id']) ? 'add' : 'edit', 'bill.php', 'ajax');

$msg = 0;

$invoice_id = $_POST['invoice_id'];
$doctor_name = $_POST['doctor_name'];
$mobile_number = $_POST['mobile_number'];
$invoice_date = $_POST['invoice_date'];
$receipt_no = $_POST['receipt_no'];
$MR_No = $_POST['MR_No'];
$age = $_POST['age'];
$p_number = $_POST['p_number'];
$gender = $_POST['gender'];
$test_name = $_POST['test_name'];
$charge = $_POST['charge'];
$sub_total = $_POST['sub_total'];
$paid = $_POST['paid'];
$due = $_POST['due'];
$final_total = $_POST['final_total'];
$bloodGroup_type = $_POST['bloodGroup_type'];
$referred_type = $_POST['referred_type'];
$channel_type = $_POST['channel_type'];
$address_type = $_POST['address_type'];

if($doctor_name != "" && $mobile_number != "" && $invoice_date != "" ) {
    if($invoice_id != "") {
        $UpdateInvoiceData = mysqli_query($conn, "UPDATE invoice SET doctor_name='$doctor_name', mobile_number='$mobile_number', invoice_date='$invoice_date', receipt_no='$receipt_no', MR_No='$MR_No', age='$age', p_number='$p_number', gender='$gender', test_name='$test_name', charge='$charge', sub_total='$sub_total', paid='$paid', due='$due', final_total='$final_total', created_by='$SessionUserId',modified_by='$SessionUserId' WHERE invoice_id='$invoice_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        if($UpdateInvoiceData) {
            $msg = 2;
        }
    } else {
        $InsertInvoiceData = mysqli_query($conn, "INSERT INTO invoice(invoice_id, doctor_name, mobile_number, invoice_date, receipt_no, MR_No, p_number, age, gender,test_name, charge, sub_total, paid, due, final_total, status, created_by, modified_by, modified_date_time) VALUES ('$invoice_id','$doctor_name','$mobile_number','$invoice_date','$receipt_no','$MR_No','$age','$p_number','$gender', '$test_name', '$charge', '$sub_total', '$paid', '$due', '$final_total', '1','$SessionUserId','$SessionUserId','$SessionUserId')") or die(mysqli_error($conn));
        if($InsertInvoiceData) {
            $msg = 1;
        }
    }
}

echo $msg;
