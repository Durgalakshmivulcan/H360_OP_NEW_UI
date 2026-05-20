
<?php
  require_once("config/config.php");

//  $SessionUserId = $_SESSION['security_id'] ?? '';
//   $SessionRoleId = $_SESSION['role_id'] ?? '';
//   $SessionOrgId = $_SESSION['org_id'] ?? '';

 
  
 

// Decryption function
function decryptData($data, $key) {
    $decoded = base64_decode($data);
    if (!$decoded || strpos($decoded, '::') === false) {
        return false;
    }
    list($encrypted_data, $iv) = explode('::', $decoded, 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

// Define your secret key (should match the encryption key used when encrypting)
$encryptionKey = "YourSecretKey123!";

// Get encrypted values from URL
$encryptedItemId = $_GET['ItemId'] ?? '';
$encryptedOrgId = $_GET['OrgID'] ?? '';

// Decrypt values
$prescriptionId = decryptData($encryptedItemId, $encryptionKey);
$orgId = decryptData($encryptedOrgId, $encryptionKey);

if (!$prescriptionId || !$orgId) {
    echo "Invalid or tampered data.";
    exit;
}

// Fetch prescription and appointment details from the database
// $query = "SELECT p.*, ao.appoint_register_id 
//           FROM prescripition p 
//           LEFT JOIN appointment_online ao 
//           ON p.patient_vitals = ao.appoint_register_id AND p.org_id = ao.org_id 
//           WHERE p.prescription_id = ? AND p.org_id = ?";

// $stmt = $conn->prepare($query);
// $stmt->bind_param("ii", $prescriptionId, $orgId);
// $stmt->execute();
// $result = $stmt->get_result();
// $data = $result->fetch_assoc();

$query = mysqli_query($conn, "SELECT p.*, ao.appoint_register_id 
          FROM prescripition p 
          LEFT JOIN appointment_online ao 
          ON p.patient_vitals = ao.appoint_register_id AND p.org_id = ao.org_id 
          WHERE p.prescription_id = '$prescriptionId' AND p.org_id = '$orgId'") or die(mysqli_error($conn));

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "No prescription found.";
    exit;
}

// Store values in variables
$prescriptionId = $data['prescription_id'];
$patientName = $data['patient_name'];
$createDateTime = $data['create_date_time'];
$appointRegisterId = $data['appoint_register_id'];
$orgId = $data['org_id'];


  

  
  $id=$_GET['ItemId'];
  $orgid=$_GET['OrgID'];

  // $getPresMedicines=mysqli_query($conn, "SELECT * FROM prescription_medicines  WHERE status='1' AND  prescription_id='$id'") or die(mysqli_error($conn));
  // $resPresMedic = mysqli_fetch_object($getPresMedicines);
  // $prescriptionID=$resPresMedic->prescription_id;

  $getUid=mysqli_query($conn,"SELECT * FROM prescripition WHERE status='1' AND prescription_id='$prescriptionId' AND org_id ='$orgId'") or die(mysqli_error($conn));
  $resUid=mysqli_fetch_object($getUid);
  $patientID=$resUid->patient_uid;
  $Appointment_ID=$resUid->appoint_register_id;
  $PAtient_Age=$resUid->age;
  $Gender=$resUid->gender;
  $orgID=$resUid->org_id;
  $prescriptionData=$resUid->create_date;
   

  $patientvitalsID=$_Get['patient_vitals'];
  
 
  $app = mysqli_query($conn, "SELECT * FROM prescripition p INNER JOIN appointment_online a   ON p.appoint_register_id = a.appoint_register_id  WHERE p.patient_uid = '$patientID'  AND p.prescription_id='$prescriptionId' AND p.org_id ='$orgId'  AND a.org_id = '$orgId'") or die(mysqli_error($conn));
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
  $getallprescripitionreportData=[];

 if (!empty($appdata->medicine_id)) {
    $decodedMedicines = json_decode($appdata->medicine_id, true);
    $decodedTests = json_decode($appdata->test_id, true);
    // $decodedReports = json_decode($appdata->images, true); // This is an array of image file names
    // echo $decodedMedicines;
    // echo $decodedTests;
    // echo $appdata->images;
    // echo $decodedReports;

    // Store decoded data if they are arrays
    if (is_array($decodedMedicines)) {
        $getallprescripitionData = $decodedMedicines;
    }
    if (is_array($decodedTests)) {
        $getallprescripitiontestData = $decodedTests;
    }
    
}


  $ogd = mysqli_query($conn, "SELECT * FROM organization AS o INNER JOIN prescripition AS p ON p.org_id = o.org_id WHERE o.org_id = '$orgId'") or die(mysqli_error($conn));
  $resOrg = mysqli_fetch_object($ogd);
  $orgName = $resOrg->organization_name	;
  $orgAddress =$resOrg->address	;
  $org_id = $resOrg->org_id;
  // echo $orgName;
  // echo "SELECT * FROM organization AS o INNER JOIN prescripition AS p ON p.org_id = o.org_id WHERE o.org_id = '$orgID'";

  $getAppointOnline = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_unicode='$patientID' AND age='$PAtient_Age' AND gender='$Gender'  AND org_id ='$orgId'") or die(mysqli_error($conn));

  while ($resAppoint = mysqli_fetch_object($getAppointOnline)) {

    $PatientName = $resAppoint->patient_name;
    $systolic = $resAppoint->systolic;
    $diastolic = $resAppoint->diastolic;

    if (!empty($resAppoint->patient_history)) {
        $decodedReports = explode(',', $resAppoint->patient_history);
        $getallprescripitionreportData = array_merge($getallprescripitionreportData, $decodedReports);
    }
}

  $getAppointExisting = mysqli_query($conn, "SELECT * FROM appointment_existing WHERE appoint_register_id='$appointRegisterId' AND appoint_unicode='$patientID' AND age='$PAtient_Age' AND gender='$Gender'  AND org_id ='$orgId'") or die(mysqli_error($conn));

  while ($resAppoint = mysqli_fetch_object($getAppointExisting)) {
      $PatientName= $resAppoint->patient_name;
      $systolic= $resAppoint->systolic;
      $diastolic= $resAppoint->diastolic;
  }

      
  $getSizes = mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' AND org_id='$resAppoint->org_id'");
  $resData = mysqli_fetch_object($getSizes);
  
  $top = "0px";
  $bottom = "0px";
  
  if (!empty($resData->top)) {
      $top = $resData->top;
  }
  
  if (!empty($resData->bottom)) {
      $bottom = $resData->bottom;
  }
  
  $getSingleSize = mysqli_query($conn, "SELECT w_size, h_size FROM pagessize WHERE status='1' AND size_id='$resData->sizes'");
  $resSingleData = mysqli_fetch_object($getSingleSize);
  
  $width = '21cm';
  // $height = '29.7cm';
  
  if (!empty($resSingleData->w_size)) {
      $width = $resSingleData->w_size;
  }
  
  // if (!empty($resSingleData->h_size)) {
  //     $height = $resSingleData->h_size;
  // }
  $hasMedicine = !empty($getallprescripitionData);
  $hasTest= !empty($getallprescripitiontestData);
  $hasReport=!empty($getallprescripitionreportData);
  
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
        /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
        position: relative; 
        font-family: "Gill Sans Extrabold", sans-serif;
        font-size: 12px; 
        /* font-family: Arial; */
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
            background-color: black; /* Continuous vertical separator */
        }
        
        .info-row {
          display: flex;
          align-items: center;
        }
        .info-label {
            /* font-weight: bold; */
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
            padding: 0px;
            text-align: center;
        }
        .vitals-table th {
            font-weight: bold;
            font-size: 16px;
        }
        th {
            /* background-color: #ddd; */
        }
        .border-box {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
            margin-left : 0px;
        }
        .section-title {
            font-weight: bold;
            font-size: 17px;
        }
        .h6{
          line-height: 0 !important;
          margin-bottom: 0px !important;
        }
        
        @media print {
          .no-print {
            display: none !important;
          }
        }

      .print-button-wrapper {
        position: sticky;
        top: 10px;
        z-index: 1000;
        background: white;
      }
    </style>

  </head>
  <body>
  <!-- <h2>Prescription Details</h2>
    <p><strong>Prescription ID:</strong> <?= htmlspecialchars($prescriptionId) ?></p>
    <p><strong>Patient Name:</strong> <?= htmlspecialchars($patientName) ?></p>
    <p><strong>Date Created:</strong> <?= htmlspecialchars($createDateTime) ?></p>
    <p><strong>Appointment Register ID:</strong> <?= htmlspecialchars($appointRegisterId) ?></p>
    <p><strong>Organization ID:</strong> <?= htmlspecialchars($orgId) ?></p> -->


  <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">

  
  <!-- <div class="d-flex justify-content-end mt-4 no-print print-button-wrapper">
  <div class="me-5">
    <button type="button" onclick="window.print()" class="btn btn-primary">Print</button>
  </div>
</div> -->

    <page size="A4" id="A4">

    <header class="clearfix d-flex justify-content-between align-items-start">
      <!-- Logo Section -->
      <div class="ms-4" id="logo">
          <?php
          $uploadDir = __DIR__ . '/../../organisation_images/';
          $defaultLogo = '<img alt="Organization Logo" src="assets/img/h360.png" style="margin-top: 30px;" width="150" height="50">';

          if ($encryptedOrgId != "0") {
              $getLogoQuery = "SELECT logo FROM organization WHERE org_id = '$orgId' AND status = '1'";
              $result = mysqli_query($conn, $getLogoQuery);

              if ($result && $org = mysqli_fetch_assoc($result)) {
                  $logoFileName = $org['logo'];
                  if (!empty($logoFileName)) {
                      echo '<img alt="Organization Logo" src="organisation_images/' . $logoFileName . '" style="margin-top: 30px;width:170px;height:100px;">';
                  } else {
                      echo $defaultLogo;
                  }
              } else {
                  echo $defaultLogo;
              }
          } else {
              echo $defaultLogo;
          }
          ?>
      </div>
      <div id="project" style="width: 310px; text-align: right;">
          <h4 style="padding-top: 36px;"><?= $orgName ?? '' ?></h4>
          <p><?= $orgAddress ?? '' ?></p>
      </div>
      <!-- Patient Info Section -->
      <div id="company" style="text-align: left;">
          <table>
              <tr>
                  <td style="padding-right: 10px;">
                      <b>Date:-</b><br>
                      <b>Name:-</b><br>
                      <b>Age/Sex:-</b>
                  </td>
                  <td>
                      <?= date("Y-m-d", strtotime($prescriptionData)); ?><br>
                      <?= strtoupper($PatientName); ?><br>
                      <strong><?= date("Y-m-d", strtotime($prescriptionData)); ?></strong>
                  </td>
              </tr>
          </table>
      </div>

      <!-- Organization Info Section -->
      
  </header>

      <main>
      <div class="d-flex flex-row align-items-center flex-wrap" style="column-gap: 20px;font-size:16px;">
  
          <!-- Name (double width) -->
          <div class="d-flex mt-2 align-items-center " style="flex-basis: 35%;">
            <div class="form-group w-100">
              <label for="patient_name" class="mb-0 ms-3" >
                <strong class="info-label" style="font-size:18px;">Name:</strong>
                <span style="font-weight: bold;"><?= strtoupper($PatientName); ?></span>
              </label>
            </div>
          </div>

          <!-- Date -->
          <div class="d-flex mt-2 align-items-center " style="flex-basis: 24%;">
            <div class="form-group w-100">
              <label for="todaydate1" class="mb-0">
                <strong class="info-label"  style="font-size:18px;">Date:</strong>
                <span style="font-weight: bold;"><?= date("Y-m-d", strtotime($prescriptionData)); ?></span>
              </label>
            </div>
          </div>

          <!-- Gender/Age -->
          <div class="d-flex mt-2 align-items-center " style="flex-basis: 35%;">
            <div class="form-group w-100">
              <label for="place" class="mb-0 ms-3">
                <strong class="info-label"  style="font-size:18px;">Gender/Age:</strong>
                <span style="font-weight: bold;"><?= strtoupper($resUid->gender) . " / " . $resUid->age ?> Y</span>
              </label>
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
                                <div class="info-label"><strong>Mobile</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px; margin-bottom:0px !important;"><?= $appdata->mobile_number;?></h6>
                                <div class="info-value" id="mobile"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Organisation</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px ;margin-bottom:0px !important;"><?= strtoupper($resOrg->organization_name);?></h6>
                                <div class="info-value" id="organization"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>UMR No</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px; margin-bottom:0px !important;"><?= strtoupper($appdata->patient_uid);?></h6>
                                <div class="info-value" id="umr_no"></div>
                            </div>
                          </div>
                          <div class="col-half">
                            <div class="info-row">
                                <div class="info-label"><strong>Bill No</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px; margin-bottom:0px !important;"><?= strtoupper($appdata->bill_id);?></h6>
                                <div class="info-value" id="bill_no"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Visit Dt</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px; margin-bottom:0px !important;"><?= strtoupper($appdata->appoint_date);?></h6>
                                <div class="info-value" id="visit_dt"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Bill Dt</strong></div><div class="info-label" style="min-width:10px;"><strong>:</strong></div><h6 style="margin-top:5px; margin-bottom:0px !important;"><?= strtoupper($appdata->reviewafterdate);?></h6>
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
                            <thead style="text-weight: 800;">
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
                            <tbody style="text-weight: 800;">
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

                             
                        <?php if (!empty($appdata->finalDiagnosis)) : ?>
                          <div class="">
                            <p style="margin-bottom:0px;"><span class="section-title">Final Diagnosis :</span></p>
                            <div class="border-box">
                              <div>
                                <p style="font-size:15px;"><?= strtoupper($appdata->finalDiagnosis); ?></p>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>

                        <?php if (!empty($appdata->chiefcomplaint)) : ?>
                          <div class="">
                          <p style="margin-bottom:0px;"><span class="section-title">Chief Complaint :</span></p>
                            <div class="border-box">
                              <div>
                                <p style="font-size:15px;"><?= strtoupper($appdata->chiefcomplaint);?></p>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>
                        <?php if (!empty($appdata->pasthistory)) : ?>
                          <div class="">
                          <p style="margin-bottom:0px;"><span class="section-title">Past History :</span></p>
                            <div class="border-box">
                              <div>
                                <p  style="font-size:15px;"><?= strtoupper($appdata->pasthistory); ?></p>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>
                        <?php if ($hasMedicine) : ?>
                            <div class="section-title">Medicine:</div>
                            <div class="form-group col-lg-12" style="text-weight: 800;font-size:14px;">
                              <table class="vitals-table">
                                <thead style=" background-color: #ddd;">
                                  <tr>
                                    <?php
                                    // Use the first item to check available keys for header display
                                    $first = $getallprescripitionData[0];

                                    if (!empty($first['medicine_name'])) echo "<th>Medicine Name</th>";
                                    if (!empty($first['type_text'])) echo "<th>Type</th>";
                                    // if (!empty($first['unit_id'])) echo "<th>Unit</th>";
                                    if (!empty($first['unit_text'])) echo "<th>Unit</th>";
                                    if (!empty($first['duration_value'])) echo "<th>Dosage</th>";
                                    if (!empty($first['duration'])) echo "<th>In-take-period</th>";
                                    // if (!empty($first['notes'])) echo "<th>When Text</th>";
                                    if (!empty($first['timeText'])) echo "<th>Duration</th>";
                                    // if (!empty($first['dosageText'])) echo "<th>Dosage Text</th>";
                                    // if (!empty($first['WhenText'])) echo "<th>When Text</th>";
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

                                      <!-- <?php if (!empty($first['unit_text'])) : ?>
                                        <td><?= htmlspecialchars($medicine['unit_text']); ?></td>
                                      <?php endif; ?> -->
                                      <?php if (!empty($first['timeText'])  || ! empty($first['dosageText'])) : ?>
                                        <td><?= 
                                          (!empty($medicine['timeText'])? htmlspecialchars($medicine['timeText']) . '<br>': '') .
                                          (!empty($medicine['dosageText']) ?htmlspecialchars($medicine['dosageText']) : '')
                                           ?></td>
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
                                      <!-- <?php if (!empty($first['notes'])) : ?>
                                        <td><?= htmlspecialchars($medicine['notes']); ?></td>
                                      <?php endif; ?> -->
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                          <?php endif; ?>
                          <?php if ($hasTest) : ?>
                            <div class="section-title">Tests:</div>
                            <div class="form-group col-lg-12" style="text-weight: 800;font-size:14px;">
                              <table class="vitals-table">
                                <thead style=" background-color: #ddd;">
                                  <tr>
                                    <?php
                                    // Use the first item to check available keys for header display
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
                        <?php if ($hasReport) : ?>
                            <div class="section-title">Reports:</div>
                            <div class="form-group col-lg-12" style="font-weight: 800; font-size: 14px;">
                              <?php foreach ($getallprescripitionreportData as $image) : ?>
                                <?php
                                  if (!empty(trim($image))) :
                                    $filePath = "ajax/Testimages/" . htmlspecialchars($image);
                                    $fileExt = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                                ?>
                                  <div style="margin: 20px auto; width: 750px; height: 800px; border: 1px solid #ccc; padding: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">
                                    <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) : ?>
                                      <img src="<?php echo $filePath; ?>" alt="reports"
                                          style="width: 100%; height: 100%; object-fit: contain;">
                                    <?php elseif ($fileExt === 'pdf') : ?>
                                      <embed src="<?php echo $filePath; ?>" type="application/pdf"
                                            width="100%" height="100%" />
                                    <?php endif; ?>
                                  </div>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </div>
                          <?php endif; ?>


                      </div>
          
        <!-- <div><b>ADVICE:</b></div>
        <div class="notice"><?php echo $result?></div>
      </div>
      </main> -->
      <!-- <footer id="hai"></footer> -->
      <!-- <div class="row">
        <div class="col-10" ></div>
        <div class="col-2" >
        </div> -->
      <!-- </div> -->
    </page>
  </form>

 

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
  <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
  <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/xlsx.mini.min.js"></script>

    <script>
      const mainContent = document.querySelector('#A4');
      let topMargin = parseInt(getURLParam('topMargin')) || 100;
      let bottomMargin = parseInt(getURLParam('bottomMargin')) || 0;

      mainContent.style.marginTop = topMargin + 'px';
      mainContent.style.marginBottom = bottomMargin + 'px';

      // Button events for top margin adjustment
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

      // Button events for bottom margin adjustment
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