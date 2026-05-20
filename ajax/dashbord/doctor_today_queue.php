<?php
// B-2020: Doctor Dashboard — today's appointment queue (auto-scoped to logged-in doctor).
require_once("../../config/functions.php");
requireCan('view', 'dashboard.php', 'ajax');

header('Content-Type: application/json');

$SessionOrgId = $_SESSION['org_id'] ?? 0;
$docScope     = currentDoctorScopeSql('a.doctor_name'); // ' AND a.doctor_name=<doc_id> '
$orgId        = (int) $SessionOrgId;

// Today's queue for THIS doctor — booked appointments (appoint_status='1') for today,
// ordered by start_time. Each row carries the click-through fields the partial needs.
$sql = "SELECT
            a.appoint_id,
            a.appoint_register_id,
            a.appoint_unicode,
            a.patient_name,
            a.age,
            a.gender,
            a.mobile_number,
            a.start_time,
            a.end_time,
            a.check_in,
            a.check_out,
            a.visitor_status
        FROM appointment_online AS a
        WHERE a.appoint_status='1'
          AND a.appoint_date = CURDATE()
          AND a.org_id = '$orgId'
          $docScope
        ORDER BY a.start_time ASC, a.appoint_id ASC
        LIMIT 100";

$res  = mysqli_query($conn, $sql);
$rows = [];
$tok  = 0;
while ($res && ($r = mysqli_fetch_assoc($res))) {
    $tok++;
    // visitor_status: '0'=cancelled, '1'=waiting, '2'=in-consult, '3'=done
    $vs = (string) ($r['visitor_status'] ?? '1');
    $statusLabel = 'Waiting';
    $statusKey   = 'waiting';
    if ($vs === '0') { $statusLabel = 'Cancelled'; $statusKey = 'cancelled'; }
    elseif ($vs === '2') { $statusLabel = 'In Consult'; $statusKey = 'active'; }
    elseif ($vs === '3') { $statusLabel = 'Done'; $statusKey = 'done'; }

    $rows[] = [
        'token'                => $tok,
        'appoint_id'           => (int) $r['appoint_id'],
        'appoint_register_id'  => $r['appoint_register_id'],
        'appoint_unicode'      => $r['appoint_unicode'],
        'patient_name'         => $r['patient_name'],
        'age'                  => $r['age'],
        'gender'               => $r['gender'],
        'mobile_number'        => $r['mobile_number'],
        'start_time'           => $r['start_time'],
        'end_time'             => $r['end_time'],
        'status_label'         => $statusLabel,
        'status_key'           => $statusKey,
    ];
}

echo json_encode([
    'success' => true,
    'count'   => count($rows),
    'rows'    => $rows,
]);
