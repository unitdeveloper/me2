function loadFindBox($div){
	// ----- find-price -----
    var $findDiv =  '<ew class="find-cust-box">'+
                    '<div class="find-cust" >'+
                    '   <div class="find-cust-render" > </div>'+
                    '   <div class="find-cust-load">'+
                    '       <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw text-info"></i>'+
                    '       <span class="sr-only">Loading...</span>'+
                    '   </div>'+
                    '</div>'+
                    '</ew>';

    $($div).append($findDiv);
}




function findCustomerTable($this){

	
    $('.find-cust').show('fast');
    $('.find-cust-load').fadeIn();


    $.ajax({
        url:"index.php?r=customers/ajax/find-cust",
        type:'POST',
        data:{word:$this.val()},
        async:true,
        success:function(getData){
            $('.find-cust-render').html(getData);

            setTimeout(function(e){ 
                $('.find-cust').slideDown('slow');
                $('.find-cust-load').fadeOut();
            },100);
            


        }
         
    })

}


$(document).click(function(e) {

  // check that your clicked
  // element has no id=info
  
  //alert($(e.target).closest('ew').attr('class'));
  if($(e.target).closest('ew').attr('class') != 'find-cust-box') {
    $(".find-cust").hide();

  }


});