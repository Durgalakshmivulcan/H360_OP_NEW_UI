<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';   

$organizations = $_POST['organizations'];
$getsizeid=mysqli_query($conn,"SELECT bill_size_id FROM bill_sizes WHERE status='1' AND org_id='$organizations' AND pagetype='2'");

$row= mysqli_fetch_object($getsizeid);
    $result=$row->bill_size_id;
    
echo $result;
?>