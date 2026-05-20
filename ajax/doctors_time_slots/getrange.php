<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
?>

<table class="table" id="tableExport2" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <?php if ($SessionUserId == "1") { ?>
                <th>Organization Name</th>
            <?php } ?>
            <th>Name</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody style="text-align:center">

<?php
// ---------------------------
// 1) Get doctors linked to this user
// ---------------------------
$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// SA_FATAL_FIXED_B_552: SA should be treated like A (sees all doctors)
if ($securityType === 'A' || $securityType === 'SA') {
    // Admin/Super-Admin sees all doctors
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
} else {
    // Normal user: linked directly or via receptionist
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status='1'
            AND (
                d.security_id = '$SessionUserId'
                OR d.doc_id IN (
                    SELECT r.doc_id 
                    FROM receptionnist r 
                    WHERE r.security_id = '$SessionUserId'
                )
            )
            ORDER BY d.doctor_name ASC";
}

$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));
$doctors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $doctors[] = $row;
}

// If no doctors linked, skip fetching timeslots
$doctorIds = array_column($doctors, 'doc_id');
$doctorIdList = !empty($doctorIds) ? "'" . implode("','", $doctorIds) . "'" : "";

// FIX_B_1903: explicit doctor-scope filter on multi_doctortimeslots.doctorName_registrationNumber (defense-in-depth)
$docScope = currentDoctorScopeSql('multi_doctortimeslots.doctorName_registrationNumber');

if (!empty($doctorIds)) {
    // ---------------------------
    // 2) Fetch multi-doctor timeslots
    // ---------------------------
    if ($SessionUserId == "1") {
        $getmultidoctor = mysqli_query($conn, "SELECT DISTINCT multi_doctortimeslots.*
            FROM multi_doctortimeslots
            INNER JOIN doctors_time_slot
                ON multi_doctortimeslots.multi_id = doctors_time_slot.multi_id
            WHERE multi_doctortimeslots.status='1'
              AND multi_doctortimeslots.doctorName_registrationNumber IN ($doctorIdList)
              $docScope
            ORDER BY doctors_time_slot.doctors_time_id DESC") or die(mysqli_error($conn));
    } else {
        $getmultidoctor = mysqli_query($conn, "SELECT DISTINCT multi_doctortimeslots.*
            FROM multi_doctortimeslots
            INNER JOIN doctors_time_slot
                ON multi_doctortimeslots.multi_id = doctors_time_slot.multi_id
            WHERE multi_doctortimeslots.status='1'
              AND multi_doctortimeslots.org_id = '$SessionOrgId'
              AND doctors_time_slot.org_id = '$SessionOrgId'
              AND multi_doctortimeslots.doctorName_registrationNumber IN ($doctorIdList)
              $docScope
            ORDER BY doctors_time_slot.doctors_time_id DESC") or die(mysqli_error($conn));
    }
} else {
    $getmultidoctor = false;
}

// ---------------------------
// 3) Display table rows
// ---------------------------
$i = 1;
if ($getmultidoctor) {
    while($resmultidoctor = mysqli_fetch_object($getmultidoctor)){
        $doc_id    = $resmultidoctor->doctorName_registrationNumber;
        $multi_id1 = $resmultidoctor->multi_id;

        // Get doctor info
        $qry = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$doc_id'") or die(mysqli_error($conn));
        $res = mysqli_fetch_object($qry);

        // Get multi timeslot info
        $qrymulti_time = mysqli_query($conn, "SELECT * FROM multi_doctortimeslots2 WHERE status='1' AND multi_id='$multi_id1'") or die(mysqli_error($conn));
        $resmulti = mysqli_fetch_object($qrymulti_time);
        ?>
        <tr>
            <td><?= $i++; ?></td>
            <?php if ($SessionUserId == "1") { ?>
                <td><?= getUserNameByOrgId($conn, $resmultidoctor->org_id) ?></td>
            <?php } ?>
            <td><?= $res->doctor_name ?> - <?= $res->doc_registration_number ?></td>
            <td><?= $resmultidoctor->from_date ?></td>
            <td><?= $resmultidoctor->to_date ?></td>
            <td style="width: 100px;">
                <a class="has-icon text-dark me-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop1" style="cursor:pointer"
                    onclick="viewmultidoctor(`<?= $resmultidoctor->multi_id ?>`,
                                            `<?= $res->doctor_name ?>-<?= $res->doc_registration_number ?>`,
                                            `<?= $resmultidoctor->from_date ?>`,
                                            `<?= $resmultidoctor->to_date ?>`,
                                            `<?= $resmulti->start_time ?>`,
                                            `<?= $resmulti->end_time ?>`)">
                    <i class="fas fa-eye fa-lg"></i>
                </a>

                <a href="#" class="has-icon me-3" style="cursor:pointer;"
                    onclick='editdoctorrange(`<?= $resmultidoctor->doctors_time_id ?>`,
                                             `<?= $resmultidoctor->available_date ?>`,
                                             `<?= $resmulti->multi_id ?>`,
                                             `<?= $resmultidoctor->doctorName_registrationNumber ?>`,
                                             `<?= $resmultidoctor->from_date ?>`,
                                             `<?= $resmultidoctor->to_date ?>`,
                                             `<?= $resmultidoctor->selectedDays ?>`,
                                             `<?= $resmulti->start_time ?>`,
                                             `<?= $resmulti->end_time ?>`,
                                             `<?= $resmultidoctor->org_id ?>`)'>
                    <i class="fa fa-edit fa-lg"></i>
                </a>

                <a class="has-icon text-danger" style="cursor:pointer"
                    onclick="deleterange('<?= $resmultidoctor->multi_id ?>',
                                         '<?= $resmultidoctor->doctorName_registrationNumber ?>')">
                    <i class="fa fa-trash fa-lg"></i>
                </a>
            </td>
        </tr>
    <?php
    }
}
?>
    </tbody>
</table>