<?php
/**
 * Super Admin dashboard partial.
 *
 * Included by dashboard.php AFTER ajax/header.php — so $conn, jQuery, ApexCharts,
 * Bootstrap, FontAwesome are already available.
 *
 * Scope: SYSTEM-WIDE governance + health. SA is the only role that bypasses
 * org_id scoping; every metric below is cross-org by design.
 *
 * Data is rendered server-side for first paint, then refreshed by AJAX:
 *   ajax/dashbord/sa_kpis.php
 *   ajax/dashbord/sa_audit_feed.php
 *   ajax/dashbord/sa_doctor_heatmap.php
 *   ajax/dashbord/sa_governance_changes.php
 *
 * All KPI cards are anchor tags — click navigates to the relevant report.
 */

if (!isset($conn) || !$conn) {
    // Defensive: should never trigger because dashboard.php requires ajax/header.php
    require_once(__DIR__ . "/../config/functions.php");
}
$SA_SessionUserId = isset($_SESSION['security_id']) ? intval($_SESSION['security_id']) : 0;
$SA_SessionRoleId = isset($_SESSION['role_id'])     ? intval($_SESSION['role_id'])     : 0;

/* ---- Initial server-side render values (so the page is useful even if JS hasn't run) ---- */
$saSeed = [
    'orgs_active'    => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM organization WHERE status='1'"))[0] ?? 0),
    'users_active'   => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM security WHERE status='1'"))[0] ?? 0),
    'audit_24h'      => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM audit_log WHERE ts >= (NOW() - INTERVAL 24 HOUR)"))[0] ?? 0),
    'doctors_active' => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM doctors WHERE status='1'"))[0] ?? 0),
    'roles_active'   => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) FROM roles WHERE status='1'"))[0] ?? 0),
    'menus_mapped'   => (int) (mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(DISTINCT menu_id) FROM role_menus"))[0] ?? 0),
];
?>
<link rel="stylesheet" href="assets/h360-ui/h360-ui.css?v=<?php echo time(); ?>">
<script>document.body && document.body.setAttribute('data-role-dashboard', 'sa');</script>

<div class="sa-wrap" id="sa-wrap" data-current-user="<?php echo $SA_SessionUserId; ?>">

  <!-- ============================================================
       Chrome bar
       ============================================================ -->
  <header class="sa-chrome" role="banner">
    <div class="sa-chrome-l">
      <div>
        <div class="sa-eyebrow">SYSTEM&nbsp;·&nbsp;Super Admin Console</div>
        <h1>Governance &amp; Operations Dashboard</h1>
      </div>
    </div>
    <div class="sa-chrome-r" aria-live="polite">
      <span><span class="sa-dot" aria-hidden="true"></span>Live</span>
      <span id="sa-clock"><?php echo date('D, d M Y · H:i'); ?> IST</span>
    </div>
  </header>

  <!-- ============================================================
       KPI strip (6 cards, all clickable)
       ============================================================ -->
  <section class="sa-kpis" aria-label="Top-level KPIs">

    <a class="sa-kpi" href="org_reports.php" data-kpi="orgs" aria-label="Active organizations">
      <span class="sa-cta">VIEW &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-building"></i></span> Active Orgs</div>
      <div class="sa-kpi-value" data-counter data-target="<?php echo $saSeed['orgs_active']; ?>">0</div>
      <div class="sa-kpi-sub" id="sa-kpi-orgs-sub">
        <span>across the platform</span>
      </div>
    </a>

    <a class="sa-kpi" href="registration.php" data-kpi="users" aria-label="Active users">
      <span class="sa-cta">VIEW &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-users"></i></span> Active Users</div>
      <div class="sa-kpi-value" data-counter data-target="<?php echo $saSeed['users_active']; ?>">0</div>
      <div class="sa-kpi-sub">
        <span>by role</span>
      </div>
      <div class="sa-spark" id="sa-spark-users" aria-hidden="true"></div>
    </a>

    <a class="sa-kpi" href="audit_log.php" data-kpi="audit" aria-label="Audit activity last 24 hours">
      <span class="sa-cta">VIEW &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-shield"></i></span> Audit · 24h</div>
      <div class="sa-kpi-value" data-counter data-target="<?php echo $saSeed['audit_24h']; ?>">0</div>
      <div class="sa-kpi-sub">
        <span class="sa-delta flat" id="sa-kpi-audit-delta">— vs prior 24h</span>
      </div>
    </a>

    <a class="sa-kpi" href="doctor.php" data-kpi="doctors" aria-label="Active doctors across all orgs">
      <span class="sa-cta">VIEW &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-user-md"></i></span> Active Doctors</div>
      <div class="sa-kpi-value" data-counter data-target="<?php echo $saSeed['doctors_active']; ?>">0</div>
      <div class="sa-kpi-sub"><span>all orgs</span></div>
    </a>

    <a class="sa-kpi" href="roles.php" data-kpi="governance" aria-label="Roles and menus mapped">
      <span class="sa-cta">VIEW &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-sitemap"></i></span> Roles · Menus</div>
      <div class="sa-kpi-value">
        <span data-counter data-target="<?php echo $saSeed['roles_active']; ?>">0</span><span style="font-size:18px;color:var(--navy-300);">&nbsp;/&nbsp;</span><span data-counter data-target="<?php echo $saSeed['menus_mapped']; ?>">0</span>
      </div>
      <div class="sa-kpi-sub"><span>active roles / menus mapped</span></div>
    </a>

    <a class="sa-kpi" href="#" data-kpi="db_health" aria-label="Database health"
       onclick="event.preventDefault(); SADashboard.openDbHealth();">
      <span class="sa-cta">DETAILS &rarr;</span>
      <div class="sa-kpi-label"><span class="sa-ico"><i class="fa fa-database"></i></span> DB Health</div>
      <div class="sa-kpi-value" id="sa-kpi-dbh-value">
        <span class="sa-skel" style="display:inline-block;width:80px;height:30px;">000</span>
      </div>
      <div class="sa-kpi-sub" id="sa-kpi-dbh-sub"><span>checking…</span></div>
    </a>

  </section>

  <!-- ============================================================
       Feed + Heatmap row
       ============================================================ -->
  <section class="sa-row" aria-label="Activity and utilization" style="grid-template-columns: 1.4fr 1fr;">

    <div class="sa-card" id="sa-feed-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-rss" aria-hidden="true"></i>&nbsp; Live Audit Stream</h3>
        <span class="sa-tag"><span class="sa-dot" aria-hidden="true" style="background:var(--info)"></span>polling 30s</span>
      </div>
      <div class="sa-card-body" id="sa-feed-body">
        <ul class="sa-feed" id="sa-feed-list" aria-live="polite">
          <li>
            <span class="sa-ts">loading…</span>
            <span class="sa-msg"><span class="sa-skel" style="display:inline-block;width:60%;height:14px;">________</span></span>
            <span class="sa-action a-update">…</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="sa-card" id="sa-heat-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-th" aria-hidden="true"></i>&nbsp; Doctor Utilization · 7d</h3>
        <span class="sa-tag" id="sa-heat-tag">top 12 doctors</span>
      </div>
      <div class="sa-heat-axis" id="sa-heat-axis" style="--sa-heat-days:7">
        <div></div>
        <div class="sa-heat-days" id="sa-heat-days"></div>
      </div>
      <div class="sa-card-body sa-heat" id="sa-heat-grid" style="--sa-heat-days:7"></div>
    </div>

  </section>

  <!-- ============================================================
       Governance changes + Quick actions row
       ============================================================ -->
  <section class="sa-row" aria-label="Governance and shortcuts" style="grid-template-columns: 1.4fr 1fr;">

    <div class="sa-card" id="sa-gov-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-gavel" aria-hidden="true"></i>&nbsp; Recent Governance Changes</h3>
        <span class="sa-tag">role · menu · doctor · org</span>
      </div>
      <div class="sa-card-body" style="padding:0;">
        <table class="sa-gov-tbl" id="sa-gov-tbl">
          <thead>
            <tr>
              <th>When</th>
              <th>Who</th>
              <th>Org</th>
              <th>Action</th>
              <th>Entity</th>
              <th>Module</th>
            </tr>
          </thead>
          <tbody id="sa-gov-body">
            <tr><td colspan="6" class="sa-empty">loading governance changes…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="sa-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-bolt" aria-hidden="true"></i>&nbsp; Quick Actions</h3>
        <span class="sa-tag">SA shortcuts</span>
      </div>
      <div class="sa-card-body">
        <div class="sa-actions" role="navigation" aria-label="Quick actions">
          <a class="sa-action-btn" href="organization.php">
            <span class="sa-ab-eyebrow">+ NEW</span>
            <span class="sa-ab-title">Add Organization</span>
          </a>
          <a class="sa-action-btn" href="roles.php">
            <span class="sa-ab-eyebrow">+ NEW</span>
            <span class="sa-ab-title">Add Role</span>
          </a>
          <a class="sa-action-btn" href="audit_log.php">
            <span class="sa-ab-eyebrow">VIEW</span>
            <span class="sa-ab-title">Audit Log</span>
          </a>
          <a class="sa-action-btn" href="registration.php">
            <span class="sa-ab-eyebrow">MANAGE</span>
            <span class="sa-ab-title">User Registration</span>
          </a>
          <a class="sa-action-btn" href="doctor.php">
            <span class="sa-ab-eyebrow">MANAGE</span>
            <span class="sa-ab-title">Doctors</span>
          </a>
          <a class="sa-action-btn" href="org_reports.php">
            <span class="sa-ab-eyebrow">REPORT</span>
            <span class="sa-ab-title">Org Reports</span>
          </a>
        </div>
      </div>
    </div>

  </section>

  <!-- ============================================================
       Upcoming Birthdays + Upcoming Revisits
       ============================================================ -->
  <section class="sa-row" aria-label="Upcoming birthdays and revisits" style="grid-template-columns: 1fr 1fr;">

    <div class="sa-card" id="sa-birthday-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-birthday-cake" aria-hidden="true"></i>&nbsp; Upcoming Birthdays</h3>
        <span class="sa-tag" id="sa-birthday-count">loading…</span>
      </div>
      <div class="sa-card-body" style="padding:0; overflow:auto; max-height:280px;">
        <table class="sa-gov-tbl" id="sa-birthday-tbl">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Mobile</th>
              <th>Birthday</th>
              <th>Age</th>
            </tr>
          </thead>
          <tbody id="sa-birthday-body">
            <tr><td colspan="4" class="sa-empty">loading birthdays…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="sa-card" id="sa-revisit-card">
      <div class="sa-card-head">
        <h3><i class="fa fa-calendar-check" aria-hidden="true"></i>&nbsp; Upcoming Revisits</h3>
        <span class="sa-tag" id="sa-revisit-count">loading…</span>
      </div>
      <div class="sa-card-body" style="padding:0; overflow:auto; max-height:280px;">
        <table class="sa-gov-tbl" id="sa-revisit-tbl">
          <thead>
            <tr>
              <th>Patient</th>
              <th>Mobile</th>
              <th>Doctor</th>
              <th>Revisit Date</th>
            </tr>
          </thead>
          <tbody id="sa-revisit-body">
            <tr><td colspan="4" class="sa-empty">loading revisits…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

  </section>

  <!-- ============================================================
       Footer meta
       ============================================================ -->
  <footer class="sa-foot">
    <span id="sa-foot-l">SUPER ADMIN · CROSS-ORG VIEW · org_id bypass active</span>
    <span id="sa-foot-r">last refresh: <span id="sa-last-refresh">—</span></span>
  </footer>

</div>

<!-- ============================================================
     DB Health modal (Bootstrap markup; styling scoped in h360-ui.css)
     ============================================================ -->
<div class="modal fade" id="sa-dbh-modal" tabindex="-1" aria-labelledby="sa-dbh-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:12px; overflow:hidden; border:1px solid var(--line);">
      <div class="modal-header" style="background:var(--navy-900); color:var(--cream); border-bottom:0;">
        <h5 class="modal-title" id="sa-dbh-title">
          <i class="fa fa-database"></i>&nbsp; Database Health · Row counts
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                onclick="document.getElementById('sa-dbh-modal').style.display='none'; document.querySelector('.modal-backdrop')?.remove(); document.body.classList.remove('modal-open');"></button>
      </div>
      <div class="modal-body" style="background:var(--cream); padding:0;">
        <table class="sa-dbh-tbl" id="sa-dbh-tbl">
          <thead><tr><th>Table</th><th class="num" style="text-align:right;">Rows</th></tr></thead>
          <tbody id="sa-dbh-body"><tr><td colspan="2" class="sa-empty">loading…</td></tr></tbody>
        </table>
      </div>
      <div class="modal-footer" style="background:var(--paper); border-top:1px solid var(--line); font-family:'Geist Mono',ui-monospace,monospace; font-size:11px; color:var(--navy-700);">
        <span id="sa-dbh-foot">server time: —</span>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     SA Dashboard JS — counters, polling, heatmap, sparkline
     Self-contained IIFE; namespace: window.SADashboard
     ============================================================ -->
<script>
(function () {
  if (!document.body.hasAttribute('data-role-dashboard')) {
    document.body.setAttribute('data-role-dashboard', 'sa');
  }

  var REDUCE = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- counter animation ---------- */
  // FIX_B_2201: animate from current displayed value, not from 0, so periodic
  // refreshKpis() doesn't visibly drop each number to 0 then climb back.
  function animateCounter(el, target, dur) {
    target = Number(target) || 0;
    // keep data-target in sync so future calls have a stable from-value
    el.setAttribute('data-target', String(target)); // FIX_B_2203
    var fromTxt = (el.textContent || '0').replace(/[^0-9.\-]/g, '');
    var from = Number(fromTxt);
    if (!isFinite(from)) from = 0;
    if (REDUCE || dur <= 0 || from === target) { el.textContent = target.toLocaleString(); return; }
    var start = performance.now();
    function step(now) {
      var t = Math.min(1, (now - start) / dur);
      var k = 1 - Math.pow(1 - t, 3); // easeOutCubic
      var v = Math.round(from + (target - from) * k);
      el.textContent = v.toLocaleString();
      if (t < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  function bootCounters() {
    document.querySelectorAll('#sa-wrap [data-counter]').forEach(function (el) {
      animateCounter(el, el.getAttribute('data-target'), 800);
    });
  }

  /* ---------- DOM helpers ---------- */
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
    return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]);
  });}
  function fmtTs(ts) {
    if (!ts) return '';
    var d = new Date(ts.replace(' ', 'T'));
    if (isNaN(d.getTime())) return ts;
    var diff = (Date.now() - d.getTime()) / 1000;
    if (diff < 60) return Math.floor(diff) + 's ago';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
  }
  function dayLabel(iso) {
    var d = new Date(iso + 'T00:00:00');
    if (isNaN(d.getTime())) return iso.slice(5);
    return d.toLocaleDateString(undefined, { weekday: 'short' });
  }

  /* ---------- last-known feed ids for fade-in of new rows ---------- */
  var lastFeedIds = new Set();

  /* ---------- AJAX ---------- */
  function fetchJSON(url) {
    return fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'fetch' }})
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); });
  }

  /* ---------- KPI refresh ---------- */
  function refreshKpis() {
    return fetchJSON('ajax/dashbord/sa_kpis.php').then(function (d) {
      // orgs
      var orgsKpi = document.querySelector('.sa-kpi[data-kpi="orgs"] [data-counter]');
      if (orgsKpi) animateCounter(orgsKpi, d.orgs.active, 600);
      var orgsSub = document.getElementById('sa-kpi-orgs-sub');
      if (orgsSub) orgsSub.innerHTML = '<span>' + esc(d.orgs.active) + ' active &middot; ' + esc(d.orgs.total) + ' total</span>';

      // users
      var usersKpi = document.querySelector('.sa-kpi[data-kpi="users"] [data-counter]');
      if (usersKpi) animateCounter(usersKpi, d.users.active, 600);

      // audit
      var auditKpi = document.querySelector('.sa-kpi[data-kpi="audit"] [data-counter]');
      if (auditKpi) animateCounter(auditKpi, d.audit.last_24h, 600);
      var deltaEl = document.getElementById('sa-kpi-audit-delta');
      if (deltaEl) {
        var dp = Number(d.audit.delta_pct) || 0;
        deltaEl.classList.remove('up','down','flat');
        var cls = dp > 0.5 ? 'up' : (dp < -0.5 ? 'down' : 'flat');
        deltaEl.classList.add(cls);
        var arrow = cls === 'up' ? '▲' : (cls === 'down' ? '▼' : '◆');
        deltaEl.textContent = arrow + ' ' + (dp >= 0 ? '+' : '') + dp.toFixed(1) + '% vs prior 24h';
      }

      // doctors
      var docsKpi = document.querySelector('.sa-kpi[data-kpi="doctors"] [data-counter]');
      if (docsKpi) animateCounter(docsKpi, d.doctors.active, 600);

      // governance
      var govKpis = document.querySelectorAll('.sa-kpi[data-kpi="governance"] [data-counter]');
      if (govKpis.length === 2) {
        animateCounter(govKpis[0], d.governance.roles, 600);
        animateCounter(govKpis[1], d.governance.menus, 600);
      }

      // db health (compact summary in card)
      var totalRows = (d.db_health.tables || []).reduce(function (a, t) { return a + (t.rows || 0); }, 0);
      var dbhVal = document.getElementById('sa-kpi-dbh-value');
      if (dbhVal) {
        // FIX_B_2203: render with data-target so re-scans never animate to NaN,
        // and preserve current value text so animateCounter eases from it.
        var prev = dbhVal.querySelector('[data-counter]');
        var fromTxt = prev ? prev.textContent : '0';
        dbhVal.innerHTML = '<span data-counter data-target="' + totalRows + '">' + esc(fromTxt) + '</span>';
        animateCounter(dbhVal.firstChild, totalRows, 700);
      }
      var dbhSub = document.getElementById('sa-kpi-dbh-sub');
      if (dbhSub) dbhSub.innerHTML = '<span>' + (d.db_health.tables || []).length + ' major tables</span>';

      // stash for modal + sparkline
      SADashboard._dbHealth = d.db_health;
      SADashboard._users    = d.users;
      renderUserSpark(d.users.by_role || []);

      stampRefresh();
    }).catch(function (e) {
      console.warn('[sa] kpis failed', e);
    });
  }

  /* ---------- user-role sparkline (ApexCharts if available, else SVG fallback) ---------- */
  var sparkInstance = null;
  function renderUserSpark(byRole) {
    var el = document.getElementById('sa-spark-users');
    if (!el) return;
    var data = byRole.map(function (r) { return r.count; });
    if (typeof ApexCharts === 'undefined') {
      // SVG fallback bar mini
      el.innerHTML = '';
      var max = Math.max.apply(null, data.concat([1]));
      var w = el.clientWidth || 200, h = 38, n = data.length, gap = 2;
      var bw = Math.max(2, Math.floor((w - gap * (n - 1)) / Math.max(1, n)));
      var svg = '<svg width="' + w + '" height="' + h + '" viewBox="0 0 ' + w + ' ' + h + '" xmlns="http://www.w3.org/2000/svg">';
      data.forEach(function (v, i) {
        var bh = Math.max(2, Math.round((v / max) * (h - 4)));
        svg += '<rect x="' + (i * (bw + gap)) + '" y="' + (h - bh) + '" width="' + bw + '" height="' + bh + '" rx="1" fill="#b88a2a"/>';
      });
      svg += '</svg>';
      el.innerHTML = svg;
      return;
    }
    if (sparkInstance) { try { sparkInstance.destroy(); } catch (e) {} }
    sparkInstance = new ApexCharts(el, {
      chart: { type: 'bar', height: 38, sparkline: { enabled: true }, animations: { enabled: !REDUCE } },
      series: [{ name: 'users', data: data }],
      plotOptions: { bar: { columnWidth: '60%', borderRadius: 1 } },
      colors: ['#b88a2a'],
      tooltip: {
        enabled: true,
        x: { formatter: function (_, opts) { return byRole[opts.dataPointIndex] ? byRole[opts.dataPointIndex].label : ''; } },
        y: { formatter: function (v) { return v + ' users'; } }
      }
    });
    sparkInstance.render();
  }

  /* ---------- audit feed ---------- */
  function refreshFeed() {
    return fetchJSON('ajax/dashbord/sa_audit_feed.php?limit=20').then(function (d) {
      var ul = document.getElementById('sa-feed-list');
      if (!ul) return;
      var newIds = new Set((d.rows || []).map(function (r) { return r.id; }));
      var firstLoad = (lastFeedIds.size === 0);
      ul.innerHTML = '';
      (d.rows || []).forEach(function (r) {
        var entityHref = 'audit_log.php' + (r.entity_id ? ('?entity_id=' + r.entity_id) : '');
        var li = document.createElement('li');
        li.setAttribute('data-id', r.id);
        if (!firstLoad && !lastFeedIds.has(r.id)) li.classList.add('sa-new');
        li.innerHTML =
          '<span class="sa-ts" title="' + esc(r.ts) + '">' + esc(fmtTs(r.ts)) + '</span>' +
          '<div>' +
            '<div class="sa-msg"><b>' + esc(r.user_name) + '</b> ' + esc(r.action) + 'd <b>' + esc(r.entity) + '</b>' + (r.entity_id ? ' #' + r.entity_id : '') + '</div>' +
            '<div class="sa-meta">' + esc(r.org_name) + ' &middot; ' + esc(r.module) + (r.ip ? ' &middot; ' + esc(r.ip) : '') + '</div>' +
          '</div>' +
          '<span class="sa-action a-' + esc(r.action) + '">' + esc(r.action) + '</span>';
        li.addEventListener('click', function () { window.location.href = entityHref; });
        ul.appendChild(li);
      });
      if (!d.rows || d.rows.length === 0) {
        ul.innerHTML = '<li class="sa-empty" style="grid-template-columns:1fr; text-align:center;">No audit events yet.</li>';
      }
      lastFeedIds = newIds;
      stampRefresh();
    }).catch(function (e) {
      console.warn('[sa] feed failed', e);
      var ul = document.getElementById('sa-feed-list');
      if (ul) ul.innerHTML = '<li class="sa-error">Unable to load audit feed.</li>';
    });
  }

  /* ---------- heatmap ---------- */
  function refreshHeatmap() {
    return fetchJSON('ajax/dashbord/sa_doctor_heatmap.php?days=7&top=12').then(function (d) {
      var days = d.days || [];
      var rows = d.rows || [];
      var max  = Math.max(1, Number(d.max_count) || 1);

      var daysEl = document.getElementById('sa-heat-days');
      var axis   = document.getElementById('sa-heat-axis');
      var grid   = document.getElementById('sa-heat-grid');
      if (!grid) return;
      axis.style.setProperty('--sa-heat-days', days.length);
      grid.style.setProperty('--sa-heat-days', days.length);
      daysEl.innerHTML = days.map(function (iso) {
        return '<span title="' + esc(iso) + '">' + esc(dayLabel(iso)) + '</span>';
      }).join('');

      var tpl = d.href_template || 'doctorTimeSlots.php?doc_id={doc_id}&date={date}';

      grid.innerHTML = rows.map(function (row) {
        var cells = row.cells.map(function (c) {
          var ratio = c.count / max;
          var lvl = c.count === 0 ? 0 : (ratio < .25 ? 1 : ratio < .5 ? 2 : ratio < .8 ? 3 : 4);
          var href = tpl.replace('{doc_id}', row.doc_id).replace('{date}', c.date);
          var title = row.doctor_name + ' · ' + c.date + ' · ' + c.count + ' appt' + (c.count === 1 ? '' : 's');
          return '<a class="sa-cell" data-level="' + lvl + '" href="' + esc(href) + '" title="' + esc(title) + '" aria-label="' + esc(title) + '">' + (c.count || '') + '</a>';
        }).join('');
        return '<div class="sa-heat-row">' +
                 '<div class="sa-heat-name" title="' + esc(row.doctor_name) + '">' +
                   esc(row.doctor_name) +
                   '<small>' + esc(row.org_name) + '</small>' +
                 '</div>' +
                 '<div class="sa-heat-cells">' + cells + '</div>' +
               '</div>';
      }).join('');

      if (rows.length === 0) {
        grid.innerHTML = '<div class="sa-empty">No doctor activity in the last 7 days.</div>';
      }
      stampRefresh();
    }).catch(function (e) {
      console.warn('[sa] heatmap failed', e);
      var grid = document.getElementById('sa-heat-grid');
      if (grid) grid.innerHTML = '<div class="sa-error">Unable to load heatmap.</div>';
    });
  }

  /* ---------- governance changes ---------- */
  function refreshGovernance() {
    return fetchJSON('ajax/dashbord/sa_governance_changes.php?limit=10').then(function (d) {
      var tb = document.getElementById('sa-gov-body');
      if (!tb) return;
      if (!d.rows || d.rows.length === 0) {
        tb.innerHTML = '<tr><td colspan="6" class="sa-empty">No governance changes recently.</td></tr>';
        return;
      }
      tb.innerHTML = d.rows.map(function (r) {
        var href = r.href + (r.entity_id ? ('?entity_id=' + r.entity_id) : '');
        return '<tr data-href="' + esc(href) + '">' +
          '<td><span class="sa-ts">' + esc(fmtTs(r.ts)) + '</span></td>' +
          '<td>' + esc(r.user_name) + '</td>' +
          '<td>' + esc(r.org_name) + '</td>' +
          '<td><span class="sa-action a-' + esc(r.action) + '">' + esc(r.action) + '</span></td>' +
          '<td><span class="sa-ent-tag">' + esc(r.entity) + (r.entity_id ? ' #' + r.entity_id : '') + '</span></td>' +
          '<td>' + esc(r.module) + '</td>' +
        '</tr>';
      }).join('');
      Array.prototype.forEach.call(tb.querySelectorAll('tr[data-href]'), function (tr) {
        tr.addEventListener('click', function () { window.location.href = tr.getAttribute('data-href'); });
      });
    }).catch(function (e) {
      console.warn('[sa] governance failed', e);
    });
  }

  /* ---------- Birthday + Revisit lists ---------- */
  var _saBirthdayPopupShown = false;
  function renderSaBirthdayRows(items) {
    var countEl = document.getElementById('sa-birthday-count');
    if (countEl) countEl.textContent = (items.length || 0) + ' patients';
    var tb = document.getElementById('sa-birthday-body');
    if (!tb) return;
    if (!items.length) {
      tb.innerHTML = '<tr><td colspan="4" class="sa-empty">No upcoming birthdays found.</td></tr>';
      return;
    }
    tb.innerHTML = items.map(function (r) {
      return '<tr>'
        + '<td><strong>' + esc(r.patient_name) + '</strong><br><small>' + esc(r.patient_id) + '</small></td>'
        + '<td>' + esc(r.mobile_number) + '</td>'
        + '<td><span class="sa-ent-tag">' + esc(r.days_label) + '</span><br><small>' + esc(r.next_birthday_display) + '</small></td>'
        + '<td>' + esc(r.turning_age) + '</td>'
        + '</tr>';
    }).join('');
  }
  function renderSaRevisitRows(items) {
    var countEl = document.getElementById('sa-revisit-count');
    if (countEl) countEl.textContent = (items.length || 0) + ' patients';
    var tb = document.getElementById('sa-revisit-body');
    if (!tb) return;
    if (!items.length) {
      tb.innerHTML = '<tr><td colspan="4" class="sa-empty">No upcoming revisits found.</td></tr>';
      return;
    }
    tb.innerHTML = items.map(function (r) {
      return '<tr>'
        + '<td><strong>' + esc(r.patient_name) + '</strong><br><small>' + esc(r.patient_id) + '</small></td>'
        + '<td>' + esc(r.mobile_number) + '</td>'
        + '<td>' + esc(r.doctor_name) + '</td>'
        + '<td><span class="sa-ent-tag">' + esc(r.days_label) + '</span><br><small>' + esc(r.revisit_date_display) + '</small></td>'
        + '</tr>';
    }).join('');
  }
  function loadSaDashboardLists() {
    return fetchJSON('ajax/dashbord/get_dashboard_lists.php').then(function (d) {
      renderSaBirthdayRows(d.birthdays || []);
      renderSaRevisitRows(d.revisits || []);
      if (!_saBirthdayPopupShown && d.today_birthdays && d.today_birthdays.length) {
        _saBirthdayPopupShown = true;
        var el = document.createElement('div');
        var rows = d.today_birthdays.map(function (r) {
          return '<tr><td>' + esc(r.patient_name) + '</td><td>' + esc(r.mobile_number) + '</td><td>' + esc(r.turning_age) + '</td></tr>';
        }).join('');
        el.innerHTML = '<div style="text-align:left"><p class="mb-2"><strong>Today\'s birthday patients</strong></p>'
          + '<table class="table table-sm mb-0"><thead><tr><th>Patient</th><th>Mobile</th><th>Age</th></tr></thead>'
          + '<tbody>' + rows + '</tbody></table></div>';
        if (window.swal) {
          swal({ title: 'Birthday Wishes Reminder', content: el, icon: 'info' });
        }
      }
    }).catch(function (e) {
      console.warn('[sa] dashboard lists failed', e);
      var tb1 = document.getElementById('sa-birthday-body');
      var tb2 = document.getElementById('sa-revisit-body');
      if (tb1) tb1.innerHTML = '<tr><td colspan="4" class="sa-empty">Unable to load.</td></tr>';
      if (tb2) tb2.innerHTML = '<tr><td colspan="4" class="sa-empty">Unable to load.</td></tr>';
    });
  }

  /* ---------- DB health modal ---------- */
  function openDbHealth() {
    var modal = document.getElementById('sa-dbh-modal');
    if (!modal) return;
    var body = document.getElementById('sa-dbh-body');
    var data = (SADashboard._dbHealth && SADashboard._dbHealth.tables) || [];
    if (data.length === 0) {
      body.innerHTML = '<tr><td colspan="2" class="sa-empty">loading…</td></tr>';
    } else {
      body.innerHTML = data.map(function (t) {
        return '<tr><td><code>' + esc(t.table) + '</code></td><td class="num">' + Number(t.rows || 0).toLocaleString() + '</td></tr>';
      }).join('');
    }
    document.getElementById('sa-dbh-foot').textContent = 'server time: ' + ((SADashboard._dbHealth && SADashboard._dbHealth.server_now) || '—');

    // Use Bootstrap 5 if present, else manual show
    if (window.bootstrap && window.bootstrap.Modal) {
      (new window.bootstrap.Modal(modal)).show();
    } else if (window.jQuery && window.jQuery.fn.modal) {
      window.jQuery(modal).modal('show');
    } else {
      // crude fallback
      modal.style.display = 'block';
      modal.classList.add('show');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');
      var bd = document.createElement('div'); bd.className = 'modal-backdrop fade show';
      document.body.appendChild(bd);
      bd.addEventListener('click', function () {
        modal.style.display = 'none'; modal.classList.remove('show');
        bd.remove(); document.body.classList.remove('modal-open');
      });
    }
  }

  /* ---------- clock + refresh stamp ---------- */
  function tickClock() {
    var el = document.getElementById('sa-clock');
    if (!el) return;
    var now = new Date();
    var opts = { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false };
    // FIX_B_2202: preserve IST suffix that the server-rendered initial string carried.
    el.textContent = now.toLocaleString(undefined, opts) + ' IST';
  }
  function stampRefresh() {
    var el = document.getElementById('sa-last-refresh');
    if (el) el.textContent = new Date().toLocaleTimeString();
  }

  /* ---------- boot ---------- */
  function boot() {
    bootCounters();
    Promise.all([refreshKpis(), refreshFeed(), refreshHeatmap(), refreshGovernance(), loadSaDashboardLists()]);

    // 30s polling for feed + governance; 60s for kpis + heatmap
    setInterval(refreshFeed, 30000);
    setInterval(refreshGovernance, 30000);
    setInterval(refreshKpis, 60000);
    setInterval(refreshHeatmap, 120000);
    setInterval(loadSaDashboardLists, 120000);

    tickClock();
    setInterval(tickClock, 30000);
  }

  window.SADashboard = {
    boot: boot,
    refreshKpis: refreshKpis,
    refreshFeed: refreshFeed,
    refreshHeatmap: refreshHeatmap,
    openDbHealth: openDbHealth,
    loadDashboardLists: loadSaDashboardLists,
    _dbHealth: null,
    _users: null,
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
