 



 


 
<script>


    $(document).ready(function(){

      if(Number($('#ew-total').attr('data')) <= 0)
      {
        $('.ew-confirm-post').hide();
      } 


      // Modal load  
      $('#ewPickCustomer').on('shown.bs.modal', function () {

         
        $('a[id="ew-pick-customer"]').attr('id','ew-pick-inv-customer');  

        
            
       }); 

       // Modal load  
       $('#ewGetItemModal').on('shown.bs.modal', function () {

          

          $('tr').each(function(i, el) {
            $(el).children('td').find('#ew-qty').fadeOut();
            $(el).children('td').find('#ew-price').fadeOut();
          }); 

       }); 
      

    });






    $('body').on('click','.close-modal',function(){
      $('#ew-modal-source').modal('hide');
    });

    $('body').on('click','a[id="ew-get-source"], #ew-add-new-line',function(){
        //alert('test');  


        $('#ew-modal-source').attr('style','z-index: 2000;');

        $('#ew-modal-source').modal('show');

        

        var customer = $('input[id="saleinvoiceheader-cust_no_"]').val();

        if(customer==='')
        {
          customer = $('ew[id="customerid"]').attr('data');
        }

        var OrderId = $('#SaleOrder').attr('ew-so-id');

        var inv_no = $('.ew-inv-no').attr('ew-no');

        var data = {
            id:inv_no,
            cust:customer,
            SaleOrder:OrderId,
          };

        route('index.php?r=accounting/ajax/json-get-source','POST',data,'ew-source-body');
        
        $('#ew-search-cust').attr('id','ew-search-ship');
        $('#ew-search-cust-btn').attr('id','ew-search-ship-btn-btn');
    });



    function FilterShipment(search,customer)
    {
      

      var inv_no = $('.ew-inv-no').attr('ew-no');

      var data = {
          id:inv_no,
          cust:customer,
          search:search,
        };

      route("index.php?r=accounting/ajax/json-get-source",'POST',data,'ew-source-body');
      

    }

    $('body').on('click','.ew-clear-filter',function(){
      FilterShipment('','');
    });

    $('body').on('change','#ew-search-ship-btn',function(){ 
      FilterShipment($('#ew-search-ship').val(),'');
    });

    $('body').on('change','#ew-search-ship',function(){ 

      FilterShipment($(this).val(),'');
      // var customer = $('input[id="saleinvoiceheader-cust_no_"]').val();

      // if(customer=='')
      // {
      //   customer = $('ew[id="customerid"]').attr('data');
      // }

      // var inv_no = $('.ew-inv-no').attr('ew-no');

      // var data = {
      //     id:inv_no,
      //     cust:customer,
      //     search:$(this).val(),
      //   };

      // route("index.php?r=accounting/ajax/json-get-source",'POST',data,'ew-source-body');

    });

    $('body').on('change','.ew-checked',function(){
     
        // if(this.checked) {
        //     FilterShipment('',$(this).attr('cust'));
        //     $('.ew-checked').attr('checked','checked');
        //     $('.ew-checked').attr('class','ship');
        // }
    });

    $('body').one('click','.ew-get-ship',function(){
      //var input = $("input[type='checkbox']").attr('data');
      //console.log(input);
      //$('.ew-render-getsource').html(input);
      //alert($('.ship:checked').serialize());
      // var data = { post:$('.ship:checked').serialize(),
      //              param:'',
      //            };

      if($('.ship:checked').serialize()!='')
      {

        var ship = $('.ship:checked').serialize(); // Test data
        
        var inv_no = $('.ew-inv-no').attr('ew-no');

        var data = {
            ship:$('.ship:checked').serializeArray(),
            id:inv_no,
          };

        $.ajax({
            url: "index.php?r=accounting/ajax/json-post-source",
            type: "post",
            data: data,
            success: function(dataData) {


              $('.ew-render-getsource').html(dataData);

              // เมื่อเลือกรายการเสร็จแล้ว ให้ปิดตัวเอง
              // จากนั้น ทำการ update ใบ ​Invoice 
              $('#ew-modal-source').modal('hide');


              // Update Invoice
               
            }
        });

         
        
      }else{
        //alert('<?=Yii::t('common','Please select one of the options.')?>');
        swal(
          '<?=Yii::t('common','Please select one of the options.')?>',
          '<?=Yii::t('common','That thing is still around?')?>',
          'warning'
        );
        return false;
      }

    });


    function renderUpdateSaleInvoice(id)
    {
     

      $.ajax({ 

            url:"index.php?r=accounting/saleinvoice/update",
            type: "GET", 
            data: {id:id},
            async:true,
            success:function(getData){
                
               
              $('.ew-render-create-invlice').html(getData);
            }
        });

      
    }


    $('body').on('click','input[type="checkbox"]',function(){
      //alert($(this).attr('cust'));
      
      // var cust = Number($(this).attr('cust'));

      // $('input[type=checkbox]').each(function () {
      //     if(cust === Number($(this).attr('cust')))
      //     {
      //       $(this).attr('disabled', true);
      //     }
          
      // }); 

         
       
      
    });

    $('body').on('click','.ew-confirm-post',function(){
      //$('#ew-modal-confirm-post').modal('show');
      swal({
        title: '<?=Yii::t('common','Are you sure?')?>',
        text: "You won't be able to post this!",
        type: 'warning',
        showCancelButton: true,
        cancelButtonText: '<?=Yii::t('common','Cancel')?>',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?=Yii::t('common','Yes, Post!')?>'
      }).then(function () {
        swal(
          'Posted!',
          '<?=Yii::t('common','รายการถูกลงบัญชีแล้ว')?>',
          'success'
        ),
        //setInterval(invPost, 1100);
        setTimeout(function(){
            invPost();
        }, 1100);
        
      });

       




    });

 
    function invPost()
    {
      var id = $('input[id="saleinvoiceheader-id"]').val();
      route('index.php?r=accounting/ajax/ajax-post','POST',{id:id},'ew-confirm-body');
    }


    //---- Change Document No.-----
    $('body').on('click','.ew-inv-re-no',function(){

      //var data = $('.Navi-Title').text();
      var data = $('div.ew-inv-no').attr('ew-no_');
      var input = '<div class="col-sm-2" style="position:absolute; right:0px;">'+
                    '<div class="ew-type input-group"><input type="text" class="form-control ew-inv-inputno" value="'+data+'" id="ew-edit-no-text">'+
                      '<span class="input-group-addon ew-cancel-edit-no" style="cursor:pointer;" title="Cancel" alt="Cancel">X</span>'+
                    '</div>'+
                   '</div>';

      $('div.ew-inv-no').html(input);

      $('div.ew-inv-no').attr('class','ew-inv-change');

    });
    
    $('body').on('change','.ew-inv-change, .ew-cancel-edit-no',function(){

          var Dold = $(this).attr('ew-no_');
          var Dnew = $('input.ew-inv-inputno').val()

         if (confirm('Do you want to change "' + Dold + ' to ' + Dnew + '" ?')) {  
            var id  = $('input[id="saleinvoiceheader-id"]').val();
            

            $(this).attr('ew-no_',$('input.ew-inv-inputno').val());
            //route('index.php?r=accounting/ajax/ajax-change-invno','POST',{id:id,val:Dnew,old:Dold},'Navi-Title');

            $.ajax({ 

                  url:"index.php?r=accounting/ajax/ajax-change-invno",
                  type: "POST", 
                  data: {id:id,val:Dnew,old:Dold},
                  async:true,
                  success:function(getData){
                     
                    if (getData != Dold)
                    { 
                        var data = '<h4>' + getData + '<h4>';
                        $('.ew-inv-change').html(data); 
                        $('.ew-inv-change').attr('class','ew-inv-no');

                    } else {
                     
                      swal(
                        '<?=Yii::t('common','Already exists.')?> !',
                        '<?=Yii::t('common','Please try again.')?> '+
                        '<a href="index.php?r=accounting/posted/index" target="_blank" id="ew-show-posted"><i class="fa fa-link" aria-hidden="true"></i> Detail</a>',
                        'error'
                      );

                      var data = '<h4>' + Dold + '<h4>';
                      $('.ew-inv-change').html(data); 
                      $('.ew-inv-change').attr('ew-no_',getData);
                      $('.ew-inv-change').attr('class','ew-inv-no');
                    }
                       
                      
                  }
              });
            
         }

        // var data = '<h4>' + $(this).attr('ew-no_') + '<h4>';
        // $(this).html(data); 
        // $(this).attr('class','ew-inv-no');
        //$(this).attr('ew-no_',$('input.ew-inv-inputno').val());

        return false;
    });

    $('body').on('click','.ew-cancel-edit-no',function(){

         

        var data = '<h4>' + $('.ew-inv-change').attr('ew-no_') + '<h4>';
        $('.ew-inv-change').html(data); 
        $('.ew-inv-change').attr('class','ew-inv-no');
        

        return false;
    });
    //---- /. Change Document No.-----
 














    
 
    $(document).ready(function(){

        // On Load
         
        var invoice = Number($('.ew-inv-no').attr('ew-no'));
        //$('#saleinvoiceheader-source_id').val(customer);

        if(invoice > 0)
        {
          $.ajax({ 

                  url:"index.php?r=accounting/ajax/json-get-customer",
                  type: "GET", 
                  data: {id:invoice},
                  async:true,
                  success:function(getData){
                       
                      
                      var obj = jQuery.parseJSON(getData);
                       
                      //alert(obj.city);
                     
                      $('#saleinvoiceheader-cust_address').val(obj.address);
                      $('#saleinvoiceheader-cust_address2').val(obj.address2);

                      //$('#saleinvoiceheader-district').val(obj.district);

                      //$('#saleinvoiceheader-city').val(obj.city);
                       
                      //$('#saleinvoiceheader-province').val(obj.province);
                      $('#saleinvoiceheader-postcode').val(obj.postcode);
                      
                      getProvinceList(obj.postcode,obj.province);
                      getCityDefault(obj.city,obj.province);
                      getDistrictFromCity(obj.city,obj.district);
                      
                  }
              });
        }


    });


    $('#saleinvoiceheader-postcode').on('keyup keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13) { 
        e.preventDefault();
         findAutoPostCode($(this).val());
        return false;
      }
    });

    function findAutoPostCode(postcode)
    {

        //$('#saleinvoiceheader-district').val('');

        var postcode = postcode;
        //route('index.php?r=ajax/postcode-validate&postcode='+$(this).val(),'GET',{postcode:$(this).val()},'loading');
        $.ajax({ 

              url:"index.php?r=ajax/postcode-validate&postcode="+postcode,
              type: "GET", 
              data: {postcode:postcode},
              async:true,
              success:function(getData){
                 
                if(Number(getData) >= 1)
                {
                          getProvince(postcode);
                          getCity(postcode);
                          getDistrict(postcode);
                          //getDistrictFromCity($('#saleinvoiceheader-city').val());

                }else {

                  

                  swal(
                      '<?=Yii::t('common','No zip code of your choice.')?>',
                      '<?=Yii::t('common','Please re-enter your zip code.')?>',
                      'warning'
                    );
                }

               }
          });
    }

    $('body').on('change','#saleinvoiceheader-postcode',function(){
        

        //findAutoPostCode($(this).val());


        // var postcode = $(this).val();
        // //route('index.php?r=ajax/postcode-validate&postcode='+$(this).val(),'GET',{postcode:$(this).val()},'loading');
        // $.ajax({ 

        //           url:"index.php?r=ajax/postcode-validate&postcode="+$(this).val(),
        //           type: "GET", 
        //           data: {postcode:$(this).val()},
        //           async:false,
        //           success:function(getData){
                     
        //             if(Number(getData) >= 1)
        //             {
        //                       getProvince(postcode);
        //                       getCity(postcode);
        //                       getDistrict(postcode);

        //             }else {

                      

        //               swal(
        //                   '<?=Yii::t('common','No zip code of your choice.')?>',
        //                   '<?=Yii::t('common','That thing is still around?')?>',
        //                   'warning'
        //                 );
        //             }

        //            }
        //       });



        
    });

    $('body').on('change','#saleinvoiceheader-city',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');
        
         
        getDistrictFromCity($(this).val());

        
    });

    $('body').on('change','#saleinvoiceheader-province',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');
        
        // Clear Postcode;
        $('#saleinvoiceheader-postcode').hide(); 
        //getPostcodeFromDisrtict($(this).val());


         
        getCityFromProvince($(this).val()); 
        
        


        
    });
    $('body').on('change','#saleinvoiceheader-district',function(){
        //route('index.php?r=ajax/get-amphur','GET',{data:1},'');
        
        $('#saleinvoiceheader-postcode').show(); 
        getPostcodeFromDisrtict($(this).val());
         

        
    });
    function getCityFromProvince(province)
    {
        
        $.ajax({ 

            url:"index.php?r=ajax/city-from-province&province="+province,
            type: "POST", 
            data: {province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#saleinvoiceheader-city').html('');
                $.each( obj, function( key, value ) { 
                   
                    $('#saleinvoiceheader-city').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                    
                    
                 
                });

            }

        });

    }
    function getPostcodeFromDisrtict(discrict)
    {
        
        $.ajax({ 

            url:"index.php?r=ajax/postcode-from-discrict&discrict="+discrict,
            type: "POST", 
            data: {discrict:discrict},
            success:function(getData){
                 
                   
                $('#saleinvoiceheader-postcode').val(getData);
                
                
                 

            }

        });

    }

    function getDistrictFromCity(city,district)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-district-city&district="+district+"&city="+city,
            type: "GET", 
            data: {city:city},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#saleinvoiceheader-district').html('');
                $.each( obj, function( key, value ) {
                   
                   $('#saleinvoiceheader-district').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                });

                 

            }

        });
    }

    function getDistrict(postcode)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-tumbol&postcode="+postcode,
            type: "POST", 
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#saleinvoiceheader-district').html('');
                $.each( obj, function( key, value ) {
                   
                   $('#saleinvoiceheader-district').append($('<option></option>').val(value.val).html(value.text));
                });

                 

            }

        });
    }
 
    function getCityDefault(city,province)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-city-default",
            type: "GET", 
            data: {postcode:$('#saleinvoiceheader-postcode').val(),city:city,province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);
                $('#saleinvoiceheader-city').html('');
                $.each( obj, function( key, value ) {
                   
                   $('#saleinvoiceheader-city').append($('<option></option>').val(value.val).html(value.text).attr('selected',value.selected));
                   $("#saleinvoiceheader-city select").val(city);
                });

                


            }

        });
    }

    function getCity(postcode)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-city&postcode="+postcode,
            type: "POST", 
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#saleinvoiceheader-city').html('');
                $.each( obj, function( key, value ) {
                   
                   $('#saleinvoiceheader-city').append($('<option ></option>').val(value.val).html(value.text));
                });

                


            }

        });
    }

   function getProvinceList(postcode,province)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-province-list",
            type: "GET", 
            data: {postcode:postcode,province:province},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#saleinvoiceheader-province').html('');
                $.each( obj, function( key, value ) {
             
                    $('#saleinvoiceheader-province').append($('<option ></option>').val(value.val).html(value.text).attr('selected',value.selected));
                    $('option[value="maxlength"]').remove();

                });

            }

        });
    }

    function getProvince(postcode)
    {
        $.ajax({ 

            url:"index.php?r=ajax/get-province&postcode="+postcode,
            type: "GET", 
            data: {postcode:postcode},
            success:function(getData){
                var obj = jQuery.parseJSON(getData);

                $('#saleinvoiceheader-province').html('');
                $.each( obj, function( key, value ) {
             
                    $('#saleinvoiceheader-province').append($('<option ></option>').val(value.val).html(value.text).attr('selected',value.selected));
                    $('option[value="maxlength"]').remove();

                });

            }

        });
    }
    


    $('#saleinvoiceheader-postcode').on('keyup keypress', function(e) {

            //getProvince($('#saleinvoiceheader-postcode').val());
            //getCity($('#saleinvoiceheader-postcode').val());
            //getDistrict($('#saleinvoiceheader-postcode').val());

    });

    $('body').keydown(function(event) {
        if(event.which == 27) { // ESC

            //$('#ew-modal-Approve').modal('hide');

        }else if(event.which == 112) { // F1
            //alert('F1');        
            //$('.reject-reason #reason-text').focus();

        }else if(event.which == 113) { //F2
             
            //alert('F2');
        }
        else if(event.which == 114) { //F3
            // if (confirm('Create New Document?')) {      

            // window.location.replace("index.php?r=SaleOrders/saleorder/create");


            // }
            // return false;

        }
        else if(event.which == 116) { //F5
             
            //alert('Function Disable.');
        }else if(event.which == 118) { //F7
            //$('#ew-modal-Approve').modal('toggle');            
            //BtnApprove($('#ew-reject'));
        }
        else if(event.which == 121) { //F10
            //$('#ew-modal-Approve').modal('toggle');            
            //∂BtnApprove($('#ew-confirm'));

        }
        else if(event.which == 13) { // Enter

             
            // If Model Open
            // if($('.ew-confirm').is(':visible')){
                
                 

                 
                        
            
            // }
               

    

        }

        

    });
</script>
 