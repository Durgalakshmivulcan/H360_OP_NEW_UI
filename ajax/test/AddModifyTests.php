<?php
// IDOR_FIXED B-585
require_once("../../config/functions.php");
requireCan(empty($_POST['test_id']) ? 'add' : 'edit', 'test.php', 'ajax'); // FIX_B_1810

$SessionUserId   = $_SESSION['security_id'] ?? '';
$SessionOrgId    = $_SESSION['org_id'] ?? '';
$datetime        = date("Y-m-d H:i:s");

$test_id         = $_POST['test_id'] ?? '';
$test_name       = $_POST['test_name'] ?? '';
$normal_range    = $_POST['normal_range'] ?? '';
$test_price      = $_POST['test_price'] ?? '';
$test_gst        = $_POST['test_gst'] ?? '';
$org_id          = $_POST['organizations'] ?? $SessionOrgId;
$excelData       = $_POST['excelData'] ?? '';

$msg = 0;
$duplicates = [];

// --------------------------
// 1️⃣ Bulk Excel upload
// --------------------------
if(!empty($excelData)){
    $rows = json_decode($excelData, true);

    if(is_array($rows) && count($rows) > 0){
        $inserted = 0;
        foreach($rows as $row){
            $tName   = trim($row['Test Name *'] ?? $row['Test Name'] ?? '');
            $nRange  = trim($row['Normal Range *'] ?? $row['Normal Range'] ?? '');
            $tPrice  = trim($row['Price *'] ?? $row['Price'] ?? '');
            $tGst    = trim($row['GST *'] ?? $row['GST'] ?? '');

            if(empty($tName) || empty($tPrice) || empty($tGst)) continue;

            // Duplicate check
            $check = mysqli_query($conn, "SELECT test_id FROM tests WHERE test_name='$tName' AND org_id='$org_id' AND status='1'");
            if(mysqli_num_rows($check) > 0){
                $duplicates[] = $tName;
                continue;
            }

            // Insert
            mysqli_query($conn, "INSERT INTO tests(test_name, normal_range, test_price, test_gst, status, created_by, modified_by, create_date_time, org_id)
                                 VALUES ('$tName','$nRange','$tPrice','$tGst','1','$SessionUserId','$SessionUserId','$datetime','$org_id')");
            $inserted++;
        }

        if($inserted > 0 && count($duplicates) == 0) $msg = 1;      // All inserted
        else if($inserted == 0 && count($duplicates) > 0) $msg = 3; // All duplicates
        else if($inserted > 0 && count($duplicates) > 0) $msg = 4;  // Partial inserted
        echo json_encode(['msg'=>$msg,'duplicates'=>$duplicates]);
        exit;
    }
}

// --------------------------
// 2️⃣ Single test insert/update
// --------------------------
if(!empty($test_name) && !empty($test_price)){
    if(!empty($test_id)){
        // UPDATE
        $check = mysqli_query($conn, "SELECT test_id FROM tests WHERE test_name='$test_name' AND test_id!='$test_id' AND org_id='$org_id' AND status='1'");
        if(mysqli_num_rows($check) > 0){
            $msg = 3; // Duplicate
        } else {
            $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tests WHERE test_id='$test_id'"));
            mysqli_query($conn, "UPDATE tests SET test_name='$test_name', normal_range='$normal_range', test_price='$test_price', test_gst='$test_gst', modified_by='$SessionUserId' WHERE test_id='$test_id' AND org_id='$SessionOrgId'");
            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tests WHERE test_id='$test_id'"));

            audit_log($conn, "Tests", "update", "tests", $test_id, $before, $after);
            $msg = 2; // Updated
        }
    } else {
        // INSERT
        $check = mysqli_query($conn, "SELECT test_id FROM tests WHERE test_name='$test_name' AND org_id='$org_id' AND status='1'");
        if(mysqli_num_rows($check) > 0){
            $msg = 3; // Duplicate
        } else {
            mysqli_query($conn, "INSERT INTO tests(test_name, normal_range, test_price, test_gst, status, created_by, modified_by, create_date_time, org_id)
                                 VALUES ('$test_name','$normal_range','$test_price','$test_gst','1','$SessionUserId','$SessionUserId','$datetime','$org_id')");
            $newId = mysqli_insert_id($conn);
            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tests WHERE test_id='$newId'"));

            // log
            audit_log($conn, "Tests", "create", "tests", $newId, null, $after);
            $msg = 1; // Added
        }
    }
}

echo $msg;
?>
