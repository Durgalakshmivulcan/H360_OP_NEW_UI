<?php

require_once('../../config/functions.php');

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$dept_id = $_POST['dept_id'] ?? '';
if ($dept_id === '' || $dept_id === null) {
    echo "<tr><td colspan='3'>No services</td></tr>";
    exit;
}

// dept_id=0 means "All Departments" — return every active doctor in the org
if ($dept_id == '0') {
    if ($SessionUserId == '1') {
        $getdocservice = mysqli_query($conn, "SELECT d.doc_registration_number, d.doctor_name, d.doctor_services, COALESCE(dep.departmentName,'') AS departmentName FROM doctors d LEFT JOIN department dep ON dep.dept_id = d.departments AND dep.status='1' WHERE d.status='1' ORDER BY d.doctor_name");
    } else {
        $getdocservice = mysqli_query($conn, "SELECT d.doc_registration_number, d.doctor_name, d.doctor_services, COALESCE(dep.departmentName,'') AS departmentName FROM doctors d LEFT JOIN department dep ON dep.dept_id = d.departments AND dep.status='1' WHERE d.status='1' AND d.org_id='$SessionOrgId' ORDER BY d.doctor_name");
    }
} elseif ($SessionUserId == '1') {
    $getdocservice = mysqli_query($conn, "SELECT DISTINCT d.doc_registration_number, d.doctor_name, d.doctor_services, COALESCE(dep.departmentName,'') AS departmentName FROM department dep INNER JOIN doctors d ON dep.dept_id = d.departments LEFT JOIN department dep2 ON dep2.dept_id = d.departments AND dep2.status='1' WHERE dep.dept_id='$dept_id' AND d.status='1'");
} else {
    $getdocservice = mysqli_query($conn, "SELECT DISTINCT d.doc_registration_number, d.doctor_name, d.doctor_services, COALESCE(dep.departmentName,'') AS departmentName FROM department dep INNER JOIN doctors d ON dep.dept_id = d.departments WHERE dep.dept_id='$dept_id' AND dep.org_id='$SessionOrgId' AND d.status='1'");
}

// FIX_B_160: emit placeholder when zero rows match
if (mysqli_num_rows($getdocservice) === 0) {
    echo "<tr><td colspan='4'>No services</td></tr>";
}
while($resdocservice=mysqli_fetch_object($getdocservice)){

    $doctor_name=$resdocservice->doctor_name;
    $doc_registration_number=$resdocservice->doc_registration_number;
    $doctorservice=$resdocservice->doctor_services;
    $departmentName=$resdocservice->departmentName;

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
<td>$departmentName</td>
<td>$doctorname_string</td>
</tr>";


}


?>