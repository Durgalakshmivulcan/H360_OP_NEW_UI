<?php
// FIX_B_206 / FIX_B_702: minimal departments-fetcher for org_reports.php.
// Scope: session org_id only (never POST). Login required.
//
// B-702 root-cause: the previous version included
// `config/functions.php` whose top-level `require_once("config.php")` is
// a *relative* path that does not resolve to `config/config.php` in the
// ajax/billing/ run-context (PHP's include_path is `.:` so it searches
// the running script's directory first, never finds config.php, and the
// require fatals -> HTTP 500).
//
// Fix: include config/config.php directly via __DIR__ (same pattern as
// the sibling getprice.php stub) and skip functions.php entirely. We
// only need $conn here.
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../include/auth_guard.php");

if (session_status() === PHP_SESSION_NONE) { session_start(); }
requireLogin();
assertOrgId();

header('Content-Type: application/json');

$SessionOrgId  = (int) ($_SESSION['org_id'] ?? 0);
$SessionUserId = $_SESSION['security_id'] ?? '';

$rows = [];

// FIX_B_702 (SA-fatal sweep template): SA (security_id == 1) sees every
// org's departments; everyone else is scoped to their session org. The
// SA branch uses an UNSCOPED query (no org predicate) — without it, SA
// users running cross-tenant reports would get an empty result set from
// the org-scoped predicate and the page would appear broken. Mirrors
// the SA-cross-tenant contract used throughout the codebase.
if ($SessionUserId == '1') {
    $sql = "SELECT dept_id, departmentName FROM department WHERE status='1' ORDER BY departmentName ASC";
} else {
    $sql = "SELECT dept_id, departmentName FROM department WHERE org_id='" . (int)$SessionOrgId
         . "' AND status='1' ORDER BY departmentName ASC";
}

$res = @mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = [
            'dept_id'   => $row['dept_id'],
            'departmentName' => $row['departmentName'],
        ];
    }
}

// Match the JS contract: org_reports.php expects an array; other callers
// tolerate {data:[]}. Returning a plain array is consistent with how the
// page consumes it (just console.log'd). Keep simple.
echo json_encode($rows);
