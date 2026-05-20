<?php
require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>

<style>
    .btn-group, .btn-group-vertical {
    position: relative;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    vertical-align: middle;
    margin-top: 15px;
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
                <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">Appointment List</li>
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
                    <h4>Appointment Date Wise Filter</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" name="myForm">
                    <input type="hidden" name="dept_id" id="dept_id" value="" >
                    <div class="card-body">
                        <div class="row">
                            <?php 
                                $SessionUserId = $_SESSION['security_id'] ?? '';
                                $SessionRoleId = $_SESSION['role_id'] ?? '';
                                $SessionOrgId = $_SESSION['org_id'] ?? '';

                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                                ?>
                                <div class="form-group col-lg-4 col-sm-12">
                                    <label for="organizations" class="Organization">Organization </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-buildings-fill"></i>
                                        </span>
                                        <select class="form-control form-select" name="organizations" id="organizations">
                                            <option value="">Select Organization</option>
                                            <?php
                                            $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                            while($ResOrganization = mysqli_fetch_object($GetOrganization)){
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
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="startdate">Start Date </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="startdate" id="startdate" value=""/>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="enddate">End Date </label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="enddate" id="enddate" value=""/>
                                </div>
                            </div>
                        
                        </div>
                          
                    </div>

                    <div class="card-footer text-center">
                        <!-- <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button> -->
                    </div>
                </form>
            </div>
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
        

        <form action="" method="POST" id="deleteFormId">
            <input type="hidden" name="deleteID" id="deleteID" value="" />
        </form>

    </section>

</div>



<?php require_once("ajax/footer.php") ?>

<script>

$(document).ready(function () {
    GetAppointment('', '');

    $('#organizations, #startdate, #enddate').on('change', function () {
        let startDate = $('#startdate').val();
        let endDate = $('#enddate').val();

        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            $('#startdate').val(endDate);
            startDate = endDate;
        }

        GetAppointment(startDate, endDate);
    });

});

function GetAppointment(startDate, endDate) {
    const org_id = $('#organizations').length ? $('#organizations').val() : '<?=$SessionOrgId ?>';

    $.ajax({
        url: 'ajax/AppointmentBooking/getappointmentreportsdata.php',
        type: 'POST',
        data: {
            startdate: startDate,
            enddate: endDate,
            org_id: org_id
        },
        success: function (data) {
            if (data) {
                $("#showMenusData").html(data);

                $("#tableExport1").DataTable({
                    retrieve: true,
                    destroy: true,
                    dom: 'lBrftip',
                    buttons: ['copy', 'excel', 'csv', 'pdf', 'print']
                });
            }
        },
        error: function (err) {
            console.log(err);
        }
    });
}


// Delete Data Records
function deleteAppointment(appoint_id, patient_name) {
        // alert(appoint_id);
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
                        if(data == 1) {
                            swal('', patient_name + ' Deleted Successfully', 'success');
                            GetAppointment();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
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

    function printPrescription(prescription_id,org_id, appoint_register_id) {
        if (org_id && appoint_register_id && prescription_id) {
            window.open("patientPrescription.php?ItemId=" + prescription_id+" & OrgID=" + org_id, "_blank");
        } else {
            swal("Error!", "Something went wrong. Please try again.", "error");
        }
    }

</script>