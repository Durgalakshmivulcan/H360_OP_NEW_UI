<?php
// IDOR_FIXED B-587
require_once("../../config/functions.php");
requireCan(empty($_POST['size_id']) ? 'add' : 'edit', 'billsizes.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';    

$msg = 0;

$size_id = $_POST['size_id'];
// $size_id2 = $_POST['size_id2'];
$sizes = $_POST['sizes'];
$organizations = $_POST['organizations'];
// $ckeditor_CP = $_POST['ckeditor_CP'];


$addorgid= "AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
    $addorgid="";
}

if($size_id != "") {

        if($SessionUserId == '1'){
            
            $update=mysqli_query($conn, "UPDATE bill_sizes SET sizes='$sizes', modify_by='$SessionUserId',org_id='$organizations' WHERE status='1' AND pagetype='1' AND bill_size_id='$size_id'");

            $updatepage=mysqli_query($conn, "UPDATE orgpages SET sizes='$sizes' WHERE status='1' AND org_id='$organizations'");

            if($update & $updatepage) {
                $msg = 2;
            }
        }else{
            $update=mysqli_query($conn, "UPDATE bill_sizes SET sizes='$sizes', modify_by='$SessionUserId',org_id='$SessionOrgId' WHERE status='1' AND pagetype='1' AND bill_size_id='$size_id' AND org_id='$SessionOrgId'");

            $updatepage=mysqli_query($conn, "UPDATE orgpages SET sizes='$sizes' WHERE status='1' AND org_id='$SessionOrgId'");

            if($update & $updatepage) {
                $msg = 2;
            }
        }

} else{


            if($SessionUserId == '1'){
                $InserMenuData = mysqli_query($conn, "INSERT INTO bill_sizes(sizes, pagetype, status, created_by, modify_by, org_id) VALUES ('$sizes','1','1','$SessionUserId', '$SessionUserId','$organizations')") or die(mysqli_error($conn));

                $InsersizeData = mysqli_query($conn, "INSERT INTO orgpages(org_id, pagesize, status) VALUES ('$organizations','$sizes','1')") or die(mysqli_error($conn));

                if($InserMenuData & $InsersizeData) {
                    $msg = 1;
                }
            }else{
                $InserMenuData = mysqli_query($conn, "INSERT INTO bill_sizes(sizes, pagetype, status, created_by, modify_by, org_id) VALUES ('$sizes','1','1','$SessionUserId', '$SessionUserId','$SessionOrgId')") or die(mysqli_error($conn));

                $InsersizeData = mysqli_query($conn, "INSERT INTO orgpages(org_id, pagesize, status) VALUES ('$SessionOrgId','$sizes','1')") or die(mysqli_error($conn));

                if($InserMenuData & $InsersizeData) {
                    $msg = 1;
                }
            }

}

echo $msg;
?>