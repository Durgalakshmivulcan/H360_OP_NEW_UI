<?php
require_once "../../config/config.php";

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';


$result=[];
$dept_id=$_POST['dept_id'];

if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getdepart=mysqli_query($conn, "SELECT dept_id,departmentName FROM department WHERE status='1'") or die(mysqli_error($conn));
} else{
    $getdepart=mysqli_query($conn, "SELECT dept_id,departmentName FROM department WHERE status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
}
while($resdep=mysqli_fetch_object($getdepart)){

    $result[]=$resdep;  
}

echo json_encode($result);

?>