<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';

$GetTests = [];

$result = [];

$org_id = $_POST['org_id'];

$GetOrgByTests = mysqli_query($conn, "SELECT test_id,test_name FROM tests WHERE status='1' AND org_id='$org_id'") or die(mysqli_error($conn));
while($resOrgByTests=mysqli_fetch_object($GetOrgByTests)){
    $GetTests[]=$resOrgByTests;
}

$result[] = array(
    'tests' => $GetTests
);

echo json_encode($result);
?>