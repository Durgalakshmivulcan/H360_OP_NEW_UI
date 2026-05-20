<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// ------------------------------------------------------
// Step 0: Check if admin
// ------------------------------------------------------
$isAdmin = false;
$qrySec = mysqli_query($conn, "SELECT security_type FROM security WHERE security_id='$SessionUserId' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
if ($row = mysqli_fetch_assoc($qrySec)) {
    // SA_FATAL_FIXED_B_555: SA also gets admin scope
    if ($row['security_type'] === 'A' || $row['security_type'] === 'SA') $isAdmin = true;
}

// ------------------------------------------------------
// Step 1: Determine allowed doc_ids for current user
// ------------------------------------------------------
$allowed_doc_ids = [];

if (!$isAdmin) {
    // Receptionist: get assigned doctors
    $qryRec = mysqli_query($conn, "SELECT doc_id FROM receptionnist WHERE security_id='$SessionUserId' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
    while ($r = mysqli_fetch_assoc($qryRec)) {
        $allowed_doc_ids[] = $r['doc_id'];
    }

    // If not receptionist, check if doctor
    if (empty($allowed_doc_ids)) {
        $qryDoc = mysqli_query($conn, "SELECT doc_id FROM doctors WHERE security_id='$SessionUserId' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        if ($r = mysqli_fetch_assoc($qryDoc)) {
            $docId = $r['doc_id'];
            $allowed_doc_ids[] = $docId;
        }
    }
}

// ------------------------------------------------------
// Step 2: Build WHERE clause for doctors_time_slot
// ------------------------------------------------------
$where = "WHERE status='1'";

// Apply doctor filter for non-admin users
if (!$isAdmin && !empty($allowed_doc_ids)) {
    $in = implode(",", $allowed_doc_ids);
    $where .= " AND doctorName_registrationNumber IN ($in)";
}

// No allowed doctors → show nothing
if (!$isAdmin && empty($allowed_doc_ids)) {
    $where .= " AND 1=0";
}

// Non-admin: filter by org
if (!$isAdmin) {
    $where .= " AND org_id='$SessionOrgId'";
}

// FIX_B_1903: explicit doctor-scope filter on doctorName_registrationNumber (defense-in-depth)
$where .= currentDoctorScopeSql('doctorName_registrationNumber');

// ------------------------------------------------------
// Step 3: Fetch doctors_time_slot
// ------------------------------------------------------
$sql = "SELECT * FROM doctors_time_slot $where ORDER BY doctors_time_id DESC";
$getdoctor = mysqli_query($conn, $sql) or die(mysqli_error($conn));
?>

<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th> S.No </th>
            <th> Type </th>
            <?php if($SessionUserId =="1"){ ?>
                <th> Organization Name</th>
            <?php } ?>
            <th> Name</th>
            <th> Doctors Available Date </th> 
            <th> Action </th>
        </tr>
    </thead>
    <tbody style="text-align:center">
        <?php
        $i = 1;
        while($resdoctor = mysqli_fetch_object($getdoctor)){
            $type = ($resdoctor->doctortime_type == 'Daily') ? 'Per-Day' : 'Range';

            $doc_id = $resdoctor->doctorName_registrationNumber;
            $qry = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$doc_id'") or die(mysqli_error($conn));
            $res = mysqli_fetch_object($qry);

            $qrymulti = mysqli_query($conn,"SELECT * FROM multi_doctortimeslots WHERE status='1' AND org_id ='$SessionOrgId' AND multi_id ='$resdoctor->multi_id' ") or die(mysqli_error($conn));
            $resmulti = mysqli_fetch_object($qrymulti);
        ?>
        <tr>
            <td> <?=$i++;?> </td>
            <td> <?= $type ?> </td>
            <?php if($SessionUserId=="1"){ ?>
                <td> <?=getUserNameByOrgId($conn, $resdoctor->org_id)?> </td>
            <?php } ?>
            <td> <?=$res->doctor_name?>-<?=$res->doc_registration_number?> </td>
            <td> <?=$resdoctor->available_date?> </td>
            <td style="width: 100px;">
                <a class="has-icon text-dark me-3" data-bs-toggle="modal" data-bs-target="#staticBackdrop" style="cursor:pointer;" 
                    onclick="viewdoctor(`<?=$resdoctor->doctors_time_id?>`,
                                        `<?=$res->doctor_name?>-<?=$res->doc_registration_number?>`, 
                                        `<?=$resdoctor->available_date ?>`,
                                        `<?=$resdoctor->starting_Time?>`,
                                        `<?=$resdoctor->ending_Time?>`)">
                    <i class="fas fa-eye fa-lg"></i>
                </a>

                <?php if ($resdoctor->doctortime_type != "Range") { ?>
                    <a class="has-icon me-3" href="#" style="cursor:pointer;" 
                        onclick='editdoctor(`<?=$resdoctor->doctors_time_id?>`,
                                            `<?=$resdoctor->doctorName_registrationNumber?>`, 
                                            `<?=$resdoctor->available_date ?>`,
                                            `<?=$resdoctor->starting_Time?>`,
                                            `<?=$resdoctor->ending_Time?>`,
                                            `<?=$resdoctor->org_id?>`)'>
                        <i class="fa fa-edit fa-lg"></i>
                    </a>
                <?php } ?>

                <a class="has-icon text-danger" href="#" style="cursor:pointer;" 
                    onclick="deletedoctor('<?=$resdoctor->doctors_time_id?>',
                                        '<?=$resmulti->multi_id?>',
                                        '<?=$resdoctor->doctorName_registrationNumber?>')">
                    <i class="fa fa-trash fa-lg"></i>
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>                            
</table>
