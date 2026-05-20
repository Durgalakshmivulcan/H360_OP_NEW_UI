<?php
require_once("ajax/header.php"); // config.php is already included
// FIX_B_920: SA-only — guard at page level (B-019 RBAC redirect is commented globally)
if (($_SESSION['security_id'] ?? '') != '1' || ($_SESSION['role_id'] ?? '') != '1') {
    header("Location: dashboard.php"); exit;
}
?>

<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Administration</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item active">Data Base</li>
            <li class="breadcrumb-item active">All Tables</li>
        </ul>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>All Table Names</h4>
                <!-- ✅ Button to open SQL query prompt -->
                <button class="btn btn-primary btn-sm" onclick="runQuery()">
                    <i class="fas fa-terminal"></i> Run SQL Query
                </button>
            </div>

            <div class="card-body">
                <?php
                $result = mysqli_query($conn, "SHOW TABLES FROM `$database`");

                if (!$result) {
                    echo "<div class='alert alert-danger'>Error fetching tables: " . mysqli_error($conn) . "</div>";
                } else {
                    echo "<div class='table-responsive custom-table-wrapper'>";
                    echo "<table class='table table-bordered'>";
                    echo "<thead><tr><th>#</th><th>Table Name</th><th>Action</th></tr></thead><tbody>";

                    $count = 1;
                    while ($row = mysqli_fetch_row($result)) {
                        $tableName = $row[0];
                        echo "<tr>";
                        echo "<td>{$count}</td>";
                        echo "<td>{$tableName}</td>";
                        echo "<td>
                                <button class='btn btn-danger btn-sm' onclick='truncateTable(\"$tableName\")'>
                                    Truncate
                                </button>
                              </td>";
                        echo "</tr>";
                        $count++;
                    }

                    echo "</tbody></table>";
                    echo "</div>";
                }
                ?>
                <!-- ✅ Container to show SQL query results -->
                <div id="queryResult" class="mt-4"></div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php require_once("ajax/footer.php"); ?>

<script>
// ✅ Run any SQL query
function runQuery() {
    Swal.fire({
        title: 'Execute SQL Query',
        input: 'textarea',
        inputPlaceholder: 'Enter your SQL query here...',
        inputAttributes: { rows: 6 },
        showCancelButton: true,
        confirmButtonText: 'Run Query',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed && result.value.trim() !== '') {
            $.ajax({
                url: "ajax/Database/run_query.php",
                type: "POST",
                dataType: "json",
                data: { query: result.value },
                success: function(response) {
                    if (response.status === "success") {
                        if (response.hasOwnProperty("table")) {
                            // Display SELECT results
                            $("#queryResult").html(response.table);
                        } else {
                            Swal.fire({
                                icon: "success",
                                title: "Query Executed",
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Query Error",
                            html: `<pre style='text-align:left'>${response.message}</pre>`
                        });
                    }
                },
                error: function() {
                    Swal.fire("Error", "Server error while executing query.", "error");
                }
            });
        }
    });
}

// ✅ Truncate Table Function
function truncateTable(tableName) {
    Swal.fire({
        title: `Are you sure?`,
        text: `This will permanently delete all data from table "${tableName}".`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, truncate it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "ajax/Database/truncate_table.php",
                type: 'POST',
                dataType: 'json',
                data: { table_name: tableName },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Truncated!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Server error while truncating table.'
                    });
                }
            });
        }
    });
}
</script>
