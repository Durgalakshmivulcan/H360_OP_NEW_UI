<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$PatientID      = $_GET['PatientID'] ?? '';
$applicationID  = $_GET['applicationID'] ?? '';
$orgId          = ($SessionUserId == "1" && $SessionRoleId == "1" && !empty($_GET['orgId']))
                    ? $_GET['orgId']
                    : $SessionOrgId;

$filter = "b.org_id = '$orgId' AND b.patient_id = '$PatientID'";

if (!empty($applicationID)) {
    $filter .= " AND b.application_id = '$applicationID'";
}

$PatientID = $_GET['PatientID'] ?? ''; 
$filter = "b.patient_id = '$PatientID'";

$getAdminDepartment = mysqli_query($conn, "
    SELECT 
        b.billing_id,
        b.patient_id,
        a.full_name,                          
        b.application_id,
        b.bill_number,
        b.bill_category,
        b.net_amount,
        IFNULL(p.paid_amount,0) AS paid_amount,                            
        (b.net_amount - IFNULL(p.paid_amount,0)) AS balance_amount, 
        b.payment_status,
        b.org_id,
        (SELECT COUNT(*) 
         FROM inp_billing 
         WHERE patient_id = b.patient_id 
           AND application_id = b.application_id) AS total_bills
    FROM inp_billing b
    LEFT JOIN inp_payment p 
           ON p.bill_id = b.bill_number
    LEFT JOIN inp_patient_registration a 
           ON a.appointment_id = b.application_id
    WHERE b.patient_id = '$PatientID' 
      AND b.application_id = '$applicationID'
      AND b.org_id = '$orgId'
    ORDER BY b.billing_id DESC
") or die(mysqli_error($conn));

?> 

<div class="card-body" id="showInstallmentData">
    <div class="col-12 col-md-12 table-responsive">
        <table id="billingTable" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>S.No</th>
            <?php if ($SessionUserId == "1") { ?><th>Organization</th><?php } ?>
            <th>Application ID</th>
            <th>Patient ID</th>
            <th>Full Name</th>
            <th>Bill Number</th>
            <th>Category</th>
            <th>Net Amount</th>
            <th>Paid Amount</th>
            <th>Balance Amount</th>
            <th>Payment Status</th>
        </tr>
    </thead>
    <tbody>
                <?php
                    $i = 1;
                    $pendingCount = 0;
                    $pendingDisplayed = false;

                    if (!empty($PatientID) && mysqli_num_rows($getAdminDepartment) > 0) {
                        mysqli_data_seek($getAdminDepartment, 0);
                        while ($tmp = mysqli_fetch_object($getAdminDepartment)) {
                            if ($tmp->payment_status == "pending") {
                                $pendingCount++;
                            }
                        }
                        mysqli_data_seek($getAdminDepartment, 0);

                        while ($row = mysqli_fetch_object($getAdminDepartment)) {
                            ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <?php if ($SessionUserId == "1") { ?>
                                    <td><?= getUserNameByOrgId($conn, $row->org_id) ?></td>
                                <?php } ?>
                                <td><?= $row->application_id ?></td>
                                <td><?= $row->patient_id ?></td>
                                <td><?= $row->full_name ?></td>
                                <td><?= $row->bill_number ?></td>
                                <td><?= $row->bill_category ?></td>
                                <td>₹<?= number_format($row->net_amount, 2) ?></td>
                                <td>₹<?= number_format($row->paid_amount, 2) ?></td>
                                <td>₹<?= number_format($row->balance_amount, 2) ?></td>

                                <?php if ($row->payment_status == "paid") { ?>
                                    <td><span class="badge bg-primary">Paid</span></td>
                                <?php } elseif ($row->payment_status == "pending") { ?>
                                    <?php if (!$pendingDisplayed) { ?>
                                        <td rowspan="<?= $pendingCount ?>" class="align-middle">
                                            <span class="badge bg-warning">Pending</span>
                                        </td>
                                        <?php $pendingDisplayed = true; ?>
                                    <?php } ?>
                                <?php } ?>
                            </tr>
                            <?php
                        }
                    } else {
                        $colspan = ($SessionUserId == "1") ? 10 : 9;
                        echo "<tr><td colspan='{$colspan}' class='text-center text-muted'>No Data Available</td></tr>";
                    }
                ?> 
            </tbody>
        </table>
    </div>
</div> 