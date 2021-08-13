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
/* @var $this yii\web\View */
/* @var $model common\models\PurchaseHeader */
/* @var $form yii\widgets\ActiveForm */
/* Remember session id */
//var_dump($_SERVER[]);
Yii::$app->session->set('P-Order',$model->id);
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
      padding: 10px 10px 10px 10px;
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

    .input-group span.input-group-addon{
      border-right:1px solid #ccc !important;
    }

    .vendorFilter .input-group{
      border-right: 1px solid #ccc;
    }
    .table{
      margin-bottom: 0px;
    }

    .tax-toggle{
      <?= $model->withholdTaxSwitch!=1 ? 'display:none;' : 'display:visable;'; ?>
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

    #Purchase_Line{
      margin-left:3px;
    }


    .dropdown-toggle{
      z-index: 1000 !important;
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

<div class="purchase-header-form" ng:controller="purchaseOrder" style="font-family: saraban;">
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
                  <?php

                    // $GenSeries        = new admin\models\Generater();
                    // $NoSeries         = $GenSeries->getRuning('purchase_header','vendor_type','1');
                    //
                    // if($model->doc_no == '') $model->doc_no    = $NoSeries;
                    //echo '<div class="ew-inv-inputno" ew-no="'.$model->id.'" style="width: 165px;">'.$form->field($model,'doc_no')->label(false).'</div>';

                    //$model->doc_no = $model->getSeries();

                    echo $form->field($model,'series_id')->hiddenInput()->label(false);
                  ?>
              </div>
                  <div class="text-center">
                    <h4 class="text-primary">
                      <i class="fa fa-file-text-o fa-lg"></i><span data-toggle="popover"
                      title="ใบสั่งซื้อสินค้า"
                      id="popover"
                      data-content="เป็นเอกสารสำหรับสั่งซื้อสินค้า ส่งให้ผู้ขายสินค้า หลังจากนั้นผู้ขายจะส่งสินค้ามาให้ตามกำหนด"> <?=Yii::t('common','PURCHASE ORDER')?> </span>
                    </h4>
                  </div>
                  <div class="row">
                    <div class="col-sm-12" id="header-top">
                      <div id="company-infomation" style="position:absolute; left: 10px; filter: opacity(0.5);">
                          <div id="company-logo">
                              <img src="images/company/logo/<?=$model->company ? $model->company->logo : ''?>" width="80" />
                              <?=$model->company ? $model->company->name : ''; ?>
                          </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-6 col-sm-3 pull-right">
                          <?=$form->field($model,'doc_no',
                            ['addon' =>
                              ['append' =>
                                [
                                'content'=>'<span class="pointer PICK-SERIES"><i class="fa fa-angle-down" aria-hidden="true"></i></span>',
                                ]
                              ]
                            ])->textInput(['class' => 'PICK-SERIES','autocomplete' => 'off','placeholder'=>Yii::t('common','Please create a document number.')])?>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-6 col-sm-3 pull-right">
                          <?php
                            $List = arrayHelper::map((\common\models\ProjectControl::find()
                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->orderBy(['budget' => SORT_ASC])
                            ->all()),'id','name');

                            echo $form->field($model, 'project')->dropDownList($List,['data-live-search'=> "true",'class' => 'selectpicker col-lg-12']); 
                          ?>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                              
                                <div class="col-xs-6 col-sm-3  pull-right"  >
                                    <?php
                                        if($model->order_date=='') {
                                          $model->order_date = date('Y-m-d');
                                        }else {
                                          $model->order_date = date('Y-m-d',strtotime($model->order_date));
                                        }
                                        echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                                                      'options' => [
                                                        'placeholder'   => Yii::t('common','Order date').'...',
                                                        'autocomplete'  => "off"
                                                      ],
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

                                        <?= $form->field($model, 'ref_pr', [
                                          'feedbackIcon' => ['default' => 'link']
                                        ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','PR Reference')]) ?>
                                </div>
                                <div class="col-xs-6 col-sm-3  pull-right">
                               
                                   <?= $form->field($model, 'delivery_date')->widget(DatePicker::classname(), [
                                                 'options' => ['placeholder' => Yii::t('common','Delivery Date').'...'],
                                                 'value' => date('Y-m-d',strtotime($model->delivery_date)),
                                                 'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                                 //'removeButton' => false,
                                                 'pluginOptions' => [
                                                     //'format' => 'dd/mm/yyyy',
                                                     'format' => 'yyyy-mm-dd',
                                                     'autoclose'=>true
                                                 ]
                                         ])->label(Yii::t('common','Delivery Date')); ?>

                                  <?= $form->field($model, 'purchaser')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Purchaser')]) ?>
                                </div>
                                
                            </div>
                            
                        </div>
                      </div>
                      <div class="row ">
                        <div class="col-xs-3"> </div>
                        <div class="col-xs-3">                              
                          
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
                 'class' => 'VENDOR-PICKER',
                 'placeholder' => Yii::t('common','Please select a vendor.'),
                 'autocomplete' => 'off'
                 ])?>
            </div>
            <?=$form->field($model,'vendor_id')->hiddenInput()->label(false)?>

            <div class="row">
                <div class="col-xs-6">
                  <?= $form->field($model, 'taxid',[
                                        'feedbackIcon' => ['default' => 'tag']
                                    ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Tax ID')])->label(false) ?>
                </div>
                <div class="col-xs-6">
                  <?= $form->field($model, 'branch_name',[
                                    'feedbackIcon' => ['default' => 'home']
                                ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Branch/Branch Number')])->label(false)
                                ?>
                </div>
            </div>
            <?= $form->field($model, 'address', [
              'feedbackIcon' => ['default' => 'comment']
            ])->textArea(['maxlength' => true,'style' => 'height:82px;'])
            ?>
           
           
            <?= $form->field($model, 'delivery_address', [
                'feedbackIcon' => ['default' => 'glyphicon glyphicon-globe']
            ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Ship Address')])
            ->label(false) ?>
     
           
        </div>
        <div class="col-sm-7">
            <div class="row">
              <div class="col-xs-6">
                     
                <?= $form->field($model, 'payment_term')->dropDownList([
                    '0'=> Yii::t('common','Cash'),
                    '7'=> '7 '.Yii::t('common','Day'),
                    '15' => '15 '.Yii::t('common','Day'),
                    '30' => '30 '.Yii::t('common','Day'),
                    '45' => '45 '.Yii::t('common','Day'),
                    '60' => '60 '.Yii::t('common','Day'),
                    '90' => '90 '.Yii::t('common','Day'),
                  ],
                  [
                    //'ng-change' => "calTermOfPayment()",
                    //'ng-model'  => 'payment_term',
                    'options' =>  [
                      '10'  =>  ['disabled'=>true]
                    ]
                  ]) ?>
              </div>
              <div class="col-xs-6">
                <?= $form->field($model, 'payment_due')->widget(DatePicker::classname(), [
                        'options' => [
                          'placeholder'   => Yii::t('common','Payment Date').'...',
                          'autocomplete'  => "off"
                        ],
                        'value' => date('Y-m-d',strtotime($model->payment_due)),
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        //'removeButton' => false,
                        'pluginOptions' => [
                            //'format' => 'dd/mm/yyyy',
                            'format' => 'yyyy-mm-dd',
                            'autoclose'=>true
                        ]
                ])->label(Yii::t('common','Payment Date')); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <?= $form->field($model, 'ref_no')->textInput([
                  'maxlength' => true, 
                  'placeholder' => Yii::t('common','Ref No')
                  ])->label(false) ?>
              </div>
              <div class="col-sm-6">
                <?=$form->field($model,'ext_document',
                  ['addon' =>
                  ['append' =>
                    [
                      'content'=>'<i class="fa fa-paperclip" aria-hidden="true" ></i>',
                    ]
                  ]
                  ])->textInput(['placeholder' => Yii::t('common','External Document')])->label(false)?>
              </div>
            </div>
            <div class="row collapse in" id="vendor-address2">
              <div class="col-xs-6"></div>
              <div class="col-xs-6">                
                <!-- Hidden Reference -->
                <?= $form->field($model,'refer_id')->hiddenInput()->label(false);?>
                <?= $form->field($model,'refer_name')->hiddenInput()->label(false);?>
                <?= $form->field($model,'vender_type')->hiddenInput()->label(false);?>
              </div>
            </div>
            <span class="pointer pull-right text-primary" data-toggle="collapse" data-target="#order-project">
              <i class="fa fa-caret-square-o-down "></i> <?=Yii::t('common','More')?>
            </span>

            <div class="row">
              <div class="col-xs-12 collapse" id="order-project">
                <div class="row">
                  <div class="col-sm-6">
                      <?= $form->field($model, 'phone',
                      [
                          'feedbackIcon' => ['default' => 'phone']
                      ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Phone')])->label(false) ?>

                      
                  </div>
                  <div class="col-sm-6">
                      <?= $form->field($model, 'contact', [
                          'feedbackIcon' => ['default' => 'headphones']
                      ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Contact')])->label(false) ?>
                      
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6">        
                    <?= $form->field($model, 'email', [
                        'feedbackIcon' => ['default' => 'envelope']
                      ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Email')])->label(false) ?>
                  </div>
                  <div class="col-sm-6">
                    <?= $form->field($model, 'fax', [
                        'feedbackIcon' => ['default' => 'print']
                      ])->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Fax')])->label(false) ?>

                    
                  </div>
                  <div class="col-sm-6">          
                      
                  </div>         
                </div>
              </div>
            </div>

        </div>
    </div>
   
    <div class="row">
      
      <div class="col-sm-7">
          
      </div>
    
    <div class="row">
      <div class="col-xs-12 text-right">
        <a class="btn btn-success-ew" data-toggle="modal" ng-click="getSourceRequest()" href='#ew-modal-source'><i class="fa fa-plus"></i> <?=Yii::t('common','Get source document')?></a>
      </div>
    </div>


    

    <div class="purchase-line-render table-responsive">
      <table class="table   table-bordered sortable" id="Purchase_Line">
        <thead>
          <tr class="bg-gray">
            <th style="width:20px;" >#</th>
            <th style="min-width:100px;"><?=Yii::t('common','Product')?></th>
            <th style="min-width:200px;"><?=Yii::t('common','Product Name')?></th>
            <th style="min-width:100px;" class="text-right"><?=Yii::t('common','Quantity')?></th>
            <th style="min-width:100px;"><?=Yii::t('common','Measure')?></th>
            <th style="min-width:100px;" class="text-right"><?=Yii::t('common','Unit Price')?></th>
            <th style="min-width:100px;"><?=Yii::t('common','Line amount')?></th>
            <th style="width:50px;" class="text-center"><input type="checkbox" ng-model="selectedAll" ng:click="checkAll()" /></th>
          </tr>
        </thead>
        <tbody >
          <tr ng-if="!PurchaseLine">
              <td colspan="8" class="text-center mt-5 mb-5"><i class="fas fa-sync-alt fa-spin fa-2x"></i></td>
          </tr>
          <tr ng:repeat="model in PurchaseLine  | orderBy : 'priority'" data-key="{{model.id}}">
            <td class="move" ng-bind="$index +1"></td>
            <td >
              <input type="text" 
              ng-model="model.item_no" 
              ng-disabled="model.complete_rec" 
              data-id="{{model.item}}" 
              autocomplete="off" 
              class="form-control" 
              name="item" 
              readonly="readonly"/> 
            </td>
            <td ><input type="text" 
            ng-model="model.description" 
            ng-disabled="model.complete_rec" 
            autocomplete="off" 
            class="form-control field-update" 
            ng-keyup="nextField($event, 'quantity_', $index, false)"
            name="description"/> </td>
            <td ><input 
              type="number" step="any" string-to-number autocomplete="off" ng-model="model.quantity" 
              data-key="{{model.id}}" 
              value="" 
              class="form-control text-right field-update" 
              ng-disabled="model.complete_rec"
              id=quantity_{{$index}}
              ng-keyup="nextField($event, 'unitcost_', $index, false)"
              ng:change="getTotalSummary()" 
              name="quantity" 
              onClick="$(this).select()" />
            </td>
            <td>
              <select ng-model="model.measure" ng-disabled="model.complete_rec"  class="form-control field-update" ng-change="getTotalSummary()" name="unit_of_measure"   >                  
                  <option ng-repeat="option in model.unitofmeasure" value="{{option.id}}">{{option.name}}</option>
              </select> 
            </td>
            <td ><input type="number" step="any" string-to-number 
              ng-model="model.unitcost" 
              autocomplete="off"
              value="" 
              id=unitcost_{{$index}}
              class="form-control text-right field-update" 
              ng:change="getTotalSummary()" name="unitcost" 
              ng-disabled="model.complete_rec"
              ng-keyup="nextField($event, 'quantity_', $index +1, ($index +1 == PurchaseLine.length ? true : false))"
              onClick="$(this).select()"/>
            </td>
            <td class="text-right" ng-bind="model.quantity * model.unitcost | number:2"></td>
            <td class="text-center"> <input type="checkbox" ng-disabled="model.complete_rec" ng-model="model.selected"/></td>            
          </tr>
        </tbody>
        <tfoot>
          <td>  </td>
          <td colspan="6">
            <div class="ew-type input-group has-feedback ">
                <input type="text" 
                class="form-control ew-InsertItems expand" 
                ng-model="searchProduct" 
                placeholder="<?=Yii::t('common','Search product, if not press ... for text message')?>" 
                id="searchProduct"
                ng-keyup="$event.keyCode == 13 ? findItem($event) : null" />
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
      </div>
      <div class="searchItem">
      <ng ng-if="searchdata.length > 0"  >
        <div ng:repeat="s in searchdata">
          <a href="#true"  ng-click="addNew($event,PurchaseLine)" data-item="{{s.item}}" data-id="{{s.id}}"  data-no="{{s.no}}" data-desc="{{s.desc_th + ' ' + s.detail + ' ' + s.size}}" data-qty="1" data-cost="{{s.cost}}" >
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
    <div class="row"  style="margin-top:100px;">
      <div class="col-md-6">
        <div class="row">
          <div class="col-lg-12" style="font-family: saraban;" >
            <?php // $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
            <?php echo $form->field($model, 'remark')->widget(\yii\redactor\widgets\Redactor::className(), [
                            'clientOptions' => [
                                'imageManagerJson' => ['/redactor/upload/image-json'],
                                'imageUpload' => ['/redactor/upload/image'],
                                'fileUpload' => ['/redactor/upload/file'],
                                'lang' => 'th',
                                'plugins' => ['fontcolor']
                            ]
                        ])?>
          </div>
          <div class="col-lg-12 mt-10" style="font-family: saraban;">
            <?= $form->field($model, 'detail')->textarea(['rows' => 6])->label(Yii::t('common','Comment').' <small class="text-yellow">(ไม่แสดงในเอกสาร)</small>')?>
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
                              'class'=>'text-right no-border ', 
                              'ng-change' => 'getTotalSummary()', 
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
                          <select name="PurchaseHeader[vat_percent]" ng-model="vat_percent" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-vat_percent">                             
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
                          <select name="PurchaseHeader[include_vat]" ng-model="include_vat" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-include_vat">                            
                            <option ng-repeat="option in incVat" value="{{option.id}}">{{option.name}}</option>
                          </select>                                             
                      </div>
                    </th>
                    <td align="right" id="ew-after-vat" ng-bind="aftervat | number:2"></td>
                  </tr>
                  <tr>
                      <th colspan="2">ค่าขนส่ง</th>
                      <td align="right" >
                        <?=$form->field($model,'transport')->textInput([
                            'placeholder' => 0,
                            'class'=>'text-right',
                            'ng-model' => 'transport',
                            'ng-change' => 'getTotalSummary()',
                            'type'=>"number",
                            'step'=>"any",
                            'string-to-number'  => true
                          ])->label(Yii::t('common','Transport'))->label(false)
                          ?>
                      </td>
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
                      <select name="PurchaseHeader[withholdTax]" ng-model="withholdingTax" ng-change="getTotalSummary()" class="form-control" id="purchaseheader-withholdtax">
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
          <?php //$model->status = 10; ?>
          <?=$form->field($model, 'status')->dropDownList([
              '0' => Yii::t('common','Edit'),
              '1' => Yii::t('common','Cancel'),
              '9' => Yii::t('common','Send Approve'),
              '10' => Yii::t('common','Approved'),
            ],
            [
              'options'=>[
                '10'=>['disabled'=>true]
                
              ],
            ])->label(false);?>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3  text-right " style="margin-bottom:40px;">        
        <div class="form-group">          
          
        </div>
      </div>              
      <ul class="list-inline pull-right text-right">                    
          <li><?= Html::a('<i class="fa fa-print" aria-hidden="true"></i> '. Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info-ew', 'target' => '_blank']) ?></li>
          <li><?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '. Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?></li>
      </ul>
    </div>
    
    <?php ActiveForm::end(); ?>


  <?php
    
    // $reqLine = \common\models\PurchaseReqLine::find()
    // ->joinwith('purchaseLine')
    // ->where(['purchase_line.source_id' => $model->id]);
    // $sources = [];
    // foreach ($reqLine->all() as $key => $line) {
    //   $sources['id']   = $line->id;
    //   $sources['item'] = $line->item;
    // }
    // $source = str_replace('"',"'",json_encode([$sources]));
  ?>

  <div class="modal  fade modal-full" id="ew-modal-source" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog "  >
      <div class="modal-content">
        <div class="modal-header bg-green">
          <a type="button" class="close" data-dismiss="modal" >&times;</a>
          <h4 class="modal-title"><i class="fas fa-check"></i> <?=Yii::t('common','Select Source')?> </h4>
        </div>

        <div class="modal-body"  >
          <div class="row">
              <div class="col-sm-6">
                  <div class="input-group">
                      <input type="text" name="search-shipment" class="form-control" id="ew-search-ship" placeholder="ค้นหา..." ng-model="searchText">
                      <span class="input-group-btn">
                        <button type="button" name="search" id="ew-search-ship-btn" class="btn btn-default btn-flat"><i class="fa fa-search"></i>
                        </button>
                      </span>
                  </div>
              </div>
          </div>
          <div class="table-responsive">
          <table class="table   table-bordered table-hover margin-top">
            <thead>
              <tr class="bg-dark">
                <th style="width:50px;" class="text-center"><input type="checkbox" ng-model="checkSource" ng:click="checkAllSource()" /></th>                
                <th ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','Document No(PR)')?></th>
                <th ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','Purchaser')?></th>
                <th ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','Apply for')?></th>   
                <th ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','Project')?></th>              
              </tr>
            </thead>
            <tbody >
              <tr ng:repeat="source in Sources  | orderBy:orderByField:reverseSort  | filter:searchText" data-key="{{source.id}}" data-id="{{source.id}}" ng-click="rowClicked(source)" class="pointer"
                ng-style="filterItems[source.id]  && {'background-color': '#afafaf','color': '#fff'} || {'color':'#000'}">
                <td class="text-center"> <input type="checkbox" ng-model="filterItems[source.id]"   ng-click="toggleObjSelection($event, source.id)"/></td>
                <td class=" " ng-bind="source.no"></td>
                <td class=" " ng-bind="source.owner_name"></td>
                <td class=" " ng-bind="source.apply"> </td>    
                <td class=" " ng-bind="source.project"> </td>                         
              </tr>
            </tbody>          
          </table>
          </div>
        </div>
        <div class="modal-footer">
           
          <a type="button" class="btn btn-default-ew pull-left close-modal" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></a>

          <a type="button" class="btn btn-info ew-get-ship"   ng-click="openModalConfirmReq(); selectPurchaseLine();"   > <i class="fa fa-check"></i>  <?=Yii::t('common','Select')?></a>
        </div>
      </div>
    </div>
  </div>



  
 
  <div class="modal fade" id="confirm-req"  role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:90%">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
          <h4 class="modal-title"><i class="fas fa-check"></i> <?=Yii::t('common','Confirm Items')?></h4>
        </div>
         <div class="table-responsive">
          <table class="table  table-hover table-bordered margin-top">
            <thead>
              <tr class="">
                <th style="width:50px;" class="text-center"><input type="checkbox"  ng-model="checkList" ng:click="checkAllList()" /></th>
                <th class="pointer" ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','PR')?></th>
                <th class="pointer" ng-click="orderByField='code'; reverseSort = !reverseSort"><?=Yii::t('common','code')?></th>
                <th class="pointer" ng-click="orderByField='desc'; reverseSort = !reverseSort"><?=Yii::t('common','Description')?></th>
                <th class="pointer" ng-click="orderByField='qty'; reverseSort = !reverseSort"><?=Yii::t('common','Quantity')?></th>
                <th class="pointer" ng-click="orderByField='measure'; reverseSort = !reverseSort"><?=Yii::t('common','Unit')?></th>
                <th class="pointer" ng-click="orderByField='cost'; reverseSort = !reverseSort"><?=Yii::t('common','Cost/Unit')?></th>
                <th ><?=Yii::t('common','Price')?></th>         
              </tr>
            </thead>
            <tbody >
              <tr ng:repeat="source in SourceList  | orderBy:orderByField:reverseSort | filter:testFilter" data-key="{{source.id}}" ng-click="rowClickedList(source)" class="pointer"
                ng-style="source.selected  && {'background-color': 'rgb(249, 254, 255)'} || {'background-color':'transparent'}">
                <td class="text-center"> <input type="checkbox" ng-model="source.selected" ng-click="toggleObjSelection($event, source.apply)"/></td>
                <td class=" " ng-bind="source.source" ng-style="source.genpo.count <= 0  && {'color': '#000'} || {'color':'red'}"> </td>    
                <td class=" " ng-bind="source.code" ng-style="source.genpo.count <= 0  && {'color': '#000'} || {'color':'red'}"></td>
                <td class=" " ng-bind="source.desc" ng-style="source.genpo.count <= 0  && {'color': '#000'} || {'color':'red'}"> </td>    
                <td class=" " ng-bind="source.qty"> </td>      
                <td class=" " ng-bind="source.measure"></td>
                <td class=" " ng-bind="source.cost"></td>
                <td class=" " ng-bind="source.qty * source.cost | number:2"></td>                   
              </tr>
            </tbody>          
          </table>
          </div>
        <div class="modal-footer">
          <a type="button" class="btn btn-default-ew pull-left  close-modal" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></a>
          <a type="button" class="btn btn-primary ew-get-ship" ng-click="confirmRequisition(SourceList)"> <i class="fas fa-download"></i>  <?=Yii::t('common','Confirm')?></a>
        </div>
      </div>
    </div>
  </div>
  
  

</div>
<?php $Options = ['depends' => [\yii\web\JqueryAsset::className()]]; ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js', $Options);?>
<?php $this->registerJsFile('@web/js/jquery.ui.touch-punch.min.js',$Options);?>
<?php $this->registerJsFile('@web/js/item-picker.js?v=4.06.04');?>
<?php $this->registerJsFile('@web/js/no.series.js?v=3.04.03.1');?>
<?php $this->registerJsFile('@web/js/purchase/vendors.js?v=4.10.27');?>
<?php $this->registerJsFile('@web/js/purchase/order.js?v=5.01.31');?>
<?php $this->registerJsFile('@web/js/purchase/purController.js?v=5.06.20.1');?>
<?= $this->render('@admin/views/setupnos/__modal'); ?>

<?php
 
$js   = <<<JS
          
$(document).ready(function() {
    setTimeout(() => {
        $("body")
            .addClass("sidebar-collapse")
            .find(".user-panel")
            .hide();        
    }, 1000);
    setTimeout(() => { 
          $('body').find('.user-panel').hide(); 
      }, 1500);
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
            url:'index.php?r=Purchase/purchase-line/view&id='+$('table.sortable tbody tr').attr('data-key'),
            type:'POST',
            data:{ids:ids},
            success:function(data){
              $('.Navi-Title').html(data);
            }
          });
        }
});

// $(function() {

//   // console.log($('input[name=\"PurchaseHeader[withholdTaxSwitch]\"]').bootstrapSwitch('this'));

//   $('input[name=\"PurchaseHeader[withholdTaxSwitch]\"]').on('switchChange.bootstrapSwitch', function(event, state) {
//     //console.log(this); // DOM element
//     //console.log(event); // jQuery event
//     //console.log(state); // true | false
//     if(state){
//       console.log('on');
//       $('.tax-toggle').slideDown();
//       //$('#purchaseheader-withholdtax').val(5);
//       $('#purchaseheader-withholdtax').val(0);
//       // if($('#purchaseheader-withholdtax').val()=='? object:null ?'){
//       //   $('#purchaseheader-withholdtax').val(0);
//       // }
//     }else {
//       console.log('off');
//       $('.tax-toggle').slideUp();
//       $('#purchaseheader-withholdtax').val(0);
//     }
//   });
// });

          
JS;

$this->registerJs($js,\yii\web\View::POS_END);
?>


 