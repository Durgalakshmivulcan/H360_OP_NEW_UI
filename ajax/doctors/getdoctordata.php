<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
ensureUserCodeColumn($conn);
?>
<table class="table" id="tableExport1">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <?php if ($SessionUserId == "1") { ?>
                <th>Organization Name</th>
            <?php } ?>
            <th>Registration Number</th>
            <th>Doctor Name</th>
            <th>User Code</th>
            <th>Doctor Type</th>
            <th>Gender</th>
            <th>Phone Number</th>
            <th>Doctor Email</th>
            <th>Departments</th>
            <th>Doctor Specialization</th>
            <th>Services</th>
            <th>Doctor Fee</th>
            <th>Receptionist User</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php
        if ($SessionUserId == "1" && $SessionRoleId == "1") {
            $getdoctor = mysqli_query($conn,
                "SELECT d.*,
                    GROUP_CONCAT(r.user_name ORDER BY r.rep_id SEPARATOR ', ') AS rep_user_name,
                    GROUP_CONCAT(r.security_id ORDER BY r.rep_id) AS rep_security_id
                 FROM doctors d
                 LEFT JOIN receptionnist r ON d.doc_id = r.doc_id AND r.status='1'
                 WHERE d.status='1'
                 GROUP BY d.doc_id
                 ORDER BY d.doc_id DESC"
            ) or die(mysqli_error($conn));
        } else {
            $getdoctor = mysqli_query($conn,
                "SELECT d.*,
                    GROUP_CONCAT(r.user_name ORDER BY r.rep_id SEPARATOR ', ') AS rep_user_name,
                    GROUP_CONCAT(r.security_id ORDER BY r.rep_id) AS rep_security_id
                 FROM doctors d
                 LEFT JOIN receptionnist r ON d.doc_id = r.doc_id AND r.status='1'
                 WHERE d.status='1' AND d.org_id='$SessionOrgId'
                 GROUP BY d.doc_id
                 ORDER BY d.doc_id DESC"
            ) or die(mysqli_error($conn));
        }

        $i = 1;
        while ($resdoctor = mysqli_fetch_object($getdoctor)) {
            // Services
            $all_service_names = '';
            if (!empty($resdoctor->doctor_services)) {
                $ids = array_map('trim', explode(',', $resdoctor->doctor_services));
                $ids = array_filter($ids, 'is_numeric');
                if (!empty($ids)) {
                    $ids_str = implode(',', $ids);
                    $sql = "SELECT service_name FROM services WHERE service_id IN ($ids_str)";
                    $resServices = mysqli_query($conn, $sql);
                    $names = [];
                    while ($row = mysqli_fetch_object($resServices)) {
                        $names[] = $row->service_name;
                    }
                    $all_service_names = implode(', ', $names);
                }
            }

            // Specialization
            $all_specialtisname = '';
            if (!empty($resdoctor->doctor_specialization)) {
                $idss = array_map('trim', explode(',', $resdoctor->doctor_specialization));
                $idss = array_filter($idss, 'is_numeric');
                if (!empty($idss)) {
                    $ids_strsp = implode(',', $idss);
                    $sql2 = "SELECT specialtisname FROM specialtis WHERE specialtis_id IN ($ids_strsp)";
                    $resSpecs = mysqli_query($conn, $sql2);
                    $names = [];
                    while ($row2 = mysqli_fetch_object($resSpecs)) {
                        $names[] = $row2->specialtisname;
                    }
                    $all_specialtisname = implode(', ', $names);
                }
            }
        ?>
        <tr>
            <td><?= $i++; ?></td>
            <?php if ($SessionUserId == "1") { ?>
                <td><?= getUserNameByOrgId($conn, $resdoctor->org_id) ?></td>
            <?php } ?>
            <td><?= $resdoctor->doc_registration_number ?></td>
            <td><?= $resdoctor->doctor_name ?></td>
            <td><?php
                $ucQ = mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='" . (int)$resdoctor->security_id . "' LIMIT 1");
                $ucRow = $ucQ ? mysqli_fetch_assoc($ucQ) : null;
                $uc = $ucRow['user_code'] ?? '';
                echo $uc ? '<span style="background:#0d6efd;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;">' . htmlspecialchars($uc) . '</span>' : '-';
            ?></td>
            <td><?= $resdoctor->doctor_type ?></td>
            <td><?= $resdoctor->gender ?></td>
            <td><?= $resdoctor->phone_number ?></td>
            <td><?= $resdoctor->email ?></td>
            <td><?= getDepartmentById($conn, $resdoctor->departments) ?></td>
            <td><?= $all_specialtisname ?></td>
            <td><?= $all_service_names ?></td>
            <td><?= $resdoctor->doctor_fee ?></td>
            <td><?= $resdoctor->rep_user_name ?? '-' ?></td>
            <td class="text-center">
                <?php if (userCan('edit', 'doctor.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;"
                   onclick='editdoctor(`<?= $resdoctor->doc_id ?>`,
                                      `<?= $resdoctor->doc_registration_number ?>`,
                                      `<?= $resdoctor->security_id ?>`,
                                      `<?= $resdoctor->doctor_name ?>`,
                                      `<?= $resdoctor->doctor_type ?>`,
                                      `<?= $resdoctor->gender ?>`,
                                      `<?= $resdoctor->phone_number ?>`,
                                      `<?= $resdoctor->email ?>`,
                                      `<?= $resdoctor->departments ?>`,
                                      `<?= $resdoctor->doctor_specialization ?>`,
                                      `<?= $resdoctor->doctor_services ?>`,
                                      `<?= $resdoctor->doctor_fee ?>`,
                                      `<?= $resdoctor->doctor_charge ?>`,
                                      `<?= $resdoctor->doctor_visit_charge ?>`,
                                      `<?= $resdoctor->time_slot_duration ?>`,
                                      `<?= $resdoctor->details ?>`,
                                      `<?= $resdoctor->org_id ?>`,
                                      `<?= $resdoctor->security_id ?>`,
                                      `<?= $resdoctor->rep_security_id ?>`
                                      )'>
                    <i class="fa fa-edit fa-lg"></i>
                </a><?php } ?>
                <?php if (userCan('delete', 'doctor.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;"
                   onclick="deletedoctor('<?= $resdoctor->doc_id ?>', '<?= $resdoctor->doctor_name ?>')">
                    <i class="fa fa-trash fa-lg"></i>
                </a><?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
