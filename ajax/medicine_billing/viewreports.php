<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// Absolute URL for viewBill so the JS-generated form posts to the right place
// regardless of the base page the AJAX response is injected into.
$_host    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$_selfDir = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$viewBillAbsUrl = $_host . $_selfDir . '/ajax/medicine_billing/viewBill.php';

$appointId = $_POST['appoint_register_id'] ?? '';
$orgId = $_POST['org_id'] ?? $SessionOrgId;

$appointmentQry = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_id='$appointId' AND org_id='$orgId' LIMIT 1") or die(mysqli_error($conn));
$appointment = mysqli_fetch_object($appointmentQry);

if (!$appointment) {
    echo "<div class='alert alert-warning mt-3'>No appointment found for the selected patient.</div>";
    exit;
}

$prescriptionQry = mysqli_query(
    $conn,
    "SELECT * FROM prescripition
     WHERE status='1'
       AND patient_uid='{$appointment->appoint_unicode}'
       AND appoint_register_id='{$appointment->appoint_register_id}'
       AND org_id='{$appointment->org_id}'
     ORDER BY prescription_id DESC
     LIMIT 1"
) or die(mysqli_error($conn));
$prescription = mysqli_fetch_assoc($prescriptionQry);

if (!$prescription) {
    echo "<div class='alert alert-warning mt-3'>No prescription found for this appointment.</div>";
    exit;
}

$prescriptionItems = json_decode($prescription['medicine_id'] ?? '[]', true);
if (!is_array($prescriptionItems) || empty($prescriptionItems)) {
    echo "<div class='alert alert-warning mt-3'>No prescribed medicines found for this appointment.</div>";
    exit;
}

// invoice_payment is a column in appointment_online (already fetched above)
$consultationPaid = !empty($appointment->invoice_payment) && $appointment->invoice_payment !== '0';

$prescriptionViewUrl = $consultationPaid
    ? "patientPrescription.php?ItemId={$prescription['prescription_id']}&OrgID={$appointment->org_id}"
    : '#';

$latestBillQry = mysqli_query(
    $conn,
    "SELECT * FROM patient_medicine_billing
     WHERE status='1'
       AND patient_id='{$appointment->appoint_unicode}'
       AND appointment_id='{$appointment->appoint_register_id}'
       AND org_id='{$appointment->org_id}'
     ORDER BY medicine_billing_id DESC
     LIMIT 1"
) or die(mysqli_error($conn));
$latestBill = mysqli_fetch_assoc($latestBillQry);

$savedItemMap = [];
if (!empty($latestBill['medicine_billing_id'])) {
    $savedItemsQry = mysqli_query(
        $conn,
        "SELECT * FROM patient_medicine_billing_items
         WHERE medicine_billing_id='{$latestBill['medicine_billing_id']}'
         ORDER BY medicine_billing_item_id ASC"
    ) or die(mysqli_error($conn));

    while ($savedItem = mysqli_fetch_assoc($savedItemsQry)) {
        $key = ($savedItem['medicine_id'] ?? '') . '|' . ($savedItem['medicine_name'] ?? '');
        $savedItemMap[$key] = $savedItem;
    }
}

$rows = [];

foreach ($prescriptionItems as $item) {
    $medicineId = (int) ($item['medicine_id'] ?? 0);
    $medicineName = trim($item['medicine_name'] ?? $item['drugName'] ?? '');
    $typeText = $item['type_text'] ?? '';
    $unitText = $item['unit_text'] ?? '';
    $dosageText = $item['dosageText'] ?? '';
    $whenText = $item['whenText'] ?? '';
    $timeText = $item['timeText'] ?? '';
    $durationValue = $item['duration_value'] ?? '';
    $duration = $item['duration'] ?? '';
    $notes = $item['notes'] ?? '';

    $masterQry = null;
    if ($medicineId > 0) {
        $masterQry = mysqli_query($conn, "SELECT price FROM medicines WHERE medicine_id='$medicineId' AND status='1' LIMIT 1");
    }

    if ((!$masterQry || mysqli_num_rows($masterQry) === 0) && $medicineName !== '') {
        $safeName = mysqli_real_escape_string($conn, $medicineName);
        $masterQry = mysqli_query($conn, "SELECT price FROM medicines WHERE medicine_name='$safeName' AND org_id='{$appointment->org_id}' AND status='1' LIMIT 1");
    }

    $masterPrice = 0;
    if ($masterQry && mysqli_num_rows($masterQry) > 0) {
        $masterRow = mysqli_fetch_assoc($masterQry);
        $masterPrice = (float) ($masterRow['price'] ?? 0);
    }

    $key = $medicineId . '|' . $medicineName;
    $saved = $savedItemMap[$key] ?? [];

    $price = isset($saved['price']) ? (float) $saved['price'] : $masterPrice;
    $discount = isset($saved['discount']) ? (float) $saved['discount'] : 0;
    $final = isset($saved['final_amount']) ? (float) $saved['final_amount'] : max($price - $discount, 0);
    $purchaseSource = $saved['purchase_source'] ?? 'Hospital Pharmacy';

    $rows[] = [
        'medicine_id' => $medicineId,
        'medicine_name' => $medicineName,
        'type_text' => $typeText,
        'unit_text' => $unitText,
        'dosage_text' => $dosageText,
        'when_text' => $whenText,
        'time_text' => $timeText,
        'duration_value' => $durationValue,
        'duration' => $duration,
        'notes' => $notes,
        'price' => $price,
        'discount' => $discount,
        'final_amount' => $final,
        'purchase_source' => $purchaseSource
    ];
}

$historyQry = mysqli_query(
    $conn,
    "SELECT * FROM patient_medicine_billing
     WHERE status='1'
       AND patient_id='{$appointment->appoint_unicode}'
       AND appointment_id='{$appointment->appoint_register_id}'
       AND org_id='{$appointment->org_id}'
     ORDER BY medicine_billing_id DESC"
) or die(mysqli_error($conn));
?>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Medicine Billing</h4>
        <div class="d-flex align-items-center gap-3">
            <?php if ($consultationPaid): ?>
                <a href="<?= htmlspecialchars($prescriptionViewUrl) ?>" target="_blank"
                   class="btn btn-primary btn-sm">
                    <i class="fa fa-file-medical me-1"></i> View Prescription
                </a>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled
                        title="Consultation fee not yet paid — prescription view locked">
                    <i class="fa fa-lock me-1"></i> View Prescription
                </button>
            <?php endif; ?>
            <div class="text-end">
                <div><strong>Patient:</strong> <?= htmlspecialchars($appointment->patient_name) ?></div>
                <div><strong>Appointment ID:</strong> <?= htmlspecialchars($appointment->appoint_register_id) ?></div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <input type="hidden" id="mb_patient_id" value="<?= htmlspecialchars($appointment->appoint_unicode) ?>">
        <input type="hidden" id="mb_appointment_id" value="<?= htmlspecialchars($appointment->appoint_register_id) ?>">
        <input type="hidden" id="mb_org_id" value="<?= htmlspecialchars($appointment->org_id) ?>">
        <input type="hidden" id="mb_prescription_id" value="<?= htmlspecialchars($prescription['prescription_id']) ?>">

        <?php
        $adviseText      = trim($prescription['advise'] ?? '');
        $personalNoteText = trim($prescription['personal_note'] ?? '');
        if (!empty($adviseText) || !empty($personalNoteText)): ?>
        <div class="row mb-3">
            <?php if (!empty($adviseText)): ?>
            <div class="col-lg-6 col-sm-12">
                <div class="alert alert-info mb-0">
                    <strong>Advice:</strong><br>
                    <?= nl2br(htmlspecialchars($adviseText)) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($personalNoteText)): ?>
            <div class="col-lg-6 col-sm-12">
                <div class="alert alert-info mb-0">
                    <strong>Personal Note:</strong><br>
                    <?= nl2br(htmlspecialchars($personalNoteText)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="medicineBillingTable">
                <thead class="text-center">
                    <tr>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Purchase Source</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) { ?>
                        <tr
                            data-medicine-id="<?= htmlspecialchars($row['medicine_id']) ?>"
                            data-medicine-name="<?= htmlspecialchars($row['medicine_name']) ?>"
                            data-type-text="<?= htmlspecialchars($row['type_text']) ?>"
                            data-unit-text="<?= htmlspecialchars($row['unit_text']) ?>"
                            data-dosage-text="<?= htmlspecialchars($row['dosage_text']) ?>"
                            data-when-text="<?= htmlspecialchars($row['when_text']) ?>"
                            data-time-text="<?= htmlspecialchars($row['time_text']) ?>"
                            data-duration-value="<?= htmlspecialchars($row['duration_value']) ?>"
                            data-duration="<?= htmlspecialchars($row['duration']) ?>"
                            data-notes="<?= htmlspecialchars($row['notes']) ?>"
                            data-default-price="<?= number_format($row['price'], 2, '.', '') ?>"
                            data-default-discount="<?= number_format($row['discount'], 2, '.', '') ?>"
                        >
                            <td>
                                <strong><?= htmlspecialchars(trim(($row['type_text'] ? $row['type_text'] . ' - ' : '') . $row['medicine_name'])) ?></strong>
                                <?php if ($row['unit_text'] !== '') { ?>
                                    <div class="text-muted small"><?= htmlspecialchars($row['unit_text']) ?></div>
                                <?php } ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($row['dosage_text']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['when_text']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['time_text']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars(trim($row['duration_value'] . ' ' . $row['duration'])) ?></div>
                            </td>
                            <td>
                                <select class="form-control form-select item-purchase-source">
                                    <option value="Hospital Pharmacy" <?= $row['purchase_source'] === 'Hospital Pharmacy' ? 'selected' : '' ?>>Hospital Pharmacy</option>
                                    <option value="Outside Pharmacy" <?= $row['purchase_source'] === 'Outside Pharmacy' ? 'selected' : '' ?>>Outside Pharmacy</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control price-input" value="<?= number_format($row['price'], 2, '.', '') ?>"></td>
                            <td><input type="text" class="form-control discount-input" value="<?= number_format($row['discount'], 2, '.', '') ?>"></td>
                            <td><input type="text" class="form-control final-amount" value="<?= number_format($row['final_amount'], 2, '.', '') ?>" readonly></td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="form-group col-lg-3 col-sm-12">
                <label for="payment_method">Hospital Payment Method</label>
                <select class="form-control form-select" id="payment_method">
                    <option value="">Select Payment Method</option>
                    <?php
                    $paymentQry = mysqli_query($conn, "SELECT payment_method FROM payment_method WHERE status='1' ORDER BY payment_method_id ASC") or die(mysqli_error($conn));
                    while ($payment = mysqli_fetch_object($paymentQry)) {
                        $selected = (($latestBill['payment_method'] ?? '') === $payment->payment_method) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($payment->payment_method) ?>" <?= $selected ?>><?= htmlspecialchars($payment->payment_method) ?></option>
                    <?php } ?>
                    <option value="Both (Cash + UPI)" <?= (($latestBill['payment_method'] ?? '') === 'Both (Cash + UPI)') ? 'selected' : '' ?>>Both (Cash + UPI)</option>
                </select>
                <small class="text-muted">Required only if any medicine is purchased at hospital.</small>
            </div>

            <!-- UPI Transaction Number (shown for UPI / Both) -->
            <div class="form-group col-lg-3 col-sm-12" id="mb_txnDetailsDiv" style="display:none;">
                <label for="mb_transaction_number">UPI Transaction Number</label>
                <input type="text" class="form-control" id="mb_transaction_number" placeholder="Enter UPI transaction number">
            </div>

            <!-- UPI Amount (shown for UPI / Both) -->
            <div class="form-group col-lg-2 col-sm-12" id="mb_txnAmountDiv" style="display:none;">
                <label for="mb_transaction_amount">UPI Amount (&#8377;)</label>
                <input type="text" class="form-control" id="mb_transaction_amount" placeholder="UPI amount">
            </div>

            <!-- Cash Amount (shown only for Both) -->
            <div class="form-group col-lg-2 col-sm-12" id="mb_cashAmountDiv" style="display:none;">
                <label for="mb_cash_amount">Cash Amount (&#8377;)</label>
                <input type="text" class="form-control" id="mb_cash_amount" placeholder="Cash amount">
            </div>
            <div class="form-group col-lg-2 col-sm-12">
                <label>Total</label>
                <input type="text" class="form-control" id="mb_total_amount" readonly>
            </div>
            <div class="form-group col-lg-2 col-sm-12">
                <label>Discount</label>
                <input type="text" class="form-control" id="mb_total_discount" readonly>
            </div>
            <div class="form-group col-lg-2 col-sm-12">
                <label>Net Amount</label>
                <input type="text" class="form-control" id="mb_net_amount" readonly>
            </div>
            <div class="form-group col-lg-1 col-sm-12">
                <label>Hospital</label>
                <input type="text" class="form-control" id="mb_hospital_total" readonly>
            </div>
            <div class="form-group col-lg-2 col-sm-12">
                <label>Outside</label>
                <input type="text" class="form-control" id="mb_outside_total" readonly>
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="button" class="btn btn-primary" id="saveMedicineBillingBtn">Save Medicine Billing</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4>Medicine Bill History</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table" id="medicineBillingHistoryTable" style="width:100%;">
                <thead class="text-center">
                    <tr>
                        <th>S.No</th>
                        <?php if ($SessionUserId == "1") { ?><th>Organization</th><?php } ?>
                        <th>Appointment ID</th>
                        <th>Patient ID</th>
                        <th>Total</th>
                        <th>Discount</th>
                        <th>Net</th>
                        <th>Purchase Mode</th>
                        <th>Payment Method</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    $i = 1;
                    if (mysqli_num_rows($historyQry) > 0) {
                        while ($history = mysqli_fetch_assoc($historyQry)) {
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <?php if ($SessionUserId == "1") { ?><td><?= htmlspecialchars(getUserNameByOrgId($conn, $history['org_id'])) ?></td><?php } ?>
                            <td><?= htmlspecialchars($history['appointment_id']) ?></td>
                            <td><?= htmlspecialchars($history['patient_id']) ?></td>
                            <td><?= number_format((float) $history['total_amount'], 2) ?></td>
                            <td><?= number_format((float) $history['discount'], 2) ?></td>
                            <td><?= number_format((float) $history['net_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($history['purchase_source']) ?></td>
                            <td><?= htmlspecialchars($history['payment_method'] ?: '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info"
                                    onclick="viewMedicineBill(<?= (int)$history['medicine_billing_id'] ?>)">View</button>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        $colspan = ($SessionUserId == "1") ? 10 : 9;
                        echo "<tr><td colspan='{$colspan}' class='text-muted'>No medicine billing records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
    function togglePurchaseRowState($row) {
        const source = $row.find('.item-purchase-source').val();
        const $price = $row.find('.price-input');
        const $discount = $row.find('.discount-input');

        if (source === 'Outside Pharmacy') {
            $price.val('0.00').prop('disabled', true);
            $discount.val('0.00').prop('disabled', true);
            $row.find('.final-amount').val('0.00');
        } else {
            $price.prop('disabled', false);
            $discount.prop('disabled', false);
        }
    }

    function recalculateMedicineBilling() {
        let total = 0;
        let discount = 0;
        let net = 0;
        let hospitalTotal = 0;
        let outsideTotal = 0;
        let hasHospitalPurchase = false;

        $('#medicineBillingTable tbody tr').each(function() {
            const source = $(this).find('.item-purchase-source').val();
            const price = parseFloat($(this).find('.price-input').val()) || 0;
            let rowDiscount = parseFloat($(this).find('.discount-input').val()) || 0;

            if (rowDiscount > price) {
                rowDiscount = price;
                $(this).find('.discount-input').val(price.toFixed(2));
            }

            const finalAmount = Math.max(price - rowDiscount, 0);
            $(this).find('.final-amount').val(finalAmount.toFixed(2));

            total += price;
            discount += rowDiscount;
            net += finalAmount;

            if (source === 'Hospital Pharmacy') {
                hospitalTotal += finalAmount;
                hasHospitalPurchase = true;
            } else {
                outsideTotal += finalAmount;
            }
        });

        $('#mb_total_amount').val(total.toFixed(2));
        $('#mb_total_discount').val(discount.toFixed(2));
        $('#mb_net_amount').val(net.toFixed(2));
        $('#mb_hospital_total').val(hospitalTotal.toFixed(2));
        $('#mb_outside_total').val(outsideTotal.toFixed(2));

        $('#payment_method').prop('disabled', !hasHospitalPurchase);
        if (!hasHospitalPurchase) {
            $('#payment_method').val('');
        }
    }

    $(document).on('input', '.price-input, .discount-input', recalculateMedicineBilling);

    $(document).on('change', '.item-purchase-source', function() {
        const $row = $(this).closest('tr');
        togglePurchaseRowState($row);
        // Restore default price/discount when switching back to Hospital Pharmacy
        if ($(this).val() === 'Hospital Pharmacy') {
            const $price = $row.find('.price-input');
            const $discount = $row.find('.discount-input');
            if ((parseFloat($price.val()) || 0) === 0) {
                $price.val($row.data('default-price'));
            }
            if ((parseFloat($discount.val()) || 0) === 0) {
                $discount.val($row.data('default-discount'));
            }
        }
        recalculateMedicineBilling();
    });

    // Initial setup: apply disabled state based on saved purchase source
    $('#medicineBillingTable tbody tr').each(function() {
        togglePurchaseRowState($(this));
    });
    recalculateMedicineBilling();

    // Show/hide UPI and cash fields based on payment method selection
    $('#payment_method').on('change', function() {
        const val = $(this).val().toLowerCase();
        if (!$(this).prop('disabled') && (val === 'upi' || val === 'both (cash + upi)')) {
            $('#mb_txnDetailsDiv').show();
            $('#mb_txnAmountDiv').show();
        } else {
            $('#mb_txnDetailsDiv').hide();
            $('#mb_txnAmountDiv').hide();
            $('#mb_transaction_number').val('');
            $('#mb_transaction_amount').val('');
        }
        if (!$(this).prop('disabled') && val === 'both (cash + upi)') {
            $('#mb_cashAmountDiv').show();
        } else {
            $('#mb_cashAmountDiv').hide();
            $('#mb_cash_amount').val('');
        }
    });

    var _viewBillUrl = <?= json_encode($viewBillAbsUrl) ?>;

    function viewMedicineBill(id) {
        if (!id) {
            swal('Error', 'No billing record ID found.', 'error');
            return;
        }
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = _viewBillUrl;
        f.target = '_blank';
        var inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'medicine_billing_id';
        inp.value = id;
        f.appendChild(inp);
        document.body.appendChild(f);
        f.submit();
        document.body.removeChild(f);
    }

    $(document).on('click', '#saveMedicineBillingBtn', function() {
        const paymentMethod = $('#payment_method').val();
        const hospitalTotal = parseFloat($('#mb_hospital_total').val()) || 0;

        if (hospitalTotal > 0 && !paymentMethod) {
            swal('Warning', 'Please select the hospital payment method.', 'warning');
            return;
        }

        const medicines = [];
        $('#medicineBillingTable tbody tr').each(function() {
            medicines.push({
                medicine_id: $(this).data('medicine-id'),
                medicine_name: $(this).data('medicine-name'),
                type_text: $(this).data('type-text'),
                unit_text: $(this).data('unit-text'),
                dosage_text: $(this).data('dosage-text'),
                when_text: $(this).data('when-text'),
                time_text: $(this).data('time-text'),
                duration_value: $(this).data('duration-value'),
                duration: $(this).data('duration'),
                notes: $(this).data('notes'),
                price: parseFloat($(this).find('.price-input').val()) || 0,
                discount: parseFloat($(this).find('.discount-input').val()) || 0,
                final_amount: parseFloat($(this).find('.final-amount').val()) || 0,
                purchase_source: $(this).find('.item-purchase-source').val()
            });
        });

        $.ajax({
            url: 'ajax/medicine_billing/saveMedicineBill.php',
            type: 'POST',
            dataType: 'json',
            data: {
                patient_id: $('#mb_patient_id').val(),
                appointment_id: $('#mb_appointment_id').val(),
                org_id: $('#mb_org_id').val(),
                prescription_id: $('#mb_prescription_id').val(),
                advice: <?= json_encode($prescription['advise'] ?? '') ?>,
                personal_note: <?= json_encode($prescription['personal_note'] ?? '') ?>,
                medicines: JSON.stringify(medicines),
                total_amount: $('#mb_total_amount').val(),
                discount: $('#mb_total_discount').val(),
                net_amount: $('#mb_net_amount').val(),
                hospital_total: $('#mb_hospital_total').val(),
                outside_total: $('#mb_outside_total').val(),
                payment_method: paymentMethod,
                transaction_number: $('#mb_transaction_number').val(),
                transaction_amount: $('#mb_transaction_amount').val(),
                cash_amount: $('#mb_cash_amount').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#mb_transaction_number').val('');
                    $('#mb_transaction_amount').val('');
                    $('#mb_cash_amount').val('');
                    $('#mb_txnDetailsDiv, #mb_txnAmountDiv, #mb_cashAmountDiv').hide();
                    swal('', response.message, 'success').then(() => {
                        loadMedicineBilling();
                    });
                } else {
                    swal('Error', response.message || 'Unable to save medicine billing.', 'error');
                }
            }
        });
    });

</script>
