<?php
require_once "../../config/config.php";

// $id=$_SESSION['dept_id'];

// $dept=$_POST['department'];


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$andorg=" AND org_id='$SessionOrgId'";

if($SessionUserId == '1' && $SessionRoleId == '1') /* FIX_B_003 */{
    $andorg="";
}
$result=[];
$Timeslot_id = $_SESSION['doc_id'];
$getdepart=mysqli_query($conn, "SELECT  doc_registration_number,doctor_name FROM doctors WHERE status='1' $andorg ORDER BY doc_id DESC") or die(mysqli_error($conn));
while($resMenus=mysqli_fetch_object($getdepart)){

    $result[]=$resMenus;  
}
    
echo json_encode($result);

?>