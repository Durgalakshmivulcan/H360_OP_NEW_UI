<?php
require_once("../../config/functions.php");

$response = array();
$response['msg'] = 0; // Initialize the flag to 0

$size_id = $_POST['size_id'];

$getsize = mysqli_query($conn, "SELECT w_size, h_size FROM pagessize WHERE status='1' AND size_id='$size_id'");
if ($getsize) {
    if ($ressize = mysqli_fetch_assoc($getsize)) {
        $response['msg'] = 1; // Records found
        $response['w_size'] = $ressize['w_size']; // Width
        $response['h_size'] = $ressize['h_size']; // Height
    }
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
