<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>
<table class="table" id="tableExportP" style="width:100%;">
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
            <th> Name </th>
            <th> Age </th>
            <th> Gender </th>
            <th> Date & Time </th>
            <th> Prescription </th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody style="text-align:center;">
        <?php
        if($SessionUserId == "1"){
            $getAdminMenus = mysqli_query($conn, "SELECT * FROM prescripition WHERE status='1' ORDER BY prescription_id DESC") or die(mysqli_error($conn));
        } else{
            $getAdminMenus = mysqli_query($conn, "SELECT * FROM prescripition WHERE status='1' AND org_id='$SessionOrgId' ORDER BY prescription_id DESC") or die(mysqli_error($conn));
        }

        if($SessionUserId == "1") {
            $getAdminMenus = mysqli_query($conn, "
                SELECT p.*, ao.*
                FROM prescripition p
                LEFT JOIN appointment_online ao 
                    ON p.patient_vitals = ao.appoint_register_id 
                    AND ao.appoint_register_id = p.patient_vitals
                WHERE p.status = '1'
                ORDER BY p.prescription_id DESC, ao.appoint_id DESC
            ") or die(mysqli_error($conn));
        } else {
            $getAdminMenus = mysqli_query($conn, "
                SELECT p.*, ao.*
                FROM prescripition p
                LEFT JOIN appointment_online ao 
                    ON p.patient_vitals = ao.appoint_register_id 
                    AND ao.appoint_register_id = p.patient_vitals
                WHERE p.status = '1' 
                AND p.org_id = '$SessionOrgId'
                ORDER BY p.prescription_id DESC, ao.appoint_id DESC
            ") or die(mysqli_error($conn));
        }
        $i = 1;
        while($resAdminMenus = mysqli_fetch_object($getAdminMenus)){
        ?>
        <tr>
            <td> <?=$i++;?> </td>
            <?php
                if($SessionUserId=="1"){
            ?>
            <td> <?=getUserNameByOrgId($conn, $resAdminMenus->org_id)?> </td>
            <?php
                }
            ?>
            <td> <?=getAppointmentById($conn, $resAdminMenus->patient_uid, $resAdminMenus->org_id)?> </td>
            <td> <?=$resAdminMenus->age?> </td>
            <td> <?=$resAdminMenus->gender?> </td>
            <td> <?= $resAdminMenus->create_date_time?> </td>
            <td style="cursor: pointer; width: 10px"><a class="dropdown-item btn btn-primary" href="patientPrescription.php?ItemId=<?=$resAdminMenus->prescription_id?>" target="_blank"> View</a></td>
            <td class="text-center">
                <ul class="navbar-nav">
                    <li class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left">
                        <a href="#" 
                            class="dropdown-item has-icon edit-prescription"
                            data-prescription='<?= json_encode([
                                "prescription_id"     => $resAdminMenus->prescription_id,
                                "organizations"       => $resAdminMenus->org_id,
                                "patientIdName"       => $resAdminMenus->patient_name,
                                "patientId"           => $resAdminMenus->patient_uid,
                                "appoint_register_id" => $resAdminMenus->appoint_register_id,
                                "age"                 => $resAdminMenus->age,
                                "gender"              => $resAdminMenus->gender,
                                "rx_id"               => $resAdminMenus->rx_id,
                                "test_group_id"       => $resAdminMenus->test_group_id,
                                "tests"               => $resAdminMenus->test_id,
                                "medicines"           => $resAdminMenus->medicine_id,
                                "prescriptiondate"    => $resAdminMenus->prescriptiondate,
                                "patient_vitals"      => $resAdminMenus->patient_vitals,
                                "finalDiagnosis"      => $resAdminMenus->finalDiagnosis,
                                "chiefcomplaint"      => $resAdminMenus->chiefcomplaint,
                                "pasthistory"         => $resAdminMenus->pasthistory,
                                "reviewafter"         => $resAdminMenus->reviewafter,
                                "reviewafterdate"     => $resAdminMenus->reviewafterdate,
                                "bpSit_systolic"      => $resAdminMenus->bpSit_systolic,
                                "bpSit_diastolic"     => $resAdminMenus->bpSit_diastolic,
                                "bpStand_systolic"    => $resAdminMenus->bpStand_systolic,
                                "bpStand_diastolic"   => $resAdminMenus->bpStand_diastolic,
                                "weight"              => $resAdminMenus->weight,
                                "height"              => $resAdminMenus->height,
                                "bmi"                 => $resAdminMenus->bmi,
                                "heart_rate"          => $resAdminMenus->heart_rate,
                                "grbs"                => $resAdminMenus->grbs,
                                "spO2"                => $resAdminMenus->spO2,
                                "patient_overview"    => $resAdminMenus->patient_overview,
                                "respiration_rate"    => $resAdminMenus->respiration_rate,
                                "mobile_number"       => $resAdminMenus->mobile_number,
                                "temperature"         => $resAdminMenus->temperature,
                                "patient_overview"    => $resAdminMenus->patient_overview
                            ]) ?>'
                            >
                            <i class="fa fa-edit"></i> Update
                        </a>

                            
                            <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="deleteP('<?=$resAdminMenus->prescription_id?>', '<?=$resAdminMenus->patientName ?>')"> <i class="fa fa-trash"></i> Delete</a>
                        </div>
                    </li>
                </ul>
            </td>
        </tr>
        <?php } ?>
    </tbody>                            
</table>




















