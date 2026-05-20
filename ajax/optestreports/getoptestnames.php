<?php
require_once("../../config/functions.php");

$SessionOrgId  = $_SESSION['org_id'] ?? '';

header('Content-Type: application/json');

$response = [];

$sql = "SELECT MIN(test_id) AS test_id, MIN(test_name) AS test_name
        FROM tests
        WHERE org_id = '$SessionOrgId'
          AND status = '1'
          AND test_name IS NOT NULL
          AND TRIM(test_name) <> ''
        GROUP BY LOWER(TRIM(test_name))
        ORDER BY test_name ASC";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = [
            "test_id"   => $row['test_id'],
            "test_name" => $row['test_name']
        ];
    }
} else {
    $response = [['error' => 'No tests found']];
}

echo json_encode($response);
exit;
