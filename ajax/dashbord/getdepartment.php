<?php

require_once('../../config/functions.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$results = [];

$adminQry = " AND org_id='$SessionOrgId'";
if ($SessionUserId == "1") {
  $adminQry = "";
}

    $getdepart = mysqli_query($conn,"SELECT DISTINCT dept_id,departmentName FROM department WHERE status='1' $adminQry ORDER BY dept_id  ASC") or die(mysqli_eroror($conn));

while($row = mysqli_fetch_object($getdepart)){
    $results[] = $row;
}

echo json_encode($results);

?>