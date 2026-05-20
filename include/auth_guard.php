<?php
// Common authentication and authorization guard for H360 PHP endpoints.
//
// This file provides helper functions to ensure that a user is logged
// in and has the appropriate role to access a given resource.  It
// should be included at the top of API endpoints before any output
// occurs.  Adjust the session key names below (role_name, role,
// role_id) to match your application's session structure.

// Start a session if one is not already active.  Without a session
// there is no reliable way to determine user identity or role.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensure that a user is logged in.  If no user is present in the
 * current session, this helper will send a 401 Unauthorized response
 * and terminate the script.  Use this at the top of any endpoint
 * that should only be accessible by authenticated users.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['security_id'])) {
        // No user_id in session – user is not authenticated.
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

/**
 * Ensure that the current user has one of the allowed roles.  Roles
 * may be stored as a string (e.g. "admin", "doctor", "nurse") or as
 * an integer ID in the session.  This function performs a case
 * insensitive comparison for string roles and a strict numeric
 * comparison for integer roles.  If the user does not have an
 * allowed role, a 403 Forbidden response is returned and the script
 * exits.  The function implicitly calls requireLogin() to verify
 * authentication first.
 *
 * @param array $allowed A list of allowed role names or IDs.
 */
function assertRole(array $allowed): void
{
    requireLogin();

    // Determine the user's role from session.  Applications may
    // populate one or more of these session keys.  Adjust if your
    // session stores roles differently (e.g. role_code or role_id).
    $sessionRole = null;
    if (isset($_SESSION['role_name'])) {
        $sessionRole = $_SESSION['role_name'];
    } elseif (isset($_SESSION['role'])) {
        $sessionRole = $_SESSION['role'];
    } elseif (isset($_SESSION['role_id'])) {
        $sessionRole = (int)($_SESSION['role_id'] ?? 0);
    }

    $roleMatch = false;
    foreach ($allowed as $role) {
        // If both values are numeric, compare as integers
        if (is_numeric($sessionRole) && is_numeric($role)) {
            if ((int) $sessionRole === (int) $role) {
                $roleMatch = true;
                break;
            }
        } else {
            // Compare strings case-insensitively
            if (strcasecmp((string) $sessionRole, (string) $role) === 0) {
                $roleMatch = true;
                break;
            }
        }
    }

    if (!$roleMatch) {
        // User is authenticated but does not have permission
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'Forbidden']);
        exit;
    }
}

/**
 * Optionally ensure that the current session has an org_id.  Many
 * multi-tenant applications, like H360, rely on an org_id in the
 * session to scope queries.  Call this helper in endpoints that
 * require an org context.  If no org_id is found, a 400 Bad Request
 * response is returned.
 */
function assertOrgId(): void
{
    if (!isset($_SESSION['org_id'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Missing org context']);
        exit;
    }
}

/**
 * Page-level role gate for full HTML pages (NOT AJAX/JSON endpoints).
 *
 * If the current user's role_id is in $deniedRoleIds, the user is
 * redirected to dashboard.php (a soft, role-aware deny). This protects
 * admin-only screens (e.g. AppointmentOnline.php, doctor.php) from
 * being directly URL-loaded by lower-privileged roles such as the
 * Pharmacist (role_id=12).
 *
 * Usage (top of the page, AFTER ajax/header.php which starts the session):
 *   require_once(__DIR__ . '/include/auth_guard.php');
 *   denyPageRoles([12]); // pharmacist denied
 *
 * Filed under bug B-1300 (RBAC: pharmacist could open admin-only pages
 * via direct URL because page-level role gates were missing).
 *
 * @param array $deniedRoleIds Numeric role_id values that must NOT see this page.
 * @param string $redirectTo   Page to redirect to (default: dashboard.php).
 */
function denyPageRoles(array $deniedRoleIds, string $redirectTo = 'dashboard.php'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $roleId = isset($_SESSION['role_id']) ? (int) $_SESSION['role_id'] : 0;
    if ($roleId === 0) {
        // Not logged in — let other guards / page-level redirects handle it.
        return;
    }
    foreach ($deniedRoleIds as $denied) {
        if ((int) $denied === $roleId) {
            // Already-sent headers (e.g. by ajax/header.php's HTML output)
            // would prevent header() redirect — fall back to JS redirect in
            // that case so the deny still works.
            if (!headers_sent()) {
                header('Location: ' . $redirectTo);
            } else {
                echo "<script>window.location.replace(" . json_encode($redirectTo) . ");</script>";
            }
            exit;
        }
    }
}

// End of auth_guard.php