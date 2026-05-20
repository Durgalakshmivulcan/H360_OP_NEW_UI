<?php
require_once("ajax/header.php");
$SessionUserId = $_SESSION['security_id'];
$SessionOrgId  = $_SESSION['org_id'];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
.ref-stat { border-radius: 10px; padding: 20px 18px; color: #fff; text-align: center; }
.ref-stat h3 { font-size: 2rem; font-weight: 700; margin: 0; }
.ref-stat p  { margin: 4px 0 0; font-size: 12px; opacity: .88; }
.badge-int  { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; background:#198754; color:#fff; }
.badge-ext  { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; background:#0d6efd; color:#fff; }
.badge-oth  { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; background:#6c757d; color:#fff; }
.filter-card { border-radius:8px; box-shadow:0 1px 6px rgba(0,0,0,.07); }
#chartWrap { min-height:260px; }
</style>

<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style">
            <li class="breadcrumb-item"><h4 class="page-title m-b-0">Referrals</h4></li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active">Referral Tracking</li>
        </ul>

        <div class="section-body">

            <!-- Stats -->
            <div class="row mb-3">
                <div class="col-6 col-md-3 mb-2"><div class="ref-stat" style="background:#8e44ad;"><h3 id="stTotal">0</h3><p>Total Referred Patients</p></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="ref-stat" style="background:#198754;"><h3 id="stInternal">0</h3><p>Internal Referrals</p></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="ref-stat" style="background:#0d6efd;"><h3 id="stExternal">0</h3><p>External Referrals</p></div></div>
                <div class="col-6 col-md-3 mb-2"><div class="ref-stat" style="background:#e67e22;"><h3 id="stDoctors">0</h3><p>Referring Doctors</p></div></div>
            </div>

            <!-- Filters -->
            <div class="card filter-card mb-3">
                <div class="card-body py-3">
                    <div class="row align-items-end g-2">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label mb-1">From Date</label>
                            <input type="date" class="form-control form-control-sm" id="filterFrom">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label mb-1">To Date</label>
                            <input type="date" class="form-control form-control-sm" id="filterTo">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label mb-1">Referral Type</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><div class="input-group-text"><i class="bi bi-arrow-left-right"></i></div></div>
                                <select class="form-control form-select" id="filterType">
                                    <option value="">All Types</option>
                                    <option value="Internal">Internal</option>
                                    <option value="External">External</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <button class="btn btn-primary btn-sm w-100" id="btnSearch">
                                <i class="bi bi-search"></i> Find
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="card filter-card">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-3 pt-2" id="refTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-summary" href="#" onclick="showTab('summary'); return false;">
                                <i class="bi bi-bar-chart-fill me-1"></i>Summary
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-details" href="#" onclick="showTab('details'); return false;">
                                <i class="bi bi-table me-1"></i>Detailed Records
                            </a>
                        </li>
                    </ul>

                    <!-- Summary Tab -->
                    <div id="pane-summary" class="p-3">
                        <div id="chartWrap" style="min-height:300px;"></div>
                    </div>

                    <!-- Details Tab -->
                    <div id="pane-details" class="p-3" style="display:none;">
                        <div class="table-responsive">
                            <table id="detailsTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Patient Name</th>
                                        <th>UMR No</th>
                                        <th>Mobile</th>
                                        <th>Age / Gender</th>
                                        <th>Doctor</th>
                                        <th>Referred By</th>
                                        <th>Hospital / Clinic</th>
                                        <th>Type</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
var refChart = null;
var detailsData = [];

function showTab(tab) {
    $('#pane-summary').toggle(tab === 'summary');
    $('#pane-details').toggle(tab === 'details');
    $('#tab-summary').toggleClass('active', tab === 'summary');
    $('#tab-details').toggleClass('active', tab === 'details');
}

function escHtml(s) {
    return $('<div>').text(String(s || '')).html();
}

function typeBadge(t) {
    if (!t) return '<span class="badge-oth">--</span>';
    if (t === 'Internal') return '<span class="badge-int">Internal</span>';
    if (t === 'External') return '<span class="badge-ext">External</span>';
    return '<span class="badge-oth">' + escHtml(t) + '</span>';
}

function getFilters() {
    return { from_date: $('#filterFrom').val(), to_date: $('#filterTo').val(), referral_type: $('#filterType').val() };
}

function loadSummary() {
    $.post('ajax/referrals/getReferralStats.php', getFilters(), function(data) {
        var total = 0, internal = 0, external = 0, doctors = new Set();
        var labels = [], vals = [], colors = [];

        if (data && data.length) {
            $.each(data, function(i, r) {
                var n = parseInt(r.total_patients) || 0;
                total += n;
                if ((r.referral_type || '').toLowerCase() === 'internal') internal += n;
                if ((r.referral_type || '').toLowerCase() === 'external') external += n;
                if (r.referred_by) doctors.add(r.referred_by);
                labels.push(r.referred_by || '--');
                vals.push(n);
                colors.push(r.referral_type === 'Internal' ? '#198754' : r.referral_type === 'External' ? '#0d6efd' : '#6c757d');
            });
        }

        $('#stTotal').text(total);
        $('#stInternal').text(internal);
        $('#stExternal').text(external);
        $('#stDoctors').text(doctors.size);
        renderChart(labels, vals, colors);
    }, 'json').fail(function(xhr) {
        console.error('Stats error:', xhr.responseText);
    });
}

function loadDetails() {
    $.post('ajax/referrals/getReferralDetails.php', getFilters(), function(data) {
        detailsData = data || [];

        if ($.fn.DataTable.isDataTable('#detailsTable')) {
            $('#detailsTable').DataTable().destroy();
        }

        $('#detailsTable').DataTable({
            data: detailsData,
            columns: [
                { data: null, render: function(d, t, r, m) { return m.row + 1; } },
                { data: 'appoint_date' },
                { data: 'patient_name' },
                { data: 'appoint_unicode' },
                { data: 'mobile_number' },
                { data: null, render: function(d) { return escHtml((d.age || '-') + ' / ' + (d.gender || '-')); } },
                { data: 'doctor_name', defaultContent: '--' },
                { data: 'referred_by', render: function(d) { return '<strong>' + escHtml(d) + '</strong>'; } },
                { data: 'referral_hospital', defaultContent: '--' },
                { data: 'referral_type', render: function(d) { return typeBadge(d); } },
                { data: 'referral_notes', defaultContent: '--', render: function(d) { return '<span style="font-size:11px;color:#555;">' + escHtml(d || '--') + '</span>'; } }
            ],
            dom: 'lBrftip',
            buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
            pageLength: 10,
            order: [[1, 'desc']],
            language: { emptyTable: 'No referral records found for the selected period.' }
        });
    }, 'json').fail(function(xhr) {
        console.error('Details error:', xhr.responseText);
    });
}

function renderChart(labels, vals, colors) {
    $('#chartWrap').empty();
    if (!labels.length) return;
    if (typeof ApexCharts === 'undefined') return;
    refChart = new ApexCharts(document.getElementById('chartWrap'), {
        chart: { type: 'bar', height: 260, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, horizontal: false } },
        dataLabels: { enabled: true },
        series: [{ name: 'Patients', data: vals }],
        xaxis: { categories: labels },
        colors: colors,
        legend: { show: false }
    });
    refChart.render();
}

$(function () {
    var today = new Date().toISOString().split('T')[0];
    var m1ago = new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().split('T')[0];
    $('#filterFrom').val(m1ago);
    $('#filterTo').val(today);

    $('#btnSearch').on('click', function () { loadSummary(); loadDetails(); });

    loadSummary();
    loadDetails();
});
</script>
