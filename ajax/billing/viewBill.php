<?php
require_once("../../config/functions.php");

$SessionUserId  = $_SESSION['security_id'] ?? '';
$SessionOrgId   = $_SESSION['org_id'] ?? '';

$appoint_uid     = $_POST['patient_uid']          ?? '';
$appoint_reg_id  = $_POST['appoint_register_id']  ?? '';
$test_billing_id = (int)($_POST['test_billing_id'] ?? 0);
$org_id          = $_POST['org_id']               ?? $SessionOrgId;

$ucQ       = mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='" . (int)$SessionUserId . "' LIMIT 1");
$billGenBy = ($ucQ && $uc = mysqli_fetch_assoc($ucQ)) ? ($uc['user_code'] ?? '') : '';

// Appointment
$apptQ = mysqli_query($conn, "
    SELECT ao.*, d.doctor_name AS doc_name, dept.departmentName
    FROM appointment_online ao
    LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
    LEFT JOIN department dept ON d.departments = dept.dept_id
    WHERE ao.appoint_status='1' AND ao.appoint_id='" . (int)$appoint_uid . "' AND ao.org_id='$org_id'
    LIMIT 1
") or die(mysqli_error($conn));
$appt = mysqli_fetch_assoc($apptQ);
if (!$appt) { echo "<p class='text-danger'>Appointment not found.</p>"; exit; }

// Bill date
$billDate = $appt['bill_date'] ?? '';
if (empty($billDate) || $billDate === '0000-00-00') {
    $billDate = date('Y-m-d');
    mysqli_query($conn, "UPDATE appointment_online SET bill_date='$billDate' WHERE appoint_id='" . (int)$appoint_uid . "'");
}
$billDateDisplay = date('d-M-Y', strtotime($billDate));
$billNo = !empty($appt['bill_id']) ? $appt['bill_id'] : ($appt['appoint_register_id'] ?? '--');

// Organization
$orgQ = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$org_id' AND status='1' LIMIT 1");
$org  = mysqli_fetch_assoc($orgQ);

$_host   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$appRoot = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl = $_host . $appRoot;

$uploadDir = __DIR__ . '/../../organisation_images/';
$logoFile  = $org['logo'] ?? '';
$logoSrc   = (!empty($logoFile) && file_exists($uploadDir . $logoFile))
    ? $baseUrl . '/organisation_images/' . rawurlencode($logoFile)
    : $baseUrl . '/assets/img/h360.png';

$stampFile = $org['org_stamp'] ?? '';
$stampSrc  = (!empty($stampFile) && file_exists(__DIR__ . '/../../organisation_stamp/' . $stampFile))
    ? $baseUrl . '/organisation_stamp/' . rawurlencode($stampFile)
    : '';

// Test billing rows
$tbQ = mysqli_query($conn, "
    SELECT * FROM patient_test_billing
    WHERE status='1'
      AND patient_id='" . mysqli_real_escape_string($conn, $appt['appoint_unicode']) . "'
      AND appointment_id='" . mysqli_real_escape_string($conn, $appt['appoint_register_id']) . "'
      AND test_billing_id='$test_billing_id'
      AND org_id='" . mysqli_real_escape_string($conn, $appt['org_id']) . "'
    ORDER BY test_billing_id DESC
") or die(mysqli_error($conn));

$allTests    = [];
$grossTotal  = 0;
$finalTotal  = 0;
$payMode     = '';
$payRef      = '--';
$txnAmount   = 0.0;
$cashAmount  = 0.0;

while ($tb = mysqli_fetch_assoc($tbQ)) {
    $payMode   = $tb['payment_method']    ?? '';
    $payRef    = $tb['transaction_number'] ?? '--';
    $txnAmount = (float)($tb['transaction_amount'] ?? 0);
    $cashAmount= (float)($tb['cash_amount']        ?? 0);
    $items     = json_decode($tb['test_details'] ?? '[]', true) ?: [];
    if (is_object($items)) $items = (array)$items;

    foreach ($items as $it) {
        $it = (array)$it;
        $std    = (float)($it['standard_price'] ?? 0);
        $billed = (float)($it['doctor_price']   ?? $std);
        $tid    = $it['test_id'] ?? '';
        $gstQ   = mysqli_query($conn, "SELECT test_gst FROM tests WHERE status='1' AND test_id='" . mysqli_real_escape_string($conn, $tid) . "' LIMIT 1");
        $gst    = ($gstQ && $gr = mysqli_fetch_assoc($gstQ)) ? ($gr['test_gst'] ?? 0) : 0;
        $disc   = $std - $billed;
        $grossTotal += $std;
        $finalTotal += $billed;
        $allTests[] = [
            'name'   => $it['test_name'] ?? '',
            'std'    => $std,
            'billed' => $billed,
            'disc'   => $disc,
            'gst'    => $gst,
        ];
    }
}

$discount = $grossTotal - $finalTotal;
$inWords  = function_exists('convertNumber') ? convertNumber($finalTotal) : '';
$payRows  = [];
if (strcasecmp($payMode, 'Both (Cash + UPI)') === 0) {
    $payRows[] = ['mode' => 'UPI',  'amount' => $txnAmount,  'ref' => $payRef ?: '--'];
    $payRows[] = ['mode' => 'CASH', 'amount' => $cashAmount, 'ref' => '--'];
} elseif (!empty($payMode)) {
    $payRows[] = ['mode' => strtoupper($payMode), 'amount' => $finalTotal, 'ref' => $payRef ?: '--'];
} else {
    $payRows[] = ['mode' => '--', 'amount' => $finalTotal, 'ref' => '--'];
}
?>

<style>
.tb-page { font-family: Arial, sans-serif; font-size: 12px; color: #000; background:#fff; }
.tb-receipt-title { text-align:center; font-size:15px; font-weight:bold; letter-spacing:1px; border-bottom:2px solid #000; padding-bottom:5px; margin-bottom:8px; text-transform:uppercase; }
.tb-header-row { display:flex; justify-content:space-between; border-bottom:1px solid #aaa; padding-bottom:8px; margin-bottom:8px; gap:12px; }
.tb-hosp-block { width:52%; display:flex; align-items:flex-start; gap:10px; }
.tb-hosp-block .tb-logo img { max-height:80px; max-width:80px; object-fit:contain; display:block; }
.tb-hosp-block .tb-htext { flex:1; }
.tb-hosp-block .tb-hname { font-size:14px; font-weight:bold; margin-bottom:3px; }
.tb-hosp-block .tb-htext p { font-size:11px; margin-bottom:1px; line-height:1.5; }
.tb-pat-block { width:46%; font-size:11px; }
.tb-pat-block table { width:100%; border-collapse:collapse; }
.tb-pat-block td { padding:1.5px 4px; vertical-align:top; }
.tb-pat-block td.lbl { white-space:nowrap; color:#333; }
.tb-pat-block td.sep { padding:1.5px 2px; }
.tb-pat-block td.val { font-weight:bold; }
.tb-items { width:100%; border-collapse:collapse; font-size:11px; }
.tb-items thead tr { background:#222; color:#fff; }
.tb-items thead th { padding:5px 7px; font-weight:normal; text-align:left; white-space:nowrap; }
.tb-items thead th.r { text-align:right; }
.tb-items tbody td { padding:4px 7px; border-bottom:1px solid #e0e0e0; }
.tb-items tbody td.r { text-align:right; }
.tb-totals-wrap { display:flex; justify-content:flex-end; border-top:1px solid #aaa; padding-top:5px; }
.tb-totals { width:260px; border-collapse:collapse; font-size:12px; }
.tb-totals td { padding:3px 7px; }
.tb-totals td.r { text-align:right; }
.tb-totals tr.bold td { font-weight:bold; background:#f0f0f0; }
.tb-pay { width:100%; border-collapse:collapse; font-size:11px; margin-top:8px; }
.tb-pay thead tr { background:#222; color:#fff; }
.tb-pay thead th { padding:4px 7px; font-weight:normal; text-align:left; }
.tb-pay tbody td { padding:3px 7px; border-bottom:1px solid #e0e0e0; }
.tb-footer-thanks { font-size:12px; font-weight:bold; margin-top:6px; }
.tb-sig-row { display:flex; justify-content:space-between; align-items:flex-end; margin-top:16px; }
.tb-sig-stamp img { width:100px; height:100px; object-fit:contain; opacity:.8; }
.tb-sig-stamp p { font-size:10px; text-align:center; margin-top:2px; border-top:1px solid #000; padding-top:2px; }
.tb-meta { font-size:9.5px; color:#555; margin-top:10px; border-top:1px solid #ccc; padding-top:4px; display:flex; justify-content:space-between; }
@media print {
    .tb-page { padding: 6mm 8mm 10mm; }
}
</style>

<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Test Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="reportModalBody">

<div class="tb-page" id="testBillContent">
    <div class="tb-receipt-title">Test Bill</div>

    <div class="tb-header-row">
        <div class="tb-hosp-block">
            <div class="tb-logo"><img src="<?= $logoSrc ?>" alt="Logo"></div>
            <div class="tb-htext">
                <div class="tb-hname"><?= htmlspecialchars($org['organization_name'] ?? '') ?></div>
                <?php if (!empty($org['address'])): ?><p><?= nl2br(htmlspecialchars($org['address'])) ?></p><?php endif; ?>
                <?php if (!empty($org['mobile_number'])): ?><p>Tel. No: <?= htmlspecialchars($org['mobile_number']) ?></p><?php endif; ?>
                <?php if (!empty($org['gst_number'])): ?><p>GST No – <?= htmlspecialchars($org['gst_number']) ?></p><?php endif; ?>
            </div>
        </div>
        <div class="tb-pat-block">
            <table>
                <tr><td class="lbl">Name</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['patient_name'] ?? '') ?></td></tr>
                <tr><td class="lbl">Age / Gender</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['age'] ?? '-') ?> / <?= htmlspecialchars($appt['gender'] ?? '-') ?></td></tr>
                <tr><td class="lbl">Mobile</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['mobile_number'] ?? '-') ?></td></tr>
                <tr><td class="lbl">UMR No</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['appoint_unicode'] ?? '') ?></td></tr>
                <tr><td class="lbl">Bill No.</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($billNo) ?></td></tr>
                <tr><td class="lbl">Bill Dt.</td><td class="sep">:</td><td class="val"><?= $billDateDisplay ?></td></tr>
                <tr><td class="lbl">Visit Type</td><td class="sep">:</td><td class="val">Test</td></tr>
                <?php if (!empty($appt['doc_name'])): ?>
                <tr><td class="lbl">Ref / Per Dr.</td><td class="sep">:</td><td class="val">Dr. <?= htmlspecialchars($appt['doc_name']) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <table class="tb-items">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Test Name</th>
                <th>GST</th>
                <th class="r" style="width:36px">Qty</th>
                <th class="r" style="width:70px">MRP (₹)</th>
                <th class="r" style="width:80px">Disc (₹)</th>
                <th class="r" style="width:70px">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allTests)): ?>
            <tr><td colspan="7" class="text-center text-muted">No test data found.</td></tr>
            <?php else: foreach ($allTests as $i => $t): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($t['name']) ?></td>
                <td><?= htmlspecialchars($t['gst']) ?>%</td>
                <td class="r">1</td>
                <td class="r"><?= number_format($t['std'], 2) ?></td>
                <td class="r"><?= $t['disc'] > 0 ? number_format($t['disc'], 2) : '-' ?></td>
                <td class="r"><strong><?= number_format($t['billed'], 2) ?></strong></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>

    <div class="tb-totals-wrap">
        <table class="tb-totals">
            <tr><td>Gross Value</td><td class="r"><?= number_format($grossTotal, 2) ?></td></tr>
            <tr><td>Discount Value</td><td class="r"><?= number_format($discount, 2) ?></td></tr>
            <tr class="bold"><td>Patient Paid</td><td class="r"><?= number_format($finalTotal, 2) ?></td></tr>
        </table>
    </div>

    <table class="tb-pay">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Payment Mode</th>
                <th>Amount</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payRows as $pi => $pr): ?>
            <tr>
                <td><?= $pi + 1 ?></td>
                <td><?= htmlspecialchars($pr['mode']) ?></td>
                <td><?= number_format($pr['amount'], 2) ?></td>
                <td><?= htmlspecialchars($pr['ref']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="tb-footer-thanks">
        Received with Thanks &nbsp;<strong>Rupees <?= htmlspecialchars(ucfirst($inWords)) ?></strong>
    </div>

    <div class="tb-sig-row">
        <div></div>
        <div class="tb-sig-stamp">
            <?php if (!empty($stampSrc)): ?><img src="<?= $stampSrc ?>" alt="Stamp"><?php endif; ?>
            <p>(Authorised Signatory)</p>
        </div>
    </div>

    <div class="tb-meta">
        <span>Created By : <?= htmlspecialchars($org['organization_name'] ?? '') ?></span>
        <?php if (!empty($billGenBy)): ?><span>Bill generated by : <strong><?= htmlspecialchars($billGenBy) ?></strong></span><?php endif; ?>
        <span>Printed On : <?= date('d-M-Y h:i A') ?> &nbsp;&nbsp; Page 1/1</span>
    </div>
</div>

      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="printTestBill()">
          <i class="fa fa-print me-1"></i> Print
        </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
function printTestBill() {
    var content = document.getElementById('testBillContent').innerHTML;
    var w = window.open('', '', 'width=900,height=700');
    w.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Test Bill</title>');
    w.document.write('<style>');
    w.document.write('*{box-sizing:border-box;margin:0;padding:0;}');
    w.document.write('body{font-family:Arial,sans-serif;font-size:12px;color:#000;background:#fff;padding:10mm 10mm 14mm;}');
    w.document.write('.tb-receipt-title{text-align:center;font-size:15px;font-weight:bold;letter-spacing:1px;border-bottom:2px solid #000;padding-bottom:5px;margin-bottom:8px;text-transform:uppercase;}');
    w.document.write('.tb-header-row{display:flex;justify-content:space-between;border-bottom:1px solid #aaa;padding-bottom:8px;margin-bottom:8px;gap:12px;}');
    w.document.write('.tb-hosp-block{width:52%;display:flex;align-items:flex-start;gap:10px;}');
    w.document.write('.tb-logo img{max-height:80px;max-width:80px;object-fit:contain;display:block;}');
    w.document.write('.tb-htext{flex:1;}.tb-hname{font-size:14px;font-weight:bold;margin-bottom:3px;}');
    w.document.write('.tb-htext p{font-size:11px;margin-bottom:1px;line-height:1.5;}');
    w.document.write('.tb-pat-block{width:46%;font-size:11px;}');
    w.document.write('.tb-pat-block table{width:100%;border-collapse:collapse;}');
    w.document.write('.tb-pat-block td{padding:1.5px 4px;vertical-align:top;}');
    w.document.write('.tb-pat-block td.lbl{white-space:nowrap;color:#333;}.tb-pat-block td.sep{padding:1.5px 2px;}.tb-pat-block td.val{font-weight:bold;}');
    w.document.write('.tb-items{width:100%;border-collapse:collapse;font-size:11px;}');
    w.document.write('.tb-items thead tr{background:#222;color:#fff;}');
    w.document.write('.tb-items thead th{padding:5px 7px;font-weight:normal;text-align:left;white-space:nowrap;}');
    w.document.write('.tb-items thead th.r{text-align:right;}.tb-items tbody td{padding:4px 7px;border-bottom:1px solid #e0e0e0;}.tb-items tbody td.r{text-align:right;}');
    w.document.write('.tb-totals-wrap{display:flex;justify-content:flex-end;border-top:1px solid #aaa;padding-top:5px;}');
    w.document.write('.tb-totals{width:260px;border-collapse:collapse;font-size:12px;}');
    w.document.write('.tb-totals td{padding:3px 7px;}.tb-totals td.r{text-align:right;}.tb-totals tr.bold td{font-weight:bold;background:#f0f0f0;}');
    w.document.write('.tb-pay{width:100%;border-collapse:collapse;font-size:11px;margin-top:8px;}');
    w.document.write('.tb-pay thead tr{background:#222;color:#fff;}.tb-pay thead th{padding:4px 7px;font-weight:normal;text-align:left;}');
    w.document.write('.tb-pay tbody td{padding:3px 7px;border-bottom:1px solid #e0e0e0;}');
    w.document.write('.tb-footer-thanks{font-size:12px;font-weight:bold;margin-top:6px;}');
    w.document.write('.tb-sig-row{display:flex;justify-content:space-between;align-items:flex-end;margin-top:16px;}');
    w.document.write('.tb-sig-stamp img{width:100px;height:100px;object-fit:contain;opacity:.8;}');
    w.document.write('.tb-sig-stamp p{font-size:10px;text-align:center;margin-top:2px;border-top:1px solid #000;padding-top:2px;}');
    w.document.write('.tb-meta{font-size:9.5px;color:#555;margin-top:10px;border-top:1px solid #ccc;padding-top:4px;display:flex;justify-content:space-between;}');
    w.document.write('</style></head><body>');
    w.document.write(content);
    w.document.write('</body></html>');
    w.document.close();
    setTimeout(function(){ w.print(); w.close(); }, 500);
}
</script>
