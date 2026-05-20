<?php
require_once("../../config/functions.php");

// FIX_B_008 — wrap response in declared print classes
if (!defined('FIX_B_008_WRAPPED')) {
    define('FIX_B_008_WRAPPED', true);
    ob_start(function ($buf) {
        return '<div class="print-div custom-print-style">' . $buf . '</div>';
    });
}
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
?>

<style>
    .reportsFile {
        width: 150px;
        height: 150px;
        margin-left: 20px;
        overflow: hidden;
    }
    .viewReports {
        margin-left: 60px;
        margin-top: 25px;
    }
    .file-preview {
        margin-bottom: 20px;
        border: 1px solid #ccc;
        padding: 10px;
        display: inline-block;
        border-radius: 10px;
        text-align: center;
        max-width: 200px;
        min-height: 260px;
        background-color: #f9f9f9;
    }
    .pages {
        max-width: 100%;
        margin: auto;
        padding: 15px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }
    table th, table td {
        text-align: left;
        padding: 20px;
    }
    table th {
        padding: 5px 20px;
        color: #5D6975;
        border-bottom: 1px solid #C1CED9;
        white-space: nowrap;
        font-weight: normal;
    }
    table td.unit, table td.qty, table td.total {
        font-size: 1.2em;
        text-align: center;
    }
    table td.grand {
        border-top: 1px solid #5D6975;
    }
</style>

<div class="pages">
    <div class="row border-bottom border-primary">
        <div class="form-group col-lg-6 col-md-12 col-sm-12 border_name" style="padding:40px">
            <?php
            $id            = $_POST['appoint_register_id'];
            $id2           = $_POST['appoint_unicode'];
            $mobile_number = $_POST['mobile_number'];
            $patient_name  = $_POST['patient_name'];
            // FIX_B_028: ignore client-supplied org_id; derive from session only
            require_once(__DIR__ . '/../../include/auth_guard.php');
            requireLogin();
            assertOrgId();
            $org_id        = $_SESSION['org_id'];

            // FIX_B_1903: doctor-scope filter
            $docScope_B1903 = currentDoctorScopeSql('doctor_name');
            // Appointment info
            $getappoint = mysqli_query($conn, "
                SELECT *
                FROM appointment_online
                WHERE appoint_status='1'
                  AND appoint_id='$patient_name'
                  AND org_id='$org_id'
                  $docScope_B1903
            ") or die(mysqli_error($conn));
            $resAppointmentData = mysqli_fetch_object($getappoint);

            // Prescription info
            $getPrescriptionData = mysqli_query($conn, "
                SELECT * 
                FROM prescripition 
                WHERE status='1' 
                  AND patient_uid='$resAppointmentData->appoint_unicode' 
                  AND org_id='$org_id' 
                ORDER BY prescription_id DESC 
                LIMIT 1
            ") or die(mysqli_error($conn));
            $resPrescriptionData = mysqli_fetch_object($getPrescriptionData);

            
            // Doctor & org info
            $getDoctorName = mysqli_query($conn, "
                SELECT * 
                FROM doctors 
                WHERE doc_id='$resAppointmentData->doctor_name' 
                  AND org_id='$resAppointmentData->org_id'
            ") or die(mysqli_error($conn));
            $resDoctorName = mysqli_fetch_object($getDoctorName);

            $getOrganizationAddress = mysqli_query($conn, "
                SELECT * 
                FROM organization 
                WHERE org_id='$org_id'
            ") or die(mysqli_error($conn));
            $resOrganizationAddress = mysqli_fetch_object($getOrganizationAddress);
            ?>
        </div>
    </div>

    <!-- Header -->
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-6">
            <?php
            $uploadDir   = __DIR__ . '/../../organisation_images/';
            $getLogoQuery = "SELECT logo FROM organization WHERE org_id = '$org_id' AND status = '1'";
            $result       = mysqli_query($conn, $getLogoQuery);

            if ($result) {
                $org = mysqli_fetch_assoc($result);
                $logoFileName = $org['logo'];
                $logoPath = $uploadDir . $logoFileName;
                if (!empty($logoFileName) && file_exists($logoPath)) {
                    echo '<img alt="Organization Logo" src="organisation_images/' . $logoFileName . '" style="margin-top: 20px;">';
                } else {
                    echo '<img alt="Organization Logo" src="assets/img/h360.png" style="margin-top: 20px; width: 100px; margin-right: 20px;">';
                }
            }
            ?>
        </div>
        <div class="col-sm-12 col-lg-6">
            <h3><b>Dr. <?= htmlspecialchars($resDoctorName->doctor_name) ?></b></h3>
            <span><?= htmlspecialchars($resOrganizationAddress->address) ?></span>
        </div>
    </div>

    <!-- Appointment details -->
    <p class="mt-4"><b>Appointment ID:</b> <?= $resAppointmentData->appoint_register_id; ?></p>
    <div class="row mt-5">
        <div class="col-4"><p><b>Patient Name:</b> <?= $resAppointmentData->patient_name; ?></p></div>
        <div class="col-4"><p><b>Patient ID:</b> <?= $resAppointmentData->appoint_unicode; ?></p></div>
        <div class="col-4"><p><b>Gender:</b> <?= $resAppointmentData->gender; ?></p></div>
    </div>
    <div class="row">
        <div class="col-4"><p><b>Age:</b> <?= $resAppointmentData->age; ?></p></div>
        <div class="col-4"><p><b>Date:</b> <?= $resAppointmentData->appoint_date; ?></p></div>
        <div class="col-4"><p><b>Ph. No:</b> <?= $resAppointmentData->mobile_number; ?></p></div>
    </div>

   

    

    <!-- Patient test history -->
    <div class="row border-bottom col-lg-12 col-sm-12 mt-5 mb-5">
        <h5><b>Patient Test History</b></h5>
        <table style="width: 100%;">
            <thead class="text-center">
                <tr>
                    <th><b>S.No</b></th>
                    <th><b>Date</b></th>
                    <th><b>Performed At</b></th>
                    <th><b>File Type</b></th>
                    <th><b>Test Name</b></th>
                    <th><b>Observations</b></th>
                    <th><b>Attachment</b></th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                $getTests = mysqli_query($conn, "
                    SELECT *
                    FROM patient_tests_history 
                    WHERE patient_id = '$id2' 
                    AND appointment_id = '$id' 
                    AND org_id = '$org_id' 
                    AND status = '1'
                ") or die(mysqli_error($conn));
                
                if (mysqli_num_rows($getTests) > 0) {
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($getTests)) {
                        $file_url="ajax/".$row['file_url'];
                        $filePath = htmlspecialchars($file_url);
                        $fileExt  = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['uploaded_date']) ?></td>
                            <td><?= htmlspecialchars($row['performed_at']) ?></td>
                            <td><?= htmlspecialchars($row['file_type']) ?></td>
                            <td><?= !empty($row['test_name']) ? htmlspecialchars($row['test_name']) : '-' ?></td>
                            <td><?= !empty($row['observations']) ? htmlspecialchars($row['observations']) : '-' ?></td>
                            <td>
                                <?php if (!empty($filePath)): ?>
                                    <?php if (!empty($fileExt)): ?>
                                        <!-- View icon -->
                                        <a href="<?= $filePath ?>" target="_blank" title="View" class="me-3">
                                            <i class="fa fa-eye" style="font-size:16px;color:#007bff;"></i>
                                        </a>
                                        <!-- Download icon -->
                                        <a href="<?= $filePath ?>" download title="Download">
                                            <i class="fa fa-download" style="font-size:16px; color:#007bff;"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- For other file types only download -->
                                        <a href="<?= $filePath ?>" download title="Download">
                                            <i class="fa fa-download" style="font-size:16px; color:#007bff;"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#888;">No file</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>No test history found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/xlsx.mini.min.js"></script>
