var VENDOR_SECTION  = '.VENDOR-PICKER';
var DELAY           = null;
 
$(document).ready(function(){
  $('a.ew-save-common').remove();
  $('a.btn-app-print').attr('style',' ');

  // Confirm Exit
  // $(window).on('beforeunload', function(){
  //     return 'Are you sure you want to leave?';
  // });
  $(document).on("submit", "form", function(event){
      $(window).off('beforeunload');
  });
  
  // if($('#purchasereqheader-vat_percent').val() >= 7) // Vat
  // {
  //     $('.show-vat-type').show('fast');

  // }else {
  //     $('.show-vat-type').hide('fast');
  // }


  //loadFindBox('.purchase-line-render');
  loadSeriesBox('.PICK-SERIES');
  loadVendorBox(VENDOR_SECTION);
  loadSummary();


});

function loadSummary(){
  SumTotalTable(
              $('#ew-purline-total'),
              $('#purchasereqheader-discount'),
              $('#ew-after-discount'),
              $('#ew-before-vat'),
              $('#ew-after-vat'),
              $('#ew-total'),
              $('#purchasereqheader-vat_percent'),
              $('#purchasereqheader-vat_type')
            );
}

// Close Modal
$('body').on('click','button[data-dismiss="modal"]',function(){
  history.pushState(null, null, 'index.php?r=Purchase/req/update&id='+$('form#form-purchase-order').data('key'));
});

  $('body').on('click','input.PICK-SERIES,.PICK-SERIES i',function(){
    ShowSeriesList($(this),{table:'purchase_req_header'});
  })

$('body').on('click','div.vendorFilter span.input-group-addon',function(){
    ShowVendorList($(this),'purchase_header','');
  })

$('body').on('click','input.VENDOR-PICKER',function(){
  if($(this).val()==''){
    ShowVendorList($(this),'purchase_header','');
  }
  // if($(this).val()!=''){
  //    ShowVendorList($(this),'purchase_header',$(this).val());
  // }



  })


PickSeriesToText('#purchasereqheader',['doc_no','series_id']);

customSeriesPopUp('span.edit-Runing-Series');

PickVendor('#purchasereqheader',
    [
      'vendor_id',
      'vendor_name',
      'vendor_address',
    ]
);


$('body').on('keyup','input#purchasereqheader-vendor_name',function(){
    ajaxFindVendor($(this),VENDOR_SECTION);
})

$('body').keydown(function(event) {

  var keyCode = event.keyCode || event.which;

  if ((keyCode === 13) && ($(event.target)[0]!=$("textarea")[0]) && ($(event.target)[0]!=$("textarea")[1])) {  // Enter
    event.preventDefault();
    return false;
  }
});


$('body').on('keydown','input#purchasereqheader-doc_no',function(e){
    var keyCode = e.keyCode || e.which;
    var form    = $('form#form-purchase-order');
  if ((keyCode === 13)) {  // Enter
   
    var str = $(this).val();

    var patt = new RegExp("\\?");
    var res = patt.test(str);

 
    if(res){
        
        $.ajax({
            url:"index.php?r=Purchase/req/find-auto&id="+form.attr('data-key')+"&code="+str.replace("\\?", ''),
            type: 'GET',
            dataType:'JSON',
            success:function(response){
                
                $('input#purchasereqheader-doc_no').val(response.data);
            }
        })
  
    }

    e.preventDefault();
    return false;
  }
});

 
 




  $('body').on('click','.ew-delete-purchase-line',function(){

    if (confirm('Do you want to delete  ?')) {

            var data = {data:$(this).attr('data'),pur:Number($('form#form-purchase-order').attr('data-key'))};

            var tr = $(this).closest('tr');
          tr.css("background-color","#aaf7ff");
          tr.fadeOut(500, function(){

              tr.remove();
              route('index.php?r=Purchase/req/ajax-delete-pur-line','POST',data,'Navi-Title');

              // SumTotalTable(
              //     $('#ew-purline-total'),
              //     $('#purchasereqheader-discount'),
              //     $('#ew-after-discount'),
              //     $('#ew-before-vat'),
              //     $('#ew-after-vat'),
              //     $('#ew-total'),
              //     $('#purchasereqheader-vat_percent'),
              //     $('#purchasereqheader-vat_type')
              //   );

                var index = 1;
                $($('td.move')).each(function(){
                  $(this).text(index);
                  index = ++index;
                });

          });

        }

        return false;

  });


function SumTotalTable($sumtotal,$discount,$afterdiscount,$beforevat,$aftervat,$grandtotal,$percentvat,$vattype)
{

  /*----- Sum Section -----   */
  var sum = 0;

  jQuery('.ew-line-total').each(function(){

    sum += parseFloat(jQuery(this).attr('data'));

  });


  var $BeforeDisc = sum;

  //var $Discount   = $discount.val();

  var percent_disc  = Number($('#purchasereqheader-percent_discount').val());
  var subtotal      = $BeforeDisc;

  var $Discount      = (subtotal * percent_disc)/ 100;



  // หักส่วนลด (ก่อน vat)
  var $subtotal   = $BeforeDisc - $Discount;
  var $vat        = Number($percentvat.val());




  if($vattype.val() == 1){

    // Vat นอก

    var $InCVat       = ($subtotal * $vat )/ 100;

    var $beforeVat    = 0;

    var $total        = ($InCVat + $subtotal);

    $beforevat.parent('tr').css("background-color","#aaf7ff");
    $beforevat.parent('tr').fadeOut(500);

  }else {

    // Vat ใน


    // 1.07 = 7%
    var $vat_revert   = ($vat/100) + 1;

    var $InCVat       = $subtotal - ($subtotal / $vat_revert);

    var $beforeVat    = $subtotal - $InCVat;

    var $total        = $subtotal;

    $beforevat.parent('tr').css("background-color","#FFF");
    $beforevat.parent('tr').fadeIn(500);
  }

  $sumtotal.text(number_format($BeforeDisc.toFixed(2))).fadeIn();

  $sumtotal.attr('data',$BeforeDisc);
  $('#totalHidden').val($BeforeDisc);

  $discount.val($Discount).fadeIn();

  $afterdiscount.text(number_format($subtotal.toFixed(2))).fadeIn();

  $beforevat.text(number_format($beforeVat.toFixed(2))).fadeIn();

  $aftervat.text(number_format($InCVat.toFixed(2))).fadeIn();

  $grandtotal.attr('data',$total).text(number_format($total.toFixed(2))).fadeIn();
  //$grandtotal.closest('div').append('<div></div>').text(number_format($total.toFixed(2)));
  $('#total-balance').attr('data',$total).val($total.toFixed(2)).fadeIn();




      /*----- /.Sum Section ----- */

}

//---- Vat type Event Change -----
$('body').on('change keyup','input[id="purchasereqheader-percent_discount"]',function(){

      // var percent_disc = $(this).val();
      // var subtotal = Number($('#ew-purline-total').attr('data'));
      // var discount = (subtotal * percent_disc)/ 100;
      //
      // $('input[id="purchasereqheader-discount"]').val(discount);
      //
      // SumTotalTable(
      //           $('#ew-purline-total'),
      //           $('#purchasereqheader-discount'),
      //           $('#ew-after-discount'),
      //           $('#ew-before-vat'),
      //           $('#ew-after-vat'),
      //           $('#ew-total'),
      //           $('#purchasereqheader-vat_percent'),
      //           $('#purchasereqheader-vat_type')
      //         );

  });


    


function vatEvent(e)
{
 if(e.val() > 0)
 {
    $('#purchasereqheader-vat_type').fadeIn('in');


 }else {
    $('#purchasereqheader-vat_type').fadeOut();
    $('#purchasereqheader-vat_type').val(1);
 }

 $('#ew-text-percent-vat').text(e.val());
}
//---- /.Vat type Event Change -----




///////////////////////////\/Update Line//////////////////////////////

  $('body').on('change','.field-update',function(event){


    var keyCode   = event.keyCode || event.which;
    var $this     = $(this);

    if (keyCode === 13) {
       var index = $('.ajax-update').index(this) + 1;
       $('.ajax-update').eq(index).focus().select();
    }

    //   var tr        = $(this).closest('tr');
    //
    //   var $qty      = tr.find('input[name="quantity"]').val().replace(/\,/g,'');
    //   var $price    = tr.find('input[name="unitcost"]').val().replace(/\,/g,'');
    //
    //   var $subtotal = $qty*$price;
    //
    //
    //   tr.find('div.ew-line-total').attr('data',$subtotal).html(number_format($subtotal.toFixed(2)));

    ajaxUpdateFieldActive({
      data:$this,
      form:'form#form-purchase-order',
      time:0
    });


    // SumTotalTable(
    //               $('#ew-purline-total'),
    //               $('#purchasereqheader-discount'),
    //               $('#ew-after-discount'),
    //               $('#ew-before-vat'),
    //               $('#ew-after-vat'),
    //               $('#ew-total'),
    //               $('#purchasereqheader-vat_percent'),
    //               $('#purchasereqheader-vat_type')
    //             );

  });

  //////////////////////////// /\.Update Line//////////////////////////////


  
