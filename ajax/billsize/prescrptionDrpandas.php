<?php
  require_once("../../config/functions.php");

  $SessionUserId = $_SESSION['security_id'] ?? '';
  $SessionRoleId = $_SESSION['role_id'] ?? '';
  $SessionOrgId = $_SESSION['org_id'] ?? '';

  $organizations1 = $_REQUEST['organizations1'];

  $getSizes=mysqli_query($conn,"SELECT sizes FROM bill_sizes WHERE status='1' AND pagetype='1' AND org_id='$SessionOrgId'");
  $resData=mysqli_fetch_object($getSizes);

  $getSingleSize=mysqli_query($conn,"SELECT w_size, h_size FROM pagessize WHERE size_id='$resData->sizes' AND status='1'");
  $resSingleData=mysqli_fetch_object($getSingleSize); 

    $width = '21cm';
    $height = '29.7cm';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>HealthHub360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='shortcut icon' type='image/x-icon' href="../../assets/img/health.png" />
    <style>

      @media print {
        .button-class {
          display: none;
        }
      }
    
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
        font-family: Arial, sans-serif; 
        font-size: 12px; 
        font-family: Arial;
      }
      page[size="A4"] {  
        width: <?= $width ?>;
        height: <?= $height ?>; 
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
            justify-content: space-between;
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #02458B;
            min-width: 100px;
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
            padding: 8px;
            text-align: center;
        }
        .vitals-table th {
            font-weight: bold;
        }

        .consultant{
          font-size:14px;
        }

        .doc-name {
            font-size: 18px;
            font-weight: bold;
        }
        .doc-details {
            font-size: 14px;
            color: #333;
        }
        .specialty {
            font-size: 14px;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
        }
        .border-box {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
        hr {
            border-top: 2px solid black;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
        }
        .footer-line {
            border-top: 2px solid black;
            margin-top: 10px;
            padding-top: 5px;
        }
        .footer-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        .card {
          --bs-card-spacer-y: 1rem;
          --bs-card-spacer-x: 0rem !important;
        }

        @media print {
            #clearprint {
                display: none;
            }
        }
        .row {
          --bs-gutter-x: 0rem !important;
        }
        .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
          --bs-gutter-x: 0rem !important;
        }
    </style>
  </head>
  <body>
    <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">
      <input type="hidden" name="organizations1" id="organizations1" value="<?php echo $organizations1 ?>">

      <?php
        $addorgid= "AND org_id='$SessionOrgId'";

        if($SessionUserId == "1"){
        $addorgid="";
        }

        $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' $addorgid ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
        $resSize=mysqli_fetch_object($Getpages1);

      ?>
      <?php

        // $getorg=mysqli_query($conn,"SELECT * FROM bill_sizes WHERE status='1' ");
        // while ($row = mysqli_fetch_object($getorg)) { 
        // $org_id=$row->org_id;
        // }
      ?>
      <!-- <input type="hidden" name="org_id" id="org_id" value="<?php echo $org_id ?>"> -->

      <?php
        $org_id=$_GET['Org_id'];
        $appointment_id=$_GET['appointment_id'];

        // $getPresMedicines=mysqli_query($conn, "SELECT * FROM prescription_medicines  WHERE status='1' AND  prescription_id='$id'") or die(mysqli_error($conn));
        // $resPresMedic = mysqli_fetch_object($getPresMedicines);

        $getPres= mysqli_query($conn,"SELECT * FROM  prescripition WHERE org_id='$org_id' AND appoint_register_id='$appointment_id'") or die(mysqli_error($conn));

        $res5=mysqli_fetch_object($getPres);
        $testIDS=$res5->test_id;
        $id=$res5->prescription_id;

        $getappnt= mysqli_query($conn,"SELECT * FROM  appointment_online WHERE appoint_register_id='$appointment_id'") or die(mysqli_error($conn));
        $res6=mysqli_fetch_object($getappnt);

        $getorg= mysqli_query($conn,"SELECT * FROM  organization WHERE org_id='$org_id'") or die(mysqli_error($conn));
        $res7=mysqli_fetch_object($getorg);

        $getvitals= mysqli_query($conn,"SELECT * FROM  vitals WHERE appointment_id='$appointment_id'") or die(mysqli_error($conn));
        $res8=mysqli_fetch_object($getvitals); 

        $getdoc= mysqli_query($conn,"SELECT * FROM  doctors WHERE doc_id='$res6->doctor_name'") or die(mysqli_error($conn));
        $res9=mysqli_fetch_object($getdoc);

        $getdept= mysqli_query($conn,"SELECT * FROM  department WHERE dept_id='$res9->departments'") or die(mysqli_error($conn));
        $res10=mysqli_fetch_object($getdept);

        $getTests=mysqli_query($conn,"SELECT * FROM  tests WHERE test_id='$testIDS'") or die(mysqli_error($conn));
        $res11=mysqli_fetch_object($getTests);
          $test=$res11->test_name; 
          $test_id_array = array_map('intval', explode(',', $testIDS));
          $test_id_string = "'" . implode("','", $test_id_array) . "'";

          $query = "SELECT * FROM tests WHERE test_id IN ($test_id_string)";
          $getTests = mysqli_query($conn, $query) or die(mysqli_error($conn));
          $testsArray = [];
            while ($res12 = mysqli_fetch_object($getTests)) {
              $testsArray[] = [
                "test_name" => $res12->test_name,
                "test_price" => $res12->test_price
              ];
            }

      ?>

      <input type="hidden" name="size_id1" id="size_id1" value="<?php echo $resSize->bill_size_id ?>">
      <page size="A4" id="A4">
        <header class="clearfix">
          <div id="logo">
            <!-- <img src="logo.png"> -->
          </div>
          <!-- <h1>INVOICE 3-2-1</h1> -->
          <!--<div id="company" class="clearfix">
            <div>Company Name</div>
            <div>455 Foggy Heights,<br /> AZ 85004, US</div>
            <div>(602) 519-0450</div>
            <div><a href="mailto:company@example.com">company@example.com</a></div>
            <div class="col-10"></div>
            <div class="col-2">
              <button id="increase-top-margin" class="btn btn-primary button-class" onclick="insertmargine1()">
                <i class="fas fa-plus-square"></i>+
              </button>
              <button id="decrease-top-margin" class="btn btn-primary button-class" onclick="insertmargine1()">
                <i class="bi bi-dash-square"></i>-
              </button>
            </div>
          </div>-->
          <div id="project">
            <!-- <div><span>PROJECT</span> Website development</div>
            <div><span>CLIENT</span> John Doe</div>
            <div><span>ADDRESS</span> 796 Silver Harbour, TX 79273, US</div>
            <div><span>EMAIL</span> <a href="mailto:john@example.com">john@example.com</a></div>
            <div><span>DATE</span> August 17, 2015</div>
            <div><span>DUE DATE</span> September 17, 2015</div> -->
          </div>
        </header>
        <main style="margin-top:230px;margin-bottom:115px">
        <div class="card mt-5" style="border: none !important;">
          <div class="card-body">
            <?php
              if ($SessionUserId == "1" && $SessionRoleId == "1") {
            ?>
            <div class="row" >
              <!-- <div class="form-group col-lg-3 col-sm-12">
                <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="OrgIdByPatientNames()">
                  <option value="">Select Organization</option>
                    <?php
                      $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                      while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                    ?>
                      <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                    <?php
                      }
                    ?>
                </select>
              </div> -->
              <div class="d-flex flex-row align-items-center" style="column-gap: 20px;">
              <div class="d-flex align-items-center">
                  <div class="form-group me-2">
                    <label for="patientName" class="mb-0">Name:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                    <p id="patientName" class="mb-0"></p>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="form-group me-2">
                    <label for="todaydate" class="mb-0">Date:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                    <p id="todaydate" class="mb-0"></p>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="form-group me-2">
                    <label for="place" class="mb-0">Place:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                    <p id="place" class="mb-0">Visakhapatnam</p>
                  </div>
                </div>
              </div>

            <?php
            } else{
              $GetUserOrganization = mysqli_query($conn, "SELECT org_id FROM organization WHERE org_id='$SessionOrgId' AND status='1'") or die(mysqli_error($conn));
              $ResUserOrg = mysqli_fetch_object($GetUserOrganization);
            ?>    
            <div class="d-flex flex-row align-items-center" style="column-gap: 20px;">
              <input type="hidden" name="organizations" id="organizations" value="<?= $ResUserOrg->org_id ?>">
              <div class="d-flex mt-2 align-items-center">
                  <div class="form-group me-2">
                    <label for="patient_name" class="mb-0">Name:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                    <p id="patient_name" class="mb-0"></p>
                  </div>
                </div>  
              <div class="d-flex mt-2 align-items-center">
                  <div class="form-group me-2">
                    <label for="todaydate1" class="mb-0">Date:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                  <p id="todaydate1" class="mb-0"></p>
                    <!-- <input type="date" name="todaydate" id="todaydate" class="form-control"> -->
                  </div>
                </div>
                <div class="d-flex mt-2 align-items-center">
                  <div class="form-group me-2">
                    <label for="place">Place:</label>
                  </div>
                  <div class="form-group" style="width: 200px;">
                  <p id="place" class="mb-0"></p>
                    <!-- <input type="text" name="place" id="place" class="form-control" value="Visakhapatnam"> -->
                  </div>
                </div>
            </div>
            <?php
            }
            ?>
            <div class="row mt-2">
              <div class="col-lg-12">
                <div class="card shadow-none" style="border: none !important;">
                  <div class="card-body">
                    <div class="row">
                      <div class="form-group col-lg-12">
                        <div class="info-box">
                          <div class="col-half">
                            <!-- <div class="info-row">
                                <div class="info-label"><strong>Patient Name:</strong></div>
                                <div class="info-value" id="patient_name"></div>
                            </div> -->
                            <div class="info-row">
                                <div class="info-label"><strong>Age/Gender:</strong></div>
                                <div class="info-value" id="age_gender"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Mobile:</strong></div>
                                <div class="info-value" id="mobile"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Organisation:</strong></div>
                                <div class="info-value" id="organization"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>UMR No:</strong></div>
                                <div class="info-value" id="umr_no"></div>
                            </div>
                          </div>
                          <div class="col-half">
                            <div class="info-row">
                                <div class="info-label"><strong>Bill No:</strong></div>
                                <div class="info-value" id="bill_no"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Visit Dt:</strong></div>
                                <div class="info-value" id="visit_dt"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Bill Dt:</strong></div>
                                <div class="info-value" id="bill_dt"></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label"><strong>Visit Type:</strong></div>
                                <div class="info-value" id="visit_type">---</div>
                            </div>
                          </div>
                        </div>
                        <h6 class="mt-2"><strong>Vitals :</strong></h6>
                        <div class="form-group col-lg-12">
                          <table class="vitals-table">
                              <thead>
                                  <tr>
                                      <th>BP Sit</th>
                                      <th>BP Stand</th>
                                      <th>Weight</th>
                                      <th>Height</th>
                                      <th>GRBS</th>
                                      <th>Heart Rate</th>
                                      <th>Temp</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td><span id="bp_sit"></span></td>
                                      <td><span id="bp_stand"></span></td>
                                      <td><span id="weight"></span></td>
                                      <td><span id="height"></span></td>
                                      <td><span id="GRBS"></span></td>
                                      <td><span id="heartrate"></span></td>
                                      <td><span id="temperature"></span></td>
                                  </tr>
                              </tbody>
                          </table>
                        </div>
                        <div class="form-group col-lg-12">
                          <table class="vitals-table">
                              <thead>
                                  <tr>
                                      <th>SP02</th>
                                      <th>Resp / min</th>
                                      <th>Blood Group</th>
                                      <th>CPAP</th>
                                      <th>HFNC</th>
                                      <th>VO2</th>
                                      <th>BMI Value</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td><span id="sp02percent"></span></td>
                                      <td><span id="resp"></span></td>
                                      <td><span id="bloodgroup"></span></td>
                                      <td><span id="CPAP"></span></td>
                                      <td><span id="HFNC"></span></td>
                                      <td><span id="VO2"></span></td>
                                      <td><span id="bmi"></span></td>
                                  </tr>
                              </tbody>
                          </table>
                        </div>
                        
                        <div class="container-fluid">
                          <p class="section-title mb-1">Final Diagnosis :</p>
                          <div class="border-box">
                            <?php if (!empty($testsArray)) : ?>
                              <ul>
                                <?php foreach ($testsArray as $test) : ?>
                                  <li><?= htmlspecialchars($test['test_name']) ?> - <i class="bi bi-currency-rupee"></i><?= htmlspecialchars($test['test_price']) ?></li>
                                <?php endforeach; ?>
                              </ul>
                            <?php else : ?>
                            <p>No tests available.</p>
                            <?php endif; ?>
                          </div>
                          <!-- <p class="section-title">Chief Complaint :</p>
                          <input type="text" class="form-control" value="ON AND OFF CHEST DISCOMFORT+" readonly>
                          <div id="chiefcomplaint"></div>
                          <p class="section-title mt-3">Plan Of Admission-Advised admission:</p>
                          <table>
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Transfer</th>
                                      <th>Doctor</th>
                                      <th>POA</th>
                                      <th>Procedure/Surgery</th>
                                      <th>Ward</th>
                                      <th>Remarks</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>1</td>
                                      <td>ADVISED ADMISSION</td>
                                      <td>Dr. Ashwin Kumar Panda</td>
                                      <td>2025-02-18</td>
                                      <td>CORONARY ANGIOGRAM (B)</td>
                                      <td>ICU</td>
                                      <td>UNDER HAL CREDIT IN AMCU1</td>
                                  </tr>
                              </tbody>
                          </table> -->
                          <?php
                              // Fetch total count of medicines
                              $getPresMedicines = mysqli_query($conn, "SELECT * FROM prescription_medicines WHERE status='1' AND prescription_id='$id'") or die(mysqli_error($conn));
                              $totalMedicines = mysqli_num_rows($getPresMedicines); // Get total count

                              // Show the div only if there are medicines
                              if ($totalMedicines > 0) {
                          ?>
                              <div id="medicinediv">
                                  <p class="section-title mt-2 mb-1">Rx :</p>

                                  <p><strong>TOTAL NO. OF MEDICINES: <?php echo $totalMedicines; ?></strong></p>

                                  <table>
                                      <thead>
                                          <tr>
                                              <th>Medicine</th>
                                              <th>Frequency - Timing - Duration</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                      <?php
                                          while ($resPresMedic = mysqli_fetch_object($getPresMedicines)) {
                                              // Fetch Medicine Details
                                              $getMedicine = mysqli_query($conn, "SELECT medicine_name, scientific_name FROM medicines WHERE medicine_id='$resPresMedic->medicine_id'") or die(mysqli_error($conn));
                                              $res1 = mysqli_fetch_object($getMedicine);
                                              $medicineName = $res1->medicine_name ?? 'N/A';
                                              $scientific_name = $res1->scientific_name ?? 'N/A';

                                              // Fetch Dosage Details
                                              $getdosage = mysqli_query($conn, "SELECT * FROM dosageandtime WHERE status='1' AND doseandtime_id='$resPresMedic->dosage_id'") or die(mysqli_error($conn));
                                              $resdosage = mysqli_fetch_object($getdosage);
                                              $dose_schedule = $resdosage->dose_schedule ?? 'Not Available';
                                      ?>
                                          <tr>
                                              <td class="service">
                                                  <span class="medicine"><?php echo $medicineName; ?></span><br>
                                                  <span style="font-size: 11px;">(<?php echo $scientific_name; ?>)</span>
                                              </td>
                                              <td class="desc"><?php echo $dose_schedule; ?></td>
                                          </tr>
                                      <?php
                                          }
                                      ?>
                                      </tbody>
                                  </table>
                              </div>
                          <?php
                              } // End of if condition
                          ?>

                      </div>

                        <!-- <div class="row" >
                          <div class="form-group col-lg-4 col-sm-12">
                            <label for="patientId"> Name <span id="patient" class="text-danger">*</span> </label>
                            <select class="form-control form-select patientIdName" name="patientIdName" id="patientIdName" onchange="GetNameByIds()">
                              <option value="">Select Name</option>
                                <?php
                                  if ($SessionUserId == "1") {
                                      $query1 = "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                      $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                      $query2 = "SELECT DISTINCT(patient_name) FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                      $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                  } else {
                                      $query1 = "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                      $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                      $query2 = "SELECT DISTINCT(patient_name) FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                      $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                  }

                                  $options = array();

                                  while ($row1 = mysqli_fetch_assoc($result1)) {
                                      $options[] = $row1;
                                  }

                                  while ($row2 = mysqli_fetch_assoc($result2)) {
                                      $options[] = $row2;
                                  }

                                  $isDataIdentical = count($options) > 0 ? true : false;
                                  $PatientNamesArray = array();

                                  foreach ($options as $option) {
                                      $Keyvalue1 = $option['patient_name'];

                                      $PatientNamesArray[] = array(
                                          'Keyvalue' => $Keyvalue1
                                      );
                                  }

                                  $PatientNamesArray = array_unique($PatientNamesArray, SORT_REGULAR);

                                  foreach ($PatientNamesArray as $patient) {
                                    ?>
                                      <option  value="<?= $patient['Keyvalue'] ?>"><?= $patient['Keyvalue'] ?></option>
                                    <?php
                                  }
                                ?>
                            </select>
                          </div>
                          <div class="form-group col-lg-4 col-sm-12">
                            <label for="patientId"> Id <span id="patient" class="text-danger">*</span> </label>
                            <select class="form-control form-select patientId" name="patientId" id="patientId" onchange="GetNameByAgeGender()">
                              <option value="">Select Id</option>
                                <?php
                                if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                    $query1 = "SELECT DISTINCT(appoint_unicode), appoint_id FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                    $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                    $query2 = "SELECT DISTINCT(appoint_unicode), appoint_id FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                    $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                } else {
                                    $query1 = "SELECT DISTINCT(appoint_unicode), appoint_id FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                    $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                    $query2 = "SELECT DISTINCT(appoint_unicode), appoint_id FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                    $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                }

                                $options = array();

                                while ($row1 = mysqli_fetch_assoc($result1)) {
                                    $options[] = $row1;
                                }

                                while ($row2 = mysqli_fetch_assoc($result2)) {
                                    $options[] = $row2;
                                }

                                $isDataIdentical = count($options) > 0 ? true : false;
                                $PatientNamesArray = array();

                                foreach ($options as $option) {
                                    $Keyvalue = $option['appoint_unicode'];
                                    $Keyvalue2 = $option['appoint_id'];

                                    $PatientNamesArray[] = array(
                                        'Keyvalue' => $Keyvalue,
                                        'Keyvalue2' => $Keyvalue2
                                    );
                                }

                                $PatientNamesArray = array_unique($PatientNamesArray, SORT_REGULAR);

                                foreach ($PatientNamesArray as $patient) {
                                    ?>
                                    <option data-custom-value="<?= $patient['Keyvalue2'] ?>" value="<?= $patient['Keyvalue'] ?>"><?= $patient['Keyvalue'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                          </div>
                          <div class="form-group col-lg-4 col-sm-12">
                            <label for="appoint_register_id"> Appointment Id <span id="patient" class="text-danger">*</span></label>
                              <select class="form-control form-select appoint_register_id " name="appoint_register_id" id="appoint_register_id">
                                <option value="">Select Appointment Ids</option>
                                  <?php
                                    $AppointmentIdsArray = [];
                                    if($SessionUserId == "1" && $SessionRoleId=="1"){
                                      // $sql = mysqli_query($conn, "SELECT * FROM appointment_online m, appointment_existing mr WHERE m.appoint_status='1' AND mr.appoint_status='1' AND m.appoint_unicode=mr.appoint_unicode AND (m.appoint_date='$currentDate' OR mr.appoint_date='$currentDate') ") or die(mysqli_error($conn));
                                  
                                      $sql1 = "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                      $sqlres1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));

                                      $sql2 = "SELECT * FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                      $sqlres2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
                                    } else{
                                      // $sql = mysqli_query($conn, "SELECT * FROM appointment_online m, appointment_existing mr WHERE m.appoint_status='1' AND mr.appoint_status='1' AND m.appoint_unicode=mr.appoint_unicode AND (m.appoint_date='$currentDate' OR mr.appoint_date='$currentDate') AND m.org_id='$SessionOrgId' AND mr.org_id='$SessionOrgId'") or die(mysqli_error($conn));
                                  
                                      $sql1 = "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                      $sqlres1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));

                                      $sql2 = "SELECT * FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId'";
                                      $sqlres2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
                                    }
                                    while ($row = mysqli_fetch_object($sqlres1)) {
                                      $AppointmentIdsArray[] = $row->appoint_register_id;
                                    }
                                    while ($row = mysqli_fetch_object($sqlres2)) {
                                      $AppointmentIdsArray[] = $row->appoint_register_id;
                                    }
                                    $AppointmentIdsArray = array_unique($AppointmentIdsArray);
                                      foreach ($AppointmentIdsArray as $appointmentids) {
                                  ?>
                                  <option value="<?=$appointmentids?>" > <?=$appointmentids?> </option>
                                  <?php   
                                  }
                                  ?>
                              </select>
                          </div>
                        </div>
                        <div class="row mt-3" style="margin-left: 0px;">
                          <div class="form-group col-lg-4 col-sm-12">
                            <label for="age"> Age <span id="patient" class="text-danger">*</span> </label>
                            <input class="form-control" name="age" id="age" value="">
                          </div>
                          <div class="form-group col-lg-4 col-sm-12">
                            <label for="menu_web_url" style="margin: 4px; margin-left: 20px;"> Gender <span class="text-danger">*</span></label>
                            <br> 
                            <div class="" style="margin: 5px; margin-left: 20px;">
                              <input  type="radio" name="gender" id="male" value="Male" /> 
                              <span style="margin-top: -100px;">Male</span>
                              <input type="radio" name="gender" id="female" value="Female"  /> Female
                              <input type="radio" name="gender" id="others"  value="Others"  /> Others 
                            </div>
                          </div>
                          <div class="form-group col-lg-4 col-sm-12">
                            <input type="hidden" name="dept_id" id="dept_id">
                            <label for="menu_web_url">RX Groups</label>
                            <select class="form-control form-select RX_Group" name="RX_Group_Id" id="RX_Group_Id" >
                              <option value="" >Select RX Group</option>
                                <?php
                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                                    $getRXGroups = mysqli_query($conn, "SELECT rx_group_id, rx_group_name FROM rx_groups_names WHERE status='1' ORDER BY rx_group_id DESC") or die(mysqli_error($conn));
                                } else{
                                    $getRXGroups = mysqli_query($conn, "SELECT rx_group_id, rx_group_name FROM rx_groups_names WHERE status='1' AND org_id='$SessionOrgId' ORDER BY rx_group_id DESC") or die(mysqli_error($conn));
                                }
                                    while($row=mysqli_fetch_object($getRXGroups)) {
                                ?>
                                <option value="<?=$row->rx_group_id ?>" > <?= $row->rx_group_name ?> </option>
                                <?php } ?>
                            </select> 
                          </div>
                        </div>
                        <div class="row mt-3" style="margin-left: 0px;">
                          <div class="form-group col-lg-4 col-sm-12">
                            <input type="hidden" name="dept_id" id="dept_id">
                            <label for="menu_web_url">Test Groups</label>
                            <select class="form-control form-select " name="test_group_id" id="test_group_id" onchange="GetAutoTest()">
                              <option value="" >Select Test Groups</option>
                                <?php
                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                                $getTestGroup = mysqli_query($conn, "SELECT test_group_id, test_group_name FROM test_group WHERE status='1' ORDER BY test_group_id DESC") or die(mysqli_error($conn));
                                } else{
                                    $getTestGroup = mysqli_query($conn, "SELECT test_group_id, test_group_name FROM test_group WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_group_id DESC") or die(mysqli_error($conn));
                                }
                                    while($row=mysqli_fetch_object($getTestGroup)) {
                                ?>
                                <option value="<?=$row->test_group_id ?>" > <?= $row->test_group_name ?> </option>
                                <?php } ?>
                            </select> 
                          </div>
                          <div class="form-group col-lg-8 col-sm-12">
                            <label for="test_name"> Test Name <span class="text-danger">*</span> </label>
                            <select class="form-control form-select tests" name="tests[]" id="tests" multiple>
                              <?php
                              if($SessionUserId == "1" && $SessionRoleId=="1"){
                                  $getTestGroup = mysqli_query($conn, "SELECT test_id, test_name FROM tests WHERE status='1' ORDER BY test_id DESC") or die(mysqli_error($conn));
                              } else{
                                  $getTestGroup = mysqli_query($conn, "SELECT test_id, test_name FROM tests WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_id DESC") or die(mysqli_error($conn));
                              }
                                  while($row=mysqli_fetch_object($getTestGroup)) {
                              ?>
                                <option value="<?= $row->test_id ?>" > <?= $row->test_name ?> </option>
                              <?php } ?>
                            </select> 
                          </div>
                        </div>
                      </div> -->
                    </div>
                    <!-- Add Button 
                    <div class="d-flex justify-content-end">
                        <a href="javascript:void(0)" class="adding-form float-end btn btn-primary Pule">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>-->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          <!--<table>
            <thead>
              <tr>
                <th class="service"><?= $prescriptionData; ?></th>
                <th class="desc"><?= $PatientName; ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th><?= $resUid->gender; ?>/<?= $resUid->age; ?></th>
              </tr>
              <tr>
                <th class="service"><b>Medicine Name</b></th>
                <th class="desc"><b>Dosage</b></th>
                <th><b>In Take Period</b></th>
                <th><b>Frequency</b></th>
                <th><b>Quantity</b></th>
                <th><b>Duration</b></th>
              </tr>
            </thead>
            <tbody>-->
            
          <!-- <div id="notices">
            <div><b>ADVICE:</b></div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div> 
          </div>
        </main>
        <footer id="hai"></footer> 
        <div class="row">
          <div class="col-10" ></div>
          <div class="col-2" >
            <button id="increase-bottom-margin" class="btn btn-primary button-class" onclick="insertmargine1()">
              <i class="fas fa-plus-square"></i>+
            </button>
            <button id="decrease-bottom-margin" class="btn btn-primary button-class" onclick="insertmargine1()">
              <i class="bi bi-dash-square"></i>-
            </button>
          </div>
          <div class="card-footer text-center">
            <button type="button" class="btn btn-primary button-class" name="saveData" id="saveData" value="" >Submit</button>
          </div>
        </div> -->
        <!-- Centered Print Button -->
         <div class="clearfix">
            <div id="clearprint" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" class="btn btn-primary">Print</button>
            </div>
         </div>
        

      </page>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
      const mainContent = document.querySelector('#A4');
      let topMargin = parseInt(getURLParam('topMargin')) || 0;
      let bottomMargin = parseInt(getURLParam('bottomMargin')) || 0;

      if (mainContent) {
          mainContent.style.marginTop = topMargin + 'px';
          mainContent.style.marginBottom = bottomMargin + 'px';
      }

      function addEventListenerIfExists(id, event, handler) {
          const element = document.getElementById(id);
          if (element) {
              element.addEventListener(event, handler);
          }
      }
      function printPage() {
          document.getElementById("printButton").style.display = "none";
          window.print();
          setTimeout(() => {
              document.getElementById("printButton").style.display = "block"; 
          }, 1000); // Show the button again after printing
      }

      addEventListenerIfExists('increase-top-margin', 'click', function() {
          topMargin += 10;
          mainContent.style.marginTop = topMargin + 'px';
          updateURLParams();
      });

      addEventListenerIfExists('decrease-top-margin', 'click', function() {
          if (topMargin > 100) {
              topMargin -= 10;
              mainContent.style.marginTop = topMargin + 'px';
              updateURLParams();
          }
      });

      addEventListenerIfExists('increase-bottom-margin', 'click', function() {
          bottomMargin += 10;
          mainContent.style.marginBottom = bottomMargin + 'px';
          updateURLParams();
      });

      addEventListenerIfExists('decrease-bottom-margin', 'click', function() {
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

      // Insert margin function
      function insertmargine1() {
          var size_id = $('#size_id1').val();
          var organizations1 = $('#organizations1').val();
          $.ajax({
              url: 'insertmargin2.php',
              type: 'POST',
              data: {
                  'size_id': size_id,
                  'topMargin': topMargin,
                  'bottomMargin': bottomMargin,
                  'organizations1': organizations1
              },
              success: function(data) {
                  console.log(data);
              },
              error: function(err) {
                  console.log(err);
              }
          });
      }

      // Set current date
      var today = new Date();
      var formattedDate = today.toLocaleDateString("en-GB", {
          day: "2-digit",
          month: "short",
          year: "numeric"
      });

      var dateElement = document.getElementById("todaydate");
      if (dateElement) {
          dateElement.innerHTML = formattedDate;
      }
  });

  $(document).ready(function(){
    var patientName = "<?= isset($res5->patient_name) ? htmlspecialchars($res5->patient_name) : 'N/A'; ?>";
    var patientdate = "<?= isset($res5->prescriptiondate) ? date('d-m-Y', strtotime($res5->prescriptiondate)) : 'N/A'; ?>";
    var patientplace= "<?= isset($res5->place) ? htmlspecialchars($res5->place) : 'N/A'; ?>";
    var age = "<?= isset($res5->age) ? htmlspecialchars($res5->age) : 'N/A'; ?>";
    var gender = "<?= isset($res5->gender) ? htmlspecialchars($res5->gender) : 'N/A'; ?>";
    var mobile_number = "<?= isset($res6->mobile_number) ? htmlspecialchars($res6->mobile_number) : 'N/A'; ?>";
    var Organization = "<?= isset($res7->organization_name) ? htmlspecialchars($res7->organization_name) : 'N/A'; ?>";
    var UMR = "<?= isset($res5->appoint_register_id) ? htmlspecialchars($res5->appoint_register_id) : 'N/A'; ?>";
    var billno = "<?= isset($res6->bill_id) ? htmlspecialchars($res6->bill_id) : 'N/A'; ?>";
    var billdate = "<?= isset($res6->bill_date) ? htmlspecialchars($res6->bill_date) : 'N/A'; ?>";
    var visitdate = "<?= isset($res6->appoint_date) ? htmlspecialchars($res6->appoint_date) : 'N/A'; ?>";
    var BPSit = "<?= isset($res8->BPsit) ? htmlspecialchars($res8->BPsit) : 'N/A'; ?>";
    var BPStand = "<?= isset($res8->BPstand) ? htmlspecialchars($res8->BPstand) : 'N/A'; ?>";
    var weight = "<?= isset($res8->weight) ? htmlspecialchars($res8->weight) : 'N/A'; ?>";
    var height = "<?= isset($res8->height) ? htmlspecialchars($res8->height) : 'N/A'; ?>";
    var GRBS = "<?= isset($res8->GRBS) ? htmlspecialchars($res8->GRBS) : 'N/A'; ?>";
    var heartrate = "<?= isset($res8->heartrate) ? htmlspecialchars($res8->heartrate) : 'N/A'; ?>";
    var temperature = "<?= isset($res8->temperature) ? htmlspecialchars($res8->temperature) : 'N/A'; ?>";
    var resp = "<?= isset($res8->resp) ? htmlspecialchars($res8->resp) : 'N/A'; ?>";
    var sp02percent = "<?= isset($res8->sp02percent) ? htmlspecialchars($res8->sp02percent) : 'N/A'; ?>";
    var bloodgroup = "<?= isset($res8->bloodgroup) ? htmlspecialchars($res8->bloodgroup) : 'N/A'; ?>";
    var CPAP = "<?= isset($res8->CPAP) ? htmlspecialchars($res8->CPAP) : 'N/A'; ?>";
    var HFNC = "<?= isset($res8->HFNC) ? htmlspecialchars($res8->HFNC) : 'N/A'; ?>";
    var VO2 = "<?= isset($res8->VO2) ? htmlspecialchars($res8->VO2) : 'N/A'; ?>";
    var BMIvalue = "<?= isset($res8->BMIvalue) ? htmlspecialchars($res8->BMIvalue) : 'N/A'; ?>";
    var doctorname = "<?= isset($res9->doctor_name) ? htmlspecialchars($res9->doctor_name) : 'N/A'; ?>";
    var details = "<?= isset($res9->details) ? htmlspecialchars($res9->details) : 'N/A'; ?>";
    var docspecialty = "<?= isset($res10->description) ? htmlspecialchars($res10->description) : 'N/A'; ?>";
    var specialty = "<?= isset($res10->departmentName) ? htmlspecialchars($res10->departmentName) : 'N/A'; ?>";
    var chiefcomplaint = "<?= isset($res5->chiefcomplaint) ? htmlspecialchars($res5->chiefcomplaint) : 'N/A'; ?>";

    $("#patient_name").text(patientName);
    $("#todaydate1").text(patientdate);
    $("#place").text(patientplace);
    $("#age_gender").text(age + " / " + gender);
    $("#mobile").text(mobile_number);
    $("#organization").text(Organization);
    $("#umr_no").text(UMR);
    $("#bill_no").text(billno);
    $("#visit_dt").text(visitdate);
    $("#bill_dt").text(billdate);
    $("#bp_sit").text(BPSit);
    $("#bp_stand").text(BPStand);
    $("#bmi").text(BMIvalue);
    $("#weight").text(weight);
    $("#height").text(height);
    $("#GRBS").text(GRBS);
    $("#heartrate").text(heartrate);
    $("#temperature").text(temperature);
    $("#resp").text(resp);
    $("#sp02percent").text(sp02percent);
    $("#bloodgroup").text(bloodgroup);
    $("#CPAP").text(CPAP);
    $("#HFNC").text(HFNC);
    $("#VO2").text(VO2);
    //$("#docname").text("Dr. "+ doctorname);
    //$("#docdetails").text(details);
    //$("#docspecialty").text(docspecialty);
    //$("#specialty").text(specialty);
    //$("#docname1").text("Dr. "+ doctorname);
    //$("#docdetails1").text(details);
    //$("#docspecialty1").text(docspecialty);
    //$("#specialty1").text(specialty);
    //$("#chiefcomplaint").text(chiefcomplaint);
  });

    </script>
  </body>
</html>
