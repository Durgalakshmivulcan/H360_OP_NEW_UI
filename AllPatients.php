<?php
require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));

$registrationid = isset($_GET['appoint_id']) ? $_GET['appoint_id'] : '';
$appoint_unicode = isset($_GET['appoint_unicode']) ? $_GET['appoint_unicode'] : '';

?>

<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
</style>

<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">All Patient</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">All Patient</li>
        </ul>

        <div class="card">
            <div class="card-header">
                <h4>All Patient Reports</h4>
            </div>
            <div class="card-body">
                <form>
                    <?php
                    $SessionUserId = $_SESSION['security_id'];
                    $SessionRoleId = $_SESSION['role_id'];
                    $SessionOrgId = $_SESSION['org_id'];

                    if ($SessionUserId == "1" && $SessionRoleId == "1") {
                    ?>

                        <div class="row">
                            <div class="row mb-lg-5 mb-sm-3">
                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                    <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="fetchpatientdetails()">
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
                            </div>
                        </div>

                    <?php
                    } else {
                    ?>
                        <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                    <?php
                    }
                    ?>

                    <div class="row">
                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="Patient Name"><i class="bi bi-person-fill"></i> Patient Name <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select patient_name" name="patient_name" id="patient_name" placeholder="Select Patient Name">
                                <option value="">Select Patient Name</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="mobileNumber"><i class="bi bi-telephone-fill"></i> Mobile <span class="text-danger">*</span> </label>
                            <select type="tel" class="form-control form-select mobile_number" name="mobile_number" id="mobile_number" placeholder="Select Mobile Number">
                                <option value="">Select Mobile Number</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-person-vcard"></i> Patient ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appoint_unicode" name="appoint_unicode" id="appoint_unicode">
                                <option value="">Select Patient ID</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-postcard-fill"></i> Appointment ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appoint_register_id" name="appoint_register_id" id="appoint_register_id">
                                <option value="">Select Appointment ID</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-primary" name="saveData" id="saveData" onclick="myFunction()">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Appointments Timeline</h4>
            </div>
            <div class="card-body" id="viewPatientTimeline">

            </div>
        </div>
    </section>
</div>

<!-- Prescription Modal -->
<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prescriptionModalLabel">Prescription Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="prescriptionModalBody">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Old Reports Modal -->
<div class="modal fade" id="oldReportsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Old Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="oldReportsModalBody">
                <!-- AJAX content injected here -->
            </div>
        </div>
    </div>
</div>



<?php require_once("ajax/footer.php") ?>




<script>
    $(document).ready(function() {
        fetchpatientdetails();

        $('#patient_name').select2();
        $('#mobile_number').select2();
        $('#appoint_unicode').select2();
        $('#appoint_register_id').select2();

        $('#patient_name, #mobile_number, #appoint_unicode, #appoint_register_id').on('change', function() {
            $('#viewPatientTimeline').html('');
            const fieldName = $(this).attr('id');
            const fieldValue = $(this).val();
            patientinfo(fieldName, fieldValue);
        });
        <?php if (!empty($registrationid)) { ?>
            patientinfo('appoint_register_id', '<?= $registrationid ?>');
        <?php } ?>

        <?php if (!empty($appoint_unicode)) { ?>
            patientinfo('appoint_unicode', '<?= $appoint_unicode ?>');
        <?php } ?>

    });



    function patientinfo(fieldName, fieldValue) {
        const organization_id = $('#organizations').val();
        console.log(fieldValue);
        $.ajax({
            url: 'ajax/Allpatientreports/patientinfo.php',
            type: 'POST',
            data: {
                organization_id,
                fieldName,
                fieldValue
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateSelect2('#patient_name', response.data.patient_name, response.data.appoint_id);
                    updateSelect2('#mobile_number', response.data.mobile_number, response.data.appoint_id);
                    updateSelect2('#appoint_unicode', response.data.appoint_unicode, response.data.appoint_id);
                    updateSelect2('#appoint_register_id', response.data.appoint_register_id, response.data.appoint_id);
                } else {
                    console.log("No matching patient found.");
                }
            },
            error: function(error) {
                console.log("Error fetching patient data.", error);
            }
        });
    }

    function updateSelect2(selector, value, id) {
        const $select = $(selector);

        if ($select.find("option[value='" + id + "']").length === 0) {
            const newOption = new Option(value, id, true, true);
            $select.append(newOption).trigger('change.select2');
        } else {
            $select.val(id).trigger('change.select2');
        }
    }


    function fetchpatientdetails() {
        var orgId = $("#organizations").val();

        if (orgId) {
            $.ajax({
                url: "ajax/Allpatientreports/fetchpatientdetails.php",
                method: "GET",
                dataType: "json",
                data: {
                    org_id: orgId
                },
                success: function(data) {
                    $('#patient_name').empty().append('<option value="">Select Patient Name</option>');
                    $('#mobile_number').empty().append('<option value="">Select Mobile Number</option>');
                    $('#appoint_unicode').empty().append('<option value="">Select Patient ID</option>');
                    $('#appoint_register_id').empty().append('<option value="">Select Appointment ID</option>');

                    const mobileMap = {};
                    const unicodeMap = {};
                    const registerMap = {};

                    data.mobile_numbers.forEach(item => {
                        mobileMap[item.appoint_id] = item.mobile_number;
                    });

                    data.patient_ids.forEach(item => {
                        unicodeMap[item.appoint_id] = item.appoint_unicode;
                    });

                    data.appointment_ids.forEach(item => {
                        registerMap[item.appoint_id] = item.appoint_register_id;
                    });

                    data.patient_names.forEach(item => {
                        const id = item.appoint_id;

                        $('#patient_name').append(`<option value="${id}">${item.patient_name}</option>`);

                        const mobile = mobileMap[id] || '';
                        if (mobile) {
                            $('#mobile_number').append(`<option value="${id}">${mobile}</option>`);
                        }

                        const unicode = unicodeMap[id] || '';
                        if (unicode) {
                            $('#appoint_unicode').append(`<option value="${id}">${unicode}</option>`);
                        }

                        const registerId = registerMap[id] || '';
                        if (registerId) {
                            $('#appoint_register_id').append(`<option value="${id}">${registerId}</option>`);
                        }
                    });

                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    };

    function myFunction() {
        var patient_name = $('#patient_name').val();
        var patient_number = $('#mobile_number').val();
        var appoint_register_id = $('#appoint_register_id').val();
        var patient_uid = $('#appoint_unicode').val();
        var organization_id = $('#organizations').val();

        if (!patient_uid) {
            swal('', 'Please fill out the mandatory fields.', 'warning');
            return;
        }

        $.ajax({
            url: 'ajax/Allpatientreports/patienttimeline.php',
            type: 'GET',
            data: {
                patient_uid: patient_uid,
                org_id: organization_id
            },
            success: function(data) {
                console.log(data);
                $('#viewPatientTimeline').html(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    };


    function viewprescription(ids) {
        $('#prescriptionModalLabel').text('Prescription Details');
        $('#prescriptionModalBody').html('<p>Loading...</p>');
        var organizations = $('#organizations').val();

        $.ajax({
            url: 'ajax/Allpatientreports/getprescriptions.php',
            type: 'POST',
            data: {
                ids: ids,
                org_id: organizations
            },
            success: function(response) {
                $('#prescriptionModalBody').html(response);
            },
            error: function() {
                $('#prescriptionModalBody').html('<p class="text-danger">Failed to fetch prescription details.</p>');
            }
        });

        $('#prescriptionModal').modal('show');
    };

    function viewgynaecprescription(id) {
        $('#prescriptionModalLabel').text('Gynaec Prescription Details');
        $('#prescriptionModalBody').html('<p>Loading...</p>');
        var organizations = $('#organizations').val();

        $.ajax({
            url: 'ajax/Allpatientreports/getgynaecprescription.php',
            type: 'POST',
            data: {
                id: id,
                org_id: organizations
            },
            success: function(response) {
                $('#prescriptionModalBody').html(response);
            },
            error: function() {
                $('#prescriptionModalBody').html('<p class="text-danger">Failed to fetch gynaec prescription details.</p>');
            }
        });

        $('#prescriptionModal').modal('show');
    };

    function viewoldreports(ids) {
        $('#oldReportsModalBody').html('<p>Loading...</p>');
        var organizations = $('#organizations').val();

        $.ajax({
            url: 'ajax/Allpatientreports/getoldreports.php',
            type: 'POST',
            data: {
                ids: ids,
                org_id: organizations
            },
            success: function(response) {
                $('#oldReportsModalBody').html(response);
            },
            error: function() {
                $('#oldReportsModalBody').html('<p class="text-danger">Failed to fetch old report details.</p>');
            }
        });

        $('#oldReportsModal').modal('show');
    }
</script>
