<?php
require_once("../../config/functions.php");

header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

// $date = $_GET["security_id"];
$security_id = $SessionUserId;
if (!empty($_GET['security_id'])) {
    $security_id = intval($_GET['security_id']);
}

$adminQry = ($SessionUserId != "1") ? " AND a.org_id='$SessionOrgId'" : "";

function calculateWorkingDays($fromDate, $toDate, $selectedDays) {
    $from = new DateTime($fromDate);
    $to   = new DateTime($toDate);

    $workingDays = 0;
    $daysArray = explode(',', $selectedDays); 

    while ($from <= $to) {
        $dayOfWeek = $from->format('N'); 
        if (in_array($dayOfWeek, $daysArray)) {
            $workingDays++;
        }
        $from->modify('+1 day');
    }
    return $workingDays;
}

function calculateSlotsFromTiming($start, $end, $slotDuration = 15) {
    $startTime = DateTime::createFromFormat('H:i', $start);
    $endTime   = DateTime::createFromFormat('H:i', $end);

    if (!$startTime || !$endTime) return 0;

    $diffMinutes = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 60;
    if ($diffMinutes <= 0) return 0;

    return floor($diffMinutes / $slotDuration); 
}

$query = "
    SELECT 
        d.doc_id AS doctor_id,
        d.doctor_name,
        d.doctor_type,
        d.phone_number,
        d.email,
        d.doc_img
    FROM doctors d
    WHERE d.status = '1' AND d.security_id = '$security_id'
    ORDER BY d.doctor_name
";
$result = mysqli_query($conn, $query);
if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "SQL Error: " . mysqli_error($conn)
    ]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {

    $slotQuery = "
        SELECT ts2.starting_Time, ts2.ending_Time 
        FROM doctors_time_slot2 ts2
        JOIN doctors_time_slot ts ON ts2.doctors_time_id = ts.doctors_time_id
        WHERE ts.doctorName_registrationNumber = '".$row['doctor_id']."'
          AND ts2.status = '1'
          AND ts.status = '1'
    ";
    $slotResult = mysqli_query($conn, $slotQuery);
    $slots = 0;
    if ($slotResult) {
        while ($sRow = mysqli_fetch_assoc($slotResult)) {
            $slots += calculateSlotsFromTiming($sRow['starting_Time'], $sRow['ending_Time'], 15);
        }
    }

    $workingDays = 0;
    $mdtQuery = "
        SELECT from_date, to_date, selectedDays 
        FROM multi_doctortimeslots
        WHERE doctorName_registrationNumber = '".$row['doctor_id']."' 
          AND status = '1'
    ";
    $mdtResult = mysqli_query($conn, $mdtQuery);
    if ($mdtResult) {
        while ($mdtRow = mysqli_fetch_assoc($mdtResult)) {
            $workingDays += calculateWorkingDays(
                $mdtRow['from_date'],
                $mdtRow['to_date'],
                $mdtRow['selectedDays']
            );
        }
    }

    $dailyCounts = []; 

$slotQuery = "
    SELECT available_date
    FROM doctors_time_slot
    WHERE status = '1'
      AND doctorName_registrationNumber = '".$row['doctor_id']."'
";
$slotResult = mysqli_query($conn, $slotQuery);

if ($slotResult) {
    while ($slotRow = mysqli_fetch_assoc($slotResult)) {
        $date = $slotRow['available_date'];

        if (!isset($dailyCounts[$date])) {
            $dailyCounts[$date] = 0;
        }
        $dailyCounts[$date]++;
    }
}

$dailyCountTotal = count($dailyCounts); 

    if ($slots == 0 && $workingDays == 0) continue;

    $total = $slots + $workingDays;

    $slots_percent   = $total > 0 ? round(($slots / $total) * 100, 1) : 0;
    $working_percent = $total > 0 ? round(($workingDays / $total) * 100, 1) : 0;

    $type = [];
    if ($slots > 0) $type[] = "Per-Day";
    if ($workingDays > 0) $type[] = "Range";

    $data[] = [
        'doctor_id'       => $row['doctor_id'],
        'doctor'          => $row['doctor_name'],
        'doctor_type'     => implode(" & ", $type),
        'phone'           => $row['phone_number'],
        'email'           => $row['email'],
        'slots'           => $slots,
        'working_days'    => $workingDays,
        'dailyCounts'   => $dailyCountTotal, 
        'total_slots'     => $total,       
        'slots_percent'   => $slots_percent,
        'working_percent' => $working_percent,
        'doc_img'         => $row['doc_img'] ?? ''
    ];
}

echo json_encode([
    "status" => "success",
    "count"  => count($data),
    "data"   => $data
]);
