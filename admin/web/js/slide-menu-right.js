

$('body').on('click','#print-billing',function(){
	//$('.menu-right-slide').hide();
	//$('.menu-left-click').fadeIn('slow');
	//$('button.menu-buttun-click-on').html('<i class="fa fa-arrow-left"></i>');
	window.print();
});

$('body').on('click','button.menu-right-keep',function(){
	$('.menu-right-slide').hide("slide", { direction: "right" }, 400);

	$('.menu-left-click').fadeIn('slow');
	$('button.menu-buttun-click-on').html('<i class="fas fa-arrow-left"></i>');
	$(this).html('<i class="fas fa-sync-alt fa-spin text-info "></i>');
});

$('body').on('click','button.menu-buttun-click-on',function(){
	$('.menu-right-slide').show("slide", { direction: "right" }, 400);

	$('.menu-left-click').fadeOut('slow');
	$('button.menu-right-keep').html('<i class="fas fa-arrow-right"></i>');
	$(this).html('<i class="fas fa-sync-alt fa-spin   "></i>');
});


 
 
 
$(window).scroll(function(event){
   // var st = $(this).scrollTop();
   // if(st > 150){
   // 		//$('.menu-right-slide').css("position",'fixed').css("top",'0');
   // 		//$('.menu-right-slide').animate({"height": '100%'});

   // 		//# $('.menu-right-slide').animate({"top": '0%'});

   // 		//# $('.menu-left-click').css("position",'fixed').css("top",'0');
   // }else {

   // 		//$('.menu-right-slide').css("position",'absolute').css("top",'137px');
   // 		//$('.menu-right-slide').animate({"height": '100%'});

   // 		//$('.menu-left-click').css("position",'absolute').css("top",'137px');
   // }

});