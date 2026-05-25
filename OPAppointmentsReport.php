<?php
// ============================================================================
// OPAppointmentsReport.php
//
// This page renders a comprehensive "OP Appointments Volume & Utilisation" report
// for the H360 platform.  Users can specify a custom date range and optionally
// filter by doctor and service.  The report displays total appointment counts
// for each status (booked, completed, cancelled and no‑shows) both in a
// summarised overview, in a bar chart, and in a detailed table broken down
// by service and doctor.
//
// The layout leverages the existing Gati template.  Filter controls are
// organised in a card at the top of the page.  Once data is loaded,
// colourful summary tiles appear, followed by a responsive bar chart and an
// interactive DataTable.  All external scripts (ApexCharts, DataTables,
// Select2, SweetAlert, etc.) are already available via ajax/footer.php.
// ============================================================================
require_once("./ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));

$opCheckDoc = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'");
$opSecType  = mysqli_fetch_assoc($opCheckDoc)['security_type'] ?? '';
$opEscOrg   = mysqli_real_escape_string($conn, $SessionOrgId);
?>
<!-- Main Content -->
<div class="main-content">
  <!-- Breadcrumb -->
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item"><h4 class="page-title m-b-0">Reports</h4></li>
    <li class="breadcrumb-item active">OP Appointments Volume & Utilisation</li>
  </ul>
  <div class="row">
    <!-- Filter card -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Filter Criteria</h4>
        </div>
        <div class="card-body">
          <form id="appointmentReportForm" class="row g-3">
            <!-- Date range pickers -->
            <div class="form-group col-md-3">
              <label for="fromDate" class="form-label">From Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="fromDate" name="fromDate" value="<?php echo date('Y-m-01'); ?>" required>
            </div>
            <div class="form-group col-md-3">
              <label for="toDate" class="form-label">To Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="toDate" name="toDate" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <!-- Doctor filter -->
            <div class="form-group col-md-3">
              <label for="doctor" class="form-label">Doctor</label>
              <select id="doctor" name="doctor" class="form-select select2">
                <option value="">All Doctors</option>
                <?php
                if ($opSecType === 'SA') {
                    $docSql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
                } elseif ($opSecType === 'A') {
                    $docSql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' AND org_id='$opEscOrg' ORDER BY doctor_name ASC";
                } elseif ($opSecType === 'U') {
                    $docSql = "SELECT d.doc_id, d.doctor_name
                               FROM doctors d
                               WHERE d.status = '1'
                               AND d.org_id = '$opEscOrg'
                               AND (
                                   d.security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'
                                   OR d.doc_id IN (
                                       SELECT r.doc_id
                                       FROM receptionnist r
                                       WHERE r.security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'
                                   )
                               )
                               ORDER BY d.doctor_name ASC";
                } else {
                    $docSql = null;
                }
                if ($docSql) {
                    $res = mysqli_query($conn, $docSql) or die(mysqli_error($conn));
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<option value=\"{$row['doc_id']}\">{$row['doctor_name']}</option>";
                    }
                }
                ?>
              </select>
            </div>
            <!-- Service filter -->
            <div class="form-group col-md-3">
              <label for="service" class="form-label">Service</label>
              <select id="service" name="service" class="form-select select2">
                <option value="">All Services</option>
                <?php
                if ($opSecType === 'SA') {
                    $serviceQry = mysqli_query($conn, "SELECT service_id, service_name FROM services WHERE status='1' ORDER BY service_name ASC") or die(mysqli_error($conn));
                } else {
                    $serviceQry = mysqli_query($conn, "SELECT DISTINCT s.service_id, s.service_name FROM services s JOIN doctors d ON FIND_IN_SET(s.service_id, d.doctor_services) > 0 WHERE s.status='1' AND d.org_id='$opEscOrg' AND d.status='1' ORDER BY s.service_name ASC") or die(mysqli_error($conn));
                }
                while($srv = mysqli_fetch_object($serviceQry)) {
                  echo "<option value=\"{$srv->service_id}\">{$srv->service_name}</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-primary" onclick="loadReport()">
                <i data-feather="bar-chart-2"></i> Run Report
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Summary tiles -->
    <div class="col-lg-12" id="summaryTiles" style="display: none;">
      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary"><i data-feather="calendar"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Booked</h4></div>
              <div class="card-body" id="tileBooked">0</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card card-statistic-1">
            <div class="card-icon bg-success"><i data-feather="check-circle"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Completed</h4></div>
              <div class="card-body" id="tileCompleted">0</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card card-statistic-1">
            <div class="card-icon bg-danger"><i data-feather="x-circle"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>Cancelled</h4></div>
              <div class="card-body" id="tileCancelled">0</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card card-statistic-1">
            <div class="card-icon bg-warning"><i data-feather="alert-circle"></i></div>
            <div class="card-wrap">
              <div class="card-header"><h4>No‑Shows</h4></div>
              <div class="card-body" id="tileNoShows">0</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Chart Section -->
    <div class="col-lg-12" id="chartContainer" style="display: none;">
      <div class="card">
        <div class="card-header"><h4>Appointment Status Distribution</h4></div>
        <div class="card-body"><div id="statusChart" style="width:100%; height:350px;"></div></div>
      </div>
    </div>
    <!-- Table Section -->
    <div class="col-lg-12" id="tableContainer" style="display: none;">
      <div class="card">
        <div class="card-header"><h4>Appointments Summary by Service & Doctor</h4></div>
        <div class="card-body table-responsive">
          <table id="reportTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Service</th>
                <th>Doctor</th>
                <th>Booked</th>
                <th>Completed</th>
                <th>Cancelled</th>
                <th>No‑Shows</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once("ajax/footer.php"); ?>
<script>
$(document).ready(function() {
  $('.select2').select2({ width: '100%' });
  loadReport();
});
function loadReport() {
  const fromDate = $('#fromDate').val();
  const toDate   = $('#toDate').val();
  if (!fromDate || !toDate) {
    iziToast.warning({ title: 'Missing input', message: 'Please select both From and To dates.' });
    return;
  }
  if (toDate < fromDate) {
    iziToast.warning({ title: 'Invalid range', message: 'The To date cannot be earlier than the From date.' });
    return;
  }
  const data = {
    fromDate: fromDate,
    toDate: toDate,
    doctor: $('#doctor').val(),
    service: $('#service').val()
  };
  $.ajax({
    url: './ajax/op_appointments/report_data.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    success: function(resp) {
      if (!resp || !resp.tableData) {
        iziToast.info({ title: 'No data', message: 'No appointments found for the selected criteria.' });
        return;
      }
      $('#summaryTiles').show();
      $('#chartContainer').show();
      $('#tableContainer').show();
      $('#tileBooked').text(resp.totals.booked);
      $('#tileCompleted').text(resp.totals.completed);
      $('#tileCancelled').text(resp.totals.cancelled);
      $('#tileNoShows').text(resp.totals.noShows);
      renderChart(resp.chartSeries);
      populateTable(resp.tableData);
    },
    error: function() {
      iziToast.error({ title: 'Error', message: 'An error occurred while fetching data.' });
    }
  });
}
function renderChart(series) {
  if (window.appointmentsChart) {
    window.appointmentsChart.destroy();
  }
  const options = {
    chart: { type: 'bar', height: 350 },
    series: series,
    xaxis: { categories: ['Booked','Completed','Cancelled','No‑Shows'] },
    colors: ['#007BFF','#28A745','#DC3545','#FFC107'],
    dataLabels: { enabled: true },
    plotOptions: { bar: { distributed: true, borderRadius: 4, horizontal: false } },
    legend: { show: false }
  };
  window.appointmentsChart = new ApexCharts(document.querySelector('#statusChart'), options);
  window.appointmentsChart.render();
}
function populateTable(rows) {
  $('#reportTable').DataTable({
    destroy: true,
    data: rows,
    columns: [
      { data: 'service' },
      { data: 'doctor' },
      { data: 'booked', className:'text-center', render: data => `<span class=\"badge bg-primary\">${data}</span>` },
      { data: 'completed', className:'text-center', render: data => `<span class=\"badge bg-success\">${data}</span>` },
      { data: 'cancelled', className:'text-center', render: data => `<span class=\"badge bg-danger\">${data}</span>` },
      { data: 'noShows', className:'text-center', render: data => `<span class=\"badge bg-warning text-dark\">${data}</span>` }
    ]
  });
}
</script>
