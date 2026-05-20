function getToken(){
  TokenElem = document.getElementById("device_token");
  // Initialize Firebase
  // TODO: Replace with your project's customized code snippet
  var config = {
    apiKey: "AIzaSyChhhxJbAT3GGPD_mY__GZ_HQjAk-8Ji3I",
    authDomain: "traklyt.firebaseapp.com",
    projectId: "traklyt",
    storageBucket: "traklyt.appspot.com",
    messagingSenderId: "1062990845113",
    appId: "1:1062990845113:web:4bc19cfa53bbbf3bd13768",
  };
  firebase.initializeApp(config);

  const messaging = firebase.messaging();
  messaging
      .requestPermission()
      .then(function () {
          console.log("Notification permission granted.");

          // get the token in the form of promise
          return messaging.getToken()
      })
      .then(function(token) {
          TokenElem.value = token;
      })
      .catch(function (err) {
          console.log("Unable to get permission to notify.", err);
      });
      messaging.onMessage(function(payload){
      console.log('Payload'+JSON.stringify(payload));
      const notificationOption={
      body:payload.data.body,
      icon:payload.data.icon
      };
      if(Notification.permission==='granted'){
        var notification = new Notification(payload.data.title,notificationOption);
        notification.onclick=function(ev){
      ev.preventDefault();
      //window.open(payload.notification.click_action,'_blank');
      notification.close();
        }
      }
        })
  }

function update_asset(asset_id,asset_name,asset_ref_no,asset_type_id,purchase_date_time,expiry_time,item_name){
    document.getElementById('asset_id').value = asset_id;
    // document.getElementById('asset_name').value = asset_name;
    document.getElementById('hideDiv').style.display="none";
    document.getElementById('no_of_assets').disabled=true;
    document.getElementById('asset_type_id').disabled=true;
    document.getElementById('asset_type_id').value = asset_type_id;
    document.getElementById('purchase_date_time').value = purchase_date_time;
    document.getElementById('expiry_time').value = expiry_time;
    document.getElementById('item_name').value = item_name;
    document.getElementById('from_source_id').value="2";
}

function delete_asset(asset_id){
  swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this record!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      document.getElementById('asset_id').value = asset_id;
      // document.getElementById('asset_name').value = "";
      document.getElementById('asset_type_id').value = "";
      document.getElementById('purchase_date_time').value = "";
      document.getElementById('expiry_time').value = "";
      document.getElementById('from_source_id').value="3";
      document.getElementById("FormId").submit();

    }
  });

}


function delete_roles(role_id){
  swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this record!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      document.getElementById('role_id').value = role_id;
      document.getElementById('role_name').value = "";
      document.getElementById('from_source_id').value="3";
      document.getElementById("FormId").submit();

    }
  });

}


$('#select_all').click(function(){
      
  if($(this).prop("checked") == true){
    $('.select2-selection__choice').hide();
    $('#to_id').val('');
    $('#to_id').prop('disabled', 'disabled');
  }
  else if($(this).prop("checked") == false){
    $('#to_id').prop('disabled', false);
  }
});

$('#send_msg').click(function(){
  $to_id = $('#to_id').val();
  $checkedBtn = $('#select_all').prop("checked");
  if( ($to_id=='' && $checkedBtn == true) || ($to_id!='' && $checkedBtn == false) ){
    document.getElementById("FormId").submit();
  }else{
    $('.select2-selection--multiple').css('border','solid black 1px');
    $('.invalid-feedback').show();
  }

});

// function changeStatus(transaction_damage_id,security_id,org_id){
//   var exception_status = $('#exception_status'+transaction_damage_id).val();
//   // alert(exception_status);
//   $.ajax({
//       url:"update_damage_exception",
//       type:"POST",
//       data:{'transaction_damage_id':transaction_damage_id,'security_id':security_id,"exception_status":exception_status,"org_id":org_id},
//       dataType:'json',
//       success:function(response){
//           // console.log(response.status);
//           if(response.status=='success'){
//               location.reload();
//           }
          
//       }
//   });
// }

function changePassword(){
  var confirm_password = document.getElementById('confirm_password').value;
  var new_password = document.getElementById('new_password').value;
  if(confirm_password == new_password && new_password!=''){
    return true;
  }else{
    $('.invalid-feedback').show();
    return false;
  }
}










