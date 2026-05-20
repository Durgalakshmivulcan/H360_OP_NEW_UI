<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>
<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th> S.No </th>
            <th> Patient Id </th>
            <th> Patient Name </th>
        </tr>
    </thead>
    <tbody class="text-center">

<?php
// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND visitor_status!='0' AND appoint_date='$currentDate' $docScope ORDER BY appoint_id ASC") or die(mysqli_error($conn));
} else {
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND visitor_status!='0' AND appoint_date='$currentDate' $docScope ORDER BY appoint_id ASC") or die(mysqli_error($conn));
}


$i = 1;
while($resAppointmentData = mysqli_fetch_object($getAppointmentData)){
    $visitor_status = $resAppointmentData->visitor_status;

    $bgColor = "";
    $textColor = "";
    if ($visitor_status == 1) {
        $bgColor = "#FFFFFF";
        $textColor = "black";
    } elseif ($visitor_status == 2) {
        $bgColor = "#6777EF";
        $textColor = "white";
    } elseif ($visitor_status == 0) {
        $bgColor = "black"; 
        $textColor = "white"; 
    }

    $style = "background-color: $bgColor; color: $textColor;";
?>
    <tr id="visitorDoctor<?= $i?>" onclick="visitorDoctor(<?= $i?>,'<?=$resAppointmentData->appoint_id?>')" style="<?= $style ?>; cursor: pointer;">
        <td> <?=$i++;?> </td>
        <td> <?=$resAppointmentData->appoint_unicode?> </td>
        <td> <?=$resAppointmentData->patient_name?> </td>
    </tr>

<?php 
//  $i++;
}
 ?>

</tbody>                            
</table>