<?php
// IDOR_FIXED B-562
require_once("../../config/functions.php");
requireCan('delete', 'concession.php', 'ajax'); // FIX_B_1810

$SessionOrgId = $_SESSION['org_id'] ?? '';
if(isset($_POST['concession_id'])) {
    $concession_id = $_POST['concession_id'];
    $beforeQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id ='$concession_id' LIMIT 1");
    $before      = null;
    if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
        $before = mysqli_fetch_assoc($beforeQuery);
    }
    $delete = mysqli_query($conn, "UPDATE concessions SET status='0' WHERE concession_id='$concession_id' AND org_id='$SessionOrgId'");

    if($delete) {
        $afterQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
        $after      = null;
        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
            $after = mysqli_fetch_assoc($afterQuery);
        }

        // 🔹 Log delete
        audit_log($conn, "Concessions", "delete", "concessions", $concession_id, $before, $after);
        echo 1;
    } else {
        echo 0;
    }
} else {
    echo 0;
}
?>
