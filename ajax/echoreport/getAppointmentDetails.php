<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
if (!$SessionUserId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$appoint_id = mysqli_real_escape_string($conn, $_POST['appoint_id'] ?? '');

if (!$appoint_id) {
    echo json_encode([]);
    exit;
}

$qry = mysqli_query($conn,
    "SELECT ao.patient_name, ao.age, ao.gender, ao.appoint_date,
            d.doctor_name
     FROM appointment_online ao
     LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
     WHERE ao.appoint_id = '$appoint_id'
     LIMIT 1"
);

if ($row = mysqli_fetch_assoc($qry)) {
    echo json_encode([
        'patient_name' => $row['patient_name'],
        'age'          => $row['age'],
        'gender'       => $row['gender'],
        'appoint_date' => $row['appoint_date'],
        'doctor_name'  => $row['doctor_name'],
    ]);
} else {
    echo json_encode([]);
}
