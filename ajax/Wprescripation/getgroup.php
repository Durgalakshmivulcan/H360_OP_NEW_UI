<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$rx_id = $_GET['rx_id']; // Assuming you are using GET method to pass the 'rx_id' parameter
$result = array();

$getrx = mysqli_query($conn, "SELECT medicine_name, dosage, in_time_period FROM rx_groups WHERE status = '1' AND org_id='$SessionOrgId' AND rx_id = '$rx_id'") or die(mysqli_error($conn));
while ($resrx = mysqli_fetch_assoc($getrx)) {
    $result[] = $resrx;
}

echo json_encode($result);

?>



