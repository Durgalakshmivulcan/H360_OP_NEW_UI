<?php
require_once("ajax/header.php");
// FIX_B_1850: per-action RBAC gate. Only SA (role_id=1) maps menus.php by
// default; everyone else gets bounced. SA bypass preserved by userCan().
requireCan('view', basename(__FILE__));
?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Access control</h4>
            </li>
            <li class="breadcrumb-item active">Add/Modify Menus</li>
        </ul>
        
        <ul class="breadcrumb breadcrumb-style" >
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">
                    
                </div>
            </li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Menus</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data">
                    <input type="hidden" name="menu_id" id="menu_id" value="" >
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="menu_name"> Menu Name <span class="text-danger">*</span> </label>
                                <input class="form-control" name="menu_name" id="menu_name" value="" required>
                            </div>
                            
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="menu_type"> Menu Type <span class="text-danger">*</span> </label>
                                <select class="form-control form-select" name="menu_type" id="menu_type" required>
                                    <option value="p"> Parent </option>
                                    <option value="s"> Sub </option>
                                </select>
                            </div>
                            
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="menu_web_url"> Web URL </label>
                                <input type="text" class="form-control" name="menu_web_url" id="menu_web_url" value="" required>
                            </div>
                            
                            <div class="form-group col-lg-2 col-sm-12">
                                <label for="menu_order"> Menu Order <span class="text-danger">*</span> </label>
                                <input type="text" class="form-control" name="menu_order" id="menu_order" value="" required>
                            </div>
                            
                            <div class="form-group col-lg-2 col-sm-12">
                                <label for="web_class_name"> Has Sub Menus <span class="text-danger">*</span> </label>
                                <select class="form-control form-select" name="web_class_name" id="web_class_name" value="" >
                                    <option value="">No</option>
                                    <option value="menu-toggle has-dropdown">Yes</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-4 col-sm-12" id="parentMenuShow">
                                <label for="parent_id"> Parent Menu </label>
                                <select class="form-control form-select" name="parent_id" id="parent_id" >
                                    <option value=""> Select </option>
                                    <?php
                                        $getMenus = mysqli_query($conn, "SELECT menu_id, menu_name FROM menus WHERE status='1' AND menu_type='p' ORDER BY menu_id DESC") or die(mysqli_error($conn));
                                        while ($resMenus = mysqli_fetch_object($getMenus)) {
                                    ?>
                                        <option value="<?=$resMenus->menu_id?>"> <?=$resMenus->menu_name?> </option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="web_icon"> ICON </label>
                                <input type="text" class="form-control" name="web_icon" id="web_icon" value="" >
                            </div>

                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="menu_access">Permission Authority <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center mt-2">
                                    <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="menu_access" id="menu_allow" value="1" required>
                                    <label class="form-check-label" for="menu_allow">Allow</label>
                                    </div>
                                    <div class="form-check">
                                    <input class="form-check-input" type="radio" name="menu_access" id="menu_deny" value="0" checked required>
                                    <label class="form-check-label" for="menu_deny">Deny</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <?php /* FIX_B_1850: hide Submit if user has neither add nor edit on menus.php. */ ?>
                        <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))) { ?>
                        <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                        <?php } ?>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Menus List</h4>
                </div>

                <div class="card-body">
                    <div class="col-12 col-md-12 table-responsive">
                        <table class="table" id="tableExport1" style="width:100%;">
                            <thead class="text-center">
                                <tr>
                                    <th> S.No </th>
                                    <th> MENU NAME </th>
                                    <th> MENU Type </th>
                                    <th> Order </th>
                                    <th> URL </th>
                                    <th> Parent Menu </th>
                                    <th> Class Name </th>
                                    <th> Icon </th>
                                    <th> Permission Authority </th>
                                    <th> Created By </th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody id="showMenusData">
                            </tbody>                            
                        </table>
                        
                    </div>
                </div>
            </div>

        </div>
        

        <form action="" method="POST" id="deleteFormId">
            <input type="hidden" name="deleteID" id="deleteID" value="" />
        </form>

    </section>

</div>

<?php require_once("ajax/footer.php") ?>

<script>

    $("document").ready(function() {
        GetMenus();
    });

    function GetMenus() {
        $.ajax({
            url: 'ajax/Menus/GetMenus.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("FormId").reset();
                    
                    $("#tableExport1").DataTable().destroy(); // FIX_B_184
                    $("#tableExport1").dataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ]
                    });
                }
            },
            error: function(err)  {
                console.log(err);
            }
        });
    }
    $('#parentMenuShow').hide();
    $(document).on('change', '#menu_type', function(){
        var menu_type = $('#menu_type').val();
        
        if (menu_type == 's') {
            $('#parentMenuShow').show();  
        } else {
            $('#parent_id').val('');  
            $('#parentMenuShow').hide();  
        }
    });

    $("#FormId").submit(function(e) {
        var menu_id = $("#menu_id").val();
        var menu_name = $("#menu_name").val();
        var menu_type = $("#menu_type").val();
        var menu_order = $("#menu_order").val();
        var menu_web_url = $("#menu_web_url").val();
        var parent_id = $("#parent_id").val();
        var web_class_name = $("#web_class_name").val();
        var web_icon = $("#web_icon").val();
        var menu_access = $("input[name='menu_access']:checked").val();

        if(menu_name != "" && menu_type != "" && menu_order != "") {
            event.preventDefault();
            $.ajax({
                url: 'ajax/Menus/AddModifyMenus.php',
                type: 'POST',
                data: {
                    'menu_id': menu_id,
                    'menu_name': menu_name,
                    'menu_type': menu_type,
                    'menu_order': menu_order,
                    'menu_web_url': menu_web_url,
                    'parent_id': parent_id,
                    'web_class_name': web_class_name,
                    'web_icon': web_icon,
                    'menu_access': menu_access
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', 'Menu Added Successfully', 'success');
                        $('#parentMenuShow').hide();  
                        GetMenus();
                    } else if(data == 2) {
                        swal('', 'Menu Updated Successfully', 'success');
                        $('#parentMenuShow').hide();  
                        GetMenus();
                    } else {
                        swal('','Error occured. Please try again', 'error')
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });
        } else {
            e.preventDefault();
            swal('','Please enter data', 'error')
        }

    })

    function editMenus(menu_id, menu_name, menu_type, menu_order, menu_web_url, parent_id, web_class_name, web_icon, menu_access) {
        $("#menu_id").val(menu_id);
        $("#menu_name").val(menu_name);
        $("#menu_type").val(menu_type);
        if (menu_type == 's'){
            $('#parentMenuShow').show();   
        } else {
            $('#parentMenuShow').hide(); 
        }
        $("#menu_order").val(menu_order);
        $("#menu_web_url").val(menu_web_url);
        $("#parent_id").val(parent_id);
        $("#web_class_name").val(web_class_name);
        $("#web_icon").val(web_icon);
        $("#menu_access").val(menu_access);
    }

    function deleteMenus(menu_id, menu_name) {
        swal({
            title: "Are you sure?",
            text: "Do you wish to delete " + menu_name + " Menu!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: 'ajax/Menus/DeleteMenus.php',
                    type: 'POST',
                    data: {
                        'menu_id': menu_id
                    },
                    success: function(data) {
                        if(data == 1) {
                            // swal('', menu_name + ' Deleted Successfully', 'success');
                            GetMenus();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });
                
                $('#deleteID').val(menu_id);
                swal('', menu_name + ' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
    }
</script>