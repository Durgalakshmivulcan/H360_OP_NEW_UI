<?php
require_once("ajax/header.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// Doctors for filter
$docOrgCond = ($SessionUserId == '1') ? '' : "AND org_id='$SessionOrgId'";
$docQ = mysqli_query($conn, "SELECT doc_id, doctor_name FROM doctors WHERE status='1' $docOrgCond ORDER BY doctor_name ASC");
$doctors = [];
while ($d = mysqli_fetch_assoc($docQ)) $doctors[] = $d;

// Orgs for super-admin filter
$orgs = [];
if ($SessionUserId == '1') {
    $orgQ = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY organization_name ASC");
    while ($o = mysqli_fetch_assoc($orgQ)) $orgs[] = $o;
}
?>

<div class="main-content">
  <section class="section">

    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">Billing Report</h4></li>
      <li class="breadcrumb-item">
        <a href="dashboard.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
               stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </a>
      </li>
      <li class="breadcrumb-item active">Billing Report</li>
    </ul>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-header"><h4 class="mb-0">Filters</h4></div>
      <div class="card-body">
        <div class="row">

          <?php if ($SessionUserId == '1'): ?>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>Organization</label>
            <select class="form-control form-select" id="br_org">
              <option value="">All Organizations</option>
              <?php foreach ($orgs as $o): ?>
              <option value="<?= $o['org_id'] ?>"><?= htmlspecialchars($o['organization_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <div class="col-lg-2 col-sm-6 mb-2">
            <label>From Date</label>
            <input type="date" id="br_from" class="form-control" value="">
          </div>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>To Date</label>
            <input type="date" id="br_to" class="form-control" value="">
          </div>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>Bill Type</label>
            <select class="form-control form-select" id="br_bill_type">
              <option value="">All Types</option>
              <option value="Consultation">Consultation</option>
              <option value="Test">Test</option>
              <option value="Medicine">Medicine</option>
            </select>
          </div>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>Doctor</label>
            <select class="form-control form-select" id="br_doctor">
              <option value="">All Doctors</option>
              <?php foreach ($doctors as $d): ?>
              <option value="<?= $d['doc_id'] ?>"><?= htmlspecialchars($d['doctor_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>Payment Method</label>
            <select class="form-control form-select" id="br_pay_method">
                <option value="">All Methods</option>
              <option value="Cash">Cash</option>
              <option value="UPI">UPI</option>
              <option value="Both (Cash + UPI)">Both (Cash + UPI)</option>
            </select>
          </div>
          <div class="col-lg-2 col-sm-6 mb-2">
            <label>Status</label>
            <select class="form-control form-select" id="br_status">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="cancelled">Cancelled / Refunded</option>
            </select>
          </div>
          <div class="col-lg-2 col-sm-6 mb-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="generateBillingReport()">
              <i class="fa fa-bar-chart me-1"></i> Generate Report
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Report Output -->
    <div id="billingReportOutput"></div>

  </section>
</div>

<?php require_once("ajax/footer.php"); ?>

<script>
function generateBillingReport() {
    const data = {
        from:          $('#br_from').val(),
        to:            $('#br_to').val(),
        bill_type:     $('#br_bill_type').val(),
        doctor_id:     $('#br_doctor').val(),
        pay_method:    $('#br_pay_method').val(),
        status_filter: $('#br_status').val(),
    };
    <?php if ($SessionUserId == '1'): ?>
    data.org_id = $('#br_org').val();
    <?php endif; ?>

    if (!data.from || !data.to) { swal('', 'Please select date range.', 'warning'); return; }

    $('#billingReportOutput').html(
        '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Generating report...</p></div>'
    );

    $.ajax({
        url: 'ajax/billing_report/getreportdata.php',
        type: 'POST',
        data: data,
        success: function(html) {
            $('#billingReportOutput').html(html);
        },
        error: function() {
            $('#billingReportOutput').html('<div class="alert alert-danger">Failed to generate report.</div>');
        }
    });
}
</script>
