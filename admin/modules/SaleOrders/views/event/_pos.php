<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Company;
$comp = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();


/* @var $this yii\web\View */
/* @var $model common\models\SaleEventHeader */
/* @var $form yii\widgets\ActiveForm */
?>
 
<?php $this->registerCssFile('@web/css/sale-pos-style.css?v=3.05.05.5');?>
<div class="sale-event-header-form" ng-controller="pointOfSale" esc-key="resetFilter()">

    <?php $form = ActiveForm::begin([
            'id' => 'form-event-order',
            'enableClientValidation' => true,
            'enableAjaxValidation' => true,
            'options' => [
              'enctype' => 'multipart/form-data',
              'data-key' => isset($_GET['id'])? $_GET['id'] : '0',
            ]
        ]); ?>
        
    <div class="row frame-slip">
        <div class="col-md-8 no-float product-zone" style="height:100%;">

            <!-- tabs -->
            <div class="tabbable">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#pos" data-toggle="tab"><i class="fas fa-barcode text-red"></i> <?=Yii::t('common','POS')?></a></li>
                    <li><a href="#product" data-toggle="tab"><i class="fas fa-cube text-aqua blink"></i> <?=Yii::t('common','PRODUCT')?></a></li>
                    <!-- <li><a href="#twee" data-toggle="tab">Twee</a></li> -->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pos">
                        <!-- sale-content -->
                        <div class="sale-content">
                        <div class="panel <?=($model->status=='closed')? 'panel-danger' : 'panel-default'; ?> panel-flat">
                            <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-12">

                                <div class="col-sm-9 col-xs-6 no-padding">
                                    <label><?=Yii::t('common','Barcode')?></label>
                                    <input type="text" class="form-control search-barcode input-lg"  ng-model="search" ng-keyup="$event.keyCode == 13 ? searchProduct($event) : null" placeholder="<?=Yii::t('common','Barcode')?>" />
                                </div>
                                <div class="col-sm-1 col-xs-2 no-padding">
                                    <label class="text-right " style="width:100%;">%<?=Yii::t('common','Discount')?></label>
                                    <input type="text" class="form-control text-right input-lg  label-success next-focus-search" ng-model="percdiscount" ng-click="percdiscount = null" numbers-only/>
                                </div>
                                <div class="col-sm-1 col-xs-2 no-padding">
                                    <label class="text-right" style="width:100%;"><?=Yii::t('common','Discount')?></label>
                                    <input type="text" class="form-control text-right input-lg label-primary next-focus-search" ng-model="discount" ng-click="discount = null" numbers-only/>
                                </div>
                                <div class="col-sm-1 col-xs-2 no-padding">
                                    <label class="text-right" style="width:100%;"><?=Yii::t('common','Quantity')?></label>
                                    <input type="text" class="form-control text-right input-lg label-warning next-focus-search" ng-model="qtyperunit" ng-click="qtyperunit = null"  numbers-only/>
                                </div>
                                </div>

                            </div>
                            </div>
                            <div class="panel-body" >

                            <div class="responsive row">

                            <table class="table table-striped table-bordered table-pos">
                                <thead>
                                    <tr style="background-color:#ccc;">
                                        <th class="hidden-xs"># </th>
                                        <th class="hidden-xs"><?=Yii::t('common','Barcode')?></th>
                                        <th style="min-width:170px;"><?=Yii::t('common','Product Name')?></th>
                                        <!-- <th><?=Yii::t('common','Unit')?></th> -->
                                        <th class="text-right" style="width:70px;"><?=Yii::t('common','Quantity')?></th>
                                        <th class="text-right"><?=Yii::t('common','Price')?></th>
                                        <th class="text-right"><?=Yii::t('common','Discount')?></th>
                                        <th class="text-right actions-style" style="padding-right:50px;"><?=Yii::t('common','Sum')?></th>
                                    </tr>
                                </thead>
                                <tbody ng:if="saleEventline.length > 0">
                                    <tr ng:repeat="model in saleEventline" data-key="{{model.id}}" data-no="{{model.no}}" ng-right-click="Rightmenu($event)">
                                        <td class="menu hidden-xs" data-key="{{model.id}}" style="height:51px;" ng-bind="$index+1"></td>
                                        <td ng-dblclick="menuDbSlide($event)" class="alias-pointer hidden-xs" ng-bind="model.barcode"></td>
                                        <td ng-bind="model.name"></td>
                                        <!-- <td ng-bind="model.unit"></td> -->
                                        <td class="text-right">
                                            <input data-key="{{model.id}}" type="number" step=any ng-pattern="/^-?[0-9][^\.]*$/"  ng-click="model.quantity=''" ng-model="model.quantity" ng-blur="changeQty(this)" class="form-control barcode-qty" <?=(!$model->rc_money)?: 'readonly="readonly"'?>>
                                        </td>
                                        <td class="text-right text-green" ng-bind="model.price | number"></td>
                                        <td class="text-right" ng-bind="model.discount * model.quantity  | number"></td>
                                        <td name="menu" class="text-right actions-render" >
                                            <span data-ng-bind="model.quantity * (model.price - model.discount)  | number"></span>
                                            <div class="action-menu-pos hidden-xs"  ng-click="menuSlide($event)"> 
                                                <i class="fas fa-bars"></i> 
                                            </div>
                                            <div class="hidden-sm hidden-md hidden-lg pull-right btn-delete-line" >
                                                <i class="fas fa-times btn btn-danger-ew btn-flat btn-sm" ng-click="deleline($event)" data="{{model.id}}"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                            </div>
                            <div class="panel-footer">
                            <div class="row">
                                <div class="col-sm-8 col-xs-6 ">
                                <div class="row">


                                    <div class="col-xs-12">
                                    <div align="left" ng-bind-html="description"></div>
                                    </div>

                                </div>
                                </div>
                                <div class="col-sm-4 col-xs-6 " >
                                    <div class="subtotal" ng-bind="subtotal|number:2"></div>
                                </div>
                            </div>

                            </div>
                        </div>
                        </div>


                        <div class="form-group text-right">
                            <?= $form->field($model, 'no')->hiddenInput(['maxlength' => true])->label(false) ?>
                            <div class="hidden">
                            <?= $form->field($model, 'balance')->textInput(['maxlength' => true,'ng-model' => 'subtotal'])->label(false) ?>
                            </div>


                        </div>
                        <!-- /sale-content -->    
                    </div>
                    <div class="tab-pane" id="product">
                        <!-- product-picker -->
                        <div class="product-picker margin-top">
                            
                            <?=$this->render('product_picker')?>
                        </div>
                        <!-- /product-picker -->

                    </div>
                    <!-- <div class="tab-pane" id="twee"> Total </div> -->
                </div>
            </div>
            <!-- /tabs -->        
            
        </div><!-- /col-md-8 -->

        <div class="col-md-4 no-float">
             
            <div class="slip">
                <div class="slip-heading">
                    <div class="row">
                        <div class="col-xs-2">
                            <img src="images/company/<?=$comp->logo?>" style="width: 70px;">
                        </div>
                        <div class="col-xs-10 text-right">
                            <h5><?=$comp->name?></h4>
                            <div class="slip-address">
                                <?=$comp->vat_address?> <?=$comp->vat_city?> <?=$comp->vat_location?> 
                            </div>
                            <div class="slip-tex">
                                เลขประจำตัวผู้เสียภาษี : <?=$comp->vat_register?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-center margin-top">
                        TAX#<?=$model->no?>
                    </div>
                    <div class="col-xs-12 text-center margin-top">
                        ใบเสร็จรับเงิน/ใบกำกับภาษีอย่างย่อ
                    </div>
                </div>
                 
                <table class="table" id="slip-table">
                    <thead>
                    <tr>
                        <th class="text-right"><!-- จำนวน --> </th>
                        <th>รายการ</th>
                        
                        <!-- <th class="text-right">ราคา</th> -->
                        <th class="text-right">ราคา</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr ng:repeat="model in saleEventline" >
                        <td class="text-left" ng-bind="model.quantity | number"></td>
                        <td ng-bind="model.name |limitTo :25"></td>
                        
                        <!-- <td class="text-right">{{model.price - model.discount  | number}}</td> -->
                        <td class="text-right" ng-bind="model.quantity * (model.price - model.discount)  | number"></td>
                        </tr>
                    </tbody>
                </table>
                 
                <div class="row">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    Total :
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="total|number:2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    Discount :
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="sumdiscount|number:2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    After Discount:
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="subtotal|number:2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    Before Vat:
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="subtotal / 1.07|number:2"></div>
                </div>
                <div class="row">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    Vat 7%:
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="subtotal - (subtotal / 1.07)|number:2"></div>
                </div>
                <div class="row margin-top">
                    <div class="col-xs-8 " style="padding-left:22px;">
                    Grand Total :
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;" ng-bind="subtotal|number:2"></div>
                </div>

                 
                <div class="row margin-top">
                    <div class="col-xs-8 text-center" style="padding-left:22px;">
                    รับเงินสด :
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;">
                    <?=$model->rc_money?>
                    </div>
                </div>
                
                <div class="row margin-top" >
                    <div class="col-xs-8 text-center text-change-money" style="padding-left:22px;">
                    เงินทอน :
                    </div>
                    <div class="col-xs-4 text-right" style="padding-right:22px;">
                        <?=$model->rc_change?>
                    </div>
                </div>
                

                <div class="row margin-top" >
                    <div class="col-xs-12 text-center"  >
                    ไม่รับเปลี่ยน/คืน ยกเว้นชำรุดจากการผลิต
                    </div>

                </div>

                

            </div>
            <!-- /.Slip -->
                 
            <div class="btn-menu margin-top button-menu" ng-if="saleEventline.length > 0">
                <!-- <button type="button" name="button" class="btn btn-default btn-lg" disabled><i class="fa fa-th-large" aria-hidden="true"></i>  </button> -->
                
                    
                    <span  ng-if="saleEventline.length > 0">
                        <button type="button" name="button" onclick="window.print()"  class="btn btn-info btn-flat btn-lg"><i class="fa fa-print" aria-hidden="true"></i> <?=Yii::t('common','Print')?></button>
                    </span>
                 
                
                
                    <!-- <?= Html::Button('<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'),['class' => 'btn btn-success btn-flat btn-lg','onClick' => 'form.submit();']) ?> -->
                <?php if($model->status=='Open') : ?>
                    <span ng-if="saleEventline.length > 0">
                        <span class="hidden-xs hidden-sm">
                            <?= Html::Button('<i class="fas fa-clock" aria-hidden="true"></i> '.Yii::t('common', 'POS-Hold'),['class' => 'btn btn-primary btn-flat btn-lg','onClick' => 'form.submit();']) ?>
                        </span>
                        <button type="button" name="button" class="btn btn-warning btn-flat btn-lg" ng-click="finance()">
                            <i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Pay')?>
                        </button>
                    </span>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    
    <?=$this->render('__modal')?>
    <?php ActiveForm::end(); ?>

    
    

<?php
    $options = ['depends' => [\yii\web\JqueryAsset::className()],'type'=>'text/javascript'];

    $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js',$options);
    $this->registerJsFile('@web/js/jquery.rippleria.min.js',$options);
    $this->registerJsFile('@web/js/saleorders/eventController.js?v=3.03.19.5',$options); 

    $this->registerCssFile('css/action-menu.css?v=3.01.21',['rel' => 'stylesheet','type' => 'text/css']);
?>
 

<?php

$js =<<<JS

//--- Focus BARCODE ---
  var attr = '';
  $(document).click(function(e) {
    var thisDiv = $(e.target).closest('div').attr('class');
    var thisTd = $(e.target).closest('td').attr('class');
    if((thisDiv == 'menu-list ng-binding ng-scope') || (thisTd == 'bg-dark menu pointer ng-binding')) {
      $('.menu-list').fadeIn('normal');
    }else {
      $('.menu-list').fadeOut('fast');
    }
    //attr = $(e.target).attr('type');
    //console.log(attr);
    if($('#product').hasClass('active')){
        $('.button-menu').hide();
    }else{
        $('.button-menu').show();
    }

    if(e.target.id != 'contextMenu') {
	    $(".contextMenu").remove();
	  }
  });
  
  
  $(document).on('keypress',function(event){
    var keyCode = event.keyCode || event.which;
    attr = $(event.target).attr('type');
    console.log(attr);
    if (keyCode !== 13){  
      if((attr=='number')||(attr=='text')){
        // Do nothing
      }else {
        // Focus BARCODE
        
        $('input[ng-model="search"]').focus();
      }  
    }
    
    
  })
//--- /.Focus BARCODE ---


  $(document).ready(function(){
    $('input.search-barcode').focus();
    $('div.alert').hide();
    
  });

  $('input[ng-model="search"]').keyup(function(event) {

    var keyCode = event.keyCode || event.which;

    if (keyCode === 32){ // space bar
      if($.trim($('input[ng-model="search"]').val())!=''){
        $('input[ng-model="search"]').focus().val($.trim($('input[ng-model="search"]').val()));
      }else {
        $('input[ng-model="search"]').focus().val('');
      }
    }


  });

  $('input.next-focus-search').keyup(function(event) {

    var keyCode = event.keyCode || event.which;

    if (keyCode === 13){
      $('input[ng-model="search"]').focus().val('');
    }
  })

    
JS;
$this->registerJs($js);
  ?>
