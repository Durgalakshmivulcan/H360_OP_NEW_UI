<?php
require_once("../../config/functions.php");
$id=$_session['org_id'];

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

    $get=mysqli_query($conn,"SELECT org_id FROM organization_table WHERE  status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    while ($res = mysqli_fetch_object($get)) {
        $result[] = $res;
    }


echo json_encode($result);

?>