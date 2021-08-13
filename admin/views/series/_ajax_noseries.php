<?php

use yii\helpers\Html;
 
?>
 
<div class="">


<div class="col-md-12"><input type="button" value="Auto Generate" name="GenSeries" class="GenSeries btn btn-info"></div><br>
	 


 
</div>


<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10">
		<div class="resource"></div>
	</div>
	<div class="col-md-1"></div>
</div>



<script type="text/javascript">
	$('.no-se').on("change mouseout", function(event){
	   
	    var param = {
	    				txt:$(this).val(),
	    				id:$(this).attr('data'),
	    				fieldname:$(this).attr('id')
	    			}; 
	    $.ajax({ 

            url:"index.php?r=series/ajax_update",
            type: 'GET', 
            data: {param:param},
            async:false,
            success:function(getData){
                 
                $('.resource').html(getData); 
                 
                
               
            }
        }) 
	});

	$('.add-btn').click(function(){

	})
	
	// $('#start_no').change(function(){
	// 	alert('test');
	// })
	$('.GenSeries').click(function(){
		 
		 $.ajax({ 

            url:"index.php?r=series/ajax_autogenseries",
            type: 'GET', 
            data: {code:01,char:'<?=$code;?>',digit:'0000',NoSeries:<?=$id;?>},
            async:false,
            success:function(getData){
                 
                $('.resource').html(getData); 
                $('.GenSeries').hide(); 
                
               
            }
        }) 
	})

 
</script>
        