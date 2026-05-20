<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$org_id = $_POST['org_id'];

$getMenus = mysqli_query($conn,"SELECT  medicine_id,medicine_name,scientific_name FROM medicines  WHERE status='1' AND org_id='$org_id' ORDER BY medicine_id DESC") or die(mysqli_error($conn));

while ($resMenus = mysqli_fetch_object($getMenus)) {

    $result[] = array(
        "medicine_id" => $resMenus->medicine_id,
        "medicine_name" => $resMenus->medicine_name,
        "scientific_name" => $resMenus->scientific_name
    );

}

echo json_encode($result);
?>