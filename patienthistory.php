<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__));

$appointRegisterId = isset($_GET['appointRegisterId']) ? $_GET['appointRegisterId'] : '';
$orgId             = isset($_GET['orgId']) ? $_GET['orgId'] : '';

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$checkDoctor  = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// SA_FATAL_FIXED_B_543: include SA so $sql is defined for super-admin
if ($securityType === 'A' || $securityType === 'SA') {
    $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
} elseif ($securityType === 'U') {
    $sql = "SELECT d.doc_id, d.doctor_name
            FROM doctors d
            WHERE d.status = '1'
            AND (
                d.security_id = '$SessionUserId'
                OR d.doc_id IN (
                        SELECT r.doc_id 
                        FROM receptionnist r 
                        WHERE r.security_id = '$SessionUserId'
                )
            )
            ORDER BY d.doctor_name ASC";
}

$res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

$doctors = [];
while ($row = mysqli_fetch_assoc($res)) {
    $doctors[] = $row;
}

$appointData = [];
if (!empty($appointRegisterId) && !empty($orgId)) {
    $appointRegisterId = mysqli_real_escape_string($conn, $appointRegisterId);
    $orgId             = mysqli_real_escape_string($conn, $orgId);

    $allowedDoctorIds = array_column($doctors, 'doc_id');
    // FIX_B_061: when no allowed doctors are resolvable, do NOT block all
    // rows ("AND 0"). The outer AND org_id=... still enforces tenancy.
    $doctorCondition  = !empty($allowedDoctorIds) 
        ? "AND doctor_name IN ('" . implode("','", $allowedDoctorIds) . "')" 
        : ""; 

    // FIX_B_1903: doctor-scope filter
    $docScope_B1903 = currentDoctorScopeSql('doctor_name');
    $query = "SELECT appoint_register_id, patient_name, mobile_number, appoint_unicode
              FROM appointment_online
              WHERE appoint_register_id = '$appointRegisterId'
              AND org_id = '$orgId'
              $doctorCondition
              $docScope_B1903";

    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $appointData = mysqli_fetch_assoc($result);
    }
}
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

  #previewContainer:empty,
    #assignedPreviewContainer:empty {
        display: none !important;
    }
    
    .empty-preview {
        display: none;
    }

    textarea.form-control {
        min-height: 250px !important;
    }
    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: 38px;  
        /* padding: 10px; */
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    </style>    



<script>
    var isAppointDataPresent = <?= !empty($appointData) ? 'true' : 'false' ?>;
    var orgIdFromUrl = "<?= $orgId ?>";
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (orgIdFromUrl) {
            const orgDropdown = document.getElementById("organizations");
            if (orgDropdown) {
                orgDropdown.value = orgIdFromUrl;
            }
        }
    });
</script>

    <div class="main-content">
        <section class="section">
            <ul class="breadcrumb breadcrumb-style ">
                <li class="breadcrumb-item">
                    <h4 class="page-title m-b-0">Patient History</h4>
                </li>
                <li class="breadcrumb-item">
                    <a href="dashboard.php">
                    </a>
                </li>
                <li class="breadcrumb-item active">Services</li>
                <li class="breadcrumb-item active">Patient History</li>
            </ul>

            <div class="card">
                <div class="card-header">
                    <h4>Patient History Reports</h4>
                </div>
                <div class="card-body">
                    <form id="patientHistoryForm">
                        <?php 
                            $SessionUserId = $_SESSION['security_id'] ?? '';
                            $SessionRoleId = $_SESSION['role_id'] ?? '';
                            $SessionOrgId = $_SESSION['org_id'] ?? '';

                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                        ?>
                            <div class="row mb-lg-5 mb-sm-3">
                                <div class="form-group col-lg-4 col-sm-12">
                                    <label>Organization <span class="text-danger">*</span></label>
                                    <select class="form-control form-select organizations" name="organizations" id="organizations" onchange="fetchpatientdetails()">
                                        <option value="">Select Organization</option>
                                        <?php
                                        $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                        while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                                        ?>
                                            <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } else{ ?>
                            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                        <?php } ?>

                        <input type="hidden" name="history_id" id="history_id" value=""/>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-sm-12">
                                        <label>Patient Name <span class="text-danger">*</span></label>
                                        <?php if (!empty($appointData)): ?>
                                            <input type="text" class="form-control" name="patient_name" value="<?= htmlspecialchars($appointData['patient_name'] ?? '') ?>" readonly />
                                        <?php else: ?>
                                            <select class="form-control form-select patient_name" name="patient_name" id="patient_name">
                                                <option value="" selected disabled>Select Patient Name</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group col-lg-6 col-sm-12">
                                        <label>Mobile <span class="text-danger">*</span></label>
                                        <?php if (!empty($appointData)): ?>
                                            <input type="text" class="form-control" name="mobile_number" value="<?= htmlspecialchars($appointData['mobile_number'] ?? '') ?>" readonly />
                                        <?php else: ?>
                                            <select class="form-control form-select mobile_number" name="mobile_number" id="mobile_number">
                                                <option value="" selected disabled>Select Mobile Number</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="form-group col-lg-6 col-sm-12">
                                        <label>Patient ID <span class="text-danger">*</span></label>
                                        <?php if (!empty($appointData)): ?>
                                            <input type="text" class="form-control" name="appoint_unicode" value="<?= htmlspecialchars($appointData['appoint_unicode'] ?? '') ?>" readonly />
                                        <?php else: ?>
                                            <select class="form-control form-select appoint_unicode" name="appoint_unicode" id="appoint_unicode">
                                                <option value="" selected disabled>Select Patient ID</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group col-lg-6 col-sm-12">
                                        <label>Appointment ID <span class="text-danger">*</span></label>
                                        <?php if (!empty($appointRegisterId)): ?>
                                            <input type="text" class="form-control" name="appoint_register_id" value="<?= htmlspecialchars($appointRegisterId) ?>" readonly />
                                        <?php else: ?>
                                            <select class="form-control form-select" name="appoint_register_id" id="appoint_register_id">
                                                <option value="" selected disabled>Select Appointment ID</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <!-- <div class="form-group col-lg-6 col-sm-12">
                                        <label>Tests Performed at <span class="text-danger">*</span></label>
                                        <select class="form-control" name="test_performed_at" id="test_performed_at" onchange="toggleTestField()">
                                            <option value="">Select</option>
                                            <option value="Within The Hospital">Within The Hospital</option>
                                            <option value="Outside Of The Hospital">Outside Of The Hospital</option>
                                        </select>
                                    </div> -->

                                    <div class="form-group col-lg-6 col-sm-12">
                                        <label>Upload File(s)</label>
                                        <input type="file" class="form-control" name="file_upload[]" id="file_upload" accept="image/*, .pdf, .doc, .docx, .txt, .jpg, .jpeg, .png" multiple>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Observations</label>
                                    <textarea class="form-control" name="observations" id="observations" rows="8"></textarea>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>New Uploads</h4>
                </div>
                <div class="card-body">
                    <div id="previewContainer" class="row g-3"></div>
                    <p>Total Files: <span id="fileCount">0</span></p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>Assigned / Saved Files</h4>
                </div>
                <div class="card-body">
                    <div id="assignedPreviewContainer" class="row g-3"></div>
                    <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))): ?>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" id="assignedUploadButton" onclick="savePatientTestHistory(isAppointDataPresent)">Upload</button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>In Patient Test Reports List</h4>
                </div>
                <div class="card-body" id="showSchemeData"></div>
            </div>
        </section>
    </div>

<div class="modal fade" id="prescriptionModal" tabindex="-1" aria-labelledby="prescriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="prescriptionModalLabel">Prescription Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="prescriptionModalBody">
        <p>Loading...</p>
      </div>
    </div>
  </div>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
    $(document).ready(function(){
    fetchpatientdetails();

    $('#patient_name, #mobile_number, #appoint_unicode, #appoint_register_id').select2();
    getOPTestReportsData();
    getTestNames(); 

    // Main form toggle
    $('#file_type').on('change', function() {
        toggleTestName($(this));
    });

    // Handle patient info changes
    $('#patient_name, #mobile_number, #appoint_unicode, #appoint_register_id').on('change', function() {
        $('#viewPatientTimeline').html('');
        const fieldName = $(this).attr('id');
        const fieldValue = $(this).val();
        patientinfo(fieldName, fieldValue);
    });
});

function toggleTestName($element) {
    const type = $element.val();
    // Main form
    const $mainTestName = $('#test_name');
    if (type === 'Prescription') {
        $mainTestName.prop('disabled', true).val('');
    } else {
        $mainTestName.prop('disabled', false);
    }

    // Dynamic previews
    $element.closest('.file-preview-container').find('.preview_file_type').each(function(){
        const selectedType = $(this).val();
        const $testInput = $(this).siblings('.preview_test_name');
        const $testLabel = $(this).siblings('label:contains("Test Name")');

        if (selectedType === 'Prescription') {
            $testInput.hide().val('');
            $testLabel.hide();
        } else {
            $testInput.show();
            $testLabel.show();
        }
    });
}

    function toggleTestField() {
        const selection = document.getElementById("test_performed_at").value;
        const dropdownDiv = document.getElementById("test_dropdown_div");
        const textDiv = document.getElementById("test_text_div");

        if (selection === "Within The Hospital") {
        dropdownDiv.style.display = "block";
        textDiv.style.display = "none";
        getTestNames();
        } else if (selection === "Outside Of The Hospital") {
        dropdownDiv.style.display = "none";
        textDiv.style.display = "block";
        } else {
        dropdownDiv.style.display = "none";
        textDiv.style.display = "none";
        }
    }



    function getTestNames() {
  $.ajax({
    url: 'ajax/optestreports/getoptestnames.php',
    method: 'GET',
    dataType: 'json',
    success: function(response) {
      var $datalist = $('#test_list');
      $datalist.empty();
      testNameToId = {};

      if (!Array.isArray(response)) {
        console.warn('getoptestnames returned non-array:', response);
        return;
      }

      response.forEach(function(item) {
        if (!item || item.error) return;
        var name = (item.test_name || '').toString().trim();
        var id   = (item.test_id === undefined || item.test_id === null) ? '' : item.test_id;
        if (!name) return;

        // normalize for matching
        var key = name.toLowerCase();

        // avoid duplicates in datalist
        if (!testNameToId.hasOwnProperty(key)) {
          testNameToId[key] = id;
          // create <option value="...">
          var $opt = $('<option>').attr('value', name);
          $datalist.append($opt);
        }
      });
    },
    error: function(xhr, status, err) {
      console.error('Failed to fetch test names:', status, err);
      // optional: show user-friendly message
      // alert('Failed to fetch test names. Check console for details.');
    }
  });
}


function getOPTestReportsData() {
    $.ajax({
        url: "ajax/optestreports/gettestsreports.php",
        type: "GET",
        success: function (response) {
            $("#showSchemeData").html(response);

           if ($.fn.DataTable.isDataTable("#tableExport1")) {
                $("#tableExport1").DataTable().clear().destroy();
            }

            $("#tableExport1").DataTable({
                dom: "lBrftip",
                buttons: ["copy", "excel", "csv", "pdf", "print"]
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching reports:", error);
            $("#showSchemeData").html(
                '<p class="text-danger text-center">Failed to load reports.</p>'
            );
        }
    });
}


function getAppointIDFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get('appointRegisterId');
}

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
                console.log(response);
                if (response.success) {
                    updateSelect2('#patient_name', response.data.patient_name, response.data.appoint_id);
                    updateSelect2('#mobile_number', response.data.mobile_number, response.data.appoint_id);
                    updateSelect2('#appoint_unicode', response.data.appoint_unicode, response.data.appoint_unicode);
                    updateSelect2('#appoint_register_id', response.data.appoint_register_id, response.data.appoint_register_id);
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
        } else {
            $select.val(id).trigger('change.select2');
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
                    console.log(data);

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

    function getnormalrange(test) {
    $.ajax({
        url: 'ajax/optestreports/gettestsrange.php',
        method: 'POST',
        data: { test_id: test },
        dataType: 'json',
        success: function(response) {
            console.log(response);                   
            if (response.normal_range) {
                $('#normal_range').val(response.normal_range).prop('disabled', true);  // Set and disable
            } else {
                console.error(response.error);
            }
        },
        error: function(error) {
            console.log(error);  
            alert("Failed to fetch normal range.");              
        }
    });
}
   function savePatientTestHistory(isAppointDataPresent) {
    const assignedFiles = document.getElementById('assignedPreviewContainer').children;

    if (assignedFiles.length === 0) {
        swal('', 'No files assigned for upload.', 'warning');
        return;
    }

    // Fetch mandatory form fields
    var appoint_unicode = isAppointDataPresent 
        ? document.getElementsByName("appoint_unicode")[0].value 
        : document.getElementById("appoint_unicode").value;
    var appoint_register_id = isAppointDataPresent 
        ? document.getElementsByName("appoint_register_id")[0].value 
        : document.getElementById("appoint_register_id").value;
    var org_id = isAppointDataPresent 
        ? document.getElementsByName("organizations")[0].value 
        : document.getElementById("organizations").value;
    // var test_performed_at = $("#test_performed_at").val();
    var observations = $("#observations").val();

    if (!appoint_unicode || !appoint_register_id || !org_id) {
        swal('', 'Please fill out all mandatory fields.', 'warning');
        return;
    }

    const formData = new FormData();

    Array.from(assignedFiles).forEach((colDiv, index) => {
        // Use the stored File object directly
        const fileObj = colDiv.fileObject;
        if (!fileObj) {
            throw swal('', `File not found for assigned card ${index + 1}`, 'warning');
        }

        formData.append('file_upload[]', fileObj);

        // Dynamic fields
        const type = colDiv.querySelector('.preview_file_type').value;
        const testNameInput = colDiv.querySelector('.preview_test_name');
        const testName = type === 'Test' && testNameInput ? testNameInput.value.trim() : '';
        const testDate = colDiv.querySelector('.preview_test_date').value;

        if (!type || (type === "Test" && !testName) || !testDate) {
            throw swal('', `Please fill Type, Test Name, and Uploaded Date for file ${fileObj.name}`, 'warning');
        }

        formData.append('file_type[]', type);
        formData.append('test_name[]', testName);
        formData.append('test_date[]', testDate);
    });

    // Append main form fields
    formData.append('appoint_unicode', appoint_unicode);
    formData.append('appoint_register_id', appoint_register_id);
    formData.append('org_id', org_id);
    // formData.append('test_performed_at', test_performed_at);
    formData.append('observations', observations);

    $.ajax({
        url: 'ajax/optestreports/saveandupdatetests.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response == 1) {
                swal('', 'Reports Added Successfully', 'success').then(() => location.reload());
            } else if (response == 2) {
                swal('', 'Reports Updated Successfully', 'success').then(() => location.reload());
            } else {
                swal('', 'Error occurred. Please try again', 'error');
            }
        },
        error: function(xhr, status, err) {
            console.error("AJAX error:", status, err);
            swal('Error', 'Something went wrong during upload.', 'error');
        }
    });
}




// function editTestReports(
//     id,
//     orgId,
//     patientId,
//     applicationId,
//     testPerformedAt,
//     testName,
//     testDate,
//     result,
//     normalRange,
//     observations
// ) {
//     window.scrollTo(0, 0);

//     $("#history_id").val(id);
//     $("#organizations").val(orgId);
//     alert(applicationId);
//     patientinfo("appoint_register_id", applicationId);
    
//     $("#test_performed_at").val(testPerformedAt);
//     $("#test_date").val(testDate);
//     $("#tests").val(testName).trigger("change");
//     $("#test_result").val(result);
//     $("#normal_range").val(normalRange);
//     $("#observations").val(observations);

   
// }



    function viewprescription(ids){
        $('#prescriptionModalBody').html('<p>Loading...</p>');
        var organizations = $('#organizations').val(); 

        $.ajax({
            url: 'ajax/Allpatientreports/getprescriptions.php',
            type: 'POST',
            data: { ids: ids, org_id: organizations },
            success: function (response) {
                $('#prescriptionModalBody').html(response);
            },
            error: function () {
                $('#prescriptionModalBody').html('<p class="text-danger">Failed to fetch prescription details.</p>');
            }
        });

        $('#prescriptionModal').modal('show');
    };

   

document.getElementById('file_upload').addEventListener('change', function(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('previewContainer');
    const assignedContainer = document.getElementById('assignedPreviewContainer');
    const fileCountDisplay = document.getElementById('fileCount');
    const newUploadsCard = previewContainer.closest('.card');
    const assignedCard = assignedContainer.closest('.card');

    // Only proceed if files are selected
    if (files.length === 0) {
        previewContainer.innerHTML = '';
        fileCountDisplay.textContent = '0';
        
        // Hide the cards if they're empty
        if (previewContainer.children.length === 0) {
            newUploadsCard.style.display = 'none';
        }
        if (assignedContainer.children.length === 0) {
            assignedCard.style.display = 'none';
        }
        
        return;
    }

    // Show the new uploads card
    newUploadsCard.style.display = 'block';
    previewContainer.innerHTML = '';
    fileCountDisplay.textContent = files.length;

    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        const fileType = file.type;

        // Outer column wrapper
        const colDiv = document.createElement('div');
        colDiv.className = 'col-6';
        colDiv.isAssigned = false;

        // STORE the File object directly on colDiv
        colDiv.fileObject = file; 

        const filePreview = document.createElement('div');
        Object.assign(filePreview.style, {
            width: '100%',
            padding: '10px',
            border: '1px solid #ccc',
            borderRadius: '10px',
            textAlign: 'left',
            backgroundColor: '#f9f9f9',
            marginBottom: '10px'
        });

        // File label
        const label = document.createElement('p');
        label.textContent = `File ${index + 1}: ${file.name}`;
        label.style.fontSize = '12px';
        label.style.fontWeight = 'bold';
        filePreview.appendChild(label);

        reader.onload = function(e) {
            // --- Preview logic (image/pdf/text) ---
            if (fileType.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.marginBottom = '5px';
                filePreview.appendChild(img);
            } else if (fileType === 'application/pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = e.target.result;
                iframe.style.width = '100%';
                iframe.style.height = '250px';
                iframe.style.border = 'none';
                filePreview.appendChild(iframe);
            } else if (fileType.startsWith('text/')) {
                const pre = document.createElement('pre');
                pre.textContent = e.target.result;
                pre.style.maxHeight = '200px';
                pre.style.overflowY = 'auto';
                pre.style.textAlign = 'left';
                pre.style.backgroundColor = '#f4f4f4';
                pre.style.padding = '5px';
                pre.style.fontSize = '12px';
                filePreview.appendChild(pre);
            } else {
                const info = document.createElement('p');
                info.innerHTML = `No preview available<br/>Name: ${file.name}`;
                filePreview.appendChild(info);
            }

            // --- Type Dropdown ---
            const typeLabel = document.createElement('label');
            typeLabel.textContent = 'Type';
            typeLabel.style.fontWeight = 'bold';
            typeLabel.style.display = 'block';
            typeLabel.style.marginTop = '5px';
            filePreview.appendChild(typeLabel);

            const typeSelect = document.createElement('select');
            typeSelect.className = 'form-control form-select preview_file_type';
            typeSelect.innerHTML = `
                <option value="">Select Type</option>
                <option value="Test">Test</option>
                <option value="Prescription">Prescription</option>
            `;
            filePreview.appendChild(typeSelect);

            // --- Test Name with datalist ---
            const testLabel = document.createElement('label');
            testLabel.textContent = 'Test Name';
            testLabel.style.fontWeight = 'bold';
            testLabel.style.display = 'block';
            testLabel.style.marginTop = '5px';
            filePreview.appendChild(testLabel);

            const testInput = document.createElement('input');
            testInput.type = 'text';
            testInput.className = 'form-control preview_test_name';
            testInput.setAttribute('list', `test_list_${index}`);
            testInput.placeholder = 'Enter Test Name';
            filePreview.appendChild(testInput);

            const dataList = document.createElement('datalist');
            dataList.id = `test_list_${index}`;
            Object.keys(testNameToId).forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                dataList.appendChild(option);
            });
            filePreview.appendChild(dataList);

            // --- Uploaded Date ---
            const dateLabel = document.createElement('label');
            dateLabel.textContent = 'Test Date';
            dateLabel.style.fontWeight = 'bold';
            dateLabel.style.display = 'block';
            dateLabel.style.marginTop = '5px';
            filePreview.appendChild(dateLabel);

            const dateInput = document.createElement('input');
            dateInput.type = 'date';
            dateInput.className = 'form-control preview_test_date';
            filePreview.appendChild(dateInput);

            // --- Hide Test Name if Prescription selected ---
            typeSelect.addEventListener('change', function() {
                if (this.value === 'Prescription') {
                    testInput.style.display = 'none';
                    testLabel.style.display = 'none';
                    testInput.value = '';
                } else {
                    testInput.style.display = 'block';
                    testLabel.style.display = 'block';
                }
                checkAndAssign();
            });

            testInput.addEventListener('input', checkAndAssign);
            dateInput.addEventListener('change', checkAndAssign);

            function checkAndAssign() {
                const typeVal = typeSelect.value;
                const testVal = testInput.value.trim();
                const dateVal = dateInput.value;
                let filled = false;

                if (typeVal === 'Prescription') filled = typeVal && dateVal;
                else filled = typeVal && testVal && dateVal;

                if (filled && !colDiv.isAssigned) {
                    // Show assigned card if it was hidden
                    assignedCard.style.display = 'block';
                    
                    assignedContainer.appendChild(colDiv);
                    colDiv.isAssigned = true;
                    
                    // Show toast notification
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'File moved to assigned preview',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        };

        if (fileType.startsWith('text/')) reader.readAsText(file);
        else reader.readAsDataURL(file);

        colDiv.appendChild(filePreview);
        previewContainer.appendChild(colDiv);
    });
});

// Initialize by hiding the cards if they're empty
document.addEventListener('DOMContentLoaded', function() {
    const previewContainer = document.getElementById('previewContainer');
    const assignedContainer = document.getElementById('assignedPreviewContainer');
    const newUploadsCard = previewContainer.closest('.card');
    const assignedCard = assignedContainer.closest('.card');
    
    if (previewContainer.children.length === 0) {
        newUploadsCard.style.display = 'none';
    }
    if (assignedContainer.children.length === 0) {
        assignedCard.style.display = 'none';
    }
});






</script>