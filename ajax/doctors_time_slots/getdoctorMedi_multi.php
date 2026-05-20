<?php
require_once("../../config/functions.php");

$result = [];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$multi_id = $_POST['multi_id'];

if($SessionUserId == '1' && $SessionRoleId == '1') /* FIX_B_003 */{
    if($multi_id != ""){

        $getmultidoc=mysqli_query($conn, "SELECT * FROM multi_doctortimeslots2 WHERE status='1' AND multi_id='$multi_id'") or die(mysqli_error($conn));
        while ($resmultidoc=mysqli_fetch_object($getmultidoc)){
            $result[] = $resmultidoc;
        }
    }

}else{

    if($multi_id != ""){
        
        $getmultidoc=mysqli_query($conn, "SELECT * FROM multi_doctortimeslots2 WHERE status='1' AND org_id='$SessionOrgId' AND multi_id='$multi_id'") or die(mysqli_error($conn));
        while ($resmultidoc=mysqli_fetch_object($getmultidoc)){
            $result[] = $resmultidoc;
        }
    }

}

echo json_encode($result);

?>


