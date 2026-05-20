<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>


<table class="table" id="tableExport2" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th> S.No </th>
            <?php
                if($SessionUserId=="1"){
            ?>
            <th> Organization </th>
            <?php
                }
            ?>
            <th> Appointment Id </th>
            <th> Registration Id </th>
            <th> Name </th>
            <th> Gender </th>
            <th> age </th>
            <th> Mobile </th>
            <th> Date </th>
            <th> Doctor Name </th>
            <th> Start & End Time </th>
            <th> Bill PDF & Print</th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody class="text-center">

<?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_existing WHERE appoint_status='1' ORDER BY atmt_id  DESC") or die(mysqli_error($conn));
} else{
    $getAppointmentData = mysqli_query($conn, "SELECT * FROM appointment_existing WHERE appoint_status='1' AND org_id='$SessionOrgId' ORDER BY atmt_id  DESC") or die(mysqli_error($conn));
}

$i = 1;
while($resAppointmentData = mysqli_fetch_object($getAppointmentData)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <?php
            if($SessionUserId=="1"){
        ?>
        <td> <?=getUserNameByOrgId($conn, $resAppointmentData->org_id)?> </td>
        <?php
            }
        ?>
        <td> <?=$resAppointmentData->appoint_register_id?> </td>
        <td> <?=$resAppointmentData->appoint_unicode?> </td>
        <td> <?=$resAppointmentData->patient_name?> </td>
        <td> <?=$resAppointmentData->gender?> </td>
        <td> <?=$resAppointmentData->age?> </td>
        <td> <?=$resAppointmentData->mobile_number?> </td>
        <td> <?= $resAppointmentData->appoint_date?> </td>
        <?php
            // $getDoctor = mysqli_query($conn, "SELECT doctorName_registrationNumber FROM doctors_time_slot WHERE doctors_time_id='$resAppointmentData->doctor_name'");
            // $resDoctor=mysqli_fetch_object($getDoctor);
    
            $getDoctorName = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE doc_id='$resAppointmentData->doctor_name'");
            $resDoctorName=mysqli_fetch_object($getDoctorName);
        ?>
        <td> <?= $resDoctorName->doctor_name?> </td>
   
       <td> <?=$resAppointmentData->start_time  ?> / <?=$resAppointmentData->end_time?></td>
       <td class="text-center"> 
            <ul class="navbar-nav">
                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fas fa-paste" style="font-size: 24px;"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-center" style="min-width:165px;">
                        <a href="billViewExisting.php?ItemId=<?=$resAppointmentData->atmt_id ?>" target="_blank" class="dropdown-item has-icon"><i class="far fa-file-pdf"></i> Bill PDF</a>
                        <a href="billPrintExisting.php?ItemId=<?=$resAppointmentData->atmt_id ?>" target="_blank" class="dropdown-item has-icon"><i class="far fa-file-powerpoint"></i> Bill Print</a>
                        <?php if (!empty($resAppointmentData->appoint_id)): ?>
                        <div class="dropdown-divider"></div>
                        <a href="combinedBill.php?appoint_id=<?= $resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item fw-semibold">
                            <i class="fas fa-file-invoice"></i> Combined Bill
                        </a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
       </td>
        <td class="text-center">
            <ul class="navbar-nav">
                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-center" style=" width:40px;">
                    
                    <a href="#" class="dropdown-item has-icon" onclick='editAppointmentExisting(
                            `<?=$resAppointmentData->atmt_id ?>`, 
                            `<?=$resAppointmentData->appoint_id ?>`, 
                            `<?=$resAppointmentData->bill_id?>`, 
                            `<?=$resAppointmentData->appoint_register_id?>`, 
                            `<?=$resAppointmentData->org_id ?>`, 
                            `<?=$resAppointmentData->amount_method?>`, 
                            `<?=$resAppointmentData->patient_name?>`, 
                            `<?=$resAppointmentData->mobile_number?>`, 
                            `<?=$resAppointmentData->appoint_unicode?>`, 
                            `<?=$resAppointmentData->gender?>`, 
                            `<?=$resAppointmentData->systolic?>`, 
                            `<?=$resAppointmentData->diastolic?>`, 
                            `<?=$resAppointmentData->temperature?>`, 
                            `<?=$resAppointmentData->glucose_level?>`, 
                            `<?=$resAppointmentData->age?>`, 
                            `<?=$resAppointmentData->patient_email?>`, 
                            `<?=$resAppointmentData->appoint_date?>`, 
                            `<?=$resAppointmentData->doctor_name?>`, 
                            `<?=$resAppointmentData->amount?>`, 
                            `<?=$resAppointmentData->start_time ?>`, 
                            `<?=$resAppointmentData->end_time?>`,

                            `<?=$resAppointmentData->bpSit_systolic?>`,
                            `<?=$resAppointmentData->bpSit_diastolic?>`,
                            `<?=$resAppointmentData->bpStand_systolic?>`,
                            `<?=$resAppointmentData->bpStand_diastolic?>`,
                            `<?=$resAppointmentData->weight?>`,
                            `<?=$resAppointmentData->height?>`,
                            `<?=$resAppointmentData->bmi?>`,
                            `<?=$resAppointmentData->heart_rate?>`,
                            `<?=$resAppointmentData->grbs?>`,
                            `<?=$resAppointmentData->spO2?>`,
                            `<?=$resAppointmentData->patient_overview?>`,
                            `<?=$resAppointmentData->respiration_rate?>`
                        )'><i class="fa fa-edit"></i> Update</a>

                        <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="deleteAppointmentExisting('<?=$resAppointmentData->atmt_id?>', '<?=$resAppointmentData->patient_name?>')"> <i class="fa fa-times"></i>  Delete </a>
                    </div>
                </li>
            </ul>
        </td>
    </tr>

<?php } ?>

</tbody>                            
</table>