function usernameAvailability(usernameInput){
    $.ajax({
     method:"POST",
     url: "ajax/department/GetSameName.php",
     data:{username:usernameInput},
     success: function(data){
       $('#departmentNameResult').html(data);
     }
   });
}


$(document).on('input','#departmentName',function(e){

    let usernameInput = $('#departmentName').val();
    let msg;
    if(usernameInput.length==0){
      msg="<span style='color:red'>Enter Department Name</span>";
    }
    else if((/^$ | \s+/).test(usernameInput))
    {
     msg="<span style='color:red'>username can't contain spaces</span>";
    }
    else if(usernameInput.length!=0 && (usernameInput.length <1 || usernameInput.length >20)){
      msg="<span style='color:red'>username must be between 1-20</span>";
    }else{
      usernameAvailability(usernameInput);
    }
    $('#departmentNameResult').html(msg);
});