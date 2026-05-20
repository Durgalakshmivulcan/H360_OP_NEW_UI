<?php
require_once("config/functions.php");

$SessionUserId = $_SESSION['security_id'];
$SessionOrgId  = $_SESSION['org_id'];
$id = (int)($_GET['ItemId'] ?? 0);

$apptQry = mysqli_query($conn, "
    SELECT ao.*, d.doctor_name AS doc_name, dept.departmentName
    FROM appointment_online ao
    LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
    LEFT JOIN department dept ON d.departments = dept.dept_id
    WHERE ao.appoint_status='1' AND ao.appoint_id='$id' LIMIT 1
") or die(mysqli_error($conn));
$appt = mysqli_fetch_assoc($apptQry);
if (!$appt) exit('Appointment not found.');

$orgId = $appt['org_id'];
$orgQry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$orgId' AND status='1' LIMIT 1");
$org = mysqli_fetch_assoc($orgQry);

$ucQ       = mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='" . (int)($appt['created_by'] ?? 0) . "' LIMIT 1");
$billGenBy = ($ucQ && $uc = mysqli_fetch_assoc($ucQ)) ? ($uc['user_code'] ?? '') : '';

$_host   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$appRoot = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$baseUrl = $_host . $appRoot;

$uploadDir = __DIR__ . '/organisation_images/';
$logoFile  = $org['logo'] ?? '';
$logoSrc   = (!empty($logoFile) && file_exists($uploadDir . $logoFile))
    ? $baseUrl . '/organisation_images/' . rawurlencode($logoFile)
    : $baseUrl . '/assets/img/h360.png';

$stampSrc = file_exists(__DIR__ . '/img/Stamp Logo.png') ? $baseUrl . '/img/Stamp%20Logo.png' : '';

$billDate = $appt['bill_date'] ?? '';
if (empty($billDate) || $billDate === '0000-00-00') {
    $billDate = date('Y-m-d');
    mysqli_query($conn, "UPDATE appointment_online SET bill_date='$billDate' WHERE appoint_id='$id'");
}
$billDateDisplay = date('d-M-Y', strtotime($billDate));
$billNo = !empty($appt['bill_id']) ? $appt['bill_id'] : $appt['appoint_register_id'];

$charge   = (float)($appt['amount'] ?? 0);
$concType = $appt['concession_type'] ?? '';
$concVal  = (float)($appt['concession_value'] ?? 0);
$discount = ($concType === 'percentage') ? round($charge * $concVal / 100, 2) : $concVal;
$net      = max($charge - $discount, 0);
$inWords  = function_exists('convertNumber') ? convertNumber($net) : '';

$payRows = [];
$payMode = $appt['amount_method'] ?? '';
if (strcasecmp($payMode, 'Both (Cash + UPI)') === 0) {
    $payRows[] = ['mode' => 'UPI',  'amount' => (float)($appt['transaction_amount'] ?? 0), 'ref' => $appt['transaction_number'] ?: '--'];
    $payRows[] = ['mode' => 'CASH', 'amount' => (float)($appt['cash_amount'] ?? 0),        'ref' => '--'];
} elseif (!empty($payMode)) {
    $payRows[] = ['mode' => strtoupper($payMode), 'amount' => $net, 'ref' => $appt['transaction_number'] ?: '--'];
} else {
    $payRows[] = ['mode' => '--', 'amount' => $net, 'ref' => '--'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Consultation Bill</title>
<link rel='shortcut icon' type='image/x-icon' href='assets/img/health.png'>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; background: #f5f5f5; color: #000; }
.no-print { background: #fff; padding: 8px 16px; text-align: right; border-bottom: 1px solid #ccc; position: sticky; top: 0; z-index: 99; }
.no-print button { padding: 6px 20px; background: #1a56a0; color: #fff; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; }
.page { width: 210mm; min-height: 297mm; margin: 16px auto; background: #fff; padding: 10mm 10mm 14mm; box-shadow: 0 1px 6px rgba(0,0,0,.18); position: relative; }
.receipt-title { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 1px; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 8px; text-transform: uppercase; }
.header-row { display: flex; justify-content: space-between; border-bottom: 1px solid #aaa; padding-bottom: 8px; margin-bottom: 8px; gap: 12px; }
.hosp-block { width: 52%; display: flex; align-items: flex-start; gap: 10px; }
.hosp-block .hosp-logo img { max-height: 80px; max-width: 80px; object-fit: contain; display: block; }
.hosp-block .hosp-text { flex: 1; }
.hosp-block .hosp-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
.hosp-block .hosp-text p { font-size: 11px; margin-bottom: 1px; line-height: 1.5; }
.pat-block { width: 46%; font-size: 11px; }
.pat-block table { width: 100%; border-collapse: collapse; }
.pat-block td { padding: 1.5px 4px; vertical-align: top; }
.pat-block td.lbl { white-space: nowrap; color: #333; }
.pat-block td.sep { padding: 1.5px 2px; }
.pat-block td.val { font-weight: bold; }
.items-table { width: 100%; border-collapse: collapse; font-size: 11px; }
.items-table thead tr { background: #222; color: #fff; }
.items-table thead th { padding: 5px 7px; font-weight: normal; text-align: left; white-space: nowrap; }
.items-table thead th.r { text-align: right; }
.items-table tbody td { padding: 4px 7px; border-bottom: 1px solid #e0e0e0; }
.items-table tbody td.r { text-align: right; }
.totals-wrap { display: flex; justify-content: flex-end; border-top: 1px solid #aaa; padding-top: 5px; }
.totals-tbl { width: 260px; border-collapse: collapse; font-size: 12px; }
.totals-tbl td { padding: 3px 7px; }
.totals-tbl td.r { text-align: right; }
.totals-tbl tr.bold td { font-weight: bold; background: #f0f0f0; }
.pay-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px; }
.pay-table thead tr { background: #222; color: #fff; }
.pay-table thead th { padding: 4px 7px; font-weight: normal; text-align: left; }
.pay-table tbody td { padding: 3px 7px; border-bottom: 1px solid #e0e0e0; }
.footer-thanks { font-size: 12px; font-weight: bold; margin-top: 6px; }
.footer-sig-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16px; }
.sig-stamp img { width: 100px; height: 100px; object-fit: contain; opacity: .8; }
.sig-stamp p { font-size: 10px; text-align: center; margin-top: 2px; border-top: 1px solid #000; padding-top: 2px; }
.footer-meta { font-size: 9.5px; color: #555; margin-top: 10px; border-top: 1px solid #ccc; padding-top: 4px; display: flex; justify-content: space-between; }
@media print {
    body { background: #fff; }
    .no-print { display: none !important; }
    .page { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 6mm 8mm 10mm; }
}
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">Print</button>
</div>

<div class="page">
    <div class="receipt-title">Consultation Bill</div>

    <div class="header-row">
        <div class="hosp-block">
            <div class="hosp-logo"><img src="<?= $logoSrc ?>" alt="Logo"></div>
            <div class="hosp-text">
                <div class="hosp-name"><?= htmlspecialchars($org['organization_name'] ?? '') ?></div>
                <?php if (!empty($org['address'])): ?><p><?= nl2br(htmlspecialchars($org['address'])) ?></p><?php endif; ?>
                <?php if (!empty($org['mobile_number'])): ?><p>Tel. No: <?= htmlspecialchars($org['mobile_number']) ?></p><?php endif; ?>
                <?php if (!empty($org['gst_number'])): ?><p>GST No – <?= htmlspecialchars($org['gst_number']) ?></p><?php endif; ?>
            </div>
        </div>
        <div class="pat-block">
            <table>
                <tr><td class="lbl">Name</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['patient_name']) ?></td></tr>
                <tr><td class="lbl">Age / Gender</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['age'] ?? '-') ?> / <?= htmlspecialchars($appt['gender'] ?? '-') ?></td></tr>
                <tr><td class="lbl">Mobile</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['mobile_number'] ?? '-') ?></td></tr>
                <tr><td class="lbl">UMR No</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appt['appoint_unicode']) ?></td></tr>
                <tr><td class="lbl">Bill No.</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($billNo) ?></td></tr>
                <tr><td class="lbl">Bill Dt.</td><td class="sep">:</td><td class="val"><?= $billDateDisplay ?></td></tr>
                <tr><td class="lbl">Visit Type</td><td class="sep">:</td><td class="val">OP</td></tr>
                <?php if (!empty($appt['doc_name'])): ?>
                <tr><td class="lbl">Ref / Per Dr.</td><td class="sep">:</td><td class="val">Dr. <?= htmlspecialchars($appt['doc_name']) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Service</th>
                <th>Department</th>
                <th class="r" style="width:36px">Qty</th>
                <th class="r" style="width:65px">Rate</th>
                <th class="r" style="width:80px">Disc (Value)</th>
                <th class="r" style="width:65px">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Dr. <?= htmlspecialchars($appt['doc_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($appt['departmentName'] ?? '-') ?></td>
                <td class="r">1</td>
                <td class="r"><?= number_format($charge, 2) ?></td>
                <td class="r"><?= number_format($discount, 2) ?></td>
                <td class="r"><?= number_format($net, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals-tbl">
            <tr><td>Gross Value</td><td class="r"><?= number_format($charge, 2) ?></td></tr>
            <tr><td>Discount Value</td><td class="r"><?= number_format($discount, 2) ?></td></tr>
            <tr class="bold"><td>Patient Paid</td><td class="r"><?= number_format($net, 2) ?></td></tr>
        </table>
    </div>

    <table class="pay-table">
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

    <div class="footer-thanks">
        Received with Thanks &nbsp;<strong>Rupees <?= htmlspecialchars(ucfirst($inWords)) ?></strong>
    </div>

    <div class="footer-sig-row">
        <div></div>
        <div class="sig-stamp">
            <?php if (!empty($stampSrc)): ?><img src="<?= $stampSrc ?>" alt="Stamp"><?php endif; ?>
            <p>(Authorised Signatory)</p>
        </div>
    </div>

    <div class="footer-meta">
        <span>Created By : <?= htmlspecialchars($org['organization_name'] ?? '') ?></span>
        <?php if (!empty($billGenBy)): ?><span>Bill generated by : <strong><?= htmlspecialchars($billGenBy) ?></strong></span><?php endif; ?>
        <span>Printed On : <?= date('d-M-Y h:i A') ?> &nbsp;&nbsp; Page 1/1</span>
    </div>
</div>

</body>
</html>
