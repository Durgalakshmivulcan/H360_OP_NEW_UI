<?php
require_once "../../../config/functions.php";

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result=[];

$addorg_id = "AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
    $addorg_id="";
}

// FIX_B_2330: never offer the Super Admin role in the create/edit picker.
// SA is bootstrap-only — nobody (not even SA themselves) should be able to
// mint or re-tag SA accounts through this form.
$getroleid = mysqli_query($conn, "SELECT role_id, role_name FROM roles WHERE status='1' AND role_id <> 1 $addorg_id") or die(mysqli_error($conn));
while($resRoles = mysqli_fetch_object($getroleid)){

    $result[] = $resRoles;  

}
echo json_encode($result);

?>