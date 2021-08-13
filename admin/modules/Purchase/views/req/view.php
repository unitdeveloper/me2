<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

use kartik\widgets\DatePicker;

use kartik\widgets\SwitchInput;


use common\models\Vendors;
use common\models\Items;

 
$model->doc_no = ($model->isNewRecord)? $doc_no : $model->doc_no;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<style type="text/css">
    
    .series-list,
    .vendor-list{
      margin: 10px 0px 0px 10px;
      padding-left: 0px !important;
    }
    .series-list li,
    .vendor-list li{
      list-style-type: none;
      margin: 15px 0px 15px 0px;
    }

    .series-list li span:hover,
    .vendor-list li span:hover{
      color:yellow;
      margin-left: 1px;
    }

    .series-list li span:active,
    .vendor-list li span:active{
      color:red;
    }

    .addNewVendor{
      list-style-type:none;
      margin: 10px 50px 10px -30px;
    }
    .addNewVendor a{
      color:#fff;
    }
    .addNewVendor a:hover{
      color:#16f2ec;
      margin-left: 1px;

    }

    input.focus{
      background-color: rgb(252, 255, 217);
    }

    span.selected{
      padding: 10px 100% 10px 10px;
    }

    #searchVendorList{
      padding: 10px 50px 10px 10px;
    }

    #AddSeries:hover,
    #searchVendorList:hover{
      color:#16f2ec;
    }

    #AddSeries:active,
    #searchVendorList:active{
      color: green;
    }

    div.vendorFilter span.input-group-addon{
      cursor: pointer;
     
    }
    .vendorFilter .input-group{
      border-right: 1px solid #ccc;
    }
    .table{
      margin-bottom: 0px;
    }

    .tax-toggle{
      <?=$model->withholdTaxSwitch!=1? 'display:none;' : 'display:visable;'; ?>
    }

    .move{
      cursor: move;
      background-color: #3aebf6;
    }

    .searchItem{
      max-height:600px !important;
      overflow:auto !important;
      padding:20px;
      border: 1px solid #efefef;
       
      display: none;
    }
    /*.expand:focus {

        width: 250px;
        -webkit-transition: all 1s linear;
        -0-transition: all 1s linear;
        -ms-transition: all 1s linear;
        -moz-transition: all 1s linear;
        transition: all 1s linear;
    }*/
 
    .animate-if.ng-enter, .animate-if.ng-leave {
      transition:all cubic-bezier(0.250, 0.460, 0.450, 0.940) 0.5s;
    }

    .animate-if.ng-enter,
    .animate-if.ng-leave.ng-leave-active {
      opacity:0;
    }

    .animate-if.ng-leave,
    .animate-if.ng-enter.ng-enter-active {
      opacity:1;
    }

 
</style>
 
<div class="purchase-header-form" ng:controller="requisitionWorksheet"  style="font-family: saraban; font-size:13px;">
    <?php $form = ActiveForm::begin([
            'id' => 'form-purchase-order',
            'enableClientValidation' => true,
            'enableAjaxValidation' => false,
            'options' => [
              'enctype' => 'multipart/form-data',
              'data-key' => isset($_GET['id'])? $_GET['id'] : '0',
              
            ]
        ]); ?>
    <div class="panel-heading">
          <div class="row">
                  <div class=" " >
                  <div class="pull-right">
                  <?=$form->field($model,'series_id')->hiddenInput()->label(false);?>
                  </div>
                  <div class="text-center">
                    <h4 class="text-primary">
                      <i class="fa fa-file-text-o fa-lg"></i><span data-toggle="popover"
                      title="ใบขอซื้อสินค้า(PR)"
                      id="popover"
                      data-content="เป็นเอกสารสำหรับแจ้งคำขอซื้อ ส่งให้เจ้าหน้าที่จัดซื้อ เพื่อเปิดใบสั่งซื้อ(PO)">  <?=Yii::t('common','PURCHASE REQUISITION')?> </span>
                    </h4>
                  </div>
                  
                  <div class="row">
                    <div class="col-sm-offset-6">
                      <div class="col-xs-6 pull-right">
                         <?=$form->field($model,'doc_no',
                          ['addon' =>
                            ['append' =>
                              [
                               'content'=>'<span class="pointer "><i class="fa fa-angle-down" aria-hidden="true"></i></span>',
                              ]
                            ]
                          ])->textInput([
                              'class' => ' ',
                              'autocomplete' => 'off',
                              'placeholder'=>Yii::t('common','Please create a document number.'),
                              'disabled' => true
                              ])?>
                      </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6"  >
                                    <?php
                                        if($model->order_date=='') {
                                          $model->order_date = date('Y-m-d');
                                        }else {
                                          $model->order_date = date('Y-m-d',strtotime($model->order_date));
                                        }
                                        echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                                      'options' => ['placeholder' => Yii::t('common','Order date').'...'],
                                                      'value' => date('Y-m-d',strtotime($model->order_date)),
                                                      'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                                      'removeButton' => false,
                                                      'disabled' => true,
                                                      'pluginOptions' => [
                                                          //'format' => 'dd/mm/yyyy',
                                                          'format' => 'yyyy-mm-dd',
                                                          'autoclose'=>true
                                                      ]
                                              ])->label(Yii::t('common','Order date'));
                                              ?>
                                </div>
                                <div class="col-xs-6">
                                <?php

                                    $List = arrayHelper::map((\common\models\ProjectControl::find()
                                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                    ->orderBy(['name' => SORT_ASC])
                                    ->all()),'id','name');

                                    echo $form->field($model, 'project')->dropDownList($List,[

                                                                    'data-live-search'=> "true",
                                                                    'class' => 'selectpicker col-lg-12',
                                                                    'prompt'=>'-Project-',
                                                                    'disabled'=>true
                                                                    
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
 

                  <div class="row">
                    <div class="col-sm-3">                         
                      <?= $form->field($model, 'department')->textInput(['maxlength' => true,'disabled' => true, 'placeholder' => Yii::t('common','Department')])?>
                    </div>
                    <div class="col-sm-3"> </div>
                    <div class="col-sm-6">                         
                      <div class="row">
                          <div class="col-sm-6"  >
                               
                          </div>
                          <div class="col-sm-6">
                              <?= $form->field($model, 'delivery_date')->widget(DatePicker::classname(), [
                                            'options' => ['placeholder' => Yii::t('common','Delivery Date').'...'],
                                            'value' => date('Y-m-d',strtotime($model->delivery_date)),
                                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                            'removeButton' => false,
                                            'disabled' => true,
                                            'pluginOptions' => [
                                                //'format' => 'dd/mm/yyyy',
                                                'format' => 'yyyy-mm-dd',
                                                'autoclose'=>true
                                            ]
                                    ])->label(Yii::t('common','Delivery Date')); ?>
                          </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-3">                         
                      <?= $form->field($model, 'contact')->textInput(['maxlength' => true,'disabled' => true, 'placeholder' => Yii::t('common','Attention')])->label(Yii::t('common','Attention'))?>
                    </div>
                    <div class="col-sm-3"> </div>
                    <div class="col-sm-6">                         
                      
                    </div>
                  </div>
                  
              </div>
          </div>
    </div>
    <?php // Default Zone

        //if($model->vendor_id == '') $model->vendor_id = 1;
    ?>

 
        
 

    <div class="purchase-line-render table-responsive">
      <table class="table   table-bordered sortable" id="Purchase_Line">
        <thead>
          <tr class=" ">
            <th style="width:20px;" >#</th>
            <th><?=Yii::t('common','Product')?></th>
            <th><?=Yii::t('common','Product Name')?></th>
            <th style="width:150px;" class="text-right"><?=Yii::t('common','Amount')?></th>
            <th style="width:150px;"><?=Yii::t('common','Measure')?></th>
            <th style="width:150px;" class="text-right"><?=Yii::t('common','Unit Price')?></th>
            <th style="width:150px;" class="text-right"><?=Yii::t('common','Line amount')?></th>
            
          </tr>
        </thead>
        <tbody >
          <tr ng:repeat="model in PurchaseLine  | orderBy : 'priority'" data-key="{{model.id}}"  ng-style="model.genpo.count <= 0  && {'color': '#000'} || {'color':'red'}">
            <td class=" " ng-bind="$index +1" style="color:#fff;"></td>
            <td ng-bind="model.item_no"></td>
            <td ng-bind="model.description"></td>
            <td ng-bind="model.quantity | number" class="text-right"></td>
            <td >
              <select ng-model="model.measure" disabled="disabled"  class="form-control field-update" ng-change="getTotalSummary()" name="unit_of_measure" >                  
                  <option ng-repeat="option in model.unitofmeasure" value="{{option.id}}">{{option.name}}</option>
              </select> 
            </td>
            <td ng-bind="model.unitcost | number" align="right"></td>
            <td class="text-right" ng-bind="model.quantity * model.unitcost | number"></td>
                       
          </tr>
        </tbody>
        
      </table>
       
    </div>
     
    <div class="row" style="margin-bottom:100px;">
      <div class="col-md-6">
        <div class="row">
          <div class="col-lg-12" style="font-family: saraban;" >
            <?= $form->field($model, 'remark')->textarea(['rows' => 6,'disabled' => true]) ?>
            <?= $form->field($model, 'detail')->textInput(['maxlength' => true,'disabled' => true, 'placeholder' => Yii::t('common','Apply for')])->label(Yii::t('common','Apply for')) ?>
          </div>
        </div>
      </div>
      <div class="col-md-6">
          <div class=" " >
            <div class=" " style="margin-top: 25px; border: 1px solid #ccc;">
                <table class="table" style=" ">
                  <tr class="panel-heading bg-gray">
                    <th colspan="2"><?=Yii::t('common','Total')?>:</th>
                    <td align="right" ng-bind="total | number:2"></td>
                  </tr>
                  <tr class="text-primary">
                      <th>
                        <?=Yii::t('common','Discount')?>:
                      </th>
                      <td>
                      <?=$form->field($model,'percent_discount',
                        ['addon' =>
                          ['append' =>
                            [
                              'content'=>'<span class=""><i class="fa fa-percent" aria-hidden="true"></i></span>',
                            ]
                          ]
                        ])->textInput([
                          'placeholder' => 0,
                          'class'=>'text-right',
                          'ng-model' => 'percentDiscount',
                          'ng-change' => 'getTotalSummary()',
                          'type'=>"number",
                          'step'=>"any",
                          'string-to-number'  => true,
                          'disabled' => true
                        ])->label(Yii::t('common','Discount'))->label(false)
                        ?>
                      </td>
                      <td align="right" style="padding-right: 0px; ">
                            <?= $form->field($model, 'discount')
                            ->textInput([
                              'placeholder' => '0',
                              'class'=>'text-right no-border',
                              'readonly'=>"readonly" ,
                              'style' => 'background-color:transparent;',
                              'ng-model' => 'discount',
                              'disabled' => true
                            ])
                            ->label(Yii::t('common','Discount'))->label(false) ?>
                      </td>
                  </tr>
                  <tr class="text-primary">
                    <th colspan="2"><?=Yii::t('common','Total after discount')?>: </th>
                    <td align="right" ng-bind="subtotal | number:2"></td>
                  </tr>
                  <tr class="text-success animate-if" ng-if="beforvat">
                    <th colspan="2"><?=Yii::t('common','Before vat')?> </th>
                    <td align="right">
                      <span  id="ew-before-vat" ng-bind="beforvat | number:2"></span>
                      </td>
                  </tr>
                  <tr class="text-success">
                    <td style="width: 150px;">
                      <?php 
                        $allVatType = \common\models\VatType::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all();
                        $vatType = [];
                        foreach ($allVatType as $value) {
                          $vatType[] = [
                            'id'    => $value->vat_value,
                            'name'  => $value->name
                          ];
                        }                          
                        $vatType = str_replace('"',"'",json_encode($vatType));
                        
                        ?>

                      <div  ng-init="dataVatPercent =<?=$vatType?>; vat_percent='0'">
                        <?=Yii::t('common','Vat')?>  <span  ng-bind="vat_percent"></span> %
                        <select name="PurchaseReqHeader[vat_percent]"  disabled="disabled"  ng-model="vat_percent" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-vat_percent">                             
                            <option ng-repeat="option in dataVatPercent" value="{{option.id}}">{{option.name}}</option>
                        </select> 

                            
                        </div>
                    </td>
                    <th >
                      <div class="show-vat-type" style="margin-top:20px;" ng-init="incVat = [
                        {'id': 0,'name': '<?=Yii::t('common','Include Vat')?>'}, 
                        {'id': 1,'name': '<?=Yii::t('common','Exclude Vat')?>'}
                      ];
                      include_vat = '1';"> 
                          <select name="PurchaseReqHeader[include_vat]" disabled="disabled" ng-model="include_vat" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-include_vat">                            
                            <option ng-repeat="option in incVat" value="{{option.id}}">{{option.name}}</option>
                          </select>                                             
                      </div>
                    </th>
                    <td align="right" id="ew-after-vat" ng-bind="aftervat | number:2"></td>
                  </tr>
                  <tr class="bg-aqua" >
                    <th colspan="2"><?=Yii::t('common','Grand total')?>: </th>
                    <td align="right" ng-bind="grandTotal | number:2"></td>
                  </tr>
                </table>
            </div>
          </div>
          <div class="panel panel-default" style="margin-top:20px;"  >
            <div class="panel-body">
              <div class="row">
                 
                <div class="col-xs-9" >   
                  <?= $form->field($model,'withholdTaxSwitch')->widget(SwitchInput::classname(), [
                    'pluginOptions' => ['size' => 'mini'],
                    'disabled' => true
                    ])
                  ->label(Yii::t('common','Withholding'),['style'=>'position:absolute; margin-left:70px;']); ?>
                </div>
                <div class="col-xs-3 tax-toggle" ng-init="holdingTag = [
                      {'id': 0,'name': '0'}, 
                      {'id': 0.5,'name': '0.5'}, 
                      {'id': 0.75,'name': '0.75'}, 
                      {'id': 1,'name': '1'}, 
                      {'id': 2,'name': '2'}, 
                      {'id': 3,'name': '3'}, 
                      {'id': 5,'name': '5'}, 
                      {'id': 10,'name': '10'}, 
                      {'id': 15,'name': '15'}
                    ];
                    withholdingTax = '5';">    
                  <select name="PurchaseReqHeader[withholdTax]" disabled="disabled" ng-model="withholdingTax" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-withholdtax">
                    <!-- <option value="">--</option> -->
                    <option ng-repeat="option in holdingTag" value="{{option.id}}">{{option.name}}</option>
                  </select>  
                      
                </div>
              </div>
            </div>
            <div class="row tax-toggle">
              <div class="col-xs-12">
                <div class="bg-default">
                  <div class="row">
                  <div class="col-xs-8">
                    หักภาษี ณ ที่จ่าย <span ng-bind="withholdingTax"></span> %
                  </div>
                  <div class="col-xs-4 text-right">
                    <span ng-bind="witholdingValue | number:2"></span>
                  </div>
                </div>
                </div>
              </div>
            </div>
            <?=$form->field($model,'balance')->hiddenInput([
              'id' => 'total-balance',
              'readonly' => true,
              'style' => 'background: transparent; color:#000;',
              'ng-model' => 'grandTotal'
              ])->label(false) ?>
          </div>
          <div class="panel-footer" style="background-color:#00a65a;color:#fff;" >
            <div class="row">
              <div class="col-xs-8">
              <?=Yii::t('common','Payments Amount')?>   
              </div>
              <div class="col-xs-4 text-right">
                <span ng-bind="grandTotalPayment | number:2"></span>
              </div>
            </div>
          </div>
      </div>
    </div>
   
    <div class="content-footer" style="
      position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 10px 10px 10px;
      right: 0px;
      text-align: right;
      z-index:1001;
    " >
      <div class="row">     
          
          <div class="col-xs-6 col-sm-6 text-left">        
              <div class="form-group">   
                <?= Html::a('<i class="fas fa-chevron-left"></i> '.Yii::t('common', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-default ']) ?>   
                <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>    
              </div>
              
          </div> 
          <div class="col-xs-6 col-sm-6  text-right">  
              <?= Html::a('<i class="fas fa-print"></i> '.Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info ','target' => '_blank']) ?>
              <?= Html::a('<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success ']) ?>                
              <?= Html::a('<i class="fas fa-sign-in-alt"></i> '.Yii::t('common', 'Convert to PO'), ['convert', 'id' => $model->id], [
                  'class' => 'btn btn-info convert-to-po',
                  'data' => [
                      'confirm' => Yii::t('common', 'You need to create a Purchase Order?'),
                      'method' => 'post',
                  ],
              ]) ?>
          </div>                   
      </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js', $Options);?>
<?php $this->registerJsFile('@web/js/jquery.ui.touch-punch.min.js',$Options);?>
<?php $this->registerJsFile('js/item-picker.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/no.series.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/purchase/vendors.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/purchase/req.js?v=3.05.23');?>
<?php $this->registerJsFile('js/purchase/reqController.js?v=3.05.23');?>
<?= $this->render('@admin/views/setupnos/__modal'); ?>


<?php 
$js=<<<JS

$(document).ready(function(){
    // var footer = $('div.content-footer').html();
    // $('footer').html(footer).find('div.content-footer').fadeIn('slow');
    //$('.convert-to-po').hide();
})

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');