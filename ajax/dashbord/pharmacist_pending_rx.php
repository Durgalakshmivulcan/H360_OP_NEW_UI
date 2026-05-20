<?php
// B-2030 — Pharmacist dashboard: doctors' Rx awaiting medicine bill
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionOrgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;

function tableExists($conn, $tbl) {
    $tbl = mysqli_real_escape_string($conn, $tbl);
    $r = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
    return $r && mysqli_num_rows($r) > 0;
}

$pmbExists = tableExists($conn, 'patient_medicine_billing');

// Pull recent prescriptions with medicines (medicine_id JSON not empty)
$sql = "SELECT pr.prescription_id,
               pr.patient_name,
               pr.patient_uid,
               pr.appoint_register_id,
               pr.medicine_id,
               pr.prescriptiondate,
               COALESCE(d.doctor_name,'-') AS doctor_name
          FROM prescripition pr
          LEFT JOIN appointment_online ao ON ao.appoint_register_id = pr.appoint_register_id
          LEFT JOIN doctors d ON d.doc_id = ao.doctor_name
         WHERE pr.org_id = '$SessionOrgId'
           AND pr.status = '1'
           AND pr.medicine_id IS NOT NULL
           AND pr.medicine_id <> ''
           AND pr.medicine_id <> '[]'
         ORDER BY pr.create_date_time DESC
         LIMIT 200";
$res = mysqli_query($conn, $sql);

// Build set of appoint_register_ids that already have a paid medicine bill
$billed = [];
if ($pmbExists) {
    $bq = mysqli_query($conn,
        "SELECT DISTINCT appointment_id
           FROM patient_medicine_billing
          WHERE org_id = '$SessionOrgId'
            AND (status IS NULL OR status = 1)");
    while ($bq && $r = mysqli_fetch_assoc($bq)) {
        $billed[$r['appointment_id']] = true;
    }
}

$pending = [];
while ($res && $row = mysqli_fetch_assoc($res)) {
    $appt = $row['appoint_register_id'];
    if (isset($billed[$appt])) continue;

    // Decode meds for preview
    $medsRaw = json_decode($row['medicine_id'], true);
    if (!is_array($medsRaw) || count($medsRaw) === 0) continue;
    $medNames = [];
    foreach ($medsRaw as $m) {
        if (!empty($m['medicine_name'])) $medNames[] = $m['medicine_name'];
    }
    if (empty($medNames)) continue;

    $preview = implode(', ', array_slice($medNames, 0, 3));
    if (count($medNames) > 3) $preview .= ' +' . (count($medNames) - 3) . ' more';

    $pending[] = [
        'prescription_id'     => intval($row['prescription_id']),
        'patient_name'        => $row['patient_name'],
        'patient_uid'         => $row['patient_uid'],
        'appoint_register_id' => $appt,
        'doctor_name'         => $row['doctor_name'],
        'rx_date'             => $row['prescriptiondate'],
        'medicines_preview'   => $preview,
        'medicine_count'      => count($medNames),
    ];
    if (count($pending) >= 30) break;
}

echo json_encode([
    'pending_count'  => count($pending),
    'items'          => $pending,
    'source_present' => $pmbExists,
]);
