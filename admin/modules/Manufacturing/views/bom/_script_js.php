 
<?php 
$JS=<<<JS

 
	$(function(){
		$('.ew-color').colorpicker();
		$('.ew-btn-insert').hide();
    });
    
    $('body').on('click','.ew-btn-insert',function(e){ 

    	var data = { param:{
                itemId: $(this).closest('tr').attr('data-key'),
            	id:$('#ew-bom-id').attr('ew-bom-id'),
            	item:$('input[name="master_code"]').attr('ew-item-no'),
            	desc:$('.InsertDesc').text(),
            	name:$('input[name="name"]').val(),
            	qty:$('input[name="quantity"]').val(),
            	color:$('input[name="color_style"]').val(),
            	}
            };
         
    	route('index.php?r=Manufacturing/bom/create-bom-line','POST',data,'ew-bom-line');

    });
	
	
	$('body').on('keydown','.master_code',function(e){
    //$('.InsertItem').keydown(function (e) {
        let el = $(this);
        
        if (e.which == 9 || e.which == 13) {

        	var inputItem = $.trim($('.master_code').val());  
            $('.master_code').val(inputItem); 

             $.ajax({ 

                url:"index.php?r=SaleOrders/ajax/json-find-item",
                type: "POST", 
                data: {param:{item:inputItem}},
                async:false,
                success:function(getData){
                     
                    
                    var obj = jQuery.parseJSON(getData);
                    //alert( obj.name === "John" );
                
                    $('#InsertDesc').text(obj.desc);
                    $('.master_code').attr('ew-item-no',obj.item);

                    el.closest('tr').attr('data-key',obj.id);

                    // เอาคำแรก
                    let firstWords = obj.desc.split(" ");

                    el.closest('tr').find('input[name="name"]').val(firstWords[0]);
                    el.closest('tr').find('input[name="quantity"]').val(1);

                    
                    // $('#InsertQty').val(1);

                    // $('.ew-price').show();
                    // $('#InsertPrice').val(obj.std);

                    if(obj.code != 'eWinl')
                    {
                        $('.ew-btn-insert').show();
                    }else {
                        $('.ew-btn-insert').hide();
                    }
                    
                    
                }
            });

            //alert(inputItem); 
            
                 
        }
               

      
    });
JS;

$this->registerJs($JS);