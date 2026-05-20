<?php

require_once('../../config/functions.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';


$results = [];

if($SessionUserId == "1"){
$gettests = mysqli_query($conn,"SELECT DISTINCT test_group_id,test_group_name FROM test_group WHERE status='1'  ORDER BY test_group_id ASC") or die(mysqli_error($conn));
}else{
$gettests = mysqli_query($conn,"SELECT DISTINCT test_group_id,test_group_name FROM test_group WHERE status='1' AND org_id='$SessionOrgId'  ORDER BY test_group_id ASC") or die(mysqli_error($conn));

}
while($row = mysqli_fetch_object($gettests)){
    $results[] = $row;
}

echo json_encode($results);

?>