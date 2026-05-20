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
    margin-top:20px;
   
};

    </style>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Specialization</h4>
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
            <li class="breadcrumb-item">Add & Modify Specialization</li>
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
                    <h4>Specialization</h4>
                </div>
                
                <form method="POST" id="SpecializationFormId" action="" >
                    <input type="hidden" name="Specialization_id" id="Specialization_id" value="" >
                    <div class="card-body">
    <div class="row">
        <div class="form-group col-lg-6 col-sm-12">
            <label for="Specialization">Specializations <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-stethoscope"></i> <!-- Stethoscope/hospital icon -->
                    </div>
                </div>
                <input type="text" class="form-control" name="Specialization" id="Specialization" value=""/>
            </div>
        </div>   

        <?php 
        $SessionUserId = $_SESSION['security_id'] ?? '';
        $SessionRoleId = $_SESSION['role_id'] ?? '';
        $SessionOrgId = $_SESSION['org_id'] ?? '';

        if($SessionUserId == "1" && $SessionRoleId=="1"){
        ?>
        <div class="form-group col-lg-6 col-sm-12">
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
<!-- 
                    </div>   -->
                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'Specialization.php') || userCan('edit', 'Specialization.php')) { /* FIX_B_1810 */ ?><button type="button" class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

            <!-- <div class="card">
                <div class="card-header">
                    <h4>Specialization List</h4>
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
                        <h4>Specialization List</h4>
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
 // name validations

    $(function () {
        $("#Specialization").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#nameId").html("");
            var regex = /^[A-Za-z ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#nameId").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });

    $(function () {
        $("#Specialization").keyup(function () {
            var Specialization1 = $(this).val();
            if (!Specialization1.trim()) {
            $(this).val('');
            }
        });
    });

    $(function () {
    $("#Specialization").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
      });
    });


      

    $("document").ready(function() {
        GetSpecialization();
    });

    var org_id = '<?=$SessionOrgId ?>';

    function GetSpecialization() {
        $.ajax({
            url: 'ajax/specialization/view.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("SpecializationFormId").reset();
                    var buttons_array = [ 0, 1]; 
                    if(org_id == "0"){
                                buttons_array = [ 0, 1, 2];
                        }

                    $("#tableExport1").dataTable({
                        // destroy: true,
                        retrieve: true,
                        // paging: false, 
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

    $("#saveData").click(function() {
        var Specialization_id = $("#Specialization_id").val();
        var Specialization = $("#Specialization").val().trim();
        var organizations = $("#organizations").val();

        if(Specialization == "") {
        swal('',"Please Enter Specialization",'warning')
        return false; 
            }
            
        if(organizations == ""){
            swal('',"Please Select organization",'warning')
        return false;
        }

            // event.preventDefault();
            $.ajax({
                url: 'ajax/specialization/insert.php',
                type: 'POST',
                data: {
                    'Specialization_id': Specialization_id,
                    'Specialization': Specialization,
                    'organizations': organizations,
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', "  Added Successfully",'success');
                        $("#Specialization_id").val('');
                        GetSpecialization();
                    } else if(data == 2) {
                        swal('', " Record Updated Successfully",'success');
                        $("#Specialization_id").val('');
                        GetSpecialization();
                    }else if(data == 3) {
                        swal('', " Already Exists",'warning');
                    }else {
                        swal('','Error . Please try again', )
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });


    })

    function edit(Specialization_id, Specialization, organizations) {
        window.scrollTo(0,0);
        
        $("#Specialization_id").val(Specialization_id);
        $("#Specialization").val(Specialization);
        $("#organizations").val(organizations);
    }

    function deletespecialtis(Specialization_id, Specialization) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete \"" +Specialization+ "\" Record  !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/specialization/delete.php',
                    type: 'POST',
                    data: {
                        'Specialization_id':Specialization_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal('success', "Deleted Successfully", 'success');
                            GetSpecialization();
                        } else {
                            swal('','Error . Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(Specialization_id);
                swal('success',"Record Deleted Successfully", 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }
</script>