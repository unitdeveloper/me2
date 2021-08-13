 
<script>
  	


    $('body').on('click','#ew-modal-pick-cust',function(){
        $('#ewPickCustomer').modal('show'); 
        
        route("index.php?r=customers/customer/pick-customer",'GET',{search:'',id:$('#SaleOrder').attr('ew-so-id')},'ew-Render-Pick-Inv-Customer');

        //$('#ew-search-ship').attr('id','ew-search-cust');
        //$('#ew-search-ship-btn').attr('id','ew-search-cust-btn');
        
    });


    $('body').on('click','#ew-pick-inv-customer',function(){
        //alert($(this).attr('ew-val'));
        if (confirm('ยืนยันการเลือกลูกค้า ! ')) { 


            

            var customer = Number($(this).attr('ew-val'));

            $.ajax({ 

                  url:"index.php?r=customers/ajax/json-get-customer",
                  type: "GET", 
                  data: {id:customer},
                  async:false,
                  success:function(getData){
                       
                      
                      var obj = jQuery.parseJSON(getData);
                       
                     
                      
                      //console.log(getData); 

                      $('#saleinvoiceheader-cust_no_').val(customer);

                      $('#saleinvoiceheader-cust_code').val(obj.code);
                     
                      $('#saleinvoiceheader-cust_name_').val(obj.name);
                      $('#saleinvoiceheader-cust_address').val(obj.address);
                      $('#saleinvoiceheader-cust_address2').val(obj.address2);
                      $('#saleinvoiceheader-taxid').val(obj.vatregis);
                      $('#saleinvoiceheader-branch').val(obj.branch);

                      $('#saleinvoiceheader-contact').val(obj.contact);

                      $('#saleinvoiceheader-phone').val(obj.phone);
                       
                      $('#saleinvoiceheader-payment_term').val(obj.payment_term);

                      $('#saleinvoiceheader-postcode').val(obj.postcode);

                      $('#saleinvoiceheader-sales_people').val(obj.owner_sales);
                      
                      getProvinceList(obj.postcode,obj.province);
                      getCityDefault(obj.city,obj.province);
                      getDistrictFromCity(obj.city,obj.district);
                      
                  }
              });

            
            $('#ewPickCustomer').modal('hide');  
        }
        //return false();
       //$('#ew-modal-source').modal('hide');  
    });

    //------ Select Customer ---------
    $('body').on('change','#ew-search-cus-text',function(){ 
        //route("index.php?r=customers/customer/pick-customer",'GET',{search:$(this).val(),id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Customer');
        alert('test');
    });

    
    $('body').on('click','#ew-search-cust-btn',function(){ 
      route("index.php?r=customers/customer/pick-customer",'GET',{search:$('#ew-search-cust-text').val(),id:$('#SaleOrder').attr('ew-so-id')},'ew-Render-Pick-Inv-Customer');
      $('a[id="ew-pick-customer"]').attr('id','ew-pick-inv-customer');
    });


    $('body').on('click','.ew-inv-close-pic-cus',function(){
    	$('#ewPickCustomer').modal('hide'); 
    });
    //------/. Select Customer -------

</script>
 