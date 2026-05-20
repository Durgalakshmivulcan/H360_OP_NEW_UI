<?php require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>

<style>
  .pointer {
    cursor: pointer;
  }

  div.card-icon svg {
    vertical-align: unset !important;
  }
</style>

<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>OP Diagnosis &amp; Prescription Patterns</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="/dashboard.php">Dashboard</a></div>
        <div class="breadcrumb-item active">Reports</div>
      </div>
    </div>

    <!-- KPI tiles row -->
    <div class="row">
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i data-feather="trending-up"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Scripts</h4>
            </div>
            <div class="card-body" id="kpi_scripts">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i data-feather="activity"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Unique Drugs</h4>
            </div>
            <div class="card-body" id="kpi_drugs">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"><i data-feather="file-text"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Top Diagnosis</h4>
            </div>
            <div class="card-body" id="kpi_topdx">&mdash;</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i data-feather="alert-triangle"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Overuse Flags</h4>
            </div>
            <div class="card-body" id="kpi_flags">0</div>
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
        <div class="row">
          <?php
          if ($SessionUserId == "1" && $SessionRoleId == "1") {
          ?>
            <div class="form-group col-lg-3 col-sm-12">
              <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
              <select class="form-control form-select" name="organizations" id="organizations">
                <option value="">Select Organization</option>
                <?php
                $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                ?>
                  <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                <?php
                }
                ?>
              </select>
            </div>
          <?php
          } else {
          ?>
            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
          <?php
          }
          ?>
        </div>
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Date range</label>
            <input id="f_daterange" class="form-control" placeholder="YYYY-MM-DD - YYYY-MM-DD" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Diagnosis (ICD code / text)</label>
            <select id="f_dx" class="form-control" placeholder="e.g. E11, T2DM, HTN">
              <option value="">Select Diagnosis</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Drug class</label>
            <select id="f_class" class="form-control" data-allow-clear="true">
              <option value="">All</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Doctor (optional)</label>
            <select id="f_doctor" class="form-control" placeholder="Doctor ID">
              <option value="">Select Doctor</option>
            </select>
          </div>
        </div>
        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
          <span class="badge bg-light text-dark pointer quick-range" data-range="today" style="cursor: pointer;">Today</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="7d" style="cursor: pointer;">Last 7 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="30d" style="cursor: pointer;">Last 30 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="qtd" style="cursor: pointer;">QTD</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="ytd" style="cursor: pointer;">YTD</span>
          <button id="btnApply" class="btn btn-primary ms-auto"><i class="fas fa-filter"></i> Apply</button>
          <button id="btnExport" class="btn btn-outline-secondary"><i class="fas fa-download"></i> Export CSV</button>
        </div>
      </div>
    </div>

    <!-- Charts row -->
    <div class="row">
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>Scripts Trend</h4>
          </div>
          <div class="card-body">
            <canvas id="chartTrend" height="220"></canvas>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-12">
        <div class="card">
          <div class="card-header">
            <h4>By Drug Class</h4>
          </div>
          <div class="card-body">
            <canvas id="chartClass" height="220"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Results table card -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Diagnosis → Top Drugs</h4>

      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="rxTable" class="table table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>Diagnosis</th>
                <!-- <th>ICD</th> -->
                <th>Drug</th>
                <!-- <th>Class</th> -->
                <th class="text-end">Scripts</th>
                <th class="text-end">Patients</th>
                <th class="text-end">Share %</th>
                <th>Flag</th>
              </tr>
            </thead>
          </table>
        </div>
        <div id="emptyState" class="text-center py-5 d-none">
          <i data-feather="file-text" style="font-size:3rem;" class="text-muted mb-3"></i>
          <div class="text-muted">No patterns in the selected period. Try changing filters.</div>
        </div>
      </div>
    </div>

    <!-- Detail modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <pre class="bg-dark text-light p-3 rounded small" id="json_detail">{}</pre>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>



  </section>
</div>
<?php require_once("ajax/footer.php") ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  $(document).ready(function() {
    // --- Date Range Picker ---
    var $drp = $('#f_daterange');
    var supportsDRP = typeof $.fn.daterangepicker !== 'undefined';
    if (supportsDRP) {
      $drp.daterangepicker({
        autoUpdateInput: true,
        startDate: moment().subtract(6, "days"),
        endDate: moment(),
        locale: {
          format: "YYYY-MM-DD"
        }
      });
    } else {
      $drp.val(
        moment().subtract(6, "days").format("YYYY-MM-DD") +
        " - " +
        moment().format("YYYY-MM-DD")
      );
    }
    var flagThreshold = 2;
    // --- Quick Range Badges ---
    $('.quick-range').click(function() {
      var range = $(this).data('range');
      var start, end = moment();
      switch (range) {
        case 'today':
          start = end;
          break;
        case '7d':
          start = moment().subtract(6, 'days');
          break;
        case '30d':
          start = moment().subtract(29, 'days');
          break;
        case 'qtd':
          start = moment().startOf('quarter');
          break;
        case 'ytd':
          start = moment().startOf('year');
          break;
        default:
          start = moment().subtract(6, 'days');
      }
      $drp.val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
      if (supportsDRP) {
        $drp.data('daterangepicker').setStartDate(start);
        $drp.data('daterangepicker').setEndDate(end);
      }
    });

    // --- Populate Dropdowns ---
    $.getJSON('ajax/oprxpatters/opfeteching.php', function(data) {
      // Diagnosis
      var $dx = $('#f_dx').empty().append('<option value="">Select Diagnosis</option>');
      var dxSet = {};
      (data.top_diagnoses || []).forEach(row => {
        if (row.diagnosis) dxSet[row.diagnosis] = true;
      });
      Object.keys(dxSet).sort().forEach(d =>
        $dx.append($('<option>', {
          value: d,
          text: d
        }))
      );

      // Drug Name
      var $class = $('#f_class').empty().append('<option value="">All</option>');
      var drugSet = {};
      (data.drug_usage || []).forEach(row => {
        if (row.drug_name) drugSet[row.drug_name] = true;
      });
      Object.keys(drugSet).sort().forEach(drug =>
        $class.append($('<option>', {
          value: drug,
          text: drug
        }))
      );

      // Doctor
      var $doctor = $('#f_doctor').empty().append('<option value="">Select Doctor</option>');
      (data.doctorArr || []).forEach(doc => {
        $doctor.append($('<option>', {
          value: doc.doc_id,
          text: doc.doctor_name + ' (' + doc.doc_id + ')'
        }));
      });

      //  Initialize Select2 after options are populated
      $('#f_dx, #f_class, #f_doctor').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: "100%"
      });
    });

    // --- Charts Instances ---
    var chartClass = null;
    var chartTrend = null;

    // --- Chart Renderers ---
    function renderDrugClassChart(drugUsage) {
      var drugCounts = {};
      (drugUsage || []).forEach(row => {
        if (row.drug_name) drugCounts[row.drug_name] = (drugCounts[row.drug_name] || 0) + row.scripts;
      });
      var labels = Object.keys(drugCounts);
      var data = labels.map(name => drugCounts[name]);
      if (chartClass) chartClass.destroy();
      chartClass = new Chart(document.getElementById('chartClass').getContext('2d'), {
        type: 'bar',
        data: {
          labels: labels.length ? labels : ['No Data'],
          datasets: [{
            label: 'Scripts',
            data: data.length ? data : [0],
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
          }]
        },
        options: {
          indexAxis: 'y',
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              beginAtZero: true
            }
          }
        }
      });
    }

    function renderTrendChart(trendArr) {
      var labels = (trendArr || []).map(row => row.date);
      var data = (trendArr || []).map(row => row.scripts);
      if (chartTrend) chartTrend.destroy();
      chartTrend = new Chart(document.getElementById('chartTrend').getContext('2d'), {
        type: 'line',
        data: {
          labels: labels.length ? labels : ['No Data'],
          datasets: [{
            label: 'Scripts',
            data: data.length ? data : [0],
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    // --- DataTable Initialization ---
    var rxTable = $('#rxTable').DataTable({
      ajax: {
        url: 'ajax/oprxpatters/opfeteching.php',
        type: 'GET',
        data: function(d) {
          var daterange = $('#f_daterange').val();
          if (daterange && daterange.includes(' - ')) {
            var parts = daterange.split(' - ');
            d.from = parts[0];
            d.to = parts[1];
          }
          d.dx = $('#f_dx').val();
          d.class = $('#f_class').val();
          d.doctor = $('#f_doctor').val();
          d.orgId = $('#organizations').val(); // <-- Add this line
        },
        dataSrc: function(json) {
          // Update KPIs
          $('#kpi_scripts').text(json.meta?.total_scripts ?? 0);
          $('#kpi_drugs').text(json.meta?.unique_drug_count ?? 0);
          $('#kpi_topdx').text(json.top_diagnoses?.[0]?.diagnosis ?? '—');
          $('#kpi_flags').text((json.drug_usage || []).filter(r => r.flag).length);
          console.log('KPI Flags:', (json.drug_usage || []).filter(r => r.flag).length);

          // Empty state
          $('#emptyState').toggleClass('d-none', !!(json.drug_usage && json.drug_usage.length));
          return (json.drug_usage || []).map(row => ({
            diagnosis: row.dx_name,
            // icd: row.icd_code || '',
            drug: row.drug_name,
            // class: row.drug_class || '',
            scripts: row.scripts,
            patients: row.patients,
            share: row.share_pct,
            // flag: row.flag || '',
            flag_number: Number(row.flag ?? row.flag_number ?? 0),
          }));
        }
      },
      dom: 'Bfrtip', // Show buttons at the top
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "All"]
      ],
      buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],

      columns: [{
          data: 'diagnosis'
        },
        // { data: 'icd' },
        {
          data: 'drug'
        },
        // { data: 'class' },
        {
          data: 'scripts',
          className: 'text-end'
        },
        {
          data: 'patients',
          className: 'text-end'
        },
        {
          data: 'share',
          className: 'text-end'
        },
        {
          data: 'flag_number',
          className: 'text-end',
          render: function(data, type, row) {
            if (data === undefined || data === null) return '';

            // Compare against the threshold
            if (data > flagThreshold) {
              // Greater than threshold → green with '>'
              return `<span style="color:green; font-weight:bold; text-align:center;">>${flagThreshold}</span>`;
            } else {
              // Less than or equal → orange with '<'
              return `<span style="color:orange; font-weight:bold; text-align:center;"><${flagThreshold}</span>`;
            }
          }
        }


      ],
      searching: true,
      paging: true,
      ordering: true,
      info: true
    });

    // --- Charts Update on Data Load ---
    $('#rxTable').on('xhr.dt', function(e, settings, json) {
      renderDrugClassChart(json.drug_usage);
      renderTrendChart(json.script_trend);
    });

    // --- Filter Actions ---
    $('#btnApply').click(function() {
      rxTable.ajax.reload();
    });
    $('#btnReset').click(function() {
      $('#f_daterange').val('');
      $('#f_dx').val('').trigger('change');
      $('#f_class').val('').trigger('change');
      $('#f_doctor').val('').trigger('change');
      rxTable.ajax.reload();
    });
  });
</script>