<?php
require_once('./ajax/header.php');
// FIX_B_1820 (scope 2 RBAC): per-action view gate; SA bypassed by userCan().
requireCan('view', basename(__FILE__));
$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId = $_SESSION['org_id'];

?>

<style>
  .scroll-container{
    max-height: 300px;   
    overflow-y: auto;    
    padding: 5px;
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

  .drag-handle {
    cursor: grab;
    color: #aaa;
    padding: 0 4px;
  }
  .drag-handle:active { cursor: grabbing; }
  #queueTable tbody tr { cursor: default; }
  #queueTable tbody tr.ui-sortable-helper { box-shadow: 0 4px 12px rgba(0,0,0,0.15); background: #fff; }
</style>

<div class="main-content">
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item"><h4 class="page-title m-b-0">Receptionist</h4></li>
    <li class="breadcrumb-item active">Patient Flow</li>
  </ul>

  <!-- Top row: summary cards and patient queue -->
  <div class="row mb-4">
    <!-- Summary cards -->
    <div class="col-lg-4 col-12 mb-3 mb-lg-0">
      <div class="card gradient-primary mb-3">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-users fa-2x"></i>
          </div>
          <div>
            <h6 class="mb-1">Total Patients</h6>
            <h2 id="totalPatients" class="mb-0">0</h2>
          </div>
        </div>
      </div>
      <div class="card gradient-success mb-3">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="fas fa-user-check fa-2x"></i>
          </div>
          <div>
            <h6 class="mb-1">Next Patient</h6>
            <h4 id="nextPatient" class="mb-0">-</h4>
          </div>
        </div>
      </div>
      <div class="card gradient-warning">
        <div class="card-body">
          <h6 class="mb-1">Patient Status</h6>
          <div class="d-flex justify-content-between">
            <div>
              <small>Done</small>
              <div id="countDone" class="fw-bold">0</div>
            </div>
            <div>
              <small>Pending</small>
              <div id="countPending" class="fw-bold">0</div>
            </div>
            <div>
              <small>Lapsed</small>
              <div id="countLapsed" class="fw-bold">0</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Patient Queue -->
    <div class="col-lg-8 col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Patient Queue</h4>
          <input type="search" id="queueSearch" placeholder="Search..." class="form-control form-control-sm w-auto">
        </div>
        <div class="card-body table-responsive px-10 scroll-container">
          <table id="queueTable" class="table mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:30px"></th>
                <th>Patient Name</th>
                <th>Doctor</th>
                <th>Appointment Time</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h4>Filters</h4></div>
        <div class="card-body">
          <form id="filterForm" class="row g-3">
            <div class="col-md-3">
              <label for="fromDate" class="form-label">From Date</label>
              <input type="date" id="fromDate" name="fromDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
              <label for="toDate" class="form-label">To Date</label>
              <input type="date" id="toDate" name="toDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
              <label for="doctor" class="form-label">Doctor</label>
              <select id="doctor" name="doctor" class="form-select select2">
                <option value="">All Doctors</option>
                <?php
                $checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
                $securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

                // ---- Doctors list ----
                // SA_FATAL_FIXED_B_545: include SA so $sql is defined for super-admin
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

                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<option value=\"{$row['doc_id']}\">{$row['doctor_name']}</option>";
                }
              ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="service" class="form-label">Service</label>
              <select id="service" name="service" class="form-select select2">
                <option value="">All Services</option>
                <?php
                $serviceRes = mysqli_query($conn, "SELECT service_id, service_name FROM services WHERE status='1'");
                while ($srv = mysqli_fetch_object($serviceRes)) {
                  echo '<option value="'.$srv->service_id.'">'.$srv->service_name.'</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-primary mt-3" id="applyFilters">Apply Filters</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header"><h4>Appointments</h4></div>
        <div class="card-body table-responsive">
          <table id="appointmentsTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Prescription</th>
                <th> Bill PDF & Print</th>
                <th>Patient ID</th>
                <th>Appointment ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Minutes Spent</th>
                <th>Valid Till</th>
                <th>Payment Method</th>
                <th>UPI Txn No.</th>
                <th>UPI Amount</th>
                <th>Cash Amount</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment & Concession Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <input type="hidden" id="modalAppointId">
                    <input type="hidden" id="modalOrgId">
                    <input type="hidden" id="patientID">
                    <input type="hidden" id="createdBy" value="<?= $SessionRoleId ?>">

                    <!-- Doctor Fee -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="Doctor_fee">Doctor Fee</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="Doctor_fee" disabled>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="modal_amount_method">Payment Method</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-cash"></i>
                                </div>
                            </div>
                            <select class="form-control form-select" id="modal_amount_method">
                                <option value="">Select Payment Method</option>
                                <?php
                                $getPayment_method = mysqli_query($conn, "SELECT payment_method_id, payment_method FROM payment_method WHERE status='1' ORDER BY payment_method_id ASC");
                                while ($resPayment = mysqli_fetch_object($getPayment_method)) {
                                    $selected = ($resPayment->payment_method == "Cash") ? "selected" : "";
                                    echo "<option value='{$resPayment->payment_method}' {$selected}>{$resPayment->payment_method}</option>";
                                }
                                ?>
                                <option value="Both (Cash + UPI)">Both (Cash + UPI)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transaction Number (UPI/Both) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_txnDetails" style="display:none;">
                        <label for="modal_transactionNumber">UPI Transaction Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="modal_transactionNumber" placeholder="Enter UPI transaction number">
                        </div>
                    </div>

                    <!-- UPI Amount (shown when UPI or Both) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_txnAmountDiv" style="display:none;">
                        <label for="modal_transactionAmount">UPI Amount (&#8377;)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-currency-rupee"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="modal_transactionAmount" placeholder="Enter UPI amount" min="0">
                        </div>
                    </div>

                    <!-- Cash Amount (shown only for Both Cash+UPI) -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_cashAmountDiv" style="display:none;">
                        <label for="modal_cashAmount">Cash Amount (&#8377;)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-cash"></i>
                                </div>
                            </div>
                            <input type="number" class="form-control" id="modal_cashAmount" placeholder="Enter cash amount" min="0">
                        </div>
                    </div>

                    <!-- Concession Name -->
                    <div class="form-group col-lg-6 col-sm-12">
                        <label for="modal_concessionName">Concession Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-card-text"></i>
                                </div>
                            </div>
                            <select class="form-control form-select" id="modal_concessionName" onchange="fillConcessionDetails();">
                                <option value="">Select Concession</option>
                                <?php
                                $getConcessions = mysqli_query($conn, "SELECT concession_id, concession_name, concession_type, concession_value FROM concessions WHERE status='1' ORDER BY concession_name ASC");
                                while ($resConcession = mysqli_fetch_object($getConcessions)) {
                                    echo '<option value="' . $resConcession->concession_id . '" 
                              data-type="' . $resConcession->concession_type . '" 
                              data-value="' . $resConcession->concession_value . '">'
                                        . $resConcession->concession_name .
                                        '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Concession Type -->
                    <div class="form-group col-lg-6 col-sm-12" id="modal_concessionTypeDiv" onchange="validateConcessionValue();" style="display:none;">
                        <label for="modal_concessionType">Concession Type</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                            </div>
                            <select id="modal_concessionType" class="form-control form-select">
                                <option value="">-- Select Type --</option>
                                <option value="percentage">Percentage</option>
                                <option value="amount">Amount</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-6 col-sm-12" id="modal_concessionValueDiv" style="display:none;">
                        <label for="modal_concessionValue" id="valueLabel">Value</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text" id="valueSymbol"> <!-- dynamic symbol here -->
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <input type="text" id="modal_concessionValue" class="form-control" placeholder="Enter value">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="skipModalPayment();">Skip</button>
                <button type="button" class="btn btn-primary" onclick="saveModalPayment();">Save</button>
            </div>
        </div>
    </div>
</div>


<?php require_once('./ajax/footer.php'); ?>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>

  let nextHtmlRaw = '';
  let upcomingnextData = null;

$(document).ready(function() {

  const source = new EventSource("ajax/receptionist/sserecptionist.php");

  source.onmessage = function(event) {
    try {
      const data = JSON.parse(event.data);
      console.log("SSE Data:", data);
      loadAppointments(data);
    } catch (err) {
      console.error("Invalid JSON from SSE", event.data);
    }
  };

  source.addEventListener("ping", function(event) {
      // Keep-alive, optional
      console.log("SSE ping:", event.data);
  });

   source.onerror = function(err) {
    console.warn("SSE connection error:", err);
  };

  window.addEventListener('beforeunload', () => {
    source.close();
  });

  loadAppointments();

  $('#applyFilters').on('click', function() {
    loadAppointments();
  });

// Load appointments when filters are applied
function loadAppointments() {
  if ($.fn.DataTable.isDataTable('#appointmentsTable')) {
    $('#appointmentsTable').DataTable().destroy();
  }
  // Now clear table bodies
  $('#appointmentsTable tbody').empty();
  $('#queueTable tbody').empty();
  var data = {
    fromDate: $('#fromDate').val(),
    toDate: $('#toDate').val(),
    doctor: $('#doctor').val(),
    service: $('#service').val()
  };
  $.ajax({
    url: './ajax/receptionist/get_appointments.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    success: function(res) {
      var tbody = '';
      var pending = 0, inSession = 0, completed = 0, noShow = 0;
      res.forEach(function(item) {
        switch (item.status) {
          case 'Pending': pending++; break;
          case 'Active': inSession++; break;
          case 'Done': completed++; break;
          case 'Lapsed': noShow++; break;
        }

        let disabledStartRow = '';
        let disabledDoneRow  = '';
        let disabledLapRow   = '';
        let disabledPrescription = 'disabled text-secondary';
        let prescriptionUrl = '#';

        if (item.status === 'Active') {
          disabledStartRow = 'disabled text-secondary';
          disabledLapRow = 'disabled';
        } else if (item.status === 'Pending') {
          disabledDoneRow = 'disabled';
        } else if (item.status === 'Done') {
          if(item.has_prescription === true) {
            disabledPrescription = '';
            prescriptionUrl = `patientPrescription.php?ItemId=${item.prescription_id}&OrgID=${item.org_id}`;
          } else {
            disabledPrescription = 'disabled text-secondary';
            prescriptionUrl = '#';
          }
        } else if (item.status === 'Lapsed') {
          disabledLapRow = 'disabled';
        }

        // ✅ Build invoice/payment section
        let paymentHtml = '';
        if (item.invoice_payment && item.invoice_payment !== '0') {
          paymentHtml = `
            <li class="dropdown">
              <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                <i class="fas fa-paste" style="font-size: 24px;"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-center" style="width: auto; min-width: 165px;">
                <a href="billview.php?ItemId=${item.appoint_id}" target="_blank" class="dropdown-item">
                  <i class="far fa-file-pdf"></i> Bill PDF
                </a>
                <a href="billPrint.php?ItemId=${item.appoint_id}" target="_blank" class="dropdown-item">
                  <i class="far fa-file-powerpoint"></i> Bill Print
                </a>
                <a href="combinedBill.php?appoint_id=${item.appoint_id}" target="_blank" class="dropdown-item">
                  <i class="fas fa-file-invoice"></i> Combined Bill
                </a>
              </div>
            </li>
          `;
        } else {
          paymentHtml = `
            <a href="#" onclick="makePayment('${item.appoint_register_id}', '${item.org_id}', '${item.appoint_unicode}', '${item.amount}')">
              <i class="bi bi-cash-coin" style="font-size: 24px; color: black;"></i>
            </a>
          `;
        }

        // ✅ Build table row
        tbody += `
          <tr>
            <td class="text-center">
              <a class="btn btn-primary btn-sm me-2 start-btn ${disabledPrescription}" 
                href="${prescriptionUrl}" target="_blank">View</a>
            </td>
            <td class="text-center"><ul class="navbar-nav">${paymentHtml}</ul></td>
            <td>${item.patient_id}</td>
            <td>${item.appoint_register_id}</td>
            <td>${item.patient_name}</td>
            <td>${item.doctor_name}</td>
            <td>${item.service_name}</td>
            <td>${item.appoint_date}</td>
            <td>${item.start_time} - ${item.end_time}</td>
            <td><span class="badge ${badgeClass(item.status)}">${item.status}</span></td>
            <td>${item.minutes_spent}</td>
            <td>${item.valid_to}</td>
            <td>${item.amount_method || '-'}</td>
            <td>${item.transaction_number || '-'}</td>
            <td>${item.transaction_amount ? '₹' + item.transaction_amount : '-'}</td>
            <td>${item.cash_amount ? '₹' + item.cash_amount : '-'}</td>
            <td>
              <button class="btn btn-outline-success btn-sm me-2 start-btn" data-id="${item.appoint_register_id}" ${disabledStartRow}>Start</button>
              <button class="btn btn-outline-danger btn-sm me-2 done-btn" data-id="${item.appoint_register_id}" ${disabledDoneRow}>Done</button>
            </td>
          </tr>
        `;
      });


      $('#appointmentsTable tbody').html(tbody);
      // Reinitialize DataTable to provide search, sort and pagination
      if ($.fn.DataTable.isDataTable('#appointmentsTable')) {
        $('#appointmentsTable').DataTable().destroy();
      }
      
      $('#appointmentsTable').DataTable({
        // Sort by appointment date (column index 5 after adding Appointment ID column)
        order: [[5, 'asc']],
        pageLength: 10
      });
      // Populate queue table with patients who are pending or in session
      var queueBody = '';
      var firstPendingName = '-';
      res.forEach(function(item) {
        
        if (item.status === 'Active' || item.status === 'Pending' || item.status === 'In Session') {
          if (firstPendingName === '-' && item.status === 'Pending') {
            firstPendingName = item.patient_name;
          }
          // Determine queue button states based on status
          var qDisabledStart = '';
          var qDisabledDone  = '';
          var qDisabledLap   = '';
          if (item.status === 'Active') {
            qDisabledStart = 'disabled';
            qDisabledLap   = 'disabled';
          } else if (item.status === 'Pending') {
            qDisabledDone  = 'disabled';
          }
          queueBody += '<tr data-id="' + item.appoint_register_id + '">' +
            '<td class="text-center"><i class="fas fa-grip-vertical drag-handle"></i></td>' +
            '<td>' + item.patient_name + '</td>' +
            '<td>' + item.doctor_name + '</td>' +
            '<td>' + item.start_time + '</td>' +
            '<td><span class="badge ' + badgeClass(item.status) + '">' + item.status + '</span></td>' +
            '<td>' +
              '<button class="btn btn-primary btn-sm me-1 queue-start" data-id="' + item.appoint_register_id + '" ' + qDisabledStart + '>Start</button>' +
              '<button class="btn btn-success btn-sm me-1 queue-done" data-id="' + item.appoint_register_id + '" ' + qDisabledDone + '>Done</button>' +
              '<button class="btn btn-danger btn-sm queue-lapsed" data-id="' + item.appoint_register_id + '" ' + qDisabledLap + '>Lapsed</button>' +
            '</td>' +
          '</tr>';
        }
      });
      $('#queueTable tbody').html(queueBody);

      // Enable drag-and-drop reorder for patient queue
      $('#queueTable tbody').sortable({
        handle: '.drag-handle',
        axis: 'y',
        cursor: 'grabbing',
        update: function() {
          var order = [];
          $('#queueTable tbody tr').each(function() {
            order.push($(this).data('id'));
          });
          $.post('./ajax/receptionist/reorder_queue.php', {order: order}, function(resp) {
            if (resp.status !== 'ok') {
              iziToast.error({title: 'Error', message: 'Failed to save queue order'});
            }
          }, 'json');
        }
      });

      // Search filtering for queue
      var qSearch = $('#queueSearch');
      qSearch.off('keyup').on('keyup', function() {
        var term = $(this).val().toLowerCase();
        $('#queueTable tbody tr').each(function() {
          var text = $(this).text().toLowerCase();
          $(this).toggle(text.indexOf(term) > -1);
        });
      });
      // Update summary cards and next patient
      $('#totalPatients').text(res.length);
      $('#nextPatient').text(firstPendingName);
      $('#countDone').text(completed);
      $('#countPending').text(pending);
      $('#countLapsed').text(noShow);
    },
    error: function() {
      iziToast.error({title: 'Error', message: 'Failed to load appointments'});
    }
  });
}

  // Delegate click events for dynamic buttons
$('#appointmentsTable').on('click', '.start-btn', function() {
  var appointId = $(this).data('id');
  var $row = $(this).closest('tr');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'start'}, function(resp) {
    if (resp.status === 'ok') {
      // Instantly update status and badge in the row
      $row.find('td:eq(7) .badge')
        .removeClass()
        .addClass('badge bg-info')
        .text('Active');
      // Disable Start, enable Done
      $row.find('.start-btn').prop('disabled', true).addClass('text-secondary');
      $row.find('.done-btn').prop('disabled', false).removeClass('text-secondary');
      iziToast.success({title: 'Success', message: resp.message});
      // Do NOT call loadAppointments() here
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

$('#appointmentsTable').on('click', '.done-btn', function() {
  var appointId = $(this).data('id');
  var $row = $(this).closest('tr');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'done'}, function(resp) {
    if (resp.status === 'ok') {
      $row.find('td:eq(7) .badge')
        .removeClass()
        .addClass('badge bg-success')
        .text('Done');
      // Disable all buttons in this row
      $row.find('.start-btn, .done-btn').prop('disabled', true).addClass('text-secondary');
      iziToast.success({title: 'Success', message: resp.message});
    
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

$('#appointmentsTable').on('click', '.lapsed-btn', function() {
  var appointId = $(this).data('id');
  var $row = $(this).closest('tr');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'lapsed'}, function(resp) {
    if (resp.status === 'ok') {
      $row.find('td:eq(7) .badge')
        .removeClass()
        .addClass('badge bg-danger')
        .text('Lapsed');
      // Disable Lapsed button
      $row.find('.lapsed-btn').prop('disabled', true).addClass('text-secondary');
      iziToast.success({title: 'Success', message: resp.message});
     
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

$('#appointmentsTable').on('click', '.start-btn', function() {
  var appointId = $(this).data('id');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'start'}, function(resp) {
    if (resp.status === 'ok') {
      loadAppointments();
      iziToast.success({title: 'Success', message: resp.message});
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

$('#appointmentsTable').on('click', '.done-btn', function() {
  var appointId = $(this).data('id');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'done'}, function(resp) {
    if (resp.status === 'ok') {
      loadAppointments();
      iziToast.success({title: 'Success', message: resp.message});
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

$('#appointmentsTable').on('click', '.lapsed-btn', function() {
  var appointId = $(this).data('id');
  $.post('./ajax/receptionist/update_duration.php', {appointment_id: appointId, action: 'lapsed'}, function(resp) {
    if (resp.status === 'ok') {
      loadAppointments();
      iziToast.success({title: 'Success', message: resp.message});
    } else {
      iziToast.error({title: 'Error', message: resp.message});
    }
  }, 'json');
});

  // Queue action handlers
  $('#queueTable').on('click', '.queue-start', function() {
    var id = $(this).data('id');
    $.post('./ajax/receptionist/update_duration.php', {appointment_id: id, action: 'start'}, function(resp) {
        loadAppointments();
      if (resp.status === 'ok') {
        iziToast.success({title: 'Success', message: resp.message});
      } else {
        iziToast.error({title: 'Error', message: resp.message});
      }
    }, 'json');
  });
  $('#queueTable').on('click', '.queue-done', function() {
    var id = $(this).data('id');
    $.post('./ajax/receptionist/update_duration.php', {appointment_id: id, action: 'done'}, function(resp) {
      if (resp.status === 'ok') {
        loadAppointments();
        iziToast.success({title: 'Success', message: resp.message});
      } else {
        iziToast.error({title: 'Error', message: resp.message});
      }
    }, 'json');
  });
  $('#queueTable').on('click', '.queue-lapsed', function() {
      var id = $(this).data('id');
      $.post('./ajax/receptionist/update_duration.php', {appointment_id: id, action: 'lapsed'}, function(resp) {
        if (resp.status === 'ok') {
          loadAppointments();
          iziToast.success({title: 'Success', message: resp.message});
        } else {
          iziToast.error({title: 'Error', message: resp.message});
        }
      }, 'json');
    });
  });


  function fillConcessionDetails() {
      let selectedOption = $('#modal_concessionName option:selected');

      if (selectedOption.val() !== "") {
          $('#modal_concessionTypeDiv, #modal_concessionValueDiv').show();

          let type = selectedOption.data('type');
          let value = selectedOption.data('value');

          $('#modal_concessionType').val(type).trigger('change');
          $('#modal_concessionValue').val(value);
          validateConcessionValue();
      } else {
          $('#modal_concessionTypeDiv, #modal_concessionValueDiv').hide();
          $('#modal_concessionType').val('');
          $('#modal_concessionValue').val('');
      }
  }

  $('#modal_concessionType, #modal_concessionValue').on('input change', validateConcessionValue);

  function validateConcessionValue() {
      let type = $('#modal_concessionType').val();
      let valueInput = $('#modal_concessionValue');
      let value = parseFloat(valueInput.val()) || 0;
      let amount = parseFloat($('#Doctor_fee').val()) || 0;
      let symbolDiv = $('#valueSymbol');

      valueInput.removeClass('is-invalid');

      if (type === "percentage") {
          symbolDiv.text('%');
      } else if (type === "amount") {
          symbolDiv.text('₹');
      } else {
          symbolDiv.text('');
      }

      if (amount === 0) {
          valueInput.val(0);
          valueInput.addClass('is-invalid');
          return;
      }

      if (type === "percentage") {
          value = Math.floor(value);
          if (value > 100) {
              valueInput.addClass('is-invalid');
              valueInput.val(100);
          } else if (value < 0) {
              valueInput.addClass('is-invalid');
              valueInput.val(0);
          } else {
              valueInput.val(value);
          }
      } else if (type === "amount") {
          if (!isNaN(amount) && value > amount) {
              valueInput.addClass('is-invalid');
              valueInput.val(amount);
          } else if (value < 0) {
              valueInput.addClass('is-invalid');
              valueInput.val(0);
          }
      }
  }

  function skipModalPayment() {
      $('#paymentModal').modal('hide');
      $('#Doctor_fee').val();
      // swal('', " Payment successfully", 'success');
      // setTimeout(function(){
      location.reload();
      // }, 2000);
  }

  function saveModalPayment() {
      const appointId = $('#modalAppointId').val();
      const orgId = $('#modalOrgId').val();
      const doctorFee = parseFloat($('#Doctor_fee').val()) || 0;
      const paymentMethod = $('#modal_amount_method').val();
      const txnNo = $('#modal_transactionNumber').val();
      const txnAmount = parseFloat($('#modal_transactionAmount').val()) || 0;
      const cashAmount = parseFloat($('#modal_cashAmount').val()) || 0;

      const concessionName = $('#modal_concessionName').val();
      const concessionType = $('#modal_concessionType').val();
      const concessionValue = $('#modal_concessionValue').val();
      const patient_id = $('#patientID').val();
      const createdBy = $('#createdBy').val();
      $.ajax({
          url: 'ajax/AppointmentBooking/Appointment_payment.php',
          type: 'POST',
          dataType: 'json',
          data: {
              patient_id: patient_id,
              appoint_id: appointId,
              createdBy: createdBy,
              org_id: orgId,
              doctor_fee: doctorFee,
              amount_method: paymentMethod,
              transaction_number: txnNo,
              transaction_amount: txnAmount,
              cash_amount: cashAmount,
              concession_name: concessionName,
              concession_type: concessionType,
              concession_value: concessionValue
          },
          success: function(res) {
              console.log(res);
              if (res.success) {
                  $('#paymentModal').modal('hide');
                  swal('', "Payment successfully", 'success');
                  setTimeout(() => location.reload(), 2000);
              } else {
                  swal('Error: ' + res.message);
              }
          },
          error: function(xhr, status, error) {
              let msg = 'Could not save payment info.';
              if (error) {
                  msg += '\n\nError: ' + error;
              }
              if (xhr.responseText) {
                  msg += '\n\nDetails: ' + xhr.responseText;
              }

              swal('Error', msg, 'error');
          }

      });
  }

  function makePayment(appointId, orgId, patientId, doctorFee) {
      // Fill modal hidden fields
      $('#modalAppointId').val(appointId);
      $('#modalOrgId').val(orgId);
      $('#patientID').val(patientId);
      $('#Doctor_fee').val(doctorFee);

      // Reset optional fields
      $('#modal_transactionNumber').val('');
      $('#modal_transactionAmount').val('');
      $('#modal_cashAmount').val('');
      $('#modal_concessionName').val('');
      $('#modal_concessionType').val('');
      $('#modal_concessionValue').val('');
      $('#modal_concessionTypeDiv').hide();
      $('#modal_concessionValueDiv').hide();
      $('#modal_txnDetails').hide();
      $('#modal_txnAmountDiv').hide();
      $('#modal_cashAmountDiv').hide();

      // Show the modal
      $('#paymentModal').modal('show');
  }

  $('#modal_amount_method').on('change', function() {
      const val = $(this).val().toLowerCase();
      if (val === 'upi' || val === 'both (cash + upi)') {
          $('#modal_txnDetails').show();
          $('#modal_txnAmountDiv').show();
      } else {
          $('#modal_txnDetails').hide();
          $('#modal_txnAmountDiv').hide();
          $('#modal_transactionNumber').val('');
          $('#modal_transactionAmount').val('');
      }
      if (val === 'both (cash + upi)') {
          $('#modal_cashAmountDiv').show();
      } else {
          $('#modal_cashAmountDiv').hide();
          $('#modal_cashAmount').val('');
      }
  });

  // Helper: return badge class based on appointment status
  function badgeClass(status) {
  
    switch (status) {
      case 'Pending': return 'bg-warning';
      case 'Active': return 'bg-info';
      case 'Done': return 'bg-success';
      case 'Lapsed': return 'bg-danger';
      default: return 'bg-secondary';
    }
  }
</script>