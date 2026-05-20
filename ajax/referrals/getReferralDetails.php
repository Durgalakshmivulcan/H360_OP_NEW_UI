<?php
ob_start();
require_once("../../config/functions.php");
ob_end_clean();

header('Content-Type: application/json');

$SessionOrgId  = $_SESSION['org_id']      ?? '';
$SessionUserId = $_SESSION['security_id'] ?? '';

$orgFilter    = $SessionUserId == "1" ? '' : "AND ao.org_id='" . mysqli_real_escape_string($conn, $SessionOrgId) . "'";
$fromDate     = mysqli_real_escape_string($conn, $_POST['from_date']     ?? '');
$toDate       = mysqli_real_escape_string($conn, $_POST['to_date']       ?? '');
$referralType = mysqli_real_escape_string($conn, $_POST['referral_type'] ?? '');

$dateFilter = '';
if ($fromDate && $toDate)  $dateFilter = "AND ao.appoint_date BETWEEN '$fromDate' AND '$toDate'";
elseif ($fromDate)         $dateFilter = "AND ao.appoint_date >= '$fromDate'";
elseif ($toDate)           $dateFilter = "AND ao.appoint_date <= '$toDate'";

$typeFilter = $referralType ? "AND ao.referral_type='" . mysqli_real_escape_string($conn, $referralType) . "'" : '';

$qry = mysqli_query($conn, "
    SELECT
        ao.appoint_date,
        ao.patient_name,
        ao.appoint_unicode,
        ao.mobile_number,
        ao.gender,
        ao.age,
        d.doctor_name,
        ao.referred_by,
        ao.referral_hospital,
        ao.referral_type,
        ao.referral_notes
    FROM appointment_online ao
    LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
    WHERE ao.appoint_status='1'
      AND ao.referred_by IS NOT NULL AND ao.referred_by != ''
      $orgFilter $dateFilter $typeFilter
    ORDER BY ao.appoint_date DESC, ao.appoint_id DESC
");

if (!$qry) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}

$rows = [];
while ($r = mysqli_fetch_assoc($qry)) $rows[] = $r;

echo json_encode($rows);
