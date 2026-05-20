<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = (int)$_SESSION['org_id'];
$organizations = (int)($_POST['organizations'] ?? 0);
$org_id = ($SessionUserId == "1") ? ($organizations ?: $SessionOrgId) : $SessionOrgId;

$orgCond = $org_id ? "AND org_id='$org_id'" : '';

$res = mysqli_query($conn,
    "SELECT g.gynaec_rx_id, g.final_diagnosis, g.patient_name,
            COALESCE(g.rx_date, DATE(g.created_at)) AS rx_date
     FROM gynaec_prescriptions g
     INNER JOIN (
         SELECT MAX(gynaec_rx_id) AS max_id
         FROM gynaec_prescriptions
         WHERE status='1'
           AND final_diagnosis IS NOT NULL
           AND TRIM(final_diagnosis) != ''
           $orgCond
         GROUP BY TRIM(LOWER(final_diagnosis))
     ) t ON g.gynaec_rx_id = t.max_id
     ORDER BY TRIM(g.final_diagnosis) ASC
     LIMIT 150"
);

if (!$res) { echo json_encode(['success'=>false,'error'=>mysqli_error($conn)]); exit; }

$list = [];
while ($r = mysqli_fetch_assoc($res)) $list[] = $r;
echo json_encode(['success' => true, 'templates' => $list]);
