<?php
require_once("../../config/functions.php");

// if (isset($_POST['departmentName'])) {
    // $query = "SELECT departmentName FROM department WHERE departmentName='".$_POST['departmentName']."'";
    // $result = mysqli_query($query, $conn);

    $dept_id = $_POST['dept_id'];
    $departmentName = $_POST['departmentName'];

    $getAdminDepartment = mysqli_query($conn, "SELECT departmentName FROM department WHERE dept_id='$dept_id'") or die(mysqli_error($conn));
    if ($getAdminDepartment->num_rows > 0) {
        echo "<span style='color:red'>This Department Name is taken. Try another</span>";
      }else{
        echo "<span style='color:green'>This Department Name is available</span>";
      }
// }

?>