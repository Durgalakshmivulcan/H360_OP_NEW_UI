<?php
// B-2030 — Pharmacist dashboard: today's medicine bills + 7-day sparkline + revenue
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionOrgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;

function tableExists($conn, $tbl) {
    $tbl = mysqli_real_escape_string($conn, $tbl);
    $r = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
    return $r && mysqli_num_rows($r) > 0;
}

$response = [
    'today_count'     => 0,
    'today_revenue'   => 0,
    'avg_bill'        => 0,
    'spark_7day'      => array_fill(0, 7, 0),
    'bills'           => [],
    'source_present'  => false,
    'pending_count'   => 0,
];

if (tableExists($conn, 'patient_medicine_billing')) {
    $response['source_present'] = true;

    // KPIs (today)
    $kpiQ = mysqli_query($conn,
        "SELECT COUNT(*) AS cnt,
                COALESCE(SUM(net_amount),0) AS rev,
                COALESCE(AVG(net_amount),0) AS avgv
           FROM patient_medicine_billing
          WHERE org_id = '$SessionOrgId'
            AND DATE(created_at) = CURDATE()
            AND (status IS NULL OR status = 1)");
    if ($kpiQ && ($r = mysqli_fetch_assoc($kpiQ))) {
        $response['today_count']   = intval($r['cnt']);
        $response['today_revenue'] = floatval($r['rev']);
        $response['avg_bill']      = round(floatval($r['avgv']), 2);
    }

    // 7-day sparkline (oldest -> newest)
    $sparkQ = mysqli_query($conn,
        "SELECT DATE(created_at) d, COUNT(*) c
           FROM patient_medicine_billing
          WHERE org_id = '$SessionOrgId'
            AND created_at >= (CURDATE() - INTERVAL 6 DAY)
            AND (status IS NULL OR status = 1)
          GROUP BY DATE(created_at)");
    $byDay = [];
    while ($sparkQ && $row = mysqli_fetch_assoc($sparkQ)) {
        $byDay[$row['d']] = intval($row['c']);
    }
    $spark = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i day"));
        $spark[] = $byDay[$d] ?? 0;
    }
    $response['spark_7day'] = $spark;

    // FIX_B_2230: real schema has no `bill_id` (PK is `medicine_billing_id`)
    // and no `patient_name` column on patient_medicine_billing. Patient name
    // lives on appointment_online (joined via appointment_id) and falls back
    // to the bare patient_id when the join misses.
    $listQ = mysqli_query($conn,
        "SELECT b.medicine_billing_id AS bill_id,
                b.patient_id,
                b.net_amount,
                b.payment_method,
                b.created_at,
                COALESCE(NULLIF(ao.patient_name,''), b.patient_id, 'Unknown') AS pname
           FROM patient_medicine_billing b
           LEFT JOIN appointment_online ao
                  ON ao.appoint_register_id = b.appointment_id
          WHERE b.org_id = '$SessionOrgId'
            AND DATE(b.created_at) = CURDATE()
            AND (b.status IS NULL OR b.status = 1)
          ORDER BY b.created_at DESC
          LIMIT 30");
    while ($listQ && $row = mysqli_fetch_assoc($listQ)) {
        $response['bills'][] = [
            'bill_id'        => $row['bill_id'] ?? '',
            'patient_name'   => $row['pname'] ?? '',
            'patient_id'     => $row['patient_id'] ?? '',
            'amount'         => floatval($row['net_amount'] ?? 0),
            'payment_method' => $row['payment_method'] ?? '',
            'time'           => date('h:i A', strtotime($row['created_at'])),
        ];
    }
}

echo json_encode($response);
