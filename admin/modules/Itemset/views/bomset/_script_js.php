<script type="text/javascript">





	$(document).ready(function(){

		
		 
		$('#ewSelect').hide();
		 


		$('input[id="ewInput-txt"]').keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [8, 9, 27, 13,97,98,99,100,101,102,103,104,105]) !== -1 ||
	             // Allow: Ctrl+A, Command+A
	            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
	             // Allow: home, end, left, right, down, up
	            (e.keyCode >= 35 && e.keyCode <= 40)) {
	                 // let it happen, don't do anything
	                 return;
	        }

	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) ) {
	            e.preventDefault();
	        }

	        
	        //alert(e.keyCode);
	        
	    });


	    $('input[id="ew-to-line"]').keydown(function (e) {
	        // Allow: backspace, delete, tab, escape, enter and .
	        if ($.inArray(e.keyCode, [8, 9, 27, 13,96,97,98,99,100,101,102,103,104,105,110]) !== -1 ||
	             // Allow: Ctrl+A, Command+A
	            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
	             // Allow: home, end, left, right, down, up
	            (e.keyCode >= 35 && e.keyCode <= 40)) {
	                 // let it happen, don't do anything
	                 return;
	        }

	        // Ensure that it is a number and stop the keypress
	        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) ) {
	            e.preventDefault();
	        }

	        
	        //alert(e.keyCode);
	        
	    });


	    $('.ew-box-click').click(function(){
	    	//alert($(this).children('input[type="number"]').val());
	    	var myVal = $(this).children('input[type="number"]').val();
	    	if(myVal =='' )
	    	{
	    		//$(this).children('input[type="number"]').val(1);
	    		//CalulateFunction($(this).children('input[id="ewInput-txt"]'));
	    	}
	    });



	    $('input[name=Quantity]').change(function(){
	    	var max 	= parseInt($('.ew-Remaining').attr('data'));
	    	var myVal 	= $(this).val();

	    	if(myVal >= max)
	    	{
	    		alert('[Warning!] <?=Yii::t('common','Quantity must not be greater than inventory.')?>');

	    		//$(this).val($('.ew-Remaining').attr('data'));
	    	}

	    	calculateProduction();
	    });

	   // $('input[name="Price"]').focus(function() { $(this).select(); } );
	    $('input[id="ew-to-line"]').click(function(){
	    	$(this).val('');
	    	//$(this).select();
	    });

	});


	function calculateProduction()
	{
		var post = $('.ew-Code').attr('ew-post-param');

		$.ajax({ 

                url:"index.php?r=Itemset/ajax/possible-count",
                type: "POST", 
                data: {

                	post:post,
                 
                },
                async:false,
                success:function(getData){
                	$('.ew-Remaining').html('<?=Yii::t('common','Inventory')?> : '+getData).attr('data',getData);
                }

            });
	}

	function CalulateFunction(getThis)
	{
		if(getThis.val() < 0 ){
	        	 getThis.val(0);
	    }
	    

		var str = '';
		var space = ' ';
		var i = 1;

		// Find text box.
		var nameIs = getThis.attr('name');

		var value = '';



		//var icode = '';

		jQuery.each($('input[name="'+nameIs+'"]') ,function(ii,val){	
			
			// Find Not null.
			if(getThis.val() > 0)
			{


				// Get First Item	
				// Create text.
				if(i==1)
				{
					value  = $.trim(getThis.attr('ew-name'));
				}else {

					value  = $.trim(getThis.attr('ew-name')).substring(2);
				}	



				// If quantity > 1 ,Then show that quantity.
				// Else not show quantity.
				if(getThis.val() >= 2)
				{
					str+= value + '/' + getThis.val() + space;
					 
				}else {
					str+= value + space;
				}


				//icode += $.trim($(this).attr('ew-code')) + ' ';


				// (i ++) When find first.
				i++;

			}
				
		});


		$('input[name="'+nameIs.slice(0,-2)+'-Char"]').val(str);
		//$('input[name="'+nameIs.slice(0,-2)+'-Code"]').val(icode);
 
		makeCode();
		mergeText();




		if($('.ew-Code').attr('ew-post-param')!='[]')
	    {
	    	$('#ewSelect').show('fast');
	    	$('input[name="Quantity"]').val(1);
	    }else {
	    	$('#ewSelect').hide('fast');
	    }
	}



	$('body').on('change','#ewInput-txt',function(){
		
		if($(this).val() < 0 ){
	        	 $(this).val(0);
	    }
	    

		var str = '';
		var space = ' ';
		var i = 1;

		// Find text box.
		var nameIs = $(this).attr('name');

		var value = '';



		//var icode = '';

		jQuery.each($('input[name="'+nameIs+'"]') ,function(ii,val){	
			
			// Find Not null.
			if($(this).val() > 0)
			{


				// Get First Item	
				// Create text.
				if(i==1)
				{
					value  = $.trim($(this).attr('ew-name'));
				}else {

					value  = $.trim($(this).attr('ew-name')).substring(2);
				}	



				// If quantity > 1 ,Then show that quantity.
				// Else not show quantity.
				if($(this).val() >= 2)
				{
					str+= value + '/' + $(this).val() + space;
					 
				}else {
					str+= value + space;
				}


				//icode += $.trim($(this).attr('ew-code')) + ' ';


				// (i ++) When find first.
				i++;

			}
				
		});


		$('input[name="'+nameIs.slice(0,-2)+'-Char"]').val(str);
		//$('input[name="'+nameIs.slice(0,-2)+'-Code"]').val(icode);
 
		makeCode();
		mergeText();




		if($('.ew-Code').attr('ew-post-param')!='[]')
	    {
	    	$('#ewSelect').show('fast');
	    	$('input[name="Quantity"]').val(1);
	    }else {
	    	$('#ewSelect').hide('fast');
	    }
	});






	$('body').on('change','input[name="'+$('#ewGenChar').attr('name')+'"]',function(){

		//$('.ew-Desc').html($('#ewGenChar').val());
	});






	




	function makeCode()
	{
		// Json Create 
		var param = '';
		 
		jsonObj = [];
		jQuery.each($('input[id="ewInput-txt"]') ,function(){	

			if($(this).val() > 0)
			{
				textval = {}	
				textval ["c"] = $(this).attr('ew-xcode');
				textval ["q"] = $(this).val();


				jsonObj.push(textval);
			}

		});


		param = JSON.stringify(jsonObj);
		// Do Somethink
 		//console.log(jsonString = JSON.stringify(jsonObj));
		//$('.ew-Code').html(param);
		$('.ew-Code').attr('ew-post-param',param);

	}





	function mergeText()
	{
		var textval = '';
		var textcode = $('#ew-Head').attr('ew-detail');
		var xcode = '';

		jQuery.each($('input[id=ewGenChar]'),function(i){
			//console.log(ii + ': ' + $(this).attr('name'));
			 textval += $(this).val();
		});

		validateItemDesc($('#ew-gen-code').attr('ew-set-code')+' '+textval);
	}



    function validateItemDesc(text)
    {
    	$.ajax({ 

                url:"index.php?r=ajax/json-validate-item&file=_script_js",
                type: "POST", 
                data: {param:{item:text}},
                async:false,
                success:function(getData){
                     
                    
                    var obj = jQuery.parseJSON(getData);

                    if($.trim(obj.desc) === $.trim(obj.sent))
                    {
                    	// มี item อยู่แล้ว
                    	$('.ew-Validate').attr('data-key',obj.item).text(obj.code);
                    	$('.ew-Validate-').text(obj.sent);

                    	$('input[name="Price"]').val(obj.std);
                    	$('input[name="Quantity"]').val(1);
                    	$('input[name="Discount"]').val(0);

                    	$('.ew-Code').text(' ');

                    	// $('.ew-Remaining').html('<?=Yii::t('common','Inventory')?> : '+obj.remain);
                    	// $('.ew-Remaining').attr('data',obj.remain);

                    }else {
                    	// ยังไม่มี item (สร้างใหม่)
                    	$('.ew-Validate').attr('data-key',obj.item).text(obj.desc);
                    	$('.ew-Validate-').text(obj.sent);

                    	$('input[name="Price"]').val(0);
                    	$('input[name="Quantity"]').val(1);
                    	$('input[name="Discount"]').val(0);


                    	//$('.ew-Remaining').attr('data','').html('');

          
                    }	
                    
                    

         
                    calculateProduction();
                    Undefined($('#ew-gen-code').attr('ew-set-code'));
                    
                }
            });
    }

    $('body').one('click','#ewSelect',function(){
		 
		console.log('create-now');
		let item = {
			list: $('body').find('.ew-Code').attr('data-item')
		}
		console.log(item);
		CreateBom($('.ew-Code').attr('ew-post-param'));

		 
        
        var itemno = $('.ew-Validate').attr('ew-icode');  
        var unit_price = $('input[name=Price]').val();

        if(unit_price==0)
        {
            //alert('ไม่มีราคา');
            
        }

        var data = {param:{ 
            itemno:itemno, 
            orderno:$('#saleheader-no').val(), 
            itemset:<?=$_GET['id']?>, 
            soid:$('#SaleOrder').attr('ew-so-id'),
            amount:$('input[name=Quantity]').val(),
            price:$('input[name=Price]').val(),
            discount:$('input[name=Discount]').val(),
         }};

         //alert(itemno);
        route("index.php?r=SaleOrders/saleorder/create_saleline",'POST',data,'SaleLine');

        //Close Modal
        $('#myModal').modal('hide');
        // swal(
        //         '<?=Yii::t('common','บันทึกรายการแล้ว')?>',
        //         '<?=Yii::t('common','สามารถเลือกสินค้าต่อได้')?>',
        //         'success'
        //     );
        
        getSumLine($('#ew-discount-amount').val()); 

        $('html, body').animate(
            { scrollTop: $('.grid-view').offset().top -80 }, 
            500); 

        LoadAjax(); 
         
         
     
	});
	

    function CreateBom(post)
    {
    	 
    	$.ajax({ 

                url:"index.php?r=Manufacturing/ajax/create-bom&text=false",
                type: "POST", 
                data: {
					param:{
						post:post,
						desc:$.trim($('#ew-real-desc').text()),
						group:$('#ew-gen-code').attr('ew-id'),
						item:$('.ew-Validate').html(),
						id: $('#ew-gen-code').attr('ew-id'),
					},
					item:{
						list: $('body').find('.ew-Code').attr('data-item')
					}
				},
                async:false,
                success:function(getData){
                	$('.ew-Code').html(getData);
                }

            });
    }


    function Undefined(e)
    {
    	var modal = '<div id="ew-Alert-Modal" class="modal  fade" role="dialog">';
    	 	modal += '<div class="modal-dialog">';
    	
    	     
    	    modal += '<div class="modal-content">';
    	    modal += '  <div class="modal-header">';
    	    modal += '    <button type="button" class="close" data-dismiss="modal">&times;</button>';
    	    modal += '    <h4 class="modal-title"><?=Yii::t('common','Not Found') ?> !</h4>';
    	    modal += '  </div>';
    	    modal += '  <div class="modal-body">';
    	    modal += '    <p>ไม่พบข้อมูลของ</p><label>' + $('.ew-set-name').text() + '</label><br>';
    	    modal += '    <p class="text-red">กรุณา ตรวจสอบการผูก Bom ใหม่ แล้วลองอีกครั้ง</p>';
    	    modal += '  </div>';
    	    modal += '  <div class="modal-footer">';
    	    modal += '    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
    	    modal += '  </div>';
    	    modal += '</div>';
    	
    	  	modal += '</div>';
    		modal += '</div>';

    	if (typeof e === "undefined") 
    	{
    		$('body').append(modal);
    		$('#ew-Alert-Modal').modal('toggle');
    	}
    	
    }

 




</script>