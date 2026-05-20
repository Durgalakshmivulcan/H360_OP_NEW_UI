<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
$CurrentDate = date('Y-m-d');
?>


<table class="table" id="tableExport1" style="width:100%;">
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
            <th> Bill PDF & Print</th>
            <th> Action </th>
            <th> Appointment Id </th>
            <th> Registration Id </th>
            <th> Name </th>
            <th> Gender </th>
            <th> age </th>
            <th> Mobile </th>
            <th> Date </th>
            <th> Doctor Name </th>
            <th> Start & End Time </th>
         
        </tr>
    </thead>
    <tbody class="text-center">

<?php
if ($SessionUserId == "1" && $SessionRoleId == "1") {
    // Admin user - should see all appointments of today
    $getAppointmentData = mysqli_query(
        $conn,
        "SELECT ao.* 
         FROM appointment_online ao 
         LEFT JOIN doctors d ON ao.doctor_name = d.doc_id 
         WHERE ao.appoint_status='1' 
           AND ao.appoint_date='$CurrentDate' 
         ORDER BY ao.appoint_id DESC"
    ) or die(mysqli_error($conn));

} else {
    // Other users - restricted to their org & security_id
    $getAppointmentData = mysqli_query(
        $conn,
        "SELECT ao.* 
         FROM appointment_online ao 
         LEFT JOIN doctors d ON ao.doctor_name = d.doc_id 
         WHERE ao.appoint_status='1' 
           AND ao.appoint_date='$CurrentDate' 
           AND d.security_id = '$SessionUserId' 
           AND ao.org_id = '$SessionOrgId'
         ORDER BY ao.appoint_id DESC"
    ) or die(mysqli_error($conn));
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
          <td class="text-center"> 
            <ul class="navbar-nav">
                <?php if(!empty($resAppointmentData->invoice_payment ) && $resAppointmentData->invoice_payment != '0') { ?>

                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fas fa-paste" style="font-size: 24px;"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-center" style="width: auto; min-width: 165px;">
                            <a href="billview.php?ItemId=<?=$resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item">
                                <i class="far fa-file-pdf"></i> Bill PDF
                            </a>
                            <a href="billPrint.php?ItemId=<?=$resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item">
                                <i class="far fa-file-powerpoint"></i> Bill Print
                            </a>
                            <a href="combinedBill.php?appoint_id=<?=$resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item fw-semibold">
                                <i class="fas fa-file-invoice"></i> Combined Bill
                            </a>
                    </div>
                </li>
            <?php } else { ?>
                    <a href="#" class="dropdown-item" onclick="makePayment('<?=$resAppointmentData->appoint_register_id?>', 
                        '<?=$resAppointmentData->org_id?>', 
                        '<?=$resAppointmentData->appoint_unicode?>', 
                        '<?=$resAppointmentData->amount?>')">
                        <i class="bi bi-cash-coin" style="font-size: 24px;"></i>
                    </a>
                <?php } ?>
            </ul>
        </td>


        <td class="text-center">
            <a href="#" class="has-icon me-3" onclick='editAppointment(
                    `<?=$resAppointmentData->org_id?>`,
                    `<?=$resAppointmentData->amount?>`,
                    `<?=$resAppointmentData->amount_method?>`,
                    `<?=$resAppointmentData->bill_id?>`,
                    `<?=$resAppointmentData->appoint_id?>`,
                    `<?=$resAppointmentData->appoint_register_id?>`,
                    `<?=$resAppointmentData->patient_name?>`,
                    `<?=$resAppointmentData->appoint_unicode?>`,
                    `<?=$resAppointmentData->mobile_number?>`,
                    `<?=$resAppointmentData->gender?>`,
                    `<?=$resAppointmentData->systolic?>`,
                    `<?=$resAppointmentData->diastolic?>`,
                    `<?=$resAppointmentData->temperature?>`,
                    `<?=$resAppointmentData->glucose_level?>`,
                    `<?=$resAppointmentData->age?>`,
                    `<?=$resAppointmentData->patient_email?>`,
                    `<?=$resAppointmentData->appoint_date?>`,
                    `<?=$resAppointmentData->doctor_name?>`,
                    `<?=$resAppointmentData->start_time?>`,
                    `<?=$resAppointmentData->end_time?>`,
                    `<?=$resAppointmentData->doctor_fee?>`,

                    `<?=$resAppointmentData->bpSit_systolic?>`,
                    `<?=$resAppointmentData->bpSit_diastolic?>`,
                    `<?=$resAppointmentData->bpStand_systolic?>`,
                    `<?=$resAppointmentData->bpStand_diastolic?>`,
                    `<?=$resAppointmentData->weight?>`,
                    `<?=$resAppointmentData->height?>`,
                    `<?=$resAppointmentData->bmi?>`,
                    `<?=$resAppointmentData->heart_rate?>`,
                    `<?=$resAppointmentData->temperature?>`,
                    `<?=$resAppointmentData->grbs?>`,
                    `<?=$resAppointmentData->spO2?>`,
                    `<?=$resAppointmentData->patient_overview?>`,
                    `<?=$resAppointmentData->respiration_rate?>`,
                    `<?=$resAppointmentData->transaction_number?>`,
                    `<?=$resAppointmentData->concession_name?>`,
                    `<?=$resAppointmentData->concession_type?>`,
                    `<?=$resAppointmentData->concession_value?>`,
                    `<?=$resAppointmentData->referred_by?>`,
                    `<?=$resAppointmentData->referral_hospital?>`,
                    `<?=$resAppointmentData->referral_notes?>`,
                    `<?=$resAppointmentData->referral_type?>`,
                    `<?=$resAppointmentData->dob?>`
                )'><i class="fa fa-edit fa-lg"></i></a>&nbsp;&nbsp;&nbsp;

            <a class="text-danger has-icon" style="cursor:pointer;" onclick="deleteAppointment('<?=$resAppointmentData->appoint_id?>', '<?=$resAppointmentData->patient_name?>')"> <i class="fa fa-trash fa-lg"></i>  </a>
        </td>
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
     
    </tr>

<?php } ?>

</tbody>                            
</table>