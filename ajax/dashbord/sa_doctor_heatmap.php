<?php
require_once(__DIR__ . "/sa_guard.php");

$days = isset($_GET['days']) ? max(3, min(31, intval($_GET['days']))) : 7;

/* Top N doctors (by appointment volume in the window) across all orgs. */
$topN = isset($_GET['top']) ? max(3, min(30, intval($_GET['top']))) : 12;

$sqlDocs = "SELECT d.doc_id, d.doctor_name, d.org_id, o.organization_name,
                   COUNT(a.appoint_id) AS vol
            FROM doctors d
            LEFT JOIN appointment_online a
                   ON a.doctor_name = d.doc_id
                  AND a.appoint_date >= (CURDATE() - INTERVAL ($days - 1) DAY)
                  AND a.appoint_date <= CURDATE()
                  AND a.appoint_status='1'
            LEFT JOIN organization o ON o.org_id = d.org_id
            WHERE d.status='1'
            GROUP BY d.doc_id
            ORDER BY vol DESC, d.doctor_name ASC
            LIMIT $topN";
$rDocs = mysqli_query($conn, $sqlDocs);

$doctors = [];
$docIds  = [];
while ($r = mysqli_fetch_assoc($rDocs)) {
    $doctors[] = [
        'doc_id'      => (int) $r['doc_id'],
        'doctor_name' => $r['doctor_name'],
        'org_id'      => (int) $r['org_id'],
        'org_name'    => $r['organization_name'] ?: '—',
    ];
    $docIds[] = (int) $r['doc_id'];
}

/* Build day axis: oldest -> today. */
$dayAxis = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $dayAxis[] = date('Y-m-d', strtotime("-$i days"));
}

/* Cells: per doctor, per day. */
$cells = [];
if (!empty($docIds)) {
    $idsCsv = implode(',', $docIds);
    $sqlAppts = "SELECT doctor_name AS doc_id, appoint_date, COUNT(*) AS c
                 FROM appointment_online
                 WHERE appoint_status='1'
                   AND doctor_name IN ($idsCsv)
                   AND appoint_date BETWEEN (CURDATE() - INTERVAL (" . ($days - 1) . ") DAY) AND CURDATE()
                 GROUP BY doctor_name, appoint_date";
    $r = mysqli_query($conn, $sqlAppts);
    while ($row = mysqli_fetch_assoc($r)) {
        $key = $row['doc_id'] . '|' . $row['appoint_date'];
        $cells[$key] = (int) $row['c'];
    }
}

/* Compose grid. */
$grid = [];
$maxCount = 0;
foreach ($doctors as $d) {
    $rowCells = [];
    foreach ($dayAxis as $day) {
        $k = $d['doc_id'] . '|' . $day;
        $c = $cells[$k] ?? 0;
        if ($c > $maxCount) $maxCount = $c;
        $rowCells[] = ['date' => $day, 'count' => $c];
    }
    $grid[] = [
        'doc_id'      => $d['doc_id'],
        'doctor_name' => $d['doctor_name'],
        'org_name'    => $d['org_name'],
        'cells'       => $rowCells,
    ];
}

echo json_encode([
    'days'      => $dayAxis,
    'rows'      => $grid,
    'max_count' => $maxCount,
    // FIX_B_2200: file is doctorstimeslot.php (lowercase, singular), not doctorTimeSlots.php
    'href_template' => 'doctorstimeslot.php?doc_id={doc_id}&date={date}',
    'generated_at'  => date('c'),
]);
