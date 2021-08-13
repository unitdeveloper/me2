 $('body').on('keydown','.ew-InsertItems',function(e){
    

        
        if (e.which == 9 || e.which == 13) {

                if($('.ew-InsertDesc').attr('ew-item-code') != 'eWinl')
                    {
                         
                        $('input.ew-InsertDesc').first().focus();
                    }

            var inputItem = $.trim($(this).val());  
            $(this).val(inputItem);  
             

             $.ajax({ 

                url:"index.php?r=SaleOrders/ajax/json-find-item",
                type: "POST", 
                data: {param:{item:inputItem}},
                async:true,
                success:function(getData){
                     
                    
                    var obj = jQuery.parseJSON(getData);
                     
                    $('.ew-desc').show();
                    $('.ew-InsertDesc').val(obj.desc);

                    $('.ew-InsertDesc').attr('ew-item-code',obj.item);

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

            //alert(inputItem); 
            //getSumLine($('#ew-discount-amount').val());
                 
        }
               

      
    });
 
$('body').on('keydown','.ew-InsertDesc',function(e){   
  
  if (e.which == 9 || e.which == 13) {

    $('input.ew-direct-qty').focus();
  }
});

$('body').on('keydown','.ew-direct-qty',function(e){     
  if (e.which == 9 || e.which == 13) {

    $('input.ew-direct-price').focus();

  }
});

$('body').on('click','tr',function(){
    var iCheck = $(this).children('td').find('input.ew-checked');
    if (iCheck.is(':checked')) {
      $(this).children('td').find('#ew-inv-qty').fadeIn(600);
      $(this).children('td').find('#ew-inv-price').fadeIn(600);
    }else {
      $(this).children('td').find('#ew-inv-qty').fadeOut(400);
      $(this).children('td').find('#ew-inv-price').fadeOut(400);
    }
  });


 

  //------ Select Item ---------
  $('body').on('change','#ew-search-items-text',function(){ 
       
      route("items/ajax/ajax-pick-items",'GET',{search:$('#ew-search-items-text').val(),id:$('.ew-inv-no').attr('ew-no')},'ew-Pick-Inc-Item');
      LoadFunction();
  });

  
  $('body').on('click','#ew-search-items-btn',function(){ 

    route("items/ajax/ajax-pick-items",'GET',{search:$('#ew-search-items-text').val(),id:$('.ew-inv-no').attr('ew-no')},'ew-Pick-Inc-Item');
    LoadFunction();
 
  });


  $('body').on('click','.ew-inc-close-pic-item',function(){
    $('#ewGetItemModal').modal('hide'); 
    window.location.hash = "index.php?r=accounting/saleinvoice/update&id="+$('.ew-inv-no').attr('ew-no');
  });
  //------/. Select Item -------


function LoadFunction()
{
    $('tr').each(function(i, el) {
        $(el).children('td').find('#ew-inv-qty').fadeOut();
        $(el).children('td').find('#ew-inv-price').fadeOut();
    });  
}

 function createItemToLine($data,$models,$module)
    {

    	$.ajax({
            url: "index.php?r="+$module+"/json-create-item-line",
            type: "POST",
            data: $data,
            success: function(getData) {

 

              // ##-- ถ้ามี Empty ให้ลบ tr นี้ออกก่อน --
              $($models+' div.empty').parent('td').parent('tr').remove();

               


              var obj = jQuery.parseJSON(getData);


              // ##-- หาจำนวนแถวถัดไป --
              var CountNumber = Number($($models+" tr" ).length); // แถวทั้งหมด (รวม header ,footer)
                
              CountNumber = CountNumber -1; // ลบจำนวนแถวออก 1 แถว จะได้ค่าที่ต้องการ

              // ##-- /.หาจำนวนแถวถัดไป--

             
              // ##-- สร้างบรรทัดใหม่ --
              
              var $row = $('<tr data-key="'+obj.id+'">'+
                '<td>'+CountNumber+'</td>'+
                '<td>'+obj.item+'</td>'+
                '<td>'+obj.desc+'</td>'+
                '<td align="right">'+(obj.remain).toFixed(2)+'</td>'+
                '<td class="text-right ew-adj-qty"><div class="text-primary " data="'+Math.abs(obj.qty)+'">'+Number(obj.qty).toFixed(2)+'</div></td>'+
                '<td align="right">'+Number(obj.remain + obj.qty).toFixed(2)+'</td>'+
                '<td class="text-right ew-adj-price"><div class="text-primary" data="'+Math.abs(obj.price)+'">'+Math.abs(obj.price).toFixed(2)+'</div></td>'+
                '<td align="right"><div class="ew-line-total" data="'+obj.qty * obj.price+'">'+Math.abs((obj.qty * obj.price).toFixed(2))+'</div></td>'+
                '<td align="right"><div class="btn btn-danger ew-delete-adj-line" data="'+obj.id+'"><i class="fa fa-trash-o" aria-hidden="true"></i></div></td>'+
              '</tr>');  


              $($models+' tbody:last').append($row);
              // ##-- /.สร้างบรรทัดใหม่ --


            }
        });


    }



//------- Edit Item ----------
$('body').on('click','.ew-adj-qty',function(){

  EditText('qty',$(this),);

  // var lineNumber  = $(this).children("div").attr('ew-line-no');
  // var value_txt   = $(this).children("div").text();

  // if(value_txt==='')
  // {
  //     value_txt   = $(this).children("input").val();
  // }

  // var text_qty    = '<input type="number" name="qty" id="ew-text-editor" value="' + value_txt + '" ew-lineno="' + lineNumber + '" class="form-control text-right pull-right" style="width:80px;"></div>';

  // $(this).attr('class','text-right');

  // $(this).html(text_qty);
  // $(this).children("input").focus();
  // $(this).children("input").select();
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
 
//------- /.Edit Item ----------
 