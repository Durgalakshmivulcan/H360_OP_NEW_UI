<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

if ($SessionUserId != "1") {
    $sql = "
        SELECT s.admin_name, s.contact, s.security_type, r.role_name
        FROM security s
        LEFT JOIN roles r ON s.role_id = r.role_id
        WHERE s.status = '1'
        AND s.org_id = '$SessionOrgId'
    ";
} else {
    $sql = "
        SELECT s.admin_name, s.contact, s.security_type, r.role_name
        FROM security s
        LEFT JOIN roles r ON s.role_id = r.role_id
        WHERE s.status = '1'
    ";
}

$result = mysqli_query($conn, $sql);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
