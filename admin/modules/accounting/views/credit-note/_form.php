<?php

use yii\helpers\Html;
use richardfan\widget\JSRegister;
use yii\widgets\Pjax;


use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
//use kartik\widgets\SwitchInput;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;
use common\models\VatType;
use common\models\SalesPeople;

use admin\models\Generater;

$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();



$session  = Yii::$app->session;
$rules_id = $session->get('Rules')['rules_id'];
$company  = $session->get('Rules')['comp_id'];
/* @var $this yii\web\View */
/* @var $model common\models\rcinvoiceheader */
/* @var $form yii\widgets\ActiveForm */
//var_dump($_POST['ship']);


// Vat Type
$dataVat = VatType::find()->all();

$ListVat = arrayHelper::map($dataVat,'vat_value', 'name');


?>

<style type="text/css">
  .ew-inv-no{
    border: 1px solid green;
    padding: 0px 20px 0px 60px;
    color: #00c0ef;
    cursor: context-menu;
  }
  .sale-invlice-line-render{
    position: relative;
  }

</style>

<div class="accController text-black" style="font-size: 14px; font-family: saraban" >
  
  <div >
  <?php $form = ActiveForm::begin([
                'id'=>'saleinvoiceheader',
                'options' => ['data-key' => $model->id]
                ]); ?>
    <!-- Invoice Header  -->
    <div class="panel-heading">
      <div class="row">
        <div class="col-sm-12 no-padding" >
          <div class="pull-right">
               
            <div class="ew-inv-no text-orange" ew-no="<?=$model->id?>" ew-no_="<?=$model->no_?>"><h4> <?=$model->no_?></h4></div>
               
          </div>
          <div class="text-center">
            <h4 class="text-red">  ใบลดหนี้ / ส่งคืนสินค้า</h4>
          </div>
        </div>                         
      </div>
      <div class="row ">
        
        <div class="col-sm-offset-8"> 
          <div class="col-xs-2 no-padding" >
          <?=$form->field($model,'cn_reference')->textInput(['readonly' => true])->label('Ref : ')?>
          </div>
          <div class="col-xs-10 no-padding"> 
            <?=$form->field($model,'ext_document',
                ['addon' =>
                  ['append' =>
                    [
                      'content'=> ($model->invfromCreditNote)? 
                        '<i class="fas fa-link pointer  ext_doc_link" aria-hidden="true" data-key='.(($model->order_id)? $model->order_id : 0).'></i>'
                        : '<i class="fas fa-unlink text-red pointer ext_doc_link" aria-hidden="true" data-key='.(($model->order_id)? $model->order_id : 0).'></i>',
                    ]
                  ]
                ])->textInput([
                  'data-key' => ($model->order_id)? $model->order_id : 0,
                  'data-no' => $model->ext_document,
                  'style' => ($model->invfromCreditNote)? 'background-color:#cfffce;' : 'background-color:#ffcece;'
                ])
            ?>
            
          </div>
        
         
          <div class="" >
            <?php
                if($model->posting_date=='') {
                  $model->posting_date = date('Y-m-d');
                }else {
                  $model->posting_date = date('Y-m-d',strtotime($model->posting_date));
                }
                echo $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => Yii::t('common','Posting date').'...'],
                    'value' => date('Y-m-d',strtotime($model->posting_date)),
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        //'format' => 'dd/mm/yyyy',
                        'format' => 'yyyy-mm-dd',
                        'autoclose'=>true
                    ]
                ])->label(Yii::t('common','Posting date'));
            ?>

            <?= $form->field($model, 'other_ref')?>
          </div>
        
        </div>
      </div>      
    </div>
    <div class="row" style="margin-top:-20px;"><hr></div>
    <div class="panel-body form-content">
        <div class="row well">
            <div class="col-sm-4">
                <?= $form->field($model, 'cust_name_',
                  ['addon' =>
                    ['append' =>
                      [
                        'content'=>'<i class="fa fa-caret-square-o-up" aria-hidden="true" style="cursor:pointer;" ></i>',
                        'options' => ['class'=>'btn btn-info','id'=>"ew-modal-pick-cust"]
                        ]
                    ]
                  ])->textInput(['maxlength' => true,'readonly'=>true,'placeholder' => Yii::t('common','Select Customer')]) 
                ?>
            </div>
            <div class="col-sm-2">
                  <?= $form->field($model, 'cust_code',
                                      ['addon' =>
                                        ['append' =>
                                          [
                                           'content'=>'<i class="fa fa-caret-square-o-up" aria-hidden="true" style="cursor:pointer;" ></i>',
                                           'options' => ['class'=>'btn btn-info','id'=>"ew-modal-pick-cust"]
                                          ]
                                        ]
                                      ])
                        ->textInput(['maxlength' => true,'readonly'=>true,'placeholder' => Yii::t('common','Select Customer')])->label(Yii::t('common','Customer code'))
                          ?>
                <?= $form->field($model, 'id')->textInput(['type' => 'hidden'])->label(false) ?>
                <?= $form->field($model, 'cust_no_')->textInput(['type' => 'hidden'])->label(false) ?>
                <?= $form->field($model, 'doc_type')->textInput(['type' => 'hidden'])->label(false) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'taxid')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Vat Registration')])->label(Yii::t('common','Vat Registration')) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'branch')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','No.').' '.Yii::t('common','Branch')])->label(Yii::t('common','No.').' '.Yii::t('common','Branch')) ?>
            </div>
        </div>
        <hr>
        <div class="row well">
            <div class="col-sm-4">
              <?= $form->field($model, 'cust_address')->textInput(['placeholder' => Yii::t('common','Customer address')])->label(Yii::t('common','Customer address')) ?>
              <div class="row">
                  <div class="col-sm-6">
                      <?= $form->field($model, 'postcode')->textInput(['placeholder' => Yii::t('common','Postcode')]) ?>
                  </div>
                  <div class="col-sm-6">
                      <?= $form->field($model, 'province')->dropDownList(['placeholder' => Yii::t('common','Province')])?>
                  </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="row">
                <div class="col-sm-6">
                  <?= $form->field($model, 'contact')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Customer contact')])->label(Yii::t('common','Customer contact')) ?>
                </div>
                <div class="col-sm-6">
                  <?= $form->field($model, 'phone')->textInput(['maxlength' => true,'placeholder' => Yii::t('common','Contact phone')]) ?>
                </div>
                <div class="col-sm-6">
                  <?= $form->field($model, 'city')->dropDownList(['placeholder' => Yii::t('common','City')]) ?>
                </div>
                <div class="col-sm-6">
                  <?= $form->field($model, 'district')->dropDownList(['placeholder' => Yii::t('common','District')]) ?>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="row">
                <div class="col-sm-6">
                    <?php
                        if($model->order_date=='') $model->order_date = date('Y-m-d');
                        echo $form->field($model, 'order_date')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => Yii::t('common','Order date').'...'],
                            'value' => $model->ship_date,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'removeButton' => false,
                            'pluginOptions' => [
                                //'format' => 'dd/mm/yyyy',
                                'format' => 'yyyy-mm-dd',
                                'autoclose'=>true
                            ]
                        ])->label(Yii::t('common','Order date'));
                    ?>
                    <?php
                      
                      $Sales = SalesPeople::find()
                      ->where(['comp_id' => $company])
                      ->andWhere(['status' => 1])
                      ->orderBy(['code' => SORT_ASC])
                      ->all();
                      $salespeople = arrayHelper::map($Sales,'id', function ($element) {
                          return '['.$element['code'] .']  ' .$element['name'];
                      });
                     echo $form->field($model, 'sale_id') ->dropDownList($salespeople,
                          [
                              'class' => 'sale_id',
                              'prompt'=>'- เลือก Sales -',
                          ]
                      )->label(Yii::t('common','Sales'));
                    ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'payment_term')->dropDownList([
                        '0'=> Yii::t('common','Cash'),
                        '7'=> '7 '.Yii::t('common','Day'),
                        '15' => '15 '.Yii::t('common','Day'),
                        '30' => '30 '.Yii::t('common','Day'),
                        '45' => '45 '.Yii::t('common','Day'),
                        '60' => '60 '.Yii::t('common','Day'),
                        '90' => '90 '.Yii::t('common','Day'),
                      ]) 
                    ?>
                    <?php
                      if($model->paymentdue=='') $model->paymentdue = date('Y-m-d');
                      echo $form->field($model, 'paymentdue')->widget(DatePicker::classname(), [
                          'options' => ['placeholder' => Yii::t('common','Due date').'...','style' => 'background-color:rgba(239, 186, 180, 0.78);'],
                          'value' => $model->paymentdue,
                          'type' => DatePicker::TYPE_COMPONENT_APPEND,
                          'removeButton' => false,
                          'pluginOptions' => [
                              'format' => 'yyyy-mm-dd',
                              'autoclose'=>true
                          ]
                      ])->label(Yii::t('common','Due date'));
                    ?>
                </div>
              </div>
            </div>
        </div>
        <!-- /. Invoice Header  -->
        <!--  Invoice Line  -->
        <div class="row ">
          <div class="row pull-right" style="margin-bottom:10px;"> 
            <div class="col-sm-12 ">            
              <?php
                if(!$model->isNewRecord){
                  if($model->customer->show_item_code==0){
                    echo '<button id="code-options" type="button" class="btn btn-success-ew"><i class="fa fa-barcode"></i> '.Yii::t('common','Show  Barcode').'</button>';
                  }else{
                    echo '<button id="code-options-off" type="button" class="btn btn-success-ew"><i class="fa fa-code"></i> '.Yii::t('common','Show Master Code').'</button>';
                  }
                }
              ?>
                                                    
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12 sale-invlice-line-render">
              <?= $this->render('__invoice_line', [
                  'dataProvider' => $dataProvider,
              ]) ?>
            </div>
            <div class="col-sm-12 invoice-list-render"></div>
          </div>
        </div>
        <!--  /. Invoice Line  -->
        <div class="row ">
          <div class="col-md-8">
          <div class="row">
            <div class="row">
              <div class="col-sm-12">
                <?= $form->field($model, 'remark')->textarea(['rows'=>'7','placeholder' => '. . .'])->label(Yii::t('common','Remark')) ?>
              </div>             
            </div>
          </div>
          </div>
          <div class="col-md-4" style="margin-bottom:20px;">
            <div class="row">
            <div class="panel">
                  <?php 
                    $OldValue = ($model->invfromCreditNote)
                                  ? ($model->include_vat == 1  //Vat นอก
                                      ? $model->invfromCreditNote->sumtotals->sumline
                                      : $model->invfromCreditNote->sumtotals->before
                                    ) 
                                  : 0 ;

                    ?>
                  
                  <table class="table" style="border: 1px solid #ccc;">

                         <tr class="panel-heading bg-gray">
                            <th colspan="2"><?=Yii::t('common','มูลค่าของสินค้าตามใบกำกับภาษีเดิม')?></th>
                            <td align="right" class="font-roboto old-value"  data-val="<?=$OldValue ?>" >
                              <?=($model->invfromCreditNote)?  number_format($OldValue,2) : '' ; ?>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2"><?=Yii::t('common','มูลค่าของสินค้าที่ถูกต้อง')?></th>
                            <td align="right" class="font-roboto real-value">
                              <?php $before = abs($model->sumtotals->subtotal - $model->sumtotals->incvat); ?>
                              <?=number_format(($OldValue - $before), 2); ?>
                              <?php //number_format((abs(($model->invfromCreditNote)? $model->invfromCreditNote->sumtotals->sumline : 0)) - abs($model->sumtotals->subtotal),2) ?>
                            </td>
                        </tr>  
                     
                        
                        <tr>
                            <th  colspan="2"><?=Yii::t('common','ผลต่าง')?></th>
                            <td align="right" class="font-roboto difference"><?= number_format(abs($model->sumtotals->subtotal),2) ?></td>
                        </tr>


                     
                    <tr class="text-primary">
                      <th>
                        <?=Yii::t('common','Discount')?>:
                      </th>
                      <td>
                        <?= $form->field($model, 'percent_discount',['addon' => ['append' => ['content'=>'<i class="fa fa-percent" aria-hidden="true"></i>']]])
                          ->textInput(['placeholder' => Yii::t('common','Discount'),'class'=>'text-right','autocomplete' => 'off'])
                          ->label(Yii::t('common','Discount'))->label(false) ?>
                      </td>
                      <td align="right" style="padding-right: 0px; ">
                           <?= $form->field($model, 'discount')
                            ->textInput(['placeholder' => '0',
                            'class'=>'text-right no-border',
                            'value' => number_format(abs($model->discount),2),
                            'autocomplete' => 'off'
                            ])
                            ->label(Yii::t('common','Discount'))->label(false) ?>
                      </td>
                    </tr> 
                    <tr class="text-primary">
                      <th colspan="2"><?=Yii::t('common','Total after discount')?>:</th>
                      <td align="right" id="ew-after-discount"><?= number_format(abs($model->sumtotals->subtotal),2) ?></td>
                    </tr>
                    <?php
                      $vatStyle = 'style="display:none;"';
                      // Vat ใน
                      if($model->include_vat == 0) $vatStyle = NULL;
                     ?>
                      <tr class="text-success" <?=$vatStyle?> >
                        <th colspan="2"><?=Yii::t('common','Before vat')?> </th>
                        <td align="right" id="ew-before-vat"><?= number_format(abs($model->sumtotals->subtotal - $model->sumtotals->incvat),2) ?></td>
                      </tr>
                    <tr class="text-success">
                      <td style="width: 110px;">
                        <div class="ew-vat-choice" >
                        <?=Yii::t('common','Vat')?>
                        <?php
                          echo $form->field($model,'vat_percent') ->dropDownList($ListVat)->label(Yii::t('common','Vat'))->label(false);
                        ?>
                         </div>
                      </td>
                      <th >
                      <span id="ew-text-percent-vat"><?=$model->sumtotals->vat?></span> %
                        <?=$form->field($model, 'include_vat')->dropDownList(['0' => Yii::t('common','Include Vat'),'1' => Yii::t('common','Exclude Vat')])->label(false);?>
                        <?php
                        // echo  $form->field($model, 'include_vat')->checkbox([
                        //   'data-toggle' => 'toggle',
                        //   'data-onstyle' => 'primary',
                        //   'data-on' => Yii::t('common','Exclude'),
                        //   'data-off' => Yii::t('common','Include'),
                        //   'data-size'=> "small",
                        //   'label' => '',
                        // ])->label(false)
                        ?>
                      </th>
                      <td align="right" id="ew-after-vat"><?= number_format(abs($model->sumtotals->incvat),2) ?></td>
                    </tr>
                    <?php
                      if(isset($_GET['cn'])) {
                          $bgSum  = 'bg-danger';
                      }else{
                          $bgSum  = 'bg-primary';
                      }
                    ?>
                    <tr class="<?=$bgSum?>">
                      <th colspan="2"><?=Yii::t('common','Grand total')?>:</th>
                      <td align="right" id="ew-total" data="<?=abs($model->sumtotals->total) ?>" ><?= number_format(abs($model->sumtotals->total),2) ?> </td>
                    </tr>
                  </table>
              
            </div>

            </div>
          </div>
        </div>

        <!--BUTTON GROUP-->
        <div class="row" style="position: fixed;
                                bottom: -20px;
                                border-top: 1px solid #ccc;                                 
                                background-color: rgba(239, 239, 239, 0.9);
                                padding: 10px 25px 15px 20px;
                                right: 0px;
                                text-align: right;
                                z-index: 1000;
                                left: 0px;">
                                
          <div class="col-md-8 col-sm-6 test-right">          
            <div class="row form-group pull-left">               
                <?=Html::a('<i class="fa fa-download" aria-hidden="true"></i> '.Yii::t('common','Download'),
                    [
                      'print',
                      'id' => base64_encode($model->id),
                      'no' => $model->no_,
                      'download' => '1',
                    ],
                    [
                        'class' => 'btn btn-success-ew ew-print-preview ',
                        'target' => '_blank',
                    ]) ?>

                 <?=Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print'),
                    [
                      'print',
                      'id' => base64_encode($model->id),
                      'no' => $model->no_,
                    ],
                    [
                        'class' => 'btn btn-info-ew ew-print-preview   ml-10',
                        'target' => '_blank',
                    ]) ?>  
            </div> 
          </div>
          <div class="col-md-4 col-sm-6 text-right">
            <div class="row form-group">
                               
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data'), 
                [
                  'class' => $model->isNewRecord ? 'btn btn-info-ew' : 'btn btn-primary-ew'
                ]) ?>
                <span class="btn btn-danger-ew ew-confirm-post"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','Post');?></span>
            </div>
          </div>
        </div>
        <!-- /.BUTTON GROUP-->

    </div> <!-- /.panel-body.-->

  <?php ActiveForm::end(); ?>
  </div>
</div>






<?= $this->render('__script_modal')?>
<?php
$id = base64_encode($model->id);
$Yii = 'Yii';
$js =<<<JS
  $(document).on('click', '#operator', function(e){
    //$.pjax.reload({container: ".invLine", url: "index.php?r=accounting/saleinvoice/sale-invoice-line&id=50"});
    
  })
  

  $(document).ready(function(){
    //$('#table-pick-items tr').click(function(event) {
      $('body').on('click','#table-pick-items tr',function(event){
        if (event.target.type !== 'checkbox') {
          $(':checkbox', this).trigger('click');
        }
      });

      $('a.btn-app-print')
      .attr('href','index.php?r=accounting%2Fcredit-note%2Fprint&id={$id}&no={$model->no_}')
      .attr('style',' ');
  });


  //---- CHANGE TO BARCODE ---
  $('body').on('click','#code-options',function(){
    // Barcode Click
    $(this).html('<i class="fa fa-code"></i> {$Yii::t("common","Show Master Code")}');
    $(this).attr('id','code-options-off');
    updateCustomerPolicy('{$model->cust_no_}',1)
  })
  $('body').on('click','#code-options-off',function(){
    // Master Code Click
    $(this).html('<i class="fa fa-barcode"></i>  {$Yii::t("common","Show Barcode")}');
    $(this).attr('id','code-options');
    updateCustomerPolicy('{$model->cust_no_}',0)
  })

  function updateCustomerPolicy(id,val){

    getCrediteNoteLine = (id) => {
        $.ajax({
            url: "index.php?r=accounting/credit-note/get-invoice-line&id=" + id,
            type: "GET",
            dataType: 'JSON',
            success: function(response) {
                $(".sale-invlice-line-render").html(response.html);
                $('input.next').eq(0).focus().select();
            }    
        });
    }

    $.ajax({
      url:'index.php?r=customers/ajax/update-policy&id='+id,
      type:'POST',
      data:{val:val},
      dataType:'JSON',
      success:function(response){
        if (response.status==200){
          getCrediteNoteLine('{$model->id}');
        }        
        //liveRenderInvLine();
      }
    })
  }
  //----/. CHANGE TO BARCODE ---


  const UpdateCheckReceive = (obj, callback) => {
    fetch("?r=accounting/credit-note/update-receive", {
          method: "POST",
          body: JSON.stringify(obj),
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
          }
      })
      .then(res => res.json())
      .then(response => {
          callback(response);            
      })
      .catch(error => {
          console.log(error);
      });
  }

  $('body').on('click', 'input[name="receive"]', function(){
    let id = $(this).attr('data-key');
    
    if($(this).is(":checked")){
      UpdateCheckReceive({id: id, set:true}, res => {
        if(response.status===200){
          console.log(id + ' true');
        }else{
          console.log(id + ' error');
        }
      })
      
    }else{
      UpdateCheckReceive({id: id, set:false}, res => {
        if(response.status===200){
          console.log(id + ' false');
        }else{
          console.log(id + ' error');
        }
      })
    }
        
    
  })

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
<?php $this->registerJsFile('js/no.series.js?v=03.07.24',['type'=>'text/javascript']);?> 
<?php $this->registerJsFile('js/accounting/invoiceUpdate.js?v=3.07.17.1',['type'=>'text/javascript']);?>
<?php $this->registerJsFile('js/accounting/credit-note.js?v=5.09.09.1',['type'=>'text/javascript']);?> 
<?php $this->registerJsFile('js/item-picker.js?v=4.12.04.1',['type'=>'text/javascript']);?>