<?php
require_once("ajax/header.php");
// FIX_B_1820 (scope 2 RBAC): per-action view gate; SA bypassed by userCan().
requireCan('view', basename(__FILE__));

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
?>

<div class="main-content">
  <section class="section">

    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">Refunds &amp; Cancellations</h4></li>
      <li class="breadcrumb-item">
        <a href="dashboard.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-home">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
        </a>
      </li>
      <li class="breadcrumb-item">Appointments and Billing</li>
      <li class="breadcrumb-item active">Refunds &amp; Cancellations</li>
    </ul>

    <!-- Patient Search Card -->
    <div class="card">
      <div class="card-header">
        <h4>Patient Search</h4>
      </div>
      <div class="col-12 col-md-12 col-lg-12">
        <form method="POST" id="FormId" action="" enctype="multipart/form-data">
          <div class="card-body">

            <?php if ($SessionUserId == "1" && $SessionRoleId == "1"): ?>
            <div class="row">
              <div class="row mb-lg-4 mb-sm-3">
                <div class="form-group col-lg-4 col-sm-12">
                  <label for="organizations">Organization <span class="text-danger">*</span></label>
                  <select class="form-control form-select" name="organizations" id="organizations"
                          onchange="fetchpatientdetails()">
                    <option value="">Select Organization</option>
                    <?php
                    $GetOrg = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                    while ($ResOrg = mysqli_fetch_object($GetOrg)):
                    ?>
                    <option value="<?= $ResOrg->org_id ?>"><?= htmlspecialchars($ResOrg->organization_name) ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
            </div>
            <?php else: ?>
            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>">
            <?php endif; ?>

            <div class="row">
              <div class="form-group col-lg-3 col-sm-12">
                <label><i class="bi bi-person-fill"></i> Patient Name <span class="text-danger">*</span></label>
                <select class="form-control form-select" name="patientName" id="patientName">
                  <option value="">Select Patient Name</option>
                </select>
              </div>
              <div class="form-group col-lg-3 col-sm-12">
                <label><i class="bi bi-telephone-fill"></i> Mobile <span class="text-danger">*</span></label>
                <select class="form-control form-select" name="mobileNumber" id="mobileNumber">
                  <option value="">Select Mobile Number</option>
                </select>
              </div>
              <div class="form-group col-lg-3 col-sm-12">
                <label><i class="bi bi-person-vcard"></i> Patient ID <span class="text-danger">*</span></label>
                <select class="form-control form-select" name="appointUnicode" id="appointUnicode">
                  <option value="">Select Patient ID</option>
                </select>
              </div>
              <div class="form-group col-lg-3 col-sm-12">
                <label><i class="bi bi-postcard-fill"></i> Appointment ID <span class="text-danger">*</span></label>
                <select class="form-control form-select" name="appointRegisterId" id="appointRegisterId">
                  <option value="">Select Appointment ID</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="card-footer text-center">
                <button type="button" class="btn btn-primary" onclick="loadRefunds()">Search</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

  </section>

  <!-- Results injected here -->
  <div id="showData"></div>

</div>

<!-- Action Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" id="modalHeader">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
          <div class="col-6"><strong>Bill Type :</strong> <span id="modalBillType"></span></div>
          <div class="col-6"><strong>Net Amount :</strong> <strong>₹<span id="modalNetAmt"></span></strong></div>
        </div>

        <!-- Refund amount field (partial refund only) -->
        <div class="mb-3" id="refundAmountRow" style="display:none;">
          <label class="form-label fw-bold">
            Refund Amount (₹) <span class="text-danger">*</span>
          </label>
          <input type="number" id="refundAmountInput" class="form-control" min="0.01" step="0.01"
                 placeholder="Enter amount to refund (must be ≤ net amount)">
          <small class="text-muted">Maximum refundable: ₹<span id="maxRefundAmt"></span></small>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">
            Reason <span class="text-danger">*</span>
          </label>
          <textarea id="refundReason" class="form-control" rows="3"
                    placeholder="Enter reason..."></textarea>
        </div>

        <div class="alert mb-0" id="modalAlert" style="font-size:13px;">
          <i class="fa fa-info-circle me-1"></i>
          <span id="modalAlertText"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn" id="confirmRefundBtn">
          <i class="fa fa-check me-1"></i> <span id="confirmBtnLabel">Confirm</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php require_once("ajax/footer.php"); ?>

<script>
let _currentInvoiceId = null;
let _currentAction    = null;

$(document).ready(function () {
    fetchpatientdetails();
    $('#patientName, #mobileNumber, #appointUnicode, #appointRegisterId').select2();
});

$(document).on('change', '#patientName, #mobileNumber, #appointUnicode, #appointRegisterId', function () {
    patientinfo($(this).attr('id'), $(this).val());
});

function patientinfo(fieldName, fieldValue) {
    const organization_id = $('#organizations').val();
    $.ajax({
        url: 'ajax/Allpatientreports/patientinformation.php',
        type: 'POST',
        data: { organization_id, fieldName, fieldValue },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                updateSelect2('#patientName',        response.data.patientName,        response.data.appoint_id);
                updateSelect2('#mobileNumber',       response.data.mobileNumber,       response.data.appoint_id);
                updateSelect2('#appointUnicode',     response.data.appointUnicode,     response.data.appoint_id);
                updateSelect2('#appointRegisterId',  response.data.appointRegisterId,  response.data.appoint_id);
            }
        }
    });
}

function updateSelect2(selector, value, id) {
    const $select = $(selector);
    if ($select.find("option[value='" + id + "']").length === 0) {
        $select.append(new Option(value, id, true, true)).trigger('change.select2');
    } else {
        $select.val(id).trigger('change.select2');
    }
}

function fetchpatientdetails() {
    const orgId = $('#organizations').val();
    if (!orgId) return;
    $.ajax({
        url: 'ajax/Allpatientreports/fetchpatientdetails.php',
        method: 'GET',
        dataType: 'json',
        data: { org_id: orgId },
        success: function (data) {
            $('#patientName').empty().append('<option value="">Select Patient Name</option>');
            $('#mobileNumber').empty().append('<option value="">Select Mobile Number</option>');
            $('#appointUnicode').empty().append('<option value="">Select Patient ID</option>');
            $('#appointRegisterId').empty().append('<option value="">Select Appointment ID</option>');

            const mobileMap = {}, unicodeMap = {}, registerMap = {};
            data.mobile_numbers.forEach(i  => mobileMap[i.appoint_id]   = i.mobile_number);
            data.patient_ids.forEach(i     => unicodeMap[i.appoint_id]   = i.appoint_unicode);
            data.appointment_ids.forEach(i => registerMap[i.appoint_id]  = i.appoint_register_id);

            data.patient_names.forEach(item => {
                const id = item.appoint_id;
                $('#patientName').append(`<option value="${id}">${item.patient_name}</option>`);
                if (mobileMap[id])   $('#mobileNumber').append(`<option value="${id}">${mobileMap[id]}</option>`);
                if (unicodeMap[id])  $('#appointUnicode').append(`<option value="${id}">${unicodeMap[id]}</option>`);
                if (registerMap[id]) $('#appointRegisterId').append(`<option value="${id}">${registerMap[id]}</option>`);
            });
        }
    });
}

function loadRefunds() {
    const appoint_register_id = $('#appointRegisterId').val();
    const patient_uid          = $('#appointUnicode').val();
    const org_id               = $('#organizations').val();

    if (!appoint_register_id || !patient_uid) {
        swal('', 'Please select Patient Name and Appointment ID first.', 'warning');
        return;
    }

    $.ajax({
        url: 'ajax/refunds/getbillingdata.php',
        type: 'POST',
        data: { appoint_register_id, patient_uid, org_id },
        dataType: 'json',
        success: function (res) {
            if (!res.success) { swal('', res.message, 'warning'); return; }
            renderRefundResults(res);
        },
        error: function () { swal('Error', 'Failed to fetch billing data.', 'error'); }
    });
}

function typeBadge(type) {
    const map = { Consultation: '#1a56a0', Test: '#198754', Medicine: '#dc3545' };
    const c = map[type] || '#6c757d';
    return `<span style="background:${c};color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">${type}</span>`;
}

function actionBadge(type) {
    if (type === 'cancel') return `<span style="background:#dc3545;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Cancelled</span>`;
    if (type === 'refund') return `<span style="background:#fd7e14;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Refunded</span>`;
    return '<span class="text-muted">-</span>';
}

function fmtDateTime(dt) {
    if (!dt) return '-';
    const d = new Date(dt);
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
         + ' ' + d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
}

function renderRefundResults(res) {
    const p = res.patient;
    const active    = res.invoices.filter(i => String(i.status) === '1');
    const cancelled = res.invoices.filter(i => String(i.status) === '0');

    const codeBadge = (code, bg) => code
        ? `<span style="background:${bg};color:#fff;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;">${code}</span>`
        : '-';

    // --- Active Bills ---
    let activeRows = active.length
        ? active.map(inv => `
            <tr>
              <td>#${inv.invoice_id}</td>
              <td>${typeBadge(inv.bill_type)}</td>
              <td>${parseFloat(inv.amount).toFixed(2)}</td>
              <td>${parseFloat(inv.concession_value || 0).toFixed(2)}</td>
              <td><strong>${parseFloat(inv.net_amount).toFixed(2)}</strong></td>
              <td>${inv.payment_method || '-'}</td>
              <td>${codeBadge(inv.generated_by_code, '#4F5ECE')}</td>
              <td>${fmtDateTime(inv.created_at)}</td>
              <td>
                <button class="btn btn-sm btn-danger me-1"
                  onclick="openRefundModal(${inv.invoice_id},'${inv.bill_type}',${parseFloat(inv.net_amount).toFixed(2)},'cancel')">
                  <i class="fa fa-times me-1"></i>Cancel
                </button>
                <button class="btn btn-sm btn-warning"
                  onclick="openRefundModal(${inv.invoice_id},'${inv.bill_type}',${parseFloat(inv.net_amount).toFixed(2)},'refund')">
                  <i class="fa fa-undo me-1"></i>Refund
                </button>
              </td>
            </tr>`).join('')
        : `<tr><td colspan="9" class="text-muted py-3">No active bills found.</td></tr>`;

    // --- Cancelled / Refunded Bills ---
    let cancelledRows = cancelled.length
        ? cancelled.map(inv => `
            <tr>
              <td>#${inv.invoice_id}</td>
              <td>${typeBadge(inv.bill_type)}</td>
              <td>${parseFloat(inv.net_amount).toFixed(2)}</td>
              <td>${actionBadge(inv.refund_type)}</td>
              <td><strong>${inv.refund_amount ? parseFloat(inv.refund_amount).toFixed(2) : parseFloat(inv.net_amount).toFixed(2)}</strong></td>
              <td>${inv.payment_method || '-'}</td>
              <td>${inv.refund_reason || '-'}</td>
              <td>${codeBadge(inv.refunded_by_code, '#6c757d')}</td>
              <td>${fmtDateTime(inv.refunded_at)}</td>
            </tr>`).join('')
        : `<tr><td colspan="9" class="text-muted py-3">No cancelled / refunded bills.</td></tr>`;

    const html = `
    <section class="section">

      <!-- Patient Info -->
      <div class="alert alert-info mb-3">
        <strong>Patient :</strong> ${p.patient_name} &nbsp;|&nbsp;
        <strong>UMR No :</strong> ${p.appoint_unicode} &nbsp;|&nbsp;
        <strong>Appointment ID :</strong> ${p.appoint_register_id} &nbsp;|&nbsp;
        <strong>Mobile :</strong> ${p.mobile_number} &nbsp;|&nbsp;
        <strong>Doctor :</strong> ${p.doc_name || '-'}
      </div>

      <!-- What's the difference? -->
      <div class="alert mb-3" style="background:#fff3cd;border-left:4px solid #fd7e14;font-size:13px;color:#856404;">
        <strong><i class="fa fa-info-circle me-1"></i>Cancel vs Refund :</strong>
        <span class="ms-1"><span style="color:#dc3545;font-weight:700;">Cancel</span> — entire bill amount is returned to the patient.
        &nbsp;&nbsp;<span style="color:#fd7e14;font-weight:700;">Refund</span> — a partial amount is returned; you specify how much.</span>
      </div>

      <!-- Active Bills -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Active Bills</h4>
          <span class="badge" style="background:#198754;font-size:14px;">${active.length}</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="text-center" style="background:#222;color:#fff;">
                <tr>
                  <th>Invoice</th><th>Bill Type</th><th>Gross (₹)</th>
                  <th>Concession (₹)</th><th>Net (₹)</th><th>Payment</th>
                  <th>Generated By</th><th>Date</th><th>Action</th>
                </tr>
              </thead>
              <tbody class="text-center">${activeRows}</tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Cancelled / Refunded Bills -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Cancelled / Refunded Bills</h4>
          <span class="badge" style="background:#dc3545;font-size:14px;">${cancelled.length}</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead class="text-center" style="background:#222;color:#fff;">
                <tr>
                  <th>Invoice</th><th>Bill Type</th><th>Net (₹)</th>
                  <th>Type</th><th>Returned (₹)</th><th>Payment</th>
                  <th>Reason</th><th>By</th><th>On</th>
                </tr>
              </thead>
              <tbody class="text-center">${cancelledRows}</tbody>
            </table>
          </div>
        </div>
      </div>

    </section>`;

    $('#showData').html(html);
}

function openRefundModal(invoiceId, billType, netAmt, action) {
    _currentInvoiceId = invoiceId;
    _currentAction    = action;

    $('#modalBillType').text(billType);
    $('#modalNetAmt').text(parseFloat(netAmt).toFixed(2));
    $('#maxRefundAmt').text(parseFloat(netAmt).toFixed(2));
    $('#refundReason').val('');
    $('#refundAmountInput').val('').attr('max', netAmt);

    if (action === 'cancel') {
        $('#modalHeader').css('background', '#dc3545');
        $('#modalTitle').html('<i class="fa fa-times-circle me-2"></i>Cancel Bill');
        $('#confirmBtnLabel').text('Confirm Cancellation');
        $('#confirmRefundBtn').removeClass('btn-warning').addClass('btn-danger');
        $('#refundAmountRow').hide();
        $('#modalAlert').removeClass('alert-warning').addClass('alert-danger');
        $('#modalAlertText').text('The full net amount ₹' + parseFloat(netAmt).toFixed(2) + ' will be returned to the patient. Bill will be marked as Cancelled.');
    } else {
        $('#modalHeader').css('background', '#fd7e14');
        $('#modalTitle').html('<i class="fa fa-undo me-2"></i>Partial Refund');
        $('#confirmBtnLabel').text('Confirm Refund');
        $('#confirmRefundBtn').removeClass('btn-danger').addClass('btn-warning');
        $('#refundAmountRow').show();
        $('#modalAlert').removeClass('alert-danger').addClass('alert-warning');
        $('#modalAlertText').text('Enter the amount to refund (must be less than or equal to the net amount). Bill will be marked as Refunded.');
    }

    $('#refundModal').modal('show');
}

$('#confirmRefundBtn').on('click', function () {
    const reason = $('#refundReason').val().trim();
    if (!reason) { swal('', 'Please enter a reason.', 'warning'); return; }

    let refundAmount = 0;
    if (_currentAction === 'refund') {
        refundAmount = parseFloat($('#refundAmountInput').val());
        if (!refundAmount || refundAmount <= 0) {
            swal('', 'Please enter a valid refund amount.', 'warning'); return;
        }
        const maxAmt = parseFloat($('#refundAmountInput').attr('max'));
        if (refundAmount > maxAmt) {
            swal('', 'Refund amount cannot exceed the net amount ₹' + maxAmt.toFixed(2) + '.', 'warning'); return;
        }
    }

    const $btn = $(this).prop('disabled', true);
    $('#confirmBtnLabel').text('Processing...');

    $.ajax({
        url: 'ajax/refunds/processrefund.php',
        type: 'POST',
        data: {
            invoice_id:    _currentInvoiceId,
            reason:        reason,
            action:        _currentAction,
            refund_amount: refundAmount
        },
        dataType: 'json',
        success: function (res) {
            $btn.prop('disabled', false);
            $('#confirmBtnLabel').text(_currentAction === 'cancel' ? 'Confirm Cancellation' : 'Confirm Refund');
            $('#refundModal').modal('hide');
            if (res.success) {
                swal('Done!', res.message, 'success').then(() => loadRefunds());
            } else {
                swal('Error', res.message, 'error');
            }
        },
        error: function () {
            $btn.prop('disabled', false);
            $('#confirmBtnLabel').text(_currentAction === 'cancel' ? 'Confirm Cancellation' : 'Confirm Refund');
            swal('Error', 'Request failed.', 'error');
        }
    });
});
</script>
