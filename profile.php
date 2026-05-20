<?php
require_once("ajax/header.php");
// FIX_B_2360: profile.php redesign — context-aware variants for Doctor / Admin
// / Super Admin / generic. Replaces the prior static one-form-fits-all layout.
// All AJAX endpoints (photo upload here, password via ajax/ChangePassword,
// signature via ajax/Profile) preserved unchanged.
requireCan('view', 'profile.php');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$qry = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
$ProfileData = mysqli_fetch_object($qry);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id = $SessionUserId"));

// ---- variant detection ----
$linkedDoctor   = null;
$doctorProfile  = null;
if ($SessionUserId !== '') {
    $dQ = mysqli_query($conn, "SELECT * FROM doctors WHERE security_id='$SessionUserId' AND status='1' LIMIT 1");
    if ($dQ && mysqli_num_rows($dQ)) {
        $linkedDoctor = mysqli_fetch_assoc($dQ);
        $docId = (int)$linkedDoctor['doc_id'];
        $pQ = mysqli_query($conn, "SELECT * FROM doctor_profiles WHERE doc_id='$docId' LIMIT 1");
        if ($pQ && mysqli_num_rows($pQ)) $doctorProfile = mysqli_fetch_assoc($pQ);
    }
}
$variant = $linkedDoctor ? 'doctor'
         : ($SessionRoleId === 1 ? 'sa'
         : ($SessionRoleId === 6 ? 'admin' : 'generic'));

// JSON decoders (safe + null-aware)
$decode = function ($json) { $a = $json ? json_decode($json, true) : null; return is_array($a) ? $a : []; };
$education  = $decode($doctorProfile['education_json']  ?? null);
$hospitals  = $decode($doctorProfile['hospitals_json']  ?? null);
$expertise  = $decode($doctorProfile['expertise_json']  ?? null);
$awards     = $decode($doctorProfile['awards_json']     ?? null);
$philosophy = $decode($doctorProfile['philosophy_json'] ?? null);
$opd        = $decode($doctorProfile['opd_timings_json'] ?? null);
$social     = $decode($doctorProfile['social_json']     ?? null);

// Photo upload (FIX_B_016 sanitization intact + FIX_B_2365: doctors save to
// doctor_images/ and update doctors.doc_img so the TV display + Rx header
// stay in sync with the profile hero; everyone else saves to img/ as before).
if (isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] !== '') {
    $imageName = $_FILES["image"]["name"];
    $imageSize = $_FILES["image"]["size"];
    $tmpName   = $_FILES["image"]["tmp_name"];
    $validImageExtension = ['jpg', 'jpeg', 'png'];
    $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    if (!in_array($imageExtension, $validImageExtension, true)) {
        echo "<script>alert('Invalid Image Extension');document.location.href='';</script>";
    } elseif ($imageSize > 1500000) {
        echo "<script>alert('Image Size Is Too Large');document.location.href='';</script>";
    } else {
        $safeUser = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$SessionUserId);
        $rand     = bin2hex(random_bytes(6));
        if ($linkedDoctor) {
            $docId = (int)$linkedDoctor['doc_id'];
            $newImageName = $docId . '_' . $safeUser . '_' . date("Ymd_His") . '_' . $rand . '.' . $imageExtension;
            @move_uploaded_file($tmpName, 'doctor_images/' . $newImageName);
            mysqli_query($conn, "UPDATE doctors SET doc_img='$newImageName' WHERE doc_id=$docId");
        } else {
            $newImageName = $safeUser . '_' . date("Ymd_His") . '_' . $rand . '.' . $imageExtension;
            @move_uploaded_file($tmpName, 'img/' . $newImageName);
            mysqli_query($conn, "UPDATE security SET image_url = '$newImageName' WHERE security_id = $SessionUserId");
        }
        echo "<script>document.location.href='';</script>";
        exit;
    }
}

// helpers for rendering
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$displayName = $linkedDoctor ? formatDoctorName($linkedDoctor['doctor_name']) : ($ProfileData->admin_name ?? '—');
// FIX_B_2365: prefer the doctor's headshot (doctor_images/) for doctor users —
// the security.image_url column carries stale/wrong values for them (e.g.
// Ashwin had " - 2025.09.19...png" pointing nowhere). Fall back to security
// for non-doctors, then to a generic placeholder.
if ($linkedDoctor && !empty($linkedDoctor['doc_img']) && is_file(__DIR__ . '/doctor_images/' . $linkedDoctor['doc_img'])) {
    $avatar = 'doctor_images/' . rawurlencode($linkedDoctor['doc_img']);
} elseif (!empty($ProfileData->image_url) && is_file(__DIR__ . '/img/' . $ProfileData->image_url)) {
    $avatar = 'img/' . rawurlencode($ProfileData->image_url);
} else {
    $avatar = 'assets/img/health.png';
}
?>

<style>
/* FIX_B_2360 — Sovereign Institutional profile redesign
   FIX_B_2363: force explicit colour + background on every text-bearing block
   so the host theme (dark mode toggle, custom.css overrides) cannot bleed
   through and produce dark-on-dark. Every card + tile + label + input
   carries its own explicit pair. */
.pf-shell, .pf-shell * { font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
.pf-shell { color: #0a1a3a !important; background: transparent; }

/* eyebrow + section labels */
.pf-eyebrow {
  font: 600 10px/1 'Geist Mono', ui-monospace, monospace;
  letter-spacing: .22em; text-transform: uppercase;
  color: #8a651e !important;   /* darker gold for ≥4.5:1 on white */
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 12px;
}
.pf-eyebrow::after { content:''; flex:1; height:1px; background: rgba(212,168,75,.25); }

/* ============== HERO ============== */
.pf-hero {
  display: grid;
  grid-template-columns: 220px 1fr auto;
  gap: 32px;
  align-items: center;
  padding: 32px 36px;
  background: linear-gradient(135deg, #050d20 0%, #0f2147 60%, #112553 100%) !important;
  color: #f5efe1 !important;
  border-radius: 6px;
  position: relative;
  overflow: hidden;
  margin-bottom: 24px;
}
/* FIX_B_2364: the parent .pf-shell { color: #0a1a3a !important } from the
   contrast hardening cascaded INTO the navy hero and forced every nested
   text element to navy-on-navy (invisible). Pin each hero text colour
   explicitly so .pf-shell can't override. */
.pf-hero,
.pf-hero .pf-hero-meta,
.pf-hero .pf-hero-meta * { color: #f5efe1 !important; }
.pf-hero::after {
  content: ''; position: absolute; inset: 0;
  background: radial-gradient(ellipse at 90% 100%, rgba(212,168,75,.18) 0%, transparent 60%);
  pointer-events: none;
}
.pf-hero-photo {
  width: 200px; height: 200px; border-radius: 50%;
  padding: 5px;
  background: linear-gradient(135deg, #d4a84b, #b88a3a);
  position: relative; z-index: 1;
  box-shadow: 0 16px 50px rgba(0,0,0,.4);
}
.pf-hero-photo img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #050d20; }
.pf-hero-photo .pf-photo-edit {
  position: absolute; bottom: 12px; right: 12px;
  width: 36px; height: 36px; border-radius: 50%;
  background: #d4a84b !important; color: #050d20 !important;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: transform .15s ease;
  border: 2px solid #050d20;
}
.pf-hero-photo .pf-photo-edit i { color: #050d20 !important; }
.pf-hero-photo .pf-photo-edit:hover { transform: scale(1.08); }
.pf-hero-photo .pf-photo-edit input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

.pf-hero-meta { position: relative; z-index: 1; min-width: 0; }
.pf-hero-role {
  font: 600 11px/1 'Geist Mono', monospace;
  letter-spacing: .22em; text-transform: uppercase;
  color: #d4a84b !important;
  margin-bottom: 12px;
}
.pf-hero-name {
  font: 700 36px/1.05 'Geist', sans-serif;
  letter-spacing: -.01em;
  color: #f5efe1 !important;
  margin: 0 0 8px;
}
.pf-hero-tagline {
  font: 500 16px/1.4 'Geist', sans-serif;
  color: #ddd5c4 !important;
  margin-bottom: 16px;
}
.pf-hero-pills {
  display: flex; flex-wrap: wrap; gap: 8px;
}
.pf-pill {
  font: 600 10px/1 'Geist Mono', monospace;
  letter-spacing: .14em; text-transform: uppercase;
  padding: 7px 12px;
  border: 1px solid rgba(212,168,75,.5);
  color: #d4a84b !important;
  background: transparent !important;
  border-radius: 12px;
}
.pf-pill.is-solid { background: #d4a84b !important; color: #050d20 !important; }

/* ============== STRIP (KPI tiles) ============== */
.pf-strip {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
  margin-bottom: 28px;
}
.pf-strip-tile {
  background: #ffffff !important;
  color: #0a1a3a !important;
  border: 1px solid rgba(8,21,48,.10);
  border-radius: 4px;
  padding: 16px 18px;
  transition: border-color .15s ease;
}
.pf-strip-tile:hover { border-color: rgba(212,168,75,.5); }
.pf-strip-label {
  font: 600 10px/1 'Geist Mono', monospace;
  letter-spacing: .18em; text-transform: uppercase;
  color: #8a651e !important; margin-bottom: 8px;     /* darker gold for 4.5:1 on white */
}
.pf-strip-value {
  font: 700 28px/1 'Geist', sans-serif;
  color: #0a1a3a !important;
}

/* ============== SECTIONS ============== */
.pf-grid {
  display: grid; grid-template-columns: 2fr 1fr; gap: 24px;
  margin-bottom: 28px;
}
@media (max-width: 1100px) { .pf-grid { grid-template-columns: 1fr; } }

.pf-card {
  background: #ffffff !important;
  color: #0a1a3a !important;
  border: 1px solid rgba(8,21,48,.10);
  border-radius: 6px;
  padding: 22px 24px;
  margin-bottom: 20px;
}
.pf-card h3, .pf-card h4, .pf-card p, .pf-card div, .pf-card span, .pf-card li {
  color: #0a1a3a;
}
.pf-card h3 {
  font: 600 16px/1.2 'Geist', sans-serif;
  margin: 0 0 14px;
}

/* qualifications timeline */
.pf-qual {
  list-style: none; margin: 0; padding: 0;
  position: relative;
}
.pf-qual::before {
  content: ''; position: absolute; left: 14px; top: 0; bottom: 0;
  width: 2px; background: rgba(212,168,75,.25);
}
.pf-qual li {
  position: relative;
  padding: 10px 0 14px 42px;
}
.pf-qual li::before {
  content: ''; position: absolute; left: 8px; top: 14px;
  width: 14px; height: 14px; border-radius: 50%;
  background: #d4a84b; box-shadow: 0 0 0 4px #fff, 0 0 0 5px rgba(212,168,75,.4);
}
.pf-qual-prog { font: 600 14px/1.3 'Geist', sans-serif; color: #0a1a3a !important; }
.pf-qual-inst { font: 500 13px/1.3 'Geist', sans-serif; color: #3a4357 !important; margin-top: 2px; }

/* expertise cards */
.pf-exp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; }
.pf-exp-card {
  border: 1px solid rgba(8,21,48,.08);
  border-left: 3px solid #d4a84b;
  border-radius: 0 4px 4px 0;
  padding: 14px 16px;
  background: #fbf6e8 !important;          /* opaque cream so theme can't darken */
  color: #0a1a3a !important;
}
.pf-exp-title { font: 600 14px/1.3 'Geist', sans-serif; color: #0a1a3a !important; margin-bottom: 4px; }
.pf-exp-summary { font: 500 13px/1.5 'Geist', sans-serif; color: #3a4357 !important; }
.pf-exp-tools { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.pf-exp-tool {
  font: 600 9px/1 'Geist Mono', monospace;
  letter-spacing: .12em; text-transform: uppercase;
  padding: 4px 8px; border-radius: 3px;
  background: #050d20 !important; color: #d4a84b !important;
}

/* hospitals */
.pf-hosp { display: flex; flex-direction: column; gap: 12px; }
.pf-hosp-card {
  border: 1px solid rgba(8,21,48,.08);
  border-radius: 4px; padding: 12px 14px;
  background: #ffffff !important;
  color: #0a1a3a !important;
}
.pf-hosp-name { font: 600 14px/1.3 'Geist', sans-serif; color: #0a1a3a !important; }
.pf-hosp-role { font: 500 11px/1 'Geist Mono', monospace; letter-spacing: .15em; text-transform: uppercase; color: #8a651e !important; margin: 4px 0; }
.pf-hosp-loc { font: 500 12px/1.3 'Geist', sans-serif; color: #3a4357 !important; }
.pf-hosp-focus { font: 500 12px/1.4 'Geist', sans-serif; color: #3a4357 !important; margin-top: 6px; }

/* OPD timings */
.pf-opd-days { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
.pf-opd-day {
  font: 600 10px/1 'Geist Mono', monospace;
  letter-spacing: .14em; text-transform: uppercase;
  padding: 6px 10px; border-radius: 3px;
  background: #f4e8c5 !important; color: #5a3f0a !important;     /* opaque cream + dark gold */
}
.pf-opd-day.is-off { background: #eef0f4 !important; color: #6d7689 !important; opacity: 1; }
.pf-opd-time {
  font: 700 22px/1 'Geist Mono', monospace;
  color: #0a1a3a !important; margin: 4px 0;
}
.pf-opd-note { font: 500 12px/1.4 'Geist', sans-serif; color: #3a4357 !important; font-style: italic; }

/* awards list */
.pf-bullets { list-style: none; margin: 0; padding: 0; }
.pf-bullets li {
  position: relative; padding: 6px 0 6px 22px;
  font: 500 13px/1.5 'Geist', sans-serif;
  color: #0a1a3a !important;
}
.pf-bullets li::before {
  content: '★'; position: absolute; left: 0; top: 6px;
  color: #d4a84b; font-size: 14px;
}
.pf-bullets.is-philosophy li::before { content: '·'; font-size: 24px; top: -2px; }

/* social/contact */
.pf-social { display: flex; flex-wrap: wrap; gap: 12px; }
.pf-social a {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 14px; border-radius: 4px;
  background: #050d20 !important; color: #f5efe1 !important;
  font: 600 12px/1 'Geist Mono', monospace;
  letter-spacing: .12em; text-transform: uppercase;
  text-decoration: none !important;
  transition: background .15s ease, color .15s ease;
}
.pf-social a:hover, .pf-social a:focus { background: #d4a84b !important; color: #050d20 !important; }
.pf-social a i { color: #d4a84b !important; }
.pf-social a:hover i, .pf-social a:focus i { color: #050d20 !important; }

/* responsibilities (admin/sa) */
.pf-resp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; }
.pf-resp-card {
  border: 1px solid rgba(8,21,48,.08);
  border-radius: 4px; padding: 14px 16px;
  background: #fbf6e8 !important;
  color: #0a1a3a !important;
  border-left: 3px solid #d4a84b;
}
.pf-resp-icon { color: #8a651e !important; margin-bottom: 6px; font-size: 18px; }
.pf-resp-title { font: 600 13px/1.3 'Geist', sans-serif; color: #0a1a3a !important; margin-bottom: 4px; }
.pf-resp-summary { font: 500 12px/1.5 'Geist', sans-serif; color: #3a4357 !important; }

/* editable form */
.pf-form-row { display: grid; grid-template-columns: 160px 1fr; gap: 16px; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(8,21,48,.06); }
.pf-form-row:last-child { border-bottom: none; }
.pf-form-label { font: 600 11px/1 'Geist Mono', monospace; letter-spacing: .14em; text-transform: uppercase; color: #8a651e !important; }
.pf-form-row input, .pf-form-row textarea {
  font: 500 14px/1.4 'Geist', sans-serif;
  padding: 9px 12px;
  border: 1px solid rgba(8,21,48,.18);
  border-radius: 3px;
  width: 100%;
  background: #ffffff !important;
  color: #0a1a3a !important;
}
.pf-form-row input::placeholder { color: #8a93a3 !important; }
.pf-form-row > div { color: #0a1a3a; }   /* the value cell in read-only rows */
.pf-form-row input:focus, .pf-form-row textarea:focus { border-color: #d4a84b; outline: none; }
.pf-btn {
  font: 600 11px/1 'Geist Mono', monospace;
  letter-spacing: .14em; text-transform: uppercase;
  padding: 11px 22px; border: none; border-radius: 3px;
  background: #d4a84b; color: #050d20;
  cursor: pointer;
  transition: background .15s ease;
}
.pf-btn:hover { background: #b88a3a; color: #fff; }
.pf-btn.is-ghost { background: transparent; color: #0a1a3a; border: 1px solid rgba(8,21,48,.2); }
.pf-btn.is-ghost:hover { background: rgba(8,21,48,.05); color: #0a1a3a; }

.pf-empty { color: #3a4357 !important; font: 500 13px/1.4 'Geist', sans-serif; padding: 8px 0; font-style: italic; }
</style>

<div class="main-content">
  <section class="section">
    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">My Profile</h4></li>
      <?php
        // FIX_B_2365a: previously rendered as "Sa" — ucfirst on the raw key.
        // Map to a friendly label so the SA breadcrumb reads "Super Admin".
        $variantLabel = [
            'doctor'  => 'Doctor',
            'admin'   => 'Admin',
            'sa'      => 'Super Admin',
            'generic' => 'Staff',
        ][$variant] ?? ucfirst($variant);
      ?>
      <li class="breadcrumb-item active"><?= $h($variantLabel) ?></li>
    </ul>

    <div class="pf-shell">

      <!-- ================== HERO ================== -->
      <div class="pf-hero">
        <form id="photoForm" enctype="multipart/form-data" method="post" style="display:contents;">
          <input type="hidden" name="id" value="<?= (int)$SessionUserId ?>">
          <input type="hidden" name="name" value="<?= $h($displayName) ?>">
          <div class="pf-hero-photo">
            <img src="<?= $h($avatar) ?>" alt="">
            <label class="pf-photo-edit" title="Change photo">
              <i class="fas fa-camera" style="font-size:14px;"></i>
              <input type="file" name="image" accept=".jpg,.jpeg,.png" onchange="document.getElementById('photoForm').submit()">
            </label>
          </div>
        </form>

        <div class="pf-hero-meta">
          <div class="pf-hero-role">
            <?php
              if ($variant === 'doctor')  echo $h($linkedDoctor['doctor_specialization'] ?: 'Consultant');
              elseif ($variant === 'admin') echo 'Admin · Operations & Finance';
              elseif ($variant === 'sa')    echo 'Super Admin · Governance';
              else echo $h(getCurrentRoleName($conn) ?: 'Staff');
            ?>
          </div>
          <h1 class="pf-hero-name"><?= $h($displayName) ?></h1>
          <?php if ($variant === 'doctor' && !empty($doctorProfile['tagline'])): ?>
            <div class="pf-hero-tagline"><?= $h($doctorProfile['tagline']) ?></div>
          <?php elseif ($variant === 'admin'): ?>
            <div class="pf-hero-tagline">Cross-doctor operations &middot; revenue ownership &middot; doctor-switcher capability</div>
          <?php elseif ($variant === 'sa'): ?>
            <div class="pf-hero-tagline">Platform governance &middot; org/roles/menus master &middot; audit owner</div>
          <?php endif; ?>

          <div class="pf-hero-pills">
            <?php if ($variant === 'doctor'): ?>
              <?php foreach (array_slice($hospitals, 0, 2) as $hx): ?>
                <span class="pf-pill"><?= $h($hx['name']) ?></span>
              <?php endforeach; ?>
              <?php if (!empty($doctorProfile['years_experience'])): ?>
                <span class="pf-pill is-solid"><?= (int)$doctorProfile['years_experience'] ?>+ Years</span>
              <?php endif; ?>
            <?php elseif ($variant === 'admin'): ?>
              <span class="pf-pill">Doctor switcher</span>
              <span class="pf-pill">Both OP rooms</span>
              <span class="pf-pill is-solid">Full data access</span>
            <?php elseif ($variant === 'sa'): ?>
              <span class="pf-pill">Bootstrap account</span>
              <span class="pf-pill">Multi-org</span>
              <span class="pf-pill is-solid">All menus</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php /* ========================= DOCTOR VARIANT ========================= */
            if ($variant === 'doctor'): ?>

        <!-- SNAPSHOT STRIP -->
        <div class="pf-strip">
          <?php if (!empty($doctorProfile['years_experience'])): ?>
            <div class="pf-strip-tile"><div class="pf-strip-label">Experience</div><div class="pf-strip-value"><?= (int)$doctorProfile['years_experience'] ?>+ <span style="font-size:14px;color:#5a6478;">years</span></div></div>
          <?php endif; ?>
          <?php if (!empty($hospitals)): ?>
            <div class="pf-strip-tile"><div class="pf-strip-label">Affiliations</div><div class="pf-strip-value"><?= count($hospitals) ?></div></div>
          <?php endif; ?>
          <?php if (!empty($expertise)): ?>
            <div class="pf-strip-tile"><div class="pf-strip-label">Expertise areas</div><div class="pf-strip-value"><?= count($expertise) ?></div></div>
          <?php endif; ?>
          <?php if (!empty($awards)): ?>
            <div class="pf-strip-tile"><div class="pf-strip-label">Awards</div><div class="pf-strip-value"><?= count($awards) ?></div></div>
          <?php endif; ?>
        </div>

        <div class="pf-grid">
          <div>
            <?php if (!empty($expertise)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Expertise &amp; procedures</div>
                <div class="pf-exp-grid">
                  <?php foreach ($expertise as $e): ?>
                    <div class="pf-exp-card">
                      <div class="pf-exp-title"><?= $h($e['title'] ?? '') ?></div>
                      <div class="pf-exp-summary"><?= $h($e['summary'] ?? '') ?></div>
                      <?php if (!empty($e['tools'])): ?>
                        <div class="pf-exp-tools">
                          <?php foreach ($e['tools'] as $t): ?>
                            <span class="pf-exp-tool"><?= $h($t) ?></span>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($awards)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Awards &amp; recognition</div>
                <ul class="pf-bullets">
                  <?php foreach ($awards as $a): ?><li><?= $h($a) ?></li><?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($philosophy)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Clinical philosophy</div>
                <ul class="pf-bullets is-philosophy">
                  <?php foreach ($philosophy as $p): ?><li><?= $h($p) ?></li><?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>

          <div>
            <?php if (!empty($education)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Education</div>
                <ul class="pf-qual">
                  <?php foreach ($education as $ed): ?>
                    <li>
                      <div class="pf-qual-prog"><?= $h($ed['program'] ?? '') ?></div>
                      <div class="pf-qual-inst"><?= $h(($ed['institution'] ?? '') . ' · ' . ($ed['location'] ?? '')) ?></div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($hospitals)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Affiliations</div>
                <div class="pf-hosp">
                  <?php foreach ($hospitals as $hx): ?>
                    <div class="pf-hosp-card">
                      <div class="pf-hosp-name"><?= $h($hx['name'] ?? '') ?></div>
                      <div class="pf-hosp-role"><?= $h($hx['role'] ?? '') ?></div>
                      <?php if (!empty($hx['location'])): ?>
                        <div class="pf-hosp-loc"><i class="fas fa-map-marker-alt"></i> <?= $h($hx['location']) ?></div>
                      <?php endif; ?>
                      <?php if (!empty($hx['focus'])): ?>
                        <div class="pf-hosp-focus"><?= $h($hx['focus']) ?></div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($opd)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">OPD timings</div>
                <?php
                  $allDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
                  $openDays = array_map(fn($d) => substr((string)$d, 0, 3), $opd['days'] ?? []);
                ?>
                <div class="pf-opd-days">
                  <?php foreach ($allDays as $d): ?>
                    <span class="pf-opd-day <?= in_array($d, $openDays, true) ? '' : 'is-off' ?>"><?= $d ?></span>
                  <?php endforeach; ?>
                </div>
                <?php if (!empty($opd['start']) && !empty($opd['end'])): ?>
                  <div class="pf-opd-time"><?= $h($opd['start']) ?> &mdash; <?= $h($opd['end']) ?></div>
                <?php endif; ?>
                <?php if (!empty($opd['notes'])): ?>
                  <div class="pf-opd-note"><?= $h($opd['notes']) ?></div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($social)): ?>
              <div class="pf-card">
                <div class="pf-eyebrow">Connect</div>
                <div class="pf-social">
                  <?php if (!empty($social['website'])): ?>
                    <a href="<?= $h($social['website']) ?>" target="_blank" rel="noopener"><i class="fas fa-globe"></i> Website</a>
                  <?php endif; ?>
                  <?php if (!empty($social['phone'])): ?>
                    <a href="tel:<?= $h(preg_replace('/[^+0-9]/', '', $social['phone'])) ?>"><i class="fas fa-phone"></i> Call</a>
                  <?php endif; ?>
                  <?php if (!empty($social['email'])): ?>
                    <a href="mailto:<?= $h($social['email']) ?>"><i class="fas fa-envelope"></i> Email</a>
                  <?php endif; ?>
                  <?php if (!empty($social['instagram'])): ?>
                    <a href="https://instagram.com/<?= $h(ltrim($social['instagram'], '@')) ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Instagram</a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

      <?php /* ========================= ADMIN VARIANT ========================= */
            elseif ($variant === 'admin'): ?>
        <div class="pf-card">
          <div class="pf-eyebrow">Responsibilities</div>
          <div class="pf-resp-grid">
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-cogs"></i></div><div class="pf-resp-title">Clinic operations</div><div class="pf-resp-summary">Staff, slots, appointments, billing, refunds across both OP rooms.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-chart-line"></i></div><div class="pf-resp-title">Finance &amp; reports</div><div class="pf-resp-summary">Daily closes, revenue reports, refund tracker, payment-method splits.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-exchange-alt"></i></div><div class="pf-resp-title">Doctor switcher</div><div class="pf-resp-summary">Narrow any view to one doctor, or All Doctors to see merged data.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-user-shield"></i></div><div class="pf-resp-title">Access guardrails</div><div class="pf-resp-summary">Sees almost all menus except Super Admin governance (org, roles, menus).</div></div>
          </div>
        </div>
        <div class="pf-card">
          <div class="pf-eyebrow">Quick links</div>
          <div class="pf-social">
            <a href="audit_log.php"><i class="fas fa-shield-alt"></i> Audit Log</a>
            <a href="dailyreports.php"><i class="fas fa-calendar-day"></i> Daily Report</a>
            <a href="RevenueReport.php"><i class="fas fa-rupee-sign"></i> Revenue</a>
            <a href="refunds.php"><i class="fas fa-undo"></i> Refunds</a>
            <a href="doctor.php"><i class="fas fa-user-md"></i> Doctors</a>
          </div>
        </div>

      <?php /* ========================= SA VARIANT ========================= */
            elseif ($variant === 'sa'): ?>
        <div class="pf-card">
          <div class="pf-eyebrow">Governance role</div>
          <div class="pf-resp-grid">
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-building"></i></div><div class="pf-resp-title">Organisations</div><div class="pf-resp-summary">Onboard sister clinics, edit master records, multi-org rollups.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-key"></i></div><div class="pf-resp-title">Roles &amp; menus</div><div class="pf-resp-summary">Bind permissions, register new menus, enforce server-side gates.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-database"></i></div><div class="pf-resp-title">DB health</div><div class="pf-resp-summary">Row counts, audit feed, governance change stream.</div></div>
            <div class="pf-resp-card"><div class="pf-resp-icon"><i class="fas fa-shield-alt"></i></div><div class="pf-resp-title">Audit owner</div><div class="pf-resp-summary">Append-only log of every meaningful action; first stop on discrepancies.</div></div>
          </div>
        </div>
        <div class="pf-card">
          <div class="pf-eyebrow">Quick links</div>
          <div class="pf-social">
            <a href="organization.php"><i class="fas fa-building"></i> Organizations</a>
            <a href="roles.php"><i class="fas fa-key"></i> Roles</a>
            <a href="menus.php"><i class="fas fa-bars"></i> Menus</a>
            <a href="audit_log.php"><i class="fas fa-shield-alt"></i> Audit Log</a>
            <a href="databaseTruncate.php"><i class="fas fa-database"></i> Administration</a>
          </div>
        </div>

      <?php endif; /* end variants */ ?>

      <!-- ================== PERSONAL DETAILS (every variant) ================== -->
      <div class="pf-card">
        <div class="pf-eyebrow">Personal details</div>
        <div class="pf-form-row">
          <div class="pf-form-label">Name</div>
          <div><?= $h($ProfileData->admin_name ?? '—') ?></div>
        </div>
        <div class="pf-form-row">
          <div class="pf-form-label">Email</div>
          <div><?= $h($ProfileData->email ?? '—') ?></div>
        </div>
        <div class="pf-form-row">
          <div class="pf-form-label">Mobile</div>
          <div><?= $h($ProfileData->contact ?? '—') ?></div>
        </div>
        <div class="pf-form-row">
          <div class="pf-form-label">User code</div>
          <div><?= $h($ProfileData->user_code ?? '—') ?></div>
        </div>
        <div class="pf-form-row">
          <div class="pf-form-label">Role</div>
          <div><?= $h(getCurrentRoleName($conn) ?: '—') ?></div>
        </div>
        <p class="pf-empty" style="margin-top:10px;">To change your name, email, mobile, or role, an admin updates them on the User Registration page.</p>
      </div>

      <!-- ================== CHANGE PASSWORD (every variant) ================== -->
      <div class="pf-card">
        <div class="pf-eyebrow">Change password</div>
        <form id="pwForm" autocomplete="off" onsubmit="return changePassword(event)">
          <div class="pf-form-row"><div class="pf-form-label">Current password</div><div><input type="password" name="old_password" id="old_password" required></div></div>
          <div class="pf-form-row"><div class="pf-form-label">New password</div><div><input type="password" name="new_password" id="new_password" required minlength="6"></div></div>
          <div class="pf-form-row"><div class="pf-form-label">Confirm new</div><div><input type="password" name="confirm_password" id="confirm_password" required minlength="6"></div></div>
          <div style="margin-top:14px;text-align:right;"><button type="submit" class="pf-btn">Update password</button></div>
        </form>
      </div>

      <?php /* ================== DIGITAL SIGNATURE (doctor only — FIX_B_2361) ================== */
            if ($variant === 'doctor'): ?>
        <div class="pf-card">
          <div class="pf-eyebrow">Digital signature</div>
          <p class="pf-empty" style="margin:0 0 12px;">Appears on the bottom of printed prescriptions and reports. Recommended 300×100 px, max 1 MB, PNG with transparent background looks cleanest.</p>
          <?php
            // FIX_B_2365b: the real signature directory is "signature/" (singular),
            // and the real upload endpoint is ajax/profiles/signature.php which
            // returns the new filename on success (not "1"/"ok"). The first cut
            // of this redesign guessed a non-existent path.
            $sigUrl = !empty($ProfileData->signature_url) ? 'signature/' . $ProfileData->signature_url : '';
            if ($sigUrl):
          ?>
            <div style="margin-bottom:14px;padding:12px;background:rgba(8,21,48,.03);border:1px solid rgba(8,21,48,.10);border-radius:3px;display:inline-block;">
              <img src="<?= $h($sigUrl) ?>" alt="signature" style="max-width:300px;max-height:100px;display:block;">
            </div>
          <?php endif; ?>
          <form id="sigForm" enctype="multipart/form-data" onsubmit="return uploadSignature(event)">
            <input type="file" name="signature_file" id="signature_file" accept=".jpg,.jpeg,.png" required>
            <button type="submit" class="pf-btn" style="margin-left:12px;">Upload</button>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php require_once("ajax/footer.php") ?>

<script>
function changePassword(e) {
  e.preventDefault();
  var f = document.getElementById('pwForm');
  if (f.new_password.value !== f.confirm_password.value) {
    swal('', 'New password and confirmation do not match.', 'warning');
    return false;
  }
  var fd = new FormData(f);
  fd.append('saveData', '1');
  $.ajax({
    url: 'ajax/ChangePassword/ChangePassword.php',
    type: 'POST', data: fd, processData: false, contentType: false,
    success: function (data) {
      var s = String(data).trim();
      if (s === '1') { swal('Updated', 'Password changed.', 'success'); f.reset(); }
      else if (s === '2') swal('', 'Current password is wrong.', 'warning');
      else swal('', s || 'Could not change password.', 'error');
    },
    error: function () { swal('', 'Network error.', 'error'); }
  });
  return false;
}

function uploadSignature(e) {
  e.preventDefault();
  var f = document.getElementById('sigForm');
  var fd = new FormData(f);
  // FIX_B_2365b: real endpoint lives at ajax/profiles/signature.php and the
  // success contract is "returns the new filename string"; the prior guess
  // (ajax/Profile/UploadSignature.php returning "1") was never wired up.
  $.ajax({
    url: 'ajax/profiles/signature.php',
    type: 'POST', data: fd, processData: false, contentType: false,
    success: function (data) {
      var s = String(data).trim();
      // Any error path returns a human-readable message; success returns the
      // filename (no spaces, starts with "signature_").
      var errs = ['User session invalid!', 'No file received!', 'Invalid file type!',
                  'File size exceeds 30MB!', 'Upload failed!', 'Database update failed!'];
      if (s && errs.indexOf(s) === -1 && s.indexOf(' ') === -1) {
        swal('Uploaded', 'Signature updated.', 'success');
        setTimeout(function(){ location.reload(); }, 900);
      } else {
        swal('', s || 'Could not upload signature.', 'error');
      }
    },
    error: function () { swal('', 'Network error.', 'error'); }
  });
  return false;
}
</script>
