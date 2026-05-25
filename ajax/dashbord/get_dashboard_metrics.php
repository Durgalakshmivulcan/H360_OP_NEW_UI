<?php
require_once("../../config/functions.php");

header('Content-Type: application/json');

$sessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
$requestedSecId = isset($_GET['security_id']) ? (int)$_GET['security_id'] : (int)$sessionUserId;

// security_id=0 means "all doctors" — use org filter only, no doctor filter
$showAll = ($requestedSecId === 0);
$isSA    = ((string)$sessionUserId === '1');
$orgCond = (!$isSA && !empty($SessionOrgId)) ? "AND a.org_id = '$SessionOrgId'" : '';
$dOrgCond= (!$isSA && !empty($SessionOrgId)) ? "AND d.org_id = '$SessionOrgId'" : '';
$docCond = $showAll ? '' : "AND d.security_id = '$requestedSecId'";

$todayAppointmentCount = mysqli_fetch_array(mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM appointment_online AS a
     LEFT JOIN doctors AS d ON a.doctor_name = d.doc_id
     WHERE a.appoint_status='1'
       AND a.appoint_date=CURDATE()
       $orgCond $docCond"
))[0];

$followUpcount = mysqli_fetch_array(mysqli_query($conn,
    "SELECT COUNT(*) AS valid_count
     FROM appointment_online AS a
     LEFT JOIN doctors AS d ON a.doctor_name = d.doc_id
     WHERE d.status='1'
       AND a.appoint_status='1'
       AND MONTH(a.appoint_date) = MONTH(CURDATE())
       AND YEAR(a.appoint_date)  = YEAR(CURDATE())
       $orgCond $docCond"
))[0];

$avgWaitingTime = mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT
         AVG(ABS(TIMESTAMPDIFF(MINUTE,
             STR_TO_DATE(CONCAT(a.appoint_date,' ',a.start_time), '%Y-%m-%d %H:%i'),
             dpd.check_in))) AS avg_waiting
     FROM appointment_online AS a
     JOIN doctor_patient_duration AS dpd ON a.appoint_register_id = dpd.appointment_id
     JOIN doctors AS d ON a.doctor_name = d.doc_id
     WHERE dpd.check_in IS NOT NULL
       $orgCond $docCond"
))[0];

$sql = "SELECT COUNT(DISTINCT d.doc_id) AS active_doctors
        FROM doctors AS d
        LEFT JOIN doctors_time_slot AS ts
          ON ts.doctorName_registrationNumber = d.doc_id AND ts.available_date = CURDATE() AND ts.status='1'
        LEFT JOIN multi_doctortimeslots AS mts
          ON mts.doctorName_registrationNumber = d.doc_id
         AND CURDATE() BETWEEN mts.from_date AND mts.to_date
         AND FIND_IN_SET(DAYOFWEEK(CURDATE()), mts.selectedDays) AND mts.status='1'
        WHERE d.status='1'
          $dOrgCond $docCond
          AND (ts.doctors_time_id IS NOT NULL OR mts.multi_id IS NOT NULL)";
$doctorsOnDuty = mysqli_fetch_assoc(mysqli_query($conn, $sql))['active_doctors'];

// Doctor info — only meaningful when a specific doctor is selected
$doctorRow = $showAll ? null : mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT d.doctor_name, ds.specialtisname, d.doc_img
     FROM doctors AS d
     LEFT JOIN specialtis AS ds ON ds.specialtis_id = d.doctor_specialization
     WHERE d.security_id = '$requestedSecId'"
));

$doctorName = $doctorRow['doctor_name']    ?? '';
$speciality = $doctorRow['specialtisname'] ?? '';
$docImg     = $doctorRow['doc_img']        ?? '';
$imgPath    = (!empty($docImg) && file_exists("../../doctor_images/".$docImg))
                ? "doctor_images/".$docImg
                : "assets/img/user.png";

echo json_encode([
    'doctor_name'        => $doctorName,
    'specialtisname'     => $speciality,
    'doctor_img'         => $imgPath,
    'todayAppointmentCount' => $todayAppointmentCount,
    'followUpcount'      => $followUpcount,
    'avgWaitingTime'     => $avgWaitingTime,
    'doctorsOnDuty'      => $doctorsOnDuty
]);

