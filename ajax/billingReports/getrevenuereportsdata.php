<?php
// FIX_B_005 — periodicRevenue.php data writer (thin adapter).
// Repoints to the same aggregation logic used by op_revenue_kpis.php so we
// avoid duplicate code paths. Shapes response for periodicRevenue.php JS.
require_once(__DIR__ . "/../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$from = $_POST['fromDate'] ?? $_POST['from'] ?? date('Y-m-01');
$to   = $_POST['toDate']   ?? $_POST['to']   ?? date('Y-m-d');
$from = mysqli_real_escape_string($conn, $from);
$to   = mysqli_real_escape_string($conn, $to);
$org  = mysqli_real_escape_string($conn, (string)$SessionOrgId);

$orgFilter = $org !== '' ? "AND a.org_id='$org'" : "";

// KPI block — mirrors op_revenue_kpis.php aggregation.
$kpiQ = mysqli_query($conn, "
    SELECT
        COALESCE(SUM(a.amount), 0)                                AS gross,
        COALESCE(SUM(a.amount - COALESCE(a.final_amount,a.amount)), 0) AS discount,
        0                                                          AS tax,
        COALESCE(SUM(COALESCE(a.final_amount,a.amount)), 0)        AS net,
        COUNT(*)                                                   AS visits
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$from' AND '$to'
      $orgFilter
");
$kpi = $kpiQ ? mysqli_fetch_assoc($kpiQ) : null;

// Periodic series (one row per day in range)
$series = [];
$sQ = mysqli_query($conn, "
    SELECT DATE(a.appoint_date) AS period,
           COUNT(*)                                                       AS visits,
           COALESCE(SUM(a.amount), 0)                                     AS gross,
           COALESCE(SUM(a.amount - COALESCE(a.final_amount,a.amount)), 0) AS discount,
           COALESCE(SUM(COALESCE(a.final_amount,a.amount)), 0)            AS net
    FROM appointment_online a
    WHERE a.appoint_status='1'
      AND DATE(a.appoint_date) BETWEEN '$from' AND '$to'
      $orgFilter
    GROUP BY DATE(a.appoint_date)
    ORDER BY period ASC
");
while ($sQ && ($r = mysqli_fetch_assoc($sQ))) {
    $series[] = [
        'period'   => $r['period'],
        'visits'   => (int)   $r['visits'],
        'gross'    => (float) $r['gross'],
        'discount' => (float) $r['discount'],
        'tax'      => 0,
        'net'      => (float) $r['net'],
    ];
}

echo json_encode([
    'kpi' => [
        'visits'   => (int)  ($kpi['visits']   ?? 0),
        'gross'    => (float)($kpi['gross']    ?? 0),
        'discount' => (float)($kpi['discount'] ?? 0),
        'tax'      => (float)($kpi['tax']      ?? 0),
        'net'      => (float)($kpi['net']      ?? 0),
    ],
    'series'  => $series,
    'rows'    => $series, // alias — some periodicRevenue handlers iterate `rows`
    'from'    => $from,
    'to'      => $to,
]);
