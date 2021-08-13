var form = '#itemjournal-';
var formid = '#form-vendors';



$(document).ready(function(){

	loadFindBox('.render-search-item');


	setTimeout(function(){

		route('warehousemoving/adjust/ajax-line&id='+$(form+'documentno').attr('data'),'GET',{data:false},'ew-adj-line');

		//ValidateSeries('Adjust','5','item_journal',$(form+'typeofdocument').val(),$(form+'adjusttype').val(),'#itemjournal-documentno',true);

        $('.ew-add-to-adj-line').hide();

        $('.ew-adj-row').slideDown('slow');
        $('.ewTimeout').fadeOut();
	}, 100);


    $(document).on('pjax:success', function() {
        LoadFunction();
    });

	// Modal load
    $('#ewGetItemModal').on('shown.bs.modal', function () {



      $('tr').each(function(i, el) {
        $(el).children('td').find('#ew-inv-qty').fadeOut();
        $(el).children('td').find('#ew-inv-price').fadeOut();
      });

    });


    disabledType($(form+'adjusttype'));

});




$('body').on('keyup','.InsertItem',function(e){
		 
		var len = $.trim($(this).val()).length;

		if(len >= 3){
				if (e.which === 32 || e.which === 13) { // 32 Space bar


				 findItemTable($(this));


				}

				if (e.which == 9) {

								if($('#InsertDesc').attr('ew-item-code') != 'eWinl')
										{

												$('#InsertDesc').first().focus();
										}

						var inputItem = $.trim($('.InsertItem').val());
						$('.InsertItem').val(inputItem);


						 $.ajax({

								url:'index.php?r=Itemset/ajax/json-find-item',
								type: 'POST',
								data: {param:{item:inputItem}},
								async:true,
								success:function(getData){


										var obj = jQuery.parseJSON(getData);

										$('.ew-desc').show();
										$('#InsertDesc').val(obj.desc);

										$('#InsertDesc').attr('ew-item-code',obj.item);

										$('.ew-qty').show();
										$('#InsertQty').val(1);

										$('.ew-price').show();
										$('#InsertPrice').val(obj.std);

										if(obj.code != 'eWinl')
										{
												$('.ew-add').show();
										}else {
												$('.ew-add').hide();
										}


								}
						});



				}


		}else{
				$('.find-item').slideUp();
		}



});



// $('body').on('click','.pick-item-to-createline',function(){
// 	createItemToLine({
// 			item:$(this).data('key'),
// 			desc:$(this).attr('desc'),
// 			qty:1,
// 			price:$(this).attr('price'),
// 			id:$(form+'documentno').attr('data'),
// 			no:$('itemjournal[id="documentno"]').attr('ew-no_'),
// 			type:$('select[name="InsertType"]').val(),
// 			docNo:$(form+'documentno').val(),
// 			typeDoc:$(form+'typeofdocument').val(),
// 			adjType:$(form+'adjusttype').val(),
// 		},'#Item_Adjust_Line','warehousemoving/adjust');
// });








$('form[id="ew-Item-Adjust"]').on('change','input[type="text"]',function(){

    $('#ew-Post-Adjust').attr('readonly','readonly');

});

$('body').on('change',form+'typeofdocument',function(){

	var field = $(this).val();
	var cond = $(form+'adjusttype').val();

	ValidateSeries('Adjust','5','item_journal',field,cond,'#itemjournal-documentno',true);


});

$('body').on('change',form+'adjusttype',function(){


       disabledType($(this));

    //if (confirm('ยืนยันการทำเปลี่ยนแปลง ! ')) {

        var field   = $(form+'typeofdocument').val();
        var cond    = $(form+'adjusttype').val();

        ValidateSeries('Adjust','5','item_journal',field,cond,'#itemjournal-documentno',true);

        $('#ew-Post-Adjust').attr('readonly','readonly');

        setTimeout(function(){
            $('form[id="ew-Item-Adjust"]').submit();
        },100);


    //}
    //return false;

});

function disabledType($this){



    if($this.val()=='+'){

        $('option[value="Output"]').attr('disabled',false);
        $('option[value="Purchase"]').attr('disabled',false);

        $('option[value="Sale"]').attr('disabled','disabled');
        $('option[value="Consumption"]').attr('disabled','disabled');

    }else {
        $(form+'typeofdocument').val('Adjust');

        $('option[value="Output"]').attr('disabled','disabled');
        $('option[value="Purchase"]').attr('disabled','disabled');

        $('option[value="Sale"]').attr('disabled',false);
        $('option[value="Consumption"]').attr('disabled',false);

    }
}


$('body').on('click','#ew-Save-Adjust',function(){
    //alert($(this).attr('ew-val'));
    //if (confirm('ยืนยันการทำรายการ ! ')) {
    	//alert('test');
    	//$('form[id="ew-Item-Adjust"]').submit();
    //}
    //return false;
});

$('body').on('click','#ew-Post-Adjust',function(){

    if (confirm('ยืนยันการทำรายการ ! ')) {
    	 var $data = {
                        sourceId:$(form+'documentno').attr('data'),
                        adjType:$(form+'adjusttype').val(),
                        postDate:$(form+'postingdate').val(),
                    };
    	 $.ajax({

                url:"index.php?r=warehousemoving/adjust/post-journal",
                type: "POST",
                data: $data,
                async:true,
                success:function(getData){
                	$('.Navi-Title').html(getData);
                }

            });
    }
    return false;
});


// // --------- Insert Item -----------

//  $('body').on('click','input[name="ew-InsertAdd"]',function(e){

//     if($('.ew-InsertDesc').attr('ew-item-code')!='eWinl'){
//         createItemToLine({
//             item:$('.ew-InsertDesc').attr('ew-item-code'),
//             desc:$('.ew-InsertDesc').val(),
//             qty:$('.ew-direct-qty').val(),
//             price:$('.ew-direct-price').val(),
//             id:$(form+'documentno').attr('data'),
//             no:$('itemjournal[id="documentno"]').attr('ew-no_'),
//             type:$('select[name="InsertType"]').val(),
//             docNo:$(form+'documentno').val(),
//             typeDoc:$(form+'typeofdocument').val(),
//             adjType:$(form+'adjusttype').val(),
//           },'#Item_Adjust_Line','warehousemoving/adjust');
//     }

//  });

// $('body').on('keydown','.ew-direct-price,.ew-add-to-adj-line', function(e) {

// 	var keyCode = e.keyCode || e.which;
// 	if (keyCode === 13)
// 	{
//       if($('.ew-InsertDesc').attr('ew-item-code')!='eWinl'){
//             createItemToLine({
//                     item:$('.ew-InsertDesc').attr('ew-item-code'),
//                     desc:$('.ew-InsertDesc').val(),
//                     qty:$('.ew-direct-qty').val(),
//                     price:$('.ew-direct-price').val(),
//                     id:$(form+'documentno').attr('data'),
//                     no:$('itemjournal[id="documentno"]').attr('ew-no_'),
//                     type:$('select[name="InsertType"]').val(),
//                     docNo:$(form+'documentno').val(),
//                     typeDoc:$(form+'typeofdocument').val(),
//                     adjType:$(form+'adjusttype').val(),
//                   },'#Item_Adjust_Line','warehousemoving/adjust');
//         }
// 	}
// });


// $('body').on('click','.ew-delete-adj-line',function(){

// 	if (confirm('Do you want to delete  ?')) {

//         var data = {id:Number($(this).attr('data'))};

//         var tr = $(this).closest('tr');
//         tr.css("background-color","#aaf7ff");
//         tr.fadeOut(500, function(){
//             tr.remove();
//             route('index.php?r=warehousemoving/adjust/delete-adj-line','POST',data,'Navi-Title');



//         });

//     }

//     return false;

// });
// // --------- /.Insert Item -----------


// Create Number Series
$('body').on('click','.ew-save-modal-common',function(){

    SeriesFormPost('item_journal',$('#numberseries-name').attr('ew-data-type'),$('ew.ew-condition').attr('ew-cond'));

});


$('body').on('click','.ew-pick-item-modal',function(){
    $('#ewGetItemModal').modal('show');

    route("items/ajax/ajax-pick-items",'GET',{search:'',id:$(form+'documentno').attr('data')},'ew-Pick-Inc-Item');

});

$('body').on('click','.ew-pick-item-to-inc-line',function(){

      createItemJournalLine();

});



function createItemJournalLine()
    {
      if($('.items:checked').serialize()!='')
      {

        var inv_no = $('.ew-inv-no').attr('ew-no');

        var data = {items:$('.items:checked').serializeArray(),id:inv_no};

          $('tr').each(function(i, el) {

            var iCheck = $(el).children('td').find('input.ew-checked');
            var item = $(el).children('td').find('input.ew-checked').val();
            var qty = $(el).children('td').find('#ew-inv-qty').val();
            var price = $(el).children('td').find('#ew-inv-price').val();


            if (iCheck.is(':checked')) {

              createItemToLine({
                item:item,
                qty:qty,
                price:price,
                id:$(form+'documentno').attr('data'),
                docNo:$(form+'documentno').val(),
                typeDoc:$(form+'typeofdocument').val(),
                adjType:$(form+'adjusttype').val(),
              },'#Item_Adjust_Line','warehousemoving/adjust');


            }

          });


          $('#ewGetItemModal').modal('hide');



      }else{

        swal(
          'Please select one of the options.',
          'That thing is still around?',
          'warning'
        );

        return false;
      }

}







// Item Picker
 


$('body').on('keydown','.ew-InsertItems',function(e){
    

        
    if (e.which == 9 || e.which == 13) {

            if($('.ew-InsertDesc').attr('ew-item-code') != 'eWinl')
                {
                     
                    $('input.ew-InsertDesc').first().focus();
                }

        var inputItem = $.trim($(this).val());  
        $(this).val(inputItem);  
         

         $.ajax({ 
            url:"index.php?r=Itemset/ajax/json-find-item",
            type: "POST", 
            data: {param:{item:inputItem}},
            async:true,
            success:function(getData){
                 
                var obj = jQuery.parseJSON(getData);              
                if(obj.all==1){                
                    AddItemToLine({
                        item:obj.id,
                        desc:obj.desc,
                        qty:1,
                        price:obj.std,
                        id:$('form#ew-Item-Adjust').data('key'),
                        no:$('itemjournal[id="documentno"]').attr('ew-no_'),
                        type:$('select[name="InsertType"]').val(),
                        docNo:$(form+'documentno').val(),
                        typeDoc:$(form+'typeofdocument').val(),
                        adjType:$(form+'adjusttype').val(),
                    },'#Item_Adjust_Line','warehousemoving/adjust');   
                    $('#PickItem-Modal').modal('hide');
                }
                $('.ew-desc').show();
                $('.ew-InsertDesc').val(obj.desc);
                $('.ew-InsertDesc').attr('ew-item-code',obj.item).attr('data-key',obj.id);
                $('.ew-direct-qty').show().val(1);
                $('.ew-direct-price').show().val(obj.std);
                $('.ew-remain').html(obj.remain);
                if(obj.code != 'eWinl')
                {
                    $('.ew-add-to-adj-line').show();
                    $('input.ew-InsertDesc').first().focus();
                }else {
                    $('.ew-add-to-adj-line').hide();
                }
            }
        });
    }
});

function AddItemToLine(data,models,module)
{
    $.ajax({
        url: "index.php?r="+module+"/json-create-item-line",
        type: "POST",
        data: data,
        dataType:'JSON',
        success: function(obj) { 
            if(obj.status==200){   
                
                 
                // ##-- ถ้ามี Empty ให้ลบ tr นี้ออกก่อน --
                $(models+' div.empty').parent('td').parent('tr').remove();            
                //var obj = jQuery.parseJSON(getData);
                // ##-- หาจำนวนแถวถัดไป --
                var CountNumber = Number($(models+" tr" ).length); // แถวทั้งหมด (รวม header ,footer)                
                CountNumber = CountNumber -1; // ลบจำนวนแถวออก 1 แถว จะได้ค่าที่ต้องการ
                // ##-- /.หาจำนวนแถวถัดไป--
                
                var options  = '';                
                $.each(obj.location,function(key,model){
                    options += '<option value="'+model.id+'">'+model.code+'</option>';
                })

                // ##-- สร้างบรรทัดใหม่ --                
                var row = $('<tr data-key="'+obj.id+'">'+
                    '<td>'+CountNumber+'</td>'+
                    '<td>'+obj.item+'</td>'+
                    '<td>'+obj.desc+'</td>'+
                    '<td class="hidden text-right">'+(obj.remain).toFixed(2)+'</td>'+
                    '<td class="text-right ew-adj-qty"><div class="text-primary " data="'+Math.abs(obj.qty)+'">'+Number(obj.qty).toFixed(2)+'</div></td>'+
                    '<td class="text-right hidden ew-adj-qty-after">'+Number(obj.remain + obj.qty).toFixed(2)+'</td>'+
                    '<td class="text-right ew-adj-price"><div class="text-primary" data="'+Math.abs(obj.price)+'">'+Math.abs(obj.price).toFixed(2)+'</div></td>'+
                    '<td align="right"><div class="ew-line-total" data="'+obj.qty * obj.price+'">'+Math.abs((obj.qty * obj.price).toFixed(2))+'</div></td>'+
                    '<td><select name="location" class="form-control location-pick">'+options+'</select></td>'+
                    '<td align="right"><div class="btn btn-danger-ew btn-flat ew-delete-adj-line" data="'+obj.id+'"><i class="fa fa-times" aria-hidden="true"></i></div></td>'+
                '</tr>');  
                $(models+' tbody:last').append(row);
                // ##-- /.สร้างบรรทัดใหม่ --                
            }else{
                var color = 'text-success';
                if(obj.remain + obj.qty < 0){
                    color = 'text-danger';
                }
                $('tr[data-key='+obj.id+']').find('td.ew-adj-qty').html('<div class="text-primary" data="'+Math.abs(obj.qty)+'">'+Number(obj.qty).toFixed(2)+'</div>');
                $('tr[data-key='+obj.id+']').find('td.ew-adj-qty-after').html('<div class="'+color+'">'+Number(obj.remain + obj.qty).toFixed(2)+'</div>');
                $.notify({
                    // options
                    icon: 'fas fa-shopping-basket',
                    message: 'เพิ่มรายการสินค้าแล้ว ('+obj.qty+' ชิ้น)',                         
                },{
                    // settings
                    type: 'warning',
                    delay: 1500,
                    z_index:3000,
                    placement: {
                        from: "top",
                        align: "center"
                    }
                });
            }
        }
    });
}

function GenerateBom(post)
{

    $.ajax({

            url:"index.php?r=Manufacturing/ajax/create-bom",
            type: "POST",
            data: {param:{
                post:post,
                desc:$.trim($('#ew-real-desc').text()),
                group:$('#ew-gen-code').attr('ew-id'),
                item:$('.ew-Validate').attr('data-key'),
                id: $('#ew-gen-code').attr('ew-id'),
                price: $('input[name="Price"]').val(),
            }},
            async:false,
            success:function(getData){
                $('.ew-Code').html(getData);
                var obj = $.parseJSON(getData);
                $('#ew-render-itemno').attr('data-key',obj.message.id);

                
            }

        });
}





//------- Edit Item ----------
$('body').on('click','.ew-adj-qty',function(){
    EditText('qty',$(this),);
  });
  $('body').on('click','.ew-adj-price',function(){
    EditText('price',$(this),);
  });

  function EditText($n,e,){
    var lineNumber  = e.children("div").attr('ew-line-no');
    var value_txt   = e.children("div").attr('data');
    if(value_txt==='')
    {
        value_txt   = e.children("input").val();
    }
    var text_qty    = '<input type="number" name="'+$n+'" id="ew-text-editor" value="' + value_txt + '" ew-lineno="' + lineNumber + '" class="form-control text-right pull-right" style="width:80px;"></div>';
    e.attr('class','text-right');
    e.html(text_qty);
    e.children("input").focus();
    e.children("input").select();
  }
  
  
    $('body').on('keydown','input[id="ew-text-editor"]',function(e){

        if ((e.which == 13) || (e.which == 9)) {
            var $doc    = $('form[id="ew-Item-Adjust"]').attr('data-key');
            var row     = $(this).closest('tr').attr('data-key');
            var $data   = {
                name:$(this).attr('name'),
                key:$(this).closest('tr').attr('data-key'),
                val:$(this).val(),
            };
            
            $.ajax({
                url: "index.php?r=warehousemoving/adjust/ajax-line&id="+$doc,
                type: "POST",
                data: $data,
                success: function(getData) {

                $('.ew-adj-line').html(getData);

                if (e.which == 9) {
                    // แก้ไขช่อยถัดไป
                    var next = parseInt(row) + 1;
                    if(e.currentTarget.name=='qty'){
                    EditText('price',$('tr[data-key='+row+']').find('td.ew-adj-price'),);
                    }else{
                    row = next;                       
                    EditText('qty',$('tr[data-key='+next+']').find('td.ew-adj-qty'),);
                    }  
                }
                }
            });
        }
    });
   

    $('body').on('change','.location-pick',function(e){

        
        var $doc    = $('form[id="ew-Item-Adjust"]').data('key');
        var row     = $(this).closest('tr').data('key');
        var $data   = {
            name:$(this).attr('name'),
            key:row,
            val:$(this).val(),
        };
        
        $.ajax({
            url: "index.php?r=warehousemoving/adjust/ajax-line&id="+$doc,
            type: "POST",
            data: $data,
            success: function(getData) {
                $('.ew-adj-line').html(getData);                 
            }
        });
        
    });
  //------- /.Edit Item ----------

