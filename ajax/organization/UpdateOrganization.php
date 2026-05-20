<?php
require_once("../../config/functions.php");



$org_id = $_POST['org_id'];
$organization_name = $_POST['organization_name'];
$description = $_POST['description'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$gstNumber = $_POST['gstNumber'];
$tanNumber = $_POST['tanNumber'];
$address = $_POST['address'];

    
if($org_id != "") {
    $UpdateOrganizationData = mysqli_query($conn, "UPDATE organization SET organization_name='$organization_name', description='$description', address='$address', longitude='$longitude', latitude='$latitude', gstNumber='$gstNumber', tanNumber='$tanNumber', modified_by='$SessionUserId' WHERE org_id='$org_id'") or die(mysqli_error($conn));
    // if($UpdateOrganizationData) {
        $msg = 2;
    // }
}
else{
    $msg = 0;
}

echo $msg;
?>