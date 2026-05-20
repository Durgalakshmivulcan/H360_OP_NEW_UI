<?php
// B-2030 — Pharmacist dashboard: top medicines billed
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionOrgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;
$range = isset($_GET['range']) && $_GET['range'] === 'week' ? 'week' : 'today';

function tableExists($conn, $tbl) {
    $tbl = mysqli_real_escape_string($conn, $tbl);
    $r = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
    return $r && mysqli_num_rows($r) > 0;
}

$response = ['range' => $range, 'items' => [], 'source_present' => false];

if (tableExists($conn, 'patient_medicine_billing_items') &&
    tableExists($conn, 'patient_medicine_billing')) {
    $response['source_present'] = true;
    $clause = $range === 'week'
        ? "b.created_at >= (CURDATE() - INTERVAL 6 DAY)"
        : "DATE(b.created_at) = CURDATE()";

    // FIX_B_2230: items table FK is `medicine_billing_id` (NOT `bill_id`),
    // and there is no `quantity` column — each row already represents one
    // dispensed line. We count rows for `qty` and reuse the same value for
    // `units` since unit-count is not tracked at the row level.
    $q = mysqli_query($conn,
        "SELECT i.medicine_name, COUNT(*) AS qty, COUNT(*) AS units
           FROM patient_medicine_billing_items i
           JOIN patient_medicine_billing b ON b.medicine_billing_id = i.medicine_billing_id
          WHERE b.org_id = '$SessionOrgId'
            AND $clause
            AND (b.status IS NULL OR b.status = 1)
          GROUP BY i.medicine_name
          ORDER BY qty DESC
          LIMIT 5");
    while ($q && $row = mysqli_fetch_assoc($q)) {
        $response['items'][] = [
            'medicine_name' => $row['medicine_name'],
            'qty'           => intval($row['qty']),
            'units'         => intval($row['units']),
        ];
    }
} else {
    // Fallback: derive from prescripition.medicine_id JSON for the range
    $response['fallback'] = 'prescriptions';
    $clause = $range === 'week'
        ? "pr.create_date_time >= (CURDATE() - INTERVAL 6 DAY)"
        : "DATE(pr.create_date_time) = CURDATE()";
    $q = mysqli_query($conn,
        "SELECT medicine_id FROM prescripition pr
          WHERE pr.org_id = '$SessionOrgId'
            AND pr.status = '1'
            AND pr.medicine_id IS NOT NULL
            AND pr.medicine_id <> ''
            AND pr.medicine_id <> '[]'
            AND $clause
          LIMIT 500");
    $tally = [];
    while ($q && $row = mysqli_fetch_assoc($q)) {
        $arr = json_decode($row['medicine_id'], true);
        if (!is_array($arr)) continue;
        foreach ($arr as $m) {
            $name = $m['medicine_name'] ?? null;
            if (!$name) continue;
            $tally[$name] = ($tally[$name] ?? 0) + 1;
        }
    }
    arsort($tally);
    $i = 0;
    foreach ($tally as $name => $qty) {
        $response['items'][] = [
            'medicine_name' => $name,
            'qty'           => $qty,
            'units'         => $qty,
        ];
        if (++$i >= 5) break;
    }
}

echo json_encode($response);
