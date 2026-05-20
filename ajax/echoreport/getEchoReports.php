<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
$orgId         = $_POST['org_id'] ?? $SessionOrgId;

if ($SessionUserId == "1") {
    $qry = mysqli_query($conn, "SELECT e.*, o.organization_name
        FROM echo_reports e
        LEFT JOIN organization o ON e.org_id = o.org_id
        WHERE e.status='1'
        ORDER BY e.echo_report_id DESC") or die(mysqli_error($conn));
} else {
    $qry = mysqli_query($conn, "SELECT e.*
        FROM echo_reports e
        WHERE e.status='1' AND e.org_id='$orgId'
        ORDER BY e.echo_report_id DESC") or die(mysqli_error($conn));
}

$rows = [];
while ($row = mysqli_fetch_assoc($qry)) {
    $rows[] = $row;
}
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover" id="echoReportHistoryTable" style="width:100%;">
        <thead class="text-center">
            <tr>
                <th>S.No</th>
                <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
                <th>Patient Name</th>
                <th>Age/Sex</th>
                <th>Date</th>
                <th>Ref By</th>
                <th>EF%</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if (empty($rows)): ?>
                <tr><td colspan="<?= $SessionUserId == "1" ? 8 : 7 ?>" class="text-muted">No echo reports found.</td></tr>
            <?php else:
                $i = 1;
                foreach ($rows as $r):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <?php if ($SessionUserId == "1"): ?><td><?= htmlspecialchars($r['organization_name'] ?? '') ?></td><?php endif; ?>
                    <td><?= htmlspecialchars($r['patient_name']) ?></td>
                    <td><?= htmlspecialchars($r['age'] . '/' . strtoupper(substr($r['gender'] ?? '', 0, 1))) ?></td>
                    <td><?= htmlspecialchars(!empty($r['report_date']) ? date('d/m/Y', strtotime($r['report_date'])) : '') ?></td>
                    <td><?= htmlspecialchars($r['ref_by'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['ef'] ?? '-') ?>%</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning edit-echo-btn"
                            data-id="<?= (int)$r['echo_report_id'] ?>">Edit</button>
                        <button type="button" class="btn btn-sm btn-info view-echo-btn"
                            onclick="viewEchoReport(<?= (int)$r['echo_report_id'] ?>)">View</button>
                        <button type="button" class="btn btn-sm btn-danger delete-echo-btn"
                            data-id="<?= (int)$r['echo_report_id'] ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
