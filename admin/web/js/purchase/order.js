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
    
    // if($('#purchaseheader-vat_percent').val() >= 7) // Vat
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
                $('#purchaseheader-discount'),
                $('#ew-after-discount'),
                $('#ew-before-vat'),
                $('#ew-after-vat'),
                $('#ew-total'),
                $('#purchaseheader-vat_percent'),
                $('#purchaseheader-vat_type')
              );
  }

  // Close Modal
  $('body').on('click','button[data-dismiss="modal"]',function(){
    history.pushState(null, null, 'index.php?r=Purchase/order/update&id='+$('form#form-purchase-order').data('key'));
  });

	$('body').on('click','input.PICK-SERIES,.PICK-SERIES i',function(){
    //ShowSeriesList($(this),{table:'purchase_header',field:'vendor_type'});
    ShowSeriesList($(this),{table:'purchase_header',field:'vatbus_posting_group'});
	})

  $('body').on('click','div.vendorFilter span.input-group-addon',function(){
	  ShowVendorList($(this),'purchase_header','');
	})

  $('body').on('click','input.VENDOR-PICKER',function(){
    $(this).select();
    if($(this).val()=='' || $(this).val()!=='เงินสด'){
      ShowVendorList($(this),'purchase_header','');
    }
    // if($(this).val()!=''){
    //    ShowVendorList($(this),'purchase_header',$(this).val());
    // }



	})


  PickSeriesToText('#purchaseheader',['doc_no','series_id']);

  customSeriesPopUp('span.edit-Runing-Series');

  PickVendor('#purchaseheader',
      [
        'vendor_id',
        'vendor_name',
        'vendor_address',
      ]
  );


  $('body').on('keyup','input#purchaseheader-vendor_name',function(){
      ajaxFindVendor($(this),VENDOR_SECTION);
  })

  $('body').keydown(function(event) {

    var keyCode = event.keyCode || event.which;
    //if ((keyCode === 13) && ($(event.target)[0]!=$("textarea")[0]) && ($(event.target)[0]!=$("textarea")[1])) {  // Enter
    if (keyCode === 13) {  // Enter
      event.preventDefault();
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
                route('index.php?r=Purchase/order/ajax-delete-pur-line','POST',data,'Navi-Title');

                // SumTotalTable(
                //     $('#ew-purline-total'),
                //     $('#purchaseheader-discount'),
                //     $('#ew-after-discount'),
                //     $('#ew-before-vat'),
                //     $('#ew-after-vat'),
                //     $('#ew-total'),
                //     $('#purchaseheader-vat_percent'),
                //     $('#purchaseheader-vat_type')
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

    var percent_disc  = Number($('#purchaseheader-percent_discount').val());
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
  $('body').on('change keyup','input[id="purchaseheader-percent_discount"]',function(){

        // var percent_disc = $(this).val();
        // var subtotal = Number($('#ew-purline-total').attr('data'));
        // var discount = (subtotal * percent_disc)/ 100;
        //
        // $('input[id="purchaseheader-discount"]').val(discount);
        //
        // SumTotalTable(
        //           $('#ew-purline-total'),
        //           $('#purchaseheader-discount'),
        //           $('#ew-after-discount'),
        //           $('#ew-before-vat'),
        //           $('#ew-after-vat'),
        //           $('#ew-total'),
        //           $('#purchaseheader-vat_percent'),
        //           $('#purchaseheader-vat_type')
        //         );

    });


      


function vatEvent(e)
{
   if(e.val() > 0)
   {
      $('#purchaseheader-vat_type').fadeIn('in');


   }else {
      $('#purchaseheader-vat_type').fadeOut();
      $('#purchaseheader-vat_type').val(1);
   }

   $('#ew-text-percent-vat').text(e.val());
}
//---- /.Vat type Event Change -----




  ///////////////////////////\/Update Line//////////////////////////////
  $('body').on('focusin','.field-update', function(){    
    $(this).attr('data-pre', $(this).val());
  });


    $('body').on('change','.field-update',function(e){

      var that     = $(this);


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
        data:that,
        form:'form#form-purchase-order',
        time:0
      });


      // SumTotalTable(
      //               $('#ew-purline-total'),
      //               $('#purchaseheader-discount'),
      //               $('#ew-after-discount'),
      //               $('#ew-before-vat'),
      //               $('#ew-after-vat'),
      //               $('#ew-total'),
      //               $('#purchaseheader-vat_percent'),
      //               $('#purchaseheader-vat_type')
      //             );

    });

    //////////////////////////// /\.Update Line//////////////////////////////


    
