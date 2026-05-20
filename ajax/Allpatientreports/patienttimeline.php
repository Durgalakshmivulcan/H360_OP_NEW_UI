<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';


$patientUid = isset($_GET['patient_uid']) ? $_GET['patient_uid'] : '';
$orgId = isset($_GET['org_id']) && !empty($_GET['org_id']) ? $_GET['org_id'] : $SessionOrgId;
// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
$fetchAppointUnicode = mysqli_query($conn, "SELECT appoint_unicode FROM appointment_online WHERE appoint_id = '$patientUid' AND appoint_status = '1' AND org_id = '$orgId' $docScope");

$appointUnicode = '';
if ($row = mysqli_fetch_assoc($fetchAppointUnicode)) {
    $appointUnicode = $row['appoint_unicode'];
}

$fetchPatientDetails = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_unicode = '$appointUnicode' AND appoint_status = '1' AND org_id = '$orgId' $docScope ORDER BY appoint_id DESC");

$records = [];

while ($row = mysqli_fetch_array($fetchPatientDetails)) {
    $records[] = $row;
}

?>
<?php
$icons = [
    'fas fa-comment-alt',
    'fas fa-stethoscope',
    'fas fa-notes-medical',
    'fas fa-user-md',
    'fas fa-briefcase-medical',
    'fas fa-calendar-check'
];
?>


<div class="section-body">
    <?php $patientName = isset($records[0]['patient_name']) ? $records[0]['patient_name'] : ''; ?>
    <h2 class="section-title"><?= $patientName ?></h2>
    <div class="row">
        <div class="col-12">
            <div class="activities">
                <?php
                $fetchTests = mysqli_query($conn, "
                                    SELECT COUNT(DISTINCT pth.patient_history_id) AS total_tests, pth.patient_id
                                        FROM patient_tests_history AS pth
                                        LEFT JOIN appointment_online AS ao 
                                            ON pth.patient_id = ao.appoint_unicode
                                        WHERE ao.appoint_unicode = '$appointUnicode'
                                        AND pth.performed_at = 'Outside the Hospital'
                                        AND pth.org_id = '$orgId';

                                ");

                $totalTests = 0;
                if ($fetchTests && mysqli_num_rows($fetchTests) > 0) {
                    $row = mysqli_fetch_assoc($fetchTests);
                    $totalTests = $row['total_tests'];
                    $patient_id = $row['patient_id'];
                }
                ?>

                <div class="activity">
                    <div class="activity-icon bg-primary text-white">
                        <!-- <i class="fas fa-vials"></i>
                          -->
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="activity-detail">
                        <div class="d-flex align-items-center">
                            <span>
                                <strong>Old Reports:</strong> <?= $totalTests ?> record<?= $totalTests != 1 ? 's' : '' ?>
                            </span>
                            <!-- <a href="outside_tests.php?patient_id=<?= urlencode($appointUnicode) ?>&org_id=<?= urlencode($orgId) ?>" 
                                        class="btn btn-primary btn-sm ms-3">View</a> -->
                            <div class="ps-4 float-right">
                                <span class="bullet" style="color: #6777ef"></span>
                                <a href="javascript:void(0);"
                                    class="text-job view-old-prescription"
                                    data-ids="<?= $patient_id ?>"
                                    style="color: #6777ef">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (count($records) > 0): ?>
                    <?php foreach ($records as $index => $record): ?>

                        <?php
                        $appointRegId = $record['appoint_register_id'];
                        $prescriptionQuery = mysqli_query($conn, "SELECT * FROM prescripition WHERE appoint_register_id = '$appointRegId' ORDER BY prescription_id DESC");

                        $prescriptions = [];
                        while ($row = mysqli_fetch_assoc($prescriptionQuery)) {
                            $prescriptions[] = $row;
                        }

                        $displayHeading = '';
                        $displayText = '';

                        if (!empty($prescriptions)) {
                            $firstPrescription = $prescriptions[0];

                            if (!empty($firstPrescription['finalDiagnosis'])) {
                                $displayHeading = 'Final Diagnosis';
                                $displayText = $firstPrescription['finalDiagnosis'];
                            } elseif (!empty($firstPrescription['chiefComplaint'])) {
                                $displayHeading = 'Chief Complaint';
                                $displayText = $firstPrescription['chiefComplaint'];
                            } elseif (!empty($firstPrescription['pastHistory'])) {
                                $displayHeading = 'Past History';
                                $displayText = $firstPrescription['pastHistory'];
                            } else {
                                $displayHeading = 'Details';
                                $displayText = 'No data available';
                            }
                        } else {
                            $displayHeading = 'Prescription';
                            $displayText = 'No prescription given';
                        }

                        // Prepare prescription IDs
                        $prescriptionIds = '';
                        if (count($prescriptions) === 1) {
                            $prescriptionIds = $prescriptions[0]['prescription_id'];
                        } elseif (count($prescriptions) > 1) {
                            $prescriptionIds = implode(',', array_column($prescriptions, 'prescription_id'));
                        }

                        // Gynaec prescriptions for this appointment
                        $gynaecQuery = mysqli_query($conn, "SELECT gynaec_rx_id, final_diagnosis, chief_complaints, rx_date FROM gynaec_prescriptions WHERE appointment_id = '$appointRegId' AND org_id = '$orgId' AND status='1' ORDER BY gynaec_rx_id DESC");
                        $gynaecRxList = [];
                        while ($gr = mysqli_fetch_assoc($gynaecQuery)) {
                            $gynaecRxList[] = $gr;
                        }
                        ?>
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <i class="<?= $icons[$index % count($icons)] ?>"></i>
                            </div>
                            <div class="activity-detail">
                                <div class="mb-2">
                                    <span class="text-job">
                                        <?= time_elapsed_string($record['create_date_time']) ?> (<?= date("d M Y", strtotime($record['create_date_time'])) ?>)
                                    </span>

                                    <?php if (!empty($prescriptionIds)): ?>
                                        <div class="ps-4 float-right">
                                            <span class="bullet" style="color: #6777ef"></span>
                                            <a href="javascript:void(0);"
                                                class="text-job view-prescription"
                                                data-ids="<?= htmlspecialchars($prescriptionIds) ?>"
                                                style="color: #6777ef">View</a>
                                        </div>
                                    <?php endif; ?>

                                    <?php foreach ($gynaecRxList as $gr): ?>
                                        <div class="ps-4 float-right ms-2">
                                            <span class="badge bg-danger" style="font-size:0.75em;">Gynaec</span>
                                            <a href="javascript:void(0);"
                                                class="text-job view-gynaec-prescription"
                                                data-id="<?= (int)$gr['gynaec_rx_id'] ?>"
                                                style="color: #dc3545;">View</a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p>
                                    <?= $displayHeading ?> :
                                    <?= $displayText ?>
                                </p>
                                <?php foreach ($gynaecRxList as $gr): ?>
                                <p style="margin-top:4px;">
                                    <span class="badge bg-danger">Gynaec</span>
                                    <?php
                                    if (!empty($gr['final_diagnosis'])) {
                                        echo 'Final Diagnosis : ' . htmlspecialchars($gr['final_diagnosis']);
                                    } elseif (!empty($gr['chief_complaints'])) {
                                        echo 'Chief Complaints : ' . htmlspecialchars($gr['chief_complaints']);
                                    } else {
                                        echo 'Gynaec Prescription on ' . htmlspecialchars($gr['rx_date'] ?? '');
                                    }
                                    ?>
                                </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="activity">
                        <div class="activity-icon bg-warning text-white">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-detail">
                            <p>No appointments found.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                function time_elapsed_string($datetime, $full = false)
                {
                    $now = new DateTime;
                    $ago = new DateTime($datetime);
                    $diff = $now->diff($ago);

                    $diff->w = floor($diff->d / 7);
                    $diff->d -= $diff->w * 7;

                    $string = [
                        'y' => 'year',
                        'm' => 'month',
                        'w' => 'week',
                        'd' => 'day',
                        'h' => 'hour',
                        'i' => 'minute',
                        's' => 'second',
                    ];
                    foreach ($string as $k => &$v) {
                        if ($diff->$k) {
                            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                        } else {
                            unset($string[$k]);
                        }
                    }

                    if (!$full) $string = array_slice($string, 0, 1);
                    return $string ? implode(', ', $string) . ' ago' : 'just now';
                }
                ?>


                <script>
                    $('.view-prescription').on('click', function() {
                        let ids = $(this).data('ids');
                        viewprescription(ids);
                    });

                    $('.view-gynaec-prescription').on('click', function() {
                        let id = $(this).data('id');
                        viewgynaecprescription(id);
                    });

                    $('.view-old-prescription').on('click', function() {
                        let ids = $(this).data('ids');
                        viewoldreports(ids);
                    });


                    //     $(document).on('click', '.view-oldreports', function(){
                    //     let ids = $(this).data('ids');
                    //     viewoldreports(ids);
                    // });
                </script>