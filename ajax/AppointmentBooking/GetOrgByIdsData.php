<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$result = [];

$org_id = $_POST['org_id'];

if(!$org_id) {
    $result[] = array(
        'Appoint_Unicode' => '',
        'Latest_Appoint_Register_Id' => '',
    );
    
    echo json_encode($result);
    return;
}

$totalCountQuery = mysqli_query($conn, "SELECT COUNT(appoint_register_id) AS Total_Count, MAX(appoint_register_id) AS Latest_Appoint_Register_Id FROM appointment_online WHERE appoint_status='1' AND org_id='$org_id'") or die(mysqli_error($conn));
$totalCountRow = mysqli_fetch_assoc($totalCountQuery);
$totalCount = $totalCountRow['Total_Count'];
$appoint_register_id = $totalCountRow['Latest_Appoint_Register_Id'];


$currentDateCountQuery = mysqli_query($conn, "SELECT COUNT(appoint_register_id) AS Current_Date_Count FROM appointment_online WHERE appoint_status='1' AND org_id='$org_id' AND create_date_time LIKE '$currentDate%'") or die(mysqli_error($conn));
$currentDateCountRow = mysqli_fetch_assoc($currentDateCountQuery);
$currentDateCount = $currentDateCountRow['Current_Date_Count'];

$Pat = 'PAT' . str_pad($totalCount, 4, '0', STR_PAD_LEFT);

// echo $pat;

if($Pat=="PAT0000"){
    $appoint_unicode = 'PAT' . str_pad($totalCount+1, 4, '0', STR_PAD_LEFT);

} else{
    $appoint_unicode = 'PAT' . str_pad($totalCount+1, 4, '0', STR_PAD_LEFT);

}

if($currentDateCount=="0"){
    $today = date('d-m-Y');
    $dateComponents = explode('-', $today);
    $date = $dateComponents[0];
    $month = $dateComponents[1];
    $year = $dateComponents[2];
    $patient = 'A';
    $id = $patient . $year . $month . $date . str_pad($currentDateCount+1, 4, '0', STR_PAD_LEFT);
} else{
    $numericPart = (int)substr($appoint_register_id, 1);

    $numericPart++;
    
    $id = 'A' . str_pad($numericPart, 12, '0', STR_PAD_LEFT);
}

$result[] = array(
    'Appoint_Unicode' => $appoint_unicode,
    'Latest_Appoint_Register_Id' => $id,
);

echo json_encode($result);








?>