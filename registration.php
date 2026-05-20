<?php
    require_once("ajax/header.php");
    // FIX_B_1850: per-action RBAC. Block users without view permission on
    // registration.php (Receptionist/Pharmacist/etc.). SA (security_id=1 OR
    // role_id=1) is auto-allowed by userCan().
    requireCan('view', basename(__FILE__));
    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';
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

    .password-container {
        position: relative;
    }

    .toggle-password{
        cursor: pointer;  
        position: absolute;
        top: 50%;
        right: 20px;
        transform: translateY(-20%);
        font-size: 25px;
    }
    
    .eyeIcon{
        width:20px;
    }

</style>

<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Access Control</h4>
            </li>
            <li class="breadcrumb-item active">Registration</li>
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
                    <h4>Registration</h4>
                </div>
                
                <form method="POST" id="regisformid" action="" enctype="multipart/form-data" >
                    <input type="hidden" name="regis_hid_id" id="regis_hid_id" value="" >
                    <div class="card-body">
                        <div class="row">
  
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="User Name"> User Name <span class="text-danger">*</span></label>
                                <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </div>
                                      </div>
                                    <input type="text" class="form-control" name="admin_name" id="admin_name" value="" minlength="" maxlength="" title="Please Enter Valid User Name" > 
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="Email">Email <span class="text-danger">*</span></label>
                                <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                      </div>
                                    <span id="lblError2"></span>
                                    <input type="text" class="form-control" name="email" id="email"  value="" title="Please Enter Valid Email" >
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                              <label for="Contact" class="contact">Contact <span class="text-danger">*</span></label>
                               <div class = "input-group">
                                      <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-telephone-fill"></i>
                                        </div>
                                      </div>
                                  <input type="text" class="form-control" class="contact" name="contact" id="contact" value=""  >
                                </div>

                            </div>

                            <div class="form-group col-lg-4 col-sm-12 password-container" id="password_field" >
                                <label for="Password" class="password"> Password <span class="text-danger">*</span></label>
                                <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                    <input type="Password" class="form-control" class="password" name="security_password" id="security_password" value="" title="Please Enter Valid password">
                                </div>
                                <div class="toggle-password">
                                    <img id="eyeIcon" class="eyeIcon" src="img/hidden.png" alt="Eye Icon">
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                               <label for="security" class="security"> Roles <span class="text-danger">*</span></label>
                               <div class = "input-group">
                                    <div class= "input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <select class="form-control form-select" name="security" id="security"></select>
                                </div>  
                            </div>
                            <?php 
                            if($SessionUserId == "1"){
                                ?>
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="organizations">Organization <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-buildings-fill"></i></span>
                                    <select class="form-control form-select" name="organizations" id="organizations">
                                        <!-- Options go here -->
                                    </select>
                                </div>
                            </div>
                            <?php
                            }
                            ?>

                            <?php /* FIX_B_2320: opt-in capability — when checked, this Receptionist
                                 user gets the doctor-switcher widget in the top bar so she can
                                 focus on one OP room's queue. Only meaningful for role=Receptionist
                                 (role_id=3); hidden for any other role. Persists in
                                 security.can_switch_doctor. */ ?>
                            <div class="form-group col-lg-12 col-sm-12 d-none" id="canSwitchDoctorRow">
                                <div class="form-check" style="background:rgba(212,168,75,.08);border:1px solid rgba(212,168,75,.35);border-radius:4px;padding:12px 16px;">
                                    <input class="form-check-input" type="checkbox" name="can_switch_doctor" id="can_switch_doctor" value="1">
                                    <label class="form-check-label" for="can_switch_doctor" style="font-weight:600;">
                                        Allow switching between doctors
                                    </label>
                                    <div class="small text-muted mt-1">
                                        When enabled, a doctor-picker appears in this receptionist's top bar. She can view all doctors\' queues merged, or narrow to one doctor when one OP room is busier than the other. Leave unchecked for receptionists who should always see the merged view.
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-center">
                                <?php /* FIX_B_1850: only render submit if user has add OR edit; the AJAX handler will split add vs edit by security_id presence. */ ?>
                                <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))) { ?>
                                <button class="btn btn-primary" name="regisdata" id="regisdata" value="">Submit</button>
                                <?php } ?>
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
                        <h4>Registration List</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" id="regis">
                            <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="" method="POST" id="deleteregisFormid">
            <input type="hidden" name="deleteregisid" id="deleteregisid" value="" /> 
            <input type="hidden" name="role_id" id="role_id">
        </form> 
        
    </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>

    $("document").ready(function() {
        Getregis();
        Getsecurity();
        Getroleid();
        GetOrganization();
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("security_password");
        const passwordIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.src = " img/eye.png"; 
        } else {
            passwordInput.type = "password";
            passwordIcon.src = " img/hidden.png"; 
        }
    }

    const eyeIcon = document.getElementById("eyeIcon"); 
    eyeIcon.addEventListener("click", togglePasswordVisibility);

    var org_id = '<?=$SessionOrgId ?>';

    function Getregis() {
        $.ajax({
            url: 'ajax/accesscontrol/registration/getregistration.php',
            type: 'GET',
            success: function(data) {
                
                if(data) {
                    $("#regis").html(data);
                    document.getElementById("regisformid").reset();
                    var buttons_array =  [ 0, 1, 2, 3, 4]; 
                    if(org_id == "0"){
                        buttons_array = [ 0, 1, 2, 3, 4, 5];
                        }
                    $("#tableregis1").dataTable({
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
                console.log(data);
            },
            error: function(err)  {
                console.log(err);
            }
        });
    }

    function Getsecurity() {
        $.ajax({
            url: 'ajax/accesscontrol/registration/getsecurity.php',
            type: 'GET',
            dataType:'json',
            success: function(data) {
                var optiondata = '<option value="">Select Security</option>';
                $.each(data, function(key, val){
                    // FIX_B_2320: tag each role option with its name so the
                    // "can switch doctors" row can show/hide for Receptionist only.
                    var rname = (val.role_name || '').toString();
                    optiondata +='<option value="'+ val.role_id +'" data-role-name="'+ rname +'">' + rname + '</option>'
                });
                $("#security").html(optiondata);
                toggleCanSwitchRow();   // re-evaluate after options load
            },
            error: function(err)  {
                console.log(err);
            }
        });
    }

    // FIX_B_2320: show the "can switch doctors" checkbox only when the
    // selected role's name contains "reception". Role names are configurable
    // in masters, so we match by name (case-insensitive) rather than hard-
    // coding role_id=3 — same logic stays correct if numbering ever changes.
    function toggleCanSwitchRow() {
        var $sel = $("#security");
        var roleName = ($sel.find('option:selected').data('role-name') || '').toString();
        var isReceptionist = /reception/i.test(roleName);
        $("#canSwitchDoctorRow").toggleClass('d-none', !isReceptionist);
        if (!isReceptionist) {
            $("#can_switch_doctor").prop('checked', false);
        }
    }
    $(document).on('change', '#security', toggleCanSwitchRow);

    function GetOrganization() {
        $.ajax({
            url: 'ajax/accesscontrol/registration/getOrganization.php',
            type: 'GET',
            dataType:'json',
            success: function(data) {
                console.log(data);
                var optiondata = '<option value="">Select Organization</option>';
                $.each(data, function(key, val){
                    optiondata +='<option value="'+ val.org_id +'">' +val.organization_name + '</option>'
                });
                $("#organizations").html(optiondata);
            },
            error: function(err)  {
                console.log(err);
            }
        });
    }

    function Getroleid(){
        var role_id = $('#role_id');
        $.ajax({
            url:'ajax/accesscontrol/registration/getroleid.php',
            type: 'GET',
            datatype: 'json',
            success: function(data) {
                console.log(data);
            data.role_id = role_id.val();
            },
        });
    }

    $("#regisformid").submit(function(){
        event.preventDefault();
        var security_id = $("#regis_hid_id").val();
        var admin_name = $("#admin_name").val().trim();
        var email = $("#email").val();  

        var contact = $("#contact").val();
        var security_password = $("#security_password").val();
        var security = $("#security").val();
        var organizations = $("#organizations").val();
        // FIX_B_2320: capture the opt-in capability flag (only meaningful for receptionists).
        var canSwitchDoctor = $("#can_switch_doctor").is(":checked") ? 1 : 0;

        if(admin_name != "" && email != "" && contact != "" && security != "" && organizations != ""){
        } else {
            swal('', 'Please fill all the mandatory fields!','warning');
            return ;
        } 

        if (security_password == null || security_password == ""){
            swal('', 'Please Enter Password!','warning');
            return ;
       } 

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test($('#email').val())) {
            swal('', 'Please enter a valid email address', 'warning');
            return;
        }

        if(!$('#contact').val().match('[0-9]{10}')){
            swal('', 'Please Enter 10 digit mobile number','warning');
            return;
        }
        $.ajax({
            url: 'ajax/accesscontrol/registration/insertregistration.php',
            type: 'POST',
            data: {
                'regis_hid_id':security_id,
                'admin_name':admin_name,
                'email':email,
                'contact':contact,
                'security_password': security_password,
                'security':security,
                'organizations':organizations,
                'can_switch_doctor': canSwitchDoctor    /* FIX_B_2320 */
            },
            success: function(data) {
                if(data == 1) {
                    swal('', ' Register Successfully', 'success');
                    $("#regis_hid_id").val('');
                    Getregis();
                    Getsecurity()
                    GetOrganization()
                } else if(data == 2) {
                    
                    swal({
                        title: '',
                        text: 'Updated Successfully',
                        icon: 'success',
                        buttons: {
                            ok: {
                                text: 'OK',
                                value: true,
                                visible: true,
                                className: 'btn btn-primary'
                            }
                        },
                        allowOutsideClick: false
                    }).then(function() {
                        $("#regis_hid_id").val('');
                        Getregis();
                            Getsecurity()
                            GetOrganization()
                        
                        location.reload();
                    });
                } else if(data == 4) {
                    swal('', "  User Name Already Exists", "warning");
                } else if(data == 5) {
                    swal('', "   Email Already Exists", "warning");
                } else if(data == 6) {
                    swal('', "  Contact Already Exists", "warning");
                } else if (data == 7){
                    swal('',"User limit reached ", 'warning');
                } else {
                    swal('','Error occured. Please try again', 'error');
                }
            },
            error: function(err)  {
                console.log(err);
            }
        });
    });

    function showPasswordField() {
        $("#password_field").show();
    }

    function editregis(security_id,admin_name,email,contact,security_password,security,organizations,canSwitchDoctor) {
        window.scrollTo(0,0);

        $("#regis_hid_id").val(security_id);
        $("#admin_name").val(admin_name);
        $("#email").val(email);
        $("#contact").val(contact);
        $("#security_password").val(security_password);
        $("#security").val(security);
        $("#organizations").val(organizations);
        // FIX_B_2320: prefill the opt-in checkbox + re-evaluate visibility for the loaded role.
        $("#can_switch_doctor").prop('checked', String(canSwitchDoctor) === '1');
        toggleCanSwitchRow();

        $("#password_field").hide();
    }

    function deleteregis(security_id, admin_name) {    
        swal({
            title: "Are you sure?",
            text: "Do you wish to delete Registration Record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/accesscontrol/registration/deleteregistration.php',
                    type: 'POST',
                    data: {
                        'security_id': security_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal("Registration Record", "Deleted Successfully");
                            Getregis();
                            Getsecurity()
                        } else {
                            swal("error", "Error occured. please try again");
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteregisid').val(security_id);
                swal('',' Deleted Successfully', 'success').then((result) => {
                    $('#deleteregisFormid').submit();
                });
            }
        });
    }

    const $input = document.querySelector("#contact");
    const PHONENUMBER_ALLOWED_CHARS_REGEXP = /[0-9\10/10]+/;

    $input.addEventListener("keypress", (e) => {
        if (!PHONENUMBER_ALLOWED_CHARS_REGEXP.test(e.key)) {
            e.preventDefault();
            swal("Only Numbers Allowed.");
        }
    });

    $input.addEventListener("paste", (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData("text/plain");
        const cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
    });

    setInterval(function () {
        original = document.getElementById("contact").value;
        if (original.length > 10) {
            lastCharRemove = original.slice(0, original.length - 1);
            document.getElementById("contact").value = lastCharRemove;
        }
    }, 100);

    $(function () {
        $("#admin_name").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#lblError1").html("");

            var regex = /^[A-Za-z.\s]+$/;

            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
            $("#lblError1").html("Only Alphabets allowed.");
            }
            return isValid;
        });
    });

    $(function () {
        $("#admin_name").keyup(function () {
            var organizationName = $(this).val();
            if (!organizationName.trim()) {
            $(this).val('');
            }
        });
    });

    $(function () {
        $("#admin_name").on("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); 
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    $(function () {
        $("#email").keypress(function (e) {
            var keyCode = e.keyCode || e.which;

            $("#lblError2").html("");

            var regex = /^[A-Za-z0-9@.]+$/;

            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError2").html("Only Alphabets&Number allowed.");
            }

            return isValid;
        });
    });

</script>