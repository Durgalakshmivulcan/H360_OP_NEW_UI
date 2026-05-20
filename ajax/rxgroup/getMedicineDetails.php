<?php
require_once "../../config/functions.php";

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$medicine_id = $_POST['medicine_id'];

// $query = "SELECT m.medicine_type, m.dosage, u.unit_id 
//           FROM medicines m 
//           LEFT JOIN units u ON m.dosage = u.unit_name 
//           WHERE m.medicine_id = '$medicine_id'";

$query = "SELECT m.medicine_id, m.medicine_name, m.dosage, m.medicine_type AS type_id, mt.type_name 
FROM medicines m 
LEFT JOIN madicine_type mt ON m.medicine_type = mt.type_id 
WHERE m.medicine_id = '$medicine_id'";


// $result = mysqli_query($conn, $query);

// if ($row = mysqli_fetch_assoc($result)) {
//     $results = [
//         'type' => $row['medicine_type'],  
//         'unit' => $row['dosage'] ?? ''  
//     ];
// } else {
//     $results = [
//         'type' => '',
//         'unit' => ''
//     ];
// }

// echo json_encode($results);


$result = mysqli_query($conn, $query);

$response = [];

if ($row = mysqli_fetch_assoc($result)) {
    $response = [
        'medicine_id' => $row['medicine_id'],
        'medicine_name' => $row['medicine_name'],
        'dosage' => $row['dosage'], // no join for units
        'type_id' => $row['type_id'],
        'type_name' => $row['type_name'] // this is the joined name from `medicinetype`
    ];
}

echo json_encode($response);


?>
