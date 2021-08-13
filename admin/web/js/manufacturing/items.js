$('#master_code').change(function(){
    $('#full_no').val($('#company').val()+'^'+$('#master_code').val());
    $.ajax({

            url:"index.php?r=ItemHasProperty/itemhas/ptview&pval=0&pid=0&Items_No="+ $('#full_no').val(),
            type: 'GET',
            data:"",
            async:true,
            success:function(getData){


                $(".property-info").html(getData);


            }
    })
});



$( "#addCat" ).click(function() {
    if($('#master_code').val() == ''){
        alert('Please input Item Code');
        return false;
    }
   $.ajax({

        url:"index.php?r=ItemHasProperty/itemhas/ptview&pval="+ $('#property-value').val() + "&pid="+ $('#property-id').val() + "&Items_No="+ $('#full_no').val(),
        type: 'GET',
        data:"",
        async:true,
        success:function(getData){


            $(".property-info").html(getData);


        }
    });

   $('#property-value').val('');


});

$('#property-value').keypress(function (e) {
    if (e.which == 13) {
      if($('#master_code').val() == ''){
          alert('Please input Item Code');
          $("#master_code" ).focus();
      }
      $.ajax({

          url:"index.php?r=ItemHasProperty/itemhas/ptview&pval="+ $('#property-value').val() + "&pid="+ $('#property-id').val() + "&Items_No="+ $('#full_no').val(),
          type: 'GET',
          data:"",
          async:true,
          success:function(getData){


              $(".property-info").html(getData);


          }
      });

     $('#property-value').val('');
     return false;
    }
  });


  $.ajax({

    url:"index.php?r=ItemHasProperty/itemhas/ptview&pval=0&pid=0&Items_No="+ $('#full_no').val(),
    type: 'GET',
    data:"",
    async:true,
    success:function(getData){


        $(".property-info").html(getData);


    }
})