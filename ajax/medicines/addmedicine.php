<?php
// IDOR_FIXED B-584
require_once("../../config/functions.php");
requireCan(empty($_POST['medicine_id']) ? 'add' : 'edit', 'medicines.php', 'ajax'); // FIX_B_1810

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;

$medicine_id = $_POST['medicine_id'];
$dept_id = $_POST['dept_id'];
$org_id = $_post['org_id'];
$type = $_POST['type'];
$medicienename = $_POST['medicienename'];
$scientificname = $_POST['scientificname'];
$dosage = $_POST['dosage'];
$department = $_POST['department'];
$notes = $_POST['notes'];
$organizations = $_POST['organizations'];

$sql=mysqli_query($conn,"SELECT dept_id FROM department WHERE departmentName='$department'") or die(mysqli_error($conn));       
$dept= mysqli_fetch_object($sql);         
$dept_id = $dept->dept_id;

if($type != "" || $medicienename != "" || $scientificname != "" ) {
    if($medicine_id != "") {
        if($SessionUserId == "1"){
            if($SessionUserId != ""){
                $UpdateMenuData = mysqli_query($conn, "UPDATE medicines SET dept_id='$dept_id',medicine_type='$type',medicine_name='$medicienename',scientific_name='$scientificname', dosage='$dosage',department='$department', notes='$notes',modifeid_by='$SessionUserId', org_id='$organizations'  WHERE medicine_id='$medicine_id'") or die(mysqli_error($conn));
                if($UpdateMenuData) {
                    $msg = 2;
                }
            }
        } else{
            $UpdateMenuData = mysqli_query($conn, "UPDATE medicines SET dept_id='$dept_id',medicine_type='$type',medicine_name='$medicienename',scientific_name='$scientificname', dosage='$dosage',department='$department', notes='$notes',modifeid_by='$SessionUserId'  WHERE medicine_id='$medicine_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            if($UpdateMenuData) {
                $msg = 2;
            }
        }
    } else {
        if($SessionUserId == "1"){
            if($SessionUserId != ""){
                $InserMenuData = mysqli_query($conn, "INSERT INTO medicines(dept_id,org_id,medicine_type,medicine_name,scientific_name, dosage,department,notes,status,created_by,modifeid_by) VALUES ('$dept_id','$organizations','$type','$medicienename','$scientificname', '$dosage','$department','$notes', '1', '$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                if($InserMenuData) {
                    $msg = 1;
                }
            }
        } else{
            $InserMenuData = mysqli_query($conn, "INSERT INTO medicines(dept_id,org_id,medicine_type,medicine_name,scientific_name, dosage,department,notes,status,created_by,modifeid_by) VALUES ('$dept_id','$SessionOrgId','$type','$medicienename','$scientificname', '$dosage','$department','$notes', '1', '$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
            if($InserMenuData) {
                $msg = 1;
            }
        }
    }
}

echo $msg;
?>