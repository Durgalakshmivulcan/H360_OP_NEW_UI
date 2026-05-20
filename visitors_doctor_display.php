<?php require_once("ajax/header.php"); requireCan('view', basename(__FILE__)); ?>
<?php /* FIX_B_2310: full redesign — TV-grade typography, auto single/dual-doctor layout,
   Sovereign Institutional palette, rotating ad strip. The drawer (?-button) is intentionally
   suppressed here — this is a kiosk view, not a desk view. */ ?>
<style>
  :root {
    --tv-navy-900: #050d20;
    --tv-navy-800: #0a1a3a;
    --tv-navy-700: #112553;
    --tv-cream:    #f5efe1;
    --tv-cream-2:  #d8cfba;
    --tv-gold:     #d4a84b;
    --tv-gold-soft:#b88a3a;
    --tv-rule:     rgba(212, 168, 75, .28);
    --tv-mute:     rgba(245, 239, 225, .55);
  }
  /* hide every piece of admin chrome — this is a kiosk */
  body > nav, body > header, body > footer, body .main-sidebar, body .navbar,
  body .topbar, body .breadcrumb, body .main-header, body .content-header,
  body .h360-help-btn { display: none !important; }

  html, body {
    margin: 0; padding: 0;
    width: 100vw !important; height: 100vh !important;
    overflow: hidden !important;
    background: var(--tv-navy-900);
    color: var(--tv-cream);
    font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    cursor: none;
  }
  ::-webkit-scrollbar { width: 0; height: 0; }

  #tv-display {
    position: fixed; inset: 0;
    display: grid;
    grid-template-rows: 56px 1fr 24vh;
    background:
      radial-gradient(ellipse at 20% 0%, rgba(17, 37, 83, .55) 0%, transparent 55%),
      radial-gradient(ellipse at 80% 100%, rgba(212, 168, 75, .12) 0%, transparent 60%),
      var(--tv-navy-900);
    transition: grid-template-rows .3s ease;
  }
  /* FIX_B_2340b: with zero ads uploaded, drop the bottom strip entirely so
     the doctor stage uses the full screen. The ad rotator JS toggles this
     class after each /tv_ads/ scan. */
  #tv-display.is-no-ads { grid-template-rows: 56px 1fr; }
  #tv-display.is-no-ads .tv-ads { display: none; }

  /* ============================================================ */
  /* TOP BAR (clinic chrome) */
  /* ============================================================ */
  .tv-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 36px;
    border-bottom: 1px solid var(--tv-rule);
    background: linear-gradient(180deg, rgba(8, 21, 48, .92) 0%, rgba(8, 21, 48, .55) 100%);
    backdrop-filter: blur(6px);
  }
  .tv-bar-left, .tv-bar-right {
    display: flex; align-items: center; gap: 16px;
    font: 600 11px/1 'Geist Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
    letter-spacing: .18em; text-transform: uppercase;
    color: var(--tv-cream);
  }
  .tv-bar-brand { color: var(--tv-gold); }
  .tv-bar-dot {
    display: inline-block; width: 8px; height: 8px; border-radius: 50%;
    background: #4ade80; box-shadow: 0 0 12px rgba(74, 222, 128, .8);
    animation: tv-pulse 2.4s ease-in-out infinite;
  }
  .tv-bar-dot.is-offline { background: #ef4444; box-shadow: 0 0 12px rgba(239, 68, 68, .8); }
  @keyframes tv-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .55; transform: scale(.78); }
  }

  /* ============================================================ */
  /* MAIN STAGE — one or two doctor columns */
  /* ============================================================ */
  .tv-stage {
    display: grid;
    gap: 0;
    overflow: hidden;
    position: relative;
  }
  .tv-stage[data-count="1"] { grid-template-columns: 1fr; }
  .tv-stage[data-count="2"] { grid-template-columns: 1fr 1px 1fr; }
  .tv-stage[data-count="3"] { grid-template-columns: 1fr 1px 1fr 1px 1fr; }

  .tv-divider {
    background: linear-gradient(180deg, transparent 0%, var(--tv-gold-soft) 30%, var(--tv-gold-soft) 70%, transparent 100%);
    opacity: .55;
  }

  .tv-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: var(--tv-mute);
    font: 500 28px/1.4 'Geist', sans-serif;
    text-align: center; padding: 80px;
  }
  .tv-empty-eyebrow {
    font: 700 12px/1 'Geist Mono', monospace;
    letter-spacing: .22em; text-transform: uppercase;
    color: var(--tv-gold); margin-bottom: 20px;
  }

  /* ============================================================ */
  /* DOCTOR COLUMN */
  /* ============================================================ */
  .tv-col {
    display: grid;
    grid-template-rows: auto auto 1fr;
    padding: 40px 48px 32px;
    gap: 28px;
    min-width: 0;
  }
  .tv-stage[data-count="1"] .tv-col {
    grid-template-columns: 380px 1fr;
    grid-template-rows: auto 1fr;
    grid-template-areas:
      "photo serving"
      "photo queue";
    column-gap: 56px;
  }
  .tv-stage[data-count="1"] .tv-col-photo  { grid-area: photo; }
  .tv-stage[data-count="1"] .tv-col-now    { grid-area: serving; }
  .tv-stage[data-count="1"] .tv-col-queue  { grid-area: queue; }

  .tv-col-photo {
    display: flex; flex-direction: column; align-items: center; gap: 20px;
  }
  .tv-photo-wrap {
    position: relative;
    width: 100%; max-width: 280px; aspect-ratio: 1;
    border-radius: 50%;
    padding: 6px;
    background: linear-gradient(135deg, var(--tv-gold) 0%, var(--tv-gold-soft) 100%);
    box-shadow:
      0 12px 40px rgba(0, 0, 0, .55),
      0 0 0 1px rgba(212, 168, 75, .35);
  }
  .tv-stage[data-count="1"] .tv-photo-wrap { max-width: 340px; }
  .tv-photo-wrap img {
    width: 100%; height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: var(--tv-navy-800);
    display: block;
  }
  .tv-doc-name {
    font: 700 32px/1.1 'Geist', sans-serif;
    letter-spacing: .01em;
    color: var(--tv-cream);
    text-align: center;
  }
  .tv-stage[data-count="1"] .tv-doc-name { font-size: 40px; }
  .tv-doc-spec {
    font: 600 12px/1 'Geist Mono', monospace;
    letter-spacing: .22em; text-transform: uppercase;
    color: var(--tv-gold);
    margin-top: 6px; text-align: center;
  }

  /* "Now serving" hero */
  .tv-col-now { min-width: 0; }
  .tv-label {
    font: 600 11px/1 'Geist Mono', monospace;
    letter-spacing: .22em; text-transform: uppercase;
    color: var(--tv-gold-soft);
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 14px;
  }
  .tv-label::after { content: ''; flex: 1; height: 1px; background: var(--tv-rule); }

  .tv-now-id {
    font: 700 84px/1 'Geist Mono', monospace;
    letter-spacing: -.02em;
    color: var(--tv-gold);
    text-shadow: 0 0 28px rgba(212, 168, 75, .35);
    margin-bottom: 6px;
    word-break: break-all;
  }
  .tv-stage[data-count="1"] .tv-now-id { font-size: 132px; }
  .tv-stage[data-count="3"] .tv-now-id { font-size: 60px; }
  .tv-now-name {
    font: 600 38px/1.15 'Geist', sans-serif;
    color: var(--tv-cream);
    margin-bottom: 22px;
  }
  .tv-stage[data-count="1"] .tv-now-name { font-size: 56px; }
  .tv-stage[data-count="3"] .tv-now-name { font-size: 28px; }

  .tv-upcoming {
    display: flex; align-items: baseline; gap: 16px;
    padding: 14px 18px;
    background: rgba(212, 168, 75, .07);
    border: 1px solid var(--tv-rule);
    border-radius: 4px;
    margin-bottom: 28px;
  }
  .tv-upcoming-eyebrow {
    font: 700 10px/1 'Geist Mono', monospace;
    letter-spacing: .22em; text-transform: uppercase;
    color: var(--tv-gold-soft);
    flex-shrink: 0;
  }
  .tv-upcoming-id {
    font: 600 24px/1 'Geist Mono', monospace;
    color: var(--tv-cream);
  }
  .tv-stage[data-count="1"] .tv-upcoming-id { font-size: 32px; }
  .tv-upcoming-name {
    font: 500 18px/1 'Geist', sans-serif;
    color: var(--tv-cream-2);
  }
  .tv-stage[data-count="1"] .tv-upcoming-name { font-size: 24px; }

  /* Queue */
  .tv-col-queue { min-width: 0; }
  .tv-queue-list {
    list-style: none; margin: 0; padding: 0;
    display: flex; flex-direction: column; gap: 8px;
    max-height: 100%;
    overflow: hidden;
  }
  .tv-queue-list li {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: baseline;
    gap: 18px;
    padding: 12px 14px;
    border-radius: 3px;
    background: rgba(255, 255, 255, .025);
    transition: background .25s ease;
  }
  .tv-queue-list li:hover { background: rgba(212, 168, 75, .06); }
  .tv-queue-id {
    font: 600 18px/1 'Geist Mono', monospace;
    color: var(--tv-gold);
    letter-spacing: -.01em;
  }
  .tv-queue-name {
    font: 500 18px/1 'Geist', sans-serif;
    color: var(--tv-cream-2);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .tv-stage[data-count="1"] .tv-queue-id   { font-size: 22px; }
  .tv-stage[data-count="1"] .tv-queue-name { font-size: 22px; }

  .tv-queue-empty {
    color: var(--tv-mute);
    font: 500 16px/1.4 'Geist', sans-serif;
    padding: 12px 0;
  }

  /* ============================================================ */
  /* AD STRIP */
  /* ============================================================ */
  .tv-ads {
    position: relative; overflow: hidden;
    background: var(--tv-navy-800);
    border-top: 1px solid var(--tv-rule);
  }
  .tv-ads-slide {
    position: absolute; inset: 0;
    background-position: center; background-size: cover; background-repeat: no-repeat;
    opacity: 0;
    transition: opacity .9s ease-in-out;
  }
  .tv-ads-slide.is-active { opacity: 1; }
  .tv-ads-empty {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 8px;
    color: var(--tv-mute);
    font: 600 12px/1 'Geist Mono', monospace;
    letter-spacing: .22em; text-transform: uppercase;
    background:
      radial-gradient(ellipse at center, rgba(17, 37, 83, .6) 0%, transparent 70%),
      var(--tv-navy-800);
  }
  .tv-ads-empty small {
    font: 500 13px/1.4 'Geist', sans-serif;
    letter-spacing: 0; text-transform: none;
    color: var(--tv-cream-2);
    max-width: 540px; text-align: center;
  }

  /* Subtle entry animation when a new "next patient" arrives */
  @keyframes tv-arrive {
    0%   { transform: scale(.92); opacity: 0; filter: blur(8px); }
    60%  { transform: scale(1.02); opacity: 1; filter: blur(0); }
    100% { transform: scale(1); }
  }
  .tv-now-id.is-fresh, .tv-now-name.is-fresh { animation: tv-arrive .55s cubic-bezier(.2,.8,.25,1); }
</style>

<div id="tv-display">
  <div class="tv-bar">
    <div class="tv-bar-left">
      <span class="tv-bar-brand">AR Clinic</span>
      <span>· Waiting Room</span>
    </div>
    <div class="tv-bar-right">
      <span id="tvClock">--:-- IST</span>
      <span class="tv-bar-dot" id="tvStatusDot" title="Live"></span>
    </div>
  </div>

  <div class="tv-stage" id="tvStage" data-count="0">
    <div class="tv-empty">
      <div class="tv-empty-eyebrow">Standing by</div>
      <div>No patients in the queue yet today.</div>
    </div>
  </div>

  <div class="tv-ads" id="tvAds">
    <div class="tv-ads-empty" id="tvAdsEmpty">
      <span>Ad space · auto-rotates</span>
      <small>Drop images into the <code>tv_ads/</code> folder on the server. They appear here automatically — no restart needed.</small>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function () {
  'use strict';

  // ---------- escape ----------
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
    });
  }

  // FIX_B_2352: doctor_name in the DB already carries a "Dr." prefix; naively
  // prepending another produced "Dr. Dr.Ashwin Kumar Panda". Strip any leading
  // dr./dr/doctor (any case, optional period + space) before re-adding the
  // canonical one. Mirror of config/functions.php formatDoctorName().
  function formatDoctorName(n) {
    var s = String(n == null ? '' : n).trim();
    if (!s) return '';
    var stripped = s.replace(/^(dr\.?|doctor)\s*/i, '');
    return 'Dr. ' + (stripped || s);
  }

  // ---------- clock ----------
  function tickClock() {
    var d = new Date();
    var h = String(d.getHours()).padStart(2, '0');
    var m = String(d.getMinutes()).padStart(2, '0');
    document.getElementById('tvClock').textContent = h + ':' + m + ' IST';
  }
  tickClock();
  setInterval(tickClock, 15000);

  // ---------- doctor column renderer ----------
  function renderDoctor(d) {
    var imgPath = 'doctor_images/' + (d.doctor.doc_img || 'default.png');
    var next = d.next;
    var up = d.upcoming;
    var queue = d.queue || [];

    var nowBlock = next
      ? '<div class="tv-label">Now serving</div>'
        + '<div class="tv-now-id">' + esc(next.appoint_unicode || '—') + '</div>'
        + '<div class="tv-now-name">' + esc(next.patient_name || '—') + '</div>'
      : '<div class="tv-label">Now serving</div>'
        + '<div class="tv-now-id" style="opacity:.45">—</div>'
        + '<div class="tv-now-name" style="opacity:.55">Waiting for the next patient</div>';

    var upBlock = up
      ? '<div class="tv-upcoming">'
        + '<span class="tv-upcoming-eyebrow">Up next</span>'
        + '<span class="tv-upcoming-id">' + esc(up.appoint_unicode || '') + '</span>'
        + '<span class="tv-upcoming-name">' + esc(up.patient_name || '') + '</span>'
        + '</div>'
      : '';

    var queueBlock;
    if (queue.length) {
      var rows = queue.slice(0, 8).map(function (q) {
        return '<li>'
          + '<span class="tv-queue-id">' + esc(q.appoint_unicode || '') + '</span>'
          + '<span class="tv-queue-name">' + esc(q.patient_name || '') + '</span>'
          + '</li>';
      }).join('');
      queueBlock = '<div class="tv-label">In queue · ' + queue.length + '</div>'
                 + '<ul class="tv-queue-list">' + rows + '</ul>';
    } else {
      queueBlock = '<div class="tv-label">In queue</div>'
                 + '<div class="tv-queue-empty">No further patients waiting.</div>';
    }

    return ''
      + '<section class="tv-col">'
      +   '<div class="tv-col-photo">'
      +     '<div class="tv-photo-wrap">'
      +       '<img src="' + imgPath + '" alt="" onerror="this.src=\'doctor_images/default.png\'">'
      +     '</div>'
      +     '<div>'
      +       '<div class="tv-doc-name">' + esc(formatDoctorName(d.doctor.doctor_name || 'Doctor')) + '</div>'
      +       (d.doctor.specialization ? '<div class="tv-doc-spec">' + esc(d.doctor.specialization) + '</div>' : '')
      +     '</div>'
      +   '</div>'
      +   '<div class="tv-col-now">' + nowBlock + upBlock + '</div>'
      +   '<div class="tv-col-queue">' + queueBlock + '</div>'
      + '</section>';
  }

  // ---------- SSE ingest ----------
  var lastNextIds = {};   // {doc_id: appoint_unicode} — to flag fresh "now serving"
  var stage = document.getElementById('tvStage');
  var statusDot = document.getElementById('tvStatusDot');

  function render(data) {
    var docs = (data && data.doctors) || [];
    if (!docs.length) {
      stage.setAttribute('data-count', '0');
      stage.innerHTML =
        '<div class="tv-empty">'
        + '<div class="tv-empty-eyebrow">Standing by</div>'
        + '<div>No patients have been called yet today.</div>'
        + '</div>';
      return;
    }
    var count = Math.min(docs.length, 3);
    stage.setAttribute('data-count', String(count));
    var html = '';
    docs.slice(0, count).forEach(function (d, i) {
      if (i > 0) html += '<div class="tv-divider"></div>';
      html += renderDoctor(d);
    });
    stage.innerHTML = html;

    // mark the newly-called patients to play the arrival animation
    docs.forEach(function (d) {
      var key = d.doctor.doc_id;
      var newId = d.next && d.next.appoint_unicode;
      if (newId && lastNextIds[key] !== newId) {
        var cols = stage.querySelectorAll('.tv-col');
        var col = cols[docs.indexOf(d)];
        if (col) {
          var idEl = col.querySelector('.tv-now-id');
          var nmEl = col.querySelector('.tv-now-name');
          if (idEl) idEl.classList.add('is-fresh');
          if (nmEl) nmEl.classList.add('is-fresh');
        }
      }
      lastNextIds[key] = newId;
    });
  }

  function connectSSE() {
    var src = new EventSource('ajax/AppointmentBooking/ssevisitordisplay.php');
    src.onmessage = function (ev) {
      statusDot.classList.remove('is-offline');
      try { render(JSON.parse(ev.data)); } catch (e) { console.warn('SSE parse', e); }
    };
    src.onerror = function () {
      statusDot.classList.add('is-offline');
      // EventSource auto-reconnects; nothing more to do
    };
    window.addEventListener('beforeunload', function () { src.close(); });
  }
  connectSSE();

  // ---------- ad rotator ----------
  var adIdx = 0;
  var adFrames = [];

  function loadAds() {
    fetch('ajax/AppointmentBooking/tv_ads_list.php', { cache: 'no-store' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        var ads = (j && j.ads) || [];
        var host = document.getElementById('tvAds');
        var empty = document.getElementById('tvAdsEmpty');
        var root = document.getElementById('tv-display');
        // FIX_B_2340b: collapse the ad row entirely when zero ads are present.
        if (root) root.classList.toggle('is-no-ads', ads.length === 0);
        if (!ads.length) {
          if (empty) empty.style.display = 'flex';
          host.querySelectorAll('.tv-ads-slide').forEach(function (n) { n.remove(); });
          adFrames = [];
          return;
        }
        if (empty) empty.style.display = 'none';
        // build (or rebuild) the slide divs
        host.querySelectorAll('.tv-ads-slide').forEach(function (n) { n.remove(); });
        adFrames = ads.map(function (src) {
          var div = document.createElement('div');
          div.className = 'tv-ads-slide';
          div.style.backgroundImage = 'url("' + src + '")';
          host.appendChild(div);
          return div;
        });
        adIdx = 0;
        if (adFrames[0]) adFrames[0].classList.add('is-active');
      })
      .catch(function () { /* ignore */ });
  }
  function rotateAds() {
    if (adFrames.length < 2) return;
    var prev = adFrames[adIdx];
    adIdx = (adIdx + 1) % adFrames.length;
    var next = adFrames[adIdx];
    if (next) next.classList.add('is-active');
    if (prev) prev.classList.remove('is-active');
  }
  loadAds();
  setInterval(rotateAds, 8000);
  setInterval(loadAds, 5 * 60 * 1000);   // re-scan folder every 5 min

  // ---------- fullscreen on first interaction (TVs often auto-launch in fullscreen kiosk mode already) ----------
  function goFullscreen() {
    var el = document.documentElement;
    var r = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
    if (r) r.call(el);
  }
  document.addEventListener('click', goFullscreen, { once: true });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'F11') { e.preventDefault(); goFullscreen(); }
  });
})();
</script>
