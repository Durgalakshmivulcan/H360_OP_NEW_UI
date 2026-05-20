<?php
require_once("../../config/functions.php");

header('Content-Type: application/json');

$SessionUserId  = $_SESSION["security_id"];
$SessionRoleId  = $_SESSION["role_id"];
$SessionOrgId   = $_SESSION["org_id"];
$test_id        = $_POST['test_id'] ?? '';

if (!empty($test_id)) {
    // Sanitize inputs
    $test_id = mysqli_real_escape_string($conn, $test_id);
    $org_id = mysqli_real_escape_string($conn, $SessionOrgId);

    $query = "SELECT normal_range FROM tests 
              WHERE test_id = '$test_id' 
              AND status = '1' 
              AND org_id = '$org_id'";

    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        echo json_encode(['normal_range' => $row['normal_range']]);
    } else {
        echo json_encode(['error' => 'Test not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid test_id']);
}
?>
