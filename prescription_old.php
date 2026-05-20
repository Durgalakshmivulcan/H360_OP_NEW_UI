<?php require_once("ajax/header.php"); ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>

    .btn-group, .btn-group-vertical {
        position: relative;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
        }

    .Pule {
        font-weight: 600;
        font-size: 12px;
        line-height: 26px;
        padding: 0.3rem 0.8rem;
        letter-spacing: .5px;
        margin-top: 21px;
        margin-right: 18px;
    }

    .add_row{
        margin-right: 17px;
        margin-top: 16px;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
    }

    .divider {
        margin: 0 5px;
    }

    .med, .invest{
        display : none;
    }

    .bpicon{
        position:relative;
        top:4px;
        font-size:21px;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px; 
        /* padding: 6px 12px; */
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Prescription</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Doctor Menu</li>
            <li class="breadcrumb-item">Add/Modify Prescription</li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12 mt-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Prescription</h4>
                    
                    <div class="d-flex gap-2">
                        <button id="reportsBtn" class="btn btn-primary fw-bold d-none"  type="button" onclick="CheckReports();" style="border-radius: 5%;">
                            Reports
                        </button>

                        <a href="prescriptionold.php" class="btn btn-primary fw-bold" style="border-radius: 5%;">
                            Old
                        </a>
                    </div>
                </div>

                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <input type="hidden" name="prescription_id" id="prescription_id" >
                    <div class="card-body">

                        <div class="row">
                            <?php 
                                $SessionUserId = $_SESSION['security_id'] ?? '';
                                $SessionRoleId = $_SESSION['role_id'] ?? '';
                                $SessionOrgId = $_SESSION['org_id'] ?? '';

                                if ($SessionRoleId == 8) {
                                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                                    echo "<script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Access Denied',
                                            text: 'You do not have access to this page.',
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = 'dashboard.php';
                                            }
                                        });
                                    </script>";
                                    exit(); 
                                }
                            
                            $appointid = isset($_GET['AppointId']) ? $_GET['AppointId'] : '';
                            $orgid = isset($_GET['OrgId']) ? $_GET['OrgId'] : '';
                            $registrationid = isset($_GET['appointRegisterId']) ? $_GET['appointRegisterId'] : '';

                            $qry = "SELECT * FROM appointment_online WHERE appoint_register_id='$registrationid' AND org_id='$orgid' AND appoint_id='$appointid'";
                            $result = mysqli_query($conn, $qry) or die(mysqli_error($conn));
                            $row = mysqli_fetch_object($result);
                            $patientName = $row->patient_name;

                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                            ?>

                            <div class="row">
                                <div class="row mb-lg-5 mb-sm-3">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="OrgIdByPatientNames();getMedichines('');">
                                            <option value="">Select Organization</option>
                                            <?php
                                            $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                            while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                                                $selected = ($orgid == $ResOrganization->org_id) ? 'selected' : '';
                                                echo "<option value='{$ResOrganization->org_id}' $selected>{$ResOrganization->organization_name}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <?php
                            } else{
                            ?>
                            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                            <?php
                            }
                            ?>
                            <input type="hidden" name="SessionUserId" id="SessionUserId" value="<?= $SessionUserId ?>" />
                            <input type="hidden" name="SessionRoleId" id="SessionRoleId" value="<?= $SessionRoleId ?>" />
                            <div class="row">
                                <div class="row mb-lg-5 mb-sm-3">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="patientId"> Name <span id="patient" class="text-danger">*</span> </label>
                                        <select class="form-control patientIdName" name="patientIdName" id="patientIdName" onchange="GetNumberANDIdByName();">
                                            <option value="">Select Name</option>
                                            <?php
                                            if ($SessionUserId == "1") {
                                                $query1 = "SELECT DISTINCT(patient_name), visitor_status 
                                                        FROM appointment_online 
                                                        WHERE appoint_status='1' 
                                                        AND appoint_date='$currentDate'";
                                                $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                                $query2 = "SELECT DISTINCT(patient_name) 
                                                        FROM appointment_existing 
                                                        WHERE appoint_status='1' 
                                                        AND appoint_date='$currentDate'";
                                                $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                            } else {
                                                $query1 = "SELECT DISTINCT(patient_name), visitor_status 
                                                        FROM appointment_online 
                                                        WHERE appoint_status='1' 
                                                        AND org_id='$SessionOrgId' 
                                                        AND appoint_date='$currentDate'";
                                                $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                                $query2 = "SELECT DISTINCT(patient_name) 
                                                        FROM appointment_existing 
                                                        WHERE appoint_status='1' 
                                                        AND org_id='$SessionOrgId' 
                                                        AND appoint_date='$currentDate'";
                                                $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                            }

                                            $options = [];

                                            while ($row1 = mysqli_fetch_assoc($result1)) {
                                                $options[] = $row1;
                                            }

                                            while ($row2 = mysqli_fetch_assoc($result2)) {
                                                $row2['visitor_status'] = 0; 
                                                $options[] = $row2;
                                            }

                                            $PatientNamesArray = [];
                                            foreach ($options as $option) {
                                                $PatientNamesArray[] = [
                                                    'Keyvalue' => $option['patient_name'],
                                                    'visitor_status' => $option['visitor_status']
                                                ];
                                            }

                                            $PatientNamesArray = array_unique($PatientNamesArray, SORT_REGULAR);

                                            foreach ($PatientNamesArray as $patient) { ?>
                                                <option value="<?= $patient['Keyvalue'] ?>"
                                                        data-status="<?= $patient['visitor_status'] ?>"
                                                        <?= ($patient['Keyvalue'] == $patientName ? 'selected' : '') ?>>
                                                    <?= $patient['Keyvalue'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="mobileNumber"> Mobile <span class="text-danger">*</span> </label>
                                        <select type="tel" class="form-control form-select mobile_number"  name="mobile_number" id="mobile_number" placeholder="Select Mobile Number" onchange="GetPatientNameAndIDByNumber()">  
                                            <option value="" >Select Mobile Number</option>
                                            <?php
                                            if($SessionUserId=="1"){
                                                $getTestGroup = mysqli_query($conn, "SELECT DISTINCT(mobile_number) FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' ORDER BY appoint_id DESC") or die(mysqli_error($conn));
                                            } else{
                                                $getTestGroup = mysqli_query($conn, "SELECT DISTINCT(mobile_number) FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' AND appoint_date='$currentDate' ORDER BY appoint_id DESC") or die(mysqli_error($conn));
                                            }
                                                while($row=mysqli_fetch_object($getTestGroup)) {
                                            ?>
                                            <option value="<?=$row->mobile_number ?>" > <?= $row->mobile_number ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-4 col-sm-12" >
                                        <label for="patientId"> Id <span id="patient" class="text-danger">*</span> </label>
                                        <select class="form-control form-select patientId" name="patientId" id="patientId" onchange="GetNameByAgeGender();GetPreviousPrescriptionData();">
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

                                    <div class="form-group col-lg-4 col-sm-12 mt-2">
                                        <label for="appoint_register_id"> Appointment Id <span id="patient" class="text-danger">*</span> </label>
                                        <select class="form-control form-select appoint_register_id " name="appoint_register_id" id="appoint_register_id" onchange ="getvitalid(this.value);">
                                            <option value="">Select Appointment Ids</option>
                                            <?php
                                                $AppointmentIdsArray = [];
                                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                                                
                                                    $sql1 = "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                                    $sqlres1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
            
                                                    $sql2 = "SELECT * FROM appointment_existing WHERE appoint_status='1' AND appoint_date='$currentDate'";
                                                    $sqlres2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
                                                } else{
                                                
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

                                    <div class="form-group col-lg-4 col-sm-12 mt-2">
                                        <label for="age"> Age <span id="patient" class="text-danger">*</span> </label>
                                        <input class="form-control" name="age" id="age" value="">
                                    </div>
                                    <div class="form-group col-lg-4 col-sm-12 mt-2">
                                        <label for="menu_web_url">Gender <span class="text-danger">*</span></label>
                                        <div class="selectgroup w-100 ">
                                            <label>
                                                <input type="radio" name="gender" id="male" value="Male" class="selectgroup-input-radio"  />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1">
                                                    <i class="bi bi-gender-male"></i>&nbsp; Male
                                                </span>
                                            </label>                  
                                            <label>
                                                <input type="radio" name="gender" id="female" value="Female" class="selectgroup-input-radio" />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1">
                                                    <i class="bi bi-gender-female"></i>&nbsp; Female
                                                </span>
                                            </label>
                                            <label>
                                                <input type="radio" name="gender" id="others" value="Others" class="selectgroup-input-radio" />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1">
                                                    <i class="bi bi-gender-ambiguous"></i>&nbsp; Other
                                                </span>
                                            </label>                  
                                        </div>
                                    </div>
                                </div>
                                <hr>
                              
                                <div class="row">
                                    <h6 class="text-dark">Vitals</h6>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="patient_bp_sit" ><i class="material-icons bpicon">airline_seat_recline_normal</i>BP/mmHg</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="existing_bpSit_systolic" id="existing_bpSit_systolic" value="">
                                            <span class="divider">/</span>
                                            <input type="text" class="form-control" name="existing_bpSit_diastolic" id="existing_bpSit_diastolic" value="">
                                        </div>
                                    </div> 

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="patient_bp_stand" ><i class="fa-solid fa-person bpicon"></i>BP/mmHg</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="existing_bpStand_systolic" id="existing_bpStand_systolic" value="">
                                            <span class="divider">/</span>
                                            <input type="text" class="form-control" name="existing_bpStand_diastolic" id="existing_bpStand_diastolic" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_weight">Weight (Kg)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-weight-scale"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="existing_weight" id="existing_weight" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_height">Height (cms)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-ruler-vertical"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="existing_height" id="existing_height" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_bmi">BMI Value</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <img src="assets/img/bmi.jpeg" alt="SpO2 Icon" width="18" height="18" classs = "fw-bold">
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="existing_bmi" id="existing_bmi" value="" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_grbs">GRBS (mg/dL)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-droplet"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="existing_grbs" id="existing_grbs" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_heart_rate">Heart Rate/min</label>
                                        <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fa-solid fa-heart-pulse"></i>
                                                </span>
                                            <input type="text" class="form-control" name="existing_heart_rate" id="existing_heart_rate" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_temperature">Temp (°F)</label>
                                        <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-thermometer-half"></i>
                                                </span>
                                            <input type="text" class="form-control" name="existing_temperature" id="existing_temperature" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_respiration_rate">Resp / min</label>
                                        <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-lungs-fill"></i>
                                                </span>
                                            <input type="text" class="form-control" name="existing_respiration_rate" id="existing_respiration_rate" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="existing_spO2">SPO2 (%) (on Room Air)</label>
                                        <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <img src="assets/img/spo2.jpg" alt="SpO2 Icon" width="22" height="22" classs = "fw-bold">
                                                    </div>
                                                </div>
                                            <input type="tel" class="form-control" name="existing_spO2" id="existing_spO2" value="">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="existing_patient_overview">Over-View of Patient</label>
                                        <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-street-view"></i>
                                                    </div>
                                                </div>
                                            <input type="text" class="form-control" name="existing_patient_overview" id="existing_patient_overview" value="">
                                        </div>
                                    </div>
                                </div> 
                                
                                <hr class="mt-5"> 
                                <h6 class="text-dark">Assessment</h6>
                                <div class="row mt-lg-4 mt-sm-4" >
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="finalDiagnosis" class="d-flex justify-content-between align-items-center">
                                            <span>
                                                Final Diagnosis
                                                <div class="btn-group">
                                                    <i class="fa fa-history dropdown-toggle fa-lg" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;margin-top:-14px;" title="Saved Templates"></i>
                                                    <ul class="dropdown-menu p-2" id="templateDropdown" style="width: 300px; max-height: 300px; overflow-y: auto;">
                                                    </ul>
                                                </div>
                                                <i class="fas fa-plus-circle text-success" id="addTemplateBtn" style="cursor:pointer;" title="Add Template"></i>
                                                
                                            </span>
                                        </label>
                                        <textarea class="form-control" name="finalDiagnosis" id="finalDiagnosis"></textarea>
                                    </div>
                                    <div class="form-group col-md-4  col-sm-12 ">
                                    <label for="chiefComplaint" class="d-flex justify-content-between align-items-center">
                                        <span>
                                            Chief Complaint
                                            <div class="btn-group">
                                                <i class="fa fa-history cheifhistory dropdown-toggle fa-lg" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;margin-top:-14px;" title="Saved Templates"></i>
                                                <ul class="dropdown-menu p-2" id="CheiftemplateDropdown" style="width: 300px; max-height: 300px; overflow-y: auto;">
                                                </ul>
                                            </div>
                                            <i class="fas fa-plus-circle text-success" id="addCheifTemplateBtn" style="cursor:pointer;" title="Add Chief Template"></i>
                                        </span>
                                    </label>
                                        <textarea class="form-control"  name="chiefComplaint" id="chiefComplaint"></textarea>
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12 ">
                                        <label for="pastHistory" class="d-flex justify-content-between align-items-center">
                                            <span>
                                                Past History
                                                <div class="btn-group">
                                                    <i class="fa fa-history pasthistory dropdown-toggle fa-lg" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;margin-top:-14px;" title="Saved Templates"></i>
                                                    <ul class="dropdown-menu p-2" id="PasttemplateDropdown" style="width: 300px; max-height: 300px; overflow-y: auto;">
                                                    </ul>
                                                </div>
                                                <i class="fas fa-plus-circle text-success" id="addPastTemplateBtn" style="cursor:pointer;" title="Add Past Template"></i>
                                            </span>
                                        </label>
                                        <textarea class="form-control"  name="pastHistory" id="pastHistory" value=""></textarea>
                                    </div>
                                </div>   
                        
                            </div>  
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="patient_data">Patient Data </label>
                                        <textarea class="form-control" id="patient_data" name="patient_data"></textarea>
                                    </div>
                                </div>  
                               
                        </div>
                    </div>                  
                    
                    <div class="adding-new-record card-body">
                        <hr>
                        <h6 class="text-dark">Medication</h6>
                        
                        <div class="row mt-lg-3 mt-sm-3">

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="medicineType">Medicine Type <span class="text-danger med">*</span></label>
                                <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-capsules"></i>
                                        </div>
                                    </div>
                                    <input list="medicineTypeDatalist"  class="form-control medicinetype" name="medicineType" id="medicineType">
                                        <datalist id="medicineTypeDatalist">
                                            <option value="">
                                        </datalist>
                                    <div id="typeDropdown" class="form-control dropdown-menu" type="hidden"></div>
                                </div>
                            </div>

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="medicineName">Medicine Name <span class="text-danger med">*</span></label>
                                <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-capsule"></i>
                                        </div>
                                    </div>
                                        <input list="drugNameDatalist" class="form-control drugname" id="drugName" name="drugName" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this)" >
                                        <datalist id="drugNameDatalist">
                                            <option value="">
                                        </datalist>
                                </div>
                            </div>   
                            
                            <div class="form-group col-lg-2 col-sm-12">
                                <label for="unit"> Unit  <span class="text-danger med">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/unit.jpeg" alt="unit Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                    <input list="unitDatalist" class="form-control unit" id="unit" name="unit">
                                    <datalist id="unitDatalist">
                                        <option value="">
                                    </datalist>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="dosage"> Dosage <span class="text-danger med">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                    <select class="form-control form-select dosage" name="dosage" id="dosage" onchange="handleDosageChange(this.value)">
                                        <option value="">Select Dosage</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12 ">
                                <label for="when"> In-take-period <span class="text-danger med">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                    <select class="form-control form-select" name="when" id="when">
                                        <option value=""> Select In-take-period </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="time"> Time <span class="text-danger med">*</span> </label>
                                <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                         <i class="fas fa-clock"></i>
                                        </div>
                                      </div>
                                    <select class="form-control form-select" name="time" id="time">
                                        <option value="">Select Time</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="dosage"> Duration <span class="text-danger med">*</span> </label>
                                <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="material-icons">date_range</i>
                                        </div>
                                    </div>
                                    <input type="number" class="form-control " name="duration_value" id="duration_value" >
                                    <select class="form-control" name="duration" id="duration">
                                        <option value="Days">Days</option>
                                        <option value="Weeks">Weeks</option>
                                        <option value="Months">Months</option>
                                        <option value="Till Further Advice">Till Further Advice</option>
                                    </select>
                                </div>
                           </div>
                           
                            <div class="form-group col-lg-1 col-sm-12">
                                <a href="javascript:void(0)" class="adding-medicine float-end btn btn-primary Pule"><i class=" fas fa-plus"></i></a>
                            </div>
                            
                            <div class="form-group col-lg-12  col-sm-12 " >
                            <label for="notes">Instructions</label>
                                 <textarea class="form-control"  name="notes"  id="notes" value=""></textarea>
                            </div>
                            <hr class="mt-lg-5">              
                            <div class="card-body" id="medicineTableWrapper"></div>

                        </div>
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label for="advise">Advise </label>
                                        <textarea class="form-control" id="advise" name="advise"></textarea>
                                    </div>
                                </div>  
                        <hr>
                        <h6 class="text-dark">Review</h6>
                        <div class= "row">
                            <div class="form-group col-lg-6 col-sm-12">
                                <label for="reviewInput">Review After</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="reviewInput" >
                                    <select class="form-control" id="reviewSelect">
                                        <option value="Days">Days</option>
                                        <option value="Weeks">Weeks</option>
                                        <option value="Months">Months</option>
                                        <option value="Till Further Advice">Till Further Advice</option>
                                    </select>

                                    <p class="my-2 mx-2">(OR)</p>

                                    <div class="form-group col-lg-4 col-sm-12">
                                      <input type="date" class="form-control" id="reviewCalculatedDate" plceholder = "">
                                    </div>               

                                </div>
                            </div>
                        </div>

                        <hr class="mt-5">
                        <h6 class="text-dark">Investigation</h6>
                        
                            <div class="row mt-lg-5 mt-sm-5">
                                <!-- Investigation -->
                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="investigation" class="d-flex justify-content-between align-items-center">
                                        <span>
                                            Investigation <span class="text-danger invest">*</span>
                                            <div class="btn-group ml-2">
                                                <i class="fa fa-history investgationhistory dropdown-toggle fa-lg" 
                                                data-bs-toggle="dropdown" aria-expanded="false" 
                                                style="cursor:pointer; margin-top:-14px;" 
                                                title="Saved Templates"></i>
                                                <ul class="dropdown-menu p-2" id="investigationtemplateDropdown" 
                                                    style="width: 300px; max-height: 300px; overflow-y: auto;"></ul>
                                            </div>
                                            <i class="fas fa-plus-circle text-success" id="addinvestigationTemplateBtn" 
                                            style="cursor:pointer;" title="Add Investigation Template"></i>
                                        </span>
                                    </label>
                                    <input list="investigationDatalist" class="form-control investigation" 
                                        id="investigation" name="investigation" 
                                        oninput="this.value = this.value.toUpperCase();">
                                    <datalist id="investigationDatalist">
                                        <option value="">
                                    </datalist>
                                </div>

                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="concession">Concession</label>
                                    <select class="form-control" name="concession" id="concession">
                                        <option value="">Select Concession</option>
                                        <?php
                                            $getConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1'") or die(mysqli_error($conn));
                                            while ($row = mysqli_fetch_assoc($getConcessions)) {
                                                echo '<option value="'.$row['concession_id'].'" 
                                                        data-type="'.$row['concession_type'].'" 
                                                        data-value="'.$row['concession_value'].'">'
                                                        .$row['concession_name'].
                                                    '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-lg-2 col-sm-12 mt-3">
                                    <input type="hidden" class="form-control currency" name="test_price" id="test_price" value="" readonly>
                                </div> 
                                
                                <input type="hidden" id="concession_type" name="concession_type" class="form-control" readonly>
                                <input type="hidden" id="concession_value" name="concession_value" class="form-control" readonly>

                            </div>          
                                        
                            <div class="row">
                                <div class="form-group col-lg-11 col-sm-12">
                                    <label for="testnotes">Instruction</label>
                                    <textarea class="form-control" name="testnotes" id="testnotes"></textarea>
                                </div>
                                <div class="form-group col-lg-1 col-sm-12">
                                    <a href="javascript:void(0)" class="adding-form float-end btn btn-primary Pule"><i class=" fas fa-plus"></i></a>
                                </div>

                                <div id="investigationTableWrapper"></div>
                            </div>

                            <hr class="mt-5">

                            <div class="form-group col-lg-11 col-sm-12">
                                <label for="personal_notes">Personal Note</label>
                                <textarea class="form-control" name="personal_note" id="personal_note"></textarea>
                            </div>

                        </div>
                        
                        <div class="card-footer text-center">
                            <button class="btn btn-primary" name="saveData" id="saveData" value="" >Submit</button>
                        </div>
                   
                </form>              
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Prescription Old List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="showPData">
                            <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="" method="POST" id="deleteFormId">
            <input type="hidden" name="deleteID" id="deleteID" value="" />
        </form>

    </section>

</div>

<!--  Edit Medicine Modal -->
<div class="modal fade" id="editVitalsModal" tabindex="-1" role="dialog" aria-labelledby="editVitalsModalTitle">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editVitalsModalTitle">Edit Medicine</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editVitalsForm">
        <div class="modal-body">

          <div class="row">
              <!-- Medicine Name -->
            <div class="form-group col-lg-12 col-sm-12">
              <label for="edit_drugName">Medicine Name <span class="text-danger">*</span></label>
              <input list="edit_drugNameDatalist" class="form-control" id="edit_drugName" name="edit_drugName" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this)">
              <datalist id="edit_drugNameDatalist">
                <option value="">
              </datalist>
            </div>
          </div>

          <div class="row">
            <!-- Medicine Type -->
            <div class="form-group col-lg-3 col-sm-12">
              <label for="edit_medicineType">Medicine Type <span class="text-danger">*</span></label>
              <input list="edit_medicineTypeDatalist" class="form-control" id="edit_medicineType" name="edit_medicineType">
              <datalist id="edit_medicineTypeDatalist">
                <option value="">
              </datalist>
            </div>

            <!-- Unit -->
            <div class="form-group col-lg-3 col-sm-12">
              <label for="edit_unit">Unit <span class="text-danger">*</span></label>
              <input list="edit_unitDatalist" class="form-control" id="edit_unit" name="edit_unit">
              <datalist id="edit_unitDatalist">
                <option value="">
              </datalist>
            </div>

            <!-- Dosage -->
            <div class="form-group col-lg-4 col-sm-12">
              <label for="edit_dosage">Dosage <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_dosage" name="edit_dosage" onchange="getmodalTimeForDose(this.value)">
                <option value="">Select Dosage</option>
              </select>
            </div>

            <!-- In-take-period -->
            <div class="form-group col-lg-4 col-sm-12">
              <label for="edit_when">In-take-period <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_when" name="edit_when">
                <option value="">Select In-take-period</option>
              </select>
            </div>

            <!-- Time -->
            <div class="form-group col-lg-3 col-sm-12">
              <label for="edit_time">Time <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_time" name="edit_time">
                <option value="">Select Time</option>
              </select>
            </div>

            <!-- Duration -->
            <div class="form-group col-lg-4 col-sm-12">
              <label for="edit_duration">Duration <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="text" class="form-control" id="edit_duration_value" name="edit_duration_value">
                <select class="form-control" id="edit_duration" name="edit_duration">
                  <option value="Days">Days</option>
                  <option value="Weeks">Weeks</option>
                  <option value="Months">Months</option>
                  <option value="Till Further Advice">Till Further Advice</option>
                </select>
              </div>
            </div>

            <!-- Instructions -->
            <div class="form-group col-lg-12 col-sm-12">
              <label for="edit_notes">Instructions <span class="text-danger">*</span></label>
              <textarea class="form-control" id="edit_notes" name="edit_notes"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer br">
          <button type="button" class="btn btn-primary" id="updateVitals">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editInvestigationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Investigation</h5>
        <button type="button" class="close" data-bs-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label>Investigation</label>
        <input list="edit_investigationDatalist" class="form-control edit_investigation" id="edit_investigation" name="edit_investigation" oninput="this.value = this.value.toUpperCase();">
        <datalist id="edit_investigationDatalist">
            <option value="">
        </datalist>

        <label class="mt-2">Price</label>
        <input type="text" class="form-control" id="edit_test_price">

        <label class="mt-2">Concession</label>
        <select class="form-control" id="edit_concession">
            <option value="">Select Concession</option>
            <?php
                $getConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1'") or die(mysqli_error($conn));
                while ($row = mysqli_fetch_assoc($getConcessions)) {
                    echo '<option value="'.$row['concession_id'].'" 
                            data-type="'.$row['concession_type'].'" 
                            data-value="'.$row['concession_value'].'">'
                            .$row['concession_name']. 
                        '</option>';
                }
            ?>
        </select>

        <label class="mt-2">Instruction</label>
        <textarea class="form-control" id="edit_instruction"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" id="cancelEditInvestigation" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="saveEditInvestigation" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateModalLabel">Add Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="templateForm">
          <input type="hidden" id="templateId" name="templateId">
          <div class="mb-2">
            <label for="templateName" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="templateName" name="templateName" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Final Diagnosis (Preview)</label>
            <textarea class="form-control" id="readonlyDiagnosis" rows="4" readonly></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="templateForm" class="btn btn-success">Save Template</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cheiftemplateModal" tabindex="-1" aria-labelledby="cheiftemplateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cheiftemplateModalLabel">Add Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="cheiftemplateForm">
          <input type="hidden" id="cheiftemplateId" name="cheiftemplateId">
          <div class="mb-2">
            <label for="cheiftemplateName" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="cheiftemplateName" name="cheiftemplateName" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Chief Complaint (Preview)</label>
            <textarea class="form-control" id="readonlyComplaint" rows="4" readonly></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="cheiftemplateForm" class="btn btn-success">Save Template</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="pasttemplateModal" tabindex="-1" aria-labelledby="pasttemplateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pasttemplateModalLabel">Add Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="pasttemplateId" name="pasttemplateId">
        <form id="pasttemplateForm">
          <div class="mb-2">
            <label for="pasttemplateName" class="form-label">Template Name</label>
            <input type="text" class="form-control" id="pasttemplateName" name="pasttemplateName" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Past History (Preview)</label>
            <textarea class="form-control" id="readonlyPast" rows="4" readonly></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="pasttemplateForm" class="btn btn-success">Save Template</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="templateSaveModal" tabindex="-1" aria-labelledby="templateSaveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Save Investigation Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="investigationtemplateName" class="form-label">Template Name</label>
          <input type="text" class="form-control" id="investigationtemplateName" placeholder="Enter template name">
        </div>
        <div class="mb-3">
          <label for="totalPrice" class="form-label">Total Price</label>
          <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="bi bi-currency-rupee"></i></div>
            </div>
            <input type="number" class="form-control" id="totalPrice" placeholder="Enter total price">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="saveTemplateBtn" class="btn btn-success">Save Template</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once("ajax/footer.php") ?>

<script>
    var NewInputsCountIni = 100;
    var Timing = "";
    
    $("document").ready(function() {
        function updateTestPriceWithConcession() {
            var investigationName = $('#investigation').val();
            var basePrice = 0;
            if (typeof allTests !== 'undefined' && Array.isArray(allTests)) {
                var test = allTests.find(function (t) {
                    return t.test_name === investigationName.trim().toUpperCase();
                });
                if (test) basePrice = parseFloat(test.test_price) || 0;
            }
            var concessionType = $('#concession option:selected').data('type');
            var concessionValue = $('#concession option:selected').data('value');
            var discountedPrice = basePrice;
            if (concessionType && concessionValue) {
                if (concessionType.toLowerCase() === 'percentage' || concessionType === '%') {
                    discountedPrice = basePrice - (basePrice * (parseFloat(concessionValue) / 100));
                } else if (concessionType.toLowerCase() === 'fixed' || concessionType.toLowerCase() === 'amount') {
                    discountedPrice = basePrice - parseFloat(concessionValue);
                }
            }
            if (discountedPrice < 0) discountedPrice = 0;
            var priceHtml = '';
            if (basePrice > discountedPrice) {
                var offText = '';
                if (concessionType && concessionValue) {
                    if (concessionType.toLowerCase() === 'percentage' || concessionType === '%') {
                        offText = ' <span style="color:#888;font-size:0.95em;">(' + parseFloat(concessionValue) + '% off)</span>';
                    } else if (concessionType.toLowerCase() === 'fixed' || concessionType.toLowerCase() === 'amount') {
                        var offAmt = basePrice - discountedPrice;
                        offText = ' <span style="color:#888;font-size:0.95em;">(Rs ' + offAmt.toFixed(2) + ' Off)</span>';
                    }
                }
                priceHtml = '<span style="text-decoration:line-through;color:#d00;font-weight:bold;">Rs ' + basePrice.toFixed(2) + '/-</span><br>'
                    + '<span style="color:#080;font-weight:bold;">Rs ' + discountedPrice.toFixed(2) + '/-</span>' + offText;
            } else {
                priceHtml = '<span style="color:#080;font-weight:bold;">Rs ' + basePrice.toFixed(2) + '/-</span>';
            }
            $('#test_price').val(discountedPrice.toFixed(2));
            if ($('#test_price').next('.price-visual').length === 0) {
                $('#test_price').after('<span class="price-visual" style="margin-left:8px;font-size:1.1em;"></span>');
            }
            $('#test_price').next('.price-visual').html(priceHtml);
        }

        $('#concession').on('change', updateTestPriceWithConcession);
        $('#investigation').on('input', updateTestPriceWithConcession);
        $("#appoint_register_id").select2();
        $(".patientId").select2();
        $('#patientIdName').select2({
            width: '100%',
            templateResult: function (data) {
                if (!data.id) return data.text;

                var $result = $('<span></span>').text(data.text);

                if ($(data.element).data('status') == 2) {
                    $result.addClass('status-green');
                }

                return $result;
            },
            
            templateSelection: function (data) {
                if (!data.id) return data.text;

                var $result = $('<span></span>').text(data.text);

                if ($(data.element).data('status') == 2) {
                    $result.addClass('status-green');
                }

                return $result;
            }
        });

        $('<style>')
        .prop('type', 'text/css')
        .html(`
            .select2-results__option .status-green {
                display: block; 
                width: 100%;
                background-color: #90EE90 !important;
                color: #000 !important;
                border-radius: 4px;
                padding: 2px 4px;
            }
            /* Also apply to selected item */
            .select2-selection__rendered .status-green {
                background-color: #90EE90 !important;
                color: #000 !important;
                border-radius: 4px;
                padding: 2px 4px;
            }
        `)
        .appendTo('head');
        $('.mobile_number').select2();

        getpriscription();
        getMedichines('');
        getMedicineType('');
        getUnit('');
        getDosages('');
        getInTakePeriod('');
        gettests();
        quantity();

        $('#drugName').on('input', toggleMedSpans);
        $('#investigation').on('input', toggleTestSpans);

        toggleMedSpans();
        toggleTestSpans();
      
    });

    function calculateExistingBMI() {
        const weight = parseFloat(document.getElementById("existing_weight").value);
        const heightCm = parseFloat(document.getElementById("existing_height").value);

        if (!isNaN(weight) && !isNaN(heightCm) && heightCm > 0) {
            const heightM = heightCm / 100;
            const bmi = weight / (heightM * heightM);
            document.getElementById("existing_bmi").value = bmi.toFixed(2);
        } else {
            document.getElementById("existing_bmi").value = '';
        }
    }

    document.getElementById("existing_weight").addEventListener("input", calculateExistingBMI);
    document.getElementById("existing_height").addEventListener("input", calculateExistingBMI);

    document.addEventListener("DOMContentLoaded", function () {
        var patientSelect = document.getElementById("patientIdName");
        if (patientSelect && patientSelect.value !== "") {
            patientSelect.dispatchEvent(new Event('change'));
        }
    });

    function toggleMedSpans() {
      var drugName = $('#drugName').val().trim();
      if (drugName === '') {
        $('.med').hide();
      } else {
        $('.med').show();
      }
    }

    function toggleTestSpans() {
      var drugName = $('#investigation').val().trim();
      if (drugName === '') {
        $('.invest').hide();
      } else {
        $('.invest').show();
      }
    }

    $('#duration_value').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#duration').on('change', function () {
        if ($(this).val() === 'Till Further Advice') {
            $('#duration_value').val('0').prop('disabled', true);
        } else {
            $('#duration_value').prop('disabled', false);
            $('#duration_value').val('');
        }
    });

    $('#totalPrice').on('input', function () {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, '');           
        value = value.replace(/(\..?)\../g, '$1');    
        $(this).val(value);
    });

    $('#reviewInput').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    var $reviewInput = $('#reviewInput');
    var $reviewSelect = $('#reviewSelect');
    var $reviewCalculatedDate = $('#reviewCalculatedDate');
    
    function formatDate(date) {
        var yyyy = date.getFullYear();
        var mm = String(date.getMonth() + 1).padStart(2, '0');
        var dd = String(date.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }
    
    $reviewCalculatedDate.attr('placeholder', formatDate(new Date()));
    
    function updateDate() {
        var numValue = parseInt($reviewInput.val(), 10);
        var unit = $reviewSelect.val();
        var currentDate = new Date();
        
        if (!numValue || !unit || unit === "Till Further Advice") {
        $reviewCalculatedDate.val(formatDate(currentDate));
        return;
        }
        
        var newDate = new Date(currentDate.getTime());
        
        switch(unit) {
        case "Days":
            newDate.setDate(newDate.getDate() + numValue);
            break;
        case "Weeks":
            newDate.setDate(newDate.getDate() + numValue * 7);
            break;
        case "Months":
            newDate.setMonth(newDate.getMonth() + numValue);
            break;
        default:
            break;
        }
        
        $reviewCalculatedDate.val(formatDate(newDate));
    }
    
    $reviewInput.on('input', updateDate);
    $reviewSelect.on('change', updateDate);

    function getpriscription() {
        var org_id = '<?=$SessionOrgId ?>';

        $.ajax({
            url: 'ajax/Wprescripation/GetMenu.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showPData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array =  [0, 1, 2, 3, 4]; 
                    if(org_id == "0"){
                        buttons_array = [0, 1, 2, 3, 4, 5];
                    }
                    $("#tableExportP").dataTable({
                        retrieve: true,
                        dom: 'lBrftip',
                        buttons: [
                            {
                                extend: 'copy',
                                exportOptions: {
                                columns: buttons_array
                                },
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                columns: buttons_array
                                },
                            },
                            {
                                extend: 'csv',
                                exportOptions: {
                                columns: buttons_array
                                },
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                columns: buttons_array
                                },
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                columns: buttons_array
                                },
                            },
                        ],
                    });
                }
            },
            error: function(err)  {
                console.log(err);
            }
        });
    }

    let tableCreated = false;
    let medicineArray = [];
    let editingIndex = null;

    $(document).on('click', '.adding-medicine', function () {
        let drugName = $('#drugName').val();
        let typeText = $('#medicineType').val();
        let unitText = $('#unit').val();

        let dosageId = $('#dosage').val();
        let dosageText = $('#dosage option:selected').text();

        let whenId = $('#when').val();
        let whenText = $('#when option:selected').text();

        let timeId = $('#time').val();
        let timeText = $('#time option:selected').text();

        let duration_value = $('#duration_value').val();
        let duration = $('#duration').val();

        let notes = $('#notes').val();
        let shortNotes = notes.length > 10 ? notes.substring(0, 10) + "..." : notes;

        if (!drugName || !typeText || !unitText || !dosageId || !whenId || !duration_value || !duration) {
            
            Swal.fire({
                text: 'Please fill all fields before adding.',
                confirmButtonText: 'OK'
            });
            return;
        }

        const medData = {
            drugName, typeText, unitText, dosageId, whenId, timeId,
            duration_value, duration, notes, dosageText, whenText, timeText
        };

        const medDisplay = {
            drugName, typeText, unitText, dosageText, whenText, timeText,
            duration, duration_value, shortNotes
        };

        medicineArray.push(medData);

        if (!tableCreated) {
            let table = $('<table id="medicineTable"></table>').css({
                'font-size': '12px',
                'width': '100%',
                'border-collapse': 'collapse',
                'border': '1px solid #ccc',
                'margin-top': '10px'
            });

            let thead = $('<thead></thead>').append(
                $('<tr></tr>').append(
                    $('<th>S.No</th>'),
                    $('<th>Type</th>'),
                    $('<th>Medicine</th>'),
                
                    $('<th>Unit</th>'),
                    $('<th>Dosage</th>'),
                    $('<th>In-take</th>'),
                    $('<th>Time</th>'),
                    $('<th>Duration</th>'),
                    $('<th>Note</th>'),
                    $('<th>Action</th>')
                )
            );

            thead.find('th').css({
                'padding': '8px',
                'border': '1px solid #ddd',
                'background': 'lightblue',
                'text-align': 'center',
                'font-weight': 'bold'
            });

            let tbody = $('<tbody></tbody>').css({
                'padding': '2px',
                'margin': '0',
            });

            table.append(thead).append(tbody);
            $('#medicineTableWrapper').append(table);
            tableCreated = true;
        }

        appendRowToTable(medDisplay, medicineArray.length - 1); 

        $('#drugName, #medicineType, #unit, #dosage, #when, #time, #duration_value, #notes').val('');
         $('#duration_value').prop('disabled', false);
        $('#duration').val('Days');
        
        toggleMedSpans();
    });

    function appendRowToTable(data, index) {
        let row = $('<tr></tr>');
        row.append($('<td class="sno"></td>').text(index + 1));
         row.append($('<td></td>').text(data.typeText));
        row.append($('<td></td>').text(data.drugName));
       
        row.append($('<td></td>').text(data.unitText));
        row.append($('<td></td>').text(data.dosageText));
        row.append($('<td></td>').text(data.whenText));
        row.append($('<td></td>').text(data.timeText));
        row.append($('<td></td>').text(data.duration_value + ' ' + data.duration));
        row.append($('<td></td>').text(data.shortNotes));

        row.children('td').css({
            'padding': '2px 4px',
            'border': '1px solid #ccc',
            'font-size': '12px',
            'text-align': 'center'
        });

        const actionTd = $('<td></td>').css({ 'text-align': 'center', 'border': '1px solid #ccc' });

        const editIcon = $('<i class="fas fa-edit edit-btn" title="Edit"></i>').css({
            'cursor': 'pointer', 'margin-right': '5px', 'color': '#007bff'
        }).data('index', index);

        const deleteIcon = $('<i class="fas fa-trash delete-btn" title="Delete"></i>').css({
            'cursor': 'pointer', 'color': 'red'
        });

        actionTd.append(editIcon).append(deleteIcon);
        row.append(actionTd);

        $('#medicineTable tbody').append(row);
        updateSerialNumbers();
        
    }

    function updateSerialNumbers() {
        $('#medicineTable tbody tr').each(function(i) {
            $(this).find('td.sno').text(i + 1);
        });
    }

    $(document).on('click', '.delete-btn', function () { 
        const row = $(this).closest('tr');
        const index = row.index();
        
        medicineArray.splice(index, 1);
        row.remove();

        if ($('#medicineTable tbody tr').length === 0) {
            $('#medicineTable').remove();
            tableCreated = false;
        } else {
            updateSerialNumbers();
        }
    });

    $(document).on('click', '.edit-btn', function () {
        editingIndex = $(this).data('index');
        const med = medicineArray[editingIndex];
        $('#edit_medicineType').val(med.typeText);
        $('#edit_drugName').val(med.drugName);
       
        $('#edit_unit').val(med.unitText);
        $('#edit_dosage').val(med.dosageId);
        getmodalTimeForDose(med.dosageId);
        $('#edit_when').val(med.whenId);
        $('#edit_duration_value').val(med.duration_value);
        $('#edit_duration').val(med.duration);
        $('#edit_notes').val(med.notes);
        setTimeout(() => {
            $('#edit_time').val(med.timeId);

        }, 500);

        $('#editVitalsModal').modal('show');
    });

    $('#updateVitals').on('click', function () {
        if (editingIndex !== null) {
            const updated = {
                typeText: $('#edit_medicineType').val(),
                drugName: $('#edit_drugName').val(),
                unitText: $('#edit_unit').val(),
                dosageId: $('#edit_dosage').val(),
                whenId: $('#edit_when').val(),
                timeId: $('#edit_time').val(),
                duration_value: $('#edit_duration_value').val(),
                duration: $('#edit_duration').val(),
                notes: $('#edit_notes').val(),
                dosageText: $('#edit_dosage option:selected').text(),
                whenText: $('#edit_when option:selected').text(),
                timeText: $('#edit_time option:selected').text()
            };

            medicineArray[editingIndex] = updated;

            const display = {
                typeText: updated.typeText,
                drugName: updated.drugName,
              
                unitText: updated.unitText,
                dosageText: $('#edit_dosage option:selected').text(),
                whenText: $('#edit_when option:selected').text(),
                timeText: $('#edit_time option:selected').text(),
                duration_value: updated.duration_value,
                duration: updated.duration,
                shortNotes: updated.notes.length > 10 ? updated.notes.substring(0, 10) + "..." : updated.notes
            };

            const row = $('#medicineTable tbody tr').eq(editingIndex);
            const cells = row.find('td');
            cells.eq(1).text(display.typeText);
            cells.eq(2).text(display.drugName);
          
            cells.eq(3).text(display.unitText);
            cells.eq(4).text(display.dosageText);
            cells.eq(5).text(display.whenText);
            cells.eq(6).text(display.timeText);
            cells.eq(7).text(display.duration_value + ' ' + display.duration);
            cells.eq(8).text(display.shortNotes);

            $('#editVitalsModal').modal('hide');
            editingIndex = null;
        }
    });

    let investigationArray = [];
    let editingInvestigationIndex = null;
    let investigationTableCreated = false;

    $(document).on('click', '.adding-form', function () {
        let investigation = $('#investigation').val().trim();
        let instruction = $('#testnotes').val().trim();
        let price = $('#test_price').val().trim();
        
        let concessionId = $('#concession').val();
        let concessionName = '';
        let concessionValue = '';
        let concessionType = '';
        if (concessionId) {
            let selected = $('#concession option:selected');
            concessionName = selected.text();
            concessionValue = selected.data('value');
            concessionType = selected.data('type');
        }

        if (!investigation) {
            Swal.fire({
                text: 'Please fill investigation field before adding.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!price) {
            Swal.fire({
                text: 'Please fill price field before adding.',
                confirmButtonText: 'OK'
            });
        }

    let concession = '';
    if (concessionName && concessionType && concessionValue) {
        if (concessionType.toLowerCase() === 'percentage' || concessionType === '%') {
            concession = concessionName + ' (' + concessionValue + '%)';
        } else if (concessionType.toLowerCase() === 'fixed' || concessionType.toLowerCase() === 'amount') {
            concession = concessionName + ' (₹' + concessionValue + ')';
        } else {
            concession = concessionName;
        }
    } else if (concessionName) {
        concession = concessionName;
    }

    const data = { investigation, instruction, price, test_group_id : '', test_group_name : '', test_group_price: '', concessionName, concessionValue, concessionType, concession };
    const display = { investigation, instruction, price, concession };

    investigationArray.push(data);

    if (!investigationTableCreated) {
            let tableWrapper = $(`
                <div id="investigationTableContainer" style="position:relative;">
                    <i class="fas fa-times text-danger" id="closeInvestigationTable"
                    style="position:absolute; top:-10px; right:-10px; background:white; border-radius:50%; padding:4px; border:1px solid #ccc; cursor:pointer;"
                    title="Remove Table"></i>
                </div>
            `);

            let table = $('<table id="investigationTable"></table>').css({
                'font-size': '12px',
                'width': '100%',
                'border-collapse': 'collapse',
                'border': '1px solid #ccc',
                'text-align': 'center',
                'margin-top': '10px'
            });

            let thead = $('<thead></thead>').append(
                $('<tr></tr>').append(
                    $('<th>S.No</th>'),
                    $('<th>Investigation</th>'),
                    $('<th>Instruction</th>'),
                    $('<th>Template Price</th>'),
                    $('<th>Concession</th>'),
                    $('<th>Action</th>')
                )
            );

            thead.find('th').css({
                'padding': '8px',
                'border': '1px solid #ddd',
                'background': 'lightblue',
                'text-align': 'center',
                'font-weight': 'bold'
            });

            let tbody = $('<tbody></tbody>').css({ 'padding': '2px', 'margin': '0' });

            table.append(thead).append(tbody);
            tableWrapper.append(table);
            $('#investigationTableWrapper').append(tableWrapper);
            investigationTableCreated = true;
        }

        appendInvestigationRow(display, investigationArray.length - 1);
        $('#investigation, #testnotes, #test_price').val('');
        $('#concession').val(''); 
        if ($('#test_price').next('.price-visual').length) {
            $('#test_price').next('.price-visual').html('');
        }
        toggleTestSpans();
    });

    function appendInvestigationRow(data, index) {
        let row = $('<tr></tr>');
        row.append($('<td class="sno"></td>').text(index + 1));
        row.append($('<td></td>').text(data.investigation));
        row.append($('<td></td>').text(data.instruction));
        row.append($('<td></td>').text(data.price));

        row.append($('<td></td>').text(data.concession || ''));

        row.children('td').css({
            'padding': '2px 4px',
            'border': '1px solid #ccc',
            'font-size': '12px',
            'text-align': 'center'
        });

        const actionTd = $('<td></td>').css({ 'text-align': 'center', 'border': '1px solid #ccc' });

        const editIcon = $('<i class="fas fa-edit edit-investigation-btn" title="Edit"></i>').css({
            'cursor': 'pointer', 'margin-right': '5px', 'color': '#007bff'
        }).data('index', index);

        const deleteIcon = $('<i class="fas fa-trash delete-investigation-btn" title="Delete"></i>').css({
            'cursor': 'pointer', 'color': 'red'
        });

        actionTd.append(editIcon).append(deleteIcon);
        row.append(actionTd);

        $('#investigationTable tbody').append(row);
        updateInvestigationSno();
    }

    function updateInvestigationSno() {
        $('#investigationTable tbody tr').each(function (i) {
            $(this).find('td.sno').text(i + 1);
        });
    }

    $(document).on('click', '.delete-investigation-btn', function () {
        const row = $(this).closest('tr');
        const index = row.index();

        investigationArray.splice(index, 1);
        row.remove();

        if ($('#investigationTable tbody tr').length === 0) {
            $('#investigationTable').remove();
            investigationTableCreated = false;
        } else {
            updateInvestigationSno();
        }
    });

    $(document).on('click', '.edit-investigation-btn', function () {
        editingInvestigationIndex = $(this).data('index');
        const data = investigationArray[editingInvestigationIndex];

        $('#edit_investigation').val(data.investigation);
        $('#edit_instruction').val(data.instruction);
        $('#edit_test_price').val(data.price);

        if (data.concessionName) {
            var found = false;
            $('#edit_concession option').each(function() {
                var text = $(this).text();
                if (text === data.concessionName || text.indexOf(data.concessionName) !== -1) {
                    $(this).prop('selected', true);
                    found = true;
                }
            });
            if (!found) {
                $('#edit_concession').val('');
            }
        } else {
            $('#edit_concession').val('');
        }

        function updateEditTestPriceWithConcession() {
            var investigationName = $('#edit_investigation').val();
            var basePrice = 0;
            if (typeof allTests !== 'undefined' && Array.isArray(allTests)) {
                var test = allTests.find(function (t) {
                    return t.test_name === investigationName.trim().toUpperCase();
                });
                if (test) basePrice = parseFloat(test.test_price) || 0;
            }
            var concessionType = $('#edit_concession option:selected').data('type');
            var concessionValue = $('#edit_concession option:selected').data('value');
            var discountedPrice = basePrice;
            if (concessionType && concessionValue) {
                if (concessionType.toLowerCase() === 'percentage' || concessionType === 'percentage') {
                    discountedPrice = basePrice - (basePrice * (parseFloat(concessionValue) / 100));
                } else if (concessionType.toLowerCase() === 'amount' || concessionType.toLowerCase() === 'amount') {
                    discountedPrice = basePrice - parseFloat(concessionValue);
                }
            }
            // if (discountedPrice < 0) discountedPrice = 0;
            $('#edit_test_price').val(discountedPrice.toFixed(2));
        }
        $('#edit_concession').off('change').on('change', updateEditTestPriceWithConcession);
        $('#edit_investigation').off('input.edit').on('input.edit', updateEditTestPriceWithConcession);

        updateEditTestPriceWithConcession();

        $('#editInvestigationModal').modal('show');
    });

    $('#cancelEditInvestigation').on('click', function () {
        $('#editInvestigationModal').modal('hide');
        editingInvestigationIndex = null;
    });

    $('#saveEditInvestigation').on('click', function () {
        if (editingInvestigationIndex !== null) {
            let original = investigationArray[editingInvestigationIndex] || {};
            let updated = { ...original };

            updated.investigation = $('#edit_investigation').val().trim();
            updated.instruction = $('#edit_instruction').val().trim();
            updated.price = $('#edit_test_price').val().trim();

            var $selected = $('#edit_concession option:selected');
if ($selected.length && $selected.val() !== "") {
    updated.concessionName  = $selected.text();
    updated.concessionValue = $selected.data('value');
    updated.concessionType  = $selected.data('type');

    if (updated.concessionType && updated.concessionValue) {
        if (updated.concessionType.toLowerCase() === 'percentage' || updated.concessionType === '%') {
            updated.concession = updated.concessionName + ' (' + updated.concessionValue + '%)';
        } else if (updated.concessionType.toLowerCase() === 'amount' || updated.concessionType.toLowerCase() === 'fixed') {
            updated.concession = updated.concessionName + ' (₹' + updated.concessionValue + ')';
        } else {
            updated.concession = updated.concessionName;
        }
    } else {
        updated.concession = updated.concessionName || '';
    }
} else {
    // ✅ keep old concession if nothing new is selected
    updated.concession      = original.concession || '';
    updated.concessionName  = original.concessionName || '';
    updated.concessionValue = original.concessionValue || '';
    updated.concessionType  = original.concessionType || '';
}

            
            investigationArray[editingInvestigationIndex] = updated;

            const row = $('#investigationTable tbody tr').eq(editingInvestigationIndex);
            const cells = row.find('td');

            cells.eq(1).text(updated.investigation);
            cells.eq(2).text(updated.instruction);
            cells.eq(3).text(updated.price);
            cells.eq(4).text(updated.concession);

            $('#editInvestigationModal').modal('hide');
        }
    });

    function getvitalid(appoint_register_id) {
        if (!appoint_register_id) {
            console.log("No appointment selected.");
            return; 
        }

        var organizations = $("#organizations").val();

        if (!organizations) {
            Swal.fire({
                text: 'Please select the Organization. Incase Please Login Again',
                confirmButtonText: 'OK'
            });
            return; 
        }

        $.ajax({
            url: 'ajax/Wprescripation/getvitalsdata.php',
            type: 'POST',
            dataType: 'json',
            data: { appointment_id: appoint_register_id, org_id: organizations },
            success: function(data) {

                if (data && data.appointment) {
                    const vitals = data.appointment;
                    $("#existing_bpSit_systolic").val(vitals.bpSit_systolic);
                    $("#existing_bpSit_diastolic").val(vitals.bpSit_diastolic);
                    $("#existing_bpStand_systolic").val(vitals.bpStand_systolic);
                    $("#existing_bpStand_diastolic").val(vitals.bpStand_diastolic);
                    $("#existing_weight").val(vitals.weight);
                    $("#existing_height").val(vitals.height);
                    $("#existing_bmi").val(vitals.bmi);
                    $("#existing_grbs").val(vitals.grbs);
                    $("#existing_heart_rate").val(vitals.heart_rate);
                    $("#existing_temperature").val(vitals.temperature);
                    $("#existing_respiration_rate").val(vitals.respiration_rate);
                    $("#existing_spO2").val(vitals.spO2);
                    $("#existing_patient_overview").val(vitals.patient_overview);

                    if (($('#SessionUserId').val() == '1') && ($('#SessionRoleId').val() == '1')) {
                        $('#organizations').val(vitals.org_id);
                    }

                    $("#age").val(vitals.age);

                    if (vitals.gender === 'Male') {
                        $('#male').prop('checked', true);
                    } else if (vitals.gender === 'Female') {
                        $('#female').prop('checked', true);
                    } else {
                        $('#others').prop('checked', true);
                    }
                    
                } else {
                    console.log("No data received");
                }
                $('#reviewInput').val('');
                $('#reviewSelect').val('');
                $('#chiefComplaint').val('');
                $('#pastHistory').val('');
                $('#finalDiagnosis').val('');
                $('#reviewCalculatedDate').val('');
                $('#prescription_id').val('');

                medicineArray = [];
                $('#medicineTableWrapper').empty();
                tableCreated = false;

                investigationArray = [];
                $('#investigationTableWrapper').empty();
                investigationTableCreated = false;

                if (data && data.prescription){
                    const prescription = data.prescription;

                    const reviewAfter = prescription.reviewafter; 
                    const [reviewValue, reviewUnit] = reviewAfter.split(" ");
                    $('#prescription_id').val(prescription.prescription_id);

                    $('#reviewInput').val(reviewValue);
                    $('#reviewSelect').val(reviewUnit);

                    $('#chiefComplaint').val(prescription.chiefcomplaint);
                    $('#pastHistory').val(prescription.pasthistory);
                    $('#finalDiagnosis').val(prescription.finalDiagnosis);
                    $('#reviewCalculatedDate').val(prescription.reviewafterdate);
                    $('#personal_note').val(prescription.personal_note);
                    $('#patient_data').val(prescription.patient_data);
                    $('#advise').val(prescription.advise);
                    
                    let medicineEditList = [];
                    try {
                        medicineEditList = typeof prescription.medicine_id === 'string' ? JSON.parse(prescription.medicine_id) : prescription.medicine_id;
                    } catch (e) {
                        console.error("Failed to parse medicines JSON:", e);
                        medicineEditList = [];
                    }

                    medicineArray = [];

                    if (!tableCreated) {
                        let table = $('<table id="medicineTable"></table>').css({
                            'font-size': '12px',
                            'width': '100%',
                            'border-collapse': 'collapse',
                            'border': '1px solid #ccc',
                            'margin-top': '10px'
                        });

                        let thead = $('<thead></thead>').append(
                            $('<tr></tr>').append(
                                $('<th>S.No</th>'),
                                $('<th>Type</th>'),
                                $('<th>Medicine</th>'),
                            
                                $('<th>Unit</th>'),
                                $('<th>Dosage</th>'),
                                $('<th>In-take</th>'),
                                $('<th>Time</th>'),
                                $('<th>Duration</th>'),
                                $('<th>Note</th>'),
                                $('<th>Action</th>')
                            )
                        );

                        thead.find('th').css({
                            'padding': '8px',
                            'border': '1px solid #ddd',
                            'background': 'lightblue',
                            'text-align': 'center',
                            'font-weight': 'bold'
                        });

                        let tbody = $('<tbody></tbody>').css({
                            'padding': '2px',
                            'margin': '0',
                        });

                        table.append(thead).append(tbody);
                        $('#medicineTableWrapper').append(table);
                        tableCreated = true;
                    } else {
                        $('#medicineTable tbody').empty();
                    }

                    medicineEditList.forEach((med, index) => {
                        const medData = {
                            typeText: med.type_text,
                            drugName: med.medicine_name,
                          
                            unitText: med.unit_text,
                            dosageId: med.dosage_id,
                            whenId: med.when_id,
                            timeId: med.time_id,
                            duration_value: med.duration_value,
                            duration: med.duration,
                            notes: med.notes,
                            dosageText: med.dosageText,
                            whenText: med.whenText,
                            timeText: med.timeText
                        };

                        const medDisplay = {
                            typeText: med.type_text,
                            drugName: med.medicine_name,
                          
                            unitText: med.unit_text,
                            dosageText: med.dosageText,
                            whenText: med.whenText,
                            timeText: med.timeText,
                            duration: med.duration,
                            duration_value: med.duration_value,
                            shortNotes: med.notes.length > 10 ? med.notes.substring(0, 10) + "..." : med.notes
                        };
                    
                        medicineArray.push(medData);
                        appendRowToTable(medDisplay, index);
                    
                    });

                    let testList = [];
                    try {
                        testList = typeof prescription.test_id === 'string' ? JSON.parse(prescription.test_id) : prescription.test_id;
                    } catch (e) {
                        console.error("Failed to parse tests JSON:", e);
                        testList = [];
                    }
                    investigationArray = [];

                    if (!investigationTableCreated) {
                        let table = $('<table id="investigationTable"></table>').css({
                            'font-size': '12px',
                            'width': '100%',
                            'border-collapse': 'collapse',
                            'border': '1px solid #ccc',
                            'margin-top': '10px'
                        });

                        let thead = $('<thead></thead>').append(
                            $('<tr></tr>').append(
                                $('<th>S.No</th>'),
                                $('<th>Investigation</th>'),
                                $('<th>Instruction</th>'),
                                $('<th>Price</th>'),
                                $('<th>Concession</th>'),
                                $('<th>Action</th>')
                            )
                        );

                        thead.find('th').css({
                            'padding': '8px',
                            'border': '1px solid #ddd',
                            'background': 'lightblue',
                            'text-align': 'center',
                            'font-weight': 'bold'
                        });

                        let tbody = $('<tbody></tbody>').css({ 'padding': '2px', 'margin': '0' });

                        table.append(thead).append(tbody);
                        $('#investigationTableWrapper').append(table);
                        investigationTableCreated = true;
                    } else {
                        $('#investigationTable tbody').empty();
                    }

                    testList.forEach((test, index) => {
                        const testData = {
                            investigation: test.test_name,
                            instruction: test.instruction,
                            price: test.doctor_price,
                            concession: test.concession || '',
                            concessionName: test.concessionName || '',
                            concessionValue: test.concessionValue || '',
                            concessionType: test.concessionType || '',
                            test_group_id : test.test_group_id,
                            test_group_name : test.test_group_name,
                            test_group_price : test.test_group_price,
                        };

                        investigationArray.push(testData);
                        appendInvestigationRow(testData, index);
                        console.log(testData);
                    });
                } else {
                    console.log("No prescription received");
                }
            },
            error: function(err) {
                console.log("Error fetching vital ID:", err);
            }
        });
    };

    function handleDosageChange(selectedDose) {
        if (selectedDose !== "") {
            getTimeForDose(selectedDose);
        } else {
            $("#time").html('<option value="">Select Time</option>');
        }
    };

    var medicineList = [];

    function getMedichines(id, value) {
        var org_id = $("#organizations").val();
        $.ajax({
            url: 'ajax/Wprescripation/getMedichines.php',
            type: 'post',
            data: {
                'org_id': org_id
            },
            dataType: 'json',
            success: function(data) {
                var optionData = '';
                $.each(data, function(index, val) {
                    var displayName = val.medicine_name + ' - (' + val.scientific_name + ')';

                    medicineList.push({
                        medicine_id: val.medicine_id, 
                        name: displayName
                    });

                    optionData += '<option value="' + displayName + '" data-id="' + val.medicine_id + '">' + displayName + '</option>';
                });

                var $datalist = $("#drugName" + id);
                if ($datalist.length === 0) {
                    console.log("No element found matching the selector");
                } else {
                    $datalist.html(optionData);
                    $("#drugNameDatalist"+ id).html(optionData);
                }

                if (value) {
                    $("#drugName" + id).val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getmedicinetypeandunit(input,id) {
        const selectedValue = input.value;

        const option = [...document.querySelectorAll('#drugNameDatalist option')]
        .find(opt => opt.value.trim().toUpperCase() === selectedValue.trim().toUpperCase());

        if (!option || !option.dataset.id) {
            console.warn("Medicine ID not found for selected value.");
            return;
        }

        const medicine_id = option.dataset.id;
        console.log(medicine_id);
        let currentRow = $(input).closest('.row');

        $.ajax({
            url: 'ajax/rxgroup/getMedicineDetails.php',
            type: 'POST',
            data: { medicine_id: medicine_id },
            dataType: 'json',
            success: function(results) {

                if (results.type_name) {
                    currentRow.find('.medicinetype').val(results.type_name).trigger('change');
                }

                if (results.dosage !== '0' && results.dosage !== '') {
                    currentRow.find('.unit').val(results.dosage).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
            }
        });
    }

    var medicineTypeList = [];

    function getMedicineType(id, value = null) {
        $.ajax({
            url: 'ajax/Wprescripation/getMedicineType.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                medicineTypeList = []; 
                var optionData = '';
                $.each(data, function(index, val) {
                    medicineTypeList.push({
                        type_id: val.type_id,
                        type_name: val.type_name
                    });
                    optionData += '<option value="' + val.type_name + '">';
                });

                
                $("#medicineType" + id + "Datalist").html(optionData);
                $("#edit_medicineTypeDatalist").html(optionData);
                setTimeout(() => {                    
                    $("#medicineType").val('Tab');
                }, 1000);

                if (value) {
                    $("#medicineType" + id).val(value);
                }
            },
            error: function(err) {
                console.log("Error fetching medicine types:", err);
            }
        });
    }

    var unitList = [];
 
    function getUnit(id = '', value = null) {
        $.ajax({
            url: 'ajax/Wprescripation/getUnit.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                unitList = [];
                var optionData = '';
                $.each(data, function(key, val) {
                    unitList.push({
                        unit_id: val.unit_id,
                        unit_name: val.unit_name
                    });
                    optionData += '<option value="' + val.unit_name + '">';
                });
                $("#unit" + id + "Datalist, #edit_unitDatalist").html(optionData);
                if (value) {
                    $("#unit" + id).val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getDosages(id = '', value = null) {
        $.ajax({
            url: 'ajax/Wprescripation/getDosages.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var optionData = '<option value="">Select Dosages</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.dosage_id + '">' + val.dosages + '</option>';
                });

                $("#dosage" + id).html(optionData);
                $("#edit_dosage").html(optionData);

                if (value) {
                    $("#dosage" + id).val(value);
                    $("#edit_dosage").val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
        
    function getInTakePeriod(id = '', value = null) {
        $.ajax({
            url: 'ajax/Wprescripation/getInTakePeriod.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var optionData = '<option value="">Select Intake Period</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.intake_id + '">' + val.intake_name + '</option>';
                });

                $("#when" + id).html(optionData);
                $("#edit_when").html(optionData);

                if (value) {
                    $("#when" + id).val(value);
                    $("#edit_when").val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getTimeForDose(doseId, id = '') {
        $.ajax({
            url: 'ajax/Wprescripation/getTime.php',
            type: 'get',
            data: { dose_id: doseId },
            dataType: 'json',
            success: function(data) {
                var timeOptions = '';
                if (data.length === 0) {
                    timeOptions = '<option value="">No available time</option>';
                } else {
                    $.each(data, function(index, timeItem) {
                        timeOptions += '<option value="' + timeItem.time_id + '">' + timeItem.time + '</option>';
                    });
                }
                $("#time" + id).html(timeOptions).trigger('change');
            },
            error: function(err) {
                console.error("Error retrieving time data", err);
            }
        });
    }

    function getmodalTimeForDose(doseId) {
        $.ajax({
            url: 'ajax/Wprescripation/getTime.php',
            type: 'get',
            data: { dose_id: doseId },
            dataType: 'json',
            success: function(data) {
                var timeOptions = '';
                if (data.length === 0) {
                    timeOptions = '<option value="">No available time</option>';
                } else {
                    $.each(data, function(index, timeItem) {
                        timeOptions += '<option value="' + timeItem.time_id + '">' + timeItem.time + '</option>';
                    });
                }
                $("#edit_time").html(timeOptions);
            },
            error: function(err) {
                console.error("Error retrieving time data", err);
            }
        });
    }

    var allTests = [];

    function gettests(value = null) {
        $.ajax({
            url: 'ajax/Wprescripation/getTests.php',
            type: 'get',
            dataType: 'json',
            success: function (data) {
                allTests = [];
                let optionData = '';
                $.each(data, function (index, test) {
                    var test_id = test.test_id;
                    var testName = test.test_name.trim().toUpperCase();
                    var testPrice = test.test_price;
                    allTests.push({ test_id: test_id, test_name: testName, test_price: testPrice });
                    optionData += '<option value="' + testName + '">';
                });
                $("#investigationDatalist").html(optionData);
                $("#edit_investigationDatalist").html(optionData);

                if (value) {
                    var trimmedValue = value.trim().toUpperCase();
                    $('#investigation').val(trimmedValue);
                    setTestPriceByName(trimmedValue, '#test_price');

                    $('#edit_investigation').val(trimmedValue);
                    setTestPriceByName(trimmedValue, '#edit_test_price');
                }
            },
            error: function (err) {
                console.log("Error fetching test data:", err);
            }
        });
    }

    function setTestPriceByName(name, priceFieldSelector) {
        var trimmedName = name.trim().toUpperCase();
        var test = allTests.find(function (t) {
            return t.test_name === trimmedName;
        });
        if (test) {
            $(priceFieldSelector).val(test.test_price);
        } else {
            $(priceFieldSelector).val('0');
        }
    }

    $(document).on('input', '#investigation', function () {
        setTestPriceByName($(this).val(), '#test_price');
    });

    $(document).on('input', '#edit_investigation', function () {
        setTestPriceByName($(this).val(), '#edit_test_price');
    });

    $("#FormId").submit(function() {
        event.preventDefault();

        var prescription_id = $("#prescription_id").val();
        var organizations = $("#organizations").val();
        var patientIdName = $("#patientIdName").val();
        var mobile_number = $("#mobile_number").val();
        var patientId = $("#patientId").val();
        var appoint_register_id = $("#appoint_register_id").val();
        var age = $("#age").val();
        var Gender = $("input[name='gender']:checked").val();
        var personal_note = $("#personal_note").val(); 
        var patient_data = $("#patient_data").val();
        var advise = $("#advise").val();

        var bpSit_systolic = $("#existing_bpSit_systolic").val();
        var bpSit_diastolic = $("#existing_bpSit_diastolic").val();
        var bpStand_systolic = $("#existing_bpStand_systolic").val();
        var bpStand_diastolic = $("#existing_bpStand_diastolic").val();
        var weight = $("#existing_weight").val();
        var height = $("#existing_height").val();
        var bmi = $("#existing_bmi").val();
        var grbs = $("#existing_grbs").val();
        var heart_rate = $("#existing_heart_rate").val();
        var temperature = $("#existing_temperature").val();
        var respiration_rate = $("#existing_respiration_rate").val();
        var spO2 = $("#existing_spO2").val();
        var patient_overview = $("#existing_patient_overview").val();
       
        if (!organizations) {
            Swal.fire({
                icon: 'warning', 
                title: 'Missing Patient Name',
                text: 'Please enter the Organization Id. Incase Please Login Again',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!patientIdName) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Patient Name',
                text: 'Please enter the patient name.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!mobile_number) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Mobile Number',
                text: 'Please enter the mobile number.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!patientId) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Patient ID',
                text: 'Please enter the patient ID.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!appoint_register_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Appointment ID',
                text: 'Please enter the appointment ID.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!age) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Age',
                text: 'Please enter the age.',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!Gender) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Gender',
                text: 'Please select the gender.',
                confirmButtonText: 'OK'
            });
            return;
        }

        var reviewInput = $("#reviewInput").val().replace(/\s+/g, '');
        var reviewSelect = $("#reviewSelect").val();
        var reviewCalculatedDate = $("#reviewCalculatedDate").val();

        var finalDiagnosis = $("#finalDiagnosis").val();
        var chiefComplaint = $("#chiefComplaint").val();
        var pastHistory = $("#pastHistory").val();

        const subitem = [];

        medicineArray.forEach(function(item) {
            const cleanDrugName = item.drugName ? item.drugName.replace(/\s+/g, '').toUpperCase() : '';
            const cleanType = item.typeText ? item.typeText.replace(/\s+/g, '').toUpperCase() : '';
            const cleanUnit = item.unitText ? item.unitText.replace(/\s+/g, '').toUpperCase() : '';

            const medicineObj = medicineList.find(med => {
                const medName = med.name ? med.name.replace(/\s+/g, '').toUpperCase() : '';
                return medName === cleanDrugName;
            });

            const unitObj = unitList.find(unit => {
                const unitName = unit.unit_name ? unit.unit_name.replace(/\s+/g, '').toUpperCase() : '';
                return unitName === cleanUnit;
            });

            const typeObj = medicineTypeList.find(type => {
                const typeName = type.type_name ? type.type_name.replace(/\s+/g, '').toUpperCase() : '';
                return typeName === cleanType;
            });

            const medicine_id = medicineObj ? medicineObj.medicine_id : null;
            const unit_id = unitObj ? unitObj.unit_id : null;
            const type_id = typeObj ? typeObj.type_id : null;

            subitem.push({
                medicine_id: medicine_id,
                medicine_name: item.drugName || '',  
                type_id: type_id,
                type_text: item.typeText || '',      
                unit_id: unit_id,
                unit_text: item.unitText || '',      
                dosage_id: item.dosageId,
                when_id: item.whenId,
                time_id: item.timeId,
                duration_value: item.duration_value,
                duration: item.duration,
                notes: item.notes,
                med_status: '1',
                timeText: item.timeText,
                dosageText: item.dosageText,
                whenText: item.whenText

            });
        });

        const investigationItems = [];
        let totalDoctorPrice = 0;
        let totalStandardPrice = 0;

        investigationArray.forEach(item => {
            const enteredName = item.investigation ? item.investigation.trim().replace(/\s+/g, '').toUpperCase() : '';
            const enteredPrice = parseFloat(item.price) || 0;

            const matchedTest = allTests.find(test => {
                const testName = test.test_name ? test.test_name.trim().replace(/\s+/g, '').toUpperCase() : '';
                return testName === enteredName;
            });

            const test_id = matchedTest ? matchedTest.test_id : null;
            const standardPrice = matchedTest ? parseFloat(matchedTest.test_price) || 0 : 0;

            totalDoctorPrice += enteredPrice;
            totalStandardPrice += standardPrice;

            investigationItems.push({
                test_id: test_id,
                test_name: item.investigation || '',
                instruction: item.instruction || '',
                concession: item.concession || '',
                concessionName: item.concessionName || '',
                concessionValue: item.concessionValue || '',
                concessionType: item.concessionType || '',
                doctor_price: enteredPrice,
                standard_price: standardPrice,
                test_status: '1',
                test_group_id: item.test_group_id || '', 
                test_group_name: item.test_group_name || '', 
                test_group_price: item.test_group_price || '',
            });
        });

        const postData = {
            prescription_id,
            organizations,
            patientIdName,
            mobile_number,
            patientId,
            appoint_register_id,
            age,
            Gender,
            reviewInput,
            reviewSelect,
            reviewCalculatedDate,
            finalDiagnosis,
            chiefComplaint,
            pastHistory,
            personal_note,
            patient_data,
            advise,
            medicine: subitem,
            investigation: investigationItems,
            totalDoctorPrice,
            totalStandardPrice,

            bpSit_systolic,
            bpSit_diastolic,
            bpStand_systolic,
            bpStand_diastolic,
            weight,
            height,
            bmi,
            grbs,
            heart_rate,
            temperature,
            respiration_rate,
            spO2,
            patient_overview

        };

        $.ajax({
            url: 'ajax/Wprescripation/addpatient.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            success: function (data) {
                if (data == 1) {
                    Swal.fire({
                        title: '',
                        text: 'Prescription Added Successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        $("#prescription_id").val('');
                        getpriscription();
                        location.reload();
                    });
                } else if (data == 2) {
                    Swal.fire({
                        title: '',
                        text: 'Prescription updated Successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        $("#prescription_id").val('');
                        getpriscription();
                        location.reload();
                    });
                } else {
                    Swal.fire('', 'All fields required', 'warning');
                }
            },
            error: function (err) {
                console.log(err);
                Swal.fire('Error', 'An error occurred while submitting.', 'error');
            }
        });

    });

    function editP(prescription_id, 
                    organizations, 
                    patientIdName, 
                    patientId, 
                    appoint_register_id, 
                    age, 
                    gender, 
                    rx_id, 
                    test_group_id, 
                    tests,
                    medicines,
                    prescriptiondate,
                    patient_vitals,
                    finalDiagnosis,
                    chiefcomplaint,
                    pasthistory,
                    personal_note,
                    reviewafter,
                    reviewafterdate
                ) {
        window.scrollTo(0,0);

    }

    $(document).on("click", ".edit-prescription", function (e) {
        e.preventDefault();

        const data = $(this).data("prescription");        
        
        $('#prescription_id').val(data.prescription_id);
        $('#age').val(data.age);

        const gender = data.gender; 
        $("input[name='gender'][value='" + gender + "']").prop("checked", true);

        $("#male").prop("checked", gender === "Male");
        $("#female").prop("checked", gender === "Female");
        $("#others").prop("checked", gender === "Other");
        $('#existing_bmi').val(data.bmi);
        $('#existing_weight').val(data.weight);
        $('#existing_height').val(data.height);
        $('#existing_heart_rate').val(data.heart_rate);
        $('#existing_spO2').val(data.spO2);
        $('#existing_grbs').val(data.grbs);
        $('#existing_respiration_rate').val(data.respiration_rate);
        $('#existing_bpSit_systolic').val(data.bpSit_systolic);
        $('#existing_bpSit_diastolic').val(data.bpSit_diastolic);
        $('#existing_bpStand_systolic').val(data.bpStand_systolic);
        $('#existing_bpStand_diastolic').val(data.bpStand_diastolic); 
        $('#existing_temperature').val(data.temperature);
        $('#existing_patient_overview').val(data.patient_overview);

        const reviewAfter = data.reviewafter; 
        const [reviewValue, reviewUnit] = reviewAfter.split(" ");

        $('#reviewInput').val(reviewValue);
        $('#reviewSelect').val(reviewUnit);

        // $('#mobile_number').val(data.mobile_number); 
        // $('#patientId').val(data.patientId);
        // $('#patientIdName').val(data.patientIdName);
        // $('#appoint_register_id').val(data.appoint_register_id);

        $('#organizations').val(data.organizations);
        $('#prescriptiondate').val(data.prescriptiondate);
        $('#personal_note').val(data.personal_note);
        $('#reviewafter').val(data.reviewafter);
        $('#reviewCalculatedDate').val(data.reviewafterdate);
        $('#patient_vitals').val(data.patient_vitals);
        $('#chiefComplaint').val(data.chiefcomplaint);
        
        $('#pastHistory').val(data.pasthistory);
        $('#finalDiagnosis').val(data.finalDiagnosis);
        $('#patient_overview').val(data.patient_overview);


        let medicineEditList = [];
        try {
            medicineEditList = typeof data.medicines === 'string' ? JSON.parse(data.medicines) : data.medicines;
        } catch (e) {
            console.error("Failed to parse medicines JSON:", e);
            medicineEditList = [];
        }

        medicineArray = [];

        if (!tableCreated) {
            let table = $('<table id="medicineTable"></table>').css({
                'font-size': '12px',
                'width': '100%',
                'border-collapse': 'collapse',
                'border': '1px solid #ccc',
                'margin-top': '10px'
            });

            let thead = $('<thead></thead>').append(
                $('<tr></tr>').append(
                    $('<th>S.No</th>'),
                    $('<th>Medicine</th>'),
                    $('<th>Type</th>'),
                    $('<th>Unit</th>'),
                    $('<th>Dosage</th>'),
                    $('<th>In-take</th>'),
                    $('<th>Time</th>'),
                    $('<th>Duration</th>'),
                    $('<th>Note</th>'),
                    $('<th>Action</th>')
                )
            );

            thead.find('th').css({
                'padding': '8px',
                'border': '1px solid #ddd',
                'background': 'lightblue',
                'text-align': 'center',
                'font-weight': 'bold'
            });

            let tbody = $('<tbody></tbody>').css({
                'padding': '2px',
                'margin': '0',
            });

            table.append(thead).append(tbody);
            $('#medicineTableWrapper').append(table);
            tableCreated = true;
        } else {
            $('#medicineTable tbody').empty();
        }

        medicineEditList.forEach((med, index) => {
            const medData = {
                drugName: med.medicine_name,
                typeText: med.type_text,
                unitText: med.unit_text,
                dosageId: med.dosage_id,
                whenId: med.when_id,
                timeId: med.time_id,
                duration_value: med.duration_value,
                duration: med.duration,
                notes: med.notes,
                dosageText: med.dosageText,
                whenText: med.whenText,
                timeText: med.timeText
            };

            const medDisplay = {
                drugName: med.medicine_name,
                typeText: med.type_text,
                unitText: med.unit_text,
                dosageText: med.dosageText,
                whenText: med.whenText,
                timeText: med.timeText,
                duration: med.duration,
                duration_value: med.duration_value,
                shortNotes: med.notes.length > 10 ? med.notes.substring(0, 10) + "..." : med.notes
            };

            medicineArray.push(medData);
            appendRowToTable(medDisplay, index);
        
        });

        let testList = [];
        try {
            testList = typeof data.tests === 'string' ? JSON.parse(data.tests) : data.tests;
        } catch (e) {
            console.error("Failed to parse tests JSON:", e);
            testList = [];
        }

        investigationArray = [];

        if (!investigationTableCreated) {
            let table = $('<table id="investigationTable"></table>').css({
                'font-size': '12px',
                'width': '100%',
                'border-collapse': 'collapse',
                'border': '1px solid #ccc',
                'margin-top': '10px'
            });

            let thead = $('<thead></thead>').append(
                $('<tr></tr>').append(
                    $('<th>S.No</th>'),
                    $('<th>Investigation</th>'),
                    $('<th>Instruction</th>'),
                    $('<th>Price</th>'),
                    $('<th>Concession</th>'),
                    $('<th>Action</th>')
                )
            );

            thead.find('th').css({
                'padding': '8px',
                'border': '1px solid #ddd',
                'background': 'lightblue',
                'text-align': 'center',
                'font-weight': 'bold'
            });

            let tbody = $('<tbody></tbody>').css({ 'padding': '2px', 'margin': '0' });

            table.append(thead).append(tbody);
            $('#investigationTableWrapper').append(table);
            investigationTableCreated = true;
        } else {
            $('#investigationTable tbody').empty();
        }

        testList.forEach((test, index) => {
            const testData = {
                investigation: test.test_name,
                instruction: test.instruction,
                price: test.doctor_price,
                concession: test.concession,
                test_group_id: test.test_group_id, 
                test_group_name: test.test_group_name, 
                test_group_price: test.test_group_price
            };

            investigationArray.push(testData);
            appendInvestigationRow(testData, index);
        });

    });

    function deleteP(prescription_id, patientName) {
        swal({
            title: "Are you sure?",
            text: "Do you really want to Delete Prescription Record?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/Wprescripation/delete.php',
                    type: 'POST',
                    data: {
                        'prescription_id': prescription_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal('',  'Deleted Successfully', 'success');
                            getpriscription();
                            clearData();
                            $('#deleteID').val(prescription_id);
                            $('#deleteFormId').submit();
                        } else {
                            swal('Error','Error occurred. Please try again', 'error');
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                        swal('Error', 'AJAX error occurred. Check console for details.', 'error');
                    }
                });
            }
        });
    }

    $(document).on('click', '.delet-btn', function () {
        $(this).closest('.open-form').remove();
    });

    function myFunction(prescription_id) {
        location.replace("patientPrescription.php?ItemId=" + prescription_id); 
    }

    function duration(NewInputsCountIni) {
        $("#duration").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError").html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError").html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });

        if (typeof NewInputsCountIni == 'number') {
            $("#duration" + NewInputsCountIni).keypress(function (e) {
                var keyCode = e.keyCode || e.which;
                $("#lblError" + NewInputsCountIni).html("");
                var regex = /^[A-Za-z0-9 ]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#lblError" + NewInputsCountIni).html("Only Alphabets&Number allowed.");
                }
                return isValid;
            });

            $(function () {
                $("#duration"+ NewInputsCountIni).on("paste", function (e) {
                    var pastedData = e.originalEvent.clipboardData.getData("text/plain");
                    var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
                    document.execCommand("insertText", false, cleanedValue);
                    e.preventDefault();
                });
            });
        }

        $(function () {
            $("#duration").keyup(function () {
                var duration = $(this).val();
                if (!duration.trim()) {
                $(this).val('');
                }
            });
            $("#duration"+ NewInputsCountIni).keyup(function () {
                var duration = $(this).val();
                if (!duration.trim()) {
                $(this).val('');
               }
            });
            $(function () {
                $("#duration"+ NewInputsCountIni).on("paste", function (e) {
                    var pastedData = e.originalEvent.clipboardData.getData("text/plain");
                    var cleanedValue = pastedData.replace(/[^\d\10/10a-z]+/g, ""); // Remove non-alphabetic characters
                    document.execCommand("insertText", false, cleanedValue);
                    e.preventDefault();
                });
            });
        });
    }

    function quantity(NewInputsCountIni) {
        $("#quantity").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError").html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });

        if (typeof NewInputsCountIni == 'number') {
            $("#quantity" + NewInputsCountIni).keypress(function (e) {
                var keyCode = e.keyCode || e.which;
                $("#lblError" + NewInputsCountIni).html("");
                var regex = /^[0-9]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#lblError" + NewInputsCountIni).html("Only Alphabets&Number allowed.");
                }
                return isValid;
            });

            $(function () {
                $("#quantity"+ NewInputsCountIni).on("paste", function (e) {
                    var pastedData = e.originalEvent.clipboardData.getData("text/plain");
                    var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); 
                    document.execCommand("insertText", false, cleanedValue);
                    e.preventDefault();
                });
            });
        }

        $(function () {

            $("#quantity").keyup(function () {
                var quantity = $(this).val();
                if (!quantity.trim()) {
                $(this).val('');
                }
            });

            $("#quantity"+ NewInputsCountIni).keyup(function () {
                var quantity = $(this).val();
                if (!quantity.trim()) {
                $(this).val('');
               }
            });

            $(function () {
                $("#quantity"+ NewInputsCountIni).on("paste", function (e) {
                    var pastedData = e.originalEvent.clipboardData.getData("text/plain");
                    var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
                    document.execCommand("insertText", false, cleanedValue);
                    e.preventDefault();
                });
            });
        });
    }

    $(function () {
        $("#quantity").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#gstNumberId").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#gstNumberId").html("Only Alphabets and Numbers Allowed.");
            }
            return isValid;
        });
    });

    $(function () {
        $("#quantity").on("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    $(function () {
            $("#age").keypress(function (e) {
                var keyCode = e.keyCode || e.which;
                $("#ageID").html("");
                var regex = /^[0-9]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#ageID").html("Only Alphabets Allowed.");
                }
                return isValid;
         });
        });

    $(function () {
        $("#age").on("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); 
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    function GetNameByAgeGender() {
        let selectElement = document.getElementById("patientId");
        if (!selectElement) {
            console.error("Select element not found.");
            return;
        }
        var organizations = $("#organizations").val();

        if (!organizations) {
            Swal.fire({
                text: 'Please select the Organization. In case, please login again.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            return;
        }

        let selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption) {
            console.error("No option selected.");
            return;
        }

        let customValue = selectedOption.getAttribute("data-custom-value");
        if (!customValue) {
            console.log("Patient name is required.");
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/GetNameByAgeGender.php',
            type: 'POST',
            data: { customValue: customValue },
            dataType: 'json',
            success: function(data) {
                var AppointmentIdSelect = $("#appoint_register_id");
                var ageSelect = $("#age");
                var genderSelect = $("input[name='gender']");

                AppointmentIdSelect.empty();
                ageSelect.empty();

                $.each(data, function(_, val) {
                    AppointmentIdSelect.append($('<option>', {
                        value: val.appoint_register_id,
                        text: val.appoint_register_id
                    }));
                });

                if (data.length > 0) {
                    AppointmentIdSelect.val(data[0].appoint_register_id);
                    AppointmentIdSelect.trigger('change');
                    ageSelect.val(data[0].age);
                    genderSelect.filter('[value="' + data[0].gender + '"]').prop('checked', true);
                } else {
                    genderSelect.prop('checked', false);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    function GetPreviousPrescriptionData() {
        let selectElement = document.getElementById("patientId");
        let patientId = selectElement.value;

        if (!patientId) {
            console.error("Patient ID not found.");
            return;
        }

        var organizations = $("#organizations").val();

        $.ajax({
            url: 'ajax/Wprescripation/GetPreviousPrescriptionData.php',
            type: 'POST',
            data: { patient_id: patientId, organizations: organizations },
            dataType: 'json',
            success: function(data) {

                if (data && data.prescription) {
                    const prescription = data.prescription;
                    const prescriptionDate = prescription.prescriptiondate;
                    const today = new Date().toISOString().split('T')[0];

                    if (prescriptionDate !== today) {
                        // alert();
                        const vitals = data.appointment;
                        
                        $("#existing_bpSit_systolic").val(vitals.bpSit_systolic);
                        $("#existing_bpSit_diastolic").val(vitals.bpSit_diastolic);
                        $("#existing_bpStand_systolic").val(vitals.bpStand_systolic);
                        $("#existing_bpStand_diastolic").val(vitals.bpStand_diastolic);
                        $("#existing_weight").val(vitals.weight);
                        $("#existing_height").val(vitals.height);
                        $("#existing_bmi").val(vitals.bmi);
                        $("#existing_grbs").val(vitals.grbs);
                        $("#existing_heart_rate").val(vitals.heart_rate);
                        $("#existing_temperature").val(vitals.temperature);
                        $("#existing_respiration_rate").val(vitals.respiration_rate);
                        $("#existing_spO2").val(vitals.spO2);
                        $("#existing_patient_overview").val(vitals.patient_overview);
                        
                        if (($('#SessionUserId').val() == '1') && ($('#SessionRoleId').val() == '1')) {
                            $('#organizations').val(vitals.org_id);
                        }

                        $("#age").val(vitals.age);

                        if (vitals.gender === 'Male') {
                            $('#male').prop('checked', true);
                        } else if (vitals.gender === 'Female') {
                            $('#female').prop('checked', true);
                        } else {
                            $('#others').prop('checked', true);
                        }

                        $('#reviewInput').val('');
                        $('#reviewSelect').val('');
                        $('#chiefComplaint').val('');
                        $('#pastHistory').val('');
                        $('#finalDiagnosis').val('');
                        $('#reviewCalculatedDate').val('');
                        $('#prescription_id').val('');

                        medicineArray = [];
                        $('#medicineTableWrapper').empty();
                        tableCreated = false;

                        investigationArray = [];
                        $('#investigationTableWrapper').empty();
                        investigationTableCreated = false;

                        const reviewAfter = prescription.reviewafter;
                        const [reviewValue, reviewUnit] = reviewAfter.split(" ");
                        $('#reviewInput').val(reviewValue);
                        $('#reviewSelect').val(reviewUnit);
                        $('#chiefComplaint').val(prescription.chiefcomplaint);
                        $('#pastHistory').val(prescription.pasthistory);
                        $('#finalDiagnosis').val(prescription.finalDiagnosis);
                        $('#reviewCalculatedDate').val(prescription.reviewafterdate);

                        let medicineEditList = [];
                        try {
                            medicineEditList = typeof prescription.medicine_id === 'string' ? JSON.parse(prescription.medicine_id) : prescription.medicine_id;
                        } catch (e) {
                            console.error("Failed to parse medicines JSON:", e);
                            medicineEditList = [];
                        }

                        medicineArray = [];

                        if (!tableCreated) {
                            let table = $('<table id="medicineTable"></table>').css({
                                'font-size': '12px',
                                'width': '100%',
                                'border-collapse': 'collapse',
                                'border': '1px solid #ccc',
                                'margin-top': '10px'
                            });

                            let thead = $('<thead></thead>').append(
                                $('<tr></tr>').append(
                                    $('<th>S.No</th>'),
                                    $('<th>Medicine</th>'),
                                    $('<th>Type</th>'),
                                    $('<th>Unit</th>'),
                                    $('<th>Dosage</th>'),
                                    $('<th>In-take</th>'),
                                    $('<th>Time</th>'),
                                    $('<th>Duration</th>'),
                                    $('<th>Note</th>'),
                                    $('<th>Action</th>')
                                )
                            );

                            thead.find('th').css({
                                'padding': '8px',
                                'border': '1px solid #ddd',
                                'background': 'lightblue',
                                'text-align': 'center',
                                'font-weight': 'bold'
                            });

                            let tbody = $('<tbody></tbody>').css({
                                'padding': '2px',
                                'margin': '0',
                            });

                            table.append(thead).append(tbody);
                            $('#medicineTableWrapper').append(table);
                            tableCreated = true;
                        } else {
                            $('#medicineTable tbody').empty();
                        }

                        medicineEditList.forEach((med, index) => {
                            const medData = {
                                drugName: med.medicine_name,
                                typeText: med.type_text,
                                unitText: med.unit_text,
                                dosageId: med.dosage_id,
                                whenId: med.when_id,
                                timeId: med.time_id,
                                duration_value: med.duration_value,
                                duration: med.duration,
                                notes: med.notes,
                                dosageText: med.dosageText,
                                whenText: med.whenText,
                                timeText: med.timeText
                            };

                            const medDisplay = {
                                drugName: med.medicine_name,
                                typeText: med.type_text,
                                unitText: med.unit_text,
                                dosageText: med.dosageText,
                                whenText: med.whenText,
                                timeText: med.timeText,
                                duration: med.duration,
                                duration_value: med.duration_value,
                                shortNotes: med.notes.length > 10 ? med.notes.substring(0, 10) + "..." : med.notes
                            };

                            medicineArray.push(medData);
                            appendRowToTable(medDisplay, index);
                        });

                        // Investigations
                        let testList = [];
                        try {
                            testList = typeof prescription.test_id === 'string' ? JSON.parse(prescription.test_id) : prescription.test_id;
                        } catch (e) {
                            console.error("Failed to parse tests JSON:", e);
                            testList = [];
                        }

                        investigationArray = [];

                        if (!investigationTableCreated) {
                            let table = $('<table id="investigationTable"></table>').css({
                                'font-size': '12px',
                                'width': '100%',
                                'border-collapse': 'collapse',
                                'border': '1px solid #ccc',
                                'margin-top': '10px'
                            });

                            let thead = $('<thead></thead>').append(
                                $('<tr></tr>').append(
                                    $('<th>S.No</th>'),
                                    $('<th>Investigation</th>'),
                                    $('<th>Instruction</th>'),
                                    $('<th>Price</th>'),
                                    $('<th>Concession</th>'),
                                    $('<th>Action</th>')
                                )
                            );

                            thead.find('th').css({
                                'padding': '8px',
                                'border': '1px solid #ddd',
                                'background': 'lightblue',
                                'text-align': 'center',
                                'font-weight': 'bold'
                            });

                            let tbody = $('<tbody></tbody>').css({ 'padding': '2px', 'margin': '0' });

                            table.append(thead).append(tbody);
                            $('#investigationTableWrapper').append(table);
                            investigationTableCreated = true;
                        } else {
                            $('#investigationTable tbody').empty();
                        }

                        testList.forEach((test, index) => {
                            const testData = {
                                investigation: test.test_name,
                                instruction: test.instruction,
                                price: test.doctor_price,
                                concession: test.concession,
                                test_group_id: test.test_group_id,
                                test_group_name: test.test_group_name,
                                test_group_price: test.test_group_price
                            };

                            investigationArray.push(testData);
                            appendInvestigationRow(testData, index);
                        });
                        console.log(testData);


                    } else {
                        $('#appoint_register_id').trigger('change');
                    }

                } else {
                    // alert();
                    const vitals = data.appointment;
                        
                        $("#existing_bpSit_systolic").val(vitals.bpSit_systolic);
                        $("#existing_bpSit_diastolic").val(vitals.bpSit_diastolic);
                        $("#existing_bpStand_systolic").val(vitals.bpStand_systolic);
                        $("#existing_bpStand_diastolic").val(vitals.bpStand_diastolic);
                        $("#existing_weight").val(vitals.weight);
                        $("#existing_height").val(vitals.height);
                        $("#existing_bmi").val(vitals.bmi);
                        $("#existing_grbs").val(vitals.grbs);
                        $("#existing_heart_rate").val(vitals.heart_rate);
                        $("#existing_temperature").val(vitals.temperature);
                        $("#existing_respiration_rate").val(vitals.respiration_rate);
                        $("#existing_spO2").val(vitals.spO2);
                        $("#existing_patient_overview").val(vitals.patient_overview);
                        
                        if (($('#SessionUserId').val() == '1') && ($('#SessionRoleId').val() == '1')) {
                            $('#organizations').val(vitals.org_id);
                        }

                        $("#age").val(vitals.age);

                        if (vitals.gender === 'Male') {
                            $('#male').prop('checked', true);
                        } else if (vitals.gender === 'Female') {
                            $('#female').prop('checked', true);
                        } else {
                            $('#others').prop('checked', true);
                        }
                    getvitalid(val.appoint_register_id);

                    console.log("No prescription received");
                }

            },
            error: function(err) {
                console.error("AJAX error:", err);
            }
        });
    }

    function OrgIdByPatientNames(){
        var org_id = $('#organizations').val();

        var organizations = $("#organizations").val();

        if (!organizations) {
            Swal.fire({
                text: 'Please select the Organization. In case, please login again.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            return;
        }
        $.ajax({
            url: 'ajax/Wprescripation/GetOrgIdByPatients.php',
            type: 'POST',
            data: { org_id: org_id },
            dataType: 'json',
            success: function(data) {
                var optionDataT = ''; 
                var optionDataPN = '<option value=""> Select Name </option>';
                var optionDataRX = '<option value=""> Select RX Group </option>';
                var optionDataTG = '<option value=""> Select Test Groups </option>';
                var optionDataMN = '<option value=""> Select Medicine Name </option>';
                $.each(data, function(key, val) {

                    for(var i = 0; i < (val.patients).length; i++ ) {
                        optionDataPN +='<option value="'+ val.patients[i] +'">'+ val.patients[i] +'</option>';  
                    }
                    
                    $.each(val.rx_groups, function(key1, val1){
                        optionDataRX +='<option value="'+ val1.rx_group_id +'">'+ val1.rx_group_name +'</option>';  
                    })
                    
                    $.each(val.test_groups, function(key1, val1){
                        optionDataTG +='<option value="'+ val1.test_group_id +'">'+ val1.test_group_name +'</option>';  
                    })
                    
                    $.each(val.tests, function(key1, val1){
                        optionDataT +='<option value="'+ val1.test_id +'">'+ val1.test_name +'</option>';  
                    })
                    
                    $.each(val.medicines, function(key1, val1){
                        optionDataMN +='<option value="'+ val1.medicine_id +'">'+ val1.medicine_name +'</option>';  
                    })
                
                });
                
                $("#patientIdName").html(optionDataPN);
                $("#RX_Group_Id").html(optionDataRX);
                $("#test_group_id").html(optionDataTG);
                Timing.destroy();
                $("#tests").html(optionDataT);
                $(".medicine").html(optionDataMN);
                Timing = new Choices('#tests', {
                    removeItemButton: true,
                }); 
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function GetPatientNameAndIDByNumber() {
        var patient_number = $('#mobile_number').val();
        var organizations = $("#organizations").val();

        if (!organizations) {
            Swal.fire({
                text: 'Please select the Organization. In case, please login again.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/getPatientNameAndIdByNumber.php',
            type: 'POST',
            data: { patient_number: patient_number },
            dataType: 'json',
            success: function(data) {
                var appointUnicodeSelect = $("#patientId");
                var PatientNameSelect    = $("#patientIdName");
                var AppointNumberSelect  = $("#appoint_register_id");

                appointUnicodeSelect.empty();
                PatientNameSelect.empty();
                AppointNumberSelect.empty();

                $.each(data, function(_, val) {
          
                    appointUnicodeSelect.append($('<option>', {
                        value: val.appoint_unicode,
                        text: val.appoint_unicode
                    }));

                    PatientNameSelect.append($('<option>', {
                        value: val.patient_name,
                        text: val.patient_name
                    }));

                    AppointNumberSelect.append($('<option>', {
                        value: val.appoint_register_id,
                        text: val.appoint_register_id
                    }));

                    $("#age").val(val.age);

                    if (val.gender === "Male") {
                        $("#male").prop("checked", true);
                    } else if (val.gender === "Female") {
                        $("#female").prop("checked", true);
                    } else if (val.gender === "Others") {
                        $("#others").prop("checked", true);
                    }
                });

                appointUnicodeSelect.prop('selectedIndex', 0);
                PatientNameSelect.prop('selectedIndex', 0);
                AppointNumberSelect.prop('selectedIndex', 0);
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
            }
        });
    }
    
    function GetNumberANDIdByName() {
        var patient_name = $('#patientIdName').val();
        var organizations = $("#organizations").val();

        if (!organizations) {
            Swal.fire({
                text: 'Please select the Organization. In case, please login again.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/getPatientNumberAndIdByName.php',
            type: 'POST',
            data: {
                patient_name: patient_name
            },
            dataType: 'json',
            success: function(data) {
                var appointUnicodeSelect = $("#patientId");
                var PatientNumberSelect = $("#mobile_number");
                var AppointNumberSelect = $("#appoint_register_id");

                appointUnicodeSelect.empty();
                PatientNumberSelect.empty();
                AppointNumberSelect.empty();

                $.each(data, function(_, val) {
                    appointUnicodeSelect.append($('<option>', {
                        value: val.appoint_unicode,
                        text: val.appoint_unicode
                    }));

                    if (appointUnicodeSelect !== "") {
                        document.getElementById('reportsBtn').classList.remove('d-none');
                    } else {
                        document.getElementById('reportsBtn').classList.add('d-none');
                    }

                    PatientNumberSelect.append($('<option>', {
                        value: val.mobile_number,
                        text: val.mobile_number
                    }));

                    <?php if (!empty($registrationid)) : ?>
                        AppointNumberSelect.append($('<option>', {
                            value: "<?php echo htmlspecialchars($registrationid); ?>",
                            text: "<?php echo htmlspecialchars($registrationid); ?>"
                        }));
                    <?php else : ?>
                        AppointNumberSelect.append($('<option>', {
                            value: val.appoint_register_id,
                            text: val.appoint_register_id
                        }));
                    <?php endif; ?>

                    $("#age").val(val.age);

                    if (val.gender === "Male") {
                        $("#male").prop("checked", true);
                    } else if (val.gender === "Female") {
                        $("#female").prop("checked", true);
                    } else if (val.gender === "Others") {
                        $("#others").prop("checked", true);
                    }
                });

                appointUnicodeSelect.prop('selectedIndex', 0);
                PatientNumberSelect.prop('selectedIndex', 0);
                AppointNumberSelect.prop('selectedIndex', 0);
                appointUnicodeSelect.trigger('change');
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
            }
        });
    }

    function CheckReports() {
        const patientId = $("#patientId").val();
        const OrgId = $("#organizations").val();

        const url = `doctorview.php?patientId=${patientId}&OrgId=${OrgId}`;

        window.open(url, '_blank');
    }
    
    $(function() {
        $('#organizations').on('change', function() {
            $('.fa-history').trigger('click');
        });

        $('#templateForm').on('submit', function(e) {
            e.preventDefault();

            const fd_id = $("#templateId").val();
            const templateName = $('#templateName').val().trim();
            const finaldiagnosis = $('#readonlyDiagnosis').val().trim();
            const org_id = $('#organizations').val(); 

            if (!templateName || !finaldiagnosis) {
                Swal.fire('Error', 'Both Template Name and Final Diagnosis are required.', 'error');
                return;
            }

            const requestData = {
                template_name: templateName,
                diagnosis_data: finaldiagnosis,
                org_id: org_id 
            };

            const url = fd_id ? 'ajax/Wprescripation/updatefinaldiagnosis.php' 
                            : 'ajax/Wprescripation/addfinaldiagnosis.php';
            
            if (fd_id) requestData.fd_id = fd_id;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: requestData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: fd_id ? 'Template Updated!' : 'Template Saved!',
                            text: `Your Final Diagnosis template was ${fd_id ? 'updated' : 'saved'} successfully.`
                        });
                        resetTemplateForm();
                        $('.fa-history').trigger('click'); 
                    } else {
                        showError(response.error || 'Operation failed');
                    }
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.error || 'Server request failed');
                }
            });
        });

        $(document).on('click', '.template-option', function(e) {
            if ($(e.target).closest('.edit-template, .FDdelete-template').length) return;

            const templateText = decodeURIComponent($(this).data('content')).trim();
            const $textarea = $('#finalDiagnosis');
            const currentText = $textarea.val().trim();

            if (currentText.includes(templateText)) return;

            $textarea.val(currentText ? `${currentText}\n${templateText}` : templateText);
            $(this).addClass('bg-light opacity-75');
        });

        $(document).on('click', '.FDdelete-template', function(e) {
            e.stopPropagation();

            const templateId = $(this).data('id');
            const $templateDiv = $(this).closest('.template-option');

            Swal.fire({
                title: 'Confirm Deletion',
                text: "This template will be permanently deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: 'ajax/Wprescripation/deleteTemplate.php',
                        type: 'POST',
                        data: { 
                            id: templateId,
                            org_id: $('#organizations').val() 
                        },
                        success: function(response) {
                            if (response.success) {
                                $templateDiv.remove();
                                Swal.fire('Deleted!', '', 'success');
                            } else {
                                showError(response.error || 'Deletion failed');
                            }
                        },
                        error: function() {
                            showError('Request failed');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.fa-history', function() {
            const orgId = $('#organizations').val();
            
            $.ajax({
                url: 'ajax/Wprescripation/getTemplates.php',
                type: 'GET',
                data: { org_id: orgId },
                dataType: 'json',
                success: function(response) {
                    const $dropdown = $('#templateDropdown');
                    $dropdown.empty();

                    if (response.success && response.templates.length > 0) {
                        response.templates.forEach(template => {
                            $dropdown.append(`
                                <div class="template-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start"
                                    data-content="${encodeURIComponent(template.template_data)}">
                                    <div>
                                        <strong>${template.template_name}</strong>
                                        <p class="mb-0 small text-muted">${template.template_data}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-center ms-2">
                                        <i class="fas fa-edit mb-2 text-primary edit-template" 
                                        onclick="gettemplatedata('${template.fd_id}')"></i>
                                        <i class="fas fa-trash-alt text-danger FDdelete-template" 
                                        data-id="${template.fd_id}"></i>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        $dropdown.html('<div class="text-muted p-2">No templates saved for this organization.</div>');
                    }
                },
                error: function(xhr) {
                    $('#templateDropdown').html(
                        `<div class="text-danger p-2">Failed to load templates. ${xhr.responseJSON?.error || ''}</div>`
                    );
                }
            });
        });

        $('#addTemplateBtn').on('click', function() {
            const finalDiagnosis = $('#finalDiagnosis').val().trim();
            if (!finalDiagnosis) {
                Swal.fire('Warning', 'Please enter diagnosis first', 'warning');
                return;
            }
            resetTemplateForm();
            $('#readonlyDiagnosis').val(finalDiagnosis);
            $('#templateModal').modal('show');
        });
    });

    function gettemplatedata(id) {
        $.ajax({
            url: 'ajax/Wprescripation/getTemplatesbyID.php',
            type: 'POST',
            data: { 
                id: id,
                org_id: $('#organizations').val() 
            },
            success: function(response) {

                if (response.success) {
                    $('#templateName').val(response.template_name);
                    $('#readonlyDiagnosis').val(response.template_data);
                    $('#templateModalLabel').text('Edit Template');
                    $('button[type="submit"]').text('Update Template');
                    $('#templateId').val(id);

                    $('#templateModal').modal('show');

                } else {
                    showError(response.error || 'Template not found');
                }
            },

            error: function() {
                showError('Request failed');
            }
        });
    }

    function resetTemplateForm() {
        $('#templateForm')[0].reset();
        $('#templateId').val('');
        $('#templateModalLabel').text('New Template');
        $('button[type="submit"]').text('Save Template');
    }

    function showError(message) {
        Swal.fire('Error', message, 'error');
        console.error(message);
    }

    $(function() {
        $('#organizations').on('change', function() {
            $('.cheifhistory').trigger('click');
        });

        $('#addCheifTemplateBtn').on('click', function() {
            const chiefComplaint = $('#chiefComplaint').val().trim();
            if (!chiefComplaint) {
                Swal.fire('Warning', 'Please fill Chief Complaint', 'warning');
                return;
            }

            $('#readonlyComplaint').val(chiefComplaint);

            $('#cheiftemplateModal').modal('show');
        });

        $('#cheiftemplateForm').on('submit', function(e) {
            e.preventDefault();

            const cc_id = $("#cheiftemplateId").val();
            const templateName = $('#cheiftemplateName').val().trim();
            const chiefComplaint = $('#readonlyComplaint').val().trim();
            const org_id = $('#organizations').val();

            if (!templateName || !chiefComplaint) {
                Swal.fire('Error', 'Both fields are required', 'error');
                return;
            }

            const requestData = {
                template_name: templateName,
                diagnosis_data: chiefComplaint,
                org_id: org_id
            };

            const url = cc_id ? 'ajax/Wprescripation/updatechiefComplaint.php' 
                            : 'ajax/Wprescripation/addchiefComplaint.php';
            
            if (cc_id) requestData.cc_id = cc_id;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: requestData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', `Template ${cc_id ? 'updated' : 'saved'}!`, 'success');
                        $('#cheiftemplateForm')[0].reset();
                        $('#cheiftemplateModal').modal('hide');
                        $('.cheifhistory').trigger('click');
                    } else {
                        Swal.fire('Error', response.error || 'Operation failed', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Server request failed', 'error');
                    console.error("Save error:", xhr.responseText);
                }
            });
        });

        $(document).on('click', '.cheifhistory', function() {
            const orgId = $('#organizations').val();
            
            $.ajax({
                url: 'ajax/Wprescripation/getcheifTemplates.php',
                type: 'GET',
                data: { org_id: orgId },
                dataType: 'json',
                success: function(response) {
                    const $dropdown = $('#CheiftemplateDropdown');
                    $dropdown.empty();

                    if (response.success && response.templates.length > 0) {
                        response.templates.forEach(template => {
                            $dropdown.append(`
                                <div class="cheiftemplate-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start"
                                    data-content="${encodeURIComponent(template.template_data)}">
                                    <div>
                                        <strong>${template.template_name}</strong>
                                        <p class="mb-0 small text-muted">${template.template_data}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-center ms-2">
                                        <i class="fas fa-edit mb-2 text-primary edit-template" 
                                        onclick="getcheiftemplatedata('${template.cc_id}')"></i>
                                        <i class="fas fa-trash-alt text-danger cheifdelete-template" 
                                        data-id="${template.cc_id}"></i>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        $dropdown.html('<div class="text-muted p-2">No templates saved for this organization.</div>');
                    }
                },
                error: function(xhr) {
                    $('#CheiftemplateDropdown').html(
                        `<div class="text-danger p-2">Failed to load templates. ${xhr.responseJSON?.error || ''}</div>`
                    );
                }
            });
        });

        $(document).on('click', '.cheiftemplate-option', function(e) {
            if ($(e.target).closest('.edit-template, .cheifdelete-template').length) return;

            const templateText = decodeURIComponent($(this).data('content')).trim();
            const $textarea = $('#chiefComplaint');
            const currentText = $textarea.val().trim();

            if (!currentText.includes(templateText)) {
                $textarea.val(currentText ? `${currentText}\n${templateText}` : templateText);
                $(this).addClass('bg-light opacity-75');
            }
        });

        $(document).on('click', '.cheifdelete-template', function(e) {
            e.stopPropagation();
            const templateId = $(this).data('id');
            const $templateDiv = $(this).closest('.cheiftemplate-option');

            Swal.fire({
                title: 'Confirm Deletion',
                text: "This template will be permanently deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax/Wprescripation/deletecheifTemplate.php',
                        type: 'POST',
                        data: { 
                            id: templateId,
                            org_id: $('#organizations').val()
                        },
                        success: function(response) {
                            if (response.success) {
                                $templateDiv.remove();
                                Swal.fire('Deleted!', '', 'success');
                            } else {
                                Swal.fire('Error', response.error || 'Deletion failed', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Request failed', 'error');
                        }
                    });
                }
            });
        });
    });                                                                                             

    function getcheiftemplatedata(id) {
        $.ajax({
            url: 'ajax/Wprescripation/getcheifTemplatesbyID.php',
            type: 'POST',
            data: { 
                id: id,
                org_id: $('#organizations').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#cheiftemplateName').val(response.template_name);
                    $('#readonlyComplaint').val(response.template_data);
                    $('#cheiftemplateModalLabel').text('Edit Template');
                    $('#cheiftemplateId').val(id);
                    $('#cheiftemplateModal').modal('show');
                } else {
                    Swal.fire('Error', response.error || 'Template not found', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Request failed', 'error');
            }
        });
    }

    $(function() {
        $('#organizations').on('change', function() {
            $('.pasthistory').trigger('click');
        });

        $('#addPastTemplateBtn').on('click', function() {
            const pastHistory = $('#pastHistory').val().trim();

            if (!pastHistory) {
                Swal.fire('Warning', 'Please fill Past History', 'warning');
                return;
            }

            $('#readonlyPast').val(pastHistory);
            
            $('#pasttemplateModal').modal('show');
        });

        $('#pasttemplateForm').on('submit', function(e) {
            e.preventDefault();

            const ph_id = $("#pasttemplateId").val();
            const templateName = $('#pasttemplateName').val().trim();
            const pastHistory = $('#readonlyPast').val().trim();
            const org_id = $('#organizations').val();

            if (!templateName || !pastHistory) {
                Swal.fire('Error', 'Both fields are required', 'error');
                return;
            }

            const requestData = {
                template_name: templateName,
                diagnosis_data: pastHistory,
                org_id: org_id
            };

            const url = ph_id ? 'ajax/Wprescripation/updatepastHistory.php' 
                            : 'ajax/Wprescripation/addpastHistory.php';
            
            if (ph_id) requestData.ph_id = ph_id;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: requestData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', `Template ${ph_id ? 'updated' : 'saved'}!`, 'success');
                        $('#pasttemplateForm')[0].reset();
                        $('#pasttemplateModal').modal('hide');
                        $('.pasthistory').trigger('click');
                    } else {
                        Swal.fire('Error', response.error || 'Operation failed', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Server request failed', 'error');
                    console.error("Save error:", xhr.responseText);
                }
            });
        });

        $(document).on('click', '.pasthistory', function() {
            const orgId = $('#organizations').val();
            
            $.ajax({
                url: 'ajax/Wprescripation/getpastTemplates.php',
                type: 'GET',
                data: { org_id: orgId },
                dataType: 'json',
                success: function(response) {
                    const $dropdown = $('#PasttemplateDropdown');
                    $dropdown.empty();

                    if (response.success && response.templates.length > 0) {
                        response.templates.forEach(template => {
                            $dropdown.append(`
                                <div class="pasttemplate-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start"
                                    data-content="${encodeURIComponent(template.template_data)}">
                                    <div>
                                        <strong>${template.template_name}</strong>
                                        <p class="mb-0 small text-muted">${template.template_data}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-center ms-2">
                                        <i class="fas fa-edit mb-2 text-primary edit-template" 
                                        onclick="getpasttemplatedata('${template.ph_id}')"></i>
                                        <i class="fas fa-trash-alt text-danger pastdelete-template" 
                                        data-id="${template.ph_id}"></i>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        $dropdown.html('<div class="text-muted p-2">No templates saved for this organization.</div>');
                    }
                },
                error: function(xhr) {
                    $('#PasttemplateDropdown').html(
                        `<div class="text-danger p-2">Failed to load templates. ${xhr.responseJSON?.error || ''}</div>`
                    );
                }
            });
        });

        $(document).on('click', '.pasttemplate-option', function(e) {
            if ($(e.target).closest('.edit-template, .pastdelete-template').length) return;

            const templateText = decodeURIComponent($(this).data('content')).trim();
            const $textarea = $('#pastHistory');
            const currentText = $textarea.val().trim();

            if (!currentText.includes(templateText)) {
                $textarea.val(currentText ? `${currentText}\n${templateText}` : templateText);
                $(this).addClass('bg-light opacity-75');
            }
        });

        $(document).on('click', '.pastdelete-template', function(e) {
            e.stopPropagation();
            const templateId = $(this).data('id');
            const $templateDiv = $(this).closest('.pasttemplate-option');

            Swal.fire({
                title: 'Confirm Deletion',
                text: "This template will be permanently deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax/Wprescripation/deletepastTemplate.php',
                        type: 'POST',
                        data: { 
                            id: templateId,
                            org_id: $('#organizations').val()
                        },
                        success: function(response) {
                            if (response.success) {
                                $templateDiv.remove();
                                Swal.fire('Deleted!', '', 'success');
                            } else {
                                Swal.fire('Error', response.error || 'Deletion failed', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Request failed', 'error');
                        }
                    });
                }
            });
        });
    });

    function getpasttemplatedata(id) {
        $.ajax({
            url: 'ajax/Wprescripation/getpastTemplatesbyID.php',
            type: 'POST',
            data: { 
                id: id,
                org_id: $('#organizations').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#pasttemplateName').val(response.template_name);
                    $('#readonlyPast').val(response.template_data);
                    $('#pasttemplateModalLabel').text('Edit Template');
                    $('#pasttemplateId').val(id);
                    $('#pasttemplateModal').modal('show');
                } else {
                    Swal.fire('Error', response.error || 'Template not found', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Request failed', 'error');
            }
        });
    }

    $('#addinvestigationTemplateBtn').on('click', function () {
        $('#investigationtemplateName').val('');
        $('#totalPrice').val('');
        $('#templateSaveModal').modal('show');
    });

    $('#saveTemplateBtn').on('click', function () {
        const templateName = $('#investigationtemplateName').val().trim();
        const totalPrice = $('#totalPrice').val().trim();

        const org_id = $('#organizations').val();

        if (!templateName || !totalPrice) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Fields',
                text: 'Please fill all fields.'
            });
            return;
        }

        if (investigationArray.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Tests Added',
                text: 'Please add at least one test.'
            });
            return;
        }

        const data = {
            template_name: templateName,
            total_price: totalPrice,
            tests: investigationArray,
            organizations: org_id
        };

        $.ajax({
            url: 'ajax/Wprescripation/addinvestigationTemplate.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Template saved successfully!'
                    });
                    $('#templateSaveModal').modal('hide');
                    investigationArray = [];
                    $('#investigationTableWrapper').empty();
                    investigationTableCreated = false;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error saving template.'
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'AJAX Error',
                    text: error
                });
                console.error('AJAX Error:', error);
            }
        });
    });

    function loadInvestigationTemplates() {
        const orgId = $('#organizations').val();

        $.ajax({
            url: 'ajax/Wprescripation/get_investigationTemplates.php',
            method: 'GET',
            data: { org_id: orgId },      
            dataType: 'json',
            success: function (response) {
                const $container = $('#investigationtemplateDropdown');
                $container.empty();

                if (response.success && response.templates.length > 0) {
                    response.templates.forEach(template => {
                        const $templateDiv = $(`
                            <div class="template-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start investigationtemplatename" 
                                style="cursor:pointer;" 
                                data-tests='${JSON.stringify(template.test_id)}'
                                data-group-id="${template.test_group_id}"
                                data-group-name="${template.test_group_name}"
                                data-group-price="${template.test_group_price}">

                                <div>
                                    <strong>${template.test_group_name}</strong>
                                    <p class="mb-0">${
                                        Array.isArray(template.test_id)
                                        ? template.test_id.map(t => t.investigation).join(', ')
                                        : ''
                                    }</p>
                                </div>

                                <div class="d-flex flex-column align-items-center ms-2">
                                    <i class="fas fa-edit mb-2 text-primary edit-template" 
                                    style="cursor:pointer;" 
                                    title="Edit Template"
                                    onclick="gettemplatedata('${template.test_group_id}');"></i>

                                    <i class="fas fa-trash-alt text-danger delete-template" 
                                    data-id="${template.test_group_id}" 
                                    style="cursor:pointer;" 
                                    title="Delete Template"></i>
                                </div>
                            </div>
                        `);
                        $container.append($templateDiv);
                    });
                } else {
                    $container.html('<div>No templates found.</div>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading templates:', error);
            }
        });
    }

    $(document).ready(function(){
        loadInvestigationTemplates();
        $('#organizations').on('change', () => loadInvestigationTemplates());
    });

    $('.investgationhistory').on('click', loadInvestigationTemplates);

    var testSerial = 1;

    $(document).on('click', '.investigationtemplatename', function (e) {
        // Prevent triggering edit/delete when those icons are clicked
        if ($(e.target).hasClass('edit-template') || $(e.target).hasClass('delete-template')) return;

        const $div = $(this).closest('.template-option');
        const dataTests = $div.attr('data-tests');
        const test_group_id = $div.data('group-id');
        const test_group_name = $div.data('group-name');
        const test_group_price = $div.data('group-price');

        let testList = [];
        try {
            testList = typeof dataTests === 'string' ? JSON.parse(dataTests) : dataTests;
        } catch (e) {
            return; 
        }

        if (!testList || testList.length === 0) return;

        const tableId = '#investigationTable';

        const isDuplicate = $(`${tableId} tbody tr`).filter(function () {
            return $(this).data('group-id') === test_group_id;
        }).length > 0;

        if (isDuplicate) return;

        if (!investigationTableCreated) {
            let tableWrapper = $(`
                <div id="investigationTableContainer" style="position:relative;">
                    <i class="fas fa-times text-danger" id="closeInvestigationTable"
                    style="position:absolute; top:-10px; right:-10px; background:white; border-radius:50%; padding:4px; border:1px solid #ccc; cursor:pointer;"
                    title="Remove Table"></i>
                </div>
            `);

            let table = $('<table id="investigationTable"></table>').css({
                'font-size': '12px',
                'width': '100%',
                'border-collapse': 'collapse',
                'border': '1px solid #ccc',
                'text-align': 'center',
                'margin-top': '10px'
            });

            let thead = $('<thead></thead>').append(
                $('<tr></tr>').append(
                    $('<th>S.No</th>'),
                    $('<th>Investigation</th>'),
                    $('<th>Instruction</th>'),
                    $('<th>Template Price</th>'),
                    $('<th>Action</th>')
                )
            );

            thead.find('th').css({
                'padding': '8px',
                'border': '1px solid #ddd',
                'background': 'lightblue',
                'text-align': 'center',
                'font-weight': 'bold'
            });

            let tbody = $('<tbody></tbody>').css({ 'padding': '2px', 'margin': '0' });

            table.append(thead).append(tbody);
            tableWrapper.append(table);
            $('#investigationTableWrapper').append(tableWrapper);
            investigationTableCreated = true;
        }

        const existingSnoCells = $(`${tableId} tbody td:first-child`);
        if (existingSnoCells.length > 0) {
            let lastSNo = 0;
            existingSnoCells.each(function () {
                const sno = parseInt($(this).text());
                if (!isNaN(sno) && sno > lastSNo) lastSNo = sno;
            });
            testSerial = lastSNo + 1;
        }

        const tbody = $(`${tableId} tbody`);

        testList.forEach((test, index) => {
            const testData = {
                investigation: test.investigation || '',
                instruction: test.instruction || '',
                price: test.price || '',
                concession: test.concession || '',
                test_group_id: test_group_id,
                test_group_name: test_group_name,
                test_group_price: test_group_price
            };

            investigationArray.push(testData);

            const $row = $('<tr></tr>').attr('data-group-id', test_group_id); 

            $row.append($('<td></td>').text(testSerial++));
            $row.append($('<td></td>').text(testData.investigation));
            $row.append($('<td></td>').text(testData.instruction));

            if (index === 0) {
                $row.append($('<td rowspan="' + testList.length + '"></td>').text(test_group_price));
                $row.append($('<td rowspan="' + testList.length + '"></td>').html(''));
            }

            $row.children('td').css({
                'padding': '2px 4px',
                'border': '1px solid #ccc',
                'font-size': '12px',
                'text-align': 'center'
            });

            tbody.append($row);
        });
    });

    $(document).on('click', '.delete-template', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'Do you want to delete this template?',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/Wprescripation/delete_investigationTemplate.php',
                    method: 'POST',
                    data: { id },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Deleted!', 'Template has been deleted.', 'success');
                            loadInvestigationTemplates();
                        } else {
                            Swal.fire('Error', 'Failed to delete template.', 'error');
                        }
                    }
                });
            }
        });
    });

    function getpasttemplatedata(id){
        $.ajax({
            url: 'ajax/Wprescripation/getpastTemplatesbyID.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id: id
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    $('#pasttemplateName').val(response.template_name);
                    $('#readonlyPast').val(response.template_data).prop('readonly', false);

                    $('#pasttemplateModalLabel').text('Edit Template');

                    $('button[type="submit"][form="pasttemplateForm"]').text('Update Template');
                    $('#pasttemplateId').val(id);

                    $('#pasttemplateModal').modal('show');
                }

            },
            error: function(err) {
                console.error('AJAX error:', err);
                alert('Something went wrong while fetching the template.');
            }
        });
    }

    $(document).on('click', '#closeInvestigationTable', function () {
        $('#investigationTableContainer').remove();
        investigationTableCreated = false;
        investigationArray = [];
        testSerial = 1;
    });

    $('#concession').on('change', function() {
        let selected = $(this).find('option:selected');
        
        if (selected.val() !== "") {
            let type  = selected.data('type');
            let value = selected.data('value');

            $('#concession_type').val(type);
            $('#concession_value').val(value);
        } else {
            $('#concession_type').val('');
            $('#concession_value').val('');
        }
    });

</script>