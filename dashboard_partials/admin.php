<?php
/**
 * B-2040 — Admin (clinic caretaker) dashboard partial.
 *
 * Included by dashboard.php AFTER ajax/header.php — so $conn, jQuery,
 * ApexCharts, Bootstrap, FontAwesome are already available.
 *
 * Scope: CLINIC-WIDE. Admin (Dinesh) sees finance + staff + reports across
 * the org, with an optional doctor-switcher (writes to
 * $_SESSION['admin_doctor_filter']) to narrow to one doctor's slice.
 *
 * Server-side seed for first paint, then refreshed by AJAX:
 *   ajax/dashbord/admin_outstanding_bills.php   (KPI strip)
 *   ajax/dashbord/admin_doctor_schedule.php     (7-day schedule grid)
 *   ajax/dashbord/admin_staff_activity.php      (right-rail feed)
 *   ajax/dashbord/admin_finance_summary.php     (30-day revenue chart)
 *
 * Legacy widgets retained from dashboard.php (rendered first by the parent
 * dashboard.php prior to including this partial; this partial layers Admin-
 * specific widgets on top under a clearly demarcated section).
 *
 * NOTE: This partial is self-contained. CSS lives in
 * assets/h360-ui/h360-ui.css under body[data-role-dashboard="admin"].
 */

if (!isset($conn) || !$conn) {
    require_once(__DIR__ . "/../config/functions.php");
}

$AD_UserId = isset($_SESSION['security_id']) ? (int) $_SESSION['security_id'] : 0;
$AD_RoleId = isset($_SESSION['role_id'])     ? (int) $_SESSION['role_id']     : 0;
$AD_OrgId  = isset($_SESSION['org_id'])      ? (int) $_SESSION['org_id']      : 0;
// FIX_B_2220: Normalize filter — header stores 'all' literal; coerce to 0 for arithmetic clarity.
$_rawFilter = $_SESSION['admin_doctor_filter'] ?? 'all';
$AD_DocFilter = ($_rawFilter === 'all' || $_rawFilter === '' || $_rawFilter === '0') ? 0 : (int) $_rawFilter;

/* ---------- Server-side seed (so the page is meaningful before JS runs) ---------- */
$adOrgClauseAo  = " AND ao.org_id = '$AD_OrgId' ";
$adOrgClauseInv = " AND inv.org_id = '$AD_OrgId' ";
$adOrgClausePmb = " AND pmb.org_id = '$AD_OrgId' ";
if ($AD_DocFilter > 0) {
    $adApptDocFilter = " AND ao.doctor_name = '$AD_DocFilter' ";
} else {
    $adApptDocFilter = '';
}

$adSeed = [
    'today_appt'  => 0,
    'today_rev'   => 0.0,
    'in_queue'    => 0,
    'outstanding' => 0,
    'doc_label'   => 'All Doctors',
];

$q = "SELECT COUNT(*) FROM appointment_online ao
      WHERE ao.appoint_status='1' AND ao.appoint_date=CURDATE()
        $adOrgClauseAo $adApptDocFilter";
$adSeed['today_appt'] = (int) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

$q = "SELECT COALESCE(SUM(net_amount),0) FROM patient_medicine_billing pmb
      WHERE pmb.status='1' AND DATE(pmb.created_at)=CURDATE() $adOrgClausePmb";
$adSeed['today_rev'] += (float) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

$q = "SELECT COALESCE(SUM(paid_amount),0) FROM invoice inv
      WHERE inv.status=1 AND DATE(inv.modified_at)=CURDATE() $adOrgClauseInv";
$adSeed['today_rev'] += (float) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

$q = "SELECT COUNT(*) FROM appointment_online ao
      WHERE ao.appoint_status='1' AND ao.appoint_date=CURDATE()
        AND (ao.check_out IS NULL OR ao.check_out='0000-00-00 00:00:00')
        $adOrgClauseAo $adApptDocFilter";
$adSeed['in_queue'] = (int) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

$q = "SELECT COUNT(*) FROM invoice inv WHERE inv.status=1 AND inv.balance_amount>0 $adOrgClauseInv";
$adSeed['outstanding'] = (int) (mysqli_fetch_array(mysqli_query($conn, $q))[0] ?? 0);

if ($AD_DocFilter > 0) {
    $rd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT doctor_name FROM doctors WHERE doc_id='$AD_DocFilter' LIMIT 1"));
    if ($rd && !empty($rd['doctor_name'])) $adSeed['doc_label'] = $rd['doctor_name'];
}
?>
<?php /* FIX_B_2225: Stable cache-buster keyed to file mtime, not time(). */ ?>
<link rel="stylesheet" href="assets/h360-ui/h360-ui.css?v=admin-<?php echo @filemtime(__DIR__ . '/../assets/h360-ui/h360-ui.css') ?: 'static'; ?>">
<script>document.body && document.body.setAttribute('data-role-dashboard','admin');</script>

<div class="ad-wrap" id="ad-wrap" data-current-user="<?php echo $AD_UserId; ?>">

  <!-- ============================================================
       Chrome bar — admin identity + scope chip
       ============================================================ -->
  <header class="ad-chrome" role="banner">
    <div class="ad-chrome-l">
      <div>
        <div class="ad-eyebrow">CLINIC&nbsp;·&nbsp;Admin Console</div>
        <h1>Operations &amp; Finance Dashboard</h1>
      </div>
    </div>
    <div class="ad-chrome-r" aria-live="polite">
      <span class="ad-scope-chip" id="ad-scope-chip">
        <span class="ad-dot" aria-hidden="true"></span>
        Currently viewing: <strong><?php echo htmlspecialchars($adSeed['doc_label']); ?></strong>
      </span>
      <span id="ad-clock"><?php echo date('D, d M Y · H:i'); ?> IST</span>
    </div>
  </header>

  <!-- ============================================================
       KPI strip — 4 large click-through cards
       ============================================================ -->
  <section class="ad-kpis" aria-label="Admin operating KPIs">

    <a class="ad-kpi" href="AppointmentOnline.php" data-kpi="today_appt"
       aria-label="Today's appointments — open Appointments page">
      <span class="ad-cta">VIEW &rarr;</span>
      <div class="ad-kpi-label"><span class="ad-ico"><i class="fa fa-calendar-check"></i></span> Today's Appointments</div>
      <div class="ad-kpi-value" data-counter data-target="<?php echo $adSeed['today_appt']; ?>">0</div>
      <div class="ad-kpi-sub"><span id="ad-kpi-appt-sub">scheduled today</span></div>
    </a>

    <a class="ad-kpi" href="RevenueReport.php" data-kpi="today_rev"
       aria-label="Today's revenue — open Revenue Report">
      <span class="ad-cta">REPORT &rarr;</span>
      <div class="ad-kpi-label"><span class="ad-ico"><i class="fa fa-rupee-sign"></i></span> Today's Revenue</div>
      <div class="ad-kpi-value">
        <span class="ad-rupee">₹</span><span data-counter data-target="<?php echo (int) $adSeed['today_rev']; ?>">0</span>
      </div>
      <div class="ad-kpi-sub"><span id="ad-kpi-rev-sub">pharmacy + billing combined</span></div>
    </a>

    <a class="ad-kpi" href="receptionist.php" data-kpi="in_queue"
       aria-label="Active patients in queue — open Receptionist board">
      <span class="ad-cta">QUEUE &rarr;</span>
      <div class="ad-kpi-label"><span class="ad-ico"><i class="fa fa-user-clock"></i></span> Patients In Queue</div>
      <div class="ad-kpi-value" data-counter data-target="<?php echo $adSeed['in_queue']; ?>">0</div>
      <div class="ad-kpi-sub"><span>not yet checked out</span></div>
    </a>

    <a class="ad-kpi" href="billing_report.php" data-kpi="outstanding"
       aria-label="Outstanding bills — open Billing report">
      <span class="ad-cta">REVIEW &rarr;</span>
      <div class="ad-kpi-label"><span class="ad-ico"><i class="fa fa-file-invoice-dollar"></i></span> Outstanding Bills</div>
      <div class="ad-kpi-value" data-counter data-target="<?php echo $adSeed['outstanding']; ?>">0</div>
      <div class="ad-kpi-sub"><span id="ad-kpi-out-sub">bills with pending balance</span></div>
    </a>

  </section>

  <!-- ============================================================
       Doctor schedule grid + staff activity rail
       ============================================================ -->
  <section class="ad-row" aria-label="Schedule and staff activity">

    <div class="ad-card" id="ad-sched-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-calendar-week" aria-hidden="true"></i>&nbsp; Doctor Schedule · Next 7 Days</h3>
        <span class="ad-tag" id="ad-sched-tag">capacity vs booked</span>
      </div>
      <div class="ad-sched-axis" id="ad-sched-axis">
        <div></div>
        <div class="ad-sched-days" id="ad-sched-days"></div>
      </div>
      <div class="ad-card-body ad-sched-grid" id="ad-sched-grid">
        <div class="ad-empty">loading schedule…</div>
      </div>
      <div class="ad-card-foot">
        <span><span class="ad-legend ad-legend-good"></span> &gt;80% utilized</span>
        <span><span class="ad-legend ad-legend-mid"></span> 40–80%</span>
        <span><span class="ad-legend ad-legend-low"></span> &lt;40%</span>
        <span class="ad-foot-r">click any cell to view that day's slots</span>
      </div>
    </div>

    <div class="ad-card" id="ad-feed-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-stream" aria-hidden="true"></i>&nbsp; Staff Activity</h3>
        <span class="ad-tag"><span class="ad-dot" aria-hidden="true" style="background:var(--info)"></span>polling 60s</span>
      </div>
      <div class="ad-card-body" id="ad-feed-body">
        <div class="ad-feed-section" data-section="login">
          <div class="ad-feed-h">Recent Logins</div>
          <ul class="ad-feed" id="ad-feed-login"><li class="ad-empty">loading…</li></ul>
        </div>
        <div class="ad-feed-section" data-section="recept">
          <div class="ad-feed-h">Receptionist · Today</div>
          <ul class="ad-feed" id="ad-feed-recept"><li class="ad-empty">loading…</li></ul>
        </div>
        <div class="ad-feed-section" data-section="pharm">
          <div class="ad-feed-h">Pharmacy · Today</div>
          <ul class="ad-feed" id="ad-feed-pharm"><li class="ad-empty">loading…</li></ul>
        </div>
      </div>
    </div>

  </section>

  <!-- ============================================================
       Upcoming Birthdays + Upcoming Revisits
       ============================================================ -->
  <section class="ad-row" aria-label="Upcoming birthdays and revisits">

    <div class="ad-card" id="ad-birthday-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-birthday-cake" aria-hidden="true"></i>&nbsp; Upcoming Birthdays</h3>
        <span class="ad-tag" id="ad-birthday-count">loading…</span>
      </div>
      <div class="ad-card-body" style="padding:0; overflow:auto; max-height:280px;">
        <table class="sa-gov-tbl" id="ad-birthday-tbl">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Mobile</th>
              <th>Birthday</th>
              <th>Age</th>
            </tr>
          </thead>
          <tbody id="ad-birthday-body">
            <tr><td colspan="4" class="sa-empty">loading birthdays…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="ad-card" id="ad-revisit-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-calendar-check" aria-hidden="true"></i>&nbsp; Upcoming Revisits</h3>
        <span class="ad-tag" id="ad-revisit-count">loading…</span>
      </div>
      <div class="ad-card-body" style="padding:0; overflow:auto; max-height:280px;">
        <table class="sa-gov-tbl" id="ad-revisit-tbl">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Mobile</th>
              <th>Doctor</th>
              <th>Revisit Date</th>
            </tr>
          </thead>
          <tbody id="ad-revisit-body">
            <tr><td colspan="4" class="sa-empty">loading revisits…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

  </section>

  <!-- ============================================================
       Finance summary — 30-day revenue trend
       ============================================================ -->
  <section class="ad-row ad-row-1" aria-label="Finance summary">
    <div class="ad-card" id="ad-fin-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-chart-area" aria-hidden="true"></i>&nbsp; Revenue · Last 30 Days</h3>
        <span class="ad-tag" id="ad-fin-tag">pharmacy + billing</span>
      </div>
      <div class="ad-card-body" id="ad-fin-body">
        <div id="ad-fin-chart" style="min-height: 280px;">
          <div class="ad-empty">loading revenue trend…</div>
        </div>
      </div>
      <div class="ad-card-foot">
        <span id="ad-fin-foot-l">click any day to open daily report</span>
        <span id="ad-fin-foot-r">—</span>
      </div>
    </div>
  </section>

  <!-- ============================================================
       Quick actions row — 6 prominent CTAs
       ============================================================ -->
  <section class="ad-row ad-row-1" aria-label="Quick actions">
    <div class="ad-card">
      <div class="ad-card-head">
        <h3><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp; Quick Actions</h3>
        <span class="ad-tag">admin shortcuts</span>
      </div>
      <div class="ad-card-body">
        <div class="ad-actions" role="navigation" aria-label="Admin quick actions">
          <a class="ad-action-btn" href="registration.php">
            <span class="ad-ab-eyebrow">+ NEW</span>
            <span class="ad-ab-title">Add Staff</span>
          </a>
          <a class="ad-action-btn" href="doctorstimeslot.php">
            <span class="ad-ab-eyebrow">SCHEDULE</span>
            <span class="ad-ab-title">Doctor Slots</span>
          </a>
          <a class="ad-action-btn" href="RevenueReport.php">
            <span class="ad-ab-eyebrow">REPORT</span>
            <span class="ad-ab-title">View Reports</span>
          </a>
          <a class="ad-action-btn" href="roles.php">
            <span class="ad-ab-eyebrow">MANAGE</span>
            <span class="ad-ab-title">Roles &amp; Access</span>
          </a>
          <a class="ad-action-btn" href="refunds.php">
            <span class="ad-ab-eyebrow">FINANCE</span>
            <span class="ad-ab-title">Refunds</span>
          </a>
          <a class="ad-action-btn" href="dailyreports.php">
            <span class="ad-ab-eyebrow">DAILY</span>
            <span class="ad-ab-title">Daily Report</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <footer class="ad-foot">
    <span id="ad-foot-l">ADMIN · ORG <?php echo (int) $AD_OrgId; ?> · clinic-wide view</span>
    <span id="ad-foot-r">last refresh: <span id="ad-last-refresh">—</span></span>
  </footer>

</div>

<!-- ============================================================
     Admin Dashboard JS — counters, polling, schedule, finance chart
     Self-contained IIFE; namespace: window.AdminDashboard
     ============================================================ -->
<script>
(function () {
  if (!document.body.hasAttribute('data-role-dashboard')) {
    document.body.setAttribute('data-role-dashboard', 'admin');
  }

  var REDUCE = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- DOM helpers ---------- */
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]);
    });
  }
  function fmtTs(ts) {
    if (!ts) return '';
    var d = new Date(String(ts).replace(' ', 'T'));
    if (isNaN(d.getTime())) return ts;
    var diff = (Date.now() - d.getTime()) / 1000;
    if (diff < 60) return Math.floor(diff) + 's ago';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
  }
  function fmtINR(n) {
    n = Number(n) || 0;
    return n.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  }

  /* ---------- counter animation ---------- */
  function animateCounter(el, target, dur) {
    target = Number(target) || 0;
    if (REDUCE || dur <= 0) { el.textContent = target.toLocaleString('en-IN'); return; }
    var start = performance.now();
    function step(now) {
      var t = Math.min(1, (now - start) / dur);
      var k = 1 - Math.pow(1 - t, 3); // easeOutCubic
      el.textContent = Math.round(target * k).toLocaleString('en-IN');
      if (t < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  function bootCounters() {
    document.querySelectorAll('#ad-wrap [data-counter]').forEach(function (el) {
      animateCounter(el, el.getAttribute('data-target'), 800);
    });
  }

  /* ---------- AJAX ---------- */
  function fetchJSON(url) {
    return fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' }})
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
  }
  function setRefreshStamp() {
    var el = document.getElementById('ad-last-refresh');
    if (el) el.textContent = new Date().toLocaleTimeString();
  }

  /* ---------- KPI refresh ---------- */
  function refreshKpis() {
    return fetchJSON('ajax/dashbord/admin_outstanding_bills.php').then(function (d) {
      var map = {
        'today_appt':   d.today_appointments,
        'today_rev':    Math.round(d.today_revenue || 0),
        'in_queue':     d.patients_in_queue,
        'outstanding':  d.outstanding_count,
      };
      Object.keys(map).forEach(function (k) {
        var el = document.querySelector('.ad-kpi[data-kpi="' + k + '"] [data-counter]');
        if (el) animateCounter(el, map[k], 600);
      });
      var subRev = document.getElementById('ad-kpi-rev-sub');
      if (subRev && d.today_revenue_split) {
        subRev.innerHTML = '<span>Pharmacy ₹' + fmtINR(d.today_revenue_split.pharmacy)
          + ' · Billing ₹' + fmtINR(d.today_revenue_split.billing) + '</span>';
      }
      var subOut = document.getElementById('ad-kpi-out-sub');
      if (subOut) subOut.textContent = d.refund_count_30d + ' refunds in last 30d';
    }).catch(function (e) { console.warn('admin kpis failed', e); });
  }

  /* ---------- Doctor schedule ---------- */
  function renderSchedule(data) {
    var days = data.days || [];
    var doctors = data.doctors || [];
    var axis = document.getElementById('ad-sched-days');
    if (axis) {
      axis.innerHTML = days.map(function (d) {
        return '<div class="ad-sched-day-h"><div class="ad-sched-day-h-l">' + esc(d.label)
             + '</div><div class="ad-sched-day-h-s">' + esc(d.short) + '</div></div>';
      }).join('');
      axis.style.gridTemplateColumns = 'repeat(' + days.length + ', minmax(0, 1fr))';
    }
    var grid = document.getElementById('ad-sched-grid');
    if (!grid) return;
    if (!doctors.length) {
      grid.innerHTML = '<div class="ad-empty">No active doctors in scope.</div>';
      return;
    }
    var html = '';
    doctors.forEach(function (doc, idx) {
      html += '<div class="ad-sched-row" style="--ad-row-i:' + idx + '">';
      var imgPath = doc.doc_img && String(doc.doc_img).trim() !== ''
        ? 'doctor_images/' + doc.doc_img
        : 'assets/img/user.png';
      html += '<div class="ad-sched-doc">'
            + '<img class="ad-sched-doc-img" src="' + esc(imgPath) + '" alt="' + esc(doc.doctor_name) + '" onerror="this.src=\'assets/img/user.png\'">'
            + '<div><div class="ad-sched-doc-name">' + esc(doc.doctor_name) + '</div>'
            + '<div class="ad-sched-doc-type">' + esc(doc.doctor_type || '') + '</div></div>'
            + '</div>';
      html += '<div class="ad-sched-cells" style="grid-template-columns: repeat(' + days.length + ', minmax(0,1fr));">';
      (doc.days || []).forEach(function (cell) {
        var lvl = 'low';
        if (cell.util >= 80) lvl = 'good';
        else if (cell.util >= 40) lvl = 'mid';
        var emptyCls = cell.slots === 0 ? ' ad-sched-cell-empty' : '';
        html += '<a class="ad-sched-cell ad-sched-cell-' + lvl + emptyCls
              + '" href="doctorstimeslot.php?doc_id=' + encodeURIComponent(doc.doc_id) + '&date=' + encodeURIComponent(cell.date) + '"'
              + ' aria-label="' + esc(doc.doctor_name) + ' on ' + esc(cell.date) + ': ' + cell.booked + ' booked of ' + cell.slots + '">'
              + '<div class="ad-sched-cell-top">' + (cell.slots > 0 ? cell.booked + '/' + cell.slots : '—') + '</div>'
              + '<div class="ad-sched-cell-bar" style="--ad-util:' + cell.util + '%"></div>'
              + '<div class="ad-sched-cell-bot">' + (cell.slots > 0 ? cell.util + '%' : 'no slot') + '</div>'
              + '</a>';
      });
      html += '</div></div>';
    });
    grid.innerHTML = html;
  }
  function refreshSchedule() {
    return fetchJSON('ajax/dashbord/admin_doctor_schedule.php').then(renderSchedule)
      .catch(function (e) {
        console.warn('admin schedule failed', e);
        var g = document.getElementById('ad-sched-grid');
        if (g) g.innerHTML = '<div class="ad-empty">Unable to load schedule.</div>';
      });
  }

  /* ---------- Staff activity ---------- */
  function renderFeed(listEl, items, emptyMsg) {
    if (!listEl) return;
    if (!items || !items.length) {
      listEl.innerHTML = '<li class="ad-empty">' + esc(emptyMsg) + '</li>';
      return;
    }
    listEl.innerHTML = items.map(function (it, i) {
      return '<li class="ad-feed-row" style="--ad-feed-i:' + i + '" data-href="' + esc(it.href || '#') + '" tabindex="0" role="link">'
           + '<span class="ad-feed-when">' + esc(fmtTs(it.when)) + '</span>'
           + '<span class="ad-feed-who"><strong>' + esc(it.who) + '</strong> <em>(' + esc(it.role) + ')</em></span>'
           + '<span class="ad-feed-detail">' + esc(it.detail) + '</span>'
           + '</li>';
    }).join('');
    // bind click
    listEl.querySelectorAll('[data-href]').forEach(function (li) {
      li.addEventListener('click', function () {
        var h = li.getAttribute('data-href');
        if (h && h !== '#') window.location.href = h;
      });
      li.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); li.click(); }
      });
    });
  }
  function refreshActivity() {
    return fetchJSON('ajax/dashbord/admin_staff_activity.php').then(function (d) {
      renderFeed(document.getElementById('ad-feed-login'),  d.logins,        'no recent logins');
      renderFeed(document.getElementById('ad-feed-recept'), d.receptionist,  'no receptionist activity yet today');
      renderFeed(document.getElementById('ad-feed-pharm'),  d.pharmacy,      'no pharmacy activity yet today');
    }).catch(function (e) {
      console.warn('admin activity failed', e);
    });
  }

  /* ---------- Birthday + Revisit lists ---------- */
  var _birthdayPopupShown = false;
  function renderBirthdayRows(items) {
    var countEl = document.getElementById('ad-birthday-count');
    if (countEl) countEl.textContent = (items.length || 0) + ' patients';
    var tb = document.getElementById('ad-birthday-body');
    if (!tb) return;
    if (!items.length) {
      tb.innerHTML = '<tr><td colspan="4" class="sa-empty">No upcoming birthdays found.</td></tr>';
      return;
    }
    tb.innerHTML = items.map(function (r) {
      return '<tr>'
        + '<td><strong>' + esc(r.patient_name) + '</strong><br><small style="color:var(--navy-400)">' + esc(r.patient_id) + '</small></td>'
        + '<td>' + esc(r.mobile_number) + '</td>'
        + '<td><span class="sa-ent-tag">' + esc(r.days_label) + '</span><br><small>' + esc(r.next_birthday_display) + '</small></td>'
        + '<td>' + esc(r.turning_age) + '</td>'
        + '</tr>';
    }).join('');
  }
  function renderRevisitRows(items) {
    var countEl = document.getElementById('ad-revisit-count');
    if (countEl) countEl.textContent = (items.length || 0) + ' patients';
    var tb = document.getElementById('ad-revisit-body');
    if (!tb) return;
    if (!items.length) {
      tb.innerHTML = '<tr><td colspan="4" class="sa-empty">No upcoming revisits found.</td></tr>';
      return;
    }
    tb.innerHTML = items.map(function (r) {
      return '<tr>'
        + '<td><strong>' + esc(r.patient_name) + '</strong><br><small style="color:var(--navy-400)">' + esc(r.patient_id) + '</small></td>'
        + '<td>' + esc(r.mobile_number) + '</td>'
        + '<td>' + esc(r.doctor_name) + '</td>'
        + '<td><span class="sa-ent-tag">' + esc(r.days_label) + '</span><br><small>' + esc(r.revisit_date_display) + '</small></td>'
        + '</tr>';
    }).join('');
  }
  function loadDashboardLists() {
    return fetchJSON('ajax/dashbord/get_dashboard_lists.php').then(function (d) {
      renderBirthdayRows(d.birthdays || []);
      renderRevisitRows(d.revisits || []);
      if (!_birthdayPopupShown && d.today_birthdays && d.today_birthdays.length) {
        _birthdayPopupShown = true;
        var el = document.createElement('div');
        var rows = d.today_birthdays.map(function (r) {
          return '<tr><td>' + esc(r.patient_name) + '</td><td>' + esc(r.mobile_number) + '</td><td>' + esc(r.turning_age) + '</td></tr>';
        }).join('');
        el.innerHTML = '<div style="text-align:left"><p class="mb-2"><strong>Today\'s birthday patients</strong></p>'
          + '<table class="table table-sm mb-0"><thead><tr><th>Patient</th><th>Mobile</th><th>Age</th></tr></thead>'
          + '<tbody>' + rows + '</tbody></table></div>';
        if (window.swal) {
          swal({ title: 'Birthday Wishes Reminder 🎂', content: el, icon: 'info' });
        }
      }
    }).catch(function (e) {
      console.warn('[admin] dashboard lists failed', e);
      var tb1 = document.getElementById('ad-birthday-body');
      var tb2 = document.getElementById('ad-revisit-body');
      if (tb1) tb1.innerHTML = '<tr><td colspan="4" class="sa-empty">Unable to load.</td></tr>';
      if (tb2) tb2.innerHTML = '<tr><td colspan="4" class="sa-empty">Unable to load.</td></tr>';
    });
  }

  /* ---------- Finance chart ---------- */
  var apexChart = null;
  function renderFinance(data) {
    var days = data.days || [];
    var labels = days.map(function (d) { return d.label; });
    var pharm  = days.map(function (d) { return Number(d.pharmacy || 0); });
    var bill   = days.map(function (d) { return Number(d.billing  || 0); });
    var dates  = days.map(function (d) { return d.date; });
    var foot = document.getElementById('ad-fin-foot-r');
    if (foot && data.totals) {
      foot.textContent = '30d total: ₹' + fmtINR(data.totals.grand)
        + '  ·  pharmacy ₹' + fmtINR(data.totals.pharmacy)
        + '  ·  billing ₹' + fmtINR(data.totals.billing);
    }
    var host = document.getElementById('ad-fin-chart');
    if (!host) return;
    if (typeof ApexCharts === 'undefined') {
      // Fallback: minimal table
      host.innerHTML = '<div class="ad-empty">ApexCharts unavailable — totals shown in footer.</div>';
      return;
    }
    var options = {
      series: [
        { name: 'Pharmacy', data: pharm },
        { name: 'Billing',  data: bill  }
      ],
      chart: {
        type: 'area', height: 280, stacked: true,
        toolbar: { show: false },
        animations: { enabled: !REDUCE, easing: 'easeOutCubic', speed: 600 },
        events: {
          dataPointSelection: function (e, ctx, cfg) {
            var idx = cfg.dataPointIndex;
            if (idx == null || idx < 0 || idx >= dates.length) return;
            window.location.href = 'dailyreports.php?date=' + encodeURIComponent(dates[idx]);
          }
        }
      },
      colors: ['#1E4475', '#C49A3F'],
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      /* FIX_B_2223: area-chart dataPointSelection requires visible markers
         to receive clicks; size 0 hides them but keeps hover/click region. */
      markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
      fill: { type: 'gradient', gradient: { shadeIntensity: 0.6, opacityFrom: 0.5, opacityTo: 0.05 } },
      xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
      yaxis: {
        labels: {
          formatter: function (v) { return '₹' + fmtINR(v); },
          style: { fontSize: '11px' }
        }
      },
      tooltip: {
        shared: true, intersect: false,
        y: { formatter: function (v) { return '₹' + fmtINR(v); } }
      },
      legend: { position: 'top', horizontalAlign: 'right' },
      grid: { borderColor: 'rgba(8,21,48,0.10)', strokeDashArray: 3 }
    };
    if (apexChart) {
      apexChart.updateOptions({ xaxis: { categories: labels } }, false, !REDUCE);
      apexChart.updateSeries(options.series, !REDUCE);
    } else {
      host.innerHTML = '';
      apexChart = new ApexCharts(host, options);
      apexChart.render();
    }
  }
  function refreshFinance() {
    return fetchJSON('ajax/dashbord/admin_finance_summary.php').then(renderFinance)
      .catch(function (e) {
        console.warn('admin finance failed', e);
        var h = document.getElementById('ad-fin-chart');
        if (h) h.innerHTML = '<div class="ad-empty">Unable to load revenue trend.</div>';
      });
  }

  /* ---------- clock ---------- */
  function tickClock() {
    var c = document.getElementById('ad-clock');
    if (c) c.textContent = new Date().toLocaleString(undefined, {
      weekday: 'short', day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    }) + ' IST';
  }

  /* ---------- boot ---------- */
  function boot() {
    bootCounters();
    Promise.all([refreshKpis(), refreshSchedule(), refreshActivity(), refreshFinance(), loadDashboardLists()])
      .then(setRefreshStamp);
    tickClock();
    setInterval(tickClock, 30000);
    // FIX_B_2224: skip polling when tab is hidden to save CPU + DB load.
    function whenVisible(fn) { return function () { if (!document.hidden) fn(); }; }
    setInterval(whenVisible(function () { refreshKpis().then(setRefreshStamp); }), 60000);
    setInterval(whenVisible(function () { refreshActivity().then(setRefreshStamp); }), 60000);
    setInterval(whenVisible(function () { refreshSchedule().then(setRefreshStamp); }), 120000);
    setInterval(whenVisible(function () { refreshFinance().then(setRefreshStamp); }), 180000);
    setInterval(whenVisible(function () { loadDashboardLists().then(setRefreshStamp); }), 120000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  window.AdminDashboard = {
    refreshKpis: refreshKpis,
    refreshSchedule: refreshSchedule,
    refreshActivity: refreshActivity,
    refreshFinance: refreshFinance,
    loadDashboardLists: loadDashboardLists
  };
})();
</script>
