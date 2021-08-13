<?php

use yii\helpers\Html;


 


?>
 <div class="sale-event-sale-line" ng-init="Title='<?=$this->title?>'" >

  <div class="row" style="margin-top:10px; margin-bottom: 10px;">
    <div class="col-xs-12 col-sm-6 text-right pull-right">
      <h4>ค้นหาบาร์โค๊ดสินค้าที่ต้องการ</h4>
      <div class="input-group margin pull-right" style=" "> 
        <span class="input-group-addon" id="basic-addon3"> </span>
        <input type="text" name="barcode" class="form-control"  placeholder="<?=Yii::t('common','Barcode');?> "/>
        <span class="input-group-btn">
          <button type='button' name='search' class="btn btn-default-ew btn-flat"><i class="fa fa-search"></i></button>
        </span>
      </div>    
      
    </div>
  </div>


    <div class="row">
      <div class="col-md-10 pull-right font-roboto">

        <div class="show-item-filter"></div>

      </div>
    </div>

</div>



<?php
 

$js =<<<JS

 

    
 
    const findBarcode = (obj, callback) =>{
      
      if(obj.code.length < 3){
        $('body').find('.input-group-addon').html('อย่างน้อย 3 ตัวอักษร');
        $('body').find('.show-item-filter').html('');
        $('body').find('input[name="barcode"]').focus();
        
      }else if(obj.code.length >= 3){
        $('body').find('.input-group-addon').html('<i class="fas fa-barcode"></i>');
        $('body').find('input[name="barcode"]').val('').attr('placeholder', obj.code);
        $('body').find('.show-item-filter').html('<div class="text-center" style="margin-top:50px;"><i class="fa fa-refresh fa-spin fa-2x"></i></div>');

        fetch("?r=items/items/barcode-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });

      } 
    }


    const reCalculate = (obj, callback) =>{
        fetch("?r=items/ajax/recalculate", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });
    }



    

    const showItemDetail = (obj) => {

      let showItem = ``;
      obj.raws.map((model, key) => {
      
          showItem+= `<div class="row item-row" data-key="` + model.id + `"  style="margin-top:0px; margin-bottom: 50px;">
                        <div class="col-sm-12">
                          <div class="panel panel-info">
                            <div class="panel-heading">
                              <h3 class="panel-title">
                                <span style="margin-left: -15px; padding: 10px; background: #8ff5bd;">`+ (key + 1) +`</span> <a href="?r=items/items/view&id=` + model.id + `" target="_blank">`+ model.code +`</a>
                                <span class="pull-right" style="margin-top: -5px;"> ` +(model.cust_name ? model.cust_name : 'สำหรับลูกค้าทั้งหมด') + `
                                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                <span>
                                
                              </h3>
                            </div>
                            <div class="panel-body">
                              <div class="col-sm-4">
                                    <img id="item-photo" src="`+ model.photo +`" class="img-thumbnail" />
                                </div>
                                <div class="col-sm-8">
                                    <div class="item-code"> </div>
                                    <div class="item-barcode" style="margin-top:20px; font-size:25px;">`+ model.barcode +`</div>
                                    <div class="item-article">`+ model.article +`</div>
                                    <div class="item-name" style="margin-top:20px; font-size:25px;">`+ model.name +`</div>
                                    <div><small class="text-gray">`+ model.name_ref + `</small></div>
                                    <div class="item-stock" data-id="` + model.id + `" style="margin-top: 36px; font-size: 16px;">
                                      <i class="fas fa-cubes"></i> <span class="text">`+ model.stock +`</span> 
                                      <span class="re-calculate pointer" style="margin-left: 20px; ">
                                        <small class="text-info"><u><i class="fas fa-refresh"></i> นับใหม่ </u></small>
                                      </span>
                                    </div>
                                </div>
                            </div>
                          </div>
                        </div>
                      </div>`;

      });


      

      if(obj.raws.length > 0 ){
       
        $('body').find('.show-item-filter').html(showItem);
      }else{
        $('body').find('.show-item-filter').html('<div><h3> ไม่มีสินค้านี้ </h3></div>')
        
      }
       
    }


 
    $('body').on('keydown', 'input[name="barcode"]', function(e){
       
      let code    = $(this).val();
      var keyCode = e.keyCode || e.which;
      if (keyCode === 13 || keyCode === 9){
          e.preventDefault();

          findBarcode({code:code}, res =>{
            showItemDetail(res);
          });
      }


    });

    $('body').on('click', 'button[name="search"]', function(e){
       
       let code = $('input[name="barcode"]').val();
 
        findBarcode({code:code}, res =>{
          showItemDetail(res);
        });
      
 
     });



    $(document).ready(function(){  
       $('input[name="barcode"]').focus();
 
    });

    

    $('body').on('click', '.re-calculate', function(){
      let el  = $(this);
      let id  = el.closest('.item-stock').attr('data-id');
                el.find('.fas').addClass('fa-spin');
      reCalculate({id:id}, res =>{
        
        let stock = res.raws.stock;
        el.closest('.item-stock').find('span.text').addClass('text-green').html(number_format(stock))
        el.find('.fas').removeClass('fa-spin');
      });
    })

    $('body').on('click','.btn-box-tool', function(){

      $(this).closest('.item-row').slideUp('slow');
      setTimeout(() => {
        $(this).closest('.item-row').remove();
      }, 3000);
     
    })
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>