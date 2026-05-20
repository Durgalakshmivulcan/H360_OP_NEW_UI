<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if (!$SessionUserId) { echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }

ensureRefundColumns($conn);

// Dropdown option value = appoint_id (integer PK)
$appointPk = (int)($_POST['appoint_register_id'] ?? 0);
$orgId     = mysqli_real_escape_string($conn, $_POST['org_id'] ?? $SessionOrgId);

if (!$appointPk) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment.']); exit;
}

$orgCond = ($SessionUserId != '1') ? "AND org_id='$SessionOrgId'" : '';

// Get appointment info
$apptQ = mysqli_query($conn, "
    SELECT appoint_id, appoint_unicode, appoint_register_id, patient_name, mobile_number, org_id, doctor_name
    FROM appointment_online
    WHERE appoint_status='1'
      AND appoint_id='$appointPk'
      $orgCond
    LIMIT 1
");
$appt = mysqli_fetch_assoc($apptQ);
if (!$appt) {
    echo json_encode(['success' => false, 'message' => 'Appointment not found.']); exit;
}

// Get doctor name
$docQ   = mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE doc_id='" . (int)$appt['doctor_name'] . "' LIMIT 1");
$docRow = mysqli_fetch_assoc($docQ);
$appt['doc_name'] = $docRow['doctor_name'] ?? '';

// Fetch all invoices for this appointment
$invoiceQ = mysqli_query($conn, "
    SELECT i.*,
           s.user_code  AS generated_by_code,
           rs.user_code AS refunded_by_code
    FROM invoice i
    LEFT JOIN security s  ON s.security_id  = i.created_by
    LEFT JOIN security rs ON rs.security_id = i.refunded_by
    WHERE i.appointment_id = '{$appt['appoint_register_id']}'
    ORDER BY i.created_at DESC
");
$invoices = [];
while ($row = mysqli_fetch_assoc($invoiceQ)) $invoices[] = $row;

echo json_encode(['success' => true, 'patient' => $appt, 'invoices' => $invoices]);
