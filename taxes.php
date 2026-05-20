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

    /* .PAN
    {
        text-transform: uppercase;
    }
    .error
    {00000000
        color: Red;
        visibility: hidden;
    } */
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Taxes</h4>
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
            <li class="breadcrumb-item">Add & Modify Taxes</li>
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
                    <h4>Taxes</h4>
                </div>

                <form method="POST" id="taxformid" action="" enctype="multipart/form-data" class="">
                    <input type="hidden" name="taxes_hid_id" id="taxes_hid_id" value="">
                    <div class="card-body">
                        <div class="row">

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="cgstNumber">CGST <span class="text-danger"></span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="cgstNumber" id="cgstNumber" value="" maxlength="5" title="Please Enter Valid CGST Number" onkeyup="sum()" />
                                    <div class="input-group-append">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="sgstNumber">SGST <span class="text-danger"></span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="sgstNumber" id="sgstNumber" value="" maxlength="5" title="Please Enter Valid SGST Number" onkeyup="sum()" />
                                    <div class="input-group-append">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="percentage">Percentage <span class="text-danger"></span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="percentage" id="percentage" value="" step=".01" title="Please Enter Valid Percentage" onkeyup="sum()" disabled />
                                    <div class="input-group-append">
                                        <div class="input-group-text">%</div>
                                    </div>
                                </div>
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
                            }
                            ?>


                            <div class="card-footer text-center">
                                <?php if (userCan('add', 'taxes.php') || userCan('edit', 'taxes.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="taxesdata" id="taxesdata" value="" onclick="validateGST()">Submit</button><?php } ?>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Taxes List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="taxes">
                            <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <form action="" method="POST" id="deletetaxFormid">
            <input type="hidden" name="deletetaxid" id="deletetaxid" value="" />
        </form>

</div>
</div>

</section>

</div>

<?php require_once("ajax/footer.php") ?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    // get ajax start

    $("document").ready(function() {
        GetTaxes();
        if (typeof sgst === 'function') { sgst(); }
        if (typeof cgst === 'function') { cgst(); }
    });



    setInterval(function() {
        let original = document.getElementById("cgstNumber").value;
        let formattedValue = original.replace(/[^0-9.]/g, '')
            .replace(/(\d{2})\d*(\.\d{0,2})?/, "$1$2");
        document.getElementById('cgstNumber').value = formattedValue;
    }, 100);



    setInterval(function() {
        original = document.getElementById("sgstNumber").value;
        let formattedValue = original.replace(/[^0-9.]/g, '')
            .replace(/(\d{2})\d*(\.\d{0,2})?/, "$1$2");
        document.getElementById('sgstNumber').value = formattedValue;
    }, 100);


    function GetTaxes() {
        var org_id = '<?= $SessionOrgId ?>';

        $.ajax({
            url: 'ajax/taxes/gettaxes.php',
            type: 'GET',
            success: function(data) {

                if (data) {
                    $("#taxes").html(data);
                    document.getElementById("taxformid").reset();
                    var buttons_array = [0, 1, 2, 3];
                    if (org_id == "0") {
                        buttons_array = [0, 1, 2, 3, 4];
                    }
                    $("#tabletax1").dataTable({
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
    // get ajax end


    // insert edit start 

    $("#taxformid").submit(function() {
        var tax_id = $("#taxes_hid_id").val();
        var cgstNumber = $("#cgstNumber").val();
        var sgstNumber = $("#sgstNumber").val();
        var organizations = $("#organizations").val();

        var percentage = $("#percentage").val();

        if (cgstNumber != "" || sgstNumber != "" || percentage != "") {
            event.preventDefault();
            $.ajax({
                url: 'ajax/taxes/inserttaxes.php',
                type: 'POST',
                data: {
                    'taxes_hid_id': tax_id,
                    'cgstNumber': cgstNumber,
                    'sgstNumber': sgstNumber,
                    'percentage': percentage,
                    'organizations': organizations
                    // 'b_pressure': blood_pressure
                },
                success: function(data) {
                    console.log(data);
                    $("#taxes").load('ajax/taxes/gettaxes.php');
                    if (data == 1) {
                        swal('', ' Tax Record Added Successfully', 'success');
                        $("#taxes_hid_id").val('');
                        GetTaxes();
                        $('#formid').load('taxes.php');
                    } else if (data == 2) {
                        swal('', ' Tax Record Updated Successfully ', "success");
                        $("#taxes_hid_id").val('');
                        GetTaxes();
                    } else if (data == 3) {
                        swal('', ' Tax Already Exits ', "warning");
                    } else if (data == 4) {
                        swal('', 'plese select organization ', "warning");
                        $("#taxes_hid_id").val('');
                        GetTaxes();
                    } else if (data == 5) {
                        window.open('index.php', '_self');
                    } else {
                        swal('', 'Error occured. Please try again', 'error');
                    }
                },
                error: function(err) {
                    // alert(4);
                    console.log(err);
                }
            });
        }
    })

    // insert ajax end

    // edit ajax start

    function edittaxes(tax_id, cgstNumber, sgstNumber, percentage, organizations) {
        window.scrollTo(0, 0);

        $("#taxes_hid_id").val(tax_id);
        $("#cgstNumber").val(cgstNumber);
        $("#sgstNumber").val(sgstNumber);
        $("#percentage").val(percentage);
        $("#organizations").val(organizations);
    }

    // edit ajax end

    // delete ajax start

    function deletetaxes(tax_id, cgstNumber) {
        swal({
            title: "Are you sure?",
            text: "Do you wish to delete Tax Record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/taxes/taxesdelete.php',
                    type: 'POST',
                    data: {
                        'tax_id': tax_id
                    },
                    success: function(data) {
                        if (data == 1) {
                            swal('', "Tax Record", "Deleted Successfully", 'success');
                            GetTaxes();
                        } else {
                            swal("error", "Error occured. please try again");
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });

                $('#deletetaxid').val(tax_id);
                swal('', ' Deleted Successfully ', 'success').then((result) => {
                    $('#deletetaxFormid').submit();
                });
            }
        });
    }
</script>

<!-- delete ajax end -->

<!-- cgst sgst validation start -->
<script type="text/javascript">
    $("#taxesdata").click(function() {

        var cgstNumber = $("#cgstNumber").val();
        var sgstNumber = $("#sgstNumber").val();
        if (cgstNumber == "" || sgstNumber == "") {
            swal('', 'All fields Required', 'warning')
            return false;
        }
    });

    // cgst field required end


    // percentage field validation start

    $(function() {
        $("#percentage").keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            $("#percen").html("");
            var regex = /^[0-9.]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#percen").html("Only Number allowed.");
            }

            return isValid;
        });
    });


    function sum() {
        var cgstNumber = document.getElementById('cgstNumber').value;
        var sgstNumber = document.getElementById('sgstNumber').value;
        var result = parseFloat(cgstNumber) + parseFloat(sgstNumber);
        if (!isNaN(result)) {
            if (cgstNumber === '' && sgstNumber === '') {
                document.getElementById('percentage').value = '';
            } else {
                document.getElementById('percentage').value = result.toFixed(2) + '%';
            }
        } else {
            document.getElementById('percentage').value = '';
        }
    }

    //  GST Addition javascript end 
</script>