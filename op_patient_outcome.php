<?php
require_once("./ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>
<style>
img, svg {
    vertical-align: unset;
}
.badge{
  padding: 12px 12px;
}
</style>
<div class="main-content">
  <section class="section">
    <!-- Section header -->
    <div class="section-header">
      <h1>OP Patient Outcomes &amp; Follow‑Up</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></div>
        <div class="breadcrumb-item active">Reports</div>
      </div>
    </div>

    <!-- Hero strip -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="alert alert-light shadow-sm d-flex align-items-center" role="alert">
          <i data-feather="heart" class="me-2"></i>
          <div>
            Track your patients’ progress over time.  Compare baseline and recent
            vital measurements, identify improvements or declines, and spot
            patients who miss scheduled follow‑ups.  Apply filters to hone in
            on specific date ranges or doctors and export the results for
            further analysis.
          </div>
        </div>
      </div>
    </div>

    <!-- KPI tiles -->
    <div class="row">
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i data-feather="users"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Total Patients</h4></div>
            <div class="card-body" id="kpi_total">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i data-feather="arrow-down"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Improved</h4></div>
            <div class="card-body" id="kpi_improved">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger"><i data-feather="arrow-up"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Declined</h4></div>
            <div class="card-body" id="kpi_declined">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning" style="text-align: center; vertical-align: middle;"><i data-feather="clock"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Missed Follow‑Ups</h4></div>
            <div class="card-body" id="kpi_missed">0</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters card -->
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="mb-0">Filters</h4>
        <button class="btn btn-outline-secondary btn-sm" id="btnReset"><i class="fas fa-undo"></i> Reset</button>
      </div>
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Date range</label>
            <input id="f_daterange" class="form-control" placeholder="YYYY-MM-DD - YYYY-MM-DD"/>
          </div>
          <div class="col-md-3">
            <label class="form-label">Doctor</label>
            <select id="f_doctor" class="form-control" data-allow-clear="true">
              <option value="">All</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Search</label>
            <input id="f_search" class="form-control" placeholder="Patient name or MRN"/>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <span class="badge bg-light text-dark pointer quick-range" data-range="today">Today</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="7d">Last 7 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="30d">Last 30 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="qtd">QTD</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="ytd">YTD</span>
          <div class="ms-auto d-flex gap-2">
            <button id="btnApply" class="btn btn-primary"><i class="fas fa-filter"></i> Apply</button>
            <button id="btnExport" class="btn btn-outline-secondary"><i class="fas fa-download"></i> Export CSV</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-lg-8 col-sm-12">
        <div class="card">
          <div class="card-header"><h4>Outcome Trend</h4></div>
          <div class="card-body"><canvas id="chartTrend" height="150"></canvas></div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h4>Outcome Composition</h4></div>
          <div class="card-body"><canvas id="chartComp" height="220"></canvas></div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Patient Outcomes</h4>
        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="offcanvas" data-bs-target="#colSettings"><i class="fas fa-columns"></i> Columns</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="outcomeTable" class="table table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Appointment Date</th>
                <th>Baseline BP</th>
                <th>Last Date</th>
                <th>Last BP</th>
                <!-- <th>Diff</th>
                <th>Status</th> -->
                <th>Next Follow‑Up</th>
                <th>Missed</th>
              </tr>
            </thead>
          </table>
        </div>
        <div id="emptyState" class="text-center py-5 d-none">
          <img src="/assets/img/empty-state.svg" style="max-width:180px" class="mb-3" alt="">
          <div class="text-muted">No outcome data for the selected filters.  Try expanding your date range.</div>
        </div>
      </div>
    </div>

    <!-- Offcanvas: column settings -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="colSettings" aria-labelledby="colSettingsLabel">
      <div class="offcanvas-header">
        <h5 id="colSettingsLabel">Table Settings</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <?php for ($i = 0; $i < 7; $i++): ?>
          <div class="form-check">
            <input class="form-check-input col-toggle" type="checkbox" value="<?= $i ?>" id="col<?= $i ?>" checked>
            <label class="form-check-label" for="col<?= $i ?>">
              <?php
              // Dynamically label columns to match the table header order
              switch ($i) {
                case 0: echo 'Patient'; break;
                case 1: echo 'Baseline Date'; break;
                case 2: echo 'Baseline BP'; break;
                case 3: echo 'Last Date'; break;
                case 4: echo 'Last BP'; break;
                // case 5: echo 'Diff'; break;
                // case 6: echo 'Status'; break;
                case 5: echo 'Next Follow‑Up'; break;
                case 6: echo 'Missed'; break;
              }
              ?>
            </label>
          </div>
        <?php endfor; ?>

        <div class="mt-3 text-end">
          <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="offcanvas">
            Close
          </button>
        </div>

      </div>
    </div>

  </section>
</div>

<?php require_once './ajax/footer.php'; ?>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

  
$(function(){
  // Date range picker setup
  const $drp = $('#f_daterange');
  const supportsDRP = !!$.fn.daterangepicker;
  if (supportsDRP) {
    $drp.daterangepicker({
      autoUpdateInput: true,
      startDate: moment().subtract(29,'days'),
      endDate: moment(),
      locale: { format:'YYYY-MM-DD' }
    });
  } else {
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') + ' - ' +
      moment().format('YYYY-MM-DD')
    );
  }
  function parseRange(str) {
    const parts = (str || '').split(' - ');
    return { from: parts[0] || '', to: parts[1] || '' };
  }

  // Populate doctor select via AJAX and enhance with Choices/Select2
      const $docSelect = $('#f_doctor');

      $.ajax({
        url: './ajax/PatientsOutcomeReport/op_patient_outcome_doctors.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'doctors' }, // 👈 required
        success: function(resp) {
          let doctors = resp.doctors || [];
          
          doctors.forEach(function(item){
            if (item && item.doc_id !== undefined) {
              $docSelect.append($('<option/>',{ value:item.doc_id, text:item.doctor_name }));
            }
          });

          if (window.Choices) {
            new Choices('#f_doctor',{ allowHTML:false, removeItemButton:true, searchPlaceholderValue:'Search doctor' });
          }
          if ($.fn.select2 && !$docSelect.data('choices')) {
            $docSelect.select2({ placeholder:'All doctors', allowClear:true, width:'100%' });
          }

          console.log('Doctor list loaded:', doctors);
        },
        error: function(xhr, status, err) {
          console.error("Doctor fetch error:", status, err, xhr.responseText);
        }
      });



  // Initialise charts
  const trendCtx = document.getElementById('chartTrend').getContext('2d');
  const compCtx  = document.getElementById('chartComp').getContext('2d');
  const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: { labels: [], datasets: [
      { label:'Improved', data: [], borderWidth:5, fill:false, borderColor:'#47c363' },
      { label:'Declined', data: [], borderWidth:5, fill:false, borderColor:'#fc544b' },
      { label:'Same', data: [], borderWidth:5, fill:false, borderColor:'#6777ef' },
      { label:'No Data', data: [], borderWidth:5, fill:false, borderColor:'#ffa426' }
    ] },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ position:'bottom' } },
      scales:{ x:{ grid:{ display:false } }, y:{ beginAtZero:true } }
    }
  });
  const compChart = new Chart(compCtx, {
    type: 'doughnut',
    data: { labels: [], datasets: [ { data: [], backgroundColor:['#47c363','#fc544b','#6777ef','#ffa426'] } ] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } }
  });

  // DataTable configuration
  const $tbl = $('#outcomeTable');
  const dt = $tbl.DataTable({
    serverSide: true,
    processing: true,
    searching: true,
    ordering: true,
    ajax: function(data, callback) {
      const range = parseRange($drp.val());
      const params = {
        start: data.start,
        length: data.length,
        search: { value: data.search.value },
        from: range.from,
        to: range.to,
        doctor: $('#f_doctor').val() || '',
        search_text: $('#f_search').val() || ''
      };
      $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_export.php', params, function(resp){
        callback({
          recordsTotal: resp.recordsTotal,
          recordsFiltered: resp.recordsFiltered,
          data: resp.data
        });
        toggleEmpty(resp.data.length === 0);
      });
    },
    columns: [
      { data:'patient' },
      { data:'baseline_date' },
      { data:'baseline_bp', className:'text-end' },
      { data:'last_date' },
      { data:'last_bp', className:'text-end' },
      { data:'next_follow', render: function(v){ return v || '—'; } },
     { 
        data:'missed', 
        className:'text-center', 
        render: function(v){ 
          if (v === 'Yes') {
            return '<i class="fas fa-times-circle text-danger"></i>'; // missed
          } else {
            return '<i class="fas fa-check-circle text-success"></i>'; // not missed
          }
        } 
  }

    ],
    order: [[3,'desc']],
    language: { emptyTable:'No data for the selected filters' }
  });

  // Column toggles
  $('.col-toggle').on('change', function(){
    dt.column(parseInt(this.value, 10)).visible(this.checked);
  });

  // Toggle empty state visibility
  function toggleEmpty(isEmpty) {
    $('#emptyState').toggleClass('d-none', !isEmpty);
    $('.table-responsive').toggleClass('d-none', isEmpty);
  }
  toggleEmpty(true);

  // Quick range chips
  $('.quick-range').on('click', function(){
    const key = $(this).data('range');
    let from = moment();
    let to   = moment();
    if (key === '7d') from = moment().subtract(6,'days');
    else if (key === '30d') from = moment().subtract(29,'days');
    else if (key === 'qtd') from = moment().startOf('quarter');
    else if (key === 'ytd') from = moment().startOf('year');
    // 'today' means from = to = today
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(from);
      $drp.data('daterangepicker').setEndDate(to);
    }
    $drp.val(from.format('YYYY-MM-DD') + ' - ' + to.format('YYYY-MM-DD'));
  });

  // Apply button: load KPIs, charts and refresh table
  $('#btnApply').on('click', function(){
    loadKpis();
    loadCharts();
    dt.ajax.reload();
  });

  // Export CSV
    $('#btnExport').on('click', function(){
      const range = parseRange($drp.val());
      const params = {
        from: range.from,
        to: range.to,
        doctor: $('#f_doctor').val() || '',
        search_text: $('#f_search').val() || '',
        export: 'csv' // 👈 force CSV
      };
      const qs = $.param(params);
      window.location = './ajax/PatientsOutcomeReport/op_patient_outcomes.php?' + qs;
    });


  // Reset filters
  $('#btnReset').on('click', function(){
    if (supportsDRP) {
      $drp.data('daterangepicker').setStartDate(moment().subtract(29,'days'));
      $drp.data('daterangepicker').setEndDate(moment());
    }
    $drp.val(
      moment().subtract(29,'days').format('YYYY-MM-DD') + ' - ' +
      moment().format('YYYY-MM-DD')
    );
    $('#f_doctor').val('');
    if ($docSelect.data('select2')) $docSelect.val(null).trigger('change');
    if ($docSelect.data('choices')) $docSelect[0].tom.selectNone();
    $('#f_search').val('');
    dt.clear().draw();
    toggleEmpty(true);
    // Reset KPIs and charts
    $('#kpi_total').text('0');
    $('#kpi_improved').text('0');
    $('#kpi_declined').text('0');
    $('#kpi_missed').text('0');
    trendChart.data.labels = [];
    trendChart.data.datasets.forEach(ds => ds.data = []);
    trendChart.update();
    compChart.data.labels = [];
    compChart.data.datasets[0].data = [];
    compChart.update();
  });

  // Load KPIs via AJAX
      function loadKpis() {
        const range = parseRange($drp.val());
        const params = {
          from: range.from,
          to: range.to,
          doctor: $('#f_doctor').val() || '',
          search_text: $('#f_search').val() || ''
        };
          $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_kpis.php', params, function(resp){
        console.log('Response:', resp);
        console.log('Elements:', $('#kpi_total'), $('#kpi_same'));
        
        $('#kpi_total').text(resp.total || 0);
        $('#kpi_improved').text(resp.improved || 0);
        $('#kpi_declined').text(resp.declined || 0);
        $('#kpi_missed').text(resp.missed || 0);
        $('#kpi_same').text(resp.same || 0);
    });
  }
  loadKpis(); 
  // Load charts via AJAX
  function loadCharts() {
    const range = parseRange($drp.val());
    const params = {
      from: range.from,
      to: range.to,
      doctor: $('#f_doctor').val() || '',
      search_text: $('#f_search').val() || ''
    };
    $.getJSON('./ajax/PatientsOutcomeReport/op_patient_outcome_charts.php', params, function(resp){
      // Expect resp.trend.labels and .datasets with keys improved, declined, same, nodata
      if (resp && resp.trend) {
        trendChart.data.labels = resp.trend.labels || [];
        trendChart.data.datasets[0].data = resp.trend.improved || [];
        trendChart.data.datasets[1].data = resp.trend.declined || [];
        trendChart.data.datasets[2].data = resp.trend.same || [];
        trendChart.data.datasets[3].data = resp.trend.nodata || [];
        trendChart.update();
      }
      if (resp && resp.comp) {
        compChart.data.labels = resp.comp.labels || [];
        compChart.data.datasets[0].data = resp.comp.data || [];
        compChart.update();
      }
    });
  }
  loadCharts();
});
</script>