<?php
// ============================================================================
// NoShowCancellationReport.php
//
// Report: No‑Show & Cancellation Rate
// Description: Analyses no‑shows and cancellations by patient demographics,
// booking lead time and service type.  The report supports targeted reminder
// campaigns by revealing which patient groups or services have higher
// cancellation and no‑show rates.
// ============================================================================
require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));

$nsCheckDoc = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'");
$nsSecType  = mysqli_fetch_assoc($nsCheckDoc)['security_type'] ?? '';
$nsEscOrg   = mysqli_real_escape_string($conn, $SessionOrgId);
?>

<style>
  .apexcharts-menu-icon{
      display: none;
    }
</style>

<div class="main-content">
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item"><h4 class="page-title m-b-0">Reports</h4></li>
    <li class="breadcrumb-item active">No‑Show & Cancellation Rate</li>
  </ul>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header"><h4>Filter Criteria</h4></div>
        <div class="card-body">
          <form id="nsReportForm" class="row g-3">
            <div class="form-group col-md-3">
              <label for="fromDateNS" class="form-label">From Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="fromDateNS" name="fromDate" value="<?php echo date('Y-m-01'); ?>" required>
            </div>
            <div class="form-group col-md-3">
              <label for="toDateNS" class="form-label">To Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="toDateNS" name="toDate" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group col-md-2">
              <label for="gender" class="form-label">Gender</label>
              <select id="gender" name="gender" class="form-select select2">
                <option value="">All</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Others">Others</option>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="ageGroup" class="form-label">Age Group</label>
              <select id="ageGroup" name="ageGroup" class="form-select select2">
                <option value="">All</option>
                <option value="<18"><18</option>
                <option value="18-30">18-30</option>
                <option value="31-50">31-50</option>
                <option value=">50">>50</option>
              </select>
            </div>
            <div class="form-group col-md-2">
              <label for="serviceNS" class="form-label">Service</label>
              <select id="serviceNS" name="service" class="form-select select2">
                <option value="">All</option>
                <?php
                if ($nsSecType === 'SA') {
                    $srv = mysqli_query($conn, "SELECT service_id, service_name FROM services WHERE status='1' ORDER BY service_name ASC") or die(mysqli_error($conn));
                } else {
                    $srv = mysqli_query($conn, "SELECT DISTINCT s.service_id, s.service_name FROM services s JOIN doctors d ON FIND_IN_SET(s.service_id, d.doctor_services) > 0 WHERE s.status='1' AND d.org_id='$nsEscOrg' AND d.status='1' ORDER BY s.service_name ASC") or die(mysqli_error($conn));
                }
                while($s = mysqli_fetch_object($srv)) {
                  echo "<option value=\"{$s->service_id}\">{$s->service_name}</option>";
                }
                ?>
              </select>
            </div>
      <div class="form-group col-md-3">
        <label for="doctorNS" class="form-label">Doctor</label>
        <select id="doctorNS" name="doctor" class="form-select select2">
          <option value="">All</option>
          <?php
            if ($nsSecType === 'SA') {
                $docSql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
            } elseif ($nsSecType === 'A') {
                $docSql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' AND org_id='$nsEscOrg' ORDER BY doctor_name ASC";
            } elseif ($nsSecType === 'U') {
                $docSql = "SELECT d.doc_id, d.doctor_name
                           FROM doctors d
                           WHERE d.status = '1'
                           AND d.org_id = '$nsEscOrg'
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
            <div class="col-12">
              <button type="button" class="btn btn-primary" onclick="loadNSReport()"><i data-feather="activity"></i> Run Report</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Chart -->
    <div class="col-lg-12" id="nsChartContainer" style="display:none;">
      <div class="card">
        <div class="card-header"><h4>No‑Show & Cancellation Rates</h4></div>
        <div class="card-body" style="overflow-x: auto;"><div id="nsStatusChart" style="min-width:600px; height:350px;"></div></div>
      </div>
    </div>
    <!-- Table -->
    <div class="col-lg-12" id="nsTableContainer" style="display:none;">
      <div class="card">
        <div class="card-header"><h4>Detailed Rates by Demographics & Service</h4></div>
        <div class="card-body table-responsive">
          <table id="nsReportTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Gender</th>
                <th>Age Group</th>
                <th>Service</th>
                <th>Total Appointments</th>
                <th>No‑Shows</th>
                <th>Cancellations</th>
                <th>No‑Show Rate</th>
                <th>Cancellation Rate</th>
                <th>Avg Lead Time (days)</th>
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
$(document).ready(function(){
  $('.select2').select2({ width:'100%' });
  loadNSReport();
});
function loadNSReport(){
  const data = {
    fromDate: $('#fromDateNS').val(),
    toDate:   $('#toDateNS').val(),
    gender:   $('#gender').val(),
    ageGroup: $('#ageGroup').val(),
    service:  $('#serviceNS').val(),
    doctor:   $('#doctorNS').val()
  };
  $.ajax({
    url: 'ajax/no_show_report/report_data.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    success: function(resp){
      if(!resp || !resp.tableData){
        iziToast.info({ title:'No data', message:'No records found for the selected criteria.' });
        return;
      }
      $('#nsChartContainer').show();
      $('#nsTableContainer').show();
      renderNSChart(resp.chartSeries);
      populateNSTable(resp.tableData);
    },
    error: function(){
      iziToast.error({ title:'Error', message:'Failed to fetch report.' });
    }
  });
}
function renderNSChart(series){
  if(window.nsChart){ window.nsChart.destroy(); }
  const options = {
    chart:{ type:'bar', height:350 },
    series: series,
    xaxis:{ categories:['No‑Show Rate','Cancellation Rate'], scrollbar: {enabled: true} },
    colors:['#DC3545','#FFC107'],
    dataLabels:{ enabled:true, formatter: val => (val*100).toFixed(1)+'%' },
    plotOptions:{ bar:{ horizontal:false, columnWidth:'40%' } },
    legend:{ show:false }
  };
  window.nsChart = new ApexCharts(document.querySelector('#nsStatusChart'), options);
  window.nsChart.render();
}
function populateNSTable(rows){
  $('#nsReportTable').DataTable({
    destroy:true,
    data: rows,
    columns:[
      { data:'gender' },
      { data:'ageGroup' },
      { data:'service' },
      { data:'total' },
      { data:'noShow' },
      { data:'cancelled' },
      { data:'noShowRate', render: data => (data*100).toFixed(1)+'%' },
      { data:'cancellationRate', render: data => (data*100).toFixed(1)+'%' },
      { data:'avgLeadTime', render: data => data.toFixed(1) }
    ]
  });
}
</script>
