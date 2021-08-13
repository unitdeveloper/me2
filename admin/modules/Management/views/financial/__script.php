
<?php
$Yii = 'Yii';
$js=<<<JS


$(function(){
    $('#chequeModal').draggable({
      handle: '.modal-header'
  });
})

$('body').on('keyup','.dataCalc',function(){

  var subTotal  = $('.text-sumVal').attr('data') * $(this).val() /100;
  var total     = subTotal + ($('.text-sumVal').attr('data') *1);
  $('.dataTotal').val(number_format(total),2);

});


  

  function EditCheque(id){

    $('.modal-body').hide('slow');
    $('div.loading-content').show();
    $('div.ew-body-cheque').html('<br><br><br><br>');

 

    setTimeout(function(e){ 
      $.ajax({ 

            url:'index.php?r=accounting/cheque/update',
            type: 'GET', 
            data: {id:id},
            async:false,
            success:function(getData){
                

                $('div.loading-content').hide();
                $('div.ew-body-cheque').html(getData);
                $('.modal-body').slideToggle('slow');

                

            }

      });
    }, 1000); 
  }


  $('body').on('click','a.view-receipt',function(){
    $('#chequeModal').modal('show'); 
    

    EditCheque($(this).attr('data'));
    $('.post-cheque').show();
    $('.getInv').attr('class','btn btn-warning post-cheque').html('<i class="fa fa-save" ></i> {$Yii::t("common","Save")}'); 

  });

  

  $('body').on('click','.close-modal-cheque',function(){
    window.location.reload();
  });


	$('body').on('click','.getInv',function(){
		getFilterPostedInv();
    
    //$(this).children('i').attr('class','fa fa-save');
	});


  $('body').on('click','button.post-cheque',function(){

    $('form[id="form-posted-inv"]').submit();

    $('button[data-dismiss="modal"]').addClass('close-modal-cheque'); 
  
  });

	function getFilterPostedInv()
  {

    var obj = [];

    if($('.ew-checked:checked').serialize()!='')
    {

        $('tr').each(function(i, el) {

          var iCheck = $(el).children('td').find('input.ew-checked');

          if (iCheck.is(':checked')) {
             obj.push({id:iCheck.attr('row-data'),cust:iCheck.attr('data'),bal:iCheck.attr('bal'),status:iCheck.attr('status')});
          }
          

        });

        //console.log(obj);
        renderChequeForm(obj);

      
    }else{
      
      swal(
        lang('common','Please select one of the options.'),
        lang('common','That thing is still around?'),
        'warning'
      );

      return false;
    }

  }

  function renderChequeForm(obj){

		
		$('.modal-body').hide('slow');
		$('div.loading-content').show();
		$('div.ew-body-cheque').html('<br><br><br><br>');

		setTimeout(function(e){ 
			$.ajax({ 

			      url:'index.php?r=accounting/cheque/create',
			      type: 'POST', 
			      data: {data:obj},
			      async:false,
			      success:function(getData){
			          

			          $('div.loading-content').hide();
			          $('div.ew-body-cheque').html(getData);
			          $('.modal-body').slideToggle( 'slow' );

                $('.getInv').attr('class','btn btn-info post-cheque').html('<i class="fa fa-save" aria-hidden="true"></i> {$Yii::t("common","Save")}');
                
			      }

			});
		}, 1000); 
  }



	$('body').on('click','.open-modal',function(){

		$('#chequeModal').modal('show'); 

		loadCheque($(this).attr('row-data'),$(this).attr('data'));
    $('.post-cheque').show();
    $('.post-cheque').attr('class','btn btn-success-ew getInv').html('<i class="fa fa-check" aria-hidden="true"></i> {$Yii::t("common","Select")}');

     

	});




	function loadCheque(id,cust)
	{
	  $('div.ew-body-cheque').html('<br><br><br><br>');	
	  $('.modal-body').hide();
	  $('div.loading-content').show();
	  setTimeout(function(e){ 
	    $.ajax({ 

	          url:'index.php?r=accounting/cheque/posted-inv-list',
	          type: 'GET', 
	          data: {id:id,cust:cust},
	          async:false,
	          success:function(getData){
	              

	              $('div.loading-content').hide();
	              $('div.ew-body-cheque').html(getData);
	              $('.modal-body').slideToggle( 'slow' );



	          }

	    });
	  }, 1000); 
	}


  $('body').on('click','.ew-delete-cheque',function(){
        if (confirm(lang('common','Do you want to confirm ?'))) { 
            var id = $(this).attr('data');
            $.ajax({ 

                url:'index.php?r=accounting/cheque/delete&id='+id,
                type: 'POST', 
                data: {id:id},
                async:false,
                success:function(getData){
                    

                     
                    $('div.ew-body-cheque').html(getData);

                    setTimeout(function(e){ 

                      $('#chequeModal').modal('hide'); 
                      window.location.reload();
                    }, 1000); 
                     

                     
                }

          });
        }
     })
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>

