<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\ShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */



use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\warehousemoving\models\FunctionWarehouse;

$Warehouse  = new FunctionWarehouse();
$Fnc        = new FunctionSaleOrder();

//echo $Warehouse->completeShip('SO1706-0135');

$gridColumns = [ 
    //'order_no',
    [
      'class' => 'yii\grid\SerialColumn'        
    ],

    [
      'label' => Yii::t('common','Type'),
      'format' => 'html',
      'contentOptions' => ['class' => 'hidden-xs'],                
      'headerOptions' => ['class' => 'hidden-xs'],
      'footerOptions' => ['class' => 'hidden-xs'],
      'value' => function($model){
        return 'item';
      },
    ],

    [
      'label' => Yii::t('common','Items'),
      'format' => 'html',
      'contentOptions' => ['class' => 'hidden-xs'],                
      'headerOptions' => ['class' => 'hidden-xs'],
      'footerOptions' => ['class' => 'hidden-xs'],
      'value' => function($model){
        return $model->itemstb['master_code'];
      },
    ],

    [
      'label' => Yii::t('common','Description'), 
      'format' => 'html',
      'value' => function($model){
        return $model->Description;
      },
    ], 

    [
        'label' => Yii::t('common','Quantity to ship'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'ew-ship-qty text-right'],
        'value' => function($model){

          $quantity = $model->Quantity * -1;

          $WhLine   = \common\models\WarehouseMoving::find();
          $WhLine->where(['apply_to' => $model->id]);
          $WSum     = $WhLine->sum('Quantity');

          $WSum     = $quantity - $WSum;

          if($model->apply_to != '' || $WSum==0){
            if($quantity < 0){
              return '<div class="pull-right" style="margin-right:2px;" >
                        <span class="text-danger"> ('.Yii::t('common','Undo').') </span> '.$quantity.'
                      </div>'; 
            }else {
              return '<div class="pull-right" style="margin-right:2px;" >
                          '.$quantity.'
                      </div>';  
            }
          }else {
            return '<div class="pull-right input-group" >
                        <input type="number" name="'.$model->id.'" value="'.$WSum.'" class="form-control text-right" style="width:80px;" ew-old-data="'.$WSum.'" id="qtyShipped" line="'.$model->id.'" readonly />
                    </div>';   
          }
        },
        'footer' => '<div class=" "></div>',
    ],
    //   //'quantity',

    //   //'itemstb.UnitOfMeasure',
    [
      'label' => Yii::t('common','Unit'),
      'headerOptions' => ['class' => 'hidden-xs'],
      'contentOptions' => ['class' => 'hidden-xs'],
      'footerOptions' => ['class' => 'hidden-xs'],
      'value' => 'itemstb.UnitOfMeasure',
    ],
    //   //'unit_measure',
      //'unit_price',
      
      
    // [
    //     'label' => Yii::t('common','Stock'),
    //     'contentOptions' => ['class' => 'hidden-xs text-right font-roboto bg-purple'],
    //     'headerOptions' => ['class' => 'hidden-xs text-right font-roboto'],
    //     'value' => function($model){
    //         return number_format($model->items->invenByCache);
    //     }
    // ],

      
];
            
 
?>



<div class="row" >
    <div class="col-sm-12">
         
            <div >
                <?php 

                    $tumbol = '';
                    $amphur = '';
                    $province = '';
                    $zipcode = '';

                    if(!empty($model->district)){
                        $tumbol = 'ต.'.$model->districttb->DISTRICT_NAME;

                    } 

                    if(!empty($model->city)){
                        $amphur = 'อ.'.$model->citytb->AMPHUR_NAME;

                    } 

                    if(!empty($model->province)){
                        $province = 'จ.'.$model->provincetb->PROVINCE_NAME;

                    } 

                    if(!empty($model->postcode)){
                        $zipcode = $model->postcode;

                    }

                    if($model->ship_to != '')
                    {
                        $ShipAddr = $model->address.' '. $model->address2;
                        $ShipAddr.= $tumbol.''.$amphur.''.$province.' '.$zipcode;
                    }else {
                        $ShipAddr = NULL;
                    } 

                    

                ?>
                <div class="row">
                  <div class="col-sm-10">
                    <div class="row"><label class="col-sm-2"><?=Yii::t('common','No')?> : </label><div  class="col-sm-10"> <?=$model->DocumentNo;?></div></div>
                    <div class="row"><label class="col-sm-2"><?=Yii::t('common','Ship Date')?> : </label><div  class="col-sm-10"> <?=$model->ship_date;?></div></div>
                    <div class="row">
                      <label class="col-sm-2"><?=Yii::t('common','Transport By')?> : </label>
                      <div  class="col-sm-10">
                        <a href="#" class="open-modal-editheader" source="shipped-tab" data="<?=$model->id?>">
                          <?=$model->Description;?> 
                          <i class="fa fa-pencil-square-o text-warning" aria-hidden="true"></i>
                        </a>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2"><?=Yii::t('common','Customer')?> : </label>
                      <div  class="col-sm-10"> 
                        <?=$model->customer->name;?>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-sm-2"><?=Yii::t('common','Address')?> : </label>
                      <div  class="col-sm-10"> 
                        <?=$ShipAddr;?>
                      </div>                      
                    </div>

                    <div class="row">
                      <label class="col-sm-2">
                          <?=Yii::t('common','Quantity of box')?> : 
                      </label>
                      <div class="col-sm-3"> 
                          <input type="number" class="form-control" name="boxs" id="box-change" value="<?=$model->boxs;?>"  data-key="<?=$model->id?>"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-2 text-right">
                      <div class="undo-btn">{Undu}</div>
                  </div>
                </div>
                 
                <form name="Shipped" class="ShippedLine">
                 
                <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'summary' => false, 
                        'showFooter' => true,
                        'columns' => $gridColumns,
                             

                            
                        
                    ]); ?>
               
                </form>
            </div>
        <div class="ew-actions"> {Print-Label} {Print-ShipList}</div>
    </div>
</div>



 