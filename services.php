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
            <li class="breadcrumb-item">Add & Modify Services</li>
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
                    <h4>Services</h4>
                </div>
                
                <form method="POST" id="ServiceFormId" action="" >
                    <input type="hidden" name="service_id" id="service_id" value="" >
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-lg-4 col-sm-12">
                                <label >Service Name <span id="name" class="text-danger" >*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-briefcase-fill"></i>
                                    </span>
                                    <input type="text" class="form-control" name="service_name" id="service_name" value="" >
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label>Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="text" class="form-control" name="service_price_initial" id="service_price_initial" value=""
                                    oninput="calculatetotalprice()" />                                
                                </div>
                            </div>

                            <div class="form-group  col-lg-4 col-sm-12 " >
                              <label for="Percent"> GST <span class="text-danger">*</span> </label>
                                <div class="input-group ">
                                <input type="text" class="form-control" name="services_gst" id="services_gst" value="0"
                                oninput="calculatetotalprice()" />                                    
                                <span class="input-group-text" >%</span>
                                </div>                                
                            </div>
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="service_total_price">Total Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class = "bi bi-currency-rupee"></i>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" name="service_price" id="service_price" value="" readonly />
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
                        <?php if (userCan('add', 'services.php') || userCan('edit', 'services.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>

            <!-- <div class="card">
                <div class="card-header">
                    <h4>Services List</h4>
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
                        <h4>Services List</h4>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script>
    
 // sarvice name validations


    $(function () {
        $("#service_name").keypress(function (e) {
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
        $("#service_name").keyup(function () {
            var service_name1 = $(this).val();
            if (!service_name1.trim()) {
            $(this).val('');
            }
        });
    });

    $("#service_price_initial, #services_gst").on("input", function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    $(function () {
    $("#service_name").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
      });
    });

    setInterval(function() {
        original = document.getElementById("services_gst").value;
        formattedValue = original.replace(/(\d{2})\d*(\.\d{0,2})?/, "$1$2");
        document.getElementById('services_gst').value = formattedValue;
    },100);

    setInterval(function() {
        original = document.getElementById("services_gst").value;
        if (original.length > 5) {
        lastCharRemove =
            original.slice(0, original.length - 1);
        document.getElementById('services_gst').value = lastCharRemove;
        }
    }, 100);
    

    $(function () {
        $("#services_gst").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#nameId").html("");
            var regex = /^[0-9.]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#nameId").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });
    
    $(function () {
    $("#services_gst").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^0-9]/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
      });
    });

  // phone number validation

        const $input = document.querySelector("#service_price");
        const PHONENUMBER_ALLOWED_CHARS_REGEXP = /[0-9\/9/]+/;
        $input.addEventListener("keypress", e => {
        console.log(e);
        if (!PHONENUMBER_ALLOWED_CHARS_REGEXP.test(e.key)) {
        e.preventDefault();
        swal(''," Only numbers allowed.",'warning');
          }
        });

    // price validation
    
    jQuery("#service_price").keypress(function (e) {
            var length = jQuery(this).val().length;
        if (length >= 5) {
            swal("warning", "Only 5 numbers are allowed.", "warning");
            return false;
        } else if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        } else if (length == 5 && e.which == 48) {
            return false;
        }
    });
    $(function () {
    $("#service_price").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^\d\10/10]/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
      });
    });
    function calculatetotalprice() {
    var price = $("#service_price_initial").val();
    var gstval = $("#services_gst").val();

    if (!price || !gstval) {
        $("#service_price").val(''); // Clear total if any input is missing
        return;
    }

    price = parseFloat(price);
    gstval = parseFloat(gstval);

    if (isNaN(price) || isNaN(gstval)) {
        swal('', 'Invalid Price or GST!', 'warning');
        $("#service_price").val('');
        return;
    }

    var totalPrice = price + (price * gstval / 100);
    $("#service_price").val(totalPrice.toFixed(2));
}


    $("#saveData").click(function(){
                    
        var service_name=$("#service_name").val();
        if(service_name == "") {
            swal('',"Please Enter service name",'warning')
        return false; 
        }
        var service_price=$("#service_price").val();
        if(service_price == "") {
            swal('',"Please Enter Your service price",'warning')
        return false;
        }

        var services_gst=$("#services_gst").val();
        if(services_gst == "") {
            swal('',"Please Enter Your service GST",'warning')
        return false;
        }

        
    });

    $("document").ready(function() {
        Getservies();
        
    });

    var org_id = '<?=$SessionOrgId ?>';

    function Getservies() {
        $.ajax({
            url: 'ajax/service/getservies.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("ServiceFormId").reset();
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

    $("#ServiceFormId").submit(function() {
        var service_id = $("#service_id").val();
        var service_name = $("#service_name").val().trim();
        var service_price = $("#service_price").val();
        var services_gst = $("#services_gst").val();
        var organizations = $("#organizations").val();

        
        if(service_name != "" || service_price != "" || services_gst != "") {
            event.preventDefault();
            $.ajax({
                url: 'ajax/service/addservies.php',
                type: 'POST',
                data: {
                    'service_id': service_id,
                    'service_name': service_name,
                    'service_price': service_price,
                    'services_gst': services_gst,
                    'organizations': organizations
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', "Service Added Successfully",'success');
                        $("#service_id").val('');
                        Getservies();
                    } else if(data == 2) {
                        swal('', "Service Updated Successfully",'success');
                        $("#service_id").val('');
                        Getservies();
                    }
                    else if(data == 3) {
                        
                        swal('' , " Service Name Already Exists!",'warning');
                       
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

    function editservices(service_id, service_name, service_price, services_gst, organizations) {
    window.scrollTo(0, 0);

    // Calculate base price before GST
    var base_price = parseFloat(service_price) / (1 + parseFloat(services_gst) / 100);

    $("#service_id").val(service_id);
    $("#service_name").val(service_name);
    $("#service_price_initial").val(base_price.toFixed(2));  // Set base price excluding GST
    $("#services_gst").val(services_gst);
    $("#organizations").val(organizations);
    $("#service_price").val(service_price);

}


    function deleteservices(service_id, service_name) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete \"" +service_name+ "\" Record  !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/service/deleteservices.php',
                    type: 'POST',
                    data: {
                        'service_id':service_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal('',"Service Deleted Successfully", 'success');
                            Getservies();
                        } else {
                            swal('','Error . Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(service_id);
                swal(''," Service Deleted Successfully", 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }
</script>