<?php
require_once("../../config/functions.php");
requireCan('edit', 'billsizes.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;

// $size_id = $_POST['size_id'];
$topMargin1 = $_POST['topMargin'];  
$bottomMargin1 = $_POST['bottomMargin'];
$organization = $_POST['organizations1'];

echo $bottomMargin1;

$addorg=" AND org_id='$SessionOrgId'";

if($SessionUserId == '1'){
$addorg=" AND org_id='$organization'";
}

$getorg=mysqli_query($conn,"SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' $addorg");
$row = mysqli_fetch_object($getorg);
$size_id=$row->bill_size_id;

if($size_id != "") {
if($SessionUserId == '1'){
    $update=mysqli_query($conn, "UPDATE bill_sizes SET top='$topMargin1',bottom='$bottomMargin1'  WHERE status='1' AND org_id='$organization' AND pagetype='2' AND bill_size_id='$size_id'");
    if($update) {
        $msg = 1;
    }
}else{
    $update=mysqli_query($conn, "UPDATE bill_sizes SET top='$topMargin1',bottom='$bottomMargin1' WHERE status='1' AND pagetype='2' AND org_id='$SessionOrgId' AND bill_size_id='$size_id'");
    if($update) {
        $msg = 1;
    }
}

}   

echo $msg;

?>