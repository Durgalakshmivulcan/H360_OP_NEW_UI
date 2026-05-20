<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


header("Content-Type: application/json");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$filterOrgId = null;
if ($SessionUserId === "1" && $SessionRoleId === "1") {
    if (!empty($_GET['org_id'])) {
        $filterOrgId = mysqli_real_escape_string($conn, $_GET['org_id']);
    }
} else {
    $filterOrgId = $SessionOrgId;
}

$sql = "SELECT test_group_id, test_group_name, test_id, test_group_price FROM test_group WHERE status = 1";

// Append org filter if needed
if ($filterOrgId !== null && $filterOrgId !== '') {
    $sql .= " AND org_id = '{$filterOrgId}'";
}

$sql .= " ORDER BY test_group_id DESC";

// Execute
$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

$templates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $templates[] = [
        'test_group_id'    => $row['test_group_id'],
        'test_group_name'  => $row['test_group_name'],
        'test_id'          => json_decode($row['test_id'], true) ?? [],
        'test_group_price' => $row['test_group_price'],
    ];
}

echo json_encode([
    'success'   => true,
    'templates' => $templates
]);
