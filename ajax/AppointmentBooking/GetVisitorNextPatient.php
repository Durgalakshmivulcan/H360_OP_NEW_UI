<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

// assume session already started and $SessionOrgId, $currentDate defined
// e.g.:
$currentDate  = date('Y-m-d');
$SessionOrgId = $_SESSION['org_id'] ?? ''; 

// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('a.doctor_name');
$getAppointmentData = mysqli_query($conn, "
  SELECT
    a.appoint_unicode,
    a.patient_name,
    d.doctor_name    AS doctor_full_name,
    d.doc_img        AS doctor_img
  FROM appointment_online a
  JOIN doctors d
    ON a.doctor_name = d.doc_id
  WHERE a.visitor_status = '1'
    AND a.org_id        = '$SessionOrgId'
    AND a.appoint_date  = '$currentDate'
    $docScope
  ORDER BY a.appoint_id ASC
  LIMIT 1
") or die(mysqli_error($conn));

$resAppointmentData = mysqli_fetch_object($getAppointmentData);

if ($resAppointmentData) {
  // return JSON with all needed fields
  echo json_encode([
    'doctor_img'        => $resAppointmentData->doctor_img,
    'doctor_full_name'  => $resAppointmentData->doctor_full_name,
    'appoint_unicode'   => $resAppointmentData->appoint_unicode,
    'patient_name'      => $resAppointmentData->patient_name
  ]);
} else {
  // no appointment → signal “empty”
  echo json_encode(null);
}
