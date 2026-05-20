<?php
require_once("ajax/header.php");

?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Menus</h4>
            </li>
            <li class="breadcrumb-item active">Organigation Reports</li>
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
                    <h4>Organigation Reports</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" >
                    <input type="hidden" name="id" id="id" value="" >
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12">
                                <label for="menu_name"> DATE AND TIME <span class="text-danger">*</span> </label>
                                <input type="text" name="daterange" value="-----/-----" />
                            </div>
                            

                        </div>
                    </div>

                    <div class="card-footer text-center">
                        <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Total Biling - All Departments</h4>
                </div>

                <div class="card-body">
                    <div class="col-12 col-md-12 table-responsive">
                        <table class="table" id="tableExport1" style="width:100%;">
                            <thead class="text-center">
                                <tr>
                                    <th> S.No </th>
                                    <th> Total billed </th>
                                    <!-- <th> total collected </th> -->
                                    <th> Card </th>
                                    <th> Unq.Billed Patients </th>
                                    <!-- <th> Experience </th> -->
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
        Getprice();
        Getdepart();
    });

    function Getprice() {
        $.ajax({
            url: 'ajax/billing/getprice.php',
            type: 'GET',
            success: function(data) {
                if(data) {
                    $("#showMenusData").html(data);
                    document.getElementById("FormId").reset();
                    
                    // FIX_B_701: DataTables modern API requires capital-D
                    // `DataTable()` (returns API instance with `.destroy()`).
                    // Legacy lowercase `dataTable()` returns the jQuery object,
                    // so calling `.destroy()` on it throws
                    // "destroy is not a function" at boot.
                    if ($.fn.DataTable.isDataTable("#tableExport1")) {
                        $("#tableExport1").DataTable().destroy();
                    }
                    $("#tableExport1").DataTable({
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






    function Getdepart() {
        $.ajax({
        url:"ajax/billing/getdepart.php",
        type:'post',
        dataType:'json',
        success:function(data){
            console.log(data);
          
        }
    });  
   }





    $("#FormId").submit(function() {
        // alert(1);
    
        event.preventDefault();
        var id = $("#id").val();
        var doctor_name = $("#doctor_name").val();
        var depart = $("#depart").val();
        var docter_id = $("#docter_id").val();
        var service_id = $("#service_id").val();
        var doc_num = $("#doc_num").val();
        var educate = $("#educate").val();
        var experi = $("#experi").val();
        var cun_ch = $("#cun_ch").val();
       
   
        if( doctor_name != "" && depart != "" && docter_id != ""  ) {
            // alert(2);
            $.ajax({
                url: 'ajax/doctors/adddoctor.php',
                type: 'POST',
                data: {
                    'id': id,
                    'doctor_name': doctor_name,
                    'depart': depart,
                    'docter_id': docter_id,
                    'service_id': service_id,
                    'doc_num': doc_num,
                    'educate': educate,
                    // 'experi': experi,
                    'cun_ch': cun_ch
                },
                success: function(data) {
                    // alert(3);
                    console.log(data);
                    // return;
                    if(data == 1) {
                        swal('', 'Doctor Added Successfully', 'success');
                        Getdoctors();
                    } else if(data == 2) {
                        swal('', 'Doctor Updated Successfully', 'success');
                        Getdoctors();
                    } else {
                        swal('','Error occured. Please try again', 'error')
                    }
                },
                error: function(err)  {
                    // alert(4);
                    console.log(err);
                }
            });
        }

    })

    function editdoctor(id, doctor_name,depart , docter_id, service_id, doc_num, educate, cun_ch) {
        $("#id").val(id);
        $("#doctor_name").val(doctor_name);
        $("#depart").val(depart);
        $("#docter_id").val(docter_id);
        $("#service_id").val(service_id);
        $("#doc_num").val(doc_num);
        $("#educate").val(educate);
        // $("#experi").val(experi);
        $("#cun_ch").val(cun_ch);
        alert(1);
    }

    function deletedoctor(id, doctor_name) {
        swal({
            title: "Are you sure?",
            text: "Do you Really want to Delete Doctor Details! ",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) =>{
            if (willDelete) {
                $.ajax({
                    url: 'ajax/doctors/deletedoctor.php',
                    type: 'POST',
                    data: {
                        'id': id
                    },
                    success: function(data){
                        if(data == 1) {
                            swal('', 'Deleted Successfully', 'success');
                            Getdoctors();
                        } else {
                            swal('','Error occured. Please try again', 'error')
                        }
                    },
                    error: function(err)  {
                        console.log(err);
                    }
                });

                $('#deleteID').val(id);
                swal('', doctor_name + ' Deleted Successfully', 'success').then((result) => {
                    $('#deleteFormId').submit();
                });
            }
        });
}






$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});


</script>
