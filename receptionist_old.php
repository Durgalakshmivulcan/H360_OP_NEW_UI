<?php
include 'ajax/header.php';
?>
<style>
    .btn-group,
    .btn-group-vertical {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }

    .table-scroll {
        max-height: 400px;
        overflow-y: hidden;   
        overflow-x: hidden;
        transition: all 0.3s ease;
        scrollbar-gutter: stable;
    }

    .table-scroll:hover {
        overflow-y: auto;
        overflow-x: auto;
    }

    .table-scroll::-webkit-scrollbar {
        width: 6px;     
        height: 6px;       
    }

    .table-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;   
        border-radius: 10px;
    }

    .table-scroll::-webkit-scrollbar-thumb {
        background: #888;      
        border-radius: 10px;
    }

    .table-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;     
    }

    .search-box {
        position: relative;
        width: 200px; 
    }

    .search-box input {
        width: 100%;
        padding: 4px 30px 4px 10px; 
        border: 1px solid #ccc;
        border-radius: 20px; 
        outline: none;
    }

    .search-box i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px; 
        color: #555;
        cursor: pointer;
    }
</style>

<div class="main-content container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title">Receptionist Handling</h4>
        </div>
    </div>

    <div class="row align-items-stretch">
        <div class="col-md-5 d-flex flex-column">
            
            <div class="card text-dark shadow p-3 rounded-4 flex-fill mb-3" 
                style="background: linear-gradient(135deg, #bcecf7ff, #5c73a1ff); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-3">Total Patients</h5>
                        <h5 id="totalPatients">0</h5>
                    </div>
                    <i class="fa fa-hospital-user fa-3x text-primary"></i>
                </div>
            </div>

            <div class="card text-dark shadow p-3 rounded-4 flex-fill mb-3"
                style = "background: linear-gradient(135deg, #cdf1d6ff, #4f8f65); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-3">Next Patient</h5>
                        <h5 id="nextPatient">-</h5>
                    </div>
                    <i class="fa fa-user-md fa-3x text-primary"></i>
                </div>
            </div>

            <div class="card text-dark shadow p-3 rounded-4 flex-fill"
                style = "background: linear-gradient(135deg, #ffe0cc, #bdbb6b); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="w-100">
                        <h5 class="fw-bold mb-3 text-left">Patient Status</h5>
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <h6 class="text-dark fw-bold">Done</h6>
                                <span id="doneCount" class="fs-5 fw-bold">0</span>
                            </div>
                            <div>
                                <h6 class="text-dark fw-bold">Pending</h6>
                                <span id="pendingCount" class="fs-5 fw-bold">0</span>
                            </div>
                            <div>
                                <h6 class="text-dark fw-bold">Lapsed</h6>
                                <span id="missedCount" class="fs-5 fw-bold">0</span>
                            </div>
                        </div>
                    </div>
                    <i class="fa fa-notes-medical fa-3x ms-3 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-7 d-flex">
            <div class="card shadow rounded flex-fill">
                <div class="card-header text-black fw-bold d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Patient Queue</h5>
                    <div class="search-box">
                        <input type="text" placeholder="Search..." id="searchQueue">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="flex-fill table-scroll">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                <th>Patient Name</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="queueTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow rounded">
                <div class="card-header">
                    <h5 class="mb-0">Today’s Appointments</h5>
                </div>
                <div class="card-body" style="max-height: 100%;">
                    <table class="table table-bordered table-hover" id="todayAppointments">
                        <thead class="table-light">
                            <tr>
                                <th>Patient Name</th>
                                <th>Patient ID</th>
                                <th>Appointment ID</th>
                                <th>Appointment Time</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="todayAppointmentsTable">
                            <!-- Data populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    loadQueue();
    loadTodayAppointments();
});

document.getElementById("searchQueue").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#queueTable tr");

    rows.forEach(row => {
      let text = row.textContent.toLowerCase();
      row.style.display = text.includes(value) ? "" : "none";
    });
});

function loadQueue() {
    $.ajax({
        url: 'ajax/AppointmentBooking/GetAppointments.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let appointments = response.appointments;
            let counts = response.countData;

            let queueHtml = '';
            let nextPatient = '-';

            let queueData = appointments.filter(row => row.visitor_status == '1' || row.visitor_status == '2');

            queueData.forEach(row => {
                let statusBadge = '';
                let actionButtons = '';

                switch(row.visitor_status) {
                    case '1':
                        statusBadge = '<span class="badge bg-secondary">Pending</span>';
                        actionButtons = `
                            <button class="btn btn-sm btn-primary me-1" onclick="updateStatus(${row.appoint_id}, '2')">Start</button>
                            <button class="btn btn-sm btn-success me-1" onclick="updateStatus(${row.appoint_id}, '0')">Done</button>
                            <button class="btn btn-sm btn-danger" onclick="updateStatus(${row.appoint_id}, '3')">Lapsed</button>
                        `;
                        if (nextPatient == '-') nextPatient = row.patient_name;
                        break;

                    case '2':
                        statusBadge = '<span class="badge bg-success">Start</span>';
                        actionButtons = `
                            <button class="btn btn-sm btn-success" onclick="updateStatus(${row.appoint_id}, '0')">Done</button>
                        `;
                        if (nextPatient == '-') nextPatient = row.patient_name;
                        break;
                }

                queueHtml += `
                    <tr>
                        <td>${row.patient_name}</td>
                        <td>${row.start_time}</td>
                        <td>${statusBadge}</td>
                        <td>${actionButtons}</td>
                    </tr>`;
            });

            // Update UI
            $('#queueTable').html(queueHtml);
            $('#totalPatients').text(appointments.length);
            $('#nextPatient').text(nextPatient);
            $('#doneCount').text(counts['0']);
            $('#pendingCount').text(counts['1']);
            $('#missedCount').text(counts['3']);
        },
        error: function(err) {
            console.error(err);
        }
    });
}


function loadTodayAppointments() {
    $.ajax({
        url: 'ajax/AppointmentBooking/GetAppointments.php',
        type: 'GET',
        dataType: 'json',
        cache: false, // disable caching
        success: function(response) {
            let tableHtml = '';
            // console.log(response);

            if (response && response.appointments) {
                // Build rows
                response.appointments.forEach(appointment => {
                    let statusLabel = '';
                    let statusClass = '';

                    switch (appointment.visitor_status) {
                        case '1': statusLabel = 'Pending'; statusClass = 'bg-warning text-dark'; break;
                        case '2': statusLabel = 'Start'; statusClass = 'bg-primary text-white'; break;
                        case '0': statusLabel = 'Done'; statusClass = 'bg-success text-white'; break;
                        case '3': statusLabel = 'Lapsed'; statusClass = 'bg-danger text-white'; break;
                        default: statusLabel = 'Unknown'; statusClass = 'bg-secondary text-white';
                    }

                    let actionDropdown = `
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary" onclick="updateStatus(${appointment.appoint_id}, '2')">
                                <i class="bi bi-person me-1"></i> Start
                            </button>
                            <button class="btn btn-sm btn-success" onclick="updateStatus(${appointment.appoint_id}, '0')">
                                <i class="bi bi-check2-circle me-1"></i> Done
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="updateStatus(${appointment.appoint_id}, '3')">
                                <i class="bi bi-x-circle me-1"></i> Lapsed
                            </button>
                        </div>
                    `;

                  tableHtml += `
                        <tr>
                            <td>${appointment.patient_name}</td>

                            <!-- Clickable appoint_unicode column -->
                            <td>
                                <span class="clickable-col text-black"
                                    data-appoint-id="${appointment.appoint_id}" style="cursor:pointer;">
                                    ${appointment.appoint_unicode}
                                </span>
                            </td>

                            <!-- Clickable appoint_register_id column -->
                            <td>
                                <span class="clickable-col text-black"
                                    data-appoint-id="${appointment.appoint_id}" style="cursor:pointer;">
                                    ${appointment.appoint_register_id}
                                </span>
                            </td>

                            <td>${appointment.start_time}</td>
                            <td>${appointment.doctor_name || '-'}</td>
                            <td><span class="badge ${statusClass}">${statusLabel}</span></td>
                            <td>${actionDropdown}</td>
                        </tr>

                    `;
                });

                // Destroy DataTable if it exists
                if ($.fn.DataTable.isDataTable('#todayAppointments')) {
                    $('#todayAppointments').DataTable().clear().destroy();
                }

                // Update table body
                $('#todayAppointmentsTable').html(tableHtml);

                // Reinitialize DataTable
                $("#todayAppointments").DataTable({
                    retrieve: true,
                    dom: 'lBrftip',
                    buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
                });
            }
        },
        error: function(err) {
            console.error('Error loading today appointments:', err);
        }
    });
}

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('clickable-col')) {
        const appointId = e.target.getAttribute('data-appoint-id');
        window.location.href = `AllPatients.php?appoint_id=${appointId}`;
    }
});



function updateStatus(appoint_id, newStatus){
    $.ajax({
        url: 'ajax/AppointmentBooking/UpdateVisitorStatus.php',
        type: 'POST',
        data: { appoint_id: appoint_id, status: newStatus },
        dataType: 'json',
        success: function(response){
            if(response.success){
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                loadQueue();
                loadTodayAppointments();
            } else {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        },
        error: function(err){
            console.error('AJAX Error:', err);
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Server error while updating status.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    });
}



</script>

<?php include 'ajax/footer.php'; ?>
