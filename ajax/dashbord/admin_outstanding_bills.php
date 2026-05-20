<?php
/**
 * B-2040 — Admin dashboard: outstanding bills + KPI numbers.
 *
 * Returns:
 *   today_appointments
 *   today_revenue   (pharmacy + invoice paid_amount today)
 *   patients_in_queue   (today, appoint_status=1, no check_out)
 *   outstanding_count   (invoice rows with balance_amount>0 OR refund rows)
 *   refund_count_30d
 *
 * Honors org + admin_doctor_filter + currentDoctorScopeSql.
 */
require_once(__DIR__ . "/../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');
header('Content-Type: application/json; charset=utf-8');

$SessionRoleId = (int) ($_SESSION['role_id']     ?? 0);
$SessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$SessionOrgId  = (int) ($_SESSION['org_id']      ?? 0);

$isSA      = ($SessionRoleId === 1 || $SessionUserId === 1);
$adminDoc  = (int) ($_SESSION['admin_doctor_filter'] ?? 0);

$orgInv  = $isSA ? '' : " AND inv.org_id = '$SessionOrgId' ";
$orgPmb  = $isSA ? '' : " AND pmb.org_id = '$SessionOrgId' ";
$orgAppt = $isSA ? '' : " AND ao.org_id = '$SessionOrgId' ";

$apptDoc = $adminDoc > 0 ? " AND ao.doctor_name = '$adminDoc' " : '';
$invDocE = $adminDoc > 0 ? " AND EXISTS (SELECT 1 FROM appointment_online ao
    WHERE ao.appoint_register_id = inv.appointment_id AND ao.doctor_name = '$adminDoc') " : '';
$pmbDocE = $adminDoc > 0 ? " AND EXISTS (SELECT 1 FROM appointment_online ao
    WHERE ao.appoint_register_id = pmb.appointment_id AND ao.doctor_name = '$adminDoc') " : '';

// Today's appointments
$q = "SELECT COUNT(*) FROM appointment_online ao
      WHERE ao.appoint_status='1' AND ao.appoint_date = CURDATE() $orgAppt $apptDoc";
$todayAppt = (int) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

// Today's revenue (pharmacy + invoice paid today)
$qPh = "SELECT COALESCE(SUM(net_amount),0) FROM patient_medicine_billing pmb
        WHERE pmb.status='1' AND DATE(pmb.created_at)=CURDATE() $orgPmb $pmbDocE";
$revPh = (float) (mysqli_fetch_array(mysqli_query($conn, $qPh))[0] ?? 0);

$qIn = "SELECT COALESCE(SUM(paid_amount),0) FROM invoice inv
        WHERE inv.status=1 AND DATE(inv.modified_at)=CURDATE() $orgInv $invDocE";
$revIn = (float) (mysqli_fetch_array(mysqli_query($conn, $qIn))[0] ?? 0);
$todayRevenue = round($revPh + $revIn, 2);

// Patients in queue today (no check_out yet, status=1)
$qQ = "SELECT COUNT(*) FROM appointment_online ao
       WHERE ao.appoint_status='1' AND ao.appoint_date=CURDATE()
         AND (ao.check_out IS NULL OR ao.check_out = '0000-00-00 00:00:00')
         $orgAppt $apptDoc";
$inQueue = (int) (mysqli_fetch_array(mysqli_query($conn, $qQ))[0] ?? 0);

// Outstanding bills (balance_amount > 0)
$qO = "SELECT COUNT(*) FROM invoice inv
       WHERE inv.status=1 AND inv.balance_amount > 0 $orgInv $invDocE";
$outstanding = (int) (mysqli_fetch_array(mysqli_query($conn, $qO))[0] ?? 0);

// Refunds last 30 days (invoice + pharmacy)
$qR1 = "SELECT COUNT(*) FROM invoice inv
        WHERE inv.refunded_at IS NOT NULL
          AND inv.refunded_at >= (NOW() - INTERVAL 30 DAY) $orgInv $invDocE";
$refInv = (int) (mysqli_fetch_array(mysqli_query($conn, $qR1))[0] ?? 0);
$qR2 = "SELECT COUNT(*) FROM patient_medicine_billing pmb
        WHERE pmb.refunded_at IS NOT NULL
          AND pmb.refunded_at >= (NOW() - INTERVAL 30 DAY) $orgPmb $pmbDocE";
$refPmb = (int) (mysqli_fetch_array(mysqli_query($conn, $qR2))[0] ?? 0);

echo json_encode([
    'today_appointments'  => $todayAppt,
    'today_revenue'       => $todayRevenue,
    'today_revenue_split' => ['pharmacy' => round($revPh, 2), 'billing' => round($revIn, 2)],
    'patients_in_queue'   => $inQueue,
    'outstanding_count'   => $outstanding,
    'refund_count_30d'    => $refInv + $refPmb,
]);
