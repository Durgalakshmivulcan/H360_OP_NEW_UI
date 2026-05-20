<?php
// B-2020: Doctor Dashboard — last 5 prescriptions written by this doctor.
// Pulls from `prescripition` for cardio-style and `gynaec_prescriptions` for gynaec.
// We scope via JOIN on appointment_online.doctor_name (FIX_B_1903 pattern).
require_once("../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');

header('Content-Type: application/json');

$SessionOrgId = (int) ($_SESSION['org_id'] ?? 0);

$docScopeAo = currentDoctorScopeSql('ao.doctor_name');

// Cardio / generic Rx
$sqlP = "SELECT
            p.prescription_id,
            p.patient_name,
            p.appoint_register_id,
            p.prescriptiondate,
            p.create_date_time,
            p.finalDiagnosis,
            'rx' AS source
         FROM prescripition AS p
         LEFT JOIN appointment_online ao
              ON ao.appoint_register_id = p.appoint_register_id
         WHERE p.status='1'
           AND p.org_id='$SessionOrgId'
           $docScopeAo
         ORDER BY p.create_date_time DESC
         LIMIT 5";

$rows = [];
$res  = mysqli_query($conn, $sqlP);
while ($res && ($r = mysqli_fetch_assoc($res))) {
    $dx = trim(strip_tags((string) ($r['finalDiagnosis'] ?? '')));
    if (mb_strlen($dx) > 80) $dx = mb_substr($dx, 0, 77) . '…';
    $rows[] = [
        'id'                  => (int) $r['prescription_id'],
        'patient_name'        => $r['patient_name'],
        'appoint_register_id' => $r['appoint_register_id'],
        'date'                => $r['prescriptiondate'] ?: substr((string) $r['create_date_time'], 0, 10),
        'diagnosis'           => $dx ?: '—',
        'source'              => 'rx',
    ];
}

// Also include gynaec recent — for Rama, prescripition will be empty so this fills the panel.
// FIX_B_2216: only run if gynaec_prescriptions table exists; otherwise skip silently.
$gTblOk = mysqli_query($conn, "SHOW TABLES LIKE 'gynaec_prescriptions'");
$sqlG = (!$gTblOk || mysqli_num_rows($gTblOk) === 0) ? '' : "SELECT
            g.gynaec_rx_id AS id,
            g.patient_name,
            g.appointment_id AS appoint_register_id,
            g.rx_date,
            g.created_at,
            g.final_diagnosis
         FROM gynaec_prescriptions AS g
         LEFT JOIN appointment_online ao
              ON ao.appoint_register_id = g.appointment_id
         WHERE g.status='1'
           AND g.org_id='$SessionOrgId'
           $docScopeAo
         ORDER BY g.created_at DESC
         LIMIT 5";

$resG = $sqlG !== '' ? mysqli_query($conn, $sqlG) : false;
while ($resG && ($r = mysqli_fetch_assoc($resG))) {
    $dx = trim(strip_tags((string) ($r['final_diagnosis'] ?? '')));
    if (mb_strlen($dx) > 80) $dx = mb_substr($dx, 0, 77) . '…';
    $rows[] = [
        'id'                  => (int) $r['id'],
        'patient_name'        => $r['patient_name'],
        'appoint_register_id' => $r['appoint_register_id'],
        'date'                => $r['rx_date'] ?: substr((string) $r['created_at'], 0, 10),
        'diagnosis'           => $dx ?: '—',
        'source'              => 'gynaec',
    ];
}

// Sort all combined by date desc and slice to 5
usort($rows, function ($a, $b) { return strcmp((string) $b['date'], (string) $a['date']); });
$rows = array_slice($rows, 0, 5);

echo json_encode(['success' => true, 'count' => count($rows), 'rows' => $rows]);
