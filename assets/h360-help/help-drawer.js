/* H360 Page Help Drawer — auto-injects a "?" button next to the page title
   and a slide-out drawer with content from H360_HELP_CONTENT[slug].
   Idempotent marker: FIX_B_2300.

   Per-page entries live in help-content.js (loaded before this file).
   Pages with no entry get no button — silent no-op. */

(function () {
  'use strict';
  if (window.__h360HelpInit) return;
  window.__h360HelpInit = true;

  function pageSlug() {
    var p = (window.location.pathname || '').split('/').pop() || '';
    return p.toLowerCase();
  }

  function escape(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
    });
  }

  // FIX_B_2350: substitute {{vars.X}} placeholders from the server-emitted
  // H360_HELP_VARS so example walkthroughs auto-localise per clinic
  // (AR Clinic shows Dr Ashwin / Dr Rama; KK ENT Clinic shows Dr Rohit /
  //  Dr T Krishnakishor). Substitution happens BEFORE escape() so the values
  // themselves get HTML-escaped — placeholders cannot inject markup.
  // FIX_B_2351: allow zero-length key (`{{vars.}}`) to substitute to empty
  // instead of being left as a literal placeholder. Unknown keys, empty keys,
  // and keys with disallowed characters (hyphens, dots) all resolve safely:
  //   - known key   → its string value
  //   - unknown key → empty string
  //   - empty key   → empty string (was: left literal)
  //   - hyphen/dot  → not matched, left literal (intentional — narrow charset)
  function interpolate(s) {
    var vars = window.H360_HELP_VARS || {};
    return String(s == null ? '' : s).replace(/\{\{\s*vars\.([a-zA-Z0-9_]*)\s*\}\}/g, function (_, k) {
      return (k && (k in vars)) ? String(vars[k]) : '';
    });
  }
  function esc(s) { return escape(interpolate(s)); }

  function renderSection(label, html) {
    return '<section class="h360-help-section">'
      + '<div class="h360-help-section-label">' + escape(label) + '</div>'
      + html + '</section>';
  }

  function renderList(items, klass) {
    if (!items || !items.length) return '';
    return '<ul class="' + klass + '">' + items.map(function (t) {
      return '<li>' + esc(t) + '</li>';
    }).join('') + '</ul>';
  }

  // FIX_B_2350: render an Example block — a titled, numbered walkthrough using
  // realistic per-clinic names. `entry.example` shape:
  //   { title: '...', steps: ['...', '...'] }
  // or just a string (rendered as a single paragraph).
  function renderExample(ex) {
    if (!ex) return '';
    if (typeof ex === 'string') {
      return renderSection('Example', '<p class="h360-help-example-body">' + esc(ex) + '</p>');
    }
    var html = '';
    if (ex.title) html += '<div class="h360-help-example-title">' + esc(ex.title) + '</div>';
    if (ex.steps && ex.steps.length) {
      html += '<ol class="h360-help-example-steps">' + ex.steps.map(function (t) {
        return '<li>' + esc(t) + '</li>';
      }).join('') + '</ol>';
    }
    if (ex.note) html += '<p class="h360-help-example-note">' + esc(ex.note) + '</p>';
    return renderSection('Example', html);
  }

  function buildDrawer(entry) {
    var body = '';
    if (entry.purpose) {
      body += renderSection('Purpose', '<p class="h360-help-purpose">' + esc(entry.purpose) + '</p>');
    }
    if (entry.steps && entry.steps.length) {
      body += renderSection('How to use', renderList(entry.steps, 'h360-help-steps'));
    }
    if (entry.example) {
      body += renderExample(entry.example);
    }
    if (entry.tips && entry.tips.length) {
      body += renderSection('Tips', renderList(entry.tips, 'h360-help-bullets'));
    }
    if (entry.warnings && entry.warnings.length) {
      body += renderSection('Watch out for', renderList(entry.warnings, 'h360-help-bullets is-warn'));
    }

    var backdrop = document.createElement('div');
    backdrop.className = 'h360-help-backdrop';
    backdrop.setAttribute('data-h360-help', 'backdrop');

    var drawer = document.createElement('aside');
    drawer.className = 'h360-help-drawer';
    drawer.setAttribute('role', 'dialog');
    drawer.setAttribute('aria-modal', 'true');
    drawer.setAttribute('aria-labelledby', 'h360HelpTitle');
    drawer.innerHTML =
      '<header class="h360-help-head">'
      +   '<div>'
      +     '<div class="h360-help-eyebrow">' + esc(entry.eyebrow || 'Page help') + '</div>'
      +     '<h2 id="h360HelpTitle" class="h360-help-title">' + esc(entry.title || 'Help') + '</h2>'
      +   '</div>'
      +   '<button type="button" class="h360-help-close" aria-label="Close help">&times;</button>'
      + '</header>'
      + '<div class="h360-help-body">' + body + '</div>'
      + '<footer class="h360-help-foot">'
      +   '<span>H360 · Page guide</span>'
      +   '<span>' + escape(entry.updated || '') + '</span>'
      + '</footer>';

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    function close() {
      backdrop.classList.remove('is-open');
      drawer.classList.remove('is-open');
      document.removeEventListener('keydown', onEsc);
    }
    function open() {
      backdrop.classList.add('is-open');
      drawer.classList.add('is-open');
      document.addEventListener('keydown', onEsc);
      // focus close for a11y
      setTimeout(function(){ drawer.querySelector('.h360-help-close').focus(); }, 80);
    }
    function onEsc(e) { if (e.key === 'Escape') close(); }

    backdrop.addEventListener('click', close);
    drawer.querySelector('.h360-help-close').addEventListener('click', close);

    return { open: open, close: close };
  }

  function findAnchor() {
    // last active breadcrumb (universal page-title anchor across H360)
    var anchors = document.querySelectorAll('.breadcrumb .breadcrumb-item.active, .breadcrumb .breadcrumb-item h4.page-title');
    if (anchors && anchors.length) return anchors[anchors.length - 1];
    // FIX_B_2371: dashboard partials use custom title classes — the doctor
    // and pharmacist dashboards have no <h1>, so the help button never
    // anchored on those pages. Check the partial-specific selectors first.
    var dashTitle = document.querySelector('[data-h360-help-anchor], .hdoc-title, .ph-title, .sa-title, .ad-title, .acc-title');
    if (dashTitle) return dashTitle;
    // generic fallback
    return document.querySelector('h4.page-title, .content-header h1, h1');
  }

  function injectButton(anchor, controller) {
    if (!anchor || anchor.querySelector('.h360-help-btn')) return;
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'h360-help-btn';
    btn.setAttribute('aria-label', 'Show page help');
    btn.innerHTML = '<span class="h360-help-btn-icon">?</span><span class="h360-help-btn-label">Help</span>';
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      controller.open();
    });
    anchor.appendChild(document.createTextNode(' '));
    anchor.appendChild(btn);
  }

  // FIX_B_2370: dashboard.php renders a different partial per role (SA /
  // Doctor / Pharmacist / Accountant / Admin), all at the same URL. Prefer
  // a role-keyed registry entry — e.g. `dashboard.sa.php` for SA — so each
  // role sees a drawer that describes their actual KPI tiles and cards.
  // Falls back to the plain `dashboard.php` entry if no role-keyed one exists.
  function resolveEntry(registry, slug) {
    if (slug === 'dashboard.php') {
      var vars = window.H360_HELP_VARS || {};
      var role = (vars.roleKey || '').toLowerCase();
      if (role) {
        var keyed = registry['dashboard.' + role + '.php'];
        if (keyed) return keyed;
      }
    }
    return registry[slug];
  }

  function init() {
    var registry = window.H360_HELP_CONTENT || {};
    var slug = pageSlug();
    var entry = resolveEntry(registry, slug);
    if (!entry) return;  // no content registered → silent no-op
    var anchor = findAnchor();
    if (!anchor) return;
    var controller = buildDrawer(entry);
    injectButton(anchor, controller);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
