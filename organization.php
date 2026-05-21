<?php
require_once("ajax/header.php");
  $row = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT MAX(org_id) AS lastid FROM organization")
  );
  $lastid = (isset($row['lastid']) ? (int)$row['lastid'] : 0) + 1;
  
  
  ?>


<style>
    .btn-group, .btn-group-vertical {
    position: relative;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    vertical-align: middle;
    margin-top: 20px;
}
.tanNumber, .gstNumber{
    text-transform: uppercase;
}
/* table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting {
    padding-right: 18px;
} */
 .form-check .form-check-label span {
    display: block;
    position: absolute;
    left: 55px;
    top: -1px;
    transition-duration: .2s;
    padding-left: 0;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Organization</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Administrator</li>
            <li class="breadcrumb-item">Add & Modify Organization</li>
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
                    <h4>Organization</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="">
                    <div class="card-body">
                        <div class="row">
                        <div class="form-group col-lg-4 col-sm-12">
                            <input type="hidden" name="org_id" id="org_id" value="">
                            <input type="hidden" name="lastorg_id" id="lastorg_id" value="<?php echo $lastid; ?>">
                            <label for="organization">Organization Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-buildings-fill"></i></span>
                                <input type="text" class="form-control" name="organization_name" id="organization_name" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="contact">Contact <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" class="form-control" name="contact" id="contact" value="">
                            </div>
                            <p id="validationMessage"></p>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                <input type="text" class="form-control" name="email" id="email" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                <input type="text" class="form-control" name="description" id="description" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="gstNumber">GST Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                <input type="text" class="form-control gstNumber" name="gstNumber" id="gstNumber" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="tanNumber">TAN Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-file-earmark-check"></i></span>
                                <input type="text" class="form-control tanNumber" name="tanNumber" id="tanNumber" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="longitude">Longitude <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" class="form-control" name="longitude" id="longitude" value="">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="latitude">Latitude <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control" name="latitude" id="latitude" value="">
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                                <label for="logo_without_text"> Logo And Caption </label><span class="text-danger">*</span>
                                <input type="file" class="form-control" name="logo_without_text" id="logo_without_text" accept=".jpg, .jpeg, .png" value="">
                            </div>
                        <div class="form-group col-lg-4 col-sm-12">
                                <label for="logo_with_text"> Only Logo </label><span class="text-danger">*</span>
                                <input type="file" class="form-control" name="logo_with_text" id="logo_with_text" accept=".jpg, .jpeg, .png" value="">
                            </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="stamp_file">Authorised Signatory Stamp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-patch-check-fill"></i></span>
                                <input type="file" class="form-control" id="stamp_file" accept=".jpg,.jpeg,.png">
                            </div>
                            <small class="text-muted">JPG / PNG, max 1.5 MB. Upload only when editing an existing org.</small>
                            <div id="stampPreview" style="margin-top:6px;display:none;">
                                <img id="stampPreviewImg" src="" alt="Stamp preview" style="max-height:80px;border:1px solid #dee2e6;border-radius:4px;padding:4px;">
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="userLimit">User Limit <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-123"></i>
                                </span>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    name="userLimit" 
                                    id="userLimit"
                                    placeholder="Enter max no. of organizations">
                            </div>
                        </div>


                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                                <textarea class="form-control" name="address" id="address"></textarea>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-sm-12">
                            <label for="ipaccess">IP Access <span class="text-danger">*</span></label>
                            <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="ipOpToggle" 
                                    style="width:50px;height:30px; cursor:pointer;pointer-events:visible">
                            <label class="form-check-label" for="ipOpToggle" style="pointer-events:none">
                                <span id="toggleLabel" style="white-space:nowrap">OP</span>
                            </label>
                            </div>
                       </div>
                    </div>
                    </div>
                    
                    <div class="card-footer text-center">
                        <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                    </div>
                </form>
            </div>

            

        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Organizations List</h4>
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


<!-- <div class="col-12 col-sm-6 col-lg-12">
    <div class="card">
        <div class="card-body text-center">
        <div class="mb-2">Info Message</div>
        <button class="btn btn-primary" id="toastr-1">Launch</button>
        </div>
    </div>
    </div>

    <div class="iziToast-body" style="padding-left: 33px;">
    <i class="iziToast-icon ico-info revealIn"></i>
    <div class="iziToast-texts">
        <strong class="iziToast-title slideIn" style="margin-right: 10px;">Hello, Mohan!</strong>
        <p class="iziToast-message slideIn">This awesome plugin is made iziToast toastr</p>
    </div>
    <div>

    </div>
</div> -->
<?php require_once("ajax/footer.php") ?>



<script>
const toggle = document.getElementById("ipOpToggle");
const label  = document.getElementById("toggleLabel");

toggle.addEventListener("change", function () {
  if (this.checked) {
    label.textContent = "OP & IP"; // When switch is ON
  } else {
    label.textContent = "OP"; // When switch is OFF
  }
});

// number key press contact validate
const $input = document.querySelector("#contact");
const PHONENUMBER_ALLOWED_CHARS_REGEXP = /[0-9\10]+/;
$input.addEventListener("keypress", e => {
  console.log(e);
  if (!PHONENUMBER_ALLOWED_CHARS_REGEXP.test(e.key)) {
    e.preventDefault();
  }
});

// mobile number should continent max length 10

setInterval(function() {
original = document.getElementById("contact").value;
if (original.length > 10) {
  lastCharRemove =
    original.slice(0, original.length - 1);
  document.getElementById('contact').value = lastCharRemove;
}
}, 100);
$(function () {
    $("#contact").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^\d\10]+/g, ""); // Remove non-alphabetic characters
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
    });
});

// number key press end




$("document").ready(function() {
    GetOrganization();
});

// Get Organization Data


function GetOrganization() {
    var org_id = '<?=$SessionOrgId ?>';
    $.ajax({
        url: 'ajax/organization/GetOrganization.php',
        type: 'GET',
        success: function(data) {
            if(data) {
                $("#showMenusData").html(data);
                document.getElementById("FormId").reset();

                var buttons_array =  [0, 1, 2, 3, 4, 5, 6]; 
                    if(org_id == "0"){
                        buttons_array = [0, 1, 2, 3, 4, 5, 6, 7];
                        }
                // $("#tableExport1").dataTable().destroy();
                $("#tableExport1").dataTable({
                    // destroy: true,
                    retrieve: true,
                    // paging: false,
                    dom: 'lBrftip',
                    // dom: '<"top"B>rt<"bottom"flip><"clear">',
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

$("#FormId").submit(function(event) {
    event.preventDefault();

    var org_id = $("#org_id").val();
    var lastorgid = $("#lastorg_id").val();
    var organization_name = $("#organization_name").val().trim();
    var contact = $("#contact").val();
    var email = $("#email").val();
    var description = $("#description").val();
    var gstNumber = $("#gstNumber").val();
    var tanNumber = $("#tanNumber").val();
    var longitude = $("#longitude").val();
    var latitude = $("#latitude").val();
    var logoFile1 = $("#logo_without_text")[0].files[0];
    var logoFile2 = $("#logo_with_text")[0].files[0];
    // var logoFile = $("#logo")[0].files[0];
    var address = $("#address").val();
    var userLimit =$("#userLimit").val();

    // === VALIDATION ===
    if (!organization_name) {
        swal('', 'Please Enter Your Organization Name!', 'warning');
        return;
    }
    if (!contact || !contact.match('[0-9]{10}')) {
        swal('', 'Please Enter A Valid Contact Number!', 'warning');
        return;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        swal('', 'Please Enter A Valid Email!', 'warning');
        return;
    }
    if (!description) {
        swal('', 'Please Enter Your Description!', 'warning');
        return;
    }
    if (!gstNumber || gstNumber.length < 15 || gstNumber.length > 17 || !/^[a-zA-Z0-9]+$/.test(gstNumber)) {
        swal('', 'Please Enter A Valid GST number!', 'warning');
        return;
    }
    if (!tanNumber || tanNumber.length !== 10 || !/^[a-zA-Z0-9]+$/.test(tanNumber)) {
        swal('', 'Please Enter A Valid TAN Number!', 'warning');
        return;
    }
    if (!longitude) {
        swal('', 'Please Enter Your Longitude!', 'warning');
        return;
    }
    if (!latitude) {
        swal('', 'Please Enter Your Latitude!', 'warning');
        return;
    }
    if (!org_id && !logoFile1 && !logoFile2) {
        swal('', 'Please Upload logo!', 'warning');
        return;
    }
    if (!address) {
        swal('', 'Please Enter Your Address!', 'warning');
        return;
    }
    if (!userLimit) {
        swal('', 'Please Enter Your User Limit!', 'warning');
        return;
    }

    //IF LOGO IS SELECTED, UPLOAD FIRST
    if ( logoFile1 || logoFile2 ) {
        var formData = new FormData();
        formData.append("org_id", org_id);
        formData.append("lastorg_id", lastorgid);
        formData.append("logoFile1", logoFile1);
        formData.append("logoFile2", logoFile2);

        $.ajax({
            url: 'ajax/organization/UploadLogo.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
           success: function(response) {
                let logoFileNames = response.trim();

                if (
                    logoFileNames === "upload_error" ||
                    logoFileNames === "db_error" ||
                    logoFileNames === "no_id_or_file" ||
                    logoFileNames === "Image Size Is Too Large" ||
                    logoFileNames === "Invalid Image Extension"
                ) {
                    swal('', 'Logo Upload Failed: ' + logoFileNames, 'error');
                    return;
                }

                // Split the comma-separated filenames
                let logos = logoFileNames.split(',');
                let logoFile1 = logos[0] || "";
                let logoFile2 = logos[1] || "";

                submitOrganizationForm(
                    logoFile1,
                    logoFile2,
                    org_id,
                    organization_name,
                    contact,
                    email,
                    description,
                    gstNumber,
                    tanNumber,
                    longitude,
                    latitude,
                    address,
                    userLimit
                );
            },
            error: function(err) {
                console.error("Logo Upload Error:", err);
                swal('', 'Logo Upload Failed!', 'error');
            }
        });
    } else {
        submitOrganizationForm("", "", org_id, organization_name, contact, email, description, gstNumber, tanNumber, longitude, latitude, address, userLimit);

    }
});

 function submitOrganizationForm(uploadedLogo1, uploadedLogo2, org_id, organization_name, contact, email, description, gstNumber, tanNumber, longitude, latitude, address, userLimit) {
    // get the ip access value
    let ipAccessValue = document.getElementById("ipOpToggle").checked ? "OP and IP" : "OP";

    let requestData = {
        'org_id': org_id,
        'organization_name': organization_name,
        'contact': contact,
        'email': email,
        'description': description,
        'gstNumber': gstNumber,
        'tanNumber': tanNumber,
        'longitude': longitude,
        'latitude': latitude,
        'address': address,
        'userLimit': userLimit,
        'ipAccess': ipAccessValue   // 👈 send to backend
    };

     if (uploadedLogo1) {
        requestData.logo_with_text = uploadedLogo1;          // first logo
    }
    if (uploadedLogo2) {
        requestData.logo_without_text = uploadedLogo2; // second logo
    }
    $.ajax({
        url: 'ajax/organization/AddModifyOrganization.php',
        type: 'POST',
        data: requestData,
        success: function(data) {
            if (data == 1) {
                swal('', "\"" + organization_name + "\" Organization Record Added Successfully", 'success');
            } else if (data == 2) {
                swal('', "\"" + organization_name + "\" Organization Record Updated Successfully", 'success');
            } else if (data == 3) {
                swal('', "Organization Data Already Exists!", 'warning');
            } else if (data == 4) {
                swal('', "Organization Name Already Exists!", 'warning');
            } else if (data == 5) {
                swal('', "Contact Already Exists!", 'warning');
            } else if (data == 6) {
                swal('', "Email Already Exists!", 'warning');
            } else if (data == 7) {
                swal('', "GST Number Already Exists!", 'warning');
            } else if (data == 8) {
                swal('', "TAN Number Already Exists!", 'warning');
            } else {
                swal('', 'Unexpected Response: ' + data, 'error');
            }
            GetOrganization();
            $("#org_id").val('');
        },
        error: function(err) {
            console.log("Form Submit Error:", err);
            swal('', 'Save Request Failed!', 'error');
        }
    });
}


// Stamp preview
$('#stamp_file').on('change', function () {
    var file = this.files[0];
    if (!file) { $('#stampPreview').hide(); return; }
    var reader = new FileReader();
    reader.onload = function (e) {
        $('#stampPreviewImg').attr('src', e.target.result);
        $('#stampPreview').show();
    };
    reader.readAsDataURL(file);
});

// Upload stamp immediately when a file is chosen (only works when editing an existing org)
$('#stamp_file').on('change', function () {
    var orgId = $('#org_id').val();
    if (!orgId) {
        swal('', 'Please save the organisation first, then upload the stamp.', 'info');
        $(this).val('');
        $('#stampPreview').hide();
        return;
    }
    var file = this.files[0];
    if (!file) return;
    var fd = new FormData();
    fd.append('org_id', orgId);
    fd.append('stamp_file', file);
    $.ajax({
        url: 'ajax/organization/UploadStamp.php',
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.success) {
                swal('', 'Stamp uploaded successfully!', 'success');
            } else {
                swal('', 'Stamp upload failed: ' + res.message, 'error');
            }
        },
        error: function () {
            swal('', 'Stamp upload request failed.', 'error');
        }
    });
});

// Edit function remains the same
function editOrganization(org_id, organization_name, contact, email, description, gstNumber, tanNumber, longitude, latitude, address, userLimit, opipaccess) {
    window.scrollTo(0,0);
    $("#org_id").val(org_id);
    $("#organization_name").val(organization_name);
    $("#contact").val(contact);
    $("#email").val(email);
    $("#description").val(description);
    $("#gstNumber").val(gstNumber);
    $("#tanNumber").val(tanNumber);
    $("#longitude").val(longitude);
    $("#latitude").val(latitude);
    $("#address").val(address);
    $("#userLimit").val(userLimit);

    // ✅ Handle toggle
    if (opipaccess=== "OP and IP") {
        $("#ipOpToggle").prop("checked", true);
        $("#toggleLabel").text("OP & IP");
    } else {
        $("#ipOpToggle").prop("checked", false);
        $("#toggleLabel").text("OP");
    }
}



// Delete Organization Data
function deleteOrganization(org_id, organization_name) {
    swal({
        title: "Are you sure?",
        text: "Do you want to delete \"" + organization_name + "\" Organization Record!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: 'ajax/organization/DeleteOrganization.php',
                type: 'POST',
                data: {
                    'org_id': org_id
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', organization_name + ' Organization Record Deleted Successfully', 'success');
                        GetOrganization();
                    } else {
                        swal('','Error occured. Please try again', 'error')
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });
            
            $('#deleteID').val(org_id);
            swal('', organization_name + ' Organization Record Deleted Successfully', 'success').then((result) => {
                $('#deleteFormId').submit();
            });
        }
    });
}


// Organization Name
$(function () {
    $("#organization_name").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        $("#organization_nameId").html("");
        var regex = /^[A-Za-z ]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
            $("#organization_nameId").html("Only Alphabets Allowed.");
        }
        return isValid;
    });
});
$(function () {
    $("#organization_name").keyup(function () {
        var organizationName = $(this).val();
        if (!organizationName.trim()) {
        $(this).val('');
        }
    });
});
$(function () {
    $("#organization_name").on("paste", function (e) {
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");
        var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); // Remove non-alphabetic characters
        document.execCommand("insertText", false, cleanedValue);
        e.preventDefault();
    });
});




// GST number
$(function () {
    $("#gstNumber").on("keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        var char = String.fromCharCode(keyCode);

        var regex = /^[a-zA-Z0-9]+$/;
        if (!regex.test(char)) {
            $("#gstNumberId").html("Only Alphabets (A-Z) and Numbers (0-9) Allowed.");
            return false;
        } else {
            $("#gstNumberId").html("");
        }

        if (this.value.length >= 15) {
            return false;
        }
    });

    $("#gstNumber").on("input", function () {
        var input = $(this).val();

        input = input.toUpperCase().replace(/[^A-Z0-9]/g, '');

        // Limit to 15 characters
        if (input.length > 15) {
            input = input.substring(0, 15);
        }

        $(this).val(input);
    });

    // On paste event
    $("#gstNumber").on("paste", function (e) {
        e.preventDefault();
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");

        // Clean pasted data
        pastedData = pastedData.toUpperCase().replace(/[^A-Z0-9]/g, '');
        pastedData = pastedData.substring(0, 15);

        document.execCommand("insertText", false, pastedData);
    });
});


$(function () {
    $("#tanNumber").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        var char = String.fromCharCode(keyCode);
        $("#tanNumberId").html("");
        var regex = /^[a-zA-Z0-9]+$/;
        var isValid = regex.test(char);
        if (!isValid) {
            $("#tanNumberId").html("Only Alphabets and Numbers Allowed.");
            return false;
        }
        if (this.value.length >= 10) {
            return false;
        }
    });

    $("#tanNumber").on("input", function () {
        var input = $(this).val();

        input = input.toUpperCase().replace(/[^A-Z0-9]/g, '');

        if (input.length > 10) {
            input = input.substring(0, 10);
        }

        $(this).val(input); // Update value
    });

    // On paste
    $("#tanNumber").on("paste", function (e) {
        e.preventDefault();
        var pastedData = e.originalEvent.clipboardData.getData("text/plain");

        pastedData = pastedData.toUpperCase().replace(/[^A-Z0-9]/g, '');
        pastedData = pastedData.substring(0, 10);

        document.execCommand("insertText", false, pastedData);
    });
});






// longitude
$(function () {
    $("#longitude").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        $("#longitudeID").html("");
        var regex = /^[0-9.]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
        $("#longitudeID").html("Only Decimal Number Allowed.");
        }
        return isValid;
    });
});

// latitude
$(function () {
    $("#latitude").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        $("#latitudeID").html("");
        var regex = /^[0-9.]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        if (!isValid) {
            $("#latitudeID").html("Only Decimal Number Allowed.");
        }
        return isValid;
    });
});
 
</script>