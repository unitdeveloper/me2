
$(document).click(function(e){
  setTimeout(() => {
    $('body').find('.menu-widget').attr('style', 'background: #e6fdff !important;');
  }, 1500);
  
});


$("body").on("click", ".ew-btn-app-click", function(e) {
  BtnApprove(this);
});

$("body").on("click", ".ew-cancel-job", function(e) {
  BtnApprove(this);
});

function BtnApprove(e) {
  var text = $(e).attr("ew-data");
  var input =
    '<label for="reason-text">เหตุผล : </label><textarea class="form-control" id="reason-text" rows="3">ตรวจสอบรายการ</textarea>';
  var showText = ShowText(text);

  $("#ew-modal-Approve").modal("toggle");
  $("#ew-data-text").html(text);
  $("#ew-showText").html(showText);

  if (text === "Reject" || text === "Cancel" || text === "Checking") {
    $(".reject-reason").html(input);

    if (text === "Cancel") {
      $("#reason-text").text("สั่งผิด");
    }
  } else {
    $(".reject-reason").html("");
  }
}

function ShowText($text) {
  if ($text == "Checking") {
    return "ยืนยัน!";
  } else if ($text == "Confirm-Cancel") {
    return "อนุมัติคำขอยกเลิก";
  } else if ($text == "Confirm") {
    return "อนุมัติ";
  } else if ($text == "Reject") {
    return "ปฏิเสธ";
  }

  return "ยืนยัน";
}

$("body").one("click", ".ew-confirm", function(e) {
  Approve("ew-approve-body", $("#ew-data-text").text());
  $(".modal-footer").hide();
});

function Approve(div, type) {
  var appdata = {
    param: {
      apk: type,
      id: $("#SaleOrder").attr("ew-so-id"),
      cur: $("#SaleOrder").attr("ew-status"),
      reson: $("#reason-text").text()
    }
  };
  route("index.php?r=approval/approve/sale-order", "POST", appdata, div);
}



$('body').on('click','a.ew-delete-order',function(){
  var key = $(this).attr('data-id');
  setTimeout(function(){ 
      if (confirm(langText.confirmdel)) {                    
          $.ajax({
              url:"index.php?r=SaleOrders/saleorder/delete&id=" + key,
              type: 'POST',
              data:{id:key},
              success:function(respond){
                  var obj = jQuery.parseJSON(respond);
                  if(obj.status == 200){
                      // When delete
                      window.location = "?r=SaleOrders%2Fsaleorder"; 
                  }else {
                      //window.location.reload();
                      $.notify({
                          // options
                          message: langText.notAllow + ' = ' + obj.value.status 
                      },{
                          // settings
                          type: 'error',
                          delay: 5000,
                      });     
                  }
              }
          });            
      }else{
           
      }

  }, 200);
   
})
 