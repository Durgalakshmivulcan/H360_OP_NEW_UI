<?php
require_once("../../config/functions.php"); 

$SessionUserId = $_SESSION['security_id'] ?? 0;
$SessionRoleId = $_SESSION['role_id'] ?? 0;
$SessionOrgId  = $_SESSION['org_id'] ?? 0;

$today = date("Y-m-d");

// --- Get Patients ---
// FIX_B_1903: doctor-scope filter
$docScope = currentDoctorScopeSql('doctor_name');
$sql = "SELECT appoint_unicode, patient_name, visitor_status, appoint_date, doctor_name
        FROM appointment_online
        WHERE DATE(appoint_date) = '$today' AND visitor_status = '1' $docScope
        ORDER BY appoint_date ASC";

$result = mysqli_query($conn, $sql);

$patients = [];
while ($row = mysqli_fetch_assoc($result)) {
    $patients[] = $row;
}

$nextPatient = null;
$upcomingPatient = null;
$allPatients = [];
$doctorData = null;

if (!empty($patients)) {
    $nextPatient = $patients[0]; 

    $doctorId = (int)$nextPatient['doctor_name'];
    $sqlDoc = "SELECT doc_img, doctor_name FROM doctors WHERE doc_id = $doctorId LIMIT 1";
    $resDoc = mysqli_query($conn, $sqlDoc);
    $doctorData = mysqli_fetch_assoc($resDoc);
}

if (count($patients) > 1) {
    $upcomingPatient = $patients[1]; 
}
if (count($patients) > 2) {
    $allPatients = array_slice($patients, 2); 
}

// --- Optional: read message from push server (port 9000) ---
$pushMessage = null; // set to null or fetch from your push server logic

// --- Final JSON response ---
echo json_encode([
    "next"        => $nextPatient,
    "upcoming"    => $upcomingPatient,
    "all"         => $allPatients,
    "doctor_data" => $doctorData,
    "pushmsg"     => $pushMessage 
]);
?>
