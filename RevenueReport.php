<?php
/*
 * Revenue Report
 *
 * This report displays gross revenue, discounts, taxes and net revenue broken down
 * by service category and doctor for a selected date range.  Users can also
 * choose the granularity (day, week or month) to see how revenue trends over
 * time.  The summary cards at the top provide totals across all data,
 * followed by an interactive chart and a detailed table.  Data is fetched
 * asynchronously via AJAX from `ajax/revenue_report/report_data.php`.
 */

require_once('ajax/header.php');
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>

<style>
  .apexcharts-menu-icon {
    display: none;
  }
</style>

<div class="main-content">
  <ul class="breadcrumb breadcrumb-style">
    <li class="breadcrumb-item">
      <h4 class="page-title m-b-0">Reports</h4>
    </li>
    <li class="breadcrumb-item active">Revenue by Service/Doctor/Date</li>
  </ul>

  <!-- Filters -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Filters</h4>
        </div>
        <div class="card-body">
          <form id="revenueFilterForm" class="row g-3">
            <div class="col-md-2">
              <label for="revFromDate" class="form-label">From</label>
              <input type="date" id="revFromDate" name="fromDate" class="form-control" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-md-2">
              <label for="revToDate" class="form-label">To</label>
              <input type="date" id="revToDate" name="toDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
              <label for="revDoctor" class="form-label">Doctor</label>
              <select id="revDoctor" name="doctor" class="form-select select2">
                <option value="">All Doctors</option>
                <?php
                $checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
                $securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

                // SA_FATAL_FIXED_B_541: include SA so $sql is defined for super-admin
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
              <label for="revService" class="form-label">Service Category</label>
              <select id="revService" name="service" class="form-select select2">
                <option value="">All Services</option>
                <?php
                $catRes = mysqli_query($conn, "SELECT DISTINCT bill_type FROM invoice WHERE bill_type <> ''");
                while ($cat = mysqli_fetch_object($catRes)) {
                  $val = htmlspecialchars($cat->bill_type);
                  echo '<option value="' . $val . '">' . $val . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-2">
              <label for="revGroupBy" class="form-label">Group By</label>
              <select id="revGroupBy" name="groupBy" class="form-select">
                <option value="day">Daily</option>
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
              </select>
            </div>
            <div class="col-12">
              <button type="button" class="btn btn-primary mt-3" id="runRevenueReport">Run Report</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row mb-4" id="revenueSummary" style="display:none;">
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center bg-primary text-white">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Gross Revenue</h6>
          <h4 id="grossTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center bg-warning text-white">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Total Discount</h6>
          <h4 id="discountTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center bg-info text-white">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Total Tax</h6>
          <h4 id="taxTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center bg-success text-white">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Active Net Revenue</h6>
          <h4 id="netTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center text-white" style="background:#dc3545;">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Refunded / Cancelled</h6>
          <h4 id="refundedTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-6 mb-3">
      <div class="card text-center text-white" style="background:#155724;">
        <div class="card-body py-3">
          <h6 class="mb-1" style="font-size:12px;">Effective Net Revenue</h6>
          <h4 id="effectiveNetTotal" class="mb-0">0</h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="row mb-4" id="revenueChartRow" style="display:none;">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Revenue Breakdown</h4>
        </div>
        <div class="card-body">
          <div id="revenueChart" style="height:400px;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="row" id="revenueTableRow" style="display:none;">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4>Detailed Revenue</h4>
        </div>
        <div class="card-body table-responsive">
          <table id="revenueTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Date Group</th>
                <th>Doctor</th>
                <th>Service</th>
                <th>Gross (₹)</th>
                <th>Discount (₹)</th>
                <th>Tax (₹)</th>
                <th>Net (₹)</th>
                <th>Status</th>
                <th>Refund Type</th>
                <th>Returned (₹)</th>
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
  var revenueChart = null;

  function renderRevenueChart(categories, grossSeries, discountSeries, taxSeries, netSeries, refundedSeries) {
    if (revenueChart) { revenueChart.destroy(); }
    revenueChart = new ApexCharts(document.querySelector('#revenueChart'), {
      chart: { type: 'bar', stacked: false, height: 400 },
      plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
      xaxis: { categories: categories },
      yaxis: { title: { text: 'Amount (₹)' } },
      tooltip: { y: { formatter: function(val) { return '₹' + val.toFixed(2); } } },
      legend: { position: 'top' },
      colors: ['#1E88E5', '#FFC107', '#00ACC1', '#43A047', '#dc3545'],
      series: [
        { name: 'Gross',              data: grossSeries    },
        { name: 'Discount',           data: discountSeries },
        { name: 'Tax',                data: taxSeries      },
        { name: 'Active Net',         data: netSeries      },
        { name: 'Refunded/Cancelled', data: refundedSeries }
      ]
    });
    revenueChart.render();
  }

  function loadRevenueReport() {
    var data = {
      fromDate: $('#revFromDate').val(),
      toDate:   $('#revToDate').val(),
      doctor:   $('#revDoctor').val(),
      service:  $('#revService').val(),
      groupBy:  $('#revGroupBy').val()
    };
    $.ajax({
      url: 'ajax/revenue_report/report_data.php',
      type: 'POST', data: data, dataType: 'json',
      success: function(res) {
        $('#revenueSummary').show();
        $('#revenueChartRow').show();
        $('#revenueTableRow').show();

        $('#grossTotal').text('₹'       + parseFloat(res.totals.gross        || 0).toFixed(2));
        $('#discountTotal').text('₹'    + parseFloat(res.totals.discount     || 0).toFixed(2));
        $('#taxTotal').text('₹'         + parseFloat(res.totals.tax          || 0).toFixed(2));
        $('#netTotal').text('₹'         + parseFloat(res.totals.net          || 0).toFixed(2));
        $('#refundedTotal').text('₹'    + parseFloat(res.totals.refunded     || 0).toFixed(2));
        $('#effectiveNetTotal').text('₹'+ parseFloat(res.totals.effective_net|| 0).toFixed(2));

        renderRevenueChart(
          res.chart.categories,
          res.chart.gross,
          res.chart.discount,
          res.chart.tax,
          res.chart.net,
          res.chart.refunded
        );

        if ($.fn.DataTable.isDataTable('#revenueTable')) {
          $('#revenueTable').DataTable().destroy();
        }
        $('#revenueTable').DataTable({
          data: res.table,
          columns: [
            { data: 'date_group' },
            { data: 'doctor_name' },
            { data: 'service' },
            { data: 'gross',    render: d => d > 0 ? '₹' + parseFloat(d).toFixed(2) : '-' },
            { data: 'discount', render: d => d > 0 ? '₹' + parseFloat(d).toFixed(2) : '-' },
            { data: 'tax',      render: d => d > 0 ? '₹' + parseFloat(d).toFixed(2) : '-' },
            { data: 'net',      render: d => d > 0 ? '₹' + parseFloat(d).toFixed(2) : '-' },
            { data: 'status',   render: function(d) {
                var cfg = { Active: ['#198754','Active'], Cancelled: ['#dc3545','Cancelled'], Refunded: ['#fd7e14','Refunded'] };
                var c = cfg[d] || ['#6c757d', d];
                return '<span style="background:' + c[0] + ';color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;">' + c[1] + '</span>';
            }},
            { data: 'refund_type', render: function(d) {
                if (!d || d === '-') return '<span class="text-muted">-</span>';
                var col = d === 'refund' ? '#fd7e14' : '#dc3545';
                var lbl = d === 'refund' ? 'Partial Refund' : 'Full Cancel';
                return '<span style="background:' + col + ';color:#fff;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">' + lbl + '</span>';
            }},
            { data: 'refund_amount', render: d => parseFloat(d) > 0 ? '<strong style="color:#dc3545;">₹' + parseFloat(d).toFixed(2) + '</strong>' : '-' }
          ],
          dom: 'lBrftip',
          buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
          pageLength: 10,
          order: [[0, 'asc']]
        });
      },
      error: function() {
        iziToast.error({ title: 'Error', message: 'Failed to load revenue data' });
      }
    });
  }

  $(document).ready(function() {
    $('#runRevenueReport').on('click', function() { loadRevenueReport(); });
    loadRevenueReport();
  });
</script>
