<?php
require_once('../../config/functions.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$date = $_POST["appoint_date"];
$security_id = $SessionUserId;
if (!empty($_POST['security_id'])) {
    $security_id = intval($_POST['security_id']);
}

$adminQry = ($SessionUserId != "1") ? " AND a.org_id='$SessionOrgId'" : "";

$FinalData = [];

// Main query to get appointments
$qryOnline = mysqli_query($conn, "
    SELECT a.*
    FROM appointment_online AS a
    LEFT JOIN doctors AS d
        ON a.doctor_name = d.doc_id
    WHERE a.appoint_status = '1'
      AND a.appoint_date = '$date'
      AND d.security_id = '$security_id'
      $adminQry
") or die(mysqli_error($conn));

// Fetch results
while ($resOnline = mysqli_fetch_object($qryOnline)) {
    $FinalData[] = $resOnline;
}

// Sort by visitor_status
$statusOrder = [
    1 => 0, // Pending first
    2 => 1, // Active second
    0 => 2, // Completed third
    3 => 3  // Lapsed last
];

usort($FinalData, function($a, $b) use ($statusOrder) {
    return $statusOrder[$a->visitor_status] - $statusOrder[$b->visitor_status];
});

// Check if any data
if (count($FinalData) == 0) {
    echo '<tr><td colspan="4" class="text-center">No Appointments found</td></tr>';
    exit;
}

// Loop through appointments
foreach ($FinalData as $resappointment) {
    $appointment_name = $resappointment->patient_name;
    $appintunicid = $resappointment->appoint_unicode;
    $visitor_status = $resappointment->visitor_status;
    $appointRegisterId = $resappointment->appoint_register_id;
    $orgid = $resappointment->org_id;
    $appointid = $resappointment->appoint_id;

    // Get doctor info
    $getdoc = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$resappointment->doctor_name' AND org_id='$SessionOrgId'");
    $resdoc1 = mysqli_fetch_object($getdoc);
    $doctor_name = $resdoc1 ? $resdoc1->doctor_name : "N/A";

    // Visitor status colors
    $bgColor = "";
    $textColor = "white";

    switch ($visitor_status) {
        case 1:
            $bgColor = "orange";
            $visitor_status = "Pending";
            break;
        case 2:
            $bgColor = "#6777EF";
            $visitor_status = "Active";
            break;
        case 0:
            $bgColor = "green";
            $visitor_status = "Completed";
            break;
        case 3:
            $bgColor = "red";
            $visitor_status = "Lapsed";
            break;
    }

    $style = "background-color: $bgColor; color: $textColor;";

    $invoicePayment = $resappointment->invoice_payment ?? '0';
    if ($invoicePayment === '1') {
        $payBadge = '<span style="background:#198754;color:#fff;padding:3px 10px;border-radius:30px;font-size:12px;font-weight:600;">Paid</span>';
    } else {
        $payBadge = '<span style="background:#fd7e14;color:#fff;padding:3px 10px;border-radius:30px;font-size:12px;font-weight:600;">Pending</span>';
    }
    ?>

    <tr class="clickable-row" data-appoint-register-id="<?= $appointRegisterId ?>" data-org-id="<?= $orgid ?>" data-appoint-id="<?= $appointid ?>">
        <td>
            <h6 class="mb-0 font-13"><?= $appointment_name ?></h6>
            <p class="m-0 font-12"> Assigned to <span class="col-green font-weight-bold"><?= $doctor_name ?></span> </p>
        </td>
        <td><?= $appintunicid ?></td>
        <td class="align-middle" style="padding-top: 15px">
            <p class="text-center" style="<?= $style ?>;border-radius: 30px;cursor: pointer"><?= $visitor_status ?></p>
        </td>
        <td class="align-middle text-center"><?= $payBadge ?></td>
    </tr>

<?php
}
?>
