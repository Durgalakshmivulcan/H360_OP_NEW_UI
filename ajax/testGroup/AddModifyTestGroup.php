<?php
// IDOR_FIXED B-593
require_once("../../config/functions.php");
requireCan(empty($_POST['test_group_id']) ? 'add' : 'edit', 'testGroup.php', 'ajax'); // FIX_B_1810

$msg = 0;

$SessionUserId  = $_SESSION['security_id'] ?? '';
$SessionRoleId  = $_SESSION['role_id'] ?? '';
$SessionOrgId   = $_SESSION['org_id'] ?? '';

$test_group_id      = $_POST['test_group_id'];
$test_group_name    = $_POST['test_group_name'];
$test_name_json     = $_POST['test_name']; // 🛠️ now it is JSON string
$test_group_price   = $_POST['test_group_price'];
$organizations      = $_POST['organizations'];

$addorgid = "AND org_id='$SessionOrgId'";
if($SessionUserId == "1") {
    $addorgid = "AND org_id='$organizations'";
}

if($test_group_name != "" && $test_name_json != "" && $test_group_price != "" ) {
    if($test_group_id != "") {
        $getAdminTestGroup = mysqli_query($conn, "SELECT * FROM test_group WHERE status='1' AND test_group_name LIKE '$test_group_name' AND test_group_id!='$test_group_id' $addorgid") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminTestGroup);
        
        if ($result > 0) {
            $msg = 3;
        } else {
            if($SessionUserId == "1") {
                if($SessionOrgId != "") {
                    $UpdateTestGroupData = mysqli_query($conn, "UPDATE test_group 
                        SET test_group_name='$test_group_name',
                            test_id='$test_name_json',
                            test_group_price='$test_group_price',
                            modified_by='$SessionUserId',
                            org_id='$organizations'
                        WHERE test_group_id='$test_group_id'") or die(mysqli_error($conn));
                    
                    if($UpdateTestGroupData) {
                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id'"));
                        audit_log($conn, "Test Group", "update", "test_group", $test_group_id, $before, $after);
                        $msg = 2;
                    }
                }
            } else {
                $UpdateTestGroupData = mysqli_query($conn, "UPDATE test_group 
                    SET test_group_name='$test_group_name',
                        test_id='$test_name_json',
                        test_group_price='$test_group_price',
                        modified_by='$SessionUserId'
                    WHERE test_group_id='$test_group_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                
                if($UpdateTestGroupData) {
                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id'"));
                        audit_log($conn, "Test Group", "update", "test_group", $test_group_id, $before, $after);
                    $msg = 2;
                }
            }
        }
    } else {
        $getAdminTestGroup = mysqli_query($conn, "SELECT * FROM test_group WHERE status='1' AND test_group_name LIKE '$test_group_name' $addorgid") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminTestGroup);
        
        if ($result > 0) {
            $msg = 3;
        } else {
            if($SessionUserId == "1") {
                if($organizations != "") {
                    $InsertTestGroupData = mysqli_query($conn, "INSERT INTO test_group(
                            test_group_name,
                            test_id,
                            test_group_price,
                            status,
                            created_by,
                            modified_by,
                            create_date_time,
                            org_id
                        ) VALUES (
                            '$test_group_name',
                            '$test_name_json',
                            '$test_group_price',
                            '1',
                            '$SessionUserId',
                            '$SessionUserId',
                            '$datetime',
                            '$organizations'
                        )") or die(mysqli_error($conn));
                    
                    $test_group_id = mysqli_insert_id($conn);
                    if($InsertTestGroupData) {
                        $newId = mysqli_insert_id($conn);

                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id'"));
                        audit_log($conn, "Test Group", "create", "test_group", $test_group_id, null, $after);
                        $msg = 1;
                    }
                }
            } else {
                if($SessionOrgId != "") {
                    $InsertTestGroupData = mysqli_query($conn, "INSERT INTO test_group(
                            test_group_name,
                            test_id,
                            test_group_price,
                            status,
                            created_by,
                            modified_by,
                            create_date_time,
                            org_id
                        ) VALUES (
                            '$test_group_name',
                            '$test_name_json',
                            '$test_group_price',
                            '1',
                            '$SessionUserId',
                            '$SessionUserId',
                            '$datetime',
                            '$SessionOrgId'
                        )") or die(mysqli_error($conn));
                    
                    $test_group_id = mysqli_insert_id($conn);
                    if($InsertTestGroupData) {
                        $newId = mysqli_insert_id($conn);

                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM test_group WHERE test_group_id='$test_group_id'"));
                        audit_log($conn, "Test Group", "create", "test_group", $test_group_id, null, $after);
                        $msg = 1;
                    }
                }
            }
        }
    }
} else {
    echo "0";
    exit; 
}

echo $msg;
?>
