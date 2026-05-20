<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$medicine_id = $_POST['medicine_id'];

if($medicine_id != "") {
    $getrx=mysqli_query($conn, "SELECT medicine_id,medicine_name,medicine_type,dosage FROM medicines WHERE status='1' AND medicine_id='$medicine_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    while ($resrx=mysqli_fetch_object($getrx)){
        $result[] = $resrx;
    }
}
echo json_encode($result);
// echo "SELECT medicine_id,medicine_name,medicine_type,dosage FROM medicines WHERE status='1' AND medicine_id='$medicine_id'";

?>


