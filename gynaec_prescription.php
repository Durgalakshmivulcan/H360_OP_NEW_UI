<?php
require_once("ajax/header.php"); requireSpecializationFor(basename(__FILE__));
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
$currentDate   = date('Y-m-d');
// FIX_B_1903: doctor-scope filter for all today's-appointment dropdowns on this page
$docScope_B1903 = currentDoctorScopeSql('doctor_name');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { height:38px; border:1px solid #ced4da; border-radius:0.375rem; }
    .btn-group, .btn-group-vertical { position:relative; display:-ms-inline-flexbox; display:inline-flex; vertical-align:middle;}
    .Pule { font-weight:600; font-size:12px; line-height:26px; padding:0.3rem 0.8rem; letter-spacing:.5px; margin-top:21px; margin-right:18px; }
    .add_row { margin-right:17px; margin-top:16px; }
    .med, .invest { display:none; }
    .bpicon { position:relative; top:4px; font-size:21px; }
    /* ── Template dropdown ── */
    .grx-tpl-dropdown { position:absolute; border:1px solid #ccc; background:#fff; z-index:1050;
        max-height:220px; overflow-y:auto; width:300px; padding:5px;
        box-shadow:0 2px 6px rgba(0,0,0,.2); display:none; }
    .grx-tpl-item { padding:6px 8px; cursor:pointer; border-bottom:1px solid #eee; }
    .grx-tpl-item:hover { background:#f0f8ff; }
</style>

<div class="main-content">
<section class="section">
    <ul class="breadcrumb breadcrumb-style">
        <li class="breadcrumb-item"><h4 class="page-title m-b-0">Gynaec Prescription</h4></li>
        <li class="breadcrumb-item">
            <a href="dashboard.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </a>
        </li>
        <li class="breadcrumb-item">Doctor Portal</li>
        <li class="breadcrumb-item">Gynaec Prescription</li>
    </ul>

    <!-- Prescription Form -->
    <div class="col-12 col-md-12 col-lg-12 mt-2" id="grxFormCard">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gynaecological Prescription</h4>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Diagnosis template dropdown — copy any gynaec diagnosis template -->
                    <div class="btn-group d-none" id="grxDxDropdownGroup">
                        <button type="button" class="btn btn-outline-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:12px;background-color:#0d6efdde;color:white;">
                            <i class="fa fa-copy me-1"></i> Copy from Diagnosis
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" id="grxDxDropdownMenu" style="min-width:320px;max-height:360px;overflow-y:auto;padding:4px;">
                            <li><div class="px-2 pb-1"><input type="text" id="grxDxSearch" class="form-control form-control-sm" placeholder="Search diagnosis..." style="font-size:12px;"></div></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <div id="grxDxItems"></div>
                        </ul>
                    </div>
                    <span id="grxPaymentStatusBadge" style="display:none; font-size:13px; font-weight:700; padding:5px 14px; border-radius:20px;"></span>
                    <button id="grxViewBtn" class="btn btn-primary btn-sm d-none" style="border-radius:5%;">View</button>
                </div>
            </div>

            <div class="card-body">
                <input type="hidden" id="grx_rx_id">
                <input type="hidden" id="grx_appoint_id_val">
                <input type="hidden" id="grx_patient_id_val">

                <!-- ── Patient Info ── -->
                <div class="row">
                    <?php if ($SessionUserId=="1" && $SessionRoleId=="1"): ?>
                    <div class="row mb-lg-3">
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Organization</label>
                            <select class="form-control form-select" id="grx_org" name="organizations" onchange="getMedichines('');">
                                <option value="">Select Organization</option>
                                <?php
                                $orgQ2 = mysqli_query($conn,"SELECT org_id,organization_name FROM organization WHERE status='1' ORDER BY org_id ASC");
                                while($o=mysqli_fetch_object($orgQ2)) echo "<option value='{$o->org_id}'>{$o->organization_name}</option>";
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" id="grx_org" value="<?= $SessionOrgId ?>">
                    <?php endif; ?>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Name <span class="text-danger">*</span></label>
                            <select class="form-control" id="grx_patientIdName" name="grx_patientIdName" onchange="grxGetNumberAndIdByName();">
                                <option value="">Select Name</option>
                                <?php
                                if ($SessionUserId == "1") {
                                    $qN = mysqli_query($conn, "SELECT DISTINCT patient_name FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' $docScope_B1903 ORDER BY patient_name ASC");
                                } else {
                                    $qN = mysqli_query($conn, "SELECT DISTINCT patient_name FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId' $docScope_B1903 ORDER BY patient_name ASC");
                                }
                                while($rN = mysqli_fetch_assoc($qN)) echo "<option value='".htmlspecialchars($rN['patient_name'])."'>".htmlspecialchars($rN['patient_name'])."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Mobile</label>
                            <select class="form-control" id="grx_mobile_number" name="grx_mobile_number" onchange="grxGetNameByNumber();">
                                <option value="">Select Mobile</option>
                                <?php
                                if ($SessionUserId == "1") {
                                    $qM = mysqli_query($conn, "SELECT DISTINCT mobile_number FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' $docScope_B1903 ORDER BY appoint_id DESC");
                                } else {
                                    $qM = mysqli_query($conn, "SELECT DISTINCT mobile_number FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId' $docScope_B1903 ORDER BY appoint_id DESC");
                                }
                                while($rM = mysqli_fetch_assoc($qM)) echo "<option value='".htmlspecialchars($rM['mobile_number'])."'>".htmlspecialchars($rM['mobile_number'])."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Patient ID</label>
                            <select class="form-control" id="grx_patientId" name="grx_patientId" onchange="grxGetByPatientId();">
                                <option value="">Select ID</option>
                                <?php
                                if ($SessionUserId == "1") {
                                    $qI = mysqli_query($conn, "SELECT DISTINCT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' $docScope_B1903");
                                } else {
                                    $qI = mysqli_query($conn, "SELECT DISTINCT appoint_unicode FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId' $docScope_B1903");
                                }
                                while($rI = mysqli_fetch_assoc($qI)) echo "<option value='".htmlspecialchars($rI['appoint_unicode'])."'>".htmlspecialchars($rI['appoint_unicode'])."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Appointment ID</label>
                            <select class="form-control" id="grx_appointId" name="grx_appointId" onchange="grxGetByAppointId();">
                                <option value="">Select Appointment ID</option>
                                <?php
                                if ($SessionUserId == "1") {
                                    $qA = mysqli_query($conn, "SELECT appoint_register_id, appoint_unicode, appoint_id FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' $docScope_B1903 ORDER BY appoint_id DESC");
                                } else {
                                    $qA = mysqli_query($conn, "SELECT appoint_register_id, appoint_unicode, appoint_id FROM appointment_online WHERE appoint_status='1' AND appoint_date='$currentDate' AND org_id='$SessionOrgId' $docScope_B1903 ORDER BY appoint_id DESC");
                                }
                                while($rA = mysqli_fetch_assoc($qA)) echo "<option value='".htmlspecialchars($rA['appoint_register_id'])."' data-unicode='".htmlspecialchars($rA['appoint_unicode'])."' data-id='".htmlspecialchars($rA['appoint_id'])."'>".htmlspecialchars($rA['appoint_register_id'])."</option>";
                                ?>
                            </select>
                        </div>
                    <!-- <div class="row mb-lg-4 mb-sm-3"> -->
                       
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" id="grx_dob">
                        </div>
                         <div class="form-group col-lg-3 col-sm-12">
                            <label>Age</label>
                            <input type="text" class="form-control" id="grx_age" placeholder="Age">
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Gender</label>
                            <div class="selectgroup w-100">
                                <label>
                                    <input type="radio" name="grx_gender" value="Male" class="selectgroup-input-radio">
                                    <span class="selectgroup-button px-2 py-1"><i class="bi bi-gender-male"></i>&nbsp;Male</span>
                                </label>
                                <label>
                                    <input type="radio" name="grx_gender" value="Female" class="selectgroup-input-radio" checked>
                                    <span class="selectgroup-button px-2 py-1"><i class="bi bi-gender-female"></i>&nbsp;Female</span>
                                </label>
                                <label>
                                    <input type="radio" name="grx_gender" value="Others" class="selectgroup-input-radio">
                                    <span class="selectgroup-button px-2 py-1"><i class="bi bi-gender-ambiguous"></i>&nbsp;Other</span>
                                </label>
                            </div>
                        </div>
                    <!-- </div> -->
                    <hr>

                    <!-- ── Vitals ── -->
                    <h6 class="text-dark">Vitals</h6>
                    <div class="row mt-lg-3 mt-sm-3">
                        <div class="form-group col-lg-2 col-sm-12">
                            <label><i class="material-icons bpicon">airline_seat_recline_normal</i>BP/mmHg</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control" id="grx_bpSit_systolic">
                                <span class="divider">/</span>
                                <input type="text" class="form-control" id="grx_bpSit_diastolic">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label><i class="fa-solid fa-person bpicon"></i>BP/mmHg</label>
                            <div class="input-wrapper">
                                <input type="text" class="form-control" id="grx_bpStand_systolic">
                                <span class="divider">/</span>
                                <input type="text" class="form-control" id="grx_bpStand_diastolic">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>Weight (Kg)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><i class="fa-solid fa-weight-scale"></i></div></div>
                                <input type="text" class="form-control" id="grx_weight">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>Height (cms)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-ruler-vertical"></i></div></div>
                                <input type="text" class="form-control" id="grx_height">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>BMI Value</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/bmi.jpeg" width="18" height="18"></div></div>
                                <input type="text" class="form-control" id="grx_bmi" readonly>
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>GRBS (mg/dL)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><i class="fa-solid fa-droplet"></i></div></div>
                                <input type="text" class="form-control" id="grx_grbs">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>Heart Rate/min</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-heart-pulse"></i></span>
                                <input type="text" class="form-control" id="grx_heart_rate">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>Temp (°F)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-thermometer-half"></i></span>
                                <input type="text" class="form-control" id="grx_temperature">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-sm-12">
                            <label>Resp / min</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lungs-fill"></i></span>
                                <input type="text" class="form-control" id="grx_respiration_rate">
                            </div>
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>SPO2 (%) (on Room Air)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/spo2.jpg" width="22" height="22"></div></div>
                                <input type="tel" class="form-control" id="grx_spO2">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Over-View of Patient</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><div class="input-group-text"><i class="fa-solid fa-street-view"></i></div></div>
                                <input type="text" class="form-control" id="grx_patient_overview">
                            </div>
                        </div>
                    </div>
                    <hr>

                    <!-- ── Assessment (Gynaec) ── -->
                    <h6 class="text-dark">Assessment</h6>
                    <div class="row mt-lg-4 mt-sm-4">
                        <?php
                        $grxTplFields = [
                            'final_diagnosis'         => 'Final Diagnosis',
                            'chief_complaints'        => 'Chief Complaints / HPI',
                            'gynaec_history'          => 'Gynaec History',
                            'obstetric_history'       => 'Obstetric History (G_P_L_A_)',
                            'family_history'          => 'Family History',
                            'personal_history'        => 'Personal History',
                            'general_examination'     => 'General Examination',
                            'previous_investigations' => 'Previous Investigations',
                        ];
                        $colMap = ['general_examination'=>'col-md-6','previous_investigations'=>'col-md-6'];
                        foreach ($grxTplFields as $fKey => $fLabel):
                            $col = $colMap[$fKey] ?? 'col-md-4';
                            $rows = in_array($fKey,['general_examination','previous_investigations']) ? 2 : 3;
                        ?>
                        <div class="form-group <?= $col ?> col-sm-12" style="position:relative;">
                            <label class="d-flex align-items-center gap-1">
                                <span><?= htmlspecialchars($fLabel) ?></span>
                                <span class="btn-group ms-1">
                                    <i class="fa fa-history grx-tpl-history dropdown-toggle fa-sm"
                                       data-bs-toggle="dropdown" aria-expanded="false"
                                       data-field="<?= $fKey ?>"
                                       style="cursor:pointer;color:#6c757d;" title="Templates"></i>
                                    <ul class="dropdown-menu p-2" id="grxTplDrop_<?= $fKey ?>"
                                        style="width:300px;max-height:260px;overflow-y:auto;"></ul>
                                </span>
                                <i class="fas fa-plus-circle text-success grx-tpl-add"
                                   data-field="<?= $fKey ?>" data-label="<?= htmlspecialchars($fLabel) ?>"
                                   style="cursor:pointer;" title="Save as template"></i>
                            </label>
                            <textarea class="form-control" id="grx_<?= $fKey ?>" rows="<?= $rows ?>"></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>

                    <!-- ── Menstrual & Basic ── -->
                    <h6 class="text-dark">Menstrual &amp; Basic Details</h6>
                    <div class="row mt-lg-3 mt-sm-3">
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Menstrual History</label>
                            <textarea class="form-control" id="grx_menstrual_history" rows="2" placeholder="e.g. Regular, 28-day cycle, 4-5 days flow..."></textarea>
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>LMP (Last Menstrual Period)</label>
                            <input type="date" class="form-control" id="grx_lmp">
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>PMC (Previous Menstrual Cycle)</label>
                            <input type="text" class="form-control" id="grx_pmc" placeholder="Regular / Irregular">
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>EDD (Expected Delivery Date)</label>
                            <input type="date" class="form-control" id="grx_edd">
                        </div>
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Risk Factors in Index Pregnancy</label>
                            <textarea class="form-control" id="grx_risk_factors" rows="2"></textarea>
                        </div>
                    </div>
                    <hr>

                    <!-- ── Scan Details ── -->
                    <h6 class="text-dark">Scan Details</h6>
                    <div class="row mt-lg-3 mt-sm-3">
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Scan Type</label>
                            <input type="text" class="form-control" id="grx_scan_type" placeholder="Obstetric / TVS / Abdominal USG">
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Scan Date</label>
                            <input type="date" class="form-control" id="grx_scan_date">
                        </div>
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Scan Findings</label>
                            <textarea class="form-control" id="grx_scan_findings" rows="3"></textarea>
                        </div>
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Scan Remarks</label>
                            <textarea class="form-control" id="grx_scan_remarks" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Patient Data ── -->
            <div class="card-body pt-0">
                <div class="row">
                    <div class="form-group col-md-12 col-sm-12">
                        <label for="grx_patient_data">Patient Data</label>
                        <textarea class="form-control" id="grx_patient_data" name="grx_patient_data" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <!-- ── MEDICATION — exact copy from prescription.php ── -->
            <div class="adding-new-record card-body">
                <hr>
                <h6 class="text-dark">Medication</h6>

                <div class="row mt-lg-3 mt-sm-3">

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="medicineType">Medicine Type <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-capsules"></i></div></div>
                            <input list="medicineTypeDatalist" class="form-control medicinetype" name="medicineType" id="medicineType">
                            <datalist id="medicineTypeDatalist"><option value=""></datalist>
                            <div id="typeDropdown" class="form-control dropdown-menu" type="hidden"></div>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="medicineName">Medicine Name <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><i class="bi bi-capsule"></i></div></div>
                            <input list="drugNameDatalist" class="form-control drugname" id="drugName" name="drugName"
                                   oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this)">
                            <datalist id="drugNameDatalist"><option value=""></datalist>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-sm-12">
                        <label for="unit">Unit <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/unit.jpeg" width="17" height="17"></div></div>
                            <input list="unitDatalist" class="form-control unit" id="unit" name="unit">
                            <datalist id="unitDatalist"><option value=""></datalist>
                        </div>
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="dosage">Dosage <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/dosage.jpeg" width="17" height="17"></div></div>
                            <select class="form-control form-select dosage" name="dosage" id="dosage" onchange="handleDosageChange(this.value)">
                                <option value="">Select Dosage</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="when">In-take-period <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/dosage.jpeg" width="17" height="17"></div></div>
                            <select class="form-control form-select" name="when" id="when">
                                <option value="">Select In-take-period</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="time">Time <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-clock"></i></div></div>
                            <select class="form-control form-select" name="time" id="time">
                                <option value="">Select Time</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="duration_value">Duration <span class="text-danger med">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><div class="input-group-text"><i class="material-icons">date_range</i></div></div>
                            <input type="number" class="form-control" name="duration_value" id="duration_value">
                            <select class="form-control" name="duration" id="duration">
                                <option value="Days">Days</option>
                                <option value="Weeks">Weeks</option>
                                <option value="Months">Months</option>
                                <option value="Till Further Advice">Till Further Advice</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-2 col-sm-12">
                        <label for="grx_route">Route</label>
                        <select class="form-control form-select" id="grx_route" name="grx_route">
                            <option value="">Route</option>
                            <?php
                            $routeQ = mysqli_query($conn,"SELECT routes FROM route WHERE status='1' AND routes!='' ORDER BY route_id ASC");
                            while($rt=mysqli_fetch_assoc($routeQ)) echo "<option value='".htmlspecialchars($rt['routes'])."'>".htmlspecialchars($rt['routes'])."</option>";
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-sm-12">
                        <label for="grx_med_concession">Prescribed Discount <small class="text-muted">(not printed)</small></label>
                        <select class="form-control" id="grx_med_concession" name="grx_med_concession">
                            <option value="">No Discount</option>
                            <?php
                            $getMedConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1'") or die(mysqli_error($conn));
                            while ($cRow = mysqli_fetch_assoc($getMedConcessions)) {
                                echo '<option value="'.$cRow['concession_id'].'"
                                    data-type="'.htmlspecialchars($cRow['concession_type']).'"
                                    data-value="'.htmlspecialchars($cRow['concession_value']).'">'.htmlspecialchars($cRow['concession_name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-lg-1 col-sm-12">
                        <a href="javascript:void(0)" class="adding-medicine float-end btn btn-primary Pule"><i class="fas fa-plus"></i></a>
                    </div>

                    <div class="form-group col-lg-12 col-sm-12">
                        <label for="notes">Instructions
                            <span class="ms-2 position-relative">
                                <i class="fa fa-history text-secondary grx-instrhistory-med" title="Load instruction template" style="cursor:pointer;font-size:14px;"></i>
                                <ul class="list-unstyled bg-white border rounded shadow p-2 grx-instr-template-dropdown grx-instr-dropdown-med"
                                    style="display:none;position:absolute;top:20px;left:0;min-width:280px;max-height:220px;overflow-y:auto;z-index:9999;"></ul>
                            </span>
                            <i class="fa fa-plus-circle text-success ms-1 grxAddInstrTemplateBtn" data-target="med" title="Save current as template" style="cursor:pointer;font-size:14px;"></i>
                        </label>
                        <textarea class="form-control" name="notes" id="notes"></textarea>
                    </div>
                    <hr class="mt-lg-5">
                    <div class="card-body" id="medicineTableWrapper"></div>
                </div>

                <hr class="mt-5">
                <h6 class="text-dark">Investigation</h6>

                <div class="row mt-lg-5 mt-sm-5">
                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="investigation" class="d-flex align-items-center gap-1">
                            <span>Investigation <span class="text-danger invest">*</span></span>
                            <span class="btn-group ms-1">
                                <i class="fa fa-history investgationhistory dropdown-toggle fa-sm"
                                   data-bs-toggle="dropdown" aria-expanded="false"
                                   style="cursor:pointer;color:#6c757d;" title="Investigation Templates"></i>
                                <ul class="dropdown-menu p-2" id="grxInvTplDropdown"
                                    style="width:300px;max-height:260px;overflow-y:auto;"></ul>
                            </span>
                            <i class="fas fa-plus-circle text-success" id="grxAddInvTplBtn"
                               style="cursor:pointer;" title="Save as template"></i>
                        </label>
                        <input list="investigationDatalist" class="form-control investigation" id="investigation" name="investigation"
                               oninput="this.value = this.value.toUpperCase();">
                        <datalist id="investigationDatalist"><option value=""></datalist>
                    </div>
                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="grx_concession">Concession</label>
                        <select class="form-control" id="grx_concession" name="grx_concession">
                            <option value="">Select Concession</option>
                            <?php
                            $getConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1'") or die(mysqli_error($conn));
                            while ($rowC = mysqli_fetch_assoc($getConcessions)) {
                                echo '<option value="'.$rowC['concession_id'].'"
                                    data-type="'.htmlspecialchars($rowC['concession_type']).'"
                                    data-value="'.htmlspecialchars($rowC['concession_value']).'">'.htmlspecialchars($rowC['concession_name']).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-3 col-sm-12 mt-3">
                        <input type="hidden" id="test_price" name="test_price" value="">
                        <span id="grxPriceDisplay" style="font-size:1.05em;"></span>
                    </div>
                    <div class="form-group col-lg-1 col-sm-12" style="display:flex;align-items:flex-end;">
                        <a href="javascript:void(0)" class="adding-form float-end btn btn-primary Pule"><i class="fas fa-plus"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-11 col-sm-12">
                        <label for="testnotes">Instruction
                            <span class="ms-2 position-relative">
                                <i class="fa fa-history text-secondary grx-instrhistory-inv" title="Load instruction template" style="cursor:pointer;font-size:14px;"></i>
                                <ul class="list-unstyled bg-white border rounded shadow p-2 grx-instr-template-dropdown grx-instr-dropdown-inv"
                                    style="display:none;position:absolute;top:20px;left:0;min-width:280px;max-height:220px;overflow-y:auto;z-index:9999;"></ul>
                            </span>
                            <i class="fa fa-plus-circle text-success ms-1 grxAddInstrTemplateBtn" data-target="inv" title="Save current as template" style="cursor:pointer;font-size:14px;"></i>
                        </label>
                        <textarea class="form-control" name="testnotes" id="testnotes"></textarea>
                    </div>
                    <div id="investigationTableWrapper" style="overflow-x:auto;"></div>
                </div>

                <!-- ── Plan / Advice / Review ── -->
                <hr class="mt-5">
                <h6 class="text-dark">Plan &amp; Review</h6>
                <div class="row mt-3">
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="personal_note">Plan</label>
                        <textarea class="form-control" id="personal_note" name="personal_note" rows="3"></textarea>
                    </div>
                    <div class="form-group col-lg-6 col-sm-12" style="position:relative;">
                        <label for="advise" class="d-flex align-items-center gap-1">
                            <span>Advice</span>
                            <span class="btn-group ms-1">
                                <i class="fa fa-history grx-tpl-history dropdown-toggle fa-sm"
                                   data-bs-toggle="dropdown" aria-expanded="false"
                                   data-field="advice"
                                   style="cursor:pointer;color:#6c757d;" title="Templates"></i>
                                <ul class="dropdown-menu p-2" id="grxTplDrop_advice"
                                    style="width:300px;max-height:260px;overflow-y:auto;"></ul>
                            </span>
                            <i class="fas fa-plus-circle text-success grx-tpl-add"
                               data-field="advice" data-label="Advice"
                               style="cursor:pointer;" title="Save as template"></i>
                        </label>
                        <textarea class="form-control" id="advise" name="advise" rows="3"></textarea>
                    </div>
                    <div class="form-group col-lg-6 col-sm-12">
                        <label>Review After</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="reviewInput" placeholder="Number">
                            <select class="form-control" id="reviewSelect">
                                <option value="Days">Days</option>
                                <option value="Weeks">Weeks</option>
                                <option value="Months">Months</option>
                                <option value="Till Further Advice">Till Further Advice</option>
                            </select>
                            <span class="input-group-text">(OR)</span>
                            <input type="date" class="form-control" id="reviewCalculatedDate">
                        </div>
                    </div>
                    <div class="form-group col-lg-6 col-sm-12">
                        <label>Review Notes</label>
                        <textarea class="form-control" id="grx_review_notes" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer text-center">
                <button class="btn btn-primary" id="saveGrxBtn">Save Gynaec Prescription</button>
            </div>
        </div>
    </div>

    <!-- History -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4>Gynaec Prescription History</h4></div>
                <div class="card-body">
                    <div id="grxHistoryContainer"><p class="text-muted">Loading...</p></div>
                </div>
            </div>
        </div>
    </div>

</section>
</div>

<!-- ── Instruction Template Modal (notes / testnotes) ── -->
<div class="modal fade" id="grxInstrTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grxInstrTemplateModalTitle">Save Instruction Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="grxInstrTplTarget">
                <div class="mb-2">
                    <label class="form-label">Template Name</label>
                    <input type="text" class="form-control" id="grxInstrTplName" placeholder="Enter name">
                </div>
                <div class="mb-2">
                    <label class="form-label">Content (preview)</label>
                    <textarea class="form-control" id="grxInstrTplPreview" rows="3" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="grxInstrTplSaveBtn">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Gynaec Field Template Modal ── -->
<div class="modal fade" id="grxTplModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grxTplModalTitle">Save Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="grxTplEditId">
                <input type="hidden" id="grxTplFieldType">
                <div class="mb-2">
                    <label class="form-label">Template Name</label>
                    <input type="text" class="form-control" id="grxTplName" placeholder="Enter name">
                </div>
                <div class="mb-2">
                    <label class="form-label">Content (preview)</label>
                    <textarea class="form-control" id="grxTplPreview" rows="4" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="grxTplSaveBtn">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Investigation Template Modal ── -->
<div class="modal fade" id="grxInvTplModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Investigation Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Template Name</label>
                    <input type="text" class="form-control" id="grxInvTplName" placeholder="Enter template name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Price</label>
                    <div class="input-group">
                        <div class="input-group-prepend"><div class="input-group-text"><i class="bi bi-currency-rupee"></i></div></div>
                        <input type="number" class="form-control" id="grxInvTplPrice" placeholder="Total price">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="grxInvTplSaveBtn">Save Template</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Medicine Modal — exact copy from prescription.php -->
<div class="modal fade" id="editVitalsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Medicine</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="editVitalsForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Medicine Name <span class="text-danger">*</span></label>
                            <input list="edit_drugNameDatalist" class="form-control" id="edit_drugName" name="edit_drugName" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this)">
                            <datalist id="edit_drugNameDatalist"><option value=""></datalist>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Medicine Type</label>
                            <input list="edit_medicineTypeDatalist" class="form-control" id="edit_medicineType" name="edit_medicineType">
                            <datalist id="edit_medicineTypeDatalist"><option value=""></datalist>
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Unit</label>
                            <input list="edit_unitDatalist" class="form-control" id="edit_unit" name="edit_unit">
                            <datalist id="edit_unitDatalist"><option value=""></datalist>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Dosage</label>
                            <select class="form-control" id="edit_dosage" name="edit_dosage" onchange="getmodalTimeForDose(this.value)">
                                <option value="">Select Dosage</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>In-take-period</label>
                            <select class="form-control" id="edit_when" name="edit_when">
                                <option value="">Select In-take-period</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-sm-12">
                            <label>Time</label>
                            <select class="form-control" id="edit_time" name="edit_time">
                                <option value="">Select Time</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label>Duration</label>
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
                        <div class="form-group col-lg-12 col-sm-12">
                            <label>Instructions</label>
                            <textarea class="form-control" id="edit_notes" name="edit_notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="updateVitals">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Investigation Modal -->
<div class="modal fade" id="editInvestigationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Investigation</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <label>Investigation</label>
                <input list="edit_investigationDatalist" class="form-control edit_investigation" id="edit_investigation" name="edit_investigation" oninput="this.value = this.value.toUpperCase();">
                <datalist id="edit_investigationDatalist"><option value=""></datalist>
                <label class="mt-2">Instruction</label>
                <textarea class="form-control mt-1" id="edit_testnotes"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveEditInvestigation">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once("ajax/footer.php"); ?>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
/* =====================================================================
   PATIENT DROPDOWNS — same pattern as prescription.php
   ===================================================================== */
$(document).ready(function(){
    loadGrxHistory();
    getMedichines('');
    getMedicineType('');
    getUnit('');
    getDosages('');
    getInTakePeriod('');
    gettests();
    $('#drugName').on('input', toggleMedSpans);
    $('#investigation').on('input', toggleTestSpans);
    toggleMedSpans(); toggleTestSpans();
    // Pre-load diagnosis templates silently in background
    grxLoadDxDropdown();
});

function toggleMedSpans(){
    var v = $('#drugName').val();
    $('.med').css('display', v ? 'inline' : 'none');
}
function toggleTestSpans(){
    var v = $('#investigation').val();
    $('.invest').css('display', v ? 'inline' : 'none');
}

// ── Fill all form fields from a gynaec prescription API response ────────────
function grxFillPrescriptionData(resp) {
    if (!resp || !resp.success) return;
    var d = resp.data;
    $('#grx_rx_id').val(d.gynaec_rx_id);
    $('#grx_appoint_id_val').val(d.appointment_id || '');
    $('#grx_patient_id_val').val(d.patient_id     || '');

    $('#grx_final_diagnosis').val(d.final_diagnosis         || '');
    $('#grx_chief_complaints').val(d.chief_complaints       || '');
    $('#grx_gynaec_history').val(d.gynaec_history           || '');
    $('#grx_obstetric_history').val(d.obstetric_history     || '');
    $('#grx_family_history').val(d.family_history           || '');
    $('#grx_personal_history').val(d.personal_history       || '');
    $('#grx_general_examination').val(d.general_examination || '');
    $('#grx_previous_investigations').val(d.previous_investigations || '');
    $('#grx_menstrual_history').val(d.menstrual_history     || '');
    $('#grx_pmc').val(d.pmc                                 || '');
    $('#grx_risk_factors').val(d.risk_factors               || '');
    $('#grx_scan_type').val(d.scan_type                     || '');
    $('#grx_scan_findings').val(d.scan_findings             || '');
    $('#grx_scan_remarks').val(d.scan_remarks               || '');
    $('#advise').val(d.advice                               || '');
    $('#personal_note').val(d.plan                          || '');
    $('#grx_review_notes').val(d.review_notes               || '');
    $('#grx_lmp').val(d.lmp  && d.lmp  !== '0000-00-00' ? d.lmp  : '');
    $('#grx_edd').val(d.edd  && d.edd  !== '0000-00-00' ? d.edd  : '');
    $('#grx_scan_date').val(d.scan_date && d.scan_date !== '0000-00-00' ? d.scan_date : '');
    $('#grx_patient_data').val(d.patient_data || '');

    // Review after
    if (d.reviewafterdate) {
        $('#reviewCalculatedDate').val(d.reviewafterdate);
    } else if (d.review_after) {
        var ra = d.review_after.trim();
        if (ra.startsWith('By ')) {
            $('#reviewCalculatedDate').val(ra.substring(3).trim());
        } else {
            var raParts = ra.split(' ');
            $('#reviewInput').val(raParts[0] || '');
            if (raParts[1]) $('#reviewSelect').val(raParts[1]);
        }
    }

    getMedichines('');

    // Rebuild medicine table
    (resp.medicines || []).forEach(function(m) {
        var medData = {
            drugName: m.drugName||m.medicine_name||'', typeText: m.typeText||m.type_text||'',
            unitText: m.unitText||m.unit_text||'',     dosageId: m.dosageId||m.dosage_id||'',
            dosageText: m.dosageText||m.dosages||'',   whenId: m.whenId||m.when_id||'',
            whenText: m.whenText||m.when||'',          timeId: m.timeId||m.time_id||'',
            timeText: m.timeText||m.time||'',          duration_value: m.duration_value||'',
            duration: m.duration||'Days',              route: m.route||'',
            notes: m.notes||m.instructions||'',
            medConcessionId: m.medConcessionId||'', medConcessionName: m.medConcessionName||''
        };
        var shortNotes = medData.notes.length > 10 ? medData.notes.substring(0,10)+'...' : medData.notes;
        var medDiscount = medData.medConcessionName || '';
        medicineArray.push(medData);
        if (!tableCreated) {
            var container = $('#medicineTableWrapper'); container.css('overflow-x','auto');
            var table = $('<table id="medicineTable"></table>').css({'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px'});
            var thead = $('<thead></thead>').append($('<tr></tr>').append($('<th>Drag and Drop</th>'),$('<th>S.No</th>'),$('<th>Type</th>'),$('<th>Medicine</th>'),$('<th>Unit</th>'),$('<th>Dosage</th>'),$('<th>In-take</th>'),$('<th>Time</th>'),$('<th>Duration</th>'),$('<th>Route</th>'),$('<th>Discount</th>'),$('<th>Note</th>'),$('<th>Action</th>')));
            thead.find('th').css({'padding':'8px','border':'1px solid #ddd','background':'lightblue','text-align':'center','font-weight':'bold'});
            table.append(thead).append($('<tbody></tbody>'));
            container.append(table); tableCreated = true;
            $('#medicineTable tbody').sortable({ handle:'.drag-handle', axis:'y', cursor:'grabbing', update:function(){ var newOrder=[]; $('#medicineTable tbody tr').each(function(){ var idx=parseInt($(this).data('med-index')); if(!isNaN(idx)) newOrder.push(medicineArray[idx]); }); medicineArray=newOrder; updateMedIndexes(); updateSerialNumbers(); } });
        }
        appendRowToTable({ drugName:medData.drugName, typeText:medData.typeText, unitText:medData.unitText, dosageText:medData.dosageText, whenText:medData.whenText, timeText:medData.timeText, duration_value:medData.duration_value, duration:medData.duration, route:medData.route, medDiscount:medDiscount, shortNotes:shortNotes }, medicineArray.length-1);
    });

    // Rebuild investigation table
    (resp.investigations || []).forEach(function(inv) {
        var data = {
            investigation: inv.investigation_name||inv.investigation||'',
            instruction:   inv.instructions||inv.instruction||'',
            price:         inv.price||'0',
            concession:    inv.concession||'',
            concessionName:'', concessionValue:'', concessionType:''
        };
        investigationArray.push(data);
        if (!investigationTableCreated) {
            var container = $('#investigationTableWrapper'); container.css('overflow-x','auto');
            var table = $('<table id="investigationTable"></table>').css({'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px'});
            var thead = $('<thead></thead>').append($('<tr></tr>').append(
                $('<th>S.No</th>'),$('<th>Investigation</th>'),$('<th>Instruction</th>'),
                $('<th>Price</th>'),$('<th>Concession</th>'),$('<th>Action</th>')
            ));
            thead.find('th').css({'padding':'8px','border':'1px solid #ddd','background':'lightblue','font-weight':'bold','text-align':'center'});
            table.append(thead).append($('<tbody></tbody>')); container.append(table);
            investigationTableCreated = true;
        }
        appendInvestigationRow(data, investigationArray.length-1);
    });
}

// ── After filling patient fields, auto-load their latest gynaec prescription ─
function grxAutoLoadPrescription(appointId, patientId) {
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/gynaec_prescription/getLatestGynaecRx.php', type: 'POST',
        data: { appointment_id: appointId, patient_id: patientId, org_id: orgId },
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.success) {
                grxFillPrescriptionData(resp);
            }
        }
    });
}

// ── Fill patient fields only (without reset, used internally) ─────────────────
function grxFillPatientDirect(d) {
    if ($('#grx_patientIdName option[value="'+d.patient_name+'"]').length === 0)
        $('#grx_patientIdName').append('<option value="'+d.patient_name+'">'+d.patient_name+'</option>');
    $('#grx_patientIdName').val(d.patient_name);

    if ($('#grx_mobile_number option[value="'+d.mobile_number+'"]').length === 0)
        $('#grx_mobile_number').append('<option value="'+d.mobile_number+'">'+d.mobile_number+'</option>');
    $('#grx_mobile_number').val(d.mobile_number);

    if ($('#grx_patientId option[value="'+d.appoint_unicode+'"]').length === 0)
        $('#grx_patientId').append('<option value="'+d.appoint_unicode+'">'+d.appoint_unicode+'</option>');
    $('#grx_patientId').val(d.appoint_unicode);

    if ($('#grx_appointId option[value="'+d.appoint_register_id+'"]').length === 0)
        $('#grx_appointId').append('<option value="'+d.appoint_register_id+'" data-unicode="'+d.appoint_unicode+'">'+d.appoint_register_id+'</option>');
    $('#grx_appointId').val(d.appoint_register_id);

    $('#grx_age').val(d.age);
    if (d.dob) $('#grx_dob').val(d.dob);
    $('#grx_appoint_id_val').val(d.appoint_register_id);
    $('#grx_patient_id_val').val(d.appoint_unicode);
    var g = d.gender || 'Female';
    $('input[name="grx_gender"][value="'+g+'"]').prop('checked', true);
    grxUpdateVitalsAndFee(d.appoint_register_id);
    showGrxDxTemplateBtn();
}

// Select name → auto-fill patient fields + load latest prescription
function grxGetNumberAndIdByName() {
    var patient_name = $('#grx_patientIdName').val();
    if (!patient_name) return;
    $.ajax({
        url: 'ajax/Wprescripation/getPatientNumberAndIdByName.php',
        type: 'POST', data: { patient_name: patient_name }, dataType: 'json',
        success: function(data) {
            if (!data.length) return;
            grxDoReset();
            $('#grx_patientIdName').val(patient_name);
            var d = data[0];
            $('#grx_mobile_number').empty().append('<option value="">Select Mobile</option>');
            $.each(data, function(_, v) { $('#grx_mobile_number').append('<option value="'+v.mobile_number+'">'+v.mobile_number+'</option>'); });
            $('#grx_mobile_number').val(d.mobile_number);
            $('#grx_patientId').empty().append('<option value="">Select ID</option>');
            $.each(data, function(_, v) { $('#grx_patientId').append('<option value="'+v.appoint_unicode+'">'+v.appoint_unicode+'</option>'); });
            $('#grx_patientId').val(d.appoint_unicode);
            $('#grx_appointId').empty().append('<option value="">Select Appointment ID</option>');
            $.each(data, function(_, v) { $('#grx_appointId').append('<option value="'+v.appoint_register_id+'" data-unicode="'+v.appoint_unicode+'">'+v.appoint_register_id+'</option>'); });
            $('#grx_appointId').val(d.appoint_register_id);
            $('#grx_age').val(d.age);
            if (d.dob) $('#grx_dob').val(d.dob);
            $('#grx_appoint_id_val').val(d.appoint_register_id);
            $('#grx_patient_id_val').val(d.appoint_unicode);
            $('input[name="grx_gender"][value="'+(d.gender||'Female')+'"]').prop('checked', true);
            grxUpdateVitalsAndFee(d.appoint_register_id);
            showGrxDxTemplateBtn();
            grxAutoLoadPrescription(d.appoint_register_id, d.appoint_unicode);
        }
    });
}

// Select mobile → auto-fill patient fields + load latest prescription
function grxGetNameByNumber() {
    var mobile = $('#grx_mobile_number').val();
    if (!mobile) return;
    $.ajax({
        url: 'ajax/Wprescripation/getPatientNameAndIdByNumber.php',
        type: 'POST', data: { patient_number: mobile }, dataType: 'json',
        success: function(data) {
            if (!data.length) return;
            grxDoReset();
            var d = data[0];
            if ($('#grx_patientIdName option[value="'+d.patient_name+'"]').length === 0)
                $('#grx_patientIdName').append('<option value="'+d.patient_name+'">'+d.patient_name+'</option>');
            $('#grx_patientIdName').val(d.patient_name);
            $('#grx_patientId').empty().append('<option value="">Select ID</option>');
            $.each(data, function(_, v) { $('#grx_patientId').append('<option value="'+v.appoint_unicode+'">'+v.appoint_unicode+'</option>'); });
            $('#grx_patientId').val(d.appoint_unicode);
            $('#grx_appointId').empty().append('<option value="">Select Appointment ID</option>');
            $.each(data, function(_, v) { $('#grx_appointId').append('<option value="'+v.appoint_register_id+'" data-unicode="'+v.appoint_unicode+'">'+v.appoint_register_id+'</option>'); });
            $('#grx_appointId').val(d.appoint_register_id);
            $('#grx_age').val(d.age);
            if (d.dob) $('#grx_dob').val(d.dob);
            $('#grx_appoint_id_val').val(d.appoint_register_id);
            $('#grx_patient_id_val').val(d.appoint_unicode);
            $('input[name="grx_gender"][value="'+(d.gender||'Female')+'"]').prop('checked', true);
            grxUpdateVitalsAndFee(d.appoint_register_id);
            showGrxDxTemplateBtn();
            grxAutoLoadPrescription(d.appoint_register_id, d.appoint_unicode);
        }
    });
}

// Select Patient ID → fill all fields + load latest prescription
function grxGetByPatientId() {
    var unicode = $('#grx_patientId').val();
    if (!unicode) return;
    $.ajax({
        url: 'ajax/gynaec_prescription/getPatientByUnicode.php', type: 'POST',
        data: { appoint_unicode: unicode }, dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                grxFillPatientDirect(resp.data);
                grxAutoLoadPrescription(resp.data.appoint_register_id, resp.data.appoint_unicode);
            }
        }
    });
}

// Select Appointment ID → fill all fields + load latest prescription
function grxGetByAppointId() {
    var regId = $('#grx_appointId').val();
    if (!regId) return;
    $.ajax({
        url: 'ajax/gynaec_prescription/getPatientByAppointId.php', type: 'POST',
        data: { appoint_register_id: regId }, dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                grxFillPatientDirect(resp.data);
                grxAutoLoadPrescription(regId, resp.data.appoint_unicode);
            }
        }
    });
}

function resetGrxForm(){ grxDoReset(); }

function grxDoReset(){
    $('#grx_rx_id,#grx_appoint_id_val,#grx_patient_id_val').val('');
    $('#grx_patientIdName,#grx_mobile_number,#grx_patientId,#grx_appointId').val('');
    $('#grx_age,#grx_dob').val('');
    $('input[name="grx_gender"][value="Female"]').prop('checked',true);
    $('#grx_final_diagnosis,#grx_chief_complaints,#grx_gynaec_history,#grx_obstetric_history').val('');
    $('#grx_family_history,#grx_personal_history,#grx_general_examination,#grx_previous_investigations').val('');
    $('#grx_menstrual_history,#grx_pmc,#grx_risk_factors,#grx_scan_type,#grx_scan_findings,#grx_scan_remarks').val('');
    $('#grx_lmp,#grx_edd,#grx_scan_date').val('');
    $('#grx_review_notes').val('');
    $('#advise,#personal_note').val('');
    $('#reviewInput').val(''); $('#reviewSelect').val('Days'); $('#reviewCalculatedDate').val('');
    // Reset vitals
    $('#grx_bpSit_systolic,#grx_bpSit_diastolic,#grx_bpStand_systolic,#grx_bpStand_diastolic').val('');
    $('#grx_weight,#grx_height,#grx_bmi,#grx_grbs,#grx_heart_rate,#grx_temperature').val('');
    $('#grx_respiration_rate,#grx_spO2,#grx_patient_overview').val('');
    $('#grx_patient_data').val('');
    // Hide header badges/buttons
    $('#grxPaymentStatusBadge').hide();
    $('#grxViewBtn').addClass('d-none');
    $('#grxDxDropdownGroup').addClass('d-none');
    // Reset medicine
    medicineArray = [];
    $('#medicineTableWrapper').empty();
    tableCreated = false;
    $('#drugName,#medicineType,#unit,#notes').val('');
    $('#dosage,#when,#time').val('');
    $('#duration_value').val(''); $('#duration').val('Days'); $('#grx_route').val(''); $('#grx_med_concession').val('');
    $('#time').html('<option value="">Select Time</option>');
    // Reset investigation
    investigationArray = [];
    $('#investigationTableWrapper').empty();
    investigationTableCreated = false;
    $('#investigation,#testnotes').val('');
    $('#grx_concession').val(''); $('#test_price').val(''); $('#grxPriceDisplay').html('');
}

/* =====================================================================
   MEDICINE — exact copy from prescription.php (medicineArray, appendRowToTable, etc.)
   ===================================================================== */
var NewInputsCountIni = 100;
var Timing = "";

let tableCreated = false;
let medicineArray = [];
let editingIndex = null;

$(document).on('click', '.adding-medicine', function() {
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
    let route = $('#grx_route').val();
    let notes = $('#notes').val();
    let shortNotes = notes.length > 10 ? notes.substring(0, 10) + "..." : notes;
    let medConcessionId   = $('#grx_med_concession').val();
    let medConcessionName = $('#grx_med_concession option:selected').text();
    let medConcessionType = $('#grx_med_concession option:selected').data('type') || '';
    let medConcessionVal  = $('#grx_med_concession option:selected').data('value') || '';
    let medDiscount = medConcessionId ? medConcessionName : '';

    if (!drugName || !typeText || !unitText || !dosageId || !whenId || !duration_value || !duration) {
        Swal.fire({ text: 'Please fill all fields before adding.', confirmButtonText: 'OK' });
        return;
    }

    const medData = { drugName, typeText, unitText, dosageId, whenId, timeId, duration_value, duration, route, notes, dosageText, whenText, timeText,
                      medConcessionId, medConcessionName, medConcessionType, medConcessionVal };
    const medDisplay = { drugName, typeText, unitText, dosageText, whenText, timeText, duration, duration_value, route, shortNotes, medDiscount };

    medicineArray.push(medData);

    if (!tableCreated) {
        let container = $('#medicineTableWrapper');
        container.css('overflow-x', 'auto');
        let table = $('<table id="medicineTable"></table>').css({ 'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px' });
        let thead = $('<thead></thead>').append(
            $('<tr></tr>').append(
                $('<th>Drag and Drop</th>'), $('<th>S.No</th>'), $('<th>Type</th>'), $('<th>Medicine</th>'),
                $('<th>Unit</th>'), $('<th>Dosage</th>'), $('<th>In-take</th>'), $('<th>Time</th>'),
                $('<th>Duration</th>'), $('<th>Route</th>'), $('<th>Discount</th>'), $('<th>Note</th>'), $('<th>Action</th>')
            )
        );
        thead.find('th').css({ 'padding':'8px','border':'1px solid #ddd','background':'lightblue','text-align':'center','font-weight':'bold' });
        let tbody = $('<tbody></tbody>').css({ 'padding':'2px','margin':'0' });
        table.append(thead).append(tbody);
        $('#medicineTableWrapper').append(table);
        tableCreated = true;

        $('#medicineTable tbody').sortable({
            handle: '.drag-handle', axis: 'y', cursor: 'grabbing',
            update: function() {
                let newOrder = [];
                $('#medicineTable tbody tr').each(function() {
                    const idx = parseInt($(this).data('med-index'));
                    if (!isNaN(idx)) newOrder.push(medicineArray[idx]);
                });
                medicineArray = newOrder;
                if (editingIndex !== null) {
                    $('#medicineTable tbody tr').each(function(i) {
                        if ($(this).find('.inline-save-btn').length > 0) { editingIndex = i; return false; }
                    });
                }
                updateMedIndexes(); updateSerialNumbers();
            }
        });
    }

    appendRowToTable(medDisplay, medicineArray.length - 1);
    $('#drugName, #medicineType, #unit, #dosage, #when, #time, #duration_value, #notes').val('');
    $('#grx_route').val('');
    $('#grx_med_concession').val('');
    $('#duration_value').prop('disabled', false);
    $('#duration').val('Days');
    toggleMedSpans();
});

function updateMedIndexes() {
    $('#medicineTable tbody tr').each(function(i) { $(this).data('med-index', i); });
}

function appendRowToTable(data, index) {
    let row = $('<tr></tr>').data('med-index', index);
    const dragHandle = $('<td></td>').append(
        $('<i class="fas fa-grip-vertical drag-handle"></i>').css({ 'cursor':'grab','color':'#aaa','padding':'0 4px' })
    ).css({ 'text-align':'center','border':'1px solid #ccc' });
    row.append(dragHandle);
    row.append($('<td class="sno"></td>').text(index + 1));
    row.append($('<td></td>').text(data.typeText));
    row.append($('<td></td>').text(data.drugName));
    row.append($('<td></td>').text(data.unitText));
    row.append($('<td></td>').text(data.dosageText));
    row.append($('<td></td>').text(data.whenText));
    row.append($('<td></td>').text(data.timeText));
    row.append($('<td></td>').text(data.duration_value + ' ' + data.duration));
    row.append($('<td></td>').text(data.route || ''));
    row.append($('<td></td>').text(data.medDiscount || ''));
    row.append($('<td></td>').text(data.shortNotes));

    row.children('td').css({ 'padding':'2px 4px','border':'1px solid #ccc','font-size':'12px','text-align':'center' });

    const actionTd = $('<td></td>').css({ 'text-align':'center','border':'1px solid #ccc' });
    const editIcon = $('<i class="fas fa-edit edit-btn" title="Edit"></i>').css({ 'cursor':'pointer','margin-right':'5px','color':'#007bff' }).data('index', index);
    const deleteIcon = $('<i class="fas fa-trash delete-btn" title="Delete"></i>').css({ 'cursor':'pointer','color':'red' });
    actionTd.append(editIcon).append(deleteIcon);
    row.append(actionTd);
    $('#medicineTable tbody').append(row);
    updateSerialNumbers();
}

function updateSerialNumbers() {
    $('#medicineTable tbody tr').each(function(i) { $(this).find('td.sno').text(i + 1); });
}

$(document).on('click', '.delete-btn', function() {
    const row = $(this).closest('tr');
    const index = row.index();
    medicineArray.splice(index, 1);
    row.remove();
    if ($('#medicineTable tbody tr').length === 0) { $('#medicineTable').remove(); tableCreated = false; }
    else updateSerialNumbers();
});

// Edit medicine row → populate top input fields and remove the row
$(document).on('click', '.edit-btn', function() {
    var rowIndex = $(this).closest('tr').index();
    var med = medicineArray[rowIndex];
    if (!med) return;

    // Populate top form fields
    $('#drugName').val(med.drugName);
    $('#medicineType').val(med.typeText);
    $('#unit').val(med.unitText);
    $('#dosage').val(med.dosageId);
    $('#when').val(med.whenId);
    $('#duration_value').val(med.duration_value);
    $('#duration').val(med.duration || 'Days');
    $('#grx_route').val(med.route || '');
    $('#notes').val(med.notes || '');
    $('#grx_med_concession').val(med.medConcessionId || '');

    // Load time options for the selected dosage, then set value
    getTimeForDose(med.dosageId, '');
    setTimeout(function() { $('#time').val(med.timeId); }, 400);

    toggleMedSpans();

    // Remove from array and table
    medicineArray.splice(rowIndex, 1);
    $('#medicineTable tbody tr').eq(rowIndex).remove();
    if ($('#medicineTable tbody tr').length === 0) {
        $('#medicineTable').remove();
        tableCreated = false;
    } else {
        updateMedIndexes();
        updateSerialNumbers();
    }
    editingIndex = null;
});

/* =====================================================================
   INVESTIGATION — with price + concession (same as prescription.php)
   ===================================================================== */
let investigationArray = [];
let editingInvestigationIndex = null;
let investigationTableCreated = false;

/* Price + concession calculation */
function grxUpdateTestPrice() {
    var investigationName = $('#investigation').val().trim().toUpperCase();
    var basePrice = 0;
    if (typeof allTests !== 'undefined' && Array.isArray(allTests)) {
        var test = allTests.find(function(t){ return t.test_name === investigationName; });
        if (test) basePrice = parseFloat(test.test_price) || 0;
    }
    var concessionType  = $('#grx_concession option:selected').data('type')  || '';
    var concessionValue = $('#grx_concession option:selected').data('value') || '';
    var discounted = basePrice;
    if (concessionType && concessionValue) {
        if (concessionType.toLowerCase() === 'percentage' || concessionType === '%')
            discounted = basePrice - (basePrice * parseFloat(concessionValue) / 100);
        else if (concessionType.toLowerCase() === 'fixed' || concessionType.toLowerCase() === 'amount')
            discounted = basePrice - parseFloat(concessionValue);
    }
    if (discounted < 0) discounted = 0;
    $('#test_price').val(discounted.toFixed(2));
    var html = '';
    if (basePrice > 0) {
        if (basePrice > discounted) {
            var offText = concessionType.toLowerCase().includes('percent') || concessionType === '%'
                ? ' <span style="color:#888;font-size:.9em;">('+parseFloat(concessionValue)+'% off)</span>'
                : ' <span style="color:#888;font-size:.9em;">(Rs '+(basePrice-discounted).toFixed(2)+' off)</span>';
            html = '<span style="text-decoration:line-through;color:#999;">Rs '+basePrice.toFixed(2)+'</span> '
                 + '<span style="color:#080;font-weight:bold;">Rs '+discounted.toFixed(2)+'/-</span>'+offText;
        } else {
            html = '<span style="color:#080;font-weight:bold;">Rs '+basePrice.toFixed(2)+'/-</span>';
        }
    }
    $('#grxPriceDisplay').html(html);
}
$('#investigation').on('input', grxUpdateTestPrice);
$('#grx_concession').on('change', grxUpdateTestPrice);

$(document).on('click', '.adding-form', function() {
    var investigation = $('#investigation').val().trim();
    var instruction   = $('#testnotes').val().trim();
    var price         = $('#test_price').val().trim() || '0';
    if (!investigation) { Swal.fire({ text:'Please fill investigation field before adding.', confirmButtonText:'OK' }); return; }
    var duplicate = investigationArray.some(function(i){ return i.investigation.toLowerCase() === investigation.toLowerCase(); });
    if (duplicate) { Swal.fire({ text:'This investigation is already added.', confirmButtonText:'OK' }); return; }

    var concessionId   = $('#grx_concession').val();
    var concessionName = '', concessionValue = '', concessionType = '', concessionDisplay = '';
    if (concessionId) {
        var sel = $('#grx_concession option:selected');
        concessionName  = sel.text();
        concessionValue = sel.data('value');
        concessionType  = sel.data('type');
        if (concessionType && concessionValue) {
            concessionDisplay = (concessionType.toLowerCase().includes('percent') || concessionType === '%')
                ? concessionName+' ('+concessionValue+'%)'
                : concessionName+' (₹'+concessionValue+')';
        } else { concessionDisplay = concessionName; }
    }

    var data = { investigation, instruction, price, concessionName, concessionValue, concessionType, concession: concessionDisplay };
    investigationArray.push(data);

    if (!investigationTableCreated) {
        var container = $('#investigationTableWrapper');
        container.css('overflow-x', 'auto');
        var table = $('<table id="investigationTable"></table>').css({ 'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px' });
        var thead = $('<thead></thead>').append($('<tr></tr>').append(
            $('<th>S.No</th>'), $('<th>Investigation</th>'), $('<th>Instruction</th>'),
            $('<th>Price</th>'), $('<th>Concession</th>'), $('<th>Action</th>')
        ));
        thead.find('th').css({ 'padding':'8px','border':'1px solid #ddd','background':'lightblue','font-weight':'bold','text-align':'center' });
        table.append(thead).append($('<tbody></tbody>'));
        container.append(table);
        investigationTableCreated = true;
    }
    appendInvestigationRow(data, investigationArray.length - 1);
    $('#investigation, #testnotes').val('');
    $('#grx_concession').val('');
    $('#test_price').val('');
    $('#grxPriceDisplay').html('');
    toggleTestSpans();
});

function appendInvestigationRow(data, index) {
    var row = $('<tr></tr>');
    row.append($('<td class="sno"></td>').text(index + 1));
    row.append($('<td class="inv-name"></td>').text(data.investigation));
    row.append($('<td class="inv-instruction"></td>').text(data.instruction));
    row.append($('<td class="inv-price"></td>').text(data.price || ''));
    row.append($('<td class="inv-concession"></td>').text(data.concession || ''));
    var actionTd = $('<td></td>').css({ 'text-align':'center','border':'1px solid #ccc' });
    var editBtn   = $('<button class="edit-investigation-inline btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></button>').data('index', index);
    var deleteBtn = $('<button class="delete-investigation-btn btn btn-sm btn-danger ms-1" title="Delete"><i class="fas fa-trash"></i></button>');
    actionTd.append(editBtn).append(deleteBtn);
    row.append(actionTd);
    row.find('td').css({ 'padding':'2px 4px','border':'1px solid #ccc','font-size':'12px','text-align':'center' });
    $('#investigationTable tbody').append(row);
    updateInvestigationSno();
}

function updateInvestigationSno() {
    $('#investigationTable tbody tr').each(function(i){ $(this).find('td.sno').text(i + 1); });
}

$(document).on('click', '.delete-investigation-btn', function() {
    var row   = $(this).closest('tr');
    var index = row.index();
    investigationArray.splice(index, 1);
    row.remove();
    if ($('#investigationTable tbody tr').length === 0) { $('#investigationTable').remove(); investigationTableCreated = false; }
    else updateInvestigationSno();
});

// Edit investigation row → populate top input fields and remove the row
$(document).on('click', '.edit-investigation-inline', function() {
    var rowIndex = $(this).closest('tr').index();
    var data = investigationArray[rowIndex];
    if (!data) return;

    // Populate top investigation fields
    $('#investigation').val(data.investigation);
    $('#testnotes').val(data.instruction || '');
    $('#test_price').val(data.price || '');

    // Restore concession selection
    if (data.concessionName) {
        $('#grx_concession option').filter(function() {
            return $(this).text() === data.concessionName;
        }).prop('selected', true);
    } else {
        $('#grx_concession').val('');
    }
    grxUpdateTestPrice();
    toggleTestSpans();

    // Remove from array and table
    investigationArray.splice(rowIndex, 1);
    $('#investigationTable tbody tr').eq(rowIndex).remove();
    if ($('#investigationTable tbody tr').length === 0) {
        $('#investigationTable').remove();
        investigationTableCreated = false;
    } else {
        updateInvestigationSno();
    }
    editingInvestigationIndex = null;
});

/* =====================================================================
   REVIEW AFTER — auto-calculate date from number + unit
   ===================================================================== */
$('#reviewInput').on('input', function(){ this.value = this.value.replace(/[^0-9]/g, ''); });

function grxFormatDate(d) {
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}
function grxUpdateReviewDate() {
    var num  = parseInt($('#reviewInput').val(), 10);
    var unit = $('#reviewSelect').val();
    var now  = new Date();
    if (!num || !unit || unit === 'Till Further Advice') {
        $('#reviewCalculatedDate').val(grxFormatDate(now)); return;
    }
    var d = new Date(now.getTime());
    if (unit === 'Days')   d.setDate(d.getDate() + num);
    else if (unit === 'Weeks')  d.setDate(d.getDate() + num * 7);
    else if (unit === 'Months') d.setMonth(d.getMonth() + num);
    $('#reviewCalculatedDate').val(grxFormatDate(d));
}
$('#reviewInput').on('input',  grxUpdateReviewDate);
$('#reviewSelect').on('change', grxUpdateReviewDate);

/* =====================================================================
   DATALIST AJAX — exact copy from prescription.php
   ===================================================================== */
var medicineList = [];
function getMedichines(id, value) {
    var org_id = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({ url:'ajax/Wprescripation/getMedichines.php', type:'post', data:{ org_id:org_id }, dataType:'json',
        success:function(data){
            var optionData = '';
            $.each(data, function(index, val){
                var displayName = val.medicine_name + ' - (' + val.scientific_name + ')';
                medicineList.push({ medicine_id:val.medicine_id, name:displayName });
                optionData += '<option value="'+displayName+'" data-id="'+val.medicine_id+'">'+displayName+'</option>';
            });
            $('#drugNameDatalist'+id).html(optionData);
            $('#edit_drugNameDatalist').html(optionData);
            if (value) $('#drugName'+id).val(value);
        }
    });
}

var medicineTypeList = [];
function getMedicineType(id, value) {
    $.ajax({ url:'ajax/Wprescripation/getMedicineType.php', type:'get', dataType:'json',
        success:function(data){
            medicineTypeList = [];
            var optionData = '';
            $.each(data, function(index, val){
                medicineTypeList.push({ type_id:val.type_id, type_name:val.type_name });
                optionData += '<option value="'+val.type_name+'">';
            });
            $('#medicineTypeDatalist').html(optionData);
            $('#edit_medicineTypeDatalist').html(optionData);
            setTimeout(function(){ $('#medicineType').val('Tab'); }, 1000);
            if (value) $('#medicineType').val(value);
        }
    });
}

var unitList = [];
function getUnit(id, value) {
    $.ajax({ url:'ajax/Wprescripation/getUnit.php', type:'get', dataType:'json',
        success:function(data){
            unitList = [];
            var optionData = '';
            $.each(data, function(key, val){
                unitList.push({ unit_id:val.unit_id, unit_name:val.unit_name });
                optionData += '<option value="'+val.unit_name+'">';
            });
            $('#unitDatalist').html(optionData);
            $('#edit_unitDatalist').html(optionData);
            if (value) $('#unit').val(value);
        }
    });
}

function getDosages(id, value) {
    $.ajax({ url:'ajax/Wprescripation/getDosages.php', type:'get', dataType:'json',
        success:function(data){
            var optionData = '<option value="">Select Dosages</option>';
            $.each(data, function(key, val){
                optionData += '<option value="'+val.dosage_id+'">'+val.dosages+'</option>';
            });
            $('#dosage').html(optionData);
            $('#edit_dosage').html(optionData);
            if (value) { $('#dosage').val(value); $('#edit_dosage').val(value); }
        }
    });
}

function getInTakePeriod(id, value) {
    $.ajax({ url:'ajax/Wprescripation/getInTakePeriod.php', type:'get', dataType:'json',
        success:function(data){
            var optionData = '<option value="">Select Intake Period</option>';
            $.each(data, function(key, val){
                optionData += '<option value="'+val.intake_id+'">'+val.intake_name+'</option>';
            });
            $('#when').html(optionData);
            $('#edit_when').html(optionData);
            if (value) { $('#when').val(value); $('#edit_when').val(value); }
        }
    });
}

function handleDosageChange(selectedDose) {
    if (selectedDose !== "") getTimeForDose(selectedDose);
    else $("#time").html('<option value="">Select Time</option>');
}

function getTimeForDose(doseId, id) {
    $.ajax({ url:'ajax/Wprescripation/getTime.php', type:'get', data:{ dose_id:doseId }, dataType:'json',
        success:function(data){
            var timeOptions = data.length ? '' : '<option value="">No available time</option>';
            $.each(data, function(index, timeItem){ timeOptions += '<option value="'+timeItem.time_id+'">'+timeItem.time+'</option>'; });
            $('#time'+(id||'')).html(timeOptions).trigger('change');
        }
    });
}

function getmodalTimeForDose(doseId) {
    $.ajax({ url:'ajax/Wprescripation/getTime.php', type:'get', data:{ dose_id:doseId }, dataType:'json',
        success:function(data){
            var timeOptions = data.length ? '' : '<option value="">No available time</option>';
            $.each(data, function(index, timeItem){ timeOptions += '<option value="'+timeItem.time_id+'">'+timeItem.time+'</option>'; });
            $('#edit_time').html(timeOptions);
        }
    });
}

var allTests = [];
function gettests(value) {
    $.ajax({ url:'ajax/Wprescripation/getTests.php', type:'get', dataType:'json',
        success:function(data){
            allTests = [];
            let optionData = '';
            $.each(data, function(index, test){
                allTests.push({ test_id:test.test_id, test_name:test.test_name.trim().toUpperCase(), test_price:test.test_price });
                optionData += '<option value="'+test.test_name.trim().toUpperCase()+'">';
            });
            $('#investigationDatalist').html(optionData);
            $('#edit_investigationDatalist').html(optionData);
            if (value) $('#investigation').val(value.trim().toUpperCase());
        }
    });
}

function getmedicinetypeandunit(input, id) {
    const selectedValue = input.value;
    const option = [...document.querySelectorAll('#drugNameDatalist option')]
        .find(function(opt){ return opt.value.trim().toUpperCase() === selectedValue.trim().toUpperCase(); });
    if (!option || !option.dataset.id) return;
    const medicine_id = option.dataset.id;
    let currentRow = $(input).closest('.row');
    $.ajax({ url:'ajax/rxgroup/getMedicineDetails.php', type:'POST', data:{ medicine_id:medicine_id }, dataType:'json',
        success:function(results){
            if (results.type_name) currentRow.find('.medicinetype').val(results.type_name).trigger('change');
            if (results.dosage && results.dosage !== '0' && results.dosage !== '') currentRow.find('.unit').val(results.dosage).trigger('change');
        }
    });
}

/* =====================================================================
   SAVE
   ===================================================================== */
$(document).on('click', '#saveGrxBtn', function(){
    var name = $('#grx_patientIdName').val().trim();
    if (!name) { Swal.fire({ icon:'warning', text:'Patient Name is required.', confirmButtonText:'OK' }); return; }
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    var reviewAfterVal = $('#reviewInput').val() ? $('#reviewInput').val()+' '+$('#reviewSelect').val() : ($('#reviewCalculatedDate').val() ? 'By '+$('#reviewCalculatedDate').val() : '');

    $.ajax({
        url: 'ajax/gynaec_prescription/saveGynaecRx.php', type: 'POST', dataType: 'json',
        data: {
            gynaec_rx_id:     $('#grx_rx_id').val(),
            org_id:           orgId,
            appointment_id:   $('#grx_appoint_id_val').val(),
            patient_id:       $('#grx_patient_id_val').val(),
            patient_name:     name,
            age:              $('#grx_age').val(),
            gender:           $('input[name="grx_gender"]:checked').val() || 'Female',
            mobile:           $('#grx_mobile_number').val(),
            final_diagnosis:  $('#grx_final_diagnosis').val(),
            chief_complaints: $('#grx_chief_complaints').val(),
            gynaec_history:   $('#grx_gynaec_history').val(),
            obstetric_history:$('#grx_obstetric_history').val(),
            family_history:   $('#grx_family_history').val(),
            personal_history: $('#grx_personal_history').val(),
            general_examination:     $('#grx_general_examination').val(),
            previous_investigations: $('#grx_previous_investigations').val(),
            menstrual_history: $('#grx_menstrual_history').val(),
            lmp:              $('#grx_lmp').val(),
            pmc:              $('#grx_pmc').val(),
            edd:              $('#grx_edd').val(),
            risk_factors:     $('#grx_risk_factors').val(),
            scan_type:        $('#grx_scan_type').val(),
            scan_date:        $('#grx_scan_date').val(),
            scan_findings:    $('#grx_scan_findings').val(),
            scan_remarks:     $('#grx_scan_remarks').val(),
            advice:           $('#advise').val(),
            plan:             $('#personal_note').val(),
            review_after:     reviewAfterVal,
            reviewafterdate:  $('#reviewCalculatedDate').val(),
            review_notes:     $('#grx_review_notes').val(),
            patient_data:     $('#grx_patient_data').val(),
            bpSit_systolic:   $('#grx_bpSit_systolic').val(),
            bpSit_diastolic:  $('#grx_bpSit_diastolic').val(),
            bpStand_systolic: $('#grx_bpStand_systolic').val(),
            bpStand_diastolic:$('#grx_bpStand_diastolic').val(),
            weight:           $('#grx_weight').val(),
            height:           $('#grx_height').val(),
            bmi:              $('#grx_bmi').val(),
            grbs:             $('#grx_grbs').val(),
            heart_rate:       $('#grx_heart_rate').val(),
            temperature:      $('#grx_temperature').val(),
            respiration_rate: $('#grx_respiration_rate').val(),
            spO2:             $('#grx_spO2').val(),
            patient_overview: $('#grx_patient_overview').val(),
            medicines:        JSON.stringify(medicineArray),
            investigations:   JSON.stringify(investigationArray.map(function(i){
                return { investigation_name:i.investigation, instructions:i.instruction,
                         price:i.price||'0', concession:i.concession||'' };
            }))
        },
        success: function(resp){
            if (resp.success) {
                Swal.fire('', resp.message, 'success').then(function(){ grxDoReset(); loadGrxHistory(); });
            } else {
                Swal.fire('Error', resp.message, 'error');
            }
        }
    });
});

/* =====================================================================
   HISTORY
   ===================================================================== */
function loadGrxHistory(){
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({ url:'ajax/gynaec_prescription/getGynaecRxList.php', type:'POST', data:{ org_id:orgId },
        success:function(html){
            $('#grxHistoryContainer').html(html);
            if ($.fn.DataTable.isDataTable('#gynaecRxTable')) $('#gynaecRxTable').DataTable().destroy();
            // Skip DataTable init when there are no real data rows (placeholder uses colspan)
            if ($('#gynaecRxTable tbody tr').length <= 1 && $('#gynaecRxTable tbody td[colspan]').length) return;
            var exportCols = <?= ($SessionUserId=="1") ? '[0,4,5,6,7,8,9,10,11]' : '[0,4,5,6,7,8,9,10]' ?>;
            $('#gynaecRxTable').DataTable({
                retrieve: true,
                order: [[0, 'desc']],
                pageLength: 10,
                dom: 'lBrftip',
                buttons: [
                    { extend:'copy',  exportOptions:{ columns: exportCols } },
                    { extend:'excel', exportOptions:{ columns: exportCols } },
                    { extend:'csv',   exportOptions:{ columns: exportCols } },
                    { extend:'pdf',   exportOptions:{ columns: exportCols } },
                    { extend:'print', exportOptions:{ columns: exportCols } }
                ]
            });
        }
    });
}

/* =====================================================================
   EDIT — clicking Edit in history table loads record into the form
   (same fill logic as auto-populate on patient selection)
   ===================================================================== */
$(document).on('click', '.edit-gynaec-btn', function(){
    var id = $(this).data('id');
    $.ajax({ url:'ajax/gynaec_prescription/getGynaecRxById.php', type:'POST', dataType:'json',
        data:{ gynaec_rx_id: id },
        success: function(resp){
            if (!resp.success){ Swal.fire('Error','Record not found','error'); return; }
            grxDoReset();
            // Fill patient dropdowns from saved data
            var d = resp.data;
            if ($('#grx_patientIdName option[value="'+d.patient_name+'"]').length === 0)
                $('#grx_patientIdName').append('<option value="'+d.patient_name+'">'+d.patient_name+'</option>');
            $('#grx_patientIdName').val(d.patient_name);
            $('#grx_age').val(d.age);
            $('input[name="grx_gender"][value="'+(d.gender||'Female')+'"]').prop('checked', true);
            if ($('#grx_mobile_number option[value="'+d.mobile+'"]').length)
                $('#grx_mobile_number').val(d.mobile);
            grxFillPrescriptionData(resp);
            $('html,body').animate({ scrollTop:$('#grxFormCard').offset().top-20 }, 400);
        }
    });
});

/* =====================================================================
   DELETE
   ===================================================================== */
$(document).on('click', '.delete-gynaec-btn', function(){
    var id = $(this).data('id');
    Swal.fire({ title:'Delete this prescription?', text:'This cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Delete', cancelButtonText:'Cancel', confirmButtonColor:'#d33' })
    .then(function(result){
        if (!result.isConfirmed) return;
        $.ajax({ url:'ajax/gynaec_prescription/deleteGynaecRx.php', type:'POST', dataType:'json', data:{ gynaec_rx_id:id },
            success:function(resp){ if(resp.success) Swal.fire('',resp.message,'success').then(loadGrxHistory); else Swal.fire('Error',resp.message,'error'); }
        });
    });
});

/* =====================================================================
   VIEW / PRINT
   ===================================================================== */
var _grxViewUrl = <?= json_encode(
    (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http')
    .'://'.$_SERVER['HTTP_HOST']
    .rtrim(dirname($_SERVER['SCRIPT_NAME']),'/')
    .'/ajax/gynaec_prescription/viewGynaecRx.php'
) ?>;

function viewGynaecRx(id){
    if (!id){ Swal.fire('','Invalid ID.','error'); return; }
    var f = document.createElement('form');
    f.method='POST'; f.action=_grxViewUrl; f.target='_blank';
    var inp = document.createElement('input'); inp.type='hidden'; inp.name='gynaec_rx_id'; inp.value=id;
    f.appendChild(inp); document.body.appendChild(f); f.submit(); document.body.removeChild(f);
}

<?php if($SessionUserId=="1"): ?>
$(document).on('change','#grx_org',function(){ loadGrxHistory(); getMedichines(''); });
<?php endif; ?>

/* =====================================================================
   ASSESSMENT FIELD TEMPLATES
   ===================================================================== */
function grxLoadFieldTemplates(fieldType) {
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/gynaec_prescription/getGynaecTemplates.php', type: 'GET',
        data: { field_type: fieldType, org_id: orgId }, dataType: 'json',
        success: function(resp) {
            var $drop = $('#grxTplDrop_' + fieldType);
            $drop.empty();
            if (resp.success && resp.templates.length > 0) {
                resp.templates.forEach(function(t) {
                    $drop.append(
                        '<div class="mb-2 p-2 border rounded d-flex justify-content-between align-items-start grx-tpl-option" '+
                        'data-content="'+encodeURIComponent(t.template_data)+'" data-id="'+t.id+'" data-field="'+fieldType+'" style="cursor:pointer;">'+
                        '<div><strong>'+$('<span>').text(t.template_name).html()+'</strong>'+
                        '<p class="mb-0 small text-muted">'+$('<span>').text(t.template_data).html().substring(0,60)+'...</p></div>'+
                        '<div class="d-flex flex-column align-items-center ms-2">'+
                        '<i class="fas fa-edit mb-1 text-primary grx-tpl-edit" data-id="'+t.id+'" data-field="'+fieldType+'" style="cursor:pointer;"></i>'+
                        '<i class="fas fa-trash-alt text-danger grx-tpl-delete" data-id="'+t.id+'" style="cursor:pointer;"></i>'+
                        '</div></div>'
                    );
                });
            } else {
                $drop.html('<div class="text-muted p-2 small">No templates saved.</div>');
            }
        }
    });
}

// Open dropdown → load templates
$(document).on('click', '.grx-tpl-history', function() {
    var field = $(this).data('field');
    grxLoadFieldTemplates(field);
});

// Click template option → append to textarea
$(document).on('click', '.grx-tpl-option', function(e) {
    if ($(e.target).closest('.grx-tpl-edit,.grx-tpl-delete').length) return;
    var text   = decodeURIComponent($(this).data('content'));
    var field  = $(this).data('field');
    var $ta    = field === 'advice' ? $('#advise') : $('#grx_' + field);
    var cur    = $ta.val().trim();
    $ta.val(cur ? cur + '\n' + text : text);
    // close dropdown
    $(this).closest('.dropdown-menu').dropdown('hide');
});

// + icon → save current textarea content as template
$(document).on('click', '.grx-tpl-add', function() {
    var field  = $(this).data('field');
    var label  = $(this).data('label');
    var content = (field === 'advice' ? $('#advise') : $('#grx_' + field)).val().trim();
    if (!content) { Swal.fire('Warning','Please enter content in "'+label+'" first.','warning'); return; }
    $('#grxTplEditId').val('');
    $('#grxTplFieldType').val(field);
    $('#grxTplModalTitle').text('Save Template — '+label);
    $('#grxTplName').val('');
    $('#grxTplPreview').val(content);
    $('#grxTplModal').modal('show');
});

// Edit template (works for all fields including advice)
$(document).on('click', '.grx-tpl-edit', function(e) {
    e.stopPropagation();
    var id    = $(this).data('id');
    var field = $(this).data('field');
    var $item = $(this).closest('.grx-tpl-option');
    var name  = $item.find('strong').text();
    var data  = decodeURIComponent($item.data('content'));
    $('#grxTplEditId').val(id);
    $('#grxTplFieldType').val(field);
    $('#grxTplModalTitle').text('Edit Template');
    $('#grxTplName').val(name);
    $('#grxTplPreview').val(data);
    $('#grxTplModal').modal('show');
});

// Delete template
$(document).on('click', '.grx-tpl-delete', function(e) {
    e.stopPropagation();
    var id    = $(this).data('id');
    var $item = $(this).closest('.grx-tpl-option');
    Swal.fire({ title:'Delete template?', icon:'warning', showCancelButton:true,
        confirmButtonText:'Delete', confirmButtonColor:'#d33' })
    .then(function(res) {
        if (!res.isConfirmed) return;
        $.ajax({ url:'ajax/gynaec_prescription/deleteGynaecTemplate.php', type:'POST',
            data:{ id:id }, dataType:'json',
            success:function(r){ if(r.success) $item.remove(); else Swal.fire('Error',r.error,'error'); }
        });
    });
});

// Save/update template
$('#grxTplSaveBtn').on('click', function() {
    var name    = $('#grxTplName').val().trim();
    var content = $('#grxTplPreview').val().trim();
    var field   = $('#grxTplFieldType').val();
    var editId  = $('#grxTplEditId').val();
    var orgId   = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    if (!name) { Swal.fire('Warning','Please enter a template name.','warning'); return; }
    $.ajax({
        url: 'ajax/gynaec_prescription/addGynaecTemplate.php', type: 'POST', dataType: 'json',
        data: { field_type:field, template_name:name, template_data:content, org_id:orgId, edit_id:editId },
        success: function(r) {
            if (r.success) {
                Swal.fire('', editId ? 'Template updated.' : 'Template saved.', 'success');
                $('#grxTplModal').modal('hide');
                grxLoadFieldTemplates(field);
            } else { Swal.fire('Error', r.error || 'Failed', 'error'); }
        }
    });
});

/* =====================================================================
   INVESTIGATION TEMPLATES (reuses prescription's test_group table)
   ===================================================================== */
function grxLoadInvTemplates() {
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/Wprescripation/get_investigationTemplates.php', type: 'GET',
        data: { org_id: orgId }, dataType: 'json',
        success: function(resp) {
            var $drop = $('#grxInvTplDropdown');
            $drop.empty();
            if (resp.success && resp.templates.length > 0) {
                resp.templates.forEach(function(t) {
                    var testNames = Array.isArray(t.test_id) ? t.test_id.map(function(x){ return x.investigation; }).join(', ') : '';
                    $drop.append(
                        '<div class="mb-2 p-2 border rounded grx-inv-tpl-option" '+
                        'data-tests=\''+JSON.stringify(t.test_id)+'\' '+
                        'data-group-id="'+t.test_group_id+'" data-group-price="'+t.test_group_price+'" '+
                        'style="cursor:pointer;">'+
                        '<strong>'+$('<span>').text(t.test_group_name).html()+'</strong>'+
                        '<p class="mb-0 small text-muted">'+$('<span>').text(testNames).html()+'</p>'+
                        '</div>'
                    );
                });
            } else {
                $drop.html('<div class="text-muted p-2 small">No investigation templates.</div>');
            }
        }
    });
}

$('.investgationhistory').on('click', grxLoadInvTemplates);

// Click investigation template → bulk-add all tests
$(document).on('click', '.grx-inv-tpl-option', function() {
    var groupId   = $(this).data('group-id');
    var dataTests = $(this).attr('data-tests');
    var testList  = [];
    try { testList = typeof dataTests === 'string' ? JSON.parse(dataTests) : dataTests; } catch(e){ return; }
    if (!testList || !testList.length) return;
    // Skip group if already added
    var alreadyAdded = investigationArray.some(function(inv){ return inv.groupId === groupId; });
    if (alreadyAdded) { Swal.fire('','This investigation template is already added.','info'); return; }

    testList.forEach(function(t) {
        var inv = (t.investigation || t.investigation_name || '').trim().toUpperCase();
        if (!inv) return;
        var dup = investigationArray.some(function(i){ return i.investigation.toLowerCase() === inv.toLowerCase(); });
        if (dup) return;
        var data = { investigation: inv, instruction: t.instruction || '', price: t.price || '0',
                     concession:'', concessionName:'', concessionValue:'', concessionType:'', groupId: groupId };
        investigationArray.push(data);
        if (!investigationTableCreated) {
            var container = $('#investigationTableWrapper');
            container.css('overflow-x', 'auto');
            var table = $('<table id="investigationTable"></table>').css({'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px'});
            var thead = $('<thead></thead>').append($('<tr></tr>').append(
                $('<th>S.No</th>'),$('<th>Investigation</th>'),$('<th>Instruction</th>'),
                $('<th>Price</th>'),$('<th>Concession</th>'),$('<th>Action</th>')
            ));
            thead.find('th').css({'padding':'8px','border':'1px solid #ddd','background':'lightblue','font-weight':'bold','text-align':'center'});
            table.append(thead).append($('<tbody></tbody>'));
            container.append(table);
            investigationTableCreated = true;
        }
        appendInvestigationRow(data, investigationArray.length - 1);
    });
    $(this).closest('.dropdown-menu').dropdown('hide');
});

// Save investigation template button
$('#grxAddInvTplBtn').on('click', function() {
    if (!investigationArray.length) { Swal.fire('Warning','Please add at least one investigation first.','warning'); return; }
    $('#grxInvTplName').val(''); $('#grxInvTplPrice').val('');
    $('#grxInvTplModal').modal('show');
});

$('#grxInvTplSaveBtn').on('click', function() {
    var name  = $('#grxInvTplName').val().trim();
    var price = $('#grxInvTplPrice').val().trim();
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    if (!name || !price) { Swal.fire('Warning','Please fill template name and total price.','warning'); return; }
    $.ajax({
        url: 'ajax/Wprescripation/addinvestigationTemplate.php', method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ template_name:name, total_price:price, tests:investigationArray, organizations:orgId }),
        dataType: 'json',
        success: function(r) {
            if (r.success) {
                Swal.fire('','Template saved successfully!','success');
                $('#grxInvTplModal').modal('hide');
            } else { Swal.fire('Error','Error saving template.','error'); }
        }
    });
});

/* =====================================================================
   VITALS + FEE STATUS — filled from appointment after patient selection
   ===================================================================== */
function grxUpdateVitalsAndFee(appointId) {
    if (!appointId) { $('#grxPaymentStatusBadge').hide(); return; }
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/Wprescripation/getvitalsdata.php', type: 'POST', dataType: 'json',
        data: { appointment_id: appointId, org_id: orgId },
        success: function(data) {
            if (!data || !data.appointment) return;
            var v = data.appointment;
            // Fee status badge
            var $badge = $('#grxPaymentStatusBadge');
            if (v.invoice_payment === '1') {
                $badge.text('Consultation: Paid').css({ background:'#198754', color:'#fff' }).show();
            } else {
                $badge.text('Consultation: Pending').css({ background:'#fd7e14', color:'#fff' }).show();
            }
            // Vitals
            $('#grx_bpSit_systolic').val(v.bpSit_systolic || '');
            $('#grx_bpSit_diastolic').val(v.bpSit_diastolic || '');
            $('#grx_bpStand_systolic').val(v.bpStand_systolic || '');
            $('#grx_bpStand_diastolic').val(v.bpStand_diastolic || '');
            $('#grx_weight').val(v.weight || '');
            $('#grx_height').val(v.height || '');
            $('#grx_bmi').val(v.bmi || '');
            $('#grx_grbs').val(v.grbs || '');
            $('#grx_heart_rate').val(v.heart_rate || '');
            $('#grx_temperature').val(v.temperature || '');
            $('#grx_respiration_rate').val(v.respiration_rate || '');
            $('#grx_spO2').val(v.spO2 || '');
            $('#grx_patient_overview').val(v.patient_overview || '');
        }
    });
}

// BMI auto-calculate for gynaec vitals
$('#grx_weight, #grx_height').on('input', function() {
    var w = parseFloat($('#grx_weight').val());
    var h = parseFloat($('#grx_height').val());
    if (w > 0 && h > 0) {
        var hm = h / 100;
        $('#grx_bmi').val((w / (hm * hm)).toFixed(1));
    } else {
        $('#grx_bmi').val('');
    }
});

/* =====================================================================
   COPY FROM DIAGNOSIS — system-wide gynaec diagnosis template library
   ===================================================================== */
var _grxDxTemplateCache = null;
var _grxDxTemplateLoading = false;

function grxLoadDxDropdown() {
    if (_grxDxTemplateCache !== null) return;
    if (_grxDxTemplateLoading) return;
    _grxDxTemplateLoading = true;
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/gynaec_prescription/getGynaecDxTemplates.php', type: 'POST',
        data: { organizations: orgId }, dataType: 'json',
        success: function(r) {
            _grxDxTemplateLoading = false;
            _grxDxTemplateCache = (r.success && r.templates) ? r.templates : [];
        },
        error: function() { _grxDxTemplateLoading = false; _grxDxTemplateCache = []; }
    });
}

function renderGrxDxTemplates(templates) {
    var $items = $('#grxDxItems');
    $items.empty();
    if (!templates.length) {
        $items.html('<div class="px-2 py-1 text-muted" style="font-size:12px;">No diagnosis templates found.</div>');
        return;
    }
    templates.forEach(function(t) {
        var label = $('<span>').text(t.final_diagnosis).html();
        var sub   = $('<span>').text((t.patient_name || '') + (t.rx_date ? ' · ' + t.rx_date : '')).html();
        $items.append(
            '<li><a class="dropdown-item grx-dx-item py-1" href="javascript:void(0)" style="font-size:12px;white-space:normal;" data-id="'+t.gynaec_rx_id+'">'
            + '<strong>'+label+'</strong><br><span class="text-muted" style="font-size:11px;">'+sub+'</span></a></li>'
        );
    });
}

function showGrxDxTemplateBtn() {
    if (_grxDxTemplateCache && _grxDxTemplateCache.length) {
        renderGrxDxTemplates(_grxDxTemplateCache);
        $('#grxDxDropdownGroup').removeClass('d-none');
    } else {
        var _wait = setInterval(function() {
            if (_grxDxTemplateCache !== null) {
                clearInterval(_wait);
                if (_grxDxTemplateCache.length) {
                    renderGrxDxTemplates(_grxDxTemplateCache);
                    $('#grxDxDropdownGroup').removeClass('d-none');
                }
            }
        }, 200);
    }
}

// Search filter in diagnosis dropdown
$('#grxDxSearch').on('input', function() {
    var q = $(this).val().toLowerCase();
    $('#grxDxItems .grx-dx-item').each(function() {
        $(this).closest('li').toggle($(this).text().toLowerCase().indexOf(q) >= 0);
    });
});
// Keep dropdown open only while typing in the search box (not for entire menu)
$('#grxDxSearch').on('click keydown', function(e) { e.stopPropagation(); });

// Click a diagnosis → confirm, then copy clinical fields + medicines + investigations
$(document).on('click', '.grx-dx-item', function(e) {
    e.preventDefault();
    var rxId    = $(this).data('id');
    var diagName = $(this).find('strong').text().trim() || 'this diagnosis';
    if (!rxId) return;
    Swal.fire({
        title: 'Copy Prescription Template?',
        html: 'Copy <strong>' + diagName + '</strong> assessment, medicines &amp; investigations into the current prescription?',
        icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Copy'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        $.ajax({
            url: 'ajax/gynaec_prescription/getGynaecRxById.php', type: 'POST', dataType: 'json',
            data: { gynaec_rx_id: rxId },
            success: function(resp) {
                if (!resp.success) { Swal.fire('Error', 'Prescription not found', 'error'); return; }
                var d = resp.data;
                // Assessment fields (not personal data or vitals)
                $('#grx_final_diagnosis').val(d.final_diagnosis || '');
                $('#grx_chief_complaints').val(d.chief_complaints || '');
                $('#grx_gynaec_history').val(d.gynaec_history || '');
                $('#grx_obstetric_history').val(d.obstetric_history || '');
                $('#grx_family_history').val(d.family_history || '');
                $('#grx_personal_history').val(d.personal_history || '');
                $('#grx_general_examination').val(d.general_examination || '');
                $('#grx_previous_investigations').val(d.previous_investigations || '');
                $('#grx_menstrual_history').val(d.menstrual_history || '');
                $('#grx_pmc').val(d.pmc || '');
                $('#grx_risk_factors').val(d.risk_factors || '');
                $('#grx_scan_type').val(d.scan_type || '');
                $('#grx_scan_findings').val(d.scan_findings || '');
                $('#grx_scan_remarks').val(d.scan_remarks || '');
                $('#advise').val(d.advice || '');
                $('#personal_note').val(d.plan || '');
                if (d.review_after) {
                    var rvParts = d.review_after.split(' ');
                    $('#reviewInput').val(rvParts[0] || '');
                    $('#reviewSelect').val(rvParts[1] || '');
                    grxUpdateReviewDate();
                }
                // Clear existing medicine table, then refill
                medicineArray = [];
                $('#medicineTableWrapper').empty();
                tableCreated = false;
                if (resp.medicines && resp.medicines.length) {
                    resp.medicines.forEach(function(m) {
                        var medData = {
                            drugName: m.drugName||m.medicine_name||'', typeText: m.typeText||m.type_text||'',
                            unitText: m.unitText||m.unit_text||'',     dosageId: m.dosageId||m.dosage_id||'',
                            dosageText: m.dosageText||m.dosages||'',   whenId: m.whenId||m.when_id||'',
                            whenText: m.whenText||m.when||'',          timeId: m.timeId||m.time_id||'',
                            timeText: m.timeText||m.time||'',          duration_value: m.duration_value||'',
                            duration: m.duration||'Days',              route: m.route||'',
                            notes: m.notes||m.instructions||'',
                            medConcessionId: m.medConcessionId||'', medConcessionName: m.medConcessionName||''
                        };
                        var shortNotes = medData.notes.length > 10 ? medData.notes.substring(0,10)+'...' : medData.notes;
                        var medDiscount = medData.medConcessionName || '';
                        medicineArray.push(medData);
                        if (!tableCreated) {
                            var container = $('#medicineTableWrapper'); container.css('overflow-x','auto');
                            var table = $('<table id="medicineTable"></table>').css({'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px'});
                            var thead = $('<thead></thead>').append($('<tr></tr>').append($('<th>Drag and Drop</th>'),$('<th>S.No</th>'),$('<th>Type</th>'),$('<th>Medicine</th>'),$('<th>Unit</th>'),$('<th>Dosage</th>'),$('<th>In-take</th>'),$('<th>Time</th>'),$('<th>Duration</th>'),$('<th>Route</th>'),$('<th>Discount</th>'),$('<th>Note</th>'),$('<th>Action</th>')));
                            thead.find('th').css({'padding':'8px','border':'1px solid #ddd','background':'lightblue','text-align':'center','font-weight':'bold'});
                            table.append(thead).append($('<tbody></tbody>'));
                            container.append(table); tableCreated = true;
                            $('#medicineTable tbody').sortable({ handle:'.drag-handle', axis:'y', cursor:'grabbing', update:function(){ var newOrder=[]; $('#medicineTable tbody tr').each(function(){ var idx=parseInt($(this).data('med-index')); if(!isNaN(idx)) newOrder.push(medicineArray[idx]); }); medicineArray=newOrder; updateMedIndexes(); updateSerialNumbers(); } });
                        }
                        appendRowToTable({ drugName:medData.drugName, typeText:medData.typeText, unitText:medData.unitText, dosageText:medData.dosageText, whenText:medData.whenText, timeText:medData.timeText, duration_value:medData.duration_value, duration:medData.duration, route:medData.route, medDiscount:medDiscount, shortNotes:shortNotes }, medicineArray.length-1);
                    });
                }
                // Clear existing investigation table, then refill
                investigationArray = [];
                $('#investigationTableWrapper').empty();
                investigationTableCreated = false;
                if (resp.investigations && resp.investigations.length) {
                    resp.investigations.forEach(function(inv) {
                        var data = {
                            investigation: inv.investigation_name||inv.investigation||'',
                            instruction:   inv.instructions||inv.instruction||'',
                            price:         inv.price||'0',
                            concession:'', concessionName:'', concessionValue:'', concessionType:''
                        };
                        investigationArray.push(data);
                        if (!investigationTableCreated) {
                            var container = $('#investigationTableWrapper'); container.css('overflow-x','auto');
                            var table = $('<table id="investigationTable"></table>').css({'font-size':'12px','width':'100%','border-collapse':'collapse','border':'1px solid #ccc','margin-top':'10px'});
                            var thead = $('<thead></thead>').append($('<tr></tr>').append($('<th>S.No</th>'),$('<th>Investigation</th>'),$('<th>Instruction</th>'),$('<th>Price</th>'),$('<th>Concession</th>'),$('<th>Action</th>')));
                            thead.find('th').css({'padding':'8px','border':'1px solid #ddd','background':'lightblue','font-weight':'bold','text-align':'center'});
                            table.append(thead).append($('<tbody></tbody>')); container.append(table);
                            investigationTableCreated = true;
                        }
                        appendInvestigationRow(data, investigationArray.length-1);
                    });
                }
                Swal.fire({icon:'success', toast:true, position:'top-end', showConfirmButton:false, timer:1800,
                    title:'Template copied! Adjust fields as needed.'});
            }
        });
    });
});

// Reset dx cache when org changes
<?php if($SessionUserId=="1"): ?>
$(document).on('change','#grx_org', function() {
    _grxDxTemplateCache = null; _grxDxTemplateLoading = false;
    $('#grxDxDropdownGroup').addClass('d-none');
    grxLoadDxDropdown();
});
<?php endif; ?>

/* =====================================================================
   INSTRUCTION TEMPLATES — for #notes (medicine) and #testnotes (investigation)
   ===================================================================== */
function grxLoadInstrTemplates(type, $list) {
    var orgId = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    $.ajax({
        url: 'ajax/Wprescripation/getInstrTemplates.php', type: 'GET',
        data: { type: type, org_id: orgId }, dataType: 'json',
        success: function(r) {
            $list.empty();
            if (!r.success || !r.templates.length) {
                $list.append('<li class="px-2 py-1 text-muted" style="font-size:12px;">No templates saved.</li>');
            } else {
                r.templates.forEach(function(t) {
                    $list.append(
                        '<li class="d-flex justify-content-between align-items-start px-2 py-1 border-bottom grx-instr-item" '+
                        'style="cursor:pointer;" data-text="'+encodeURIComponent(t.template_data)+'" data-id="'+t.it_id+'">'+
                        '<span style="font-size:12px;">'+$('<span>').text(t.template_name).html()+'</span>'+
                        '<i class="fas fa-trash-alt text-danger ms-2 grx-instr-delete" data-id="'+t.it_id+'" style="cursor:pointer;font-size:11px;"></i>'+
                        '</li>'
                    );
                });
            }
        }
    });
}

// Toggle instruction template dropdown
$(document).on('click', '.grx-instrhistory-med', function(e) {
    e.stopPropagation();
    var $drop = $(this).next('.grx-instr-dropdown-med');
    var isOpen = $drop.is(':visible');
    $('.grx-instr-template-dropdown').hide();
    if (!isOpen) { $drop.show(); grxLoadInstrTemplates('medicine', $drop); }
});
$(document).on('click', '.grx-instrhistory-inv', function(e) {
    e.stopPropagation();
    var $drop = $(this).next('.grx-instr-dropdown-inv');
    var isOpen = $drop.is(':visible');
    $('.grx-instr-template-dropdown').hide();
    if (!isOpen) { $drop.show(); grxLoadInstrTemplates('investigation', $drop); }
});
$(document).on('click', function() { $('.grx-instr-template-dropdown').hide(); });

// Apply template
$(document).on('click', '.grx-instr-item', function(e) {
    if ($(e.target).hasClass('grx-instr-delete')) return;
    var text = decodeURIComponent($(this).data('text'));
    var $drop = $(this).closest('.grx-instr-template-dropdown');
    var $ta = $drop.hasClass('grx-instr-dropdown-med') ? $('#notes') : $('#testnotes');
    var cur = $ta.val().trim();
    $ta.val(cur ? cur + '\n' + text : text);
    $drop.hide();
});

// Delete instruction template
$(document).on('click', '.grx-instr-delete', function(e) {
    e.stopPropagation();
    var id   = $(this).data('id');
    var $item = $(this).closest('.grx-instr-item');
    var $drop = $(this).closest('.grx-instr-template-dropdown');
    var isMed = $drop.hasClass('grx-instr-dropdown-med');
    Swal.fire({ title:'Delete template?', icon:'warning', showCancelButton:true,
        confirmButtonText:'Delete', confirmButtonColor:'#d33' })
    .then(function(res) {
        if (!res.isConfirmed) return;
        $.ajax({ url:'ajax/Wprescripation/deleteInstrTemplate.php', type:'POST',
            data:{ it_id:id }, dataType:'json',
            success:function(r){ if(r.success) { $item.remove(); } else { Swal.fire('Error',r.error,'error'); } }
        });
    });
});

// Open save modal
$(document).on('click', '.grxAddInstrTemplateBtn', function() {
    var target = $(this).data('target');
    var content = target === 'med' ? $('#notes').val().trim() : $('#testnotes').val().trim();
    var label   = target === 'med' ? 'Medicine Instructions' : 'Investigation Instructions';
    if (!content) { Swal.fire('Warning','Please enter content in "'+label+'" first.','warning'); return; }
    $('#grxInstrTplTarget').val(target);
    $('#grxInstrTemplateModalTitle').text('Save Template — '+label);
    $('#grxInstrTplName').val('');
    $('#grxInstrTplPreview').val(content);
    $('#grxInstrTemplateModal').modal('show');
});

// Save instruction template
$('#grxInstrTplSaveBtn').on('click', function() {
    var target  = $('#grxInstrTplTarget').val();
    var name    = $('#grxInstrTplName').val().trim();
    var content = $('#grxInstrTplPreview').val().trim();
    var type    = target === 'med' ? 'medicine' : 'investigation';
    var orgId   = $('#grx_org').val() || '<?= $SessionOrgId ?>';
    if (!name) { Swal.fire('Warning','Please enter a template name.','warning'); return; }
    $.ajax({
        url: 'ajax/Wprescripation/addInstrTemplate.php', type: 'POST', dataType: 'json',
        data: { template_name:name, template_data:content, type:type, org_id:orgId },
        success: function(r) {
            if (r.success) {
                Swal.fire('','Template saved.','success');
                $('#grxInstrTemplateModal').modal('hide');
            } else { Swal.fire('Error', r.error || 'Failed', 'error'); }
        }
    });
});

// View button — opens the saved gynaec prescription printout
$(document).on('click', '#grxViewBtn', function() {
    var rxId = $(this).data('rx-id');
    if (rxId) viewGynaecRx(rxId);
});

// Update view button when a prescription is loaded
var _grxOrigFill = grxFillPrescriptionData;
grxFillPrescriptionData = function(resp) {
    _grxOrigFill(resp);
    if (resp && resp.success && resp.data && resp.data.gynaec_rx_id) {
        $('#grxViewBtn').data('rx-id', resp.data.gynaec_rx_id).removeClass('d-none');
    }
};
</script>
