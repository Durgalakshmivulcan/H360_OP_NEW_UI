<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$queryCondition = ($SessionUserId == "1" && $SessionRoleId == "1") ? "" : "AND org_id='$SessionOrgId'";

// FIX_B_1903: doctor-scope filter (applied to both UNION arms via the doctor_name column)
$docScopeAe = currentDoctorScopeSql('ae.doctor_name');
$docScopeAo = currentDoctorScopeSql('ao.doctor_name');

$getVitals = mysqli_query($conn, "
    SELECT ae.appoint_register_id AS appointment_id, ae.patient_name, v.*
    FROM appointment_existing ae
    INNER JOIN vitals v ON ae.appoint_register_id = v.appointment_id
    WHERE ae.appoint_status='1' AND status = '1' " . (($SessionUserId == "1" && $SessionRoleId == "1") ? "" : "AND ae.org_id='$SessionOrgId'") . " $docScopeAe

    UNION ALL

    SELECT ao.appoint_register_id AS appointment_id, ao.patient_name, v.*
    FROM appointment_online ao
    INNER JOIN vitals v ON ao.appoint_register_id = v.appointment_id
    WHERE ao.appoint_status='1' AND status = '1' " . (($SessionUserId == "1" && $SessionRoleId == "1") ? "" : "AND ao.org_id='$SessionOrgId'") . " $docScopeAo
") or die(mysqli_error($conn));
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
            <th> Patient Name </th>
            <th> Blood Pressure (Sitting) </th>
            <th> Blood Pressure (Standing) </th>
            <th> Weight </th>
            <th> Height </th>
            <th> Blood Group </th>
            <th> Heart Rate </th>
            <th> Temperature </th>
            <th> Respiration Rate </th>
            <th> SPO2 % </th>
            <th> BMI </th>
            <th> GRBS </th>
            <th> CPAP </th>
            <th> HFNC </th>
            <th> VO2 </th>
            <th> Over-View of Patient</th>
            <th> Actions </th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php
        $i = 1;
        while ($resVitals = mysqli_fetch_object($getVitals)) {
        ?>
            <tr>
                <td> <?= $i++; ?> </td>
                <?php
                    if($SessionUserId=="1"){
                ?>
                <td> <?=getUserNameByOrgId($conn, $resVitals->org_id)?> </td>
                <?php
                    }
                ?>
                <td> <?= $resVitals->appointment_id ?> </td>
                <td> <?= $resVitals->patient_name ?> </td>
                <td> <?= $resVitals->BPsit ?? 'N/A' ?> </td>
                <td> <?= $resVitals->BPstand ?? 'N/A' ?> </td>
                <td> <?= $resVitals->weight ?? 'N/A' ?> </td>
                <td> <?= $resVitals->height ?? 'N/A' ?> </td>
                <td> <?= $resVitals->bloodgroup ?? 'N/A' ?> </td>
                <td> <?= $resVitals->heartrate ?? 'N/A' ?> </td>
                <td> <?= $resVitals->temperature ?? 'N/A' ?> </td>
                <td> <?= $resVitals->resp ?? 'N/A' ?> </td>
                <td> <?= $resVitals->sp02percent ?? 'N/A' ?> </td>
                <td> <?= $resVitals->BMIvalue ?? 'N/A' ?> </td>
                <td> <?= $resVitals->GRBS ?? 'N/A' ?> </td>
                <td> <?= $resVitals->CPAP ?? 'N/A' ?> </td>
                <td> <?= $resVitals->HFNC ?? 'N/A' ?> </td>
                <td> <?= $resVitals->VO2 ?? 'N/A' ?> </td>
                <td> <?= $resVitals->Overviewofpatient ?? 'N/A' ?> </td>
                <td class="text-center">
                    <!-- <ul class="navbar-nav">
                        <li> -->
                            <!-- <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                                <i class="fa fa-ellipsis-v"></i>
                            </a> -->
                            <!-- <div class="dropdown-menu dropdown-menu-center" style=" width:40px;"> -->
                                <a href="#" class="edit-vital"
                                data-vital='<?= json_encode($resVitals) ?>'>
                                <i class="fa fa-edit"></i> 
                                </a>

                                <a class="delete-vital text-danger" 
                                data-deletevitalid="<?= $resVitals->vital_id ?>" 
                                data-patientname="<?= $resVitals->patient_name ?>"  
                                style="cursor:pointer;">
                                <i class="fa fa-trash"></i>
                                </a>
                            <!-- </div> -->
                        <!-- </li> -->
                    <!-- </ul> -->
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
