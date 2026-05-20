<?php
// FIX_B_2310: rewritten for the TV display redesign.
// The visitor display is a *shared* org-wide screen — it must show every doctor
// who has patients in the called/waiting queue today, not just one. Previously
// this filtered by the logged-in user's security_id, so a TV logged in as
// "reception" got no data. Now scoped only by org_id, returning one block per
// active doctor.

error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('zlib.output_compression', 0);
@ini_set('output_buffering', 0);
@ini_set('implicit_flush', 1);
while (ob_get_level()) ob_end_flush();
ob_implicit_flush(true);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

require("../../config/functions.php");
global $conn;

$SessionOrgId = (int) ($_SESSION['org_id'] ?? 1);
$today        = date("Y-m-d");

function tvDisplayPayload($conn, $orgId, $today) {
    // FIX_B_2341a: doctor_specialization stores the specialtis_id, not a label.
    // JOIN specialtis so the TV shows "Cardiology" instead of "18".
    $sql = "SELECT a.appoint_register_id, a.appoint_unicode, a.patient_name,
                   a.visitor_status, a.appoint_date, a.doctor_name AS doc_id,
                   a.start_time, a.end_time,
                   d.doctor_name AS doctor_name, d.doc_img,
                   COALESCE(s.specialtisname, '') AS specialization
              FROM appointment_online a
              LEFT JOIN doctors d
                ON d.doc_id = a.doctor_name AND d.org_id = '$orgId'
              LEFT JOIN specialtis s
                ON s.specialtis_id = d.doctor_specialization
             WHERE DATE(a.appoint_date) = '$today'
               AND a.org_id = '$orgId'
               AND a.visitor_status = '1'
             ORDER BY a.doctor_name ASC, a.appoint_date ASC, a.start_time ASC";

    $result = mysqli_query($conn, $sql);
    $byDoctor = [];
    while ($result && $row = mysqli_fetch_assoc($result)) {
        $docId = $row['doc_id'] ?: 'unassigned';
        if (!isset($byDoctor[$docId])) {
            $byDoctor[$docId] = [
                'doctor' => [
                    'doc_id'         => $docId,
                    'doctor_name'    => $row['doctor_name'] ?: 'Doctor',
                    'doc_img'        => $row['doc_img'] ?: 'default.png',
                    'specialization' => $row['specialization'] ?: '',
                ],
                'next'     => null,
                'upcoming' => null,
                'queue'    => [],
            ];
        }
        $patientRow = [
            'appoint_unicode' => $row['appoint_unicode'],
            'patient_name'    => $row['patient_name'],
            'start_time'      => $row['start_time'],
        ];
        $bucket = &$byDoctor[$docId];
        if ($bucket['next'] === null)         $bucket['next']     = $patientRow;
        elseif ($bucket['upcoming'] === null) $bucket['upcoming'] = $patientRow;
        else                                  $bucket['queue'][]  = $patientRow;
        unset($bucket);
    }

    return [
        'doctors'     => array_values($byDoctor),
        'doctorCount' => count($byDoctor),
        'org_id'      => $orgId,
        'now'         => date('c'),
    ];
}

$data = tvDisplayPayload($conn, $SessionOrgId, $today);
echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";

ob_flush();
flush();
$conn->close();
exit();
