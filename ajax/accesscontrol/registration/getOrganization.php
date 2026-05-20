<?php
require_once "../../../config/config.php";

$result=[];
$org_id=$_POST['org_id'];

$getOrganizationId = mysqli_query($conn, "SELECT * FROM organization WHERE  status='1'") or die(mysqli_error($conn)); 
while($resOrganization = mysqli_fetch_object($getOrganizationId)){

    $result[] = $resOrganization;  

}
echo json_encode($result);

?>


