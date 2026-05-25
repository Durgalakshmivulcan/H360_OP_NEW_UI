<?php
require_once("ajax/header.php");

/* FIX_B_2000: per-role dashboard router. Each role gets its own
   enterprise-grade interactive partial under dashboard_partials/.
   Receptionists are auto-routed to receptionist.php (their dedicated queue
   screen) since they don't need a dashboard layer.
   FIX_B_2003: Super Admin can view ANY role's dashboard via ?as=<role>
   (sa|doctor|admin|pharmacist|accountant). Param honored only for SA.
   Falls through to the legacy admin-style dashboard for any unmatched role. */
$_h360RouterRole = (int) ($_SESSION['role_id'] ?? 0);
$_h360IsSA = ((int)($_SESSION['security_id'] ?? 0) === 1) || $_h360RouterRole === 1;
$_h360ViewAs = $_h360IsSA ? strtolower((string)($_GET['as'] ?? '')) : '';
$_h360PartialMap = [
    'sa'         => 'dashboard_partials/sa.php',
    'doctor'     => 'dashboard_partials/doctor.php',
    'admin'      => 'dashboard_partials/admin.php',
    'pharmacist' => 'dashboard_partials/pharmacist.php',
    'accountant' => 'dashboard_partials/accountant.php',
];
/* FIX_B_2110: every partial render is wrapped in `.main-content` (so it
   clears the sidebar) AND followed by `ajax/footer.php` (which loads
   sidebar nav JS, ApexCharts, etc.). Previously the partials rendered
   bare — sidebar clicks were dead and content slid under the sidebar. */
function _h360RenderPartial(string $partial): void {
    /* FIX_B_2111: PHP function scope hides $conn / $Session* / $datetime
       from the include — without these globals re-imported, the partial's
       SQL calls run with $conn=NULL and fatal at the first mysqli_query.
       Re-globalize before the include so partials see the same context as
       the legacy dashboard.php top-level scope. */
    global $conn, $SessionUserId, $SessionRoleId, $SessionOrgId, $datetime,
           $logo_path, $logo_without_text_path, $ProfileData, $menuUrls,
           $hidePrescription, $wrapClass;
    $alreadyWraps = in_array(basename($partial), ['accountant.php', 'pharmacist.php'], true);
    if (!$alreadyWraps) echo '<div class="main-content">';
    include __DIR__ . '/' . $partial;
    if (!$alreadyWraps) echo '</div>';
    require_once __DIR__ . '/ajax/footer.php';
    exit;
}
if ($_h360IsSA && isset($_h360PartialMap[$_h360ViewAs])) {
    _h360RenderPartial($_h360PartialMap[$_h360ViewAs]);
}
switch ($_h360RouterRole) {
    case 3: header('Location: receptionist.php'); exit;          // Receptionist
    case 1: _h360RenderPartial('dashboard_partials/sa.php');
    case 2: _h360RenderPartial('dashboard_partials/doctor.php');
    case 4: _h360RenderPartial('dashboard_partials/pharmacist.php');
    case 5: _h360RenderPartial('dashboard_partials/accountant.php');
    case 6: _h360RenderPartial('dashboard_partials/admin.php');
}
/* fall-through: legacy admin dashboard (preserved for any role not handled above) */

function calculatePercentage($diff, $prev, $decimalPlaces = 2)
{
  if ($prev == 0) {
    return 0;
  }
  $percentage = ($diff / $prev) * 100;
  $roundedPercentage = round($percentage, $decimalPlaces);
  return $roundedPercentage;
}

$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId = $_SESSION['org_id'];

$currentDate = date('Y-m');
$previousDate = date('Y-m', strtotime(date('Y-m') . " -1 month"));

$currentMonthArray = explode('-', $currentDate);
$PreviousMonthArray = explode('-', $previousDate);

// FIX_B_1903: doctor-scope filter on KPI counts (empty for SA / non-doctors)
$docScope_B1903 = currentDoctorScopeSql('doctor_name');
$currentMonthCount = mysqli_fetch_array(mysqli_query(
  $conn,
  "SELECT COUNT(*) FROM appointment_online
     WHERE MONTH(appoint_date)=MONTH(CURDATE()) AND YEAR(appoint_date)=YEAR(CURDATE()) AND appoint_status='1' $docScope_B1903"
))[0];

$previousMonthCount = mysqli_fetch_array(mysqli_query(
  $conn,
  "SELECT COUNT(*) FROM appointment_online
     WHERE MONTH(appoint_date)=MONTH(CURDATE() - INTERVAL 1 MONTH)
       AND YEAR(appoint_date)=YEAR(CURDATE() - INTERVAL 1 MONTH) AND appoint_status='1' $docScope_B1903"
))[0];

$overallGrowth = ($previousMonthCount > 0) ? round((($currentMonthCount - $previousMonthCount) / $previousMonthCount) * 100, 2) : 0;

$checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
$securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

// SA_FATAL_FIXED_B_546: SA sees ALL orgs (no org filter); A sees own org only
if ($securityType === 'SA') {
  // Super-Admin: see all doctors across all orgs
  $query = "SELECT
                s.admin_name,
                s.security_id,
                d.doc_id,
                d.doctor_name,
                d.doctor_specialization,
                d.doc_img,
                ds.specialtisname
              FROM doctors AS d
              INNER JOIN security AS s
                  ON s.security_id = d.security_id
              LEFT JOIN specialtis AS ds
                  ON ds.specialtis_id = d.doctor_specialization
              WHERE d.status='1'";
} elseif ($securityType === 'A') {
  // Admin: see all doctors in org
  $query = "SELECT
                s.admin_name,
                s.security_id,
                d.doc_id,
                d.doctor_name,
                d.doctor_specialization,
                d.doc_img,
                ds.specialtisname
              FROM doctors AS d
              INNER JOIN security AS s
                  ON s.security_id = d.security_id
              LEFT JOIN specialtis AS ds
                  ON ds.specialtis_id = d.doctor_specialization
              WHERE d.org_id = '$SessionOrgId' AND d.status='1'";
} elseif ($securityType === 'U') {
  // Doctor OR Receptionist
  $query = "SELECT DISTINCT 
                s.admin_name,
                s.security_id,
                d.doc_id,
                d.doctor_name,
                d.doctor_specialization,
                d.doc_img,
                ds.specialtisname
              FROM doctors AS d
              INNER JOIN security AS s 
                  ON s.security_id = d.security_id
              LEFT JOIN specialtis AS ds
                  ON ds.specialtis_id = d.doctor_specialization
              LEFT JOIN receptionnist r 
                  ON d.doc_id = r.doc_id
              WHERE d.org_id = '$SessionOrgId'
                AND (
                    -- Case 1: Doctor logged in
                    d.security_id = '$SessionUserId'
                    -- Case 2: Receptionist logged in (assigned doctors only)
                    OR r.security_id = '$SessionUserId'
                ) AND d.status='1'";
} else {
  // Fallback (no results)
  $query = "SELECT NULL AS admin_name, NULL AS security_id, NULL AS doc_id, 
                     NULL AS doctor_name, NULL AS doctor_specialization, 
                     NULL AS doc_img, NULL AS specialtisname 
              LIMIT 0";
}

// Execute query
$res = mysqli_query($conn, $query);

$admins = [];
while ($row = mysqli_fetch_assoc($res)) {
  $admins[] = [
    'name'          => $row['admin_name'] ?? '',
    'id'            => $row['security_id'] ?? '',
    'specialtisname' => $row['specialtisname'] ?? '',
    'doctor_name'   => $row['doctor_name'] ?? '',
    'doctor_img'    => $row['doc_img'] ?? '',
  ];
}
$total = count($admins);
// Doctors default to their own security_id; receptionists/admins with
// multiple assigned doctors default to '0' meaning "all" so the initial
// load shows combined data across all assigned doctors.
$isSessionUserDoctor = false;
foreach ($admins as $a) {
    if ((string)$a['id'] === (string)$SessionUserId) { $isSessionUserDoctor = true; break; }
}
if ($isSessionUserDoctor) {
    $defaultDashboardSecurityId = $SessionUserId;
} elseif (count($admins) > 1) {
    $defaultDashboardSecurityId = '0';
} else {
    $defaultDashboardSecurityId = !empty($admins[0]['id']) ? $admins[0]['id'] : $SessionUserId;
}
?>

<style>
  .shadow {
    box-shadow: none;
  }

  input {
    position: relative;
  }

  input[type="date"]::-webkit-calendar-picker-indicator {
    background-position: right;
    background-size: auto;
    cursor: pointer;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 10px;
    top: 7px;
    width: auto;
  }

  .table-responsive.collapse1 {
    width: 100%;
    height: 400px;
  }

  .table-scroll {
    overflow-y: hidden;
    padding-right: 10px;
  }

  .custom-file,
  .custom-file-label,
  .custom-select,
  .custom-file-label:after,
  .form-control[type=color],
  select.form-control:not([size]):not([multiple]) {
    height: calc(28px + 3px);
  }

  .input-group-text,
  select.form-control:not([size]):not([multiple]),
  .form-control:not(.form-control-sm):not(.form-control-lg) {
    font-size: 14px;
    padding: 0px 14px;
  }

  .collapse1 {
    max-height: 400px;
    overflow-y: hidden;
    transition: all 0.3s ease;
    scrollbar-gutter: stable;
  }

  .collapse1:hover {
    overflow-y: auto;
  }

  .collapse2 {
    max-height: 165px;
    overflow-x: hidden;
    transition: all 0.3s ease;
    scrollbar-gutter: stable;
  }

  .collapse2:hover {
    overflow-x: auto;
  }

  .doctor-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* margin-bottom: 20px; */
  }

  .doctor-info {
    flex: 1;
  }

  .doctor-name {
    font-weight: 600;
    font-size: 15px;
    color: #333;
  }

  .doctor-slots {
    font-size: 13px;
    color: #777;
    margin-left: 8px;
  }

  /* .progress-bars {
    /* margin-top: 5px; */
  /* } */

  .progress-line {
    height: 4px;
    border-radius: 4px;
    margin: 6px 0;
  }

  .progress-blue {
    background: #0d6efd;
  }

  .progress-red {
    background: #dc3545;
  }

  /* .btn-filter {
    background-color: #ff4d4d;
    color: #fff;
    border-radius: 50px;
    padding: 5px 18px;
    font-weight: 500;
    border: none;
    box-shadow: 0 4px 8px rgba(255, 77, 77, 0.4);
  } */

  .doctor-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
    vertical-align: middle;
  }

  .doctor-avatar-fallback {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-size: 14px;
    vertical-align: middle;
  }

  .doctor-name {
    display: flex;
    align-items: center;
    font-weight: 600;
  }

  .doctor-list-scroll::-webkit-scrollbar {
    width: 6px;
  }

  .doctor-list-scroll::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
  }

  .doctor-list-scroll::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .doctor-list-scroll::-webkit-scrollbar-track {
    background: transparent;
  }

  .doctor-list-scroll {
    scrollbar-width: thin;
    scrollbar-color: #888 transparent;
  }

  .avatar-group {

    /* font-weight: 600; */
    display: flex;
    align-items: center;
    justify-content: end;
    cursor: pointer;
    /* margin-left: -10px;
    transition: transform 0.25s ease, box-shadow 0.25s ease; */
  }

  .avatar-group-avatar {
    display: flex;
    align-items: center;
    justify-content: end;
    cursor: pointer;
  }

  .avatar-group-avatar :hover {
    background: #007bff;
    transform: scale(1.2);
    z-index: 10;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #fff;
    object-fit: cover;
    margin-left: -10px;
    cursor: pointer;
    background: #6c757d;
    color: #fff;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    border: 1px solid black;
  }

  .doctor-display {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
  }

  .hidden-users {
    position: absolute;
    top: 88px;
    /* adjust vertical position */
    right: 0;
    background: #fff;
    padding: 8px 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
    border-radius: 6px;
    white-space: nowrap;
    display: none;
    min-width: 120px;
    z-index: 20;
  }

  .avatar-group .hidden-users {
    display: block;
  }

  /* Fixed height cards with scroll */
.dashboard-cards-row {
  display: flex;
  flex-wrap: wrap;
}

/* Fixed height for all cards in this section */
.fixed-height-card {
  height: 400px; /* Fixed height for all cards */
  display: flex;
  flex-direction: column;
}

.fixed-height-card .card-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 0; /* Important for flex children */
  padding: 0; /* Remove padding to maximize scroll area */
}

/* Scrollable containers - HOVER behavior like existing cards */
.scrollable-container {
  flex: 1;
  overflow: hidden; /* Hide scrollbar by default */
  position: relative;
  transition: all 0.3s ease;
  scrollbar-gutter: stable;
}

.scrollable-container:hover {
  overflow-y: auto; /* Show scrollbar on hover */
}

.scrollable-container .table-responsive,
.scrollable-container #doctorSlotChart {
  max-height: 100%;
  overflow-y: hidden; /* Hide by default */
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.scrollable-container:hover .table-responsive,
.scrollable-container:hover #doctorSlotChart {
  overflow-y: auto; /* Show on hover */
}

/* Specific styling for tables inside scrollable containers */
.scrollable-container .table {
  margin-bottom: 0;
}

.scrollable-container .table thead {
  position: sticky;
  top: 0;
  background: white;
  z-index: 10;
}

/* Doctor slots specific styling */
.scrollable-container #doctorSlotChart {
  padding: 1rem;
}

/* Custom scrollbar styling - Same as your existing cards */
.scrollable-container::-webkit-scrollbar,
.collapse1::-webkit-scrollbar,
.collapse2::-webkit-scrollbar {
  scrollbar-width: thin;
}

.scrollable-container::-webkit-scrollbar-track,
.collapse1::-webkit-scrollbar-track,
.collapse2::-webkit-scrollbar-track {
  background: black;
  /* border-radius: 3px; */
}

.scrollable-container::-webkit-scrollbar-thumb,
.collapse1::-webkit-scrollbar-thumb,
.collapse2::-webkit-scrollbar-thumb {
  background: #888;
  /* border-radius: 4px; */
}

.scrollable-container::-webkit-scrollbar-thumb:hover,
.collapse1::-webkit-scrollbar-thumb:hover,
.collapse2::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Firefox scrollbar */
.scrollable-container,
.collapse1,
.collapse2 {
  scrollbar-width: thin;
  scrollbar-color: #888 transparent;
}

.scrollable-container:hover,
.collapse1:hover,
.collapse2:hover {
  scrollbar-color: #888 #f1f1f1;
}

.dashboard-list-card .card-body {
  padding: 0;
}

.dashboard-list-card .table {
  margin-bottom: 0;
}

.dashboard-list-card .table thead {
  position: sticky;
  top: 0;
  background: #fff;
  z-index: 5;
}

.dashboard-list-card .table td,
.dashboard-list-card .table th {
  vertical-align: middle;
}

.dashboard-empty-state {
  text-align: center;
  color: #6c757d;
  padding: 28px 12px;
}

.dashboard-pill {
  background: #ffa726;
  color: #000;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  display: inline-block;
}
</style>

<!-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->
<!-- Main Content -->
<div class="main-content">
  <section class="section">

    <div class="row mb-3 align-items-center">
      <div class="col-12 col-md-6">
        <ul class="breadcrumb breadcrumb-style mb-0">
          <li class="breadcrumb-item">
            <h4 class="page-title m-b-0">Dashboard</h4>
          </li>
        </ul>
      </div>

      <div class="col-12 col-md-6">
        <div class="avatar-group position-relative">
          <div class="col-12 doctor-display card p-4 mt-2 mx-3 position-relative" id="doctorDisplay">
            <p class="mb-0 text-muted">Please select a doctor from the avatars above.</p>
          </div>
          <div class="avatar-group-avatar position-relative">
            <?php
            $visible = array_slice($admins, 0, 3);

            foreach ($visible as $a) {
              $imgPath = "doctor_images/" . $a['doctor_img'];
              $defaultImg = "assets/img/user.png";

              // build avatar content
              if (!empty($a['doctor_img']) && file_exists($imgPath)) {
                $avatarContent = '<img src="' . $imgPath . '" alt="' . htmlspecialchars($a['name']) . '" class="avatar-img">';
              } else {
                $avatarContent = substr($a['name'], 0, 1);
              }

              $imgPath = "doctor_images/" . $a['doctor_img'];
              $defaultImg = "assets/img/user.png";
              $finalImg = (!empty($a['doctor_img']) && file_exists($imgPath)) ? $imgPath : $defaultImg;

              echo '<div class="avatar"
                        data-name="' . htmlspecialchars($a['name']) . '"
                        data-specialization="' . htmlspecialchars($a['specialtisname']) . '"
                        data-img="' . $finalImg . '"
                        onclick="loadDashboardMetrics(\'' . $a['id'] . '\')">'
                . $avatarContent .
                '</div>';
            }

            if ($total > 3) {
              echo '<div class="avatar more" onclick="toggleUsers()">+' . ($total - 3) .
                '</div>';
            }
            ?>
          </div>
          <div class="avatar-doctor-detailes">
            <div id="hiddenUsers" class="hidden-users d-none">
              <?php
              $hidden = array_slice($admins, 3);
              foreach ($hidden as $a) {
                echo '<div class="py-1 px-2 cursor-pointer"
                              data-name="' . htmlspecialchars($a['name']) . '"
                              onclick="loadDashboardMetrics(\'' . $a['id'] . '\')">'
                  . htmlspecialchars($a['name'])
                  . '</div>';
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row ">
      <div class="col-xl-3 col-lg-6">
        <div class="card l-bg-style1">
          <div class="card-statistic-3" style="height: 132px;">
            <div class="card-icon card-icon-large"><i class="fa fa-calendar-check"></i></div>
            <div class="card-content" style="margin-top: 4px;">
              <h4 class="card-title">APPOINTMENTS</h4>
              <?php
              $totalCount = 0;

              // ===== Current Month Appointments =====
              // FIX_B_1903: doctor-scope filter
              $docScope_KPI = currentDoctorScopeSql('doctor_name');
              if ($SessionUserId == "1") {
                $getAppointCount1 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_online WHERE appoint_status='1' AND MONTH(appoint_date)='{$currentMonthArray[1]}' AND YEAR(appoint_date)='{$currentMonthArray[0]}' $docScope_KPI") or die(mysqli_error($conn));
              } else {
                $getAppointCount1 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_online WHERE appoint_status='1' AND MONTH(appoint_date)='{$currentMonthArray[1]}' AND YEAR(appoint_date)='{$currentMonthArray[0]}' AND org_id='{$SessionOrgId}' $docScope_KPI") or die(mysqli_error($conn));
              }
              $row1 = mysqli_fetch_assoc($getAppointCount1);
              $totalCount += $row1['count'];

              if ($SessionUserId == "1") {
                $getAppointCount2 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_existing WHERE appoint_status='1' AND MONTH(appoint_date)='{$currentMonthArray[1]}' AND YEAR(appoint_date)='{$currentMonthArray[0]}'") or die(mysqli_error($conn));
              } else {
                $getAppointCount2 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_existing WHERE appoint_status='1' AND MONTH(appoint_date)='{$currentMonthArray[1]}' AND YEAR(appoint_date)='{$currentMonthArray[0]}' AND org_id='{$SessionOrgId}'") or die(mysqli_error($conn));
              }
              $row2 = mysqli_fetch_assoc($getAppointCount2);
              $totalCount += $row2['count'];

              // ===== Previous Month Appointments =====
              $totalCountTwo = 0;
              if ($SessionUserId == "1") {
                $getAppointCount5 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_online WHERE appoint_status='1' AND MONTH(appoint_date)='{$PreviousMonthArray[1]}' AND YEAR(appoint_date)='{$PreviousMonthArray[0]}' $docScope_KPI") or die(mysqli_error($conn));
              } else {
                $getAppointCount5 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_online WHERE appoint_status='1' AND MONTH(appoint_date)='{$PreviousMonthArray[1]}' AND YEAR(appoint_date)='{$PreviousMonthArray[0]}' AND org_id='{$SessionOrgId}' $docScope_KPI") or die(mysqli_error($conn));
              }
              $row5 = mysqli_fetch_assoc($getAppointCount5);
              $totalCountTwo += $row5['count'];

              if ($SessionUserId == "1") {
                $getAppointCount6 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_existing WHERE appoint_status='1' AND MONTH(appoint_date)='{$PreviousMonthArray[1]}' AND YEAR(appoint_date)='{$PreviousMonthArray[0]}'") or die(mysqli_error($conn));
              } else {
                $getAppointCount6 = mysqli_query($conn, "SELECT COUNT(*) AS count FROM appointment_existing WHERE appoint_status='1' AND MONTH(appoint_date)='{$PreviousMonthArray[1]}' AND YEAR(appoint_date)='{$PreviousMonthArray[0]}' AND org_id='{$SessionOrgId}'") or die(mysqli_error($conn));
              }
              $row6 = mysqli_fetch_assoc($getAppointCount6);
              $totalCountTwo += $row6['count'];

              $CurrentMonthAD = $totalCount;
              $PreviousMonthAD = $totalCountTwo;

              // Calculate percentage difference correctly
              if ($PreviousMonthAD > 0 && $PreviousMonthAD != 0) {
                $percentage = (($CurrentMonthAD - $PreviousMonthAD) / $PreviousMonthAD) * 100;
              } else {
                $percentage = ($CurrentMonthAD > 0) ? 100 : 0;
              }

              // Decide icon and text
              if ($percentage > 0) {
                $iconClass = "fa-arrow-up";
                $PerText = "Increment";
              } elseif ($percentage < 0) {
                $iconClass = "fa-arrow-down";
                $PerText = "Decrement";
              } else {
                $iconClass = "fa-arrows-alt-h"; // neutral icon
                $PerText = "No Change";
              }

              ?>
              <span><?php echo $CurrentMonthAD; ?></span>
              <div class="progress mt-2 mb-1" data-height="8">
                <div class="progress-bar <?= $barColor ?>" role="progressbar"
                  style="width:<?= $growthBar ?>%"
                  aria-valuenow="<?= $growthBar ?>" aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
              <p class="mb-0 text-sm">
                <span class="mr-2"><i class="fa <?= $iconClass ?>"></i> <?= abs(round($percentage, 2)) ?>% </span>
                <span class="text-nowrap"><?= $PerText ?></span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-lg-6">
        <div class="card l-bg-style2">
          <div class="card-statistic-3" style="height: 131px;">
            <div class="card-icon card-icon-large"><i class="fa fa-briefcase"></i></div>
            <div class="card-content">
              <h4 class="card-title">SERVICES</h4>

              <?php
              if ($SessionUserId == "1") {
                $getservice = mysqli_query($conn, "SELECT COUNT(1) FROM services WHERE status='1'");
              } else {
                $getservice = mysqli_query($conn, "SELECT COUNT(1) FROM services WHERE status='1' AND org_id='$SessionOrgId'");
              }
              $resservice = mysqli_fetch_array($getservice);
              $countservice = $resservice[0];
              ?>

              <span><?php echo $countservice; ?></span>
              <div class="progress mt-1 mb-1" data-height="8">
                <div class="progress-bar l-bg-orange" role="progressbar" data-width="<?php echo $countservice; ?>%" aria-valuenow="25"
                  aria-valuemin="0" aria-valuemax="100">
                </div>
              </div>
              <p class="mb-0 text-sm">
                <span class="mr-2"><i class="fa fa-arrow-up"></i> <?php echo $countservice; ?>%</span>
                <span class="text-nowrap">Services</span>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-lg-6">
        <div class="card l-bg-style3">
          <div class="card-statistic-3" style="height: 131px;">
            <div class="card-icon card-icon-large"><i class="fa fa-user-md"></i></div>
            <div class="card-content">
              <h4 class="card-title"> DOCTORS</h4>
              <?php
              if ($SessionUserId == "1") {
                $getdoctor = mysqli_query($conn, "SELECT COUNT(1) FROM doctors WHERE status='1'");
              } else {
                $getdoctor = mysqli_query($conn, "SELECT COUNT(1) FROM doctors WHERE status='1' AND org_id='$SessionOrgId'");
              }
              $resdoctor = mysqli_fetch_array($getdoctor);
              $countdocters = $resdoctor[0];
              ?>
              <span><?php echo $countdocters; ?></span>
              <div class="progress mt-1 mb-1" data-height="8">
                <div class="progress-bar l-bg-cyan" role="progressbar" data-width="<?php echo $countdocters; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="mb-0 text-sm">
                <span class="mr-2"><i class="fa fa-arrow-up"></i> <?php echo $countdocters; ?>%</span>
                <span class="text-nowrap">Doctors</span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-lg-6">
        <div class="card l-bg-style4">
          <div class="card-statistic-3" style="height: 131px;">
            <div class="card-icon card-icon-large"><i class="fa fa-id-card-alt"></i></div>
            <div class="card-content">
              <h4 class="card-title"> DEPARTMENTS</h4>
              <?php
              if ($SessionUserId == "1") {
                $getdepartment = mysqli_query($conn, "SELECT COUNT(1) FROM department WHERE status='1'");
              } else {
                $getdepartment = mysqli_query($conn, "SELECT COUNT(1) FROM department WHERE status='1' AND org_id='$SessionOrgId'");
              }
              $resdepartment = mysqli_fetch_array($getdepartment);
              $countdepartments = $resdepartment[0];
              ?>
              <span><?php echo $countdepartments; ?></span>
              <div class="progress mt-1 mb-1" data-height="8">
                <div class="progress-bar l-bg-green" role="progressbar" data-width="<?php echo $countdepartments; ?>%" aria-valuenow="25"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="mb-0 text-sm">
                <span class="mr-2"><i class="fa fa-arrow-up"></i> <?php echo $countdepartments; ?>%</span>
                <span class="text-nowrap">Departments</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>Detailed Metrics</h4>
          </div>
          <div class="card-body p-3">
            <div class="row text-center">
              <div class="col-md-3 col-12 border-end">
                <i class="bi bi-calendar-check fs-2 text-success"></i>
                <h6 class="mt-2">Today Appointments</h6>
                <h4 id="todayAppointments" class="fw-bold">0</h4>
                <small class="text-muted">Scheduled Today</small>
              </div>

              <div class="col-md-3 col-12 border-end">
                <i class="bi bi-arrow-repeat fs-2 text-info"></i>
                <h6 class="mt-2">Follow-up Appointments</h6>
                <h4 id="followUps" class="fw-bold">0</h4>
                <small class="text-muted">Due this month</small>
              </div>

              <div class="col-md-3 col-12 border-end">
                <i class="bi bi-stopwatch fs-2 text-danger"></i>
                <h6 class="mt-2">Average Waiting Time</h6>
                <h4 id="avgWaitTime" class="fw-bold">0 min</h4>
                <small class="text-muted">Across all visits</small>
              </div>

              <div class="col-md-3 col-12 border-end">
                <i class="bi bi-person-workspace fs-2 text-warning"></i>
                <h6 class="mt-2">Doctors on Duty</h6>
                <!-- <?php if ($doctorsOnDuty > 0): ?>
                                    <h4 class="fw-bold text-success">Active</h4>
                                    <small class="text-muted"><?php echo $doctorsOnDuty; ?> currently available</small>
                                <?php else: ?>
                                    <h4 class="fw-bold text-danger">Inactive</h4>
                                    <small class="text-muted">No doctors available today</small>
                                <?php endif; ?> -->
                <h4 id="doctorsStatus" class="fw-bold"></h4>
                <small id="doctorsCount" class="text-muted"></small>
              </div>
              <!-- <div class="col-md-3 col-6">
                              <i class="bi bi-graph-up fs-2 text-success"></i>
                              <h6 class="mt-2">Overall Growth</h6>
                              <h4 class="fw-bold"><?php echo $overallGrowth; ?>%</h4>
                              <small class="text-muted">This month</small>
                          </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>

   <div class="row dashboard-cards-row">
  <!-- Appointments Card -->
  <div class="col-12 col-lg-6">
    <div class="card fixed-height-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Appointments</h4>
        <div class="card-header-form">
          <form>
            <div class="input-group">
              <input type="date" class="form-control" name="date" id="date" onchange="GetAppointDate()" value="<?php echo date("Y-m-d"); ?>">
            </div>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="scrollable-container">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th><b>Patient Name</b></th>
                  <th><b>Patient ID</b></th>
                  <th><b>Status</b></th>
                  <th><b>Payment</b></th>
                </tr>
              </thead>
              <tbody id="tabledata">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Doctors + Slots Cards -->
  <div class="col-12 col-lg-6">
    <!-- Doctors Card -->
    <div class="card mb-4 fixed-height-card" style="height: 180px;"> <!-- Smaller fixed height for doctors card -->
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h4 class="mb-0">Doctors</h4>
        <div class="card-header-form">
          <form>
            <div class="input-group">
              <select class="form-control form-select" name="department" id="department" onchange="getdocservice()">
              </select>
            </div>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="scrollable-container">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th><b>Doctor ID</b></th>
                  <th><b>Doctor Name</b></th>
                  <th><b>Department</b></th>
                  <th><b>Service</b></th>
                </tr>
              </thead>
              <tbody id="tabledatadoc">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Doctor Slots Card -->
    <div class="card fixed-height-card" style="height: 200px;"> <!-- Fixed height for slots card -->
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h4 class="mb-0">Doctor Slots</h4>
        <div class="dropdown">
          <button class="btn btn-filter dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Month
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
            <li class="dropdown-item">Select Period</li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div class="scrollable-container">
          <div id="doctorSlotChart" class="doctor-list"></div>
        </div>
      </div>
    </div>
  </div>
</div>


    <div class="row">
      

      <div class="col-12 col-lg-6 col-md-12 col-sm-12 d-flex mb-4">
        <div class="card flex-fill">
          <div class="card-header">
            <h4 class="mt-2">User Access List</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive collapse1" id="proTeamScroll">
              <table class="table table">
                <thead>
                  <tr>
                    <th><b>User Name</b></th>
                    <th><b>Contact</b></th>
                    <th><b>Role</b></th>
                  </tr>
                </thead>
                <tbody id="tabledatalist"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Top 5 Prescribed Tests</h4>
            <div class="card-header-form">
              <select class="form-control form-select" id="topTestsYear" onchange="loadTopPrescribedTests()" style="min-width:90px;">
                <?php
                $cy = date('Y');
                for ($y = $cy - 3; $y <= $cy + 1; $y++) {
                  $sel = ($y == $cy) ? 'selected' : '';
                  echo "<option value='$y' $sel>$y</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive collapse1">
              <table class="table table" id="topTestsTableEl">
                <thead id="topTestsHead">
                  <tr>
                    <th><b>#</b></th>
                    <th><b>Test Name</b></th>
                    <th><b>Total</b></th>
                  </tr>
                </thead>
                <tbody id="topTestsTable">
                  <tr><td colspan="3" class="text-center text-muted py-3">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-lg-6 col-md-12 col-sm-12 d-flex mb-4">
        <div class="card flex-fill dashboard-list-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mt-2">Upcoming Birthdays</h4>
            <span class="dashboard-pill" id="dashboardBirthdayCount">Top 10</span>
          </div>
          <div class="card-body">
            <div class="table-responsive collapse1">
              <table class="table table">
                <thead>
                  <tr>
                    <th><b>Patient</b></th>
                    <th><b>Mobile</b></th>
                    <th><b>Birthday</b></th>
                    <th><b>Age</b></th>
                  </tr>
                </thead>
                <tbody id="dashboardBirthdayTable">
                  <tr>
                    <td colspan="4" class="dashboard-empty-state">Loading upcoming birthdays...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 col-md-12 col-sm-12 d-flex mb-4">
        <div class="card flex-fill dashboard-list-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mt-2">Upcoming Revisits</h4>
            <span class="dashboard-pill" id="dashboardRevisitCount">Top 10</span>
          </div>
          <div class="card-body">
            <div class="table-responsive collapse1">
              <table class="table table">
                <thead>
                  <tr>
                    <th><b>Patient</b></th>
                    <th><b>Mobile</b></th>
                    <th><b>Doctor</b></th>
                    <th><b>Department</b></th>
                    <th><b>Revisit Date</b></th>
                  </tr>
                </thead>
                <tbody id="dashboardRevisitTable">
                  <tr>
                    <td colspan="5" class="dashboard-empty-state">Loading upcoming revisits...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      
      <div class="col-md-12 col-lg-12 col-xl-12 ">
        <div class="card">
          <div class="card-header ">
            <h4>Appointments</h4>

            <div class="card-header-action">
              <ul class="nav nav-pills" role="tablist" id="chart-tabs">
                <li class="nav-item" role="presentation">
                  <?php
                  $currentYear = date("Y");
                  $yearsToShow = 2;
                  $startYear = $currentYear - $yearsToShow;
                  $endYear = $currentYear + $yearsToShow;
                  ?>

                  <select class="form-select shadow" name="year" id="year">
                    <?php
                    for ($year = $startYear; $year <= $endYear; $year++) {
                      $selected = ($year == $currentYear) ? 'selected' : '';
                      echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                  </select>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div id="chart"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<?php require_once("ajax/footer.php") ?>


<script>
  <?php
  function getAppointmentsByYear($conn, $year)
  {
    $appointments = array();
    $SessionOrgId = $_SESSION['org_id'];
    // FIX_B_1903: doctor-scope filter
    $docScope_chart = currentDoctorScopeSql('doctor_name');
    $getAppoint1 = mysqli_query($conn, "SELECT appoint_date FROM appointment_online WHERE  YEAR(appoint_date) = '$year' AND appoint_status='1' $docScope_chart");
    while ($resAppoint1 = mysqli_fetch_object($getAppoint1)) {
      $shortMonth = date("M", strtotime($resAppoint1->appoint_date));
      $appointments[] = $shortMonth;
    }

    $getAppoint2 = mysqli_query($conn, "SELECT appoint_date FROM appointment_existing WHERE  YEAR(appoint_date) = '$year' AND org_id='$SessionOrgId' AND appoint_status='1'");
    while ($resAppoint2 = mysqli_fetch_object($getAppoint2)) {
      $shortMonth = date("M", strtotime($resAppoint2->appoint_date));
      $appointments[] = $shortMonth;
    }

    $allShortMonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

    $monthYearCountArray = array_count_values($appointments);

    foreach ($allShortMonths as $shortMonth) {
      if (!isset($monthYearCountArray[$shortMonth])) {
        $monthYearCountArray[$shortMonth] = 0;
      }
    }

    $sortedMonthYearCountArray = array();
    foreach ($allShortMonths as $shortMonth) {
      $sortedMonthYearCountArray[$shortMonth] = $monthYearCountArray[$shortMonth];
    }

    $shortMonths = array_keys($sortedMonthYearCountArray);
    $countData = array_values($sortedMonthYearCountArray);

    return array('shortMonths' => $shortMonths, 'countData' => $countData);
  }

  $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
  $dataForYear = getAppointmentsByYear($conn, $year);

  $jsonShortMonths = json_encode($dataForYear['shortMonths']);
  $jsonCountData = json_encode($dataForYear['countData']);
  ?>

  var jsonMonthsYears = <?php echo $jsonShortMonths; ?>;
  var jsonCountData = <?php echo $jsonCountData; ?>;
  var defaultDashboardSecurityId = <?php echo json_encode((string) $defaultDashboardSecurityId); ?>;
  var currentSelectedSecId = defaultDashboardSecurityId;
  var birthdayPopupShownFor = {};

  $("document").ready(function() {
    GetAppointDate(defaultDashboardSecurityId);
    getDepartment();
    getdocservice();
    loadTopPrescribedTests();
    loadDoctorSlotChart(defaultDashboardSecurityId);
    renderChart();
    fetchSecurityData();
    loadDashboardMetrics(defaultDashboardSecurityId);
  });



  document.addEventListener('DOMContentLoaded', () => {
    const avatars = document.querySelectorAll('.avatar-group-avatar .avatar');
    const display = document.getElementById('doctorDisplay');

    avatars.forEach(av => {
      av.addEventListener('mouseenter', () => {
        const name = av.dataset.name || 'Choose';
        const spec = av.dataset.specialization || '';
        const img = av.dataset.img || 'assets/img/user.png';

        display.innerHTML = `
          <div class="d-flex align-items-center gap-2">
            <img src="${img}" alt="${name}" 
                class="rounded-circle avatar-img" 
                style="width:50px; height:50px; object-fit:cover;">
            <div>
              <h5 id="docName" class="mb-1">${name}</h5>
              <small id="docSpec" class="text-muted">
                Specialization: ${spec}
              </small>
            </div>
          </div>
        `;
      });

      av.addEventListener('mouseleave', () => {
        // Optional: reset display
        // display.innerHTML = '<p class="mb-0 text-muted">Please select a doctor.</p>';
      });
    });
  });



  function loadDashboardMetrics(secId) {
    secId = secId || defaultDashboardSecurityId;
    currentSelectedSecId = secId;
    // Find the clicked element
    const clicked = document.querySelector(
      `[onclick="loadDashboardMetrics('${secId}')"]`
    );

    const display = document.getElementById('doctorDisplay');
    const isAll = (String(secId) === '0');
    const name = isAll ? 'All Doctors' : (clicked?.dataset.name || 'Choose');
    const specialization = isAll ? '' : (clicked?.dataset.specialization || '-');
    const avatar = isAll ? 'assets/img/default-doctor.png' : (clicked?.dataset.img || 'assets/img/default-doctor.png');

    // Initial doctor info before AJAX
    display.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="me-3">
                <img id="docAvatar"
                     src="${avatar}" 
                     alt="${name}" 
                     class="rounded-circle border" 
                     style="width:60px; height:60px; object-fit:cover;">
            </div>
            <div>
                <h5 id="docName" class="mb-1">${name}</h5>
                <small id="docSpec" class="text-muted">
                    Specialization: ${specialization}
                </small>
            </div>
        </div>
    `;

    GetAppointDate(secId);
    loadDoctorSlotChart(secId);
    loadDashboardLists(secId);

    $.ajax({
      url: 'ajax/dashbord/get_dashboard_metrics.php',
      method: 'GET',
      dataType: 'json',
      data: {
        security_id: secId
      },
      success: function(data) {
        $('#todayAppointments').text(data.todayAppointmentCount || 0);
        $('#followUps').text(data.followUpcount || 0);

        $('#avgWaitTime').text((data.avgWaitingTime || 0) + ' min');
        $('#docName').text(data.doctor_name || name);
        $('#docSpec').text(
          'Specialization: ' + (data.specialtisname || specialization || '--')
        );

        if (data.doctor_img) {
          $('#docAvatar').attr(
            'src',
            '' + data.doctor_img
          );
        }

        if (parseInt(data.doctorsOnDuty, 10) > 0) {
          $('#doctorsStatus')
            .text('Active')
            .removeClass('text-danger')
            .addClass('text-success');
          $('#doctorsCount').text(data.doctorsOnDuty + ' currently available');
        } else {
          $('#doctorsStatus')
            .text('Inactive')
            .removeClass('text-success')
            .addClass('text-danger');
          $('#doctorsCount').text('No doctors available today');
        }
      },
      error: function(xhr, status, error) {
        console.error('Failed to load metrics:', error);
        $('#todayAppointments').text('0');
        $('#followUps').text('0');
        $('#avgWaitTime').text('0 min');
        $('#doctorsStatus')
          .text('Error')
          .removeClass('text-success')
          .addClass('text-danger');
        $('#doctorsCount').text('Unable to fetch data');
      }
    });
  }

  function loadDashboardLists(secId) {
    secId = secId || defaultDashboardSecurityId;

    $.ajax({
      url: 'ajax/dashbord/get_dashboard_lists.php',
      method: 'GET',
      dataType: 'json',
      data: {
        security_id: secId
      },
      success: function(response) {
        renderBirthdayRows(response.birthdays || []);
        renderRevisitRows(response.revisits || []);

        if (response.today_birthdays && response.today_birthdays.length && !birthdayPopupShownFor[secId]) {
          birthdayPopupShownFor[secId] = true;
          showBirthdayPopup(response.today_birthdays);
        }
      },
      error: function() {
        $('#dashboardBirthdayCount').text('Top 10');
        $('#dashboardRevisitCount').text('Top 10');
        $('#dashboardBirthdayTable').html('<tr><td colspan="4" class="dashboard-empty-state">Unable to load upcoming birthdays.</td></tr>');
        $('#dashboardRevisitTable').html('<tr><td colspan="4" class="dashboard-empty-state">Unable to load upcoming revisits.</td></tr>');
      }
    });
  }

  function renderBirthdayRows(items) {
    $('#dashboardBirthdayCount').text((items.length || 0) + ' patients');

    if (!items.length) {
      $('#dashboardBirthdayTable').html('<tr><td colspan="4" class="dashboard-empty-state">No upcoming birthdays found.</td></tr>');
      return;
    }

    let html = '';
    items.forEach(function(item) {
      html += '<tr>';
      html += '<td><strong>' + escapeDashboardHtml(item.patient_name) + '</strong><br><small class="text-muted">' + escapeDashboardHtml(item.patient_id) + '</small></td>';
      html += '<td>' + escapeDashboardHtml(item.mobile_number) + '</td>';
      html += '<td><span class="dashboard-pill">' + escapeDashboardHtml(item.days_label) + '</span><br><small>' + escapeDashboardHtml(item.next_birthday_display) + '</small></td>';
      html += '<td>' + escapeDashboardHtml(item.turning_age) + '</td>';
      html += '</tr>';
    });

    $('#dashboardBirthdayTable').html(html);
  }

  function renderRevisitRows(items) {
    $('#dashboardRevisitCount').text((items.length || 0) + ' patients');

    if (!items.length) {
      $('#dashboardRevisitTable').html('<tr><td colspan="5" class="dashboard-empty-state">No upcoming revisits found.</td></tr>');
      return;
    }

    let html = '';
    items.forEach(function(item) {
      html += '<tr>';
      html += '<td><strong>' + escapeDashboardHtml(item.patient_name) + '</strong><br><small class="text-muted">' + escapeDashboardHtml(item.patient_id) + '</small></td>';
      html += '<td>' + escapeDashboardHtml(item.mobile_number) + '</td>';
      html += '<td>' + escapeDashboardHtml(item.doctor_name) + '</td>';
      html += '<td>' + escapeDashboardHtml(item.department_name) + '</td>';
      html += '<td><span class="dashboard-pill">' + escapeDashboardHtml(item.days_label) + '</span><br><small>' + escapeDashboardHtml(item.revisit_date_display) + '</small></td>';
      html += '</tr>';
    });

    $('#dashboardRevisitTable').html(html);
  }

  function showBirthdayPopup(items) {
    let html = '<div class="text-start">';
    html += '<p class="mb-2"><strong>Today\'s birthday patients</strong></p>';
    html += '<table class="table table-sm mb-0"><thead><tr><th>Patient</th><th>Mobile</th><th>Age</th></tr></thead><tbody>';

    items.forEach(function(item) {
      html += '<tr>';
      html += '<td>' + escapeDashboardHtml(item.patient_name) + '</td>';
      html += '<td>' + escapeDashboardHtml(item.mobile_number) + '</td>';
      html += '<td>' + escapeDashboardHtml(item.turning_age) + '</td>';
      html += '</tr>';
    });

    html += '</tbody></table></div>';

    const popupContainer = document.createElement('div');
    popupContainer.innerHTML = html;

    swal({
      title: 'Birthday Wishes Reminder',
      content: popupContainer,
      icon: 'info',
      buttons: {
        confirm: {
          text: 'Close',
          value: true,
          visible: true,
          className: 'btn btn-primary'
        }
      }
    });
  }

  function escapeDashboardHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
  }

  function toggleUsers() {
    document.getElementById('hiddenUsers').classList.toggle('d-none');
  }

  function userRoleBadge(securityType, roleName) {
    var label = (securityType === 'A') ? 'Admin' : (roleName || 'User');
    return '<span style="background:#1a56a0;color:#fff;font-weight:600;padding:4px 12px;border-radius:20px;display:inline-block;">'
      + escapeDashboardHtml(label) + '</span>';
  }

  function fetchSecurityData() {
    $.ajax({
      url: 'ajax/dashbord/getuserlist.php',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        let html = '';
        response.forEach(function(row) {
          html += '<tr>';
          html += '<td>' + escapeDashboardHtml(row.admin_name) + '</td>';
          html += '<td>' + escapeDashboardHtml(row.contact) + '</td>';
          html += '<td>' + userRoleBadge(row.security_type, row.role_name) + '</td>';
          html += '</tr>';
        });
        $('#tabledatalist').html(html);
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
      }
    });
  }

  function loadDoctorSlotChart(secId) {
    $.ajax({
      url: 'ajax/dashbord/getDoctorSlotStats.php',
      type: 'GET',
      dataType: 'json',
      data: {
        security_id: secId
      },
      success: function(response) {
        if (response.status === "error") {
          console.error("Server Error:", response.message);
          $("#doctorSlotChart").html(
            `<div class="error-msg">${response.message}</div>`
          );
          return;
        }

        let data = response.data || [];

        if (data.length === 0) {
          $("#doctorSlotChart").html(
            `<div class="no-record">No doctor slot data found for this month.</div>`
          );
          return;
        }

        let html = `<div class="doctor-list-scroll">`;

        data.forEach(item => {
          let slots = item.slots ?? 0;
          let rangeSlots = item.working_days ?? 0;
          let dailyCountTotal = item.dailyCounts ?? 0;

          if (slots === 0 && rangeSlots === 0) {
            return;
          }

          let imgPath =
            item.doc_img && item.doc_img.trim() !== "" ?
            "doctor_images/" + item.doc_img :
            "doctor_images/default.png";

          let avatar = `<img src="${imgPath}" class="doctor-avatar" alt="Dr. ${item.doctor}">`;

          let doctorName =
            item.doctor.trim().toLowerCase().startsWith("dr.") ?
            item.doctor :
            `Dr. ${item.doctor}`;

          let doctorType = item.doctor_type ?? "";

          let perDaySlotsHtml = "";
          if (item.slots_per_day && Object.keys(item.slots_per_day).length > 0) {
            perDaySlotsHtml = `<ul class="per-day-slots">`;
            for (let date in item.slots_per_day) {
              perDaySlotsHtml += `<li>${date}: ${item.slots_per_day[date]} Slots</li>`;
            }
            perDaySlotsHtml += `</ul>`;
          }

          html += `
            <div class="doctor-item">
              <div class="doctor-info">
                <div class="doctor-name">
                  ${avatar}
                  <span class="doctor-text">
                    ${doctorName}
                    <span class="doctor-slots">
                      ${dailyCountTotal} Days / ${slots} Slots Booked
                    </span>
                  </span>
                </div>
                <div class="progress-bars">
                  <div class="progress-line progress-blue" style="width:${item.slots_percent}%;"></div>
                  <div class="progress-line progress-red" style="width:${item.dailyCounts}%;"></div>
                </div>
              </div>
              <div class="doctor-stats text-end">
                <div class="slot-count">${slots}</div>
                <div class="day-count">${dailyCountTotal}</div>
              </div>
            </div>
          `;

        });

        html += `</div>`;
        $("#doctorSlotChart").html(html);

        const doctorItems = $(".doctor-item");
        if (doctorItems.length > 6) {
          $(".doctor-list-scroll").css({
            "max-height": (doctorItems.first().outerHeight(true) * 6) + "px",
            "overflow-y": "hidden",
            "scrollbar-gutter": "stable"
          }).hover(
            function() {
              $(this).css("overflow-y", "auto");
            },
            function() {
              $(this).css("overflow-y", "hidden");
            }
          );
        }

      },
      error: function(xhr, status, error) {
        console.error("Error loading data:", error);
        $("#doctorSlotChart").html(
          `<div class="error-msg">Failed to load doctor slots.</div>`
        );
      }
    });
  }

  function renderChart() {
    var options = {
      series: [{
        name: "Appointments",
        data: jsonCountData,
      }, ],
      chart: {
        height: 380,
        type: "area",
        dropShadow: {
          enabled: true,
          opacity: 0.3,
          blur: 5,
          left: -7,
          top: 22,
        },
        toolbar: {
          show: false,
        },
      },
      colors: ["#8b31d0", "#757575"],
      dataLabels: {
        enabled: false,
      },
      stroke: {
        show: true,
        curve: "smooth",
        width: 3,
        lineCap: "square",
      },
      xaxis: {
        categories: jsonMonthsYears,
        style: {
          colors: "#8e8da4",
        },
      },
      yaxis: {
        labels: {
          offsetX: 0,
          offsetY: 0,
          style: {
            color: "#8e8da4",
          },
        },
      },
      legend: {
        position: "top",
        horizontalAlign: "right",
        markers: {
          width: 10,
          height: 10,
        },
        itemMargin: {
          horizontal: 0,
          vertical: 20,
        },
        labels: {
          colors: "#8e8da4",
          useSeriesColors: false,
        },
      },
      tooltip: {
        theme: "dark",
        marker: {
          show: true,
        },
        x: {
          show: true,
        },
      },
      fill: {
        type: "gradient",
        gradient: {
          type: "vertical",
          shadeIntensity: 1,
          inverseColors: !1,
          opacityFrom: 0.28,
          opacityTo: 0.05,
          stops: [45, 100],
        },
      },
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    $('#year').change(function(e) {
      var id = this.value;
      if (id) {
        $.ajax({
          url: 'ajax/dashbord/getmonthyear.php',
          type: 'post',
          data: {
            year: id,
            security_id: currentSelectedSecId
          },
          success: function(data) {
            var response = JSON.parse(data);

            var year = JSON.parse(response.year);

            chart.updateSeries([{
              name: "Appointments",
              data: year
            }]);
          },
        });

      }
    });
  }

  function renderChart1(val) {
    var id = val;
    if (id) {

      chart.updateSeries([{
        name: "income",
        data: [1, 8, 6, 0, 0, 0, 5, 0]
      }]);
    }
  }

  function GetAppointDate(secId) {
    if (secId === undefined || secId === null) secId = currentSelectedSecId;
    var appoint_date = $('#date').val();

    $.ajax({
      url: 'ajax/dashbord/getdate.php',
      type: 'POST',
      data: {
        'appoint_date': appoint_date,
        'security_id': secId
      },
      success: function(data) {
        $("#tabledata").html(data);
      },
      error: function(err) {
        console.log(err);
      }
    });
  }

  function getDepartment() {
    $.ajax({
      url: 'ajax/dashbord/getdepartment.php',
      type: 'get',
      dataType: 'json',
      success: function(data) {
        var optionData = '<option value="0">All Departments</option>';
        $.each(data, function(key, val) {
          optionData += '<option value="' + val.dept_id + '"> ' + val.departmentName + ' </option>';
        });
        $("#department").html(optionData);
        getdocservice();
      },
      error: function(err) {
        console.log(err);
      }
    });
  }

  function getdocservice() {
    var dept_id = $("#department").val();
    var part = {};
    part['dept_id'] = dept_id;
    $.ajax({
      url: 'ajax/dashbord/getdocservice.php',
      type: 'POST',
      data: part,
      success: function(data) {
        $("#tabledatadoc").html(data);
      },
      error: function(err) {
        console.log(err);
      }
    });
  }

  function roleColor(role) {
    return '#1a56a0';
  }

  function loadTopPrescribedTests() {
    var year = $('#topTestsYear').val() || <?= date('Y') ?>;
    $.ajax({
      url: 'ajax/dashbord/gettopprescribedtests.php',
      type: 'POST',
      dataType: 'json',
      data: { year: year },
      success: function(resp) {
        var tests = resp.tests || [];
        var roles = resp.roles || [];

        // Build dynamic header
        var headHtml = '<tr><th><b>#</b></th><th><b>Test Name</b></th><th><b>Total</b></th>';
        roles.forEach(function(r) {
          headHtml += '<th><b style="color:' + roleColor(r) + ';">' + r + '</b></th>';
        });
        headHtml += '</tr>';
        $('#topTestsHead').html(headHtml);

        if (!tests.length) {
          var colspan = 3 + roles.length;
          $('#topTestsTable').html('<tr><td colspan="' + colspan + '" class="text-center text-muted py-3">No tests prescribed in ' + year + '</td></tr>');
          return;
        }

        var html = '';
        tests.forEach(function(item, i) {
          html += '<tr>';
          html += '<td>' + (i + 1) + '</td>';
          html += '<td><strong>' + $('<div>').text(item.test_name).html() + '</strong></td>';
          html += '<td><span style="background:#1a56a0;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">' + item.total + '</span></td>';
          roles.forEach(function(r) {
            var cnt = (item.by_role && item.by_role[r]) ? item.by_role[r] : 0;
            html += '<td>';
            if (cnt > 0) {
              html += '<span style="background:' + roleColor(r) + ';color:#fff;padding:2px 8px;border-radius:20px;font-size:12px;font-weight:600;">' + cnt + '</span>';
            } else {
              html += '<span class="text-muted">-</span>';
            }
            html += '</td>';
          });
          html += '</tr>';
        });
        $('#topTestsTable').html(html);
      },
      error: function() {
        $('#topTestsTable').html('<tr><td colspan="6" class="text-danger text-center py-2">Failed to load test data.</td></tr>');
      }
    });
  }


  $(document).on("click", ".clickable-row", function() {
    var appointmentId = $(this).data("appoint-register-id");
    var orgid = $(this).data("org-id");
    var appointid = $(this).data("appoint-id");
    if (appointmentId) {
      window.location.href = "prescription.php?appointRegisterId=" + appointmentId + "&OrgId=" + orgid + "&AppointId=" + appointid + "";
    }
  });


</script>
