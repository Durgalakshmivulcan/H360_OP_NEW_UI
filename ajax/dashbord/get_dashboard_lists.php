<?php
require_once("../../config/functions.php");

header('Content-Type: application/json');

$sessionUserId = $_SESSION['security_id'] ?? '';
$sessionRoleId = $_SESSION['role_id'] ?? '';
$sessionOrgId = $_SESSION['org_id'] ?? '';
$securityId = !empty($_GET['security_id']) ? (int) $_GET['security_id'] : (int) $sessionUserId;

$isSA    = ((string)$sessionUserId === '1' || (string)$sessionRoleId === '1');
$isAdmin = ((string)$sessionRoleId === '6');

// Determine the effective security_id to filter by.
// Doctors are always locked to their own data regardless of the passed parameter.
// Receptionists/Admins use the passed security_id so the avatar selector works.
$effectiveSecId = (int)$securityId;
if (!$isSA && !$isAdmin && !empty($sessionUserId)) {
    $esc_me = mysqli_real_escape_string($conn, $sessionUserId);
    $myDoc = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT doc_id FROM doctors WHERE security_id='$esc_me' AND status='1' LIMIT 1"));
    if ($myDoc && !empty($myDoc['doc_id'])) {
        // Logged-in user is a doctor — force their own security_id
        $effectiveSecId = (int)$sessionUserId;
    }
}

// Resolve effectiveSecId → doc_id for appointment_online.doctor_name filtering
$filterDocId   = null; // null = no doctor filter (SA or admin seeing all)
$filterSecId   = null; // security_id for gynaec_prescriptions.created_by
if (!$isSA && $effectiveSecId > 0) {
    $esc_esid = mysqli_real_escape_string($conn, (string)$effectiveSecId);
    $drRow = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT doc_id FROM doctors WHERE security_id='$esc_esid' AND status='1' LIMIT 1"));
    if ($drRow && !empty($drRow['doc_id'])) {
        $filterDocId = (int)$drRow['doc_id'];
        $filterSecId = $effectiveSecId;
    }
}

$orgFilter = "";
$gOrgFilter = "";
$birthdayOrgFilter = "";

if (!empty($sessionOrgId)) {
    $esc_org = mysqli_real_escape_string($conn, (string) $sessionOrgId);
    $orgFilter        = " AND ao.org_id = '$esc_org'";
    $gOrgFilter       = " AND gp.org_id = '$esc_org'";
    $birthdayOrgFilter = " AND ao.org_id = '$esc_org'";
}

// Apply doctor-level scope using resolved doc_id / security_id
if ($filterDocId !== null) {
    $orgFilter        .= " AND ao.doctor_name = '$filterDocId'";
    $birthdayOrgFilter .= " AND ao.doctor_name = '$filterDocId'";
    $gOrgFilter       .= " AND gp.created_by = '$filterSecId'";
}

$birthdaySql = "
    SELECT
        ao.appoint_unicode,
        ao.patient_name,
        ao.mobile_number,
        ao.dob,
        CASE
            WHEN DATE_FORMAT(ao.dob, '%m-%d') >= DATE_FORMAT(CURDATE(), '%m-%d')
                THEN STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(ao.dob, '%m-%d')), '%Y-%m-%d')
            ELSE STR_TO_DATE(CONCAT(YEAR(CURDATE()) + 1, '-', DATE_FORMAT(ao.dob, '%m-%d')), '%Y-%m-%d')
        END AS next_birthday
    FROM appointment_online ao
    INNER JOIN (
        SELECT ao.org_id, ao.appoint_unicode, MAX(ao.appoint_id) AS latest_id
        FROM appointment_online ao
        WHERE ao.appoint_status = '1'
          AND ao.dob IS NOT NULL
          AND ao.dob <> ''
          AND ao.dob <> '0000-00-00'
          $birthdayOrgFilter
        GROUP BY ao.org_id, ao.appoint_unicode
    ) latest ON latest.latest_id = ao.appoint_id
    WHERE ao.appoint_status = '1'
      AND ao.dob IS NOT NULL
      AND ao.dob <> ''
      AND ao.dob <> '0000-00-00'
    HAVING next_birthday IS NOT NULL
    ORDER BY next_birthday ASC, ao.patient_name ASC
    LIMIT 10
";

$revisitSql = "
    SELECT
        revisit_source.patient_id AS appoint_unicode,
        revisit_source.patient_name,
        revisit_source.mobile_number,
        revisit_source.reviewafterdate,
        revisit_source.doctor_name,
        revisit_source.department_name
    FROM (
        SELECT
            p.patient_uid AS patient_id,
            p.patient_name,
            COALESCE(ao.mobile_number, '') AS mobile_number,
            p.reviewafterdate,
            COALESCE(d.doctor_name, '') AS doctor_name,
            COALESCE(dep.departmentName, '') AS department_name,
            STR_TO_DATE(p.reviewafterdate, '%Y-%m-%d') AS sort_review_date
        FROM prescripition p
        INNER JOIN (
            SELECT patient_uid, MAX(prescription_id) AS latest_rx_id
            FROM prescripition
            WHERE status = '1'
              AND gynaec_mirror = 0
              AND reviewafterdate IS NOT NULL
              AND reviewafterdate <> ''
              AND reviewafterdate <> '0000-00-00'
              AND STR_TO_DATE(reviewafterdate, '%Y-%m-%d') >= CURDATE()
            GROUP BY patient_uid
        ) latest ON latest.latest_rx_id = p.prescription_id
        LEFT JOIN appointment_online ao ON ao.appoint_register_id = p.appoint_register_id
        LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
        LEFT JOIN department dep ON dep.dept_id = d.departments AND dep.status = '1'
        WHERE p.status = '1'
          AND p.gynaec_mirror = 0
          AND p.reviewafterdate IS NOT NULL
          AND p.reviewafterdate <> ''
          AND p.reviewafterdate <> '0000-00-00'
          AND STR_TO_DATE(p.reviewafterdate, '%Y-%m-%d') >= CURDATE()
          $orgFilter

        UNION ALL

        SELECT
            gp.patient_id AS patient_id,
            gp.patient_name,
            gp.mobile AS mobile_number,
            gp.reviewafterdate,
            COALESCE(dg.doctor_name, '') AS doctor_name,
            COALESCE(depg.departmentName, '') AS department_name,
            STR_TO_DATE(gp.reviewafterdate, '%Y-%m-%d') AS sort_review_date
        FROM gynaec_prescriptions gp
        LEFT JOIN doctors dg ON dg.security_id = gp.created_by AND dg.status = '1'
        LEFT JOIN department depg ON depg.dept_id = dg.departments AND depg.status = '1'
        INNER JOIN (
            SELECT patient_id, MAX(gynaec_rx_id) AS latest_rx_id
            FROM gynaec_prescriptions
            WHERE status = '1'
              AND reviewafterdate IS NOT NULL
              AND reviewafterdate <> ''
              AND reviewafterdate <> '0000-00-00'
              AND STR_TO_DATE(reviewafterdate, '%Y-%m-%d') >= CURDATE()
            GROUP BY patient_id
        ) glatest ON glatest.latest_rx_id = gp.gynaec_rx_id
        WHERE gp.status = '1'
          AND gp.reviewafterdate IS NOT NULL
          AND gp.reviewafterdate <> ''
          AND gp.reviewafterdate <> '0000-00-00'
          AND STR_TO_DATE(gp.reviewafterdate, '%Y-%m-%d') >= CURDATE()
          $gOrgFilter
    ) revisit_source
    WHERE revisit_source.sort_review_date IS NOT NULL
    ORDER BY revisit_source.sort_review_date ASC, revisit_source.patient_name ASC
    LIMIT 10
";

$birthdayResult = mysqli_query($conn, $birthdaySql);
$revisitResult = mysqli_query($conn, $revisitSql);

if (!$birthdayResult || !$revisitResult) {
    echo json_encode([
        'success' => false,
        'message' => mysqli_error($conn),
        'birthdays' => [],
        'today_birthdays' => [],
        'revisits' => []
    ]);
    exit;
}

$today = new DateTime(date('Y-m-d'));
$birthdays = [];
$todayBirthdays = [];

while ($row = mysqli_fetch_assoc($birthdayResult)) {
    try {
        $dob = new DateTime($row['dob']);
        $nextBirthday = new DateTime($row['next_birthday']);
    } catch (Exception $e) {
        continue;
    }

    $daysLeft = (int) $today->diff($nextBirthday)->format('%r%a');
    if ($daysLeft < 0) {
        continue;
    }

    if ($daysLeft === 0) {
        $daysLabel = 'Today';
    } elseif ($daysLeft === 1) {
        $daysLabel = 'Tomorrow';
    } else {
        $daysLabel = 'In ' . $daysLeft . ' days';
    }

    $item = [
        'patient_id' => $row['appoint_unicode'],
        'patient_name' => $row['patient_name'],
        'mobile_number' => $row['mobile_number'],
        'next_birthday_display' => $nextBirthday->format('d M Y'),
        'days_label' => $daysLabel,
        'turning_age' => $dob->diff($nextBirthday)->y
    ];

    $birthdays[] = $item;

    if ($daysLeft === 0) {
        $todayBirthdays[] = $item;
    }
}

$revisits = [];
while ($row = mysqli_fetch_assoc($revisitResult)) {
    try {
        $revisitDate = new DateTime($row['reviewafterdate']);
    } catch (Exception $e) {
        continue;
    }

    $revisits[] = [
        'patient_id' => $row['appoint_unicode'],
        'patient_name' => $row['patient_name'],
        'mobile_number' => $row['mobile_number'],
        'doctor_name' => $row['doctor_name'],
        'department_name' => $row['department_name'],
        'revisit_date_display' => $revisitDate->format('d M Y'),
        'days_label' => 'Review After'
    ];
}

echo json_encode([
    'success' => true,
    'birthdays' => $birthdays,
    'today_birthdays' => $todayBirthdays,
    'revisits' => $revisits
]);
?>
