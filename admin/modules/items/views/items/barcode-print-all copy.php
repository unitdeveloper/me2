<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Barcode');
$this->params['breadcrumbs'][] = $this->title;


$gridColumns = [
          [
              'contentOptions' => ['style' => 'width:50px;'],
              'class' => 'yii\grid\SerialColumn',
          ],
          [
            'contentOptions' => function($model){
              return ['id' => $model->barcode,'style' => 'min-width:100px;'];
            },
            'label' => Yii::t('common','Barcode'),
            'value' => function($model){
              $code = [
                      'elementId' => $model->barcode,
                      'value'     => $model->barcode,
                      'type'      =>'ean13',
                      ];
              $barcode =  $model->barcode ? \barcode\barcode\BarcodeGenerator::widget($code) : '';

              return $barcode;
            }
          ],



          [
              'label' => Yii::t('common','Master Code'),
              'value' => function($model){
                return $model->master_code;
              }
          ],

          [
            'label' => Yii::t('common','Article'),
            'value' => function($model){
              return $model->ref_code != '' ? $model->ref_code : '';
            }
          ],

          // [
          //   'label' => Yii::t('common','Image'),
          //   'format' => 'html',
          //   'value' => function($model){
          //     return Html::img($model->picture,['style' => 'width:50px;']);
          //   }
          // ],

          [
            'label' => Yii::t('common','Name'),
            'format' => 'raw',
            
            'value' => function($model){
              $html = $model->description_th == $model->ref_name 
                        ? $model->description_th 
                        : ('<div>'.$model->description_th.'</div><div>'.$model->ref_name.'</div>');
               
              return $html;
            }
          ],

          [
            'label' => Yii::t('common','Stock'),
            'format' => 'raw',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right'],
            'value' => function($model){
              $stock = $model->ProductionBom > 0
                        ? $model->last_possible 
                        : $model->last_stock;
               
              return number_format($stock);
            }
          ],

          
         

          //'description',

         // 'UnitOfMeasure',


];


?>
 <div class="sale-event-sale-line" ng-init="Title='<?=$this->title?>'" >

  <div class="row" style="margin-top:50px; margin-bottom: 10px;">
    <div class="col-xs-12 text-right">
      <div class="input-group margin pull-right" style=" width:400px;"> 
        <input type="text" name="barcode" class="form-control"  placeholder="<?=Yii::t('common','Barcode');?> "/>
        <span class="input-group-btn">
          <button type='button' name='search' class="btn btn-default-ew btn-flat"><i class="fa fa-search"></i></button>
        </span>
      </div>    
      
    </div>
  </div>


    <div class="row">
      <div class="col-md-12 font-roboto">

        <div class="show-item-filter"></div>

        <?php Pjax::begin(); ?>
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            // 'tableOptions' => ['class' => 'table   table-bordered table-hover'],
            'rowOptions' => function($model){
                 return ['data-id' => $model->id];
            },
            'columns' => $gridColumns,

        ]); ?>
        <?php Pjax::end(); ?>
      </div>
    </div>

</div>



<?php
 

$js =<<<JS

 

    
 
    const findBarcode = (obj, callback) =>{
      $('body').find('input[name="barcode"]').val('').attr('placeholder', obj.code);      
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
      obj.raws.map(model => {
      
          showItem+= `<div class="row " data-key="` + model.id + `"  style="margin-top:0px; margin-bottom: 50px;">
                        <div class="col-sm-12">
                          <div class="panel panel-info">
                            <div class="panel-heading">
                              <h3 class="panel-title">`+ model.code +`</h3>
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

                                    <div class="item-stock" data-id="` + model.id + `" style="margin-top:10px;">
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
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>