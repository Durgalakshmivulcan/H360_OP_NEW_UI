<?php 
    require_once("config/functions.php"); 
    session_start();

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $hidePrescription = ($SessionRoleId == 8);
    $wrapClass        = $hidePrescription ? 'hide-prescription' : '';

    $uploadDirHead = '../organisation_images/';
    $logoFileNameHead = '';
    if ($SessionRoleId != "") {
       /* FIX_B_1800: gate sidebar items by role_menus.menu_access='1'. The column
          existed but was ignored — every mapped row was visible regardless of
          access value. We backfilled existing rows to '1' before turning the
          gate on, so existing roles keep their full sidebar; future toggles to
          '0' now actually hide the menu. */
       $menus = mysqli_query($conn, "SELECT
            m.menu_web_url
        FROM
            role_menus rm
        JOIN
            menus m ON rm.menu_id = m.menu_id
        WHERE
            rm.role_id = '$SessionRoleId'
            AND FIND_IN_SET('view', rm.permissions) > 0
        ORDER BY
            m.menu_order");

            $menuUrls = [];
            while ($row = mysqli_fetch_assoc($menus)) {
                $menuUrls[] = trim($row['menu_web_url']);
            }
            $menuUrls = array_filter($menuUrls);
            $currentPage = trim(basename($_SERVER['PHP_SELF']));
            // FIX_B_1200: SA/admin-only pages must reject other roles (e.g.
            // role_id=3 receptionist, role_id=4 pharmacist) instead of
            // rendering the page. The full menu-mapping block above is too
            // sweeping (would block dashboard, profile, etc.). Use a tight
            // deny-list keyed off the canonical role_menus assignment.
            $restrictedPages = [
                // FIX_B_1920: Clinic Manager (role_id=6, Dinesh) is an Admin-class
                // role who manages the clinic (staff, slots, billing, reports)
                // but is NOT a doctor and CANNOT create new doctors (the
                // 'add' permission is revoked at the role_menus.permissions
                // SET level). Add him to the whitelist for the admin-class
                // pages he needs.
                'registration.php'         => [1, 2, 6],  // SA + Admin + Clinic Manager
                'menus.php'                => [1],         // SA only
                'roles.php'                => [1, 2, 6],  // SA + Admin + Clinic Manager
                // FIX_B_1400: Accountant (role_id=5) is finance-only and
                // must NOT reach clinical / appointment / prescription
                // surfaces. Admin (2), Receptionist (11) keep front-desk
                // surfaces; Admin (2) acts as Doctor for prescriptions.
                'AppointmentOnline.php'    => [1, 2, 3, 6],
                'prescription.php'         => [1, 2],      // doctors only (gated further by specialization)
                'gynaec_prescription.php'  => [1, 2],      // doctors only (gated further to gynec specialty)
                'doctor.php'               => [1, 2, 6],  // Clinic Manager can VIEW (add gated by role_menus)
            ];
            if (isset($restrictedPages[$currentPage])
                && !in_array((int)$SessionRoleId, $restrictedPages[$currentPage], true)) {
                header("Location: dashboard.php");
                exit;
            }

            // FIX_B_2250: Specialization gate must fire BEFORE the <!DOCTYPE>
            // block below. The legacy call-site in prescription.php /
            // gynaec_prescription.php fired AFTER ajax/header.php had already
            // emitted HTML, so `header(Location)` was a no-op and the page
            // rendered fully — only a JS redirect was emitted at the very
            // bottom, leaving the page DOM scrapable by bots / JS-disabled
            // clients and allowing any auto-fetching AJAX on the page to
            // leak data before the JS redirect ran. Move the check here so
            // we can issue a true 302 HTTP redirect before any output. SA
            // (role_id=1) and non-doctor users are bypassed inside
            // userMaySeeBySpecialization() itself.
            if (function_exists('userMaySeeBySpecialization')
                && !userMaySeeBySpecialization($currentPage)) {
                header("Location: dashboard.php");
                exit;
            }
    }

    if ($SessionOrgId){
        $result = mysqli_query($conn,"SELECT logo, logo_without_text FROM organization WHERE org_id = '$SessionOrgId' AND status = '1'");

        $orgHead = mysqli_fetch_assoc($result);
        $logoFile = $orgHead['logo']; 
        $logoFileWithText = $orgHead['logo_without_text']; 
    }

    if (!empty($logoFile) && $SessionUserId != '1' ) {
        $logo_path = 'organisation_images/'. $logoFile;
        $logo_without_text_path = 'organisation_images/'. $logoFileWithText;
    } else {
        $logo_path = 'assets/img/h360.png';
    }
    
    if($SessionUserId != "") {


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <title>Health360</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/css/app.min.css">
    <link rel="stylesheet" href="assets/bundles/prism/prism.css">
    <link rel="stylesheet" href="assets/bundles/summernote/summernote-bs4.css">
    <link href="assets/bundles/lightgallery/dist/css/lightgallery.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/health.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/bundles/jqvmap/dist/jqvmap.min.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <?php // FIX_B_982: when $ProfileData->image_url is empty, the original href was
          // 'profile_images/' — Apache returned 403 (directory listing forbidden) on
          // every page-load. Fall back to assets/img/health.png. ?>
    <link rel='shortcut icon' type='image/x-icon' href='<?php echo (!empty($ProfileData->image_url) ? "profile_images/".htmlspecialchars($ProfileData->image_url, ENT_QUOTES) : "assets/img/health.png"); ?>' />

    <link rel="stylesheet" href="assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="assets/bundles/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="assets/bundles/jquery-selectric/selectric.css">
    <link rel="stylesheet" href="assets/bundles/bootstrap-timepicker/css/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="assets/bundles/bootstrap-tagsinput/dist/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">
    <link rel="stylesheet" href="assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/bundles/izitoast/css/iziToast.min.css">
    <link rel="stylesheet" href="assets/bundles/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
    <link rel='shortcut icon' type='image/x-icon' href="assets/img/health.png" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- H360 UI · Sovereign Institutional · Terminal v2 (see /design.md §3.2) -->
    <link rel="stylesheet" href="assets/h360-ui/h360-ui.css?v=59">
    <script src="assets/h360-ui/h360-ui.js?v=10"></script>
</head>
<style>
    html {
        scroll-behavior: smooth;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
    }
    .main-sidebar .sidebar-menu li a {
        font-weight: 700 !important;
    }

    .form-group>label {
        font-weight: 800 !important;
    }
    table thead th, table th{
        font-weight: 900 !important;
    }
    table tbody td, table td{
        font-weight: 600 !important;
    }
    
     .quick-wrap {
      position: fixed;
      top: 100px;
      right: 0;
      z-index: 1001;
    }

    .toggle-btn {
      width: 30px; height: 30px;
      background: #4F5ECE;
      border-radius: 4px 0 0 4px;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 20px;
      transition: opacity 0.3s;
    }

    .quick-access {
      position: absolute;
      top: 0; right: -220px;
      width: 220px;
      background: #fff;
      box-shadow: -2px 0 5px rgba(0,0,0,0.2);
      transition: right 0.3s ease;
      border-radius: 6px 0 0 6px;
      padding: 12px;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-auto-rows: min-content;
      grid-gap: 8px;
      box-sizing: border-box;
    }

    .quick-wrap.active .quick-access {
      right: 0;
    }

    .quick-wrap.active .toggle-btn {
      opacity: 0;
      pointer-events: none;
    }

    .menu {
      list-style: none; margin: 0; padding: 0;
      display: contents;
    }
    .menu li { text-align: center; }
    .menu a {
      display: flex; flex-direction: column;
      align-items: center; text-decoration: none;
      color: #333; font-size: 13px;
    }
    .menu a i {
      font-size: 24px; margin-bottom: 4px;
    }
    .quick-wrap.hide-prescription .menu li:last-child {
      grid-column: span 2;
      display: flex;
      justify-content: center;
    }
    td a.has-icon {
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  padding: 4px 8px;
  margin-right: 4px;
  border-radius: 4px;
  color: #fff !important;
  text-decoration: none;
  white-space: nowrap;
  gap: 4px;
  vertical-align: middle;
}

/* Icon fix */
td a.has-icon i {
  font-size: 14px;
  color: #fff;
}

/* View */
td a.has-icon:has(.fa-eye) {
  background-color: #4879c1 !important;
}
td a.has-icon:has(.fa-eye)::after {
  content: " View";
  color: #fff;
}

/* Edit */
td a.has-icon:has(.fa-edit) {
  background-color: #4879c1 !important;
}
td a.has-icon:has(.fa-edit)::after {
  content: " Edit";
  color: #fff;
}

/* Delete */
td a.has-icon:has(.fa-trash) {
  background-color: #d63f4d !important;
}
td a.has-icon:has(.fa-trash)::after {
  content: " Delete";
  color: #fff;
}

/* FIX: Parent <td> should not stack links */
td {
  white-space: nowrap; /* prevent wrapping */
}

  
</style>




<body>

    <div class="quick-wrap <?= $wrapClass ?>" id="quickWrap">
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']); 
    if ($SessionOrgId !== '0' && $currentPage !== 'visitors_doctor_display.php'): 
    ?>
    <div class="toggle-btn" id="toggleQuickAccess"><i class="fas fa-bars"></i></div>
    <?php endif; ?>

    <?php if ($SessionOrgId !== '0'): ?>
        <div class="quick-access">
        <ul class="menu">
            <li>
            <a href="dashboard.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
            </li>
            <li>
            <a href="doctorstimeslot.php"><i class="bi bi-clock-history"></i><span>Time Slots</span></a>
            </li>
            <li>
            <a href="AppointmentOnline.php"><i class="bi bi-calendar2-check"></i><span>Appointments</span></a>
            </li>
            <li>
            <a href="patienthistory.php"><i class="bi bi-cloud-upload"></i><span>Upload Reports</span></a>
            </li>

            <?php if (! $hidePrescription): // hide for role_id 8 ?>
            <li>
            <a href="prescription.php"><i class="bi bi-capsule-pill"></i><span>Prescription</span></a>
            </li>
            <li>
            <a href="gynaec_prescription.php"><i class="bi bi-gender-female"></i><span>Gynaec Rx</span></a>
            </li>
            <?php endif; ?>

            <li>
            <a href="TestReport.php"><i class="bi bi-file-earmark-text"></i><span>View Reports</span></a>
            </li>
            <li>
            <a href="receptionist.php"><i class="bi bi-person-badge"></i><span>Receptionist</span></a>
            </li>
            <li>
            <a href="visitors_doctor_display.php"><i class="bi bi-display"></i><span>Visitors Display</span></a>
            </li>
            <li>
            <a href="AllPatients.php"><i class="bi bi-people"></i><span>Patients</span></a>
            </li>
        </ul>
        </div>
        <?php endif; ?>
  </div>


    <div id="app">
            <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg navbar-expand-md main-navbar sticky">
                <div class="form-inline me-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-bs-toggle="sidebar" class="nav-link nav-link-lg collapse-btn" onclick="iconimage()"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a></li>
                        <li>
                        <form class="form-inline me-auto">
                            <div class="search-element d-flex">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            </div>
                        </form>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <?php /* FIX_B_2002 + FIX_B_2320: clinic-wide doctor-switcher.
                         Lets multi-room operators narrow their views to one
                         doctor or see merged data via "All Doctors". Persists
                         in $_SESSION['admin_doctor_filter']; consumed by
                         currentDoctorScopeSql() across every patient-bearing
                         query.
                           Admin (6)        — always visible (clinic-wide role).
                           Receptionist (3) — visible only when an admin has
                             opted her in via security.can_switch_doctor=1,
                             cached in $_SESSION['_can_switch_doctor'].
                         Lazy-cache the flag from the security row on first
                         request after login. */ ?>
                    <?php
                      // FIX_B_23420: stop caching can_switch_doctor in $_SESSION.
                      // Cached value went stale when an admin flipped the flag mid-
                      // session (the receptionist kept her switcher until logout)
                      // AND it forced the session to be primed by a header.php
                      // page-load before any direct AJAX could succeed. Reading
                      // live is one indexed PK lookup — negligible.
                      $_roleNow = (int)($_SESSION['role_id'] ?? 0);
                      $_showSwitcher = ($_roleNow === 6) || ($_roleNow === 3 && canSwitchDoctorLive($conn) === 1);
                    ?>
                    <?php if ($_showSwitcher) {
                        $_currentFilter = $_SESSION['admin_doctor_filter'] ?? 'all';
                        $_doctorRows = mysqli_query($conn, "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name");
                    ?>
                    <li class="d-flex align-items-center" style="margin-right:12px;">
                      <label for="adminDocFilter" class="me-2 small text-uppercase" style="letter-spacing:.08em;color:var(--ink-mute);font-weight:600;">Viewing</label>
                      <select id="adminDocFilter" class="form-select form-select-sm" style="min-width:170px;border:1px solid var(--gold);background:var(--cream);color:var(--ink);font-weight:600;cursor:pointer;">
                        <option value="all" <?= ($_currentFilter==='all'?'selected':'') ?>>All Doctors</option>
                        <?php while ($_d = mysqli_fetch_assoc($_doctorRows)) { ?>
                          <option value="<?= (int)$_d['doc_id'] ?>" <?= ((string)$_currentFilter===(string)$_d['doc_id']?'selected':'') ?>><?= htmlspecialchars($_d['doctor_name']) ?></option>
                        <?php } ?>
                      </select>
                      <script>
                        (function(){
                          var el = document.getElementById('adminDocFilter');
                          if(!el) return;
                          el.addEventListener('change', function(){
                            fetch('ajax/admin/setdoctorfilter.php', {
                              method:'POST',
                              headers:{'Content-Type':'application/x-www-form-urlencoded'},
                              body: 'doc_id=' + encodeURIComponent(el.value)
                            }).then(r => r.json()).then(_ => location.reload());
                          });
                        })();
                      </script>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="#" class="nav-link nav-link-lg fullscreen-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                            </svg>
                        </a>
                    </li>
                    <li class="dropdown">
                        <?php
                        $SessionUserId = $_SESSION['security_id'] ?? '';
                        $SessionRoleId = $_SESSION['role_id'] ?? '';
                        $SessionOrgId = $_SESSION['org_id'] ?? '';
                        
                        $qry = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
                        
                        $ProfileData=mysqli_fetch_object($qry);
                        ?>
                        <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <?php
                            // FIX_B_2372: header avatar — same resolution as the profile hero
                            // (B-2365). For doctors, prefer doctor_images/<doc_img>; for everyone
                            // else, security.image_url under img/; final fallback assets/img/user.png.
                            // Earlier the header read raw security.image_url which still held a
                            // stale " - 2025.09.19...png" string for Dr. Ashwin and showed a broken image.
                            $ProfileImage = $ProfileData->image_url;
                            $headerAvatar = '';
                            $headerTitle  = $ProfileData->admin_name ?? 'User';
                            $docRow = mysqli_fetch_assoc(mysqli_query($conn,
                                "SELECT doc_img FROM doctors WHERE security_id='$SessionUserId' AND status='1' LIMIT 1"));
                            if ($docRow && !empty($docRow['doc_img'])
                                && file_exists(__DIR__ . '/../doctor_images/' . $docRow['doc_img'])) {
                                $headerAvatar = 'doctor_images/' . rawurlencode($docRow['doc_img']);
                            } elseif (!empty($ProfileImage) && file_exists(__DIR__ . '/../img/' . $ProfileImage)) {
                                $headerAvatar = 'img/' . rawurlencode($ProfileImage);
                            } else {
                                $headerAvatar = 'assets/img/user.png';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($headerAvatar, ENT_QUOTES) ?>" class="user-img-radius-style" style="border-radius: 50px;height: 30px;" title="<?= htmlspecialchars($headerTitle, ENT_QUOTES) ?>">
                        </a>
                    
                        <div class="dropdown-menu dropdown-menu-right pullDown">
                            <div class="dropdown-title">Hello Admin</div>
                                <a href="profile.php" class="dropdown-item has-icon">
                                    <i class="far fa-user"></i> Profile
                                <!-- </a> sidebar-brand -->
                                <!-- <a href="#" class="dropdown-item has-icon" data-bs-toggle="tooltip" title="Mail">
                                    <i class="far fa-envelope"></i>Mail
                                </a>
                                <a href="#" class="dropdown-item has-icon" data-bs-toggle="tooltip" title="Chat">
                                    <i class="far fa-comment"></i>Chat
                                </a> -->
                            <div class="dropdown-divider"></div>
                            <a href="ajax/logout.php" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div class="main-sidebar sidebar-style-2" tabindex="4" style="overflow: hidden; outline: none;">    
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="dashboard.php">
                            <input type="hidden" name="image1" id="image1" value="1">

                         <img id="logo1" src="<?=$logo_without_text_path?>" class="header-logo w-50 h-auto mt-4" style="visibility:hidden; position:absolute;">
                         <img id="logo2" src="<?=$logo_path?>" class="header-logo w-50 h-auto">

                            <!-- <span class="logo-name"></span> -->
                        </a>
                    </div>

                <div class="sidebar-user">
                    <div class="sidebar-user-picture mt-1">         
                    <!-- <img alt="image" src="assets/img/user.png"> -->
                    <?php /* FIX_B_2372: reuse the resolved $headerAvatar from above. */ ?>
                    <img src="<?= htmlspecialchars($headerAvatar, ENT_QUOTES) ?>" class="user-img-radius-style" title="<?= htmlspecialchars($headerTitle, ENT_QUOTES) ?>">
                    </div>
                    <?php
                        $qry1 = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
                        $res1=mysqli_fetch_object($qry1);
                    ?>
                    <div class="sidebar-user-details ">
                        <div class="user-name">
                            <?= $res1->admin_name?>
                        </div>
                        <?php
                            $qry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$SessionOrgId'") or die(mysqli_error($conn));
                            $res = mysqli_fetch_object($qry);
                        ?>
                        <div class="user-role">
                            <?= $res->organization_name?>
                        </div>
                        <div class="sidebar-userpic-btn mt-4">
                            <a href="profile.php" data-bs-toggle="tooltip" aria-label="Profile" data-bs-original-title="Profile">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </a>
                            <!-- <a href="email-inbox.html" data-bs-toggle="tooltip" aria-label="Mail" data-bs-original-title="Mail">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </a>
                            <a href="chat.html" data-bs-toggle="tooltip" aria-label="Chat" data-bs-original-title="Chat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            </a> -->
                            <a href="ajax/logout.php" data-bs-toggle="tooltip" aria-label="Log Out" data-bs-original-title="Log Out">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <ul class="sidebar-menu">
                    <li class="menu-header">Main</li>
                    <?php
                    /* FIX_B_1800: gate sidebar items by FIND_IN_SET('view', rm.permissions). */
                    /* FIX_B_1901 (specialization filter): when the logged-in user is also
                       a doctor (security_id linked to a doctors row), additionally hide
                       any menu whose `restricted_to_specializations` is non-empty AND
                       does NOT contain the doctor's specialization. So Dr. Rama
                       (Gynaecology) sees Gynaec Rx but not Normal Rx; Dr. Ashwin
                       (Cardiology) sees Normal Rx but not Gynaec Rx. SA + non-doctor
                       Admins see everything (the doctor JOIN returns NULL spec → the
                       FIND_IN_SET check is bypassed). */
                    /* Composed of two parts:
                       (a) RESTRICTED list: include menu only when (no list) OR (doctor's
                           spec is in the list) OR (user is not a doctor / SA).
                       (b) EXCLUDED list: hide menu when (list is non-empty) AND
                           (doctor's spec IS in the list). Lets us mark Normal Rx
                           as excluded for gynaec doctors so Rama doesn't see it. */
                    $specFilter = " AND ("
                       . "  m.restricted_to_specializations IS NULL"
                       . "  OR m.restricted_to_specializations=''"
                       . "  OR FIND_IN_SET((SELECT doctor_specialization FROM doctors WHERE security_id='$SessionUserId' LIMIT 1), m.restricted_to_specializations) > 0"
                       . "  OR (SELECT COUNT(*) FROM doctors WHERE security_id='$SessionUserId') = 0"
                       . ") AND ("
                       . "  m.excluded_specializations IS NULL"
                       . "  OR m.excluded_specializations=''"
                       . "  OR FIND_IN_SET((SELECT doctor_specialization FROM doctors WHERE security_id='$SessionUserId' LIMIT 1), m.excluded_specializations) = 0"
                       . ") ";
                    /* FIX_B_2113: removed the legacy SA-special branch
                       (`$SessionOrgId === '0'` → SELECT … s.security_type='SA'
                       AND m.menu_order='2'`) — that hard-coded `menu_order='2'`
                       collapsed SA's sidebar to a single menu. Sidebar is now
                       driven purely by role_menus.permissions for every role,
                       including SA. No org_id branching. */
                    $qryGetMenus = mysqli_query($conn, "
                        SELECT m.menu_id, m.menu_name, m.menu_order, m.web_icon, m.menu_web_url, m.web_class_name
                          FROM menus m, role_menus rm
                         WHERE m.status='1'
                           AND m.menu_type='p'
                           AND m.menu_id = rm.menu_id
                           AND FIND_IN_SET('view', rm.permissions) > 0
                           AND rm.role_id = '$SessionRoleId'
                           $specFilter
                         ORDER BY m.menu_order
                    ") or die(mysqli_error($conn));
                        while($resGetMenus = mysqli_fetch_object($qryGetMenus)){
                            $MainMenuName = $resGetMenus->menu_name;
                            
                            $icon = $resGetMenus->web_icon;
                            $mainURL = $resGetMenus->menu_web_url;
                            if($icon == "") {
                                $icon = "briefcase";
                            }
                            if($mainURL == "") {
                                $mainURL = "#";
                            }
                    ?>
                    <li class="dropdown">
                        <a href="<?=$mainURL?>" class="<?=$resGetMenus->web_class_name?>" class="menu-toggle nav-link has-dropdown toggled">
                            <i data-feather="<?=$icon?>"></i><span><?=$MainMenuName?></span>
                        </a>
                        <ul class="dropdown-menu" style="display: none;">
                        <?php
                            /* FIX_B_1800: gate sub-menus by FIND_IN_SET('view',
                               rm.permissions). FIX_B_1901: also apply the
                               specialization filter ($specFilter built above) so
                               restricted sub-menus stay hidden for the wrong specialty. */
                            $qryGetSubMenus = mysqli_query($conn,"SELECT m.menu_name, m.menu_web_url FROM menus m, role_menus rm WHERE m.status='1' AND m.menu_type='s' AND rm.menu_id=m.menu_id AND FIND_IN_SET('view', rm.permissions) > 0 AND m.parent_id='$resGetMenus->menu_id' AND rm.role_id='$SessionRoleId' $specFilter ORDER BY menu_order") or die(mysqli_error($conn));
                            while($resGetSubMenus = mysqli_fetch_object($qryGetSubMenus)){
                                $SubMenuName = $resGetSubMenus->menu_name;
                        ?>
                            <li>
                                <a class="nav-link toggled" href="<?=$resGetSubMenus->menu_web_url?>"><?=$SubMenuName?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                 
                </ul>
                </aside>
            </div>
        </div>
    </div>

<?php

} else {
    // FIX_B_994: previously this redirect did NOT exit, so PHP kept executing
    // the host page (patienthistory.php, etc.) after the Location header was
    // sent. Pages that compute their SQL based on a valid session crashed with
    // "mysqli_query(): Argument #2 ($query) cannot be empty". exit; halts the
    // script immediately so unauthenticated requests just see the redirect.
    header("Location:index.php");
    exit;
}
?>
<script>

    
function iconimage() {
    const logo1 = document.getElementById('logo1');
    const logo2 = document.getElementById('logo2');

    if (logo1.style.visibility === "hidden") {
        logo1.style.visibility = "visible";
        logo2.style.visibility = "hidden";
    } else {
        logo1.style.visibility = "hidden";
        logo2.style.visibility = "visible";
    }
}

    const toggleBtn = document.getElementById('toggleQuickAccess');
    const quickWrap = document.getElementById('quickWrap');
    if (toggleBtn && quickWrap) {
        toggleBtn.addEventListener('click', e => { e.stopPropagation(); quickWrap.classList.toggle('active'); });
        quickWrap.addEventListener('click', e => e.stopPropagation());
        document.addEventListener('click', () => quickWrap.classList.remove('active'));
    }
    

</script>


