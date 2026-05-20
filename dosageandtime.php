<?php
require_once("ajax/header.php");

?>

<style>
    .btn-group, .btn-group-vertical {
    position: relative;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    vertical-align: middle;
    margin-top:20px;
   
};

    </style>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Services</h4>
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
            <li class="breadcrumb-item">Dosage and Time</li>
        </ul>
        
        <ul class="breadcrumb breadcrumb-style" >
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">
                    
                </div>
            </li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Dosage and Time</h4>
                </div>
                
                <form method="POST" id="DosageandTimeFormId" action="" >
                    <input type="hidden" name="doseandtime_id" id="doseandtime_id" value="" >
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12">
                                <label >Dosage <span id="name" class="text-danger" >*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                        <select class="form-select" name="dosage" id="dosage" onchange="getintakeperiod(this.value);">
                                    <option value="">-Select-</option>
                                    <?php
                                        $getMenus = mysqli_query($conn, "SELECT * FROM dose WHERE Status='1' ORDER BY dose_id ASC") or die(mysqli_error($conn));
                                        while ($resMenus = mysqli_fetch_object($getMenus)) {
                                        ?>
                                            <option value="<?=$resMenus->dose_id?>"><?= $resMenus->morning ?> - <?= $resMenus->afternoon ?> - <?= $resMenus->evening ?></option>
                                        <?php
                                        }
                                        ?>
                                 </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label> In-take-Period <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/dosage.jpeg" alt="dosage Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                    <select class="form-select" name="Intake_period" id="Intake_period" onchange="gettime(this.value);">
                                        <option value="">-Select-</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group  col-lg-4 col-sm-12 " >
                                <label for="Percent"> Time <span class="text-danger">*</span> </label>
                                <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                         <i class="fas fa-clock"></i>
                                        </div>
                                      </div>
                                    <select class="form-select" name="time" id="time" >
                                        <option value="">-Select-</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-8 col-sm-12">
                                <label >Dosage Combination </label>
                                <input type="text" class="form-control" name="dosage_combination" id="dosage_combination" value="" disabled>
                            </div>
                            <?php
                            $SessionUserId = $_SESSION['security_id'] ?? '';
                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                            ?>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="organizations" class="Organization">Organization <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-buildings-fill"></i>
                                </span>
                                <select class="form-control form-select" name="organizations" id="organizations">
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

                            <?php
                            }
                            ?>
                        </div>
                    </div>  
                           
                    <div class="card-footer text-center">
                        <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Dosage and Time List</h4>
                </div>

                <div class="card-body" id="showMenusData">
                    <div class="col-12 col-md-12 table-responsive">
                     
                        
                    </div>
                </div>
            </div>

        </div>

        <form action="" method="POST" id="deleteFormId">
            <input type="hidden" name="deleteID" id="deleteID" value="" />
        </form>

    </section>

</div>

<?php require_once("ajax/footer.php") ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script>
    $("document").ready(function() {
        Getservies();
        
    });
    var org_id = '<?=$SessionOrgId ?>';

    function Getservies() {
        $.ajax({
            url: 'ajax/service/getdosageandtime.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("DosageandTimeFormId").reset();
                    var buttons_array =  [0, 1, 2, 3]; 
                    if(org_id == "0"){
                                buttons_array = [0, 1, 2, 3, 4];
                        }
                    $("#tableExport1").dataTable({
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

   
    $("#DosageandTimeFormId").submit(function(e) {
       
        var doseandtime_id = $("#doseandtime_id").val();
        var dosage = $("#dosage").val().trim();
        var Intake_period = $("#Intake_period").val();
        var time = $("#time").val();
        var dosage_combination = $("#dosage_combination").val();
        var organizations = $("#organizations").val();

        if(dosage == "") {
            swal('',"Please select Dosage ",'warning')
        return false; 
        }

        if(Intake_period == "") {
            swal('',"Please select In take period",'warning')
        return false;
        }

        if(time == "") {
            swal('',"Please select Time",'warning')
        return false;
        }

        
        if(dosage != "" || Intake_period != "" || time != "") {
            event.preventDefault();
            $.ajax({
                url: 'ajax/service/adddosageandtime.php',
                type: 'POST',
                data: {
                    'doseandtime_id': doseandtime_id,
                    'dosage': dosage,
                    'Intake_period': Intake_period,
                    'time': time,
                    'dosage_combination' : dosage_combination,
                    'organizations': organizations
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', "Record Added Successfully",'success');
                        $("#doseandtime_id").val('');
                        $("#time").empty();
                        $("#time").html('<option value="">-Select-</option>');
                        Getservies();
                    } else if(data == 2) {
                        swal('', "Record Updated Successfully",'success');
                        $("#doseandtime_id").val('');
                        $("#time").empty();
                        $("#time").html('<option value="">-Select-</option>');
                        Getservies();
                    }
                    else if(data == 3) {
                        
                        swal('' , " Dosage and Time Combination Already Exists!",'warning');
                       
                    } else {
                        swal('','Error . Please try again', );
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });
        }

    })

    function editservices(doseandtime_id, dosage, Intake_period, dosage_combination, organizations) {
        $("#doseandtime_id").val(doseandtime_id);
        $("#dosage").val(dosage).trigger("change");
        setTimeout(function() {
            $("#Intake_period").val(Intake_period).trigger("change");
        }, 500);
        $("#dosage_combination").val(dosage_combination);
        $("#organizations").val(organizations);
    }

    function deleteservices(doseandtime_id, dosage_combination) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete \"" +dosage_combination+ "\" Record  !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/service/deletedosageandtime.php',
                    type: 'POST',
                    data: {
                        'doseandtime_id':doseandtime_id
                    },
                    success: function(data) {
                        console.log(data);
                        if(data == 1) {
                            swal('',"Record Deleted Successfully", 'success');
                            Getservies();
                        } else {
                            swal('','Error . Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(doseandtime_id);
                swal(''," Record Deleted Successfully", 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    // /fetching the options for In-take Period and Time

    function getintakeperiod(doseId) {
        if (doseId === "") {
            $("#Intake_period").html('<option value="">-Select-</option>');
            return;
        }
        
        $("#time").html('<option value="">-Select-</option>');
        $("#dosage_combination").val("");

        $.ajax({
            url: "ajax/service/getintakeperiod.php",
            type: "GET",
            data: { dose_id: doseId, fetch: '1'},
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#Intake_period").html('<option value="">-Select-</option>');
                    response.intakePeriods.forEach(function(period) {
                        let periodOption = `<option value="${period.id}">${period.text}</option>`;
                        $("#Intake_period").append(periodOption);
                    });
                } else {
                    alert("No data found");
                    $("#Intake_period").html('<option value="">No Data Found</option>');
                }
            },
            error: function() {
                alert("Error loading data");
                $("#Intake_period").html('<option value="">Error loading</option>');
            }
        });
    }

    function gettime(intakeId) {
        if (intakeId === "") {
            $("#time").html('<option value="">-Select-</option>');
            return;
        }

        $.ajax({
            url: "ajax/service/getintakeperiod.php",
            type: "GET",
            data: { intake_id: intakeId, fetch:'2'},
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#time").empty();
                    response.times.forEach(function(time) {
                        let timeOption = `<option value="${time.id}">${time.text}</option>`;
                        $("#time").append(timeOption);
                    });
                    const dosage = $("#dosage option:selected").text();
                    const intakePeriod = $("#Intake_period option:selected").text();
                    const time = $("#time option:selected").text();

                    let combination = [dosage, intakePeriod, time].filter(Boolean).join(" | ");

                    $("#dosage_combination").val(combination);
                } else {
                    alert("No data found");
                    $("#time").html('<option value="">No Data Found</option>');
                }
            },
            error: function() {
                alert("Error loading data");
                $("#time").html('<option value="">Error loading</option>');
            }
        });
    }

    
</script>