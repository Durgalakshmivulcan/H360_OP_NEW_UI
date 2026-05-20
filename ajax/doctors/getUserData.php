<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

$securityId = $_POST['securityId'] ?? 0;

if ($securityId) {
    // sanitize the input to avoid SQL injection
    $securityId = (int)$securityId; 

    $query = "SELECT security_id, contact, email FROM security WHERE security_id = $securityId";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(null);
    }
} else {
    echo json_encode(null);
}
