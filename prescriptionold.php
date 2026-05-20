<?php
require_once("ajax/header.php");
?>

<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css"> -->
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


/* .col-lg-4 {
    width: 31.333333%;
} */

/* .form-group {
    margin-bottom: 5px;
} */

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
                    <a href="prescription.php" class="btn btn-primary me-md-2" type="button" style="border-radius:5px !important;">New</a>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <input type="hidden" name="prescription_id" id="prescription_id" value="" >
                    <div class="card-body">

                        <div class="row">
                            <?php 
                            $SessionUserId = $_SESSION['security_id'] ?? '';
                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                            ?>

                            <div class="row">
                                <div class="row mb-lg-5 mb-sm-3">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="OrgIdByPatientNames();getMedichines('');">
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
                            <div style="background-color:#7ec5e5; padding:10px !important;font-size:12px;" class="alert  alert-dismissible fade show" role="alert">
                                <strong style="font-size:15px">NOTE:</strong> This page provides access to prescription information for patients who have previously visited. You can review their medical history and issue a revised prescription based on their current condition.
                                <button style="background-color:#7ec5e5; padding:5px !important;" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>

                                <div class="row mb-lg-5 mb-sm-5">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="patientId"> Name <span id="patient" class="text-danger">*</span> </label>
                                        <select class="form-control patientIdName" name="patientIdName" id="patientIdName" onchange="GetNumberANDIdByName();">
                                            <option value="">Select Name</option>
                                            <?php
                                            if ($SessionUserId == "1") {
                                                $query1 = "SELECT DISTINCT(patient_name) FROM prescripition Where status='1'";
                                                $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                                $query2 = "SELECT DISTINCT(patient_name) FROM prescripition status='1'";
                                                $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                            } else {
                                                $query1 = "SELECT DISTINCT(patient_name) FROM prescripition WHERE status='1' AND org_id='$SessionOrgId'";
                                                $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));

                                                $query2 = "SELECT DISTINCT(patient_name) FROM prescripition WHERE status='1' AND  org_id='$SessionOrgId'";
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
                                                $Keyvalue2 = $option['patient_name'];

                                                $PatientNamesArray[] = array(
                                                    'Keyvalue' => $Keyvalue1,
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
                                        <label for="mobileNumber"> Mobile <span class="text-danger">*</span> </label>
                                        <select type="tel" class="form-control form-select mobile_number"  name="mobile_number" id="mobile_number" placeholder="Select Mobile Number" onchange="GetPatientNameAndIDByNumber()">  
                                            <option value="" >Select Mobile Number</option>
                                            
                                            <?php
                                                // Adjust query based on user type
                                                if($SessionUserId == "1") {
                                                    $query = "
                                                        SELECT DISTINCT ao.mobile_number 
                                                        FROM appointment_online ao 
                                                        INNER JOIN prescripition p ON ao.appoint_register_id = p.appoint_register_id 
                                                        WHERE ao.appoint_status = '1' AND p.status = '1'
                                                        ORDER BY ao.appoint_id DESC
                                                    ";
                                                } else {
                                                    $query = "
                                                        SELECT DISTINCT ao.mobile_number 
                                                        FROM appointment_online ao 
                                                        INNER JOIN prescripition p ON ao.appoint_register_id = p.appoint_register_id 
                                                        WHERE ao.appoint_status = '1' AND p.status = '1' AND ao.org_id = '$SessionOrgId' 
                                                        ORDER BY ao.appoint_id DESC
                                                    ";
                                                }

                                                $getMobileNumbers = mysqli_query($conn, $query) or die(mysqli_error($conn));

                                                while($row = mysqli_fetch_object($getMobileNumbers)) {
                                                    
                                            ?>
                                                <option value="<?=$row->mobile_number ?>" > <?= $row->mobile_number ?> </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-lg-4 col-sm-12" >
                                        <label for="patientId"> Id <span id="patient" class="text-danger">*</span> </label>
                                        <select class="form-control form-select patientId" name="patientId" id="patientId" onchange="GetNameByAgeGender()">
                                            <option value="">Select Id</option>
                                            <?php
                                           if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                            $query2 = "SELECT DISTINCT(patient_uid), prescription_id FROM prescripition WHERE status='1'";
                                            $result2 = mysqli_query($conn, $query2) or die(mysqli_error($conn));
                                        } else {
                                            $query1 = "SELECT DISTINCT(patient_uid) FROM prescripition WHERE status='1' AND org_id='$SessionOrgId'";
                                            $result1 = mysqli_query($conn, $query1) or die(mysqli_error($conn));
                                        
                                            $query2 = "SELECT DISTINCT(patient_uid) FROM prescripition WHERE status='1' AND org_id='$SessionOrgId'";
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
                                                $Keyvalue = $option['patient_uid'];
                                                $Keyvalue2 = $option['precritpion_id'];

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
                                                    // $sql = mysqli_query($conn, "SELECT * FROM appointment_online m, appointment_existing mr WHERE m.appoint_status='1' AND mr.appoint_status='1' AND m.appoint_unicode=mr.appoint_unicode AND (m.appoint_date='$currentDate' OR mr.appoint_date='$currentDate') ") or die(mysqli_error($conn));
                                                
                                                    $sql1 = "SELECT * FROM appointment_online WHERE appoint_status='1' ";
                                                    $sqlres1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
            
                                                    $sql2 = "SELECT * FROM appointment_existing WHERE appoint_status='1'";
                                                    $sqlres2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
                                                } else{
                                                    // $sql = mysqli_query($conn, "SELECT * FROM appointment_online m, appointment_existing mr WHERE m.appoint_status='1' AND mr.appoint_status='1' AND m.appoint_unicode=mr.appoint_unicode AND (m.appoint_date='$currentDate' OR mr.appoint_date='$currentDate') AND m.org_id='$SessionOrgId' AND mr.org_id='$SessionOrgId'") or die(mysqli_error($conn));
                                                
                                                    $sql1 = "SELECT * FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId'";
                                                    $sqlres1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
            
                                                    $sql2 = "SELECT * FROM appointment_existing WHERE appoint_status='1' AND org_id='$SessionOrgId'";
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
                                        <label for="existing_bpSit_systolic">BP <i class="fa-solid fa-wheelchair"></i> /mmHg</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="existing_bpSit_systolic" id="existing_bpSit_systolic" value="">
                                            <span class="divider">/</span>
                                            <input type="text" class="form-control" name="existing_bpSit_diastolic" id="existing_bpSit_diastolic" value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="existing_bpStand_systolic">BP <i class="fa-solid fa-person"></i> /mmHg</label>
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
                                            <input type="text" class="form-control" name="existing_bmi" id="existing_bmi" value="">
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
                                                    <!-- Dynamic content will be appended here -->
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
                                                <!-- Dynamic content will be appended here -->
                                                </ul>
                                            </div>
                                            <i class="fas fa-plus-circle text-success" id="addCheifTemplateBtn" style="cursor:pointer;" title="Add Chief Template"></i>
                                        </span>
                                    </label>
                                        <textarea class="form-control"  name="chiefComplaint" id="chiefComplaint"></textarea>
                                    </div>
                                    <!-- <div class="form-group col-md-4 col-sm-12 ">
                                        <label for="pastHistory">Past History</label>
                                        <textarea class="form-control"  name="pastHistory" id="pastHistory" value=""></textarea>
                                    </div> -->
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
                                <label for="medicineName">Medicine Name <span class="text-danger med">*</span></label>
                                <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-capsule"></i>
                                        </div>
                                    </div>
                                        <input list="drugNameDatalist" class="form-control drugname" id="drugName" name="drugName" oninput="this.value = this.value.toUpperCase();">
                                        <datalist id="drugNameDatalist">
                                            <option value="">
                                        </datalist>
                                </div>
                            </div>

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
                                    <input type="text" class="form-control " name="duration_value" id="duration_value" >
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
                                    <label for="advise">Advise</label>
                                     <textarea class="form-control" id="advise" name="advise"></textarea>


                                </div>
                                </div>  
                        <hr>
                        <h6 class="text-dark">Review</h6>
                        <div class= "row">
                            <div class="form-group col-lg-6 col-sm-12">
                                <label for="reviewInput">Review After</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="reviewInput" >
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
                            <div class="form-group col-lg-5 col-sm-12">
                                <!-- <label for="investigation">Investigation<span class="text-danger invest">*</span></label> -->
                                <label for="investigation" class="d-flex justify-content-between align-items-center">
                                    <span>
                                        Investigation<span class="text-danger invest">*</span>
                                        <div class="btn-group ml-5">
                                            <i class="fa fa-history investgationhistory dropdown-toggle fa-lg" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;margin-top:-14px;" title="Saved Templates"></i>
                                            <ul class="dropdown-menu p-2" id="investigationtemplateDropdown" style="width: 300px; max-height: 300px; overflow-y: auto;"></ul>
                                        </div>
                                        <i class="fas fa-plus-circle text-success" id="addinvestigationTemplateBtn" style="cursor:pointer;" title="Add Investigation Template"></i>
                                    </span>
                                </label>
                                <input list="investigationDatalist" class="form-control investigation" id="investigation" name="investigation" oninput="this.value = this.value.toUpperCase();">
                                <datalist id="investigationDatalist">
                                    <option value="">
                                </datalist>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="test_price">Price<span class="text-danger invest">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><i class="bi bi-currency-rupee"></i></div>
                                        </div>
                                        <input type="text" class="form-control currency" name="test_price" id="test_price" value="">
                                    </div>
                                </div>
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
              <input list="edit_drugNameDatalist" class="form-control" id="edit_drugName" name="edit_drugName" oninput="this.value = this.value.toUpperCase();">
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


<!-- Modal for Saving Template -->
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
                <div class="input-group-text">₹</div>
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
        $("#appoint_register_id, .patientId, .patientIdName, .mobile_number").select2({
            width: '100%'   
        });

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

    $(document).on('change', '#duration', function() {
        if(($('#duration').val()) == 'Till Further Advice'){
            $('#duration_value').val('0');
        }
    });

    $(document).on('input', '#duration_value', function() {
        if(($('#duration').val()) == 'Till Further Advice'){
            $('#duration_value').val('0');
        }
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
                console.log(data);
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

        if (!drugName || !typeText || !unitText || !dosageId || !whenId ||  !duration_value || !duration) {
            
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

        appendRowToTable(medDisplay, medicineArray.length - 1); // Use the correct index

        $('#drugName, #medicineType, #unit, #dosage, #when, #time, #duration_value, #notes').val('');
        toggleMedSpans();
        // console.log(medicineArray);
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

        $('#edit_drugName').val(med.drugName);
        $('#edit_medicineType').val(med.typeText);
        $('#edit_unit').val(med.unitText);
        $('#edit_dosage').val(med.dosageId);
        getmodalTimeForDose(med.dosageId);
        $('#edit_when').val(med.whenId);
        $('#edit_duration_value').val(med.duration_value);
        $('#edit_duration').val(med.duration);
        $('#edit_notes').val(med.notes);
        $('#edit_time').val(med.timeId);

        $('#editVitalsModal').modal('show');
    });


    $('#updateVitals').on('click', function () {
        if (editingIndex !== null) {
            const updated = {
                drugName: $('#edit_drugName').val(),
                typeText: $('#edit_medicineType').val(),
                unitText: $('#edit_unit').val(),
                dosageId: $('#edit_dosage').val(),
                whenId: $('#edit_when').val(),
                timeId: $('#edit_time').val(),
                duration_value: $('#edit_duration_value').val(),
                duration: $('#edit_duration').val(),
                notes: $('#edit_notes').val()
            };

            medicineArray[editingIndex] = updated;

            const display = {
                drugName: updated.drugName,
                typeText: updated.typeText,
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

    $('#test_price').on('input', function () {
        let value = $(this).val();
        value = value.replace(/[^0-9.]/g, ''); 
        value = value.replace(/(\..*?)\..*/g, '$1'); 
        $(this).val(value);
    });

    // Medicine One

    // Tests One
    let investigationArray = [];
    let editingInvestigationIndex = null;
    let investigationTableCreated = false;

    $(document).on('click', '.adding-form', function () {
        let investigation = $('#investigation').val().trim();
        let instruction = $('#testnotes').val().trim();
        let price = $('#test_price').val().trim();

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

        const data = { investigation, instruction, price, test_group_id : '', test_group_name : '', test_group_price: '' };
        const display = { investigation, instruction, price };

        investigationArray.push(data);

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
        }

        appendInvestigationRow(display, investigationArray.length - 1);
        $('#investigation, #testnotes, #test_price').val('');
        toggleTestSpans();

        console.log(investigationArray);
    });

    function appendInvestigationRow(data, index) {
        let row = $('<tr></tr>');
        row.append($('<td class="sno"></td>').text(index + 1));
        row.append($('<td></td>').text(data.investigation));
        row.append($('<td></td>').text(data.instruction));
        row.append($('<td></td>').text(data.price));

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

        $('#editInvestigationModal').modal('show');
    });

    $('#cancelEditInvestigation').on('click', function () {
        $('#editInvestigationModal').modal('hide');
        editingInvestigationIndex = null;
    });

    $('#saveEditInvestigation').on('click', function () {
        if (editingInvestigationIndex !== null) {
            const updated = {
                investigation: $('#edit_investigation').val().trim(),
                instruction: $('#edit_instruction').val().trim(),
                price: $('#edit_test_price').val().trim()
            };

            investigationArray[editingInvestigationIndex] = updated;

            const row = $('#investigationTable tbody tr').eq(editingInvestigationIndex);
            const cells = row.find('td');

            cells.eq(1).text(updated.investigation);
            cells.eq(2).text(updated.instruction);
            cells.eq(3).text(updated.price);

            $('#editInvestigationModal').modal('hide');
            editingInvestigationIndex = null;
        }
    });


    // Tests One



    function getvitalid(appoint_register_id) {
        if (!appoint_register_id) {
            console.log("No appointment selected.");
            return; 
        }

        var organizations = $("#organizations").val();

        $.ajax({
            url: 'ajax/Wprescripation/getvitalsdata.php',
            type: 'POST',
            dataType: 'json',
            data: { appointment_id: appoint_register_id, org_id: organizations },
            success: function(data) {
                // console.log("AJAX Response: ", data); 

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

                    $("#age").val(vitals.age);

                    if (vitals.gender === 'Male') {
                        $('#male').prop('checked', true);
                    } else if (vitals.gender === 'Female') {
                        $('#female').prop('checked', true);
                    } else {
                        $('#others').prop('checked', true);
                    }

                    if (($('#SessionUserId').val() == '1') && ($('#SessionRoleId').val() == '1')) {
                        $('#organizations').val(vitals.org_id);
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

                    $('#reviewInput').val(reviewValue);
                    $('#reviewSelect').val(reviewUnit);

                    $('#chiefComplaint').val(prescription.chiefcomplaint);
                    $('#pastHistory').val(prescription.pasthistory);
                    $('#finalDiagnosis').val(prescription.finalDiagnosis);
                    $('#reviewCalculatedDate').val(data.reviewafterdate);
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
                            test_group_id : test.test_group_id,
                            test_group_name : test.test_group_name,
                            test_group_price : test.test_group_price
                        };

                        investigationArray.push(testData);
                        appendInvestigationRow(testData, index);
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

    // get medicine name
    function getMedichines(id, value = null) {
        var id = '';
        var org_id = $("#organizations").val();
        $.ajax({
            url: 'ajax/Wprescripation/getMedichines.php',
            type: 'post',
            data: {
                'org_id': org_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '';
                $.each(data, function(index, val) {
                    var displayName = val.medicine_name + ' - (' + val.scientific_name + ')';

                    medicineList.push({
                        medicine_id: val.medicine_id, 
                        name: displayName
                    });

                    optionData += '<option value="' + displayName + '"></option>';
                });

                var $datalist = $("#drugName" + id + "Datalist");
                if ($datalist.length === 0) {
                    console.log("No element found matching the selector");
                } else {
                    $datalist.html(optionData);
                    $("#edit_drugNameDatalist").html(optionData);
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

    // Global array to store medicine types
    var medicineTypeList = [];

    // Function to fetch and populate medicine types
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

    // get unit 
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

                // Update both main and modal select elements
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

                // Update both main and modal select elements
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
                // console.log(data);
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



    // data insert and update functionality
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
                doctor_price: enteredPrice,
                standard_price: standardPrice,
                test_status: '1',
                test_group_id: item.test_group_id || '', 
                test_group_name: item.test_group_name || '', 
                test_group_price: item.test_group_price || ''
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

        console.log(postData);

        $.ajax({
            url: 'ajax/Wprescripation/addpatientold.php',
            type: 'POST',
            data: JSON.stringify(postData),
            contentType: 'application/json',
            success: function (data) {
                // console.log(data);
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


    // // edit data
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
                    reviewafter,
                    reviewafterdate
                ) {

                    console.log(medicines);
        // $("#prescription_id").val(prescription_id);
        // $("#organizations").val(organizations).trigger('change');
        // setTimeout(function() {
        //     $("#patientIdName").val(patientIdName).trigger('change');
        //     $("#RX_Group_Id").val(rx_id);
        //     $("#test_group_id").val(test_group_id);
        //     $("#tests").val(tests);
        //     Timing.setChoiceByValue(tests.split(','));
        //     getPresMedi(prescription_id);

        //     setTimeout(function() {
        //         $("#patientId").val(patientId).trigger('change');
        //     }, 200);
        // }, 200);
        
        // $("#appoint_register_id").val(appoint_register_id).trigger('change');
        // $("#age").val(age);
        // $("#male").prop("checked", gender === "Male");
        // $("#female").prop("checked", gender === "Female");
        // $("#others").prop("checked", gender === "Other");

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
                test_group_id: test.test_group_id, 
                test_group_name: test.test_group_name, 
                test_group_price: test.test_group_price
            };

            investigationArray.push(testData);
            appendInvestigationRow(testData, index);
        });


    });


    // delete functionality

    function deleteP(prescription_id, patientName) {

        console.log(medicineArray);
        return;
        swal({
            title: "Are you sure?",
            text: "Do you really want to Delete Prescripition Record?",
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
                            swal('',  ' Deleted Successfully', 'success');
                            getpriscription();
                            clearData();
                        } else {
                            swal('error','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(prescription_id);
                swal('',' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    // remove form data

    $(document).on('click', '.delet-btn', function () {
        $(this).closest('.open-form').remove();
    });



    function myFunction(prescription_id) {
        location.replace("patientPrescription.php?ItemId=" + prescription_id); 
    }

    // duration loop validation
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

    // quantity Loop Validation
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
                var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
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


    // Quantity 
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


    

    


    // Patient Age 
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
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });


// GetNameByAgeGender auto fill
function GetNameByAgeGender() {
    let selectElement = document.getElementById("patientId");
    if (!selectElement) {
        console.error("Select element not found.");
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
            // console.log(data);
            var AppointmentIdSelect = $("#appoint_register_id");
            var ageSelect = $("#age");
            var genderSelect = $("input[name='gender']");

            // Clear existing options
            AppointmentIdSelect.empty();
            ageSelect.empty();

            $.each(data, function(_, val) {
                AppointmentIdSelect.append($('<option>', {
                    value: val.appoint_register_id,
                    text: val.appoint_register_id
                }));
            });

            // Set the value of input field
            if (data.length > 0) {
                AppointmentIdSelect.val(data[0].appoint_register_id);
                // Trigger the change event so any event listeners will fire
                AppointmentIdSelect.trigger('change');
                ageSelect.val(data[0].age);
                // Update the gender radio buttons based on data
                genderSelect.filter('[value="' + data[0].gender + '"]').prop('checked', true);
            } else {
                // Clear the gender radio buttons if no data is available
                genderSelect.prop('checked', false);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

// Org Id by Patient Names
function OrgIdByPatientNames(){
    var org_id = $('#organizations').val();
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
            
            // console.log(data);

            $("#patientIdName").html(optionDataPN);
            $("#RX_Group_Id").html(optionDataRX);
            $("#test_group_id").html(optionDataTG);
            Timing.destroy();
            $("#tests").html(optionDataT);
            $(".medicine").html(optionDataMN);
            Timing = new Choices('#tests', {
                removeItemButton: true,
            }); 
            // GetNameByIds();
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function GetPatientNameAndIDByNumber() {
    var patient_number = $('#mobile_number').val();
    $.ajax({
        url: 'ajax/Wprescripation/getPatientNameAndIdByNumber.php',
        type: 'POST',
        data: {
            patient_number: patient_number
        },
        dataType: 'json',
        success: function(data) {
            var appointUnicodeSelect = $("#patientId");
            var PatientNameSelect = $("#patientIdName");
            var AppointNumberSelect = $("#appoint_register_id");

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

                getvitalid(val.appoint_register_id);
            });

            // Select the first option by default
            appointUnicodeSelect.prop('selectedIndex', 0);
            PatientNameSelect.prop('selectedIndex', 0);
            AppointNumberSelect.prop('selectedIndex', 0);
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", error);
        }
    });
}
function GetNumberANDIdByName(){
    var patient_name = $('#patientIdName').val();
    $.ajax({
        url: 'ajax/Wprescripation/getPatientNumberAndIdByName.php',
        type: 'POST',
        data: {
            patient_name: patient_name
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
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

                PatientNumberSelect.append($('<option>', {
                    value: val.mobile_number,
                    text: val.mobile_number
                }));

                AppointNumberSelect.append($('<option>', {
                    value: val.appoint_register_id,
                    text: val.appoint_register_id
                }));
                getvitalid(val.appoint_register_id);
            });

            // Select the first option by default
            appointUnicodeSelect.prop('selectedIndex', 0);
            PatientNumberSelect.prop('selectedIndex', 0);
            AppointNumberSelect.prop('selectedIndex', 0);
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", error);
        }
    }); 
}

$('#templateForm').on('submit', function(e) {
        e.preventDefault();

        const templateName = $('#templateName').val().trim();
        const finalDiagnosis = $('#finalDiagnosis').val().trim();

        if (!templateName || !finalDiagnosis) {
            alert("Both Template Name and Final Diagnosis are required.");
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/addfinaldiagnosis.php',
            type: 'POST',
            dataType: 'json',
            data: {
            template_name: templateName,
            diagnosis_data: finalDiagnosis
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Template Saved!',
                        text: 'Your final diagnosis template was saved successfully.',
                        confirmButtonText: 'OK'
                    });
                    $('#templateForm')[0].reset();
                    $('#finalDiagnosis').val('');
                    $('#templateModal').modal('hide');
                } else {
                    console.log("Failed to save template: " + (response.error || 'Unknown error'));
                }
            },
            error: function(err) {
                console.error(err);
            }
        });
    });

    $(document).on('click', '.template-option', function(e) {
        if ($(e.target).hasClass('delete-template')) return;

        const templateText = decodeURIComponent($(this).data('content'));
        const $textarea = $('#finalDiagnosis');
        const existingText = $textarea.val().trim();

        const newText = existingText ? `${existingText}\n${templateText}` : templateText;
        $textarea.val(newText);
    });

    $(document).on('click', '.delete-template', function(e) {
        e.stopPropagation(); // Prevent textarea update

        const templateId = $(this).data('id');
        const $templateDiv = $(this).closest('.template-option');

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this template?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion
                $.ajax({
                    url: 'ajax/Wprescripation/deleteTemplate.php', // Your PHP handler
                    type: 'POST',
                    data: { id: templateId },
                    success: function(response) {
                        Swal.fire('Deleted!', 'Template has been deleted.', 'success');
                        $templateDiv.remove(); // Remove from DOM
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.fa-history', function(){
        $.ajax({
            url: 'ajax/Wprescripation/getTemplates.php',
            type: 'GET',
            dataType: 'json',
            success: function(templates) {
                const $dropdown = $('#templateDropdown');
                $dropdown.empty();

                if (templates.length === 0) {
                    $dropdown.append('<li class="text-muted px-2">No templates saved.</li>');
                    return;
                }

                let optionsHtml = '';

                templates.forEach(template => {
                    optionsHtml += `
                        <div class="template-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start" style="cursor:pointer;" data-content="${encodeURIComponent(template.template_data)}">
                        <div>
                            <strong>${template.template_name}</strong>
                            <p class="mb-0">${template.template_data}</p>
                        </div>
                        <i class="fas fa-trash-alt text-danger delete-template" data-id="${template.fd_id}" style="cursor:pointer;" title="Delete Template"></i>
                        </div>
                    `;
                });
                $dropdown.html(optionsHtml);
            },
            error: function(err) {
                console.error('Failed to load templates:', err);
            }
        });
    });

    $('#addTemplateBtn').on('click', function () {
        const finalDiagnosis = $('#finalDiagnosis').val().trim();

        if (!finalDiagnosis) {
            Swal.fire({
            icon: 'warning',
            title: 'Please fill Final Diagnosis',
            confirmButtonText: 'OK'
            });
            return;
        }

        $('#readonlyDiagnosis').val(finalDiagnosis);

        $('#templateModal').modal('show');
    });

    //cheif Complaint
    $('#addCheifTemplateBtn').on('click', function () {
        const chiefComplaint = $('#chiefComplaint').val().trim();

        if (!chiefComplaint) {
            Swal.fire({
            icon: 'warning',
            title: 'Please fill Chief Complaint',
            confirmButtonText: 'OK'
            });
            return;
        }

        $('#readonlyComplaint').val(chiefComplaint);

        $('#cheiftemplateModal').modal('show');
    });

    $('#cheiftemplateForm').on('submit', function(e) {
        e.preventDefault();

        const templateName = $('#cheiftemplateName').val().trim();
        const chiefComplaint = $('#chiefComplaint').val().trim();

        if (!templateName || !chiefComplaint) {
            alert("Both Template Name and Chief Complaint are required.");
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/addchiefComplaint.php',
            type: 'POST',
            dataType: 'json',
            data: {
            template_name: templateName,
            diagnosis_data: chiefComplaint
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Template Saved!',
                        text: 'Your Chief Complaint template was saved successfully.',
                        confirmButtonText: 'OK'
                    });
                    $('#cheiftemplateForm')[0].reset();
                    $('#chiefComplaint').val('');
                    $('#cheiftemplateModal').modal('hide');
                } else {
                    console.log("Failed to save template: " + (response.error || 'Unknown error'));
                }
            },
            error: function(err) {
                console.error(err);
            }
        });
    });

    $(document).on('click', '.cheifhistory', function(){
        $.ajax({
            url: 'ajax/Wprescripation/getcheifTemplates.php',
            type: 'GET',
            dataType: 'json',
            success: function(templates) {
                const $dropdown = $('#CheiftemplateDropdown');
                $dropdown.empty();

                if (templates.length === 0) {
                    $dropdown.append('<li class="text-muted px-2">No templates saved.</li>');
                    return;
                }

                let optionsHtml = '';

                templates.forEach(template => {
                    optionsHtml += `
                        <div class="cheiftemplate-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start" style="cursor:pointer;" data-content="${encodeURIComponent(template.template_data)}">
                        <div>
                            <strong>${template.template_name}</strong>
                            <p class="mb-0">${template.template_data}</p>
                        </div>
                        <i class="fas fa-trash-alt text-danger cheifdelete-template" data-id="${template.cc_id}" style="cursor:pointer;" title="Delete Template"></i>
                        </div>
                    `;
                });
                $dropdown.html(optionsHtml);
            },
            error: function(err) {
                console.error('Failed to load templates:', err);
            }
        });
    });

    $(document).on('click', '.cheiftemplate-option', function(e) {
        if ($(e.target).hasClass('cheifdelete-template')) return;

        const templateText = decodeURIComponent($(this).data('content'));
        const $textarea = $('#chiefComplaint');
        const existingText = $textarea.val().trim();

        const newText = existingText ? `${existingText}\n${templateText}` : templateText;
        $textarea.val(newText);
    });

    $(document).on('click', '.cheifdelete-template', function(e) {
        e.stopPropagation(); // Prevent textarea update

        const templateId = $(this).data('id');
        const $templateDiv = $(this).closest('.template-option');

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this template?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/Wprescripation/deletecheifTemplate.php', 
                    type: 'POST',
                    data: { id: templateId },
                    success: function(response) {
                        Swal.fire('Deleted!', 'Template has been deleted.', 'success');
                        $templateDiv.remove(); 
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });

    //past History
    $('#addPastTemplateBtn').on('click', function () {
        const pastHistory = $('#pastHistory').val().trim();

        if (!pastHistory) {
            Swal.fire({
            icon: 'warning',
            title: 'Please fill Past History',
            confirmButtonText: 'OK'
            });
            return;
        }

        $('#readonlyPast').val(pastHistory);

        $('#pasttemplateModal').modal('show');
    });

    $('#pasttemplateForm').on('submit', function(e) {
        e.preventDefault();

        const templateName = $('#pasttemplateName').val().trim();
        const pastHistory = $('#pastHistory').val().trim();

        if (!templateName || !pastHistory) {
            alert("Both Template Name and Past History are required.");
            return;
        }

        $.ajax({
            url: 'ajax/Wprescripation/addpastHistory.php',
            type: 'POST',
            dataType: 'json',
            data: {
            template_name: templateName,
            diagnosis_data: pastHistory
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Template Saved!',
                        text: 'Your Past History template was saved successfully.',
                        confirmButtonText: 'OK'
                    });
                    $('#pasttemplateForm')[0].reset();
                    $('#pastHistory').val('');
                    $('#pasttemplateModal').modal('hide');
                } else {
                    console.log("Failed to save template: " + (response.error || 'Unknown error'));
                }
            },
            error: function(err) {
                console.error(err);
            }
        });
    });

    $(document).on('click', '.pasthistory', function(){
        $.ajax({
            url: 'ajax/Wprescripation/getpastTemplates.php',
            type: 'GET',
            dataType: 'json',
            success: function(templates) {
                const $dropdown = $('#PasttemplateDropdown');
                $dropdown.empty();

                if (templates.length === 0) {
                    $dropdown.append('<li class="text-muted px-2">No templates saved.</li>');
                    return;
                }

                let optionsHtml = '';

                templates.forEach(template => {
                    optionsHtml += `
                        <div class="pasttemplate-option mb-2 p-2 border rounded d-flex justify-content-between align-items-start" style="cursor:pointer;" data-content="${encodeURIComponent(template.template_data)}">
                        <div>
                            <strong>${template.template_name}</strong>
                            <p class="mb-0">${template.template_data}</p>
                        </div>
                        <i class="fas fa-trash-alt text-danger pastdelete-template" data-id="${template.ph_id}" style="cursor:pointer;" title="Delete Template"></i>
                        </div>
                    `;
                });
                $dropdown.html(optionsHtml);
            },
            error: function(err) {
                console.error('Failed to load templates:', err);
            }
        });
    });

    $(document).on('click', '.pasttemplate-option', function(e) {
        if ($(e.target).hasClass('pastdelete-template')) return;

        const templateText = decodeURIComponent($(this).data('content'));
        const $textarea = $('#pastHistory');
        const existingText = $textarea.val().trim();

        const newText = existingText ? `${existingText}\n${templateText}` : templateText;
        $textarea.val(newText);
    });

    $(document).on('click', '.pastdelete-template', function(e) {
        e.stopPropagation(); // Prevent textarea update

        const templateId = $(this).data('id');
        const $templateDiv = $(this).closest('.template-option');

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this template?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/Wprescripation/deletepastTemplate.php', 
                    type: 'POST',
                    data: { id: templateId },
                    success: function(response) {
                        console.log(response);
                        Swal.fire('Deleted!', 'Template has been deleted.', 'success');
                        $templateDiv.remove(); 
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete template.', 'error');
                    }
                });
            }
        });
    });

    //investigation

    // Show modal when template icon clicked
    $('#addinvestigationTemplateBtn').on('click', function () {
        $('#investigationtemplateName').val('');
        $('#totalPrice').val('');
        $('#templateSaveModal').modal('show');
    });

    $('#saveTemplateBtn').on('click', function () {
        const templateName = $('#investigationtemplateName').val().trim();
        const totalPrice = $('#totalPrice').val().trim();

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
            tests: investigationArray
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
        $.ajax({
            url: 'ajax/Wprescripation/get_investigationTemplates.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log("Server Response:", response);  // Log the server response

                const $dropdown = $('#investigationtemplateDropdown');
                $dropdown.empty();

                if (response.success && response.templates.length > 0) {
                    response.templates.forEach(template => {
                        const item = $(`
                            <li class="d-flex justify-content-between align-items-center template-item"
                                data-tests='${JSON.stringify(template.test_id)}'
                                data-group-id="${template.test_group_id}"
                                data-group-name="${template.test_group_name}"
                                data-group-price="${template.test_group_price}">
                                <span class="investigationtemplatename" style="cursor:pointer;">
                                    ${template.test_group_name}
                                </span>
                                <i class="fa fa-trash text-danger delete-template"
                                data-id="${template.test_group_id}"
                                style="cursor:pointer;"></i>
                            </li>
                        `);
                        $dropdown.append(item);
                    });
                } else {
                    $dropdown.html('<li>No templates found.</li>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading templates:', error);
                console.log('XHR:', xhr);
                console.log('Status:', status);
            }
        });
    }

    $('.investgationhistory').on('click', loadInvestigationTemplates);

    var testSerial = 1; // renamed to reflect per-test numbering

    $(document).on('click', '.investigationtemplatename', function () {
        const $li = $(this).closest('li');
        const dataTests = $li.attr('data-tests');
        const test_group_id = $li.data('group-id');
        const test_group_name = $li.data('group-name');
        const test_group_price = $li.data('group-price');

        let testList = [];
        try {
            testList = typeof dataTests === 'string' ? JSON.parse(dataTests) : dataTests;
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Invalid Data', text: 'Error parsing template data.' });
            return;
        }

        if (!testList || testList.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'No Tests Found',
                text: 'This template has no associated tests.',
            });
            return;
        }

        const tableId = '#investigationTable';

        // Create table if it doesn't exist
        if (!investigationTableCreated) {
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
            $('#investigationTableWrapper').append(table);
            investigationTableCreated = true;
        }

        // Get latest test serial number
        const existingSnoCells = $(`${tableId} tbody td:first-child`);
        if (existingSnoCells.length > 0) {
            let lastSNo = 0;
            existingSnoCells.each(function () {
                const sno = parseInt($(this).text());
                if (!isNaN(sno) && sno > lastSNo) {
                    lastSNo = sno;
                }
            });
            testSerial = lastSNo + 1;
        }

        const tbody = $(`${tableId} tbody`);

        testList.forEach((test, index) => {
            const testData = {
                investigation: test.investigation || '',
                instruction: test.instruction || '',
                price: test.price || '',
                test_group_id: test_group_id,
                test_group_name: test_group_name,
                test_group_price: test_group_price
            };

            investigationArray.push(testData);

            const $row = $('<tr></tr>');

            // S.No for every test
            $row.append($('<td></td>').text(testSerial++));

            $row.append($('<td></td>').text(testData.investigation));
            $row.append($('<td></td>').text(testData.instruction));

            // Only once per test group for price and action
            if (index === 0) {
                $row.append($('<td rowspan="' + testList.length + '"></td>').text(test_group_price));
                $row.append($('<td rowspan="' + testList.length + '"></td>').html(''));
            }

            // Style cells
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
</script>