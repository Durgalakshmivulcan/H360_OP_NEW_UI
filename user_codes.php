<?php
// FIX_B_065 — deduplicated (file shipped pasted 6× with stray <?php).
require_once("ajax/header.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

ensureUserCodeColumn($conn);

$orgFilter    = ($SessionUserId == "1") ? "" : "AND d.org_id='$SessionOrgId'";
$recOrgFilter = ($SessionUserId == "1") ? "" : "AND r.org_id='$SessionOrgId'";
$pharOrgFilter = ($SessionUserId == "1") ? "" : "AND s.org_id='$SessionOrgId'";

// ── Backfill D-codes for active doctors with missing or wrong-prefix codes ──
$bfDocQ = mysqli_query($conn,
    "SELECT d.security_id FROM doctors d
     LEFT JOIN security s ON s.security_id = d.security_id
     WHERE d.status='1' AND s.status='1'
     AND (s.user_code IS NULL OR s.user_code = '' OR s.user_code NOT LIKE 'D%')
     $orgFilter"
);
while ($bf = mysqli_fetch_assoc($bfDocQ)) {
    if (!empty($bf['security_id'])) {
        $code = generateUserCode($conn, 'D');
        mysqli_query($conn, "UPDATE security SET user_code='$code' WHERE security_id='" . (int)$bf['security_id'] . "'");
    }
}

// ── Backfill R-codes for active receptionists with missing or wrong-prefix codes ─
$bfRecQ = mysqli_query($conn,
    "SELECT DISTINCT r.security_id FROM receptionnist r
     LEFT JOIN security s ON s.security_id = r.security_id
     WHERE r.status='1' AND s.status='1'
     AND (s.user_code IS NULL OR s.user_code = '' OR s.user_code NOT LIKE 'R%')
     $recOrgFilter"
);
while ($bf = mysqli_fetch_assoc($bfRecQ)) {
    if (!empty($bf['security_id'])) {
        $code = generateUserCode($conn, 'R');
        mysqli_query($conn, "UPDATE security SET user_code='$code' WHERE security_id='" . (int)$bf['security_id'] . "'");
    }
}

// ── Backfill P-codes for active pharmacists with missing or wrong-prefix codes ─
$bfPhaQ = mysqli_query($conn,
    "SELECT s.security_id FROM security s
     INNER JOIN roles r ON r.role_id = s.role_id AND LOWER(r.role_name) = 'pharmacist'
     WHERE s.status='1'
     AND (s.user_code IS NULL OR s.user_code = '' OR s.user_code NOT LIKE 'P%')
     $pharOrgFilter"
);
while ($bf = mysqli_fetch_assoc($bfPhaQ)) {
    if (!empty($bf['security_id'])) {
        $code = generateUserCode($conn, 'P');
        mysqli_query($conn, "UPDATE security SET user_code='$code' WHERE security_id='" . (int)$bf['security_id'] . "'");
    }
}

// ── Doctors ───────────────────────────────────────────────────────────────
$doctorsQ = mysqli_query($conn,
    "SELECT d.doc_id, d.doctor_name, d.phone_number, d.email,
            s.user_code, s.security_id,
            o.organization_name
     FROM doctors d
     LEFT JOIN security      s ON s.security_id = d.security_id
     LEFT JOIN organization  o ON o.org_id       = d.org_id AND o.status='1'
     WHERE d.status='1' AND s.status='1' $orgFilter
     ORDER BY s.user_code ASC"
) or die(mysqli_error($conn));

$doctors = [];
while ($r = mysqli_fetch_assoc($doctorsQ)) $doctors[] = $r;

// ── Receptionists (distinct security accounts, active only) ───────────────
$recQ = mysqli_query($conn,
    "SELECT DISTINCT
            s.security_id, s.admin_name, s.email, s.contact,
            s.user_code,
            o.organization_name,
            GROUP_CONCAT(DISTINCT d.doctor_name ORDER BY d.doctor_name SEPARATOR ', ') AS assigned_doctors
     FROM receptionnist r
     LEFT JOIN security     s ON s.security_id = r.security_id
     LEFT JOIN doctors      d ON d.doc_id       = r.doc_id AND d.status='1'
     LEFT JOIN organization o ON o.org_id        = r.org_id AND o.status='1'
     WHERE r.status='1' AND s.status='1' $recOrgFilter
     GROUP BY s.security_id
     ORDER BY s.user_code ASC"
) or die(mysqli_error($conn));

$receptionists = [];
while ($r = mysqli_fetch_assoc($recQ)) $receptionists[] = $r;

// ── Pharmacists ───────────────────────────────────────────────────────────
$pharQ = mysqli_query($conn,
    "SELECT s.security_id, s.admin_name, s.email, s.contact, s.user_code,
            o.organization_name
     FROM security s
     INNER JOIN roles r ON r.role_id = s.role_id AND LOWER(r.role_name) = 'pharmacist'
     LEFT JOIN organization o ON o.org_id = s.org_id AND o.status='1'
     WHERE s.status='1' $pharOrgFilter
     ORDER BY s.user_code ASC"
) or die(mysqli_error($conn));

$pharmacists = [];
while ($r = mysqli_fetch_assoc($pharQ)) $pharmacists[] = $r;
?>

<style>
.code-badge-d {
    display:inline-block; background:#4F5ECE; color:#fff;
    font-weight:700; padding:3px 13px; border-radius:20px;
    font-size:13px; letter-spacing:.5px; font-family:monospace;
}
.code-badge-r {
    display:inline-block; background:#198754; color:#fff;
    font-weight:700; padding:3px 13px; border-radius:20px;
    font-size:13px; letter-spacing:.5px; font-family:monospace;
}
.code-badge-p {
    display:inline-block; background:#dc3545; color:#fff;
    font-weight:700; padding:3px 13px; border-radius:20px;
    font-size:13px; letter-spacing:.5px; font-family:monospace;
}
.summary-card {
    border-radius:10px; padding:20px 18px; color:#fff; text-align:center;
}
.summary-card h3 { font-size:2rem; font-weight:700; margin:0; }
.summary-card p  { margin:4px 0 0; font-size:12px; opacity:.9; }
</style>

<div class="main-content">
  <section class="section">

    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">User Codes</h4></li>
      <li class="breadcrumb-item">
        <a href="dashboard.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               class="feather feather-home">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
        </a>
      </li>
      <li class="breadcrumb-item active">User Code Reference</li>
    </ul>

    <!-- Summary cards -->
    <div class="row mb-4">
      <div class="col-6 col-md-3 mb-2">
        <div class="summary-card" style="background:#4F5ECE;">
          <h3><?= count($doctors) ?></h3>
          <p>Doctors (D-codes)</p>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-2">
        <div class="summary-card" style="background:#198754;">
          <h3><?= count($receptionists) ?></h3>
          <p>Receptionists (R-codes)</p>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-2">
        <div class="summary-card" style="background:#dc3545;">
          <h3><?= count($pharmacists) ?></h3>
          <p>Pharmacists (P-codes)</p>
        </div>
      </div>
    </div>

    <!-- Doctors table -->
    <div class="card mb-4">
      <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Doctors — User Code Reference</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="doctorCodeTable" style="width:100%;">
            <thead class="text-center">
              <tr>
                <th>S No</th>
                <th>User Code</th>
                <th>Doctor Name</th>
                <th>Phone</th>
                <th>Email</th>
                <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
              </tr>
            </thead>
            <tbody class="text-center">
              <?php $i = 1; foreach ($doctors as $d): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <?php if (!empty($d['user_code'])): ?>
                  <span class="code-badge-d"><?= htmlspecialchars($d['user_code']) ?></span>
                  <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($d['doctor_name']) ?></td>
                <td><?= htmlspecialchars($d['phone_number']) ?></td>
                <td><?= htmlspecialchars($d['email']) ?></td>
                <?php if ($SessionUserId == "1"): ?>
                <td><?= htmlspecialchars($d['organization_name'] ?? '') ?></td>
                <?php endif; ?>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($doctors)): ?>
              <tr><td colspan="<?= $SessionUserId == '1' ? 6 : 5 ?>" class="text-center text-muted py-3">No doctors found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Receptionists table -->
    <div class="card mb-4">
      <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-headset me-2"></i>Receptionists — User Code Reference</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="recCodeTable" style="width:100%;">
            <thead class="text-center">
              <tr>
                <th>S No</th>
                <th>User Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Assigned Doctor(s)</th>
                <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
              </tr>
            </thead>
            <tbody class="text-center">
              <?php $i = 1; foreach ($receptionists as $r): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <?php if (!empty($r['user_code'])): ?>
                  <span class="code-badge-r"><?= htmlspecialchars($r['user_code']) ?></span>
                  <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['admin_name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars($r['contact']) ?></td>
                <td><?= htmlspecialchars($r['assigned_doctors'] ?? '—') ?></td>
                <?php if ($SessionUserId == "1"): ?>
                <td><?= htmlspecialchars($r['organization_name'] ?? '') ?></td>
                <?php endif; ?>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($receptionists)): ?>
              <tr><td colspan="<?= $SessionUserId == '1' ? 7 : 6 ?>" class="text-center text-muted py-3">No receptionists found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Pharmacists table -->
    <div class="card">
      <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-capsule me-2"></i>Pharmacists — User Code Reference</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="pharCodeTable" style="width:100%;">
            <thead class="text-center">
              <tr>
                <th>S No</th>
                <th>User Code</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
              </tr>
            </thead>
            <tbody class="text-center">
              <?php $i = 1; foreach ($pharmacists as $p): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <?php if (!empty($p['user_code'])): ?>
                  <span class="code-badge-p"><?= htmlspecialchars($p['user_code']) ?></span>
                  <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['admin_name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['contact']) ?></td>
                <?php if ($SessionUserId == "1"): ?>
                <td><?= htmlspecialchars($p['organization_name'] ?? '') ?></td>
                <?php endif; ?>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($pharmacists)): ?>
              <tr><td colspan="<?= $SessionUserId == '1' ? 6 : 5 ?>" class="text-center text-muted py-3">No pharmacists found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </section>
</div>

<?php require_once("ajax/footer.php") ?>
