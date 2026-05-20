

<?php
	require_once "../../config/functions.php";
	
    $tests=$_POST['tests'];

    $results = [];
	$SessionUserId = $_SESSION['security_id'] ?? '';
	$SessionRoleId = $_SESSION['role_id'] ?? '';
	$SessionOrgId = $_SESSION['org_id'] ?? '';

if($SessionUserId == "1" && $SessionRoleId=="1"){
	$sql = mysqli_query($conn,"SELECT test_id, test_name,test_price FROM tests WHERE status='1' ORDER BY test_id='$tests'") or die(mysqli_error($conn));
} else{
	$sql = mysqli_query($conn,"SELECT test_id, test_name, test_price FROM tests WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_id='$tests'") or die(mysqli_error($conn));
}
	while($row = mysqli_fetch_object($sql)){
		$results[] = $row;
	}

    echo json_encode($results);
?>