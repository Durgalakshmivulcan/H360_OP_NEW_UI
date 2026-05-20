<?php
	require_once "../../config/functions.php";
	
	$SessionUserId = $_SESSION['security_id'] ?? '';
	$SessionRoleId = $_SESSION['role_id'] ?? '';
	$SessionOrgId = $_SESSION['org_id'] ?? '';


    $doseandtime_id = $_POST['doseandtime_id'];

    $query = "SELECT * FROM dosageandtime WHERE doseandtime_id = '$doseandtime_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    $results = [
        'frequency' => $row['frequency']
    ];

    echo json_encode($results);
?>