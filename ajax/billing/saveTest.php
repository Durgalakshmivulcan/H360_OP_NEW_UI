<?php
require_once("../../config/functions.php");
// session_start();

$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId  = $_SESSION['org_id'];

// FIX_B_1820 (scope 2 RBAC): per-action gate.
requireCan('add', 'bill.php', 'ajax');


$action = $_POST['action'] ?? '';

if ($action === '2') {
    $patient_id         = $_POST['patient_id'] ?? '';
    $appointment_id     = $_POST['appointment_id'] ?? '';
    $tests_json         = $_POST['tests'] ?? '';
    $payment_method     = $_POST['payment_method'] ?? '';
    $transaction_number = mysqli_real_escape_string($conn, $_POST['transaction_number'] ?? '');
    $transaction_amount = isset($_POST['transaction_amount']) && $_POST['transaction_amount'] !== '' ? floatval($_POST['transaction_amount']) : null;
    $cash_amount        = isset($_POST['cash_amount']) && $_POST['cash_amount'] !== '' ? floatval($_POST['cash_amount']) : null;

    $org_id     = $_SESSION['org_id'];
    $created_by = $_SESSION['security_id'];

    $getappoint = mysqli_query($conn, "
      SELECT * 
      FROM appointment_online 
      WHERE appoint_status='1' 
        AND appoint_register_id='$appointment_id'
        AND org_id='$org_id'
    ");

    $resapport = mysqli_fetch_object($getappoint);

    $tests_array = json_decode($_POST['tests'], true);

    if (!empty($tests_array)) {
        $doctor_total   = floatval($_POST['doctor_total'] ?? 0);
        $standard_total = floatval($_POST['standard_total'] ?? 0);
        $discount       = $standard_total - $doctor_total;

        $test_details = mysqli_real_escape_string($conn, json_encode($tests_array));
        $bill_type     = "Test";
        $category_type = "Test fee";

        // Ensure transaction columns exist in patient_test_billing
        $checkTxnCol = mysqli_query($conn, "SHOW COLUMNS FROM patient_test_billing LIKE 'transaction_number'");
        if (mysqli_num_rows($checkTxnCol) == 0) {
            mysqli_query($conn, "ALTER TABLE patient_test_billing ADD COLUMN transaction_number VARCHAR(100) DEFAULT NULL, ADD COLUMN transaction_amount DECIMAL(10,2) DEFAULT NULL, ADD COLUMN cash_amount DECIMAL(10,2) DEFAULT NULL");
        }

        $txnAmountSql  = $transaction_amount !== null ? "'$transaction_amount'" : "NULL";
        $cashAmountSql = $cash_amount !== null ? "'$cash_amount'" : "NULL";

        $insert_query = "
            INSERT INTO patient_test_billing
                (patient_id, appointment_id, test_details, total_amount, discount, net_amount, payment_method, transaction_number, transaction_amount, cash_amount, status, org_id, created_by, created_at)
            VALUES
                ('$patient_id', '$appointment_id', '$test_details', '$standard_total', '$discount', '$doctor_total', '$payment_method', '$transaction_number', $txnAmountSql, $cashAmountSql, '1', '$org_id', '$created_by', NOW())
        ";

        if (!mysqli_query($conn, $insert_query)) {
            http_response_code(500);
            echo "Error: " . mysqli_error($conn);
            exit;
        }

        if (strcasecmp($payment_method, 'Both (Cash + UPI)') === 0) {
            // Split into two invoice rows so Cash and UPI are recorded separately.
            $splitRows = [
                ['Cash', $cash_amount ?? 0],
                ['UPI',  $transaction_amount ?? 0],
            ];
            foreach ($splitRows as [$rowMethod, $rowAmt]) {
                $rowInsert = "
                    INSERT INTO invoice
                        (patient_id, appointment_id, bill_type, category_type, amount, concession_value, net_amount, payment_method, status, org_id, created_by, created_at)
                    VALUES
                        ('$patient_id', '$appointment_id', '$bill_type', '$category_type', '$rowAmt', '0', '$rowAmt', '$rowMethod', '1', '$org_id', '$created_by', NOW())
                ";
                if (!mysqli_query($conn, $rowInsert)) {
                    http_response_code(500);
                    echo "Error in invoice: " . mysqli_error($conn);
                    exit;
                }
            }
        } else {
            $invoiceEnum   = ['Cash', 'Card', 'UPI', 'Cheque', 'Online', 'Other'];
            $invoiceMethod = in_array($payment_method, $invoiceEnum, true) ? $payment_method : 'Other';
            $insert_invoice = "
                INSERT INTO invoice
                    (patient_id, appointment_id, bill_type, category_type, amount, concession_value, net_amount, payment_method, status, org_id, created_by, created_at)
                VALUES
                    ('$patient_id', '$appointment_id', '$bill_type', '$category_type', '$standard_total', '$discount', '$doctor_total', '$invoiceMethod', '1', '$org_id', '$created_by', NOW())
            ";
            if (!mysqli_query($conn, $insert_invoice)) {
                http_response_code(500);
                echo "Error in invoice: " . mysqli_error($conn);
                exit;
            }
        }

        $newId = mysqli_insert_id($conn);

        $after = [
            'test_details'   => $tests_array,
            'total_amount'   => $standard_total,
            'discount'       => $discount,
            'net_amount'     => $doctor_total,
            'payment_method' => $payment_method,
        ];

        audit_log($conn, "TestBill", "create", "patienttestbilling", $newId, null, $after);

    } else {
        echo "No tests selected!";
        exit;
    }
} else {
    $PatientID     = $_POST['PatientID'] ?? '';
    $applicationID = $_POST['applicationID'] ?? '';
    $orgId         = ($SessionUserId == "1" && $SessionRoleId == "1" && !empty($_POST['orgId']))
                        ? $_POST['orgId']
                        : $SessionOrgId;

    $getappoint = mysqli_query($conn, "
    SELECT appoint_id , org_id
    FROM appointment_online 
    WHERE appoint_status='1' 
      AND appoint_register_id='$applicationID'
      AND org_id='$orgId'
    ");
    $resapport = mysqli_fetch_object($getappoint);


    $billingData = [];

    if (!empty($PatientID)) {   
        $query = "
            SELECT * 
            FROM patient_test_billing 
            WHERE patient_id = '$PatientID' 
              AND appointment_id = '$applicationID' 
              AND org_id = '$orgId'
              AND status = '1'
            ORDER BY test_billing_id DESC
        ";

        $getBilling = mysqli_query($conn, $query) or die(mysqli_error($conn));

        if ($getBilling && mysqli_num_rows($getBilling) > 0) {
            while ($row = mysqli_fetch_assoc($getBilling)) {
                $testDetails = json_decode($row['test_details'], true);

                if (is_array($testDetails)) {
                    if (isset($testDetails[0]) && is_array($testDetails[0])) {
                        $testNames = array_column($testDetails, 'test_name');
                        $row['test_name'] = implode(", ", $testNames);
                    } else {
                        $row['test_name'] = $testDetails['test_name'] ?? '-';
                    }
                } else {
                    $row['test_name'] = '-';
                }

                $billingData[] = $row;
            }
        }

    }
    ?>

    <div class="card-body" id="showInstallmentData">
        <div class="col-12 col-md-12 table-responsive">
            <table class="table" id="tableExport1" style="width:100%;">
                <thead class="text-center">
                    <tr>
                        <th>S.No</th>
                        <?php if ($SessionUserId == "1") { ?>
                            <th>Organization</th>
                        <?php } ?>
                        <th>Application Id</th>
                        <th>Patient Id</th>
                        <th>Test Name</th>
                        <th>Total Amount (Standard)</th>
                        <th>Discount</th>
                        <th>Net Amount (Doctor)</th>
                        <th>Payment Method</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    $i = 1;
                    if (!empty($billingData)) {
                        foreach ($billingData as $row) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <?php if ($SessionUserId == "1") { ?>
                                    <td><?= getUserNameByOrgId($conn, $row['org_id']) ?></td>
                                <?php } ?>
                                <td><?= $row['appointment_id'] ?></td>
                                <td><?= $row['patient_id'] ?></td>
                                <td><?= htmlspecialchars($row['test_name']) ?></td>
                                <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                                <td>₹<?= number_format($row['discount'], 2) ?></td>
                                <td>₹<?= number_format($row['net_amount'], 2) ?></td>
                                <td><?= $row['payment_method'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info viewTestBilling"
                                            data-id="<?= $row['test_billing_id'] ?>"
                                            data-appoint-id="<?= $resapport->appoint_id ?? '' ?>"
                                            data-org-id="<?= $resapport->org_id ?? '' ?>">
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        $colspan = ($SessionUserId == "1") ? 11 : 10;
                        echo "<tr><td colspan='{$colspan}' class='text-center text-muted'>No Data Available</td></tr>";
                    }
                    ?> 
                </tbody>
            </table>
        </div>
    </div>

<?php } ?>

<script>

$(document).on("click", ".viewTestBilling", function() {
    var id = $(this).data("id");
    var appoint_register_id = $(this).data("appoint-id");
    var patient_uid = $(this).data("appoint-id");
    var org_id = $(this).data("org-id");

    $.ajax({
        url: "ajax/billing/viewBill.php",
        type: "POST",
        data: { test_billing_id: id
                , appoint_register_id: appoint_register_id
                , patient_uid: patient_uid,
                org_id: org_id
         }, 
        success: function(response) {
            // Remove any previous instance so IDs don't collide.
            $("#reportModal").remove();
            // Append directly to <body> so Bootstrap's position:fixed resolves
            // to the viewport, not the nested AJAX content area.
            $("body").append(response);
            $("#reportModal").modal("show");
            $("#reportModal").on("hidden.bs.modal", function () {
                $(this).remove();
            });
        }
    });
});

</script>