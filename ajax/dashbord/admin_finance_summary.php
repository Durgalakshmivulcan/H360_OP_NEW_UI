<?php
/**
 * B-2040 — Admin dashboard: 30-day revenue trend.
 *
 * Returns: { days: [{date, label, pharmacy, billing, total}], totals: {...} }
 *
 * Sources:
 *   patient_medicine_billing.net_amount   (pharmacy)
 *   invoice.paid_amount                   (consultation/test billing — paid only)
 *
 * Honors:
 *   - org scoping via $_SESSION['org_id'] (SA bypass when role_id=1)
 *   - admin doctor-switcher via $_SESSION['admin_doctor_filter']
 *     (set by ajax/admin/setdoctorfilter.php on a sibling branch — defensive
 *     read here; absent => all doctors).
 *   - currentDoctorScopeSql() additionally narrows for actual doctor logins.
 */
require_once(__DIR__ . "/../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');
header('Content-Type: application/json; charset=utf-8');

$SessionRoleId = (int) ($_SESSION['role_id']     ?? 0);
$SessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$SessionOrgId  = (int) ($_SESSION['org_id']      ?? 0);

$isSA = ($SessionRoleId === 1 || $SessionUserId === 1);
$orgClause     = $isSA ? '' : " AND org_id = '$SessionOrgId' ";
$orgClauseInv  = $isSA ? '' : " AND inv.org_id = '$SessionOrgId' ";

// admin doctor-switcher (B-2002 sibling branch). Defensive read.
$adminDocFilter = isset($_SESSION['admin_doctor_filter']) ? trim((string) $_SESSION['admin_doctor_filter']) : '';
$adminDocId     = (int) $adminDocFilter; // 0 = all
$docScope_appt  = currentDoctorScopeSql('doctor_name'); // narrows for actual doctor users

// Range: last 30 days (inclusive of today)
$rows = [];
for ($i = 29; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $rows[$d] = ['date' => $d, 'label' => date('d M', strtotime($d)), 'pharmacy' => 0.0, 'billing' => 0.0, 'total' => 0.0];
}

// Pharmacy revenue (medicine billing) — sum non-refunded
$adminPharmacyClause = '';
if ($adminDocId > 0) {
    // join via appointment to filter by doctor
    $adminPharmacyClause = " AND EXISTS (
        SELECT 1 FROM appointment_online ao
        WHERE ao.appoint_register_id = patient_medicine_billing.appointment_id
          AND ao.doctor_name = '$adminDocId'
    ) ";
}
$sqlPharma = "SELECT DATE(created_at) AS d, COALESCE(SUM(net_amount),0) AS amt
              FROM patient_medicine_billing
              WHERE created_at >= (CURDATE() - INTERVAL 29 DAY)
                AND status = '1'
                $orgClause $adminPharmacyClause
              GROUP BY DATE(created_at)";
$res = mysqli_query($conn, $sqlPharma);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        if (isset($rows[$r['d']])) $rows[$r['d']]['pharmacy'] = (float) $r['amt'];
    }
}

// Invoice / consultation revenue
$adminInvClause = '';
if ($adminDocId > 0) {
    $adminInvClause = " AND EXISTS (
        SELECT 1 FROM appointment_online ao
        WHERE ao.appoint_register_id = inv.appointment_id
          AND ao.doctor_name = '$adminDocId'
    ) ";
}
$sqlInv = "SELECT DATE(inv.modified_at) AS d, COALESCE(SUM(inv.paid_amount),0) AS amt
           FROM invoice inv
           WHERE inv.modified_at >= (CURDATE() - INTERVAL 29 DAY)
             AND inv.status = 1
             $orgClauseInv $adminInvClause";
$sqlInv .= " GROUP BY DATE(inv.modified_at)";
$res2 = mysqli_query($conn, $sqlInv);
if ($res2) {
    while ($r = mysqli_fetch_assoc($res2)) {
        if (isset($rows[$r['d']])) $rows[$r['d']]['billing'] = (float) $r['amt'];
    }
}

$days = array_values($rows);
$totalPharma = 0; $totalBill = 0;
foreach ($days as &$row) {
    $row['total'] = round($row['pharmacy'] + $row['billing'], 2);
    $totalPharma += $row['pharmacy'];
    $totalBill   += $row['billing'];
}
unset($row);

echo json_encode([
    'days'   => $days,
    'totals' => [
        'pharmacy' => round($totalPharma, 2),
        'billing'  => round($totalBill, 2),
        'grand'    => round($totalPharma + $totalBill, 2),
    ],
    'scope'  => [
        'org_id'        => $isSA ? 0 : $SessionOrgId,
        'admin_doctor'  => $adminDocId,
        'session_doc'   => trim($docScope_appt) !== '' ? 'narrowed' : 'all',
    ],
]);
