<?php
// FIX_B_1300 — RBAC: pharmacist must not access admin-only doctor.php via
// direct URL. Page-level guard added BEFORE the ajax/header.php include so
// the redirect can fire before any output.
// FIX_B_2251: roles were renumbered (Doctor=2, Receptionist=3, Pharmacist=4,
// Accountant=5, Admin=6). The old constant `12` referred to a stale legacy
// pharmacist role_id; current pharmacist is role_id=4. Adding accountant=5
// to the deny-list as well — neither finance nor pharmacy needs CRUD on
// doctor records. SA(1)/Doctor(2)/Admin(6) keep access.
require_once(__DIR__ . "/include/auth_guard.php");
denyPageRoles([4, 5]);
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

  $SessionUserId = $_SESSION['security_id'] ?? '';
  $SessionRoleId = $_SESSION['role_id'] ?? '';
  $SessionOrgId = $_SESSION['org_id'] ?? '';

  $sql = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM organization WHERE status='1' AND org_id='$SessionOrgId'"));
  $opipaccess = $sql['opipaccess'];
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
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Doctors</h4>
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
            <li class="breadcrumb-item">Add & Modify Doctors</li>
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
                    <h4>Doctors</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="" >
                    <input type="hidden" name="doc_id" id="doc_id" value="" >
                    <input type="hidden" name="securityId" id="securityId">
                    <div class="card-body">
                        <div class="row">
                            <?php 
                                $SessionUserId = $_SESSION['security_id'] ?? '';
                                $SessionRoleId = $_SESSION['role_id'] ?? '';
                                $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                            ?>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="organizations" class="Organization">Organization <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-buildings-fill"></i>
                                    </span>
                                    <select class="form-control form-select" name="organizations" id="organizations" onchange="GetOrgByIdsautoincrement()">
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
                            <div class="form-group col-lg-3 col-sm-12">
                                <label > Registration Number <span class="text-danger" id="number">*</span> </label>
                                <input class="form-control doc_num" name="doc_num" id="doc_num"  value="" disabled>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="doc_name">
                                    Doctor <span class="text-danger" id="name">*</span>
                                </label>
                                <div class="input-group" id="doctorInputGroup">
                                    <span class="input-group-text">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                    <select id="doc_name" name="doc_name" class="form-control form-select" onchange="loadSecurityDetails(this.value)">
                                        <option value="">Select Doctor</option>
                                        <?php
                                        if ($SessionUserId == "1" && $SessionRoleId == "1") {
                                            $query = "
                                                SELECT s.security_id, s.admin_name 
                                                FROM security s
                                                WHERE s.status = '1'
                                                AND s.security_id != '1'
                                                AND s.security_id NOT IN (SELECT d.security_id FROM doctors d WHERE d.status='1')
                                                AND s.security_id NOT IN (SELECT r.security_id FROM receptionnist r WHERE r.status='1')
                                            ";
                                        } else {
                                            $query = "
                                                SELECT s.security_id, s.admin_name 
                                                FROM security s
                                                WHERE s.status = '1'
                                                AND s.security_id != '1'
                                                AND s.org_id = '$SessionOrgId'
                                                AND s.security_id NOT IN (SELECT d.security_id FROM doctors d WHERE d.status='1' AND d.org_id = s.org_id)
                                                AND s.security_id NOT IN (SELECT r.security_id FROM receptionnist r WHERE r.status='1' AND r.org_id = s.org_id)
                                            ";
                                        }

                                        $docRes = mysqli_query($conn, $query) or die(mysqli_error($conn));

                                        while ($dr = mysqli_fetch_object($docRes)) {
                                            echo "<option value=\"{$dr->security_id}\">{$dr->admin_name}</option>";
                                        }
                                        ?>
                                    </select>
                                    <select id="doc_name_input" name="doc_name_input" class="form-control form-control" style="display:none;" readonly>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12 col-12">
                                <label for="menu_web_url">Gender <span class="text-danger">*</span></label>
                                <div class="selectgroup w-100 d-flex ">
                                    <label>
                                        <input type="radio" name="gender" id="male" value="Male" class="selectgroup-input-radio"  />
                                        <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1 text-dark">
                                            <i class="bi bi-gender-male"></i> Male
                                        </span>
                                    </label>                                        
                                    <label>
                                        <input type="radio" name="gender" id="female" value="Female" class="selectgroup-input-radio" />
                                        <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1 text-dark">
                                            <i class="bi bi-gender-female"></i> Female
                                        </span>
                                    </label>
                                    <label>
                                        <input type="radio" name="gender" id="others" value="Others" class="selectgroup-input-radio" />
                                        <span class="selectgroup-button d-flex align-items-center justify-content-center  px-2 py-1 text-dark">
                                        <i class="bi bi-gender-ambiguous"></i> Other
                                        </span>
                                    </label>                     
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="phonenumber">Phone Number <span class="text-danger" id="phpnenumbe">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-telephone-fill"></i>
                                    </span>
                                    <input type="text" class="form-control" name="phonenumber" id="phonenumber">
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="doc_email">Email <span class="text-danger" id="doc_Email">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>
                                    <input type="email" class="form-control" name="doc_email" id="doc_email">
                                </div>
                            </div>

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="departments">Departments <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                    <i class="material-icons">local_hospital</i>
                                    </span>
                                    <select class="form-control form-select" name="departments" id="departments">
                                        <option value=""> Select Departments</option>
                                        <?php
                                        if($SessionUserId == "1" && $SessionRoleId == "1"){
                                            $getMenus = mysqli_query($conn,"SELECT * FROM department WHERE status='1' ORDER BY dept_id DESC") or die(mysqli_error($conn));
                                        } else {
                                            $getMenus = mysqli_query($conn,"SELECT * FROM department WHERE status='1' AND org_id='$SessionOrgId' ORDER BY dept_id DESC") or die(mysqli_error($conn));
                                        }
                                        while ($resMenus = mysqli_fetch_object($getMenus)) {
                                        ?>
                                            <option value="<?=$resMenus->dept_id?>"><?=$resMenus->departmentName?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for=""><i class="fa fa-stethoscope"></i> Specialization <span class="text-danger">*</span></label>
                            
                                    <select  class="form-control " name="specialtis" id="specialtis" multiple>
                                    
                                        <?php
                                        if($SessionUserId == "1" && $SessionRoleId=="1"){
                                            $getMenus = mysqli_query($conn,"SELECT specialtis_id, specialtisname FROM specialtis WHERE status='1' ORDER BY specialtis_id DESC") or die(mysqli_error($conn));
                                        } else{
                                            $getMenus = mysqli_query($conn,"SELECT specialtis_id, specialtisname FROM specialtis WHERE status='1'AND org_id='$SessionOrgId' ORDER BY specialtis_id DESC") or die(mysqli_error($conn));
                                        }
                                            while ($resMenus = mysqli_fetch_object($getMenus)) {
                                        ?>
                                            <option value="<?=$resMenus->specialtis_id?>"> <?=$resMenus->specialtisname?> </option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                            </div>

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for=""><i class="bi bi-briefcase-fill"></i> Services <span class="text-danger">*</span> </label>
                                <select  class="form-control form-select" name="doc_services" id="doc_services" multiple>
                                <?php
                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                                    $getMenus = mysqli_query($conn,"SELECT service_id,service_name FROM services WHERE status='1' ORDER BY service_id DESC") or die(mysqli_error($conn));
                                } else{
                                    $getMenus = mysqli_query($conn,"SELECT service_id,service_name FROM services WHERE status='1'AND org_id='$SessionOrgId' ORDER BY service_id DESC") or die(mysqli_error($conn));
                                }
                                while ($resMenus = mysqli_fetch_object($getMenus)) {
                                ?>
                                    <option value="<?=$resMenus->service_id?>"> <?=$resMenus->service_name?> </option>
                                <?php
                                    }
                                ?>
                                </select>
                            </div>

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="dpic"> Doctor Picture </label><span class="text-danger">*</span>
                                <input type="file" class="form-control" name="dpic" id="dpic" accept=".jpg, .jpeg, .png" value="">
                            </div>
                            <?php if($opipaccess == "OP and IP"){ ?>
                               <div class="form-group col-lg-3 col-sm-12">
                                            <label for="doctor_type"> Doctor Type <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-person-lines-fill"></i>
                                                </span>
                                                <select name="doctor_type" id="doctor_type" class="form-control" required>
                                                    <option value="">--- Select ---</option>
                                                    <option value="In">Inside the hospital</option>
                                                    <option value="Out">Outside the hospital</option>
                                                </select>
                                            </div>
                                    </div>
                            <?php } else { ?>
                                <input type="hidden" name="doctor_type" id="doctor_type" value="In">
                            <?php } ?>
                          
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="price"> Consultation Fee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="text" class="form-control" name="price" id="price" required />
                                </div>
                            </div>
                         
                            <?php if($opipaccess == "OP and IP"){ ?>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="doctor_charge">IP Fee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="text" class="form-control" name="doctor_charge" id="doctor_charge" value="" required />
                                </div>
                            </div>
                            <?php } else { ?>
                                <input type="hidden" name="doctor_charge" id="doctor_charge" value="0">
                            <?php } ?>
                            <?php if($opipaccess == "OP and IP"){ ?>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="doctor_visit_charge">Visit Fee </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                    </span>
                                    <input type="number" class="form-control" name="doctor_visit_charge" id="doctor_visit_charge">
                                 </div>
                             </div> 
                             <?php } else { ?>
                                <input type="hidden" name="doctor_visit_charge" id="doctor_visit_charge" value="0">
                                <?php } ?>        
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="time_slot_duration ">Time Slot Duration <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="time_slot_duration" id="time_slot_duration" placeholder="Minutes" required />
                                    <span class="input-group-text">Minute</span>
                                    </div>
                            </div>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label>Receptionist(s) <span class="text-danger">*</span></label>
                                <select class="form-control" name="receptionist_name" id="receptionist_name" multiple>
                                    <?php
                                    $rQuery = "SELECT s.security_id, s.admin_name FROM security s WHERE s.status='1' AND s.security_id != '1' AND s.security_id NOT IN (SELECT d.security_id FROM doctors d WHERE d.status='1')";
                                    if ($SessionUserId != "1") { $rQuery .= " AND s.org_id = '$SessionOrgId'"; }
                                    $rRes = mysqli_query($conn, $rQuery) or die(mysqli_error($conn));
                                    while ($r = mysqli_fetch_object($rRes)) {
                                        echo "<option value=\"{$r->security_id}\">{$r->admin_name}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-sm-12">
                                    <label> Details</span> </label>
                                    <textarea name="details" id="details" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <?php if (userCan('add', 'doctor.php') || userCan('edit', 'doctor.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Doctors List</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="showdata">
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script>

  $("document").ready(function() {
        Getdoctors(); 
        // $("#doc_name").select2({
        //     width: '75%'
        // }),

        // $("#receptionist_name").select2({
        //     width: '75%'
        // });
        $("#departments").select2({
            width: '70%'
        });


        // GetAutoserives();
        setTimeout(function() {
            if('<?=$SessionUserId?>' != '1'){
                getdoc();
            }
        }, 200);
    });

    function populateDoctorEdit(docId, securityId, doctorName, registrationNumber, doctorsecurity_id) {
        document.getElementById('doc_id').value = docId;
        document.getElementById('securityId').value = securityId;
        document.getElementById('doc_num').value = registrationNumber;

        document.getElementById('doc_name').style.display = "none";
        var select = document.getElementById('doc_name_input');
        select.style.display = "block";
        if (!Array.from(select.options).some(o => o.value == doctorsecurity_id)) {
            var option = document.createElement('option');
            option.value = doctorsecurity_id;
            option.text = doctorName;
            select.add(option);
        }
        select.value = doctorsecurity_id;
    }
    $(function () {
        $("#doc_name").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            $("#nameId").html("");
            var regex = /^[A-Za-z.\s]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#nameId").html("Only Alphabets Allowed.");
            }
            return isValid;
        });
    });

    $(function () {
        $("#doc_name").keyup(function () {
            var doc_name = $(this).val();
            if (!doc_name.trim()) {
            $(this).val('');
            }
        });
    });

    $(function () {
        $("#doc_name").on("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^A-Za-z ]/g, ""); 
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    jQuery("#price").keypress(function (e) {
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
    jQuery("#time_slot_duration").keypress(function (e) {
        var length = jQuery(this).val().length;
        if (e.which < 48 || e.which > 57) {
            if (![8, 0, 37, 38, 39, 40].includes(e.which)) {
                e.preventDefault();
            }
        }

        if (length >= 2) {
            swal("Warning", "Only 2 digits are allowed.", "warning");
            e.preventDefault();
        }
    });


    $(function () {
        $("#price").on("paste", function (e) {
            var pastedData = e.originalEvent.clipboardData.getData("text/plain");
            var cleanedValue = pastedData.replace(/[^\d\10/10]/g, ""); 
            document.execCommand("insertText", false, cleanedValue);
            e.preventDefault();
        });
    });

    const $input = document.querySelector("#phonenumber");
        const PHONENUMBER_ALLOWED_CHARS_REGEXP = /[0-9\/10]+/;
        $input.addEventListener("keypress", e => {
        console.log(e);
        if (!PHONENUMBER_ALLOWED_CHARS_REGEXP.test(e.key)) {
            e.preventDefault();
        }
    });

    $input.addEventListener("paste", (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData("text/plain");
        const cleanedValue = pastedData.replace(/[^\d\10/10]+/g, ""); 
        document.execCommand("insertText", false, cleanedValue);
    });

    setInterval(function () {
        original = document.getElementById("phonenumber").value;
        if (original.length > 10) {
            lastCharRemove = original.slice(0, original.length - 1);
            document.getElementById("phonenumber").value = lastCharRemove;
        }
    }, 100);

   function loadSecurityDetails(securityId) {
        $.ajax({
            url: 'ajax/doctors/getUserData.php',
            type: 'POST',
            data: { securityId: securityId },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    $('#phonenumber').val(response.contact);
                    $('#doc_email').val(response.email);
                    $('#securityId').val(response.security_id);
                } else {
                    console.warn("No data found for this doctor");
                }
            },
            error: function(err) {
                console.error("Error fetching doctor details:", err);
                swal('', 'Failed to fetch doctor details!', 'error');
            }
        });
    }
    $("#saveData").click(function(){
    
        var name=$("#doc_num").val();
        if(name == "") {
            swal('',"Please Enter Doctor Registration Number",'warning')
            return false; 
        }
        var doctorName = $("#doc_name").val();
        var doctorNameInput = $("#doc_name_input").val();
        if ((!doctorName || doctorName.trim() === "") && 
            (!doctorNameInput || doctorNameInput.trim() === "")) {
            swal('', "Please select or enter a Doctor Name" , 'warning');
            return false;
        }
        var name = doctorName && doctorName.trim() !== "" ? doctorName : doctorNameInput;

        var Gender = $("input[name='gender']:checked").val();
        if(Gender == "") {
            swal('',"Please Select Gender",'warning')
            return false;
        }
        var phonenumber=$("#phonenumber").val();
        if(phonenumber == "") {
            swal('',"Please Enter Your Phonenumber",'warning')
            return false;
        }
        var departments=$("#departments").val();
        if(departments == "") {
            swal('',"Please Select departments",'warning')
            return false;
        }
        
        var specialtis=$("#specialtis").val();
        if(specialtis == "") {
            swal('',"Please Select Specialtization",'warning')
            return false;
        }

        var servies=$("#doc_services").val();
        if(servies == "") {
            swal('',"Please Select doctors services",'warning')
            return false;
        }

        var doctor_fee=$("#price").val();
        if(doctor_fee == "") {
            swal('',"Please Enter Doctor Fee",'warning')
            return false;
        }  
        var organizations=$("#organizations").val();
        if(organizations == "") {
            swal('',"Please Select Organizations",'warning')
            return false;
        }
        var details=$("#details").val();
        var time_slot_duration=$("#time_slot_duration").val();
        if(time_slot_duration == "") {
            swal('',"Please Select Duration",'warning')
            return false;
        }
        
    });
 
    var Specialization = new Choices('#specialtis', {
        removeItemButton: true,
    });

    var Services = new Choices('#doc_services', {
       removeItemButton: true,
    });

    var Receptionists = new Choices('#receptionist_name', {
        removeItemButton: true,
        searchEnabled: true,
        placeholderValue: 'Select Receptionist(s)',
    });


    function Getdoctors() {
        $.ajax({
            url: 'ajax/doctors/getdoctordata.php',
            type: 'GET',
            success: function(data) {
                
                var org_id = '<?=$SessionOrgId ?>';

                if(data) {
                    $("#showdata").html(data);
                    document.getElementById("FormId").reset();
                    var   buttons_array= [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];;
                    if(org_id == "0"){
                        buttons_array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,];
                    }
                    $("#tableExport1").dataTable({
                       
                        retrieve: true,
                        dom: 'lBrftip',
                        buttons: [
                                    {
                                        extend: 'copy',
                                        exportOptions: {
                                        columns: buttons_array,
                                        },
                                    },
                                    {
                                        extend: 'excel',
                                        exportOptions: {
                                        columns: buttons_array,
                                        },
                                    },
                                    {
                                        extend: 'csv',
                                        exportOptions: {
                                        columns: buttons_array,
                                        },
                                    },
                                    {
                                        extend: 'pdf',
                                        exportOptions: {
                                        columns: buttons_array,
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

        var doc_id = $("#doc_id").val();
        var doc_num = $("#doc_num").val().trim();
        var doc_num = $("#doc_num").val().trim();
        var $doctorField = $("#doc_name_input").is(":visible") ? $("#doc_name_input") : $("#doc_name");
        var securityId = $doctorField.val();
        var doc_name = $doctorField.find("option:selected").text();

        console.log("Selected Doctor ID:", securityId);
        console.log("Selected Doctor Name:", doc_name);
        var doctor_type = $("#doctor_type").val();

        var Gender = $("input[name='gender']:checked").val();
        var phonenumber = $("#phonenumber").val();
        var doc_email = $("#doc_email").val();
        
        var specialtis = $("#specialtis").val();
        var doc_services = $("#doc_services").val();
        var departments = $("#departments").val();
        var doctor_fee = $("#price").val();
        var doctor_visit_charge = $("#doctor_visit_charge").val();
        var organizations = $("#organizations").val();
        var time_slot_duration = $("#time_slot_duration").val();
        var details = $("#details").val();
        var doctor_charge = $("#doctor_charge").val();
        var securityId = $("#securityId").val();
    
        var dpicFile = $("#dpic")[0].files[0];

        var receptionistIds = $("#receptionist_name").val() || [];
        var receptionistUsers = $("#receptionist_name option:selected").map(function() {
            return $(this).text().trim();
        }).get();

        if (!dpicFile) {
            swal('', 'Please Upload logo!', 'warning');
            return;
        }
                
        if(!$('#phonenumber').val().match('[0-9]{10}'))  {
            swal('warning', 'Please Enter 10 digit mobile number!','warning');
            return;
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test($('#doc_email').val())) {
            swal('','Please Enter valid Email!', 'warning');
            return;
        }

        if(doc_name != "" || Gender != "" || phonenumber != "" || specialtis != "" || doctor_fee != "" || organizations != "" ) {

            var formData = new FormData();
            formData.append("dpic", dpicFile);

            $.ajax({
                url: 'ajax/doctors/Upload_dpic.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let dpicFileName = response.trim();
                    console.log(dpicFileName);

                    if (
                        dpicFileName === "upload_error" ||
                        dpicFileName === "db_error" ||
                        dpicFileName === "no_id_or_file" ||
                        dpicFileName === "Image Size Is Too Large" ||
                        dpicFileName === "Invalid Image Extension"
                    ) {
                        swal('', 'Doctor picture Upload Failed: ' + dpicFileName, 'error');
                        return;
                    }
                    submitOrganizationForm(dpicFileName);
                },
                error: function(err) {
                    console.error("Doctor picture Upload Error:", err);
                    swal('', 'Doctor picture Upload Failed!', 'error');
                }
            });

            function submitOrganizationForm(uploadedLogo) {
                $.ajax({
                    url: 'ajax/doctors/adddoctor.php',
                    type: 'POST',
                    data: {
                        'doc_id': doc_id,
                        'doc_num': doc_num,
                        'doc_name': doc_name,
                        'doctor_type': doctor_type,
                        'gender': Gender,
                        'phonenumber': phonenumber,
                        'doc_email': doc_email,
                        'specialtis': specialtis.toString(),
                        'doc_services': doc_services.toString(),
                        'departments': departments,
                        'doctor_fee': doctor_fee,
                        'doctor_visit_charge': doctor_visit_charge,
                        'organizations': organizations,
                        'time_slot_duration': time_slot_duration,
                        'details': details,
                        'doctor_charge': doctor_charge,
                        'doc_img': uploadedLogo,
                        'securityId': securityId,
                        'receptionistIds': receptionistIds.join(','),
                        'receptionistUsers': receptionistUsers.join(',')
                    },
                    success: function(data) {
                        resetDoctorForm();
                        if(data == 1) {
                            swal('', " Doctor Record Added  Successfully ",'success');
                            $("#doc_id").val('');
                            Getdoctors();
                            getdoc();
                            $("#FormId")[0].reset();
                        } else if(data == 2) {
                            swal('', "Doctor Record Updated Successfully ",'success');
                            $("#doc_id").val('');
                            Getdoctors();
                            getdoc();
                            $("#FormId")[0].reset();
                        } else if(data == 3) {
                            swal('',  " Doctor Phone Number Already Exists!",'warning');
                        } else if(data == 4) {
                            swal('', " Doctor Email Already Exists!",'warning');
                        } else if(data == 0) {
                            swal('', " All fields are required!",'warning');
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
            }
        }
    });

    function resetDoctorForm() {
        document.getElementById('doc_id').value = "";
        document.getElementById('securityId').value = "";
        $("#departments").val('').trigger("change");
        document.getElementById('doc_num').value = "";
        document.getElementById('doc_name').style.display = "block";
        document.getElementById('doc_name_input').style.display = "none";
        document.getElementById('doc_name').value = "";
        Receptionists.removeActiveItems();
    }

   function editdoctor(
        doc_id, doc_num, doctorsecurity_id, doc_name, doctor_type, gender, phonenumber, doc_email,
        departments, specialtis, doc_services, doctor_fee, doctor_charge,
        doctor_visit_charge, time_slot_duration, details, organizations, security_id, rep_security_ids
        ) {
        window.scrollTo(0, 0);

        populateDoctorEdit(doc_id, security_id, doc_name, doc_num, doctorsecurity_id);

        Receptionists.removeActiveItems();
        if (rep_security_ids) {
            Receptionists.setChoiceByValue(rep_security_ids.toString().split(',').map(function(id){ return id.trim(); }).filter(Boolean));
        }

        $("#securityId").val(security_id);

        $("#doctor_type").val(doctor_type);

        $("#male").prop("checked", gender === "Male");
        $("#female").prop("checked", gender === "Female");
        $("#others").prop("checked", gender === "Other");

        $("#phonenumber").val(phonenumber);
        $("#doc_email").val(doc_email);

        $("#doc_services").val(doc_services);
        $("#departments").val(departments).trigger("change");

        $("#price").val(doctor_fee);
        $("#doctor_charge").val(doctor_charge);
        $("#doctor_visit_charge").val(doctor_visit_charge);
        $("#time_slot_duration").val(time_slot_duration);
        $("#details").val(details);
        $("#organizations").val(organizations);
        Specialization.removeActiveItems();
        if (specialtis) {
            Specialization.setChoiceByValue(specialtis.split(','));
        }
        Services.removeActiveItems();
        if (doc_services) {
            Services.setChoiceByValue(doc_services.split(','));
        }
    }


   
    function deletedoctor(doc_id, doc_name) {
        swal({
            title: "Are you sure?",
            text: "Do you want to delete Doctor Record ",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/doctors/deletedoctor.php',
                    type: 'POST',
                    data: {
                        'doc_id':doc_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            swal('success', "Doctor Record Deleted Successfully", 'success');
                            Getdoctors();
                            getdoc();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(doc_id);
                swal('success', ' Record Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                    $("#FormId")[0].reset();
                    
                });
            }
        });
    }


    function getdoc(){
        $.ajax({
            url: 'ajax/doctors/number.php',
            type: 'get',
            dataType: 'json',
            success: function(data) {
                $("#doc_num").val(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function GetOrgByIdsautoincrement(){
        var org_id = $('#organizations').val();
        $.ajax({
            url: 'ajax/doctors/GetOrgByIdsautoincrement.php',
            type: 'POST',
            data: { org_id: org_id },
            dataType: 'json',
            success: function(data) {
                console.log(data);
                var optionDataDI = ''; 
                var optionDataDP = '<option value=""> Select Deportment </option>';
                var optionDataSP = '';
                var optionDataSS = '';
                $.each(data, function(key, val) {
                    $("#doc_num").val(val.doc_registration_number);
                    
                    $.each(val.departmentName, function(key1, val1){
                        optionDataDP +='<option value="'+ val1.dept_id +'">'+ val1.departmentName +'</option>';  
                    })
                    
                    $.each(val.specialtisname, function(key1, val1){
                        optionDataSP +='<option value="'+ val1.specialtis_id +'">'+ val1.specialtisname +'</option>';  
                    })
                    
                    $.each(val.service_name, function(key1, val1){
                        optionDataSS +='<option value="'+ val1.service_id +'">'+ val1.service_name +'</option>';  
                    });
                });
                
                Specialization.destroy();
                Services.destroy();
                $("#departments").html(optionDataDP);
                $("#specialtis").html(optionDataSP);
                $("#doc_services").html(optionDataSS);
                console.log(optionDataSS);
                Specialization = new Choices('#specialtis', {
                    removeItemButton: true,
                }); 
                Services = new Choices('#doc_services', {
                    removeItemButton: true,
                }); 

            },
            error: function(err) {
                console.log(err);
            }
        });
    }

</script>
