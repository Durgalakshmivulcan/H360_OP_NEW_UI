<?php
    require_once("ajax/header.php");
// FIX_B_1820 (scope 2 RBAC): per-action view gate; SA bypassed by userCan().
requireCan('view', basename(__FILE__));


    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $patientNamesArray = [];

    if($SessionUserId == "1"){
        $getPrescriptionData=mysqli_query($conn, "SELECT DISTINCT * FROM prescripition WHERE status='1' ORDER BY prescription_id DESC") or die(mysqli_error($conn));
    }else{
    $getPrescriptionData=mysqli_query($conn, "SELECT DISTINCT * FROM prescripition WHERE status='1' AND org_id='$SessionOrgId' ORDER BY create_date_time DESC") or die(mysqli_error($conn));

    }
    while($resPrescriptionData=mysqli_fetch_object($getPrescriptionData)){
        $patient=$resPrescriptionData->patient_uid;
        $apoint_unic=$resPrescriptionData->appoint_register_id;
        $org_id1=$resPrescriptionData->org_id;


        if($SessionUserId == "1"){
            $getTest = mysqli_query($conn, "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_unicode='$patient' AND appoint_register_id='$apoint_unic' ORDER BY appoint_id DESC") or die(mysqli_error($conn));
        } else{
            $getTest = mysqli_query($conn, "SELECT DISTINCT(patient_name) FROM appointment_online WHERE appoint_status='1' AND appoint_unicode='$patient' AND appoint_register_id='$apoint_unic' AND org_id='$org_id1'  ORDER BY appoint_id DESC") or die(mysqli_error($conn));
        }
        
        while($row=mysqli_fetch_object($getTest)) {
            $patientNamesArray[] = $row->patient_name;
        }
    }
    $patientNamesArray = array_unique($patientNamesArray);
?>

<style>

    /* .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px; 
        /* padding: 6px 12px; */
        /* border: 1px solid #ced4da;
        border-radius: 0.375rem;
    } */ 

</style>

    <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">

            <ul class="breadcrumb breadcrumb-style ">
                <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Tests Bill</h4>
                </li>
                <li class="breadcrumb-item">
                    <a href="dashboard.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </a>
                </li>
                <li class="breadcrumb-item">Billing</li>
                <li class="breadcrumb-item">Test Billing</li>
            </ul>

            <div class="card">
                <div class="card-header">
                    <h4> Patient Search</h4>
                </div>
                <div class="col-12 col-md-12 col-lg-12">                
                    <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">
                        <input type="hidden" name="patient_code" id="patient_code" value="" >
                        <div class="card-body">
                        <?php
                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                        ?>

                            <div class="row">
                                <div class="row mb-lg-5 mb-sm-3">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="fetchpatientdetails()">
                                            <option value="">Select Organization</option>
                                            <?php
                                            $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                            while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                                            ?>
                                                <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        <?php
                            } else{
                        ?>
                            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                        <?php
                            }
                        ?>
                           <div class="row">
                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="Patient Name"><i class="bi bi-person-fill"></i> Patient Name <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select patientName"  name="patientName" id="patientName"  placeholder="Select Patient Name" >
                                <option value="" >Select Patient Name</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="mobileNumber"><i class="bi bi-telephone-fill"></i> Mobile <span class="text-danger">*</span> </label>
                            <select type="tel" class="form-control form-select mobileNumber"  name="mobileNumber" id="mobileNumber" placeholder="Select Mobile Number" >
                                <option value="" >Select Mobile Number</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-person-vcard"></i> Patient ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appointUnicode"  name="appointUnicode" id="appointUnicode" >
                                <option value="" >Select Patient ID</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-postcard-fill"></i> Appointment ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appointRegisterId"  name="appointRegisterId" id="appointRegisterId" >
                                <option value="" >Select Appointment ID</option>
                            </select>
                            <input type="hidden" name="bill_id" id="bill_id" value="" />
                        </div>
                    </div>
                            
                            <div class="row">
                                <div class="card-footer text-center">
                                    
                                <button type="button" class="btn btn-primary" name="saveData" id="saveData" value="" target="_blank" onclick="myFunction()">Search</button>
                                </div>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>

        </section>
        <div class="" >
            <div class="" id="showData"></div>
            <!-- <div class="" id="showDataPB"></div> -->
        </div>

        

    </div>



<?php require_once("ajax/footer.php") ?>
    <!-- <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script> -->
<script>

    function printDiv() {
        var divContents = document.getElementById("showData").innerHTML;
        var a = window.open('', '','width: max-content','height: max-content;');
        a.document.write('<html>');
        a.document.write('<body >');
        a.document.write(divContents);
        a.document.write('</body></html>');
        a.document.close();
        a.print();
    }

    $("document").ready(function() {
        
        fetchpatientdetails();

        $('#patientName').select2();
        $('#mobileNumber').select2();
        $('#appointUnicode').select2();
        $('#appointRegisterId').select2();
        
    });

    $(document).on('change', '#patientName, #mobileNumber, #appointUnicode, #appointRegisterId', function() {
        const fieldName = $(this).attr('id');
        const fieldValue = $(this).val();
        // alert(fieldValue);

        patientinfo(fieldName, fieldValue);
    });

    

    function patientinfo(fieldName, fieldValue){
        const organization_id = $('#organizations').val(); 
        
        $.ajax({
            url: 'ajax/Allpatientreports/patientinformation.php',
            type: 'POST',
            data: {
                organization_id,
                fieldName,
                fieldValue
            },
            dataType: 'json',
            success: function(response) {
                // console.log("Response:", response);
                
                if (response.success) {
                    updateSelect2('#patientName', response.data.patientName, response.data.appoint_id);
                    updateSelect2('#mobileNumber', response.data.mobileNumber, response.data.appoint_id);
                    updateSelect2('#appointUnicode', response.data.appointUnicode, response.data.appoint_id);
                    updateSelect2('#appointRegisterId', response.data.appointRegisterId, response.data.appoint_id);
                } else {
                    console.log("No matching patient found.");
                }
            },
            error: function(error) {
                console.log("Error fetching patient data.", error);
            }
        });
    }

    function updateSelect2(selector, value, id) {
        const $select = $(selector);
        if ($select.find("option[value='" + id + "']").length === 0) {
            const newOption = new Option(value, id, true, true);
            $select.append(newOption).trigger('change.select2');
            console.log('1');
        } else {
            
            $select.val(id).trigger('change.select2');
            console.log('2');
        }
    } 

    function fetchpatientdetails (){
        var orgId = $("#organizations").val();

        if(orgId){
            $.ajax({
                url: "ajax/Allpatientreports/fetchpatientdetails.php",
                method: "GET",
                dataType: "json",
                data: { org_id: orgId },
                success: function (data) {

                    $('#patientName').empty().append('<option value="">Select Patient Name</option>');
                    $('#mobileNumber').empty().append('<option value="">Select Mobile Number</option>');
                    $('#appointUnicode').empty().append('<option value="">Select Patient ID</option>');
                    $('#appointRegisterId').empty().append('<option value="">Select Appointment ID</option>');

                    const mobileMap = {};
                    const unicodeMap = {};
                    const registerMap = {};

                    data.mobile_numbers.forEach(item => {
                        mobileMap[item.appoint_id] = item.mobile_number;
                    });

                    data.patient_ids.forEach(item => {
                        unicodeMap[item.appoint_id] = item.appoint_unicode;
                    });

                    data.appointment_ids.forEach(item => {
                        registerMap[item.appoint_id] = item.appoint_register_id;
                    });

                    data.patient_names.forEach(item => {
                        const id = item.appoint_id;

                        $('#patientName').append(`<option value="${id}">${item.patient_name}</option>`);

                        const mobile = mobileMap[id] || '';
                        if (mobile) {
                            $('#mobileNumber').append(`<option value="${id}">${mobile}</option>`);
                        }

                        const unicode = unicodeMap[id] || '';
                        if (unicode) {
                            $('#appointUnicode').append(`<option value="${id}">${unicode}</option>`);
                        }

                        const registerId = registerMap[id] || '';
                        if (registerId) {
                            $('#appointRegisterId').append(`<option value="${id}">${registerId}</option>`);
                        }
                    });

                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    };

    function myFunction() {
        var appoint_register_id = $('#appointRegisterId').val();
        var patient_uid = $('#appointUnicode').val();
        var org_id = $("#organizations").val();

        if (!appoint_register_id || !patient_uid) {
            swal('', 'Please select Patient Name and Appointment ID first.', 'warning');
            return;
        }

        $.ajax({
            url: 'ajax/billing/viewrepots.php',
            type: 'POST',
            data: {
                appoint_register_id: appoint_register_id,
                patient_uid: patient_uid,
                org_id: org_id
            },
            success: function (data) {

                if (data) {
                    $("#showData").html(data);

                } else {
                    $("#showData").html("<p class='text-warning'>No tests found.</p>");

                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
                $("#showData").html("<p class='text-danger'>Error fetching data.</p>");
            }
        });
    }

</script>
