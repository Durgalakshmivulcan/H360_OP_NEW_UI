<?php
require_once("config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$appoint_id = (int)($_GET['appoint_id'] ?? 0);

$ucQ       = mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='$SessionUserId' LIMIT 1");
$billGenBy = ($ucQ && $uc = mysqli_fetch_assoc($ucQ)) ? ($uc['user_code'] ?? '') : '';
if (!$appoint_id) { exit('Invalid request.'); }

// ── Appointment / Patient / Doctor ───────────────────────────────────────────
$apptQry = mysqli_query($conn, "
    SELECT ao.*, d.doctor_name AS doc_name, dept.departmentName
    FROM appointment_online ao
    LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
    LEFT JOIN department dept ON d.departments = dept.dept_id
    WHERE ao.appoint_id = '$appoint_id' AND ao.appoint_status = '1'
    LIMIT 1
") or die(mysqli_error($conn));
$appt = mysqli_fetch_assoc($apptQry);
if (!$appt) { exit('Appointment not found.'); }

$appt_register_id = $appt['appoint_register_id'];
$orgId            = $appt['org_id'];

// ── Organisation ─────────────────────────────────────────────────────────────
$orgQry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$orgId' AND status='1' LIMIT 1");
$org    = mysqli_fetch_assoc($orgQry);

// ── Logo (absolute URL for standalone tab) ────────────────────────────────────
$_host   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$appRoot = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$baseUrl = $_host . $appRoot;

$uploadDir = __DIR__ . '/organisation_images/';
$logoFile  = $org['logo'] ?? '';
$logoSrc   = (!empty($logoFile) && file_exists($uploadDir . $logoFile))
    ? $baseUrl . '/organisation_images/' . rawurlencode($logoFile)
    : $baseUrl . '/assets/img/h360.png';

// Stamp — per-org from organisation_stamp/
$stampFile = $org['org_stamp'] ?? '';
$stampSrc  = (!empty($stampFile) && file_exists(__DIR__ . '/organisation_stamp/' . $stampFile))
    ? $baseUrl . '/organisation_stamp/' . rawurlencode($stampFile)
    : '';

// ── Bill date ─────────────────────────────────────────────────────────────────
// FIX_B_910: side-effecting GET hardened — UPDATE is now set-once-on-first-print
// (idempotent at SQL level). Guarded by `bill_date IS NULL OR = '0000-00-00'`
// so re-renders never mutate. $appoint_id is int-cast at top of file.
$billDate = $appt['bill_date'] ?? '';
if (empty($billDate) || $billDate === '0000-00-00') {
    $billDate = date('Y-m-d');
    mysqli_query(
        $conn,
        "UPDATE appointment_online
            SET bill_date='$billDate'
          WHERE appoint_id='$appoint_id'
            AND (bill_date IS NULL OR bill_date = '0000-00-00')"
    );
}
$billDateDisplay = date('d-M-Y', strtotime($billDate));

// ── Consultation ──────────────────────────────────────────────────────────────
$consultCharge   = (float)($appt['amount'] ?? 0);
$concType        = $appt['concession_type']  ?? '';
$concValue       = (float)($appt['concession_value'] ?? 0);
$consultDiscount = ($concType === 'percentage')
    ? round($consultCharge * $concValue / 100, 2)
    : $concValue;
$consultNet = max($consultCharge - $consultDiscount, 0);

// ── Test bills ────────────────────────────────────────────────────────────────
$testBills = [];
$testQry   = mysqli_query($conn, "
    SELECT * FROM patient_test_billing
    WHERE appointment_id = '$appt_register_id' AND status = '1'
    ORDER BY test_billing_id ASC
") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($testQry)) { $testBills[] = $row; }

// Flatten test line-items
$testRows = [];
foreach ($testBills as $bill) {
    $tests = json_decode($bill['test_details'] ?? '[]', true);
    if (!is_array($tests)) continue;
    foreach ($tests as $t) {
        $std  = (float)($t['standard_price'] ?? 0);
        $doc  = (float)($t['doctor_price']   ?? 0);
        $disc = $std - $doc;
        $testRows[] = ['name' => $t['test_name'] ?? '-', 'charge' => $std, 'discount' => $disc, 'net' => $doc];
    }
}

// ── Grand totals ──────────────────────────────────────────────────────────────
$grandCharge   = $consultCharge;
$grandDiscount = $consultDiscount;
$grandNet      = $consultNet;
foreach ($testRows as $tr) {
    $grandCharge   += $tr['charge'];
    $grandDiscount += $tr['discount'];
    $grandNet      += $tr['net'];
}
$grandInWords = function_exists('convertNumber') ? convertNumber($grandNet) : '';

// ── Payment ───────────────────────────────────────────────────────────────────
$billNo = !empty($appt['bill_id']) ? $appt['bill_id'] : $appt_register_id;

// Accumulate amounts + references grouped by payment mode
$payMap = []; // key = normalised mode, value = ['amount' => float, 'refs' => string[]]

function addToPayMap(array &$map, string $mode, float $amount, string $ref): void {
    $key = strtoupper(trim($mode));
    if (!isset($map[$key])) $map[$key] = ['amount' => 0, 'refs' => []];
    $map[$key]['amount'] += $amount;
    if (!empty($ref) && $ref !== '--' && !in_array($ref, $map[$key]['refs'], true)) {
        $map[$key]['refs'][] = $ref;
    }
}

function spreadPayment(array &$map, string $mode, float $txnAmt, float $cashAmt, string $txnNo, float $fallback): void {
    if (strcasecmp($mode, 'Both (Cash + UPI)') === 0) {
        addToPayMap($map, 'UPI',  $txnAmt,  $txnNo);
        addToPayMap($map, 'CASH', $cashAmt, '');
    } elseif (!empty($mode)) {
        addToPayMap($map, $mode, $fallback, $txnNo);
    }
}

// Consultation
$consultPayMode = $appt['amount_method'] ?? '';
if (!empty($consultPayMode)) {
    spreadPayment($payMap, $consultPayMode,
        (float)($appt['transaction_amount'] ?? 0),
        (float)($appt['cash_amount']        ?? 0),
        $appt['transaction_number'] ?? '',
        $consultNet);
}

// Test billing
foreach ($testBills as $tb) {
    $tbMode = $tb['payment_method'] ?? '';
    if (empty($tbMode)) continue;
    spreadPayment($payMap, $tbMode,
        (float)($tb['transaction_amount'] ?? 0),
        (float)($tb['cash_amount']        ?? 0),
        $tb['transaction_number'] ?? '',
        (float)($tb['net_amount'] ?? 0));
}

// Build final display rows
$payRows = [];
foreach ($payMap as $mode => $data) {
    $payRows[] = [
        'mode'   => $mode,
        'amount' => $data['amount'],
        'ref'    => !empty($data['refs']) ? implode(', ', $data['refs']) : '--',
    ];
}

if (empty($payRows)) {
    $payRows[] = ['mode' => '--', 'amount' => $grandNet, 'ref' => '--'];
}

// Serial counter for items
$sno = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Out Patient Receipt</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; background: #f5f5f5; color: #000; }

.no-print { background: #fff; text-align: right; border-bottom: 1px solid #ccc; position: sticky; top: 0; z-index: 99; }
.no-print button { padding: 6px 20px; background: #1a56a0; color: #ffd966; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; }

/* Page */
.page {
    width: 210mm;
    min-height: 297mm;
    margin: 16px auto;
    background: #fff;
    padding: 10mm 10mm 14mm;
    box-shadow: 0 1px 6px rgba(0,0,0,.18);
    position: relative;
}

/* ── Title ── */
.receipt-title {
    text-align: center;
    font-size: 15px;
    font-weight: bold;
    letter-spacing: 1px;
    border-bottom: 2px solid #000;
    padding-bottom: 5px;
    margin-bottom: 8px;
    text-transform: uppercase;
}

/* ── Header: hospital left, patient right ── */
.header-row {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #aaa;
    padding-bottom: 8px;
    margin-bottom: 8px;
    gap: 12px;
}
.hosp-block { width: 52%; display: flex; align-items: flex-start; gap: 10px; }
.hosp-block .hosp-logo img { max-height: 80px; max-width: 80px; object-fit: contain; display: block; }
.hosp-block .hosp-text { flex: 1; }
.hosp-block .hosp-name { font-size: 14px; font-weight: bold; margin-bottom: 3px; }
.hosp-block p { font-size: 11px; margin-bottom: 1px; line-height: 1.5; }

.pat-block { width: 46%; font-size: 11px; }
.pat-block table { width: 100%; border-collapse: collapse; }
.pat-block table td { padding: 1.5px 4px; vertical-align: top; }
.pat-block table td.lbl { white-space: nowrap; font-weight: normal; color: #333; }
.pat-block table td.sep { padding: 1.5px 2px; }
.pat-block table td.val { font-weight: bold; }

/* ── Items table ── */
.items-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 0; }
.items-table thead tr { background: #222; color: #fff; }
.items-table thead th { padding: 5px 7px; font-weight: normal; text-align: left; white-space: nowrap; }
.items-table thead th.r { text-align: right; }
.items-table tbody td { padding: 4px 7px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
.items-table tbody td.r { text-align: right; }
.items-table tr.sec-header td { background: #ebebeb; font-weight: bold; padding: 3px 7px; font-size: 11px; letter-spacing: .5px; }
.items-table tbody tr:hover { background: #fafafa; }

/* ── Totals ── */
.totals-wrap { display: flex; justify-content: flex-end; border-top: 1px solid #aaa; padding-top: 5px; margin-top: 0; }
.totals-tbl { width: 260px; border-collapse: collapse; font-size: 12px; }
.totals-tbl td { padding: 3px 7px; }
.totals-tbl td.r { text-align: right; }
.totals-tbl tr.bold td { font-weight: bold; background: #f0f0f0; }

/* ── Payment table ── */
.pay-label { font-size: 11px; font-weight: bold; margin: 10px 0 3px; }
.pay-table { width: 100%; border-collapse: collapse; font-size: 11px; }
.pay-table thead tr { background: #222; color: #fff; }
.pay-table thead th { padding: 4px 7px; font-weight: normal; text-align: left; }
.pay-table tbody td { padding: 3px 7px; border-bottom: 1px solid #e0e0e0; }

/* ── Footer ── */
.footer-validity { font-size: 11px; margin-top: 10px; }
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
    <button onclick="window.print()">&#128438;&nbsp; Print</button>
</div>

<div class="page">

    <!-- ══ TITLE ══════════════════════════════════════════════════════════════ -->
    <div class="receipt-title">Out Patient Receipt</div>

    <!-- ══ HEADER ═════════════════════════════════════════════════════════════ -->
    <div class="header-row">

        <!-- Hospital: logo left, text right -->
        <div class="hosp-block">
            <?php if (!empty($logoSrc)): ?>
            <div class="hosp-logo"><img src="<?= $logoSrc ?>" alt="Logo"></div>
            <?php endif; ?>
            <div class="hosp-text">
                <div class="hosp-name"><?= htmlspecialchars($org['organization_name'] ?? '') ?></div>
                <?php if (!empty($org['address'])): ?>
                <p><?= nl2br(htmlspecialchars($org['address'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($org['mobile_number'])): ?>
                <p>Tel. No: <?= htmlspecialchars($org['mobile_number']) ?></p>
                <?php endif; ?>
                <?php if (!empty($org['gst_number'])): ?>
                <p>GST No – <?= htmlspecialchars($org['gst_number']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Patient / Bill info -->
        <div class="pat-block">
            <table>
                <tr>
                    <td class="lbl">Name</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($appt['patient_name']) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Age / Gender</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($appt['age'] ?? '-') ?> / <?= htmlspecialchars($appt['gender'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">Mobile</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($appt['mobile_number'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">UMR No</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($appt['appoint_unicode']) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Bill No.</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($billNo) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Bill Dt.</td><td class="sep">:</td>
                    <td class="val"><?= htmlspecialchars($billDateDisplay) ?></td>
                </tr>
                <tr>
                    <td class="lbl">Visit Type</td><td class="sep">:</td>
                    <td class="val">OP</td>
                </tr>
                <?php if (!empty($appt['doc_name'])): ?>
                <tr>
                    <td class="lbl">Ref / Per Dr.</td><td class="sep">:</td>
                    <td class="val">Dr. <?= htmlspecialchars($appt['doc_name']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

    </div><!-- /header-row -->

    <!-- ══ ITEMS TABLE ════════════════════════════════════════════════════════ -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>Service Name</th>
                <th>Department</th>
                <th class="r" style="width:36px">Qty</th>
                <th class="r" style="width:60px">Rate</th>
                <th class="r" style="width:72px">Disc (Value)</th>
                <th class="r" style="width:60px">Total</th>
            </tr>
        </thead>
        <tbody>

            <!-- Consultation section -->
            <tr class="sec-header"><td colspan="7">CONSULTATION</td></tr>
            <tr>
                <td><?= ++$sno ?></td>
                <td>Dr. <?= htmlspecialchars($appt['doc_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($appt['departmentName'] ?? '-') ?></td>
                <td class="r">1</td>
                <td class="r"><?= number_format($consultCharge, 2) ?></td>
                <td class="r"><?= number_format($consultDiscount, 2) ?></td>
                <td class="r"><?= number_format($consultNet, 2) ?></td>
            </tr>

            <?php if (!empty($testRows)): ?>
            <!-- Investigation section -->
            <tr class="sec-header"><td colspan="7">INVESTIGATION</td></tr>
            <?php foreach ($testRows as $tr): ?>
            <tr>
                <td><?= ++$sno ?></td>
                <td><?= htmlspecialchars($tr['name']) ?></td>
                <td>Lab</td>
                <td class="r">1</td>
                <td class="r"><?= number_format($tr['charge'], 2) ?></td>
                <td class="r"><?= number_format($tr['discount'], 2) ?></td>
                <td class="r"><?= number_format($tr['net'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>

        </tbody>
    </table>

    <!-- ══ TOTALS ══════════════════════════════════════════════════════════════ -->
    <div class="totals-wrap">
        <table class="totals-tbl">
            <tr>
                <td>Gross Value</td>
                <td class="r"><?= number_format($grandCharge, 2) ?></td>
            </tr>
            <tr>
                <td>Discount Value</td>
                <td class="r"><?= number_format($grandDiscount, 2) ?></td>
            </tr>
            <tr class="bold">
                <td>Patient Paid</td>
                <td class="r"><?= number_format($grandNet, 2) ?></td>
            </tr>
        </table>
    </div>

    <!-- ══ PAYMENT TABLE ════════════════════════════════════════════════════════ -->
    <table class="pay-table" style="margin-top:8px;">
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

    <!-- ══ FOOTER ════════════════════════════════════════════════════════════== -->
    <div class="footer-thanks">
        Received with Thanks &nbsp;<strong>Rupees <?= htmlspecialchars(ucfirst($grandInWords)) ?></strong>
    </div>

    <div class="footer-sig-row">
        <div></div>
        <div class="sig-stamp">
            <?php if (!empty($stampSrc)): ?>
                <img src="<?= $stampSrc ?>" alt="Stamp">
            <?php endif; ?>
            <p>(Authorised Signatory)</p>
        </div>
    </div>

    <div class="footer-meta">
        <span>Created By : <?= htmlspecialchars($org['organization_name'] ?? '') ?></span>
        <?php if (!empty($billGenBy)): ?><span>Bill generated by : <strong><?= htmlspecialchars($billGenBy) ?></strong></span><?php endif; ?>
        <span>Printed On : <?= date('d-M-Y h:i A') ?> &nbsp;&nbsp; Page 1/1</span>
    </div>

</div><!-- /.page -->
</body>
</html>
