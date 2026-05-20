<?php
require_once("../../config/functions.php");
/* B-1830 RBAC */ requireCan('delete', 'doctorstimeslot.php', 'ajax');
$langDeleted = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$delete_id=$_POST['multi_id'];


if($SessionUserId == "1" && $SessionRoleId=="1"){

    $qryDelete3 = mysqli_query($conn,"UPDATE multi_doctortimeslots SET status='0' WHERE multi_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete = mysqli_query($conn,"UPDATE doctors_time_slot SET status='0' WHERE multi_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete2 = mysqli_query($conn,"UPDATE doctors_time_slot2 SET status='0' WHERE  multi_id ='$delete_id'") or die(mysqli_error($conn));

  

} else{

    $qryDelete3 = mysqli_query($conn,"UPDATE multi_doctortimeslots SET status='0' WHERE org_id='$SessionOrgId' AND multi_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete = mysqli_query($conn,"UPDATE doctors_time_slot SET status='0' WHERE org_id='$SessionOrgId' AND multi_id ='$delete_id'") or die(mysqli_error($conn));

    $qryDelete2 = mysqli_query($conn,"UPDATE doctors_time_slot2 SET status='0' WHERE org_id='$SessionOrgId' AND multi_id ='$delete_id'") or die(mysqli_error($conn));


}
if($qryDelete2&$qryDelete3&$qryDelete) {    
    $langDeleted = 1;
}

echo $langDeleted;

?>

