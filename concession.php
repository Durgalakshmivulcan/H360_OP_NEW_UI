<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810
    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

?>
<style>
    .btn-group,
    .btn-group-vertical {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        margin-top: 20px;
    }
</style>

<div class="main-content">
    <ul class="breadcrumb breadcrumb-style ">
        <li class="breadcrumb-item">
            <h4 class="page-title m-b-0">Concession</h4>
        </li>
        <li class="breadcrumb-item">
            <a href="dashboard.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </a>
        </li>
        <li class="breadcrumb-item">Master</li>
        <li class="breadcrumb-item">Add & Modify Concessions</li>
    </ul>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Concessions</h4>
                </div>
                <div class="card-body">
                    <form id="concessionForm">
                        <input type="hidden" id="RoleId" name="RoleId" value="<?= $SessionUserId?>">
                        <input type="hidden" id="concession_id" name="concession_id" value="">

                        <?php 
                            if($SessionUserId == "1" && $SessionRoleId=="1"){
                        ?>
                        <div class = row>
                            <div class="form-group col-lg-3 col-sm-12">
                                <label for="organizations" class="Organization">Organization <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-buildings-fill"></i>
                                    </span>
                                    <select class="form-control form-select" name="organizations" id="organizations">
                                        <option value="">Select Organization</option>
                                        <?php
                                        $GetOrganization = mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                        while($ResOrganization = mysqli_fetch_object($GetOrganization)){
                                        ?>
                                            <option value="<?= $ResOrganization->org_id ?>"><?= $ResOrganization->organization_name ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                            } else {
                            ?>
                            <input type="hidden" name="organizations" id="organizations" value="<?= $SessionOrgId ?>" />
                            <?php 
                            }
                        ?> 
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="concessionName">Concession Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-card-text"></i>
                                    </span>
                                    <input type="text" id="concessionName" name="concessionName" class="form-control"
                                        placeholder="Concession name" required>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="concessionType">Concession Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </span>
                                    <select id="concessionType" name="concessionType" class="form-control form-select" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="percentage">Percentage</option>
                                        <option value="amount">Amount</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="concessionValue" id="valueLabel">Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-cash-coin"></i>
                                    </span>
                                    <input type="number" id="concessionValue" name="concessionValue" class="form-control"
                                        placeholder="Enter value" required>
                                </div>
                            </div>

                        </div>
                        <div class="text-center">
                            <button type="reset" class="btn btn-secondary">Reset</button>
                            <?php if (userCan('add', 'concession.php') || userCan('edit', 'concession.php')) { /* FIX_B_1810 */ ?><button type="submit" class="btn btn-primary">Save Concession</button><?php } ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Concessions List</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="concessionTable">
                                <div id="tableExport_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php include 'ajax/footer.php'; ?>

<script>
    $(document).ready(function () {
        loadConcessions();
    });

    $('#concessionType').on('change', function () {
        let type = $(this).val();
        let $valueInput = $('#concessionValue'); 

        if (type === "percentage") {
            $('#valueLabel').text("Percentage Value (%)");
            $valueInput.attr("type", "number");
            $valueInput.attr("placeholder", "Enter percentage (e.g., 10 for 10%)");
            $valueInput.attr("max", 100); 
            $valueInput.val('');
        } else if (type === "amount") {
            $('#valueLabel').text("Amount Value (₹)");
            $valueInput.attr("type", "number");
            $valueInput.attr("placeholder", "Enter amount (e.g., 500)");
            $valueInput.removeAttr("max"); 
            $valueInput.val('');
        } else {
            $('#valueLabel').text("Value");
            $valueInput.attr("type", "number");
            $valueInput.attr("placeholder", "Enter value");
            $valueInput.removeAttr("max"); 
            $valueInput.val('');
        }
    });

    // Extra validation: prevent values > 100 for percentage
    $('#concessionValue').on('input', function () {
        if ($('#concessionType').val() === "percentage") {
            let val = parseFloat($(this).val());
            if (val > 100) {
                $(this).val(100);
            }
            if (val < 0) {
                $(this).val(0);
            }
        }
    });


    $('#concessionForm').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        $.ajax({
            url: "ajax/Concession/SaveConcession.php",
            type: "POST",
            data: formData,
            success: function (response) {
            console.log(response);
                switch (response) {
                    case '1':
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Concession saved successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#concessionForm')[0].reset();
                        $('#concessionType').val('');         
                        $('#valueLabel').text('Value');       
                        $('#concessionValue').attr('placeholder', 'Enter value'); 
                        $('#concessionValue').removeAttr('max');
                        loadConcessions();
                        break;

                    case '2':
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Concession updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#concessionForm')[0].reset();
                        $('#concessionType').val('');         
                        $('#valueLabel').text('Value');       
                        $('#concessionValue').attr('placeholder', 'Enter value'); 
                        $('#concessionValue').removeAttr('max');
                        loadConcessions();
                        break;

                    case '3':
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate',
                            text: 'This concession already exists'
                        });
                        break;

                    default:
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong!'
                        });
                        break;
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong!'
                });
            }
        });
    });

    function loadConcessions() {
        var org_id = '<?= $SessionOrgId ?>';

        $.ajax({
            url: 'ajax/Concession/GetConcessions.php',
            type: 'GET',
            success: function(response) {
                if(response) {
                    $("#concessionTable").html(response);
                    document.getElementById("concessionForm").reset();
                    var buttons_array =  [0, 1, 2, 3]; 
                    if(org_id == "0"){
                        buttons_array = [0, 1, 2, 3, 4];
                    }
                    $("#concessionid").dataTable({
                        retrieve: true,
                        dom: 'lBrftip',
                        buttons: [
                                    {
                                        extend: 'copy',
                                        exportOptions: {
                                        columns: buttons_array
                                        },
                                    },
                                    {
                                        extend: 'excel',
                                        exportOptions: {
                                        columns: buttons_array
                                        },
                                    },
                                    {
                                        extend: 'csv',
                                        exportOptions: {
                                        columns: buttons_array
                                        },
                                    },
                                    {
                                        extend: 'pdf',
                                        exportOptions: {
                                        columns: buttons_array
                                        },
                                    },
                                    {
                                        extend: 'print',
                                        exportOptions: {
                                        columns: buttons_array
                                        },
                                    },
                                ],
                    });
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function validateConcessionValue() {
        let type = $('#concessionType').val();
        let valueInput = $('#concessionValue');
        let value = parseFloat(valueInput.val());

        // Reset border first
        valueInput.removeClass('is-invalid');

        if (type === "percentage") {
            value = Math.floor(value);
            if (value > 100) {
                valueInput.addClass('is-invalid');
                valueInput.val(100);
            } else if (value < 0) {
                valueInput.addClass('is-invalid');
                valueInput.val(0);
            } else {
                valueInput.val(value); // Ensure integer display
            }
        }
    }

    function editConcession(concessionId, concession_name, concession_type, concession_value) {
        window.scrollTo(0,0);
        $('#concessionForm')[0].reset();
        $('#concession_id').val(concessionId);
        $('#concessionName').val(concession_name);
        $('#concessionType').val(concession_type).trigger('change');
        $('#concessionValue').val(concession_value).trigger('change');

    }

    function deleteConcession(concessionId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/Concession/DeleteConcession.php', // Create this PHP file
                    type: 'POST',
                    data: { concession_id: concessionId },
                    success: function(response) {
                        if(response == '1') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Concession has been deleted.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadConcessions(); // Refresh table
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not delete concession!'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Something went wrong!'
                        });
                    }
                });
            }
        });
    }

</script>

