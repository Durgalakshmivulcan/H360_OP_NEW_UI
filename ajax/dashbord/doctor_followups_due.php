<?php
// B-2020: Doctor Dashboard — follow-ups due (reviewafterdate <= today, status active).
// Combines `prescripition.reviewafterdate` and `gynaec_prescriptions.reviewafterdate`.
require_once("../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');

header('Content-Type: application/json');

$SessionOrgId = (int) ($_SESSION['org_id'] ?? 0);
$docScopeAo   = currentDoctorScopeSql('ao.doctor_name');

$today = date('Y-m-d');

$sqlP = "SELECT
            p.prescription_id      AS id,
            p.patient_name,
            p.appoint_register_id,
            p.reviewafterdate      AS review_date,
            'rx' AS source
         FROM prescripition AS p
         LEFT JOIN appointment_online ao
              ON ao.appoint_register_id = p.appoint_register_id
         WHERE p.status='1'
           AND p.org_id='$SessionOrgId'
           AND p.reviewafterdate IS NOT NULL
           AND p.reviewafterdate <> ''
           AND STR_TO_DATE(p.reviewafterdate, '%Y-%m-%d') <= '$today'
           $docScopeAo
         ORDER BY STR_TO_DATE(p.reviewafterdate, '%Y-%m-%d') DESC
         LIMIT 50";

$rows = [];
$res  = mysqli_query($conn, $sqlP);
while ($res && ($r = mysqli_fetch_assoc($res))) $rows[] = $r;

// FIX_B_2216: skip gynaec query when table is absent on the deployment.
$gTblOk = mysqli_query($conn, "SHOW TABLES LIKE 'gynaec_prescriptions'");
$sqlG = (!$gTblOk || mysqli_num_rows($gTblOk) === 0) ? '' : "SELECT
            g.gynaec_rx_id         AS id,
            g.patient_name,
            g.appointment_id       AS appoint_register_id,
            g.reviewafterdate      AS review_date,
            'gynaec' AS source
         FROM gynaec_prescriptions AS g
         LEFT JOIN appointment_online ao
              ON ao.appoint_register_id = g.appointment_id
         WHERE g.status='1'
           AND g.org_id='$SessionOrgId'
           AND g.reviewafterdate IS NOT NULL
           AND g.reviewafterdate <> ''
           AND STR_TO_DATE(g.reviewafterdate, '%Y-%m-%d') <= '$today'
           $docScopeAo
         ORDER BY STR_TO_DATE(g.reviewafterdate, '%Y-%m-%d') DESC
         LIMIT 50";

$resG = $sqlG !== '' ? mysqli_query($conn, $sqlG) : false;
while ($resG && ($r = mysqli_fetch_assoc($resG))) $rows[] = $r;

echo json_encode(['success' => true, 'count' => count($rows), 'rows' => $rows]);
