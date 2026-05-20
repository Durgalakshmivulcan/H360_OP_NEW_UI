<?php
// B-2050 Accountant dashboard: today's + week revenue, outstanding bills, refunds
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = (int)($_SESSION['security_id'] ?? 0);
$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$today = date('Y-m-d');
// ISO Mon-Sun week
$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd   = date('Y-m-d', strtotime('sunday this week'));
// Previous Mon-Sun week
$prevWeekStart = date('Y-m-d', strtotime('monday last week'));
$prevWeekEnd   = date('Y-m-d', strtotime('sunday last week'));

function scalarSum(mysqli $conn, string $sql): float {
    $r = mysqli_query($conn, $sql);
    if (!$r) return 0.0;
    $row = mysqli_fetch_array($r);
    return (float)($row[0] ?? 0);
}
function scalarCount(mysqli $conn, string $sql): int {
    $r = mysqli_query($conn, $sql);
    if (!$r) return 0;
    $row = mysqli_fetch_array($r);
    return (int)($row[0] ?? 0);
}

$org = (int)$SessionOrgId;

// ---------- Today's revenue ----------
$invToday = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'");
$testToday = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM patient_test_billing
     WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'");
$revenueToday = $invToday + $testToday;

// ---------- This week's revenue ----------
$invWeek = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$weekStart' AND '$weekEnd'");
$testWeek = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM patient_test_billing
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$weekStart' AND '$weekEnd'");
$revenueWeek = $invWeek + $testWeek;

// ---------- Previous week's revenue (for WoW%) ----------
$invPrevWeek = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$prevWeekStart' AND '$prevWeekEnd'");
$testPrevWeek = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM patient_test_billing
     WHERE org_id='$org' AND status='1'
     AND DATE(created_at) BETWEEN '$prevWeekStart' AND '$prevWeekEnd'");
$revenuePrevWeek = $invPrevWeek + $testPrevWeek;
$wowPct = $revenuePrevWeek > 0
    ? round((($revenueWeek - $revenuePrevWeek) / $revenuePrevWeek) * 100, 1)
    : ($revenueWeek > 0 ? 100.0 : 0.0);

// ---------- Outstanding bills (balance_amount > 0 OR paid_amount < net_amount) ----------
$outstandingCount = scalarCount($conn,
    "SELECT COUNT(*) FROM invoice
     WHERE org_id='$org' AND status='1'
     AND (balance_amount > 0 OR paid_amount < net_amount)");
$outstandingSum = scalarSum($conn,
    "SELECT COALESCE(SUM(GREATEST(net_amount - paid_amount, 0)),0) FROM invoice
     WHERE org_id='$org' AND status='1'
     AND (balance_amount > 0 OR paid_amount < net_amount)");

// ---------- Refunds today ----------
// Refund table not present in schema yet. Use status=0 invoices changed today as proxy.
$refundsTodayCount = scalarCount($conn,
    "SELECT COUNT(*) FROM invoice
     WHERE org_id='$org' AND status='0' AND DATE(modified_at)='$today'");
$refundsTodaySum = scalarSum($conn,
    "SELECT COALESCE(SUM(net_amount),0) FROM invoice
     WHERE org_id='$org' AND status='0' AND DATE(modified_at)='$today'");

// ---------- Today's billable appointments (appointments that produced a bill today) ----------
$apptToday = scalarCount($conn,
    "SELECT COUNT(DISTINCT appointment_id) FROM invoice
     WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'");

echo json_encode([
    'revenue_today'        => $revenueToday,
    'revenue_today_count'  => (int)scalarCount($conn,
        "SELECT COUNT(*) FROM invoice WHERE org_id='$org' AND status='1' AND DATE(created_at)='$today'"),
    'revenue_week'         => $revenueWeek,
    'revenue_prev_week'    => $revenuePrevWeek,
    'wow_pct'              => $wowPct,
    'outstanding_count'    => $outstandingCount,
    'outstanding_amount'   => $outstandingSum,
    'refunds_today_count'  => $refundsTodayCount,
    'refunds_today_amount' => $refundsTodaySum,
    'appointments_today'   => $apptToday,
    'week_start'           => $weekStart,
    'week_end'             => $weekEnd,
]);
