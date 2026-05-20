<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

header("Content-Type: application/json");
if (!isset($_SESSION['security_id'])) { echo json_encode(['success'=>false,'error'=>'Unauthorized']); exit; }
$SessionUserId = $_SESSION['security_id'] ?? '';
$orgId = ($SessionUserId == 1) ? (int)($_GET['org_id'] ?? 0) : (int)($_SESSION['org_id'] ?? 0);
$type  = in_array($_GET['type'] ?? '', ['medicine','investigation']) ? $_GET['type'] : 'medicine';
$cond  = $orgId ? "AND org_id='$orgId'" : '';

// Ensure table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `instruction_template` (
    `it_id` int(11) NOT NULL AUTO_INCREMENT,
    `template_name` varchar(200) NOT NULL,
    `template_data` text NOT NULL,
    `type` enum('medicine','investigation') NOT NULL DEFAULT 'medicine',
    `status` tinyint(1) NOT NULL DEFAULT 1,
    `org_id` int(11) NOT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`it_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$res = mysqli_query($conn, "SELECT it_id, template_name, template_data FROM instruction_template WHERE status='1' AND type='$type' $cond ORDER BY template_name ASC");
$templates = [];
while ($r = mysqli_fetch_assoc($res)) $templates[] = $r;
echo json_encode(['success' => true, 'templates' => $templates]);
