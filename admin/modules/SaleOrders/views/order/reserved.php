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
use admin\modules\apps_rules\models\SysRuleModels;
$this->title = Yii::t('app', 'Sale Line');
$this->params['breadcrumbs'][] = $this->title;



 
$gridColumns = [

      [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['class' => 'bg-gray']
      ],
      
      [
        'attribute' => 'need_ship_date',
        'label' => Yii::t('common','Shipment Date'),
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
          return date('Y-m-d', strtotime($model->need_ship_date ? $model->need_ship_date : $model->saleHeader->ship_date));
        }
      ],

      [
        'attribute' => 'order_no',
        'label' => Yii::t('common','No'),
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'font-roboto'],
        'format' => 'raw',
        'value' => function($model){
          return $html = Html::a($model->order_no,['/SaleOrders/saleorder/view','id' => $model->sourcedoc],['target' => '_blank']);
        }
      ],

      [
        //'attribute' => 'customer',
        'headerOptions' => ['class' => 'bg-gray'],
        'label' => Yii::t('common','Customer'),
        'contentOptions' => [
            'style'=>'max-width:180px; min-height:100px; overflow: auto; word-wrap: break-word;'
        ],
        'format' => 'raw',
        'value' => function($model){
          return $html = Html::a($model->saleHeader->customer->name,['/customers/customer/view','id' => $model->saleHeader->customer_id],['target' => '_blank']);
        }
      ],
    //   [
    //     //'attribute' => 'quantity',
    //     'label' => Yii::t('common','Vat'),
    //     'value' => function($model){
    //       if($model->orderNo->vat_type==1){
    //         $vat = 'Vat';
    //       }else{
    //         $vat = 'No Vat';
    //       }
    //       return $vat;
    //     }
    //   ],
      //'type',
      //'itemstb.master_code',
      [
        'label' => Yii::t('common','Items'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'bg-gray'],
        'contentOptions' => ['class' => 'font-roboto'],
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
        'attribute' => 'quantity',
        'label' => Yii::t('common','Quantity'),
        'headerOptions' => ['class' => 'text-right bg-yellow'],
        'contentOptions' => ['class' => 'text-right font-roboto bg-yellow'],        
        'value' => function($model){
          return number_format($model->quantity);
        }
      ],
      // [
      //   'attribute' => 'unit_price',
      //   'label' => Yii::t('common','Unit Price'),
      //   'headerOptions' => ['class' => 'text-right'],
      //   'contentOptions' => ['class' => 'text-right font-roboto'],        
      //   'value' => function($model){
      //     return number_format($model->unit_price);
      //   }
      // ],

      // [ 
      //   'label' => Yii::t('common','Amount'),
      //   'headerOptions' => ['class' => 'text-right'],
      //   'contentOptions' => ['class' => 'text-right font-roboto'],        
      //   'value' => function($model){
      //     return number_format($model->quantity * $model->unit_price);
      //   }
      // ],

    //   [ 
    //     'label' => Yii::t('common','Cancel Reserve'),
    //     'format' => 'raw',
    //     'headerOptions' => ['class' => 'text-right'],
    //     'contentOptions' => ['class' => 'text-right'],
    //     'value' => function($model){
    //       return '<a href="#" class="btn btn-sm btn-warning-ew btn-delete-reserve"><i class="fa fa-trash"></i> '.Yii::t('common','Remove').'</a>';
    //     }
    //   ],

      [
        'class' => 'yii\grid\ActionColumn',       
        'visible'           => in_array(Yii::$app->session->get('Rules')['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SaleAdmin','SaleAdmin')) ? true : false,
        'contentOptions'    => ['class' => 'text-right'],
        'headerOptions'     => ['class' => 'hidden-xs bg-gray'],
        'filterOptions'     => ['class' => 'hidden-xs'],
        'template'          => '<div class="btn-group btn-group text-center" role="group"> {delete} </div>',
        'buttons'           => [

            'delete' => function($url,$model,$key){
                return Html::a('<i class="fas fa-times text-red"></i> '. Yii::t('common','Cancel reserve'),['cancel-reserve', 'id' => $model->id],[
                    'class' => 'btn btn-sm btn-warning-ew',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to cancel reserved?'),
                        'method' => 'post',
                    ],
                ]);
            },

          ]
      ],

];



?>
 
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">
  <div class="col-xs-12">
      <!-- /.box-header -->
      <div class="box-body no-padding ew-render-saleorder-linelist">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'panel'=>[
            //         'before'=>' '
            // ],
            //'filterModel' => $searchModel,
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
            'options' => ['class' => 'table-responsive'],
            'columns' => $gridColumns,
            'pjax'=>false,    
            'responsiveWrap' => false, // Disable Mobile responsive    
            
        ]); ?>
              
      </div>

    <!-- /.box -->
  </div>
</div>

<?php

$js=<<<JS

  $(document).ready(function(){
    
  });
 

JS;


$this->registerJS($js,\Yii\web\View::POS_END);
?>
