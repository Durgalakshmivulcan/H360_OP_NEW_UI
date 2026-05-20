<?php
// IDOR_FIXED B-577
require_once("../../config/functions.php");


  $SessionUserId = $_SESSION['security_id'];
  $SessionRoleId = $_SESSION['role_id'];
  $SessionOrgId = $_SESSION['org_id'];
        
  $id             = $_POST['appoint_register_id']; 
  $id2            = $_POST['patient_uid'];         
  $mobile_number  = mysqli_real_escape_string($conn, $_POST['mobile_number']);
  $patient_name   = mysqli_real_escape_string($conn, $_POST['patient_name']);
  $org_id         = $_POST['org_id'];

  if ($SessionUserId == "1") {
    $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND  appoint_id='$id2' AND org_id='$org_id'");
  } else {
    $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND  appoint_id='$id2' AND org_id='$org_id'");
  }

  $resapport = mysqli_fetch_object($getappoint);

  if ($resapport) {
      $bill_id = $resapport->bill_id;
      $org = $resapport->org_id;
      $bill_date = $resapport->bill_date;
      $appoint_id = $resapport->appoint_id; 

      if ($bill_date == '0000-00-00' || empty($bill_date)) {
        $current_date = date("Y-m-d");
        $updateQuery = "UPDATE appointment_online SET bill_date = '$current_date' WHERE appoint_id = '$appoint_id' AND org_id='$SessionOrgId'";
        mysqli_query($conn, $updateQuery);

        $bill_date = $current_date;
      }

      $bill_date_display = date("d/m/Y", strtotime($bill_date));
  } else {
      $bill_date_display = "N/A"; 
  }

    $getuid=mysqli_query($conn,"SELECT * FROM prescripition WHERE status='1' AND patient_uid='$resapport->appoint_unicode' AND org_id='$org_id'") or die(mysqli_error($conn));
    $resuid=mysqli_fetch_object($getuid);

    $getdoctime = mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE status='1' AND doctors_time_id='$resapport->doctor_name'");
    $restime=mysqli_fetch_object($getdoctime);

    $getdoc = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$restime->doctorName_registrationNumber'");
    $resdoc=mysqli_fetch_object($getdoc);

    $getservice = mysqli_query($conn, "SELECT * FROM services WHERE status='1' AND service_id='$resdoc->doctor_services'");
    $resservice=mysqli_fetch_object($getservice);

      
    $getSizes = mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='3' AND org_id='$org_id'");
    $resData = mysqli_fetch_object($getSizes);
    
    $top = "100px";
    $bottom = "0px";
    
    if (!empty($resData->top)) {
        $top = $resData->top;
    }
    
    if (!empty($resData->bottom)) {
        $bottom = $resData->bottom;
    }
    
    $getSingleSize = mysqli_query($conn, "SELECT w_size, h_size FROM pagessize WHERE status='1' AND size_id='$resData->sizes'");
    $resSingleData = mysqli_fetch_object($getSingleSize);
    
    $width = '21cm';
    $height = '29.7cm';
    
    if (!empty($resSingleData->w_size)) {
        $width = $resSingleData->w_size;
    }
    
    if (!empty($resSingleData->h_size)) {
        $height = $resSingleData->h_size;
    }


  $getOrgDetails = mysqli_query($conn, "SELECT address,organization_name FROM organization WHERE status='1' AND org_id='$resapport->org_id'");
  $resOrgDetails = mysqli_fetch_object($getOrgDetails);

  $getappoint = mysqli_query($conn, "
      SELECT * 
      FROM appointment_online 
      WHERE appoint_status='1' 
        AND appoint_id='$id2'
        AND org_id='$org_id'
  ");

  $resapport = mysqli_fetch_object($getappoint);
 
$query = "
    SELECT test_id, patient_uid, appoint_register_id
    FROM prescripition 
    WHERE patient_uid='$resapport->appoint_unicode'
      AND appoint_register_id='$resapport->appoint_register_id'
      AND status='1'
    ORDER BY prescription_id DESC 
    LIMIT 1
";

  $result = mysqli_query($conn, $query);

  if (!$result) {
      echo "<p class='text-danger'>SQL Error: " . mysqli_error($conn) . "</p>";
      exit;
  }

  $row = mysqli_fetch_assoc($result);

  $PatientID     = $row['patient_uid'] ?? '';
  $applicationID = $row['appoint_register_id'] ?? '';

?>

  <style>

    .select2-selection__choice {
      background-color: white !important;  
      color: black !important;             
      border: 1px solid #ccc;
      margin-top: 4px;
    }

    .select2-container--default .select2-selection--multiple {
      background-color: white;
      border: 1px solid #aaa;
      border-radius: 2px;
      cursor: text;
      height: 42px;
    }

    .btn-group,
    .btn-group-vertical {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }
      
  </style>

  <div class='card mt-3' style="min-height: 300px;">
    <div class='card-header'>
      <h4>Patient Tests</h4>
    </div>
      <div class='card-body'>
        <?php
          if ($row) {

            $tests = json_decode($row['test_id'], true);
            // echo $tests;

            if (json_last_error() === JSON_ERROR_NONE && !empty($tests)) {
                ?>

                <input type="hidden" id="appointUnicode" name="appointUnicode" value="<?= $PatientID?>">
                <input type="hidden" id="appointRegisterId" name="appointRegisterId" value="<?= $applicationID ?>">


                <div class="row">
                  <div class="form-group col-lg-8 col-sm-12">
                    <label for="tests">Select Tests <span class="text-danger">*</span></label>
                    <div class="input-group mt-2">
                      <span class="input-group-text">
                        <i class="bi bi-ui-checks fs-4"></i>
                      </span>
                      <select id="tests" name="tests[]" class="form-control" multiple>
                        <script>
                          var testsData = <?php echo json_encode($tests ?? []); ?>;
                        </script>
                        <?php
                        foreach ($tests as $test) {
                          $tid    = $test['test_id'] ?? '';
                          $tname  = $test['test_name'] ?? '';
                          $tprice = $test['doctor_price'] ?? 0;
                          $tstandard = $test['standard_price'] ?? 0;
                          if ($tname != '') {
                            ?>
                            <option value="<?= $tid ?>" data-name="<?= $tname ?>" data-price="<?= $tprice ?>" data-standard="<?= $tstandard ?>">
                              <?= $tname ?>
                            </option>
                            <?php
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <!-- Table for selected tests and prices -->
                    <div class="mt-3 table-responsive">
                      <table class="table table-bordered" id="selectedTestsTable" style="display:none;">
                        <thead>
                          <tr>
                            <th>Test Name</th>
                            <th>Price</th>
                          </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                   <script>
                      $(document).ready(function() {
                        function updateSelectedTestsTable() {
                          var $table = $('#selectedTestsTable');
                          var $tbody = $table.find('tbody');
                          $tbody.empty();
                          var any = false;

                          $('#tests option:selected').each(function() {
                            var name = $(this).data('name');
                            var doctorPrice = parseFloat($(this).data('price')) || 0;   
                            var standardPrice = parseFloat($(this).data('standard')) || 0; 
                            var priceDisplay = '';

                            if (standardPrice > 0) {
                              if (doctorPrice < standardPrice) {
                                priceDisplay = '<div><span style="text-decoration:line-through;color:red;">Rs ' 
                                              + standardPrice.toFixed(2) + '/-</span></div>';

                                var discountAmt = standardPrice - doctorPrice;
                                priceDisplay += '<div><span style="color:green;font-weight:bold;">Rs ' 
                                                + doctorPrice.toFixed(2) + '/-</span> ' +
                                                '<span style="color:#666;font-size:0.9em;">(Rs ' 
                                                + discountAmt.toFixed(2) + ' Off)</span></div>';
                              } else {
                                priceDisplay = '<div><span style="color:black;">Rs ' 
                                              + standardPrice.toFixed(2) + '/-</span></div>';
                              }
                            } else {
                              priceDisplay = '<div><span style="color:black;">Rs ' 
                                            + doctorPrice.toFixed(2) + '/-</span></div>';
                            }

                            $tbody.append('<tr><td>' + name + '</td><td>' + priceDisplay + '</td></tr>');
                            any = true;
                          });

                          if (any) {
                            $table.show();
                          } else {
                            $table.hide();
                          }
                        }

                        $('#tests').on('change', updateSelectedTestsTable);
                        updateSelectedTestsTable();
                      });

                    </script>
                  </div>

                  <div class="form-group col-lg-4 col-sm-12">
                    <label for="amount_method" class="amount_method">Payment Method <span class="text-danger">*</span></label>
                    <div class="input-group mt-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="bi bi-cash"></i>
                        </div>
                      </div>
                      <select class="form-control form-select" name="amount_method" id="amount_method">
                        <option value="">Select Payment Method</option>
                        <?php
                        $getPayment_method = mysqli_query(
                          $conn,
                          "SELECT payment_method_id, payment_method
                          FROM payment_method
                          WHERE status='1'
                          ORDER BY payment_method_id ASC"
                        ) or die(mysqli_error($conn));
                        while ($resPayment = mysqli_fetch_object($getPayment_method)) {
                          ?>
                          <option value="<?php echo $resPayment->payment_method; ?>">
                            <?php echo $resPayment->payment_method; ?>
                          </option>
                          <?php
                        }
                        ?>
                        <option value="Both (Cash + UPI)">Both (Cash + UPI)</option>
                      </select>
                    </div>
                  </div>

                  <!-- UPI Transaction Number (shown for UPI / Both) -->
                  <div class="form-group col-lg-4 col-sm-12" id="txnDetailsDiv" style="display:none;">
                    <label for="transaction_number">UPI Transaction Number</label>
                    <div class="input-group mt-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="bi bi-receipt"></i></div>
                      </div>
                      <input type="text" class="form-control" id="transaction_number" name="transaction_number" placeholder="Enter UPI transaction number">
                    </div>
                  </div>

                  <!-- UPI Amount (shown for UPI / Both) -->
                  <div class="form-group col-lg-4 col-sm-12" id="txnAmountDiv" style="display:none;">
                    <label for="transaction_amount">UPI Amount (&#8377;)</label>
                    <div class="input-group mt-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="bi bi-currency-rupee"></i></div>
                      </div>
                      <input type="number" class="form-control" id="transaction_amount" name="transaction_amount" placeholder="Enter UPI amount" min="0">
                    </div>
                  </div>

                  <!-- Cash Amount (shown only for Both) -->
                  <div class="form-group col-lg-4 col-sm-12" id="cashAmountDiv" style="display:none;">
                    <label for="cash_amount">Cash Amount (&#8377;)</label>
                    <div class="input-group mt-2">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="bi bi-cash"></i></div>
                      </div>
                      <input type="number" class="form-control" id="cash_amount" name="cash_amount" placeholder="Enter cash amount" min="0">
                    </div>
                  </div>
                </div>

                <div class='card-footer text-end'>
                  <button type="button" id="saveTests" class="btn btn-primary" onclick="saveTests()">Submit</button>
                </div>
                <?php
            } else {
                ?>
                  <p class='text-danger'><strong>No Tests found for this patient.</strong></p>
                <?php
            }
          } else {
            ?>
              <p class='text-danger'><strong>No Tests found for this patient.</strong></p>
            <?php
          }
        ?>
      </div>

  </div>

  <div class="col-12">
      <div class="card">
          <div class="card-header">
              <h4>Tests Bill List</h4>
          </div>
          <div id="tableExport_wrapper"></div>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  $(document).ready(function(){

    GetView();

    $('#tests').select2({
      width: '90%',
      placeholder: "Select tests",
      closeOnSelect: false,  
      allowClear: false,
      templateResult: function (data) {
        if (!data.id) {
            return data.text;
        }

        var isSelected = $('#tests').val() && $('#tests').val().includes(data.id);

        return $('<span><input type="checkbox" ' + (isSelected ? 'checked' : '') + ' style="margin-right:6px;"> ' + data.text + '</span>');
      },
      templateSelection: function (data) {
        return data.text;
      }
    });

    $('#tests').on('mouseenter', function () {
      if (!$(this).data('select2-open')) {
        $(this).select2('open');
        $(this).data('select2-open', true);
      }
    });

    $(document).on('mouseleave', '.select2-dropdown', function () {
      $('#tests').select2('close');
      $('#tests').removeData('select2-open');
    });

    $('#tests').on('change', function () {
      $('#tests').select2('close').select2('open'); 
    });

  });

  // Show/hide UPI and cash fields based on selected payment method
  $('#amount_method').on('change', function() {
    const val = $(this).val().toLowerCase();
    if (val === 'upi' || val === 'both (cash + upi)') {
      $('#txnDetailsDiv').show();
      $('#txnAmountDiv').show();
    } else {
      $('#txnDetailsDiv').hide();
      $('#txnAmountDiv').hide();
      $('#transaction_number').val('');
      $('#transaction_amount').val('');
    }
    if (val === 'both (cash + upi)') {
      $('#cashAmountDiv').show();
    } else {
      $('#cashAmountDiv').hide();
      $('#cash_amount').val('');
    }
  });

  function saveTests() {
    let testsData = [];
    let doctorTotal = 0;

    $("#tests option:selected").each(function() {
        let id = $(this).val();
        let name = $(this).data("name");
        let doctor_price = parseFloat($(this).data("price")) || 0;
        let standard_price = parseFloat($(this).data("standard")) || 0; 

        testsData.push({ 
            test_id: id, 
            test_name: name, 
            instruction: '', 
            doctor_price: doctor_price, 
            standard_price: standard_price 
        });

        doctorTotal += doctor_price;
    });

    let paymentMethod = $("#amount_method").val();

    if (testsData.length === 0) {
        Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Please select at least one test',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
          });
        return;
    }

    if (paymentMethod === "") {
        Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Please select payment method',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
          });
        return;
    }

    let standardTotal = testsData.reduce((sum, test) => {
        return sum + (parseFloat(test.standard_price) || 0);
    }, 0);

    let transactionNumber = $("#transaction_number").val();
    let transactionAmount = $("#transaction_amount").val();
    let cashAmount        = $("#cash_amount").val();

    $.ajax({
      url: "ajax/billing/saveTest.php",
      type: "POST",
      data: {
        action:"2",
          patient_id: "<?= $PatientID ?>",
          appointment_id: "<?= $applicationID ?>",
          tests: JSON.stringify(testsData),
          doctor_total: doctorTotal,
          standard_total: standardTotal,
          payment_method: paymentMethod,
          transaction_number: transactionNumber,
          transaction_amount: transactionAmount,
          cash_amount: cashAmount
      },
      success: function(response) {
          Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Tests saved successfully!',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true,
              didClose: () => {
                $("#tests option:selected").prop("selected", false);
                $("#tests").trigger("change.select2");
                $("#amount_method").val("").trigger("change");
                $("#transaction_number").val('');
                $("#transaction_amount").val('');
                $("#cash_amount").val('');
                $("#txnDetailsDiv, #txnAmountDiv, #cashAmountDiv").hide();
                $("#testsDataContainer").empty();
                // Clear the selected tests table
                var $table = $('#selectedTestsTable');
                $table.find('tbody').empty();
                $table.hide();
                GetView();
              }
          });
      },
      error: function() {
          Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Something went wrong!',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
          });
      }
    });

  }

  function GetView() {
    var orgId = $("#organizations").val();
    var applicationId = $("#appointRegisterId option:selected").text();
    var patientId = $("#appointUnicode option:selected").text();
  
    // console.log("Debug Values -> orgId:", orgId, "PatientID:", patientId, "applicationID:", applicationId);

    $.ajax({
        url: "ajax/billing/saveTest.php",
        method: "POST",
        data: {
          action:"1",
            orgId: orgId,
            PatientID: patientId,
            applicationID: applicationId,
        },
        success: function (response) {
          $("#tableExport_wrapper").html(response);

          if ($("#tableExport1").length) {
            if ($.fn.dataTable.isDataTable('#tableExport1')) {
              $('#tableExport1').DataTable().destroy();
            }

              $('#tableExport1').DataTable({
                  retrieve: true,
                  dom: 'lBrftip',
                  buttons: [
                      {
                          extend: 'copy',
                          exportOptions: { columns: ':visible' },
                      },
                      {
                          extend: 'excel',
                          exportOptions: { columns: ':visible' },
                      },
                      {
                          extend: 'csv',
                          exportOptions: { columns: ':visible' },
                      },
                      {
                          extend: 'pdf',
                          exportOptions: { columns: ':visible' },
                      },
                      {
                          extend: 'print',
                          exportOptions: { columns: ':visible' },
                      },
                  ]
              });
            }
          },
        error: function (error) {
            console.error("AJAX Error:", error);
            $("#tableExport_wrapper").html("<div class='text-center text-danger'>Error loading collection data.</div>");
        }
    });
  }

</script>
