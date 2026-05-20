<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$rx_id = $_POST['rx_group_id'];
$org_id = $_POST['org_id'];

$result = [];

if($rx_id != "") {
    if($SessionUserId == "1"){
        $query = "SELECT * FROM rx_groups_names WHERE rx_group_id='$rx_id' AND org_id='$org_id'";
    } else {
        $query = "SELECT * FROM rx_groups_names WHERE rx_group_id='$rx_id' AND org_id='$SessionOrgId'";
    }

    $res = mysqli_query($conn, $query) or die(mysqli_error($conn));

    if($row = mysqli_fetch_assoc($res)) {
        // Ensure medicine_detailes is valid JSON
        if(empty($row['medicine_detailes'])) {
            $row['medicine_detailes'] = '[]';
        }

        $result = $row;
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($result);
exit;
?>
