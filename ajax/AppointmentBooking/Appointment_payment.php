<?php
header('Content-Type: application/json');
require_once '../../config/functions.php';

$SessionUserId = $_SESSION['security_id'] ?? 0;
$SessionOrgId  = $_SESSION['org_id'] ?? 0;

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('add', 'AppointmentOnline.php', 'ajax');


// Inputs
$appoint_id       = $_POST['appoint_id'] ?? '';
// FIX_B_034: ignore POST org_id (client-supplied cross-tenant); trust session only.
$org_id           = $SessionOrgId;
$doctor_fee       = isset($_POST['doctor_fee']) ? floatval($_POST['doctor_fee']) : 0;
$amount_method    = trim($_POST['amount_method'] ?? '');
$txn              = $_POST['transaction_number'] ?? '';
$txn_amount       = isset($_POST['transaction_amount']) && $_POST['transaction_amount'] !== '' ? floatval($_POST['transaction_amount']) : 0.0;
$cash_amount      = isset($_POST['cash_amount'])        && $_POST['cash_amount']        !== '' ? floatval($_POST['cash_amount'])        : 0.0;
$concession_name  = $_POST['concession_name'] ?? '';
$concession_type  = $_POST['concession_type'] ?? '';
$concession_value = isset($_POST['concession_value']) ? floatval($_POST['concession_value']) : 0;
$patient_id       = $_POST['patient_id'] ?? '';
$created_by       = $_POST['createdBy'] ?? $SessionUserId;
$invoice_payment  = "1";

// appointment_online.amount_method is VARCHAR — store the raw value ("Both (Cash + UPI)",
// "Cash", "UPI", etc.) so bill-print files can reconstruct the split breakdown.
// invoice.payment_method is ENUM; handled below per-row at INSERT time.
$isCashUpi = strcasecmp($amount_method, 'Both (Cash + UPI)') === 0;

if (empty($appoint_id) || empty($org_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing IDs']);
    exit;
}

// ---- Calculate final_amount ----
$final_amount = $doctor_fee;
if ($concession_type !== '' && $concession_value > 0) {
    if ($concession_type === 'percentage') {
        $final_amount = $doctor_fee - ($doctor_fee * $concession_value / 100);
    } else { // amount
        $final_amount = $doctor_fee - $concession_value;
    }
}
$final_amount = max(0, $final_amount);

// ---- UPDATE appointment_online ----
$updateSql = "
    UPDATE appointment_online SET
        amount_method      = '$amount_method',
        transaction_number = '$txn',
        transaction_amount = '$txn_amount',
        cash_amount        = '$cash_amount',
        concession_name    = '$concession_name',
        concession_type    = '$concession_type',
        concession_value   = '$concession_value',
        final_amount       = '$final_amount',
        invoice_payment    = '$invoice_payment'
    WHERE appoint_register_id = '$appoint_id' AND org_id = '$org_id'
";

$updateResult = mysqli_query($conn, $updateSql);
if (!$updateResult) {
    echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    exit;
}

// ---- INSERT INTO invoice ----
$billType          = 'Consultation';
$category          = 'Doctor Fee';
$balAmt            = 0.00;
$tests             = '';
$status            = 1;
$concessionPercent = ($concession_type === 'percentage') ? $concession_value : 0.00;
$concessionAmt     = ($concession_type !== 'percentage') ? $concession_value : 0.00;

if ($isCashUpi) {
    // Split into two invoice rows so Cash and UPI are recorded separately.
    // cash_amount + txn_amount = final_amount (user-entered values).
    $rowsCashUpi = [
        ['Cash', $cash_amount],
        ['UPI',  $txn_amount],
    ];
    foreach ($rowsCashUpi as [$rowMethod, $rowAmt]) {
        $rowInsert = "
            INSERT INTO invoice (
                patient_id, appointment_id, bill_type, category_type,
                amount, concession_type, concession_percent, concession_value,
                net_amount, paid_amount, balance_amount, tests,
                payment_method, status, org_id, created_by
            ) VALUES (
                '$patient_id', '$appoint_id', '$billType', '$category',
                '$rowAmt', '', '0', '0',
                '$rowAmt', '$rowAmt', '$balAmt', '$tests',
                '$rowMethod', '$status', '$org_id', '$created_by'
            )
        ";
        $rowResult = mysqli_query($conn, $rowInsert);
        if (!$rowResult) {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
            exit;
        }
    }
} else {
    // Single payment mode — coerce into the invoice ENUM.
    $paymentEnum    = ['Cash', 'Card', 'UPI', 'Cheque', 'Online', 'Other'];
    $candidate      = ucfirst(strtolower($amount_method));
    $invoiceMethod  = in_array($candidate, $paymentEnum, true) ? $candidate : 'Other';

    $insertSql = "
        INSERT INTO invoice (
            patient_id, appointment_id, bill_type, category_type,
            amount, concession_type, concession_percent, concession_value,
            net_amount, paid_amount, balance_amount, tests,
            payment_method, status, org_id, created_by
        ) VALUES (
            '$patient_id', '$appoint_id', '$billType', '$category',
            '$doctor_fee', '$concession_type', '$concessionPercent', '$concessionAmt',
            '$final_amount', '$final_amount', '$balAmt', '$tests',
            '$invoiceMethod', '$status', '$org_id', '$created_by'
        )
    ";
    $insertResult = mysqli_query($conn, $insertSql);
    if (!$insertResult) {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        exit;
    }
}

// Success
echo json_encode(['success' => true, 'final_amount' => $final_amount]);

mysqli_close($conn);
?>
