<?php

use yii\helpers\Html;
use richardfan\widget\JSRegister;
use yii\widgets\Pjax;


use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
//use kartik\widgets\SwitchInput;
use kartik\export\ExportMenu;


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
/* @var $model common\models\SaleInvoiceHeader */
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

<div class="accController font-roboto" style="font-size: 16px;">
  <div >
  <div class="row">
    <div class="col-xs-12">
    
      <?=$this->render('@admin/modules/SaleOrders/views/reserve/_script_inv_list')?>
    </div>  
    
  </div>
  
  <div class="row" ><hr></div>

  <?php $form = ActiveForm::begin([
                'id'=>'form-sale-invoice',
                'options' => ['data-key' => $model->id]
                ]); ?>
    <!-- Invoice Header  -->
    <div class="panel-heading row">
      <div class="row">
        <div class="col-sm-12" >
          <div class="pull-right">
              <?php if($model->no_ == ''): ?>
              <?php
                $NoSeries         = Generater::NextRuning('vat_type','vat_value','7',false);
                $model->no_       = $NoSeries;

                if(isset($_GET['cn'])) {
                    $model->no_       = $NoSeries.'-CN';
                }
                echo '<div class="ew-inv-inputno" ew-no="'.$model->id.'" style="width: 165px;">'.$form->field($model,'no_')->label(false).'</div>';
              ?>
              <?php else:  ?>
                <div class="ew-inv-no" ew-no="<?=$model->id?>" ew-no_="<?=$model->no_?>"><h4> <?=$model->no_?></h4></div>
              <?php endif; ?>
          </div>
          <div class="text-center">
            <?php 
              if(isset($_GET['cn'])) {
                echo '<h4 class="text-red">  ใบลดหนี้ / ส่งคืนสินค้า</h4>';
              }else{
                echo '<h4 class="text-primary">  ใบส่งสินค้า / ใบกำกับภาษี</h4>';
              }
            ?>
            
          </div>
        </div>                         
      </div>
      <div class="row">
        
        <div class="col-sm-12"> 
          <div class="pull-right" style="width: 165px;">
            <?=$form->field($model,'ext_document',
                ['addon' =>
                  ['append' =>
                    [
                      'content'=>'<i class="fa fa-paperclip pointer ext_doc_link" aria-hidden="true" data-key='.(($model->order_id)? $model->order_id : 0).'></i>',
                    ]
                  ]
                ])->textInput([
                  'data-key' => ($model->order_id)? $model->order_id : 0
                ])
            ?>
          </div>
          <div class="pull-right" style="width: 165px; margin-right: 5px;">
            <?php
                if($model->posting_date=='') {
                  $model->posting_date = date('Y-m-d');
                }else {
                  $model->posting_date = date('Y-m-d',strtotime($model->posting_date));
                }
                echo $form->field($model, 'posting_date')->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => Yii::t('common','Posting date').'...', 'class' => 'bg-orange'],
                    'value' => date('Y-m-d',strtotime($model->posting_date)),
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        //'format' => 'dd/mm/yyyy',
                        'format' => 'yyyy-mm-dd',
                        'autoclose'=>true
                    ],
                    'pluginEvents' => [
                      "changeDate" => "function(e) {
                        (function($, window, document, undefined){
                          $('#saleinvoiceheader-posting_date').on('change', function(){
                        
                             var today  = $('#saleinvoiceheader-posting_date').val();
                             var date   = new Date(today),
                                 days   = parseInt($('#saleinvoiceheader-payment_term').val(), 10);
                        
                              if(!isNaN(date.getTime())){
                                  date.setDate(date.getDate() + days);
                        
                                  $('#saleinvoiceheader-paymentdue').val(date.toInputFormat());
                              } else {
                                  alert('Invalid Date');
                              }
                          });
                          Date.prototype.toInputFormat = function() {
                             var yyyy = this.getFullYear().toString();
                             var mm = (this.getMonth()+1).toString();  
                             var dd  = this.getDate().toString();
                             return yyyy + '-' + (mm[1]?mm:'0'+mm[0]) + '-' + (dd[1]?dd:'0'+dd[0]);  
                          };
                        })(jQuery, this, document);
                       }",
                    ],
                ])->label(Yii::t('common','Posting date'));
            ?>
           
          </div>
        </div>
      </div>      
    </div>
    <div class="row" style="margin-top:-20px;"><hr></div>
    <div class="panel-body">
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
                <?php 
                  $model->doc_type = 'Sale';
                  if(isset($_GET['cn'])) {
                    $model->doc_type = 'Credit-Note';
                  }
                  
                  echo $form->field($model, 'doc_type')->textInput(['type' => 'hidden'])->label(false); 
                  
                ?>
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
                    <?= $form->field($model, 'payment_term')->dropDownList([
                        '0'=> Yii::t('common','Cash'),
                        '7'=> '7 '.Yii::t('common','Day'),
                        '15' => '15 '.Yii::t('common','Day'),
                        '30' => '30 '.Yii::t('common','Day'),
                        '45' => '45 '.Yii::t('common','Day'),
                        '60' => '60 '.Yii::t('common','Day'),
                        '90' => '90 '.Yii::t('common','Day'),
                    ],['class' => 'bg-green']) 
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
                <div class="col-sm-6 ">
                            
                    <div class="hidden">
                      <?php // ไม่ต้องเปลี่ยน เพราะเป็นแค่วันสร้างเอกสาร (เก็บไว้ดูว่า วันที่สร้างจริงเป็นวันไหน)
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
                    </div>
                    
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
                    <label for="creator"><?=Yii::t('common','Creator')?></label>
                    <div id="creator"><u><?=$model->users ? $model->users->username : '';?></u></div>
                     
                </div>
               
              </div>
            </div>
        </div>
        <!-- /. Invoice Header  -->
        <!--  Invoice Line  -->
        <div class="row ">
          <div class="row pull-right" style="margin-bottom:10px;"> 
            <div class="col-sm-12 ">           
              <?=$this->render('_get_source')?>       
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
        </div>
        </div>
        <!--  /. Invoice Line  -->
        <div class="row " style="margin-bottom: 25px;">
          <?php
            // $vat          = $model->vat_percent;
            // //$BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);
            // $BeforeDisc   = $model->sumline;

            // if($model->discount=='') $model->discount = 0; 
            // $Discount     = $model->discount;
            // // หักส่วนลด (ก่อน vat)
            // $subtotal     = $BeforeDisc - $Discount ;
            // if($model->include_vat == 1){
            // // Vat นอก
            // $InCVat   = ($subtotal * $vat )/ 100;
            //   $total    = ($InCVat + $subtotal);
            // }else {
            // // Vat ใน
            // // 1.07 = 7%
            // $vat_revert = ($vat/100) + 1;
            // $InCVat   = $subtotal - ($subtotal / $vat_revert);
            //   $total    = $subtotal;
            // }
          ?>
          <div class="col-sm-7">
            <div class="row">
              <div class="row">
                <div class="col-sm-12">
                  <?= $form->field($model, 'remark')->textarea(['rows'=>'7','placeholder' => '. . .'])->label(Yii::t('common','Remark')) ?>
                </div>     
                <div class="col-sm-12">
                  <?php $model->comments = $model->comments ? $model->comments : "\rจำนวนกล่อง: \rวันที่ส่ง: \rเล่มที่: "; ?>
                  <?= $form->field($model, 'comments')->textarea(['rows'=>'7','placeholder' => '. . .'])->label(Yii::t('common','Comment')) ?>
                </div>             
              </div>
            </div>
          </div>
          <div class="col-sm-5">
            <div class="row">
            <div class="panel">
              <div class="table-responsive" style="margin-top: 25px;">
                  <table class="table" style="border: 1px solid #ccc;">
                    <tr class="panel-heading bg-gray">
                      <th colspan="2"><?=Yii::t('common','Total')?>:</th>
                      <td align="right"><div id="ew-invline-total" data="<?=$model->sumtotals->sumline?>"><?=number_format($model->sumtotals->sumline,2) ?></div></td>
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
                            ->textInput(['placeholder' => '0','class'=>'text-right no-border','autocomplete' => 'off'])
                            ->label(Yii::t('common','Discount'))->label(false) ?>
                      </td>
                    </tr>
                    <tr class="text-primary">
                      <th colspan="2"><?=Yii::t('common','Total after discount')?>:</th>
                      <td align="right" id="ew-after-discount"><?= number_format($model->sumtotals->subtotal,2) ?></td>
                    </tr>
                    <?php
                      $vatStyle = 'style="display:none;"';
                      // Vat ใน
                      if($model->include_vat == 0) $vatStyle = NULL;
                     ?>
                      <tr class="text-success" <?=$vatStyle?> >
                        <th colspan="2"><?=Yii::t('common','Before vat')?> </th>
                        <td align="right" id="ew-before-vat"><?= number_format($model->sumtotals->subtotal - $model->sumtotals->incvat,2) ?></td>
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
                      <td align="right" id="ew-after-vat"><?= number_format($model->sumtotals->incvat,2) ?></td>
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
                      <td align="right" id="ew-total" data="<?=$model->sumtotals->total ?>" ><?= number_format($model->sumtotals->total,2) ?> </td>
                    </tr>
                  </table>
              </div>
            </div>

            </div>
          </div>
        </div>

        <!--BUTTON GROUP-->
        <div class="row" style="position: fixed;
                                bottom: -20px;
                                border-top: 1px solid #ccc;                                
                                background-color: rgba(239, 239, 239, 0.95);
                                padding: 10px 25px 15px 10px;
                                right: 0px;
                                text-align: right;
                                z-index:1000;
                                left: -15px;">

          <div class="col-md-8 col-sm-6 hidden-xs">
            <div class="row form-group">
              <div class="col-xs-12 text-left">
                <?=Html::a('<i class="fas fa-file-pdf text-red"></i> '.Yii::t('common','Download PDF'),
                    [
                      'print-inv-page',
                      'id'        => $model->id,
                      'footer'    => '1',
                      'download'  => '1',
                    ],
                    [
                        'class'   => 'btn btn-success-ew ew-print-preview',
                        'style'   => 'margin-left: 20px;',
                        'target'  => '_blank',
                    ]) ?>

                <?=Html::a('<i class="fas fa-file-excel"></i> '.Yii::t('common','Download Excel'),[
                      '/accounting/print/export',
                      'id' => $model->id,
                      'status' => $model->status,
                      'vat'   => $model->vat_percent
                    ],
                    [
                        'class' => 'btn btn-success ew-print-preview',
                        'style' => 'margin-left: 20px; margin-right: 20px;',          
                        'target'  => '_blank',              
                    ]) ?>

                <?php 
                  if($model->revenue==0){
                    echo '<button type="button" class="btn-cancel-invoice btn btn-default-ew"><i class="fas fa-ban "></i> '.Yii::t("common","Cancel Document").'</button>';
                  }else{
                    echo '<button type="button" class="btn-open-invoice btn btn-primary-ew"><i class="fas fa-check-circle"></i> '.Yii::t("common","Enabled").'</button>';
                  }

                ?>
                
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 text-right">
            <div class="row form-group">
              

                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data') : '<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save data'), 
                [
                  'class' => $model->isNewRecord ? 'btn btn-info-ew' : 'btn btn-primary-ew'
                ]) ?>
                <span class="btn btn-danger-ew ew-confirm-post"><i class="fa fa-server" aria-hidden="true"></i> <?=Yii::t('common','Post');?></span>
                <?=Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print'),
                    [
                      'print-inv-page',
                      'id' => $model->id,
                      'footer' => '1',
                    ],
                    [
                        'class' => 'btn btn-info-ew ew-print-preview ',
                        'style' => 'margin-left: 30px;',
                        'target' => '_blank',
                    ]) ?>    
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
      .attr('href','index.php?r=accounting%2Fsaleinvoice%2Fprint-inv-page&id='+$('.ew-inv-no')
      .attr('ew-no')+'&footer=1')
      .attr('style',' ');
  });


  //---- CHANGE TO BARCODE ---
  $('body').on('click','#code-options',function(){
    // Barcode Click
    $(this).html('<i class="fa fa-code"></i> {$Yii::t("common","Show Master Code")}');
    $(this).attr('id','code-options-off');
    updateCustomerPolicy($('#saleinvoiceheader-cust_no_').val(),1)
  })
  $('body').on('click','#code-options-off',function(){
    // Master Code Click
    $(this).html('<i class="fa fa-barcode"></i>  {$Yii::t("common","Show Barcode")}');
    $(this).attr('id','code-options');
    updateCustomerPolicy($('#saleinvoiceheader-cust_no_').val(),0)
  })
  function updateCustomerPolicy(id,val){
    $.ajax({
      url:'index.php?r=customers/ajax/update-policy&id='+id,
      type:'POST',
      data:{val:val},
      dataType:'JSON',
      success:function(response){
        getInvoiceLine($('form[id="form-sale-invoice"]').attr('data-key'));
        //liveRenderInvLine();
      }
    })
  }
  //----/. CHANGE TO BARCODE ---

$('body').on('click','.ext_doc_link',function(){
  if($(this).data('key')!=0){
    //window.location.href = 'index.php?r=SaleOrders/saleorder/view&id='+$(this).data('key');
    window.open('index.php?r=SaleOrders/saleorder/view&id='+$(this).data('key'),'_blank');
  }
  
});



const cancelDocument = (obj, callback) => {
  fetch("?r=accounting%2Fsaleinvoice%2Fcancel-document", {
    method: "POST",
    body: JSON.stringify(obj),
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
    },
  })
  .then(res => res.json())
  .then(response => { callback(response); })
  .catch(e => { console.log(e); });
}


$('body').on('click', '.btn-cancel-invoice', function(){
  let id = parseInt("{$model->id}");
  let el = $(this);
  if(confirm("Cancel ?")){   
    
    cancelDocument({id:id, action:1}, res => {
      if(res.status===200){
        el.html('<i class="fas fa-check-circle"></i> '+ "{$Yii::t('common','Enabled')}");        
        el.removeClass('btn-cancel-invoice btn-default-ew');
        el.addClass('btn-open-invoice btn-primary-ew');
      }
    })
  }
});

$('body').on('click', '.btn-open-invoice', function(){
  let id = parseInt("{$model->id}");
  let el = $(this);
  if(confirm("Enabled ?")){    
    cancelDocument({id:id, action:0}, res => {
      if(res.status===200){
        el.html('<i class="fas fa-ban"></i> '+ "{$Yii::t('common','Cancel Document')}");        
        el.removeClass('btn-open-invoice btn-primary-ew');
        el.addClass('btn-cancel-invoice  btn-default-ew');
      }
    })
  }
})

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
<?php $this->registerJsFile('js/no.series.min.js?v=03.06.20',['type'=>'text/javascript']);?> 

<?php if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/accounting/invoiceUpdate.js?v=4.04.01',['type'=>'text/javascript']);?>
<?php if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/item-picker.js?v=3.07.19.2',['type'=>'text/javascript']);?>
<?php if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/action_sale_invoice.js?v=5.05.8',['type'=>'text/javascript']);?>