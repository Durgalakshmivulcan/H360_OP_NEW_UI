<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

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

    .tanNumber,
    .gstNumber {
        text-transform: uppercase;
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Test</h4>
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
            <li class="breadcrumb-item">Add & Modify Test</li>
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
                    <h4>Tests</h4>
                </div>

                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <input type="hidden" name="test_id" id="test_id" value="">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="test_name">Test Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-prescription2"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="test_name" id="test_name" value="" />
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="test_name">Normal Range <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-signpost"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="normal_range" id="normal_range" value="" />
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-currency-rupee"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="price" id="price" value="" />
                                </div>
                            </div>


                            <div class="form-group  col-lg-4 col-sm-12 ">
                                <label for="test_gst"> GST <span class="text-danger">*</span> </label>
                                <div class="input-group ">
                                    <input type="text" class="form-control" name="test_gst" id="test_gst" value="0">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="test_price">Total Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-currency-rupee"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="test_price" id="test_price" value="" readonly />
                                </div>
                            </div>

                           <div class="form-group col-lg-1 col-sm-12 mt-4">
                                <span id="orBlock" class="align-self-center mx-2 fw-bold">(OR)</span>
                           </div>



                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="test_file">Upload Excel File <span class="text-danger">*</span></label>
                                <div class="input-group">

                                    <!-- <span class="align-self-center mx-2 fw-bold">(OR)</span> -->


                                    <div class="input-group-prepend">
                                        
                                        <div class="input-group-text">
                                            <i class="bi bi-upload"></i>
                                        </div>
                                    </div>
                                    <!-- Hidden file input -->
                                    <input type="file" class="d-none" name="test_file" id="test_file" accept=".xlsx,.xls" />

                                    <!-- Upload button -->
                                    <button type="button" class="btn btn-primary w-30 me-5" id="uploadBtn">
                                     Upload Excel
                                    </button>

                                    <!-- Download Sample button -->
                                    <button type="button" class="btn btn-success w-30" id="downloadSampleBtn">
                                         Sample <i class="bi bi-download"></i>
                                    </button>
                                </div>
                                <!-- Selected file name will appear here -->
                                <small id="fileNameDisplay" class="form-text text-muted"></small>
                            </div>


                            <?php
                            $SessionUserId = $_SESSION['security_id'] ?? '';
                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if ($SessionUserId == "1" && $SessionRoleId == "1") {
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
                            }else {
                                ?>
                                <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                                <?php 
                                }
                    
                            ?>

                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'test.php') || userCan('edit', 'test.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

           
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Tests List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="showTestData">
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
        GetTest();
        //cgst();
    });

    window.excelArray = []; // global variable

    document.getElementById("test_file").addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });

            const firstSheet = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheet];

            // Convert to JSON
            window.excelArray = XLSX.utils.sheet_to_json(worksheet, { defval: "" });
            console.log("Excel Data:", window.excelArray);
        };
        reader.readAsArrayBuffer(file);
    });


    // get data Tests
    $("#price, #test_gst").on("input", function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });


    function GetTest() {
        var org_id = '<?= $SessionOrgId ?>';
        $.ajax({
            url: 'ajax/test/getTest.php',
            type: 'GET',
            success: function(data) {
                if (data) {
                    $("#showTestData").html(data);
                    document.getElementById("FormId").reset();
                    var buttons_array = [0, 1, 2, 3];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2, 3, 4];
                    }
                    // $("#tableExport1").dataTable().destroy();
                    $("#tableExport1").dataTable({
                        // destroy: true,
                        retrieve: true,
                        // paging: false,
                        dom: 'lBrftip',
                        // dom: '<"top"B>rt<"bottom"flip><"clear">',
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

        // Trigger hidden input when button clicked
    $("#uploadBtn").on("click", function() {
        $("#test_file").click();
    });

    // Show selected filename under button
    $("#test_file").on("change", function() {
        const fileName = this.files.length ? this.files[0].name : "";
        $("#fileNameDisplay").text(fileName);
    });

        $("#downloadSampleBtn").on("click", function() {
        const sampleData = [
            {"S.No":1,"Test Name *":"Test 1","Normal Range *":"10-20 units","Price *":100,"GST *":18,"Total Price *":118},
            {"S.No":2,"Test Name *":"Test 2","Normal Range *":"20-40 units","Price *":200,"GST *":18,"Total Price *":236},
            {"S.No":3,"Test Name *":"Test 3","Normal Range *":"30-60 units","Price *":300,"GST *":18,"Total Price *":354},
            {"S.No":4,"Test Name *":"Test 4","Normal Range *":"40-80 units","Price *":400,"GST *":18,"Total Price *":472},
            {"S.No":5,"Test Name *":"Test 5","Normal Range *":"50-100 units","Price *":500,"GST *":18,"Total Price *":590}
        ];

        // Convert JSON to worksheet
        const ws = XLSX.utils.json_to_sheet(sampleData);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Sample Tests");

        // Trigger download
        XLSX.writeFile(wb, "Sample_Tests.xlsx");
    });


    // insert and update data
        $("#FormId").submit(function(e){
        e.preventDefault();

        var test_id       = $("#test_id").val();
        var test_name     = $("#test_name").val().trim();
        var normal_range  = $("#normal_range").val();
        var test_price    = $("#test_price").val();
        var test_gst      = $("#test_gst").val();
        var organizations = $("#organizations").val();
        var excelData     = window.excelArray || [];

        // BULK UPLOAD
        if(excelData.length > 0 && test_id === ''){

            for (var i = 0; i < excelData.length; i++) {
                if (!excelData[i]["Test Name *"]) {
                    swal('', 'Please Select Test Name in row ' + (i + 1), 'warning');
                    return false;
                }
                if (!excelData[i]["Normal Range *"]) {
                    swal('', 'Please Enter Normal range  in row ' + (i + 1), 'warning');
                    return false;
                }
                if (!excelData[i]["Price *"]) {
                    swal('', 'Please Enter Price in row ' + (i + 1), 'warning');
                    return false;
                }
                if (!excelData[i]["GST *"]) {
                    swal('', 'Please Enter GST in row ' + (i + 1), 'warning');
                    return false;
                }
                if (!excelData[i]["Total Price"]) {
                    swal('', 'Please Enter Total Price in row ' + (i + 1), 'warning');
                    return false;
                }
                if (organizations === "") {
                    swal('', 'Please Select Organization Name!', 'warning');
                    return false;
                }
            }

            $.ajax({
                url: 'ajax/test/AddModifyTests.php',
                type: 'POST',
                data: {
                    excelData: JSON.stringify(excelData),
                    organizations: organizations
                },
                success: function(res){
                    try {
                        var data = JSON.parse(res);
                        console.log(data);

                        if(data.msg == 1) {
                            swal('', 'All Tests Added Successfully','success');
                            GetTest();
                            resetForm();
                        } else if(data.msg == 3) {
                            swal('', 'All Tests are Duplicates','warning');
                        } else if(data.msg == 4) {
                            swal('', 'Some Tests Added, Some Duplicates: ' + data.duplicates.join(', '),'info');
                            GetTest();
                            resetForm();
                        }
                    } catch(err){
                        console.log('JSON parse error', err, res);
                    }
                },
                error: function(err){
                    console.log(err);
                }
            });
            return; 
        }

        if (!test_name) {
            swal('', 'Test Name is required!', 'warning');
            return;
        }
        if (!normal_range) {
            swal('', 'Normal Range is required!', 'warning');
            return;
        }
        if (!test_price) {
            swal('', 'Test Price is required!', 'warning');
            return;
        }
        if (!test_gst) {
            swal('', 'GST is required!', 'warning');
            return;
        }
        if (!organizations) {
            swal('', 'Organization Name is required!', 'warning');
            return;
        }

        $.ajax({
            url: 'ajax/test/AddModifyTests.php',
            type: 'POST',
            data: {
                test_id: test_id,
                test_name: test_name,
                normal_range: normal_range,
                test_price: test_price,
                test_gst: test_gst,
                organizations: organizations
            },
            success: function(res){
                console.log(res);
                if(res == 1) {
                    swal('', 'Test Added Successfully','success');
                    GetTest();
                    resetForm();
                }
                else if(res == 2) {
                    swal('', 'Test Updated Successfully','success');
                    GetTest();
                    resetForm();
                }
                else if(res == 3) {
                    swal('', 'Test Name Already Exists !','warning');
                }
                else {
                    swal('', 'Error occurred, try again','error');
                }
            },
            error: function(err){
                console.log(err);
            }
        });
    });



    function resetForm() {
        $("#FormId")[0].reset();         
        $("#test_id").val('');             
        $("#test_file").val('');           
        $("#fileNameDisplay").text('');   
        $("#test_file").closest('.form-group').show(); 
        $("#orBlock").closest('.form-group').show();
        window.excelArray = []; 
    }
    function editTest(test_id, test_name, normal_range, test_price, test_gst, organizations) {
        window.scrollTo(0, 0);
        $("#test_id").val(test_id);
        $("#test_name").val(test_name);
        $("#normal_range").val(normal_range);
        $("#test_price").val(test_price);
        $("#test_gst").val(test_gst);
        $("#organizations").val(organizations);
        $("#test_file").closest('.form-group').hide(); // hide Excel in edit mode
        $("#orBlock").hide();  // works 100%

        var basePrice = 0;
        var totalPrice = parseFloat(test_price);
        var gst = parseFloat(test_gst);

        if (!isNaN(totalPrice) && !isNaN(gst)) {
            basePrice = totalPrice / (1 + gst / 100);
        }
        $("#price").val(basePrice.toFixed(2));
    }

    
    

    // delete data

    function deleteTest(test_id, test_name) {
        swal({
            title: "Are you sure?",
            text: "Do you wish to delete Test!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/test/deleteTest.php',
                    type: 'POST',
                    data: {
                        'test_id': test_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            swal('', ' Deleted Successfully', 'success');
                            GetTest();
                        } else {
                            swal('', 'Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                $('#deleteID').val(test_id);
                swal('', ' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }

    // test name validation
    $(function() {
        $("#test_name").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#test_nameId").html("");
            var regex = /^[A-Za-z0-9 ()]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_nameId").html("Only Alphabets and Numbers Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#test_name").keyup(function() {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
                $(this).val('');
            }
        });
    });
    $(function() {
        $("#test_name").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    // test price validation
    $(function() {
        $("#test_price").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#test_priceID").html("");
            var regex = /^[0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_priceID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });
    $(function() {
        $("#test_price").on("paste", function(e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10]+/g, ""); // Remove non-alphabetic characters
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    // gst validation
    $(function() {
        $("#test_gst").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#test_priceID").html("");
            var regex = /^[0-9.]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#test_priceID").html("Only Numbers Allowed.");
            }
            return isValid;
        });
    });
    setInterval(function() {
        original = document.getElementById("test_gst").value;
        if (original.length > 5) {
            lastCharRemove =
                original.slice(0, original.length - 1);
            document.getElementById('test_gst').value = lastCharRemove;
        }
    }, 100);
    setInterval(function() {
        original = document.getElementById("test_gst").value;
        formattedValue = original.replace(/(\d{2})\d*(\.\d{0,2})?/, "$1$2");
        document.getElementById('test_gst').value = formattedValue;
    }, 100);


    // function calculatetotalprice(gstval) {
    //     var price = $("#price").val();

    //     if (!price) {
    //         swal('', 'Please Enter Your Price!', 'warning');
    //         return;
    //     }

    //     price = parseFloat(price);
    //     gstval = parseFloat(gstval);

    //     if (isNaN(price) || isNaN(gstval)) {
    //         swal('', 'Invalid Price or GST!', 'warning');
    //         return;
    //     }

    //     var totalPrice = price + (price * gstval / 100);
    //     $("#test_price").val(totalPrice.toFixed(2));
    // }



    function sanitizeGST() {
        const gstInput = document.getElementById("test_gst");
        let gstValue = gstInput.value;

        // Allow only digits and limit to first 2 digits
        gstValue = gstValue.replace(/\D/g, '').slice(0, 2);
        gstInput.value = gstValue;
    }

    function calculateTotalPrice() {
        const price = parseFloat(document.getElementById("price").value);
        let gst = document.getElementById("test_gst").value;

        // Sanitize GST to ensure only first 2 digits are considered
        gst = gst.replace(/\D/g, '').slice(0, 2);

        if (!isNaN(price) && gst !== "") {
            const gstNum = parseFloat(gst);
            const total = price + (price * gstNum / 100);
            document.getElementById("test_price").value = total.toFixed(2);
        } else {
            document.getElementById("test_price").value = '';
        }
    }

    document.getElementById("price").addEventListener("input", calculateTotalPrice);
    document.getElementById("test_gst").addEventListener("input", function() {
        sanitizeGST();
        calculateTotalPrice();
    });





    // setInterval(function() {
    //     original = document.getElementById("test_gst").value;
    //     formattedValue = original.replace(/^(\d{0,2})\.?(\d{0,2}).*$/, function(match, p1, p2) {
    //         p1 = p1.length > 2 ? p1.slice(0, 2) : p1;
    //         p2 = p2.length > 2 ? p2.slice(0, 2) : p2;
    //         return p1 + (p2 === '' ? '' : '.' + p2);
    //     });
    //     if (formattedValue.includes(".")) {
    //         formattedValue += "%";
    //     }

    //     document.getElementById('test_gst').value = formattedValue;
    // }, 100);
</script>