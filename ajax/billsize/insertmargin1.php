<?php
require_once("../../config/functions.php");
requireCan('edit', 'billsizes.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;

$topMargin = $_POST['topMargin'];
// echo $topMargin;
$bottomMargin = $_POST['bottomMargin'];
// echo $bottomMargin;
$organization = $_POST['organizations'];


$addorg=" AND org_id='$SessionOrgId'";

if($SessionUserId == '1'){
$addorg=" AND org_id='$organization'";
}

$getorg=mysqli_query($conn,"SELECT * FROM bill_sizes WHERE status='1' AND pagetype='1' $addorg");
$row = mysqli_fetch_object($getorg);
$size_id=$row->bill_size_id;

if($size_id != "") {
if($SessionUserId == '1'){

    $update=mysqli_query($conn, "UPDATE bill_sizes SET top='$topMargin',bottom='$bottomMargin' WHERE status='1'  AND org_id='$organization' AND pagetype='1'  AND bill_size_id='$size_id'");

    echo "UPDATE bill_sizes SET top='$topMargin',bottom='$bottomMargin' WHERE status='1'  AND org_id='$organization' AND pagetype='1'  AND bill_size_id='$size_id'";

    if($update) {
        $msg = 1;
    }

}else{

    $update=mysqli_query($conn, "UPDATE bill_sizes SET top='$topMargin',bottom='$bottomMargin' WHERE status='1' AND org_id='$SessionOrgId' AND pagetype='1' AND bill_size_id='$size_id'");
    
    if($update) {
        $msg = 1;
    }
}
}   

echo $msg;

?>      