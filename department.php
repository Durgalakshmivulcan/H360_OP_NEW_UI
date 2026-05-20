<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

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
              <h4 class="page-title m-b-0">Departments</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Master</li>
            <li class="breadcrumb-item">Add & Modify Departments</li>
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
                    <h4>Departments</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" name="myForm">
                    <input type="hidden" name="dept_id" id="dept_id" value="" >
                    <div class="card-body">
                        <div class="row">

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="departmentName">Department Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="material-icons">local_hospital</i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="departmentName" id="departmentName" value=""/>
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="material-icons">description</i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="description" id="description" value=""/>
                            </div>
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
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'department.php') || userCan('edit', 'department.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

            <!-- <div class="card">
                <div class="card-header">
                    <h4>Departments List</h4>
                </div>

                <div class="card-body" id="showMenusData">
                    <div class="col-12 col-md-12 table-responsive">
                     </div>
                </div>
            </div> --> 

        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Departments List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="showMenusData">
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

<?php require_once("ajax/footer.php") ?>


<script>

    $("document").ready(function() {
        GetDepartment();
    });


    function GetDepartment() {
        $.ajax({
            url: 'ajax/department/getdepartment.php',
            type: 'GET',
            success: function(data) {
                var org_id = '<?=$SessionOrgId ?>';
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array =  [0, 1, 2]; 
                    if(org_id == "0"){
                                buttons_array = [0, 1, 2, 3];
                        }
                    // $("#tableExport1").dataTable().destroy();
                    $("#tableExport1").dataTable({
                        // destroy: true,
                        retrieve: true,
                        // paging: false,
                        dom: 'lBrftip',
                        // dom: '<"top"B>Rt<"bottom"flip><"clear">',
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

    $("#FormId").submit(function() {
        event.preventDefault();
        var dept_id = $("#dept_id").val();
        var organizations = $("#organizations").val();
        var departmentName = $("#departmentName").val().trim();
        var description = $("#description").val();


        if(! departmentName) {
            swal('', 'Please Enter Your DepartmentName!','warning');
            return;
        }
        if(! description) {
            swal('', 'Please Enter Your Description!','warning');
            return;
        }

        if(organizations == "") {
            swal('', 'Please Enter Your Organization Name!','warning');
            return;
        }
        
        $.ajax({
            url: 'ajax/department/AddModifyDepartments.php',
            type: 'POST',
            data: {
                'dept_id': dept_id,
                'organizations': organizations,
                'departmentName': departmentName,
                'description': description
            },
            success: function(data) {
                if(data == 1) {
                    swal('', "\"" +departmentName+ "\" Record Added Successfully", 'success');
                    $("#dept_id").val('');
                    GetDepartment();
                } else if(data == 2) {
                    swal('', "\"" +departmentName+ "\" Record Updated Successfully", 'success');
                    $("#dept_id").val('');
                    GetDepartment();
                } else if(data == 3) {
                    swal('', "\"" +departmentName+ "\" Already Exists !",'warning');
                    // GetDepartment();
                } else {
                    swal('', 'All fields Required !','warning');
                }
            },
            error: function(err)  {
                console.log(err);
            }
        });
    });

    function editDepartment(dept_id, departmentName, description, organizations) {
        window.scrollTo(0,0);
        // alert();
        $("#dept_id").val(dept_id);
        $("#organizations").val(organizations);
        $("#departmentName").val(departmentName);
        $("#description").val(description);
    }

    function deleteDepartment(dept_id, departmentName) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete \"" +departmentName+ "\" Record in Departments!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/department/DeleteDepartment.php',
                    type: 'POST',
                    data: {
                        'dept_id': dept_id
                    },
                    success: function(data) {
                        // alert(data);
                        if(data == 1) {
                            swal('', departmentName + ' Record Deleted Successfully', 'success');
                            GetDepartment();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(dept_id);
                swal('', departmentName + ' Record Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    // department name 
        $(function () {
            $("#departmentName").keypress(function (e) {
                var keyCode = e.keyCode || e.which;
                $("#departmentNameResult").html("");
                var regex = /^[A-Za-z ]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#departmentNameResult").html("Only Alphabets Allowed.");
                }
                return isValid;
         });
        });
    $(function () {
        $("#departmentName").keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
            $(this).val('');
            }
        });
    });

    
</script>