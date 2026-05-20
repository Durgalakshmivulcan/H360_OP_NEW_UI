<?php
/**
 * B-2040 — Admin dashboard: recent staff activity feed.
 *
 * Composite stream:
 *   - last 5 staff logins  (from audit_log, action='login')
 *   - last 5 receptionist actions today (appointment_online rows created today)
 *   - last 5 pharmacy actions today (patient_medicine_billing rows created today)
 *
 * Each row carries a stable href (target page) so the partial can navigate.
 *
 * Honors org scoping. Doctor-switcher narrows the receptionist + pharmacy
 * streams (filter by appointment.doctor_name).
 */
require_once(__DIR__ . "/../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');
header('Content-Type: application/json; charset=utf-8');

$SessionRoleId = (int) ($_SESSION['role_id']     ?? 0);
$SessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$SessionOrgId  = (int) ($_SESSION['org_id']      ?? 0);

$isSA      = ($SessionRoleId === 1 || $SessionUserId === 1);
$adminDoc  = (int) ($_SESSION['admin_doctor_filter'] ?? 0);

$orgClauseAudit = $isSA ? '' : " AND a.org_id = '$SessionOrgId' ";
$orgClauseAppt  = $isSA ? '' : " AND ao.org_id = '$SessionOrgId' ";
$orgClausePmb   = $isSA ? '' : " AND pmb.org_id = '$SessionOrgId' ";

// 1) Recent logins
$logins = [];
$qL = "SELECT a.ts, a.user_id, a.ip, s.admin_name, s.security_type, s.role_id, r.role_name
       FROM audit_log a
       LEFT JOIN security s ON s.security_id = a.user_id
       LEFT JOIN roles    r ON r.role_id = s.role_id
       WHERE a.action = 'login' $orgClauseAudit
       ORDER BY a.ts DESC LIMIT 5";
$rL = mysqli_query($conn, $qL);
if ($rL) {
    while ($row = mysqli_fetch_assoc($rL)) {
        $logins[] = [
            'kind'   => 'login',
            'when'   => $row['ts'],
            'who'    => $row['admin_name'] ?: ('user#' . (int) $row['user_id']),
            'role'   => $row['role_name']  ?: '—',
            'detail' => 'Logged in' . ($row['ip'] ? ' from ' . $row['ip'] : ''),
            'href'   => 'audit_log.php',
        ];
    }
}

// 2) Receptionist activity (appointments created today)
$apptDocFilter = $adminDoc > 0 ? " AND ao.doctor_name = '$adminDoc' " : '';
$recept = [];
$qR = "SELECT ao.create_date_time, ao.created_by, ao.patient_name, ao.appoint_register_id,
              s.admin_name, s.role_id, r.role_name, d.doctor_name
       FROM appointment_online ao
       LEFT JOIN security s ON s.security_id = ao.created_by
       LEFT JOIN roles    r ON r.role_id = s.role_id
       LEFT JOIN doctors  d ON d.doc_id = ao.doctor_name
       WHERE DATE(ao.create_date_time) = CURDATE()
         AND ao.appoint_status = '1'
         $orgClauseAppt $apptDocFilter
       ORDER BY ao.create_date_time DESC
       LIMIT 5";
$rR = mysqli_query($conn, $qR);
if ($rR) {
    while ($row = mysqli_fetch_assoc($rR)) {
        $recept[] = [
            'kind'   => 'appointment',
            'when'   => $row['create_date_time'],
            'who'    => $row['admin_name'] ?: ('user#' . (int) $row['created_by']),
            'role'   => $row['role_name']  ?: 'Receptionist',
            'detail' => 'Booked ' . $row['patient_name'] . ' → ' . ($row['doctor_name'] ?: 'doctor'),
            'href'   => 'AppointmentOnline.php',
        ];
    }
}

// 3) Pharmacy activity (medicine bills created today)
$pmbDocFilter = '';
if ($adminDoc > 0) {
    $pmbDocFilter = " AND EXISTS (
        SELECT 1 FROM appointment_online ao
        WHERE ao.appoint_register_id = pmb.appointment_id
          AND ao.doctor_name = '$adminDoc'
    ) ";
}
$pharm = [];
$qP = "SELECT pmb.created_at, pmb.created_by, pmb.net_amount, pmb.patient_id,
              s.admin_name, r.role_name
       FROM patient_medicine_billing pmb
       LEFT JOIN security s ON s.security_id = pmb.created_by
       LEFT JOIN roles    r ON r.role_id = s.role_id
       WHERE DATE(pmb.created_at) = CURDATE()
         AND pmb.status = '1'
         $orgClausePmb $pmbDocFilter
       ORDER BY pmb.created_at DESC
       LIMIT 5";
$rP = mysqli_query($conn, $qP);
if ($rP) {
    while ($row = mysqli_fetch_assoc($rP)) {
        $pharm[] = [
            'kind'   => 'pharmacy',
            'when'   => $row['created_at'],
            'who'    => $row['admin_name'] ?: ('user#' . (int) $row['created_by']),
            'role'   => $row['role_name']  ?: 'Pharmacist',
            'detail' => 'Bill ₹' . number_format((float) $row['net_amount'], 2) . ' for ' . $row['patient_id'],
            'href'   => 'billing_report.php',
        ];
    }
}

echo json_encode([
    'logins'        => $logins,
    'receptionist'  => $recept,
    'pharmacy'      => $pharm,
]);
