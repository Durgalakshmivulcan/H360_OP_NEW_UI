<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');
// FIX_B_025: refund cascade flips the row matching invoice_id (not newest by appointment).

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('add', 'refunds.php', 'ajax');

if (!$SessionUserId) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

ensureRefundColumns($conn);

$invoiceId   = (int)($_POST['invoice_id']     ?? 0);
$reason      = mysqli_real_escape_string($conn, trim($_POST['reason']        ?? ''));
$action      = in_array($_POST['action'] ?? '', ['cancel', 'refund']) ? $_POST['action'] : 'cancel';
$refundAmt   = (float)($_POST['refund_amount'] ?? 0);
$now         = date('Y-m-d H:i:s');

if (!$invoiceId) { echo json_encode(['success' => false, 'message' => 'Invalid invoice.']); exit; }
if ($reason === '') { echo json_encode(['success' => false, 'message' => 'Reason is required.']); exit; }

$orgCond = ($SessionUserId != '1') ? "AND org_id='$SessionOrgId'" : '';
$invQ    = mysqli_query($conn, "SELECT * FROM invoice WHERE invoice_id='$invoiceId' AND status='1' $orgCond LIMIT 1");
$inv     = mysqli_fetch_assoc($invQ);
if (!$inv) { echo json_encode(['success' => false, 'message' => 'Invoice not found or already cancelled.']); exit; }

$netAmount = (float)$inv['net_amount'];

if ($action === 'cancel') {
    $refundAmt  = $netAmount;   // full amount returned
    $refundType = 'cancel';
} else {
    $refundType = 'refund';
    if ($refundAmt <= 0) {
        echo json_encode(['success' => false, 'message' => 'Enter a valid refund amount.']); exit;
    }
    if ($refundAmt > $netAmount) {
        echo json_encode(['success' => false, 'message' => 'Refund amount cannot exceed net amount ₹' . number_format($netAmount, 2) . '.']); exit;
    }
}

$refundAmtEsc = mysqli_real_escape_string($conn, $refundAmt);

mysqli_begin_transaction($conn);
try {
    $ok = mysqli_query($conn, "
        UPDATE invoice
        SET status='0',
            refund_reason='$reason',
            refunded_by='$SessionUserId',
            refunded_at='$now',
            refund_amount='$refundAmtEsc',
            refund_type='$refundType',
            modified_by='$SessionUserId'
        WHERE invoice_id='$invoiceId'
    ");
    if (!$ok) throw new Exception(mysqli_error($conn));

    $appointId = $inv['appointment_id'];
    $billType  = $inv['bill_type'];

    if ($billType === 'Consultation') {
        mysqli_query($conn, "
            UPDATE appointment_online
            SET invoice_payment='0', modified_by='$SessionUserId'
            WHERE appoint_register_id='$appointId'
        ");
    } elseif ($billType === 'Test') {
        // FIX_B_1100: patient_test_billing has no invoice_id column in this schema —
        // referencing it caused mysqli_sql_exception ("Unknown column 'invoice_id'
        // in 'where clause'") and rolled back the refund. Scope to appointment_id
        // + status='1' instead. If a future migration adds invoice_id we should
        // re-add the predicate.
        mysqli_query($conn, "
            UPDATE patient_test_billing
            SET status='0', refund_reason='$reason', refunded_by='$SessionUserId', refunded_at='$now'
            WHERE appointment_id='$appointId' AND status='1'
        ");
    } elseif ($billType === 'Medicine') {
        // FIX_B_1100: same root cause as the Test branch above — patient_medicine_billing
        // has no invoice_id column. Drop the predicate.
        mysqli_query($conn, "
            UPDATE patient_medicine_billing
            SET status='0', refund_reason='$reason', refunded_by='$SessionUserId', refunded_at='$now'
            WHERE appointment_id='$appointId' AND status='1'
        ");
    }

    $before = ['status' => '1'];
    $after  = ['status' => '0', 'refund_type' => $refundType, 'refund_amount' => $refundAmt, 'refund_reason' => $reason, 'refunded_by' => $SessionUserId, 'refunded_at' => $now];
    // FIX_B_1101: audit_log.action is an ENUM('create','update','delete','login','logout');
    // passing 'refund' or 'cancel' triggers a STRICT-mode "Data truncated" exception that
    // rolls back the entire refund transaction. Map both refund flavors to 'delete'
    // (the row IS being soft-deleted) and stash the actual refund_type in the after-json
    // so audit consumers can still distinguish refund vs cancel.
    audit_log($conn, 'Invoice', 'delete', 'invoice', $invoiceId, $before, $after);

    mysqli_commit($conn);

    $label   = $refundType === 'cancel' ? 'Cancelled' : 'Refunded';
    $amtFmt  = '₹' . number_format($refundAmt, 2);
    echo json_encode(['success' => true, 'message' => ucfirst(strtolower($billType)) . " bill {$label}. {$amtFmt} to be returned to patient."]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
