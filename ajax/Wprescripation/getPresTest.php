<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$result = [];

$prescription_id = $_POST['prescription_id'];

if($prescription_id != "") {
    $getPrescriptions=mysqli_query($conn, "SELECT test_id FROM prescription_test WHERE status='1' AND prescription_id='$prescription_id'") or die(mysqli_error($conn));
    while ($resPrescriptions=mysqli_fetch_object($getPrescriptions)){
        $result[] = $resPrescriptions;
    }
}
echo json_encode($result);

?>


