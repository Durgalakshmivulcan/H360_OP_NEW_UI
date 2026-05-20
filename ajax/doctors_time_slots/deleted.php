<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan('delete', 'doctorstimeslot.php', 'ajax');
$langDeleted = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$delete_id=$_POST['Timeslot_id'] ?? '';
$delete_id1=$_POST['multi_id'] ?? '';

// FIX_B_518: validate Timeslot_id is non-empty + numeric before any SQL.
// Empty string against an int column triggers "Truncated incorrect DECIMAL"
// fatals under strict SQL mode; non-numeric values do the same.
if ($delete_id === '' || !ctype_digit((string)$delete_id)) {
    echo 0;
    exit;
}
$delete_id = (int)$delete_id;
// multi_id is optional; coerce non-numeric to empty so the if(!empty) guard
// below skips the Range UPDATE rather than firing it with bad input.
if ($delete_id1 !== '' && !ctype_digit((string)$delete_id1)) {
    $delete_id1 = '';
}
// Initialise so the final aggregate check works when multi_id is empty.
$qryDelete3 = true;

$beforeQuery = mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$delete_id' LIMIT 1");
$before      = null;
    if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
        $before = mysqli_fetch_assoc($beforeQuery);
    }
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $qryDelete = mysqli_query($conn,"UPDATE doctors_time_slot SET status='0' WHERE doctors_time_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete2 = mysqli_query($conn,"UPDATE doctors_time_slot2 SET status='0' WHERE  doctors_time_id ='$delete_id'") or die(mysqli_error($conn));

    // FIX_B_001: gate on real multi_id POST field
    if (!empty($delete_id1)) { $qryDelete3 = mysqli_query($conn,"UPDATE multi_doctortimeslots SET status='0' WHERE org_id='$SessionOrgId' AND multi_id ='$delete_id1' ") or die(mysqli_error($conn)); }

} else{
    $qryDelete = mysqli_query($conn,"UPDATE doctors_time_slot SET status='0' WHERE org_id='$SessionOrgId' AND doctors_time_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete2 = mysqli_query($conn,"UPDATE doctors_time_slot2 SET status='0' WHERE org_id='$SessionOrgId' AND doctors_time_id ='$delete_id'") or die(mysqli_error($conn));

    // FIX_B_001: gate on real multi_id POST field
    if (!empty($delete_id1)) { $qryDelete3 = mysqli_query($conn,"UPDATE multi_doctortimeslots SET status='0' WHERE org_id='$SessionOrgId' AND multi_id ='$delete_id1' ") or die(mysqli_error($conn)); }

}
if($qryDelete3 && $qryDelete2 && $qryDelete) { // FIX_B_518: bitwise & → logical &&
    $langDeleted = 1;
    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE doctors_time_id='$delete_id'"));

        audit_log($conn, "Doctors Time Slot", "delete", "doctors_time_slot", $delete_id, $before, $after);
}

echo $langDeleted;

?>
