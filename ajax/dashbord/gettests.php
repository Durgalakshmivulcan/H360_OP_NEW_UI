<?php
require_once('../../config/functions.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$test_group_id = $_POST['test_group_id'] ?? '';

if ($SessionUserId === '1') {
    $gettestgroup = mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id' AND status='1'") or die(mysqli_error($conn));
} else {
    $gettestgroup = mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id' AND org_id='$SessionOrgId' AND status='1'") or die(mysqli_error($conn));
}

$hasData = false;

while ($restestgroup = mysqli_fetch_object($gettestgroup)) {
    
    $test_id_json = $restestgroup->test_id;
    
    $tests_array = json_decode($test_id_json, true);

    if (!empty($tests_array)) {
        foreach ($tests_array as $single_test) {
            $investigation = $single_test['investigation'];
            $test_group_name = $single_test['test_group_name'];

            $hasData = true;
            echo "<tr>";
            echo "<td>{$investigation}</td>";
            echo "</tr>";
        }
    }
}

if (!$hasData) {
    echo "<tr><td>No tests found</td></tr>";
}
?>
