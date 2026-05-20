<?php
  require_once("ajax/header.php");

  $SessionUserId = $_SESSION['security_id'] ?? '';
  $SessionRoleId = $_SESSION['role_id'] ?? '';
  $SessionOrgId = $_SESSION['org_id'] ?? '';
?>

<style>
  /* ✅ Dashboard banner style */
  .dashboard-banner {
    background: url('img/orgdashboardimg.jpeg') no-repeat center center;
    background-size: cover; /* makes it cover fully */
    width: 100%;
    height:420px; /* adjust height as you prefer */
    border-radius: 12px;  /* optional */
    box-shadow: 0px 4px 12px rgba(0,0,0,0.2); /* optional */
  }

  /* Prevent extra vertical scroll */
  body, html {
    margin: 0;
    padding: 0;
    overflow-x: hidden; /* disable horizontal scroll */
    overflow-y: hidden;
  }
  .main-content {
    padding-left: 250px;
    padding-right: 0px;
    padding-top: 64px;
    padding-bottom: 0px;
    width: 100%;
    position: relative;
    height:auto;
}
.main-footer {
    padding: 20px 30px 20px 280px;
    margin-top: 40px;
    color: #98a6ad;
    border-top: 1px solid #e3eaef;
    display: inline-block;
    background: #fff;
    font-weight: 400;
    text-transform: uppercase;
    font-size: 11px;
    width: 100%;
}
</style>

<!-- Main Content --> 
<div class="main-content">
  <section class="section">

    <!-- <div class="d-flex justify-content-between align-items-center">
      <ul class="breadcrumb breadcrumb-style mb-0">
        <li class="breadcrumb-item">
          <h4 class="page-title m-b-0">Dashboard</h4>
        </li>
      </ul>
    </div> -->

    <!-- ✅ Dashboard Banner -->
    <div class="dashboard-banner"></div>

  </section>
</div>

<?php require_once("ajax/footer.php") ?>
