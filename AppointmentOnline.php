<?php
// FIX_B_1300 — RBAC: pharmacist must not access admin-only
// AppointmentOnline.php via direct URL. Page-level guard added BEFORE the
// ajax/header.php include so the redirect can fire before any output.
// FIX_B_2251: roles were renumbered (Pharmacist=4, Accountant=5). Old `12`
// referred to a stale legacy role_id. Front desk = SA(1)/Doctor(2)/
// Receptionist(3)/Admin(6). Pharmacist/Accountant blocked.
require_once(__DIR__ . "/include/auth_guard.php");
denyPageRoles([4, 5]);
require_once("ajax/header.php");
// FIX_B_1820 (scope 2 RBAC): per-action view gate; SA bypassed by userCan().
requireCan('view', basename(__FILE__));


$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId = $_SESSION['org_id'];

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .select2 {
        width: 100% !important;
    }

    .btn-group,
    .btn-group-vertical {
        position: relative;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }

    .tanNumber,
    .gstNumber {
        text-transform: uppercase;
    }

    .amount {
        text-transform: uppercase;
    }

    .TimeSlot {
        border-radius: 5px 5px 5px 5px;
        margin: 8px;
        text-align: center;
        width: 82px;
        height: 38px;
        color: #fff;
        cursor: pointer;
        font-size: 13px;
        font-weight: bold;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        background-color: rgba(51, 208, 161, 0.8);
        padding-top: 5px;
        text-decoration: none;
    }

    .theme-white .nav-pills .nav-link.active {
        margin-left: 27px;
        color: #fff;
        background-color: #6777ef;
    }

    .nav-pills .nav-item .nav-link {
        color: #6777ef;
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .nav-pills .nav-link {
        margin-left: 27px;
        background: 0;
        border: 0;
        border-radius: 0.25rem;
    }

    input {
        position: relative;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        background-position: right;
        background-size: auto;
        cursor: pointer;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 5px;
        top: 12px;
        width: auto;
    }

    * .input-wrapper {
        display: flex;
        align-items: center;
    }

    .divider {
        margin: 0 5px;

    }

    .swal2-actions {
        display: flex;
        justify-content: space-between;
        gap: 0px;
    }

    .swal2-confirm,
    .swal2-cancel {
        margin-left: 7px;
        /* margin-right: 10px;  */
    }

    .is-invalid {
        border: 2px solid red !important;
    }

</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Appointment</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Billing</li>
            <li class="breadcrumb-item">Add & Modify Appointment</li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">
            <!-- nav tabs  -->
            <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation" id="open">
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">New Patient</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Existing Patient</button>
                    </li>
                </ul> -->

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header">
                            <h4> Appointment New Patient </h4>
                        </div>
                        <form method="POST" id="Myform" action="" enctype="multipart/form-data" class="">
                            <input type="hidden" name="appoint_id" id="appoint_id" value="">
                            <input type="hidden" name="bill_id" id="bill_id" value="">
                            <input type="hidden" name="validto" id="validto">
                            <input type="hidden" name="appstatus" id="appstatus">
                            <input type="hidden" name="doc" id="doc">
                            <div class="card-body">
                                <div class="row">
                                    <?php

                                    if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                    ?>
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="organizations" class="Organization"> Organization <span class="text-danger">*</span></label>
                                            <select class="form-control form-select" name="organizations" id="organizations" onchange="GetOrgByIds();GetOrgByDoctor(); fetchPatientNameMobile()">
                                                <option value="">Select Organization</option>
                                                <?php
                                                $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                                while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                                                ?>
                                                    <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php
                                    } else {
                                    ?>
                                        <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                                    <?php
                                    }
                                    ?>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="appointID"> Appointment ID <span class="text-danger">*</span> </label>
                                        <input type="text" class="form-control" name="appointID" id="appointID" value="" disabled>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="appoint_unicode"> Registration ID <span class="text-danger">*</span> </label>
                                        <input type="text" class="form-control" name="appoint_unicode" id="appoint_unicode" value="" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="patient_name">Patient Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="bi bi-person-fill"></i>
                                                </span>
                                            </div>
                                            <input type="text" list="patient_nameDatalist" id="patient_name" name="patient_name" class="form-control" required>
                                            <datalist id="patient_nameDatalist"></datalist>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="patient_mobile">Mobile <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-telephone-fill"></i>
                                            </span>
                                            <input type="text" list="patient_mobileDatalist" id="patient_mobile" name="patient_mobile" class="form-control" required>
                                            <datalist id="patient_mobileDatalist"></datalist>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12 ">
                                        <label for="dob">Date of Birth</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-regular fa-calendar"></i>
                                                </div>
                                            </div>
                                            <input type="date" class="form-control" name="dob" id="dob" onchange="calculateAgeFromDOB('dob','age')" />
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12 ">
                                        <label for="age"> Age <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-arrow-up-9-1"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" name="age" id="age" value="" required />
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <div class="selectgroup w-100 d-flex">
                                            <label>
                                                <input type="radio" name="gender" id="male" value="Male" class="selectgroup-input-radio" required />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center py-1">
                                                    <b> <i class="bi bi-gender-male"></i>&nbsp;Male</b>
                                                </span>
                                            </label>
                                            <label>
                                                <input type="radio" name="gender" id="female" value="Female" class="selectgroup-input-radio" />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center py-1">
                                                    <b><i class="bi bi-gender-female"></i>&nbsp;Female</b>
                                                </span>
                                            </label>
                                            <label>
                                                <input type="radio" name="gender" id="others" value="Others" class="selectgroup-input-radio" />
                                                <span class="selectgroup-button d-flex align-items-center justify-content-center py-1">
                                                    <b> <i class="bi bi-gender-ambiguous"></i>&nbsp;Others</b>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="patient_email">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-envelope-fill"></i>
                                            </span>
                                            <input type="email" class="form-control" name="patient_email" id="patient_email" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 mb-2 mt-1">
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <span style="font-size:12px;font-weight:600;color:#6c757d;white-space:nowrap;letter-spacing:.5px;text-transform:uppercase;">Referral Details</span>
                                            <hr style="flex:1;border-top:1px solid #dee2e6;margin:0;">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="referred_by">Referred By</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi bi-person-lines-fill"></i></div>
                                            </div>
                                            <input type="text" class="form-control" name="referred_by" id="referred_by" placeholder="Dr. Name" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="referral_hospital">Referral Hospital / Clinic</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi bi-hospital"></i></div>
                                            </div>
                                            <input type="text" class="form-control" name="referral_hospital" id="referral_hospital" placeholder="Hospital / Clinic name" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="referral_type">Referral Type</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text"><i class="bi bi-arrow-left-right"></i></div>
                                            </div>
                                            <select class="form-control form-select" name="referral_type" id="referral_type">
                                                <option value="">Select Referral Type</option>
                                                <option value="Internal">Internal</option>
                                                <option value="External">External</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="referral_notes">Referral Notes / Remarks</label>
                                        <div class="input-group">
                                            
                                            <textarea  class="form-control" name="referral_notes" id="referral_notes" placeholder="Notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="bpSit_systolic"><i class="material-icons fa-lg">airline_seat_recline_normal</i>BP/mmHg</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="bpSit_systolic" id="bpSit_systolic" value="">
                                            <span class="divider">/</span>
                                            <input type="text" class="form-control" name="bpSit_diastolic" id="bpSit_diastolic" value="">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="bpStand_systolic"><i class="fa-solid fa-person"></i>BP/mmHg</label>
                                        <div class="input-wrapper">
                                            <input type="text" class="form-control" name="bpStand_systolic" id="bpStand_systolic" value="">
                                            <span class="divider">/</span>
                                            <input type="text" class="form-control" name="bpStand_diastolic" id="bpStand_diastolic" value="">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="weight"> Weight (Kg)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-weight-scale"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="weight" id="weight" value="" />
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="height"> Height (cms)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fas fa-ruler-vertical"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="height" id="height" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="bmi">BMI Value</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <img src="assets/img/bmi.jpeg" alt="BMI Icon" width="18" height="18" class="fw-bold">
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="bmi" id="bmi" value="" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="grbs">GRBS (mg/dL)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-droplet"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="grbs" id="grbs" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="heart_rate">Heart Rate/min</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fa-solid fa-heart-pulse"></i>
                                            </span>
                                            <input type="text" class="form-control" name="heart_rate" id="heart_rate" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="temperature">Temp (°F) </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-thermometer-half"></i>
                                            </span>
                                            <input type="text" class="form-control" name="temperature" id="temperature" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="respiration_rate">Resp / min </label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-lungs-fill"></i>
                                            </span>
                                            <input type="text" class="form-control" name="respiration_rate" id="respiration_rate" value="" />
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-2 col-sm-12">
                                        <label for="spO2"> SpO2 (%)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <img src="assets/img/spo2.jpg" alt="SpO2 Icon" width="22" height="22" classs="fw-bold">
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="spO2" id="spO2" value="" />
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="patient_overview"> Over-View of Patient</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-street-view"></i>
                                                </div>
                                            </div>
                                            <input type="text" class="form-control" name="patient_overview" id="patient_overview" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="appoint_date"> Date <span class="text-danger">*</span> </label>
                                        <input type="date" class="form-control" name="appoint_date" id="appoint_date" value="<?php echo $currentDate; ?>" onchange="GetDateByDoctorName()" />
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="doctor_name">Doctor Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-stethoscope"></i>
                                                </div>
                                            </div>
                                            <select class="form-control form-select" name="doctor_name" id="doctor_name" onchange="GetDoctorTime();GetDoctorFee();enableConcession();" onclick="checkDoctorTime();" disabled>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-sm-12">
                                        <label for="amount">Amount <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-currency-rupee"></i>
                                            </span>
                                            <input type="number" class="form-control amount" name="amount" id="amount" value="" />
                                        </div>
                                    </div>

                                    <!-- <div class="form-group col-lg-3 col-sm-12">
                                        <label for="amount_method" class="amount_method">Payment Method </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="bi bi-cash"></i>
                                                </div>
                                            </div>
                                            <select class="form-control form-select" name="amount_method" id="amount_method" onchange="enterTxn();">
                                                <option value="">Select Payment Method</option>
                                                <?php
                                                $getPayment_method = mysqli_query($conn, "SELECT payment_method_id, payment_method FROM payment_method WHERE status='1' ORDER BY payment_method_id ASC") or die(mysqli_error($conn));
                                                while ($resPayment = mysqli_fetch_object($getPayment_method)) {
                                                    $selected = ($resPayment->payment_method == "Cash") ? "selected" : "";
                                                ?>
                                                    <option value="<?php echo $resPayment->payment_method; ?>" <?php echo $selected; ?>>
                                                        <?php echo $resPayment->payment_method; ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="row">
                                        <!-- <div class="form-group col-lg-3 col-sm-12" id="txnDetails" style="display:none;">
                                            <label for="transactionNumber" id="valueLabel">Transaction Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-receipt"></i>
                                                </span>
                                                <input type="text" id="transactionNumber" name="transactionNumber" class="form-control"
                                                    placeholder="Enter transaction number" required>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="concessionName">Concession Name</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi bi-card-text"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control form-select" name="concessionName" id="concessionName" onchange="fillConcessionDetails();" disabled>
                                                    <option value="">Select Concession</option>
                                                    <?php
                                                    $orgId = $SessionOrgId;
                                                    if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                                        $getConcessions = mysqli_query($conn, "
                                                            SELECT concession_id, concession_name, concession_type, concession_value 
                                                            FROM concessions 
                                                            WHERE status='1'
                                                            ORDER BY concession_name ASC
                                                        ") or die(mysqli_error($conn));
                                                    } else {
                                                        $getConcessions = mysqli_query($conn, "
                                                            SELECT concession_id, concession_name, concession_type, concession_value 
                                                            FROM concessions 
                                                            WHERE status='1' AND org_id='$orgId'
                                                            ORDER BY concession_name ASC
                                                        ") or die(mysqli_error($conn));
                                                    }

                                                    while ($resConcession = mysqli_fetch_object($getConcessions)) {
                                                        echo '<option value="' . $resConcession->concession_id . '" 
                                                                    data-type="' . $resConcession->concession_type . '" 
                                                                    data-value="' . $resConcession->concession_value . '">'
                                                            . $resConcession->concession_name .
                                                            '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div> -->

                                        <!-- Concession Type -->
                                        <!-- <div class="form-group col-lg-3 col-sm-12" id="concessionTypeDiv" style="display:none;">
                                            <label for="concessionType">Concession Type</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-diagram-3-fill"></i>
                                                </span>
                                                <select id="concessionType" name="concessionType" class="form-control form-select" onchange="validateConcessionValue();">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="percentage">Percentage</option>
                                                    <option value="amount">Amount</option>
                                                </select>
                                            </div>
                                        </div> -->

                                        <!-- Concession Value -->
                                        <!-- <div class="form-group col-lg-3 col-sm-12" id="concessionValueDiv" style="display:none;">
                                            <label for="concessionValue" id="valueLabel">Value</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-cash-coin"></i>
                                                </span>
                                                <input type="text" id="concessionValue" name="concessionValue" class="form-control"
                                                    placeholder="Enter value" value="" onchange="validateConcessionValue();">
                                            </div>
                                        </div> -->
                                    </div>

                                    <div class="form-group col-lg-12 col-sm-12" id="">
                                        <input type="hidden" name="start_time" id="start_time" value="" />
                                        <input type="hidden" id="end_time" name="end_time" value="" />
                                        <label> Time Slot <span class="text-danger">*</span> </label>
                                        <div class="row" id="showTimeSlot" name="showTimeSlot" id='myDIV1'>
                                            <!-- Time Slots -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-center">
                                <button class="btn btn-primary" type="button" name="saveData" id="saveData" value="" data-bs-toggle='modal' data-bs-target='#basicModal' onclick='myConformation();One()'> Book Appointment</button>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Appointment List</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="showMenusData">
                                        <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- existing patient -->
                <!-- <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header">
                                <h4> Appointment Existing Patient </h4>
                            </div>
                            <form method="POST" id="Myform1" action="" enctype="multipart/form-data" class="" >
                                <input type="hidden" name="appointDate" id="appointDate" value="" >
                                <input type="hidden" name="existing_appoint_id" id="existing_appoint_id" value="" >
                                <input type="hidden" name="Existing_bill_id" id="Existing_bill_id" value="" >
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="ExistingAppointID"> Appointment ID <span class="text-danger">*</span> </label>
                                            <input type="text" class="form-control"  name="ExistingAppointID" id="ExistingAppointID"  value="" disabled>
                                            <input type="hidden" class="form-control"  name="AppointIDs" id="AppointIDs"  value="">
                                        </div>
                                        <?php

                                        if ($SessionUserId == "1") {
                                        ?>
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="E_organizations" class="Organization"> Organization <span class="text-danger">*</span></label>
                                            <select class="form-control form-select" name="E_organizations" id="E_organizations" onchange="EGetOrgByDoctor();Getorgpatientnames()">
                                                <option value="">Select Organization</option>
                                                <?php
                                                $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                                while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                                                ?>
                                                    <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                        }
                                        ?>  
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="payment_method" class="payment_method"> Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-control form-select" name="payment_method" id="payment_method">
                                                <option value="">Select Payment Method</option>
                                                <?php
                                                $getPayment_method = mysqli_query($conn, "SELECT payment_method_id, payment_method FROM payment_method WHERE status='1'  ORDER BY payment_method_id ASC") or die(mysqli_error($conn));
                                                while ($resPayment = mysqli_fetch_object($getPayment_method)) {
                                                ?>
                                                <option value="<?php echo $resPayment->payment_method; ?>"><?php echo $resPayment->payment_method; ?></option>
                                                <?php
                                                }
                                                ?>
                                                <option value="Both (Cash + UPI)">Both (Cash + UPI)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_patient_name"> Mobile Number <span class="text-danger">*</span> </label>
                                            <select type="text" class="form-control form-select existing_patient_name"  name="existing_patient_name" id="existing_patient_name" onchange="GetName()">
                                            <option value="" >Select Mobile Number</option>
                                            <?php
                                            // FIX_B_1903: doctor-scope filter
                                            $docScope_B1903 = currentDoctorScopeSql('doctor_name');
                                            if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                                $getTestGroup = mysqli_query($conn, "SELECT patient_name, appoint_id, mobile_number FROM appointment_online WHERE appoint_status='1' $docScope_B1903") or die(mysqli_error($conn));
                                            } else {
                                                $getTestGroup = mysqli_query($conn, "SELECT patient_name, appoint_id, mobile_number FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' $docScope_B1903 ORDER BY appoint_id DESC") or die(mysqli_error($conn));
                                            }
                                            while ($row = mysqli_fetch_object($getTestGroup)) {
                                                // $old_date = $row->appoint_date;
                                                // $currentDate;
                                                // $diff = strtotime($currentDate) - strtotime($old_date);
                                                // $daysDifference = $diff / (60 * 60 * 24);
                                                // if($daysDifference > 10){
                                                //     $Keyvalue = "" ;
                                                //     $Keyvalue1 = "" ;
                                                // } else{
                                                $Keyvalue = $row->patient_name;
                                                $Keyvalue1 = $row->appoint_id;
                                                $mobile = $row->mobile_number;
                                                // }
                                            ?>
                                            <option data-custom-value="<?= $Keyvalue1; ?>" value="<?= $Keyvalue1; ?>" > <?= $mobile; ?> </option>
                                            <?php } ?>
                                            </select>
                                            <span id="existing_patient_nameID"></span>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_mobile_number"> Name <span class="text-danger">*</span> </label>
                                                <select type="tel" class="form-control form-select existing_mobile_number"  name="existing_mobile_number" id="existing_mobile_number">
                                                <option value="" >Select Name</option>
                                                <?php
                                                // FIX_B_1903: doctor-scope filter
                                                $docScope_B1903b = currentDoctorScopeSql('doctor_name');
                                                $getTestGroup = mysqli_query($conn, "SELECT mobile_number, appoint_id, patient_name  FROM appointment_online WHERE appoint_status='1' AND org_id='$SessionOrgId' $docScope_B1903b ORDER BY appoint_id DESC") or die(mysqli_error($conn));
                                                while ($row = mysqli_fetch_object($getTestGroup)) {
                                                ?>
                                                <option value="<?= $row->appoint_id ?>" > <?= $row->patient_name ?>  </option>
                                                <?php
                                                }
                                                ?>
                                            
                                                </select>

                                            <span id="existing_mobile_numberID"></span>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="appoint_unicode"> Registration ID <span class="text-danger">*</span> </label>
                                            <input type="text" class="form-control"  name="existing_appoint_unicode" id="existing_appoint_unicode"  value="" disabled/>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="patient_email"> Gender <span class="text-danger">*</span></label>
                                                <br> 
                                                <div class="">
                                                    <input type="radio" name="gender" id="male" value="Male"/> Male
                                                    <input type="radio" name="gender" id="female" value="Female" /> Female
                                                    <input type="radio" name="gender" id="other" value="Others" /> Others 
                                                </div>
                                        </div>
                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_dob">Date of Birth</label>
                                            <input type="date" class="form-control" name="existing_dob" id="existing_dob" onchange="calculateAgeFromDOB('existing_dob','existing_age')" />
                                        </div>
                                        <div class="form-group col-lg-1 col-sm-12">
                                            <label for="existing_age"> Age <span class="text-danger">*</span> </label>
                                            <input type="text" class="form-control" name="existing_age" id="existing_age" value=""/>
                                        </div>
                                    </div>
                                    <div class="row">
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
                                                <input type="text" class="form-control" name="existing_weight" id="existing_weight" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_height">Height (cms)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_height" id="existing_height" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_bmi">BMI Value</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_bmi" id="existing_bmi" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_grbs">GRBS (mg/dL)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_grbs" id="existing_grbs" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_heart_rate">Heart Rate/min</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_heart_rate" id="existing_heart_rate" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_temperature">Temp (°F)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_temperature" id="existing_temperature" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-sm-12">
                                            <label for="existing_respiration_rate">Resp / min</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_respiration_rate" id="existing_respiration_rate" value="">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_spO2">SPO2 (%) (on Room Air)</label>
                                            <div class="input-group">
                                                <input type="tel" class="form-control" name="existing_spO2" id="existing_spO2" value="">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_patient_overview">Over-View of Patient</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="existing_patient_overview" id="existing_patient_overview" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">    

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="patient_email"> Email  <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"  name="existing_patient_email" id="existing_patient_email"  value=""/>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_appoint_date"> Date <span class="text-danger">*</span> </label>
                                            <input type="date" class="form-control"  name="existing_appoint_date" id="existing_appoint_date"  value="<?php echo $currentDate ?>" onchange="GetDoctorNameExisting()"/>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="existing_doctor_name"> Doctor Name <span class="text-danger">*</span> </label>
                                            <select class="form-control form-select" name="existing_doctor_name" id="existing_doctor_name" onchange="GetDoctorTimeExisting();GetExistingDoctorFee()">
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-3 col-sm-12">
                                            <label for="payment"> Amount <span class="text-danger">*</span> </label>
                                            <input type="tel" class="form-control payment"  name="payment" id="payment"  value="">
                                        </div>

                                        <div class="form-group col-lg-12 col-sm-12" id="">
                                            <input type="hidden" name="existing_start_time" id="existing_start_time" value=""/>  
                                            <input type="hidden" id="existing_end_time" name="existing_end_time"  value="" />
                                            <label> Time Slot <span class="text-danger">*</span> </label>
                                            <div class="row" id="Existing_showTimeSlot" name="Existing_showTimeSlot" id='myDIV1'>
                                            Time Slots -->
                <!-- </div>
                                        </div> -->

                <!-- </div> -->
                <!-- </div> -->

                <!-- <div class="card-footer text-center">
                                    <button class="btn btn-primary" type="button" name="saveDataExisting" id="saveDataExisting" value="" data-bs-toggle='modal' data-bs-target='#exampleModal'  onclick='myExistingConformation();Two()'> Book Appointment</button>
                                </div>
                                
                            </form>  
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Appointment Existing List</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive" id="showMenusDataExisting">
                                            <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
            </div>

        </div>
</div>

<form action="" method="POST" id="deleteFormId">
    <input type="hidden" name="deleteID" id="deleteID" value="" />
</form>
<form action="" method="POST" id="deleteFormId1">
    <input type="hidden" name="deleteID1" id="deleteID1" value="" />
</form>
</div>
</section>

</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment & Concession Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <input type="hidden" id="modalAppointId">
                    <input type="hidden" id="modalOrgId">
                    <input type="hidden" id="patientID">
                    <input type="hidden" id="createdBy" value="<?= $SessionRoleId ?>">

                    <!-- Doctor Fee -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="Doctor_fee">Doctor Fee</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="Doctor_fee" disabled>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="modal_amount_method">Payment Method</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-cash"></i>
                                </div>
                            </div>
                            <select class="form-control form-select" id="modal_amount_method">
                                <option value="">Select Payment Method</option>
                                <?php
                                $getPayment_method = mysqli_query($conn, "SELECT payment_method_id, payment_method FROM payment_method WHERE status='1' ORDER BY payment_method_id ASC");
                                while ($resPayment = mysqli_fetch_object($getPayment_method)) {
                                    $selected = ($resPayment->payment_method == "Cash") ? "selected" : "";
                                    echo "<option value='{$resPayment->payment_method}' {$selected}>{$resPayment->payment_method}</option>";
                                }
                                ?>
                                <option value="Both (Cash + UPI)">Both (Cash + UPI)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transaction Number (UPI/Both) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_txnDetails" style="display:none;">
                        <label for="modal_transactionNumber">UPI Transaction Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="modal_transactionNumber" placeholder="Enter UPI transaction number">
                        </div>
                    </div>

                    <!-- UPI Amount (shown when UPI or Both) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_txnAmountDiv" style="display:none;">
                        <label for="modal_transactionAmount">UPI Amount (&#8377;)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="modal_transactionAmount" placeholder="Enter UPI amount" min="0">
                        </div>
                    </div>

                    <!-- Cash Amount (shown only for Both Cash+UPI) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_cashAmountDiv" style="display:none;">
                        <label for="modal_cashAmount">Cash Amount (&#8377;)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-cash"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="modal_cashAmount" placeholder="Enter cash amount" min="0">
                        </div>
                    </div>

                    <!-- Concession Name -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="modal_concessionName">Concession Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-card-text"></i>
                                </div>
                            </div>
                            <select class="form-control form-select" id="modal_concessionName" onchange="fillConcessionDetails();">
                                <option value="">Select Concession</option>
                                <?php
                                // FIX_B_053: scope concession dropdown to caller's org (was global)
                                $getConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1' AND org_id='$SessionOrgId' ORDER BY concession_name ASC");
                                while ($resConcession = mysqli_fetch_object($getConcessions)) {
                                    echo '<option value="' . $resConcession->concession_id . '" 
                              data-type="' . $resConcession->concession_type . '" 
                              data-value="' . $resConcession->concession_value . '">'
                                        . $resConcession->concession_name .
                                        '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Concession Type -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_concessionTypeDiv" onchange="validateConcessionValue();" style="display:none;">
                        <label for="modal_concessionType">Concession Type</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                            </div>
                            <select id="modal_concessionType" class="form-control form-select">
                                <option value="">-- Select Type --</option>
                                <option value="percentage">Percentage</option>
                                <option value="amount">Amount</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-6 col-sm-12" id="modal_concessionValueDiv" style="display:none;">
                        <label for="modal_concessionValue" id="valueLabel">Value</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text" id="valueSymbol"> <!-- dynamic symbol here -->
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <input type="text" id="modal_concessionValue" class="form-control" placeholder="Enter value">
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="skipModalPayment();">Skip</button>
                <button type="button" class="btn btn-primary" onclick="saveModalPayment();">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- basic modal new patient-->
<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Appointment Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>amount_method1
            <div class="modal-body">
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <div class="" id="showMenusData1">
                        <input type="hidden" name="appoint_id1" id="appoint_id1" value="">
                        <input type="hidden" name="patient_name1" id="patient_name1" value="">
                        <input type="hidden" name="appoint_unicode1" id="appoint_unicode1" value="">
                        <input type="hidden" name="appointID1" id="appointID1" value="">
                        <input type="hidden" name="gender" id="gender1" value="">
                        <input type="hidden" name="temperature1" id="temperature1" value="">
                        <input type="hidden" name="dob1" id="dob1" value="">
                        <input type="hidden" name="age1" id="age1" value="">
                        <input type="hidden" name="mobile_number1" id="mobile_number1" value="">
                        <input type="hidden" name="patient_email1" id="patient_email1" value="">
                        <input type="hidden" name="appoint_date1" id="appoint_date1" value="">
                        <input type="hidden" name="doctor_name1" id="doctor_name1" value="">
                        <input type="hidden" name="start_time1" id="start_time1" value="">
                        <input type="hidden" name="end_time1" id="end_time1" value="">
                        <input type="hidden" name="hidden111" id="hidden111" value="">
                        <input type="hidden" name="organizations1" id="organizations1" value="">
                        <input type="hidden" name="amount_method1" id="amount_method1" value="">
                        <input type="hidden" name="amount1" id="amount1" value="">
                        <input type="hidden" name="billID1" id="billID1" value="">

                        <!-- New Inputs for Vital Signs -->
                        <input type="hidden" name="bpSit_systolic1" id="bpSit_systolic1" value="">
                        <input type="hidden" name="bpSit_diastolic1" id="bpSit_diastolic1" value="">
                        <input type="hidden" name="bpStand_systolic1" id="bpStand_systolic1" value="">
                        <input type="hidden" name="bpStand_diastolic1" id="bpStand_diastolic1" value="">
                        <input type="hidden" name="weight1" id="weight1" value="">
                        <input type="hidden" name="height1" id="height1" value="">
                        <input type="hidden" name="bmi1" id="bmi1" value="">
                        <input type="hidden" name="heart_rate1" id="heart_rate1" value="">
                        <input type="hidden" name="grbs1" id="grbs1" value="">
                        <input type="hidden" name="spO21" id="spO21" value="">
                        <input type="hidden" name="patient_overview1" id="patient_overview1" value="">
                        <input type="hidden" name="respiration_rate1" id="respiration_rate1" value="">
                        <input type="hidden" name="referred_by1" id="referred_by1" value="">
                        <input type="hidden" name="referral_hospital1" id="referral_hospital1" value="">
                        <input type="hidden" name="referral_notes1" id="referral_notes1" value="">
                        <input type="hidden" name="referral_type1" id="referral_type1" value="">



                        <!-- <b id="data1"></b> -->
                        <div class="row">
                            <div class="col-12">
                                <p>Payment Method / Amount: - <b id="amount_method_view"></b> / <b id="amount_view"></b></p>
                            </div>
                        </div>
                        <p>Bill ID: - <b id="BillIDdata"></b></p>
                        <div class="row">
                            <div class="col-7">
                                <p>Appointment ID: - <b id="data16"></b></p>
                            </div>
                            <div class="col-5">
                                <p>Registration ID: - <b id="data11"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p> Name: - <b id="data2"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Blood Pressure <i class="fa-solid fa-person"></i>: - <b id="data17"></b>/<b id="data18"></b></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <p>Gender: - <b id="data3"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Blood Pressure <i class="fa-solid fa-wheelchair"></i>: - <b id="data12"></b>/<b id="data15"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Temperature: - <b id="data13"></b><b>°C</b></p>
                            </div>
                            <div class="col-6">
                                <p>Glucose Level: - <b id="data14"></b><b>%</b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Age: - <b id="data10"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Mobile Number: - <b id="data4"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Email: - <b id="data5"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Booking Date: - <b id="data6"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Doctor Name: - <b id="data7"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Time Slot: - <b id="data8"></b> - <b id="data9"></b></p>
                            </div>
                        </div>
                        <div class="row" id="referral_display_row" style="display:none;">
                            <div class="col-6">
                                <p>Referred By: - <b id="show_referred_by"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Referral Type: - <b id="show_referral_type"></b></p>
                            </div>
                        </div>
                        <div class="row" id="referral_hospital_row" style="display:none;">
                            <div class="col-6">
                                <p>Referral Hospital: - <b id="show_referral_hospital"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Referral Notes: - <b id="show_referral_notes"></b></p>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer br">
                        <button type="submit" class="btn btn-primary" value="">Confirm</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- basic modal11111111111-->
<div class="modal fade" id="basicModalExisting" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Appointment Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="" name="">
                    <input type="text" value="" />
                </form>
            </div>
            <div class="modal-footer br">
                <button class="btn btn-primary" value="">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
            </div>

        </div>
    </div>
</div>

<!-- Modal with form Existing patient -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModal" style=" padding-left: 118px;"> Appointment Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -50px; margin-right: -16px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="MyFormId" action="" enctype="multipart/form-data" class="">
                    <div class="" id="showMenusData2">
                        <input type="hidden" name="appointDate2" id="appointDate2" value="">
                        <input type="hidden" name="atmt_id" id="atmt_id" value="">
                        <input type="text" name="patient_name2" id="patient_name2" value="" />
                        <input type="hidden" name="appoint_unicode2" id="appoint_unicode2" value="" />
                        <input type="hidden" name="appointID2" id="appointID2" value="" />
                        <input type="hidden" name="gender2" id="gender2" value="" />
                        <!-- <input type="hidden" name="systolic2" id="systolic2" value=""/> -->
                        <!-- <input type="hidden" name="diastolic2" id="diastolic2" value=""/> -->
                        <input type="hidden" name="temperature2" id="temperature2" value="" />
                        <!-- <input type="hidden" name="glucose_level2" id="glucose_level2" value=""/> -->
                        <input type="hidden" name="age2" id="age2" value="" />
                        <input type="text" name="mobile_number2" id="mobile_number2" value="" />
                        <input type="hidden" name="patient_email2" id="patient_email2" value="" />
                        <input type="hidden" name="appoint_date2" id="appoint_date2" value="" />
                        <input type="hidden" name="doctor_name2" id="doctor_name2" value="" />
                        <input type="hidden" name="start_time2" id="start_time2" value="" />
                        <input type="hidden" name="end_time2" id="end_time2" value="" />
                        <input type="hidden" name="E_organizations2" id="E_organizations2" value="" />
                        <input type="hidden" name="payment_method2" id="payment_method2" value="" />
                        <input type="hidden" name="payment2" id="payment2" value="" />
                        <input type="hidden" name="billId2" id="billId2" value="" />

                        <input type="hidden" name="bpSit_systolic2" id="bpSit_systolic2" value="" />
                        <input type="hidden" name="bpSit_diastolic2" id="bpSit_diastolic2" value="" />
                        <input type="hidden" name="bpStand_systolic2" id="bpStand_systolic2" value="" />
                        <input type="hidden" name="bpStand_diastolic2" id="bpStand_diastolic2" value="" />
                        <input type="hidden" name="weight2" id="weight2" value="" />
                        <input type="hidden" name="height2" id="height2" value="" />
                        <input type="hidden" name="bmi2" id="bmi2" value="" />
                        <input type="hidden" name="grbs2" id="grbs2" value="" />
                        <input type="hidden" name="heart_rate2" id="heart_rate2" value="" />
                        <input type="hidden" name="respiration_rate2" id="respiration_rate2" value="" />
                        <input type="hidden" name="spO22" id="spO22" value="" />
                        <input type="hidden" name="patient_overview2" id="patient_overview2" value="" />

                        <div class="row">
                            <div class="col-12">
                                <p>Payment Method / Amount: - <b id="payment_method_view"></b> / <b id="payment_view"></b></p>
                            </div>
                        </div>
                        <p>Bill ID:- <b id="ExistingBillId"></b></p>
                        <div class="row">
                            <div class="col-7">
                                <p>Appointment ID:- <b id="ExistingData15"></b></p>
                            </div>
                            <div class="col-5">
                                <p>Patient ID:- <b id="ExistingData3"></b></p>
                            </div>
                        </div>
                        <p> Name:- <b id="ExistingData2"></b></p>
                        <div class="row">
                            <div class="col-6">
                                <p>Gender:- <b id="ExistingData10"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Blood Pressure:- <b id="ExistingData11"></b>/<b id="ExistingData14"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Temperature:- <b id="ExistingData12"></b><b>°C</b></p>
                            </div>
                            <div class="col-6">
                                <p>Glucose Level:- <b id="ExistingData13"></b><b>%</b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Age:- <b id="ExistingData1"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Mobile Number:- <b id="ExistingData4"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Email:- <b id="ExistingData5"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Booking Date:- <b id="ExistingData6"></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p>Doctor Name:- <b id="ExistingData7"></b></p>
                            </div>
                            <div class="col-6">
                                <p>Time Slot:- <b id="ExistingData8"></b> - <b id="ExistingData9"></b></p>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer br">
                        <button class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#exampleModal' value="">Confirm</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<?php require_once("ajax/footer.php") ?>

<script>
    function showRedSubmitButton() {
        $("#saveData").hide();
        $("#redSaveData").show();
    }

    function enableSubmitButton() {
        $("#saveData").prop("disabled", false).show();
        $("#redSaveData").hide();
    }

    $("#redSaveData").click(function() {
        timeslots();
    });

    let patientList = [];
    $(document).ready(function() {
        // GetDoctorName();
        GetAppointment();
        GetAppointmentExisting();
        GetDoctorNameExisting();

        // GetDateByDoctorName();

        // GetPatientID();
        GetMobileNumberExisting();
        getPatientIDUsingDate();
        // getPatientIDUsingDateExisting();

        getPatientUniqueID();

        getBillIDUsingDate();
        getExistingBillIDUsingDate();

        $('.existing_patient_name').select2();
        $('.existing_mobile_number').select2();

        $('#existing_patient_name').select2();
        $('#existing_patient_name').on('change', function() {
            $('#existing_mobile_number').val(null).trigger('change');
            $('#existing_appoint_unicode').val('');
        });

        fetchPatientNameMobile();

        $("#patient_name").on("change", function() {
            var genderSelect = $("input[name='gender']");
            genderSelect.prop('checked', false);
            $("#patient_email").val('');
            $("#respiration_rate").val('');
            $("#spO2").val('');
            $("#patient_overview").val('');
            $("#doctor_name").val('');

            const value = $(this).val();
            let name = value.trim();
            let mobile = "";

            if (value.includes(" - ")) {
                [name, mobile] = value.split(" - ").map(v => v.trim());
                $(this).val(name);
                $("#patient_mobile").val(mobile);
            }

            checkIfPatientExists(name, mobile);
            GetDateByDoctorName();
        });

        $("#patient_mobile").on("change", function() {
            const value = $(this).val();
            let mobile = value.trim();
            let name = "";

            if (value.includes(" - ")) {
                [mobile, name] = value.split(" - ").map(v => v.trim());
                $(this).val(mobile);
                $("#patient_name").val(name);
            }

            checkIfPatientExists(name, mobile);
        });

        $("#patient_mobile").on("keypress", function(e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
                e.preventDefault();
            }
        });

    });
    // function enterTxn(){
    //     const value = $("#amount_method").val();
    //     if (value == "UPI" ) {
    //         $('#txnDetails').show();
    //     } else {
    //         $('#txnDetails').hide();

    //     }
    // }

    $('#modal_amount_method').on('change', function() {
        const val = $(this).val().toLowerCase();
        if (val === 'upi' || val === 'both (cash + upi)') {
            $('#modal_txnDetails').show();
            $('#modal_txnAmountDiv').show();
        } else {
            $('#modal_txnDetails').hide();
            $('#modal_txnAmountDiv').hide();
            $('#modal_transactionNumber').val('');
            $('#modal_transactionAmount').val('');
        }
        if (val === 'both (cash + upi)') {
            $('#modal_cashAmountDiv').show();
        } else {
            $('#modal_cashAmountDiv').hide();
            $('#modal_cashAmount').val('');
        }
    });


    function calculateBMI() {
        const weight = parseFloat(document.getElementById("weight").value);
        const heightCm = parseFloat(document.getElementById("height").value);

        if (!isNaN(weight) && !isNaN(heightCm) && heightCm > 0) {
            const heightM = heightCm / 100;
            const bmi = weight / (heightM * heightM);
            document.getElementById("bmi").value = bmi.toFixed(2);
        } else {
            document.getElementById("bmi").value = '';
        }
    }

    document.getElementById("weight").addEventListener("input", calculateBMI);
    document.getElementById("height").addEventListener("input", calculateBMI);

    function enableConcession() {
        let doctorEl = document.getElementById("doctor_name");
        let concession = document.getElementById("concessionName");
        if (!concession) return;
        concession.disabled = !doctorEl || doctorEl.value === "";
    }

    //    function fillConcessionDetails() {
    //         let selectedOption = $('#concessionName option:selected');

    //         if (selectedOption.val() !== "") {
    //             // $('#concessionTypeDiv, #concessionValueDiv').show();

    //             let type = selectedOption.data('type');
    //             let value = selectedOption.data('value');

    //             $('#concessionType').val(type).trigger('change');
    //             $('#concessionValue').val(value);
    //             validateConcessionValue();
    //         } else {
    //             $('#concessionTypeDiv, #concessionValueDiv').hide();
    //             $('#concessionType').val('');
    //             $('#concessionValue').val('');
    //         }
    //     }

    function fillConcessionDetails() {
        let selectedOption = $('#modal_concessionName option:selected');

        if (selectedOption.val() !== "") {
            $('#modal_concessionTypeDiv, #modal_concessionValueDiv').show();

            let type = selectedOption.data('type');
            let value = selectedOption.data('value');

            $('#modal_concessionType').val(type).trigger('change');
            $('#modal_concessionValue').val(value);
            validateConcessionValue();
        } else {
            $('#modal_concessionTypeDiv, #modal_concessionValueDiv').hide();
            $('#modal_concessionType').val('');
            $('#modal_concessionValue').val('');
        }
    }


    $('#modal_concessionType, #modal_concessionValue').on('input change', validateConcessionValue);

    function validateConcessionValue() {
        let type = $('#modal_concessionType').val();
        let valueInput = $('#modal_concessionValue');
        let value = parseFloat(valueInput.val()) || 0;
        let amount = parseFloat($('#Doctor_fee').val()) || 0;
        let symbolDiv = $('#valueSymbol');

        valueInput.removeClass('is-invalid');

        if (type === "percentage") {
            symbolDiv.text('%');
        } else if (type === "amount") {
            symbolDiv.text('₹');
        } else {
            symbolDiv.text('');
        }

        if (amount === 0) {
            valueInput.val(0);
            valueInput.addClass('is-invalid');
            return;
        }

        if (type === "percentage") {
            value = Math.floor(value);
            if (value > 100) {
                valueInput.addClass('is-invalid');
                valueInput.val(100);
            } else if (value < 0) {
                valueInput.addClass('is-invalid');
                valueInput.val(0);
            } else {
                valueInput.val(value);
            }
        } else if (type === "amount") {
            if (!isNaN(amount) && value > amount) {
                valueInput.addClass('is-invalid');
                valueInput.val(amount);
            } else if (value < 0) {
                valueInput.addClass('is-invalid');
                valueInput.val(0);
            }
        }
    }

    function fetchPatientNameMobile() {
        var orgId = $("#organizations").val();
        $.ajax({
            url: "ajax/AppointmentBooking/fetch_patient_names.php",
            method: "GET",
            dataType: "json",
            data: {
                org_id: orgId
            },
            success: function(data) {
                patientList = data;
                const nameList = $("#patient_nameDatalist");
                const mobileList = $("#patient_mobileDatalist");

                nameList.empty();
                mobileList.empty();

                data.forEach(function(patient) {
                    const nameMobile = `${patient.name} - ${patient.mobile}`;
                    const mobileName = `${patient.mobile} - ${patient.name}`;

                    nameList.append(`<option value="${nameMobile}">`);
                    mobileList.append(`<option value="${mobileName}">`);
                });
            }
        });
    }

    function checkIfPatientExists(name, mobile) {
        var organizations = $('#organizations').val();

        $.ajax({
            url: 'ajax/AppointmentBooking/checkPatientExists.php',
            type: 'POST',
            data: {
                name: name,
                mobile: mobile,
                org_id: organizations
            },
            success: function(data) {

                if (data != 1) {
                    var res = JSON.parse(data);

                    $("#age").val(res.age);
                    if (res.dob) { $("#dob").val(res.dob); }
                    $("#bpSit_systolic").val(res.bpSit_systolic);
                    $("#bpSit_diastolic").val(res.bpSit_diastolic);
                    $("#bpStand_systolic").val(res.bpStand_systolic);
                    $("#bpStand_diastolic").val(res.bpStand_diastolic);
                    $("#weight").val(res.weight);
                    $("#height").val(res.height);
                    $("#bmi").val(res.bmi);
                    $("#grbs").val(res.grbs);
                    $("#heart_rate").val(res.heart_rate);
                    $("#temperature").val(res.temperature);
                    $("#respiration_rate").val(res.respiration_rate);
                    $("#spO2").val(res.spO2);
                    $("#patient_overview").val(res.patient_overview);
                    $("#patient_email").val(res.patient_email);
                    $("#appoint_unicode").val(res.appoint_unicode);
                    // FIX_B_2381: id is "others" (plural) on the new-patient form, not "other".
                    $("#male").prop("checked", res.gender === "Male");
                    $("#female").prop("checked", res.gender === "Female");
                    $("#others").prop("checked", res.gender === "Others");
                    $("#validto").val(res.valid_to);
                    $("#appstatus").val(res.appointment_status);
                    $("#doc").val(res.doctor_name);
                    checkFields();
                } else {
                    getPatientUniqueID();
                    getPatientIDUsingDate();
                    clearData();
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    function calculateAgeFromDOB(dobFieldId, ageFieldId) {
        var dob = document.getElementById(dobFieldId).value;
        if (!dob) return;
        var today = new Date();
        var birthDate = new Date(dob);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age >= 0) {
            document.getElementById(ageFieldId).value = age;
        }
    }

    function clearData() {
        $("#age").val('');
        $("#bpSit_systolic").val('');
        $("#bpSit_diastolic").val('');
        $("#bpStand_systolic").val('');
        $("#bpStand_diastolic").val('');
        $("#weight").val('');
        $("#height").val('');
        $("#bmi").val('');
        $("#grbs").val('');
        $("#heart_rate").val('');
        $("#temperature").val('');
        $("#spO2").val('');
        $("#patient_overview").val('');
        $("#patient_email").val('');
        $("#appoint_unicode").val('');
        $("#male").prop("checked", false);
        $("#female").prop("checked", false);
        $("#other").prop("checked", false);
    }

    var color1 = 'rgba(253, 92, 92, 0.8';
    var color2 = 'rgba(51, 208, 161, 0.8)';
    var selectedDiv = null;

    function toggleBackgroundColor(element) {
        var currentColor = element.style.backgroundColor;
        if (currentColor === color1) {
            element.style.backgroundColor = color1;
        } else {
            element.style.backgroundColor = color2;
        }
    }

    function handleDivClick(element) {
        if (selectedDiv === element) {
            element.style.backgroundColor = color2;
            element.style.backgroundColor = color2;
            selectedDiv = null;
        } else {
            if (selectedDiv !== null) {
                selectedDiv.style.backgroundColor = color2;
            }
            element.style.backgroundColor = color1;
            selectedDiv = element;
        }
    }

    function move(start_time, end_time, res_available_date, count) {
        $('#start_time').val(start_time);
        $('#end_time').val(end_time);
        $('#appoint_date').val(res_available_date);

        var element = document.getElementById('myDIV' + count);
        toggleBackgroundColor(element);
        handleDivClick(element);
        checkBookFields();
    }


    function GetDateByDoctorName() {
        var appoint_date = $('#appoint_date').val();
        var org_id = $('#organizations').val();
        $.ajax({
            url: 'ajax/AppointmentBooking/GetDateByName.php',
            type: 'POST',
            data: {
                appoint_date: appoint_date,
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);

                if (!data || data.length === 0) {
                    $("#doctor_name").html('<option value="">No doctors available</option>').prop('disabled', true);
                    return;
                }

                var optionData = '<option value="">Select Doctor Name</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.doctorName_registrationNumber + '">' + val.doctor_name + '</option>';
                });

                $("#doctor_name").html(optionData);

                // Handle select enable/disable logic
                if (data.length === 1 || data[0].role === 'doctor') {
                    // If only one doctor available OR role is doctor, select it and disable dropdown
                    $("#doctor_name").val(data[0].doctorName_registrationNumber).prop('disabled', true);
                } else {
                    // Multiple doctors, enable selection
                    $("#doctor_name").prop('disabled', false);
                }

                // Trigger functions after populating
                GetDoctorTime();
                GetDoctorFee();
                enableConcession();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }


    function checkDoctorTime() {
        const dropdown = document.getElementById("doctor_name");
        const organizationname = document.getElementById("organizations");

        if (organizationname && organizationname.value !== '') {
            setTimeout(() => {
                if (dropdown.options.length <= 1) {
                    Swal.fire({
                        title: '',
                        text: 'Doctors time slots are not provided. Click OK to go to the page.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'OK',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'doctorstimeslot.php';
                        }
                    });
                }
            }, 200);
        } else {
            Swal.fire({
                title: '',
                text: 'Please select organization',
                icon: 'warning',
                confirmButtonText: 'OK',
                buttonsStyling: true
            });
        }
    }

    function GetDoctorTime() {
        var timeId = $("#doctor_name").val();
        var appoint_date = $("#appoint_date").val();
        var organizations = $("#organizations").val();

        var item = {};
        item['doctorName_registrationNumber'] = timeId;
        item['available_date'] = appoint_date;
        item['organizations'] = organizations;

        $.ajax({
            url: "ajax/AppointmentBooking/AppoinmentGetTimes.php",
            type: 'post',
            data: item,
            success: function(data) {
                $("#showTimeSlot").html(data);
                GetAppointment();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function GetDoctorFee() {
        // Assuming you have GetDoctorFee functionality
        // This is just a placeholder
    }

    function GetAppointment() {
        $.ajax({
            url: 'ajax/AppointmentBooking/getAllData.php',
            type: 'GET',
            success: function(data) {
                var org_id = '<?= $SessionOrgId ?>';
                if (data) {
                    $("#showMenusData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array = [0, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];
                    }

                    $("#tableExport1").dataTable({
                        retrieve: true,
                        dom: 'lBrftip',
                        buttons: [{
                                extend: 'copy',
                                exportOptions: {
                                    columns: buttons_array,
                                },
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: buttons_array,
                                },
                            },
                            {
                                extend: 'csv',
                                exportOptions: {
                                    columns: buttons_array,
                                },
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: buttons_array,
                                },
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: buttons_array,
                                },
                            },
                        ],
                    });
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function One(doctor_name) {
        var doctor_name = $("#doctor_name").val();
        $.ajax({
            url: 'ajax/AppointmentBooking/GetIDByName.php',
            type: 'POST',
            data: {
                doctor_name: doctor_name
            },
            dataType: 'json',
            success: function(data) {
                var optionData = '';
                $.each(data, function(key, val) {
                    optionData += ' ' + val.doctorName_registrationNumber + '';
                });
                $("#data7").html(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });

    }

    function GetDoctorFee() {
        var appointDate = $("#appoint_date").val();
        var doctors_time_id = $("#doctor_name").val();
        var validToDate = $("#validto").val();
        var appointStatus = $("#appstatus").val();
        var doctor = $("#doc").val();
        var selectedDoc = $("#doctor_name").val();

        var currentDate = new Date();
        var formattedDate = currentDate.getFullYear() + '-' +
            ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' +
            ('0' + currentDate.getDate()).slice(-2);

        if (selectedDoc != doctor) {
            getFeeByDoctor();
            return;
        }
        if (selectedDoc == doctor && appointStatus == 1 && validToDate >= formattedDate && appointDate <= validToDate) {
            $("#amount").val('0');
            return;
        }
        $.ajax({
            url: "ajax/AppointmentBooking/AppointmentDoctorFee.php",
            type: 'POST',
            data: {
                doctors_time_id: doctors_time_id
            },
            dataType: 'json',
            success: function(data) {
                $("#amount").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getFeeByDoctor() {
        var name = $("#patient_name").val();
        var mobile = $("#patient_mobile").val();
        var doctor = $("#doctor_name").val();
        var appointDate = $("#appoint_date").val();

        $.ajax({
            url: 'ajax/AppointmentBooking/getFeeByDoctor.php',
            type: 'POST',
            data: {
                name: name,
                mobile: mobile,
                doctor: doctor,
            },
            success: function(data) {
                // FIX_B_275: hoist `res` so `appointStatus`/`validToDate` reads below
                // work whether or not the JSON path fired. Was throwing
                // "Cannot read properties of undefined (reading 'appointment_status')"
                // 2× per page-load when data == 1 (no fee record path).
                var res = {};
                if (data != 1) {
                    try { res = JSON.parse(data); } catch (e) { res = {}; }
                    $("#validto").val(res.valid_to);

                } else {
                    $.ajax({
                        url: "ajax/AppointmentBooking/AppointmentDoctorFee.php",
                        type: 'POST',
                        data: {
                            doctors_time_id: doctor
                        },
                        dataType: 'json',
                        success: function(data) {
                            $("#amount").val(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
                var appointStatus = res.appointment_status;
                var validToDate = res.valid_to;
                var currentDate = new Date();
                var formattedDate = currentDate.getFullYear() + '-' +
                    ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + currentDate.getDate()).slice(-2);

                if (appointStatus == 1 && validToDate >= formattedDate && appointDate <= validToDate) {
                    $("#amount").val('0');
                    return;
                } else {
                    $.ajax({
                        url: "ajax/AppointmentBooking/AppointmentDoctorFee.php",
                        type: 'POST',
                        data: {
                            doctors_time_id: doctor
                        },
                        dataType: 'json',
                        success: function(data) {
                            $("#amount").val(data);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }




            }

        })
    }

    function getBillIDUsingDate() {
        $.ajax({
            url: "ajax/AppointmentBooking/AutoGenareteBill_Ids.php",
            type: 'get',
            dataType: 'json',
            success: function(data) {
                $("#bill_id").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function myConformation() {
        var appoint_id = $("#appoint_id").val();
        var appointID = $("#appointID").val();
        var patient_name = $("#patient_name").val();
        var appoint_unicode = $("#appoint_unicode").val();
        var gender = $("input[name='gender']:checked").val();
        var mobile_number = $("#patient_mobile").val();
        var organizations = $("#organizations").val();
        var patient_email = $("#patient_email").val();
        var age = $("#age").val();
        // var systolic = $("#systolic").val();
        // var diastolic = $("#diastolic").val();

        var bpSit_systolic = $("#bpSit_systolic").val();
        var bpSit_diastolic = $("#bpSit_diastolic").val();
        var bpStand_systolic = $("#bpStand_systolic").val();
        var bpStand_diastolic = $("#bpStand_diastolic").val();
        var weight = $("#weight").val();
        var height = $("#height").val();
        var bmi = $("#bmi").val();
        var heart_rate = $("#heart_rate").val();
        var temperature = $("#temperature").val();
        var grbs = $("#grbs").val();
        var spO2 = $("#spO2").val();
        var patient_overview = $("#patient_overview").val();
        var respiration_rate = $("#respiration_rate").val();
        var referred_by = $("#referred_by").val();
        var referral_hospital = $("#referral_hospital").val();
        var referral_notes = $("#referral_notes").val();
        var referral_type = $("#referral_type").val();

        var appoint_date = $("#appoint_date").val();
        var doctor_name = $("#doctor_name").val();
        var start_time = $("#start_time").val();
        var end_time = $("#end_time").val();
        var amount_method = $("#amount_method").val();
        var amount = $("#amount").val();
        var bill_id = $("#bill_id").val();


        $("#data1").html(appoint_id);
        $("#data16").html(appointID);
        $("#data2").html(patient_name);
        $("#data11").html(appoint_unicode);
        $("#data3").html(gender);
        $("#data12").html(bpSit_systolic);
        $("#data15").html(bpSit_diastolic);
        $("#data13").html(temperature);
        $("#data14").html(grbs);
        $("#data10").html(age);
        $("#data4").html(mobile_number);
        $("#data5").html(patient_email);
        $("#data6").html(appoint_date);
        // $("#data7").html(resultName);
        $("#data8").html(start_time);
        $("#data9").html(end_time);
        $("#amount_method_view").html(amount_method);
        $("#amount_view").html(amount);
        $("#BillIDdata").html(bill_id);

        $("#data17").html(bpStand_systolic);
        $("#data18").html(bpStand_diastolic);


        $("#appoint_id1").val(appoint_id);
        $("#appointID1").val(appointID);
        $("#patient_name1").val(patient_name);
        $("#appoint_unicode1").val(appoint_unicode);
        $("#gender1").val(gender);
        $("#age1").val(age);
        $("#dob1").val($("#dob").val());
        $("#mobile_number1").val(mobile_number);
        $("#patient_email1").val(patient_email);
        $("#appoint_date1").val(appoint_date);
        $("#doctor_name1").val(doctor_name);
        $("#start_time1").val(start_time);
        $("#end_time1").val(end_time);
        $("#organizations1").val(organizations);
        $("#amount_method1").val(amount_method);
        $("#amount1").val(amount);
        $("#billID1").val(bill_id);

        $("#bpSit_systolic1").val(bpSit_systolic);
        $("#bpSit_diastolic1").val(bpSit_diastolic);
        $("#bpStand_systolic1").val(bpStand_systolic);
        $("#bpStand_diastolic1").val(bpStand_diastolic);
        $("#weight1").val(weight);
        $("#height1").val(height);
        $("#bmi1").val(bmi);
        $("#heart_rate1").val(heart_rate);
        $("#temperature1").val(temperature);
        $("#grbs1").val(grbs);
        $("#spO21").val(spO2);
        $("#patient_overview1").val(patient_overview);
        $("#respiration_rate1").val(respiration_rate);
        $("#referred_by1").val(referred_by);
        $("#referral_hospital1").val(referral_hospital);
        $("#referral_notes1").val(referral_notes);
        $("#referral_type1").val(referral_type);

        if (referred_by || referral_hospital || referral_type) {
            $("#show_referred_by").html(referred_by || '--');
            $("#show_referral_type").html(referral_type || '--');
            $("#referral_display_row").show();
            if (referral_hospital || referral_notes) {
                $("#show_referral_hospital").html(referral_hospital || '--');
                $("#show_referral_notes").html(referral_notes || '--');
                $("#referral_hospital_row").show();
            }
        } else {
            $("#referral_display_row").hide();
            $("#referral_hospital_row").hide();
        }
    }

    $("#FormId").submit(function(event) {
        event.preventDefault();

        document.getElementById('basicModal').removeAttribute('style');

        var appoint_id = $("#appoint_id1").val();
        var appointID = $("#appointID1").val();
        var patient_name = $("#patient_name1").val().trim();
        var appoint_unicode = $("#appoint_unicode1").val();
        var gender = $("#gender1").val();
        var mobile_number = $("#mobile_number1").val();
        var organizations = $("#organizations").val();
        var patient_email = $("#patient_email1").val().trim();
        var age = $("#age1").val();
        var bpSit_systolic = $("#bpSit_systolic1").val();
        var bpSit_diastolic = $("#bpSit_diastolic1").val();
        var bpStand_systolic = $("#bpStand_systolic1").val();
        var bpStand_diastolic = $("#bpStand_diastolic1").val();
        var weight = $("#weight1").val();
        var height = $("#height1").val();
        var bmi = $("#bmi1").val();
        var heart_rate = $("#heart_rate1").val();
        var temperature = $("#temperature1").val().trim();
        var grbs = $("#grbs1").val();
        var spO2 = $("#spO21").val();
        var patient_overview = $("#patient_overview1").val();
        var respiration_rate = $("#respiration_rate1").val();
        var appoint_date = $("#appoint_date1").val();
        var doctor_name = $("#doctor_name1").val();
        var start_time = $("#start_time1").val();
        var end_time = $("#end_time1").val();
        var amount_method = $("#amount_method1").val();
        var amount = $("#amount1").val();
        var bill_id = $("#billID1").val();
        var validToDate = $("#validto").val();
        var transactionNumber = $("#transactionNumber").val();
        var concessionName = $("#concessionName").val();
        var concessionType = $("#concessionType").val();
        var concessionValue = $("#concessionValue").val();

        if (!patient_name && !mobile_number && !age && !gender && !appoint_date && !doctor_name && !amount) {
            swal('', 'All fields Required', 'warning');
            return;
        }
        if (!patient_name) {
            swal('', 'Please Enter Patient Name', 'warning');
            return;
        }

        if (!$('#patient_mobile').val().match('[0-9]{10}')) {
            swal('', 'Please Enter valid number', 'warning');
            return;
        }

        if (!age) {
            swal('', 'Please Enter Patient Age', 'warning');
            return;
        }


        if (!gender) {
            swal('', 'Please Select Gender', 'warning');
            return;
        }

        if (patient_email !== '') {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(patient_email)) {
                swal('', 'Please enter a valid Email', 'warning');
                return;
            }
        }

        if (!appoint_date) {
            swal('', 'Please Select Date', 'warning');
            return;
        }

        if (!doctor_name) {
            swal('', 'Please Select Doctor Name', 'warning');
            return;
        }


        if (!amount) {
            swal('', 'Please Enter Doctor Fee', 'warning');
            return;
        }

        var vitals_status = (
            bpSit_systolic ||
            bpSit_diastolic ||
            bpStand_systolic ||
            bpStand_diastolic ||
            weight ||
            height ||
            bmi ||
            heart_rate ||
            temperature ||
            grbs ||
            spO2 ||
            respiration_rate
        ) ? 1 : 0;

        var subitem = {
            transactionNumber1: transactionNumber,
            concessionName1: concessionName,
            concessionType1: concessionType,
            concessionValue1: concessionValue,
            appoint_id1: appoint_id,
            appointID1: appointID,
            patient_name1: patient_name,
            appoint_unicode1: appoint_unicode,
            gender1: gender,
            mobile_number1: mobile_number,
            organizations: organizations,
            patient_email1: patient_email,
            age1: age,
            bpSit_systolic1: bpSit_systolic,
            bpSit_diastolic1: bpSit_diastolic,
            bpStand_systolic1: bpStand_systolic,
            bpStand_diastolic1: bpStand_diastolic,
            weight1: weight,
            height1: height,
            bmi1: bmi,
            heart_rate1: heart_rate,
            temperature1: temperature,
            grbs1: grbs,
            spO21: spO2,
            patient_overview1: patient_overview,
            respiration_rate1: respiration_rate,
            appoint_date1: appoint_date,
            doctor_name1: doctor_name,
            start_time1: start_time,
            end_time1: end_time,
            amount_method1: amount_method,
            amount1: amount,
            billID1: bill_id,
            vitals_status: vitals_status,
            validToDate: validToDate,
            dob1: $("#dob1").val(),
            referred_by1: $("#referred_by1").val(),
            referral_hospital1: $("#referral_hospital1").val(),
            referral_notes1: $("#referral_notes1").val(),
            referral_type1: $("#referral_type1").val()
        };

        if (!organizations) {
            swal({
                title: '',
                text: 'Organization needs to be selected!',
                icon: 'warning',
                buttons: {
                    ok: {
                        text: 'OK',
                        value: true,
                        visible: true,
                        className: 'btn btn-primary'
                    }
                }
            });

            return;
        }
        if (!appoint_id) {
            $.ajax({
                url: 'ajax/AppointmentBooking/AppointIdVerify.php',
                type: 'POST',
                data: {
                    appointID: appointID,
                    organizations: organizations
                },
                success: function(data) {
                    // FIX_B_986: previous behaviour fired a swal "Appointment ID
                    // already exists. Do you want to proceed with the new ID?" with
                    // only an OK button — pure friction with no real decision for
                    // the user. The candidate ID generator (getdateforpatientid.php)
                    // uses COUNT(*) across appointment_online + appointment_existing
                    // while the verifier (AppointIdVerify.php) uses MAX(appoint_register_id)
                    // on appointment_online — so the two regularly disagree even on
                    // a fresh booking. Silently accept the verifier's ID and submit;
                    // also reflect it in the visible #appointID field.
                    var verifiedAppointID = JSON.parse(data);
                    if (appointID !== verifiedAppointID) {
                        $("#appointID").val(verifiedAppointID);
                        $("#appointID1").val(verifiedAppointID);
                        subitem['appointID1'] = verifiedAppointID;
                    }
                    submitForm(subitem);
                },
                error: function(error) {
                    // FIX_B_2382b: AppointIdVerify failure used to silently console.error
                    // while basicModal stayed open — same stuck-modal trap as B-2380.
                    // Dismiss the modal and surface the error so the user can recover.
                    console.error("Error in AJAX:", error);
                    try {
                        var _elV = document.getElementById('basicModal');
                        if (_elV) bootstrap.Modal.getOrCreateInstance(_elV).hide();
                    } catch (e) {}
                    var _msgV = (error && error.responseText) ? String(error.responseText).slice(0, 500) : 'Could not verify appointment id.';
                    swal('Could not save', 'Status ' + (error && error.status || '?') + ': ' + _msgV, 'error');
                }
            });
        } else {
            submitForm(subitem);
        }
    });

    function submitForm(subitem) {
        $.ajax({
            url: 'ajax/AppointmentBooking/addmodifyappoint_online.php',
            type: 'POST',
            data: subitem,
            success: function(data) {
                data = $.trim(data);

                if (data == '1') {
                    // swal({
                    //     title: 'Do you want to insert reports?',
                    //     icon: 'warning',
                    //     buttons: {
                    //     no: {
                    //         text: 'No',
                    //         value: false,
                    //         visible: true,
                    //         className: 'btn btn-secondary'
                    //     },
                    //     yes: {
                    //         text: 'Yes',
                    //         value: true,
                    //         visible: true,
                    //         className: 'btn btn-primary'
                    //     }
                    //     },
                    //     dangerMode: true,
                    //     closeOnClickOutside: false
                    // }).then(function(wantsReports) {
                    //     if (wantsReports) {
                    //     var $appointID = subitem.appointID1 || '';
                    //     var $orgID = subitem.organizations || '';
                    //     console.log($appointID);

                    //    window.location.href ="patienthistory.php?appointRegisterId=" + encodeURIComponent($appointID) + "&orgId=" + encodeURIComponent($orgID);
                    //     } else {
                    var appointID = subitem.appointID1 || '';
                    var orgID = subitem.organizations || '';
                    var Doctorfee = subitem.amount1 || '';
                    var appoint_unicode1 = subitem.appoint_unicode1 || '';

                    // Fill hidden fields or text inside the modal (if needed)
                    $('#modalAppointId').val(appointID); // example
                    $('#modalOrgId').val(orgID);
                    $('#Doctor_fee').val(Doctorfee);
                    $('#patientID').val(appoint_unicode1);



                    // FIX_B_2380: data-bs-toggle modals don't always have an instance
                    // when getInstance() is called the first time — use
                    // getOrCreateInstance() so close + show is reliable.
                    var confirmationModalEl = document.getElementById('basicModal');
                    bootstrap.Modal.getOrCreateInstance(confirmationModalEl).hide();

                    var paymentModalEl = document.getElementById('paymentModal');
                    bootstrap.Modal.getOrCreateInstance(paymentModalEl).show();

                    $("#FormId")[0].reset();
                    // swal('', " Appointment Booked successfully", 'success');
                    //     setTimeout(function(){
                    //         location.reload();
                    //     }, 2000);
                    // }
                    // });

                } else if(data == '2'){
                    swal({
                        title: "",
                        text: "Appointment Updated successfully",
                        icon: "success"
                    }).then(() => {
                        $("#FormId")[0].reset();

                        location.reload();
                    });
                } else if (data == '3') {
                    // FIX_B_2382a: dismiss basicModal before swal so the page un-blurs
                    // on every non-success branch (B-2380 sibling).
                    try { var _el3 = document.getElementById('basicModal'); if (_el3) bootstrap.Modal.getOrCreateInstance(_el3).hide(); } catch (e) {}
                    swal('', " Mobile Already Exists !", 'warning');
                } else if (data == '0') {
                    // FIX_B_2382a: dismiss basicModal so user can edit fields.
                    try { var _el0 = document.getElementById('basicModal'); if (_el0) bootstrap.Modal.getOrCreateInstance(_el0).hide(); } catch (e) {}
                    swal('', 'All fields Required', 'warning');
                    GetAppointment();
                } else {
                    // FIX_B_2382a: dismiss basicModal on unexpected response.
                    try { var _elU = document.getElementById('basicModal'); if (_elU) bootstrap.Modal.getOrCreateInstance(_elU).hide(); } catch (e) {}
                    swal('Error', 'Unexpected response from appointment save: ' + data, 'error');
                }
            },
            error: function(err) {
                // FIX_B_2380: silent error left the confirmation modal stuck on screen
                // (blurred page, no progress). Dismiss the modal + show what failed.
                console.log(err);
                try {
                    var el = document.getElementById('basicModal');
                    if (el) bootstrap.Modal.getOrCreateInstance(el).hide();
                } catch (e) {}
                var msg = (err && err.responseText) ? String(err.responseText).slice(0, 500) : 'Server error.';
                swal('Could not save', 'Status ' + (err.status || '?') + ': ' + msg, 'error');
            }
        });
    }

    function skipModalPayment() {
        // FIX_B_2382c: same latent stuck-modal class as B-2380b — getInstance() returns
        // null for declaratively-opened modals. Use getOrCreateInstance() so .hide() is reliable.
        var paymentModalEl = document.getElementById('paymentModal');
        if (paymentModalEl) {
            bootstrap.Modal.getOrCreateInstance(paymentModalEl).hide();
        }
        $('#Doctor_fee').val();
        // swal('', " Payment successfully", 'success');
        // setTimeout(function(){
        location.reload();
        // }, 2000);
    }

    function saveModalPayment() {
        const appointId = $('#modalAppointId').val();
        const orgId = $('#modalOrgId').val();
        const doctorFee = parseFloat($('#Doctor_fee').val()) || 0;
        const paymentMethod = $('#modal_amount_method').val();
        const txnNo = $('#modal_transactionNumber').val();
        const txnAmount = parseFloat($('#modal_transactionAmount').val()) || 0;
        const cashAmount = parseFloat($('#modal_cashAmount').val()) || 0;

        const concessionName = $('#modal_concessionName').val();
        const concessionType = $('#modal_concessionType').val();
        const concessionValue = $('#modal_concessionValue').val();
        const patient_id = $('#patientID').val();
        const createdBy = $('#createdBy').val();


        $.ajax({
            url: 'ajax/AppointmentBooking/Appointment_payment.php',
            type: 'POST',
            dataType: 'json',
            data: {
                patient_id: patient_id,
                appoint_id: appointId,
                createdBy: createdBy,
                org_id: orgId,
                doctor_fee: doctorFee,
                amount_method: paymentMethod,
                transaction_number: txnNo,
                transaction_amount: txnAmount,
                cash_amount: cashAmount,
                concession_name: concessionName,
                concession_type: concessionType,
                concession_value: concessionValue
            },
            success: function(res) {
                if (res.success) {
                    // FIX_B_2382c: getInstance() can return null — use getOrCreateInstance().
                    var paymentModalEl = document.getElementById('paymentModal');
                    if (paymentModalEl) {
                        bootstrap.Modal.getOrCreateInstance(paymentModalEl).hide();
                    }
                    swal('', "Payment successfully", 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    swal('Error: ' + res.message);
                }
            },
            error: function(xhr, status, error) {
                let msg = 'Could not save payment info.';
                if (error) {
                    msg += '\n\nError: ' + error;
                }
                if (xhr.responseText) {
                    msg += '\n\nDetails: ' + xhr.responseText;
                }

                swal('Error', msg, 'error');
            }

        });
    }

    function makePayment(appointId, orgId, patientId, doctorFee) {
        $('#modalAppointId').val(appointId);
        $('#modalOrgId').val(orgId);
        $('#patientID').val(patientId);
        $('#Doctor_fee').val(doctorFee);

        // Clear previous payment/concession inputs
        $('#modal_amount_method').val('');
        $('#modal_transactionNumber').val('');
        $('#modal_transactionAmount').val('');
        $('#modal_cashAmount').val('');
        $('#modal_txnDetails').hide();
        $('#modal_txnAmountDiv').hide();
        $('#modal_cashAmountDiv').hide();
        $('#modal_concessionName').val('').trigger('change');
        $('#modal_concessionTypeDiv').hide();
        $('#modal_concessionValueDiv').hide();
        $('#modal_concessionType').val('');
        $('#modal_concessionValue').val('');

        var paymentModalEl = document.getElementById('paymentModal');
        var paymentModal = bootstrap.Modal.getOrCreateInstance(paymentModalEl);
        paymentModal.show();
    }



    function editAppointment(
        organizations, amount, amount_method, bill_id, appoint_id, appointID, patient_name, appoint_unicode,
        mobile_number, gender, systolic, diastolic, temperature, glucose_level, age, patient_email, appoint_date,
        doctor_name, start_time, end_time, doctor_fee, bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
        weight, height, bmi, heart_rate, temperature2, grbs, spO2, patient_overview, respiration_rate,
        transaction_number, concession_name, concession_type, concession_value,
        referred_by, referral_hospital, referral_notes, referral_type, dob
    ) {
        window.scrollTo(0, 0);
        $("#organizations").val(organizations);
        $("#appoint_date").val(appoint_date).trigger('change');
        $("#amount_method").val(amount_method);
        $("#bill_id").val(bill_id);

        $("#appoint_id").val(appoint_id);
        $("#appointID").val(appointID);
        $("#patient_name").val(patient_name);
        $("#appoint_unicode").val(appoint_unicode);
        $("#patient_mobile").val(mobile_number);
        $("#male").prop("checked", gender === "Male");
        $("#female").prop("checked", gender === "Female");
        $("#other").prop("checked", gender === "Others");
        $("#systolic").val(systolic);
        $("#diastolic").val(diastolic);
        $("#temperature").val(temperature);
        $("#glucose_level").val(glucose_level);
        $("#age").val(age);
        $("#dob").val(dob || '');
        $("#patient_email").val(patient_email);
        $("#start_time").val(start_time);
        $("#end_time").val(end_time);
        $("#amount").val(amount);


        $("#bpSit_systolic").val(bpSit_systolic);
        $("#bpSit_diastolic").val(bpSit_diastolic);
        $("#bpStand_systolic").val(bpStand_systolic);
        $("#bpStand_diastolic").val(bpStand_diastolic);
        $("#weight").val(weight);
        $("#height").val(height);
        $("#bmi").val(bmi);
        $("#heart_rate").val(heart_rate);
        $("#grbs").val(grbs);
        $("#spO2").val(spO2);
        $("#patient_overview").val(patient_overview);
        $("#respiration_rate").val(respiration_rate);
        $("#referred_by").val(referred_by || '');
        $("#referral_hospital").val(referral_hospital || '');
        $("#referral_notes").val(referral_notes || '');
        $("#referral_type").val(referral_type || '');

        setTimeout(function() {
            $("#doctor_name").val(doctor_name);
            $("#doctor_name").prop("disabled", false);
            GetDoctorTime();

            $("#amount").val(amount);
            $("#doctor_fee").val(doctor_fee);
            enableConcession();
            // FIX_B_051: restore concession fields on edit
            $("#transactionNumber").val(transaction_number);
            $("#concessionName").val(concession_name);
            $('#concessionTypeDiv, #concessionValueDiv').show();
            $("#concessionType").val(concession_type);
            $("#concessionValue").val(concession_value);


            $("#saveData").prop("disabled", false);

            if (typeof checkFields === 'function') checkFields();
            if (typeof checkBookFields === 'function') checkBookFields();
        }, 500);

        $("#saveData").html("Update");
    }

    function deleteAppointment(appoint_id, patient_name) {
        swal({
            title: "Are you sure?",
            text: "Do you wish to \"" + patient_name + "\" Cancel Your Appointment !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/AppointmentBooking/AppointmentDelete.php',
                    type: 'POST',
                    data: {
                        'appoint_id': appoint_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            swal('', patient_name + ' Deleted Successfully', 'success');
                            GetAppointment();
                        } else {
                            swal('', 'Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                $('#deleteID').val(appoint_id);
                swal('', patient_name + ' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    $(function() {
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();

        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var minDate = year + '-' + month + '-' + day;

        $('#appoint_date').attr('min', minDate);
    });

    $(function() {
        $("#patient_name").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#patient_nameID").html("");
            var regex = /^[A-Za-z.\s]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#patient_nameID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#patient_name").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });
    $(function() {
        $("#patient_name").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, "");
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    $(function() {
        $("#mobile_number").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#mobile_numberID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#mobile_numberID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });

    function validateNumber(input) {
        var re = /^(\d{3})[- ]?(\d{3})[- ]?(\d{4})$/
        return re.test(input)
    }
    setInterval(function() {
        original = document.getElementById("patient_mobile").value;
        if (original.length > 10) {
            lastCharRemove =
                original.slice(0, original.length - 1);
            document.getElementById('patient_mobile').value = lastCharRemove;
        }
    }, 100);

    $(function() {
        $("#mobile_number").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, "");
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });
    $(function() {
        $("#amount").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#amountID").html("");
            var inputValue = String.fromCharCode(keyCode);

            if (inputValue === "n" || inputValue === "a" || inputValue === "/") {
                return true;
            }

            var regex = /^[0-9]+$/;
            var isValid = regex.test(inputValue);
            if (!isValid) {
                $("#amountID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });

    function validateNumber(input) {
        var re = /^(\d{3})[- ]?(\d{3})[- ]?(\d{4})$/;
        return re.test(input);
    }
    setInterval(function() {
        var original = document.getElementById("amount").value;

        if (original.length > 6) {
            var lastCharRemove = original.slice(0, original.length - 1);
            document.getElementById("amount").value = lastCharRemove;
        }
    }, 100);
    $(function() {
        $("#amount").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^NA0-9/]+/g, "");
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });
    $(function() {
        $("#appoint_unicode").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#appoint_unicodeID").html("");
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#appoint_unicodeID").html("Only Alphabets And Numbers Allowed.");
            }
            return isValid;
        });
    });

    $(function() {
        $("#doctor_fee").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#doctor_feeID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#doctor_feeID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#patient_email").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    $(function() {
        $("#bpSit_systolic").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#systolicID").html("");
            var regex = /^[0-9/ ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#systolicID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#bpSit_systolic").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });
    $(function() {
        $("#bpSit_systolic").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, "");
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });
    setInterval(function() {
        original = document.getElementById("bpSit_systolic").value;
        if (original.length > 3) {
            lastCharRemove =
                original.slice(0, original.length - 1);
            document.getElementById('bpSit_systolic').value = lastCharRemove;
        }
    }, 100);

    $(function() {
        $("#bpSit_diastolic").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#diastolicID").html("");
            var regex = /^[0-9/ ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#diastolicID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#bpSit_diastolic").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });
    $(function() {
        $("#bpSit_diastolic").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, "");
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });
    setInterval(function() {
        original = document.getElementById("bpSit_diastolic").value;
        if (original.length > 3) {
            lastCharRemove =
                original.slice(0, original.length - 1);
            document.getElementById('bpSit_diastolic').value = lastCharRemove;
        }
    }, 100);

    $(function() {
        $("#temperature").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#temperatureID").html("");
            var regex = /^[0-9/]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                // $("#temperatureID").html("Only Alphabets Allowed.");
                // swal('',"Only numbers and '&deg' allowed.",'warning');
            }
            return isValid;
        });
    });
    $(function() {
        $("#temperature").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    $(function() {
        $("#glucose_level").keypress(function(e) {

            var keyCode = e.keyCode || e.which;
            $("#glucose_levelID").html("");
            var regex = /^[0-9/]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {}
            return isValid;
        });
    });
    $(function() {
        $("#glucose_level").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    $(function() {
        $("#age").keypress(function(e) {
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
    $(function() {
        $("#age").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    function getPatientIDUsingDate() {
        var organizations = $('#organizations').val();

        $.ajax({
            url: "ajax/AppointmentBooking/getdateforpatientid.php",
            type: 'get',
            data: {
                org_id: organizations
            },
            dataType: 'json',
            success: function(data) {
                $("#appointID").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getPatientUniqueID() {
        var organizations = $('#organizations').val();

        $.ajax({
            url: "ajax/AppointmentBooking/GetPatientUnicodeID.php",
            type: 'get',
            data: {
                org_id: organizations
            },
            dataType: 'json',
            success: function(data) {
                $("#appoint_unicode").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function GetAppointmentExisting() {
        $.ajax({
            url: 'ajax/AppointmentBooking/getAllData2.php',
            type: 'GET',
            success: function(data) {
                var org_id = '<?= $SessionOrgId  ?>';
                if (data) {
                    $("#showMenusDataExisting").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                    }
                    $("#tableExport2").dataTable({
                        retrieve: true,
                        dom: 'lBrftip',
                        buttons: [{
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
            error: function(err) {
                console.log(err);
            }
        });
    }

    $(function() {
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var minDate = year + '-' + month + '-' + day;

        $('#existing_appoint_date').attr('min', minDate);
    });

    function GetDoctorNameExisting() {
        $('#existing_start_time').val('');
        $('#existing_end_time').val('');
        var existing_appoint_date = $('#existing_appoint_date').val();
        var org_id = $('#E_organizations').val();
        $.ajax({
            url: "ajax/AppointmentBooking/getdoctors.php",
            type: 'POST',
            data: {
                existing_appoint_date: existing_appoint_date,
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                var optionData = '<option value=""> Select Doctor Name</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.doctorName_registrationNumber + '"> ' + val.doctor_name + ' </option>';
                });
                $("#existing_doctor_name").html(optionData);
                GetDoctorTimeExisting();
                GetExistingDoctorFee();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // Existing number
    function GetMobileNumberExisting() {
        // alert(1);
        $.ajax({
            url: 'ajax/AppointmentBooking/getPatientNumbers.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '<option value=""> Select Doctor Name</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.mobile_number + '"> ' + val.mobile_number + ' </option>';
                });
                //   alert();
                $("#existing_mobile_number").val(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
    // time slots book or not
    var color1 = 'rgba(253, 92, 92, 0.8';
    var color2 = 'rgba(51, 208, 161, 0.8)';
    var selectedDiv = null;

    function toggleBackgroundColor1(element) {
        // alert(1)
        var currentColor = element.style.backgroundColor;
        if (currentColor === color1) {
            element.style.backgroundColor = color1;
        } else {
            element.style.backgroundColor = color2;
        }
    }

    function handleDivClick1(element) {
        // alert(2)
        if (selectedDiv === element) {
            element.style.backgroundColor = color2;
            element.style.backgroundColor = color2;
            selectedDiv = null;
        } else {
            if (selectedDiv !== null) {
                selectedDiv.style.backgroundColor = color2;
            }
            element.style.backgroundColor = color1;
            selectedDiv = element;
        }
    }

    function move1(start_time, end_time, res_available_date, count) {

        $('#existing_start_time').val('');
        $('#existing_end_time').val('');

        var doctor_name = $("#existing_doctor_name").val();
        var appoint_unicode = $("#existing_appoint_unicode").val();
        $.ajax({
            url: "ajax/AppointmentBooking/checkPatientExist.php",
            type: 'post',
            data: {
                doctor_name: doctor_name,
                appoint_unicode: appoint_unicode,
                appoint_date: res_available_date
            },
            success: function(data) {
                console.log(data);
                if (data) {
                    swal("warning", "You have already book a slot at " + data, "warning");
                    return;
                }
                $('#existing_start_time').val(start_time);
                $('#existing_end_time').val(end_time);
                $('#existing_appoint_date').val(res_available_date);
                var element = document.getElementById('myDIV' + count);
                toggleBackgroundColor1(element);
                handleDivClick1(element);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // Existing Patient div times
    function GetDoctorTimeExisting() {
        var timeId = $("#existing_doctor_name").val();
        var appoint_date = $("#existing_appoint_date").val();
        var organizations = $("#E_organizations").val();

        var item = {};
        item['doctorName_registrationNumber'] = timeId;
        item['available_date'] = appoint_date;
        item['organizations'] = organizations;
        $.ajax({
            url: "ajax/AppointmentBooking/appointmentTimeSlot.php",
            type: 'post',
            data: item,
            success: function(data) {
                // console.log(data);
                $("#Existing_showTimeSlot").html(data);
                // GetAppointment();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // Existing Patient Doctors Fee
    function GetExistingDoctorFee() {
        var doctors_time_id = $("#existing_doctor_name").val();
        var mobile = $("#existing_patient_name option:selected").text();
        $.ajax({
            url: "ajax/AppointmentBooking/AppointmentDoctorFee.php",
            type: 'POST',
            data: {
                doctors_time_id: doctors_time_id,
                mobile: mobile
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $("#payment").val(data);
            },

            error: function(err) {
                console.log(err);
            }
        });
    }

    function Two(doctor_name) {
        var doctor_name = $("#existing_doctor_name").val();
        $.ajax({
            url: 'ajax/AppointmentBooking/GetIDByName.php',
            type: 'POST',
            data: {
                doctor_name: doctor_name
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var optionData = '';
                $.each(data, function(key, val) {
                    optionData += ' ' + val.doctorName_registrationNumber + '';
                });
                $("#ExistingData7").html(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getExistingBillIDUsingDate() {
        $.ajax({
            url: "ajax/AppointmentBooking/AutoGenareteBill_Ids.php",
            type: 'get',
            dataType: 'json',
            success: function(data) {
                $("#Existing_bill_id").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // Existing Patient Conformation
    function myExistingConformation() {
        var appointDate = $("#appointDate").val();
        var atmt_id = $("#atmt_id").val();
        var AppointIDs = $("#AppointIDs").val();
        var patient_name = $("#existing_mobile_number option:selected").text();
        var appoint_unicode = $("#existing_appoint_unicode").val();
        var appointID = $("#ExistingAppointID").val();
        var gender = $("input[name='gender']:checked").val();
        var temperature = $("#existing_temperature").val();
        var age = $("#existing_age").val();
        var mobile_number = $("#existing_patient_name option:selected").text();
        var patient_email = $("#existing_patient_email").val();
        var appoint_date = $("#existing_appoint_date").val();
        var doctor_name = $("#existing_doctor_name").val();
        var start_time = $("#existing_start_time").val();
        var end_time = $("#existing_end_time").val();
        var organizations = $("#E_organizations").val();
        var payment_method = $("#payment_method").val();
        var payment = $("#payment").val();
        var bill_id = $("#Existing_bill_id").val();

        var bpSit_systolic = $("#existing_bpSit_systolic").val();
        var bpSit_diastolic = $("#existing_bpSit_diastolic").val();
        var bpStand_systolic = $("#existing_bpStand_systolic").val();
        var bpStand_diastolic = $("#existing_bpStand_diastolic").val();
        var weight = $("#existing_weight").val();
        var height = $("#existing_height").val();
        var bmi = $("#existing_bmi").val();
        var grbs = $("#existing_grbs").val();
        var heart_rate = $("#existing_heart_rate").val();
        var respiration_rate = $("#existing_respiration_rate").val();
        var spO2 = $("#existing_spO2").val();
        var patient_overview = $("#existing_patient_overview").val();
        $("#ExistingData2").html(patient_name);
        $("#ExistingData3").html(appoint_unicode);
        $("#ExistingData15").html(appointID);
        $("#ExistingData10").html(gender);
        $("#ExistingData11").html(bpSit_systolic);
        $("#ExistingData14").html(bpSit_diastolic);
        $("#ExistingData12").html(temperature);
        $("#ExistingData13").html(grbs);
        $("#ExistingData1").html(age);
        $("#ExistingData4").html(mobile_number);
        $("#ExistingData5").html(patient_email);
        $("#ExistingData6").html(appoint_date);
        $("#ExistingData8").html(start_time);
        $("#ExistingData9").html(end_time);
        $("#organizations").html(organizations);
        $("#payment_method_view").html(payment_method);
        $("#payment_view").html(payment);
        $("#ExistingBillId").html(bill_id);

        $("#appointDate2").val(appointDate);
        $("#atmt_id").val(atmt_id);
        $("#AppointIDs").val(AppointIDs);
        $("#patient_name2").val(patient_name);
        $("#appoint_unicode2").val(appoint_unicode);
        $("#appointID2").val(appointID);
        $("#gender2").val(gender);
        $("#temperature2").val(temperature);
        $("#age2").val(age);
        $("#mobile_number2").val(mobile_number);
        $("#patient_email2").val(patient_email);
        $("#appoint_date2").val(appoint_date);
        $("#doctor_name2").val(doctor_name);
        $("#start_time2").val(start_time);
        $("#end_time2").val(end_time);
        $("#E_organizations2").val(organizations);
        $("#payment_method2").val(payment_method);
        $("#payment2").val(payment);
        $("#billId2").val(bill_id);
        $("#bpSit_systolic2").val(bpSit_systolic);
        $("#bpSit_diastolic2").val(bpSit_diastolic);
        $("#bpStand_systolic2").val(bpStand_systolic);
        $("#bpStand_diastolic2").val(bpStand_diastolic);
        $("#weight2").val(weight);
        $("#height2").val(height);
        $("#bmi2").val(bmi);
        $("#grbs2").val(grbs);
        $("#heart_rate2").val(heart_rate);
        $("#respiration_rate2").val(respiration_rate);
        $("#spO22").val(spO2);
        $("#patient_overview2").val(patient_overview);

    }

    $("#MyFormId").submit(function() {
        event.preventDefault();

        document.getElementById('exampleModal').removeAttribute('style');

        var appointDate = $("#appointDate2").val();
        var atmt_id = $("#atmt_id").val();
        var AppointIDs = $("#AppointIDs").val();
        var patient_name = $("#patient_name2").val().trim();
        var appoint_unicode = $("#appoint_unicode2").val();
        var appointID = $("#appointID2").val();
        var gender = $("#gender2").val();
        var temperature = $("#temperature2").val().trim();
        var age = $("#age2").val();
        var mobile_number = $("#mobile_number2").val();
        var patient_email = $("#patient_email2").val().trim();
        var appoint_date = $("#appoint_date2").val();
        var doctor_name = $("#doctor_name2").val();
        var start_time = $("#start_time2").val();
        var end_time = $("#end_time2").val();
        var organizations = $("#E_organizations2").val();
        var payment_method = $("#payment_method2").val();
        var payment = $("#payment2").val();
        var bill_id = $("#billId2").val();

        var bpSit_systolic = $("#bpSit_systolic2").val();
        var bpSit_diastolic = $("#bpSit_diastolic2").val();
        var bpStand_systolic = $("#bpStand_systolic2").val();
        var bpStand_diastolic = $("#bpStand_diastolic2").val();
        var weight = $("#weight2").val();
        var height = $("#height2").val();
        var bmi = $("#bmi2").val();
        var grbs = $("#grbs2").val();
        var heart_rate = $("#heart_rate2").val();
        var respiration_rate = $("#respiration_rate2").val();
        var spO2 = $("#spO22").val();
        var patient_overview = $("#patient_overview2").val();



        if (!patient_name && !mobile_number && !age && !gender && !appoint_date && !doctor_name && !amount) {
            swal('', 'All fields Required', 'warning');
            return;
        }

        if (!patient_name) {
            swal('', 'Please Enter Patient Name', 'warning');
            return;
        }

        if (!$('#mobile_number2').val().match('[0-9]{10}')) {
            swal('', 'Please Enter valid number', 'warning');
            return;
        }

        if (!age) {
            swal('', 'Please Enter Patient Age', 'warning');
            return;
        }

        if (!gender) {
            swal('', 'Please Select Gender', 'warning');
            return;
        }

        if (patient_email !== '') {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test($('#patient_email2').val())) {
                swal('', 'Please Enter valid Email', 'warning');
                return;
            }
        }

        if (!appoint_date) {
            swal('', 'Please Select Date', 'warning');
            return;
        }

        if (!doctor_name) {
            swal('', 'Please Select Doctor Name', 'warning');
            return;
        }


        if (!payment) {
            swal('', 'Please Enter Doctor Fee', 'warning');
            return;
        }


        if (!start_time) {
            swal('', 'Please Select Time Slot', 'warning');
            return;
        }


        var subitem = {
            appointDate2: appointDate,
            atmt_id: atmt_id,
            AppointIDs: AppointIDs,
            patient_name2: patient_name,
            appoint_unicode2: appoint_unicode,
            appointID2: appointID,
            gender2: gender,
            temperature2: temperature,
            age2: age,
            mobile_number2: mobile_number,
            patient_email2: patient_email,
            appoint_date2: appoint_date,
            doctor_name2: doctor_name,
            start_time2: start_time,
            end_time2: end_time,
            E_organizations2: organizations,
            payment_method2: payment_method,
            payment2: payment,
            billId2: bill_id,
            bpSit_systolic2: bpSit_systolic,
            bpSit_diastolic2: bpSit_diastolic,
            bpStand_systolic2: bpStand_systolic,
            bpStand_diastolic2: bpStand_diastolic,
            weight2: weight,
            height2: height,
            bmi2: bmi,
            grbs2: grbs,
            heart_rate2: heart_rate,
            respiration_rate2: respiration_rate,
            spO22: spO2,
            patient_overview2: patient_overview,
            dob2: $("#existing_dob").val()
        };
        $.ajax({
            url: 'ajax/AppointmentBooking/AddModifyExistingAppoint_online.php',
            type: 'POST',
            data: subitem,
            success: function(data) {
                console.log(data);
                if (data == 1) {
                    swal({
                        title: 'success',
                        text: "" + patient_name + ' Your Appointment Booking Successfully',
                        icon: 'success',
                        buttons: {
                            ok: {
                                text: 'OK',
                                value: true,
                                visible: true,
                                className: 'btn btn-primary'
                            }
                        },
                        allowOutsideClick: false
                    }).then(function() {
                        $('#exampleModal').html('');
                        $("#Myform1")[0].reset();
                        GetAppointmentExisting();
                        location.reload();
                    });
                } else if (data == 2) {
                    swal({
                        title: 'success',
                        text: ' Record Updated Successfully',
                        icon: 'success',
                        buttons: {
                            ok: {
                                text: 'OK',
                                value: true,
                                visible: true,
                                className: 'btn btn-primary'
                            }
                        },
                        allowOutsideClick: false
                    }).then(function() {
                        $('#exampleModal').html('');
                        $("#Myform1")[0].reset();
                        GetAppointmentExisting();
                        location.reload();
                    });
                } else if (data == 3) {
                    swal('', "  Already Booked", 'warning');
                } else if (data == 0) {
                    swal('', 'All fields Required', 'warning');
                    GetAppointmentExisting();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    function editAppointmentExisting(
        atmt_id, AppointIDs, bill_id, appointID, organizations, payment_method,
        patient_name, mobile_number, appoint_unicode, gender, systolic, diastolic,
        temperature, glucose_level, age, patient_email, appoint_date, doctor_name,
        payment, start_time, end_time,
        bpSit_systolic, bpSit_diastolic, bpStand_systolic, bpStand_diastolic,
        weight, height, bmi, grbs, heart_rate, respiration_rate, spO2, patient_overview
    ) {
        $("#atmt_id").val(atmt_id);
        $("#AppointIDs").val(AppointIDs);
        $("#Existing_bill_id").val(bill_id);
        $("#ExistingAppointID").val(appointID);
        $("#E_organizations").val(organizations);
        $("#existing_appoint_date").val(appoint_date).trigger('change');
        $("#payment_method").val(payment_method);
        $("#existing_patient_name").val(patient_name).trigger('change');
        $("#existing_mobile_number").val(mobile_number).trigger('change');
        $("#existing_appoint_unicode").val(appoint_unicode);


        $("#male").prop("checked", gender === "Male");
        $("#female").prop("checked", gender === "Female");
        $("#others").prop("checked", gender === "Others");

        // $("#existing_systolic").val(systolic);
        // $("#existing_diastolic").val(diastolic);
        $("#existing_temperature").val(temperature);
        // $("#existing_glucose_level").val(glucose_level);
        $("#existing_age").val(age);
        $("#existing_patient_email").val(patient_email);
        $("#payment").val(payment);
        $("#existing_start_time").val(start_time);
        $("#existing_end_time").val(end_time);

        $("#existing_bpSit_systolic").val(bpSit_systolic);
        $("#existing_bpSit_diastolic").val(bpSit_diastolic);
        $("#existing_bpStand_systolic").val(bpStand_systolic);
        $("#existing_bpStand_diastolic").val(bpStand_diastolic);
        $("#existing_weight").val(weight);
        $("#existing_height").val(height);
        $("#existing_bmi").val(bmi);
        $("#existing_grbs").val(grbs);
        $("#existing_heart_rate").val(heart_rate);
        $("#existing_respiration_rate").val(respiration_rate);
        $("#existing_spO2").val(spO2);
        $("#existing_patient_overview").val(patient_overview);

        setTimeout(function() {
            $("#existing_doctor_name").val(doctor_name);
            GetDoctorTimeExisting();
        }, 200);

        $("#saveDataExisting").html("update");
    }

    function deleteAppointmentExisting(atmt_id, patient_name) {
        swal({
            title: "Are you sure?",
            text: "Do you wish to \"" + patient_name + "\" Cancel Your Appointment !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/AppointmentBooking/AppointDeleteExisting.php',
                    type: 'POST',
                    data: {
                        'atmt_id': atmt_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            swal('', patient_name + ' Deleted Successfully', 'success');
                            GetAppointmentExisting();
                        } else {
                            swal('', 'Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                $('#deleteID1').val(atmt_id);
                // FIX_B_052: submit the form that owns #deleteID1 (#deleteFormId1)
                swal('', patient_name + ' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId1').submit();
                });
            }
        });
    }
    $(function() {
        $("#patient_email2").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });
    $(function() {
        $("#existing_patient_name").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_patient_nameID").html("");
            var regex = /^[A-Za-z ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#existing_patient_nameID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });

    $(function() {
        $("#existing_patient_name").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    $(function() {
        $("#existing_patient_name").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    // only Patient Id 
    $(function() {
        $("#existing_appoint_unicode").keypress(function(e) {

            var keyCode = e.keyCode || e.which;
            $("#existing_appoint_unicodeID").html("");
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#existing_appoint_unicodeID").html("Only Alphabets And Numbers Allowed.");
            }
            return isValid;
        });
    });

    // only Doctor Fee
    $(function() {
        $("#existing_doctor_fee").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_doctor_feeID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#existing_doctor_feeID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });

    // Get Names Based on Organization selection
    function Getorgpatientnames() {
        var organizationselected = $("#E_organizations").val();

        var patientNameSelect = $("#existing_patient_name");
        var patientMobileSelect = $("#existing_mobile_number");
        var appointUnicodeSelect = $("#existing_appoint_unicode");
        var genderSelect = $("input[name='gender']");
        var AppointIDSelect = $("#ExistingAppointID");
        var IDsSelect = $("#AppointIDs");
        // var SystolicSelect = $("#existing_systolic");
        // var DiastolicSelect = $("#existing_diastolic");
        var TemperatureSelect = $("#existing_temperature");
        // var GlucoseLevelSelect = $("#existing_glucose_level");
        var AgeSelect = $("#existing_age");
        var EmailSelect = $("#existing_patient_email");
        var PaymentMethodSelect = $("#payment_method");
        var organizationsSelect = $("#E_organizations");
        var appointDateSelect = $("#existing_appoint_date");
        var doctorNameSelect = $("#existing_doctor_name");
        var paymentAmountSelect = $("#payment");
        var existingStartTimeSelect = $("#existing_start_time");
        var existingEndTimeSelect = $("#existing_end_time");

        // Get today's date
        var today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD

        // Clear values
        patientNameSelect.empty();
        patientMobileSelect.empty();
        appointUnicodeSelect.val('');
        AppointIDSelect.val('');
        IDsSelect.val('');
        // SystolicSelect.val('');
        // DiastolicSelect.val('');
        TemperatureSelect.val('');
        // GlucoseLevelSelect.val('');
        AgeSelect.val('');
        EmailSelect.val('');
        appointDateSelect.val(today); // Set today's date
        doctorNameSelect.empty();
        paymentAmountSelect.val('');
        existingStartTimeSelect.val('');
        existingEndTimeSelect.val('');

        // Reset dropdowns
        patientNameSelect.append('<option value="" selected>Select Name</option>');
        patientMobileSelect.append('<option value="" selected>Select Mobile Number</option>');
        doctorNameSelect.append('<option value="" selected>Select Doctor</option>');
        PaymentMethodSelect.append('<option value="" selected>Select Payment Method</option>');


        $.ajax({
            url: 'ajax/AppointmentBooking/AppointmentPatientNames.php',
            type: 'POST',
            data: {
                orgid: organizationselected
            },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $.each(data, function(_, val) {
                        patientNameSelect.append($('<option>', {
                            text: val.patient_name
                        }).attr('data-custom-value', val.appoint_id));
                        patientMobileSelect.append($('<option>', {
                            value: val.mobile_number,
                            text: val.mobile_number
                        }));
                    });

                } else {
                    console.log("No patients found for the selected organization.");
                }

            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // 
    function GetName() {
        var selectElement = document.getElementById("existing_patient_name");
        var patId = $("#existing_patient_name").val();
        $("#existing_mobile_number").val(patId).trigger('change');
        var customValue = patId;
        var appointID = "";
        if (!customValue) {
            console.log("Patient name is required.");
            return;
        }
        $.ajax({
            url: "ajax/AppointmentBooking/getdateforpatientid.php",
            type: 'get',
            dataType: 'json',
            success: function(data) {
                appointId = data;


                $.ajax({
                    url: 'ajax/AppointmentBooking/AppointGetAutoData.php',
                    type: 'POST',
                    data: {
                        customValue: customValue,
                        appointId: appointId
                    },
                    dataType: 'json',
                    success: function(data) {
                        var mobileNumberSelect = $("#existing_mobile_number");
                        var appointUnicodeSelect = $("#existing_appoint_unicode");
                        var genderSelect = $("input[name='gender']");
                        var AppointIDSelect = $("#ExistingAppointID");
                        var IDsSelect = $("#AppointIDs");
                        var TemperatureSelect = $("#existing_temperature");
                        var AgeSelect = $("#existing_age");
                        var EmailSelect = $("#existing_patient_email");

                        var PaymentMethodSelect = $("#payment_method");

                        var organizationsSelect = $("#E_organizations");

                        var appoint_dateSelect = $("#appointDate");
                        appointUnicodeSelect.empty();
                        AppointIDSelect.empty();
                        IDsSelect.empty();
                        TemperatureSelect.empty();
                        AgeSelect.empty();
                        EmailSelect.empty();
                        appoint_dateSelect.empty();
                        if (data.length > 0) {
                            appointUnicodeSelect.val(data[0].appoint_unicode);
                            AppointIDSelect.val(data[0].appoint_register_id);
                            IDsSelect.val(data[0].appoint_id);
                            TemperatureSelect.val(data[0].temperature);
                            AgeSelect.val(data[0].patient_age);
                            if (data[0].patient_dob) { $('#existing_dob').val(data[0].patient_dob); }
                            EmailSelect.val(data[0].patient_email);
                            PaymentMethodSelect.val(data[0].payment_type);
                            appoint_dateSelect.val(data[0].appoint_date);

                            $('#existing_bpSit_systolic').val(data[0].bpSit_systolic);
                            $('#existing_bpSit_diastolic').val(data[0].bpSit_diastolic);

                            $('#existing_bpStand_systolic').val(data[0].bpStand_systolic);
                            $('#existing_bpStand_diastolic').val(data[0].bpStand_diastolic);

                            $('#existing_weight').val(data[0].weight);
                            $('#existing_height').val(data[0].height);
                            $('#existing_bmi').val(data[0].bmi);

                            $('#existing_grbs').val(data[0].grbs);
                            $('#existing_heart_rate').val(data[0].heart_rate);
                            $('#existing_respiration_rate').val(data[0].respiration_rate);
                            $('#existing_spO2').val(data[0].spO2);
                            $('#existing_patient_overview').val(data[0].patient_overview);
                            // Update the gender radio buttons based on data
                            genderSelect.filter('[value="' + data[0].gender + '"]').prop('checked', true);
                        } else {
                            // Clear the gender radio buttons if no data is available
                            genderSelect.prop('checked', false);
                            $('#existing_bpSit_systolic, #existing_bpSit_diastolic, #existing_bpStand_systolic, #existing_bpStand_diastolic, #existing_weight, #existing_height, #existing_bmi, #existing_grbs, #existing_heart_rate, #existing_temperature, #existing_respiration_rate, #existing_spO2, #existing_patient_overview').val('');

                        }
                        organizationsSelect.val();

                        if (organizationsSelect == "") {
                            // EGetOrgByDoctor();
                        } else {
                            EGetOrgByDoctor();
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // existing_blood_pressure
    $(function() {
        $("#existing_bpSit_systolic").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_blood_pressureID").html("");
            var regex = /^[A-Za-z0-9/ ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#existing_blood_pressureID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#existing_bpSit_systolic").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    // existing_temperature
    $(function() {
        $("#existing_temperature").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_temperatureID").html("");
            var regex = /^[0-9/]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {}
            return isValid;
        });
    });
    $(function() {
        $("#existing_temperature").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });


    // existing_glucose_level
    $(function() {
        $("#existing_grbs").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_glucose_levelID").html("");
            var regex = /^[0-9/]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {}
            return isValid;
        });
    });
    $(function() {
        $("#existing_grbs").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }

        });
    });

    // Amont payment

    $(function() {
        $("#payment").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#payment").html("");
            var regex = /^[0-9/]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {}
            return isValid;
        });
    })

    // existing_Patient Age 
    $(function() {
        $("#existing_age").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#existing_ageID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#existing_ageID").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#existing_age").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });



    // new patient Organization

    function GetOrgByIds() {
        var org_id = $('#organizations').val();
        $.ajax({
            url: 'ajax/AppointmentBooking/GetOrgByIdsData.php',
            type: 'POST',
            data: {
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                var AppointmentIdSelect = $("#appointID");
                var appoint_unicodeSelect = $("#appoint_unicode");
                var doctor_nameSelect = $("#appoint_unicode");
                AppointmentIdSelect.empty();
                appoint_unicodeSelect.empty();
                if (data.length > 0) {
                    AppointmentIdSelect.val(data[0].Latest_Appoint_Register_Id);
                    appoint_unicodeSelect.val(data[0].Appoint_Unicode);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    }

    function GetOrgByDoctor() {
        var org_id = $('#organizations').val();
        var appoint_date = $('#appoint_date').val();
        $.ajax({
            url: 'ajax/AppointmentBooking/GetOrgByIdsDoctors.php',
            type: 'POST',
            data: {
                org_id: org_id,
                appoint_date: appoint_date
            },
            dataType: 'json',
            success: function(data) {
                var optionData = '<option value=""> Select Doctor Name </option>';
                $.each(data, function(key, val) {
                    optionData += ' <option value=' + val.doctorName_registrationNumber + '>' + val.doctor_name + '</option>';
                });

                $("#doctor_name").html(optionData);
                GetDoctorTime();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }


    function EGetOrgByDoctor() {
        var org_id = $('#E_organizations').val();
        var existing_appoint_date = $('#existing_appoint_date').val();
        if (!org_id && '<?= $SessionUserId ?>' != '1') {
            org_id = '<?= $SessionOrgId ?>';
        }
        $.ajax({
            url: 'ajax/AppointmentBooking/GetOrgIdsByEDoctors.php',
            type: 'POST',
            data: {
                org_id: org_id,
                existing_appoint_date: existing_appoint_date
            },
            dataType: 'json',
            success: function(data) {
                var optionData = '<option value=""> Select Doctor Name </option>';
                $.each(data, function(key, val) {
                    optionData += ' <option value=' + val.doctorName_registrationNumber + '>' + val.doctor_name + '</option>';
                });

                $("#existing_doctor_name").html(optionData);
                GetDoctorTimeExisting();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    const patientName = document.getElementById('patient_name');
    const patientMobile = document.getElementById('patient_mobile');
    const ageInput = document.getElementById('age');
    const genderRadios = document.querySelectorAll('input[name="gender"]');
    const doctorSelect = document.getElementById('doctor_name');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const saveButton = document.getElementById('saveData');

    function checkFields() {
        const nameFilled = patientName.value.trim() !== '';
        const mobileFilled = patientMobile.value.trim() !== '';
        const ageFilled = ageInput.value.trim() !== '';
        const genderChecked = !!document.querySelector('input[name="gender"]:checked');

        doctorSelect.disabled = !(nameFilled && mobileFilled && ageFilled && genderChecked);

        checkBookFields();
    }

    function checkBookFields() {
        const doctorChosen = doctorSelect.value.trim() !== '';
        const startFilled = startTimeInput.value.trim() !== '';
        const endFilled = endTimeInput.value.trim() !== '';

        saveButton.disabled = !(doctorChosen && startFilled && endFilled);
    }


    patientName.addEventListener('input', checkFields);
    patientMobile.addEventListener('input', checkFields);
    ageInput.addEventListener('input', checkFields);
    genderRadios.forEach(r => r.addEventListener('change', checkFields));

    doctorSelect.addEventListener('change', checkBookFields);
    startTimeInput.addEventListener('change', checkBookFields);
    endTimeInput.addEventListener('change', checkBookFields);
</script>
