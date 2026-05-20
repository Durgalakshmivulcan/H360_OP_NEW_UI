<?php
// B-2020: Doctor Dashboard — specialty-specific panel.
//   - CARDIOLOGIST  → "Cardiology Risk Patients" — distinct patients with hypertensive /
//     cardiac / BP markers in finalDiagnosis.
//   - GYNAECOLOGIST → "Antenatal Follow-ups" — patients with EDD in the future.
require_once("../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');

header('Content-Type: application/json');

$SessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$SessionOrgId  = (int) ($_SESSION['org_id'] ?? 0);
$docScopeAo    = currentDoctorScopeSql('ao.doctor_name');

// Detect specialty for the logged-in doctor.
// `doctors.doctor_specialization` stores the literal name on this deployment
// (e.g. "CARDIOLOGIST"); we still LEFT JOIN specialtis as a fallback in case
// other deployments use the FK shape.
$spec = '';
$qSp  = mysqli_query($conn,
    "SELECT d.doctor_specialization AS raw_spec,
            COALESCE(s.specialtisname, '') AS named_spec
       FROM doctors d
       LEFT JOIN specialtis s ON s.specialtis_id = d.doctor_specialization
      WHERE d.security_id='$SessionUserId' AND d.status='1' LIMIT 1");
if ($qSp && ($r = mysqli_fetch_assoc($qSp))) {
    $spec = strtoupper(trim((string) ($r['named_spec'] !== '' ? $r['named_spec'] : $r['raw_spec'])));
}

$panel = ['kind' => 'generic', 'title' => 'Patient Highlights', 'rows' => []];

if (strpos($spec, 'CARDIO') !== false) {
    $sql = "SELECT DISTINCT
                p.patient_name,
                p.appoint_register_id,
                p.finalDiagnosis,
                p.create_date_time
            FROM prescripition AS p
            LEFT JOIN appointment_online ao
                  ON ao.appoint_register_id = p.appoint_register_id
            WHERE p.status='1'
              AND p.org_id='$SessionOrgId'
              AND (
                    p.finalDiagnosis LIKE '%hypertension%'
                 OR p.finalDiagnosis LIKE '%cardiac%'
                 OR p.finalDiagnosis LIKE '% bp%'
                 OR p.finalDiagnosis LIKE '%BP %'
                 OR p.finalDiagnosis LIKE '%HTN%'
                 OR p.finalDiagnosis LIKE '%CAD%'
              )
              $docScopeAo
            ORDER BY p.create_date_time DESC
            LIMIT 8";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    while ($res && ($r = mysqli_fetch_assoc($res))) {
        $dx = trim(strip_tags((string) ($r['finalDiagnosis'] ?? '')));
        if (mb_strlen($dx) > 70) $dx = mb_substr($dx, 0, 67) . '…';
        $rows[] = [
            'patient_name'        => $r['patient_name'],
            'appoint_register_id' => $r['appoint_register_id'],
            'tag'                 => $dx ?: 'Cardio risk',
            'date'                => substr((string) $r['create_date_time'], 0, 10),
        ];
    }
    $panel = ['kind' => 'cardio', 'title' => 'Cardiology Risk Patients', 'rows' => $rows];

} elseif (strpos($spec, 'GYNAEC') !== false || strpos($spec, 'OBSTET') !== false) {
    // FIX_B_2216: defensive guard — if gynaec_prescriptions table is absent on this
    // deployment we degrade to a clean empty panel rather than emitting a SQL error.
    $tblOk = mysqli_query($conn, "SHOW TABLES LIKE 'gynaec_prescriptions'");
    if (!$tblOk || mysqli_num_rows($tblOk) === 0) {
        echo json_encode(['success' => true, 'panel' => ['kind' => 'gynaec', 'title' => 'Antenatal Follow-ups', 'rows' => []], 'specialty' => $spec]);
        exit;
    }
    $today = date('Y-m-d');
    $sql = "SELECT
                g.patient_name,
                g.appointment_id AS appoint_register_id,
                g.edd,
                g.lmp,
                DATEDIFF(g.edd, '$today') AS days_to_edd
            FROM gynaec_prescriptions AS g
            LEFT JOIN appointment_online ao
                  ON ao.appoint_register_id = g.appointment_id
            WHERE g.status='1'
              AND g.org_id='$SessionOrgId'
              AND g.edd IS NOT NULL
              AND g.edd >= '$today'
              $docScopeAo
            ORDER BY g.edd ASC
            LIMIT 8";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    while ($res && ($r = mysqli_fetch_assoc($res))) {
        $rows[] = [
            'patient_name'        => $r['patient_name'],
            'appoint_register_id' => $r['appoint_register_id'],
            'tag'                 => 'EDD ' . $r['edd'] . ' (' . (int) $r['days_to_edd'] . 'd)',
            'date'                => $r['edd'],
        ];
    }
    $panel = ['kind' => 'gynaec', 'title' => 'Antenatal Follow-ups', 'rows' => $rows];
}

echo json_encode(['success' => true, 'panel' => $panel, 'specialty' => $spec]);
