<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$test_group_id =  $_POST['test_group_id'];
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppoint=mysqli_query($conn, "SELECT test_id FROM test_group WHERE status='1' AND test_group_id='$test_group_id'") or die(mysqli_error($conn));
} else{
    $getAppoint=mysqli_query($conn, "SELECT test_id FROM test_group WHERE status='1' AND org_id='$SessionOrgId' AND test_group_id='$test_group_id'") or die(mysqli_error($conn));
}
$resAppoint=mysqli_fetch_object($getAppoint);
$testArr = explode(',', $resAppoint->test_id);
for ($i=0; $i < count($testArr); $i++){
    $result[] = $testArr[$i];
}
echo json_encode($result);


?>


