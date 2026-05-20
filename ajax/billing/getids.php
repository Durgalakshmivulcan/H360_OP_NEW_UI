<?php
require_once "../../config/functions.php";


$appoint_id=$_POST['appoint_id'];
$medicine_id=$_POST['medicine_id'];
$medicine_price=$_POST['medicine_price'];
$test_id=$_POST['test_id'];
$test_price=$_POST['test_price'];
$doc_id=$_POST['doc_id'];
$doctor_fee=$_POST['doctor_fee'];
$service_id=$_POST['service_id'];
$price=$_POST['price'];
$tax_id=$_POST['tax_id'];
$percentage=$_POST['percentage'];
$prescription_id=$_POST['prescription_id'];

for($i=0 ;$i< count($appoint_id);$i++){
// echo json_encode($appoint_id[$i]);
$insertbill=mysqli_query($conn,"INSERT INTO billing(appoint_id,medicine_id,medicine_price,test_id,test_price,doc_id,doctor_fee,service_id,service_price,tax_id,tax_percentage,prescription_id,status,org_id,create_date_time,last_updated) VALUES('$appoint_id[$i]','$medicine_id[$i]','$medicine_price[$i]','$test_id[$i]','$test_price[$i]','$doc_id[$i]','$doctor_fee[$i]','$service_id[$i]','$price[$i]','$tax_id[$i]','$percentage[$i]','$prescription_id[$i]','1','$SessionOrgId','$datetime','$SessionUserId')") or(mysqli_error($conn));

}

echo 'inserted';

?>