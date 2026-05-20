<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    .btn-group,
    .btn-group-vertical {

        position: relative;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }

    .med,
    .invest {
        display: none;
    }
</style>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Rx Groups</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Services</li>
            <li class="breadcrumb-item">Add & Modify Rx Groups</li>
        </ul>

        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">

                </div>
            </li>
        </ul>
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>RX Groups</h4>
                </div>
                <form method="POST" id="FormId" action="" enctype="multipart/form-data">
                    <input type="hidden" name="medicine_id[]" id="rx_group_id" value="">
                    <div class="card-body" id="card">
                        <div class="row">
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="rx_group_name"> RX-Group Name <span class="text-danger">*</span> </label>
                                <input class="form-control" name="rx_group_name[]" id="rx_group_name">
                            </div>
                            <?php
                            $SessionUserId = $_SESSION['security_id'] ?? '';
                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if ($SessionUserId == "1" && $SessionRoleId == "1") {
                            ?>
                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                    <select class="form-control form-select" name="organizations" id="organizations" onchange="RangeOrgBymadicines()">
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
                        </div>
                        <div class="form-group col-lg-12 col-sm-10 " style="float:right; padding-right:24px">
                            <a href="javascript:void(0)" class="add-more-form float-end btn btn-primary" id="addbtn_id"><i class=" fas fa-plus"></i>
                            </a>
                        </div>
                        <div class="adding-new-record card-body mt-3">
                            <!-- <hr> -->
                            <h6 class="text-dark">Medication</h6>
                            <div class="row mt-lg-3 mt-sm-3">

                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="medicineType">Medicine Type <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-capsules"></i>
                                            </div>
                                        </div>
                                        <input list="medicineTypeDatalist" class="form-control medicinetype" name="medicineType[]" id="medicineType">
                                        <datalist id="medicineTypeDatalist">
                                            <option value="">
                                        </datalist>
                                        <div id="typeDropdown" class="form-control dropdown-menu" type="hidden"></div>
                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="medicineName">Medicine Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="bi bi-capsule"></i>
                                            </div>
                                        </div>
                                        <input list="drugNameDatalist" class="form-control drugname" id="drugName" name="drugName[]" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this)">
                                        <datalist id="drugNameDatalist">
                                            <option value="">
                                        </datalist>
                                    </div>
                                </div>



                                <div class="form-group col-lg-2 col-sm-12">
                                    <label for="unit"> Unit <span class="text-danger">*</span> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <img src="assets/img/unit.jpeg" alt="unit Icon" width="17" height="17" classs="fw-bold">
                                            </div>
                                        </div>
                                        <input list="unitDatalist" class="form-control unit" id="unit" name="unit[]">
                                        <datalist id="unitDatalist">
                                            <option value="">
                                        </datalist>
                                    </div>
                                </div>


                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="dosage"> Dosage <span class="text-danger">*</span> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs="fw-bold">
                                            </div>
                                        </div>
                                        <select class="form-control form-select dosage" name="dosage[]" id="dosage" onchange="handleDosageChange(this.value,'')">
                                            <option value="">Select Dosage</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-4 col-sm-12 ">
                                    <label for="when"> In-take-period <span class="text-danger">*</span> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs="fw-bold">
                                            </div>
                                        </div>
                                        <select class="form-control form-select" name="when[]" id="when">
                                            <option value=""> Select In-take-period </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="time">Time <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                        <select class="form-control form-select" name="time[]" id="time">
                                            <option value="">Select Time</option>
                                        </select>
                                        <!-- <input type="text" class="form-control" name="time" id="time" /> -->
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="dosage"> Duration <span class="text-danger">*</span> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="material-icons">date_range</i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control " name="duration_value[]" id="duration_value" value="">
                                        <select class="form-control" name="duration[]" id="duration">
                                            <option value="Days">Days</option>
                                            <option value="Weeks">Weeks</option>
                                            <option value="Months">Months</option>
                                            <option value="Till Further Advice">Till Further Advice</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group col-lg-9  col-sm-12 w-35">
                                    <label for="notes">Notes</label><span class="text-danger"></span>
                                    <textarea class="form-control w-100" name="notes[]" id="notes" value=""></textarea>
                                </div>
                            </div>
                            <!-- <div class="adding-new-record card-body">
                            <div class="row ">
                                <div class="form-group col-lg-6 col-sm-12">
                                    <label for="depart">  Medicine <span class="text-danger" onchange="gettype(id)">*</span> </label>
                                    <select class="form-control form-select medicine"  name="medicine[]" id="medicine" >
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12 ">
                                    <label for="type"> Medicine Type <span class="text-danger" >*</span> </label>
                                    <select class="form-control form-select type" name="type[]" id="type">
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12 ">
                                    <label for="dose_d"> Unit  <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select unit" name="unit[]" id="unit" >
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-sm-12 ">
                                    <label for="dose_d"> Dosage  <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select dosage" name="dosage[]" id="dosage" >
                                    </select>
                                </div>
                                <!--<div class="form-group col-lg-3 col-sm-12 ">
                                <label for="dosage"> Dosage <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select dosage" name="dosage[]" id="dosage" >
                                    </select>
                                </div> 
                                <div class="form-group col-lg-3 col-sm-12 ">
                                    <label for="when"> In-take-period <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select" name="when[]" id="when" multiple>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12 ">
                                    <label for="time"> Time <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select time" name="time[]" id="time" >
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="frequency">  Frequency <span class="text-danger">*</span> </label>
                                    <select class="form-control form-select frequency"  name="frequency[]" id="frequency" >
                                    </select>
                                </div>
                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="duration"> Duration <span class="text-danger">*</span> </label>
                                    <input class="form-control" name="duration[]" id="duration" value="">
                                </div>
                                <div class="form-group col-lg-3 col-sm-12">
                                    <label for="quantity"> Quantity <span class="text-danger">*</span> </label>
                                    <input class="form-control" name="quantity[]" id="quantity" value="">
                                </div> 
                                <div class="form-group col-lg-9  col-sm-12 w-35">
                                    <label for="notes">Notes</label><span class="text-danger"></span>
                                    <textarea class="form-control w-100"  name="notes[]"  id="notes" value=""></textarea>
                                </div>
                            </div>
                        </div>-->

                        </div>
                        <div class="card-footer text-center">
                            <?php if (userCan('add', 'rxgroup.php') || userCan('edit', 'rxgroup.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                        </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Medicine List</h4>
            </div>

            <div class="card-body">
                <div class="col-12 col-md-12 table-responsive" id="showPData">
                </div>
            </div>
        </div>
</div>

<form action="" method="POST" id="deleteFormId">
    <input type="hidden" name="deleteID" id="deleteID" value="" />
</form>
</section>

<!-- </div> -->

<?php require_once("ajax/footer.php") ?>

<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>



<script>
    var NewInputsCount = 1;

    $("document").ready(function() {
        $('.medicine').select2();
        $('.type').select2();
        // $('.unit').select2();
        // $('.dosage').select2();
        //$('.time').select2();
        $('.frequency').select2();
        getRxGroup();
        getMedichines('');
        getMedicineType('');
        getUnit('');
        getDosages('');
        getInTakePeriod('');
        quantity();
        getNote('');


        $('#drugName').on('input', toggleMedSpans);
        $('#investigation').on('input', toggleTestSpans);

        toggleMedSpans();
        toggleTestSpans();
    });

    function toggleMedSpans() {
        var drugName = ($('#drugName').val() || '').trim();
        if (drugName === '') {
            $('.med').hide();
        } else {
            $('.med').show();
        }
    }

    function toggleTestSpans() {
        var drugName = ($('#investigation').val() || '').trim();
        if (drugName === '') {
            $('.invest').hide();
        } else {
            $('.invest').show();
        }
    }

    $(document).on('change', '#duration', function() {
        if (($('#duration').val()) == 'Till Further Advice') {
            $('#duration_value').val('0');
        }
    });

    $(document).on('input', '#duration_value', function() {
        if (($('#duration').val()) == 'Till Further Advice') {
            $('#duration_value').val('0');
        }
    });

    //   // Add event listener to the input field
    //   $('#rx_group_name').on('blur', function() {
    //     const rx_group_name = $(this).val();

    //     // Send an AJAX request to the server
    //     $.ajax({
    //       url: 'ajax/rxgroup/checkname.php', // Replace with the server-side script to check the name in the database
    //       type: 'POST',
    //       data: { 'rx_group_name': rx_group_name },
    //       success: function(response) {
    //         console.log(response);
    //         if (response === 'exists') {
    //           // Name already exists in the database
    //           swal('','Group Name Already Exists', 'warning');
    //         }
    //         // You can add more logic here for handling the response if needed
    //       },
    //       error: function() {
    //         // Handle AJAX request error
    //         alert('Error while checking the name. Please try again.');
    //       }
    //     });
    //   });

    var org_id = '<?= $SessionOrgId ?>';

    function getRxGroup() {
        $.ajax({
            url: 'ajax/rxgroup/getMedicineData.php',
            type: 'GET',
            success: function(data) {
                if (data) {
                    $("#showPData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array = [0, 1];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2];
                    }
                    $("#tableExportmedicine").dataTable({
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

    // Changes
    $(document).on('change', '.medicine', function() {
        let medicine_id = $(this).val();
        let currentRow = $(this).closest('.row');

        $.ajax({
            url: 'ajax/rxgroup/getMedicineDetails.php',
            type: 'POST',
            data: {
                medicine_id: medicine_id
            },
            dataType: 'json',
            success: function(results) {
                console.log(results);
                currentRow.find('.type').val(results.type).trigger('change');
                if (results.unit !== '0' && results.unit !== '') {
                    currentRow.find('.unit').val(results.unit).trigger('change');
                }
            }
        });
    });

    $(document).on('change', '.dosage', function() {
        let doseandtime_id = $(this).val();
        let currentRow = $(this).closest('.row');

        $.ajax({
            url: 'ajax/rxgroup/getDosageDetails.php',
            type: 'POST',
            data: {
                doseandtime_id: doseandtime_id
            },
            dataType: 'json',
            success: function(results) {
                if (results.frequency !== '0' && results.frequency !== '') {
                    currentRow.find('.frequency').val(results.frequency).trigger('change');
                }
            }
        });
    });
    // Changes
    function handleDosageChange(doseId, id) {
        if (doseId !== "") {
            getTimeForDose(doseId, id);
        } else {
            $("#time" + id).html('<option value="">Select Time</option>');
        }
    }


    var medicineList = [];

    function getMedichines(id, value) {
        // var id = '';
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

                    optionData += '<option value="' + displayName + '" data-id="' + val.medicine_id + '">' + displayName + '</option>';
                });

                var $datalist = $("#drugName" + id);
                if ($datalist.length === 0) {
                    console.log("No element found matching the selector");
                } else {
                    $datalist.html(optionData);
                    $("#drugNameDatalist" + id).html(optionData);
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

    function getMedicineTypeById(typeId, callback) {
        $.ajax({
            url: 'ajax/Wprescripation/getMedicineType.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                const match = data.find(item => item.type_id == typeId);
                if (match) {
                    callback(match.type_name);
                } else {
                    callback(null);
                }
            },
            error: function(err) {
                console.log("Error fetching medicine types:", err);
                callback(null);
            }
        });
    }




    var unitList = [];

    // get unit 
    function getUnit(id, value) {
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
                $("#unit" + id + ", #unitDatalist" + id + "").html(optionData);
                if (value) {
                    $("#unit" + id).val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }


    function getDosages(id, value) {
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



    function getInTakePeriod(id, value) {
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


    function getTimeForDose(doseId, id, value) {
        $.ajax({
            url: 'ajax/Wprescripation/getTime.php',
            type: 'get',
            data: {
                dose_id: doseId
            },
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
                if (value) {
                    $("#time" + id).val(value);
                    $("#edit_time").val(value);
                }
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
            data: {
                dose_id: doseId
            },
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

    // get note
    function getNote(id, value) {
        $.ajax({
            url: 'ajax/rxgroup/getNote.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '';
                $.each(data, function(key, val) {
                    optionData += '' + val.notes + '';
                });
                // $("#" + id).html(optionData);
                if (value) {
                    $("#notes" + id).val(value);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getDuration(id, value) {
        $.ajax({
            url: 'ajax/rxgroup/getDuration.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                // console.log(data);

                // Populate duration select if needed (currently not done here)

                if (value) {
                    // Split the value: e.g., "5 Days" → ["5", "Days"]
                    console.log(value);
                    let parts = value.trim().split(" ");
                    let durationVal = parts[0]; // "5"
                    console.log(durationVal);
                    let durationUnit = parts.slice(1).join(" "); // "Days" or "Till Further Advice"

                    $("#duration_value" + id).val(durationVal);
                    $("#duration" + id).val(durationUnit);
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }





    function getmedicinetypeandunit(input, id) {
        const selectedValue = input.value;

        // Get the selected option and its data-id
        const option = [...document.querySelectorAll('#drugNameDatalist option')]
            .find(opt => opt.value.trim().toUpperCase() === selectedValue.trim().toUpperCase());

        if (!option || !option.dataset.id) {
            console.warn("Medicine ID not found for selected value.");
            return;
        }

        const medicine_id = option.dataset.id;
        console.log(medicine_id);
        // Find the current row from the input element
        let currentRow = $(input).closest('.row');

        $.ajax({
            url: 'ajax/rxgroup/getMedicineDetails.php',
            type: 'POST',
            data: {
                medicine_id: medicine_id
            },
            dataType: 'json',
            success: function(results) {
                // console.log(results);

                // Set and disable medicine type
                if (results.type_name) {
                    currentRow.find('.medicinetype').val(results.type_name).trigger('change');
                }

                // Set and disable unit
                if (results.dosage !== '0' && results.dosage !== '') {
                    currentRow.find('.unit').val(results.dosage).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
            }
        });
    }




    $("#FormId").submit(function(event) {
        event.preventDefault();

        var rx_group_id = $("#rx_group_id").val();
        var rx_group_name = ($("#rx_group_name").val() || "").trim();
        var organizations = $("#organizations").val();

        if (rx_group_name === "") {
            swal('warning', 'Please Enter RX-Group Name !', 'warning');
            return;
        }

        let medicine_elements = document.getElementsByName("drugName[]");
        for (var i = 0; i < medicine_elements.length; i++) {
            if ((medicine_elements[i].value || "").trim() === "") {
                swal('warning', 'Please enter medicine!', 'warning');
                return;
            }
        }

        var medicines = [];
        $("input[name='drugName[]']").each(function(index) {
            let medName = ($(this).val() || "").trim();
            let type = ($("input[name='medicineType[]']").eq(index).val() || "").trim();
            let unit = ($("input[name='unit[]']").eq(index).val() || "").trim();
            let dosage = ($("select[name='dosage[]']").eq(index).val() || "").trim();
            let whenVal = ($("select[name='when[]']").eq(index).val() || "").trim();
            let timeVal = ($("select[name='time[]']").eq(index).val() || "").trim();
            let durVal = ($("input[name='duration_value[]']").eq(index).val() || "").trim();
            let durUnit = ($("select[name='duration[]']").eq(index).val() || "").trim();
            let notes = ($("input[name='notes[]']").eq(index).val() || "").trim();

            // Get the displayed text instead of value (id)
            let dosageText = $("select[name='dosage[]']").eq(index).find("option:selected").text();
            let whenText = $("select[name='when[]']").eq(index).find("option:selected").text();
            let timeText = $("select[name='time[]']").eq(index).find("option:selected").text();

            medicines.push({
                medicine_id: (index + 1).toString(),
                medicine_name: medName,
                type_id: type,
                type_text: type,
                unit_id: unit,
                unit_text: unit,
                dosage_id: dosage,
                when_id: whenVal,
                time_id: timeVal,
                duration_value: durVal,
                duration: durUnit,
                notes: notes,
                med_status: "1",
                timeText: timeText,
                dosageText: dosageText,
                whenText: whenText
            });
        });


        if (medicines.length === 0) {
            swal('warning', 'Please add at least one medicine!', 'warning');
            return;
        }

        var subitem = {
            rx_group_id: rx_group_id,
            rx_group_name: rx_group_name,
            organizations: organizations,
            medicine: JSON.stringify(medicines)
        };

        // console.log("Final payload:", subitem);
        // return;

        $.ajax({
            url: 'ajax/rxgroup/AddModifyRxGroup.php',
            type: 'POST',
            data: subitem,
            dataType: 'json',
            success: function(data) {
                if (data == 1) {
                    swal({
                        text: 'Rx-Group Record Added Successfully',
                        icon: 'success',
                        buttons: {
                            ok: {
                                text: 'OK',
                                className: 'btn btn-primary'
                            }
                        }
                    }).then(function() {
                        $("#rx_group_id").val('');
                        getRxGroup();
                        location.reload();
                    });
                } else if (data == 2) {
                    swal({
                        text: 'Rx-Group Record Updated Successfully',
                        icon: 'success',
                        buttons: {
                            ok: {
                                text: 'OK',
                                className: 'btn btn-primary'
                            }
                        }
                    }).then(function() {
                        $("#rx_group_id").val('');
                        getRxGroup();
                        location.reload();
                    });
                } else if (data == 3) {
                    swal('', 'Group Name Already Exists', 'warning');
                } else if (data == 0) {
                    swal('', 'All Fields Required', 'warning');
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    });


    function editMedicine(rx_group_id, rx_group_name, organizations) {
        window.scrollTo(0, 0);
        $("#rx_group_id").val(rx_group_id);
        $("#rx_group_name").val(rx_group_name);
        $("#organizations").val(organizations);
        getPresRx(rx_group_id);
    }



    function deleteMedicine(rx_group_id, rx_group_name) {
        swal({
            title: "Are you sure?",
            text: "Do you really want to delete Rx-Group Record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/rxgroup/deleterxgroup.php',
                    type: 'POST',
                    data: {
                        'rx_group_id': rx_group_id
                    },
                    success: function(data) {
                        // console.log(data);
                        if (data == 1) {
                            swal('', 'Deleted Successfully', 'success');
                            getRxGroup();
                            location.reload();
                        } else {
                            swal('', 'Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
                $('#deleteID').val(rx_group_id);
                swal('', 'Record Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    function clearData() {
        $('.adding-new-record').html('');
        NewInputsCount = 1;
        $('.adding-new-record').append('<div class="row main-form " >\
            <div class="form-group col-sm-1  col-sm-12">\
                <span ><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1 " ><i class=" fas fa-minus"></i></a></span>\
            </div>\
            <div class="row">\
                <div class="form-group col-lg-3 col-sm-12">\
                    <label for="drugName' + NewInputsCount + '">Medicine Name <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="bi bi-capsule"></i>\
                            </div>\
                        </div>\
                        <input list="drugNameDatalist" class="form-control drugname" id="drugName' + NewInputsCount + '" name="drugName[]" oninput="this.value = this.value.toUpperCase();"  onchange="getmedicinetypeandunit(this, ' + NewInputsCount + ')">\
                        <datalist id="drugNameDatalist">\
                            <option value="">\
                        </datalist>\
                    </div>\
                </div>\
                <div class="form-group col-lg-3 col-sm-12">\
                    <label for="medicineType' + NewInputsCount + '">Medicine Type <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="fas fa-capsules"></i>\
                            </div>\
                        </div>\
                        <input list="medicineTypeDatalist" class="form-control medicinetype" name="medicineType[]" id="medicineType' + NewInputsCount + '">\
                        <datalist id="medicineTypeDatalist">\
                            <option value="">\
                        </datalist>\
                        <div id="typeDropdown' + NewInputsCount + '" class="form-control dropdown-menu" type="hidden"></div>\
                    </div>\
                </div>\
                <div class="form-group col-lg-2 col-sm-12">\
                    <label for="unit' + NewInputsCount + '">Unit <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <img src="assets/img/unit.jpeg" alt="unit Icon" width="17" height="17">\
                            </div>\
                        </div>\
                        <input list="unitDatalist" class="form-control unit" id="unit' + NewInputsCount + '" name="unit[]">\
                        <datalist id="unitDatalist">\
                            <option value="">\
                        </datalist>\
                    </div>\
                </div>\
                <div class="form-group col-lg-4 col-sm-12">\
                    <label for="dosage' + NewInputsCount + '">Dosage <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17">\
                            </div>\
                        </div>\
                        <select class="form-control form-select dosage" name="dosage[]" id="dosage' + NewInputsCount + '" onchange="handleDosageChange(this.value, ' + NewInputsCount + ')">\
                            <option value="">Select Dosage</option>\
                        </select>\
                    </div>\
                </div>\
                <div class="form-group col-lg-4 col-sm-12">\
                    <label for="when' + NewInputsCount + '">In-take-period <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17">\
                            </div>\
                        </div>\
                        <select class="form-control form-select" name="when[]" id="when' + NewInputsCount + '">\
                            <option value="">Select In-take-period</option>\
                        </select>\
                    </div>\
                </div>\
                <div class="form-group col-lg-3 col-sm-12">\
                    <label for="time' + NewInputsCount + '">Time <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="fas fa-clock"></i>\
                            </div>\
                        </div>\
                        <select class="form-control form-select" name="time[]" id="time' + NewInputsCount + '">\
                            <option value="">Select Time</option>\
                        </select>\
                    </div>\
                </div>\
                <div class="form-group col-lg-4 col-sm-12">\
                    <label for="duration_value' + NewInputsCount + '">Duration <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="material-icons">date_range</i>\
                            </div>\
                        </div>\
                        <input type="text" class="form-control" name="duration_value[]" id="duration_value' + NewInputsCount + '" value="">\
                        <select class="form-control" name="duration[]" id="duration' + NewInputsCount + '">\
                            <option value="Days">Days</option>\
                            <option value="Weeks">Weeks</option>\
                            <option value="Months">Months</option>\
                            <option value="Till Further Advice">Till Further Advice</option>\
                        </select>\
                    </div>\
                </div>\
                <div class="form-group col-lg-9 col-sm-12 w-35">\
                    <label for="notes' + NewInputsCount + '">Notes</label>\
                    <textarea class="form-control w-100" name="notes[]" id="notes' + NewInputsCount + '"></textarea>\
                </div>\
        </div>');
        getMedichines(NewInputsCount);
        // getMedicineType(NewInputsCount);
        getUnit(NewInputsCount);
        getDosages(NewInputsCount);
        getInTakePeriod(NewInputsCount);
        quantity(NewInputsCount);
        getNote(NewInputsCount);
        // getMedicine(NewInputsCount);
        // getMedicineType(NewInputsCount);
        // getMedicineUnit(NewInputsCount);
        // getDosages(NewInputsCount);
        // //getInTake(NewInputsCount);
        // //getTimes(NewInputsCount);
        // getFrequency(NewInputsCount);
        // durartin(NewInputsCount);
        // quantity(NewInputsCount);
        NewInputsCount++;
    }


    function getPresRx(rx_group_id) {
        var org_id = $("#organizations").val();
        $.ajax({
            url: 'ajax/rxgroup/getPresrx.php',
            type: 'POST',
            data: {
                'rx_group_id': rx_group_id,
                'org_id': org_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                $('.adding-new-record').html('');
                NewInputsCount = 1;

                var medicines = [];
                if (data && data.medicine_detailes) {
                    try {
                        medicines = JSON.parse(data.medicine_detailes);
                    } catch (e) {
                        console.error("Error parsing medicine_detailes:", e);
                        medicines = [];
                    }
                }

                $.each(medicines, function(index, val) {
                    $('.adding-new-record').append('<div class="row main-form">\
                    <div class="form-group col-sm-1 col-sm-12">\
                        <span><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1"><i class="fas fa-minus"></i></a></span>\
                    </div>\
                    <div class="row">\
                        <div class="form-group col-lg-3 col-sm-12">\
                            <label for="medicineType' + NewInputsCount + '">Medicine Type <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-capsules"></i></div></div>\
                                <input list="medicineTypeDatalist' + NewInputsCount + '" class="form-control medicinetype" name="medicineType[]" id="medicineType' + NewInputsCount + '" value="' + (val.type_text || '') + '">\
                                <datalist id="medicineTypeDatalist' + NewInputsCount + '"><option value=""></datalist>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-3 col-sm-12">\
                            <label for="drugName' + NewInputsCount + '">Medicine Name <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend"><div class="input-group-text"><i class="bi bi-capsule"></i></div></div>\
                                <input list="drugNameDatalist' + NewInputsCount + '" class="form-control drugname" id="drugName' + NewInputsCount + '" name="drugName[]" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this, ' + NewInputsCount + ')" value="' + (val.medicine_name || '') + '">\
                                <datalist id="drugNameDatalist' + NewInputsCount + '"><option value=""></datalist>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-2 col-sm-12">\
                            <label for="unit' + NewInputsCount + '">Unit <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/unit.jpeg" width="17" height="17"></div></div>\
                                <input list="unitDatalist' + NewInputsCount + '" class="form-control unit" id="unit' + NewInputsCount + '" name="unit[]" value="' + (val.unit_text || '') + '">\
                                <datalist id="unitDatalist' + NewInputsCount + '"><option value=""></datalist>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-4 col-sm-12">\
                            <label for="dosage' + NewInputsCount + '">Dosage <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend">\
                                    <div class="input-group-text">\
                                        <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17">\
                                    </div>\
                                </div>\
                                <select class="form-control form-select dosage" name="dosage[]" id="dosage' + NewInputsCount + '" onchange="handleDosageChange(this.value, ' + NewInputsCount + ')">\
                                    <option value="">Select Dosage</option>\
                                </select>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-4 col-sm-12">\
                            <label for="when' + NewInputsCount + '">In-take-period <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend"><div class="input-group-text"><img src="assets/img/dosage.jpeg" width="17" height="17"></div></div>\
                                <select class="form-control form-select" name="when[]" id="when' + NewInputsCount + '"></select>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-3 col-sm-12">\
                            <label for="time' + NewInputsCount + '">Time <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend">\
                                    <div class="input-group-text">\
                                        <i class="fas fa-clock"></i>\
                                    </div>\
                                </div>\
                                <select class="form-control form-select" name="time[]" id="time' + NewInputsCount + '">\
                                    <option value="">Select Time</option>\
                                </select>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-4 col-sm-12">\
                            <label for="duration_value' + NewInputsCount + '">Duration <span class="text-danger">*</span></label>\
                            <div class="input-group">\
                                <div class="input-group-prepend"><div class="input-group-text"><i class="material-icons">date_range</i></div></div>\
                                <input type="text" class="form-control" name="duration_value[]" id="duration_value' + NewInputsCount + '" value="' + (val.duration_value || '') + '">\
                                <select class="form-control" name="duration[]" id="duration' + NewInputsCount + '">\
                                    <option value="Days"' + ((val.duration === 'Days') ? ' selected' : '') + '>Days</option>\
                                    <option value="Weeks"' + ((val.duration === 'Weeks') ? ' selected' : '') + '>Weeks</option>\
                                    <option value="Months"' + ((val.duration === 'Months') ? ' selected' : '') + '>Months</option>\
                                    <option value="Till Further Advice"' + ((val.duration === 'Till Further Advice') ? ' selected' : '') + '>Till Further Advice</option>\
                                </select>\
                            </div>\
                        </div>\
                        <div class="form-group col-lg-9 col-sm-12 w-35">\
                            <label for="notes' + NewInputsCount + '">Notes</label>\
                            <textarea class="form-control w-100" name="notes[]" id="notes' + NewInputsCount + '">' + (val.notes || '') + '</textarea>\
                        </div>\
                    </div>\
                </div>');

                    getMedichines(NewInputsCount, val.medicine_name || '');
                    getMedicineType(NewInputsCount, val.type_text || '');
                    getUnit(NewInputsCount, val.unit_text || '');
                    getDosages(NewInputsCount, val.dosage_id || '');
                    getInTakePeriod(NewInputsCount, val.when_id || '');
                    let dosageSelect = $('#dosage' + NewInputsCount);
                    if (val.dosageText) {
                        dosageSelect.val(val.dosage_id || '').trigger('change');
                    }
                    setTimeout(() => {
                        dosageSelect.trigger('change');
                        getTimeForDose(val.dosageText || '', NewInputsCount, val.timeText || '');

                    }, 500);
                    quantity(NewInputsCount, val.quantity || '');
                    getNote(NewInputsCount, val.notes || '');
                    getDuration(NewInputsCount, val.duration_value || '');

                    NewInputsCount++;
                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getMedicineType(id, value) {
        $.ajax({
            url: 'ajax/Wprescripation/getMedicineType.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var optionData = '';
                $.each(data, function(index, val) {
                    optionData += '<option value="' + val.type_name + '">';
                });

                $("#medicineTypeDatalist" + id).html(optionData);

                if (value) {
                    $("#medicineType" + id).val(value);
                } else {
                    $("#medicineType" + id).val('Tab');
                }
            },
            error: function(err) {
                console.log("Error fetching medicine types:", err);
            }
        });
    }


    // adding rows
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('.main-form').remove();
    });

    var NewInputsCount = 1;

    $(document).on('click', '.add-more-form', function() {
        $('.adding-new-record').append('<div class="row main-form " >\
        <div class="form-group col-sm-1  col-sm-12">\
            <span ><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1 " ><i class=" fas fa-minus"></i></a></span>\
        </div>\
        <div class="row">\
                 <div class="form-group col-lg-3 col-sm-12">\
                        <label for="medicineType' + NewInputsCount + '">Medicine Type <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <i class="fas fa-capsules"></i>\
                                </div>\
                            </div>\
                            <input list="medicineTypeDatalist' + NewInputsCount + '" class="form-control medicinetype" name="medicineType[]" id="medicineType' + NewInputsCount + '">\
                            <datalist id="medicineTypeDatalist' + NewInputsCount + '">\
                                <option value="">\
                            </datalist>\
                            <div id="typeDropdown' + NewInputsCount + '" class="form-control dropdown-menu" type="hidden"></div>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-3 col-sm-12">\
                        <label for="drugName' + NewInputsCount + '">Medicine Name <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <i class="bi bi-capsule"></i>\
                                </div>\
                            </div>\
                            <input list="drugNameDatalist' + NewInputsCount + '" class="form-control drugname" id="drugName' + NewInputsCount + '" name="drugName[]" oninput="this.value = this.value.toUpperCase();" onchange="getmedicinetypeandunit(this, ' + NewInputsCount + ')">\
                            <datalist id="drugNameDatalist' + NewInputsCount + '">\
                                <option value="">\
                            </datalist>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-2 col-sm-12">\
                        <label for="unit' + NewInputsCount + '">Unit <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <img src="assets/img/unit.jpeg" alt="unit Icon" width="17" height="17">\
                                </div>\
                            </div>\
                            <input list="unitDatalist' + NewInputsCount + '" class="form-control unit" id="unit' + NewInputsCount + '" name="unit[]">\
                            <datalist id="unitDatalist' + NewInputsCount + '">\
                                <option value="">\
                            </datalist>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-4 col-sm-12">\
                        <label for="dosage' + NewInputsCount + '">Dosage <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17">\
                                </div>\
                            </div>\
                            <select class="form-control form-select dosage" name="dosage[]" id="dosage' + NewInputsCount + '" onchange="handleDosageChange(this.value, ' + NewInputsCount + ')">\
                                <option value="">Select Dosage</option>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-4 col-sm-12">\
                        <label for="when' + NewInputsCount + '">In-take-period <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17">\
                                </div>\
                            </div>\
                            <select class="form-control form-select" name="when[]" id="when' + NewInputsCount + '">\
                                <option value="">Select In-take-period</option>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-3 col-sm-12">\
                        <label for="time' + NewInputsCount + '">Time <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <i class="fas fa-clock"></i>\
                                </div>\
                            </div>\
                            <select class="form-control form-select" name="time[]" id="time' + NewInputsCount + '">\
                                <option value="">Select Time</option>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-4 col-sm-12">\
                        <label for="duration_value' + NewInputsCount + '">Duration <span class="text-danger">*</span></label>\
                        <div class="input-group">\
                            <div class="input-group-prepend">\
                                <div class="input-group-text">\
                                    <i class="material-icons">date_range</i>\
                                </div>\
                            </div>\
                            <input type="text" class="form-control" name="duration_value[]" id="duration_value' + NewInputsCount + '"  value="">\
                            <select class="form-control" name="duration[]" id="duration' + NewInputsCount + '">\
                                <option value="Days">Days</option>\
                                <option value="Weeks">Weeks</option>\
                                <option value="Months">Months</option>\
                                <option value="Till Further Advice">Till Further Advice</option>\
                            </select>\
                        </div>\
                    </div>\
                    <div class="form-group col-lg-9 col-sm-12 w-35">\
                        <label for="notes' + NewInputsCount + '">Notes</label>\
                        <textarea class="form-control w-100" name="notes[]" id="notes' + NewInputsCount + '"></textarea>\
                    </div>\
                </div>');
        // getRxGroup();
        // $('#medicine' + NewInputsCount + ', #type' + NewInputsCount + ', #unit' + NewInputsCount + ', #dosage' + NewInputsCount + ', #frequency' + NewInputsCount).select2();
        getMedichines(NewInputsCount);
        getMedicineType(NewInputsCount);
        getUnit(NewInputsCount);
        getDosages(NewInputsCount);
        getInTakePeriod(NewInputsCount);
        quantity(NewInputsCount);
        getNote(NewInputsCount);
        // getMedicine(NewInputsCount, val.medicine_name);
        // getMedicineType(NewInputsCount, val.medicine_type);
        // getMedicineUnit(NewInputsCount, val.unit);
        // getDosages(NewInputsCount, val.dosage);
        // getInTake(NewInputsCount, val.in_time_period);
        // getTimes(NewInputsCount, val.timing);
        // getFrequency(NewInputsCount, val.frequency);
        // getDuration(NewInputsCount, val.duration);
        // getQuantity(NewInputsCount, val.quantity);
        // getNote(NewInputsCount, val.notes);
        // durartin(NewInputsCount);
        // quantity(NewInputsCount);
        NewInputsCount++;

    });

    // Name Validation
    $(function() {
        $("#rx_group_name").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError").html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError").html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#rx_group_name").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });

    // Quantity Only Numbers
    function quantity(NewInputsCount) {
        $("#quantity").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError2").html("");
            var regex = /^[0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError2").html("Only Numbers allowed.");
            }
            return isValid;
        });

        $("#quantity" + NewInputsCount).keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError2" + NewInputsCount).html("");
            var regex = /^[0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError2" + NewInputsCount).html("Only Numbers allowed.");
            }
            return isValid;
        });

        $(function() {
            $("#quantity").keyup(function() {
                var organizationName = $(this).val();
                if (!organizationName.trim()) {
                    $(this).val('');
                }
            });

            $("#quantity" + NewInputsCount).keyup(function() {
                var organizationName = $(this).val();
                if (!organizationName.trim()) {
                    $(this).val('');
                }
            });
        });
    }
    // Duration 
    function durartin(NewInputsCount) {

        $("#duration").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError3").html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError3").html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });

        if (typeof NewInputsCount == 'number') {
            $("#duration" + NewInputsCount).keypress(function(e) {
                var keyCode = e.keyCode || e.which;
                $("#lblError3" + NewInputsCount).html("");
                var regex = /^[A-Za-z0-9 ]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#lblError3" + NewInputsCount).html("Only Alphabets&Number allowed.");
                }
                return isValid;
            });
        }

        $(function() {
            $("#duration").keyup(function() {
                var organizationName = $(this).val();
                if (!organizationName.trim()) {
                    $(this).val('');
                }
            });

            $("#duration" + NewInputsCount).keyup(function() {
                var organizationName = $(this).val();
                if (!organizationName.trim()) {
                    $(this).val('');
                }
            });
        });
    };


    function RangeOrgBymadicines() {
        var org_id = $('#organizations').val();
        $.ajax({
            url: 'ajax/rxgroup/GetOrgByIdsmedicines.php',
            type: 'POST',
            data: {
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '<option value=""> Select Medicine Name</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.medicine_id + '"> ' + val.medicine_name + '-( ' + val.scientific_name + ') </option>';
                });
                $(".medicine").html(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
</script>