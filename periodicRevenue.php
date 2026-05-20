<?php
require_once("ajax/header.php");
?>

<style>
    .btn-group, .btn-group-vertical {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 15px;
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Revenue Reports</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active">Reports</li>
            <li class="breadcrumb-item active">Periodic Revenue Report</li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Revenue Filter</h4>
                </div>

                <form method="POST" id="RevenueForm" action="" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <?php 
                                $SessionUserId = $_SESSION['security_id'] ?? '';
                                $SessionRoleId = $_SESSION['role_id'] ?? '';
                                $SessionOrgId = $_SESSION['org_id'] ?? '';

                                if($SessionUserId == "1" && $SessionRoleId=="1"){
                            ?>
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="organizations">Organization </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-buildings-fill"></i></span>
                                    <select class="form-control form-select" name="organizations" id="organizations">
                                        <option value="">Select Organization</option>
                                        <?php
                                        $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                        while($ResOrganization = mysqli_fetch_object($GetOrganization)){
                                        ?>
                                            <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="reportdate">Date</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="reportdate" id="reportdate" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Revenue Report Results</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive" id="showReportData">
                        <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
$(document).ready(function () {

    function loadReport() {
        let reportDate = $('#reportdate').val();
        const org_id = $('#organizations').length ? $('#organizations').val() : '<?=$SessionOrgId ?>';

        $.ajax({
            url: 'ajax/billingReports/getrevenuereportsdata.php',
            type: 'POST',
            data: { report_date: reportDate, org_id: org_id },
            success: function (data) {
                if (data) {
                    $("#showReportData").html(data);
                    $("#tableExport1").DataTable({
                        retrieve: true,
                        destroy: true,
                        dom: 'lBrftip',
                        buttons: ['copy', 'excel', 'csv', 'pdf', 'print']
                    });
                }
            }
        });
    }

    // Load on page load
    loadReport();

    // Reload when filter changes
    $('#reportdate, #organizations').on('change', loadReport);
});
</script>
