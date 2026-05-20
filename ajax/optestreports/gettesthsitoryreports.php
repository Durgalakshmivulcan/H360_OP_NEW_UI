<?php
require_once "../../config/functions.php";

$SessionUserId = $_SESSION["security_id"];
$SessionOrgId  = $_SESSION["org_id"];

// Get security type and allowed doctors
$checkDoctor   = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType  = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// SA_FATAL_FIXED_B_559: include SA so $sql is defined for super-admin
if ($securityType === 'A' || $securityType === 'SA') {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status = '1'
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

$allowedDoctorIds = array_column($doctors, 'doc_id');
$doctorCondition  = !empty($allowedDoctorIds)
    ? "AND ao.doctor_name IN ('" . implode("','", $allowedDoctorIds) . "')"
    : "AND 0"; 
?>

<div class="table-responsive">
    <table class="table table-bordered" id="tableExport1">
        <thead class="text-center">
            <tr>
                <th>S.No</th>
                <?php if($SessionUserId=="1") echo "<th>Organization</th>"; ?>
                <th>Patient ID</th>
                <th>Appointment ID</th>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Performed At</th>
                <th>Type</th> 
                <th>Test Name</th>
                <th>Uploaded Date</th>
                <th>File</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $addOrgId = ($SessionUserId != "1") ? " AND t.org_id='$SessionOrgId'" : "";

        // Fetch only "Within the Hospital"
        $performedAtCondition = " AND t.performed_at = 'Outside the Hospital'";

        // FIX_B_1903: doctor-scope filter (defense-in-depth alongside doctorCondition)
        $docScope = currentDoctorScopeSql('ao.doctor_name');
        $getReports = mysqli_query($conn,"
            SELECT t.*, tm.test_name AS master_name
            FROM patient_tests_history t
            LEFT JOIN tests tm ON t.test_name = tm.test_id
            LEFT JOIN appointment_online ao ON t.appointment_id = ao.appoint_register_id
            WHERE t.status='1' $addOrgId $doctorCondition $performedAtCondition $docScope
            ORDER BY t.patient_history_id DESC
        ");

        $i = 1;
        while($r = mysqli_fetch_object($getReports)){
            $fileType = !empty($r->file_type) ? $r->file_type : "-";

            if ($fileType === "Prescription") {
                $displayName = "-";
            } else {
                $displayName = !empty($r->master_name) ? $r->master_name : $r->test_name;
            }
            ?>
            <tr class="text-center">
                <td><?= $i++ ?></td>
                <?php if($SessionUserId=="1") echo "<td>".getUserNameByOrgId($conn,$r->org_id)."</td>"; ?>
                <td><?= htmlspecialchars($r->patient_id) ?></td>
                <td><?= htmlspecialchars($r->appointment_id) ?></td>
                <td><?= htmlspecialchars($r->doctor_name) ?></td>
                <td><?= htmlspecialchars($r->specialization) ?></td>
                <td><?= htmlspecialchars($r->performed_at) ?></td>
                <td><?= htmlspecialchars($r->file_type) ?></td>
                <td><?= htmlspecialchars($displayName) ?></td>
                <td><?= htmlspecialchars($r->uploaded_date) ?></td>
                <td>
                    <?php if(!empty($r->file_url)){
                        foreach(explode(",",$r->file_url) as $f){
                            echo "<a href='ajax/$f' target='_blank'>View</a><br>";
                        }
                    } ?>
                </td>
                <td><?= htmlspecialchars($r->observations) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
