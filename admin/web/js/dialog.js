$(document).ready(function(){
		//$('.panel-dialog').show("slide", { direction: "right" }, 1000);



		$('.maximize').hover(function(){
			$(this).attr('src','images/icon/maximize-.png');
		},function(){
			$(this).attr('src','images/icon/maximize.png');
		});


		

		$('.close-dialog').click(function(){
			$('.panel-dialog').hide("slide", { direction: "right" }, 500);
			$('body').attr('style','overflow: auto;');
			//$('.panel-dialog').slide();
		});






		$('body').on('click','.hide-dialog',function(){
			$(this).attr('class','show-dialog');
			$('body').attr('style','overflow: auto;');

			$('.panel-dialog').animate({"right": '0%'});
		    $('.panel-dialog').animate({"top":'6%'});

			$('.panel-dialog').animate({"width": '230px'});
			$('.panel-dialog').animate({"height": '50px'});

			
		   	$('.panel-body').fadeOut(800);
			$('.panel-footer').fadeOut(800);


			$('.panel-heading').attr('style','cursor: move;');

			$('.maximize').attr('class','minimize').attr('style','margin-top: -3px;');

		    

			$(".panel-dialog").draggable({
			      handle: ".panel-heading"
			 });
		});


		$('body').on('click','.show-dialog',function(){
			$(this).attr('class','hide-dialog');

			$('.panel-dialog').animate({"left": '0%'}, 200);
			$('.panel-dialog').animate({"top": '0%'});
		    fullScreen($(this));
		});


		$('body').on('dblclick','.heading-dialog',function(){
			fullScreen($(this));
		});


	});

	$('body').on('click','.maximize',function(){
		$(this).attr('class','minimize').attr('style','margin-top: -3px;');

		fullScreen($(this));

 

	});

	$('body').on('click','.minimize',function(){
		$(this).attr('class','maximize').attr('style','margin-top: -3px;');

		dialogBox($(this));

	})

function fullScreen($this)
{
	$('body').attr('style','overflow: hidden; position: relative; height: 100%;');

	$('.panel-dialog').animate({"width": '100%'});
    $('.panel-dialog').animate({"height": '100%'});

    $('.maximize').attr('class','minimize').attr('style','margin-top: -3px;');

   	$('.panel-body').fadeIn(2000);
	$('.panel-footer').fadeIn(2000);



	//$(".panel-dialog").draggable('disable');
}


function dialogBox($this)
{
	$('body').attr('style','overflow: hidden; position: relative; height: 100%;');
	
	$('.panel-dialog').animate({"top": '0%'});
	$('.panel-dialog').animate({"right": '0%'});

	$('.panel-dialog').animate({"width": '70%'});
    $('.panel-dialog').animate({"height": '100%'});


	
	
	$('.show-dialog').attr('class','hide-dialog');
	//$('.panel-heading').attr('style','cursor: move;');


 

	$('.panel-body').fadeIn(2000);
	$('.panel-footer').fadeIn(2000);
}