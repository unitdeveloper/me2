<?php

use yii\helpers\Html;

use kartik\widgets\ActiveForm;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

//use kartik\widgets\ActiveForm;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sale Order Line List');
$this->params['breadcrumbs'][] = $this->title;



 
$gridColumns = [

      [
        'contentOptions' => ['class' => 'bg-gray'],
        'headerOptions' => ['class' => 'bg-primary', 'style' => 'width:30px;'],
        'class' => 'yii\grid\SerialColumn'
      ],
     

      [ 
        //'attribute' => 'order_date',
        'label' => Yii::t('common','Order date'),
        'contentOptions' => ['class' => ' '],
        'headerOptions' => ['class' => 'bg-gray', 'style' => 'width:100px;'],
        'value' => function($model){
          $link  = $model->orderNo->order_date;
          return $link;
        }
      ],
      //'orderNo.sales_people',
      //'orderNo.customer.name',
       
      //'order_no',
      [
        'attribute' => 'order_no',
        'label' => Yii::t('common','No'),
        'format' => 'raw',
        'contentOptions' => ['class' => ' '],
        'headerOptions' => ['class' => 'bg-gray', 'style' => 'width:110px;'],
        'value' => function($model){
          return $html = Html::a($model->order_no,['/SaleOrders/saleorder/view','id' => $model->sourcedoc],['target' => '_blank']);
        }
      ],

      [
        'attribute' => 'customer_id',
        'label' => Yii::t('common','Customer Code'),
        'format' => 'raw',
        'contentOptions' => ['class' => ' '],
        'headerOptions' => ['class' => 'bg-gray', 'style' => 'width:40px;'],
        'value' => function($model){

          $customer_code = $model->saleHeader 
                        ? ($model->saleHeader->customer ? $model->saleHeader->customer->code : '') 
                        : '';

          $customer_id = $model->saleHeader 
                        ? ($model->saleHeader->customer ? $model->saleHeader->customer->id : '') 
                        : '';

          $customer_name = $model->saleHeader 
                        ? ($model->saleHeader->customer ? $model->saleHeader->customer->name : '') 
                        : '';

          return $html = Html::a($customer_code,['/customers/customer/view', 'id' => $customer_id],['target' => '_blank', 'title' => $customer_name]);
        }
      ],
      

      [
        //'attribute' => 'quantity',
        'label' => Yii::t('common','Vat'),
        'contentOptions' => ['class' => 'hidden'],
        'headerOptions' => ['class' => 'hidden'],
        'filterOptions' => ['class' => 'hidden'],
        'value' => function($model){
          if($model->orderNo->vat_type==1){
            $vat = 'Vat';
          }else{
            $vat = 'No Vat';
          }
          return $vat;
        }
      ],
      //'type',
      //'itemstb.master_code',
      [
        'label' => Yii::t('common','Code'),
        'format' => 'raw',
        'contentOptions' => ['class' => ' '],
        'headerOptions' => ['class' => 'bg-gray', 'style' => 'width:130px;'],
        'value' => function($model){
            $html = Html::a($model->items->master_code,['/items/items/view-only','id' => $model->items->id],['target' => '_blank']);
            if($model->items->ProductionBom != 0){
              $html = Html::a($model->items->master_code,['/Manufacturing/prodbom/view-only','id' => $model->items->ProductionBom],['target' => '_blank']);
            }
          return $html;
        }
      ],
      //'itemstb.description_th',
      [
        //'attribute' => 'description',
        'label' => Yii::t('common','Product Name'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => [
                'style'=>'max-width:180px; min-height:100px; overflow: auto; word-wrap: break-word;'
        ],
        'value' => function($model){
          return $model->description ? $model->description : $model->items->description_th;
        }
      ],

      [
        'label' => Yii::t('common','Ship'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'font-roboto text-center', 'style' => 'width:60px;'],
        'value' => function($model){
            return $model->saleHeader != null
                        ? ($model->saleHeader->status == 'Shiped'
                            ? '<i class="far fa-check-square"></i>'
                            : '')
                        : '';
        }
      ],  
      //'quantity',
      [
        //'attribute' => 'quantity',
        'label' => Yii::t('common','Quantity'),
        'contentOptions' => ['class' => 'text-right bg-warning'],
        'headerOptions' => ['class' => 'text-right bg-gray', 'style' => 'width:80px;'],
        'filterOptions' => ['class' => 'hidden'],
        'value' => function($model){
          return number_format($model->quantity);
        }
      ],

      [
        //'attribute' => 'quantity',
        'label' => Yii::t('common','Cut off stock'),
        'contentOptions' => ['class' => 'text-right bg-yellow'],
        'headerOptions' => ['class' => 'text-right bg-gray', 'style' => 'width:80px;'],
        'filterOptions' => ['class' => 'hidden'],
        'value' => function($model){
          $qty = $model->shiped->qty;

          return number_format($qty);
        }
      ],

       

      [
        //'attribute' => 'unit_price',
        'label' => Yii::t('common','Unit Price'),
        'contentOptions' => ['class' => 'text-right hidden'],
        'headerOptions' => ['class' => 'text-right hidden'],
        'value' => function($model){
          return $model->unit_price *1;
        }
      ],
      // 'unit_measure',
      // 'unit_price',
      [ 
        'label' => Yii::t('common','Amount'),
        'contentOptions' => ['class' => 'text-right hidden'],
        'headerOptions' => ['class' => 'text-right hidden'],
        'value' => function($model){
          return $model->quantity * $model->unit_price;
        }
      ]
];

?>
<?=Html::a(Html::encode($this->title), ['/SaleOrders/order/line-list']);?>
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">
  <div class="col-xs-12">
    <div class=" ">
      <div class=" ">
         
        <?php $form = ActiveForm::begin(['id' => 'saleline-search','method' => 'GET']); ?>
        <div class="row-" style="margin-bottom: 10px;">

          <div class="col-sm-offset-2 col-md-offset-4">

            <div class="row hidden">

              <div class="col-sm-5 col-md-6">  
                <?php

                $startDate  = '';
                $endDate    = '';

                if(isset($_GET['fil_from_date']))    
                {
                  if($_GET['fil_from_date']!='') $startDate   = date('d-m-Y',strtotime($_GET['fil_from_date']));
                }


                if(isset($_GET['fil_to_date'])){      
                  if($_GET['fil_to_date']!='') $endDate     = date('d-m-Y',strtotime($_GET['fil_to_date']));

                }

                echo DatePicker::widget([
                    'name' => 'fil_from_date',
                    'value' => $startDate,
                    'type' => DatePicker::TYPE_RANGE,
                    'name2' => 'fil_to_date',
                    'value2' => $endDate,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-mm-yyyy'
                    ],
                    'options' => [
                      'autocomplete' => 'off'
                    ],
                    'options2' => [
                      'autocomplete' => 'off'
                    ],
                    'pluginEvents' => [
                        //"changeDate" => "function(e) { ReloadSearch(); }",
                    ],
                ]);

                ?>
              </div>
              <div class="col-sm-3 col-md-2 ">
                <select name="vat_type" class="form-control ">
                    <option value="" <?=(@$_GET['vat_type']==1)? 'selected': '';?>><?=Yii::t('common','All')?></option>
                    <option value="1" <?=(@$_GET['vat_type']==1)? 'selected': '';?>>Vat</option>
                    <option value="2" <?=(@$_GET['vat_type']==2)? 'selected': '';?>>No Vat</option>
                </select>
              </div>
              <div class="col-sm-4 col-md-4"> 
                              
                  <div class="box-tools">
                    <div class="input-group  "  >
                      <input type="text" name="table_search" class="form-control pull-right" placeholder="<?=Yii::t('common','Search');?>">

                      <div class="input-group-btn">
                        <button type="submit" class="btn btn-default s-click"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                  
              </div>
            </div>
          </div><!-- /.col-sm-offset-6 -->
        </div><!-- /.row -->
         <?php ActiveForm::end(); ?>
      </div>
      <!-- /.box-header -->
      <div class="box-body no-padding ew-render-saleorder-linelist">
          <style>
            tr.bg-white{
              background-color:#fff !important;
            }

            tr.bg-succ{
              background-color:#dff0d8 !important;
            }
          </style>
            <?= GridView::widget([
                      'dataProvider' => $dataProvider,
                      'tableOptions' => [
                          'class' => 'font-roboto table-condensed '
                      ],
                      'rowOptions'    => function($model){
                          return [
                              'class' => $model->shiped->qty != 0 ? 'bg-succ' : 'bg-white',
                          ];
                      },
                      // 'panel'=>[
                      //         'before'=>' '
                      // ],
                      'filterModel' => $searchModel,
                      'pager' => [
                        'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                        'prevPageLabel' => '«',   // Set the label for the "previous" page button
                        'nextPageLabel' => '»',   // Set the label for the "next" page button
                        'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
                        'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
                        'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                        'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
                        ],
                      'options' => ['class' => ' font-roboto'],
                      'columns' => $gridColumns,
                      'pjax'=>false,    
                      'responsiveWrap' => false, // Disable Mobile responsive    
                      
                  ]); ?>



       
              
      </div>


      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>

<?php


$js =<<<JS

  $(document).ready(function(){
    $('a.ew-bt-app-new').hide();
    $('#w1-togdata-page').attr('title',' ');
  });
  

 

  $('body').on('click','button.s-click',function(){

    $('form[id="saleline-search"]').submit();

  });

JS;

$this->registerJS($js,\yii\web\View::POS_END);