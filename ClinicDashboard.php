<?php
/*
 * Clinic Dashboard
 *
 * This unified dashboard displays key metrics across the clinic for doctors,
 * receptionists and accounts staff.  It summarises appointment volumes,
 * revenue, consultation times and service performance, and visualises
 * trends through interactive charts.  Use the date range filter to
 * customise the reporting period.
 */

require_once('ajax/header.php');
?>

<div class="main-content">
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item"><h4 class="page-title m-b-0">Dashboard</h4></li>
    <li class="breadcrumb-item active">Clinic Overview</li>
  </ul>
  <!-- Date range filter -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form id="dashboardFilter" class="row g-3 align-items-end">
            <div class="col-md-3">
              <label for="dashFrom" class="form-label">From</label>
              <input type="date" id="dashFrom" class="form-control" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-md-3">
              <label for="dashTo" class="form-label">To</label>
              <input type="date" id="dashTo" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
              <button type="button" id="refreshDashboard" class="btn btn-primary">Refresh</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Summary cards -->
  <div id="dashboardSummary" class="row mb-4" style="display:none;">
    <!-- Appointments summary -->
    <div class="col-md-6 col-lg-3 mb-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h6 class="mb-1">Total Appointments</h6>
          <h3 id="sumTotalAppointments" class="mb-0">0</h3>
          <div class="small">Pending: <span id="sumPending">0</span> | Active: <span id="sumActive">0</span> | Done: <span id="sumDone">0</span> | No Show: <span id="sumNoShow">0</span></div>
        </div>
      </div>
    </div>
    <!-- Revenue summary -->
    <div class="col-md-6 col-lg-3 mb-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h6 class="mb-1">Net Revenue</h6>
          <h3 id="sumNetRevenue" class="mb-0">₹0</h3>
          <div class="small">Gross: <span id="sumGross">₹0</span> | Discount: <span id="sumDiscount">₹0</span> | Tax: <span id="sumTax">₹0</span></div>
        </div>
      </div>
    </div>
    <!-- Consultation time summary -->
    <div class="col-md-6 col-lg-3 mb-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h6 class="mb-1">Avg Consultation Time</h6>
          <h3 id="avgConsultation" class="mb-0">0 min</h3>
          <div class="small">Avg Session Duration in minutes</div>
        </div>
      </div>
    </div>
    <!-- Waiting time summary -->
    <div class="col-md-6 col-lg-3 mb-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h6 class="mb-1">Avg Waiting Time</h6>
          <h3 id="avgWaiting" class="mb-0">0 min</h3>
          <div class="small">Avg time from check‑in to consultation (if available)</div>
        </div>
      </div>
    </div>
  </div>
  <!-- Charts -->
  <div id="dashboardCharts" style="display:none;">
    <div class="row mb-4">
      <div class="col-md-12 col-lg-6">
        <div class="card">
          <div class="card-header"><h5>Appointments by Status Over Time</h5></div>
          <div class="card-body">
            <div id="appointmentsStatusChart" style="height:300px;"></div>
          </div>
        </div>
      </div>
      <div class="col-md-12 col-lg-6">
        <div class="card">
          <div class="card-header"><h5>Revenue Trend</h5></div>
          <div class="card-body">
            <div id="revenueTrendChart" style="height:300px;"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 col-lg-6">
        <div class="card">
          <div class="card-header"><h5>Top Doctors by Appointments</h5></div>
          <div class="card-body">
            <div id="topDoctorsChart" style="height:300px;"></div>
          </div>
        </div>
      </div>
      <div class="col-md-12 col-lg-6">
        <div class="card">
          <div class="card-header"><h5>Top Services by Revenue</h5></div>
          <div class="card-body">
            <div id="topServicesChart" style="height:300px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once('ajax/footer.php'); ?>

<script>
function renderStackedAppointmentsChart(data) {
  var options = {
    chart: { type: 'bar', height: 300, stacked: true },
    plotOptions: { bar: { horizontal: false, columnWidth: '50%' } },
    xaxis: { categories: data.categories },
    yaxis: { title: { text: 'Appointments' } },
    legend: { position: 'top' },
    colors: ['#42A5F5','#66BB6A','#FFA726','#EF5350'],
    series: [
      { name: 'Pending', data: data.pending },
      { name: 'Active', data: data.active },
      { name: 'Done', data: data.done },
      { name: 'No Show', data: data.no_show }
    ]
  };
  var chart = new ApexCharts(document.querySelector('#appointmentsStatusChart'), options);
  chart.render();
}

function renderRevenueTrendChart(data) {
  var options = {
    chart: { type: 'line', height: 300 },
    xaxis: { categories: data.categories },
    yaxis: { title: { text: 'Net Revenue' } },
    colors: ['#43A047'],
    series: [ { name: 'Net Revenue', data: data.net } ]
  };
  var chart = new ApexCharts(document.querySelector('#revenueTrendChart'), options);
  chart.render();
}

function renderTopDoctorsChart(data) {
  var options = {
    chart: { type: 'bar', height: 300 },
    plotOptions: { bar: { horizontal: true } },
    xaxis: { categories: data.names, title: { text: 'Appointments' } },
    colors: ['#5C6BC0'],
    series: [ { name: 'Appointments', data: data.counts } ]
  };
  var chart = new ApexCharts(document.querySelector('#topDoctorsChart'), options);
  chart.render();
}

function renderTopServicesChart(data) {
  var options = {
    chart: { type: 'pie', height: 300 },
    labels: data.names,
    series: data.values,
    colors: ['#42A5F5','#66BB6A','#FFA726','#AB47BC','#26C6DA'],
    responsive: [ { breakpoint: 480, options: { chart: { width: 300 }, legend: { position: 'bottom' } } } ]
  };
  var chart = new ApexCharts(document.querySelector('#topServicesChart'), options);
  chart.render();
}

function loadDashboard() {
  var data = {
    fromDate: $('#dashFrom').val(),
    toDate: $('#dashTo').val()
  };
  $.ajax({
    url: 'ajax/dashboard/get_dashboard_data.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    success: function(res) {
      // Show summary
      $('#dashboardSummary').show();
      $('#dashboardCharts').show();
      // Update appointment summary
      $('#sumTotalAppointments').text(res.appointments.total);
      $('#sumPending').text(res.appointments.pending);
      $('#sumActive').text(res.appointments.active);
      $('#sumDone').text(res.appointments.done);
      $('#sumNoShow').text(res.appointments.no_show);
      // Update revenue summary
      $('#sumNetRevenue').text('₹' + res.revenue.net.toFixed(2));
      $('#sumGross').text('₹' + res.revenue.gross.toFixed(2));
      $('#sumDiscount').text('₹' + res.revenue.discount.toFixed(2));
      $('#sumTax').text('₹' + res.revenue.tax.toFixed(2));
      // Update consultation/waiting times
      $('#avgConsultation').text(res.avg.consultation ? res.avg.consultation.toFixed(2) + ' min' : '0 min');
      $('#avgWaiting').text(res.avg.waiting ? res.avg.waiting.toFixed(2) + ' min' : '0 min');
      // Render charts
      renderStackedAppointmentsChart(res.charts.appointments);
      renderRevenueTrendChart(res.charts.revenue);
      renderTopDoctorsChart(res.charts.top_doctors);
      renderTopServicesChart(res.charts.top_services);
    },
    error: function() {
      iziToast.error({ title:'Error', message:'Failed to load dashboard data' });
    }
  });
}

$(document).ready(function() {
  // Initial load
  loadDashboard();
  // Refresh on button
  $('#refreshDashboard').on('click', function() {
    loadDashboard();
  });
});
</script>