<?php
    require_once("../../config/functions.php");
    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $orgId = isset($_GET['org_id']) && !empty($_GET['org_id']) ? $_GET['org_id'] : $SessionOrgId;


    
    function generateNextID($conn, $orgId, $SessionUserId) {
        $getDateForPatientId = mysqli_query($conn, "SELECT COUNT(DISTINCT appoint_unicode) FROM appointment_online WHERE  org_id='$orgId' ORDER BY appoint_id DESC") or die(mysqli_error($conn));
        $resDateForPatientId = mysqli_fetch_array($getDateForPatientId);
        $count = $resDateForPatientId[0];
    
        $result = $count + 1;
    
        $patient = 'PAT';
        $id = $patient . str_pad($result, 4, '0', STR_PAD_LEFT);
    
        return $id;
    }

    $id = generateNextID($conn, $orgId, $SessionUserId);
    // if($SessionUserId == "1"){
    //     $id = "";
    // }

echo json_encode($id);
?>