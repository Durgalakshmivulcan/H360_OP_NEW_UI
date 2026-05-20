<?php
// IDOR_FIXED B-576
require_once("../../config/functions.php");

$SessionOrgId = $_SESSION['org_id'] ?? '';

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('edit', 'bill.php', 'ajax');

if (isset($_POST['bill_id'])) {
    $bill_id = $_POST['bill_id'];

    // Check if the print date already exists
    $query = "SELECT print_date FROM appointment_online WHERE bill_id = '$bill_id'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if (empty($row['print_date']) || $row['print_date'] == '0000-00-00') {
        // Insert current date if no print date exists
        $current_date = date("Y-m-d");
        $updateQuery = "UPDATE bill_table SET print_date = '$current_date' WHERE bill_id = '$bill_id' AND org_id='$SessionOrgId'";
        mysqli_query($conn, $updateQuery);
    }
}

?>
