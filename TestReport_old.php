<?php
require_once("ajax/header.php");

?>

<style>

  @media print {
    .custom-print-style {
        width: 100% ;
    }
    }

    .print-div {
      width: 148mm; /* A4 width is 210mm, so half of it is 105mm */
      height: 210mm; /* A4 height is 297mm */
      border: 1px solid black;
      padding: 10px;
      box-sizing: border-box;
    }
</style>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Main Content -->
<div class="main-content">
    <section class="section">                
    <ul class="breadcrumb breadcrumb-style ">
        <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Test Reports</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
                <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">Test Reports</li>
        </ul>     
        <div class="card">
            <div class="card-header">
            <h4> Patient Test Reports</h4>
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
                            <select type="text" class="form-control form-select patient_name"  name="patient_name" id="patient_name"  placeholder="Select Patient Name" >
                                <option value="" >Select Patient Name</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="mobileNumber"><i class="bi bi-telephone-fill"></i> Mobile <span class="text-danger">*</span> </label>
                            <select type="tel" class="form-control form-select mobile_number"  name="mobile_number" id="mobile_number" placeholder="Select Mobile Number" >
                                <option value="" >Select Mobile Number</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-person-vcard"></i> Patient ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appoint_unicode"  name="appoint_unicode" id="appoint_unicode" >
                                <option value="" >Select Patient ID</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-3 col-sm-12">
                            <label for="appoint_unicode"><i class="bi bi-postcard-fill"></i> Appointment ID <span class="text-danger">*</span> </label>
                            <select type="text" class="form-control form-select appoint_register_id"  name="appoint_register_id" id="appoint_register_id" >
                                <option value="" >Select Appointment ID</option>
                            </select>
                        </div>
                    </div>
                <div class="row">
                    <div class="card-footer text-center">
                    <button type="button" class="btn btn-primary" name="saveData" id="saveData" value="" onclick="myFunction()">Submit</button>
                    </div>
                </div>
                </div>
            </form> 
            </div>
        </div>
    </section>
    <div class="card">
        <div class="card-header">
            <h4> Patient Test Reports</h4>
        </div>

        <div class="card-body" id="showData">
            <div class="col-12 col-md-12 table-responsive">
                

            </div>
        </div>   

    </div>
</div>





<?php require_once("ajax/footer.php") ?>


<script>
$("document").ready(function() {
    // GetNameByNumber();
    // GetPatientNameAndNumberByID();
    // GetNameAndNumberAndIDByID();

    // $('.patient_name').select2();
    // $('.mobile_number').select2();
    // $('.appoint_unicode').select2();
    // $('.appoint_register_id').select2();
    // $('.Organization_id').select2();


    // $('#patient_name').select2();
    // $('#patient_name').on('change', function() {
    //   $('#mobile_number').val(null).trigger('change');
    //   $('#appoint_unicode').val(null).trigger('change');
    //   $('#appoint_register_id').val(null).trigger('change');
    //   $('#Organization_id').val(null).trigger('change');
    // });
    fetchpatientdetails();

            $('#patient_name').select2();
            $('#mobile_number').select2();
            $('#appoint_unicode').select2();
            $('#appoint_register_id').select2();

});

$(document).on('change', '#patient_name, #mobile_number, #appoint_unicode, #appoint_register_id', function() {
            const fieldName = $(this).attr('id');
            const fieldValue = $(this).val();
            patientinfo(fieldName, fieldValue);
        });

        

        function patientinfo(fieldName, fieldValue){
            const organization_id = $('#organizations').val(); 

            $.ajax({
                url: 'ajax/Allpatientreports/patientinfo.php',
                type: 'POST',
                data: {
                    organization_id,
                    fieldName,
                    fieldValue
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        updateSelect2('#patient_name', response.data.patient_name, response.data.appoint_id);
                        updateSelect2('#mobile_number', response.data.mobile_number, response.data.appoint_id);
                        updateSelect2('#appoint_unicode', response.data.appoint_unicode, response.data.appoint_id);
                        updateSelect2('#appoint_register_id', response.data.appoint_register_id, response.data.appoint_id);
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

                    $('#patient_name').empty().append('<option value="">Select Patient Name</option>');
                    $('#mobile_number').empty().append('<option value="">Select Mobile Number</option>');
                    $('#appoint_unicode').empty().append('<option value="">Select Patient ID</option>');
                    $('#appoint_register_id').empty().append('<option value="">Select Appointment ID</option>');

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

                        $('#patient_name').append(`<option value="${id}">${item.patient_name}</option>`);

                        const mobile = mobileMap[id] || '';
                        if (mobile) {
                            $('#mobile_number').append(`<option value="${id}">${mobile}</option>`);
                        }

                        const unicode = unicodeMap[id] || '';
                        if (unicode) {
                            $('#appoint_unicode').append(`<option value="${id}">${unicode}</option>`);
                        }

                        const registerId = registerMap[id] || '';
                        if (registerId) {
                            $('#appoint_register_id').append(`<option value="${id}">${registerId}</option>`);
                        }
                    });

                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    };


// function GetNameByNumber() {
//     // alert();
//     var patient_name = $('#patient_name').val();

//     if (!patient_name) {
//         console.log("patient name are required.");
//         return;
//     }
    
//     $.ajax({
//         url: 'ajax/TestReports/getPatientName.php',
//         type: 'POST',
//         data: { patient_name: patient_name },
//         dataType: 'json',
//         success: function(data) {
//             var mobileNumberSelect = $("#mobile_number");
//             // var appointUnicodeSelect = $("#appoint_unicode");
//             // var appointRegisterIdSelect = $("#appoint_register_id");
//             // var OrganizationIdSelect = $("#Organization_id");

//             // Clear existing options
//             mobileNumberSelect.empty();
//             // appointUnicodeSelect.empty();
//             // appointRegisterIdSelect.empty();
//             // OrganizationIdSelect.empty();

//             $.each(data, function(_, val) {
//                 mobileNumberSelect.append($('<option>', {
//                     value: val.mobile_number,
//                     text: val.mobile_number
//                 }));

//                 // appointUnicodeSelect.append($('<option>', {
//                 //     value: val.appoint_unicode,
//                 //     text: val.appoint_unicode
//                 // }));

//                 // appointRegisterIdSelect.append($('<option>', {
//                 //     value: val.appoint_register_id,
//                 //     text: val.appoint_register_id
//                 // }));
//                 // OrganizationIdSelect.append($('<option>', {
//                 //     value: val.org_id, 
//                 //     text: val.org_name
//                 // }));
//             });

//             // Select the first option by default
//             mobileNumberSelect.prop('selectedIndex', 0);
//             // appointUnicodeSelect.prop('selectedIndex', 0);
//             // appointRegisterIdSelect.prop('selectedIndex', 0);
//             // OrganizationIdSelect.prop('selectedIndex', 0);
//             GetPatientNameAndNumberByID();
//         },
//         error: function(err) {
//             console.log(err);
//         }
//     });
// }




// function GetPatientNameAndNumberByID() {
//     var patient_name = $('#patient_name').val();
//     var patient_number = $('#mobile_number').val();

//     if (!patient_name || !patient_number) {
//         console.log("Both patient name and number are required.");
//         return;
//     }

//     $.ajax({
//         url: 'ajax/TestReports/getPatientNameAndNumberById.php',
//         type: 'POST',
//         data: {
//             patient_name: patient_name,
//             patient_number: patient_number
//         },
//         dataType: 'json',
//         success: function(data) {
//             var appointUnicodeSelect = $("#appoint_unicode");
//             // var appointRegisterIdSelect = $("#appoint_register_id");
//             // var OrganizationIdSelect = $("#Organization_id");

//             appointUnicodeSelect.empty();
//             // appointRegisterIdSelect.empty();
//             // OrganizationIdSelect.empty();

//             $.each(data, function(_, val) {
//                 appointUnicodeSelect.append($('<option>', {
//                     value: val.appoint_unicode,
//                     text: val.appoint_unicode
//                 }));

//                 // appointRegisterIdSelect.append($('<option>', {
//                 //     value: val.appoint_register_id,
//                 //     text: val.appoint_register_id
//                 // }));
//                 // OrganizationIdSelect.append($('<option>', {
//                 //     value: val.org_id, 
//                 //     text: val.org_name
//                 // }));
//             });

//             // Select the first option by default
//             appointUnicodeSelect.prop('selectedIndex', 0);
//             // appointRegisterIdSelect.prop('selectedIndex', 0);
//             // OrganizationIdSelect.prop('selectedIndex', 0);

//             GetNameAndNumberAndIDByID();
//         },
//         error: function(xhr, status, error) {
//             console.log("AJAX Error:", error);
//         }
//     });
// }







function myFunction() {
    var patient_name = $('#patient_name').val();
    var patient_number = $('#mobile_number').val();
    var appoint_unicode = $('#appoint_unicode').val();
    var appoint_register_id = $('#appoint_register_id').val();
    var org_id = $("#organizations").val();

      if (!appoint_register_id) {
            swal('', 'Submission is empty. Please fill out the mandatory fields.', 'warning');
            return;
        }

      $.ajax({
        url: 'ajax/TestReports/GetDataByAppointmentID.php',
        type: 'POST',
        data: {
          patient_name: patient_name,
          patient_number: patient_number,
          appoint_unicode: appoint_unicode,
          appoint_register_id: appoint_register_id,
          org_id: org_id
        },
        success: function (data) {
          // console.log(data);
          if (data) {
            $("#showData").html(data);
            // $("#FormId")[0].reset();
            // $('.patient_name').select2();
            // $('.mobile_number').select2();
            // $('.appoint_unicode').select2();
            // $('.appoint_register_id').select2();
            // $('.Organization_id').select2();


            // $('#patient_name').select2();
            // $('#patient_name').on('change', function() {
            // $('#mobile_number').val(null).trigger('change');
            // $('#appoint_unicode').val(null).trigger('change');
            // $('#appoint_register_id').val(null).trigger('change');
            // $('#Organization_id').val(null).trigger('change');
            // });

            $("#showData").show();
          }
        }
      });
    }

</script>