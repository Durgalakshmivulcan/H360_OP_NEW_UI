

<?php
    require_once "../../config/functions.php";
    $results = [];
    $dose_id = isset($_GET['dose_id']) ? intval($_GET['dose_id']) : null;
    
    $whereClause = "WHERE status='1'";
    if ($dose_id !== null) {
        $whereClause .= " AND dose_id='" . $dose_id . "'";
    }
    
    $sql = mysqli_query($conn, "SELECT time_id, time, dose_id FROM times $whereClause ") or die(mysqli_error($conn));
    while ($row = mysqli_fetch_object($sql)) {
        $results[] = $row;
    }
    
    echo json_encode($results);
?>
