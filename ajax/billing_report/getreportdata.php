<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if (!$SessionUserId) exit('Unauthorized');

ensureRefundColumns($conn);

$from       = mysqli_real_escape_string($conn, $_POST['from']    ?? date('Y-m-01'));
$to         = mysqli_real_escape_string($conn, $_POST['to']      ?? date('Y-m-d'));
$billType   = mysqli_real_escape_string($conn, $_POST['bill_type']      ?? '');
$payMethod  = mysqli_real_escape_string($conn, $_POST['pay_method']     ?? '');
$statusFilt = mysqli_real_escape_string($conn, $_POST['status_filter']  ?? '');
$doctorFilt = (int)($_POST['doctor_id'] ?? 0);
$orgFilt    = ($SessionUserId == '1') ? (int)($_POST['org_id'] ?? 0) : (int)$SessionOrgId;

$where = "WHERE DATE(i.created_at) BETWEEN '$from' AND '$to'";
if ($orgFilt)        $where .= " AND i.org_id='$orgFilt'";
if ($billType)       $where .= " AND i.bill_type='$billType'";
if ($payMethod)      $where .= " AND i.payment_method='$payMethod'";
if ($statusFilt !== '') $where .= " AND i.status='" . ($statusFilt === 'active' ? 1 : 0) . "'";
if ($doctorFilt)     $where .= " AND ao.doctor_name='$doctorFilt'";

$sql = "
    SELECT
        i.invoice_id,
        i.bill_type,
        i.category_type,
        i.amount,
        i.concession_value,
        i.net_amount,
        i.payment_method,
        i.status,
        i.refund_type,
        i.refund_amount,
        i.refund_reason,
        i.refunded_at,
        i.created_at,
        i.org_id,
        ao.patient_name,
        ao.appoint_unicode    AS umr_no,
        ao.appoint_register_id,
        ao.mobile_number,
        d.doctor_name,
        o.organization_name,
        s.user_code           AS generated_by,
        rs.user_code          AS refunded_by_code
    FROM invoice i
    LEFT JOIN appointment_online ao ON ao.appoint_register_id = i.appointment_id AND ao.appoint_status='1'
    LEFT JOIN doctors      d  ON d.doc_id            = ao.doctor_name
    LEFT JOIN organization o  ON o.org_id             = i.org_id AND o.status='1'
    LEFT JOIN security     s  ON s.security_id        = i.created_by
    LEFT JOIN security     rs ON rs.security_id       = i.refunded_by
    $where
    ORDER BY i.created_at DESC
";

$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$rows         = [];
$totalGross   = 0;
$totalDisc    = 0;
$totalNet     = 0;
$totalRefund  = 0;
$activeNet    = 0;

while ($row = mysqli_fetch_assoc($res)) {
    $rows[] = $row;
    $gross = (float)$row['amount'];
    $disc  = (float)$row['concession_value'];
    $net   = (float)$row['net_amount'];
    $totalGross += $gross;
    $totalDisc  += $disc;
    $totalNet   += $net;
    if ($row['status'] == '0') {
        $refAmt = (float)($row['refund_amount'] ?? 0);
        $totalRefund += ($refAmt > 0 ? $refAmt : $net);
    } else {
        $activeNet += $net;
    }
}

$sno = 1;
$isAdmin = ($SessionUserId == '1');
?>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-6 col-md-3 mb-2">
    <div style="background:#1a56a0;color:#fff;border-radius:10px;padding:16px 18px;text-align:center;">
      <h3 style="font-size:1.7rem;font-weight:700;margin:0;"><?= count($rows) ?></h3>
      <p style="margin:4px 0 0;font-size:12px;opacity:.9;">Total Transactions</p>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div style="background:#198754;color:#fff;border-radius:10px;padding:16px 18px;text-align:center;">
      <h3 style="font-size:1.7rem;font-weight:700;margin:0;">₹<?= number_format($activeNet, 2) ?></h3>
      <p style="margin:4px 0 0;font-size:12px;opacity:.9;">Active Net Amount</p>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div style="background:#dc3545;color:#fff;border-radius:10px;padding:16px 18px;text-align:center;">
      <h3 style="font-size:1.7rem;font-weight:700;margin:0;">₹<?= number_format($totalRefund, 2) ?></h3>
      <p style="margin:4px 0 0;font-size:12px;opacity:.9;">Total Cancelled / Refunded</p>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div style="background:#6c757d;color:#fff;border-radius:10px;padding:16px 18px;text-align:center;">
      <h3 style="font-size:1.7rem;font-weight:700;margin:0;">₹<?= number_format($totalDisc, 2) ?></h3>
      <p style="margin:4px 0 0;font-size:12px;opacity:.9;">Total Concessions</p>
    </div>
  </div>
</div>

<!-- Report Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table" id="billingReportTable" style="width:100%;">
        <thead class="text-center">
          <tr>
            <th>S.No</th>
            <?php if ($isAdmin): ?><th>Organization</th><?php endif; ?>
            <th>Date</th>
            <th>Invoice</th>
            <th>Patient Name</th>
            <th>UMR No</th>
            <th>Appt. ID</th>
            <th>Doctor</th>
            <th>Bill Type</th>
            <th>Gross (₹)</th>
            <th>Concession (₹)</th>
            <th>Net (₹)</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Generated By</th>
            <th>Cancelled By</th>
            <th>Cancel Reason</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php if (empty($rows)): ?>
          <tr><td colspan="<?= $isAdmin ? 17 : 16 ?>" class="text-muted py-3">No billing records found for the selected filters.</td></tr>
          <?php else: foreach ($rows as $r):
            $typeColors = ['Consultation' => '#1a56a0', 'Test' => '#198754', 'Medicine' => '#dc3545'];
            $typeColor  = $typeColors[$r['bill_type']] ?? '#6c757d';
            $isActive   = $r['status'] == '1';
            $refAmt     = (float)($r['refund_amount'] ?? 0);
            $netAmt     = (float)$r['net_amount'];
            $isPartial  = !$isActive && $refAmt > 0 && $refAmt < $netAmt;
            if ($isActive) {
                $statusBadge = '<span style="background:#198754;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Active</span>';
            } elseif ($isPartial) {
                $statusBadge = '<span style="background:#fd7e14;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Partial Refund (₹' . number_format($refAmt, 2) . ')</span>';
            } else {
                $statusBadge = '<span style="background:#dc3545;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">Cancelled</span>';
            }
          ?>
          <tr>
            <td><?= $sno++ ?></td>
            <?php if ($isAdmin): ?><td><?= htmlspecialchars($r['organization_name'] ?? '-') ?></td><?php endif; ?>
            <td><?= date('d-M-Y', strtotime($r['created_at'])) ?></td>
            <td>#<?= $r['invoice_id'] ?></td>
            <td><?= htmlspecialchars($r['patient_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['umr_no'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['appoint_register_id'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['doctor_name'] ?? '-') ?></td>
            <td><span style="background:<?= $typeColor ?>;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;"><?= htmlspecialchars($r['bill_type']) ?></span></td>
            <td><?= number_format((float)$r['amount'], 2) ?></td>
            <td><?= number_format((float)$r['concession_value'], 2) ?></td>
            <td><strong><?= number_format((float)$r['net_amount'], 2) ?></strong></td>
            <td><?= htmlspecialchars($r['payment_method'] ?? '-') ?></td>
            <td><?= $statusBadge ?></td>
            <td>
              <?php if (!empty($r['generated_by'])): ?>
              <span style="background:#4F5ECE;color:#fff;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;"><?= htmlspecialchars($r['generated_by']) ?></span>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td>
              <?php if (!empty($r['refunded_by_code'])): ?>
              <span style="background:#6c757d;color:#fff;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:700;"><?= htmlspecialchars($r['refunded_by_code']) ?></span>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td><?= htmlspecialchars($r['refund_reason'] ?? '-') ?></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
$(function() {
    if ($.fn.DataTable.isDataTable('#billingReportTable')) {
        $('#billingReportTable').DataTable().destroy();
    }
    $('#billingReportTable').DataTable({
        dom: 'lBrftip',
        buttons: [
            { extend: 'copy',  exportOptions: { columns: ':visible' } },
            { extend: 'excel', exportOptions: { columns: ':visible' } },
            { extend: 'csv',   exportOptions: { columns: ':visible' } },
            { extend: 'pdf',   exportOptions: { columns: ':visible' } },
            { extend: 'print', exportOptions: { columns: ':visible' } }
        ],
        order: [[2, 'desc']]
    });
});
</script>
