<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Company;
$comp = Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();


/* @var $this yii\web\View */
/* @var $model common\models\SaleEventHeader */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
  @media print {
      body.receipt { width: 58mm }
      .sale-content,
      .main-footer,
      .product-picker,
      .btn-menu{
        display: none;
      }
      .slip{
        display: block;
        height: 100% !important;
        width: 100% !important;
        border: 0px !important;
        
      }

      #endFinance{
        display: none;
      }
       
      
      .sale-content .panel-body{
 
        overflow: hidden;
      }

      
      .modal {
          position: absolute;
          left: 0;
          top: 0;
          margin: 0;
          padding: 0;
          visibility: visible;
          /**Remove scrollbar for printing.**/
          overflow: visible !important;
      }
  
      .modal-body{
          visibility: visible !important;
          /**Remove scrollbar for printing.**/
          overflow: visible !important;
        
      }
      .modal-footer,.modal-header{
          display: none;
      }


  }

  .sale-event-header-form{

  }
  .sale-content .panel-body{
    height: 300px;
    overflow-y: auto;
  }
  .btn-menu{
    width: 100%;
    /*text-align: center;*/
  }
  .menu-list{
    position: absolute;
    border: 1px solid #000;
    background-color: #e9e9e9;
    color: rgb(0, 48, 255);
    padding: 10px;
    width: 200px;
    height: 50px;
    box-shadow: 1px 1px 1px 1px rgb(201, 201, 201);
  }
  .slip{
    font-family: 'saraban',monospace;
    height: 390px;
    overflow-y: auto;
    overflow-x: hidden;
    width: 100%;
    border: 1px solid #ccc;
    padding:10px;
    font-size:10px;

  }
  .subtotal{
    height:50px;
    width:100%;
    border:1px solid;
    font-size:25px;
    background-color:#000;
    color:#02f74e;
    padding:5px;
    text-align:right;
  }
</style>
<div class="sale-event-header-form" ng-controller="pointOfSale">

    <?php $form = ActiveForm::begin([
            'id' => 'form-event-order',
            'enableClientValidation' => true,
            'enableAjaxValidation' => true,
            'options' => [
              'enctype' => 'multipart/form-data',
              'data-key' => isset($_GET['id'])? $_GET['id'] : '0',
            ]
        ]); ?>
        <div class="product-picker hidden-xs">
           
           <?=$this->render('product_picker')?>
         </div>
    <div class="row">
      <div class="col-md-8">
        <div class="sale-content">
          <div class="panel <?=($model->status=='closed')? 'panel-success' : 'panel-default'; ?>">
            <div class="panel-heading">
              <div class="row">
                <div class="col-xs-12">

                  <div class="col-sm-9 col-xs-6 no-padding">
                    <label><?=Yii::t('common','Barcode')?></label>
                    <input type="text" class="form-control search-barcode" ng-model="search" ng-keyup="$event.keyCode == 13 ? searchProduct($event) : null" placeholder="<?=Yii::t('common','Barcode')?>" />
                  </div>
                  <div class="col-sm-1 col-xs-2 no-padding">
                    <label class="text-right" style="width:100%;">%<?=Yii::t('common','Discount')?></label>
                    <input type="text" class="form-control text-right  label-warning next-focus-search" ng-model="percdiscount" numbers-only/>
                  </div>
                  <div class="col-sm-1 col-xs-2 no-padding">
                    <label class="text-right" style="width:100%;"><?=Yii::t('common','Discount')?></label>
                    <input type="text" class="form-control text-right  label-info next-focus-search" ng-model="discount" numbers-only/>
                  </div>
                  <div class="col-sm-1 col-xs-2 no-padding">
                    <label class="text-right" style="width:100%;"><?=Yii::t('common','Quantity')?></label>
                    <input type="text" class="form-control text-right label-primary next-focus-search" ng-model="qtyperunit"  numbers-only/>
                  </div>
                </div>

              </div>
            </div>
            <div class="panel-body">

              <div class="table-responsive">

              <table class="table table-striped">
                <thead>
                  <th>#</th>
                  <th><?=Yii::t('common','Barcode')?></th>
                  <th><?=Yii::t('common','Product Name')?></th>
                  <th><?=Yii::t('common','Unit')?></th>
                  <th><?=Yii::t('common','Quantity')?></th>
                  <th><?=Yii::t('common','Price')?></th>
                  <th><?=Yii::t('common','Discount')?></th>
                  <th><?=Yii::t('common','Sum')?></th>
                </thead>
                <tbody ng:if="saleEventline.length > 0">
                  <tr ng:repeat="model in saleEventline" >
                    <td class="bg-dark menu pointer" ng-click="menuHover($event)" data-key="{{model.id}}">{{$index+1}}</td>
                    <td>{{model.barcode}}</td>
                    <td>{{model.name}}</td>
                    <td>{{model.unit}}</td>
                    <td class="text-right">{{model.quantity | number}}</td>
                    <td class="text-right">{{model.price | number}}</td>
                    <td class="text-right">{{model.discount * model.quantity  | number}}</td>
                    <td class="text-right">{{model.quantity * (model.price - model.discount)  | number}}</td>
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
                  <div class="subtotal" >
                    {{subtotal|number:2}}
                  </div>

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

        <div class="product-picker margin-top hidden-sm hidden-lg hidden-md">
          <div class="row">
            <div class="col-sm-12">
              Goods Sale
            </div>
          </div>
          <?=$this->render('product_picker')?>
        </div>
        
      </div>
      <div class="col-md-4">

          <div class="row ">
            <div class="col-xs-12">
              <div class="slip" style="<?=($model->status!='closed')?: 'background-color: #d6e9c6;'; ?>">
                <div class="slip-heading">
                  <div class="col-xs-2">
                    <img src="images/company/<?=$comp->logo?>" style="width: 50px;">
                  </div>
                  <div class="col-xs-10 text-right">
                     <h5><?=$comp->name?></h4>
                    <div class="">
                      <?=$comp->vat_address?> <?=$comp->vat_city?> <?=$comp->vat_location?> <?=$comp->postcode?>
                    </div>
                    <div class="">
                      เลขประจำตัวผู้เสียภาษี : <?=$comp->vat_register?>
                    </div>
                  </div>

                </div>

                <table class="table">
                  <thead>
                    <tr>
                      <th>รายการ</th>
                      <th class="text-right">จำนวน</th>
                      <th class="text-right">ราคา</th>
                      <th class="text-right">รวม</th>
                    </tr>
                  </thead>
                  <tr ng:repeat="model in saleEventline" >
                    <td>{{model.name |limitTo :25}}</td>
                    <td class="text-right">{{model.quantity | number}}</td>
                    <td class="text-right">{{model.price - model.discount  | number}}</td>
                    <td class="text-right">{{model.quantity * (model.price - model.discount)  | number}}</td>
                  </tr>
                </table>
                <div class="row">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    Total :
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{total|number:2}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    Discount :
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{sumdiscount|number:2}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    After Discount:
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{subtotal|number:2}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    Before Vat:
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{subtotal / 1.07|number:2}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    Vat 7%:
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{subtotal - (subtotal / 1.07)|number:2}}
                  </div>
                </div>
                <div class="row margin-top">
                  <div class="col-xs-8 " style="padding-left:22px;">
                    Grand Total :
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    {{subtotal|number:2}}
                  </div>
                </div>

                <div class="row margin-top">
                  <div class="col-xs-8 text-center" style="padding-left:22px;">
                    รับเงินสด :
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                    <?=$model->rc_money?>
                  </div>
                </div>
                <div class="row margin-top" style="font-size:12px; font-weight:900;">
                  <div class="col-xs-8 text-center" style="padding-left:22px;">
                    เงินทอน :
                  </div>
                  <div class="col-xs-4 text-right" style="padding-right:22px;">
                      <?=$model->rc_change?>
                  </div>
                </div>


                <div class="row margin-top" >
                  <div class="col-xs-12 text-center" style="padding-left:22px;">
                    สินค้าซื้อแล้วไม่รับเปลี่ยน/คืน ยกเว้นชำรุดจากการผลิต
                  </div>

                </div>




              </div><!-- /.Slip -->
            </div>

          </div>

          <div class="btn-menu margin-top">
            <!-- <button type="button" name="button" class="btn btn-default btn-lg" disabled><i class="fa fa-th-large" aria-hidden="true"></i>  </button> -->
            <button type="button" name="button" onclick="window.print()"  class="btn btn-info btn-lg"><i class="fa fa-print" aria-hidden="true"></i> <?=Yii::t('common','Print')?></button>
            <?= Html::Button('<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save'),['class' => 'btn btn-success btn-lg','onClick' => 'form.submit();']) ?>
            <button type="button" name="button" class="btn btn-warning btn-lg" ng-click="finance()">
              <i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Pay')?>
            </button>
          </div>
      </div>
    </div>

    

    <?php ActiveForm::end(); ?>

    <div id="findItems" class="modal fade" >
      <div class="modal-dialog modal-lg ">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-green-ew">
            <button type="button" class="close" ng-click="closeModal()">&times;</button>
            <h4 class="modal-title">{{modalHeader}}</h4>
          </div>
          <div class="modal-body">
            <div class=" ">
              <div class="col-xs-offset-8">
                <div class="col-xs-12">
                  <div class="form-group has-warning has-feedback">
                    <input type="text" class="form-control" ng-model="itemSearch" placeholder="<?=Yii::t('common','Search')?>">
                    <span class="form-control-feedback">
                      <i class="glyphicon glyphicon-search"></i>
                    </span>
                  </div>
                </div>
              </div>


              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Barcode</th>
                    <th>Name</th>
                    <th class="text-right">Price</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="pointer" ng:repeat="item in findItem | limitTo:10" ng-click="defineCode($event)" data-key="{{item.no}}">
                    <td>{{$index+1}}</td>
                    <td>
                      <div >{{item.barcode}}
                      <button data-key="{{item.no}}"
                      class="btn btn-success btn-xs"
                      ng-class="{hidden : item.active === false}"
                      ng-click="updateItem($event)">
                        <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Comfirm')?>
                      </button>
                      </div>
                    </td>
                    <td >{{item.desc_th}}</td>
                    <td class="text-right">{{item.price}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div align="left" ng-bind-html="bodyModal"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" ng-click="closeModal()"><i class="fa fa-power-off" aria-hidden="true"></i> Close</button>
          </div>
        </div>

      </div>
    </div>



    <div id="financeModal" class="modal fade" >
      <div class="modal-dialog  ">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <button type="button" class="close" ng-click="closeModalFn()">&times;</button>
            <h4 class="modal-title" ng-bind-html="fnHeader"></h4>
          </div>
          <div class="modal-body">
              <div class="row">
                <div class="col-xs-6">
                  <h3>ยอดเงิน</h3>
                </div>
                <div class="col-xs-6">
                  <div class="subtotal" style="color:#fff;">
                    {{subtotal|number:2}}
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-xs-6">
                  <h3>รับเงิน</h3>
                </div>
                <div class="col-xs-6">
                    <input type="text" class="form-control subtotal" ng-model="rcMoney" ng-keyup="receiveMoney($event)" numbers-only>
                </div>
              </div>
          </div>

          <div class="modal-footer">
            <div class="row " style="margin-top:-20px;">

              <div class="col-xs-6" ng:if="rcMoney > 0">
                <h3>เงินทอน</h3>
              </div>
              <div class="col-xs-6" ng:if="rcMoney > 0">
                <div class="subtotal" style="background-color:#ccc;color:#000; border:1px solid green;" >
                  <span >{{rcMoney - subtotal|number:2}}</span>
                </div>
              </div>
              
            </div>
          </div>

        </div>

      </div>
    </div>

</div>




<?php $this->registerJsFile('js/saleorders/eventController.js?v=3.02.13.2');?>

<?php
$js =<<<JS

  var attr = '';
  $(document).click(function(e) {
    var thisDiv = $(e.target).closest('div').attr('class');
    var thisTd = $(e.target).closest('td').attr('class');
    if((thisDiv == 'menu-list ng-binding ng-scope') || (thisTd == 'bg-dark menu pointer ng-binding')) {
      $('.menu-list').fadeIn('normal');
    }else {
      $('.menu-list').fadeOut('fast');
    }
    attr = $(e.target).attr('type');
    console.log(attr);
  });
  
  
  $(document).on('keypress',function(event){
    var keyCode = event.keyCode || event.which;
    if (keyCode !== 13){  
      if(attr!='text'){
        $('input[ng-model=\"search\"]').focus();
      }       
    }
    
    
  })


  $(document).ready(function(){
    $('input.search-barcode').focus();
    $('div.alert').hide();
  });

  $('input[ng-model=\"search\"]').keyup(function(event) {

    var keyCode = event.keyCode || event.which;

    if (keyCode === 32){ // space bar
      if($.trim($('input[ng-model=\"search\"]').val())!=''){
        $('input[ng-model=\"search\"]').focus().val($.trim($('input[ng-model=\"search\"]').val()));
      }else {
        $('input[ng-model=\"search\"]').focus().val('');
      }
    }

    // if ((keyCode === 13) && ($(event.target)[0]!=$(\"textarea\")[0]) && ($(event.target)[0]!=$(\"textarea\")[1])) {  // Enter
    //   if($(event.target)[0]==$(\"input\")[0]){
    //
    //   }
    //   console.log($(event.target)[0]);
    //   console.log($(\"input[ng-model=\"searchProduct\"]\"));
    //   event.preventDefault();
    //   return false;
    // }
  });

  $('input.next-focus-search').keyup(function(event) {

    var keyCode = event.keyCode || event.which;

    if (keyCode === 13){
      $('input[ng-model=\"search\"]').focus().val('');
    }
  })

  // $('body').on('click','.menu-hover td',function(){
  //   $('div.menu-list').remove();
  //   var html = '<div class=\"menu-list\">ddd</div>';
  //   $(this).append(html);
  // })
JS;
$this->registerJs($js);
  ?>
