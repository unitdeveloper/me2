 
<script>

  $('#form-sale-invoice').on('keyup keypress', function(e) {

  // Disable form submit on enter.
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
      e.preventDefault();
      return false;
    } 
  });

  $('body').on('click','.btn-info-ew',function(){
    //$('form').submit();
  });
  $(document).ready(function(){

    $('.ew-add-to-inv-line').hide();

    $('input,select').change(function() { 
           $('button[type="submit"]').attr('style','border:1px solid red;');
           $('.ew-confirm-post').attr('disabled','disabled').attr('class','btn btn-default');
           $('.ew-print-preview').attr('disabled','disabled').attr('class','btn btn-default').attr('href','#').attr('target','');
      }); 
  


    vatEvent($('#saleinvoiceheader-vat_percent'));

    // Modal load  
    $('#ew-add-series').on('shown.bs.modal', function () {

         
        var formIDmodal = $('.modal button[type=submit]').closest("form").attr('class');
         
        // $('.ew-save-modal-common').attr('onclick','$(\'#'+ formIDmodal +'\').submit()');

      $('button[type=submit]').hide();

      $('#numberseries-name').val('SaleInvoice');

      $('#numberseries-name').attr('disabled','disabled');

      }); 
 
 }); 


  $('body').on('click','.add-series-close-modal',function(){
    $('#ew-add-series').modal('hide');
  }); 

// $('body').on('keyup','#ew-add-series',function(event) {
$('body').keydown(function(event) {
 
  var keyCode = event.keyCode || event.which;
  //alert(keyCode);
  // if (keyCode === 8) {  // Back keyboard
  //   event.preventDefault();
  //    LoadSeries();
  //   return false;
  // }else 

  if (keyCode === 13) {  // Enter
    event.preventDefault();
     //AjaxFormPost();
    return false;
  }else if (keyCode === 27) { // Esc
    event.preventDefault();

      $('#ew-add-series').modal('hide');
      $('#ewSaleInvoiceModal').modal('hide');
      $('#ew-modal-source').modal('hide');
      
    return false;
  }

});

function CheckRuningNumberSeries(id,code)
{
  

             

    $.ajax({ 

        url:"index.php?r=series/ajax-find-noseries",
        type: 'GET', 
        data: {id:id,code:code},
        async:false,
        success:function(getData){
             
            if(getData)
            {
              $('#RunNoSeries').modal('toggle');

              $('.data-body').html(getData); 
              $('.modal-title').html('No Series'); 

            }
           
            
           
        }
    })   
}

function AjaxFormPost()
{

  var cond = $('ew.ew-condition').attr('ew-cond');
  var formClass = $('.modal button[type=submit]').closest("form").attr('class');


     

    var formpost = $('form.'+formClass+'').serializeArray();
    var appdata = { 
            form:formpost,
            name:$('#numberseries-name').val(),
            desc:$('#numberseries-description').val(),
            char:$('#numberseries-starting_char').val(),
            table:'vat_type',
            field:'vat_value',
            cond:cond,
    };

    $.ajax({ 

      url:"index.php?r=series/create-ajax",
      type: "POST", 
      data: appdata,
      async:false,
      success:function(getData){
          
          $('.ew-series-body').html(getData);
          //var obj = jQuery.parseJSON(getData);
           
           
          
      }
    });
}
 

$('body').on('click','.ew-save-modal-common',function(){

    AjaxFormPost();

});  

function LoadSeries()
{

  var cond = $('ew.ew-condition').attr('ew-cond');
  var data = {
      series:{
        form:'Invoice',
        value:cond,
      },
      table:{
        id:'1',
        name:'vat_type',
        field:'vat_value',
        cond:cond,

      }
      
  };


  route('index.php?r=series/create','GET',data,'ew-series-body');

  $('#numberseries-name').val('SaleInvoice');
  $('#numberseries-name').attr('disabled','disabled');
  $('button[type=submit]').hide();
}


$('body').on('click','#modal-back',function(){
  LoadSeries();

});
 
</script>
 