<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__));

?>
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
        right: 0;
        top: 15px;
        width: auto;
    }

    *

    /* input[type='time']::-webkit-calendar-picker-indicator { 
    background-position: right;
    background-size: auto;
    cursor: pointer;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 10px;
    top: 10px;
    width: auto;
} */
    .pluse {
        cursor: pointer;
        /* border-radius: 10px 10px 10px 10px;    */
    }

    .add_row {
        cursor: pointer;
        /* border-radius: 10px 10px 10px 10px; */
    }

    .nav-tabs {
        border: 0px solid;
    }

    .weekDays-selector input {
        display: none !important;
    }

    .weekDays-selector input[type=checkbox]+label {
        display: inline-block;
        border-radius: 6px;
        background: #dddddd;
        height: 40px;
        width: 35px;
        margin-right: 3px;
        line-height: 40px;
        text-align: center;
        cursor: pointer;
    }

    .weekDays-selector input[type=checkbox]:checked+label {
        background: #2AD705;
        color: #ffffff;
    }

    .disabled-button {
        background-color: red;
        color: red;
        cursor: not-allowed;
    }

    .tabs {
        margin-left: 25px;
    }
</style>



<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Doctor Time Slots</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            <li class="breadcrumb-item active">Doctor Menu</li>
            <li class="breadcrumb-item active">Add/Modify Doctor Time Slots</li>
        </ul>

        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">

                </div>
            </li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">

            <div class="tabs">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <!-- <button class="nav-link active " id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Daily</button> -->
                        <input class="nav-link active " id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" name="Daily" type="button" role="tab" aria-controls="pills-home" aria-selected="true" value="Daily">
                    </li>
                    <li class="nav-item" role="presentation">
                        <input class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" name="Range" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" value="Range">
                        <!-- <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Range</button> -->
                    </li>
                </ul>
            </div>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header">
                            <h4>Doctor Time Slot</h4>
                        </div>
                        <div class="tab-pane active" id="Daily">
                            <ul style="margin-right: 30px;">
                                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                                    <?php
                                    $SessionUserId = $_SESSION['security_id'] ?? 0;
                                    $SessionOrgId  = $_SESSION['org_id'] ?? 0;

                                    // Step 0: Get security type for logged-in user
                                    $sec_type = '';
                                    $qrySec = mysqli_query($conn, "SELECT security_type FROM security WHERE security_id='$SessionUserId'") or die(mysqli_error($conn));
                                    if ($row = mysqli_fetch_assoc($qrySec)) {
                                        $sec_type = $row['security_type'];
                                    }

                                    // Step 1: Prepare doctor list
                                    $doctors = [];

                                    // SA_FATAL_FIXED_B_547: SA sees doctors across all orgs
                                    if ($sec_type == 'SA') {
                                        $getMenus = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' ORDER BY doc_id DESC") or die(mysqli_error($conn));
                                        while ($resMenus = mysqli_fetch_object($getMenus)) {
                                            $doctors[] = $resMenus;
                                        }
                                    } elseif ($sec_type == 'A') {
                                        // Admin: fetch all doctors in the admin's organization
                                        $getMenus = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND org_id='$SessionOrgId' ORDER BY doc_id DESC") or die(mysqli_error($conn));
                                        while ($resMenus = mysqli_fetch_object($getMenus)) {
                                            $doctors[] = $resMenus;
                                        }
                                    } else {
                                        // Non-admin: check if assigned (receptionist) or doctor themselves
                                        $res = mysqli_query($conn, "SELECT doc_id FROM receptionnist WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));

                                        $doc_ids = [];
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            $doc_ids[] = $row['doc_id'];
                                        }

                                        if (!empty($doc_ids)) {
                                            // Receptionist: fetch assigned doctors
                                            $in = implode(",", $doc_ids);
                                            $getDocs = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id IN ($in) ORDER BY doc_id DESC") or die(mysqli_error($conn));
                                            while ($doc = mysqli_fetch_object($getDocs)) {
                                                $doctors[] = $doc;
                                            }
                                        } else {
                                            // Doctor themselves: fetch their own record
                                            $res2 = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
                                            while ($doc = mysqli_fetch_object($res2)) {
                                                $doctors[] = $doc;
                                            }
                                        }
                                    }
                                    ?>

                                    <div class="card-body">
                                        <input type="hidden" name="Timeslot_id" id="Timeslot_id" value="">
                                        <input type="hidden" name="multi_id" id="multi_id" value="">
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <label>Name <span class="text-danger">*</span></label>
                                                <select class="form-control form-select" name="doc_name" id="doc_name">
                                                    <option value="">Select Option</option>
                                                    <?php foreach ($doctors as $d): ?>
                                                        <option value="<?= $d->doc_id ?>">
                                                            <b><?= htmlspecialchars($d->doctor_name) ?></b> - <?= htmlspecialchars($d->doc_registration_number) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="available_date" id="available_date" novalidate>
                                            </div>

                                            <?php if (userCan('add', basename(__FILE__))): ?>
                                            <div class="form-group col-lg-12 col-sm-12" style="margin-top:-10px;">
                                                <a href="javascript:void(0)" class="adding-form float-end btn btn-primary pluse">
                                                    <i class="fas fa-plus pluse"></i>
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="adding-new-record card-body">
                                        <div class="row open-form">
                                            <div class="form-group col-lg-6 col-sm-12 timepicker-max-time">
                                                <label for="">Start Time<span class="text-danger">*</span></label>
                                                <input type="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time" onchange="timeslots()" value="" />
                                            </div>
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <label for="">End Time <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" name="available_ending_time[]" id="available_ending_time" onchange="timeslots()" value="" />
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))): ?>
                                    <div class="card-footer text-center">
                                        <button type="Submit" class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                                        <button type="button" id="redSaveData" class="red-button btn btn-danger" style="display: none;">Submit</button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Doctor Slots List</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id="showData">
                                        <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Range -->
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header">
                            <h4>Doctor Time Slot</h4>
                        </div>
                        <div class="tab-pane" id="Range">
                            <ul style="margin-right: 30px;">
                                <form method="POST" id="FormId1" action="" enctype="multipart/form-data" class="">
                                    <div class="card-body">
                                        <input type="hidden" name="Timeslot_id1" id="Timeslot_id1" value="">
                                        <div class="row">
                                            <?php
                                            $SessionUserId = $_SESSION['security_id'] ?? '';
                                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                                            if ($SessionUserId == "1") {
                                            ?>
                                                <div class="form-group col-lg-4 col-sm-12">
                                                    <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                                    <select class="form-control form-select" name="organizations1" id="organizations1" onchange="getorgdoctor1()">
                                                        <option value="">Select Organization</option>
                                                        <?php
                                                        $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                                        while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                                                            // echo "$ResOrganization->org_id";
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
                                        </div>
                                        <div class="row ">
                                            <?php if ($sec_type == 'A' || $sec_type == 'SA') { /* SA_FATAL_FIXED_B_547 */ ?>
                                                <!-- Admin: only doctors dropdown -->
                                                <div class="form-group col-lg-4 col-sm-12">
                                                    <label>Name <span class="text-danger">*</span></label>
                                                    <select class="form-control form-select" name="doc_name1" id="doc_name1">
                                                        <option value="">Select Option</option>
                                                        <?php foreach ($doctors as $d): ?>
                                                            <option value="<?= $d->doc_id ?>"> <b><?= htmlspecialchars($d->doctor_name) ?></b> - <?= htmlspecialchars($d->doc_registration_number) ?> </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            <?php } else { ?>
                                                <!-- Non-admin (Receptionist/Doctor) -->
                                                <div class="form-group col-lg-4 col-sm-12">
                                                    <label>Name <span class="text-danger">*</span></label>
                                                    <select class="form-control form-select" name="doc_name1" id="doc_name1">
                                                        <option value="">Select Option</option>
                                                        <?php foreach ($doctors as $d): ?>
                                                            <option value="<?= $d->doc_id ?>"> <b><?= htmlspecialchars($d->doctor_name) ?></b> - <?= htmlspecialchars($d->doc_registration_number) ?> </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group col-lg-4 col-sm-12">
                                                <label>Form Date<span class="text-danger">*</span> </label>
                                                <input type="date" class="form-control" name="available_date1" id="available_date1" onchange="getDays()">
                                            </div>
                                            <div class="form-group col-lg-4 col-sm-12">
                                                <label>To Date<span class="text-danger">*</span> </label>
                                                <input type="date" class="form-control" name="end_date1" id="end_date1" onchange="getDays()">
                                            </div>
                                            <div class="form-group col-lg-4 col-sm-12">

                                                <input type="hidden" id="weekdays" name="weekdays">
                                                <input type="hidden" id="avaliable_dates" name="avaliable_dates">
                                                <input type="hidden" id="selectedDaysIndex" name="selectedDaysIndex">
                                                <div class="weekDays-selector" onclick="getDays()" id="selectalect">
                                                    <input type="checkbox" id="weekday-sun" class="weekday" value='sun' />
                                                    <label for="weekday-sun">Sun</label>
                                                    <input type="checkbox" id="weekday-mon" class="weekday" value='mon' />
                                                    <label for="weekday-mon">Mon</label>
                                                    <input type="checkbox" id="weekday-tue" class="weekday" value='tue' />
                                                    <label for="weekday-tue">Tue</label>
                                                    <input type="checkbox" id="weekday-wed" class="weekday" value='wed' />
                                                    <label for="weekday-wed">Wed</label>
                                                    <input type="checkbox" id="weekday-thu" class="weekday" value='thu' />
                                                    <label for="weekday-thu">Thu</label>
                                                    <input type="checkbox" id="weekday-fri" class="weekday" value='fri' />
                                                    <label for="weekday-fri">Fri</label>
                                                    <input type="checkbox" id="weekday-sat" class="weekday" value='sat' />
                                                    <label for="weekday-sat">Sat</label>
                                                </div>
                                            </div>
                                            <?php if (userCan('add', basename(__FILE__))): ?>
                                            <div class="form-group col-lg-8 col-sm-12" style="margin-top: 4px;">
                                                <a href="javascript:void(0)" class="adding-form1 float-end btn btn-primary pluse"><i class=" fas fa-plus pluse"></i></a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="adding-new-record1 card-body">
                                        <div class="row open-form1">
                                            <div class="form-group col-lg-6 col-sm-12 timepicker-max-time">
                                                <label for="">Start Time<span class="text-danger">*</span></label>
                                                <input type="time" class="form-control available_start_time1" name="available_start_time1[]" id="available_start_time1" onchange=timeslots1() value="" />
                                            </div>
                                            <div class="form-group col-lg-6 col-sm-12">
                                                <label for=""> End Time <span class="text-danger">*</span> </label>
                                                <input type="time" class="form-control " name="available_ending_time1[]" id="available_ending_time1" onchange=timeslots1() value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))): ?>
                                    <div class="card-footer text-center">
                                        <button type="submit" class="btn btn-primary" name="saveData1" id="saveData1" value="">Submit</button>
                                        <button type="button" id="redSaveData1" class="red-button btn btn-danger " style="display: none;">Submit</button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4>Doctor Time Slot</h4>
                        </div>

                        <div class="card-body" id="showData1">
                            <div class="col-12 col-md-12 table-responsive">
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <!-- </div>   -->
            <form action="" method="POST" id="deleteFormId">
                <input type="hidden" name="deleteID" id="deleteID" value="" />
            </form>
            <form action="" method="POST" id="deleteFormId1">
                <input type="hidden" name="deleteID1" id="deleteID1" value="" />
            </form>
    </section>
</div>


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <p>Name : <b class="" name="doctornameview" id="doctornameview"></b></p>
                <p>Date : <b class="" name="dateview" id="dateview"></b></p>
                <p>Doctors Times : <b class="" name="dateview1" id="dateview1"></b></p>

                <div class="row">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button> -->
            <!-- <button type="button" class="btn btn-primary">Understood</button> -->
            <!-- </div> -->
        </div>
    </div>
</div>

<!-- Model 2 -->
<div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <p>Name : <b class="" name="doctornameview1" id="doctornameview1"></b></p>
                <p>FromDate : <b class="" name="dateview3" id="dateview3"></b></p>
                <p>ToDate : <b class="" name="dateview4" id="dateview4"></b></p>
                <p>Doctors Times : <b class="" name="dateview5" id="dateview5"></b></p>

                <div class="row">
                    <div class="col-10">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button> -->
            <!-- <button type="button" class="btn btn-primary">Understood</button> -->
            <!-- </div> -->
        </div>
    </div>
</div>

<?php require_once("ajax/footer.php") ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->

<script>
    // checkboxes select
    $('#myTab li').click(function(e) {

        $(this).find('a').tab('show');

        $(this).closest('ul').find('input[type="checkbox"]').prop('checked', '');
        $(this).closest('li').find('input[type="checkbox"]').prop('checked', 'checked');

        // $('#checkbox-sun').prop('checked', 'checked');

    });



    // function populateCheckboxes(dates) {
    //     const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    //     dates.forEach(date => {
    //         const dateObject = new Date(date); // Convert the date string to a Date object
    //         const dayOfWeek = daysOfWeek[dateObject.getDay()]; // Get the day of the week

    //         // Check the checkbox corresponding to the day of the week
    //         const checkboxId = `weekday-${dayOfWeek.toLowerCase()}`;
    //         const checkbox = document.getElementById(checkboxId);

    //         if (checkbox) {
    //             checkbox.checked = true;
    //         }
    //     });
    // }

    //     function getcheckeddays{

    //         $('[data-date]').each(function () {
    //     const date = $(this).data('date');
    //     const isChecked = data.includes(date);
    //     $(this).prop('checked', isChecked);
    //   });

    //         $('#weekday-sun').prop('checked', 'checked');
    //         $('#weekday-mon').prop('checked', 'checked');
    //         $('#weekday-tue').prop('checked', 'checked');
    //         $('#weekday-sun').prop('checked', 'checked');
    //         $('#weekday-sun').prop('checked', 'checked');


    //     }

    $(document).on('change', '.available_start_time', function(event) {
        const time = event.target.value;
        const splitted = time.split(":");
        const selecthours = parseInt(splitted[0]);
        const selectminutes = parseInt(splitted[1]);
        const currentDate = new Date();
        const prasenthours = currentDate.getHours();
        const prasentminutes = currentDate.getMinutes();
        const dateInputValue = $('#available_date').val();
        // alert(dateInputValue);
        const selectedDate = new Date(dateInputValue);
        if (
            selectedDate.getDate() === currentDate.getDate() &&
            selectedDate.getMonth() === currentDate.getMonth() &&
            selectedDate.getFullYear() === currentDate.getFullYear()
        ) {
            if (selecthours < prasenthours || (selecthours === prasenthours && selectminutes < prasentminutes)) {
                swal('', 'Please select the current time or later.', 'warning');
                event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            }
        }
    });

    $(document).on('change', '.available_start_time', function(event) {
        const time = event.target.value;
        const splitted = time.split(":");
        const selecthours = parseInt(splitted[0]);
        const selectminutes = parseInt(splitted[1]);
        const currentDate = new Date();
        const prasenthours = currentDate.getHours();
        const prasentminutes = currentDate.getMinutes();
        const dateInputValue = $('#available_date').val();

        if (!dateInputValue) {
            swal('', 'Please select the Available Date', 'warning');
            event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            return;
        }

        const selectedDate = new Date(dateInputValue);

        if (
            selectedDate.getDate() === currentDate.getDate() &&
            selectedDate.getMonth() === currentDate.getMonth() &&
            selectedDate.getFullYear() === currentDate.getFullYear()
        ) {
            if (selecthours < prasenthours || (selecthours === prasenthours && selectminutes < prasentminutes)) {
                swal('', 'Please select the current Time or later', 'warning');
                event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            }
        }
    });


    // currect start time for range


    $(document).on('change', '.available_start_time1', function(event) {
        const time = event.target.value;
        const splitted = time.split(":");
        const selecthours = parseInt(splitted[0]);
        const selectminutes = parseInt(splitted[1]);
        const currentDate = new Date();
        const prasenthours = currentDate.getHours();
        const prasentminutes = currentDate.getMinutes();
        const dateInputValue = $('#available_date').val();
        // alert(dateInputValue);
        const selectedDate = new Date(dateInputValue);
        if (
            selectedDate.getDate() === currentDate.getDate() &&
            selectedDate.getMonth() === currentDate.getMonth() &&
            selectedDate.getFullYear() === currentDate.getFullYear()
        ) {
            // if (selecthours < prasenthours || (selecthours === prasenthours && selectminutes < prasentminutes)) {
            //     swal('','Please select the current Time or later','warning'); 
            //     event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            // }
        }
    });

    $(document).on('change', '.available_start_time1', function(event) {
        const time = event.target.value;
        const splitted = time.split(":");
        const selecthours = parseInt(splitted[0]);
        const selectminutes = parseInt(splitted[1]);
        const currentDate = new Date();
        const prasenthours = currentDate.getHours();
        const prasentminutes = currentDate.getMinutes();
        const dateInputValue = $('#available_date1').val();

        if (!dateInputValue) {
            swal('', 'Please select the Available Date', 'warning');
            event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            return;
        }

        const selectedDate = new Date(dateInputValue);

        if (
            selectedDate.getDate() === currentDate.getDate() &&
            selectedDate.getMonth() === currentDate.getMonth() &&
            selectedDate.getFullYear() === currentDate.getFullYear()
        ) {
            // if (selecthours < prasenthours || (selecthours === prasenthours && selectminutes < prasentminutes)) {
            //     swal('','Please select the current Time or later','warning'); 
            //     event.target.value = prasenthours.toString().padStart(2, '0') + ':' + prasentminutes.toString().padStart(2, '0');
            // }
        }
    });











    $(document).on('paste', '.available_start_time', function(event) {
        event.preventDefault();
    });

    var NewInputsCount = 1;

    $("document").ready(function() {
        Getdoctorstime();
        Getdoctorstimerange();
    });


    // $(function() {
    // function getCurrentDate() {
    //     var dtToday = new Date();
    //     var month = dtToday.getMonth() + 1;
    //     var day = dtToday.getDate();
    //     var year = dtToday.getFullYear();
    //     if (month < 10)
    //         month = '0' + month;
    //     if (day < 10)
    //         day = '0' + day;
    //     return year + '-' + month + '-' + day;
    // }

    // var minDate = getCurrentDate();
    // $('#available_date').attr('min', getCurrentDate());
    // $('#available_date').attr('min', new Date().toISOString().split('T')[0]);
    // $('#available_date').on('change', function() {

    //     var selectedDate = $(this).val();
    //     var minDate = $(this).attr('min');
    // });
    // });



    function Getdoctorstime() {
        var org_id = '<?= $SessionOrgId ?>';

        $.ajax({
            url: 'ajax/doctors_time_slots/getdata.php',
            type: 'GET',
            success: function(data) {
                console.log(data);
                if (data) {
                    $("#showData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array = [0, 1, 2];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2, 3];
                    }
                    $("#tableExport1").dataTable({

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

    var org_id = '<?= $SessionOrgId ?>';

    function Getdoctorstimerange() {
        $.ajax({
            url: 'ajax/doctors_time_slots/getrange.php',
            type: 'GET',
            success: function(data) {
                console.log(data);
                if (data) {
                    $("#showData1").html(data);
                    document.getElementById("FormId1").reset();
                    var buttons_array = [0, 1, 2, 3];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2, 3, 4];
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

        $('#available_date').attr('min', minDate);
    });

    // Form one submit

    $("#FormId").submit(function() {
        event.preventDefault();
        var Timeslot_id = $("#Timeslot_id").val();
        var doc_name = $("#doc_name").val();
        var available_date = $("#available_date").val();
        var doctortime_type = $("#pills-home-tab").val();

        var organizations = $("#organizations").val();
        // alert(organizations);


        var dtToday = new Date();
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month;
        if (day < 10)
            day = '0' + day;

        var current_date = year + '-' + month + '-' + day;

        // var availableDateInput = document.getElementById('available_date');

        $('#available_date').attr('min', current_date);
        // availableDateInput.min = current_date;

        //    alert(current_date);

        if (available_date < current_date) {
            swal('', 'Selected date should be greater than the current date', 'warning')
            return false;
        }
        var available_start_time = [];
        $("input[name='available_start_time[]']").each(function() {
            available_start_time.push($(this).val());
        });

        var available_ending_time = [];
        $("input[name='available_ending_time[]']").each(function() {
            available_ending_time.push($(this).val());
        });

        if (doc_name == "") {
            swal('', 'Please select doctor name', 'warning')
            return false;
        }
        if (available_date == "") {
            swal('', 'Please select date', 'warning')
            return false;
        }


        for (let i = 0; i < available_start_time.length; i++) {
            const time = available_start_time[i];
            if (time.trim() === '') {
                swal('', 'Please select an available start time', 'warning');
                return false;
            }
        }

        const encounteredHours = new Set();

        for (let i = 0; i < available_start_time.length; i++) {
            const startTime = available_start_time[i];
            const endTime = available_ending_time[i];

            if (startTime.trim() === '') {
                swal('', 'Please select an available start time!', 'warning');
                return false;
            }

            if (endTime.trim() === '') {
                swal('', 'Please select an available end time!', 'warning');
                return false;
            }

            const startObj = new Date(`${available_date} . ${startTime}`);
            const endObj = new Date(`${available_date} . ${endTime}`);
            const startHour = startObj.getHours();
            const startMinute = startObj.getMinutes();
            const endHour = endObj.getHours();
            const endMinute = endObj.getMinutes();

            // console.log(startHour);

            const key = `${startHour}:${startMinute}`;
            if (encounteredHours.has(key)) {
                swal('', 'Duplicate hours are not allowed!', 'warning');
                return false;
            } else {
                encounteredHours.add(key);
            }
            encounteredHours.add(startHour);

            if (startObj > endObj) {

                swal('', 'Selected start-time should be less than selected end-time!', 'warning');
                return false;
            }

            const timeDifferenceMinutes = (endObj > startObj) / (1000 * 60);
        }


        for (let i = 0; i < available_ending_time.length; i++) {
            const time2 = available_ending_time[i];
            if (time2.trim() === '') {
                swal('', 'Please select available end time!', 'warning');
                return false;
            }
        }

        for (var i = 0; i < available_start_time.length; i++) {
            var startTime = new Date(available_date + ' ' + available_start_time[i]);
            var endTime = new Date(available_date + ' ' + available_ending_time[i]);

            if (startTime >= endTime) {
                swal("Warning", 'Ending time should be greater than starting time', 'warning');
                return false;
            }
        }


        var subitem = {};

        subitem['Timeslot_id'] = Timeslot_id;
        subitem['doc_name'] = doc_name;
        subitem['available_date'] = available_date;
        subitem['doctortime_type'] = doctortime_type;
        subitem['organizations'] = organizations;
        subitem['available_start_time'] = available_start_time;
        subitem['available_ending_time'] = available_ending_time;
        //   console.log(subitem);

        $.ajax({
            url: 'ajax/doctors_time_slots/inserupdate.php',
            type: 'POST',
            data: subitem,
            success: function(data) {
                console.log(data);
                if (data == 1) {
                    swal("success", "Record Added Successfully", "success");
                    $("#Timeslot_id").val('');
                    Getdoctorstime();
                    Getdoctorstimerange();
                    clearData();
                } else if (data == 2) {
                    swal({
                        title: '',
                        text: ' Record Updated  Successfully',
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
                        $("#multi_id").val('');
                        $("#Timeslot_id").val('');
                        $("#FormId")[0].reset();
                        Getdoctorstime();
                        Getdoctorstimerange();
                        clearData();
                        location.reload(); // Refresh the page
                    });
                } else if (data == 3) {
                    swal('Warning', 'Doctor date and times already exist', 'warning');
                } else if (data == 4) {
                    swal('Warning', "The selected time slot is already blocked in previous slots", 'warning');
                } else {
                    swal("warning", "error");
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    });

    function clearData() {
        $('.adding-new-record').html('');
        NewInputsCount = 1;
        $('.adding-new-record').append('<div class="row open-form">\
        <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Starting Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time' + NewInputsCount + '" onchange=timeslots() value="" >\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> Ending Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time[]" id="available_ending_time' + NewInputsCount + '" value="" onchange=timeslots() maxlength="10" >\
                </div>\
        </div>');
        NewInputsCount++;
    }

    function timeslots() {
        var st_array = [];
        $("input[name='available_start_time[]']").each(function() {
            st_array.push($(this).val());
        });

        var et_array = [];
        $("input[name='available_ending_time[]']").each(function() {
            et_array.push($(this).val());
        });

        for (let i = 0; i < st_array.length; i++) {
            var currentDate = new Date();
            var startTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), st_array[i].split(':')[0], st_array[i].split(':')[1]);
            var endTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), et_array[i].split(':')[0], et_array[i].split(':')[1]);
            var timeDifference = (endTime - startTime) / (1000 * 60);

            if (timeDifference < 15) {
                swal({
                    title: "Warning",
                    text: "The time slot must be at least 15 minutes long.",
                    icon: 'warning'
                }).then(() => {
                    showRedSubmitButton();
                });
                return;
            }

            if (i > 0) {
                var prevEndTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), et_array[i - 1].split(':')[0], et_array[i - 1].split(':')[1]);
                var nextStartTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), st_array[i].split(':')[0], st_array[i].split(':')[1]);
                var timeDifferenceBetweenSlots = (nextStartTime - prevEndTime) / (1000 * 60);

                if (timeDifferenceBetweenSlots < 15) {
                    swal({
                        title: "Warning",
                        text: "The next start time should be at least 15 minutes greater than the previous end time.",
                        icon: 'warning'
                    }).then(() => {
                        showRedSubmitButton();
                    });
                    return;
                }
            }

            if (et_array[i] !== "" && endTime <= startTime) {
                swal({
                    title: "Warning",
                    text: "End time should be greater than start time.",
                    icon: 'warning'
                }).then(() => {
                    showRedSubmitButton();
                });
                return;
            }
        }

        // If all time slots are valid, enable the original submit button
        enableSubmitButton();
    }


    function showRedSubmitButton() {
        $("#saveData").hide();
        $("#redSaveData").show();
    }

    function enableSubmitButton() {
        $("#saveData").prop("disabled", false).show();
        $("#redSaveData").hide();
    }

    // Attach the timeslots function to the redSaveData button click
    $("#redSaveData").click(function() {
        timeslots();
    });


    function viewdoctor(Timeslot_id, doc_name, available_date, available_start_time, available_ending_time) {
        $("#doctornameview").html(doc_name);
        $("#dateview").html(available_date);

        getdoctorMeditime(Timeslot_id);

    }


    function getdoctorMeditime(Timeslot_id) {
        $.ajax({
            url: 'ajax/doctors_time_slots/getdoctorMedi.php',
            type: 'post',
            data: {
                'Timeslot_id': Timeslot_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);lot.ending_Time).join(',');
                // console.log(formattedData);

                var timeformatted = data.map(slot => slot.starting_Time + "-" + slot.ending_Time).join(',');
                $("#dateview1").html(timeformatted);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }


    function editdoctor(Timeslot_id, doc_name, available_date, available_start_time, available_ending_time, organizations) {
        window.scrollTo(0, 0);

        $("#Timeslot_id").val(Timeslot_id);
        $("#doc_name").val(doc_name);
        $("#available_date").val(available_date);
        $("#available_start_time").val(available_start_time); // 🟢 add this
        $("#available_ending_time").val(available_ending_time); // 🟢 add this
        $("#organizations").val(organizations);

        getdoctorMedi(Timeslot_id);
    }


    function getdoctorMedi(Timeslot_id) {
        $.ajax({
            url: 'ajax/doctors_time_slots/getdoctorMedi.php',
            type: 'post',
            data: {
                'Timeslot_id': Timeslot_id,
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('.adding-new-record').html('');
                NewInputsCount = 1;

                $.each(data, function(key, val) {
                    let minusBtn = '';
                    if (data.length > 1) { // show minus button only if more than 1 row
                        minusBtn = '<span style="margin-right: 100px;"><a href="javascript:void(0)" class="delet-btn float-end btn btn-danger add_row"><i class="fas fa-minus"></i></a></span>';
                    }

                    $('.adding-new-record').append('<div class="row open-form">\
                    <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-12 col-sm-12" style="margin-top:-20px;">\
                    ' + minusBtn + '\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Start Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time' + NewInputsCount + '" onchange=timeslots() value="' + val.starting_Time + '">\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> End Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time[]" id="available_ending_time' + NewInputsCount + '" onchange=timeslots() value="' + val.ending_Time + '">\
                </div>\
                </div>');
                    NewInputsCount++;
                });

            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // delete
    function deletedoctor(Timeslot_id, multi_id, doc_name) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete this time slots record?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/doctors_time_slots/deleted.php',
                    type: 'POST',
                    data: {
                        'Timeslot_id': Timeslot_id,
                        'multi_id': multi_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            swal("success", " Record Deleted Successfully", 'success');

                            // ✅ clear static fields
                            $("#Timeslot_id").val('');
                            $("#doc_name").val('');
                            $("#available_date").val('');
                            $("#available_start_time").val('');
                            $("#available_ending_time").val('');
                            $("#organizations").val('');

                            // ✅ clear dynamic fields too
                            $('.adding-new-record').html('');
                            NewInputsCount = 1;

                            // (optional: keep one empty row visible if you want)
                            $('.adding-new-record').append('<div class="row open-form">\
                            <input type="hidden" name="lastid[]" id="lastid" value="" >\
                            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                                <label for="">Start Time<span class="text-danger">*</span></label>\
                                <input type ="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time0" onchange=timeslots() value="">\
                            </div>\
                            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                                <label for=""> End Time <span class="text-danger">*</span></label>\
                                <input type ="time" class="form-control " name="available_ending_time[]" id="available_ending_time0" onchange=timeslots() value="">\
                            </div>\
                        </div>');

                            Getdoctorstime();
                            Getdoctorstimerange();
                        } else {
                            swal('', 'Error occured. Please try again', 'error')
                        }
                    },

                    error: function(err) {
                        console.log(err);
                    }
                });

                $('#deleteID').val(Timeslot_id);
                swal('', "\"" + doc_name + "\" Record Deleted Successfully", 'success')
                    .then((result) => {
                        $('#deleteFormId').submit();
                    });
            }
        });
    }

    $(document).on('click', '.delet-btn1', function() {
        var openFormLength1 = $(".open-form1").length;
        $(this).closest('.open-form1').remove();
        if (openFormLength1 == 1) {
            $('.adding-new-record1').append('<div class="row open-form1">\
                    <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Start Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time1" name="available_start_time1[]" id="available_start_time1" onchange=timeslots1() value="">\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> End Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time1[]" id="available_ending_time1" onchange=timeslots1() value="" maxlength="10" >\
                </div>\
                </div>');
            NewInputsCount = 1;
        }
    });

    $(document).on('click', '.adding-form1', function() {
        $('.adding-new-record1').append('<div class="row open-form1">\
                    <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-12 col-sm-12" style="margin-top:-12px;">\
                    <span style="margin-right: 100px;"><a href="javascript:void(0)" class="delet-btn1 float-end btn btn-danger add_row"><i class=" fas fa-minus"></i></a></span>\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Start Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time1" name="available_start_time1[]" id="available_start_time1' + NewInputsCount + '" onchange=timeslots1() value="">\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> End Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time1[]" id="available_ending_time1' + NewInputsCount + '" onchange=timeslots1() value="" maxlength="10" >\
                </div>\
                </div>');

        NewInputsCount++;
    });


    $(document).on('click', '.delet-btn', function() {
        var openFormLength = $(".open-form").length;
        $(this).closest('.open-form').remove();
        if (openFormLength == 1) {
            $('.adding-new-record').append('<div class="row open-form">\
                    <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Start Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time" onchange=timeslots() value="">\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> End Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time[]" id="available_ending_time" onchange=timeslots() value="" maxlength="10" >\
                </div>\
            </div>');
            NewInputsCount = 1;
        }
    });

    $(document).on('click', '.adding-form', function() {
        $('.adding-new-record').append('<div class="row open-form">\
                    <input type="hidden" name="lastid[]" id="lastid" value="" >\
                    <div class="form-group col-lg-12 col-sm-12" style="margin-top:-12px;">\
                    <span style="margin-right: 100px;"><a href="javascript:void(0)" class="delet-btn float-end btn btn-danger add_row"><i class=" fas fa-minus"></i></a></span>\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Start Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time" name="available_start_time[]" id="available_start_time' + NewInputsCount + '" onchange=timeslots() value="">\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> End Time <span class="text-danger">*</span></label>\
                    <input type ="time" class="form-control " name="available_ending_time[]" id="available_ending_time' + NewInputsCount + '" onchange=timeslots() value="" maxlength="10" >\
                </div>\
                </div>');

        NewInputsCount++;
    });


    // multi time slat

    // // last date disable
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

        $('#available_date1').attr('min', minDate);
    });

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

        $('#end_date1').attr('min', minDate);
    });


    var selectedDays = [];
    var selectedDates = [];

    function getDayName(dayIndex) {
        var days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        return days[dayIndex];
    }

    function getDayIndex(dayName) {
        var days = {
            "sun": 0,
            "mon": 1,
            "tue": 2,
            "wed": 3,
            "thu": 4,
            "fri": 5,
            "sat": 6
        };
        return days[dayName];
    }

    function getDays() {
        var startDateInput = document.getElementById("available_date1");
        console.log(startDateInput);
        var endDateInput = document.getElementById("end_date1");
        console.log(endDateInput);

        var startDate = moment(startDateInput.value);
        var endDate = moment(endDateInput.value);

        // if (selectedDates.length > 0) {
        //     startDateInput.value = selectedDates[0];
        //     endDateInput.value = selectedDates[selectedDates.length - 1];
        // } else {
        //     // If there are no selected dates, you can set some default values or leave them unchanged.
        //     // For example, setting them to empty strings:
        //     startDateInput.value = "";
        //     endDateInput.value = "";
        // }

        // if (!startDate.isValid() || !endDate.isValid()) {
        //     swal('', 'Please enter valid start and end dates', 'warning');
        //     // Clear the global variables
        //     selectedDays = [];
        //     // selectedDates = [];
        //     return;
        // }

        if (startDate >= endDate) {
            swal('', 'Start date must be before end date', 'warning');
            // Clear the global variables
            selectedDays = [];
            selectedDates = [];
            return;
        }

        var Mapping = {
            "weekday-sun": 0,
            "weekday-mon": 1,
            "weekday-tue": 2,
            "weekday-wed": 3,
            "weekday-thu": 4,
            "weekday-fri": 5,
            "weekday-sat": 6
        };

        selectedDays = [];
        for (let i = 0; i < 7; i++) {
            let checkbox = document.getElementById("weekday-" + getDayName(i));
            if (checkbox.checked) {
                let mappedDay = Mapping[checkbox.id];
                selectedDays.push(mappedDay);
            }
        }


        selectedDates = [];
        selectedDaysIndex = [];
        let currentDate = startDate.clone();

        while (currentDate <= endDate) {
            const dayIndex = getDayIndex(currentDate.format('ddd').toLowerCase());
            console.log(dayIndex);
            if (selectedDays.includes(dayIndex)) {
                selectedDaysIndex.push(dayIndex);
                selectedDates.push(currentDate.format("YYYY-MM-DD"));
            }
            currentDate.add(1, 'days');
            console.log(currentDate);
        }

        // Check if selectedDates is empty
        // if (selectedDates.length === 0) {
        //     swal('', 'Please Select Corresponding Days', 'warning');
        //     return false;
        // }

        console.log("Selected Days:", selectedDays);
        console.log("Selected Dates:", selectedDates);

        updateSelectedDaysInput();
        updateSelectedDaysInput1()
    }

    function updateSelectedDaysInput() {

        var selectedDaysInput = document.getElementById("weekdays");

        var selectedDaysString = selectedDays.join(",");

        weekdays.value = selectedDaysString;
    }

    function updateSelectedDaysInput1() {

        var selectedDaysInput = document.getElementById("avaliable_dates");

        var selectedDatesString = selectedDates.join(",");

        avaliable_dates.value = selectedDatesString;
    }

    // FIX_B_077 — renamed from duplicate updateSelectedDaysInput1
    function updateSelectedDaysIndex() {

        var selectedDaysindexInput = document.getElementById("selectedDaysIndex");

        var selectedDatesindexString = selectedDates.join(",");

        selectedDaysIndex.value = selectedDatesindexString;
    }

    // form two submit

    var NewInputsCount = 1;
    $("#FormId1").submit(function() {
        event.preventDefault();
        var Timeslot_id1 = $("#Timeslot_id1").val();
        var multi_id = $("#multi_id").val();
        var doc_name1 = $("#doc_name1").val();
        var formdate1 = $("#available_date1").val();
        var fromdate2 = $("#available_date1").val();
        var doctortime_type1 = $("#pills-profile-tab").val();
        var organizations = $("#organizations1").val();
        var todate2 = $("#end_date1").val();
        var selectedDays1 = $("#weekdays").val();
        var selectedDaysIndex1 = $("#selectedDaysIndex").val();

        var available_start_time1 = [];
        $("input[name='available_start_time1[]']").each(function() {
            available_start_time1.push($(this).val());
        });

        var available_ending_time1 = [];
        $("input[name='available_ending_time1[]']").each(function() {
            available_ending_time1.push($(this).val());
        });

        if (doc_name1 == "") {
            swal('', 'Please Select Doctor Name', 'warning')
            return false;
        }

        if (formdate1 == "") {
            swal('', 'Please Select Doctor From Date', 'warning')
            return false;
        }

        if (todate2 == "") {
            swal('', 'Please Select Doctor To Date', 'warning')
            return false;
        }

        if (selectedDays == "") {
            swal('', 'Please Select Corresponding Days', 'warning')
            return false;
        }

        if (available_start_time1 == "") {
            swal('', 'Please Select Start Time', 'warning')
            return false;
        }

        if (available_ending_time1 == "") {
            swal('', 'Please Select End Time', 'warning')
            return false;
        }

        for (let i = 0; i < available_ending_time1.length; i++) {
            const monthtime2 = available_ending_time1[i];
            if (monthtime2.trim() === '') {
                swal('', 'All fields are required', 'warning');
                return false;
            }
        }

        for (var i = 0; i < available_start_time1.length; i++) {
            var startTime1 = available_start_time1[i];
            var endTime1 = available_ending_time1[i];

            if (startTime1 >= endTime1) {
                swal('', "Ending time should be greater than starting time", 'warning');
                return false;
            }
        }

        for (let i = 0; i < available_start_time1.length; i++) {
            const monthtime = available_start_time1[i];
            if (monthtime.trim() === '') {
                swal('', 'All fields are required', 'warning');
                return false;
            }
        }

        const encounteredHours = new Set();

        for (let i = 0; i < available_start_time1.length; i++) {
            const startTime = available_start_time1[i];
            const endTime = available_ending_time1[i];

            if (startTime.trim() === '') {
                swal('', 'Please Select Available Start Time!', 'warning');
                return false;
            }

            if (endTime.trim() === '') {
                swal('', 'Please Select Available End Time!', 'warning');
                return false;
            }

            const startObj = new Date(`${formdate1} . ${startTime}`);
            const endObj = new Date(`${formdate1} . ${endTime}`);
            const startHour = startObj.getHours();
            const startMinute = startObj.getMinutes();
            const endHour = endObj.getHours();
            const endMinute = endObj.getMinutes();

            const key = `${startHour}:${startMinute}`;
            if (encounteredHours.has(key)) {
                swal('', 'Duplicate hours are not allowed!', 'warning');
                return false;
            } else {
                encounteredHours.add(key);
            }
            encounteredHours.add(startHour);

            if (startObj > endObj) {
                swal('', 'Start time can be greater than end time!', 'warning');
                return false;
            }
            const timeDifferenceMinutes = (endObj > startObj) / (1000 * 60);
        }

        var subitem2 = {};

        subitem2['Timeslot_id1'] = Timeslot_id1;
        subitem2['multi_id'] = multi_id;
        subitem2['doc_name1'] = doc_name1;
        subitem2['weeks'] = JSON.stringify(selectedDates);
        console.log(JSON.stringify({
            selectedDates
        }));
        subitem2['available_date1'] = fromdate2;
        subitem2['end_date1'] = todate2;
        subitem2['doctortime_type1'] = doctortime_type1;
        subitem2['available_start_time1'] = available_start_time1;
        subitem2['available_ending_time1'] = available_ending_time1;
        subitem2['organizations'] = organizations;
        subitem2['weekdays'] = selectedDays1;
        subitem2['selectedDaysIndex'] = selectedDaysIndex;
        // alert(selectedDays1);

        console.log(subitem2);

        if (doc_name1 == "" || available_date1 == "" || available_start_time1 == "" || available_ending_time1 == "") {
            swal('', 'All fields Required', 'warning')
            return false;
        }

        $.ajax({
            url: 'ajax/doctors_time_slots/doctor_dates_insert.php',
            type: 'POST',
            data: subitem2,
            success: function(data) {

                console.log(data);
                if (data == 1) {
                    swal({
                        title: '',
                        text: ' Record Added  Successfully',
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
                        $("#multi_id").val('');
                        $("#Timeslot_id1").val('');
                        $("#FormId1")[0].reset();
                        Getdoctorstimerange();
                        Getdoctorstime();
                        clearData1();
                        //    location.reload(); // Refresh the page
                    });

                } else if (data == 2) {
                    swal({
                        title: '',
                        text: ' Record Updated  Successfully',
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
                        $("#multi_id").val('');
                        $("#Timeslot_id1").val('');
                        $("#FormId1")[0].reset();
                        Getdoctorstimerange();
                        Getdoctorstime();
                        clearData1();
                        // location.reload(); // Refresh the page
                    });
                } else if (data == 3) {
                    swal('', "Date And Times  Already Exists", 'warning');
                } else if (data == 4) {
                    swal('Warning', "The selected slot is already blocked in previous slots.", 'warning');
                } else {
                    swal('Warning', "Error", 'warning');
                }
            },
            error: function(err) {
                console.log(err);
            }
        });

    });



    function viewmultidoctor(multi_id, doc_name1, fromdate, enddate, start_time, end_time) {
        $("#doctornameview1").html(doc_name1);
        $("#dateview3").html(fromdate);
        $("#dateview4").html(enddate);

        getmultidoctorMeditime(multi_id);

    }

    function getmultidoctorMeditime(multi_id) {
        $.ajax({
            url: 'ajax/doctors_time_slots/getdoctorMedi_multi.php',
            type: 'POST',
            data: {
                'multi_id': multi_id
            },
            dataType: 'json',
            success: function(data) {

                console.log(data);
                var timeformatted = data.map(slot => slot.start_time + "-" + slot.end_time).join(',');
                $("#dateview5").html(timeformatted);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function editdoctorrange(Timeslot_id1, selectedDates, multi_id, doc_name1, fromdate2, todate2, selectedDays, available_start_time1, available_ending_time1, organizations) {
        $("#Timeslot_id1").val(Timeslot_id1);
        // alert(Timeslot_id1);
        $("#multi_id").val(multi_id);
        // alert(multi_id);
        $("#doc_name1").val(doc_name1);
        $("#available_date1").val(fromdate2);
        // alert(fromdate2);
        $("#end_date1").val(todate2);
        $("#available_start_time1").val(available_start_time1);
        $("#available_ending_time1").val(available_ending_time1);

        $("#organizations1").val(organizations);
        $("#avaliable_dates").val(selectedDates);
        // alert(selectedDates);

        $("#weekdays").val(selectedDays);

        for (let i = 0; i < selectedDays.length; i++) {
            const dayIndex = selectedDays[i];
            const id = `weekday-${getDayName(dayIndex)}`;
            const checkbox = document.getElementById(id);

            if (checkbox) {
                checkbox.checked = true;
            } else {
                console.log(`Checkbox with ID ${id} not found.`);
            }
        }

        console.log("Selected Dates in editdoctorrange:", selectedDates);


        getDays(selectedDates);

        getmultidoctorMedi(multi_id);
    }

    function getDayName(dayIndex) {
        var days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        return days[dayIndex];
    }


    function getmultidoctorMedi(multi_id) {




        $.ajax({
            url: 'ajax/doctors_time_slots/getdoctorMedi_multi.php',
            type: 'POST',
            data: {
                'multi_id': multi_id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $('.adding-new-record1').html('');
                NewInputsCount = 1;
                $.each(data, function(key, val) {
                    $('.adding-new-record1').append('<div class="row open-form1">\
            <input type="hidden" name="lastid[]" id="lastid" value="" >\
            <div class="form-group col-lg-12 col-sm-12" style="margin-top:-12px;">\
            <span style="margin-right: 100px;"><a href="javascript:void(0)" class="delet-btn1 float-end btn btn-danger add_row"><i class=" fas fa-minus"></i></a></span>\
            </div>\
            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
            <label for="">Starting Time<span class="text-danger">*</span></label>\
                <input type ="time" class="form-control available_start_time1" name="available_start_time1[]" id="available_start_time1' + NewInputsCount + '" onchange=timeslots1() value="' + val.start_time + '">\
            </div>\
            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                <label for=""> Ending Time <span class="text-danger">*</span></label>\
            <input type ="time" class="form-control " name="available_ending_time1[]" id="available_ending_time1' + NewInputsCount + '" onchange=timeslots1() value="' + val.end_time + '" maxlength="10" >\
        </div>\
        </div>');

                    NewInputsCount++;

                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function deleterange(multi_id, doc_name) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete this Time Slots Record?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/doctors_time_slots/deleted_range.php',
                    type: 'POST',
                    data: {
                        'multi_id': multi_id
                    },
                    success: function(data) {
                        if (data == 1) {

                            // ✅ Clear static fields
                            $("#Timeslot_id1").val('');
                            $("#multi_id").val('');
                            $("#doc_name1").val('');
                            $("#available_date1").val('');
                            $("#end_date1").val('');
                            $("#available_start_time1").val('');
                            $("#available_ending_time1").val('');
                            $("#organizations1").val('');
                            $("#avaliable_dates").val('');
                            $("#weekdays").val('');

                            // ✅ Clear dynamic rows
                            $('.adding-new-record1').html('');
                            NewInputsCount = 1;

                            // ✅ Optionally, keep one empty row ready
                            $('.adding-new-record1').append('<div class="row open-form1">\
                            <input type="hidden" name="lastid1[]" id="lastid1" value="" >\
                            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                                <label for="">Starting Time<span class="text-danger">*</span></label>\
                                <input type="time" class="form-control available_start_time1" name="available_start_time1[]" id="available_start_time1' + NewInputsCount + '" onchange=timeslots1() value="" >\
                            </div>\
                            <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                                <label for="">Ending Time<span class="text-danger">*</span></label>\
                                <input type="time" class="form-control" name="available_ending_time1[]" id="available_ending_time1' + NewInputsCount + '" onchange=timeslots1() value="" >\
                            </div>\
                        </div>');
                            NewInputsCount++;

                            swal('', 'Record Deleted Successfully', 'success');

                            // Refresh the doctor slots
                            Getdoctorstimerange();
                            Getdoctorstime();
                        } else {
                            swal('', 'Error occurred. Please try again', 'error');
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            }
        });
    }



    function clearData1() {
        $('.adding-new-record1').html('');
        NewInputsCount = 1;
        $('.adding-new-record1').append('<div class="row open-form">\
                    <input type="hidden" name="lastid1[]" id="lastid1" value="" >\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                    <label for="">Starting Time<span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control available_start_time" name="available_start_time1[]" id="available_start_time1' + NewInputsCount + '" onchange=timeslots1() value="" >\
                    </div>\
                    <div class="form-group col-lg-6 col-sm-12" style="margin-top:-40px;">\
                        <label for=""> Ending Time <span class="text-danger">*</span></label>\
                        <input type ="time" class="form-control " name="available_ending_time1[]" id="available_ending_time1' + NewInputsCount + '" onchange=timeslots1() value=""  >\
                  </div>\
        </div>');
        NewInputsCount++;
    }


    function timeslots1() {
        var st_array = [];
        $("input[name='available_start_time1[]']").each(function() {
            st_array.push($(this).val());
        });

        var et_array = [];
        $("input[name='available_ending_time1[]']").each(function() {
            et_array.push($(this).val());
        });

        for (let i = 0; i < st_array.length; i++) {
            var currentDate = new Date();
            var startTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), st_array[i].split(':')[0], st_array[i].split(':')[1]);
            var endTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), et_array[i].split(':')[0], et_array[i].split(':')[1]);
            var timeDifference = (endTime - startTime) / (1000 * 60);

            if (timeDifference < 15) {
                swal({
                    title: "Warning",
                    text: "The time slot must be at least 15 minutes long.",
                    icon: 'warning'
                }).then(() => {
                    showRedSubmitButton();
                });
                return;
            }

            if (i > 0) {
                var prevEndTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), et_array[i - 1].split(':')[0], et_array[i - 1].split(':')[1]);
                var nextStartTime = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate(), st_array[i].split(':')[0], st_array[i].split(':')[1]);
                var timeDifferenceBetweenSlots = (nextStartTime - prevEndTime) / (1000 * 60);

                if (timeDifferenceBetweenSlots < 15) {
                    swal({
                        title: "Warning",
                        text: "The next start time should be at least 15 minutes greater than the previous end time.",
                        icon: 'warning'
                    }).then(() => {
                        showRedSubmitButton();
                    });
                    return;
                }
            }

            if (et_array[i] !== "" && endTime <= startTime) {
                swal({
                    title: "Warning",
                    text: "End time should be greater than start time.",
                    icon: 'warning'
                }).then(() => {
                    showRedSubmitButton();
                });
                return;
            }
        }

        // If all time slots are valid, enable the original submit button
        enableSubmitButton();
    }

    function getorgdoctor() {
        var org_id = $('#organizations').val();
        $.ajax({
            url: 'ajax/doctors_time_slots/getorgdoctor.php',
            type: 'POST',
            data: {
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '<option value=""> Select  Option</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.doc_id + '"> ' + val.doctor_name + '-( ' + val.doc_registration_number + ') </option>';
                });
                $("#doc_name").html(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function getorgdoctor1() {
        var org_id = $('#organizations1').val();
        $.ajax({
            url: 'ajax/doctors_time_slots/getorgdoctor1.php',
            type: 'POST',
            data: {
                org_id: org_id
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data);
                var optionData = '<option value=""> Select  Option</option>';
                $.each(data, function(key, val) {
                    optionData += '<option value="' + val.doc_id + '"> ' + val.doctor_name + '-( ' + val.doc_registration_number + ') </option>';
                });
                $("#doc_name1").html(optionData);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
</script>