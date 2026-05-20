<?php require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>OP Lab Test Utilisation</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></div>
        <div class="breadcrumb-item active">Reports</div>
      </div>
    </div>

    <!-- Hero / intro strip -->
    <div class="row">
      <div class="col-12">
        <div class="alert alert-light shadow-sm d-flex align-items-center" role="alert">
          <i data-feather="bar-chart-2" class="me-2"></i>
          <div>
            Lists the number and type of lab tests ordered for out-patients.
            Use the filters; export when you’re ready.
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Filters</h4>
        <button class="btn btn-outline-secondary btn-sm" id="btnReset">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Date range</label>
            <input id="f_daterange" class="form-control" placeholder="YYYY-MM-DD - YYYY-MM-DD"/>
          </div>
<!-- 
          <div class="col-md-3">
            <label class="form-label">Category</label> -->
            <!-- Choices/Select2 will enhance this; will be loaded by JS via categories API later -->
            <!-- <select id="f_category" class="form-control" data-allow-clear="true">
              <option value="">All</option>
            </select>
          </div> -->
          <div class="col-md-3">
            <label class="form-label">Doctor</label>
            <select id="f_doctor" class="form-control" data-allow-clear="true">
              <option value="">All doctors</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Test name</label>
            <select id="f_test_search" class="form-control" data-allow-clear="true">
              <option value="">All tests</option>
            </select>
          </div>
        </div>

        <!-- quick chips -->
        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
          <span class="badge bg-light text-dark pointer quick-range" data-range="today" style="cursor: pointer;">Today</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="7d" style="cursor: pointer;">Last 7 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="30d" style="cursor: pointer;">Last 30 days</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="qtd" style="cursor: pointer;">QTD</span>
          <span class="badge bg-light text-dark pointer quick-range" data-range="ytd" style="cursor: pointer;">YTD</span>
       

        
          <button id="btnApply" class="btn btn-primary ms-auto">
            <i class="fas fa-filter"></i> Apply
          </button>
          <button id="btnExport" class="btn btn-outline-secondary">
            <i class="fas fa-download"></i> Export CSV
          </button>
        
          <!-- fun extra: saved view -->
          <!-- <div class="ms-auto d-flex gap-2">
            <input id="f_view_name" class="form-control form-control-sm" placeholder="Save view as…">
            <button id="btnSaveView" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-bookmark"></i> Save View
            </button>
          </div> -->
      
          </div>
      </div>
    </div>

    <!-- Results -->
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0">Results</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="utilTable" class="table table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>Test</th>
                <!-- <th>Category</th> -->
                <th class="text-end">Orders</th>
              </tr>
            </thead>
            <tbody>
              <!-- Will be filled by JS / DataTables -->
            </tbody>
          </table>
        </div>
        <!-- Empty state -->
        <div id="emptyState" class="text-center py-5 d-none">
          <img src="assets/img/dosage.jpeg" alt="" style="max-width:180px" class="mb-3">
          <div class="text-muted">No data for the selected filters. Try widening your date range.</div>
        </div>
      </div>
    </div>

    <!-- Offcanvas for column settings (optional flourish) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="colSettings" aria-labelledby="colSettingsLabel">
      <div class="offcanvas-header">
        <h5 id="colSettingsLabel">Table Settings</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="0" id="col0" checked>
          <label class="form-check-label" for="col0">Test</label>
        </div>
        <!-- <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="1" id="col1" checked>
          <label class="form-check-label" for="col1">Category</label>
        </div> -->
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="2" id="col2" checked>
          <label class="form-check-label" for="col2">Orders</label>
        </div>

        <!-- Added Close button -->
        <div class="mt-3 text-end">
          <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="offcanvas">
            Close
          </button>
        </div>
      </div>
    </div>

    <div class="text-end mt-3">
      <button class="btn btn-outline-dark btn-sm" data-bs-toggle="offcanvas" data-bs-target="#colSettings">
        <i class="fas fa-columns"></i> Columns
      </button>
    </div>

  </section>
</div>
<?php require_once("ajax/footer.php") ?>
<script src="./assets/js/page/reports/op_lab_util.js"></script>

