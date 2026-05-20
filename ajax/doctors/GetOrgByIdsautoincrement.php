<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
// $SessionOrgId = $_SESSION['org_id'] ?? '';

$departmentName = [];
$specialtisname = [];
$service_name = [];

$result = [];

$org_id = $_POST['org_id'];

if(!$org_id) {
    $result[] = array(
        'doc_registration_number' => '',
        'departmentName' => '',
        'service_name' => '',
        'specialtisname' => ''
    );  
    echo json_encode($result);
    return;
}

$id = generateNextDocID($conn, $org_id, $SessionUserId, $currentDate);


// $departmentName="";
$getdepartment= mysqli_query($conn,"SELECT dept_id, departmentName FROM department WHERE status='1' AND org_id='$org_id'");
while($resdepart=mysqli_fetch_object($getdepartment)){

    $departmentName[]=$resdepart;
    
}

// $specialtisname="";
$getspecial= mysqli_query($conn,"SELECT specialtis_id,specialtisname FROM specialtis WHERE status='1' AND org_id='$org_id'");
while($resspecial=mysqli_fetch_object($getspecial)){

    $specialtisname[]=$resspecial;

}

// $service_name="";
$getservice= mysqli_query($conn,"SELECT service_id, service_name FROM services WHERE status='1' AND org_id='$org_id'");
while($resservice=mysqli_fetch_object($getservice)){

    $service_name[]=$resservice;

}

$result[] = array(
    'doc_registration_number' => $id,
    'departmentName' => $departmentName,
    'service_name' => $service_name,
    'specialtisname' => $specialtisname

);

echo json_encode($result);

?>