	
$(document).ready(function(){

	// Load chat-tracking
	var chatDive = $('.chat-tracking');
	$.ajax({
		url: 'index.php?r=ajax/chat-module&id='+chatDive.attr('data')+'&typeofdoc='+String(chatDive.attr('data-text')),
		success:function(getData){
			chatDive.html(getData);
		}
	});



	
});
 

$('body').on('click','.icon-choice',function(){
    $('.emojicon').slideToggle();   
})

$('body').on('click','.emojicon-click',function(){
    $('.emojicon').slideToggle();
    $('input.text-emojicon').val($(this).attr('data'));
    $('.icon-choice').html($(this).html());
})

$('body').on('keyup','input.send-message',function(){
	$('.emojicon').slideUp();  
	if($(this).val()=='card'){
		$('input.text-emojicon').val(1);
		$('.icon-choice').html('<i class="fa fa-credit-card text-danger" aria-hidden="true"></i>');
		$('input.send-message').val('');
	}

	if($(this).val()=='?'){
		$('input.text-emojicon').val(3);
		$('.icon-choice').html('<i class="fa fa-question text-danger" aria-hidden="true"></i>');
		$('input.send-message').val('');
	}

	if($(this).val()=='clock'){
		$('input.text-emojicon').val(4);
		$('.icon-choice').html('<i class="fa fa-clock-o text-danger" aria-hidden="true"></i>');
		$('input.send-message').val('');
	}

	if($(this).val()=='!'){
		$('input.text-emojicon').val(5);
		$('.icon-choice').html('<i class="fa fa-info-circle text-danger" aria-hidden="true"></i>');
		$('input.send-message').val('');
	}

	if($(this).val()=='time'){
		$('input.text-emojicon').val(6);
		$('.icon-choice').html('<i class="fa fa-hourglass-half" aria-hidden="true"></i>');
		$('input.send-message').val('');
	}
})
	
function AjaxChatSubmit($form,$destination){

	var form = $form;
	var formData = form.serialize();
	var chatDive = $('.chat-tracking');
	$.ajax({
	  url: 'index.php?r=ajax/chat-module&id='+chatDive.attr('data')+'&typeofdoc='+String(chatDive.attr('data-text')),
	  type: form.attr("method"),
	  data: formData,
	  success: function (getData) {
	      $($destination).html(getData);	      
	  },
	  error: function () {
	      alert("Something went wrong");
	  }
	});

}
	
	
