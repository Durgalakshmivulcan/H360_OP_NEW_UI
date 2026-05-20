<?php
require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>
<div class="main-content">
  <section class="section">
    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">Reports</h4></li>
      <li class="breadcrumb-item active">Patient Waiting Time</li>
    </ul>
    <div class="row">
      <!-- Filter card -->
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header"><h4>Filter Criteria</h4></div>
          <div class="card-body">
            <form id="waitReportForm" class="row g-3">
              <div class="form-group col-md-3">
                <label for="fromDatePW" class="form-label">From Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="fromDatePW" name="fromDate" value="<?php echo date('Y-m-01'); ?>" required>
              </div>
              <div class="form-group col-md-3">
                <label for="toDatePW" class="form-label">To Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="toDatePW" name="toDate" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
              <div class="form-group col-md-3">
                <label for="doctorPW" class="form-label">Doctor</label>
                <select id="doctorPW" name="doctor" class="form-select select2">
                  <option value="">All Doctors</option>
                  <?php
                 $checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
                  $securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

                  // ---- Doctors list ----
                  // SA_FATAL_FIXED_B_540: include SA so $sql is defined for super-admin
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
              <div class="form-group col-md-3">
                <label for="servicePW" class="form-label">Service</label>
                <select id="servicePW" name="service" class="form-select select2">
                  <option value="">All Services</option>
                  <?php
                  $srvRes = mysqli_query($conn, "SELECT service_id, service_name FROM services WHERE status='1'") or die(mysqli_error($conn));
                  while($sv = mysqli_fetch_object($srvRes)){
                    echo "<option value=\"{$sv->service_id}\">{$sv->service_name}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-12">
                <button type="button" class="btn btn-primary" onclick="loadWaitReport()">
                  <i data-feather="clock"></i> Run Report
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- Summary tiles -->
      <div class="col-lg-12" id="waitSummary" style="display:none;">
        <div class="row">
          <div class="col-md-6 col-sm-6">
            <div class="card card-statistic-1 bg-info text-white">
              <div class="card-icon"><i data-feather="watch"></i></div>
              <div class="card-wrap">
                <div class="card-header"><h5>Average Waiting Time</h5></div>
                <div class="card-body" id="tileAvgWait">0 mins</div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-6">
            <div class="card card-statistic-1 bg-secondary text-white">
              <div class="card-icon"><i data-feather="activity"></i></div>
              <div class="card-wrap">
                <div class="card-header"><h5>Average Visit Duration</h5></div>
                <div class="card-body" id="tileAvgVisit">0 mins</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Chart -->
      <div class="col-lg-12" id="waitChartContainer" style="display:none;">
        <div class="card">
          <div class="card-header"><h4>Average Waiting vs Visit Duration</h4></div>
          <div class="card-body"><div id="waitChart" style="width:100%; height:350px;"></div></div>
        </div>
      </div>
      <!-- Table -->
      <div class="col-lg-12" id="waitTableContainer" style="display:none;">
        <div class="card">
          <div class="card-header"><h4>Detailed Waiting & Duration Statistics</h4></div>
          <div class="card-body table-responsive">
            <table id="waitReportTable" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>Doctor</th>
                  <th>Service</th>
                  <th>Total Appointments</th>
                  <th>Average Waiting (mins)</th>
                  <th>Average Visit Duration (mins)</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once("./ajax/footer.php"); ?>
<script>
$(document).ready(function(){
  $('.select2').select2({ width:'100%' });
  loadWaitReport();
});

function loadWaitReport(){
  const data = {
    fromDate: $('#fromDatePW').val(),
    toDate:   $('#toDatePW').val(),
    doctor:   $('#doctorPW').val(),
    service:  $('#servicePW').val()
  };
  if(!data.fromDate || !data.toDate){
    iziToast.warning({ title:'Missing input', message:'Please choose both From and To dates.' });
    return;
  }
  if(data.toDate < data.fromDate){
    iziToast.warning({ title:'Invalid range', message:'The To date cannot be earlier than the From date.' });
    return;
  }
  $.ajax({
    url: './ajax/patient_waiting/report_data.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    success: function(resp){
    console.log(resp);
      if(!resp || !resp.tableData){
        iziToast.info({ title:'No data', message:'No appointment timing records found for the selected criteria.' });
        return;
      }
      $('#waitSummary').show();
      $('#waitChartContainer').show();
      $('#waitTableContainer').show();
     // Show overall average visit duration
      $('#tileAvgVisit').text(resp.averages.avgVisit.toFixed(1) + ' mins');

      // If you don’t have “avgWait”, remove or guard this line:
      if (resp.averages.avgWait !== undefined) {
          $('#tileAvgWait').text(resp.averages.avgWait.toFixed(1) + ' mins');
      }

      renderWaitChart(resp.chartSeries);
      populateWaitTable(resp.tableData);
    },
    error: function(){
      iziToast.error({ title:'Error', message:'Failed to load report data.' });
    }
  });
}
function renderWaitChart(series){
 if (window.waitChart && typeof window.waitChart.destroy === 'function') {
    window.waitChart.destroy();
  }
  const options = {
    chart:{ type:'bar', height:350 },
    series: series,
    xaxis:{ categories:['Average Waiting','Average Visit Duration'] },
    colors:['#17A2B8','#6C757D'],
    dataLabels:{ enabled:true, formatter: val => val.toFixed(1) + ' mins' },
    plotOptions:{ bar:{ horizontal:false, columnWidth:'40%' } },
    legend:{ show:false }
  };
  window.waitChart = new ApexCharts(document.querySelector('#waitChart'), options);
  window.waitChart.render();
}
function populateWaitTable(rows){
  $('#waitReportTable').DataTable({
    destroy:true,
    data: rows,
    columns:[
      { data:'doctor' },
      { data:'service' },
      { data:'total', className:'text-center', render: d => `<span class="badge bg-primary">${d}</span>` },
      { data:'avgWait', className:'text-center', render: d => d.toFixed(1) },
      { data:'avgDuration', className:'text-center', render: d => d.toFixed(1) }
    ]
  });
}
</script>