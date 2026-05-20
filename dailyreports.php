<?php require_once("ajax/header.php");
// FIX_B_1840 — RBAC: per-action page guard (view).
requireCan('view', basename(__FILE__));
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Stat cards (circle-like design) */
  .stat-card {
    border-radius: 20px;
    padding: 25px;
    text-align: center;
    color: #fff;
    font-weight: bold;
    box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-5px);
  }

  .stat-title {
    font-size: 14px;
    opacity: 0.9;
  }

  .stat-value {
    font-size: 24px;
    margin-top: 10px;
  }

  .bg-gross {
    background: #4e73df;
  }

  .bg-discount {
    background: #f6c23e;
  }

  .bg-tax {
    background: #36b9cc;
  }

  .bg-net {
    background: #1cc88a;
  }

  /* Chart container */
  #revenueChart {
    max-height: 350px;
  }
</style>

<div class="main-content">
  <section class="section">
    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item">
        <h4 class="page-title m-b-0">Daily Billing Report</h4>
      </li>
      <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
      <li class="breadcrumb-item">Reports</li>
      <li class="breadcrumb-item">Daily Billing</li>
    </ul>
    <!-- Stat Cards -->
    <div class="row mt-4">
      <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-gross">
          <div class="stat-title">Gross Revenue</div>
          <div class="stat-value">₹</div>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-discount">
          <div class="stat-title">Total Discount</div>
          <div class="stat-value">₹</div>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-tax">
          <div class="stat-title">Total Tax</div>
          <div class="stat-value">₹</div>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-3">
        <div class="stat-card bg-net">
          <div class="stat-title">Net Revenue</div>
          <div class="stat-value">₹</div>
        </div>
      </div>
    </div>
    <!-- Filter Box -->
    <div class="card">
      <div class="card-body">
        <div class="row">
          <?php
          if ($SessionUserId == "1" && $SessionRoleId == "1") {
          ?>
            <div class="form-group col-lg-3 col-sm-12">
              <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
              <select class="form-control form-select" name="organizations" id="organizations">
                <option value="">Select Organization</option>
                <?php
                $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                while ($ResOrganization = mysqli_fetch_object($GetOrganization)) {
                ?>
                  <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                <?php
                }
                ?>
              </select>
            </div>
          <?php
          } else {
          ?>
            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
          <?php
          }
          ?>
        </div>
        <form id="reportFilter" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Doctor </label>
            <select id="doctor_id" class="form-control">
              <option value="All">All</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Patient ID</label>
            <select id="patient_id" class="form-control">
              <option value="All">All</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Current Date</label>
            <input type="date" id="fromDate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
          </div>

          <!-- <div class="col-md-4">
            <label class="form-label">To Date</label>
            <input type="date" id="toDate" class="form-control">
          </div> -->
        </form>
      </div>
      <div class="card-footer text-center">
        <button class="btn btn-primary" id="submitBtn">Submit</button>
      </div>
    </div>

    <!-- Bar Graph -->
    <div class="card mt-3">
      <div class="card-header">
        <h5>Revenue Breakdown</h5>
      </div>
      <div class="card-body">
        <canvas id="revenueChart"></canvas>
      </div>
    </div>

    <!-- Result Table -->
    <div class="card mt-3">
      <div class="card-header">
        <h5>Report Results</h5>
      </div>
      <div class="card-body">
        <table id="billingTable" class="display nowrap" style="width:100%">
          <thead>
            <tr>
              <th>Date</th>
              <th>Patient ID</th>
              <th>Patient Name</th>
              <th>Total Amount</th>
              <!-- <th>Action</th> -->
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php require_once("ajax/footer.php"); ?>

<script>
  $(document).ready(function() {

    // --- Initialize Chart.js ---
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Today'],
        datasets: [{
            label: 'Gross',
            data: [0],
            backgroundColor: '#4e73df'
          },
          {
            label: 'Discount',
            data: [0],
            backgroundColor: '#f6c23e'
          },
          {
            label: 'Tax',
            data: [0],
            backgroundColor: '#36b9cc'
          },
          {
            label: 'Net',
            data: [0],
            backgroundColor: '#1cc88a'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // --- Function to load Stat Cards + Chart ---
    function loadStats() {
      let from = $("#fromDate").val();
      // let to = $("#toDate").val();
      let patient_id = $("#patient_id").val();
      let doctor_id = $("#doctor_id").val();
      let orgId = $("#organizations").val();

      $.getJSON("ajax/DailyReports/daily_reports.php", {
        from,
        patient_id,
        doctor_id,
        orgId
      }, function(res) {
        if (res.totals) {
          $(".bg-gross .stat-value").text("₹" + res.totals.gross_revenue);
          $(".bg-discount .stat-value").text("₹" + res.totals.total_discount);
          $(".bg-tax .stat-value").text("₹" + res.totals.total_tax);
          $(".bg-net .stat-value").text("₹" + res.totals.net_revenue);

          // Update chart
          revenueChart.data.datasets[0].data = [parseFloat(res.totals.gross_revenue.replace(/,/g, ''))];
          revenueChart.data.datasets[1].data = [parseFloat(res.totals.total_discount.replace(/,/g, ''))];
          revenueChart.data.datasets[2].data = [parseFloat(res.totals.total_tax.replace(/,/g, ''))];
          revenueChart.data.datasets[3].data = [parseFloat(res.totals.net_revenue.replace(/,/g, ''))];
          revenueChart.update();
        }
      });
    }

    // --- Initialize DataTable ---
    let billingTable = $('#billingTable').DataTable({
      ajax: {
        url: 'ajax/DailyReports/daily_reports.php',
        data: function(d) {
          d.from = $("#fromDate").val();
          d.to = $("#toDate").val();
          d.patient_id = $("#patient_id").val();
          d.doctor_id = $("#doctor_id").val();
          d.orgId = $("#organizations").val();
        }
      },
      columns: [{
          data: "date"
        },
        {
          data: "patient_id"
        },
        {
          data: "patient_name"
        },
        {
          data: "total"
        }
      ]
    });

    // --- Filter submit ---
    $("#submitBtn").on("click", function(e) {
      e.preventDefault();
      loadStats(); // update cards + chart
      billingTable.ajax.reload(); // reload table
    });

    // --- Function to fetch doctor/patient data ---
    function fetchPatientDoctor(doctorId = 'All', patientId = 'All', isInitial = false) {
      $.ajax({
        url: 'ajax/DailyReports/patientid.php',
        method: 'GET',
        dataType: 'json',
        data: {
          doctor_id: doctorId,
          patient_id: patientId
        },
        success: function(data) {
          console.log('Fetched doctor/patient data:', data);

          // --- Populate doctor dropdown only on initial load ---
          if (isInitial) {
            let doctorSelect = $('#doctor_id');
            let currentDoctor = doctorSelect.val() || 'All';
            doctorSelect.empty().append('<option value="All">All</option>');
            data.doctors.forEach(item => {
              let selected = (item.id == currentDoctor) ? 'selected' : '';
              doctorSelect.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
            });
          }

          // --- Populate patient dropdown ---
          let patientSelect = $('#patient_id');
          let currentPatient = patientSelect.val() || 'All';
          patientSelect.empty().append('<option value="All">All</option>');

          data.patients.forEach(item => {
            // If doctor is selected, show only patients of that doctor
            if (doctorId === 'All' || item.doctor_id === doctorId) {
            let selected = (item.id == currentPatient) ? 'selected' : '';
            patientSelect.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
        }
          });
        },
        error: function(xhr, status, error) {
          console.error("Error fetching doctor/patient data:", error);
        }
      });
    }

    // --- Initial load ---
    fetchPatientDoctor('All', 'All', true);

    // --- Doctor change ---
    $('#doctor_id').on('change', function() {
      let doctorId = $(this).val();
      // Reset patient to All and fetch linked patients
      $('#patient_id').val('All');
      fetchPatientDoctor(doctorId, 'All', false);
    });

    // --- Patient change ---
    $('#patient_id').on('change', function() {
      let patientId = $(this).val();
      let doctorId = $('#doctor_id').val();
      fetchPatientDoctor(doctorId, patientId, false);
    });

    // --- Initial load ---
    fetchPatientDoctor(); // populate dropdowns
    loadStats(); // populate stats and chart
  });
</script>