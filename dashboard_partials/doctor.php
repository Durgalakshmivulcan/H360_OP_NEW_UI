<?php
/**
 * dashboard_partials/doctor.php — B-2020
 * --------------------------------------------------------------------------
 * Enterprise-grade interactive Doctor dashboard partial.
 * Rendered when role_id=2 (Doctor) hits dashboard.php. Auto-scopes to the
 * logged-in doctor's own appointments / Rx / patients via currentDoctorScopeSql().
 * Aesthetic: Sovereign Institutional (navy / cream / gold). Scoped CSS appended
 * at the end of assets/h360-ui/h360-ui.css under body[data-role-dashboard="doctor"].
 * --------------------------------------------------------------------------
 * Constitutional:
 *   - This partial only emits a self-contained <section> + <script>. No global
 *     navbar / sidebar / header / footer changes.
 *   - All AJAX endpoints under ajax/dashbord/doctor_*.php gate with
 *     requireCan('view','dashboard.php','ajax') and currentDoctorScopeSql().
 *   - Respects prefers-reduced-motion (CSS @media + JS guard).
 */

if (!isset($_SESSION)) { @session_start(); }
if (!function_exists('currentDoctorScopeSql')) {
    require_once __DIR__ . '/../config/functions.php';
}

$dashSessionUserId = (int) ($_SESSION['security_id'] ?? 0);
$dashSessionOrgId  = (int) ($_SESSION['org_id'] ?? 0);

// Resolve doctor row + specialty
$dashDoc = ['doc_id' => 0, 'doctor_name' => '', 'specialty' => '', 'doc_img' => ''];
if ($dashSessionUserId > 0 && isset($conn)) {
    // Specialty: `doctors.doctor_specialization` stores the literal name on this
    // deployment (e.g. "CARDIOLOGIST"); fall back to a JOIN against specialtis
    // in case other deployments store the FK id there.
    $qDoc = mysqli_query($conn,
        "SELECT d.doc_id, d.doctor_name, d.doc_img,
                d.doctor_specialization AS raw_spec,
                COALESCE(s.specialtisname,'') AS named_spec
           FROM doctors d
           LEFT JOIN specialtis s ON s.specialtis_id = d.doctor_specialization
          WHERE d.security_id='$dashSessionUserId' AND d.status='1' LIMIT 1");
    if ($qDoc && ($r = mysqli_fetch_assoc($qDoc))) {
        $resolvedSpec = $r['named_spec'] !== '' ? $r['named_spec'] : $r['raw_spec'];
        $dashDoc = [
            'doc_id'      => (int) $r['doc_id'],
            'doctor_name' => $r['doctor_name'],
            'doc_img'     => $r['doc_img'],
            'specialty'   => strtoupper(trim((string) $resolvedSpec)),
        ];
    }
}

$isGynaec = (strpos($dashDoc['specialty'], 'GYNAEC') !== false || strpos($dashDoc['specialty'], 'OBSTET') !== false);
$rxPage   = $isGynaec ? 'gynaec_prescription.php' : 'prescription.php';

// ---- Hero KPIs (server-side counts; counters animate from 0 in JS) ----------
$docScope = currentDoctorScopeSql('a.doctor_name');

$kpiToday = (int) (mysqli_fetch_array(mysqli_query($conn,
    "SELECT COUNT(*) FROM appointment_online a
     WHERE a.appoint_status='1' AND a.appoint_date=CURDATE()
       AND a.org_id='$dashSessionOrgId' $docScope"))[0] ?? 0);

$kpiFollowups = 0;
$docScopeAo = currentDoctorScopeSql('ao.doctor_name');
$today      = date('Y-m-d');
$qFu = mysqli_query($conn,
    "SELECT COUNT(*) FROM prescripition p
       LEFT JOIN appointment_online ao ON ao.appoint_register_id=p.appoint_register_id
      WHERE p.status='1' AND p.org_id='$dashSessionOrgId'
        AND p.reviewafterdate IS NOT NULL AND p.reviewafterdate<>''
        AND STR_TO_DATE(p.reviewafterdate,'%Y-%m-%d')<='$today' $docScopeAo");
if ($qFu) $kpiFollowups += (int) (mysqli_fetch_array($qFu)[0] ?? 0);
$qFuG = mysqli_query($conn,
    "SELECT COUNT(*) FROM gynaec_prescriptions g
       LEFT JOIN appointment_online ao ON ao.appoint_register_id=g.appointment_id
      WHERE g.status='1' AND g.org_id='$dashSessionOrgId'
        AND g.reviewafterdate IS NOT NULL AND g.reviewafterdate<>''
        AND STR_TO_DATE(g.reviewafterdate,'%Y-%m-%d')<='$today' $docScopeAo");
if ($qFuG) $kpiFollowups += (int) (mysqli_fetch_array($qFuG)[0] ?? 0);

// Active slots this week (Mon..Sun) — count rows in doctorstimeslots-style tables.
// Use a lightweight COUNT on doctors_time_slots if present; otherwise fall back to 0.
$kpiSlots = 0;
$kpiSlotUtil = 0;
$tblExists = mysqli_query($conn, "SHOW TABLES LIKE 'doctors_time_slots'");
if ($tblExists && mysqli_num_rows($tblExists) > 0) {
    $docId = (int) $dashDoc['doc_id'];
    $qSl = mysqli_query($conn,
        "SELECT COUNT(*) FROM doctors_time_slots
          WHERE status='1' AND doc_id='$docId'
            AND slot_date BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                              AND DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY)");
    if ($qSl) $kpiSlots = (int) (mysqli_fetch_array($qSl)[0] ?? 0);
    if ($kpiSlots > 0) {
        $qSlBooked = mysqli_query($conn,
            "SELECT COUNT(*) FROM appointment_online a
              WHERE a.appoint_status='1' AND a.org_id='$dashSessionOrgId'
                AND a.appoint_date BETWEEN DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                                       AND DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 6 DAY)
                $docScope");
        $booked = $qSlBooked ? (int) (mysqli_fetch_array($qSlBooked)[0] ?? 0) : 0;
        $kpiSlotUtil = (int) min(100, round(($booked / max(1, $kpiSlots)) * 100));
    }
}

// Next patient (today, status not done/cancelled, earliest start_time)
$nextPatient = null;
$qNP = mysqli_query($conn,
    "SELECT a.appoint_register_id, a.appoint_id, a.patient_name, a.age, a.start_time, a.visitor_status
       FROM appointment_online a
      WHERE a.appoint_status='1' AND a.appoint_date=CURDATE()
        AND a.visitor_status IN ('1','2') AND a.org_id='$dashSessionOrgId' $docScope
      ORDER BY a.start_time ASC, a.appoint_id ASC LIMIT 1");
if ($qNP && ($r = mysqli_fetch_assoc($qNP))) $nextPatient = $r;

// 7-day sparkline for KPI #1 (server-rendered tiny inline series)
$spark = array_fill(0, 7, 0);
$qSp = mysqli_query($conn,
    "SELECT DATE(a.appoint_date) d, COUNT(*) c
       FROM appointment_online a
      WHERE a.appoint_status='1' AND a.org_id='$dashSessionOrgId'
        AND a.appoint_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
        $docScope GROUP BY DATE(a.appoint_date)");
$spm = [];
while ($qSp && ($r = mysqli_fetch_assoc($qSp))) $spm[$r['d']] = (int) $r['c'];
for ($i = 0; $i < 7; $i++) {
    $d = date('Y-m-d', strtotime('-' . (6 - $i) . ' day'));
    $spark[$i] = $spm[$d] ?? 0;
}

// Helper for safe HTML
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

// Pretty time (HH:MM AM/PM) from raw varchar start_time
$prettyTime = function ($t) {
    $t = trim((string) $t);
    if ($t === '') return '—';
    $ts = strtotime($t);
    if ($ts === false) return $t;
    return date('g:i A', $ts);
};
?>
<script>document.body && document.body.setAttribute('data-role-dashboard','doctor');</script>

<section class="h360-doc-dashboard"
         data-rx-page="<?= $h($rxPage) ?>"
         data-doc-id="<?= (int) $dashDoc['doc_id'] ?>"
         data-specialty="<?= $h($dashDoc['specialty']) ?>"
         aria-label="Doctor Dashboard">

    <!-- Chrome bar: title + welcome -->
    <header class="hdoc-chrome">
        <div class="hdoc-chrome__left">
            <span class="hdoc-dot" aria-hidden="true"></span>
            <span class="hdoc-mono">DOCTOR · CONSOLE</span>
            <span class="hdoc-sep">/</span>
            <span class="hdoc-title">Welcome, <?= $h(formatDoctorName($dashDoc['doctor_name'] ?: '—')) ?></span><?php /* FIX_B_2352 */ ?>
        </div>
        <div class="hdoc-chrome__right">
            <span class="hdoc-mono hdoc-faint">SPECIALTY</span>
            <span class="hdoc-tag"><?= $h($dashDoc['specialty'] ?: 'GENERAL') ?></span>
            <span class="hdoc-mono hdoc-faint"><?= $h(date('D · d M Y')) ?></span>
        </div>
    </header>

    <!-- HERO KPIs -->
    <div class="hdoc-kpis" role="list">
        <a class="hdoc-kpi hdoc-kpi--primary" role="listitem"
           href="AppointmentOnline.php" data-counter="<?= (int) $kpiToday ?>">
            <div class="hdoc-kpi__top">
                <span class="hdoc-mono">TODAY</span>
                <span class="hdoc-kpi__icon" aria-hidden="true"><i class="fas fa-calendar-day"></i></span>
            </div>
            <div class="hdoc-kpi__num" data-num="<?= (int) $kpiToday ?>">0</div>
            <div class="hdoc-kpi__label">Today's Appointments</div>
            <svg class="hdoc-spark" viewBox="0 0 100 28" preserveAspectRatio="none" aria-hidden="true">
                <?php
                $max = max(1, max($spark));
                $pts = [];
                foreach ($spark as $i => $v) {
                    $x = ($i / 6) * 100;
                    $y = 26 - ($v / $max) * 22;
                    $pts[] = round($x, 2) . ',' . round($y, 2);
                }
                ?>
                <polyline points="<?= implode(' ', $pts) ?>" fill="none" stroke="currentColor" stroke-width="1.5" />
            </svg>
            <span class="hdoc-kpi__cta">Open queue <i class="fas fa-arrow-right"></i></span>
        </a>

        <a class="hdoc-kpi hdoc-kpi--accent <?= $nextPatient ? 'is-pulsing' : '' ?>" role="listitem"
           <?php /* FIX_B_2211: when no next patient, route to today's queue rather than an empty Rx page */ ?>
           href="<?= $nextPatient ? $h($rxPage . '?appointRegisterId=' . urlencode($nextPatient['appoint_register_id']) . '&appoint_id=' . (int) $nextPatient['appoint_id']) : 'AppointmentOnline.php' ?>">
            <div class="hdoc-kpi__top">
                <span class="hdoc-mono">NEXT</span>
                <span class="hdoc-kpi__icon" aria-hidden="true"><i class="fas fa-user-md"></i></span>
            </div>
            <?php if ($nextPatient): ?>
                <div class="hdoc-kpi__name"><?= $h($nextPatient['patient_name']) ?></div>
                <div class="hdoc-kpi__sub">
                    <span class="hdoc-pill"><?= $h($nextPatient['age']) ?> yrs</span>
                    <span class="hdoc-pill hdoc-pill--gold"><?= $h($prettyTime($nextPatient['start_time'])) ?></span>
                </div>
                <div class="hdoc-kpi__label">Next Patient in Queue</div>
                <span class="hdoc-kpi__cta">Open Rx <i class="fas fa-arrow-right"></i></span>
            <?php else: ?>
                <div class="hdoc-kpi__name hdoc-empty">Queue clear</div>
                <div class="hdoc-kpi__sub"><span class="hdoc-pill">No waiting patient</span></div>
                <div class="hdoc-kpi__label">Next Patient in Queue</div>
                <?php /* FIX_B_2218: empty-state CTA points to queue, not an empty Rx form */ ?>
                <span class="hdoc-kpi__cta">View queue <i class="fas fa-arrow-right"></i></span>
            <?php endif; ?>
        </a>

        <a class="hdoc-kpi" role="listitem"
           href="prescriptionreports.php?followups=1" data-counter="<?= (int) $kpiFollowups ?>">
            <div class="hdoc-kpi__top">
                <span class="hdoc-mono">FOLLOW-UPS</span>
                <span class="hdoc-kpi__icon" aria-hidden="true"><i class="fas fa-bell"></i></span>
            </div>
            <div class="hdoc-kpi__num" data-num="<?= (int) $kpiFollowups ?>">0</div>
            <div class="hdoc-kpi__label">Due Today / Overdue</div>
            <span class="hdoc-kpi__cta">Open list <i class="fas fa-arrow-right"></i></span>
        </a>

        <a class="hdoc-kpi" role="listitem"
           href="doctorstimeslot.php" data-counter="<?= (int) $kpiSlots ?>">
            <div class="hdoc-kpi__top">
                <span class="hdoc-mono">SLOTS · WEEK</span>
                <span class="hdoc-kpi__icon" aria-hidden="true"><i class="fas fa-clock"></i></span>
            </div>
            <div class="hdoc-kpi__num" data-num="<?= (int) $kpiSlots ?>">0</div>
            <div class="hdoc-kpi__label">Active Slots · <?= (int) $kpiSlotUtil ?>% utilized</div>
            <div class="hdoc-meter" aria-hidden="true">
                <span style="--util: <?= (int) $kpiSlotUtil ?>%"></span>
            </div>
            <span class="hdoc-kpi__cta">Manage slots <i class="fas fa-arrow-right"></i></span>
        </a>
    </div>

    <!-- Quick actions -->
    <nav class="hdoc-quick" aria-label="Quick actions">
        <a class="hdoc-quick__btn" href="AppointmentOnline.php"><i class="fas fa-play"></i> Start Consultation</a>
        <a class="hdoc-quick__btn hdoc-quick__btn--gold" href="<?= $h($rxPage) ?>"><i class="fas fa-prescription"></i> Write Prescription</a>
        <a class="hdoc-quick__btn" href="doctorstimeslot.php"><i class="fas fa-calendar-alt"></i> Manage Slots</a>
        <a class="hdoc-quick__btn" href="patienthistory.php"><i class="fas fa-history"></i> Patient History</a>
        <a class="hdoc-quick__btn" href="prescriptionreports.php"><i class="fas fa-file-medical"></i> View Reports</a>
    </nav>

    <!-- Main grid -->
    <div class="hdoc-grid">
        <!-- Today's queue (wide column) -->
        <article class="hdoc-card hdoc-card--queue" id="hdocQueue">
            <header class="hdoc-card__head">
                <h3>Today's Queue</h3>
                <span class="hdoc-mono hdoc-faint" id="hdocQueueCount">…</span>
                <button type="button" class="hdoc-card__action" id="hdocAutoCallNext" title="Auto-call next">
                    <i class="fas fa-bullhorn"></i> Auto-call next
                </button>
            </header>
            <div class="hdoc-card__body">
                <div class="hdoc-skeleton" id="hdocQueueSkel"><span></span><span></span><span></span></div>
                <div style="overflow-x:auto;">
                <table class="hdoc-table" id="hdocQueueTbl" hidden style="min-width:600px;">
                    <thead>
                        <tr><th>#</th><th>Time</th><th>Patient</th><th>Age</th><th>Status</th><th>Payment</th><th class="ta-r" style="white-space:nowrap;">Action</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
                </div>
                <div class="hdoc-empty-state" id="hdocQueueEmpty" hidden>
                    <i class="fas fa-coffee"></i>
                    <p>No patients booked for today.</p>
                </div>
            </div>
        </article>

        <!-- Recent prescriptions -->
        <article class="hdoc-card hdoc-card--rx" id="hdocRecentRx">
            <header class="hdoc-card__head">
                <h3>Recent Prescriptions</h3>
                <a class="hdoc-card__link" href="prescriptionreports.php">All <i class="fas fa-arrow-right"></i></a>
            </header>
            <div class="hdoc-card__body">
                <div class="hdoc-skeleton" id="hdocRxSkel"><span></span><span></span><span></span></div>
                <ul class="hdoc-list" id="hdocRxList" hidden></ul>
                <div class="hdoc-empty-state" id="hdocRxEmpty" hidden>
                    <i class="fas fa-prescription-bottle-medical"></i>
                    <p>No prescriptions written yet.</p>
                </div>
            </div>
        </article>

        <!-- Specialty panel -->
        <article class="hdoc-card hdoc-card--spec" id="hdocSpec">
            <header class="hdoc-card__head">
                <h3 id="hdocSpecTitle"><?= $isGynaec ? 'Antenatal Follow-ups' : 'Cardiology Risk Patients' ?></h3>
                <span class="hdoc-mono hdoc-faint" id="hdocSpecKind"><?= $isGynaec ? 'GYNAEC' : 'CARDIO' ?></span>
            </header>
            <div class="hdoc-card__body">
                <div class="hdoc-skeleton" id="hdocSpecSkel"><span></span><span></span><span></span></div>
                <ul class="hdoc-list hdoc-list--spec" id="hdocSpecList" hidden></ul>
                <div class="hdoc-empty-state" id="hdocSpecEmpty" hidden>
                    <i class="fas fa-stethoscope"></i>
                    <p>No flagged patients yet.</p>
                </div>
            </div>
        </article>

        <!-- Weekly trend chart -->
        <article class="hdoc-card hdoc-card--trend" id="hdocTrendCard">
            <header class="hdoc-card__head">
                <h3>Last 7 Days</h3>
                <span class="hdoc-mono hdoc-faint">APPOINTMENTS</span>
            </header>
            <div class="hdoc-card__body">
                <div id="hdocTrendChart" style="min-height:230px"></div>
            </div>
        </article>
    </div>
</section>

<script>
(function () {
    'use strict';

    var ROOT = document.querySelector('.h360-doc-dashboard');
    if (!ROOT) return;

    var RX_PAGE = ROOT.getAttribute('data-rx-page') || 'prescription.php';
    var REDUCED = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---- 1. Counter animation (KPI numbers 0 → value) ---------------------
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-num') || '0', 10);
        if (REDUCED || target <= 0) { el.textContent = target.toLocaleString(); return; }
        var start = performance.now();
        var dur   = 800;
        function tick(t) {
            var p = Math.min(1, (t - start) / dur);
            // ease-out cubic
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.round(target * eased).toLocaleString();
            if (p < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }
    Array.prototype.forEach.call(ROOT.querySelectorAll('.hdoc-kpi__num[data-num]'), animateCounter);

    // ---- 2. Helper: safe text + html escape -------------------------------
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function fmtTime(t) {
        if (!t) return '—';
        var m = String(t).match(/^(\d{1,2}):(\d{2})/);
        if (!m) return esc(t);
        var h = parseInt(m[1], 10), mm = m[2], ap = h >= 12 ? 'PM' : 'AM';
        h = h % 12; if (h === 0) h = 12;
        return h + ':' + mm + ' ' + ap;
    }
    function fetchJSON(url) {
        return fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
    }

    // ---- 3. Today's queue --------------------------------------------------
    fetchJSON('ajax/dashbord/doctor_today_queue.php').then(function (data) {
        var skel = document.getElementById('hdocQueueSkel');
        var tbl  = document.getElementById('hdocQueueTbl');
        var empty = document.getElementById('hdocQueueEmpty');
        var cnt  = document.getElementById('hdocQueueCount');
        if (skel) skel.hidden = true;
        cnt.textContent = (data.count || 0) + ' SCHEDULED';
        if (!data.success || !data.rows || data.rows.length === 0) {
            if (empty) empty.hidden = false;
            return;
        }
        var tb = tbl.querySelector('tbody');
        tb.innerHTML = data.rows.map(function (r) {
            var rxHref = RX_PAGE + '?appointRegisterId=' + encodeURIComponent(r.appoint_register_id) + '&appoint_id=' + r.appoint_id;
            return '<tr class="hdoc-row" data-href="' + esc(rxHref) + '">' +
                '<td class="hdoc-tok">' + r.token + '</td>' +
                '<td class="hdoc-mono">' + esc(fmtTime(r.start_time)) + '</td>' +
                '<td>' + esc(r.patient_name) + '<span class="hdoc-row__sub">' + esc(r.appoint_register_id) + '</span></td>' +
                '<td>' + esc(r.age) + '</td>' +
                '<td><span class="hdoc-badge hdoc-badge--' + esc(r.status_key) + '">' + esc(r.status_label) + '</span></td>' +
                '<td>' + (r.payment_paid
                    ? '<span class="hdoc-badge hdoc-badge--done">Paid</span>' + (r.payment_method ? '<span class="hdoc-row__sub">' + esc(r.payment_method) + '</span>' : '')
                    : '<span class="hdoc-badge hdoc-badge--waiting">Pending</span>') + '</td>' +
                '<td class="ta-r" style="white-space:nowrap;"><a class="hdoc-mini-btn" href="' + esc(rxHref) + '"><i class="fas fa-pen"></i> Write Rx</a></td>' +
                '</tr>';
        }).join('');
        tbl.hidden = false;
        // Row click → Rx
        Array.prototype.forEach.call(tb.querySelectorAll('.hdoc-row'), function (tr) {
            tr.addEventListener('click', function (e) {
                if (e.target.closest('a,button')) return;
                window.location.href = tr.getAttribute('data-href');
            });
        });
        // Auto-call next button: scroll & flash first waiting row
        var btn = document.getElementById('hdocAutoCallNext');
        if (btn) btn.addEventListener('click', function () {
            var first = tb.querySelector('.hdoc-badge--waiting');
            var row = first ? first.closest('tr') : null;
            if (!row) return;
            row.scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth', block: 'center' });
            row.classList.add('is-flash');
            setTimeout(function () { row.classList.remove('is-flash'); }, 1400);
        });
    }).catch(function () {
        var skel = document.getElementById('hdocQueueSkel');
        var empty = document.getElementById('hdocQueueEmpty');
        if (skel) skel.hidden = true;
        if (empty) empty.hidden = false;
    });

    // ---- 4. Recent Rx ------------------------------------------------------
    fetchJSON('ajax/dashbord/doctor_recent_rx.php').then(function (data) {
        var skel = document.getElementById('hdocRxSkel');
        var list = document.getElementById('hdocRxList');
        var empty = document.getElementById('hdocRxEmpty');
        if (skel) skel.hidden = true;
        if (!data.success || !data.rows || data.rows.length === 0) {
            if (empty) empty.hidden = false; return;
        }
        list.innerHTML = data.rows.map(function (r) {
            var rxHref = (r.source === 'gynaec' ? 'gynaec_prescription.php' : 'prescription.php') +
                         '?appointRegisterId=' + encodeURIComponent(r.appoint_register_id || '');
            return '<li class="hdoc-list__item">' +
                '<a href="' + esc(rxHref) + '">' +
                  '<div class="hdoc-list__main">' +
                    '<span class="hdoc-list__name">' + esc(r.patient_name) + '</span>' +
                    '<span class="hdoc-list__date">' + esc(r.date) + '</span>' +
                  '</div>' +
                  '<div class="hdoc-list__sub">' + esc(r.diagnosis) + '</div>' +
                '</a></li>';
        }).join('');
        list.hidden = false;
    }).catch(function () {
        var skel = document.getElementById('hdocRxSkel');
        var empty = document.getElementById('hdocRxEmpty');
        if (skel) skel.hidden = true;
        if (empty) empty.hidden = false;
    });

    // ---- 5. Specialty panel ------------------------------------------------
    fetchJSON('ajax/dashbord/doctor_specialty_panel.php').then(function (data) {
        var skel = document.getElementById('hdocSpecSkel');
        var list = document.getElementById('hdocSpecList');
        var empty = document.getElementById('hdocSpecEmpty');
        var ttl  = document.getElementById('hdocSpecTitle');
        var kind = document.getElementById('hdocSpecKind');
        if (skel) skel.hidden = true;
        if (!data.success || !data.panel) { if (empty) empty.hidden = false; return; }
        if (ttl)  ttl.textContent  = data.panel.title || ttl.textContent;
        if (kind) kind.textContent = (data.panel.kind || 'GENERIC').toUpperCase();
        if (!data.panel.rows || data.panel.rows.length === 0) { empty.hidden = false; return; }
        list.innerHTML = data.panel.rows.map(function (r) {
            var rxHref = (data.panel.kind === 'gynaec' ? 'gynaec_prescription.php' : 'prescription.php') +
                         '?appointRegisterId=' + encodeURIComponent(r.appoint_register_id || '');
            return '<li class="hdoc-list__item">' +
                '<a href="' + esc(rxHref) + '">' +
                  '<div class="hdoc-list__main">' +
                    '<span class="hdoc-list__name">' + esc(r.patient_name) + '</span>' +
                    '<span class="hdoc-list__date">' + esc(r.date || '') + '</span>' +
                  '</div>' +
                  '<div class="hdoc-list__sub">' + esc(r.tag) + '</div>' +
                '</a></li>';
        }).join('');
        list.hidden = false;
    }).catch(function () {
        var skel = document.getElementById('hdocSpecSkel');
        var empty = document.getElementById('hdocSpecEmpty');
        if (skel) skel.hidden = true;
        if (empty) empty.hidden = false;
    });

    // ---- 6. Weekly trend chart (ApexCharts; defer until available) --------
    function whenApex(cb, tries) {
        tries = tries || 0;
        if (window.ApexCharts) return cb();
        if (tries > 60) return; // ~6s
        setTimeout(function () { whenApex(cb, tries + 1); }, 100);
    }

    fetchJSON('ajax/dashbord/doctor_weekly_trend.php').then(function (data) {
        if (!data.success) return;
        whenApex(function () {
            var el = document.getElementById('hdocTrendChart');
            if (!el) return;
            var opts = {
                chart: {
                    type: 'bar',
                    height: 230,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    animations: {
                        enabled: !REDUCED,
                        easing: 'easeinout',
                        speed: 700,
                        animateGradually: { enabled: true, delay: 80 },
                        dynamicAnimation: { enabled: true, speed: 350 }
                    },
                    events: {
                        dataPointSelection: function (e, ctx, cfg) {
                            var idx = cfg.dataPointIndex;
                            var d = new Date();
                            d.setDate(d.getDate() - (6 - idx));
                            var iso = d.toISOString().slice(0, 10);
                            window.location.href = 'AppointmentOnline.php?date=' + iso;
                        }
                    }
                },
                series: [{ name: 'Appointments', data: data.series || [] }],
                xaxis: {
                    categories: data.labels || [],
                    labels: { style: { colors: '#5C667B', fontSize: '11px', fontFamily: 'ui-monospace, Menlo, monospace' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#5C667B', fontSize: '11px' } },
                    forceNiceScale: true
                },
                grid: { borderColor: 'rgba(21,49,90,0.08)', strokeDashArray: 3 },
                colors: ['#1E4475'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '52%',
                        distributed: false
                    }
                },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: 'light',
                    y: { formatter: function (v) { return v + ' appt' + (v === 1 ? '' : 's'); } }
                },
                states: {
                    hover: { filter: { type: 'darken', value: 0.85 } },
                    active: { filter: { type: 'darken', value: 0.7 } }
                }
            };
            try { new ApexCharts(el, opts).render(); } catch (e) {}
        });
    });

    // ---- 7. Soft pulse on Next-patient card every 4s ----------------------
    if (!REDUCED) {
        var pulseEl = ROOT.querySelector('.hdoc-kpi--accent.is-pulsing');
        if (pulseEl) {
            setInterval(function () {
                pulseEl.classList.remove('hdoc-pulse');
                // reflow to restart animation
                void pulseEl.offsetWidth;
                pulseEl.classList.add('hdoc-pulse');
            }, 4000);
        }
    }
})();
</script>
