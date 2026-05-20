<?php
/**
 * Pharmacist Dashboard — Sovereign Institutional aesthetic
 * Branch: feature/B-2030-dashboard-pharmacist
 * Scope:  role_id = 4 (Pharmacist). Narrow scope = medicine billing flow.
 *
 * Constitutional:
 *   CR-2: scoped under body[data-role-dashboard="pharmacist"]
 *   CR-3: navy/cream/gold tokens
 *   CR-4: WCAG AA contrast
 *
 * Data endpoints (all org-scoped via session):
 *   ajax/dashbord/pharmacist_today_bills.php
 *   ajax/dashbord/pharmacist_pending_rx.php
 *   ajax/dashbord/pharmacist_top_medicines.php
 *   ajax/dashbord/pharmacist_payment_breakdown.php
 *
 * NOTE: this file is included from dashboard.php BEFORE the legacy markup
 *       and short-circuits the response for role_id == 4.
 */

$SessionOrgId = isset($_SESSION['org_id']) ? intval($_SESSION['org_id']) : 0;
?>
<script>
  // Tag <body> as pharmacist so CSS scope kicks in
  document.documentElement.setAttribute('data-role-dashboard-init', 'pharmacist');
  document.addEventListener('DOMContentLoaded', function () {
    document.body.setAttribute('data-role-dashboard', 'pharmacist');
  });
</script>

<style>
  /* ===========================================================
     Pharmacist Dashboard — scoped under body[data-role-dashboard="pharmacist"]
     Sovereign Institutional: navy + cream + gold.
     =========================================================== */
  body[data-role-dashboard="pharmacist"] {
    --navy:        #0b1f3a;
    --navy-2:      #102a4c;
    --navy-3:      #16365f;
    --cream:       #f6f1e4;
    --cream-2:     #efe6cf;
    --gold:        #c2a14b;
    --gold-2:      #d8b969;
    --gold-soft:   rgba(194, 161, 75, 0.12);
    --ink:         #1a1a1a;
    --ink-2:       #4a4a4a;
    --mute:        #7a7a7a;
    --line:        rgba(11, 31, 58, 0.12);
    --ok:          #2e7d4f;
    --warn:        #b8651a;
    --danger:      #9b2a2a;
    --shadow-sm:   0 1px 2px rgba(11,31,58,.06), 0 1px 1px rgba(11,31,58,.04);
    --shadow-md:   0 6px 18px rgba(11,31,58,.10), 0 2px 6px rgba(11,31,58,.06);
    --shadow-lg:   0 14px 36px rgba(11,31,58,.18), 0 4px 12px rgba(11,31,58,.10);
    --r-sm:        4px;
    --r-md:        8px;
    --r-lg:        12px;
    --t-fast:      150ms cubic-bezier(.2,.7,.2,1);
    --t-med:       250ms cubic-bezier(.2,.7,.2,1);
    --t-slow:      450ms cubic-bezier(.2,.7,.2,1);
    --mono:        ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;
    background: linear-gradient(180deg, #f8f4e8 0%, #f1ead6 100%);
  }
  body[data-role-dashboard="pharmacist"] .ph-wrap {
    padding: 18px 22px 40px;
    font-family: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
    color: var(--ink);
  }

  /* ===== Chrome bar ===== */
  body[data-role-dashboard="pharmacist"] .ph-chrome {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--navy);
    color: var(--cream);
    padding: 10px 16px;
    border-radius: var(--r-md);
    box-shadow: var(--shadow-md);
    margin-bottom: 18px;
    border: 1px solid var(--navy-3);
  }
  body[data-role-dashboard="pharmacist"] .ph-chrome .ph-eyebrow {
    font-family: var(--mono);
    font-size: 11px;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--gold-2);
  }
  body[data-role-dashboard="pharmacist"] .ph-chrome .ph-title {
    font-size: 20px; font-weight: 600; letter-spacing: -.01em;
  }
  body[data-role-dashboard="pharmacist"] .ph-chrome .ph-mark {
    color: var(--gold-2); font-weight: 700;
  }
  body[data-role-dashboard="pharmacist"] .ph-chrome .ph-status {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: var(--mono); font-size: 11px; letter-spacing: .1em;
    text-transform: uppercase;
  }
  body[data-role-dashboard="pharmacist"] .ph-dot {
    width: 8px; height: 8px; border-radius: 50%; background: var(--ok);
    box-shadow: 0 0 0 0 rgba(46,125,79,.6);
    animation: ph-pulse 2.2s infinite;
  }
  @keyframes ph-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(46,125,79,.55); }
    70%  { box-shadow: 0 0 0 10px rgba(46,125,79,0); }
    100% { box-shadow: 0 0 0 0 rgba(46,125,79,0); }
  }

  /* ===== KPI strip ===== */
  body[data-role-dashboard="pharmacist"] .ph-kpis {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 18px;
  }
  @media (max-width: 1100px) { body[data-role-dashboard="pharmacist"] .ph-kpis { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px)  { body[data-role-dashboard="pharmacist"] .ph-kpis { grid-template-columns: 1fr; } }

  body[data-role-dashboard="pharmacist"] .ph-kpi {
    position: relative; cursor: pointer; user-select: none;
    background: #fff; border: 1px solid var(--line);
    border-radius: var(--r-lg); padding: 16px 18px 14px;
    box-shadow: var(--shadow-sm);
    transition: transform var(--t-med), box-shadow var(--t-med), border-color var(--t-med);
    overflow: hidden;
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi::before {
    content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--gold), var(--gold-2));
    opacity: .85;
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    border-color: var(--gold);
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi .ph-kpi-label {
    font-family: var(--mono); font-size: 10.5px; letter-spacing: .14em;
    text-transform: uppercase; color: var(--ink-2); margin-bottom: 6px;
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi .ph-kpi-value {
    font-size: 34px; font-weight: 700; line-height: 1.05; color: var(--navy);
    letter-spacing: -.02em;
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi .ph-kpi-sub {
    font-size: 12px; color: var(--mute); margin-top: 4px;
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi .ph-kpi-cta {
    position: absolute; top: 14px; right: 14px;
    font-family: var(--mono); font-size: 10px; letter-spacing: .12em;
    color: var(--gold); text-transform: uppercase;
    opacity: 0; transform: translateX(-4px);
    transition: opacity var(--t-med), transform var(--t-med);
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi:hover .ph-kpi-cta {
    opacity: 1; transform: translateX(0);
  }
  body[data-role-dashboard="pharmacist"] .ph-kpi .ph-spark {
    height: 36px; margin-top: 8px;
  }

  /* ===== Two-column layout ===== */
  body[data-role-dashboard="pharmacist"] .ph-grid {
    display: grid; grid-template-columns: 1.15fr 1fr; gap: 16px;
  }
  @media (max-width: 1024px) { body[data-role-dashboard="pharmacist"] .ph-grid { grid-template-columns: 1fr; } }

  body[data-role-dashboard="pharmacist"] .ph-panel {
    background: #fff; border: 1px solid var(--line);
    border-radius: var(--r-lg); box-shadow: var(--shadow-sm); overflow: hidden;
  }
  body[data-role-dashboard="pharmacist"] .ph-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 16px; background: var(--navy);
    color: var(--cream);
    border-bottom: 1px solid var(--navy-3);
  }
  body[data-role-dashboard="pharmacist"] .ph-panel-head h5 {
    margin: 0; font-size: 13px; font-weight: 600; letter-spacing: .02em;
  }
  body[data-role-dashboard="pharmacist"] .ph-panel-head .ph-tag {
    font-family: var(--mono); font-size: 10.5px; letter-spacing: .14em;
    text-transform: uppercase; color: var(--gold-2);
    background: rgba(216,185,105,.08); padding: 3px 8px; border-radius: 999px;
    border: 1px solid rgba(216,185,105,.3);
  }
  body[data-role-dashboard="pharmacist"] .ph-panel-body { padding: 6px 0 4px; }
  body[data-role-dashboard="pharmacist"] .ph-panel-body.padded { padding: 14px 16px; }

  /* ===== Row list ===== */
  body[data-role-dashboard="pharmacist"] .ph-row {
    display: grid; grid-template-columns: 80px 1fr auto auto auto; gap: 12px;
    align-items: center;
    padding: 10px 16px; border-top: 1px solid var(--line);
    transition: background var(--t-fast);
    cursor: pointer; position: relative;
  }
  body[data-role-dashboard="pharmacist"] .ph-row:first-child { border-top: 0; }
  body[data-role-dashboard="pharmacist"] .ph-row:hover { background: var(--gold-soft); }
  body[data-role-dashboard="pharmacist"] .ph-row .ph-id {
    font-family: var(--mono); font-size: 11px; color: var(--ink-2);
  }
  body[data-role-dashboard="pharmacist"] .ph-row .ph-name {
    font-weight: 600; color: var(--navy); font-size: 13.5px;
  }
  body[data-role-dashboard="pharmacist"] .ph-row .ph-sub {
    font-size: 11px; color: var(--mute);
  }
  body[data-role-dashboard="pharmacist"] .ph-row .ph-amt {
    font-family: var(--mono); font-weight: 700; color: var(--navy); font-size: 13px;
  }
  body[data-role-dashboard="pharmacist"] .ph-pay-chip {
    display: inline-block; padding: 2px 8px; border-radius: 999px;
    font-family: var(--mono); font-size: 10px; letter-spacing: .1em;
    text-transform: uppercase; border: 1px solid var(--line); color: var(--ink-2);
    background: #fafafa;
  }
  body[data-role-dashboard="pharmacist"] .ph-time {
    font-family: var(--mono); font-size: 11px; color: var(--mute);
  }

  /* Pending Rx row inline CTA */
  body[data-role-dashboard="pharmacist"] .ph-row-rx { grid-template-columns: 1fr auto; }
  body[data-role-dashboard="pharmacist"] .ph-row-rx .ph-rx-cta {
    background: var(--navy); color: var(--cream);
    border: 1px solid var(--navy-3); padding: 6px 12px; border-radius: var(--r-sm);
    font-family: var(--mono); font-size: 11px; letter-spacing: .1em;
    text-transform: uppercase; cursor: pointer;
    opacity: 0; transform: translateX(8px);
    transition: opacity var(--t-med), transform var(--t-med), background var(--t-fast);
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
  }
  body[data-role-dashboard="pharmacist"] .ph-row-rx:hover .ph-rx-cta {
    opacity: 1; transform: translateX(0);
  }
  body[data-role-dashboard="pharmacist"] .ph-row-rx .ph-rx-cta:hover {
    background: var(--gold); color: var(--navy); border-color: var(--gold);
  }

  /* Empty states */
  body[data-role-dashboard="pharmacist"] .ph-empty {
    padding: 28px 16px; text-align: center; color: var(--mute);
    font-size: 13px; font-style: italic;
  }
  body[data-role-dashboard="pharmacist"] .ph-empty small {
    display: block; margin-top: 6px; font-style: normal;
    font-family: var(--mono); font-size: 10.5px; letter-spacing: .1em;
    text-transform: uppercase; color: var(--gold);
  }

  /* ===== Top medicines bar table ===== */
  body[data-role-dashboard="pharmacist"] .ph-bar-row {
    display: grid; grid-template-columns: 1fr 80px 60px; gap: 12px;
    align-items: center; padding: 10px 16px;
    border-top: 1px solid var(--line); cursor: pointer;
    transition: background var(--t-fast);
  }
  body[data-role-dashboard="pharmacist"] .ph-bar-row:first-child { border-top: 0; }
  body[data-role-dashboard="pharmacist"] .ph-bar-row:hover { background: var(--gold-soft); }
  body[data-role-dashboard="pharmacist"] .ph-bar-row .ph-med-name {
    font-weight: 600; color: var(--navy); font-size: 13px;
  }
  body[data-role-dashboard="pharmacist"] .ph-bar-track {
    position: relative; height: 6px; background: var(--cream-2); border-radius: 99px; overflow: hidden;
  }
  body[data-role-dashboard="pharmacist"] .ph-bar-fill {
    position: absolute; top: 0; left: 0; bottom: 0;
    background: linear-gradient(90deg, var(--gold), var(--gold-2));
    width: 0%;
    transition: width 900ms cubic-bezier(.2,.7,.2,1);
  }
  body[data-role-dashboard="pharmacist"] .ph-med-qty {
    font-family: var(--mono); font-size: 12px; color: var(--navy); font-weight: 700; text-align: right;
  }

  /* ===== Range tabs ===== */
  body[data-role-dashboard="pharmacist"] .ph-tabs {
    display: inline-flex; background: rgba(255,255,255,.08);
    border: 1px solid rgba(216,185,105,.3); border-radius: 99px; padding: 2px;
  }
  body[data-role-dashboard="pharmacist"] .ph-tab {
    font-family: var(--mono); font-size: 10.5px; letter-spacing: .1em;
    text-transform: uppercase; padding: 4px 12px; border-radius: 99px;
    cursor: pointer; color: var(--cream); transition: all var(--t-fast);
    border: 0; background: transparent;
  }
  body[data-role-dashboard="pharmacist"] .ph-tab.is-active {
    background: var(--gold); color: var(--navy); font-weight: 700;
  }

  /* ===== Quick actions ===== */
  body[data-role-dashboard="pharmacist"] .ph-actions {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 16px;
  }
  @media (max-width: 720px) { body[data-role-dashboard="pharmacist"] .ph-actions { grid-template-columns: 1fr; } }
  body[data-role-dashboard="pharmacist"] .ph-action {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px; background: #fff; border: 1px solid var(--line);
    border-radius: var(--r-md); box-shadow: var(--shadow-sm);
    text-decoration: none; color: var(--navy);
    transition: transform var(--t-med), box-shadow var(--t-med), border-color var(--t-med);
    cursor: pointer;
  }
  body[data-role-dashboard="pharmacist"] .ph-action:hover {
    transform: translateY(-2px); box-shadow: var(--shadow-md);
    border-color: var(--gold); color: var(--navy);
  }
  body[data-role-dashboard="pharmacist"] .ph-action .ph-action-label {
    font-weight: 600; font-size: 13.5px;
  }
  body[data-role-dashboard="pharmacist"] .ph-action .ph-action-sub {
    font-family: var(--mono); font-size: 10px; letter-spacing: .12em;
    text-transform: uppercase; color: var(--mute); margin-top: 2px;
  }
  body[data-role-dashboard="pharmacist"] .ph-action .ph-action-arrow {
    color: var(--gold); font-size: 18px;
    transition: transform var(--t-fast);
  }
  body[data-role-dashboard="pharmacist"] .ph-action:hover .ph-action-arrow {
    transform: translateX(4px);
  }

  /* ===== Lower split: top meds + payment donut ===== */
  body[data-role-dashboard="pharmacist"] .ph-lower {
    display: grid; grid-template-columns: 1.4fr 1fr; gap: 16px; margin-top: 16px;
  }
  @media (max-width: 1024px) { body[data-role-dashboard="pharmacist"] .ph-lower { grid-template-columns: 1fr; } }

  body[data-role-dashboard="pharmacist"] #ph-donut {
    min-height: 240px;
  }

  /* ===== Reuse-band: legacy metric strip (kept for parity) ===== */
  body[data-role-dashboard="pharmacist"] .ph-legacy-band {
    margin-top: 16px;
  }
  body[data-role-dashboard="pharmacist"] .ph-legacy-band .ph-mini {
    text-align: center; padding: 14px 8px;
    border-right: 1px solid var(--line);
  }
  body[data-role-dashboard="pharmacist"] .ph-legacy-band .ph-mini:last-child { border-right: 0; }
  body[data-role-dashboard="pharmacist"] .ph-legacy-band .ph-mini h6 {
    font-family: var(--mono); font-size: 10.5px; letter-spacing: .14em;
    text-transform: uppercase; color: var(--ink-2); margin: 8px 0 2px;
  }
  body[data-role-dashboard="pharmacist"] .ph-legacy-band .ph-mini .ph-mini-val {
    font-size: 22px; font-weight: 700; color: var(--navy); letter-spacing: -.01em;
  }
  body[data-role-dashboard="pharmacist"] .ph-legacy-band .ph-mini small {
    color: var(--mute); font-size: 11px;
  }

  /* ===== Reduced motion ===== */
  @media (prefers-reduced-motion: reduce) {
    body[data-role-dashboard="pharmacist"] * {
      animation-duration: 0.01ms !important;
      transition-duration: 0.01ms !important;
    }
    body[data-role-dashboard="pharmacist"] .ph-dot { animation: none; }
  }

  /* ===== Focus visibility (a11y) ===== */
  body[data-role-dashboard="pharmacist"] .ph-kpi:focus-visible,
  body[data-role-dashboard="pharmacist"] .ph-row:focus-visible,
  body[data-role-dashboard="pharmacist"] .ph-action:focus-visible,
  body[data-role-dashboard="pharmacist"] .ph-bar-row:focus-visible {
    outline: 2px solid var(--gold);
    outline-offset: 2px;
  }
</style>

<div class="main-content">
  <section class="section">
    <div class="ph-wrap">

      <!-- ===== Chrome bar ===== -->
      <div class="ph-chrome">
        <div>
          <div class="ph-eyebrow">H360 / OP CONSOLE / <span class="ph-mark">PHARMACIST</span></div>
          <div class="ph-title">Medicine Billing Desk</div>
        </div>
        <div class="ph-status">
          <span class="ph-dot"></span>
          <span id="ph-clock">--:-- IST</span>
        </div>
      </div>

      <!-- ===== Hero KPI strip ===== -->
      <div class="ph-kpis" role="region" aria-label="Pharmacist KPIs">
        <div class="ph-kpi" tabindex="0" role="button"
             data-href="medicine_bill.php?date=today"
             aria-label="Today's bills">
          <div class="ph-kpi-cta">View &rarr;</div>
          <div class="ph-kpi-label">Today's Bills</div>
          <div class="ph-kpi-value" data-counter id="ph-kpi-count">0</div>
          <div class="ph-kpi-sub">Last 7 days</div>
          <div class="ph-spark"><canvas id="ph-spark-canvas" width="240" height="36"></canvas></div>
        </div>

        <div class="ph-kpi" tabindex="0" role="button"
             data-href="dailyreports.php?source=pharmacy&amp;date=today"
             aria-label="Today's revenue">
          <div class="ph-kpi-cta">Report &rarr;</div>
          <div class="ph-kpi-label">Today's Revenue</div>
          <div class="ph-kpi-value">&#8377; <span data-counter id="ph-kpi-rev">0</span></div>
          <div class="ph-kpi-sub">Net amount, paid bills</div>
        </div>

        <div class="ph-kpi" tabindex="0" role="button"
             data-href="#ph-pending"
             aria-label="Pending prescription pickups">
          <div class="ph-kpi-cta">Queue &rarr;</div>
          <div class="ph-kpi-label">Pending Rx Pickups</div>
          <div class="ph-kpi-value" data-counter id="ph-kpi-pending">0</div>
          <div class="ph-kpi-sub">Rx without medicine bill</div>
        </div>

        <div class="ph-kpi" tabindex="0" role="button"
             data-href="dailyreports.php?source=pharmacy&amp;view=avg"
             aria-label="Average bill value">
          <div class="ph-kpi-cta">Drill-down &rarr;</div>
          <div class="ph-kpi-label">Avg Bill Value</div>
          <div class="ph-kpi-value">&#8377; <span data-counter id="ph-kpi-avg">0</span></div>
          <div class="ph-kpi-sub">Today, mean per bill</div>
        </div>
      </div>

      <!-- ===== Two columns: today's bills + pending Rx ===== -->
      <div class="ph-grid">
        <div class="ph-panel">
          <div class="ph-panel-head">
            <h5>TODAY&rsquo;S BILLS</h5>
            <span class="ph-tag" id="ph-bills-count">0</span>
          </div>
          <div class="ph-panel-body" id="ph-bills-body">
            <div class="ph-empty">Loading today&rsquo;s bills&hellip;</div>
          </div>
        </div>

        <div class="ph-panel" id="ph-pending">
          <div class="ph-panel-head">
            <h5>PENDING Rx PICKUPS</h5>
            <span class="ph-tag" id="ph-pending-count">0</span>
          </div>
          <div class="ph-panel-body" id="ph-pending-body">
            <div class="ph-empty">Scanning prescriptions&hellip;</div>
          </div>
        </div>
      </div>

      <!-- ===== Lower split: top meds + payment donut ===== -->
      <div class="ph-lower">
        <div class="ph-panel">
          <div class="ph-panel-head">
            <h5>TOP MEDICINES</h5>
            <div class="ph-tabs" role="tablist" aria-label="Range">
              <button class="ph-tab is-active" data-range="today" role="tab" aria-selected="true">Today</button>
              <button class="ph-tab" data-range="week" role="tab" aria-selected="false">7 Days</button>
            </div>
          </div>
          <div class="ph-panel-body" id="ph-top-body">
            <div class="ph-empty">Loading top medicines&hellip;</div>
          </div>
        </div>

        <div class="ph-panel">
          <div class="ph-panel-head">
            <h5>PAYMENT METHODS &mdash; TODAY</h5>
            <span class="ph-tag" id="ph-pay-total">&#8377; 0</span>
          </div>
          <div class="ph-panel-body padded" id="ph-donut-wrap">
            <div id="ph-donut"></div>
            <div class="ph-empty" id="ph-donut-empty" style="display:none;">No payments recorded yet today.</div>
          </div>
        </div>
      </div>

      <!-- ===== Quick actions ===== -->
      <div class="ph-actions">
        <a class="ph-action" href="medicine_bill.php" aria-label="Start a new bill">
          <div>
            <div class="ph-action-label">Start New Bill</div>
            <div class="ph-action-sub">Open billing form</div>
          </div>
          <div class="ph-action-arrow">&rarr;</div>
        </a>
        <a class="ph-action" href="medicine_bill.php?view=list" aria-label="View past bills">
          <div>
            <div class="ph-action-label">Past Bills</div>
            <div class="ph-action-sub">Searchable history</div>
          </div>
          <div class="ph-action-arrow">&rarr;</div>
        </a>
        <a class="ph-action" href="medicines.php" aria-label="Medicine inventory">
          <div>
            <div class="ph-action-label">Medicine Inventory</div>
            <div class="ph-action-sub">Stock &amp; pricing</div>
          </div>
          <div class="ph-action-arrow">&rarr;</div>
        </a>
      </div>

      <!-- ===== Legacy "Detailed Metrics" band — adapted for pharmacist ===== -->
      <div class="ph-panel ph-legacy-band">
        <div class="ph-panel-head">
          <h5>DETAILED METRICS</h5>
          <span class="ph-tag">Adapted from legacy</span>
        </div>
        <div class="ph-panel-body padded">
          <div class="row g-0">
            <div class="col-md-3 col-6 ph-mini">
              <i class="bi bi-receipt-cutoff" style="font-size:1.4rem;color:var(--gold);"></i>
              <h6>Today Bills</h6>
              <div class="ph-mini-val" id="ph-mini-count">0</div>
              <small>Paid bills today</small>
            </div>
            <div class="col-md-3 col-6 ph-mini">
              <i class="bi bi-currency-rupee" style="font-size:1.4rem;color:var(--gold);"></i>
              <h6>Revenue Today</h6>
              <div class="ph-mini-val">&#8377; <span id="ph-mini-rev">0</span></div>
              <small>Net amount</small>
            </div>
            <div class="col-md-3 col-6 ph-mini">
              <i class="bi bi-hourglass-split" style="font-size:1.4rem;color:var(--gold);"></i>
              <h6>Pending Rx</h6>
              <div class="ph-mini-val" id="ph-mini-pending">0</div>
              <small>Awaiting bill</small>
            </div>
            <div class="col-md-3 col-6 ph-mini">
              <i class="bi bi-bar-chart-line" style="font-size:1.4rem;color:var(--gold);"></i>
              <h6>Avg Bill</h6>
              <div class="ph-mini-val">&#8377; <span id="ph-mini-avg">0</span></div>
              <small>Today's mean</small>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<!-- ApexCharts for donut -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
(function () {
  'use strict';

  // ---------- helpers ----------
  var INR = function (n) {
    n = Number(n) || 0;
    return n.toLocaleString('en-IN', { maximumFractionDigits: 0 });
  };
  var escapeHTML = function (s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  };

  var prefersReduced = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // counter animation
  function animateCounter(el, target, duration) {
    if (!el) return;
    target = Number(target) || 0;
    duration = duration || 800;
    if (prefersReduced) { el.textContent = INR(target); return; }
    var start = performance.now();
    var from = 0;
    function step(t) {
      var p = Math.min(1, (t - start) / duration);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = INR(Math.round(from + (target - from) * eased));
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // clock
  function tickClock() {
    var el = document.getElementById('ph-clock');
    if (!el) return;
    var d = new Date();
    var hh = String(d.getHours()).padStart(2, '0');
    var mm = String(d.getMinutes()).padStart(2, '0');
    el.textContent = hh + ':' + mm + ' IST';
  }
  tickClock(); setInterval(tickClock, 30000);

  // KPI click-through
  document.querySelectorAll('body[data-role-dashboard="pharmacist"] .ph-kpi').forEach(function (k) {
    var href = k.getAttribute('data-href');
    if (!href) return;
    var go = function () {
      if (href.charAt(0) === '#') {
        var t = document.querySelector(href);
        if (t) t.scrollIntoView({ behavior: prefersReduced ? 'auto' : 'smooth', block: 'start' });
      } else {
        window.location.href = href;
      }
    };
    k.addEventListener('click', go);
    k.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); }
    });
  });

  // ---------- sparkline (canvas) ----------
  function drawSpark(canvas, data) {
    if (!canvas || !canvas.getContext) return;
    var ctx = canvas.getContext('2d');
    var w = canvas.width, h = canvas.height;
    ctx.clearRect(0, 0, w, h);
    if (!data || !data.length) return;
    var max = Math.max.apply(null, data), min = Math.min.apply(null, data);
    if (max === min) { max = min + 1; }
    var pad = 4;
    var step = (w - pad * 2) / (data.length - 1 || 1);
    var pts = data.map(function (v, i) {
      var x = pad + i * step;
      var y = h - pad - ((v - min) / (max - min)) * (h - pad * 2);
      return [x, y];
    });
    // fill
    var grad = ctx.createLinearGradient(0, 0, 0, h);
    grad.addColorStop(0, 'rgba(194,161,75,0.30)');
    grad.addColorStop(1, 'rgba(194,161,75,0.02)');
    ctx.beginPath();
    ctx.moveTo(pts[0][0], h - pad);
    pts.forEach(function (p) { ctx.lineTo(p[0], p[1]); });
    ctx.lineTo(pts[pts.length - 1][0], h - pad);
    ctx.closePath();
    ctx.fillStyle = grad; ctx.fill();
    // stroke
    ctx.beginPath();
    pts.forEach(function (p, i) { i ? ctx.lineTo(p[0], p[1]) : ctx.moveTo(p[0], p[1]); });
    ctx.lineWidth = 1.6; ctx.strokeStyle = '#c2a14b'; ctx.stroke();
    // last point dot
    var last = pts[pts.length - 1];
    ctx.beginPath(); ctx.arc(last[0], last[1], 2.6, 0, Math.PI * 2);
    ctx.fillStyle = '#0b1f3a'; ctx.fill();
    ctx.strokeStyle = '#c2a14b'; ctx.lineWidth = 1.4; ctx.stroke();
  }

  // ---------- data loaders ----------
  function loadTodayBills() {
    return fetch('ajax/dashbord/pharmacist_today_bills.php', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        // KPIs
        animateCounter(document.getElementById('ph-kpi-count'), data.today_count);
        animateCounter(document.getElementById('ph-kpi-rev'),   data.today_revenue);
        animateCounter(document.getElementById('ph-kpi-avg'),   data.avg_bill);
        animateCounter(document.getElementById('ph-mini-count'),  data.today_count);
        animateCounter(document.getElementById('ph-mini-rev'),    data.today_revenue);
        animateCounter(document.getElementById('ph-mini-avg'),    data.avg_bill);

        // Sparkline
        drawSpark(document.getElementById('ph-spark-canvas'), data.spark_7day);

        // Bill list
        var body = document.getElementById('ph-bills-body');
        var tag  = document.getElementById('ph-bills-count');
        if (!data.source_present) {
          body.innerHTML = '<div class="ph-empty">Medicine billing module not yet provisioned for this org.<small>endpoint detected no patient_medicine_billing table</small></div>';
          tag.textContent = '0';
          return;
        }
        if (!data.bills || data.bills.length === 0) {
          body.innerHTML = '<div class="ph-empty">No bills generated today yet.<small>Open "Start New Bill" to begin</small></div>';
          tag.textContent = '0';
          return;
        }
        var html = '';
        data.bills.forEach(function (b) {
          html += '<div class="ph-row" tabindex="0" data-bill="' + escapeHTML(b.bill_id) + '">' +
                  '  <div class="ph-id">#' + escapeHTML(b.bill_id) + '</div>' +
                  '  <div>' +
                  '    <div class="ph-name">' + escapeHTML(b.patient_name) + '</div>' +
                  '    <div class="ph-sub">' + escapeHTML(b.patient_id) + '</div>' +
                  '  </div>' +
                  '  <div class="ph-amt">&#8377; ' + INR(b.amount) + '</div>' +
                  '  <div><span class="ph-pay-chip">' + escapeHTML(b.payment_method || 'Other') + '</span></div>' +
                  '  <div class="ph-time">' + escapeHTML(b.time) + '</div>' +
                  '</div>';
        });
        body.innerHTML = html;
        tag.textContent = data.bills.length;

        body.querySelectorAll('.ph-row').forEach(function (r) {
          var bid = r.getAttribute('data-bill');
          var go = function () { window.location.href = 'medicine_bill.php?view=' + encodeURIComponent(bid); };
          r.addEventListener('click', go);
          r.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); }
          });
        });
      })
      .catch(function () {
        document.getElementById('ph-bills-body').innerHTML =
          '<div class="ph-empty">Failed to load today&rsquo;s bills.</div>';
      });
  }

  function loadPendingRx() {
    return fetch('ajax/dashbord/pharmacist_pending_rx.php', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        animateCounter(document.getElementById('ph-kpi-pending'),  data.pending_count);
        animateCounter(document.getElementById('ph-mini-pending'), data.pending_count);
        document.getElementById('ph-pending-count').textContent = data.pending_count;

        var body = document.getElementById('ph-pending-body');
        if (!data.items || data.items.length === 0) {
          body.innerHTML = '<div class="ph-empty">No pending pickups. All caught up.<small>Doctors&rsquo; Rx will appear here</small></div>';
          return;
        }
        var html = '';
        data.items.forEach(function (it) {
          html += '<div class="ph-row ph-row-rx" tabindex="0" data-appt="' + escapeHTML(it.appoint_register_id) + '">' +
                  '  <div>' +
                  '    <div class="ph-name">' + escapeHTML(it.patient_name) +
                       ' <span class="ph-pay-chip">' + escapeHTML(it.medicine_count) + ' med' + (it.medicine_count > 1 ? 's' : '') + '</span></div>' +
                  '    <div class="ph-sub">Dr. ' + escapeHTML(it.doctor_name) +
                       ' &middot; ' + escapeHTML(it.rx_date || '') +
                       ' &middot; ' + escapeHTML(it.medicines_preview) + '</div>' +
                  '  </div>' +
                  '  <a class="ph-rx-cta" href="medicine_bill.php?appoint_register_id=' +
                       encodeURIComponent(it.appoint_register_id) + '">Start Bill &rarr;</a>' +
                  '</div>';
        });
        body.innerHTML = html;

        body.querySelectorAll('.ph-row-rx').forEach(function (r) {
          r.addEventListener('click', function (e) {
            if (e.target.closest('.ph-rx-cta')) return;
            window.location.href = 'medicine_bill.php?appoint_register_id=' +
              encodeURIComponent(r.getAttribute('data-appt'));
          });
        });
      })
      .catch(function () {
        document.getElementById('ph-pending-body').innerHTML =
          '<div class="ph-empty">Failed to load pending prescriptions.</div>';
      });
  }

  var _currentTopRange = 'today';
  function loadTopMedicines(range) {
    _currentTopRange = range || 'today';
    return fetch('ajax/dashbord/pharmacist_top_medicines.php?range=' + encodeURIComponent(_currentTopRange),
                 { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var body = document.getElementById('ph-top-body');
        if (!data.items || data.items.length === 0) {
          body.innerHTML = '<div class="ph-empty">No medicines billed in this range yet.</div>';
          return;
        }
        var max = Math.max.apply(null, data.items.map(function (m) { return m.qty; }));
        var html = '';
        data.items.forEach(function (m) {
          var pct = max > 0 ? Math.round((m.qty / max) * 100) : 0;
          html += '<div class="ph-bar-row" tabindex="0" data-med="' + escapeHTML(m.medicine_name) + '">' +
                  '  <div>' +
                  '    <div class="ph-med-name">' + escapeHTML(m.medicine_name) + '</div>' +
                  '    <div class="ph-bar-track"><div class="ph-bar-fill" data-pct="' + pct + '"></div></div>' +
                  '  </div>' +
                  '  <div class="ph-med-qty">' + m.qty + '</div>' +
                  '  <div class="ph-time">&rarr;</div>' +
                  '</div>';
        });
        body.innerHTML = html;
        // animate bars
        setTimeout(function () {
          body.querySelectorAll('.ph-bar-fill').forEach(function (f) {
            f.style.width = (f.getAttribute('data-pct') || 0) + '%';
          });
        }, 40);
        body.querySelectorAll('.ph-bar-row').forEach(function (r) {
          r.addEventListener('click', function () {
            window.location.href = 'medicines.php?search=' + encodeURIComponent(r.getAttribute('data-med'));
          });
        });
      })
      .catch(function () {
        document.getElementById('ph-top-body').innerHTML =
          '<div class="ph-empty">Failed to load top medicines.</div>';
      });
  }

  // tabs
  document.querySelectorAll('body[data-role-dashboard="pharmacist"] .ph-tab').forEach(function (t) {
    t.addEventListener('click', function () {
      document.querySelectorAll('body[data-role-dashboard="pharmacist"] .ph-tab').forEach(function (x) {
        x.classList.remove('is-active'); x.setAttribute('aria-selected', 'false');
      });
      t.classList.add('is-active'); t.setAttribute('aria-selected', 'true');
      loadTopMedicines(t.getAttribute('data-range'));
    });
  });

  // ---------- payment donut ----------
  var donut = null;
  function loadPaymentBreakdown() {
    return fetch('ajax/dashbord/pharmacist_payment_breakdown.php', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var empty = document.getElementById('ph-donut-empty');
        var wrap  = document.getElementById('ph-donut');
        if (!data.items || data.items.length === 0) {
          wrap.innerHTML = ''; empty.style.display = 'block';
          document.getElementById('ph-pay-total').textContent = '₹ 0';
          return;
        }
        empty.style.display = 'none';
        var labels  = data.items.map(function (i) { return i.method; });
        var counts  = data.items.map(function (i) { return i.count; });
        var total   = data.items.reduce(function (s, i) { return s + Number(i.total || 0); }, 0);
        document.getElementById('ph-pay-total').textContent = '₹ ' + INR(total);

        if (donut) { donut.destroy(); donut = null; }
        donut = new ApexCharts(wrap, {
          chart: {
            type: 'donut', height: 240,
            animations: { enabled: !prefersReduced, speed: 600 },
            fontFamily: 'inherit'
          },
          series: counts,
          labels: labels,
          colors: ['#0b1f3a', '#c2a14b', '#16365f', '#d8b969', '#7a7a7a', '#9b2a2a'],
          stroke: { width: 2, colors: ['#fff'] },
          legend: { position: 'bottom', fontSize: '11px', labels: { colors: '#1a1a1a' } },
          dataLabels: {
            style: { fontSize: '11px', fontWeight: 600, colors: ['#fff'] },
            dropShadow: { enabled: false }
          },
          plotOptions: {
            pie: {
              donut: {
                size: '64%',
                labels: {
                  show: true,
                  total: {
                    show: true, label: 'Bills', fontSize: '11px',
                    color: '#0b1f3a', fontFamily: 'inherit'
                  },
                  value: { fontSize: '20px', fontWeight: 700, color: '#0b1f3a' }
                }
              }
            }
          },
          tooltip: { y: { formatter: function (v) { return v + ' bills'; } } }
        });
        donut.render();
        // slice click → filter today's bills by method
        donut.addEventListener && donut.addEventListener('dataPointSelection', function (e, ctx, cfg) {
          var pm = labels[cfg.dataPointIndex];
          window.location.href = 'medicine_bill.php?date=today&payment_method=' + encodeURIComponent(pm);
        });
      })
      .catch(function () {
        document.getElementById('ph-donut').innerHTML = '';
        document.getElementById('ph-donut-empty').style.display = 'block';
      });
  }

  // ---------- boot ----------
  function boot() {
    loadTodayBills();
    loadPendingRx();
    loadTopMedicines('today');
    loadPaymentBreakdown();
    // soft auto-refresh every 60s
    setInterval(function () {
      loadTodayBills(); loadPendingRx(); loadPaymentBreakdown();
    }, 60000);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
