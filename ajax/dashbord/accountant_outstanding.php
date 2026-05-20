<?php
// B-2050 Accountant dashboard: outstanding bills queue with age in days
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$org   = (int)$SessionOrgId;
$limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 15;

$q = mysqli_query($conn,
    "SELECT inv.invoice_id, inv.patient_id, inv.appointment_id, inv.bill_type,
            inv.net_amount, inv.paid_amount,
            GREATEST(inv.net_amount - inv.paid_amount, 0) AS balance,
            DATEDIFF(CURDATE(), DATE(inv.created_at)) AS age_days,
            DATE(inv.created_at) AS bill_date,
            COALESCE(ao.patient_name, 'Unknown') AS patient_name
     FROM invoice inv
     LEFT JOIN appointment_online ao ON ao.appoint_register_id = inv.appointment_id
     /* FIX_B_2051: removed `JOIN patients p` — no `patients` table exists in
        the OP-only schema; patient identity is inline on appointment_online. */
     WHERE inv.org_id='$org' AND inv.status='1'
     AND (inv.balance_amount > 0 OR inv.paid_amount < inv.net_amount)
     ORDER BY age_days DESC, inv.invoice_id DESC
     LIMIT $limit");

$rows = [];
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = [
            'invoice_id'   => (int)$r['invoice_id'],
            'patient_id'   => $r['patient_id'],
            'patient_name' => $r['patient_name'],
            'bill_type'    => $r['bill_type'],
            'net_amount'   => (float)$r['net_amount'],
            'paid_amount'  => (float)$r['paid_amount'],
            'balance'      => (float)$r['balance'],
            'age_days'     => (int)$r['age_days'],
            'bill_date'    => $r['bill_date'],
            'aged'         => ((int)$r['age_days']) > 7,
        ];
    }
}

echo json_encode(['rows' => $rows, 'count' => count($rows)]);
