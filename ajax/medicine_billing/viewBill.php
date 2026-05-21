<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$billingId = (int)($_POST['medicine_billing_id'] ?? 0);
if (!$billingId) { exit('Invalid request.'); }

$billingQry = mysqli_query($conn, "SELECT * FROM patient_medicine_billing WHERE medicine_billing_id='$billingId' AND status='1' LIMIT 1") or die(mysqli_error($conn));
$billing = mysqli_fetch_assoc($billingQry);
if (!$billing) { exit('Billing record not found.'); }

$ucQ       = mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='" . (int)($billing['created_by'] ?? 0) . "' LIMIT 1");
$billGenBy = ($ucQ && $uc = mysqli_fetch_assoc($ucQ)) ? ($uc['user_code'] ?? '') : '';

$orgId = $billing['org_id'];

// Appointment + doctor + department
$appointQry = mysqli_query($conn, "
    SELECT ao.*, d.doctor_name AS doc_name, dept.departmentName
    FROM appointment_online ao
    LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
    LEFT JOIN department dept ON d.departments = dept.dept_id
    WHERE ao.appoint_register_id='{$billing['appointment_id']}' AND ao.org_id='$orgId'
    LIMIT 1
") or die(mysqli_error($conn));
$appointment = mysqli_fetch_assoc($appointQry);

// Bill date
$billDate = $appointment['bill_date'] ?? '';
if (empty($billDate) || $billDate === '0000-00-00') {
    $billDate = date('Y-m-d');
    mysqli_query($conn, "UPDATE appointment_online SET bill_date='$billDate' WHERE appoint_register_id='{$billing['appointment_id']}'");
}
$billDateDisplay = date('d-M-Y', strtotime($billDate));
$billNo = !empty($appointment['bill_id']) ? $appointment['bill_id'] : $billing['appointment_id'];

// Organisation
$orgQry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$orgId' AND status='1' LIMIT 1") or die(mysqli_error($conn));
$org = mysqli_fetch_assoc($orgQry);

// Base URL for images (standalone tab)
$_host    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$_appRoot = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl  = $_host . $_appRoot;

$uploadDir = __DIR__ . '/../../organisation_images/';
$logoFile  = $org['logo'] ?? '';
$logoSrc   = (!empty($logoFile) && file_exists($uploadDir . $logoFile))
    ? $baseUrl . '/organisation_images/' . rawurlencode($logoFile)
    : $baseUrl . '/assets/img/h360.png';

$stampFile = $org['org_stamp'] ?? '';
$stampSrc  = (!empty($stampFile) && file_exists(__DIR__ . '/../../organisation_stamp/' . $stampFile))
    ? $baseUrl . '/organisation_stamp/' . rawurlencode($stampFile)
    : '';

// Billing items
$itemQry = mysqli_query($conn, "SELECT * FROM patient_medicine_billing_items WHERE medicine_billing_id='$billingId' ORDER BY medicine_billing_item_id ASC") or die(mysqli_error($conn));
$items = [];
while ($item = mysqli_fetch_assoc($itemQry)) { $items[] = $item; }
if (empty($items)) {
    $fallback = json_decode($billing['medicine_details'] ?? '[]', true);
    if (is_array($fallback)) $items = $fallback;
}

// Totals
$grossTotal = 0;
foreach ($items as $item) { $grossTotal += (float)($item['price'] ?? 0); }
$netTotal  = (float)($billing['net_amount'] ?? 0);
$discTotal = $grossTotal - $netTotal;
$inWords   = function_exists('convertNumber') ? convertNumber($netTotal) : '';

// Payment rows
$payRows = [];
$payMode = $billing['payment_method'] ?? '';
if (strcasecmp($payMode, 'Both (Cash + UPI)') === 0) {
    $payRows[] = ['mode' => 'UPI',  'amount' => (float)($billing['transaction_amount'] ?? 0), 'ref' => $billing['transaction_number'] ?: '--'];
    $payRows[] = ['mode' => 'CASH', 'amount' => (float)($billing['cash_amount'] ?? 0),        'ref' => '--'];
} elseif (!empty($payMode)) {
    $payRows[] = ['mode' => strtoupper($payMode), 'amount' => $netTotal, 'ref' => $billing['transaction_number'] ?: '--'];
} else {
    $payRows[] = ['mode' => '--', 'amount' => $netTotal, 'ref' => '--'];
}

// Note
$noteQry  = mysqli_query($conn, "SELECT note FROM bill_sizes WHERE status='1' AND pagetype='3' AND org_id='$orgId' ORDER BY bill_size_id ASC LIMIT 1");
$billNote = mysqli_fetch_assoc($noteQry)['note'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Medicine Bill</title>
<link rel='shortcut icon' type='image/x-icon' href='assets/img/health.png'>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; background: #f5f5f5; color: #000; }
.no-print { background: #fff; text-align: right; border-bottom: 1px solid #ccc; position: sticky; top: 0; z-index: 99; }
.no-print button { padding: 6px 20px; background: #1a56a0; color: #ffd966; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; }
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
.items-table tbody td { padding: 4px 7px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
.items-table tbody td.r { text-align: right; }
.sub-text { font-size: 10px; color: #888; margin-top: 2px; }
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
.bill-note { font-size: 11px; margin-top: 10px; }
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
    <div class="receipt-title">Medicine Bill</div>

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
                <tr><td class="lbl">Name</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appointment['patient_name'] ?? $billing['patient_id']) ?></td></tr>
                <tr><td class="lbl">Age / Gender</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appointment['age'] ?? '-') ?> / <?= htmlspecialchars($appointment['gender'] ?? '-') ?></td></tr>
                <tr><td class="lbl">Mobile</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($appointment['mobile_number'] ?? '-') ?></td></tr>
                <tr><td class="lbl">UMR No</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($billing['patient_id']) ?></td></tr>
                <tr><td class="lbl">Bill No.</td><td class="sep">:</td><td class="val"><?= htmlspecialchars($billNo) ?></td></tr>
                <tr><td class="lbl">Bill Dt.</td><td class="sep">:</td><td class="val"><?= $billDateDisplay ?></td></tr>
                <tr><td class="lbl">Visit Type</td><td class="sep">:</td><td class="val">OP</td></tr>
                <?php if (!empty($appointment['doc_name'])): ?>
                <tr><td class="lbl">Ref / Per Dr.</td><td class="sep">:</td><td class="val">Dr. <?= htmlspecialchars($appointment['doc_name']) ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Medicine</th>
                <th>Dosage / Duration</th>
                <th>Source</th>
                <th class="r" style="width:65px">Rate</th>
                <th class="r" style="width:72px">Disc (Value)</th>
                <th class="r" style="width:65px">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($items as $item):
                $medicineName = trim((($item['type_text'] ?? '') ? $item['type_text'] . ' - ' : '') . ($item['medicine_name'] ?? ''));
                $dosageLine   = implode(' ', array_filter([
                    $item['dosage_text']   ?? '',
                    $item['when_text']     ?? '',
                    $item['time_text']     ?? '',
                    trim(($item['duration_value'] ?? '') . ' ' . ($item['duration'] ?? ''))
                ]));
                $price       = (float)($item['price']        ?? 0);
                $discount    = (float)($item['discount']     ?? 0);
                $finalAmount = (float)($item['final_amount'] ?? 0);
                $source      = $item['purchase_source'] ?? $billing['purchase_source'] ?? '';
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td>
                    <strong><?= htmlspecialchars($medicineName) ?></strong>
                    <?php if (!empty($item['unit_text'])): ?><div class="sub-text"><?= htmlspecialchars($item['unit_text']) ?></div><?php endif; ?>
                    <?php if (!empty($item['notes'])): ?><div class="sub-text"><em><?= htmlspecialchars($item['notes']) ?></em></div><?php endif; ?>
                </td>
                <td style="font-size:11px;"><?= htmlspecialchars($dosageLine) ?></td>
                <td><?= htmlspecialchars($source) ?></td>
                <td class="r"><?= number_format($price, 2) ?></td>
                <td class="r"><?= $discount > 0 ? number_format($discount, 2) : '-' ?></td>
                <td class="r"><?= number_format($finalAmount, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals-tbl">
            <tr><td>Gross Value</td><td class="r"><?= number_format($grossTotal, 2) ?></td></tr>
            <tr><td>Discount Value</td><td class="r"><?= number_format($discTotal, 2) ?></td></tr>
            <tr class="bold"><td>Patient Paid</td><td class="r"><?= number_format($netTotal, 2) ?></td></tr>
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

    <?php if (!empty($billNote)): ?>
    <div class="bill-note"><b>NOTE:</b> <?= htmlspecialchars($billNote) ?></div>
    <?php endif; ?>

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
