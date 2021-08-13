$(document).ready(function(){



		$('#ewSelect').hide();



		// $('input[id="ewInput-txt"]').keydown(function (e) {
	  //       // Allow: backspace, delete, tab, escape, enter and .
	  //       if ($.inArray(e.keyCode, [8, 9, 27, 13,97,98,99,100,101,102,103,104,105]) !== -1 ||
	  //            // Allow: Ctrl+A, Command+A
	  //           (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
	  //            // Allow: home, end, left, right, down, up
	  //           (e.keyCode >= 35 && e.keyCode <= 40)) {
	  //                // let it happen, don't do anything
	  //                return;
	  //       }
		//
	  //       // Ensure that it is a number and stop the keypress
	  //       if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) ) {
	  //           e.preventDefault();
	  //       }
		//
		//
	  //       //alert(e.keyCode);
		//
	  //   });


	    $('input.ew-to-line').keydown(function (e) {
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
	    	if(myVal =='' ){
	    		//$(this).children('input[type="number"]').val(1);
	    		//CalulateFunction($(this).children('input[id="ewInput-txt"]'));
	    	}
	    });


			$('body').on('change','input[name=Quantity]',function(){
	    //$('input[name=Quantity]').change(function(){
	    	var max 	= parseInt($('.ew-Remaining').attr('data'));
	    	var myVal 	= $(this).val();

	    	if(myVal >= max)
	    	{
	    		console.log('[Warning!] Quantity must not be greater than inventory');

	    		//$(this).val($('.ew-Remaining').attr('data'));
	    	}

	    	calculateProduction();
	    });

	   // $('input[name="Price"]').focus(function() { $(this).select(); } );
	    // $('input.ew-to-line').click(function(){
	    // 	$(this).val('');
	    // 	//$(this).select();
	    // });

	});


	function calculateProduction()
	{
		// var post = $('.ew-Code').attr('ew-post-param');

		// $.ajax({

        //         url:"index.php?r=Itemset/ajax/possible-count",
        //         type: "POST",
        //         data: {

        //         	post:post,

        //         },
        //         async:false,
        //         success:function(getData){
        //         	$('.ew-Remaining').html('Inventory : '+getData).attr('data',getData);
        //         }

        //     });
	}

	function CalulateFunction(getThis){
		if(getThis.val() < 0 ){
	        	 getThis.val(0);
	    }

		var str 	= '';
		var space 	= ' ';
		var i 		= 1;
		// Find text box.
		var nameIs 	= getThis.attr('name');
		var value 	= '';
		jQuery.each($('input[name="'+nameIs+'"]') ,function(ii,el){

			// Find Not null.
			if($(this).val() > 0){
				// Get First Item
				// Create text.
				if(i==1){
					value  = $.trim($(this).attr('ew-name'));
				}else {
					value  = $.trim($(this).attr('ew-name')).substring(2);
				}
				// If quantity > 1 ,Then show that quantity.
				// Else not show quantity.
				if($(this).val() >= 2){
					str+= value + '/' + $(this).val() + space;
				}else {
					str+= value + space;
				}

				// (i ++) When find first.
				i++;

			}

		});

		$('input[name="'+nameIs.slice(0,-2)+'-Char"]').val(str);

		makeCode();
		mergeText();

		if($('.ew-Code').attr('ew-post-param')!='[]'){
	    	$('#ewSelect').show('fast');
	    	//$('input[name="Quantity"]').val(1);
	    }else {
	    	$('#ewSelect').hide('fast');
	    }
	}


	$('body').on('change','input[name="'+$('#ewGenChar').attr('name')+'"]',function(){

		//$('.ew-Desc').html($('#ewGenChar').val());
	});











	function makeCode()
	{
		// Json Create
		var param = '';

		jsonObj = [];
		jQuery.each($('input.ewInput-txt') ,function(){

			if($(this).val() > 0)
			{
				textval = {}
				textval ["c"] 	= $(this).attr('ew-xcode');
				textval ["q"] 	= $(this).val();
				textval ["id"]	= $(this).attr('data-key');

				jsonObj.push(textval);
			}

		});

		let raw = [];
		$.each($('.ewInput-txt'), function(){			
			if($(this).val() > 0){
				raw.push({
					id:$(this).attr('data-key'),
					qty: $(this).val()
				});
			}
		});

		param = JSON.stringify(jsonObj);
		// Do Somethink
 		//console.log(jsonString = JSON.stringify(jsonObj));
		//$('.ew-Code').html(param);
		$('.ew-Code').attr('ew-post-param',param).attr('data-item', JSON.stringify(raw));

	}





	function mergeText(){
		var textval = '';

		jQuery.each($('input[id=ewGenChar]'),function(i){
			 textval += $(this).val();
		});

		// Set Interval 08/10/2020
		// https://stackoverflow.com/questions/17156332/jquery-ajax-how-to-handle-timeouts-best/27245238 
		setInterval(validateItemDesc($('#ew-gen-code').attr('ew-set-code')+' '+textval),3500); 

	 
		//validateItemDesc($('#ew-gen-code').attr('ew-set-code')+' '+textval);
	}



    function validateItemDesc(text){
		var post 	= $('.ew-Code').attr('ew-post-param');
		let items 	= $('body').find('.ew-Code').attr('data-item');

		$('body').find('.ew-Validate-').text(text);

		$('body').find('input').attr('disabled', true);
    	$.ajax({

                url:"index.php?r=ajax/json-validate-item&file=item_set",
                type: "POST",
                data: {param:{item:text,post:post,itemList:items}},
                async:true,
                success:function(getData){


                    var obj = jQuery.parseJSON(getData);
					 
					if(obj.status == 200){
                    	// มี item อยู่แล้ว
                    	$('body').find('.ew-Validate').attr('data-key',obj.item).text(obj.code);
						//$('body').find('.ew-Validate-').text(obj.sent);
						
						$('#ew-render-itemno').attr('data-key',obj.itemid);

						
						$('input[name="Price"]').val((obj.std ? obj.std : 0));
						if(obj.lastprice > 0){
							$('input[name="Price"]').attr('placeholder',obj.text.LastPrice+ ' = '+obj.lastprice);
						}


                    	//$('.ew-to-line').val(1);
                    	$('input[name="Discount"]').val(0);

						
                    	// Change Photo
						//$('.ew-main-photo').attr('src','images/product/' +obj.ig +'/' + obj.Photo);
						$('.ew-main-photo').attr('src',obj.Photo);
							

                    	$('.ew-Code').text(' ');

						$('.ew-Remaining').attr('data',obj.remain).html(obj.text.Inventory+' : '+obj.remain);


                    }else {
                    	// ยังไม่มี item (สร้างใหม่)
                    	$('body').find('.ew-Validate').attr('data-key',obj.item).text(obj.desc);
						//$('body').find('.ew-Validate-').text(obj.sent);
						
						$('#ew-render-itemno').attr('data-key',obj.itemid);

                    	$('body').find('input[name="Price"]').val(0).attr('placeholder',' ');
                    	//$('.ew-to-line').val(1);
                    	$('body').find('input[name="Discount"]').val(0);

                    	// Change Photo
               	 		$('.ew-main-photo').attr('src','images/icon/production-.png');

						$('.ew-Remaining').attr('data',obj.possible).html(obj.text.Inventory+' : '+obj.possible);

                    	//$('.ew-Remaining').attr('data','').html('');

						//calculateProduction();
                    }



					$('body').find('input').attr('disabled', false);

                    getUndefined($('#ew-gen-code').attr('ew-set-code'));
					 
                },
				error: function(request, status, err) {
					if (status == "timeout") {
						// timeout -> reload the page and try again	
						clearInterval(validateItemDesc(text));				 
						//window.location.reload(); //make it comment if you don't want to reload page
					} else {
						// another error occured  
						$.notify({
							// options
							icon: "fas fa-box-open",
							message: request + status + err
						  },{
							  // settings
							  placement: {
								from: "top",
								align: "center"
							  },
							  type: "warning",
							  delay: 3000,
							  z_index: 3000
						}); 
					}

					  
				}
            });
	}
	
	 

    function CreateBom(post)
    {

    	$.ajax({

                url:"index.php?r=Manufacturing/ajax/create-bom&test=true",
                type: "POST",
                data: {
					param:{
						post:post,
						desc:$.trim($('#ew-real-desc').text()),
						group:$('#ew-gen-code').attr('ew-id'),
						item:$('body').find('.ew-Validate').attr('data-key'),
						id: $('#ew-gen-code').attr('ew-id'),
						price: $('input[name="Price"]').val(),
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


    function getUndefined(e)
    {
    	var modal = '<div id="ew-Alert-Modal" class="modal  fade" role="dialog">';
    	 	modal += '<div class="modal-dialog">';


    	    modal += '<div class="modal-content">';
    	    modal += '  <div class="modal-header">';
    	    modal += '    <button type="button" class="close" data-dismiss="modal">&times;</button>';
    	    modal += '    <h4 class="modal-title">Not Found !</h4>';
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



 //NUMBER
	$('body').on('click','.btn-number',function(e){
    e.preventDefault();

    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $(this).closest('div').find("input[name='"+fieldName+"']");
    var currentVal = parseInt(input.val());

    if (!isNaN(currentVal)) {
        if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).attr('value',currentVal + 1);
				ChangeNumber($(input));
			}
			
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
			}
			
			

        }else if(type == 'minus') {

            if(currentVal > input.attr('min')) {

				input.val(currentVal - 1).attr('value',currentVal - 1);				
				if(currentVal - 1 <= 0){
					input.val(0).attr('value','0');
				}			
				ChangeNumber($(input));
            }

            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
			}
			
			

        }
    } else {
        input.val(1).attr('value',1);
		ChangeNumber($(input));


    }



});

$('body').on('focusin','.input-number',function(){
  $(this).data('oldValue', $(this).val());
});

$('div#PickItem-Modal').on('change','.input-number',function(){
	ChangeNumber($(this));
});

function ChangeNumber($this){
	minValue 			= parseInt($this.attr('min'));
	maxValue 			= parseInt($this.attr('max'));
	valueCurrent 		= parseInt($this.val());

	if (isNaN(valueCurrent)) {
		valueCurrent = 0;
	}
	 
	name 				= $this.attr('name');

	if(valueCurrent >= minValue) {
		$('body').find(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled')
	}else{
		//alert('Sorry, the minimum value was reached');
		$this.val('');	
	} 
			// alert('Sorry, the minimum value was reached');
			// $this.val($this.data('oldValue'));
	 
	if(valueCurrent > maxValue) {
		alert('Sorry, the maximum value was reached');
		$this.val($this.data('oldValue'));
	}else{
			$('body').find(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
	} 

	if(valueCurrent > 0){
		$this.attr('style','background-color:#ffcf47;');
	}else{
		$this.attr('style','background-color:#fff;');
	}

	CalulateFunction($this);

	let remain = $this.closest('div.ew-box-click').find('span.remain').attr('data-val');
	// console.log(valueCurrent);
	// console.log(remain);
	if(remain < valueCurrent){
		$this.closest('div.ew-box-click').find('span.remain').attr('style','background-color:red; padding:0px 2px 0px 2px; color:#fff;').addClass('blink');
		$this.closest('div.ew-box-click').find('span.remain').text('สินค้าหมด');
	}else{
		$this.closest('div.ew-box-click').find('span.remain').text(number_format((remain- valueCurrent),2)).removeClass('blink').attr('style',' ');
	}

}



$('body').on('keydown','.input-number',function(e){
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
         // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
         // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

