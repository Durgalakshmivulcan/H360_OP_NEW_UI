<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';



// FIX_B_163: accept GET or POST org_id; only override when non-empty
$org_id = $_GET['org_id'] ?? ($_POST['org_id'] ?? '');
if ($SessionUserId == '1' && $org_id !== '' && ctype_digit((string)$org_id)) {
    $SessionOrgId = $org_id;
}
$id = generateNextDocID($conn, $SessionOrgId, $SessionUserId, $currentDate);

echo json_encode($id);
?>


