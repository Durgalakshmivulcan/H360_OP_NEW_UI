<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">

<style>
.btn-group, .btn-group-vertical {
    position: relative;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    vertical-align: middle;
    margin-top: 20px;
}
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Test Group</h4>
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
            <li class="breadcrumb-item">Add & Modify Test Group</li>
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
                    <h4>Create Test Group</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <input type="hidden" name="test_group_id" id="test_group_id" value="" >
                    <div class="card-body">
                        <div class="row">
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
                                    <select class="form-control form-select" name="organizations" id="organizations" onchange="GetOrgByTest()">
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

                            <div class="form-group col-lg-5 col-sm-12">
                                <label for="test_group_name"> Package Name <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                     <i class="bi bi-box-fill"></i>
                                    </span>
                                    <input class="form-control" name="test_group_name" id="test_group_name" value=""/>
                                </div>
                                <!-- <span id="test_group_nameID"></span> -->
                            </div>

                            <div class="form-group col-lg-5 col-sm-12" >
                                <label for="test_name"><i class="bi bi-prescription2"></i> Test Name <span class="text-danger">*</span> </label>
                                <select class="form-control form-select tests" name="test_name" id="test_name" multiple>
                                    <?php
                                    if($SessionUserId == "1" && $SessionRoleId=="1"){
                                        $getTestGroup = mysqli_query($conn, "SELECT test_id, test_name FROM tests WHERE status='1' ORDER BY test_id DESC") or die(mysqli_error($conn));
                                    } else{
                                        $getTestGroup = mysqli_query($conn, "SELECT test_id, test_name FROM tests WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_id DESC") or die(mysqli_error($conn));
                                    }
                                        while($row=mysqli_fetch_object($getTestGroup)) {
                                    ?>
                                    <option value="<?= $row->test_name ?>" > <?= $row->test_name ?> </option>
                                    <?php 
                                    } 
                                    ?>
                                </select> 
                            </div>
                            <div class="form-group col-lg-6 col-sm-12">
                                <label for="test_group_price">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="text" class="form-control" name="test_group_price" id="test_group_price" value=""/>
                                </div>
                            </div>

 
                        </div>

                    </div>

                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'testGroup.php') || userCan('edit', 'testGroup.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

            
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Test Group List</h4>
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

<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<script>
    $("document").ready(function() {
        GetTestGroup();
        GetAutoTest();
    });


    var Tests = new Choices('#test_name', {
       removeItemButton: true,
     }); 

     var org_id = '<?=$SessionOrgId ?>';

    function GetTestGroup() {
        $.ajax({
            url: 'ajax/testGroup/GetTestGroup.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("FormId").reset();
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


    // insert and update

    $("#FormId").submit(function(event) {
    event.preventDefault();

    var test_group_id    = $("#test_group_id").val();
var test_group_name  = $("#test_group_name").val().trim();
var test_name        = $("#test_name").val(); // Can be single or multiple (array)
var test_price       = $("#test_group_price").val();
var organizations    = $("#organizations").val();
const investigationItems = [];

// Check if multiple test_name values
if (Array.isArray(test_name)) {
    // If multiple
    test_name.forEach(function (singleTestName) {
        investigationItems.push({
            // test_id: test_group_id,
            investigation: singleTestName,  // Single test name in each object
            instruction: '',
           price: test_price || '',
            // doctor_price: '',
            test_group_id: '',
            test_group_name: test_group_name || '', 

            // standard_price: '',
            // test_status: '1',
            // test_group_id: '', 
            test_group_price: ''
            
        });
    });
} else {
    // If only one selected
    investigationItems.push({
            // test_id: test_group_id,
            investigation: singleTestName,  // Single test name in each object
            instruction: '',
           price: test_price || '',
            // doctor_price: '',
            test_group_id: '',
            test_group_name: test_group_name || '', 

            // standard_price: '',
            // test_status: '1',
            // test_group_id: '', 
            test_group_price: ''
    });
}


    var test_group_price = $("#test_group_price").val().trim();

    if('<?= $SessionUserId?>' == '1') {
        if(!organizations) {
            swal('', 'Please Select Organization Name!', 'warning');
            return;
        }
    }

    if(!test_group_name) {
        swal('', 'Please Enter Your Test Package Name!', 'warning');
        return;
    }

    if(test_name == "") {
        swal('', 'Please Select Test Name!', 'warning');
        return;
    }

    if(!test_group_price) {
        swal('', 'Please Enter Your Test Package Price!', 'warning');
        return;
    }

    $.ajax({
        url: 'ajax/testGroup/AddModifyTestGroup.php',
        type: 'POST',
        data: {
            'test_group_id'    : test_group_id,
            'test_group_name'  : test_group_name,
            'test_name'        : JSON.stringify(investigationItems), // 🛠️ Corrected here
            'test_group_price' : test_group_price,
            'organizations'    : organizations
        },
        success: function(data) {
            if(data == 1) {
                swal('', "\"" + test_group_name + "\" Test Added Successfully", 'success');
                GetTestGroup();
                $("#test_group_id").val('');
            } else if(data == 2) {
                swal('', "\"" + test_group_name + "\" Updated Successfully", 'success');
                GetTestGroup();
                $("#test_group_id").val('');
            } else if(data == 0) {
                swal('', 'All fields Required !', 'warning');
            } else if(data == 3) {
                swal('', "Test Group Name Already Exists!", 'warning');
            } else {
                swal('', 'Error occurred. Please try again', 'error');
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
});



    // onclick edit functionality
    function editTestGroup(test_group_id, test_group_name, test_name, test_group_price, organizations) {
    // alert("Test Name: " + test_name + "\nTest Group Name: " + test_group_name);
    window.scrollTo(0,0);
    $("#test_group_id").val(test_group_id);
    $("#test_group_name").val(test_group_name);
    $("#test_name").val(test_name);  // no need to .toString()
    
    Tests.setChoiceByValue(test_name.split(','));  // Assuming 'Tests' is a Choices.js object
    $("#test_group_price").val(test_group_price);
    $("#organizations").val(organizations);
}


    function GetAutoTest() {
        var test_name = $('#test_name').val();
        Tests.removeActiveItems();
        if(test_name != "") {
            $.ajax({
                url: 'ajax/testGroup/getTests.php',
                type: 'POST',
                data: { test_name: test_name },
                dataType: 'json',
                success: function(data) {
                    Tests.setChoiceByValue(data);
                },
                error: function(err) {
                console.log(err);
                }
            });
        }
    }

    // delete functionality

    function deleteTestGroup(test_group_id, test_group_name) {
        var str = "Do you wish to delete <b>" + test_group_name + "</b> Record!";
        swal({
            title: "Are you sure?",
            text: str,
            type: 'input',
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/testGroup/DeleteTestGroup.php',
                    type: 'POST',
                    data: {
                        'test_group_id': test_group_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal('','Deleted Successfully', 'success');
                            GetTestGroup();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(test_id);
                swal('',' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

// // Test Group Name
    $(function () {
        $("#test_group_name").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#test_group_nameID").html("");
            var regex = /^[a-zA-Z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_group_nameID").html("Only Alphabets and Number!");
            }
            return isValid;
        });
    });

$(function () {
    $("#test_group_name").keyup(function () {
        var organizationName = $(this).val();
        if (!organizationName.trim()) {
        $(this).val('');
        }
    });
});

// // Test Name
    $(function () {
        $("#test_name").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#test_nameID").html("");
            var regex = /^[a-zA-Z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_nameID").html("Only Alphabets !");
            }
            return isValid;
        });
    });

$(function () {
    $("#test_name").keyup(function () {
        var test_name = $(this).val();
        if (!test_name.trim()) {
        $(this).val('');
        }
    });
});



// // Test Group Price
$(function () {
        $("#test_group_price").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#test_group_priceID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_group_priceID").html("Only Alphabets and Number!");
            }
            return isValid;
        });
    });

$(function () {
    $("#test_group_price").keyup(function () {
        var organizationName = $(this).val();
        if (!organizationName.trim()) {
        $(this).val('');
        }
    });
});

// Get Org By Tests in TestGroup
function GetOrgByTest() {
    var org_id = $("#organizations").val();
    $.ajax({
        url: 'ajax/testGroup/GetOrgByTests.php',
        type: 'POST',
        data: { org_id: org_id },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            var optionDataTest = '';
            $.each(data, function(key, val) {
                $.each(val.tests, function(key1, val1){
                    optionDataTest +='<option value="'+ val1.test_id +'">'+ val1.test_name +'</option>';  
                })
            });
            
            Tests.destroy();
            $("#test_name").html(optionDataTest);
            Tests = new Choices('#test_name', {
                removeItemButton: true,
            }); 

        },
        error: function(err) {
        console.log(err);
        }
    });

}
</script>