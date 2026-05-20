<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('add', 'medicine_bill.php', 'ajax');


if (!$SessionUserId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$patientId = mysqli_real_escape_string($conn, $_POST['patient_id'] ?? '');
$appointmentId = mysqli_real_escape_string($conn, $_POST['appointment_id'] ?? '');
$prescriptionId = (int) ($_POST['prescription_id'] ?? 0);
$orgId = mysqli_real_escape_string($conn, $_POST['org_id'] ?? $SessionOrgId);
$advice = mysqli_real_escape_string($conn, $_POST['advice'] ?? '');
$personalNote = mysqli_real_escape_string($conn, $_POST['personal_note'] ?? '');
$paymentMethod     = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
$transactionNumber = mysqli_real_escape_string($conn, $_POST['transaction_number'] ?? '');
$transactionAmount = isset($_POST['transaction_amount']) && $_POST['transaction_amount'] !== '' ? (float) $_POST['transaction_amount'] : null;
$cashAmount        = isset($_POST['cash_amount']) && $_POST['cash_amount'] !== '' ? (float) $_POST['cash_amount'] : null;
$totalAmount = (float) ($_POST['total_amount'] ?? 0);
$discount = (float) ($_POST['discount'] ?? 0);
$netAmount = (float) ($_POST['net_amount'] ?? 0);
$hospitalTotal = (float) ($_POST['hospital_total'] ?? 0);
$outsideTotal = (float) ($_POST['outside_total'] ?? 0);
$hospitalGross = 0;
$hospitalDiscount = 0;

$medicines = json_decode($_POST['medicines'] ?? '[]', true);
if (empty($patientId) || empty($appointmentId) || !is_array($medicines) || empty($medicines)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete medicine billing data']);
    exit;
}

$hasHospital = false;
$hasOutside = false;
foreach ($medicines as $medicine) {
    $source = $medicine['purchase_source'] ?? 'Hospital Pharmacy';
    if ($source === 'Hospital Pharmacy') {
        $hasHospital = true;
        $hospitalGross += (float) ($medicine['price'] ?? 0);
    }
    if ($source === 'Outside Pharmacy') {
        $hasOutside = true;
    }
    if ($source === 'Hospital Pharmacy') {
        $hospitalDiscount += (float) ($medicine['discount'] ?? 0);
    }
}

if ($hasHospital && $paymentMethod === '') {
    echo json_encode(['success' => false, 'message' => 'Payment method is required for hospital purchases']);
    exit;
}

$purchaseSource = 'Hospital Pharmacy';
if ($hasHospital && $hasOutside) {
    $purchaseSource = 'Mixed';
} elseif ($hasOutside && !$hasHospital) {
    $purchaseSource = 'Outside Pharmacy';
}

$medicineJson = mysqli_real_escape_string($conn, json_encode($medicines));

mysqli_begin_transaction($conn);

try {
    // Ensure payment split columns exist
    $checkTxnCol = mysqli_query($conn, "SHOW COLUMNS FROM patient_medicine_billing LIKE 'transaction_number'");
    if (mysqli_num_rows($checkTxnCol) == 0) {
        mysqli_query($conn, "ALTER TABLE patient_medicine_billing ADD COLUMN transaction_number VARCHAR(100) DEFAULT NULL, ADD COLUMN transaction_amount DECIMAL(10,2) DEFAULT NULL, ADD COLUMN cash_amount DECIMAL(10,2) DEFAULT NULL");
    }

    $txnAmountSql  = $transactionAmount !== null ? "'$transactionAmount'" : "NULL";
    $cashAmountSql = $cashAmount !== null ? "'$cashAmount'" : "NULL";

    $insertBill = "
        INSERT INTO patient_medicine_billing
            (patient_id, appointment_id, prescription_id, medicine_details, advice, personal_note, total_amount, discount, net_amount, purchase_source, payment_method, transaction_number, transaction_amount, cash_amount, status, org_id, created_by, created_at)
        VALUES
            ('$patientId', '$appointmentId', '$prescriptionId', '$medicineJson', '$advice', '$personalNote', '$totalAmount', '$discount', '$netAmount', '$purchaseSource', " . ($paymentMethod !== '' ? "'$paymentMethod'" : "NULL") . ", '$transactionNumber', $txnAmountSql, $cashAmountSql, '1', '$orgId', '$SessionUserId', NOW())
    ";

    if (!mysqli_query($conn, $insertBill)) {
        throw new Exception(mysqli_error($conn));
    }

    $newId = mysqli_insert_id($conn);

    foreach ($medicines as $medicine) {
        $medicineId = isset($medicine['medicine_id']) && $medicine['medicine_id'] !== '' ? (int) $medicine['medicine_id'] : 'NULL';
        $medicineName = mysqli_real_escape_string($conn, $medicine['medicine_name'] ?? '');
        $typeText = mysqli_real_escape_string($conn, $medicine['type_text'] ?? '');
        $unitText = mysqli_real_escape_string($conn, $medicine['unit_text'] ?? '');
        $dosageText = mysqli_real_escape_string($conn, $medicine['dosage_text'] ?? '');
        $whenText = mysqli_real_escape_string($conn, $medicine['when_text'] ?? '');
        $timeText = mysqli_real_escape_string($conn, $medicine['time_text'] ?? '');
        $durationValue = mysqli_real_escape_string($conn, $medicine['duration_value'] ?? '');
        $duration = mysqli_real_escape_string($conn, $medicine['duration'] ?? '');
        $notes = mysqli_real_escape_string($conn, $medicine['notes'] ?? '');
        $itemPurchaseSource = mysqli_real_escape_string($conn, $medicine['purchase_source'] ?? 'Hospital Pharmacy');

        $price = (float) ($medicine['price'] ?? 0);
        $itemDiscount = (float) ($medicine['discount'] ?? 0);
        $finalAmount = (float) ($medicine['final_amount'] ?? 0);

        if ($itemPurchaseSource === 'Outside Pharmacy') {
            $price = 0;
            $itemDiscount = 0;
            $finalAmount = 0;
        }

        $insertItem = "
            INSERT INTO patient_medicine_billing_items
                (medicine_billing_id, medicine_id, medicine_name, type_text, unit_text, dosage_text, when_text, time_text, duration_value, duration, notes, price, discount, final_amount, purchase_source, org_id, created_by, created_at)
            VALUES
                ('$newId', $medicineId, '$medicineName', '$typeText', '$unitText', '$dosageText', '$whenText', '$timeText', '$durationValue', '$duration', '$notes', '$price', '$itemDiscount', '$finalAmount', '$itemPurchaseSource', '$orgId', '$SessionUserId', NOW())
        ";

        if (!mysqli_query($conn, $insertItem)) {
            throw new Exception(mysqli_error($conn));
        }
    }

    if ($hospitalTotal > 0) {
        $billType       = 'Medicine';
        $categoryType   = 'Medicine fee';
        $concessionType = $hospitalDiscount > 0 ? 'Percentage' : 'None';

        if (strcasecmp($paymentMethod, 'Both (Cash + UPI)') === 0) {
            // Split into two invoice rows so Cash and UPI are recorded separately.
            $splitRows = [
                ['Cash', $cashAmount ?? 0],
                ['UPI',  $transactionAmount ?? 0],
            ];
            foreach ($splitRows as [$rowMethod, $rowAmt]) {
                $rowInsert = "
                    INSERT INTO invoice
                        (patient_id, appointment_id, bill_type, category_type, amount, concession_type, concession_value, net_amount, payment_method, status, org_id, created_by, created_at)
                    VALUES
                        ('$patientId', '$appointmentId', '$billType', '$categoryType', '$rowAmt', 'None', '0', '$rowAmt', '$rowMethod', '1', '$orgId', '$SessionUserId', NOW())
                ";
                if (!mysqli_query($conn, $rowInsert)) {
                    throw new Exception(mysqli_error($conn));
                }
            }
        } else {
            $invoiceEnum          = ['Cash', 'Card', 'UPI', 'Cheque', 'Online', 'Other'];
            $invoicePaymentMethod = in_array($paymentMethod, $invoiceEnum, true) ? $paymentMethod : 'Other';
            $insertInvoice = "
                INSERT INTO invoice
                    (patient_id, appointment_id, bill_type, category_type, amount, concession_type, concession_value, net_amount, payment_method, status, org_id, created_by, created_at)
                VALUES
                    ('$patientId', '$appointmentId', '$billType', '$categoryType', '$hospitalGross', '$concessionType', '$hospitalDiscount', '$hospitalTotal', '$invoicePaymentMethod', '1', '$orgId', '$SessionUserId', NOW())
            ";
            if (!mysqli_query($conn, $insertInvoice)) {
                throw new Exception(mysqli_error($conn));
            }
        }
    }

    $after = [
        'patient_id' => $patientId,
        'appointment_id' => $appointmentId,
        'prescription_id' => $prescriptionId,
        'medicine_details' => $medicines,
        'advice' => $advice,
        'personal_note' => $personalNote,
        'total_amount' => $totalAmount,
        'discount' => $discount,
        'net_amount' => $netAmount,
        'hospital_gross' => $hospitalGross,
        'hospital_discount' => $hospitalDiscount,
        'hospital_total' => $hospitalTotal,
        'outside_total' => $outsideTotal,
        'purchase_source' => $purchaseSource,
        'payment_method' => $paymentMethod
    ];

    audit_log($conn, 'MedicineBill', 'create', 'patient_medicine_billing', $newId, null, $after);

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Medicine billing saved successfully']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
