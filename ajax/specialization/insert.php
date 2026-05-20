<?php
// IDOR_FIXED B-583
require_once("../../config/functions.php");
requireCan(empty($_POST['Specialization_id']) ? 'add' : 'edit', 'Specialization.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;

$Specialization_id = $_POST['Specialization_id'];
$Specialization = $_POST['Specialization'];
$organizations = $_POST['organizations'];

$addorgid= "AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
$addorgid="AND org_id='$organizations'";
}

if($Specialization != "") {
    if($Specialization_id != "") {
        
        $getAdminSpecialization = mysqli_query($conn,"SELECT specialtisname FROM specialtis WHERE specialtisname='$Specialization' AND status='1'AND specialtis_id !='$Specialization_id' $addorgid") or die(mysqli_error($conn));
        $result=mysqli_num_rows($getAdminSpecialization);
        if ($result > 0) {
            $msg = 3;
        } else{
            $before = null;
            $beforeQuery = mysqli_query($conn, "SELECT * FROM specialtis WHERE specialtis_id='$Specialization_id' LIMIT 1");
            if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
                $before = mysqli_fetch_assoc($beforeQuery);
            }
            if($SessionUserId == "1"){
                if($organizations != "") {
                    $UpdateMenuData = mysqli_query($conn, "UPDATE specialtis SET specialtisname='$Specialization',org_id='$organizations', modified_by='$SessionUserId' WHERE specialtis_id='$Specialization_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                    if($UpdateMenuData) {
                        $msg = 2;
                    }
                }
            } else{
                $UpdateMenuData = mysqli_query($conn, "UPDATE specialtis SET specialtisname='$Specialization',modified_by='$SessionUserId' WHERE specialtis_id='$Specialization_id'") or die(mysqli_error($conn));
                if($UpdateMenuData) {
                    $msg = 2;
                    $after = null;
                $afterQuery = mysqli_query($conn, "SELECT * FROM specialtis WHERE specialtis_id='$Specialization_id' LIMIT 1");
                if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                    $after = mysqli_fetch_assoc($afterQuery);
                }

                // 🔹 Audit log update
                audit_log($conn, "Specialization", "update", "specialtis", $Specialization_id, $before, $after);
                }
            }
        }
    } else {

            $getAdminSpecialization = mysqli_query($conn,"SELECT specialtisname FROM specialtis WHERE specialtisname='$Specialization'AND status='1' $addorgid") or die(mysqli_error($conn));
            $result=mysqli_num_rows($getAdminSpecialization);
       
        if ($result > 0) {
            $msg = 3;
        } else{
            if($SessionUserId == "1"){
                if($organizations != "") {
                    $InserMenuData = mysqli_query($conn, "INSERT INTO specialtis(specialtisname, status,created_by, modified_by,org_id) VALUES ('$Specialization','1','$SessionUserId', '$SessionUserId','$organizations')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $msg = 1;
                    }
                }
            } else{
                if($SessionOrgId != "") {
                    $InserMenuData = mysqli_query($conn, "INSERT INTO specialtis(specialtisname, status,created_by, modified_by,org_id) VALUES ('$Specialization','1','$SessionUserId', '$SessionUserId','$SessionOrgId')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $msg = 1;
                        $newId = mysqli_insert_id($conn);

                        // 🔹 Fetch AFTER row
                        $after = null;
                        $afterQuery = mysqli_query($conn, "SELECT * FROM specialtis WHERE specialtis_id='$newId' LIMIT 1");
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }

                        // 🔹 Audit log create
                        audit_log($conn, "Specialization", "create", "specialtis", $newId, null, $after);
                    } 
                }
            }
        }
    }
}else{
    echo "0";
}
echo $msg;
?>