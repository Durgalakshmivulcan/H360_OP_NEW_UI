<?php

require_once('../../config/functions.php');

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$dept_id = $_POST['dept_id'] ?? '';
// FIX_B_160: server-side fallback — never return empty 200 body
if ($dept_id === '' || $dept_id === null) {
    echo "<tr><td colspan='3'>No services</td></tr>";
    exit;
}
if($SessionUserId =='1'){
$getdocservice=mysqli_query($conn,"SELECT DISTINCT doc_registration_number,doctor_name,doctor_services FROM  department INNER JOIN doctors ON department.dept_id=doctors.departments WHERE department.dept_id='$dept_id' AND doctors.status='1'");
}else{
$getdocservice=mysqli_query($conn,"SELECT DISTINCT doc_registration_number,doctor_name,doctor_services FROM  department INNER JOIN doctors ON department.dept_id=doctors.departments WHERE department.dept_id='$dept_id' AND department.org_id='$SessionOrgId' AND doctors.status='1'");
}

// FIX_B_160: emit placeholder when zero rows match
if (mysqli_num_rows($getdocservice) === 0) {
    echo "<tr><td colspan='3'>No services</td></tr>";
}
while($resdocservice=mysqli_fetch_object($getdocservice)){

    $doctor_name=$resdocservice->doctor_name;
    $doc_registration_number=$resdocservice->doc_registration_number;
    $doctorservice=$resdocservice->doctor_services;

    // $getservice=mysqli_query($conn,"SELECT * FROM services WHERE service_id='$resdocservice->doctor_services'");
    // $resdocservice=mysqli_fetch_object($getservice);
    // $doctor_service=$resdocservice->service_name;

$doctor_id_array = array_map('intval', explode(',', $doctorservice));

$doctor_id_string = "'" . implode("','", $doctor_id_array) . "'";


$query = "SELECT * FROM services WHERE service_id IN ($doctor_id_string)";
$getdoctors = mysqli_query($conn, $query) or die(mysqli_error($conn));

$doctornames = array(); 

if (mysqli_num_rows($getdoctors) > 0) {
    while ($res6 = mysqli_fetch_object($getdoctors)) {
        $doctornames[] = $res6->service_name; 
    }

    $doctorname_string = implode(', ', $doctornames);

    // echo $doctorname_string;
} else {
    // echo "No doctors found."; 
}



echo "<tr>
<td>$doc_registration_number</td>
<td>$doctor_name</td>
<td>$doctorname_string</td>
</tr>";


}


?>