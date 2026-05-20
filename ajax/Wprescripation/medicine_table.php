<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan('view', 'prescription.php', 'ajax');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
if ($SessionUserId == "1") {
    $query = "
        SELECT 
            l.*, 
            p.prescribed_on,
            d.dosages,
            ip.intake_name,
            t.time AS times
        FROM inp_prescription_list l
        LEFT JOIN inp_prescription p ON l.prescription_id = p.prescription_id
        LEFT JOIN dosage d ON l.dosage = d.dosage_id
        LEFT JOIN in_take_period ip ON l.intake_period = ip.intake_id
        LEFT JOIN times t ON l.time = t.time_id
        INNER JOIN inp_patient_registration pr 
            ON l.patient_id = pr.patient_id 
            AND l.application_id = pr.appointment_id
        WHERE l.status1 = '1' 
          AND pr.admission_status = 'Admitted'
        ORDER BY l.prescription_id DESC
    ";
} else {
    $query = "
        SELECT 
            l.*, 
            p.prescribed_on,
            d.dosages,
            ip.intake_name,
            t.time AS times
        FROM inp_prescription_list l
        LEFT JOIN inp_prescription p ON l.prescription_id = p.prescription_id
        LEFT JOIN dosage d ON l.dosage = d.dosage_id
        LEFT JOIN in_take_period ip ON l.intake_period = ip.intake_id
        LEFT JOIN times t ON l.time = t.time_id
        INNER JOIN inp_patient_registration pr 
            ON l.patient_id = pr.patient_id 
            AND l.application_id = pr.appointment_id
        WHERE l.status1 = '1' 
          AND l.org_id = '$SessionOrgId'
          AND pr.admission_status = 'Admitted'
        ORDER BY l.prescription_id DESC
    ";
}

$result = mysqli_query($conn, $query);
?>

<div class="table-responsive" style="overflow-x: auto;">
<table class="table" id="tableExportP" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <th>Actions</th>
            <th>Patient ID</th>
            <th>Application No</th>
            <th>Medicine Name</th>
            <th>Medicine Price</th>
            <th>Dosage</th>
            <th>Unit</th>
            <th>Intake Period</th>
            <th>Time</th>
            <th>Duration (Days)</th>
            <th>Route</th>
            <th>Medicine Status</th>
            <th>Instructions</th>
            <th>Prescription Date</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $i++ ?></td>
            <td>
                <a class="has-icon" style="cursor:pointer;margin-right: 20px;" 
                    onclick="editMedicine(
                        '<?= $row['prescription_id'] ?>',
                        '<?= $row['id'] ?>',
                        '<?= htmlspecialchars($row['patient_id'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['application_id'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['medicine_name'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['price'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['dosage'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['unit'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['intake_period'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['time'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['duration_days'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['route'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['medicine_status'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['instructions'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['prescribed_on'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['org_id'], ENT_QUOTES) ?>'
                    )">
                    <i class="fa fa-edit fa-lg"></i>
                </a>
                <a class="has-icon text-danger" style="cursor:pointer;" 
                    onclick="deleteMedicine('<?= $row['id'] ?>')">
                    <i class="fa fa-trash fa-lg"></i>
                </a>
            </td>
            <td><?= htmlspecialchars($row['patient_id']) ?></td>
            <td><?= htmlspecialchars($row['application_id']) ?></td>
            <td><?= htmlspecialchars($row['medicine_name']) ?></td>
            <td><?= htmlspecialchars($row['price']) ?></td>
            <td><?= htmlspecialchars($row['dosages']) ?></td>  
            <td><?= htmlspecialchars($row['unit']) ?></td>
            <td><?= htmlspecialchars($row['intake_name']) ?></td>  
            <td><?= htmlspecialchars($row['times']) ?></td>
            <td><?= htmlspecialchars($row['duration_days']) ?></td>
            <td><?= htmlspecialchars($row['route']) ?></td>
            <td><?= htmlspecialchars($row['medicine_status']) ?></td>
            <td><?= htmlspecialchars($row['instructions']) ?></td>
            <td><?= htmlspecialchars($row['prescribed_on']) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>