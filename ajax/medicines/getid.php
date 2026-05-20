<?php
require_once "../../config/config.php";

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';


$dept=$_POST['department'];
$dept_id=$_POST['dept_id'];

$result=[];
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getdepart=mysqli_query($conn, "SELECT dept_id,department FROM department WHERE status='1' And department='$dept' ") or die(mysqli_error($conn));
} else{
    $getdepart=mysqli_query($conn, "SELECT dept_id,department FROM department WHERE status='1' And department='$dept' AND org_id='$SessionOrgId' ") or die(mysqli_error($conn));
}
$department_id=mysqli_num_rows($getdepart);

if($department_id>0){
    $resdep=mysqli_fetch_object($getdepart);

    $result=$resdep->dept_id;

}

echo json_encode($result);

?>