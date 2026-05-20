<?php
require_once("../../config/functions.php");

$SessionOrgId = $_SESSION['org_id'] ?? '';
$orgId = isset($_POST['org_id']) && !empty($_POST['org_id']) ? $_POST['org_id'] : $SessionOrgId;

if (isset($_POST['ids']) && !empty($_POST['ids'])) {
    $ids = array_map('intval', explode(',', $_POST['ids']));
    $idsStr = implode(',', $ids);

    // FIX_B_1903: doctor-scope filter via join to appointment_online.doctor_name
    $docScope = currentDoctorScopeSql('ao.doctor_name');
    $query = "SELECT p.* FROM prescripition p
              LEFT JOIN appointment_online ao ON ao.appoint_register_id = p.appoint_register_id
              WHERE p.patient_uid = $idsStr
              AND p.org_id = '$orgId'
              AND p.status = '1'
              $docScope
              ORDER BY p.prescription_id DESC";
    $result = mysqli_query($conn, $query);

    $hasReports = false;

    if ($result && mysqli_num_rows($result) > 0) {
        while ($prescription = mysqli_fetch_assoc($result)) {
            $appointId = $prescription['appoint_register_id'];
            $patientUid = $prescription['patient_uid'];

            // Fetch Prescriptions
            // Fetch Prescriptions
            $presReportsQuery = mysqli_query(
                $conn,
                "SELECT * FROM patient_tests_history 
                    WHERE patient_id = '$patientUid' 
                    AND appointment_id = '$appointId' 
                    AND file_type = 'Prescription' 
                    AND performed_at = 'Outside the Hospital' 
                    AND org_id = '$orgId' 
                    AND status = '1'
                    ORDER BY uploaded_date + 0 ASC"
            );


            if (mysqli_num_rows($presReportsQuery) > 0) {
                $hasReports = true;
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
                            <div><strong>Doctor:</strong> <?= $doctorName ?></div>
                            <div><strong>Specialization:</strong> <?= $specialization ?></div>
                            <?php
                            if (!empty($images)) {
                                foreach ($images as $img) {
                                    $filePath = "ajax/Testimages/" . basename($img);
                                    // Detect PDF or Image
                                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                                    if (in_array(strtolower($ext), ['pdf'])) {
                                        echo '<iframe src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:400px; object-fit:contain;"></iframe>';
                                    } else {
                                        echo '<img src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:auto; object-fit:contain;">';
                                    }
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

            $testReportsQuery = mysqli_query(
                $conn,
                "SELECT * FROM patient_tests_history 
                    WHERE patient_id = '$patientUid' 
                    AND appointment_id = '$appointId' 
                    AND file_type = 'Test' 
                    AND performed_at = 'Outside the Hospital' 
                    AND org_id = '$orgId' 
                    AND status = '1'
                    ORDER BY uploaded_date + 0 ASC"
            );


            if (mysqli_num_rows($testReportsQuery) > 0) {
                $hasReports = true;
                while ($report = mysqli_fetch_assoc($testReportsQuery)) {
                    $uploadedDate = !empty($report['uploaded_date']) ? date("d M Y", strtotime($report['uploaded_date'])) : 'N/A';
                    $doctorName = !empty($report['doctor_name']) ? ucfirst($report['doctor_name']) : 'N/A';
                    $specialization = !empty($report['specialization']) ? ucfirst($report['specialization']) : 'N/A';
                    $test_name = !empty($report['test_name']) ? ucfirst($report['test_name']) : 'N/A';
                    $images = !empty($report['file_url']) ? explode(',', $report['file_url']) : [];
                ?>
                    <div class="card mt-3">
                        <h6 class="mt-3 p-2">Test Report</h6>
                        <div class="card-body">
                            <div><strong>Date:</strong> <?= $uploadedDate ?></div>
                            <div><strong>Doctor:</strong> <?= $doctorName ?></div>
                            <div><strong>Specialization:</strong> <?= $specialization ?></div>
                            <div><strong>Test Name:</strong> <?= $test_name ?></div>
                            <?php
                            if (!empty($images)) {
                                foreach ($images as $img) {
                                    $filePath = "ajax/Testimages/" . basename($img);
                                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                                    if (in_array(strtolower($ext), ['pdf'])) {
                                        echo '<iframe src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:400px; object-fit:contain;"></iframe>';
                                    } else {
                                        echo '<img src="' . $filePath . '" class="img-fluid mb-2" style="width:100%; height:auto; object-fit:contain;">';
                                    }
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
        } // end while
    }

    if (!$hasReports) {
        echo '<p class="text-muted">No old reports found.</p>';
    }
} else {
    echo '<p class="text-danger">No old reports was uploaded.</p>';
}
?>