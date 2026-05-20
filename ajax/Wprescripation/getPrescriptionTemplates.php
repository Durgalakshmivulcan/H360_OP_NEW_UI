<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = (int)$_SESSION['org_id'];
$organizations = (int)($_POST['organizations'] ?? 0);
$org_id = ($SessionUserId == "1") ? ($organizations ?: $SessionOrgId) : $SessionOrgId;

$orgCond = $org_id ? "AND org_id='$org_id'" : '';

$res = mysqli_query($conn,
    "SELECT p.prescription_id, p.finalDiagnosis, p.patient_name,
            COALESCE(p.prescriptiondate, DATE(p.create_date_time)) AS rx_date
     FROM prescripition p
     INNER JOIN (
         SELECT MAX(prescription_id) AS max_id
         FROM prescripition
         WHERE status='1'
           AND finalDiagnosis IS NOT NULL
           AND TRIM(finalDiagnosis) != ''
           $orgCond
         GROUP BY TRIM(LOWER(finalDiagnosis))
     ) t ON p.prescription_id = t.max_id
     ORDER BY TRIM(p.finalDiagnosis) ASC
     LIMIT 150"
);

if (!$res) { echo json_encode(['success'=>false,'error'=>mysqli_error($conn)]); exit; }

$list = [];
while ($r = mysqli_fetch_assoc($res)) $list[] = $r;
echo json_encode(['success' => true, 'templates' => $list]);
