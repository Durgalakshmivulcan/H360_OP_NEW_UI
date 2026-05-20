<?php

require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

function getAppointmentsByYear($conn, $year) {
  // FIX_B_059 + FIX_B_991: dedup across appointment_online + appointment_existing
  // by appoint_register_id. Earlier B-059 referenced a non-existent column
  // `invoice_id` (the table only has an `invoice_payment` enum), which threw
  // "Unknown column 'invoice_id' in 'field list'" → 500 on every dashboard load.
  // appoint_register_id is the canonical per-row identifier for both tables.
  global $SessionOrgId;
  $appointments = array();
  $sql = "
    SELECT DATE_FORMAT(appoint_date, '%b') AS m FROM (
      SELECT appoint_date, appoint_register_id AS dedup_key
      FROM appointment_online
      WHERE YEAR(appoint_date) = '$year' AND appoint_status='1' AND org_id='$SessionOrgId'
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

$year = isset($_POST['year']) ? intval($_POST['year']) : date('Y'); // Default to the current year
$dataForYear = getAppointmentsByYear($conn, $year);

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