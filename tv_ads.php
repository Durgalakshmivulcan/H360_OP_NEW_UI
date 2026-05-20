<?php require_once("ajax/header.php"); requireCan('view', basename(__FILE__)); ?>
<?php
/* FIX_B_2340: admin UI to upload + manage the rotating ads shown in the
   bottom strip of the waiting-room TV (visitors_doctor_display.php). Strict
   resolution + filesize validation, tuned for a 60-inch panel. */
?>

<style>
  .tvad-wrap { padding: 14px 0 40px; }
  .tvad-hero {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    margin-bottom: 28px;
  }
  @media (max-width: 992px) { .tvad-hero { grid-template-columns: 1fr; } }

  .tvad-card {
    background: #fff;
    border: 1px solid rgba(212, 168, 75, .25);
    border-radius: 6px;
    padding: 22px;
  }
  .tvad-card h5 {
    margin: 0 0 4px;
    font: 600 14px/1.2 'Geist', sans-serif;
  }
  .tvad-card .tvad-eyebrow {
    font: 600 10px/1 'Geist Mono', monospace;
    letter-spacing: .18em; text-transform: uppercase;
    color: #b88a3a;
    margin-bottom: 14px;
  }

  /* upload drop zone */
  .tvad-drop {
    border: 2px dashed rgba(8, 21, 48, .25);
    border-radius: 6px;
    padding: 36px 24px;
    text-align: center;
    transition: border-color .18s ease, background .18s ease;
    cursor: pointer;
    background: rgba(212, 168, 75, .04);
  }
  .tvad-drop:hover, .tvad-drop.is-over {
    border-color: #d4a84b;
    background: rgba(212, 168, 75, .10);
  }
  .tvad-drop input[type=file] { display: none; }
  .tvad-drop-icon { font-size: 36px; color: #b88a3a; margin-bottom: 8px; }
  .tvad-drop-title {
    font: 600 16px/1.3 'Geist', sans-serif;
    color: #0a1a3a;
    margin-bottom: 4px;
  }
  .tvad-drop-sub {
    font: 500 12px/1.4 'Geist', sans-serif;
    color: #5a6478;
  }

  /* rules sidecar */
  .tvad-rules { font: 500 12px/1.55 'Geist', sans-serif; color: #1a2940; }
  .tvad-rules dt {
    font: 600 10px/1 'Geist Mono', monospace;
    letter-spacing: .15em; text-transform: uppercase;
    color: #b88a3a;
    margin-top: 12px;
  }
  .tvad-rules dt:first-child { margin-top: 0; }
  .tvad-rules dd { margin: 4px 0 0; }

  /* live validation panel */
  .tvad-validate {
    display: none;
    margin-top: 18px;
    padding: 14px 18px;
    border-radius: 4px;
    border: 1px solid rgba(8, 21, 48, .12);
    background: #f8f5ec;
  }
  .tvad-validate.is-visible { display: block; }
  .tvad-validate ul { margin: 8px 0 0; padding: 0; list-style: none; }
  .tvad-validate li {
    font: 500 13px/1.5 'Geist', sans-serif;
    padding: 3px 0 3px 24px;
    position: relative;
  }
  .tvad-validate li::before {
    content: '';
    position: absolute; left: 0; top: 7px;
    width: 14px; height: 14px;
    border-radius: 50%;
  }
  .tvad-validate li.tvad-ok::before    { background: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.15); }
  .tvad-validate li.tvad-warn::before  { background: #d97706; box-shadow: 0 0 0 3px rgba(217,119,6,.15); }
  .tvad-validate li.tvad-fail::before  { background: #b9442d; box-shadow: 0 0 0 3px rgba(185,68,45,.15); }

  .tvad-preview {
    margin-top: 16px;
    display: none;
    border: 1px solid rgba(8, 21, 48, .14);
    border-radius: 4px;
    overflow: hidden;
    background: #050d20;
    aspect-ratio: 16 / 5;
  }
  .tvad-preview.is-visible { display: block; }
  .tvad-preview img {
    display: block;
    width: 100%; height: 100%;
    object-fit: cover;
  }
  .tvad-actions { margin-top: 14px; display: flex; gap: 10px; }

  .tvad-btn {
    font: 600 12px/1 'Geist Mono', monospace;
    letter-spacing: .12em; text-transform: uppercase;
    padding: 10px 18px;
    border-radius: 3px;
    border: none;
    cursor: pointer;
    transition: background .15s ease;
  }
  .tvad-btn-primary { background: #d4a84b; color: #050d20; }
  .tvad-btn-primary:hover { background: #b88a3a; color: #fff; }
  .tvad-btn-primary:disabled { background: #d6cfbb; color: #888; cursor: not-allowed; }
  .tvad-btn-ghost { background: transparent; color: #1a2940; border: 1px solid rgba(8,21,48,.2); }
  .tvad-btn-ghost:hover { background: rgba(8,21,48,.05); }

  /* current ads grid */
  .tvad-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 12px;
  }
  .tvad-item {
    background: #fff;
    border: 1px solid rgba(8,21,48,.10);
    border-radius: 6px;
    overflow: hidden;
    display: flex; flex-direction: column;
    transition: box-shadow .15s ease, border-color .15s ease;
  }
  .tvad-item:hover { box-shadow: 0 6px 16px rgba(8,21,48,.08); border-color: rgba(212,168,75,.5); }
  .tvad-item-thumb {
    aspect-ratio: 16 / 5;
    background: #050d20 center / cover no-repeat;
  }
  .tvad-item-meta {
    padding: 12px 14px;
    border-top: 1px solid rgba(8,21,48,.06);
    display: flex; justify-content: space-between; align-items: center; gap: 10px;
  }
  .tvad-item-stats {
    font: 500 11px/1.4 'Geist Mono', monospace;
    color: #5a6478;
  }
  .tvad-item-name {
    font: 600 12px/1.3 'Geist', sans-serif;
    color: #0a1a3a;
    margin-bottom: 4px;
    word-break: break-all;
    max-width: 200px;
  }
  .tvad-del {
    background: transparent; border: 1px solid rgba(185,68,45,.4); color: #b9442d;
    padding: 6px 10px; font: 600 10px/1 'Geist Mono', monospace;
    letter-spacing: .12em; text-transform: uppercase;
    border-radius: 3px; cursor: pointer;
    transition: background .15s ease, color .15s ease;
  }
  .tvad-del:hover { background: #b9442d; color: #fff; }

  .tvad-empty {
    text-align: center; padding: 40px 20px;
    color: #5a6478;
    font: 500 14px/1.4 'Geist', sans-serif;
  }
</style>

<div class="main-content">
  <section class="section">
    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">Appointments</h4></li>
      <li class="breadcrumb-item active">TV Ads</li>
    </ul>

    <div class="tvad-wrap">

      <!-- ============================================================== -->
      <!-- HERO: upload + rules                                            -->
      <!-- ============================================================== -->
      <div class="tvad-hero">
        <div class="tvad-card">
          <div class="tvad-eyebrow">Add new ad</div>
          <h5>Upload an image for the waiting-room TV</h5>
          <p style="font-size:13px;color:#5a6478;margin:6px 0 16px;">
            Images appear in the bottom strip of the visitor display, rotating every 8 seconds.
            Validated for a 60-inch TV — low-quality uploads are rejected.
          </p>

          <label class="tvad-drop" id="tvAdDrop">
            <input type="file" id="tvAdFile" accept="image/jpeg,image/png,image/webp">
            <div class="tvad-drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
            <div class="tvad-drop-title">Click to choose, or drop an image here</div>
            <div class="tvad-drop-sub">JPG, PNG or WebP — 1920×600 minimum (3840×1200 ideal), 50 KB to 5 MB</div>
          </label>

          <div class="tvad-validate" id="tvAdValidate">
            <div style="font:600 11px/1 'Geist Mono',monospace;letter-spacing:.18em;text-transform:uppercase;color:#b88a3a;">Check results</div>
            <ul id="tvAdChecks"></ul>
          </div>

          <div class="tvad-preview" id="tvAdPreview">
            <img id="tvAdPreviewImg" alt="">
          </div>

          <div class="tvad-actions" id="tvAdActions" style="display:none;">
            <button type="button" class="tvad-btn tvad-btn-primary" id="tvAdUploadBtn" disabled>
              Upload to TV
            </button>
            <button type="button" class="tvad-btn tvad-btn-ghost" id="tvAdResetBtn">Choose a different file</button>
          </div>
        </div>

        <aside class="tvad-card">
          <div class="tvad-eyebrow">Quality rules</div>
          <dl class="tvad-rules">
            <dt>Minimum resolution</dt>
            <dd>1920 × 600 px (1080p baseline)</dd>

            <dt>Recommended</dt>
            <dd>3840 × 1200 px (sharp on a 60" 4K TV)</dd>

            <dt>Maximum</dt>
            <dd>4096 × 2160 px</dd>

            <dt>Aspect ratio</dt>
            <dd>16 : 5 ideal (between 2.72 : 1 and 3.68 : 1)</dd>

            <dt>File size</dt>
            <dd>50 KB minimum, 5 MB maximum</dd>

            <dt>Format</dt>
            <dd>JPG, PNG, or WebP — no GIF / animation</dd>
          </dl>
        </aside>
      </div>

      <!-- ============================================================== -->
      <!-- CURRENT ADS                                                     -->
      <!-- ============================================================== -->
      <div class="tvad-card">
        <div class="tvad-eyebrow">Now showing</div>
        <h5>Ads currently on the TV</h5>
        <p style="font-size:13px;color:#5a6478;margin:6px 0 4px;">
          The display rotates these every 8 seconds. Removing one here removes it from the TV within a few minutes (or immediately on next page refresh).
        </p>
        <div class="tvad-grid" id="tvAdGrid">
          <div class="tvad-empty">Loading…</div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
(function () {
  'use strict';

  // mirrored from ajax/tv_ads/_constants.php — kept in sync at write-time
  var RULES = {
    minW: 1920, minH: 600,
    maxW: 4096, maxH: 2160,
    minB: 50 * 1024, maxB: 5 * 1024 * 1024,
    minA: 2.72, maxA: 3.68, idealA: 3.20,
    recoW: 3840, recoH: 1200,
    types: ['image/jpeg', 'image/png', 'image/webp'],
  };

  var $drop      = document.getElementById('tvAdDrop');
  var $file      = document.getElementById('tvAdFile');
  var $validate  = document.getElementById('tvAdValidate');
  var $checks    = document.getElementById('tvAdChecks');
  var $preview   = document.getElementById('tvAdPreview');
  var $previewIm = document.getElementById('tvAdPreviewImg');
  var $actions   = document.getElementById('tvAdActions');
  var $upBtn     = document.getElementById('tvAdUploadBtn');
  var $resetBtn  = document.getElementById('tvAdResetBtn');
  var $grid      = document.getElementById('tvAdGrid');

  var pickedFile = null;

  function fmtBytes(b) {
    if (b < 1024) return b + ' B';
    if (b < 1024 * 1024) return (b / 1024).toFixed(0) + ' KB';
    return (b / 1024 / 1024).toFixed(2) + ' MB';
  }

  function check(state, label) {
    return '<li class="tvad-' + state + '">' + label + '</li>';
  }

  function validateLocal(file) {
    return new Promise(function (resolve) {
      var results = [];
      // type
      if (RULES.types.indexOf(file.type) === -1) {
        results.push({ state: 'fail', label: 'File type <code>' + (file.type || 'unknown') + '</code> not allowed — use JPG, PNG, or WebP.' });
        return resolve({ pass: false, results: results, w: 0, h: 0 });
      }
      results.push({ state: 'ok', label: 'Format · ' + file.type });
      // bytes
      if (file.size < RULES.minB) {
        results.push({ state: 'fail', label: 'File size ' + fmtBytes(file.size) + ' is below the ' + fmtBytes(RULES.minB) + ' minimum — would look soft on the TV.' });
      } else if (file.size > RULES.maxB) {
        results.push({ state: 'fail', label: 'File size ' + fmtBytes(file.size) + ' exceeds the ' + fmtBytes(RULES.maxB) + ' maximum.' });
      } else {
        results.push({ state: 'ok', label: 'File size · ' + fmtBytes(file.size) });
      }
      // dimensions (load via Image)
      var img = new Image();
      var url = URL.createObjectURL(file);
      img.onload = function () {
        var w = img.naturalWidth, h = img.naturalHeight;
        var a = w / Math.max(1, h);
        if (w < RULES.minW || h < RULES.minH) {
          results.push({ state: 'fail', label: 'Resolution ' + w + '×' + h + ' is below the ' + RULES.minW + '×' + RULES.minH + ' minimum.' });
        } else if (w > RULES.maxW || h > RULES.maxH) {
          results.push({ state: 'fail', label: 'Resolution ' + w + '×' + h + ' exceeds the ' + RULES.maxW + '×' + RULES.maxH + ' maximum.' });
        } else if (w < RULES.recoW || h < RULES.recoH) {
          results.push({ state: 'warn', label: 'Resolution ' + w + '×' + h + ' meets the minimum, but ' + RULES.recoW + '×' + RULES.recoH + ' is recommended for a 60-inch 4K TV.' });
        } else {
          results.push({ state: 'ok', label: 'Resolution · ' + w + '×' + h });
        }
        if (a < RULES.minA || a > RULES.maxA) {
          results.push({ state: 'fail', label: 'Aspect ratio ' + a.toFixed(2) + ':1 is outside the allowed band (' + RULES.minA + ':1 to ' + RULES.maxA + ':1). Target is 16:5.' });
        } else {
          results.push({ state: 'ok', label: 'Aspect ratio · ' + a.toFixed(2) + ':1' });
        }
        // preview
        $previewIm.src = url;
        $preview.classList.add('is-visible');
        var pass = !results.some(function (r) { return r.state === 'fail'; });
        resolve({ pass: pass, results: results, w: w, h: h, url: url });
      };
      img.onerror = function () {
        results.push({ state: 'fail', label: 'Could not read image — file may be corrupt.' });
        resolve({ pass: false, results: results, w: 0, h: 0 });
      };
      img.src = url;
    });
  }

  function renderChecks(report) {
    $checks.innerHTML = report.results.map(function (r) { return check(r.state, r.label); }).join('');
    $validate.classList.add('is-visible');
    $actions.style.display = 'flex';
    $upBtn.disabled = !report.pass;
  }

  function reset() {
    pickedFile = null;
    $file.value = '';
    $checks.innerHTML = '';
    $validate.classList.remove('is-visible');
    $preview.classList.remove('is-visible');
    $previewIm.src = '';
    $actions.style.display = 'none';
    $upBtn.disabled = true;
    $upBtn.textContent = 'Upload to TV';
  }

  function handleFile(file) {
    pickedFile = file;
    validateLocal(file).then(renderChecks);
  }

  $drop.addEventListener('click', function () { $file.click(); });
  $file.addEventListener('change', function () {
    if ($file.files && $file.files[0]) handleFile($file.files[0]);
  });
  ['dragenter','dragover'].forEach(function (ev) {
    $drop.addEventListener(ev, function (e) { e.preventDefault(); $drop.classList.add('is-over'); });
  });
  ['dragleave','drop'].forEach(function (ev) {
    $drop.addEventListener(ev, function (e) { e.preventDefault(); $drop.classList.remove('is-over'); });
  });
  $drop.addEventListener('drop', function (e) {
    e.preventDefault();
    if (e.dataTransfer.files && e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]);
  });

  $resetBtn.addEventListener('click', reset);

  $upBtn.addEventListener('click', function () {
    if (!pickedFile) return;
    $upBtn.disabled = true; $upBtn.textContent = 'Uploading…';
    var fd = new FormData();
    fd.append('file', pickedFile);
    fetch('ajax/tv_ads/upload.php', { method: 'POST', body: fd })
      .then(function (r) { return r.json().catch(function () { return { ok: false, error: 'Server returned non-JSON (HTTP ' + r.status + ').' }; }); })
      .then(function (j) {
        if (!j.ok) {
          $upBtn.disabled = false; $upBtn.textContent = 'Upload to TV';
          swal('Upload rejected', j.error || 'Unknown error', 'error');
          return;
        }
        swal('Uploaded', 'Ad saved as ' + j.file + ' (' + j.width + '×' + j.height + ').', 'success');
        reset();
        loadAds();
      })
      .catch(function (e) {
        $upBtn.disabled = false; $upBtn.textContent = 'Upload to TV';
        swal('Network error', String(e), 'error');
      });
  });

  // ----- existing ads -----
  // FIX_B_2341c: build cards with DOM APIs (textContent / setAttribute) so a
  // file dropped via FTP with HTML/JS in its name can never inject markup.
  function escAttr(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
    });
  }
  function loadAds() {
    $grid.innerHTML = '<div class="tvad-empty">Loading…</div>';
    fetch('ajax/tv_ads/list_admin.php', { cache: 'no-store' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        var ads = (j && j.ads) || [];
        if (!ads.length) {
          $grid.innerHTML = '<div class="tvad-empty">No ads uploaded yet. Add the first one above — it will appear on the TV within a few minutes.</div>';
          return;
        }
        $grid.innerHTML = '';
        ads.forEach(function (a) {
          var dims = a.width && a.height ? (a.width + '×' + a.height) : '–';
          var size = a.bytes ? fmtBytes(a.bytes) : '–';

          var article = document.createElement('article');
          article.className = 'tvad-item';

          var thumb = document.createElement('div');
          thumb.className = 'tvad-item-thumb';
          // url is server-emitted via rawurlencode() but extra-escape the quote anyway
          thumb.style.backgroundImage = "url('" + String(a.url || '').replace(/'/g, '%27') + "')";

          var meta = document.createElement('div');
          meta.className = 'tvad-item-meta';

          var info = document.createElement('div');
          var name = document.createElement('div');
          name.className = 'tvad-item-name';
          name.textContent = a.name || '';                  // safe
          var stats = document.createElement('div');
          stats.className = 'tvad-item-stats';
          stats.textContent = dims + ' · ' + size;
          info.appendChild(name);
          info.appendChild(stats);

          var btn = document.createElement('button');
          btn.className = 'tvad-del';
          btn.setAttribute('data-file', a.name || '');     // safe
          btn.textContent = 'Remove';

          meta.appendChild(info);
          meta.appendChild(btn);
          article.appendChild(thumb);
          article.appendChild(meta);
          $grid.appendChild(article);
        });
      })
      .catch(function () {
        $grid.innerHTML = '<div class="tvad-empty">Could not load current ads.</div>';
      });
  }

  $grid.addEventListener('click', function (e) {
    var btn = e.target.closest('.tvad-del');
    if (!btn) return;
    var name = btn.getAttribute('data-file');
    swal({
      title: 'Remove this ad?',
      text: name,
      icon: 'warning', buttons: true, dangerMode: true,
    }).then(function (yes) {
      if (!yes) return;
      var fd = new FormData(); fd.append('file', name);
      fetch('ajax/tv_ads/delete.php', { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (j.ok) { swal('Removed', '', 'success'); loadAds(); }
          else swal('Failed', j.error || 'Could not delete.', 'error');
        });
    });
  });

  loadAds();
})();
</script>
