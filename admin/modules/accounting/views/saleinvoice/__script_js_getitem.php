 
<script>
$('#ewGetItemModal').on('keyup keypress', function(e) {

    var keyCode = e.keyCode || e.which;
    //alert(keyCode);
    // 91 = Win , Command
    // 116 = F5
    if (keyCode === 116) { 
      e.preventDefault();
      window.location = "index.php?r=accounting/saleinvoice/update&id="+$('.ew-inv-no').attr('ew-no');
      return false;
    }
  });

  
  $('body').on('keyup keypress','#ew-price', function(e) {

    var keyCode = e.keyCode || e.which;
    if (keyCode === 13)
    {
      createSaleInvoiceLine();
    } 
  });



  $(document).on('pjax:success', function() {
        LoadFunction();
  });


  function LoadFunction()
  {
    $('tr').each(function(i, el) {
        $(el).children('td').find('#ew-qty').fadeOut();
        $(el).children('td').find('#ew-price').fadeOut();
      });  
  }

	

    $('body').on('click','.ew-pick-item-modal',function(){
        $('#ewGetItemModal').modal('show'); 
        
        route("items/ajax/ajax-pick-items",'GET',{search:'',id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Inv-Item');

        //$('#ew-search-ship').attr('id','ew-search-cust');
        //$('#ew-search-ship-btn').attr('id','ew-search-cust-btn');
        
    });

    $('body').on('click','tr',function(){
    	var iCheck = $(this).children('td').find('input.ew-checked');
    	if (iCheck.is(':checked')) {
    	 	$(this).children('td').find('#ew-qty').fadeIn(600);
    	 	$(this).children('td').find('#ew-price').fadeIn(600);
    	}else {
    		$(this).children('td').find('#ew-qty').fadeOut(400);
    		$(this).children('td').find('#ew-price').fadeOut(400);
    	}
    });


    function createSaleInvoiceLine()
    {
      if($('.items:checked').serialize()!='')
      {

        var inv_no = $('.ew-inv-no').attr('ew-no');

        var data = {items:$('.items:checked').serializeArray(),id:inv_no};

          $('tr').each(function(i, el) {

            var iCheck = $(el).children('td').find('input.ew-checked');
            var item = $(el).children('td').find('input.ew-checked').val();
            var qty = $(el).children('td').find('#ew-qty').val();
            var price = $(el).children('td').find('#ew-price').val();


            if (iCheck.is(':checked')) {
              liveCreate({
                  item:item,
                  qty:qty,
                  price:price,
                  id:$('.ew-inv-no').attr('ew-no'),
                  no:$('.ew-inv-no').attr('ew-no_'),
                },$(el).children('td').children('div').children('div'));
            }

          });

        
          $('#ewGetItemModal').modal('hide');


        
      }else{
        
        swal(
          '<?=Yii::t('common','Please select one of the options.')?>',
          '<?=Yii::t('common','That thing is still around?')?>',
          'warning'
        );

        return false;
      }

    }


    $('body').on('click','.ew-pick-item-to-inv-line',function(){

      createSaleInvoiceLine();

    });
    $('body').on('click','input[name="ew-InsertAdd"]',function(e){

      
        liveCreate({
            item:$('.ew-InsertDesc').attr('ew-item-code'),
            desc:$('.ew-InsertDesc').val(),
            qty:$('.ew-qty').val(),
            price:$('.ew-price').val(),
            id:$('.ew-inv-no').attr('ew-no'),
            no:$('.ew-inv-no').attr('ew-no_'),
            type:$('select[name="InsertType"]').val(),
          },$('.Xtest'));
        
    });   

    $('body').on('keydown','.ew-price,.ew-qty',function(e){

        if (e.which == 13) {
            liveCreate({
                item:$('.ew-InsertDesc').attr('ew-item-code'),
                desc:$('.ew-InsertDesc').val(),
                qty:$('.ew-qty').val(),
                price:$('.ew-price').val(),
                id:$('.ew-inv-no').attr('ew-no'),
                no:$('.ew-inv-no').attr('ew-no_'),
                type:$('select[name="InsertType"]').val(),
              },$('.Xtest'));
        }
    });    

    $('body').on('keydown','.ew-InsertItems',function(e){
    

        
        if (e.which == 9 || e.which == 13) {

                if($('.ew-InsertDesc').attr('ew-item-code') != 'eWinl')
                    {
                         
                        $('.ew-InsertDesc').first().focus();
                    }

            var inputItem = $.trim($(this).val());  
            $(this).val(inputItem);  
             

             $.ajax({ 

                url:"index.php?r=SaleOrders/ajax/json-find-item",
                type: "POST", 
                data: {param:{item:inputItem}},
                async:false,
                success:function(getData){
                     
                    
                    var obj = jQuery.parseJSON(getData);
                     
                    $('.ew-desc').show();
                    $('.ew-InsertDesc').val(obj.desc);

                    $('.ew-InsertDesc').attr('ew-item-code',obj.item);

                    $('.ew-qty').show().val(1);
                   

                    $('.ew-price').show().val(obj.std);
                    

                    if(obj.code != 'eWinl')
                    {
                        $('.ew-add-to-inv-line').show();
                    }else {
                        $('.ew-add-to-inv-line').hide();
                    }
                    
                    
                }
            });

            //alert(inputItem); 
            //getSumLine($('#ew-discount-amount').val());
                 
        }
               

      
    });

    function liveCreate($data,element)
    {
    	$.ajax({
            url: "index.php?r=accounting/ajax/json-create-item-line",
            type: "post",
            data: $data,
            success: function(getData) {

            	//$(element).append($('<div id="dd"></div>'));

            	//$(element).children('div').html(getData);

              // ##-- ถ้ามี Empty ให้ลบ tr นี้ออกก่อน --
              $('#Sale_Invoice_Line div.empty').parent('td').parent('tr').remove();

               


              var obj = jQuery.parseJSON(getData);


              // ##-- หาจำนวนแถวถัดไป --
              var CountNumber = Number($( "#Sale_Invoice_Line tr" ).length); // แถวทั้งหมด (รวม header ,footer)
                
              CountNumber = CountNumber -1; // ลบจำนวนแถวออก 1 แถว จะได้ค่าที่ต้องการ

              // ##-- /.หาจำนวนแถวถัดไป--

             
              // ##-- สร้างบรรทัดใหม่ --
              var $row = $('<tr>'+
                '<td>'+CountNumber+'</td>'+
                '<td>'+obj.item+'</td>'+
                '<td>'+obj.desc+'</td>'+
                '<td align="right">'+Number(obj.qty)+'</td>'+
                '<td align="right">'+Number(obj.price)+'</td>'+
                '<td align="right"><div class="ew-line-total" data="'+obj.qty * obj.price+'">'+number_format((obj.qty * obj.price).toFixed(2))+'</div></td>'+
                '<td align="right"><div class="btn btn-danger ew-delete-inv-line" data="'+obj.id+'"><i class="fa fa-trash-o" aria-hidden="true"></i></div></td>'+
              '</tr>');  


              $('#Sale_Invoice_Line tbody:last').append($row);
              // ##-- /.สร้างบรรทัดใหม่ --


              // ##-- Refresh ข้อมูลตารางสรุป --
              liveRenderInvLine();

            }
        });


    }

    function liveRenderInvLine()
    {
    	
      //$.pjax.reload({container:".accController"}); //for pjax update  ## เกิด Dobule Action!!! (ต้องแก้ไข)

      

    	//route('accounting/ajax/render-inv-line','POST',{id:$('.ew-inv-no').attr('ew-no')},'sale-invlice-line-render');
    	// $.ajax({
     //        url: "index.php?r=accounting/ajax/render-inv-line",
     //        type: "POST",
     //        data: {id:$('.ew-inv-no').attr('ew-no')},
     //        success: function(getData) {

     //          $.pjax.reload({container:"#form-sale-invoice"}); //for pjax update

     //        }
     //    });

      
    	SumTotalTable(
	            		$('#ew-invline-total'),
	            		$('#saleinvoiceheader-discount'),
	            		$('#ew-after-discount'),
	            		$('#ew-before-vat'),
	            		$('#ew-after-vat'),
	            		$('#ew-total'),
	            		$('#saleinvoiceheader-vat_percent'),
	            		$('#saleinvoiceheader-include_vat')
	            	);
    }



    //------ Select Item ---------
    $('body').on('change','#ew-search-items-text',function(){ 
         
        route("items/ajax/ajax-pick-items",'POST',{search:$('#ew-search-items-text').val(),id:$('.ew-inv-no').attr('ew-no')},'ew-Pick-Inv-Item');
        LoadFunction();
    });

    
    $('body').on('click','#ew-search-items-btn',function(){ 

      route("items/ajax/ajax-pick-items",'POST',{search:$('#ew-search-items-text').val(),id:$('.ew-inv-no').attr('ew-no')},'ew-Pick-Inv-Item');
      LoadFunction();
      //$('a[id="ew-pick-customer"]').attr('id','ew-pick-inv-customer');
    });


    $('body').on('click','.ew-inv-close-pic-item',function(){
    	$('#ewGetItemModal').modal('hide'); 
      window.location.hash = "index.php?r=accounting/saleinvoice/update&id="+$('.ew-inv-no').attr('ew-no');
    });
    //------/. Select Item -------



	$('body').on('click','.ew-delete-inv-line',function(){

		if (confirm('Do you want to delete  ?')) {  

            var data = {data:$(this).attr('data'),inv:Number($('div.ew-inv-no').attr('ew-no'))};

            var tr = $(this).closest('tr');
	        tr.css("background-color","#aaf7ff");
	        tr.fadeOut(500, function(){
	            tr.remove();
	            route('index.php?r=accounting/ajax/delete-inv-line','POST',data,'Navi-Title');

	            SumTotalTable(
	            		$('#ew-invline-total'),
	            		$('#saleinvoiceheader-discount'),
	            		$('#ew-after-discount'),
	            		$('#ew-before-vat'),
	            		$('#ew-after-vat'),
	            		$('#ew-total'),
	            		$('#saleinvoiceheader-vat_percent'),
	            		$('#saleinvoiceheader-include_vat')
	            	);

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
		var $Discount 	= $discount.val();

		// หักส่วนลด (ก่อน vat)
		var $subtotal 	= $BeforeDisc - $Discount;
		var $vat 		= Number($percentvat.val());




		if($vattype.val() == 1){ 

			// Vat นอก

			var $InCVat   		= ($subtotal * $vat )/ 100;

			var $beforeVat 		= 0;

			var $total    		= ($InCVat + $subtotal);

			$beforevat.parent('tr').css("background-color","#aaf7ff");
			$beforevat.parent('tr').fadeOut(500);

		}else {

			// Vat ใน


			// 1.07 = 7%
			var $vat_revert 	= ($vat/100) + 1;

			var $InCVat   		= $subtotal - ($subtotal / $vat_revert);

			var $beforeVat 		= $subtotal - $InCVat;

			var $total    		= $subtotal;

			$beforevat.parent('tr').css("background-color","#FFF");
			$beforevat.parent('tr').fadeIn(500);
		}
 		
 		$sumtotal.text(number_format($BeforeDisc.toFixed(2))).fadeIn();

 		$sumtotal.attr('data',number_format($BeforeDisc));

		$discount.val($Discount).fadeIn();

		$afterdiscount.text(number_format($subtotal.toFixed(2))).fadeIn();

		$beforevat.text(number_format($beforeVat.toFixed(2))).fadeIn();

		$aftervat.text(number_format($InCVat.toFixed(2))).fadeIn();

		$grandtotal.text(number_format($total.toFixed(2))).fadeIn();

        /*----- /.Sum Section ----- */

	}

	
//---- Vat type Event Change -----
	$('body').on('change','input[id="saleinvoiceheader-percent_discount"]',function(){

        var percent_disc = $(this).val();
        var subtotal = Number($('#ew-invline-total').attr('data'));
        var discount = (subtotal * percent_disc)/ 100;

        
        
        

        $('input[id="saleinvoiceheader-discount"]').val(discount);

        SumTotalTable(
	            		$('#ew-invline-total'),
	            		$('#saleinvoiceheader-discount'),
	            		$('#ew-after-discount'),
	            		$('#ew-before-vat'),
	            		$('#ew-after-vat'),
	            		$('#ew-total'),
	            		$('#saleinvoiceheader-vat_percent'),
	            		$('#saleinvoiceheader-include_vat')
	            	);

    });
  

    $('body').on('change','input[id="saleinvoiceheader-discount"]',function(){
        var discount = $(this).val(); // Baht
        var subtotal = Number($('#ew-invline-total').attr('data'));
        var percent_disc = discount/subtotal*100;

         

        $('input[id="saleinvoiceheader-percent_discount"]').val(Math.round(percent_disc * 100) / 100);

        if($(this).val()==0)
        {
        	$('input[id="saleinvoiceheader-percent_discount"]').val(0);
        }

        SumTotalTable(
	            		$('#ew-invline-total'),
	            		$('#saleinvoiceheader-discount'),
	            		$('#ew-after-discount'),
	            		$('#ew-before-vat'),
	            		$('#ew-after-vat'),
	            		$('#ew-total'),
	            		$('#saleinvoiceheader-vat_percent'),
	            		$('#saleinvoiceheader-include_vat')
	            	);
    }); 


  $('body').on('change','#saleinvoiceheader-vat_percent',function(){

    var thiscond = $(this).val();
    
    var data = {
        series:{
          form:'Invoice',
          value:$(this).val(),
        },
        table:{
          id:'1',
          name:'vat_type',
          field:'vat_value',
          cond:$(this).val(),

        }
        
    };

	
	 	

    $.ajax({ 

          url:"index.php?r=setupnos/ajax-find-series",
          type: "POST", 
          data: data,
          async:false,
          success:function(getData){
              
            var obj = jQuery.parseJSON(getData);

              if(obj.id)
              {

                // มี Number Series แล้ว
                //alert('Exists'+obj.id);
                CheckRuningNumberSeries(obj.id,obj.code);

              }else {
                 
                swal(
                  '<?=Yii::t('common','Runing number seires is empty.')?>',
                  '<?=Yii::t('common','Please create running number series.')?>',
                  'info'
                );

                //  ยังไม่มี  ให้ไปสร้างใหม่
                $('#ew-add-series').modal('show'); 

                route('index.php?r=series/create','GET',data,'ew-series-body');

                $('ew.ew-condition').attr('ew-cond',thiscond);
              }
              
          }
      });

    vatEvent($(this));

    SumTotalTable(
	            		$('#ew-invline-total'),
	            		$('#saleinvoiceheader-discount'),
	            		$('#ew-after-discount'),
	            		$('#ew-before-vat'),
	            		$('#ew-after-vat'),
	            		$('#ew-total'),
	            		$(this),
	            		$('#saleinvoiceheader-include_vat')
	            	);
  });
  
	$('body').on('change','#saleinvoiceheader-include_vat',function(){

		SumTotalTable(
	        		$('#ew-invline-total'),
	        		$('#saleinvoiceheader-discount'),
	        		$('#ew-after-discount'),
	        		$('#ew-before-vat'),
	        		$('#ew-after-vat'),
	        		$('#ew-total'),
	        		$('#saleinvoiceheader-vat_percent'),
	        		$(this)
	        	);
	})


function vatEvent(e)
{
   if(e.val() > 0)
   {
      $('#saleinvoiceheader-include_vat').fadeIn('in');

      
   }else {
      $('#saleinvoiceheader-include_vat').fadeOut();
      $('#saleinvoiceheader-include_vat').val(1);
   }

   $('#ew-text-percent-vat').text(e.val());
}
//---- /.Vat type Event Change -----


$('body').on('click','.kv-editable-submit',function()
{

	// $.ajax({
 //            url: "index.php?r=accounting/ajax/render-inv-line",
 //            type: "GET",
 //            data: {id:$('.ew-inv-no').attr('ew-no')},
 //            success: function(getData) {

 //              jQuery(".sale-invlice-line-render").load('index.php?r=accounting/ajax/render-inv-line&id='+$('.ew-inv-no').attr('ew-no'));

 //            }
 //        });

      //jQuery("#list-of-post").load(<?php #echo Yii::app()->createAbsoluteUrl("ForumPost/index?id=true"); ?>);
	
              
	//$('form[id="form-sale-invoice"]').submit();
});
</script>
 

 