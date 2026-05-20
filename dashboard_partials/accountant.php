<?php
/**
 * B-2050 — Accountant Dashboard partial
 *
 * Rendered when role_id = 5 (Accountant) logs in.
 * Self-contained: pulls in ajax/header.php so it can also be navigated to
 * directly during dev/QA. dashboard.php is expected to `include` this file
 * for accountants in a future small routing edit (constitutional: scoped).
 *
 * Aesthetic: Sovereign Institutional (navy / cream / gold) — tokens defined
 * inline below under body[data-role-dashboard="accountant"] so the partial
 * does not depend on assets/h360-ui/h360-ui.css existing on this branch.
 */

if (!isset($conn)) {
    require_once(__DIR__ . "/../ajax/header.php");
}

$SessionUserId = (int)($_SESSION['security_id'] ?? 0);
$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

// Greeting
$nameRow  = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT admin_name FROM security WHERE security_id='$SessionUserId' LIMIT 1")) ?: [];
$adminName = $nameRow['admin_name'] ?? 'Accountant';
$hour = (int)date('G');
$greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>
<script>
  // Mark body so all our scoped styles + JS apply only here.
  document.body && document.body.setAttribute('data-role-dashboard', 'accountant');
</script>

<style>
/* ---------- Sovereign Institutional tokens (scoped) ---------- */
body[data-role-dashboard="accountant"] {
  --acc-navy:        #0E1A2B;
  --acc-navy-2:      #16243A;
  --acc-navy-3:      #1F324E;
  --acc-cream:       #F6F1E4;
  --acc-cream-2:     #FBF8EE;
  --acc-gold:        #B8893B;
  --acc-gold-2:      #D6A95C;
  --acc-ink:         #0B1424;
  --acc-ink-2:       #2B3A55;
  --acc-muted:       #7C8AA3;
  --acc-line:        #E2D9C2;
  --acc-line-2:      #1F2C44;
  --acc-pos:         #2F7D32;
  --acc-neg:         #B5371E;
  --acc-warn-bg:     #FFF3E0;
  --acc-warn-bd:     #F5A623;
  --acc-shadow:      0 1px 0 rgba(11,20,36,.04), 0 12px 28px -16px rgba(11,20,36,.18);
  --acc-shadow-lift: 0 6px 0 rgba(11,20,36,.04), 0 22px 40px -18px rgba(11,20,36,.28);
  --acc-mono:        ui-monospace, "JetBrains Mono", "SFMono-Regular", Menlo, Consolas, monospace;
  --acc-sans:        Inter, "Geist", system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

body[data-role-dashboard="accountant"] .acc-wrap {
  background: var(--acc-cream-2);
  min-height: calc(100vh - 60px);
  padding: 24px 28px 56px;
  font-family: var(--acc-sans);
  color: var(--acc-ink);
}

body[data-role-dashboard="accountant"] .acc-chrome {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--acc-navy);
  color: var(--acc-cream);
  padding: 14px 22px;
  border-radius: 10px 10px 0 0;
  border: 1px solid var(--acc-line-2);
  border-bottom: 2px solid var(--acc-gold);
  font-family: var(--acc-mono);
  font-size: 12px; letter-spacing: .14em; text-transform: uppercase;
}
body[data-role-dashboard="accountant"] .acc-chrome .acc-chip {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 4px 10px; border: 1px solid var(--acc-gold); border-radius: 999px;
  color: var(--acc-gold-2);
}
body[data-role-dashboard="accountant"] .acc-chrome .acc-status-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #4ADE80; box-shadow: 0 0 0 3px rgba(74,222,128,.18);
  display: inline-block;
}
body[data-role-dashboard="accountant"] .acc-greeting {
  background: #fff;
  border: 1px solid var(--acc-line);
  border-top: none;
  padding: 22px 26px;
  border-radius: 0 0 10px 10px;
  display: flex; align-items: end; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
  margin-bottom: 22px;
  box-shadow: var(--acc-shadow);
}
body[data-role-dashboard="accountant"] .acc-greeting h1 {
  font-family: var(--acc-sans);
  font-size: 26px; font-weight: 700; margin: 0;
  color: var(--acc-navy);
  letter-spacing: -0.01em;
}
body[data-role-dashboard="accountant"] .acc-greeting .acc-meta {
  font-family: var(--acc-mono); font-size: 11px;
  color: var(--acc-muted); letter-spacing: .12em; text-transform: uppercase;
  margin-top: 4px;
}

/* ---- KPI strip ---- */
body[data-role-dashboard="accountant"] .acc-kpi-grid {
  display: grid; gap: 16px;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  margin-bottom: 22px;
}
@media (max-width: 1100px) { body[data-role-dashboard="accountant"] .acc-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { body[data-role-dashboard="accountant"] .acc-kpi-grid { grid-template-columns: 1fr; } }

body[data-role-dashboard="accountant"] .acc-kpi {
  position: relative;
  background: #fff;
  border: 1px solid var(--acc-line);
  border-radius: 10px;
  padding: 18px 20px 20px;
  cursor: pointer;
  transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
  box-shadow: var(--acc-shadow);
  overflow: hidden;
}
body[data-role-dashboard="accountant"] .acc-kpi:hover {
  transform: translateY(-2px);
  box-shadow: var(--acc-shadow-lift);
  border-color: var(--acc-gold);
}
body[data-role-dashboard="accountant"] .acc-kpi::before {
  content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
  background: var(--acc-gold);
}
body[data-role-dashboard="accountant"] .acc-kpi .acc-kpi-label {
  font-family: var(--acc-mono); font-size: 11px;
  letter-spacing: .14em; text-transform: uppercase;
  color: var(--acc-muted);
  display: flex; align-items: center; gap: 8px;
}
body[data-role-dashboard="accountant"] .acc-kpi .acc-kpi-value {
  font-size: 30px; font-weight: 700; color: var(--acc-navy);
  margin: 8px 0 6px; letter-spacing: -0.01em;
  line-height: 1.1; font-variant-numeric: tabular-nums;
}
body[data-role-dashboard="accountant"] .acc-kpi .acc-kpi-sub {
  font-size: 12px; color: var(--acc-ink-2);
}
body[data-role-dashboard="accountant"] .acc-kpi .acc-delta-pos { color: var(--acc-pos); font-weight: 600; }
body[data-role-dashboard="accountant"] .acc-kpi .acc-delta-neg { color: var(--acc-neg); font-weight: 600; }

/* ---- Section grid ---- */
body[data-role-dashboard="accountant"] .acc-grid {
  display: grid; gap: 16px;
  grid-template-columns: 2fr 1fr;
  margin-bottom: 22px;
}
@media (max-width: 1100px) { body[data-role-dashboard="accountant"] .acc-grid { grid-template-columns: 1fr; } }

body[data-role-dashboard="accountant"] .acc-card {
  background: #fff;
  border: 1px solid var(--acc-line);
  border-radius: 10px;
  box-shadow: var(--acc-shadow);
  overflow: hidden;
}
body[data-role-dashboard="accountant"] .acc-card-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid var(--acc-line);
  background: #fff;
}
body[data-role-dashboard="accountant"] .acc-card-head h3 {
  margin: 0; font-size: 14px; font-weight: 700; color: var(--acc-navy);
  letter-spacing: .02em;
}
body[data-role-dashboard="accountant"] .acc-card-head .acc-tag {
  font-family: var(--acc-mono); font-size: 10px; letter-spacing: .14em;
  text-transform: uppercase; color: var(--acc-muted);
}
body[data-role-dashboard="accountant"] .acc-card-body { padding: 16px 18px; }

/* ---- Tables ---- */
body[data-role-dashboard="accountant"] .acc-table {
  width: 100%; border-collapse: collapse; font-size: 13px;
}
body[data-role-dashboard="accountant"] .acc-table th {
  text-align: left; font-family: var(--acc-mono);
  font-size: 10px; letter-spacing: .12em; text-transform: uppercase;
  color: var(--acc-muted); font-weight: 600;
  padding: 10px 12px; border-bottom: 1px solid var(--acc-line);
  background: var(--acc-cream);
}
body[data-role-dashboard="accountant"] .acc-table td {
  padding: 12px; border-bottom: 1px solid var(--acc-line);
  color: var(--acc-ink); font-variant-numeric: tabular-nums;
}
body[data-role-dashboard="accountant"] .acc-table tr {
  cursor: pointer; transition: background-color .18s ease;
}
body[data-role-dashboard="accountant"] .acc-table tbody tr:hover {
  background: var(--acc-cream-2);
}
body[data-role-dashboard="accountant"] .acc-table .acc-aged {
  background: var(--acc-warn-bg);
}
body[data-role-dashboard="accountant"] .acc-table .acc-aged td:first-child {
  border-left: 3px solid var(--acc-warn-bd);
}
@media (prefers-reduced-motion: no-preference) {
  body[data-role-dashboard="accountant"] .acc-table .acc-aged {
    animation: accAgedPulse 4s ease-in-out infinite;
  }
}
@keyframes accAgedPulse {
  0%, 100% { background: var(--acc-warn-bg); }
  50%      { background: #FFE7BD; }
}

/* ---- Top-sources tabs ---- */
body[data-role-dashboard="accountant"] .acc-tabs {
  display: inline-flex; background: var(--acc-cream); border-radius: 8px;
  padding: 3px; border: 1px solid var(--acc-line);
}
body[data-role-dashboard="accountant"] .acc-tab {
  padding: 6px 14px; font-size: 12px; font-family: var(--acc-mono);
  letter-spacing: .08em; text-transform: uppercase;
  color: var(--acc-ink-2); cursor: pointer; border-radius: 6px;
  transition: all .2s ease; border: none; background: transparent;
}
body[data-role-dashboard="accountant"] .acc-tab.active {
  background: var(--acc-navy); color: var(--acc-cream);
}

/* ---- Quick actions ---- */
body[data-role-dashboard="accountant"] .acc-actions {
  display: grid; gap: 12px;
  grid-template-columns: repeat(5, 1fr);
}
@media (max-width: 900px) { body[data-role-dashboard="accountant"] .acc-actions { grid-template-columns: repeat(2, 1fr); } }
body[data-role-dashboard="accountant"] .acc-action {
  display: flex; align-items: center; gap: 12px;
  background: #fff; border: 1px solid var(--acc-line);
  border-radius: 10px; padding: 14px 16px; cursor: pointer;
  text-decoration: none; color: var(--acc-navy);
  transition: all .25s ease;
  box-shadow: var(--acc-shadow);
  font-weight: 600;
}
body[data-role-dashboard="accountant"] .acc-action:hover {
  transform: translateY(-2px);
  border-color: var(--acc-gold);
  background: var(--acc-cream);
  color: var(--acc-navy);
}
body[data-role-dashboard="accountant"] .acc-action .acc-action-icon {
  width: 38px; height: 38px; border-radius: 8px;
  background: var(--acc-navy); color: var(--acc-gold-2);
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 16px;
}

/* ---- Refund + audit panels ---- */
body[data-role-dashboard="accountant"] .acc-mini-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0; border-bottom: 1px dashed var(--acc-line);
  font-size: 13px;
}
body[data-role-dashboard="accountant"] .acc-mini-row:last-child { border-bottom: none; }
body[data-role-dashboard="accountant"] .acc-mini-row .acc-mini-meta {
  font-family: var(--acc-mono); font-size: 10px; color: var(--acc-muted);
  letter-spacing: .08em; text-transform: uppercase;
}

/* ---- Donut center ---- */
body[data-role-dashboard="accountant"] .acc-donut-wrap {
  position: relative; min-height: 280px;
}
body[data-role-dashboard="accountant"] #accDonutCenter {
  pointer-events: none;
}

/* ---- Skeleton shimmer ---- */
body[data-role-dashboard="accountant"] .acc-skel {
  background: linear-gradient(90deg, #ECE6D2 0%, #F8F3E2 40%, #ECE6D2 80%);
  background-size: 200% 100%;
  animation: accShimmer 1.4s ease-in-out infinite;
  border-radius: 4px;
}
@keyframes accShimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
@media (prefers-reduced-motion: reduce) {
  body[data-role-dashboard="accountant"] .acc-skel { animation: none; }
  body[data-role-dashboard="accountant"] .acc-kpi { transition: none; }
  body[data-role-dashboard="accountant"] .acc-action { transition: none; }
}

/* ---- Reused-widget callouts (parity with dashboard.php) ---- */
body[data-role-dashboard="accountant"] .acc-legacy {
  display: inline-flex; align-items: center; gap: 6px;
  font-family: var(--acc-mono); font-size: 9px; letter-spacing: .12em;
  text-transform: uppercase; color: var(--acc-gold);
  border: 1px solid var(--acc-gold); padding: 2px 8px; border-radius: 999px;
}
</style>

<div class="main-content acc-wrap">

  <!-- Chrome bar (Bloomberg/FactSet terminal style) -->
  <div class="acc-chrome">
    <div>FINANCE&nbsp;CONSOLE&nbsp;&middot;&nbsp;ACCOUNTANT&nbsp;VIEW</div>
    <div>
      <span class="acc-chip"><span class="acc-status-dot"></span> LIVE</span>
      <span style="margin-left:14px;" id="accChromeTime"><?= date('D, d M Y &middot; H:i') ?></span>
    </div>
  </div>

  <!-- Greeting band -->
  <div class="acc-greeting">
    <div>
      <h1><?= htmlspecialchars($greet) ?>, <?= htmlspecialchars($adminName) ?></h1>
      <div class="acc-meta">FINANCIAL OVERVIEW &middot; ORG #<?= $SessionOrgId ?> &middot; FY <?= date('Y') ?></div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="RevenueReport.php" class="acc-action" style="margin:0;">
        <span class="acc-action-icon"><i class="fas fa-chart-line"></i></span>
        Open Revenue Report
      </a>
    </div>
  </div>

  <!-- KPI hero strip -->
  <div class="acc-kpi-grid">
    <div class="acc-kpi" data-href="RevenueReport.php?range=today" role="button" tabindex="0" aria-label="Today's Revenue">
      <div class="acc-kpi-label"><i class="fas fa-rupee-sign"></i> Today's Revenue</div>
      <div class="acc-kpi-value" data-counter="0" id="kpiRevenueToday">&#8377;0</div>
      <div class="acc-kpi-sub"><span id="kpiRevenueTodayCount">0</span> bills today &middot; <span class="acc-legacy">RETAINED · APPOINTMENTS</span></div>
    </div>
    <div class="acc-kpi" data-href="RevenueReport.php?range=week" role="button" tabindex="0" aria-label="This Week's Revenue">
      <div class="acc-kpi-label"><i class="fas fa-calendar-week"></i> This Week's Revenue</div>
      <div class="acc-kpi-value" id="kpiRevenueWeek">&#8377;0</div>
      <div class="acc-kpi-sub" id="kpiRevenueWeekDelta">vs last week</div>
    </div>
    <div class="acc-kpi" data-href="billing_report.php" role="button" tabindex="0" aria-label="Outstanding Bills">
      <div class="acc-kpi-label"><i class="fas fa-file-invoice-dollar"></i> Outstanding Bills</div>
      <div class="acc-kpi-value" id="kpiOutstandingCount">0</div>
      <div class="acc-kpi-sub"><span id="kpiOutstandingAmount">&#8377;0</span> unpaid balance</div>
    </div>
    <div class="acc-kpi" data-href="refunds.php" role="button" tabindex="0" aria-label="Refunds Today">
      <div class="acc-kpi-label"><i class="fas fa-undo-alt"></i> Refunds Today</div>
      <div class="acc-kpi-value" id="kpiRefundsCount">0</div>
      <div class="acc-kpi-sub"><span id="kpiRefundsAmount">&#8377;0</span> refunded</div>
    </div>
  </div>

  <!-- Revenue trend + payment donut -->
  <div class="acc-grid">
    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Revenue Trend &middot; 30 Days (Stacked)</h3>
        <span class="acc-tag">CONSULTATION · TESTS · MEDICINE</span>
      </div>
      <div class="acc-card-body" style="padding:8px 8px 0;">
        <div id="accRevenueTrend" style="min-height:300px;"></div>
      </div>
    </div>
    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Today's Payments by Method</h3>
        <span class="acc-tag">DONUT</span>
      </div>
      <div class="acc-card-body acc-donut-wrap">
        <div id="accDonut" style="min-height:280px;"></div>
        <div id="accDonutCenter" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">
          <div style="font-family:var(--acc-mono);font-size:10px;letter-spacing:.14em;color:var(--acc-muted);text-transform:uppercase;">Total today</div>
          <div id="accDonutTotal" style="font-size:22px;font-weight:700;color:var(--acc-navy);font-variant-numeric:tabular-nums;">&#8377;0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top sources + Outstanding queue -->
  <div class="acc-grid">
    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Outstanding Bills Queue</h3>
        <span class="acc-tag">AGED &gt; 7 DAYS HIGHLIGHTED</span>
      </div>
      <div class="acc-card-body" style="padding:0;">
        <table class="acc-table" id="accOutstanding">
          <thead>
            <tr><th>Bill #</th><th>Patient</th><th>Type</th><th style="text-align:right;">Balance</th><th>Age</th><th></th></tr>
          </thead>
          <tbody>
            <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--acc-muted);">Loading…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Top Revenue Sources <span class="acc-legacy" style="margin-left:8px;">RETAINED · TOP 5</span></h3>
        <div class="acc-tabs" role="tablist">
          <button class="acc-tab active" data-pane="services" role="tab">Services</button>
          <button class="acc-tab" data-pane="tests" role="tab">Tests</button>
        </div>
      </div>
      <div class="acc-card-body" style="padding:0;">
        <div id="accTopServices">
          <table class="acc-table"><thead>
            <tr><th>Service</th><th style="text-align:right;">Bills</th><th style="text-align:right;">Revenue</th></tr>
          </thead><tbody><tr><td colspan="3" style="text-align:center;padding:24px;color:var(--acc-muted);">Loading…</td></tr></tbody></table>
        </div>
        <div id="accTopTests" style="display:none;">
          <table class="acc-table"><thead>
            <tr><th>Test</th><th style="text-align:right;">Bills</th><th style="text-align:right;">Revenue</th></tr>
          </thead><tbody><tr><td colspan="3" style="text-align:center;padding:24px;color:var(--acc-muted);">Loading…</td></tr></tbody></table>
        </div>
      </div>
    </div>
  </div>

  <!-- Refund tracker + Audit log -->
  <div class="acc-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Refund Tracker</h3>
        <a href="refunds.php" class="acc-tag" style="text-decoration:none;color:var(--acc-gold);cursor:pointer;">
          PROCESS NEW REFUND &rarr;
        </a>
      </div>
      <div class="acc-card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
          <div style="padding:12px;background:var(--acc-cream);border-radius:8px;">
            <div class="acc-kpi-label">TODAY</div>
            <div style="font-size:20px;font-weight:700;color:var(--acc-navy);font-variant-numeric:tabular-nums;">
              <span id="refTodayAmount">&#8377;0</span>
            </div>
            <div style="font-size:11px;color:var(--acc-muted);"><span id="refTodayCount">0</span> refunds</div>
          </div>
          <div style="padding:12px;background:var(--acc-cream);border-radius:8px;">
            <div class="acc-kpi-label">THIS WEEK</div>
            <div style="font-size:20px;font-weight:700;color:var(--acc-navy);font-variant-numeric:tabular-nums;">
              <span id="refWeekAmount">&#8377;0</span>
            </div>
            <div style="font-size:11px;color:var(--acc-muted);"><span id="refWeekCount">0</span> refunds</div>
          </div>
        </div>
        <div id="accRefundList">
          <div style="text-align:center;padding:18px;color:var(--acc-muted);font-size:12px;">Loading…</div>
        </div>
      </div>
    </div>

    <div class="acc-card">
      <div class="acc-card-head">
        <h3>Recent Audit Trail</h3>
        <a href="audit_log.php" class="acc-tag" style="text-decoration:none;color:var(--acc-gold);cursor:pointer;">
          VIEW ALL &rarr;
        </a>
      </div>
      <div class="acc-card-body">
        <div id="accAuditList">
          <div style="text-align:center;padding:18px;color:var(--acc-muted);font-size:12px;">Loading…</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick actions -->
  <div class="acc-card">
    <div class="acc-card-head">
      <h3>Quick Actions</h3>
      <span class="acc-tag">FINANCIAL OPERATIONS</span>
    </div>
    <div class="acc-card-body">
      <div class="acc-actions">
        <a href="refunds.php" class="acc-action">
          <span class="acc-action-icon"><i class="fas fa-undo-alt"></i></span>
          Process Refund
        </a>
        <a href="dailyreports.php" class="acc-action">
          <span class="acc-action-icon"><i class="fas fa-calendar-day"></i></span>
          Daily Report
        </a>
        <a href="RevenueReport.php" class="acc-action">
          <span class="acc-action-icon"><i class="fas fa-chart-line"></i></span>
          Revenue Report
        </a>
        <a href="billing_report.php" class="acc-action">
          <span class="acc-action-icon"><i class="fas fa-file-invoice"></i></span>
          Billing Report
        </a>
        <a href="audit_log.php" class="acc-action">
          <span class="acc-action-icon"><i class="fas fa-shield-alt"></i></span>
          Audit Log
        </a>
      </div>
    </div>
  </div>

</div>

<!-- ApexCharts (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
<script>
(function() {
  // ---------- Helpers ----------
  function fmtINR(n) {
    n = Number(n) || 0;
    var sign = n < 0 ? '-' : '';
    n = Math.abs(Math.round(n));
    // Indian comma grouping: last 3 then groups of 2.
    var s = String(n);
    if (s.length <= 3) return sign + '₹' + s;
    var last3 = s.slice(-3);
    var rest  = s.slice(0, -3);
    rest = rest.replace(/\B(?=(\d{2})+(?!\d))/g, ',');
    return sign + '₹' + rest + ',' + last3;
  }
  function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
  function animateCounter(el, target, opts) {
    if (!el) return;
    opts = opts || {};
    var dur = opts.duration || 800;
    var prefix = opts.prefix || '';
    var formatter = opts.formatter || function(v){ return prefix + Math.round(v); };
    var t0 = null;
    function step(ts) {
      if (!t0) t0 = ts;
      var p = Math.min(1, (ts - t0) / dur);
      var v = easeOutCubic(p) * target;
      el.textContent = formatter(v);
      if (p < 1) requestAnimationFrame(step);
    }
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      el.textContent = formatter(target);
      return;
    }
    requestAnimationFrame(step);
  }
  function fmtDate(s) {
    if (!s) return '';
    try { return new Date(s).toLocaleString('en-IN', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' }); }
    catch (e) { return s; }
  }

  // ---------- KPI click-through ----------
  document.querySelectorAll('body[data-role-dashboard="accountant"] .acc-kpi[data-href]').forEach(function(card) {
    card.addEventListener('click', function() { window.location.href = card.dataset.href; });
    card.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); window.location.href = card.dataset.href; }
    });
  });

  // ---------- Tabs (Top Sources) ----------
  document.querySelectorAll('body[data-role-dashboard="accountant"] .acc-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var pane = btn.dataset.pane;
      document.querySelectorAll('body[data-role-dashboard="accountant"] .acc-tab').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById('accTopServices').style.display = (pane === 'services') ? '' : 'none';
      document.getElementById('accTopTests').style.display    = (pane === 'tests') ? '' : 'none';
    });
  });

  // ---------- Load: KPI strip + Refund summary (one endpoint each) ----------
  fetch('ajax/dashbord/accountant_revenue_today.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      animateCounter(document.getElementById('kpiRevenueToday'), d.revenue_today || 0,
        { formatter: function(v){ return fmtINR(v); } });
      document.getElementById('kpiRevenueTodayCount').textContent = (d.revenue_today_count || 0);

      animateCounter(document.getElementById('kpiRevenueWeek'), d.revenue_week || 0,
        { formatter: function(v){ return fmtINR(v); } });
      var dEl = document.getElementById('kpiRevenueWeekDelta');
      var pct = d.wow_pct || 0;
      var cls = pct >= 0 ? 'acc-delta-pos' : 'acc-delta-neg';
      var arrow = pct >= 0 ? '▲' : '▼';
      dEl.innerHTML = '<span class="' + cls + '">' + arrow + ' ' + Math.abs(pct).toFixed(1) + '%</span> vs last week';

      document.getElementById('kpiOutstandingCount').textContent = (d.outstanding_count || 0);
      document.getElementById('kpiOutstandingAmount').textContent = fmtINR(d.outstanding_amount || 0);

      document.getElementById('kpiRefundsCount').textContent = (d.refunds_today_count || 0);
      document.getElementById('kpiRefundsAmount').textContent = fmtINR(d.refunds_today_amount || 0);
    })
    .catch(function(){ /* silent */ });

  // ---------- Revenue trend (30-day stacked area) ----------
  fetch('ajax/dashbord/accountant_revenue_trend.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      if (!window.ApexCharts) return;
      var opts = {
        chart: {
          type: 'area', height: 300, stacked: true,
          fontFamily: 'Inter, system-ui, sans-serif',
          toolbar: { show: false },
          animations: { enabled: true, easing: 'easeinout', speed: 700,
            animateGradually: { enabled: true, delay: 50 } },
          events: {
            dataPointSelection: function(event, ctx, cfg) {
              var idx = cfg.dataPointIndex;
              var date = (d.categories || [])[idx];
              if (date) window.location.href = 'dailyreports.php?date=' + encodeURIComponent(date);
            }
          }
        },
        series: [
          { name: 'Consultation', data: d.consultation || [] },
          { name: 'Tests',        data: d.tests        || [] },
          { name: 'Medicine',     data: d.medicine     || [] }
        ],
        xaxis: { categories: d.categories || [], type: 'datetime',
          labels: { style: { colors: '#7C8AA3', fontSize: '10px', fontFamily: 'ui-monospace, monospace' } } },
        yaxis: { labels: { formatter: function(v){ return fmtINR(v); },
          style: { colors: '#7C8AA3', fontSize: '10px', fontFamily: 'ui-monospace, monospace' } } },
        colors: ['#0E1A2B', '#B8893B', '#1F324E'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient',
          gradient: { shadeIntensity: 1, opacityFrom: 0.55, opacityTo: 0.05, stops: [0, 90, 100] } },
        legend: { position: 'top', horizontalAlign: 'right', fontFamily: 'ui-monospace, monospace', fontSize: '11px' },
        tooltip: { y: { formatter: function(v){ return fmtINR(v); } }, theme: 'light' },
        grid: { borderColor: '#E2D9C2', strokeDashArray: 3 }
      };
      var chart = new ApexCharts(document.getElementById('accRevenueTrend'), opts);
      chart.render();
    })
    .catch(function(){ /* silent */ });

  // ---------- Payment-method donut ----------
  fetch('ajax/dashbord/accountant_payment_methods.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      document.getElementById('accDonutTotal').textContent = fmtINR(d.total || 0);
      if (!window.ApexCharts) return;
      var labels = (d.labels && d.labels.length) ? d.labels : ['No data'];
      var amounts = (d.amounts && d.amounts.length) ? d.amounts : [1];
      var counts  = d.counts  || [];
      var opts = {
        chart: {
          type: 'donut', height: 280,
          fontFamily: 'Inter, system-ui, sans-serif',
          animations: { enabled: true, easing: 'easeinout', speed: 700 },
          events: {
            dataPointSelection: function(event, ctx, cfg) {
              var idx = cfg.dataPointIndex;
              var pm = labels[idx];
              if (pm && pm !== 'No data') {
                window.location.href = 'billing_report.php?date=' + encodeURIComponent(new Date().toISOString().slice(0,10)) + '&pm=' + encodeURIComponent(pm);
              }
            }
          }
        },
        series: amounts,
        labels: labels,
        colors: ['#0E1A2B', '#B8893B', '#1F324E', '#D6A95C', '#2B3A55', '#7C8AA3'],
        legend: { position: 'bottom', fontFamily: 'ui-monospace, monospace', fontSize: '11px' },
        plotOptions: { pie: { donut: { size: '72%', labels: { show: false } } } },
        stroke: { width: 2, colors: ['#FBF8EE'] },
        dataLabels: { enabled: false },
        tooltip: {
          y: { formatter: function(v, o){
            var i = o && o.seriesIndex;
            var c = (typeof i === 'number' && counts[i] !== undefined) ? (counts[i] + ' bill' + (counts[i]==1?'':'s') + ' · ') : '';
            return c + fmtINR(v);
          } }
        }
      };
      var chart = new ApexCharts(document.getElementById('accDonut'), opts);
      chart.render();
    })
    .catch(function(){ /* silent */ });

  // ---------- Top sources ----------
  fetch('ajax/dashbord/accountant_top_sources.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      function fillTbl(containerId, rows, urlBuilder) {
        var tbody = document.querySelector('#' + containerId + ' tbody');
        if (!tbody) return;
        if (!rows || !rows.length) {
          tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:18px;color:var(--acc-muted);">No data this month.</td></tr>';
          return;
        }
        tbody.innerHTML = rows.map(function(r) {
          var href = urlBuilder(r);
          return '<tr data-href="' + href + '">' +
            '<td>' + (r.label || '').replace(/</g,'&lt;') + '</td>' +
            '<td style="text-align:right;">' + (r.count || 0) + '</td>' +
            '<td style="text-align:right;font-weight:700;color:var(--acc-navy);">' + fmtINR(r.total) + '</td>' +
          '</tr>';
        }).join('');
        tbody.querySelectorAll('tr[data-href]').forEach(function(tr){
          tr.addEventListener('click', function(){ window.location.href = tr.dataset.href; });
        });
      }
      fillTbl('accTopServices', d.services || [], function(r){
        return 'RevenueReport.php?service=' + encodeURIComponent(r.label);
      });
      fillTbl('accTopTests', d.tests || [], function(r){
        return 'TestReport.php?test=' + encodeURIComponent(r.label);
      });
    })
    .catch(function(){ /* silent */ });

  // ---------- Outstanding bills ----------
  fetch('ajax/dashbord/accountant_outstanding.php?limit=10', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      var tbody = document.querySelector('#accOutstanding tbody');
      if (!tbody) return;
      var rows = d.rows || [];
      if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--acc-pos);">No outstanding bills. All clear.</td></tr>';
        return;
      }
      tbody.innerHTML = rows.map(function(r) {
        var aged = r.aged ? ' acc-aged' : '';
        return '<tr class="' + aged + '" data-href="billview.php?id=' + r.invoice_id + '">' +
          '<td><span style="font-family:var(--acc-mono);">#' + r.invoice_id + '</span></td>' +
          '<td>' + String(r.patient_name || '').replace(/</g,'&lt;') +
            '<div style="font-size:10px;color:var(--acc-muted);font-family:var(--acc-mono);">' + (r.patient_id || '') + '</div></td>' +
          '<td><span style="font-size:11px;color:var(--acc-ink-2);">' + (r.bill_type || '') + '</span></td>' +
          '<td style="text-align:right;font-weight:700;color:var(--acc-navy);">' + fmtINR(r.balance) + '</td>' +
          '<td><span style="font-family:var(--acc-mono);font-size:11px;' + (r.aged ? 'color:var(--acc-neg);font-weight:700;' : 'color:var(--acc-muted);') + '">' + r.age_days + 'd</span></td>' +
          '<td style="text-align:right;"><span style="color:var(--acc-gold);font-size:12px;">View &rarr;</span></td>' +
        '</tr>';
      }).join('');
      tbody.querySelectorAll('tr[data-href]').forEach(function(tr){
        tr.addEventListener('click', function(){ window.location.href = tr.dataset.href; });
      });
    })
    .catch(function(){ /* silent */ });

  // ---------- Refund tracker ----------
  fetch('ajax/dashbord/accountant_refunds.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      document.getElementById('refTodayAmount').textContent = fmtINR((d.today && d.today.amount) || 0);
      document.getElementById('refTodayCount').textContent  = (d.today && d.today.count) || 0;
      document.getElementById('refWeekAmount').textContent  = fmtINR((d.week && d.week.amount) || 0);
      document.getElementById('refWeekCount').textContent   = (d.week && d.week.count) || 0;

      var list = document.getElementById('accRefundList');
      var rows = d.recent || [];
      if (!rows.length) {
        list.innerHTML = '<div style="text-align:center;padding:14px;color:var(--acc-muted);font-size:12px;">No recent refunds.</div>';
        return;
      }
      list.innerHTML = rows.map(function(r) {
        return '<div class="acc-mini-row" onclick="window.location.href=\'billview.php?id=' + r.invoice_id + '\'" style="cursor:pointer;">' +
          '<div>' +
            '<div style="font-weight:600;">#' + r.invoice_id + ' &middot; ' + (r.patient_id || '') + '</div>' +
            '<div class="acc-mini-meta">' + (r.bill_type || '') + ' &middot; ' + fmtDate(r.when) + '</div>' +
          '</div>' +
          '<div style="font-weight:700;color:var(--acc-neg);font-variant-numeric:tabular-nums;">-' + fmtINR(r.amount) + '</div>' +
        '</div>';
      }).join('');
    })
    .catch(function(){ /* silent */ });

  // ---------- Audit log ----------
  fetch('ajax/dashbord/accountant_audit_recent.php', { credentials: 'same-origin' })
    .then(function(r){ return r.json(); })
    .then(function(d) {
      var list = document.getElementById('accAuditList');
      var rows = d.rows || [];
      if (!rows.length) {
        list.innerHTML = '<div style="text-align:center;padding:14px;color:var(--acc-muted);font-size:12px;">No recent audit events.</div>';
        return;
      }
      list.innerHTML = rows.map(function(r) {
        var color = r.action === 'delete' ? 'var(--acc-neg)' :
                    r.action === 'create' ? 'var(--acc-pos)' :
                    'var(--acc-gold)';
        return '<div class="acc-mini-row" onclick="window.location.href=\'audit_log.php?entity=' + encodeURIComponent(r.entity) + '\'" style="cursor:pointer;">' +
          '<div>' +
            '<div style="font-weight:600;"><span style="color:' + color + ';text-transform:uppercase;font-family:var(--acc-mono);font-size:10px;letter-spacing:.12em;">' + r.action + '</span> &middot; ' + r.entity + (r.entity_id ? (' #' + r.entity_id) : '') + '</div>' +
            '<div class="acc-mini-meta">' + (r.user_name || 'system') + ' &middot; ' + fmtDate(r.when) + '</div>' +
          '</div>' +
          '<div style="color:var(--acc-muted);font-size:11px;">&rarr;</div>' +
        '</div>';
      }).join('');
    })
    .catch(function(){ /* silent */ });

  // ---------- Live clock ----------
  setInterval(function(){
    var el = document.getElementById('accChromeTime');
    if (el) {
      var d = new Date();
      var pad = function(n){ return String(n).padStart(2,'0'); };
      var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
      var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      el.textContent = days[d.getDay()] + ', ' + pad(d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() + ' · ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }
  }, 60000);
})();
</script>
