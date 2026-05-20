<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$result = [];

$prescription_id = $_POST['prescription_id'];

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if($SessionUserId == "1" && $SessionRoleId=="1"){
    if($prescription_id != "") {
        $getPrescriptions=mysqli_query($conn, "SELECT medicine_id, type_id, unit_id, dosage_id,intake_id,time_id,frequency_ids,test_id,duration,quantity,note FROM prescription_medicines WHERE status='1' AND prescription_id='$prescription_id'") or die(mysqli_error($conn));
        while ($resPrescriptions=mysqli_fetch_object($getPrescriptions)){
            $result[] = $resPrescriptions;
        }
    }
} else{
    if($prescription_id != "") {
        $getPrescriptions=mysqli_query($conn, "SELECT medicine_id, type_id, unit_id, dosage_id,intake_id,time_id,frequency_ids,test_id,duration,quantity,note FROM prescription_medicines WHERE status='1' AND org_id='$SessionOrgId' AND prescription_id='$prescription_id'") or die(mysqli_error($conn));
        while ($resPrescriptions=mysqli_fetch_object($getPrescriptions)){
            $result[] = $resPrescriptions;
        }
    }
}

echo json_encode($result);

?>

