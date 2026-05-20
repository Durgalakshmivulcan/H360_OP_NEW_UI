<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';



$orgId = isset($_POST['org_id']) && !empty($_POST['org_id']) ? $_POST['org_id'] : $SessionOrgId;

if (isset($_POST['ids']) && !empty($_POST['ids'])) {
  $ids = array_map('intval', explode(',', $_POST['ids']));
  $idsStr = implode(',', $ids);

  // FIX_B_1903: doctor-scope filter via join to appointment_online.doctor_name
  $docScope = currentDoctorScopeSql('ao.doctor_name');
  $query = "SELECT p.* FROM prescripition p
              LEFT JOIN appointment_online ao ON ao.appoint_register_id = p.appoint_register_id
              WHERE p.prescription_id IN ($idsStr) AND p.org_id = '$orgId' AND p.status = '1'
              $docScope
              ORDER BY p.prescription_id DESC";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    while ($prescription = mysqli_fetch_assoc($result)) {

      $appointId = $prescription['appoint_register_id'];
      $patientUid = $prescription['patient_uid'];

      $appointmentQuery = mysqli_query(
        $conn,
        "SELECT * FROM appointment_online 
                 WHERE appoint_register_id = '$appointId' 
                   AND appoint_unicode = '$patientUid' 
                   AND org_id = '$orgId' 
                 LIMIT 1"
      );

      $appointment = mysqli_fetch_assoc($appointmentQuery);

      $orginfo = mysqli_query(
        $conn,
        "SELECT organization_name FROM organization 
                WHERE org_id  = '$orgId'"
      );

      $orgname = mysqli_fetch_assoc($orginfo);

      $getallprescripitionData = [];
      $getallprescripitiontestData = [];

      if (!empty($prescription['medicine_id'])) {
        $decodedMedicines = json_decode($prescription['medicine_id'], true);
        if (is_array($decodedMedicines)) {
          $getallprescripitionData = $decodedMedicines;
        }
      }

      if (!empty($prescription['test_id'])) {
        $decodedTests = json_decode($prescription['test_id'], true);
        if (is_array($decodedTests)) {
          $getallprescripitiontestData = $decodedTests;
        }
      }

      $hasMedicine = !empty($getallprescripitionData);
      $hasTest = !empty($getallprescripitiontestData);
?>
      <style>
        .head {
          font-weight: 800 !important;
        }

        .info {
          font-weight: 600 !important;
        }

        .clearfix:after {
          content: "";
          display: table;
          clear: both;
        }

        a {
          color: #5D6975;
          text-decoration: underline;
        }


        header {
          padding: 10px 0;
          margin-bottom: 30px;
        }

        #logo {
          text-align: center;
          margin-bottom: 10px;
        }

        #logo img {
          width: 90px;
        }

        h1 {
          border-top: 1px solid #5D6975;
          border-bottom: 1px solid #5D6975;
          color: #5D6975;
          font-size: 2.4em;
          line-height: 1.4em;
          font-weight: normal;
          text-align: center;
          margin: 0 0 20px 0;
          background: url(dimension.png);
        }

        #project {
          float: left;
        }

        #project span {
          color: #5D6975;
          text-align: right;
          width: 52px;
          margin-right: 10px;
          display: inline-block;
          font-size: 0.8em;
        }

        #company {
          float: right;
          text-align: right;
          font-size: 14px;
        }

        #project div,
        #company div {
          white-space: nowrap;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
        }



        table th,
        table td {
          text-align: center;
        }

        table th {
          padding: 5px 20px;
          color: #5D6975;
          border-bottom: 1px solid #C1CED9;
          white-space: nowrap;
          font-weight: normal;
        }

        table .service,
        table .desc {
          text-align: left;
        }

        table td {
          padding: 20px;
          text-align: right;
        }

        table td.service,
        table td.desc {
          vertical-align: top;
        }


        table td.unit,
        table td.qty,
        table td.total {
          font-size: 1.2em;
        }

        table td.grand {
          border-top: 1px solid #5D6975;
          ;
        }

        #notices .notice {
          color: #5D6975;
          font-size: 1.2em;
        }

        footer {
          color: #5D6975;
          width: 100%;
          height: 30px;
          position: absolute;
          bottom: 0;
          border-top: 1px solid #C1CED9;
          padding: 8px 0;
          text-align: center;
        }

        .info-box {
          border: 1px solid #000;
          display: flex;
          flex-wrap: wrap;
          margin-bottom: 10px;
        }

        .col-half {
          width: 50%;
          padding: 5px 10px;
          position: relative;
        }

        .col-half:not(:last-child)::after {
          content: "";
          position: absolute;
          right: 0;
          top: 0;
          height: 100%;
          width: 2px;
          background-color: black;
        }

        .info-row {
          display: flex;
          align-items: center;
        }

        .info-label {
          color: #3462ca;
          min-width: 130px;
          font-size: 18px;
        }

        .info-value {
          flex: 1;
        }

        .align-right {
          text-align: right;
        }

        .vitals-container {
          margin-top: 20px;
        }

        .vitals-title {
          font-weight: bold;
          margin-bottom: 5px;
        }

        .vitals-table {
          width: 100%;
          border-collapse: collapse;
          table-layout: auto;
          word-wrap: break-word;
        }

        .vitals-table th,
        .vitals-table td {
          border: 1px solid #ccc;
          padding: 6px 8px;
          text-align: left;
          font-size: 14px;
        }

        .vitals-table-container {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
          margin-top: 5px;
          margin-bottom: 10px;
        }

        .section-title {
          font-weight: bold;
          font-size: 15px;
          margin-top: 10px;
        }

        .border-box {
          border: 1px solid #ccc;
          padding: 8px;
          border-radius: 6px;
          background: #f9f9f9;
        }

        .h6 {
          line-height: 0 !important;
          margin-bottom: 0px !important;
        }
      </style>

      <div class="card">
        <div class="card-body">
          <div class="row mb-1">
            <div class="col-md-10 col-sm-12"></div>
            <div class="col-md-1 col-sm-12">
            </div>
            <div class="col-md-1 col-sm-12">
              <div class="info"><span class="head"><a class="btn btn-primary" href="patientPrescription.php?ItemId=<?= $prescription['prescription_id'] ?>&OrgID=<?= $prescription['org_id'] ?>" target="_blank"> View</a></span></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Name : </span><?= !empty($prescription['patient_name']) ? strtoupper($prescription['patient_name']) : 'N/A' ?></div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Gender / Age : </span><?= !empty($prescription['gender']) ? strtoupper($prescription['gender']) : 'N/A' ?> / <?= !empty($prescription['age']) ? strtoupper($prescription['age']) : 'N/A' ?> Y</div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Date : </span><?= !empty($prescription['prescriptiondate']) ? strtoupper($prescription['prescriptiondate']) : 'N/A' ?></div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Mobile : </span><?= !empty($appointment['mobile_number']) ? strtoupper($appointment['mobile_number']) : 'N/A' ?></div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Appointment Id : </span><?= !empty($appointment['appoint_register_id']) ? strtoupper($appointment['appoint_register_id']) : 'N/A' ?></div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Organization : </span><?= !empty($orgname['organization_name']) ? strtoupper($orgname['organization_name']) : 'N/A' ?></div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">UMR No : </span><?= !empty($prescription['patient_uid']) ? strtoupper($prescription['patient_uid']) : 'N/A' ?></div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Bill No : </span><?= !empty($appointment['bill_id']) ? strtoupper($appointment['bill_id']) : 'N/A' ?></div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Visit Dt : </span><?= !empty($appointment['appoint_date']) ? strtoupper($appointment['appoint_date']) : 'N/A' ?></div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="info"><span class="head">Bill Dt : </span><?= !empty($appointment['bill_date']) ? strtoupper($appointment['bill_date']) : 'N/A' ?></div>
            </div>
          </div>
          <div class="row">
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

            <div class="form-group col-lg-12">
              <div class="vitals-table-container">
                <table class="vitals-table">
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
                        <th>spO2</th>
                      <?php endif; ?>
                      <?php if (!empty($appointment['bmi'])) : ?>
                        <th>BMI</th>
                      <?php endif; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <?php if (!empty($appointment['bpSit_systolic']) && !empty($appointment['bpSit_diastolic'])) : ?>
                        <td><?= $appointment['bpSit_systolic']; ?>/<?= $appointment['bpSit_diastolic']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['bpStand_systolic']) || !empty($appointment['bpStand_diastolic'])) : ?>
                        <td><?= $appointment['bpStand_systolic']; ?>/<?= $appointment['bpStand_diastolic']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['weight'])) : ?>
                        <td><?= $appointment['weight']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['height'])) : ?>
                        <td><?= $appointment['height']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['grbs'])) : ?>
                        <td><?= $appointment['grbs']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['heart_rate'])) : ?>
                        <td><?= $appointment['heart_rate']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['spO2'])) : ?>
                        <td><?= $appointment['spO2']; ?></td>
                      <?php endif; ?>
                      <?php if (!empty($appointment['bmi'])) : ?>
                        <td><?= $appointment['bmi']; ?></td>
                      <?php endif; ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <?php if (!empty($prescription['finalDiagnosis'])) : ?>
              <div>
                <p style="margin-bottom:0px;"><span class="section-title">Final Diagnosis :</span></p>
                <div class="border-box">
                  <div>
                    <p style="font-size:15px;"><?= strtoupper($prescription['finalDiagnosis']); ?></p>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if (!empty($prescription['chiefcomplaint'])) : ?>
              <div>
                <p style="margin-bottom:0px;"><span class="section-title">Chief Complaint :</span></p>
                <div class="border-box">
                  <div>
                    <p style="font-size:15px;"><?= strtoupper($prescription['chiefcomplaint']); ?></p>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if (!empty($prescription['pasthistory'])) : ?>
              <div>
                <p style="margin-bottom:0px;"><span class="section-title">Past History :</span></p>
                <div class="border-box">
                  <div>
                    <p style="font-size:15px;"><?= strtoupper($prescription['pasthistory']); ?></p>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if (!empty($prescription['patient_data'])) : ?>
              <div>
                <p style="margin-bottom:0px;"><span class="section-title">Patient Data :</span></p>
                <div class="border-box">
                  <div>
                    <p style="font-size:14px; margin-bottom:0; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($prescription['patient_data'])); ?></p>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($hasMedicine) : ?>
              <div class="section-title">Medicine:</div>
              <div class="vitals-table-container">
                <table class="vitals-table">
                  <thead style="background-color: #ddd;">
                    <tr>
                      <?php
                      $first = $getallprescripitionData[0];
                      if (!empty($first['medicine_name'])) echo "<th>Medicine Name</th>";
                      if (!empty($first['type_text'])) echo "<th>Type</th>";
                      if (!empty($first['unit_text'])) echo "<th>Unit</th>";
                      if (!empty($first['duration_value'])) echo "<th>Dosage</th>";
                      if (!empty($first['duration'])) echo "<th>In-take-period</th>";
                      if (!empty($first['timeText'])) echo "<th>Duration</th>";
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($getallprescripitionData as $medicine) : ?>
                      <tr>
                        <?php if (!empty($first['medicine_name'])) : ?>
                          <td><?= htmlspecialchars($medicine['medicine_name']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['type_text'])) : ?>
                          <td><?= htmlspecialchars($medicine['type_text']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['unit_id'])) : ?>
                          <td><?= htmlspecialchars($medicine['unit_text']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['timeText']) || !empty($first['dosageText'])) : ?>
                          <td>
                            <?= (!empty($medicine['timeText']) ? htmlspecialchars($medicine['timeText']) . '<br>' : '') .
                              (!empty($medicine['dosageText']) ? htmlspecialchars($medicine['dosageText']) : '') ?>
                          </td>
                        <?php endif; ?>
                        <?php if (!empty($first['whenText'])) : ?>
                          <td><?= htmlspecialchars($medicine['whenText']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['duration_value'])) : ?>
                          <td>
                            <?= (!empty($medicine['duration_value']) ? htmlspecialchars($medicine['duration_value']) . ' ' : '') .
                              (!empty($medicine['duration']) ? htmlspecialchars($medicine['duration']) : '') ?>
                          </td>
                        <?php endif; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

            <?php if ($hasTest) : ?>
              <div class="section-title">Tests:</div>
              <div class="vitals-table-container">
                <table class="vitals-table">
                  <thead style="background-color: #ddd;">
                    <tr>
                      <?php
                      $first = $getallprescripitiontestData[0];
                      if (!empty($first['test_name'])) echo "<th>Test Name</th>";
                      if (!empty($first['instruction'])) echo "<th>Instructions</th>";
                      if (!empty($first['doctor_price'])) echo "<th>Amount</th>";
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($getallprescripitiontestData as $test) : ?>
                      <tr>
                        <?php if (!empty($first['test_name'])) : ?>
                          <td><?= htmlspecialchars($test['test_name']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['instruction'])) : ?>
                          <td><?= htmlspecialchars($test['instruction']); ?></td>
                        <?php endif; ?>
                        <?php if (!empty($first['doctor_price'])) : ?>
                          <td><?= htmlspecialchars($test['doctor_price']); ?></td>
                        <?php endif; ?>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

          </div>


        </div>
      </div>



      <?php
      $testReportsQuery = mysqli_query(
        $conn,
        "SELECT * FROM patient_tests_history 
              WHERE patient_id = '$patientUid' 
                AND appointment_id = '$appointId' 
                AND file_type = 'Test' 
                AND performed_at = 'Within the Hospital'
                AND status = '1'
                AND org_id = '$orgId' 
              ORDER BY uploaded_date DESC"
      );

      if ($testReportsQuery && mysqli_num_rows($testReportsQuery) > 0) {
        echo '<div class="card mt-3">';
        echo '<div class="card-body">';

        $dates = [];
        $testNames = [];
        $images = [];
        $imgIndex = 0;

        while ($report = mysqli_fetch_assoc($testReportsQuery)) {
          $dates[] = !empty($report['uploaded_date']) ? $report['uploaded_date'] : 'N/A';

          if (!empty($report['test_name'])) {
            $testNames[] = ucfirst($report['test_name']);
          }

          if (!empty($report['file_url'])) {
            $images[] = $report['file_url'];
          }
        }

        $uniqueDates = array_unique($dates);
        $uniqueTests = array_unique($testNames);

        echo '<h5 class="card-title">Test Reports (' . implode(', ', $uniqueTests) . ' - ' . implode(', ', $uniqueDates) . ')</h5>';



        if (!empty($images)) {
          foreach ($images as $img) {
            $filePath = "ajax/Testimages/" . basename($img);
            $modalId = "testModal" . $imgIndex;

            echo '<div class="mb-3">';
            echo '<img src="' . $filePath . '" 
                                alt="Test Report" 
                                class="img-fluid" 
                                style="width:600px; height:400px; object-fit:contain; cursor:pointer;"
                                data-bs-toggle="modal" 
                                data-bs-target="#' . $modalId . '">';
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
                              <img src="' . $filePath . '" class="img-fluid" 
                                  alt="Test Report" 
                                  style="max-height:100%; max-width:100%; object-fit:contain;">
                            </div>
                          </div>
                        </div>
                      </div>';
            $imgIndex++;
          }
        } else {
          echo '<p>No images found</p>';
        }

        echo '</div>';
        echo '</div>';
      } else {
        // echo '<div class="card mt-3"><div class="card-body">';
        // echo '<h5 class="card-title">Test Reports</h5>';
        // echo '<p>No test reports found.</p>';
        // echo '</div></div>';
      }
      ?>

      <?php
      $presReportsQuery = mysqli_query(
        $conn,
        "SELECT * FROM patient_tests_history 
              WHERE patient_id = '$patientUid' 
                AND appointment_id = '$appointId' 
                AND file_type = 'Prescription'
                AND status = '1'
                AND performed_at = 'Within the Hospital' 
                AND org_id = '$orgId' 
              ORDER BY uploaded_date DESC"
      );

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
            echo '<img src="' . $filePath . '" 
                          alt="Prescription Report" 
                          class="img-fluid" 
                          style="width:600px; height:400px; object-fit:contain; cursor:pointer;"
                          data-bs-toggle="modal" 
                          data-bs-target="#' . $modalId . '">';
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
                        <img src="' . $filePath . '" class="img-fluid" 
                            alt="Prescription Report" 
                            style="max-height:100%; max-width:100%; object-fit:contain;">
                      </div>
                    </div>
                  </div>
                </div>';
            $imgIndex++;
          }
        } else {
          echo '<p>No images found</p>';
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
    }
  } else {
    echo "<p>No prescription records found.</p>";
  }

  mysqli_close($conn);
} else {
  echo "<p>Invalid request or missing prescription IDs.</p>";
}
?>