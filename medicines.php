<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .btn-group, .btn-group-vertical {
        position: relative;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }

    .or-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #6c757d;
    font-weight: 600;
    }

    .or-divider::before,
    .or-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #ccc;
    }

    .or-divider:not(:empty)::before {
    margin-right: .75em;
    }
    .or-divider:not(:empty)::after {
    margin-left: .75em;
    }

</style>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Medicines</h4>
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
            <li class="breadcrumb-item">Add & Modify Medicines</li>
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
                    <div class="form-group col-lg-4 col-sm-12 ">
                    <h4>Medicines</h4>
                    </div>    
                </div>
                <div class="card-body">
                    <div class="form-group col-lg-4 col-sm-12">
                        <label for="test_file">Upload Excel File <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                
                                <div class="input-group-text">
                                    <i class="bi bi-upload"></i>
                                </div>
                            </div>
                            <input type="file" class="d-none" name="test_file" id="test_file" accept=".xlsx,.xls" />

                            <button type="button" class="btn btn-primary w-30 me-5" id="uploadBtn">
                               Upload Excel
                            </button>

                            <button type="button" class="btn btn-success w-30" id="downloadSampleBtn">
                                    Sample<i class="bi bi-download"></i>
                            </button>
                        </div>
                        <small id="fileNameDisplay" class="form-text text-muted"></small>
                    </div>
                </div>

                <div class="or-divider text-center my-3">
                    <span>(OR)</span>
                    </div>


                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="" >
                    <div class="card-body">
                        <input type="hidden" name="medicine_id[]" id="medicine_id" value="" >
                        <div class="row" id="remove">
                             <div class="form-group col-lg-4 col-sm-12 ">
                                    <label for="type"> Medicine Type <span class="text-danger" >*</span> </label>
                                    <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                         <i class="bi bi-capsule"></i>
                                        </div>
                                      </div>
                                        <select class="form-control form-select " name="type[]" id="type">
                                        </select>
                                    </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="medicienename"> Brand Name <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/brand.png" alt="unit Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                        <input class="form-control" name="medicienename[]" id="medicienename" >
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="scientificname"> Composition Name <span class="text-danger">*</span> </label>
                                <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                         <i class="fas fa-capsules"></i>
                                        </div>
                                      </div>
                                 <input class="form-control" name="scientificname[]" id="scientificname" >
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="dosage"> Unit <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <img src="assets/img/unit.jpeg" alt="unit Icon" width="17" height="17" classs = "fw-bold">
                                        </div>
                                    </div>
                                
                                   <input list="ice-cream-flavors" class="form-control" id="dosage" name="dosage[]">
                                </div>
                                <datalist id="ice-cream-flavors">
                    
                                </datalist>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="price">Medicine Price <span class="text-danger">*</span> </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-capsule"></i>
                                        </div>
                                    </div>
                                    <input 
                                        type="number"
                                        class="form-control" 
                                        id="medicinePrice" 
                                        name="medicinePrice[]" 
                                        placeholder="Enter price">
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
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12 w-35">
                                <label for="notes">Note</label>
                                <div class="input-group">
                                    <textarea class="form-control w-100" name="notes[]" id="notes" maxlength="100" style="height: 100px" placeholder="Enter your notes here..."></textarea>
                                </div>
                        </div>

                        <?php
                        } else{
                        ?>
                        <div class="form-group col-lg-8  col-sm-12 w-35">
                            <label for="notes">Note</label>
                                <textarea class="form-control w-100"  name="notes[]" id="notes" maxlength="100" style="height: 100px"></textarea>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="form-group col-lg-10 col-sm-12 " >

                        </div>

                        
                        <?php
                        if($SessionUserId == "1" && $SessionRoleId=="1"){
                        ?>

                        <div class="form-group col-lg-12 col-sm-12">
                            <a href="javascript:void(0)" class="adding-form1 float-end btn btn-primary"><i class=" fas fa-plus"></i></a>
                        </div>
                        <?php
                        } else{
                        ?>
                            <div class="form-group col-lg-12 col-sm-12">
                            <a href="javascript:void(0)" class="adding-form float-end btn btn-primary"><i class=" fas fa-plus"></i></a>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="paste-new-forms" id="medicdetails"></div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'medicines.php') || userCan('edit', 'medicines.php')) { /* FIX_B_1810 */ ?><button type="button" class="btn btn-primary" name="saveData" id="saveData" value="" onclick="arrayinsert()">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Medicine List</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="showPData">
                                <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                            </div>
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

var i=0;

    $("document").ready(function() {
        getmedicines();
        medicine();
        scientficmedicine();
        unit();
        gettype('');
        getorganization('');
        getUnit('');
    });

    window.excelArray = [];

    document.getElementById("test_file").addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });

            const firstSheet = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheet];
            window.excelArray = XLSX.utils.sheet_to_json(worksheet, { defval: "" });
        };
        reader.readAsArrayBuffer(file);
    });


function getUnit(id, value=null) { 
    $.ajax({
        url: 'ajax/rxgroup/getUnits.php',
        type: 'get',
        dataType: 'json',
        success: function(data) {
            var optionData = '<option value=""> Select Unit </option>';
            $.each(data, function(key, val) {
                optionData += '<option value="' + val.unit_name + '"> ' + val.unit_name + ' </option>';
            });
            $("#ice-cream-flavors" + id).html(optionData);
            if(value) {
                $("#ice-cream-flavors" + id).val(value);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}


function gettype(id, value=null) {
    $.ajax({
        url: 'ajax/medicines/gettype.php',
        type: 'get',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            var optionData = '<option value=""> Select Type </option>';
            $.each(data, function(key, val) {
                optionData += '<option value="' + val.type_id + '"> ' + val.type_name + ' </option>';
            });
            $("#type" + id).html(optionData);
            if(value) {
                $("#type" + id).val(value);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}

function getorganization(id, value=null) {
    $.ajax({
        url: 'ajax/medicines/getorganization.php',
        type: 'get',    
        dataType: 'json',
        success: function(data) {
            console.log(data);
            var optionData = '<option value=""> Select Organization </option>';
            $.each(data, function(key, val) {
                optionData += '<option value="' + val.org_id + '"> ' + val.organization_name + ' </option>';
            });
            $("#organizations" + id).html(optionData);
            if(value) {
                $("#organizations" + id).val(value);
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
}



function getmedicines() {
    var org_id = '<?=$SessionOrgId ?>';
    $.ajax({
        url: 'ajax/medicines/getmedicine.php',
        type: 'GET',
        success: function(data) {
            
            if(data) {
                $("#showPData").html(data);
                document.getElementById("FormId").reset();
                var buttons_array =  [0, 1, 2, 3, 4, 5]; 
                    if(org_id == "0"){
                                buttons_array = [0, 1, 2, 3, 4, 5, 6];
                        }
                $("#tableExportmedicine").dataTable({
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

function arrayinsert(value) {
    var medicine_id_array = [];
    var type_array = [];
    var medicinename_array = [];
    var scientific_name_array = [];
    var dosage_array = [];
    var notes_array = [];
    var medicine_price_array = [];
    var organizations = $("#organizations").val();
    var medicine_hidden_id = $("#medicine_id").val();

    var medicine_id_element = document.getElementsByName("medicine_id[]");
    var type_element = document.getElementsByName("type[]");
    var medicine_name_element = document.getElementsByName("medicienename[]");
    var scientificname_element = document.getElementsByName("scientificname[]");
    var dosage_element = document.getElementsByName("dosage[]");
    var notes_element = document.getElementsByName("notes[]");
    var medicinePriceElements = document.getElementsByName("medicinePrice[]");

    var excelData = window.excelArray || [];

    // === If Excel data exists and new entry ===
    if (excelData.length > 0 && medicine_hidden_id === '') {

        for (var i = 0; i < excelData.length; i++) {
            if (!excelData[i]["Medicine Type *"]) { swal('', 'Please Select Type Name in row ' + (i + 1), 'warning'); return false; }
            if (!excelData[i]["Brand Name *"]) { swal('', 'Please Enter Brand Name in row ' + (i + 1), 'warning'); return false; }
            if (!excelData[i]["Composition Name *"]) { swal('', 'Please Enter Composition Name in row ' + (i + 1), 'warning'); return false; }
            if (!excelData[i]["Unit *"]) { swal('', 'Please Enter Units in row ' + (i + 1), 'warning'); return false; }
            if (!excelData[i]["Price"]) { swal('', 'Please Enter Medicine Price in row ' + (i + 1), 'warning'); return false; }
        }

        if (organizations === "") { swal('', 'Please Select Organization Name!', 'warning'); return false; }

        $.ajax({
            url: 'ajax/medicines/arrayinsert.php',
            type: 'POST',
            data: { organizations: organizations, excelData: JSON.stringify(excelData) },
            success: function(data) {
                data = Number($.trim(data)); // trim and convert to number
                if (data === 1) {
                    swal({ text: 'Medicines Added Successfully', icon: 'success' })
                        .then(function() { $("#medicine_id").val(''); getmedicines(); location.reload(); });
                } else {
                    swal('', "Error or Already Exists", 'warning');
                }
            },
            error: function(err) { console.error(err); }
        });

    } else { 
        // === Normal form data ===
        for (var i = 0; i < medicine_name_element.length; i++) {
            var medicine_id = medicine_id_element[i].value;
            var type = type_element[i].value;
            var medicinename = medicine_name_element[i].value.trim();
            var scientific_name = scientificname_element[i].value.trim();
            var dosage = dosage_element[i].value;
            var notes = notes_element[i].value;
            var price = medicinePriceElements[i].value;

            if(type === "") { swal('', 'Please Select Type Name!', 'warning'); return false; }
            if(medicinename === "") { swal('', 'Please Enter Brand Name!', 'warning'); return false; }
            if(scientific_name === "") { swal('', 'Please Enter Composition Name!', 'warning'); return false; }
            if(dosage === "") { swal('', 'Please Enter Units!', 'warning'); return false; }
            if(price === "") { swal('', 'Please Enter Medicine Price!', 'warning'); return false; }
        }

        if(organizations === "") { swal('', 'Please Select Organization Name!', 'warning'); return false; }

        // Push values to arrays
        for (var i = 0; i < medicine_name_element.length; i++) {
            medicine_id_array.push(medicine_id_element[i].value);
            type_array.push(type_element[i].value);
            medicinename_array.push(medicine_name_element[i].value.trim());
            scientific_name_array.push(scientificname_element[i].value.trim());
            dosage_array.push(dosage_element[i].value);
            notes_array.push(notes_element[i].value);
            medicine_price_array.push(medicinePriceElements[i].value);
        }

        $.ajax({
            url: 'ajax/medicines/arrayinsert.php',
            type: 'POST',
            data: {
                'medicine_id': medicine_id_array,
                'type': type_array,
                'medicienename': medicinename_array,
                'scientificname': scientific_name_array,
                'dosage': dosage_array,
                'notes': notes_array,
                'medicine_price_array': medicine_price_array,
                'organizations': organizations
            },
            success: function(data) {
                data = Number($.trim(data)); // trim and convert to number

                if (data === 1) {
                    swal({ text: 'Medicine Added Successfully', icon: 'success' })
                        .then(function() { $("#medicine_id").val(''); getmedicines(); location.reload(); });

                } else if (data === 2) {
                    swal({ text: 'Medicine Updated Successfully', icon: 'success' })
                        .then(function() { $("#medicine_id").val(''); getmedicines(); });

                } else if (data === 3) {
                    swal('', "Already Exists", 'warning');
                } else {
                    swal('', "Unexpected response: " + data, 'warning');
                }
            },
            error: function(err) { console.log(err); }
        });
    }
}


    $("#uploadBtn").on("click", function() {
        $("#test_file").click();
    });

    $("#test_file").on("change", function() {
        const fileName = this.files.length ? this.files[0].name : "";
        $("#fileNameDisplay").text(fileName);
    });

        $("#downloadSampleBtn").on("click", function() {
        const sampleData = [
                {
                    "S.No": 1,
                    "Medicine Type *": "Tab",
                    "Brand Name *": "Paracetamol",
                    "Composition Name *": "Paracetamol 500mg",
                    "Unit *": "500 MG",
                    "Price": 25,
                    "Note": "Take after food"
                },
                {
                    "S.No": 2,
                    "Medicine Type *": "Cap",
                    "Brand Name *": "Amoxicillin",
                    "Composition Name *": "Amoxicillin 250mg",
                    "Unit *": "250 MG",
                    "Price": 50,
                    "Note": "Twice a day"
                },
                {
                    "S.No": 3,
                    "Medicine Type *": "Syp",
                    "Brand Name *": "Cough Syrup",
                    "Composition Name *": "Dextromethorphan",
                    "Unit *": "100 ML",
                    "Price": 80,
                    "Note": "Shake well before use"
                },
                {
                    "S.No": 4,
                    "Medicine Type *": "Inj",
                    "Brand Name *": "Insulin",
                    "Composition Name *": "Insulin Regular",
                    "Unit *": "10 ML",
                    "Price": 120,
                    "Note": "Store in refrigerator"
                },
                {
                    "S.No": 5,
                    "Medicine Type *": "Drug",
                    "Brand Name *": "Ibuprofen",
                    "Composition Name *": "Ibuprofen 400mg",
                    "Unit *": "400 MG",
                    "Price": 30,
                    "Note": "Do not take on empty stomach"
                }
                ];

        const ws = XLSX.utils.json_to_sheet(sampleData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Sample Tests");
        XLSX.writeFile(wb, "Sample_Medicines.xlsx");
    });


function editmedicine(medicine_id, type, medicienename, scientificname, dosage, notes, organizations, price) {
    window.scrollTo(0,0);
    
    $("#medicine_id").val(medicine_id);
    $("#type").val(type);
    $("#medicienename").val(medicienename);
    $("#scientificname").val(scientificname);
    $("#dosage").val(dosage);
    $("#notes").val(notes);
    $("#organizations").val(organizations);
    $("#medicinePrice").val(price);

    // Trigger the click event of .remove-btn
    $(".remove-btn").trigger('click');
}

// Remove button click event handler
$(document).on('click', '.remove-btn', function () {
    // alert(1);
    $(this).closest('.main-form').remove();
});






// delete function

function deletemedicine(medicine_id, scientificname) {
    swal({  title:"Medition", 
        text: "Do you wish to delete Medicine",
            buttons: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'ajax/medicines/deletemedicine.php',
                type: 'POST',
                data: {
                    'medicine_id': medicine_id      
                },
                success: function(data) {
                    if(data == 1) {
                    swal('', "Medicine Deleted Successfully",'success');
                        getmedicines();
                        // Getdepart();
                    } else {
                    swal("Error", "Error occured. Please try again" );
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });
            
            $('#deleteID').val(medicine_id);
            swal('',  'Record Deleted Successfully', 'success').then((result) => {
                $('#deleteFormId').submit();
            });
        }
    });
}


function medicine(i) {
    console.log( "medic",$("#medicdetails").children().length);
    console.log("index" + i)

    $("#medicienename").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        $("#lblError").html("");
        var regex = /^[A-Za-z0-9 ]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
            $("#lblError").html("Only Alphabets&Number allowed.");
        }
        return isValid;
    });

    if (typeof i == 'number') {
        $("#medicienename" + i).keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError" + i).html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError" + i).html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });
    }


    $(function () {

        $("#medicienename").keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
             }
        });

        $("#medicienename"+ i).keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
            $(this).val('');
               }
        });
    });
}

function scientficmedicine(i) {
    console.log( "medic",$("#medicdetails").children().length);
    console.log("index" + i)
        $("#scientificname" ).keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError1").html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError1").html("Only Alphabets&Number allowed.");
            }
            return isValid;
       });
       if (typeof i == 'number') {
            $("#scientificname"+ i).keypress(function (e) {
                var keyCode = e.keyCode || e.which;
                $("#lblError1"+ i).html("");
                var regex = /^[A-Za-z0-9 ]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#lblError1"+ i).html("Only Alphabets&Number allowed.");
                }
                return isValid;
            });
        }

    $(function () {
        $("#scientificname").keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
            $(this).val('');
           }
        });

        $("#scientificname"+ i).keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
            $(this).val('');
           }
        });
    });
}

function unit(i) {
    $("#dosage" ).keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        $("#lblError2").html("");
        var regex = /^[A-Za-z0-9 ]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
            $("#lblError2").html("Only Alphabets&Number allowed.");
        }
        return isValid;
    });
    if (typeof i == 'number') {
        $("#dosage"+ i).keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError2"+ i).html("");
            var regex = /^[A-Za-z0-9 ]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError2"+ i).html("Only Alphabets&Number allowed.");
            }
            return isValid;
        });
    }

    $(function () {
        $("#dosage").keyup(function () {
            var unit = $(this).val();
            if (!unit.trim()) {
                $(this).val('');
            }
        });

        $("#dosage"+ i).keyup(function () {
            var unit = $(this).val();
            if (!unit.trim()) {
            $(this).val('');
           }
        });
    });
}


$(document).on('click', '.remove-btn', function () {
    $(this).closest('.main-form').remove();
});

// Prevent mouse scroll wheel from changing number input values
$(document).on('wheel', 'input[type=number]', function () {
    $(this).blur();
});

$(document).on('click', '.adding-form1', function () {
    $('.paste-new-forms').append(' <input type="hidden" name="medicine_id[]" id="medicine_id'+i+'" value="" >\
        <div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-sm-1  col-sm-12">\
            <span ><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1 " ><i class=" fas fa-minus"></i></a></span>\
            </div>\
            <div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-lg-4 col-sm-12 ">\
                                    <label for="type"> Medicine Type <span class="text-danger" >*</span> </label>\
                                    <select class="form-control form-select " name="type[]" id="type'+i+'">\
                                    </select>\
                            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="medicienename'+i+'"> Brand Name <span class="text-danger">*</span> </label>\
                <input class="form-control medicienename " name="medicienename[]" id="medicienename'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="scientificname"> Composition Name <span class="text-danger">*</span> </label>\
                <input class="form-control" name="scientificname[]" id="scientificname'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                                <label for="dosage"> Unit <span class="text-danger">*</span> </label>\
                                <input list="ice-cream-flavors" class="form-control" id="dosage'+i+'" name="dosage[]">\
                                            <datalist id="ice-cream-flavors">\
                                                    <option value=""> Select Unit </option>\
                                                    <option value="50 MG"> 50 MG </option>\
                                                    <option value="100 MG"> 100 MG </option>\
                                                    <option value="150 MG"> 150 MG  </option>\
                                                    <option value="100 ML"> 100 ML </option>\
                                            </datalist>\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                    <label>Medicine Price <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="bi bi-capsule"></i>\
                            </div>\
                        </div>\
                        <input type="number" id="medicinePrice" class="form-control" name="medicinePrice[]" placeholder="Enter price">\
                    </div>\
                </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                                <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>\
                                <select class="form-control form-select" name="organizations" id="organizations'+i+'">\
                                    <option value="">Select Organization</option>\
                                </select>\
                            </div>\
                            <div class="form-group col-lg-4  col-sm-12 w-35">\
                                <label for="notes">Note</label>\
                                 <textarea class="form-control w-100"  name="notes[]" id="notes'+i+'" maxlength="100" style="height: 100px"></textarea>\
                            </div>\
        </div>\
        </div>'
        );
        // getmedicines();
        medicine(i);
        scientficmedicine(i);
        unit(i);
        gettype(i);
        getorganization(i);
        i++;
});

$(document).on('click', '.adding-form', function () {
    $('.paste-new-forms').append(' <input type="hidden" name="medicine_id[]" id="medicine_id'+i+'" value="" >\
        <div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-sm-1  col-sm-12">\
            <span ><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1 " ><i class=" fas fa-minus"></i></a></span>\
            </div>\
            <div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-lg-4 col-sm-12 ">\
                                    <label for="type"> Medicine Type <span class="text-danger" >*</span> </label>\
                                    <select class="form-control form-select " name="type[]" id="type'+i+'">\
                                    </select>\
                            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="medicienename'+i+'"> Brand Name <span class="text-danger">*</span> </label>\
                <input class="form-control medicienename " name="medicienename[]" id="medicienename'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="scientificname"> Composition Name <span class="text-danger">*</span> </label>\
                <input class="form-control" name="scientificname[]" id="scientificname'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                                <label for="dosage"> Unit <span class="text-danger">*</span> </label>\
                                <input list="ice-cream-flavors" class="form-control" id="dosage'+i+'" name="dosage[]">\
                                            <datalist id="ice-cream-flavors">\
                                                    <option value=""> Select Unit </option>\
                                                    <option value="50 MG"> 50 MG </option>\
                                                    <option value="100 MG"> 100 MG </option>\
                                                    <option value="150 MG"> 150 MG  </option>\
                                                    <option value="100 ML"> 100 ML </option>\
                                            </datalist>\
            </div>\
             <div class="form-group col-lg-4 col-sm-12">\
                    <label>Medicine Price <span class="text-danger">*</span></label>\
                    <div class="input-group">\
                        <div class="input-group-prepend">\
                            <div class="input-group-text">\
                                <i class="bi bi-capsule"></i>\
                            </div>\
                        </div>\
                        <input type="number" id="medicinePrice" class="form-control" name="medicinePrice[]" placeholder="Enter price">\
                    </div>\
                </div>\
            <div class="form-group col-lg-8  col-sm-12 w-35">\
            <label for="notes">Notes</label>\
                <textarea class="form-control w-100"  name="notes[]" id="notes'+i+'" style="height: 100px"></textarea>\
            </div>\
        </div>\
        </div>'
        );
        medicine(i);
        scientficmedicine(i);
        unit(i);

        gettype(i);
        getorganization(i);
        i++;
});

// clear data functionality
function clearData() {
    $('.paste-new-forms').html('');
    NewInputsCount = 1;
    $('.paste-new-forms').append('<div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-sm-1  col-sm-12">\
            <span ><a href="javascript:void(0)" class="remove-btn float-end btn btn-danger multi_add1 " ><i class=" fas fa-minus"></i></a></span>\
            </div>\
            <div class="row main-form" style="margin-top:-12px;">\
            <div class="form-group col-lg-4 col-sm-12 ">\
                                    <label for="type"> Medicine Type <span class="text-danger" >*</span> </label>\
                                    <select class="form-control form-select " name="type[]" id="type'+i+'">\
                                    </select>\
                            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="medicienename'+i+'"> Brand Name <span class="text-danger">*</span> </label>\
                <input class="form-control medicienename " name="medicienename[]" id="medicienename'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                <label for="scientificname"> Composition Name <span class="text-danger">*</span> </label>\
                <input class="form-control" name="scientificname[]" id="scientificname'+i+'" >\
            </div>\
            <div class="form-group col-lg-4 col-sm-12">\
                                <label for="dosage"> Unit <span class="text-danger">*</span> </label>\
                                <input list="ice-cream-flavors" class="form-control" id="dosage'+i+'" name="dosage[]">\
                                            <datalist id="ice-cream-flavors">\
                                                    <option value=""> Select Unit </option>\
                                                    <option value="50 MG"> 50 MG </option>\
                                                    <option value="100 MG"> 100 MG </option>\
                                                    <option value="150 MG"> 150 MG  </option>\
                                                    <option value="100 ML"> 100 ML </option>\
                                            </datalist>\
            </div>\
            <div class="form-group col-lg-8  col-sm-12 w-35">\
            <label for="notes">Notes</label>\
                <textarea class="form-control w-100"  name="notes[]" id="notes'+i+'" style="height: 100px"></textarea>\
            </div>\
        </div>\
        </div>'
        );
        medicine(i);
        scientficmedicine(i);
        unit(i);
        gettype(i);
        getorganization(i);
        i++;
};

function btn2() {
    // multi_add1.style.display = "none";
    // multi_add.style.display = "block";
};

function btn1() {
    // multi_add1.style.display = "none";
    // multi_add.style.display = "block";
};


</script>