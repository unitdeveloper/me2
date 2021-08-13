<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
use common\models\TransportList;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\ShipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\WarehouseMoving;
use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\warehousemoving\models\FunctionWarehouse;

$Warehouse  = new FunctionWarehouse();
$Fnc        = new FunctionSaleOrder();


$gridColumns = [
      //'order_no',
      [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['class' => 'bg-gray'],
      ],
      //'type',
      //  'itemstb.master_code',
      [
        'label' => Yii::t('common','Type'),
        'format' => 'html',
        'contentOptions' => ['class' => 'hidden'],
        'headerOptions' => ['class' => 'hidden bg-gray'],
        'footerOptions' => ['class' => 'hidden'],
        'value' => function($model){
          return $model->type;
        },

      ],
      [
        'label' => Yii::t('common','Items'),
        'format' => 'raw',
        'contentOptions' => ['class' => 'hidden-xs'],
        'headerOptions' => ['class' => 'hidden-xs bg-gray'],
        'footerOptions' => ['class' => 'hidden-xs'],
        'value' => function($model){
          return Html::a($model->items->master_code,['/items/items/view-only','id' => $model->items->id],['target' => '_blank']);
        },

      ],
      //'itemstb.Description',
      [
        'label' => Yii::t('common','Description'),
        'format' => 'html',
        'headerOptions' => ['class' => 'bg-gray'],
        'value' => function($model){

          if($model->description==''){
            return $model->items->description_th;
          }else {
            return $model->description;
          }
        },

      ],

      [
        'label' => Yii::t('common','Stock'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right bg-yellow'],
        'value' => function($model){
          return $model->items->qtyAfter;
        },

      ],

      [
        'label' => Yii::t('common','Can Produce'),
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function($model){
          return $model->items->invenByCache;
        },

      ],

      [
        'label' => Yii::t('common','Reserved'),
        'format' => 'html',
        'headerOptions' => ['class' => 'text-right bg-gray'],
        'contentOptions' => ['class' => 'text-right'],
        'value' => function($model){
          return $model->items->reserveInSaleLine * 1;
        },

      ],
      
      [
          'label' => Yii::t('common','Quantity to ship'),
          'format' => 'raw',
          'headerOptions' => ['class' => 'text-right bg-gray'],
          'contentOptions' => ['class' => 'ew-ship-qty pull-right'],
          'value' => function($model){

            $disabled = ($model->quantity - $model->quantity_shipped) <= 0 ? 'disabled' : NULL;
            $icon     = '<i class="fa fa-cube text-warning" aria-hidden="true"></i>';
            $status   = '<label class="input-group-addon label-info"><i class="fas fa-hourglass-half"></i></label>';
            

            // if($model->saleHeader->status=='Shiped'){
            //   $quantity   = 0;
            // }else{
                
            //   $query      = WarehouseMoving::find()->where(['SourceDoc' => $model->id,'TypeOfDocument' => 'Sale']);
            //   $Whsum      = $query->sum('Quantity') * -1; // กลับค่า​ (-) <-> (+)

            //   $quantity   = $model->quantity - $Whsum;
            
            //   if($Whsum == $model->quantity) $icon = '<i class="fa fa-check text-success" aria-hidden="true"></i>';
                
            // }

            $confirm = $model->confirm * 1;

            if($confirm > 0) $status    = '<label class="input-group-addon label-success"> <i class="fa fa-check text-success" aria-hidden="true"></i> </label>';
            //if($quantity==0) $disabled  = 'disabled';
            
            return '<div class="pull-right input-group" >
                        <label class="input-group-addon">'.$icon.'</label>
                        <input type="number" name="'.$model->id.'" value="'.($model->quantity - $model->quantity_shipped).'" class="form-control text-right bg-orange" style="width:100px;" ew-old-data="'.($model->quantity - $model->quantity_shipped).'" id="qtyToShip" line="'.$model->id.'" '.$disabled.'>
                        '.$status.'
                    </div>';
          },
          'footer' => '<div class=" "></div>',
      ],
        //'quantity',

        //'itemstb.UnitOfMeasure',
      [
        'label' => Yii::t('common','Unit'),
        'headerOptions' => ['class' => 'hidden-xs bg-gray'],
        'contentOptions' => ['class' => 'hidden-xs'],
        'footerOptions' => ['class' => 'hidden-xs'],
        'value' => 'itemstb.UnitOfMeasure',
      ],
        //'unit_measure',
        //'unit_price',
];
?>



<div class="row">
  <div class="col-sm-12">
    <div class="font-roboto " >
      <div >
        <?php

            $tumbol = '';
            $amphur = '';
            $province = '';
            $zipcode = '';
            $ShipAddr = '';
            //$SaleHeader = \commmon\models\SaleHeader::findOne($model->id);
            if(isset($model->customer->district))
            $tumbol = 'ต.'.$model->customer->districttb->DISTRICT_NAME;
            if(isset($model->customer->city))
            $amphur = 'อ.'.$model->customer->citytb->AMPHUR_NAME;
            if($model->customer->province!='')
            $province = 'จ.'.$model->customer->provincetb->PROVINCE_NAME;
            $zipcode = $model->customer->postcode;
            $ShipAddr = $model->customer->address.' '. $model->customer->address2 .' ';
            $ShipAddr.= $tumbol.''.$amphur.''.$province.''.$zipcode;
          ?>
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active  bg-warning">
              <a href="#notshipment" data-toggle="tab" aria-expanded="true">
                <i class="fa fa-calendar-check-o text-warning" aria-hidden="true"></i>
                <?=Yii::t('common','Packing not complete');?>
              </a>
            </li>
            <li class=" bg-success">
              <a href="#shippedList" data-toggle="tab" aria-expanded="false">
                <i class="fa fa-cubes text-success" aria-hidden="true"></i>
                <?=Yii::t('common','Complete packing');?>
              </a>
            </li>
            <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active " id="notshipment">
                        <!-- <form name="Shipment" class="Shipment"> -->
                        <?php $form = ActiveForm::begin([
                          'options' => ['name'=>"Shipment",'class'=>"Shipment",],
                        ]) ?>
                        <div class="row">
                          <div class="">
                            <label class="col-sm-2"><?=Yii::t('common','Ship Date') ?> : </label>
                            <div class="col-sm-2">
                              <?php
                              if($model->ship_date=='')$model->ship_date = date('Y-m-d');
                              echo $form->field($model, 'ship_date')->widget(DatePicker::classname(), [
                                            'options' => ['placeholder' => Yii::t('common','Shipment date').'...'],
                                            'value' => $model->ship_date,
                                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                            'pluginOptions' => [
                                                //'format' => 'dd/mm/yyyy',
                                                'format' => 'yyyy-mm-dd',
                                                'autoclose'=>true
                                            ]
                                        ])->label(false);

                                        ?>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                              <label class="col-sm-2">
                                <?=Yii::t('common','Transport By')?> :  </label>
                              <div class="col-sm-4">
                                <?php /*
                                if($model->transport=='')
                                  $model->transport = '<span class="btn btn-danger"><i class="fa fa-list" aria-hidden="true"></i> เลือกผู้ขนส่ง</span>';*/

                                  //<a href="#TRANSPORT" class="ew-transport-show"><?=$model->transport </a>
                                ?>
                                

                                <?php
                                  $model->transport = $model->customer->default_transport;
                                              
                                  $TransportList = \common\models\TransportList::find()
                                  ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                  ->orderBy(['name' => SORT_ASC])
                                  ->all();
                            
                                  echo $form->field($model, 'transport',[
                                      'addon' => ['append' => ['content'=> Html::Button('<i class="fas fa-plus pointer add-transport"></i>',['class' => 'no-border'])]]
                                    ])->dropDownList(
                                      arrayHelper::map($TransportList,'id', 'name'),
                                      [
                                          'class' => 'form-control',
                                          //'prompt'=>'- เลือก -',
                                      ]
                                  )->label(false);
                                ?>
                              </div>
                              <div class="col-sm-8"></div>
                        </div>
                        <div class="row">
                              <label class="col-sm-2">
                                <?=Yii::t('common','Address')?> : 
                              </label>
                              <div class="col-sm-10"> 
                                <a href="#JSON" class="ew-form-address">  
                                  <?=$ShipAddr;?>
                                </a>
                                <a href="#JSON" class="btn btn-primary pull-right" style="margin-bottom: 5px;">
                                  <i class="fa fa-address-card-o" aria-hidden="true"></i>
                                  <?=Yii::t('common','Change address');?>
                                </a>
                              </div>
                        </div>
                        <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'summary' => false,
                                'showFooter' => false,
                                'columns' => $gridColumns
                            ]); ?>
                        <?php ActiveForm::end(); ?>
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane " id="shippedList">
              <?php
                $SHiP     = $Warehouse->getShipmentHeader($model->id);
                $ship_div = '<div class="well"><div class="row ">';
                $ship_div.= '<div class="col-xs-12">';
                foreach ($SHiP as $value) {
                  //if($Warehouse->getShipmentLine($value['id']) == 0)
                  if(in_array($value->status,['Undo','Undo-Shiped'])){
                    $color = 'text-default';
                    $icon  = '<i class="fa fa-folder-o text-warning fa-lg"></i>';
                    $src   = 'Docs-icon-gray.png';
                  }else {
                    $color = 'text-success';
                    $icon  = '<i class="fa fa-truck"></i>';
                    $src   = 'Docs-icon.png';
                  }
                  $ship_div.= '<div class="col-xs-3 col-sm-2 col-md-1 text-center" style="margin-bottom: 15px; font-size:12px; font-weight:bold;  min-width:125px">
                                  <img src="images/icon/'.$src.'" class="img-responsive  ew-shipment" ew-shipped-id="'.$value['id'].'" style="cursor:pointer; margin-left: 2px;">
                                  <span class="'.$color.'  ew-shipment"  ew-shipped-id="'.$value['id'].'">
                                  '.$value['DocumentNo'].'</span>
                              </div>';
                }
                $ship_div.=  '</div></div>';
                $ship_div.= '</div>';
                echo $ship_div;
                echo Yii::$app->request->cookies->getValue('commonAddress');
              ?>
              <?php if($SHiP == NULL): ?>
                  <div class="row">
                    <div class="col-sm-12" style="text-align: center;">
                      <p>​<?=Yii::t('yii','No results found.')?></p>
                    </div>
                  </div>
              <?php endif;?>
              <form name="Shipment" class="Shipped"></form>
            </div>
            <!-- /.tab-pane -->
            <!-- /.tab-pane -->
          </div>
          <!-- /.tab-content -->
        </div>

      </div><!-- /.div -->
    </div><!-- /.div -->
  </div><!-- /.col-12 -->
</div><!-- /.row -->
 
  