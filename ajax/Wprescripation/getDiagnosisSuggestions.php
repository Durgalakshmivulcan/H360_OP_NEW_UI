<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$keyword = $_GET['q'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

if (empty($keyword)) {
    echo json_encode([]);
    exit;
}

// Get RX Groups
$sql = "SELECT rx_group_id, rx_group_name, medicine_detailes 
        FROM rx_groups_names 
        WHERE rx_group_name LIKE '%$keyword%' 
          AND org_id = '$SessionOrgId'
        LIMIT 50";
$result = mysqli_query($conn, $sql);

$suggestions = [];
$existingMedicineSets = []; // store name + medicine sets to avoid exact duplicates

while ($row = mysqli_fetch_assoc($result)) {
    $rxName = $row['rx_group_name'];
    $medicines = json_decode($row['medicine_detailes'], true);

    if (!is_array($medicines)) continue;

    // get sorted medicine names
    $medNames = array_map(fn($m) => trim($m['medicine_name'] ?? ''), $medicines);
    sort($medNames);

    // combine RX name + medicine set for duplicate check
    $medSetKey = $rxName . '|' . json_encode($medNames);

    if (in_array($medSetKey, $existingMedicineSets)) continue;
    $existingMedicineSets[] = $medSetKey;

    $medObjArray = [];
    foreach ($medicines as $m) {
        if (empty($m['medicine_name'])) continue;

        $unitText = $m['unit_text'] ?? "-";
        $timeText = $m['timeText'] ?? "No time available";

        $medObjArray[] = [
            'medicine_id'   => $m['medicine_id'] ?? '',
            'medicine_name' => $m['medicine_name'],
            'type_id'       => $m['type_id'] ?? '',
            'type_text'     => $m['type_text'] ?? '',
            'unit_id'       => $m['unit_id'] ?? '',
            'unit_text'     => $unitText,
            'dosage_id'     => $m['dosage_id'] ?? '',
            'dosageText'    => $m['dosageText'] ?? '',
            'when_id'       => $m['when_id'] ?? '',
            'whenText'      => $m['whenText'] ?? '',
            'time_id'       => $m['time_id'] ?? '',
            'timeText'      => $timeText,
            'duration_value'=> $m['duration_value'] ?? '',
            'duration'      => $m['duration'] ?? '',
            'notes'         => $m['notes'] ?? ''
        ];
    }

    if (!empty($medObjArray)) {
        $suggestions[] = [
            'rx_group_name' => $rxName,
            'rx_group_id'   => $row['rx_group_id'],
            'medicines'     => $medObjArray
        ];
    }
}

echo json_encode($suggestions);
