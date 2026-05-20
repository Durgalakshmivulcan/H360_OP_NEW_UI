<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId  = $_SESSION['org_id'];

$startDate = $_POST['startdate'] ?? '';
$endDate   = $_POST['enddate']   ?? '';
$orgid     = $_POST['org_id']    ?? '';

// --- Date condition for regular prescriptions ---
if (empty($startDate) && empty($endDate)) {
    $today = date('Y-m-d');
    $dateConditionP = "p.prescriptiondate = '$today'";
    $dateConditionG = "g.rx_date = '$today'";
} elseif (!empty($startDate) && empty($endDate)) {
    $dateConditionP = "p.prescriptiondate >= '$startDate'";
    $dateConditionG = "g.rx_date >= '$startDate'";
} elseif (!empty($startDate) && !empty($endDate)) {
    $dateConditionP = "p.prescriptiondate BETWEEN '$startDate' AND '$endDate'";
    $dateConditionG = "g.rx_date BETWEEN '$startDate' AND '$endDate'";
} else {
    $dateConditionP = "1";
    $dateConditionG = "1";
}

// --- Security type ---
$checkDoctor  = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

$allowedDoctorIds = [];
// SA_FATAL_FIXED_B_548: include SA so $doctorRes is defined for super-admin
if ($securityType === 'A' || $securityType === 'SA') {
    $doctorRes = mysqli_query($conn, "SELECT doc_id FROM doctors WHERE status='1'");
} elseif ($securityType === 'U') {
    $doctorRes = mysqli_query($conn,
        "SELECT d.doc_id FROM doctors d WHERE d.status='1'
         AND (d.security_id='$SessionUserId'
              OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id='$SessionUserId'))");
}
if (isset($doctorRes)) {
    while ($row = mysqli_fetch_assoc($doctorRes)) $allowedDoctorIds[] = $row['doc_id'];
}

if (!empty($allowedDoctorIds)) {
    $quotedIds     = array_map(fn($id) => "'$id'", $allowedDoctorIds);
    $doctorCond    = "AND a.doctor_name IN (" . implode(',', $quotedIds) . ")";
} else {
    $doctorCond = "AND 0";
}

$orgFilter  = $orgid ? "AND p.org_id='$orgid'" : '';
$orgFilterG = $orgid ? "AND g.org_id='$orgid'" : '';

// FIX_B_1903: explicit doctor-scope filter (defense-in-depth alongside doctorCond above).
// For gynaec we join via appointment_online to get a doctor_name to filter on.
$docScopeP = currentDoctorScopeSql('a.doctor_name');
$docScopeG = currentDoctorScopeSql('ao.doctor_name');

// ── 1. Regular prescriptions ──────────────────────────────────────────────
$queryP = "SELECT p.prescription_id AS rx_id, p.patient_name, p.age, p.gender,
                  p.appoint_register_id AS appoint_register_id, p.patient_uid AS patient_uid,
                  p.prescriptiondate AS rx_date, p.org_id, a.mobile_number AS mobile,
                  p.reviewafterdate AS review_after,
                  'Regular' AS rx_type
           FROM prescripition p
           LEFT JOIN appointment_online a
                  ON p.appoint_register_id = a.appoint_register_id AND p.org_id = a.org_id
           WHERE p.status='1' AND $dateConditionP $doctorCond $orgFilter $docScopeP
           ORDER BY p.prescription_id DESC";

$getPrescriptions = mysqli_query($conn, $queryP) or die(mysqli_error($conn));

// ── 2. Gynaec prescriptions ───────────────────────────────────────────────
// No doctor join for gynaec (not linked to doctor table)
$queryG = "SELECT g.gynaec_rx_id AS rx_id, g.patient_name, g.age, g.gender,
                  g.appointment_id AS appoint_register_id, g.patient_id AS patient_uid,
                  g.rx_date, g.org_id, g.mobile,
                  g.reviewafterdate AS review_after,
                  'Gynaec' AS rx_type
           FROM gynaec_prescriptions g
           LEFT JOIN appointment_online ao ON ao.appoint_register_id = g.appointment_id
           WHERE g.status='1' AND $dateConditionG $orgFilterG $docScopeG
           ORDER BY g.gynaec_rx_id DESC";

$getGynaec = mysqli_query($conn, $queryG) or die(mysqli_error($conn));

// Merge rows
$allRows = [];
while ($r = mysqli_fetch_assoc($getPrescriptions)) $allRows[] = $r;
while ($r = mysqli_fetch_assoc($getGynaec))        $allRows[] = $r;

// Sort by rx_date DESC
usort($allRows, fn($a, $b) => strcmp($b['rx_date'], $a['rx_date']));
?>

<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <th>Type</th>
            <th>Actions</th>
            <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
            <th>Patient Name</th>
            <th>Appointment ID</th>
            <th>Patient UID</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Prescription Date</th>
            <th>Mobile Number</th>
            <th>Review After</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php $i = 1; foreach ($allRows as $row): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td>
                <?php if ($row['rx_type'] === 'Gynaec'): ?>
                    <span class="badge bg-danger">Gynaec</span>
                <?php else: ?>
                    <span class="badge bg-primary">Regular</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php if ($row['rx_type'] === 'Gynaec'): ?>
                    <ul class="navbar-nav">
                        <li class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black;">
                                <i class="fas fa-paste" style="font-size:24px;"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" style="cursor:pointer;"
                                   onclick="printGynaecRx(<?= (int)$row['rx_id'] ?>)">
                                    <i class="fa fa-print"></i> Print
                                </a>
                            </div>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav">
                        <li class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black;">
                                <i class="fas fa-paste" style="font-size:24px;"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" style="cursor:pointer;"
                                   onclick="printPrescription('<?= htmlspecialchars($row['rx_id']) ?>','<?= htmlspecialchars($row['org_id']) ?>','<?= htmlspecialchars($row['appoint_register_id']) ?>')">
                                    <i class="fa fa-print"></i> Print
                                </a>
                            </div>
                        </li>
                    </ul>
                <?php endif; ?>
            </td>
            <?php if ($SessionUserId == "1"): ?>
                <td><?= htmlspecialchars(getUserNameByOrgId($conn, $row['org_id'])) ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($row['patient_name']) ?></td>
            <td><?= htmlspecialchars($row['appoint_register_id'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['patient_uid'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['age'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['gender'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['rx_date'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['mobile'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['review_after'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($allRows)): ?>
            <tr><td colspan="<?= $SessionUserId == '1' ? 12 : 11 ?>" class="text-muted">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
/* Print gynaec prescription — opens viewGynaecRx via POST form */
function printGynaecRx(rxId) {
    var baseUrl = '<?= (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http')
        .'://'.$_SERVER['HTTP_HOST']
        .rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))),'/')
        .'/ajax/gynaec_prescription/viewGynaecRx.php' ?>';
    var f = document.createElement('form');
    f.method = 'POST'; f.action = baseUrl; f.target = '_blank';
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'gynaec_rx_id'; inp.value = rxId;
    f.appendChild(inp); document.body.appendChild(f); f.submit(); document.body.removeChild(f);
}
</script>
