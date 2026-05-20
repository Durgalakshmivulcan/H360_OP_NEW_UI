<?php
/**
 * B-2040 — Admin dashboard: 7-day × N-doctor schedule grid.
 *
 * For each (doctor, day in next 7 days incl. today), returns:
 *   slots  : total slot capacity available that day
 *   booked : appointments booked
 *   free   : slots - booked (>=0)
 *   util   : 0..100 (booked/slots)
 *
 * Slot count derivation: doctors have time_slot_duration (minutes); each
 * doctors_time_slot row represents one working day. Total slots/day =
 * working_window_minutes / time_slot_duration. We approximate the window as
 * 8 hours (480 min) when no per-doctor window exists, capped reasonably.
 *
 * Honors org_id + admin_doctor_filter + currentDoctorScopeSql.
 */
require_once(__DIR__ . "/../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');
header('Content-Type: application/json; charset=utf-8');

$SessionRoleId = (int) ($_SESSION['role_id']     ?? 0);
$SessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$SessionOrgId  = (int) ($_SESSION['org_id']      ?? 0);

$isSA = ($SessionRoleId === 1 || $SessionUserId === 1);
$orgClauseDoc = $isSA ? '' : " AND d.org_id = '$SessionOrgId' ";

$adminDocId = (int) ($_SESSION['admin_doctor_filter'] ?? 0);
$docScope   = currentDoctorScopeSql('d.doc_id');

// Build doctors list
$docFilter = '';
if ($adminDocId > 0) $docFilter = " AND d.doc_id = '$adminDocId' ";

$qDoc = "SELECT d.doc_id, d.doctor_name, d.doctor_type, d.doc_img,
                COALESCE(d.time_slot_duration, 15) AS slot_min
         FROM doctors d
         WHERE d.status = '1' $orgClauseDoc $docFilter $docScope
         ORDER BY d.doctor_name ASC";
$rDoc = mysqli_query($conn, $qDoc);
$doctors = [];
while ($row = mysqli_fetch_assoc($rDoc)) {
    $row['slot_min'] = max(5, (int) $row['slot_min']);
    $row['days']    = [];
    $doctors[(int) $row['doc_id']] = $row;
}

// Build day list
$days = [];
for ($i = 0; $i < 7; $i++) {
    $d = date('Y-m-d', strtotime("+$i day"));
    $days[] = ['date' => $d, 'label' => date('D', strtotime($d)), 'short' => date('d M', strtotime($d))];
}

if (empty($doctors)) {
    echo json_encode(['doctors' => [], 'days' => $days]);
    exit;
}

$docIdsCsv = implode(',', array_map('intval', array_keys($doctors)));
$startDate = $days[0]['date'];
$endDate   = $days[6]['date'];

// Slot rows per doctor per day
$qSlot = "SELECT doctorName_registrationNumber AS doc_id, available_date, COUNT(*) AS slot_rows
          FROM doctors_time_slot
          WHERE status = '1'
            AND doctorName_registrationNumber IN ($docIdsCsv)
            AND available_date BETWEEN '$startDate' AND '$endDate'
          GROUP BY doctorName_registrationNumber, available_date";
$rSlot = mysqli_query($conn, $qSlot);
$slotMap = [];
if ($rSlot) {
    while ($r = mysqli_fetch_assoc($rSlot)) {
        $slotMap[(int) $r['doc_id']][$r['available_date']] = (int) $r['slot_rows'];
    }
}

// Booked appointments per doctor per day
$orgClauseAppt = $isSA ? '' : " AND org_id = '$SessionOrgId' ";
$qAppt = "SELECT doctor_name AS doc_id, appoint_date, COUNT(*) AS booked
          FROM appointment_online
          WHERE appoint_status = '1'
            AND doctor_name IN ($docIdsCsv)
            AND appoint_date BETWEEN '$startDate' AND '$endDate'
            $orgClauseAppt
          GROUP BY doctor_name, appoint_date";
$rAppt = mysqli_query($conn, $qAppt);
$apptMap = [];
if ($rAppt) {
    while ($r = mysqli_fetch_assoc($rAppt)) {
        $apptMap[(int) $r['doc_id']][$r['appoint_date']] = (int) $r['booked'];
    }
}

// FIX_B_2222: total slots per day = actual count of doctors_time_slot rows
// for that doctor/day. Prior version always returned floor(480/slot_min) (=32 at 15-min)
// regardless of how many real slots the doctor created, which produced
// wildly inflated capacity figures (e.g. doctor with 6 slots showed 32).
foreach ($doctors as $docId => &$doc) {
    foreach ($days as $day) {
        $totalSlots = (int) ($slotMap[$docId][$day['date']] ?? 0);
        $booked     = (int) ($apptMap[$docId][$day['date']] ?? 0);
        $free       = max(0, $totalSlots - $booked);
        $util       = $totalSlots > 0 ? min(100, (int) round($booked * 100 / $totalSlots)) : 0;
        $doc['days'][] = [
            'date'   => $day['date'],
            'slots'  => $totalSlots,
            'booked' => $booked,
            'free'   => $free,
            'util'   => $util,
        ];
    }
}
unset($doc);

echo json_encode([
    'days'    => $days,
    'doctors' => array_values($doctors),
]);
