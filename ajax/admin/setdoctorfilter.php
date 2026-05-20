<?php
// FIX_B_2002: Admin (role_id=6) doctor-switcher setter. Persists the filter
// in $_SESSION so currentDoctorScopeSql() returns the right WHERE fragment
// for downstream queries.
require_once("../../config/functions.php");
header('Content-Type: application/json');

$role = (int) ($_SESSION['role_id'] ?? 0);
// FIX_B_2226 + FIX_B_2320: only roles whose queries actually consume the
// filter may write it — Admin (6) and Receptionist (3, but only when opted in).
// SA (1) bypasses the filter in currentDoctorScopeSql(), so writing it would
// be a no-op. Anyone else writing is a permission leak.
// FIX_B_23420: read live (no $_SESSION cache) so admin un-checking the flag
// takes effect immediately for an already-logged-in receptionist, and so
// direct AJAX (no preceding header.php pageload) does not 403 spuriously.
$allowed = ($role === 6) || ($role === 3 && canSwitchDoctorLive($conn) === 1);
if (!$allowed) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

// FIX_B_2226: scope active-doctor lookup to caller's org_id to prevent
// cross-org doctor selection.
$orgId  = (int) ($_SESSION['org_id'] ?? 0);
$filter = $_POST['doc_id'] ?? 'all';
if ($filter === '' || $filter === '0' || $filter === 'all') {
    $_SESSION['admin_doctor_filter'] = 'all';
} else {
    $f = (int) $filter;
    if ($f <= 0) { http_response_code(400); echo json_encode(['error'=>'invalid doctor']); exit; }
    $orgClause = $orgId > 0 ? " AND org_id='$orgId' " : '';
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT doc_id FROM doctors WHERE doc_id='$f' AND status='1' $orgClause LIMIT 1"));
    if (!$r) { http_response_code(400); echo json_encode(['error'=>'unknown doctor']); exit; }
    $_SESSION['admin_doctor_filter'] = (string) $f;
}
echo json_encode(['ok' => true, 'filter' => $_SESSION['admin_doctor_filter']]);
