<?php
// CSV export for OP Revenue & Invoices Report
//
// Streams aggregated revenue data to a CSV file.  Columns: Date,
// Service, Doctor, Payer, Invoices, Gross, Discount, Tax, Net.
// Filters are identical to those used by the list and KPI endpoints.

require_once("../../config/functions.php");
require_once ("../../include/auth_guard.php");
assertRole(['admin','org_admin']);

$org_id = (int) ($_SESSION['org_id'] ?? 0);
$from   = $_GET['from'] ?? null;
$to     = $_GET['to'] ?? null;
$service= trim($_GET['service'] ?? '');
$doctor = trim($_GET['doctor'] ?? '');
$payer  = trim($_GET['payer'] ?? '');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=op_revenue_'.date('Ymd_His').'.csv');

$out = fopen('php://output','w');
fputcsv($out, ['Date','Service','Doctor','Payer','Invoices','Gross','Discount','Tax','Net']);

$where = 'WHERE inv.org_id = ?';
$types = 'i';
$params= [$org_id];

if ($from) { $where .= ' AND inv.created_at >= ?'; $types.='s'; $params[] = $from.' 00:00:00'; }
if ($to)   { $where .= ' AND inv.created_at <= ?'; $types.='s'; $params[] = $to.' 23:59:59'; }
if ($service !== '') { $where .= ' AND svc.name = ?'; $types.='s'; $params[] = $service; }
if ($doctor !== '') { $where .= ' AND v.doctor_id = ?'; $types.='i'; $params[] = (int)$doctor; }
if ($payer !== '') { $where .= ' AND inv.payer = ?'; $types.='s'; $params[] = $payer; }

$sql = "SELECT
    DATE(inv.created_at)     AS date,
    svc.name                 AS service,
    v.doctor_id             AS doctor,
    inv.payer               AS payer,
    COUNT(DISTINCT inv.invoice_id) AS invoices,
    SUM(inv.amount)         AS gross,
    SUM(inv.discount)       AS discount,
    SUM(inv.tax)            AS tax,
    SUM(inv.net_amount)     AS net
  FROM invoices inv
  JOIN visits v ON v.visit_id = inv.visit_id
  JOIN invoice_items ii ON ii.invoice_id = inv.invoice_id
  JOIN services svc ON svc.service_id = ii.item_id
  $where
  GROUP BY date, service, doctor, payer
  ORDER BY date DESC, service ASC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
      $row['date'],
      $row['service'],
      $row['doctor'],
      $row['payer'],
      $row['invoices'],
      $row['gross'],
      $row['discount'],
      $row['tax'],
      $row['net']
    ]);
}
$stmt->close();