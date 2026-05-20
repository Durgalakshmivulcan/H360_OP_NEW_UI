/* H360 UI · overlay JS  (CR-1: one script per file)
 * Three responsibilities:
 *  1. Copy each sidebar menu item's <span> text onto its <a> as data-tip="…"
 *     so CSS can render mini-state tooltips via `content: attr(data-tip)`.
 *  2. In collapsed sidebar (sidebar-mini OR sidebar-gone), clicking a parent
 *     menu item (a.has-dropdown) pins its submenu as a popover next to the
 *     icon.  Click outside or press ESC to dismiss.  Leaf items navigate
 *     directly (we never preventDefault on them).
 *  3. Apply the design.md §3.3.H ApexCharts theme as a global default so any
 *     chart created on any page picks up the navy + gold treatment without
 *     touching the inline chart-config blocks. */
(function () {
  // ---- 3 · ApexCharts global theme override ----
  // Set BEFORE any ApexCharts() call. Inline chart configs can still override
  // per-chart, but defaults match our locked aesthetic.
  function applyChartTheme() {
    if (!window.Apex) window.Apex = {};
    var navy = '#081530', navyDeep = '#050E22';
    var gold = '#C49A3F', goldLight = '#E5CB8A';
    Object.assign(window.Apex, {
      colors: [navy, gold, '#1E4475', '#A37E2B', '#16744D', '#A52323'],
      chart: { fontFamily: '"Geist Mono", ui-monospace, monospace', foreColor: '#5C667B', toolbar: { show: false } },
      stroke: { width: 2, curve: 'smooth' },
      fill: { type: 'gradient', gradient: { shade: 'light', shadeIntensity: 0.25, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 90, 100] } },
      grid: { borderColor: 'rgba(8,21,48,.08)', strokeDashArray: 0, padding: { left: 8, right: 8, top: 0, bottom: 0 } },
      xaxis: { axisBorder: { show: false }, axisTicks: { color: 'rgba(8,21,48,.08)' }, labels: { style: { fontSize: '10px', fontFamily: '"Geist Mono", monospace', colors: '#5C667B' } } },
      yaxis: { labels: { style: { fontSize: '10px', fontFamily: '"Geist Mono", monospace', colors: '#5C667B' } } },
      legend: { fontFamily: '"Geist", system-ui, sans-serif', fontSize: '11px', labels: { colors: '#2C313A' } },
      tooltip: { theme: 'dark', style: { fontFamily: '"Geist Mono", monospace', fontSize: '11px' } },
      dataLabels: { enabled: false },
      markers: { size: 0, hover: { size: 4 } },
    });
  }
  applyChartTheme();   // run synchronously so it lands before any chart init

  function tagTips() {
    document.querySelectorAll('.main-sidebar .sidebar-menu li > a').forEach(function (a) {
      if (a.dataset.tip) return;
      var span = a.querySelector('span');
      if (span && span.textContent.trim()) {
        a.setAttribute('data-tip', span.textContent.trim());
      }
    });
  }

  function isCollapsed() {
    return document.body.classList.contains('sidebar-mini') ||
           document.body.classList.contains('sidebar-gone');
  }

  function closeAllPopovers() {
    document.querySelectorAll('.main-sidebar .sidebar-menu li.vk-popped').forEach(function (li) {
      li.classList.remove('vk-popped');
    });
  }

  function onSidebarClick(e) {
    if (!isCollapsed()) return;
    if (!e.target.closest('.main-sidebar')) return;
    var a = e.target.closest('a');
    if (!a || !a.closest('.sidebar-menu')) return;
    var li = a.closest('li');
    var hasDropdown = a.classList.contains('has-dropdown') ||
                      (li && li.querySelector(':scope > ul.dropdown-menu li > a'));
    if (!hasDropdown) {
      closeAllPopovers();
      return;
    }
    // parent item — pin popover, suppress nav
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    var wasPopped = li.classList.contains('vk-popped');
    closeAllPopovers();
    if (!wasPopped) li.classList.add('vk-popped');
  }

  function onDocClick(e) {
    if (!isCollapsed()) return;
    if (e.target.closest('.main-sidebar .sidebar-menu li.vk-popped')) return;
    closeAllPopovers();
  }
  function onKey(e) {
    if (e.key === 'Escape') closeAllPopovers();
  }

  // Mark dashboard pages so our dashboard-specific CSS doesn't leak to
   // registration / report pages that share `<section class="section">`.
  function tagDashboardPage() {
    if (document.querySelector('.dashboard-cards-row, .card-statistic-3')) {
      document.body.classList.add('h360-page-dashboard');
    }
  }

  function init() {
    tagTips();
    tagDashboardPage();
    // attach at document capture phase so we beat Stisla / jQuery handlers
    document.addEventListener('click', onSidebarClick, true);
    document.addEventListener('click', onDocClick);
    document.addEventListener('keydown', onKey);
    // re-tag on DOM mutations (Feather replacement, etc.)
    var sb = document.querySelector('.main-sidebar');
    var mo = new MutationObserver(tagTips);
    if (sb) mo.observe(sb, { childList: true, subtree: true });
    // close popovers when sidebar state changes
    var bodyMo = new MutationObserver(function () {
      // any class change on body — close popovers if not collapsed
      if (!isCollapsed()) closeAllPopovers();
    });
    bodyMo.observe(document.body, { attributes: true, attributeFilter: ['class'] });
  }

  // ---- KPI count-up animation ----
  // When a stat number first comes into view, count from 0 to its rendered
  // value over ~900 ms.  Respects prefers-reduced-motion.
  function bindCountUp() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!('IntersectionObserver' in window)) return;
    var targets = document.querySelectorAll(
      '.main-content .card-statistic-3 .card-content > span,' +
      '.main-content .card .card-body.p-3 h4#todayAppointments,' +
      '.main-content .card .card-body.p-3 h4#followUps'
    );
    var seen = new WeakSet();
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting || seen.has(e.target)) return;
        seen.add(e.target);
        var raw = e.target.textContent.trim();
        var m = raw.match(/^(\D*)(-?\d+)(.*)$/);
        if (!m) return;
        var pre = m[1] || '', target = parseInt(m[2], 10), suf = m[3] || '';
        if (!isFinite(target) || target <= 0) return;
        var start = performance.now(), dur = 900;
        function tick(now) {
          var t = Math.min(1, (now - start) / dur);
          var eased = 1 - Math.pow(1 - t, 3);    // ease-out cubic
          e.target.textContent = pre + Math.round(target * eased) + suf;
          if (t < 1) requestAnimationFrame(tick);
        }
        e.target.textContent = pre + '0' + suf;
        requestAnimationFrame(tick);
      });
    }, { threshold: 0.4 });
    targets.forEach(function (t) { io.observe(t); });
  }

  // ---- Doctor-scope filter active state ----
  // The avatars on dashboard.php are filter buttons. Mark the clicked one
  // active so CSS can paint the gold underline + name highlight.
  function bindAvatarFilterActive() {
    document.addEventListener('click', function (e) {
      var av = e.target.closest('.main-content .avatar-group .avatar:not(.more)');
      if (!av) return;
      document.querySelectorAll('.main-content .avatar-group .avatar.vk-active')
        .forEach(function (a) { a.classList.remove('vk-active'); });
      av.classList.add('vk-active');
    }, true);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      init();
      bindAvatarFilterActive();
      // small delay so async-loaded numbers (#todayAppointments etc) have a value
      setTimeout(bindCountUp, 600);
    });
  } else {
    init();
    bindAvatarFilterActive();
    setTimeout(bindCountUp, 600);
  }
})();
