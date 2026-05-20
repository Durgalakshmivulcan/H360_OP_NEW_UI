<?php
// B-2020: Doctor Dashboard — appointments per day for the last 7 days (incl. today).
require_once("../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');

header('Content-Type: application/json');

$SessionOrgId = (int) ($_SESSION['org_id'] ?? 0);
$docScope     = currentDoctorScopeSql('a.doctor_name');

$sql = "SELECT DATE(a.appoint_date) AS d, COUNT(*) AS c
        FROM appointment_online AS a
        WHERE a.appoint_status='1'
          AND a.org_id='$SessionOrgId'
          AND a.appoint_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
          $docScope
        GROUP BY DATE(a.appoint_date)";

$counts = [];
$res = mysqli_query($conn, $sql);
while ($res && ($r = mysqli_fetch_assoc($res))) $counts[$r['d']] = (int) $r['c'];

$series = [];
$labels = [];
$dows   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $labels[] = $dows[(int) date('w', strtotime($d))] . ' ' . date('j', strtotime($d));
    $series[] = $counts[$d] ?? 0;
}

echo json_encode([
    'success' => true,
    'labels'  => $labels,
    'series'  => $series,
]);
