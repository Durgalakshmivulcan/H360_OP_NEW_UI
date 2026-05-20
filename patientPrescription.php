<?php
  require_once("config/config.php");

 $SessionUserId = $_SESSION['security_id'] ?? '';
  $SessionRoleId = $_SESSION['role_id'] ?? '';
  $SessionOrgId = $_SESSION['org_id'] ?? '';

  
  $id=$_GET['ItemId'];
  $orgid=$_GET['OrgID'];

  // $getPresMedicines=mysqli_query($conn, "SELECT * FROM prescription_medicines  WHERE status='1' AND  prescription_id='$id'") or die(mysqli_error($conn));
  // $resPresMedic = mysqli_fetch_object($getPresMedicines);
  // $prescriptionID=$resPresMedic->prescription_id;

  $getUid=mysqli_query($conn,"SELECT * FROM prescripition WHERE status='1' AND prescription_id='$id' AND org_id ='$orgid'") or die(mysqli_error($conn));
  $resUid=mysqli_fetch_object($getUid);
  $patientID=$resUid->patient_uid;
  $Appointment_ID=$resUid->appoint_register_id;
  $PAtient_Age=$resUid->age;
  $Gender=$resUid->gender;
  $orgID=$resUid->org_id;
  $prescriptionData=$resUid->create_date;
   

  $patientvitalsID=$_Get['patient_vitals'];


 
  $app = mysqli_query($conn, "SELECT * FROM prescripition p INNER JOIN appointment_online a   ON p.appoint_register_id = a.appoint_register_id  WHERE p.patient_uid = '$patientID'  AND p.prescription_id='$id' AND p.org_id ='$orgid'  AND a.org_id = '$orgid'") or die(mysqli_error($conn));
  $appdata = mysqli_fetch_object($app);
  $patientvitalsId = $appdata-> patient_vitals;
  $BpSit = $appdata->systolic;
  $Bpsit = $appdata->	diastolic;
  $mobile = $appdata->mobile_number;
  $weight = $appdata->weight;
  $height = $appdata->height;
  // echo"$patient_vitals";
  // echo $weight;
  $getallprescripitionData = [];
  $getallprescripitiontestData = [];

  if (!empty($appdata->medicine_id)) {
      $decodedMedicines = json_decode($appdata->medicine_id, true); // decode JSON string into array
      $decodedTests = json_decode($appdata->test_id, true); // decode JSON string into array
  
      if (is_array($decodedMedicines)) {
          $getallprescripitionData = $decodedMedicines;
      }
      if (is_array($decodedTests)) {
        $getallprescripitiontestData = $decodedTests;
    }
  }
  $ogd = mysqli_query($conn, "SELECT * FROM organization AS o INNER JOIN prescripition AS p ON p.org_id = o.org_id WHERE o.org_id = '$orgid'") or die(mysqli_error($conn));
  $resOrg = mysqli_fetch_object($ogd);
  $orgName = $resOrg->organization_name	;
  $org_id = $resOrg->org_id;
  // echo $orgName;
  // echo "SELECT * FROM organization AS o INNER JOIN prescripition AS p ON p.org_id = o.org_id WHERE o.org_id = '$orgID'";

  $getAppointOnline = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_register_id='$Appointment_ID' AND appoint_unicode='$patientID' AND age='$PAtient_Age' AND gender='$Gender'  AND org_id ='$orgid'") or die(mysqli_error($conn));

  while ($resAppoint = mysqli_fetch_object($getAppointOnline)) {
      $PatientName= $resAppoint->patient_name;
      $systolic= $resAppoint->systolic;
      $diastolic= $resAppoint->diastolic;
      
  }

  $getAppointExisting = mysqli_query($conn, "SELECT * FROM appointment_existing WHERE appoint_register_id='$Appointment_ID' AND appoint_unicode='$patientID' AND age='$PAtient_Age' AND gender='$Gender'  AND org_id ='$orgid'") or die(mysqli_error($conn));

  while ($resAppoint = mysqli_fetch_object($getAppointExisting)) {
      $PatientName= $resAppoint->patient_name;
      $systolic= $resAppoint->systolic;
      $diastolic= $resAppoint->diastolic;
  }

      
  $getSizes = mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' AND org_id='$resAppoint->org_id'");
  $resData = mysqli_fetch_object($getSizes);
  
  $top = "150px";
  $bottom = "30px";
  
  if (!empty($resData->top)) {
      $top = $resData->top;
  }
  
  if (!empty($resData->bottom)) {
      $bottom = $resData->bottom;
  }
  
  $getSingleSize = mysqli_query($conn, "SELECT w_size, h_size FROM pagessize WHERE status='1' AND size_id='$resData->sizes'");
  $resSingleData = mysqli_fetch_object($getSingleSize);
  
  $width = '21cm';
  $height = '29.2cm';
  
  if (!empty($resSingleData->w_size)) {
      $width = $resSingleData->w_size;
  }
  
  // if (!empty($resSingleData->h_size)) {
  //     $height = $resSingleData->h_size;
  // }
  $hasMedicine = !empty($getallprescripitionData);
  $hasTest= !empty($getallprescripitiontestData);

    // FIX_B_058 — prescriber-aware doctor / signature lookup. Falls back
    // to org-only filter when prescriber id is not resolvable.
    $prescriberDocId = isset($resAppoint) ? ($resAppoint->doctor_name ?? '') : '';
    $prescriberDocId = mysqli_real_escape_string($conn, (string)$prescriberDocId);
    $docFilter = $prescriberDocId !== '' ? " AND doc_id='$prescriberDocId'" : '';
    $signature = mysqli_query($conn, "SELECT doctor_name,departments,security_id FROM doctors WHERE status='1' AND org_id='$org_id'$docFilter LIMIT 1") or die(mysqli_error($conn));
    $resignature = mysqli_fetch_object($signature);

    $secFilter = (!empty($resignature->security_id))
        ? " AND security_id='" . mysqli_real_escape_string($conn,(string)$resignature->security_id) . "'"
        : '';
    $sign = mysqli_query($conn, "SELECT signature_url FROM security WHERE status='1' AND org_id='$org_id'$secFilter LIMIT 1") or die(mysqli_error($conn));
    $resig = mysqli_fetch_object($sign);

    $dep = mysqli_query($conn, "SELECT departmentName FROM department WHERE status='1' AND dept_id='$resignature->departments'") or die(mysqli_error($conn));
    $resdep = mysqli_fetch_object($dep);

    $nameParts = explode(' ', ucwords(strtolower($PatientName)));
    $firstLine = implode(' ', array_slice($nameParts, 0, 2));
    $secondLine = implode(' ', array_slice($nameParts, 2));
?>

<!DOCTYPE html> 
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>HealthHub360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='shortcut icon' type='image/x-icon' href="assets/img/health.png" />
    
    <style>

      body {
        background: white; 
      }
     page {
          background: white;
          display: block;
          margin: 0 auto;
          margin-bottom: 0.5cm;
          position: relative; 
          font-family: 'Arial', serif;
          font-size: 12px; 
      }
      page[size="A4"] {  
        width: <?= $width ?>;
        height: <?= $height ?>; 
        margin-top: <?= $top ?>;
        margin-bottom: <?= $bottom ?>;
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
        padding: 30px 0;
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
        border-top: 1px solid  #5D6975;
        border-bottom: 1px solid  #5D6975;
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
        margin-bottom: 20px;
      }

      table th,
      table td {
        text-align: left;
      }

      table th {
        padding: 5px 20px;
        /* color: #5D6975; */
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
        text-align: left;
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
        border-top: 1px solid #5D6975;;
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
            border: 1px solid #ddd; 
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
            right: 15px; 
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #ddd; 
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
        }

        .vitals-table th, .vitals-table td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }

        .vitals-table th {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            font-weight: 700;
            background-color: #ddd;
        }

        .border-box {
          border: 1px solid #ddd;
          padding: 10px;
          margin-bottom: 10px;
          margin-left : 0px;
        }

        .border-boxes {
          border: 1px solid #ddd;
          padding: 10px;
          margin-bottom: 10px;
          margin-left : 0px;
        }

        .section-title {
            font-weight: bold; 
            font-size: 15px;
        }

        .h6{
          line-height: 0 !important;
          margin-bottom: 0px !important;
        }
        
      @media print {
        @page:first {
          margin-top: 0mm;
          margin-left: 10mm;
          margin-right: 10mm;
          margin-bottom: 25mm;
        }

        @page {
          margin-top: 60mm; 
          margin-left: 10mm;
          margin-right: 10mm;
          margin-bottom: 15mm;
        }

        body * {
          visibility: visible;
        }

        .noprint,
        button,
        nav,
        footer {
          display: none !important;
        }

        table, tr, td,{
          page-break-inside: avoid !important;
        }

      }

      @media print {
        table {
          border-collapse: collapse;
          /* width: 100%; */
        }
        tr {
          page-break-inside: avoid;
        }
        td, th {
          page-break-inside: avoid;
        }
      }

    </style>

  </head>
  <body>

  <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">

    <div class="d-flex justify-content-end mt-4 no-print print-button-wrapper">
      <div class="me-5">
        <button type="button" onclick="window.print()" class="btn btn-primary">Print</button>
      </div>
      
      <div id="toBeContinued" style="display:none; position: fixed; bottom: 10px; right: 20px; font-style: italic; font-size: 14px; color: #555;">
        To be continued…
      </div>
    </div>

    <page size="A4" id="A4">

      <header class="clearfix">
        <div id="logo"></div>
        <div id="company" class="clearfix">
          <div class="col-10"></div>
          <div class="col-2"></div>
        </div>
        <div id="project"></div>
      </header>
      <main>
        <div class="d-flex flex-row align-items-start flex-wrap" style="font-size:17px;">

          <div class="d-flex mt-2 align-items-center" style="flex-basis: 24%;">
              <div class="form-group w-100">
                  <label for="todaydate1" class="mb-0 ms-3" style="font-size:17px; font-weight: bold;">
                      Date :
                  </label>
                  <span><?= date("Y-m-d", strtotime($prescriptionData)); ?></span>
              </div>
          </div>

          <div class="d-flex mt-2 align-items-start" style="flex-basis: 40%; min-width: 0;">
              <div class="form-group w-100 ms-2" style="min-width: 0;">
                  <div style="font-size:17px; font-weight: bold; display:inline-block; vertical-align: top;">
                      Name :
                  </div>
                  <div style="display:inline-block; font-size:17px;">
                    <?= $firstLine ?><br>
                    <span><?= $secondLine ?></span>
                  </div>
              </div>
          </div>

          <div class="d-flex mt-2 align-items-center justify-end" style="flex-basis: 36%;">
            <div class="form-group w-100">
              <label for="place" class="mb-0 ms-5" style="font-size:17px; font-weight: bold;">
                  Gender/Age :
              </label>
              <?= (strtolower($resUid->gender) === 'female' ? 'F' : (strtolower($resUid->gender) === 'male' ? 'M' : 'O')) . " / " . $resUid->age ?> Y
            </div>
          </div>

        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card shadow-none" style="border: none !important;">
              <div class="card-body">
                <div class="row">
                  <div class="form-group col-lg-12">
                    <div class="info-box">
                      <div class="col-half">
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">Mobile</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold;">:</div>
                          <h6 style="margin-bottom:0px !important; font-size: 17px;"><?= $appdata->mobile_number; ?></h6>
                          <div class="info-value" id="mobile"></div>
                        </div>
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">Organisation</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold; font-weight: bold;">:</div>
                          <h6 style="margin-top:5px; margin-bottom:0px !important; font-size: 17px;"><?= strtoupper($resOrg->organization_name); ?></h6>
                          <div class="info-value" id="organization"></div>
                        </div>
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">UMR No</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold;">:</div>
                          <h6 style="margin-top:5px; margin-bottom:0px !important; font-size: 17px;"><?= strtoupper($appdata->patient_uid); ?></h6>
                          <div class="info-value" id="umr_no"></div>
                        </div>
                      </div>

                      <div class="col-half">
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">Appointment No</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold;">:</div>
                          <h6 style="margin-top:5px; margin-bottom:0px !important; font-size: 17px;"><?= strtoupper($appdata->appoint_register_id); ?></h6>
                          <div class="info-value" id="bill_no"></div>
                        </div>
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">Visit Dt</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold;">:</div>
                          <h6 style="margin-top:5px; margin-bottom:0px !important; font-size: 17px;"><?= strtoupper($appdata->appoint_date); ?></h6>
                          <div class="info-value" id="visit_dt"></div>
                        </div>
                        <div class="info-row">
                          <div style="font-size: 17px; font-weight: bold;">Review After Dt</div>
                          <div style="min-width:10px; font-size: 17px; font-weight: bold;">:</div>
                          <h6 style="margin-top:5px; margin-bottom:0px !important; font-size: 17px;"><?= strtoupper($appdata->reviewafterdate); ?></h6>
                          <div class="info-value" id="bill_dt"></div>
                        </div>
                      </div>
                    </div>
     
                    <?php if (
                              !empty($appdata->bpSit_systolic) ||
                              !empty($appdata->bpSit_diastolic) ||
                              !empty($appdata->bpStand_systolic) ||
                              !empty($appdata->bpStand_diastolic) ||
                              !empty($appdata->weight) ||
                              !empty($appdata->height) ||
                              !empty($appdata->grbs) ||
                              !empty($appdata->heart_rate) ||
                              !empty($appdata->spO2) ||
                              !empty($appdata->bmi)
                          ) : ?>
                            <div class="section-title">Vitals :</div>
                          <?php endif; ?>
                    <div class="form-group col-lg-12">
                      <table class="vitals-table" >
                       <thead style="font-weight: bold; background-color: #ddd;">
                          <tr>
                          <?php if (!empty($appdata->bpSit_systolic) && !empty($appdata->bpSit_diastolic)) : ?>
                              <th>BP Sit</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->bpStand_systolic) || !empty($appdata->bpStand_diastolic)) : ?>
                              <th>BP Stand</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->weight)) : ?>
                              <th>Weight</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->height)) : ?>
                              <th>Height</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->grbs)) : ?>
                              <th>GRBS</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->heart_rate)) : ?>
                              <th>Heart Rate</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->spO2)) : ?>
                              <th>spO2</th>
                            <?php endif; ?>
                            <?php if (!empty($appdata->bmi)) : ?>
                              <th>BMI</th>
                            <?php endif; ?>
                          </tr>
                        </thead>
                        <tbody style="text-weight: 800; font-size: 13px;">
                          <tr>
                          <?php if (!empty($appdata->bpSit_systolic) && !empty($appdata->bpSit_diastolic)) : ?>
                            <td><?= $appdata->bpSit_systolic;?>/<?= $appdata->bpSit_diastolic;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->bpStand_systolic) || !empty($appdata->bpStand_diastolic)) : ?>
                            <td><?= $appdata->bpStand_systolic;?>/<?= $appdata->bpStand_diastolic;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->weight)) : ?>
                            <td><?= $appdata->weight;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->height)) : ?>
                            <td><?= $appdata->height;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->grbs)) : ?>
                            <td><?= $appdata->grbs;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->heart_rate)) : ?>
                              <td><?= $appdata->heart_rate;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->spO2)) : ?>
                              <td><?= $appdata->spO2;?></td>
                            <?php endif; ?>
                            <?php if (!empty($appdata->bmi)) : ?>
                              <td><?= $appdata->bmi;?></td>
                            <?php endif; ?>
                          </tr>
                        </tbody>

                      </table>
                    </div>
     
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">

                      <?php if (!empty($appdata->finalDiagnosis)) : ?>
                        <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                          <p style="margin-bottom:0px;"><span class="section-title">Final Diagnosis :</span></p>
                          <div class="border-box" style="flex: 1;">
                            <div style="flex: 1;">
                              <p style="font-size:13px; margin-bottom: 0;"><?= strtoupper($appdata->finalDiagnosis); ?></p>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>

                      <?php if (!empty($appdata->chiefcomplaint)) : ?>
                        <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                          <p style="margin-bottom:0px;"><span class="section-title">Chief Complaint :</span></p>
                          <div class="border-box" style="flex: 1;">
                            <div style="flex: 1;">
                              <p style="font-size:13px; margin-bottom: 0;"><?= strtoupper($appdata->chiefcomplaint); ?></p>
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>

                    </div>
                    
                    <?php if (!empty($appdata->pasthistory)) : ?>
                      <div class="">
                      <p style="margin-bottom:0px;"><span class="section-title">Past History :</span></p>
                        <div class="border-boxes">
                          <div>
                            <p  style="font-size:13px; margin-bottom: 0;"><?= strtoupper($appdata->pasthistory); ?></p>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($appdata->patient_data)) : ?>
                      <div class="">
                      <p style="margin-bottom:0px;"><span class="section-title">Patient Data :</span></p>
                        <div class="border-boxes">
                          <div>
                            <p style="font-size:13px; margin-bottom:0; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($appdata->patient_data)); ?></p>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ($hasMedicine) : ?>
                        <div class="section-title">Medicine:</div>
                          <div class="form-group col-lg-12">
                            <table class="vitals-table">
                              <thead>
                                <tr>
                                  <?php
                                  $first = $getallprescripitionData[0];

                                  if (!empty($first['medicine_name'])) echo "<th>Medicine</th>";
                                  if (!empty($first['duration_value'])) echo "<th>Dosage</th>";
                                  if (!empty($first['duration'])) echo "<th>In-take-period</th>";
                                  if (!empty($first['timeText'])) echo "<th>Duration</th>";
                                  ?>
                                </tr>
                              </thead>
                              <tbody style="font-weight: 500; font-size: 13px;">
                                <?php foreach ($getallprescripitionData as $medicine) : ?>
                                  <tr>
                                    <?php if (!empty($first['medicine_name']) || !empty($first['type_text']) || !empty($first['unit_id'])) : ?>
                                      <td>
                                        <?php if (!empty($medicine['type_text']) || !empty($medicine['medicine_name'])) : ?>
                                          <div><?= htmlspecialchars($medicine['type_text']) . " - " . htmlspecialchars($medicine['medicine_name']); ?></div>
                                        <?php endif; ?>

                                        <?php if (!empty($medicine['unit_text'])) : ?>
                                          <div><span style="font-size: 13px;">Unit/Vol.</span> - <span style="font-size: 13px;"><?= htmlspecialchars($medicine['unit_text']); ?></span></div>
                                        <?php endif; ?>
                                      </td>
                                    <?php endif; ?>
                                    <?php if (!empty($first['timeText'])  || !empty($first['dosageText'])) : ?>
                                      <td>
                                        <?=
                                          (!empty($medicine['dosageText']) ? htmlspecialchars($medicine['dosageText']) . '<br>' : '') .
                                          (!empty($medicine['timeText'])
                                            ? '<span style="font-size: 12px;">(' . htmlspecialchars(
                                                implode(' - ',
                                                  array_filter(
                                                    array_map('trim', explode('-', $medicine['timeText'])),
                                                    fn($val) => $val !== '0'
                                                  )
                                                )
                                              ) . ')</span>'
                                            : ''
                                          )
                                        ?>
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
                        </div>
                      <?php endif; ?>
                      <?php if ($hasTest) : ?>
                        <div class="section-title mt-3">Tests:</div>
                        <div class="form-group col-lg-12">
                          <table class="vitals-table">
                            <thead>
                              <tr>
                                <th>S.No</th>
                                <th>Test Name</th>
                                <th>Instructions</th>
                              </tr>
                            </thead>
                            <tbody style="font-weight: 500; font-size: 13px;">
                              <?php 
                                $serial = 1;
                                foreach ($getallprescripitiontestData as $test) : ?>
                                  <tr>
                                    <td><?= $serial++; ?></td>
                                    <td><?= !empty($test['test_name']) ? htmlspecialchars($test['test_name']) : '' ?></td>
                                    <td><?= !empty($test['instruction']) ? htmlspecialchars($test['instruction']) : '' ?></td>
                                  </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      <?php endif; ?>

                      <div style="text-align: right; margin-top: 50px; padding-right: 30px;">
                        <?php
                          $signature_url = $resig->signature_url ?? '';
                          if (!empty($signature_url)) {
                              echo "<img src='signature/$signature_url' alt='Digital Signature' style='max-width: 300px; max-height: 100px;' />";
                          }

                          $doctorName = !empty($resignature->doctor_name) ? $resignature->doctor_name : 'Ashwin Kumar Pandas';
                        ?>
                        <div class="info-label" style="font-weight: bold; font-size: 16px;">
                          <?= htmlspecialchars($doctorName); ?>
                        </div>
                        <div class="info-label" style="font-weight: bold; font-size: 16px;">
                          <?= htmlspecialchars($resdep->departmentName); ?>
                        </div>

                      </div>

                    </div>    
    </page>
  </form>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
      const mainContent = document.querySelector('#A4');
      let topMargin = parseInt(getURLParam('topMargin')) || 100;
      let bottomMargin = parseInt(getURLParam('bottomMargin')) || 0;

      mainContent.style.marginTop = topMargin + 'px';
      mainContent.style.marginBottom = bottomMargin + 'px';

      const increaseTopMarginButton = document.getElementById('increase-top-margin');
      const decreaseTopMarginButton = document.getElementById('decrease-top-margin');

      increaseTopMarginButton.addEventListener('click', function() {
        topMargin += 10;
        mainContent.style.marginTop = topMargin + 'px';
        updateURLParams();
      });

      decreaseTopMarginButton.addEventListener('click', function() {
        if (topMargin > 100) {
          topMargin -= 10;
          mainContent.style.marginTop = topMargin + 'px';
          updateURLParams();
        }
      });

      const increaseBottomMarginButton = document.getElementById('increase-bottom-margin');
      const decreaseBottomMarginButton = document.getElementById('decrease-bottom-margin');

      increaseBottomMarginButton.addEventListener('click', function() {
        bottomMargin += 10;
        mainContent.style.marginBottom = bottomMargin + 'px';
        updateURLParams();
      });

      decreaseBottomMarginButton.addEventListener('click', function() {
        if (bottomMargin > 0) {
          bottomMargin -= 10;
          mainContent.style.marginBottom = bottomMargin + 'px';
          updateURLParams();
        }
      });

      function getURLParam(paramName) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(paramName);
      }

      function updateURLParams() {
        const url = new URL(window.location.href);
        url.searchParams.set('topMargin', topMargin);
        url.searchParams.set('bottomMargin', bottomMargin);
        history.pushState({}, '', url);
      }

    </script>
  </body>
</html>