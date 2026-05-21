<?php
    require_once("../../config/functions.php");

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $orgId = isset($_POST['org_id']) && !empty($_POST['org_id']) ? $_POST['org_id'] : $SessionOrgId;

if (isset($_POST['patient_uid']) && !empty($_POST['patient_uid'])) {
    $patientUid = mysqli_real_escape_string($conn, $_POST['patient_uid']);

// FIX_B_1903: doctor-scope filter via join to appointment_online.doctor_name
$docScope = currentDoctorScopeSql('ao.doctor_name');
$query = "SELECT p.* FROM prescripition p
          LEFT JOIN appointment_online ao ON ao.appoint_register_id = p.appoint_register_id
          WHERE p.patient_uid = '$patientUid'
            AND p.org_id = '$orgId'
            AND p.status = '1'
            $docScope
          ORDER BY p.create_date_time DESC";

$result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {

?>

<style>

    .prescription-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }
    .prescription-info-grid div {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
    }
    .prescription-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .prescription-table th, .prescription-table td {
        border: 1px solid #ccc;
        padding: 6px 10px;
        font-size: 14px;
        text-align: left;
    }

    .section-title {
        font-weight: 600;
        margin: 10px 0 5px;
    }

    .prescription-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 15px 20px;
        margin-bottom: 20px;
        background: #fff;
        position: relative;
        cursor: pointer;
    }
    .prescription-time {
        font-size: 14px;
        color: #6c757d;
        font-weight: 600;
    }
    .prescription-time span {
        font-weight: bold;
        color: #222;
    }
    .prescription-diagnosis {
        margin-top: 8px;
        font-size: 16px;
        font-weight: 500;
        color: #333;
    }
    .prescription-view {
        position: absolute;
        right: 20px;
        top: 20px;
    }
    .prescription-view a {
        color: #3b6ef7;
        text-decoration: none;
        font-weight: 600;
    }

    .prescription-details {
        display: none; 
        margin-top: 15px;
        border-top: 1px solid #ccc;
        padding-top: 15px;
    }

    .medicine-table-wrapper {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 10px;
    }

    .prescription-table th,
    .prescription-table td {
        border: 1px solid #ccc;
        padding: 6px 10px;
        font-size: 14px;
        text-align: left;
        white-space: normal;        
        word-break: break-word;    
        /* overflow-wrap: anywhere;     */
        max-width: 250px;          
    }

    @media (max-width: 768px) {
        .prescription-table th,
        .prescription-table td {
            white-space: normal;
            max-width: none;
        }
        .medicine-table-wrapper {
            overflow-x: auto;
        }
    }

    @media print {
        .medicine-table-wrapper {
            overflow: visible !important;
        }
        .prescription-table {
            page-break-inside: auto;
        }
        .prescription-table thead {
            display: table-header-group; 
        }
    }

</style>


    <?php
        while ($prescription = mysqli_fetch_assoc($result)) {
            $appointId = $prescription['appoint_register_id'];
            $patientUid = $prescription['patient_uid'];

            $appointmentQuery = mysqli_query($conn, 
                "SELECT * FROM appointment_online 
                 WHERE appoint_register_id = '$appointId' 
                   AND appoint_unicode = '$patientUid' 
                   AND org_id = '$orgId' 
                 LIMIT 1");
            $appointment = mysqli_fetch_assoc($appointmentQuery);

            $orginfo = mysqli_query($conn, 
                "SELECT organization_name FROM organization 
                WHERE org_id  = '$orgId'");
            $orgname = mysqli_fetch_assoc($orginfo);

            $getallprescripitionData = [];
            $getallprescripitiontestData = [];

            if (!empty($prescription['medicine_id'])) {
                $decodedMedicines = json_decode($prescription['medicine_id'], true);
                if (is_array($decodedMedicines)) $getallprescripitionData = $decodedMedicines;
            }

            if (!empty($prescription['test_id'])) {
                $decodedTests = json_decode($prescription['test_id'], true);
                if (is_array($decodedTests)) $getallprescripitiontestData = $decodedTests;
            }

            $hasMedicine = !empty($getallprescripitionData);
            $hasTest = !empty($getallprescripitiontestData);

            date_default_timezone_set('Asia/Kolkata'); 

            $prescriptionDate = new DateTime($prescription['create_date_time']);
            $now = new DateTime();
            $diff = $now->getTimestamp() - $prescriptionDate->getTimestamp();

            if ($diff < 60) $timeAgo = $diff . " seconds ago";
            elseif ($diff < 3600) $timeAgo = floor($diff / 60) . " minutes ago";
            elseif ($diff < 86400) $timeAgo = floor($diff / 3600) . " hours ago";
            elseif ($diff < 604800) $timeAgo = floor($diff / 86400) . " days ago";
            elseif ($diff < 2419200) $timeAgo = floor($diff / 604800) . " weeks ago";
            else $timeAgo = floor($diff / 2419200) . " months ago";

        ?>

            <?php
                $hasPresReports = mysqli_num_rows(mysqli_query($conn, 
                    "SELECT * FROM patient_tests_history 
                    WHERE patient_id = '$patientUid' 
                    AND appointment_id = '$appointId' 
                    AND file_type = 'Prescription' 
                    AND performed_at = 'Outside the Hospital'
                    AND org_id = '$orgId'
                    AND status = '1'")) > 0;

                $hasTestReports = mysqli_num_rows(mysqli_query($conn, 
                    "SELECT * FROM patient_tests_history 
                    WHERE patient_id = '$patientUid' 
                    AND appointment_id = '$appointId' 
                    AND file_type = 'Test' 
                    AND performed_at = 'Outside the Hospital'
                    AND org_id = '$orgId'
                    AND status = '1'")) > 0;

                if ($hasPresReports || $hasTestReports):
                ?>
                <div class="prescription-card bg-light shadow-sm p-3 rounded">
                    <h5 class="mb-3 d-flex justify-content-between align-items-center">
                        Old Reports
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#oldReportsCollapse">
                            VIEW
                        </button>
                    </h5>

                    <div class="collapse" id="oldReportsCollapse">
                        <?php
                        // Prescription Reports
                        $presReportsQuery = mysqli_query($conn, 
                            "SELECT * FROM patient_tests_history 
                            WHERE patient_id = '$patientUid' 
                            AND appointment_id = '$appointId' 
                            AND file_type = 'Prescription' 
                            AND performed_at = 'Outside the Hospital'
                            AND org_id = '$orgId'
                            AND status = '1'
                            ORDER BY uploaded_date ASC");

                        if (mysqli_num_rows($presReportsQuery) > 0) {
                            while ($report = mysqli_fetch_assoc($presReportsQuery)) {
                                $uploadedDate = !empty($report['uploaded_date']) ? date("d M Y", strtotime($report['uploaded_date'])) : 'N/A';
                                $doctorName = !empty($report['doctor_name']) ? ucfirst($report['doctor_name']) : 'N/A';
                                $specialization = !empty($report['specialization']) ? ucfirst($report['specialization']) : 'N/A';
                                $images = !empty($report['file_url']) ? explode(',', $report['file_url']) : [];
                        ?>
                                <div class="card mt-3">
                                    <h6 class="mt-3 p-2">Prescription</h6>
                                    <div class="card-body">
                                        <div><strong>Date:</strong> <?= $uploadedDate ?></div>
                                        <div><strong>Doctor:</strong> <?= $doctorName ?> </div>
                                        <div><strong>Specialization: <?= $specialization ?></strong></div>

                                        <?php
                                        if (!empty($images)) {
                                            foreach ($images as $img) {
                                                $filePath = "ajax/Testimages/" . basename($img);
                                                echo '<iframe src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:400px; object-fit:contain;"></iframe>';
                                            }
                                        } else {
                                            echo '<p>No images found.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                        <?php
                            }
                        }

                        $testReportsQuery = mysqli_query($conn, 
                            "SELECT * FROM patient_tests_history 
                            WHERE patient_id = '$patientUid' 
                            AND appointment_id = '$appointId' 
                            AND file_type = 'Test' 
                            AND performed_at = 'Outside the Hospital'
                            AND org_id = '$orgId'
                            AND status = '1'
                            ORDER BY uploaded_date ASC");

                        if (mysqli_num_rows($testReportsQuery) > 0) {
                            while ($report = mysqli_fetch_assoc($testReportsQuery)) {
                                $uploadedDate = !empty($report['uploaded_date']) ? date("d M Y", strtotime($report['uploaded_date'])) : 'N/A';
                                $doctorName = !empty($report['doctor_name']) ? ucfirst($report['doctor_name']) : 'N/A';
                                $specialization = !empty($report['specialization']) ? ucfirst($report['specialization']) : 'N/A';
                                $test_name = !empty($report['test_name']) ? ucfirst($report['test_name']) : 'N/A';
                                $images = !empty($report['file_url']) ? explode(',', $report['file_url']) : [];
                        ?>
                                <div class="card mt-3">
                                    <h6 class="mt-3 p-2">Tests Report</h6>
                                    <div class="card-body">
                                        <div><strong>Date:</strong> <?= $uploadedDate ?></div>
                                        <div><strong>Doctor:</strong> <?= $doctorName ?></div>
                                        <div><strong>Specialization : <?= $specialization ?> </strong></div>
                                        <div><strong>Test Name : <?= $test_name ?></strong></div>

                                        <?php
                                        if (!empty($images)) {
                                            foreach ($images as $img) {
                                                $filePath = "ajax/Testimages/" . basename($img);
                                                echo '<iframe src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:400px; object-fit:contain;"></iframe>';
                                            }
                                        } else {
                                            echo '<p>No images found.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="prescription-card">
                    <div class="prescription-time">
                        <?= $timeAgo ?> 
                        <span>(<?= date("d M Y", strtotime($prescription['create_date_time'])) ?>)</span>
                    </div>

                    <div class="prescription-diagnosis">
                        Final Diagnosis : <?= !empty($prescription['finalDiagnosis']) ? ucfirst($prescription['finalDiagnosis']) : 'N/A' ?>
                    </div>

                    <div class="prescription-view">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm view-details-btn">VIEW</a>
                    </div>

                    <div class="prescription-details">

                    <div class="prescription-info-grid">
                        <div><strong>Name:</strong> <?= !empty($prescription['patient_name']) ? strtoupper($prescription['patient_name']) : 'N/A' ?></div>
                        <div><strong>Gender / Age:</strong> <?= !empty($prescription['gender']) ? strtoupper($prescription['gender']) : 'N/A' ?> / <?= !empty($prescription['age']) ? strtoupper($prescription['age']) : 'N/A' ?> Y</div>
                        <div><strong>Appointment Id:</strong> <?= !empty($appointment['appoint_register_id']) ? strtoupper($appointment['appoint_register_id']) : 'N/A' ?></div>
                        <div><strong>UMR No:</strong> <?= !empty($prescription['patient_uid']) ? strtoupper($prescription['patient_uid']) : 'N/A' ?></div>
                    </div>

                <?php if (
                    !empty($appointment['bpSit_systolic']) ||
                    !empty($appointment['bpSit_diastolic']) ||
                    !empty($appointment['bpStand_systolic']) ||
                    !empty($appointment['bpStand_diastolic']) ||
                    !empty($appointment['weight']) ||
                    !empty($appointment['height']) ||
                    !empty($appointment['grbs']) ||
                    !empty($appointment['heart_rate']) ||
                    !empty($appointment['spO2']) ||
                    !empty($appointment['bmi'])
                ) : ?>
                    <div class="section-title">Vitals :</div>
                <?php endif; ?>

                <div class="table-responsive" style=" padding: 10px; border-radius: 5px;">
                    <table class="prescription-table">
                        <thead>
                            <tr>
                                <?php if (!empty($appointment['bpSit_systolic']) && !empty($appointment['bpSit_diastolic'])) : ?>
                                    <th>BP Sit</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['bpStand_systolic']) || !empty($appointment['bpStand_diastolic'])) : ?>
                                    <th>BP Stand</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['weight'])) : ?>
                                    <th>Weight</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['height'])) : ?>
                                    <th>Height</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['grbs'])) : ?>
                                    <th>GRBS</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['heart_rate'])) : ?>
                                    <th>Heart Rate</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['spO2'])) : ?>
                                    <th>SpO2</th>
                                <?php endif; ?>
                                <?php if (!empty($appointment['bmi'])) : ?>
                                    <th>BMI</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php if (!empty($appointment['bpSit_systolic']) && !empty($appointment['bpSit_diastolic'])) : ?>
                                    <td><?= $appointment['bpSit_systolic'];?>/<?= $appointment['bpSit_diastolic'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['bpStand_systolic']) || !empty($appointment['bpStand_diastolic'])) : ?>
                                    <td><?= $appointment['bpStand_systolic'];?>/<?= $appointment['bpStand_diastolic'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['weight'])) : ?>
                                    <td><?= $appointment['weight'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['height'])) : ?>
                                    <td><?= $appointment['height'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['grbs'])) : ?>
                                    <td><?= $appointment['grbs'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['heart_rate'])) : ?>
                                    <td><?= $appointment['heart_rate'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['spO2'])) : ?>
                                    <td><?= $appointment['spO2'];?></td>
                                <?php endif; ?>
                                <?php if (!empty($appointment['bmi'])) : ?>
                                    <td><?= $appointment['bmi'];?></td>
                                <?php endif; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($prescription['finalDiagnosis'])) : ?>
                    <div class="mb-3">
                        <p style="margin-bottom:0px;"><span class="section-title">Final Diagnosis :</span></p>
                        <div style="border:1px solid grey; padding:10px; border-radius:5px;">
                            <p style="font-size:15px; margin:0;"><?= strtoupper($prescription['finalDiagnosis']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($prescription['chiefcomplaint'])) : ?>
                    <div class="mb-3">
                        <p style="margin-bottom:0px;"><span class="section-title">Chief Complaint :</span></p>
                        <div style="border:1px solid grey; padding:10px; border-radius:5px;">
                            <p style="font-size:15px; margin:0;"><?= strtoupper($prescription['chiefcomplaint']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($prescription['pasthistory'])) : ?>
                    <div class="mb-3">
                        <p style="margin-bottom:0px;"><span class="section-title">Past History :</span></p>
                        <div style="border:1px solid grey; padding:10px; border-radius:5px;">
                            <p style="font-size:15px; margin:0;"><?= strtoupper($prescription['pasthistory']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Medicine Section -->
                <?php if ($hasMedicine) : ?>
                    <div class="section-title">Medicine:</div>

                    <div class="medicine-table-wrapper">
                        <table class="prescription-table">
                            <thead>
                                <tr>
                                    <?php $first = $getallprescripitionData[0]; ?>
                                    <?php if (!empty($first['medicine_name'])) echo "<th>Medicine Name</th>"; ?>
                                    <?php if (!empty($first['type_text'])) echo "<th>Type</th>"; ?>
                                    <?php if (!empty($first['unit_text'])) echo "<th>Unit</th>"; ?>
                                    <?php if (!empty($first['timeText']) || !empty($first['dosageText'])) echo "<th>Dosage</th>"; ?>
                                    <?php if (!empty($first['whenText'])) echo "<th>In-take-period</th>"; ?>
                                    <?php if (!empty($first['duration_value'])) echo "<th>Duration</th>"; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getallprescripitionData as $med) : ?>
                                    <tr>
                                        <?php if (!empty($first['medicine_name'])) : ?><td><?= htmlspecialchars($med['medicine_name']); ?></td><?php endif; ?>
                                        <?php if (!empty($first['type_text'])) : ?><td><?= htmlspecialchars($med['type_text']); ?></td><?php endif; ?>
                                        <?php if (!empty($first['unit_text'])) : ?><td><?= htmlspecialchars($med['unit_text']); ?></td><?php endif; ?>
                                        <?php if (!empty($first['timeText']) || !empty($first['dosageText'])) : ?>
                                            <td><?= (!empty($med['timeText'])? htmlspecialchars($med['timeText']).'<br>':'') . (!empty($med['dosageText'])? htmlspecialchars($med['dosageText']):''); ?></td>
                                        <?php endif; ?>
                                        <?php if (!empty($first['whenText'])) : ?><td><?= htmlspecialchars($med['whenText']); ?></td><?php endif; ?>
                                        <?php if (!empty($first['duration_value'])) : ?><td><?= htmlspecialchars($med['duration_value']) . ' ' . htmlspecialchars($med['duration']); ?></td><?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Test Section -->
                <?php if ($hasTest) : ?>
                    <?php
                        $hasInstruction = false;
                        foreach ($getallprescripitiontestData as $t) {
                            if (!empty($t['instruction'])) {
                                $hasInstruction = true;
                                break;
                            }
                        }
                    ?>
                    <div class="section-title">Tests:</div>
                    <table class="prescription-table">
                        <thead>
                            <tr>
                                <?php $first = $getallprescripitiontestData[0]; ?>
                                <?php if (!empty($first['test_name'])) echo "<th>Test Name</th>"; ?>
                                <?php if ($hasInstruction) echo "<th>Instructions</th>"; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($getallprescripitiontestData as $test) : ?>
                            <tr>
                                <?php if (!empty($first['test_name'])) : ?>
                                    <td><?= htmlspecialchars($test['test_name']); ?></td>
                                <?php endif; ?>

                                <?php if ($hasInstruction) : ?>
                                    <td><?= !empty($test['instruction']) ? htmlspecialchars($test['instruction']) : ''; ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php
                    $presReportsQuery = mysqli_query($conn, 
                        "SELECT * FROM patient_tests_history 
                        WHERE patient_id = '$patientUid' 
                            AND appointment_id = '$appointId' 
                            AND file_type = 'Prescription' 
                            AND performed_at = 'Within the Hospital'
                            AND org_id = '$orgId' 
                        ORDER BY uploaded_date DESC");

                    if (mysqli_num_rows($presReportsQuery) > 0) {
                        echo '<div class="card mt-3">';
                        echo '<div class="card-body">';

                        $dates = [];
                        $images = [];

                        while ($report = mysqli_fetch_assoc($presReportsQuery)) {
                            $dates[] = !empty($report['uploaded_date']) ? $report['uploaded_date'] : 'N/A';
                            if (!empty($report['file_url'])) {
                                $images[] = $report['file_url'];
                            }
                        }

                        echo '<h5 class="card-title">Prescription (' . implode(', ', array_unique($dates)) . ')</h5>';

                        if (!empty($images)) {
                        $imgIndex = 0;
                        foreach ($images as $img) {
                            $filePath = "ajax/Testimages/" . basename($img);
                            $modalId = "imageModal" . $imgIndex; 

                            echo '<div class="mb-3">';
                            echo '<iframe src="' . $filePath . '" 
                                    alt="Prescription " 
                                    class="img-fluid" 
                                    style="width:600px; height:400px; object-fit:contain; cursor:pointer;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#' . $modalId . '"> </iframe>';
                            echo '</div>';

                            echo '
                            <div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content" style="width:100vw; height:95vh; max-width:100vw;">
                                <div class="modal-header d-flex justify-content-between align-items-center">
                                    <h5 class="modal-title">Prescription Report View</h5>
                                    <div class="d-flex gap-2">
                                    <a href="' . $filePath . '" download class="btn btn-outline-success btn-sm" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal" title="Close">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    </div>
                                </div>
                                <div class="modal-body text-center d-flex justify-content-center align-items-center" style="height:85vh; overflow:auto;">
                                    <iframe src="' . $filePath . '" class="img-fluid" 
                                        alt="Prescription Report" 
                                        style="max-height:100%; max-width:100%; object-fit:contain;"></iframe>
                                </div>
                                </div>
                            </div>
                            </div>';
                            $imgIndex++;
                        }
                        } else {
                            // echo '<p>No images found</p>';
                        }

                        echo '</div>';
                        echo '</div>';
                    } else {
                        // echo '<div class="card mt-3">';
                        // echo '<div class="card-body">';
                        // echo '<h5 class="card-title">Prescription Report</h5>';
                        // echo '<p>No prescription reports found.</p>';
                        // echo '</div>';
                        // echo '</div>';
                    }
                ?>

                <?php
                    $testReportsQuery = mysqli_query($conn, 
                        "SELECT * FROM patient_tests_history 
                        WHERE patient_id = '$patientUid' 
                        AND appointment_id = '$appointId' 
                        AND file_type = 'Test' 
                        AND performed_at = 'Within the Hospital'
                        AND org_id = '$orgId' 
                        ORDER BY uploaded_date DESC");

                    if ($testReportsQuery && mysqli_num_rows($testReportsQuery) > 0) {
                        echo '<div class="card mt-3">';
                        echo '<div class="card-body">';

                        $reportsInfo = []; 
                        $images = [];
                        $imgIndex = 0;

                        while ($report = mysqli_fetch_assoc($testReportsQuery)) {
                            $testName = !empty($report['test_name']) ? ucfirst($report['test_name']) : 'N/A';
                            $uploadedDate = !empty($report['uploaded_date']) ? date("d M Y", strtotime($report['uploaded_date'])) : 'N/A';
                           $reportsInfo[] = $testName . ', ' . $uploadedDate; 

                            if (!empty($report['file_url'])) {
                                $images[] = $report['file_url'];
                            }
                        }

                        echo '<h5 class="card-title">Test Reports (' . implode(', ', $reportsInfo) . ')</h5>';

                        if (!empty($images)) {
                            foreach ($images as $img) {
                                $filePath = "ajax/Testimages/" . basename($img);
                                $modalId = "testModal" . $imgIndex;

                                echo '<div class="mb-3">';
                                echo '<iframe src="' . $filePath . '" 
                                            class="img-fluid" 
                                            style="width:600px; height:400px; object-fit:contain; cursor:pointer;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#' . $modalId . '"></iframe>';
                                echo '</div>';

                                echo '
                                <div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                        <div class="modal-content" style="width:100vw; height:95vh; max-width:100vw;">
                                            <div class="modal-header d-flex justify-content-between align-items-center">
                                                <h5 class="modal-title">Test Report View</h5>
                                                <div class="d-flex gap-2">
                                                    <a href="' . $filePath . '" download class="btn btn-outline-success btn-sm fw-bold" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal" title="Close">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="modal-body text-center d-flex justify-content-center align-items-center" style="height:85vh; overflow:auto;">
                                                <iframe src="' . $filePath . '" class="img-fluid" style="max-height:100%; max-width:100%; object-fit:contain;"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                $imgIndex++;
                            }
                        }

                        echo '</div></div>';
                    }
                ?>

            </div> 

        </div> 

        <?php
        } 

    ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(document).on('click', '.view-details-btn', function() {
        var $card = $(this).closest('.prescription-card');
        var $details = $card.find('.prescription-details');

        if (!$details.is(':visible')) {
            $details.slideDown();
        }
    });

    // $(document).on('click', '.view-details-btn', function() {
    //     var $card = $(this).closest('.prescription-card');
    //     var $details = $card.find('.prescription-details');

    //     $details.slideToggle(); 
    // });


</script>

<?php

    } else {
        echo "<p>No prescription records found.</p>";
    }

    mysqli_close($conn);
    } else {
        echo "<p>Invalid request or missing prescription IDs.</p>";
    }
?>