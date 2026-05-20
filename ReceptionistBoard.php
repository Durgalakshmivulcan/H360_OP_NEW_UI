<?php
/*
 * Receptionist Adsboard
 *
 * This page acts as an operational board for the receptionist.  It
 * summarizes the current day's appointments and allows walk‑in / walk‑out
 * times to be recorded via Start and Done buttons.  The top of the page
 * displays summary cards showing how many patients are pending, in session,
 * completed or no‑shows.  Below the cards is a table listing each
 * appointment with the patient's name, doctor, service, appointment time,
 * status and actions.  The design follows the Gati theme used across
 * H360.
 */

require_once('ajax/header.php');
?>

<div class="main-content">
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item"><h4 class="page-title m-b-0">Receptionist Adsboard</h4></li>
    <li class="breadcrumb-item active">Patient Flow & Walk‑ins</li>
  </ul>
  <!-- Summary cards -->
  <div class="row mb-4">
    <div class="col-md-3 col-6">
      <div class="card text-center">
        <div class="card-body">
          <h6 class="text-muted">Pending</h6>
          <h2 id="countPending" class="text-warning mb-0">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card text-center">
        <div class="card-body">
          <h6 class="text-muted">In Session</h6>
          <h2 id="countInSession" class="text-info mb-0">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card text-center">
        <div class="card-body">
          <h6 class="text-muted">Completed</h6>
          <h2 id="countCompleted" class="text-success mb-0">0</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="card text-center">
        <div class="card-body">
          <h6 class="text-muted">No Show</h6>
          <h2 id="countNoShow" class="text-danger mb-0">0</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Date filter row -->
  <div class="row mb-4">
    <div class="col-md-4">
      <label for="filterDate" class="form-label">Date</label>
      <input type="date" id="filterDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="col-md-2 d-flex align-items-end">
      <button type="button" id="applyDate" class="btn btn-primary">Apply</button>
    </div>
  </div>

  <!-- Appointments table -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h4>Appointments</h4></div>
        <div class="card-body table-responsive">
          <table id="boardTable" class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Service</th>
                <th>Time</th>
                <th>Status</th>
                <th>Minutes Spent</th>
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

<?php require_once('ajax/footer.php'); ?>

<script>
function loadBoard() {
  var selectedDate = $('#filterDate').val();
  $.ajax({
    url: 'ajax/receptionist/get_appointments.php',
    type: 'POST',
    data: {fromDate: selectedDate, toDate: selectedDate},
    dataType: 'json',
    success: function(res) {
      var pending = 0, inSession = 0, completed = 0, noShow = 0;
      var tbody = '';
      res.forEach(function(item) {
        // Count statuses for summary cards
        switch (item.status) {
          case 'Pending':   pending++;   break;
          case 'In Session': inSession++; break;
          case 'Completed': completed++; break;
          case 'No Show':   noShow++;   break;
        }
        // Determine button states as in Receptionist.php
        var disabledStart = '';
        var disabledDone  = '';
        if (item.status === 'In Session' || item.open_sessions > 0) {
          disabledStart = 'disabled';
          disabledDone  = '';
        } else if (item.status === 'Pending') {
          disabledStart = '';
          disabledDone  = 'disabled';
        } else {
          disabledStart = 'disabled';
          disabledDone  = 'disabled';
        }
        // Compose row
        tbody += '<tr>' +
          '<td>' + item.patient_name + '</td>' +
          '<td>' + item.doctor_name + '</td>' +
          '<td>' + item.service_name + '</td>' +
          '<td>' + item.start_time + ' - ' + item.end_time + '</td>' +
          '<td><span class="badge ' + badgeClass(item.status) + '">' + item.status + '</span></td>' +
          '<td>' + item.minutes_spent + '</td>' +
          '<td>' +
            '<button class="btn btn-outline-success btn-sm me-2 start-btn" data-id="' + item.appoint_id + '" ' + disabledStart + '>Start</button>' +
            '<button class="btn btn-outline-danger btn-sm done-btn" data-id="' + item.appoint_id + '" ' + disabledDone + '>Done</button>' +
          '</td>' +
        '</tr>';
      });
      $('#boardTable tbody').html(tbody);
      // Update summary cards
      $('#countPending').text(pending);
      $('#countInSession').text(inSession);
      $('#countCompleted').text(completed);
      $('#countNoShow').text(noShow);
    },
    error: function() {
      iziToast.error({title: 'Error', message: 'Failed to load board data'});
    }
  });
}

// Return CSS badge class based on status
function badgeClass(status) {
  if (status === 'Pending') return 'bg-warning';
  if (status === 'In Session') return 'bg-info';
  if (status === 'Completed') return 'bg-success';
  if (status === 'No Show') return 'bg-danger';
  return 'bg-secondary';
}

$(document).ready(function() {
  loadBoard();
  $('#applyDate').on('click', function() { loadBoard(); });
  // Handle start/done actions
  $('#boardTable').on('click', '.start-btn', function() {
    var id = $(this).data('id');
    $.post('ajax/receptionist/update_duration.php', {appointment_id: id, action: 'start'}, function(resp) {
      if (resp.status === 'ok') {
        iziToast.success({title: 'Success', message: resp.message});
        loadBoard();
      } else {
        iziToast.error({title: 'Error', message: resp.message});
      }
    }, 'json');
  });
  $('#boardTable').on('click', '.done-btn', function() {
    var id = $(this).data('id');
    $.post('ajax/receptionist/update_duration.php', {appointment_id: id, action: 'done'}, function(resp) {
      if (resp.status === 'ok') {
        iziToast.success({title: 'Success', message: resp.message});
        loadBoard();
      } else {
        iziToast.error({title: 'Error', message: resp.message});
      }
    }, 'json');
  });
});
</script>