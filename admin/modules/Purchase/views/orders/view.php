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

    .project-picker{
      z-index:1030 !important;
    }
</style>

<div class="purchase-header-form font-roboto" ng:controller="purchaseOrder">
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
                      <span data-toggle="popover"
                        title="ใบขอซื้อสินค้า(PR)"
                        id="popover"                        
                        data-content="เป็นเอกสารสำหรับแจ้งคำขอซื้อ ส่งให้เจ้าหน้าที่จัดซื้อ เพื่อเปิดใบสั่งซื้อ(PO)">  
                        <?=Yii::t('common','PURCHASE ORDER')?> 
                      </span>
                      <span class="pointer" data-toggle="collapse" href="#view-detail" aria-expanded="false" aria-controls="view-detail">
                        <i class="fas fa-comment-dots text-yellow"></i>
                      </span>
                    </h4>
                  </div>                   
                  <div class="collapse" id="view-detail">
                    <?= $this->render('_view', ['model' => $model])?>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                    <div class="row">
                      <div class="col-xs-6 col-sm-3 pull-right">
                         <?=$form->field($model,'doc_no',
                            ['addon' =>
                              ['append' =>
                                ['content'  =>  '<span class="pointer "><i class="fa fa-angle-down" aria-hidden="true"></i></span>',]
                              ]
                            ])->textInput([
                                'class' => ' ',
                                'autocomplete' => 'off',
                                'placeholder' =>  Yii::t('common','Please create a document number.'),
                                'disabled' => true
                              ])?>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6 col-sm-3 pull-right">
                          <?php
                            $List = arrayHelper::map((\common\models\ProjectControl::find()
                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->orderBy(['name' => SORT_ASC])
                            ->all()),'id','name');

                            echo $form->field($model, 'project')->dropDownList($List,[
                                'data-live-search'  => "true",
                                'class'   => 'selectpicker col-lg-12 project-picker',
                                'prompt'  =>  '-Project-',
                                'disabled'=> true
                            ]);
                          ?>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-xs-12">
                              <div class="row ">
                                <div class="col-xs-6 col-sm-3 pull-right">
                                    <?php
                                        if($model->order_date=='') {
                                          $model->order_date = date('Y-m-d');
                                        }else {
                                          $model->order_date = date('Y-m-d',strtotime($model->order_date));
                                        }
                                        echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                                'options'   => ['placeholder' => Yii::t('common','Order date').'...'],
                                                'value'     => date('Y-m-d',strtotime($model->order_date)),
                                                'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                                'removeButton' => false,
                                                'disabled'  => true,
                                                'pluginOptions' => [
                                                    'format'    => 'yyyy-mm-dd',
                                                    'autoclose' => true
                                                ]
                                        ])->label(Yii::t('common','Order date'));
                                    ?>
                                </div>
                              <div class="col-xs-6 col-sm-3 pull-right">
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
                      <div class="col-xs-3"> </div>
                        <div class="col-xs-3"></div>
                        <div class="col-xs-12 col-sm-6">
                          <?= $form->field($model, 'delivery_address', [
                              'feedbackIcon' => ['default' => 'glyphicon glyphicon-globe']
                          ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Ship Address'), 'disabled' => true,])->label(Yii::t('common','Ship Address')) ?>
                        </div>                         
                      </div>
                    </div>
                  </div>
              </div>
          </div>
    </div>
    <?php // Default Zone

        //if($model->vendor_id == '') $model->vendor_id = 1;
    ?>

    <div class="row">
        <div class="col-sm-5">
          <div class="vendorFilter" >
              <?=$form->field($model,'vendor_name',
               ['addon' =>
                 ['append' =>
                   [
                    'content'=>'<span class="pointer"><i class="fa fa-angle-down" aria-hidden="true"></i></span>',
                  ]
                 ]
               ])->textInput([
                   'class' => ' ',
                   'placeholder' => Yii::t('common','Please select a vendor.'),
                   'autocomplete' => 'off',
                   'disabled' => true,
                   ])?>
            </div>
            <?=$form->field($model,'vendor_id')->hiddenInput()->label(false)?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'phone',
                    [
                        'feedbackIcon' => ['default' => 'phone']
                    ])->textInput([
                        'maxlength' => true,
                        'placeholder' => Yii::t('common','Phone'),
                        'disabled' => true,
                        ])->label(false) ?>

                    
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'contact', [
                        'feedbackIcon' => ['default' => 'headphones']
                    ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Contact'),'disabled' => true,])->label(false) ?>
                    
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <?= $form->field($model, 'address', [
              'feedbackIcon' => ['default' => 'comment']
            ])->textArea(['maxlength' => true,'style' => 'height:82px;','disabled' => true,])
            ?>
            <div class="row collapse in" id="vendor-address2">
              <div class="col-xs-6"></div>
              <div class="col-xs-6">                
                <!-- Hidden Reference -->
                <?= $form->field($model, 'refer_id')->hiddenInput()->label(false);?>
                <?= $form->field($model, 'refer_name')->hiddenInput()->label(false);?>
                <?= $form->field($model, 'vender_type')->hiddenInput()->label(false);?>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <span class="pointer pull-right text-primary" data-toggle="collapse" data-target="#order-project">
          <i class="fa fa-caret-square-o-down " aria-hidden="true"></i> <?=Yii::t('common','More')?>
        </span>
      </div>
      <div class="col-xs-12 collapse" id="order-project">
        <div class="row">
          <div class="col-lg-offset-10">
            <div class="col-sm-12">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-5">
            <div class="row">
              <div class="col-sm-6">            
                <?= $form->field($model, 'email', [
                            'feedbackIcon' => ['default' => 'envelope']
                        ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Email'),'disabled' => true,])->label(false) ?>
                <?= $form->field($model, 'ref_no')->textInput(['maxlength' => true,'disabled' => true,]) ?>
              </div>
              <div class="col-sm-6">
                <?= $form->field($model, 'fax', [
                            'feedbackIcon' => ['default' => 'print']
                        ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Fax'),'disabled' => true,])->label(false) ?>
                <?=$form->field($model,'ext_document',
                    ['addon'  => [
                        'append' => [
                          'content' => '<i class="fa fa-paperclip" aria-hidden="true" ></i>',
                        ]
                    ]])->textInput(['disabled' => true])?>
                
              </div>
            </div>
          </div>
          <div class="col-sm-7">    
            <div class="row">
              <div class="col-sm-6">        
                <?= $form->field($model, 'taxid',[
                              'feedbackIcon' => ['default' => 'tag']
                          ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Tax ID'),'disabled' => true,])->label(false) ?>
                <?= $form->field($model, 'purchaser')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Purchaser'),'disabled' => true,]) ?>
              </div>
              <div class="col-sm-6">
                <?= $form->field($model, 'branch_name',[
                                'feedbackIcon' => ['default' => 'home']
                            ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Branch/Branch Number'),'disabled' => true,])->label(false)
                            ?>
                <div class="row">
                  <div class="col-sm-6">          
                      <?= $form->field($model, 'payment_term')->dropDownList([
                        '0'=> Yii::t('common','Cash'),
                        '7'=> '7 '.Yii::t('common','Day'),
                        '15' => '15 '.Yii::t('common','Day'),
                        '30' => '30 '.Yii::t('common','Day'),
                        '45' => '45 '.Yii::t('common','Day'),
                        '60' => '60 '.Yii::t('common','Day'),
                        '90' => '90 '.Yii::t('common','Day'),
                      ],['disabled' => true]) ?>
                  </div>         
                  <div class="col-sm-6">

                      <?php // $form->field($model, 'project_name')->textInput(['maxlength' => true,'class' => 'form-control label-info']) ?>
                     
                  </div>
                </div>        
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>

    <div class="purchase-line-render table-responsive" style="margin-top:50px; margin-bottom:50px;">
      <table class="table   table-bordered sortable" id="Purchase_Line">
        <thead>
          <tr class=" ">
            <th class="bg-primary" style="width:20px;" >#</th>
            <th class="bg-primary" style="width:140px;"><?=Yii::t('common','Code')?></th>
            <th class="bg-primary" ><?=Yii::t('common','Product Name')?></th>
            <th class="bg-primary text-right" style="width:100px;" ><?=Yii::t('common','Quantity')?></th>
            <th class="bg-primary text-right" style="width:100px;" ><?=Yii::t('common','Received')?></th>
            <th class="bg-primary" style="width:150px;" ><?=Yii::t('common','Measure')?></th>
            <th class="bg-primary text-right" style="width:150px;" ><?=Yii::t('common','Unit Price')?></th>            
            <th class="bg-primary text-right" style="width:150px;" ><?=Yii::t('common','Line amount')?></th>            
          </tr>
        </thead>
        <tbody>
          <tr ng:repeat="model in PurchaseLine  | orderBy : 'priority'" data-key="{{model.id}}" data-qty="{{model.quantity}}"  ng-style="model.complete_rec  && {'background-color': '#dff0d8'} || {'color':'#6c6f88'}">
            <td class="bg-gray" ng-bind="$index +1"></td>
            <td ><a href="?r=items/items/view&id={{model.item}}" ng-bind="model.item_no" target="_blank"></a></td>
            <td ng-bind="model.description"></td>
            <td ng-bind="model.quantity | number:2" class="text-right bg-yellow"></td>
            <td class="text-right {{model.quantity - model.received > 0 ? 'bg-warning': ' '}}">
              <button type="button" class="btn btn-default-ew receive-list"><span ng-bind="model.received | number:2"></span> <i class="fas fa-caret-up"></i></button>
            </td>
            <td>
              <select ng-model="model.measure" disabled="disabled"  class="form-control field-update" ng-change="getTotalSummary()" name="unit_of_measure">
                  <option ng-repeat="option in model.unitofmeasure" value="{{option.id}}">{{option.name}}</option>
              </select>
            </td>
            <td class="text-right" ng-bind="model.unitcost | number:2"></td>           
            <td class="text-right" ng-bind="model.quantity * model.unitcost | number:2"></td>
          </tr>
        </tbody>        
      </table>
      <div class="searchItem">
      <ng ng-if="searchdata.length > 0"  >
        <div ng:repeat="s in searchdata">
          <a href="#true"  ng-click="addNew($event,PurchaseLine)" data-item="{{s.item}}" data-no="{{s.no}}" data-desc="{{s.desc_th}}" data-qty="1" data-cost="{{s.cost}}" >
            <div class="panel panel-info">
              <div class="panel-body">
                <div class="row">
                    <div class="col-md-1 col-sm-2">
                      <img src="{{s.img}}" alt="" class="img-responsive" style="min-width:50px; margin-bottom:20px;">
                    </div>
                    <div class="col-md-11 col-sm-10">
                      <div class="row">
                        <div class="col-md-10 col-xs-8" ng-bind="s.desc_th"></div>
                        <div class="col-md-2 col-xs-4 text-right">
                          <span class="find-price"><p class="price"><?=Yii::t('common','Price')?></p>{{s.cost}}</span>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12"><span class="text-sm text-gray">{{s.desc_en}}</span></div>
                        <div class="col-xs-12"><label class="text-black"><?=Yii::t('common','Code')?> : {{s.item}}</label></div>
                      </div>
                      <div class="row">
                        <div class="col-xs-8"><label><?=Yii::t('common','Stock')?></label></div>
                        <div class="col-xs-4 text-right"><span class="text-gray">{{s.inven}}</span></div>
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      </ng>
    </div>
    </div>
    <div class="row" style="margin-bottom:25px;">
      <div class="col-md-6">
        <div class="row">
          <div class="col-lg-12"  >
            <div class="well mb-10" style="margin-top:25px;">
              <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fas fa-align-left text-aqua"></i> 
                    <h3 class="box-title"><?=Yii::t('common','Remark')?> <small >(แสดงท้ายเอกสาร)</small></h3>
                </div>             
                <div class="box-body text-primary">
                  <?= $model->remark ?>
                </div>             
              </div> 
            

              <div class="box box-solid" style="margin-top:50px;">
                <div class="box-header with-border">
                    <i class="far fa-comment-dots text-yellow"></i>
                    <h3 class="box-title"><?=Yii::t('common','Detail')?> <small >(ไม่แสดงในเอกสาร)</small></h3>
                </div>             
                <div class="box-body text-red">
                  <?= $model->detail ?>
                </div>             
              </div> 
            </div>
            
            
            <?php if(($model->projects) && $model->projects->budget > 0 ) {?>
            <div class="  " id="budget">
              <div class="panel panel-warning">
                  <div class="panel-heading">
                    <h3 class="panel-title"><?=Yii::t('common','Project Control');?></h3>                    
                  </div>
                  <div class="panel-body" ng-init="budget_value=<?=$model->projects ? $model->projects->budget : 0;?>; budget_remain=<?=$model->projects ? $model->projects->remain: 0 ?>;">

                    <div class="col-xs-4"><?=Yii::t('common','Project Name');?></div>
                    <div class="col-xs-8">: <span id="budget_name"><?=$model->projects ? $model->projects->name : '';?></span></div>

                    <div class="col-xs-4"><?=Yii::t('common','Budget Remain');?></div>
                    <div class="col-xs-8">: <span id="budget_value" ng-bind="budget_remain | number:2"></span>
                    <?=$model->projects ? $model->projects->percent->progress : '';?>
                    </div>

                    <div class="col-xs-4"><?=Yii::t('common','Balance');?></div>
                    <div class="col-xs-8">: <span id="budget_balance" ng-bind="grandTotalPayment | number:2">0</span></div>
                    

                    <div class="col-xs-12" ng-if="grandTotalPayment > budget_value">
                      <h4 class="pull-left blink text-danger" id="over_budget"><?=Yii::t('common','Over Budget');?> : <span ng-bind="grandTotalPayment | number:2"></span></h4> 
                    </div>
                     
                  </div>
                  
              </div>
               
            </div>            
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-md-6 ">
          <div class=" " >
            <div class=" " style="margin-top: 25px; border: 1px solid #ccc;">
                <table class="table" style=" ">
                  <tr class="panel-heading bg-primary">
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
                        <select name="PurchaseHeader[vat_percent]"  disabled="disabled"  ng-model="vat_percent" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-vat_percent">                             
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
                          <select name="PurchaseHeader[include_vat]" disabled="disabled" ng-model="include_vat" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-include_vat">                            
                            <option ng-repeat="option in incVat" value="{{option.id}}">{{option.name}}</option>
                          </select>                                             
                      </div>
                    </th>
                    <td align="right" id="ew-after-vat" ng-bind="aftervat | number:2"></td>
                  </tr>
                  <tr>
                      <th colspan="2">ค่าขนส่ง</th>
                      <td align="right" >
                        <?=number_format($model->transport,2)?>
                      </td>
                  </tr>
                  <tr class="bg-aqua" >
                    <th colspan="2"><?=Yii::t('common','Grand total')?>: </th>
                    <td align="right" ng-bind="grandTotal | number:2"></td>
                  </tr>
                </table>
            </div>
          </div>
          <div class="panel panel-default" style="margin-top:20px;">
            <div class="panel-body">
                <div class="row">
                  <div class=" ">
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
                      <select name="PurchaseHeader[withholdTax]" disabled="disabled" ng-model="withholdingTax" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-withholdtax">
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
                <?=$form->field($model,'balance')->textInput([
                  'id' => 'total-balance',                 
                  'style' => 'background: transparent; color:#000;',
                  'ng-model' => 'grandTotal',
                  'class' => 'hidden'
                  ])->label(false) ?>
          </div>
          <div class="panel-footer" >
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
    </div>

    <div class="content-footer "  style="
      position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 10px 15px 10px;
      right: 0px;
      text-align: right;
      z-index:100;
    " >
      <div class="row">     
          <div class="col-sm-6"></div>     
          <div class="col-xs-6 col-sm-3  text-right ">
              <div class="hidden">
              <?php $model->status = 10; ?>
              <?=$form->field($model, 'status')->dropDownList([
                  '0' => Yii::t('common','Edit'),
                  '1' => Yii::t('common','Cancel'),
                  '9' => Yii::t('common','Send Approve'),
                  '10' => Yii::t('common','Approved'),
                  ],
                  [
                  'options'=>[
                      //'10'=>['disabled'=>true]
                      
                  ],
                  ])->label(false);?>
              </div>
          </div>
          <div class="col-xs-12 col-sm-6 text-left">        
              <div style="margin-left:50px;">
              <?= Html::a('<i class="fas fa-chevron-left"></i> '.Yii::t('common', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-default ']) ?>   
              
              <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                  'class' => $model->deletion == 1 ? 'hidden' : 'btn btn-danger-ew',
                  'data' => [
                      'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                      'method' => 'post',
                  ],
              ]) ?>    
              
              </div>
          </div> 
          <div class="col-xs-12 col-sm-6  text-right" >  

            <div class="btn-group dropup <?=$model->deletion == 1 ? 'hidden' : ' ';?>" >              
              <?= Html::a('<i class="fas fa-hands"></i> '.Yii::t('common','Product Receive'),['receive','id' => $model->id],['class'=>'btn btn-warning']); ?>
              <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only"><?=Yii::t('common','Options')?></span>
              </button>
              <ul class="dropdown-menu" style="border:1px solid #f29c33;">
              <li><a  data-toggle="modal" href='#modal-Payment'> <i class="far fa-credit-card"></i> <?=Yii::t('common','PAYMENT')?></a></li>  
                <li role="separator" class="divider"></li>  
                <li><a  data-toggle="modal" href='#modal-Purchase-Receive'> <i class="fas fa-tasks"></i> <?=Yii::t('common','RECEIVED')?></a></li>
              </ul>
            </div>     
             

            
            <?= Html::a('<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success ']) ?> 
            
            <?= Html::a('<i class="fas fa-print"></i> '.Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info ','target' => '_blank']) ?>   

            
          </div>             
          
      </div>
    </div>
    <?php ActiveForm::end(); ?>



 
  <div class="modal fade" id="modal-Payment">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-green">
          <a href="javascript:void(0);" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
          <h4 class="modal-title"><i class="far fa-credit-card"></i> <?=Yii::t('common','Payment')?></h4>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <a type="button" href="javascript:void(0);" class="btn btn-default" data-dismiss="modal">Close</a>
          <a type="button" href="javascript:void(0);" class="btn btn-primary">Save changes</a>
        </div>
      </div>
    </div>
  </div>

  
  <div class="modal fade" id="modal-Purchase-Receive" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg"  style="width:80%;">
      <div class="modal-content">
        <div class="modal-header bg-orange">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><i class="fas fa-tasks"></i> <?=Yii::t('common','Purchase Receive')?></h4>
        </div>

        <div class="modal-body" style="max-height:77vh; overflow-y:auto;">
        <table class="table table-hover">
            <thead>
              <tr class="bg-dark">
                <th style="width:10px;" class="text-center"><?=Yii::t('common','#')?></th>
                <th style="width:150px;"><?=Yii::t('common','Date')?></th>
                <th style="width:110px;"><?=Yii::t('common','Document No')?></th>
                <th><?=Yii::t('common','Detail')?></th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="model in PurchaseReceive.header" data-key="{{model.id}}">
                <td >{{$index +1}}</td>
                <td >{{model.date}}</td>
                <td ><a ng-bind="model.no" ng-href="index.php?r=warehousemoving%2Freceive%2Fview&id={{model.id}}&po={{model.po}}" target="_blank"></a></td>
                <td >
                  <table class="table table-bordered">
                    <thead>
                      <tr class="bg-gray">
                        <th class=" " style="width:150px;"><?=Yii::t('common','Product')?></th>
                        <th class=" " ><?=Yii::t('common','Description')?></th>
                        <th class="text-right" style="width:80px;"><?=Yii::t('common','Quantity')?></th>
                        <th class="text-right" style="width:80px;"><?=Yii::t('common','Measure')?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr ng-repeat="line in model.line" ng-if="line.type = 'Purchase'">
                        <td>{{line.item_no}}</td>
                        <td>{{line.desc}} <img ng-src="{{line.img}}" class="img-responsive pull-right" style="width:20px;"></td>
                        <td class="text-right" >{{line.qty | number}}</td>
                        <td class="text-right" >{{line.measure}}</td>
                      </tr>
                    </tbody>
                  </table>                
                </td>
              </tr>
              <tr ng-hide="PurchaseReceive.header.length"><td></td><td colspan="3"><?=Yii::t('common','Not receive')?></td></tr>
            </tbody>
          </table>
        </div>

        <div class="modal-footer">
        <a type="button" class="btn btn-default-ew pull-left  close-modal" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></a>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="modal fade" id="modal-receive-list">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common', 'Received Line')?></h4>
      </div>
      <div class="modal-body-render">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>



<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js', $Options);?>
<?php $this->registerJsFile('js/purchase/viewController.js?v=5.01.06');?>
<?php 
$js=<<<JS

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');
});


const getReceiveList = (obj, callback) => {
  fetch("?r=Purchase/order/received-list", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
                       
        if(res.status===403){
            $.notify({
                // options
                icon: 'fas fa-clock',
                message: res.message
            },{
                // settings
                type: 'warning',
                delay: 10000,
                z_index:3000,
            });    
            $.notify(
                {
                    message: res.calculating.years + ' : ' + parseInt(res.percent) + '%'
                },
                {
                    type: 'info',
                    delay: 10000,
                    z_index:3000
                }
            );            
        }
        callback(res);
    })
    .catch(error => {
        console.log(error);
    });
}

const renderTableRecievList = (data, quantity) => {
  let body = ``;

  let total = 0;
  data.map((model, keys) => {
    let qty = model.qty * 1;
        total+= qty;
    body+= `
            <tr>
              <td class="bg-gray">` + (keys + 1) + `</td>
              <td>` + model.date + `</td>
              <td><a href="index.php?r=warehousemoving%2Freceive%2Fview&id=` + model.id + `" target="_blank" >` + model.doc + `</a></td>
              <td>` + model.ref + ` </td>
              <td class="">` + model.remark + `</td>
              <td class="text-right bg-warning">` + number_format(qty.toFixed(0))+ `</td>
            </tr>
    `;
  });

  let table = `
              <table class="table table-bordered font-roboto">
                <thead>
                  <tr>
                    <th class="bg-primary" style="width:30px;">#</th>
                    <th class="bg-gray" style="width:110px;">Date</th>
                    <th class="bg-gray" style="width:120px;">No</th>
                    <th class="bg-gray" style="width:120px;">Ref.No</th>
                    <th class="bg-gray">Remark</th>
                    <th class="bg-yellow text-right" style="width:120px;">Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  ` + body + `
                </tbody>
                <tfoot>
                  <tr>
                    <td class="bg-gray"></td>
                    <td colspan="4" class="text-right"> Total </td>
                    <td class="text-right bg-yellow">` + number_format(total.toFixed(0)) + `</td>
                  </tr>
                  <tr>
                    <td class="bg-gray"></td>
                    <td colspan="4" class="text-right"> ค้างส่ง / Outstanding </td>
                    <td class="text-right bg-primary">` + number_format((quantity - total).toFixed(0)) + `</td>
                  </tr>
                </tfoot>
              </table>
  `;

  $('#modal-receive-list .modal-body-render').html(table);

}

$('body').on('click', '.receive-list', function(){

  let id        = $(this).closest('tr').attr('data-key');
  let quantity  = $(this).closest('tr').attr('data-qty');

  getReceiveList({id:id}, res => {
    $('#modal-receive-list').modal('show');
    renderTableRecievList(res.raws, quantity);
  });

})
 
JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');