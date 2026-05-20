<?php
// audit_feature/pages/audit_log.php
// Front-end page for viewing audit logs using Gati elements.
require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
// Include combined header + sidebar
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
      <h1>Audit Log</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item"><a href="/dashboard.php">Dashboard</a></div>
        <div class="breadcrumb-item active">Audit Log</div>
      </div>
    </div>

    <!-- KPI summary -->
    <div class="row">
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary"><i data-feather="activity"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Logs</h4>
            </div>
            <div class="card-body" id="kpi_total">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success"><i data-feather="plus-square"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Creates</h4>
            </div>
            <div class="card-body" id="kpi_create">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning"><i data-feather="edit-2"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Updates</h4>
            </div>
            <div class="card-body" id="kpi_update">0</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger"><i data-feather="trash-2"></i></div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Deletes</h4>
            </div>
            <div class="card-body" id="kpi_delete">0</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="card-header">
        <h4>Filters</h4>
      </div>
      <div class="card-body">
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Date Range</label>
            <input id="f_daterange" class="form-control" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Module</label>
            <select id="f_module" class="form-select select2">
              <option value="">All</option>
              <?php
              $SessionUserId = $_SESSION['security_id'] ?? '';
              $SessionRoleId = $_SESSION['role_id'] ?? '';
              $SessionOrgId = $_SESSION['org_id'] ?? '';
              $query = "SELECT DISTINCT module 
                          FROM audit_log 
                          WHERE org_id = '$SessionOrgId' 
                          ORDER BY module ASC";
              $result = $conn->query($query);
              if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $module = htmlspecialchars($row['module']);
                  echo "<option value='{$module}'>{$module}</option>";
                }
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Action</label>
            <select id="f_action" class="form-select select2">
              <option value="">All</option>
              <option value="create">Create</option>
              <option value="update">Update</option>
              <option value="delete">Delete</option>
              <option value="login">Login</option>
              <option value="logout">Logout</option>
            </select>
          </div>
          <?php
          $logged_in_security_id = $_SESSION['security_id'] ?? 0;

          // Receptionist check
          $res = mysqli_query($conn, "SELECT doc_id FROM receptionnist WHERE security_id = {$logged_in_security_id}");
          $doc_ids = [];
          while ($row = mysqli_fetch_assoc($res)) {
            $doc_ids[] = (int)$row['doc_id'];
          }

          if (!empty($doc_ids)) {
            $doc_ids_sql = implode(',', array_map('intval', $doc_ids));
            $res2 = mysqli_query($conn, "SELECT doc_id, doctor_name, security_id 
                                 FROM doctors 
                                 WHERE doc_id IN ($doc_ids_sql)");
            $doctors = [];
            while ($doc = mysqli_fetch_assoc($res2)) {
              $doctors[] = $doc;
            }

            if (count($doctors) > 1):
          ?>

              <div class="col-md-2">
                <label class="form-label">Select Doctor</label>
                <select id="f_user" class="form-select select2">
                  <option value="">Select Doctor</option>
                  <?php foreach ($doctors as $d): ?>
                    <option value="<?= $d['security_id'] ?>"><?= htmlspecialchars($d['doctor_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
          <?php
            endif;
          }
          ?>
          <div class="col-md-3 d-flex gap-2">
            <button id="btnApply" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Apply</button>
            <button id="btnExport" class="btn btn-outline-secondary w-100"><i class="fas fa-download"></i> Export CSV</button>
          </div>
        </div>

        <div class="mt-3">
          <span class="badge bg-light text-dark pointer me-2 mt-2 quick-range" data-range="today">Today</span>
          <span class="badge bg-light text-dark pointer me-2 mt-2 quick-range" data-range="7d">Last 7 days</span>
          <span class="badge bg-light text-dark pointer me-2 mt-2 quick-range" data-range="30d">Last 30 days</span>
          <span class="badge bg-light text-dark pointer mt-2 quick-range" data-range="qtd">Quarter-to-date</span>
        </div>
      </div>
    </div>

    <!-- Log table -->
    <div class="card">
      <div class="card-header">
        <h4>Log Entries</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="auditTable" class="table table-striped table-bordered w-100">
            <thead>
              <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>User</th>
                <th>Module</th>
                <th>Action</th>
                <th>Entity</th>
                <!-- <th>Entity ID</th>
                <th>IP</th> -->
                <th>Details</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

  </section>
</div>

<!-- Detail modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Change Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="auditDetailContainer" class="table-responsive mt-3"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once("ajax/footer.php") ?>
<script src="./assets/js/page/audit-log.js"></script>