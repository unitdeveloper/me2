/*!
* EWIN
* v3.06.19 - 2018
* (c) Assawin.Thkch; MIT License
*/


$(document).click(function(e){
    // alert(e.target.id);
    if(e.target.id != 'ew-month-menu') {
        $("#ew-month-box").slideUp();

    }  
});

$('body').on('click','.ew-expand-wrapper',function(){
	//alert($(this).parent('div').attr('class'));
	$(this).parent('div').parent('div').children('div').children('div.ew-wrapper').slideToggle();

});


$('body').on('click','div[id="ew-tr-modal"]',function(){
    $('body').attr('style','overflow:hidden;'); 
    $('.ew-tracking-modal').modal('show');
    var $id = $(this).attr('data');
    //$('.ew-render-tracking-info').hide();
    $('.ew-tracking-body').show();
    $('.ew-render-tracking-info').html('<div class="text-center">'+
            '<i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>'+
            '<div class="blink"> Loading .... </div></div>');

    setTimeout(function(){ 
    	$('.ew-render-tracking-info').hide();

    	$.ajax({ 

	        url:"index.php?r=tracking/sales/track-modal",
	        type: 'POST', 
	        data: {id:$id},
	        async:true,
	        success:function(getData){
	             
	            $('.ew-render-tracking-info').html(getData).slideToggle( "slow" );
	            
	           
	        }
	    });
    	

    }, 1000);
    


})


$('body').on('click','.ew-tracking-close-modal',function(){
	$('.ew-tracking-body').slideToggle();
 	$('.ew-render-tracking-info').html('<div class="text-center">'+
            '<i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>'+
            '<div class="blink"> Loading .... </div></div>');

 	setTimeout(function(e){ 
            $('.ew-tracking-modal').modal('hide');
    }, 350);
     
   $('body').attr('style','overflow:auto;');   
    


})


