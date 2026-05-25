<?php

require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

function getAppointmentsByYear($conn, $year, $filterDocId = null) {
  // FIX_B_059 + FIX_B_991: dedup across appointment_online + appointment_existing
  // by appoint_register_id. Earlier B-059 referenced a non-existent column
  // `invoice_id` (the table only has an `invoice_payment` enum), which threw
  // "Unknown column 'invoice_id' in 'field list'" → 500 on every dashboard load.
  // appoint_register_id is the canonical per-row identifier for both tables.
  global $SessionOrgId, $SessionUserId, $SessionRoleId;
  $appointments = array();

  // Build doctor scope: prefer explicit filterDocId (from passed security_id), else session scope
  if ($filterDocId !== null) {
      $docScope = " AND doctor_name = '" . (int)$filterDocId . "'";
  } else {
      $docScope = currentDoctorScopeSql('doctor_name');
  }

  $sql = "
    SELECT DATE_FORMAT(appoint_date, '%b') AS m FROM (
      SELECT appoint_date, appoint_register_id AS dedup_key
      FROM appointment_online
      WHERE YEAR(appoint_date) = '$year' AND appoint_status='1' AND org_id='$SessionOrgId'
        $docScope
      UNION
      SELECT appoint_date, appoint_register_id AS dedup_key
      FROM appointment_existing
      WHERE YEAR(appoint_date) = '$year' AND appoint_status='1' AND org_id='$SessionOrgId'
    ) AS dedup
  ";
  $rs = mysqli_query($conn, $sql);
  if ($rs) {
    while ($row = mysqli_fetch_assoc($rs)) {
      $appointments[] = $row['m'];
    }
  }

  $allShortMonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

  $monthYearCountArray = array_count_values($appointments);

  foreach ($allShortMonths as $shortMonth) {
      if (!isset($monthYearCountArray[$shortMonth])) {
          $monthYearCountArray[$shortMonth] = 0;
      }
  }

  $sortedMonthYearCountArray = array();
  foreach ($allShortMonths as $shortMonth) {
      $sortedMonthYearCountArray[$shortMonth] = $monthYearCountArray[$shortMonth];
  }

  $shortMonths = array_keys($sortedMonthYearCountArray);
  $countData = array_values($sortedMonthYearCountArray);

  return array('shortMonths' => $shortMonths, 'countData' => $countData);
}

$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');

// Resolve passed security_id to a doc_id for doctor-level filtering
$passedSecId = isset($_POST['security_id']) ? (int)$_POST['security_id'] : 0;
$isSA_my  = ((string)$SessionUserId === '1' || (string)$SessionRoleId === '1');
$filterDocId = null;
if (!$isSA_my && $passedSecId > 0) {
    $esc_sid = mysqli_real_escape_string($conn, (string)$passedSecId);
    $dr = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT doc_id FROM doctors WHERE security_id='$esc_sid' AND status='1' LIMIT 1"));
    if ($dr && !empty($dr['doc_id'])) {
        $filterDocId = (int)$dr['doc_id'];
    }
}

$dataForYear = getAppointmentsByYear($conn, $year, $filterDocId);

$jsonShortMonths = json_encode($dataForYear['shortMonths']);
$jsonCountData = json_encode($dataForYear['countData']);

// echo $jsonShortMonths;
// echo $jsonCountData;

$countyear = array(
    'month' => $jsonShortMonths,
    'year' => $jsonCountData
);
echo json_encode($countyear);

?>