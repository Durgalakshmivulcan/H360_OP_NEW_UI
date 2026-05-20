<?php
require_once("../../config/functions.php");
header("Content-Type: application/json");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$from       = $_GET['from'] ?? null;
$patient_id = $_GET['patient_id'] ?? 'All';
$doctor_id  = $_GET['doctor_id'] ?? 'All';
$orgId      = $_GET['orgId'] ?? $SessionOrgId;

// ---------- Get security type ----------
$checkSecurity = mysqli_query($conn, "
    SELECT security_type 
    FROM security 
    WHERE status='1' AND security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'
");
$securityType = mysqli_fetch_assoc($checkSecurity)['security_type'] ?? '';

// ---------- Build base filter ----------
$where = " WHERE inv.org_id = '" . mysqli_real_escape_string($conn, $orgId) . "' AND inv.status='1' ";

if ($from) $where .= " AND DATE(inv.created_at) >= '" . mysqli_real_escape_string($conn, $from) . "' ";
// if ($to)   $where .= " AND DATE(inv.created_at) <= '".mysqli_real_escape_string($conn, $to)."' ";
if ($patient_id !== "All") $where .= " AND inv.patient_id = '" . mysqli_real_escape_string($conn, $patient_id) . "' ";
if ($doctor_id !== "All")  $where .= " AND ao.doctor_name = '" . mysqli_real_escape_string($conn, $doctor_id) . "' ";

// ---------- User-based filter ----------
if ($securityType === 'U') {
    $docRes = mysqli_query($conn, "
        SELECT doc_id 
        FROM doctors 
        WHERE security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "' AND status='1'
        LIMIT 1
    ");
    $mappedDocId = mysqli_fetch_assoc($docRes)['doc_id'] ?? '';

    if ($mappedDocId) {
        $where .= " AND (
            ao.doctor_name = '$mappedDocId'
            OR ao.doctor_name IN (
                SELECT r.doc_id 
                FROM receptionnist r 
                WHERE r.security_id = '" . mysqli_real_escape_string($conn, $SessionUserId) . "'
            )
        )";
    }
    // else → normal user sees nothing, no changes
}

// ---------- Main query ----------
$sql = "
SELECT 
    inv.invoice_id,
    inv.patient_id,
    inv.appointment_id,
    inv.category_type,
    inv.amount,
    inv.concession_value,
    inv.net_amount,
    inv.created_at,
    pr.test_id,
    ao.patient_name,
    ao.doctor_name
FROM invoice inv
LEFT JOIN appointment_online ao ON ao.appoint_register_id = inv.appointment_id
LEFT JOIN prescripition pr ON inv.appointment_id = pr.appoint_register_id
$where
ORDER BY inv.created_at DESC
";

$result = mysqli_query($conn, $sql);

// ---------- Prepare output ----------
$data = [];
$grossRevenue = 0;
$totalDiscount = 0;
$totalTax = 0;
$netRevenue = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            "date"         => date("Y-m-d", strtotime($row['created_at'])),
            "patient_id"   => $row['patient_id'],
            "patient_name" => $row['patient_name'] ?? "Unknown",
            "total"        => number_format($row['net_amount'], 2)
        ];

        // --- Keep original calculations as-is ---
        $grossRevenue  += (float)$row['amount'];
        $totalDiscount += (float)$row['amount'] - (float)$row['net_amount'];
        $totalTax      += 0; // leave as original if calculated elsewhere
        $netRevenue    += (float)$row['net_amount'];
    }
}

// ---------- Return JSON ----------
echo json_encode([
    "data" => $data,
    "totals" => [
        "gross_revenue"  => number_format($grossRevenue, 2),
        "total_discount" => number_format($totalDiscount, 2),
        "total_tax"      => number_format($totalTax, 2),
        "net_revenue"    => number_format($netRevenue, 2)
    ]
]);
exit;
