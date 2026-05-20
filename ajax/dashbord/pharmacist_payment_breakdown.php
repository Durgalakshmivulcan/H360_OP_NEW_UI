<?php
// B-2030 — Pharmacist dashboard: today's bills split by payment method
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionOrgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;

function tableExists($conn, $tbl) {
    $tbl = mysqli_real_escape_string($conn, $tbl);
    $r = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
    return $r && mysqli_num_rows($r) > 0;
}

$response = ['items' => [], 'source_present' => false];

if (tableExists($conn, 'patient_medicine_billing')) {
    $response['source_present'] = true;
    $q = mysqli_query($conn,
        "SELECT COALESCE(NULLIF(payment_method,''),'Other') AS pm,
                COUNT(*) AS cnt,
                COALESCE(SUM(net_amount),0) AS total
           FROM patient_medicine_billing
          WHERE org_id = '$SessionOrgId'
            AND DATE(created_at) = CURDATE()
            AND (status IS NULL OR status = 1)
          GROUP BY pm
          ORDER BY cnt DESC");
    while ($q && $row = mysqli_fetch_assoc($q)) {
        $response['items'][] = [
            'method' => ucwords(strtolower($row['pm'])),
            'count'  => intval($row['cnt']),
            'total'  => floatval($row['total']),
        ];
    }
}

echo json_encode($response);
