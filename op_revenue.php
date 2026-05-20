<?php


require_once("ajax/header.php");
?>

<div class="main-content">
  <section class="section">
    <!-- Page header -->
    <div class="section-header">
      <h1>OP Revenue &amp; Invoices Report</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></div>
        <div class="breadcrumb-item active">Reports</div>
      </div>
    </div>

    <!-- Hero strip -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="alert alert-light shadow-sm d-flex align-items-center" role="alert">
          <i data-feather="dollar-sign" class="me-2"></i>
          <div>
            Monitor your clinic’s revenue at a glance.  See gross, discount, tax
            and net amounts by date, doctor, service and payer.  Apply filters
            to drill down and export your data when you’re ready.
          </div>
        </div>
      </div>
    </div>

    <!-- KPI tiles -->
    <div class="row">
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i data-feather="bar-chart-2"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Total Gross</h4></div>
            <div class="card-body" id="kpi_gross">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i data-feather="gift"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Discounts</h4></div>
            <div class="card-body" id="kpi_discount">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info"><i data-feather="percent"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Tax</h4></div>
            <div class="card-body" id="kpi_tax">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i data-feather="trending-up"></i></div>
          <div class="card-wrap">
            <div class="card-header"><h4>Net Revenue</h4></div>
            <div class="card-body" id="kpi_net">0</div>
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
            <label class="form-label">Service</label>
            <select id="f_service" class="form-control" data-allow-clear="true">
              <option value="">All</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Doctor</label>
            <input id="f_doctor" type="number" class="form-control" placeholder="Doctor ID"/>
          </div>
          <div class="col-md-2">
            <label class="form-label">Payer</label>
            <select id="f_payer" class="form-control" data-allow-clear="true">
              <option value="">All</option>
              <option value="self">Self pay</option>
              <option value="insurance">Insurance</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Search</label>
            <input id="f_search" class="form-control" placeholder="Search description"/>
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

    <!-- Charts row -->
    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><h4>Revenue Trend</h4></div>
          <div class="card-body"><canvas id="chartTrend" height="110"></canvas></div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h4>Composition</h4></div>
          <div class="card-body"><canvas id="chartComp" height="220"></canvas></div>
        </div>
      </div>
    </div>

    <!-- Table and empty state -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Revenue Details</h4>
        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="offcanvas" data-bs-target="#colSettings"><i class="fas fa-columns"></i> Columns</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="revTable" class="table table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>Date</th>
                <th>Service</th>
                <th>Doctor</th>
                <th>Payer</th>
                <th class="text-end">Invoices</th>
                <th class="text-end">Gross</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax</th>
                <th class="text-end">Net</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <div id="emptyState" class="text-center py-5 d-none">
          <img src="./assets/img/empty-state.svg" style="max-width:180px" class="mb-3" alt="">
          <div class="text-muted">No revenue data for the selected filters. Try widening your search.</div>
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
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="0" id="col0" checked>
          <label class="form-check-label" for="col0">Date</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="1" id="col1" checked>
          <label class="form-check-label" for="col1">Service</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="2" id="col2" checked>
          <label class="form-check-label" for="col2">Doctor</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="3" id="col3" checked>
          <label class="form-check-label" for="col3">Payer</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="4" id="col4" checked>
          <label class="form-check-label" for="col4">Invoices</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="5" id="col5" checked>
          <label class="form-check-label" for="col5">Gross</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="6" id="col6" checked>
          <label class="form-check-label" for="col6">Discount</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="7" id="col7" checked>
          <label class="form-check-label" for="col7">Tax</label>
        </div>
        <div class="form-check">
          <input class="form-check-input col-toggle" type="checkbox" value="8" id="col8" checked>
          <label class="form-check-label" for="col8">Net</label>
        </div>
      </div>
    </div>

  </section>
</div>

<?php require_once 'ajax/footer.php'; ?>
<!-- Page specific JS -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./assets/js/page/reports/op_revenue.js"></script>