<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
$startDate = $_POST['startdate'] ?? '';
$endDate = $_POST['enddate'] ?? '';
$orgid = $_POST['org_id'];

if (empty($startDate) && empty($endDate)) {
    $today = date('Y-m-d');
    $dateCondition = "appoint_date = '$today'";
} elseif (!empty($startDate) && empty($endDate)) {
    $dateCondition = "appoint_date >= '$startDate'";
} elseif (!empty($startDate) && !empty($endDate)) {
    $dateCondition = "appoint_date BETWEEN '$startDate' AND '$endDate'";
} else {
    $dateCondition = "1";
}

$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

$doctorIds = [];

// FIX_B_272: initialise $doctorRes so unknown security_type rows do not
// hit `mysqli_fetch_assoc(null)` (TypeError → HTTP 500).
$doctorRes = false;

// SA_FATAL_FIXED_B_272: include SA so super-admin sees all doctors
if ($securityType === 'A' || $securityType === 'SA') {
    $doctorRes = mysqli_query($conn, "SELECT doc_id FROM doctors WHERE status='1'");
} elseif ($securityType === 'U') {
    $doctorRes = mysqli_query($conn, "SELECT d.doc_id
        FROM doctors d
        WHERE d.status = '1'
        AND (d.security_id = '$SessionUserId' 
             OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id = '$SessionUserId'))");
}

// FIX_B_272: only iterate when one of the two branches actually queried.
if ($doctorRes) {
    while ($row = mysqli_fetch_assoc($doctorRes)) {
        $doctorIds[] = $row['doc_id'];
    }
}

$appointments = [];
if (!empty($doctorIds)) {
    $doctorIdsStr = implode(',', $doctorIds);
    // FIX_B_1903: explicit doctor-scope filter (defense-in-depth alongside doctor_name IN above)
    $docScope = currentDoctorScopeSql('a.doctor_name');
    $sql = "SELECT a.*, d.doctor_name, p.reviewafterdate
            FROM appointment_online a
            JOIN doctors d ON d.doc_id = a.doctor_name
            LEFT JOIN prescripition p ON p.appoint_register_id = a.appoint_register_id
                AND p.org_id = a.org_id AND p.status='1'
            WHERE a.appoint_status='1'
            AND a.doctor_name IN ($doctorIdsStr)
            AND $dateCondition
            $docScope";

    if ($orgid) {
        $sql .= " AND a.org_id='$orgid'";
    }

    $sql .= " ORDER BY a.appoint_id DESC";

    $resAppointments = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));
    while ($row = mysqli_fetch_object($resAppointments)) {
        $appointments[] = $row;
    }
}
?>

<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <?php if ($SessionUserId == "1") { ?><th>Organization</th><?php } ?>
            <th>Bill PDF & Print</th>
            <th>Appointment Id</th>
            <th>Registration Id</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Mobile</th>
            <th>Date</th>
            <th>Doctor Name</th>
            <th>Start & End Time</th>
            <th>Review After</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php $i = 1; foreach ($appointments as $resAppointmentData): ?>
        <tr>
            <td><?= $i++; ?></td>
            <td class="text-center">
                <ul class="navbar-nav">
                    <li class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                            <i class="fas fa-paste" style="font-size: 24px;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-center" style="width: auto; min-width: 165px;">
                            <a href="billview.php?ItemId=<?= $resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item">
                                <i class="far fa-file-pdf"></i> Bill PDF
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="billPrint.php?ItemId=<?= $resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item">
                                <i class="far fa-file-powerpoint"></i> Bill Print
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="combinedBill.php?appoint_id=<?= $resAppointmentData->appoint_id ?>" target="_blank" class="dropdown-item fw-semibold">
                                <i class="fas fa-file-invoice"></i> Combined Bill
                            </a>
                        </div>
                    </li>
                </ul>
            </td>
            <?php if ($SessionUserId == "1"): ?>
                <td><?= getUserNameByOrgId($conn, $resAppointmentData->org_id) ?></td>
            <?php endif; ?>
            <td><?= $resAppointmentData->appoint_register_id ?></td>
            <td><?= $resAppointmentData->appoint_unicode ?></td>
            <td><?= $resAppointmentData->patient_name ?></td>
            <td><?= $resAppointmentData->gender ?></td>
            <td><?= $resAppointmentData->age ?></td>
            <td><?= $resAppointmentData->mobile_number ?></td>
            <td><?= $resAppointmentData->appoint_date ?></td>
            <td><?= $resAppointmentData->doctor_name ?></td>
            <td><?= $resAppointmentData->start_time ?> / <?= $resAppointmentData->end_time ?></td>
            <td><?= htmlspecialchars($resAppointmentData->reviewafterdate ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>