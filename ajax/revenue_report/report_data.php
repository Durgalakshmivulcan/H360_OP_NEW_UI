<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

ensureRefundColumns($conn);

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id']     ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';

$fromDate = isset($_POST['fromDate']) && $_POST['fromDate'] !== '' ? $_POST['fromDate'] : date('Y-m-01');
$toDate   = isset($_POST['toDate'])   && $_POST['toDate']   !== '' ? $_POST['toDate']   : date('Y-m-d');
$doctorId = isset($_POST['doctor'])   && $_POST['doctor']   !== '' ? (int)$_POST['doctor'] : null;
$service  = isset($_POST['service'])  && $_POST['service']  !== '' ? $_POST['service']  : null;
$groupBy  = isset($_POST['groupBy'])  && in_array($_POST['groupBy'], ['day','week','month']) ? $_POST['groupBy'] : 'day';

switch ($groupBy) {
    case 'week':  $selectExpr = "DATE_FORMAT(inv.created_at, '%Y-W%u')"; break;
    case 'month': $selectExpr = "DATE_FORMAT(inv.created_at, '%Y-%m')";  break;
    default:      $selectExpr = "DATE(inv.created_at)";                   break;
}

$checkSecurity = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id='".mysqli_real_escape_string($conn,$SessionUserId)."'");
$securityType  = mysqli_fetch_assoc($checkSecurity)['security_type'] ?? '';

// Include ALL statuses (active + cancelled/refunded) so revenue reflects deductions
$where  = " WHERE inv.org_id='".mysqli_real_escape_string($conn,$SessionOrgId)."'";
$where .= " AND DATE(inv.created_at) BETWEEN '".mysqli_real_escape_string($conn,$fromDate)."' AND '".mysqli_real_escape_string($conn,$toDate)."'";

if ($doctorId) {
    $where .= " AND ao.doctor_name='".mysqli_real_escape_string($conn,$doctorId)."'";
}
if ($service) {
    $where .= " AND inv.bill_type='".mysqli_real_escape_string($conn,$service)."'";
}
if ($securityType === 'U') {
    $docRes      = mysqli_query($conn,"SELECT doc_id FROM doctors WHERE security_id='".mysqli_real_escape_string($conn,$SessionUserId)."' AND status='1' LIMIT 1");
    $mappedDocId = mysqli_fetch_assoc($docRes)['doc_id'] ?? '';
    if ($mappedDocId) {
        $where .= " AND (ao.doctor_name='$mappedDocId' OR ao.doctor_name IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id='".mysqli_real_escape_string($conn,$SessionUserId)."'))";
    }
}

$sql = "SELECT $selectExpr AS date_group,
               inv.*,
               d.doctor_name,
               s.service_name
        FROM invoice inv
        LEFT JOIN appointment_online ao ON ao.appoint_register_id = inv.appointment_id
        LEFT JOIN doctors  d ON d.doc_id    = ao.doctor_name
        LEFT JOIN services s ON s.service_id = d.doctor_services
        $where
        ORDER BY date_group ASC";

$result = $conn->query($sql);
if (!$result) { echo json_encode(['error' => $conn->error]); exit; }

$rows   = [];
$chart  = [];
$totals = ['gross'=>0, 'discount'=>0, 'tax'=>0, 'net'=>0, 'refunded'=>0, 'effective_net'=>0];

while ($row = $result->fetch_assoc()) {
    $dateGroup   = $row['date_group'];
    $doctorName  = $row['doctor_name']  ?: 'Unknown';
    $serviceName = $row['service_name'] ?: ($row['bill_type'] ?: 'General');
    $isActive    = ($row['status'] == '1');

    $gross      = (float)$row['amount'];
    $discount   = $gross - (float)$row['net_amount'];
    $tax        = isset($row['tax_value']) ? (float)$row['tax_value'] : 0;
    $net        = (float)$row['net_amount'] + $tax;
    $refundAmt  = $isActive ? 0 : (float)($row['refund_amount'] ?? $row['net_amount']);
    $refundType = $row['refund_type'] ?? '';

    if (!isset($chart[$dateGroup])) {
        $chart[$dateGroup] = ['gross'=>0, 'discount'=>0, 'tax'=>0, 'net'=>0, 'refunded'=>0];
    }

    if ($isActive) {
        $totals['gross']    += $gross;
        $totals['discount'] += $discount;
        $totals['tax']      += $tax;
        $totals['net']      += $net;
        $chart[$dateGroup]['gross']    += $gross;
        $chart[$dateGroup]['discount'] += $discount;
        $chart[$dateGroup]['tax']      += $tax;
        $chart[$dateGroup]['net']      += $net;
    } else {
        $totals['refunded'] += $refundAmt;
        $chart[$dateGroup]['refunded'] += $refundAmt;
    }

    if ($isActive) {
        $statusLabel = 'Active';
    } elseif ($refundType === 'refund') {
        $statusLabel = 'Refunded';
    } else {
        $statusLabel = 'Cancelled';
    }

    $rows[] = [
        'date_group'    => $dateGroup,
        'doctor_name'   => $doctorName,
        'service'       => $serviceName,
        'gross'         => $isActive ? $gross    : 0,
        'discount'      => $isActive ? $discount : 0,
        'tax'           => $isActive ? $tax      : 0,
        'net'           => $isActive ? $net      : 0,
        'status'        => $statusLabel,
        'refund_type'   => $refundType ?: '-',
        'refund_amount' => $refundAmt,
    ];
}

$totals['effective_net'] = $totals['net'] - $totals['refunded'];

ksort($chart);
$chartCategories = $grossSeries = $discountSeries = $taxSeries = $netSeries = $refundedSeries = [];
foreach ($chart as $dg => $vals) {
    $chartCategories[] = $dg;
    $grossSeries[]     = round((float)$vals['gross'],    2);
    $discountSeries[]  = round((float)$vals['discount'], 2);
    $taxSeries[]       = round((float)$vals['tax'],      2);
    $netSeries[]       = round((float)$vals['net'],      2);
    $refundedSeries[]  = round((float)$vals['refunded'], 2);
}

echo json_encode([
    'totals' => $totals,
    'chart'  => [
        'categories' => $chartCategories,
        'gross'      => $grossSeries,
        'discount'   => $discountSeries,
        'tax'        => $taxSeries,
        'net'        => $netSeries,
        'refunded'   => $refundedSeries,
    ],
    'table' => $rows,
]);
