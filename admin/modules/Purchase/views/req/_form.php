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

$Rules        = \common\models\AppsRules::findOne(Yii::$app->session->get('Rules')['id']);
/*$model->department  = ($model->isNewRecord)  
                        ? $Rules != null? $Rules->rulesetup->name : $model->department
                        : $model->department;*/
                        
//$model->contact     = Yii::t('common','Purchase');
$model->doc_no = ($model->isNewRecord)? $doc_no : $model->doc_no;
?>
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

    .list-inline {
      position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 10px 15px 10px;
      right: 0px;
      text-align: right;
      z-index:100;
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

    <div class="pull-right"><?=$form->field($model,'series_id')->hiddenInput()->label(false);?></div>
    <div class="panel-heading">
      <div class="row">
        <div class=" " >       
         
          <div class="row">
            <div class="text-center"  >
              <h4 class="text-primary">
                <i class="fa fa-file-text-o fa-lg"></i><span data-toggle="popover"
                title="ใบขอซื้อสินค้า(PR)"
                id="popover"
                data-content="เป็นเอกสารสำหรับแจ้งคำขอซื้อ ส่งให้เจ้าหน้าที่จัดซื้อ เพื่อเปิดใบสั่งซื้อ(PO)">  <?=Yii::t('common','PURCHASE REQUISITION')?> </span>
              </h4>
            </div>
          
            <div class="col-sm-offset-6">
              <div class="col-xs-6 pull-right">
              <?=$form->field($model,'doc_no',
                    ['addon' =>
                      ['append' =>
                        [
                        'content'=>'<span class="pointer PICK-SERIES"><i class="fa fa-angle-down" aria-hidden="true"></i></span>',
                        ]
                      ]
                    ])->textInput(['class' => 'PICK-SERIES','autocomplete' => 'off','placeholder'=>Yii::t('common','Please create a document number.')])?>
              </div>
              <div class="col-xs-12">
                <div class="row">
                  <div class="col-xs-6">
                  <?php $model->purchaser = Yii::$app->user->identity->profile->firstname.' '.Yii::$app->user->identity->profile->lastname?>
                  <?= $form->field($model, 'purchaser')->textInput(['maxlength' => true, 'placeholder' => Yii::t('common','Purchaser')]) ?>
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
                                                          
                        ]) ?>
                  </div>
                </div>
              </div>

              <div class="col-xs-12">
                  
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-3">                         
              <?= $form->field($model, 'department')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Department')])?>
            </div>
            <div class="col-sm-3"> </div>
            <div class="col-sm-6">                         
              <div class="row">
                  <div class="col-sm-6"  >
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
                                        'pluginOptions' => [
                                            //'format' => 'dd/mm/yyyy',
                                            'format' => 'yyyy-mm-dd',
                                            'autoclose'=>true
                                        ]
                                ])->label(Yii::t('common','Order date'));
                                ?>
                  </div>
                  <div class="col-sm-6">
                      <?= $form->field($model, 'delivery_date')->widget(DatePicker::classname(), [
                                    'options' => ['placeholder' => Yii::t('common','Delivery Date').'...'],
                                    'value' => date('Y-m-d',strtotime($model->delivery_date)),
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'removeButton' => false,
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
              <?= $form->field($model, 'contact')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Attention')])->label(Yii::t('common','Attention'))?>
            </div>
            <div class="col-sm-3"> </div>
            <div class="col-sm-3"> </div>
            <div class="col-sm-3">                         
              <?= $form->field($model, 'payment_term')->dropDownList([
                '0'=> Yii::t('common','Cash'),
                '7'=> '7 '.Yii::t('common','Day'),
                '15' => '15 '.Yii::t('common','Day'),
                '30' => '30 '.Yii::t('common','Day'),
                '45' => '45 '.Yii::t('common','Day'),
                '60' => '60 '.Yii::t('common','Day'),
                '90' => '90 '.Yii::t('common','Day'),
              ]) ?>
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
          <tr class="bg-gray">
            <th style="width:20px;" >#</th>
            <th style="min-width:100px;"><?=Yii::t('common','Product')?></th>
            <th style="min-width:200px;"><?=Yii::t('common','Product Name')?></th>
            <th style=" " class="text-right"><?=Yii::t('common','Quantity')?></th>
            <th style="min-width:100px;"><?=Yii::t('common','Measure')?></th>
            <th style=" " class="text-right"><?=Yii::t('common','Unit Price')?></th>
            <th style="min-width:100px;" class="text-right"><?=Yii::t('common','Line amount')?></th>
            <th style="width:50px;" class="text-center"><input type="checkbox" ng-model="selectedAll" ng:click="checkAll()" /></th>
          </tr>
        </thead>
        <tbody >
          <tr ng:repeat="model in PurchaseLine  | orderBy : 'priority'" data-key="{{model.id}}">
            <td class="move" ng-bind="$index +1"></td>
            <td ><input type="text" ng-model="model.item_no" data-id="{{model.item}}" autocomplete="off" class="form-control" name="item" readonly="readonly"> </td>
            <td ><input type="text" ng-model="model.description" autocomplete="off" class="form-control field-update" name="description"></td>
            <td class="text-right"><input 
              type="number" step="any" string-to-number autocomplete="off" ng-model="model.quantity" 
              data-key="{{model.id}}" value="" class="form-control text-right field-update pull-right" style="max-width:100px;"
              ng:change="getTotalSummary()" name="quantity" onClick="$(this).select()" />
            </td>
            <td>
              <select ng-model="model.measure"  class="form-control field-update" ng-change="getTotalSummary()" name="unit_of_measure" >                  
                  <option ng-repeat="option in model.unitofmeasure" value="{{option.id}}">{{option.name}}</option>
              </select> 
            </td>
            <td class="text-right"><input type="number" step="any" string-to-number 
              ng-model="model.unitcost" autocomplete="off"
              value="" 
              class="form-control text-right field-update pull-right" 
              ng:change="getTotalSummary()" name="unitcost" style="max-width:130px;"
              onClick="$(this).select()"/>
            </td>
            <td class="text-right" ng-bind="model.quantity * model.unitcost | number:3"></td>
            <td class="text-center"> <input type="checkbox" ng-model="model.selected"/></td>            
          </tr>
        </tbody>
        <tfoot>
          <td>  </td>
          <td colspan="6">
            <div class="ew-type input-group has-feedback ">
                <input type="text" class="form-control ew-InsertItems expand" ng-model="searchProduct" placeholder="<?=Yii::t('common','Search product, if not press ... for text message')?>" 
                ng-keyup="$event.keyCode == 13 ? findItem($event) : null"
                >
                <span class="form-control-feedback ew-pick-item-modal" aria-hidden=""><i class="glyphicon glyphicon-search"></i></span>
                <small class="text-gray">*หากใส่เลขอื่น (ที่ไม่ใช่ code) จะถือว่าเป็นข้อความ   </small>             
            </div>
          </td>
          <td>
            <div class="btn btn-danger pointer" ng:click="remove()">
            <i class="fa fa-trash-o" aria-hidden="true"></i></div>
          </td>
        </tfoot>
      </table>
      <div class="searchItem"/>
      <ng ng-if="searchdata.length > 0"  >
        <div ng:repeat="s in searchdata">
          <a href="#true"  ng-click="addNew($event,PurchaseLine)" data-key="{{s.id}}" data-item="{{s.item}}" data-no="{{s.no}}" data-desc="{{s.desc_th + ' ' + s.detail + ' ' + s.size}}" data-qty="1" data-cost="{{s.cost}}" >
            <div class="panel panel-info">
              <div class="panel-body">
                <div class="row">
                    <div class="col-md-1 col-sm-2">
                      <img src="{{s.img}}" alt="" class="img-responsive" style="min-width:50px; margin-bottom:20px;">
                    </div>
                    <div class="col-md-11 col-sm-10">
                      <div class="row">
                        <div class="col-md-10 col-xs-8" ng-bind="s.desc_th + ' ' + s.detail + ' ' + s.size"></div>
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
          <div class="col-lg-12">
            <?= $form->field($model, 'remark')->textarea(['rows' => 4, 'style' => 'font-family: saraban;']) ?>            
            <?php if($model->project!='') {?>
            <div class="  " id="budget">
              <div class="panel panel-warning font-roboto">
                  <div class="panel-heading">
                    <h3 class="panel-title"><?=Yii::t('common','Project Control');?></h3>                    
                  </div>
                  <div class="panel-body" ng-init="budget_value=<?=$model->projects->budget;?>;budget_remain=<?=$model->projects->remain?>">

                    <div class="col-xs-4"><?=Yii::t('common','Project Name');?></div>
                    <div class="col-xs-8">: <span id="budget_name"><?=$model->projects->name;?></span></div>

                    <div class="col-xs-4"><?=Yii::t('common','Budget Remain');?></div>
                    <div class="col-xs-8">: <span id="budget_value" ng-bind="budget_remain | number:2"></span></div>

                    <div class="col-xs-4"><?=Yii::t('common','Balance');?></div>
                    <div class="col-xs-8">: <span id="budget_balance" ng-bind="grandTotalPayment | number:2">100</span></div>

                    <div class="col-xs-12" ng-if="grandTotalPayment > budget_remain">
                      <h4 class="pull-left blink text-danger" id="over_budget"><?=Yii::t('common','Over Budget');?> : <span ng-bind="grandTotalPayment - budget_remain | number:2"></span></h4> 
                    </div>
                  </div>
              </div>
               
            </div>            
            <?php } ?>
            <?= $form->field($model, 'detail')->textInput(['maxlength' => true, 'placeholder' => Yii::t('common','Apply for'), 'style' => 'font-family: saraban;'])->label(Yii::t('common','Comment')) ?>
          </div>
        </div>
      </div>
      <div class="col-md-6 ">
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
                          'string-to-number'  => true
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
                          <select name="PurchaseReqHeader[vat_percent]" ng-model="vat_percent" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-vat_percent">                             
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
                          <select name="PurchaseReqHeader[include_vat]" ng-model="include_vat" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-include_vat">                            
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
                  <div class=" ">
                    <div class="col-xs-9" >   
                      <?= $form->field($model,'withholdTaxSwitch')->widget(SwitchInput::classname(), [
                        'pluginOptions' => ['size' => 'mini'],])
                      ->label(Yii::t('common','Withholding'),['style'=>'position:absolute; margin-left:70px;']); ?>
                    </div>
                    <div class="col-xs-3 tax-toggle" ng-init="holdingTag = [
                        {'id': 0,'name': '0'}, 
                        {'id': 0.5,'name': '0.5'}, 
                        {'id': 0.75,'name': '0.75'}, 
                        {'id': 1,'name': '1'}, 
                        {'id': 1.5,'name': '1.5'}, 
                        {'id': 2,'name': '2'}, 
                        {'id': 3,'name': '3'}, 
                        {'id': 5,'name': '5'}, 
                        {'id': 10,'name': '10'}, 
                        {'id': 15,'name': '15'}
                      ];
                      withholdingTax = '5';">    
                      <select name="PurchaseReqHeader[withholdTax]" ng-model="withholdingTax" ng-change="getTotalSummary()" class="form-control" id="purchasereqheader-withholdtax">
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
        
        <div class="col-xs-6 col-sm-3  text-right">        
          <div class="form-group">          
            
          </div>
        </div>        
        <ul class="list-inline pull-right text-right">                    
          <li><?= Html::a('<i class="fa fa-print" aria-hidden="true"></i> '. Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info-ew', 'target' => '_blank']) ?></li>
          <li><?= Html::submitButton('<i class="fa fa-floppy-o"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?></li>
        </ul>      
      </div>
    
    
    <?php ActiveForm::end(); ?>
</div>

<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js', $Options);?>
<?php $this->registerJsFile('@web/js/jquery.ui.touch-punch.min.js',$Options);?>
<?php $this->registerJsFile('js/item-picker.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/no.series.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/purchase/vendors.js?v=3.04.03.1');?>
<?php $this->registerJsFile('js/purchase/req.js?v=3.04.25');?>
<?php $this->registerJsFile('js/purchase/reqController.js?v=5.05.18.1');?>
<?= $this->render('@admin/views/setupnos/__modal'); ?>

<?php
 
$js   = <<<JS
          
          $(document).ready(function(){
            
            
            
          });

          $(document).click(function(e) {
            if($(e.target).closest('div.searchItem').attr('class') != 'searchItem') {
              $('.searchItem').slideUp('fast');
            }
          });

          $(document).on('pjax:send', function() {
              console.log('pjax:send');
          });
          $(document).on('pjax:complete', function() {
              console.log('pjax:complete');
          });
          $(document).on('pjax:success', function() {
              console.log('pjax:success');
              var num = $('form#companySearch input.submit-search').val();
              $('form#companySearch input.submit-search').focus().val('').val(num);
          });
          $(document).on('pjax:error', function() {
              console.log('pjax:error');
          });
          $(document).on('pjax:timeout', function() {
              console.log('pjax:timeout');
          });

          $(document).click(function(e) {

            if($(e.target).data('toggle') != 'popover') {
              $('#popover').popover('hide');
            }


          });

          $(function () {
              $('[data-toggle=\"popover\"]').popover()
          })


          $('table.sortable tbody ').sortable({
        		update: function(e,ui){

                    var index = 1;
                    $($('td.move')).each(function(){
                      $(this).text(index);
                      index = ++index;
                    });

                   var lis = $('table.sortable tbody tr');
                   var ids = lis.map(function(i,el){
                   		return {id:el.dataset.key}
                    }).get();

                   //console.log(JSON.stringify(ids));
                   //console.log(ids);
                   $.ajax({
        						 url:'index.php?r=Purchase/req/view-line&id='+$('table.sortable tbody tr').attr('data-key'),
        						 type:'POST',
        						 data:{ids:ids},
        						 success:function(data){
        							 $('.Navi-Title').html(data);
        						 }
        					 });
                 }
        	});

 

          
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>


 