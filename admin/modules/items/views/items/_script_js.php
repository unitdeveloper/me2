<?php
//$Actions = Yii::$app->controller->action->actionMethod;
# actionIndex,actionView, actionUpdate, actionCreate

?>
<style type="text/css">
	.items-create
	{

	}
	.ew-content
	{
		/*margin-top: 45px;*/
		/*border:1px solid rgba(1,1,1, .5); */
       

	}
	.ew-content a
	{
		margin:4px 4px;
	}
	.ew-menu-itemgroup
	{
        /* display:none; */
        margin-top:20px;
	}
	.ew-radio
	{
		cursor: pointer;
	}
	.ew-panel-body
	{
        display:none;
		/*max-height: 400px;
		overflow-y: scroll;*/
	}
	.ew-selected
	{
		display: block; 
        width: 100%; 
        text-align:left;
	}
	#ew-panel-header
	{
		font-weight: bold;
	}
	#ew-sub-header
	{
		cursor: pointer;
		color: #00a65a;

	}
	.ew-ul-itemgroup{
		margin:0 0 2px -35px;


	}
	.ew-ul-itemgroup li
	{
		list-style-type: none;
		padding:0 0 5px 0;
	}

	.property-sort li{
		list-style-type: none;
		margin-left: -35px !important;
	}

    #source-picker-modal .modal-dialog  {width:80%;}
    @media screen and (max-width: 768px) {
        #source-picker-modal .modal-dialog  {width:98%;}
        #source-picker-modal .modal-body  {max-height: calc(100vh - 210px);overflow-y: auto;}
    }

    .product-name-popup{     
        display:none;   
        height: 400px;
        width:100%;        
        padding-right:30px; 
        margin-top:-15px;
        position:absolute;        
        z-index:100;
    }
    .product-name-popup .product-popup{
        border:1px solid #bfbfbf;
        background-color:#fdfdfd;
        height: 100%;
        width:100%;
        margin-right:15px;
        overflow-y: auto;
        overflow-x:hidden;
        box-shadow: 2px 3px 2px rgba(0,0,0,.1);
    }

    .product-name-popup .product-popup-close{
        position:absolute;
        right:40px;
        padding:10px;
    }

</style>

<?php
	# actionIndex,actionView, actionUpdate, actionCreate
	// if($Actions == 'actionCreate')
	// {
	// 	//echo "<script>$('.items-create').hide('nomal');</script>";
	// }else{
    //     echo "<script>$('.ew-menu-itemgroup').fadeIn();</script>";
    // }


?>


<?php
$Yii    = 'Yii';
$js=<<<JS
  $('#sortable').sortable({
		update: function(e,ui){
           var lis = $('#sortable li i');
           var ids = lis.map(function(i,el){
           		return {id:el.dataset.key, priority:el.dataset.prop, propId:el.dataset.id}
            }).get();
           //console.log(JSON.stringify(ids));
					 $.ajax({
						 url:'index.php?r=items/ajax/update-priority&id='+$('form#items-form').attr('data-key'),
						 type:'POST',
						 data:{ids:ids},
						 success:function(data){
							 $('.Navi-Title').html(data);
						 }
					 });


         }
    });
    

    $('.bin-remove').click(function(){
        if (confirm('{$Yii::t('common','Are you sure you want to delete this item?')}')) {           
            var div = $(this).attr("data-id");
            var el  = $(this);
            $.ajax({    
                url:"index.php?r=items/items/delpic&id="+ $('#full_no').val() + "&pic=" + $(this).attr("data-id"),
                type: 'GET',
                async:true,
                success:function(getData){  
                    $('.'+ div).attr('src', 'images/nopic.png');  
                    el.children('span').html(' '); 
                }
            });    
        }
        return false;
    });



	$( document ).ready(function() {
        // Set Button Save
        $('.ew-save-common').attr('onclick',"$('#items-form').submit()"); // action_script.js ไม่เจอค่า จึงต้องมาตั้งที่นี่

	    var ShowText = $('input[name=ItemGroup]:checked', '.ew-menu-itemgroup').attr('ew-input-desc');
		$('#ew-sub-header').html(ShowText);
		$('.ew-panel-body').hide();

        var data = { param:{
            id:$('#items-itemgroup').val(),
            itemno:$('#full_no').val(),
            itemid: $('form#items-form').attr('data-id')
        }};
        route('index.php?r=ajax/json-load','GET',data,'ew-property-query');
        //$('.ew-menu-itemgroup').fadeIn();


        //Load Item Cross Reference
        getReference();
        
         
	});





	$('.ew-href').on('click', function(event) {
		$('.ew-content').hide('nomal');
        $('.ew-content').show('slow');
        
        //console.log($(this).attr('ew-data'));
		var data = { param:{
	    				id:$(this).attr('ew-data'),
	    				}
	    			};
	    route('index.php?r=ajax/item-group-child','POST',data,'ew-menu-itemgroup');



		// Send parameter 'id' to ew-data div
		$('#ew-panel-header').attr('ew-data',$(this).attr('ew-data'));
		$('#items-category').val($(this).attr('ew-data'));


	    // Send parameter 'name' to div
	    $('#ew-panel-header').hide().html($(this).attr('href').substr(1) + '<span id="ew-sub-header"></span>').fadeIn('slow');


	    $('.items-create').hide('nomal');
        $('.ew-panel-body').show();
        $('.ew-menu-itemgroup').show();
        
	});



    // Header Menu / Main menu
	$('body').on('click','#ew-sub-header',function(){
		$('.ew-panel-body').toggle('fast');
		$('.items-create').toggle('nomal');

	});

    // Item Group   / Sub menu
    $('body').on('click','.ew-selected',function(){
        $('#ew-panel-header').attr('ew-data',$(this).attr('ew-radio-data'));
        $('#ew-sub-header').hide().html(' > ' + $(this).attr('ew-desc')).fadeIn('fast');

        $('.ew-panel-body').hide('nomal');
        $('.items-create').show('fast');
        $('.ew-content').children('a').remove();
        $('#itemgrous-list').collapse('hide');

    });

    // Item Group   / Sub menu
    $('body').on('click','.ew-radio',function(){

        $('#items-itemgroup').val($(this).attr('ew-radio-data'));
        var data = { param:{
            id:$(this).attr('ew-radio-data'),
            property:$(this).val(),
            itemno:$('#full_no').val(),
            itemid: $('form#items-form').attr('data-id')
        }};
        route('index.php?r=ajax/json-load-property','GET',data,'ew-property-query');
    });


    $('body').on('change','.ew-ajax-save',function(){
        var param = { param: {
            property:$(this).val(),
            itemno:$('#full_no').val(),
            itemid: $('form#items-form').attr('data-id')
        }};

        route('index.php?r=ItemHasProperty/itemhas/ajax-create&pval='+ $(this).val() + '&pid='+ $(this).attr('ew-pt-id') + '&Items_No='+ $('#full_no').val(),'GET',param,'property-info');
    });





    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.img-added')
                    .attr('src', e.target.result)
                    .width(220)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    function small1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.small1')
                    .attr('src', e.target.result)
                    .width(60)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    function small2(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.small2')
                    .attr('src', e.target.result)
                    .width(60)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    function small3(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.small3')
                    .attr('src', e.target.result)
                    .width(60)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    function small4(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.small4')
                    .attr('src', e.target.result)
                    .width(60)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    function small5(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.small5')
                    .attr('src', e.target.result)
                    .width(60)
                    .height('auto');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready( function () {
        $('#items-barcode').attr('maxlength','13');

        // var dataMeasure = [];
        // setTimeout(() => {
        //     $.ajax({
        //         url:'?r=measure%2Fmeasure%2Fget-measure',
        //         type:'GET',
        //         dataType:'JSON',
        //         async:true,
        //         success:function(response){
        //             console.log(response);
        //         }
        //     });
        // }, 1000);
    });


    // window.onload = function() {
    //   document.getElementById('items-barcode').focus();
    // };

    $('#items-barcode').change(function() {

        var barcode = $('#items-barcode').val();
        if(barcode.match(/^\d+$/)) {
            $('#myValidate').modal('show');
        }

        $.ajax({

            url:'index.php?r=ajax/checkbarcode&id=' + $('#items-barcode').val(),
            type: 'POST',
            data:'',
            async:false,
            success:function(getData){
                 $('.data-validate').html(getData);                 
            }
        });
    });

    $('#items-barcode').keypress(function (e) {
      var barcode = $('#items-barcode').val();
      var len = $(this).val().length;
      if (e.which == 13) {
            if(len > 10){


                //$('#master_code').focus();
                //$('#items-description_th').focus();


                if(barcode.match(/^\d+$/)) {
                    $('#myValidate').modal('show');
                }

                $.ajax({

                    url:'index.php?r=ajax/checkbarcode&id=' + $('#items-barcode').val(),
                    type: 'POST',
                    data:'',
                    async:false,
                    success:function(getData){
                         $('.data-validate').html(getData);
                    }
                });
            }
            return false;

      }
    });

    // $('#master_code').keypress(function (e) {

    //   if (e.which == 13) {
    //     $('#items-description').focus();
    //     $('#myValidate').modal('toggle');

    //   }

    // });


    $('body').on('change','#items-brand_logo',function(){
        $('img.brand-logo').fadeOut('fast');
        $('img.brand-logo').attr('src',$(this).val());
        $('img.brand-logo').fadeIn('fast');
    });



    //-----Item Cross Reference-----
    function getReference(){
        let id = parseInt($('body').find('form#items-form').attr('data-id'));
        $.ajax({
            url:'index.php?r=items/ajax/item-cross-reference',
            type:'POST',
            data:{no:btoa($('form#items-form').attr('data-id')), id:id},
            dataType:'JSON',
            success:function(getData){
                $('.render-reference').html(getData.value.html);
            }
        });
    }
    //----/.Item Cross Reference-----


    //-----SOURCE PICKER-----
    var myrow = 0;
    $('body').on('click','.btn-source-picker',function(){
        $('#source-picker-modal').modal('show');
        myrow = $(this).closest('tr').data('key');
        searchCustomer('');
    })
    $('body').on('click','#ew-search-cust-btn',function(){
        searchCustomer($("#ew-search-cust-text").val());
    })
    $('body').on('click','#ew-pick-customer',function(){
        $('#source-picker-modal').modal('hide');
        var id = $(this).attr('ew-val');         
        $.ajax({
            url:'index.php?r=customers/ajax/json-get-customer&id='+id,
            type:'GET',
            dataType:'JSON',
            success:function(response){                 
                $('tr[data-key='+myrow+']').find('input[name=source_id]').val(id);
                $('tr[data-key='+myrow+']').find('input[name=source]').val(response.name);
                $('tr[data-key='+myrow+']').find('input[name=source]').attr('style','border-color:green;');

                 
                createReference(myrow);
            }
        });      
    })   
    function searchCustomer(word){        
        $.ajax({
            url:'index.php?r=customers/customer/pick-customer&search='+word,
            type:'GET',
            data:{no:btoa($('form#items-form').attr('data-key'))},
            success:function(getData){
                $('#source-picker-modal .modal-body .render-refernce').html(getData);
            }
        });
    }    
    //-----/.SOURCE PICKER-----


    
    





    //-----MEASURE PICKER-----
    $('body').on('click','.btn-measure-picker',function(){
        $('#measure').modal('show');
        myrow = $(this).closest('tr').data('key');
        searchMeasure('');
    })    
    $('body').on('click','.event-measure-selected',function(){
        $('#measure').modal('hide');
        $('tr[data-key='+myrow+']').find('input[name=measure_code]').val($(this).data('key'));
        $('tr[data-key='+myrow+']').find('input[name=measure]').val($(this).data('desc'));
        $('tr[data-key='+myrow+']').find('input[name=measure]').attr('style','border-color:green;');  
    })  
    function searchMeasure(word){        
        $.ajax({
            url:'index.php?r=items/ajax/pick-measure&search='+word,
            type:'GET',
            data:{no:btoa($('form#items-form').attr('data-key'))},
            dataType:'JSON',
            success:function(getData){
                $('#measure .modal-body').html(getData.html);
            }
        });
    } 
    //-----/.MEASURE PICKER-----


    //-----REFERENCE CHANGE-----
    $('body').on('change','input[name=ref-description],input[name=ref-item],input[name=ref-barcode]',function(){        
        myrow = $(this).closest('tr').data('key');
        createReference(myrow);
        
    }) 

    function createReference(myrow){

        var data = {
            line:myrow,
            type:$('tr[data-key='+myrow+']').find('select[name=refer_type]').val(),
            ref:$('tr[data-key='+myrow+']').find('input[name=source_id]').val(),
            barcode:$('tr[data-key='+myrow+']').find('input[name=ref-barcode]').val(),
            mastercode:$('tr[data-key='+myrow+']').find('input[name=ref-item]').val(),            
            no:btoa($('form#items-form').attr('data-key')),
            desc:$('tr[data-key='+myrow+']').find('input[name=ref-description]').val(),
            unit:$('tr[data-key='+myrow+']').find('input[name=measure_code]').val(),
            item:$('form#items-form').attr('data-id')
        }
        $.ajax({
            url:'index.php?r=items/ajax/create-reference',
            type:'POST',
            data:data,
            dataType:'JSON',
            success:function(response){
                if(response.status==200){
                    getReference();
                }else if(response.status==201){
                    $.notify({
                        // options
                        message: '{$Yii::t('common','Already exists.')}'
                    },{
                        // settings
                        type: 'warning',
                        delay: 3000,
                    });
                    getReference();
                    
                }else if(response.status==202){
                    $.notify({
                        // options
                        message: '{$Yii::t('common','The system has been updated.')}'
                    },{
                        // settings
                        type: 'success',
                        delay: 3000,
                    });
                    getReference();
                }else{
                    alert(response.message);
                }
                
            }
        })
    }
    //-----/.REFERENCE CHANGE-----
    
    //-----REFERENCE DELETE-----
    $('body').on('click','.delete-ref-line',function(){        
        myrow = $(this).closest('tr').data('key');
        if(confirm('{$Yii::t('common','Are you sure you want to delete this item?')}')){
            deleteReference(myrow);
        }
       
        
    }) 
    function deleteReference(key){
        $.ajax({
            url:'index.php?r=items/ajax/delete-reference',
            type:'POST',
            data:{key:key},
            dataType:'JSON',
            success:function(response){
                if(response.status==200){
                    getReference();
                }else{
                    alert(response.message);
                }                
            }
        })
    }
    //-----/.REFERENCE DELETE-----


    $('body').on('click','.product-popup-close',function(){
        $('.product-name-popup').toggle("slide", { direction: "up" }, 100);
    });

    $(document).click(function(e){
        //console.log(e.target.className); 
        if($(e.target).hasClass('form-control')) {
            $('.product-name-popup').slideUp('fast');            
        }
    });


    $('body').on('change','#master_code',function(){
        //e.preventDefault();
        let data = $(this).val();
        console.log(data);
    });
    
JS;

$this->registerJS($js,\yii\web\View::POS_END);
?>
