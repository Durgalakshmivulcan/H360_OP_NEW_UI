<?php
require_once("ajax/header.php");
// FIX_B_1820 (scope 2 RBAC): per-action view gate; SA bypassed by userCan().
requireCan('view', basename(__FILE__));

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>

<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Medicine Bill</h4>
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
            <li class="breadcrumb-item">Medicine Billing</li>
        </ul>

        <div class="card">
            <div class="card-header">
                <h4>Patient Search</h4>
            </div>
            <div class="col-12 col-md-12 col-lg-12">
                <form method="POST" id="FormId" action="" enctype="multipart/form-data">
                    <div class="card-body">
                        <?php if ($SessionUserId == "1" && $SessionRoleId == "1") { ?>
                            <div class="row">
                                <div class="row mb-lg-4 mb-sm-3">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="organizations">Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="fetchpatientdetails()">
                                            <option value="">Select Organization</option>
                                            <?php
                                            $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                            while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                                            ?>
                                                <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                        <?php } ?>

                        <div class="row">
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="patientName">Patient Name <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="patientName" id="patientName">
                                    <option value="">Select Patient Name</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="mobileNumber">Mobile <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="mobileNumber" id="mobileNumber">
                                    <option value="">Select Mobile Number</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="appointUnicode">Patient ID <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="appointUnicode" id="appointUnicode">
                                    <option value="">Select Patient ID</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="appointRegisterId">Appointment ID <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="appointRegisterId" id="appointRegisterId">
                                    <option value="">Select Appointment ID</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="card-footer text-center">
                                <button type="button" class="btn btn-primary" onclick="loadMedicineBilling()">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="showData"></div>
    </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
    $(document).ready(function() {
        fetchpatientdetails();
        $('#patientName, #mobileNumber, #appointUnicode, #appointRegisterId').select2();
    });

    $(document).on('change', '#patientName, #mobileNumber, #appointUnicode, #appointRegisterId', function() {
        patientinfo($(this).attr('id'), $(this).val());
    });

    function patientinfo(fieldName, fieldValue) {
        const organization_id = $('#organizations').val();

        $.ajax({
            url: 'ajax/Allpatientreports/patientinformation.php',
            type: 'POST',
            data: { organization_id, fieldName, fieldValue },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateSelect2('#patientName', response.data.patientName, response.data.appoint_id);
                    updateSelect2('#mobileNumber', response.data.mobileNumber, response.data.appoint_id);
                    updateSelect2('#appointUnicode', response.data.appointUnicode, response.data.appoint_id);
                    updateSelect2('#appointRegisterId', response.data.appointRegisterId, response.data.appoint_id);
                }
            }
        });
    }

    function updateSelect2(selector, value, id) {
        const $select = $(selector);
        if ($select.find("option[value='" + id + "']").length === 0) {
            $select.append(new Option(value, id, true, true)).trigger('change.select2');
        } else {
            $select.val(id).trigger('change.select2');
        }
    }

    function fetchpatientdetails() {
        const orgId = $("#organizations").val();
        if (!orgId) {
            return;
        }

        $.ajax({
            url: "ajax/Allpatientreports/fetchpatientdetails.php",
            method: "GET",
            dataType: "json",
            data: { org_id: orgId },
            success: function(data) {
                $('#patientName').empty().append('<option value="">Select Patient Name</option>');
                $('#mobileNumber').empty().append('<option value="">Select Mobile Number</option>');
                $('#appointUnicode').empty().append('<option value="">Select Patient ID</option>');
                $('#appointRegisterId').empty().append('<option value="">Select Appointment ID</option>');

                const mobileMap = {};
                const unicodeMap = {};
                const registerMap = {};

                data.mobile_numbers.forEach(item => mobileMap[item.appoint_id] = item.mobile_number);
                data.patient_ids.forEach(item => unicodeMap[item.appoint_id] = item.appoint_unicode);
                data.appointment_ids.forEach(item => registerMap[item.appoint_id] = item.appoint_register_id);

                data.patient_names.forEach(item => {
                    const id = item.appoint_id;
                    $('#patientName').append(`<option value="${id}">${item.patient_name}</option>`);
                    if (mobileMap[id]) $('#mobileNumber').append(`<option value="${id}">${mobileMap[id]}</option>`);
                    if (unicodeMap[id]) $('#appointUnicode').append(`<option value="${id}">${unicodeMap[id]}</option>`);
                    if (registerMap[id]) $('#appointRegisterId').append(`<option value="${id}">${registerMap[id]}</option>`);
                });
            }
        });
    }

    function loadMedicineBilling() {
        const appoint_register_id = $('#appointRegisterId').val();
        const patient_uid = $('#appointUnicode').val();
        const org_id = $("#organizations").val();

        if (!appoint_register_id || !patient_uid) {
            swal('', 'Please select Patient Name and Appointment ID first.', 'warning');
            return;
        }

        $.ajax({
            url: 'ajax/medicine_billing/viewreports.php',
            type: 'POST',
            data: {
                appoint_register_id: appoint_register_id,
                patient_uid: patient_uid,
                org_id: org_id
            },
            success: function(response) {
                $('#showData').html(response);
            }
        });
    }
</script>
