var delayIdVendor=null; /* ต้องอยู่นอก function (จะทำงานครั้งเดียว)*/

// window.onbeforeunload = function() {
//   window.location.href = "index.php?r=Purchase/order/update&id=1";
//     return "You're about to end your session, are you sure?";
// }
$(document).click(function(e) {
  // check that your clicked
  // element has no id=pick-vendor-render
  if($(e.target).closest('ul').attr('class') != 'vendor-list') {
    $(".pick-vendor").fadeOut('fast');
  }


});
function loadVendorBox($div){
  $('ew.pick-vendor-box').remove();
  // ----- find-price -----
  var $findDiv =  '<ew class="pick-vendor-box">'+
                  '<div class="pick-vendor" >'+
                  '   <div class="pick-vendor-render" > </div>'+
                  '   <div class="pick-load">'+
                  '       <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-info"></i>'+
                  '       <span class="sr-only">Loading...</span>'+
                  '   </div>'+
                  '</div>'+
                  '</ew>';
  $($div).closest('div').append($findDiv);
}



function ajaxFindVendor($this,$div){

  var data = $($this).val();

  //****Delay 0.5 Sec****
  if(delayIdVendor){ clearTimeout(delayIdVendor);}
    delayIdVendor=setTimeout(function(){
      // ### Start
      //console.log(data);
      loadVendorBox($div);
      ShowVendorList($this,'purchase_header',data);
    delayIdVendor=null;
  },500);
  //****/. Delay 0.5 Sec****

}



function ShowVendorList($this,$module,$cond){

    $.ajax({
        url:"index.php?r=vendors/vendors/ajax-find-vendor",
        type:'POST',
        data:{module:$module,cond:$cond},
        async:true,
        dataType: 'json',
        success:function(getData){
            $('.pick-vendor-render').html(getData.html);

            setTimeout(function(e){
                $('.pick-vendor').slideDown('fast');
                $('.pick-vendor .pick-load').fadeOut();
            },100);
        }
    })

}



function PickVendor(source,$obj){

	$('body').on('click','.vendor-list .selected',function(){
    getVendorInfo($(this).attr('key'),function(obj){
      $(source+'-'+$obj[0]).val(obj.id);
  		$(source+'-'+$obj[1]).val(obj.name);
      $(source+'-address').val(obj.address);
      $(source+'-phone').val(obj.phone);
      $(source+'-fax').val(obj.fax);
      $(source+'-email').val(obj.email);
      $(source+'-contact').val(obj.contact);
      $(source+'-vat_regis').val(obj.vat_regis);
      $(source+'-branch_name').val(obj.branch_name);
      $(source+'-payment_term').val(obj.payment_term);
      console.log(obj.payment_term);
    });
		$('.pick-vendor').slideUp('fast');
	})

}


function getVendorInfo($id,handleData){
  $.ajax({
    url:'index.php?r=vendors/vendors/ajax-get-vendor-info',
    type:'POST',
    data:{id:$id},
    success:function(obj){
      handleData(jQuery.parseJSON(obj));
    },
    error:function(){
      alert('Error');
    }
  });
}

function renderModal($this){
  $('div#searchVendorModal').remove();
  var $modal = '<div class="modal  fade" id="searchVendorModal" data-keyboard="false" data-backdrop="static">'+
                '<div class="modal-dialog modal-lg">'+
                  '<div class="modal-content">'+
                    '<div class="modal-header bg-green-ew">'+
                      '<button type="button" class="close" data-dismiss="modal">&times;</button>'+
                      '<h4 class="modal-title"><i class="fa fa-address-book-o" aria-hidden="true"></i> ค้นหาข้อมูลรายละเอียดบริษัท จากฐานข้อมูลของกรมสรรพากร</h4>'+
                    '</div>'+
                    '<div class="modal-body">'+
                      '<p>Loading...</p>'+
                    '</div>'+
                    '<div class="modal-footer">'+
                      '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>'+
                    '</div>'+
                  '</div>'+

                '</div>'+
              '</div>';

  $($this).closest('body').prepend($modal);

  $('#searchVendorModal').modal('show');
}


$('body').on('click','span#searchVendorList',function(){
  renderModal($(this));
  $.ajax({
      url:"index.php?r=vendors/vendors/ajax-find-customer",
      type:'POST',
      data:{module:'',cond:$('#purchaseheader-vendor_name').val()},
      async:true,
      success:function(getData){
          //$(document).pjax('a', '#grid-pjax'); // จะทำให้ Page ทำงาน
          $('div#searchVendorModal .modal-body').html(getData);
          $('form#companySearch input.submit-search').val($('#purchaseheader-vendor_name').val()).focus();
          //$.pjax.reload({container:'#grid-pjax',url: "index.php?r=vendors/vendors/ajax-find-customer&page=5&per-page=10"});
      }
  })
});

$('body').on('keydown','form#companySearch input.submit-search',function(event){
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {  // Enter
    $('form#companySearch').submit();
  }
});



$('body').on('click','tr.getCompany',function(){
  console.log($(this).data('key'));
  $('#searchVendorModal').modal('hide');
  pickCompany($(this).data('key'));
});

function pickCompany($id){
  $.ajax({
    url:'index.php?r=customers/ajax/json-get-customer&id='+$id,
    type:'GET',
    data:{id:$id},
    success:function(getData){

      //console.log(obj);
      var obj = jQuery.parseJSON(getData);

      $('#purchaseheader-vendor_id').val(0);
      $('#purchaseheader-vendor_name').val(obj.name);
      $('#purchaseheader-address').val(obj.fulladdress);
      $('#purchaseheader-phone').val(obj.phone);
      $('#purchaseheader-fax').val(obj.fax);
      $('#purchaseheader-email').val(obj.email);
      $('#purchaseheader-contact').val(obj.contact);
      //$('#purchaseheader-branch').val(obj.branch);
      $('#purchaseheader-branch_name').val(obj.branch_name);
      $('#purchaseheader-taxid').val(obj.vatregis);
      $('#purchaseheader-refer_id').val($id);
      $('#purchaseheader-refer_name').val('customer');

      history.pushState(null, null, 'index.php?r=Purchase/order/update&id='+$('form#form-purchase-order').data('key'));

    },
    error:function(){
      alert('Error');
    }
  });
}
