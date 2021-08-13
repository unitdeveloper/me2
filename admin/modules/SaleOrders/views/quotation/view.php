<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;


use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use yii\grid\GridView;
//use kartik\grid\GridView;


use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;

$Bahttext     = new FunctionBahttext();

$Fnc          = new FunctionSaleOrder();

$myCompany    = Yii::$app->session->get('Rules')['comp_id'];
$myRule       = Yii::$app->session->get('Rules');
$SalePeople   = $myRule['sale_id'];

$this->title  = $model->no;


if(empty($model->customer->code)){

  return Yii::$app->response->redirect(Url::to(['/SaleOrders/quotation/update', 'id' => $model->id]));
}

// Sales = 3
// ไม่อนุญาตให้ดูใบงานที่ไม่ใช่ของตัวเอง
// Policy 1

 
 
if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))){
  if($model->sale_id!=$SalePeople) return Yii::$app->response->redirect(Url::to(['/SaleOrders/quotation/index']));
}



if($model->vat_percent==0){
  $alert = 'alert-warning';
}else {
  $alert = 'alert-success hidden-xs hidden-sm hidden-md hidden-lg';
}

 
?>


<?= $this->render('__menu_saleorder',['dataProvider' => $dataProvider,'model' => $model]) ?>
 
 
<style>
  @media screen and (max-width: 767px){
    .table-responsive {
        overflow-x: hidden;
        border: none !important;
    }
    .SaleLine{
        /* margin:0 -15px 0 -15px !important; */
    }
    .rule-xs-mac{
        max-width:340px; 
        overflow-x:auto;
    }
    .add-product-service{
        color:#888;
    }
    input.no-border{
        background:none;
    }
    a#complete-btn:active{
        color:green;
    }
    .submit-btn-zone{
        position:fixed;
        bottom:0px;
        background-color: rgb(253,253,253);
        border-top:1px solid #eaeaea;
        padding:10px 0 10px 0;
        width:100%;
        z-index:1000;
    }
    #menuFilter{
        padding-bottom:50px;
    }
    .FilterResource{
        padding-bottom:50px;
    }
    .grid-view td {
      white-space: normal !important;
    }
}
.break-word {
  width: 200px;
  overflow-wrap: break-word !important;
  word-wrap: break-word;
    word-break: normal;
}
</style>
<?php $this->registerCssFile('css/sale_orders.css?update-051017');?>
<div class="row">
 

<div class=" " ng-init="Title='<?=$this->title?>'">
    <div class=" ">
        <div class="col-xs-offset-6 text-right ">
            <div class="col-xs-12 text-info">
                <div><?=$model->no?></div>
                <div><?=$model->sales_people?></div>
            </div>
        </div>
    </div>
    <div class="panel-heading" style="position: relative;" id="SaleOrder" ew-so-id="<?=$_GET['id']?>" ew-status="<?=$model->status ?>">
      <a data-toggle="collapse"  href="#collapseOne" >
        
        <?php
          if($model->customer_id==''){
              echo '<i class="far fa-address-card"></i> '.Yii::t('common','Customer'); 
          }else{
              echo '<i class="far fa-address-card fa-2x text-green"></i> <span >'.$model->customer->name.'</span>';
          }
        ?> 
      </a>
       
      

    </div>

    <div class="panel-body">
        <div class="customer-form" >
            <div class="nav-tabs-custom panel-collapse collapse" id="collapseOne"  role="tabpanel" aria-labelledby="headingOne">
                <ul class="nav nav-tabs">
                  <li class="active">
                      <a href="#General" data-toggle="tab" aria-expanded="true">
                      <?= Icon::show('user', [], Icon::BSG) ?>
                      <?=Yii::t('common','General'); ?> </a>
                  </li>
                  <li class="">
                      <a href="#Invoicing" data-toggle="tab" aria-expanded="false">
                      <?= Icon::show('barcode', [], Icon::BSG) ?>
                      <?=Yii::t('common','Invoicing'); ?> </a>
                  </li>
                  <li class="">
                      <a href="#Shipping" data-toggle="tab" aria-expanded="false">
                      <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
                      <?=Yii::t('common','Ship<span class="hidden-xs">ping</span>'); ?></a>
                  </li>


                </ul>
                <div class="tab-content">
                    <div class="tab-pane  active" id="General">
                        <div class="row">
                            <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">

                              <div class="well">
                                  <div class="row">

                                  <div class="col-sm-3 hidden-xs"><?= Yii::t('common','Code') ?>  : </div>
                                  <div class="col-lg-9"><a href="index.php?r=customers/customer/view&id=<?=$model->customer->id?>" target="_blank"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?= $model->customer->code ?> </a></div>

                                  <div class="col-sm-3"> </div>
                                  <div class="col-sm-9"><h4><?= $model->customer->name ?></h4> </div>

                                  <div class="col-sm-3"  >  </div>
                                  <div class="col-lg-9"><?= $model->customer->locations->address ?></div>


                                  <div class="col-sm-3 hidden-xs">  </div>
                                  <div class="col-lg-9">
                                  <?= Yii::t('common','Phone') ?> : <?= $model->customer->phone ?>
                                  <?= Yii::t('common','Fax') ?> : <?= $model->customer->fax ?></div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12"> <h5>Credit</h5></div>


                                    <div class="col-xs-4"> <?=Yii::t('common','Limit'); ?> : </div>
                                    <div class="col-xs-8 text-right"> <?=number_format($model->customer->credit_limit,2) ?></div>


                                    <div class="col-xs-4"> <?=Yii::t('common','Usage')?> : </div>
                                    <div class="col-xs-8 text-right">
                                      <?=Html::a(number_format($model->customer->getCredit()->PayIn,2),
                                              ['/SaleOrders/quotation','SalehearderSearch[customer_id]' => $model->customer->id],
                                              ['target' => '_blank'])
                                      ?>
                                    </div>

                                    <div class="col-xs-4"> <?=Yii::t('common','Available')?> :  </div>
                                    <div class="col-xs-8 text-right"> <?=number_format($model->customer->getCredit()->CreditAvailable,2) ?> </div>
                                </div>

                              </div>
                            </div>

                            <div class="col-md-6 col-md-5">

                                <?php
                                 if($model->include_vat == '0')
                                  {
                                    $vats = Yii::t('common','Include Vat');
                                  }else {
                                    $vats = Yii::t('common','Exclude Vat');
                                  }
                                ?>

                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order No') ?></div><div class="col-lg-8"> : <b><?= $model->no ?> </b></div>

                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order Date') ?></div><div class="col-lg-8"> : <?= $model->order_date ?> </div>

                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Due Date') ?></div><div class="col-lg-8"> : <?= $model->paymentdue ?> .</div>   

                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Vat') ?></div><div class="col-lg-8"> : <?= $vats ?> </div>

                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','PO.') ?></div><div class="col-lg-8"> : <?=$model->ext_document ?> .</div>

                                
                                


                            </div>



                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane fade" id="Invoicing">
                            <!-- The timeline -->
                            <div class="row">
                            <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">


                                <div class="col-sm-4 hidden-xs"> <?=Yii::t('common','Customer No') ?> : </div>
                                <div class="col-lg-8"><?= $model->customer->code ?> </div>

                                <div class="col-sm-4 hidden-xs"> <?=Yii::t('common','Customer Name') ?> : </div>
                                <div class="col-lg-8"><?= $model->customer->name ?> </div>

                                <div class="col-sm-4 hidden-xs">  </div>
                                <div class="col-lg-8">
                                  <?= $model->customer->address ?>

                                </div>



                                <div class="col-sm-4 hidden-xs">  </div>
                                <div class="col-lg-8"><?=Yii::t('common','Phone')?> : <?= $model->customer->phone ?> <?=Yii::t('common','Fax') ?> : <?= $model->customer->fax ?></div>
                            </div>
                            <div class="col-md-6 col-md-5">
                                <div class="row">
                                  <label class="col-sm-3"><?=Yii::t('common','Payment')?> :  </label>
                                    <?php
                                      if($model->payment_term!='0')
                                      echo $model->payment_term.' วัน';
                                      else
                                      echo 'เงินสด';
                                    ?>

                                </div>

                                <div class="row">
                                  <label class="col-sm-3"><?=Yii::t('common','Due Date')?> :  </label><?= $model->paymentdue ?>
                                </div>


                            </div>
                        </div>

                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane fade " id="Shipping">
                        <div class="row">


                        <?php $form = ActiveForm::begin([
                              'id' => 'Form-SaleOrder',
                              'action' => ['saleorder/update-header','id' => $model->id],
                              'method'=> 'post'
                              ]); ?>



                         <?php if(Yii::$app->session->get('Rules')['rules_id']==4):   ?>

                            <div class="col-xs-9">

                              <?= $form->field($model,'transport')->textInput()->label(Yii::t('common','Transport By')) ?>

                              <?= $form->field($model,'ship_address')->textInput()->label(Yii::t('common','Ship Address'))  ?>
                            </div>


                            <div class="col-xs-3">

                              <div><label>Save</label></div>
                               <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), 
                               [
                                 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                 'data-rippleria' => ''
                               ]) ?>
                            </div>
                          <?php else: ?>

                          <div class="col-sm-12"><?= $model->ship_address ?></div>

                          <?php endif; ?>


                          <?php ActiveForm::end(); ?>


                        </div>
                        <div class="row">
                        <div class="col-sm-12">
                        <?=Yii::t('common','Create By') ?> : [<?= $model->user_id ?>] <?= $model->users->username ?>
                        </div>
                        <div class="col-sm-12">
                        <?=Yii::t('common','Update By') ?> : [<?= $model->update_by ?>] <?= $model->update_date ?>
                        </div>
                        </div>
                    </div>
                     <!-- /.tab-pane -->
                 </div>
                 <!-- /.tab-content -->
             </div>

            <div class="row">
                <div class="col-xs-12">
                  <div class="box-">

                    <!-- /.box-header -->
                    <div class="box-body   no-padding">
                    <?php
                    
                    $gridColumns = [
                                  //'order_no',
                                  [
                                    'class' => 'yii\grid\SerialColumn',
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                  ],
                                  [
                                      'attribute' => 'Photo',
                                      'format' => 'raw',
                                      'contentOptions' => ['class' => 'relative'],
                                      'headerOptions' => ['class' => 'hidden-xs'],
                                      'label' => Yii::t('common', 'Image'),
                                      'value' => function($model){


                                          if(isset($model->item_no))
                                          {
                                            $photo = 'images/product/'.$model->items->ItemGroup.'/'.$model->items->Photo;
                                            $item  = $model->items->No;

                                          }else {
                                            $photo = 'images/nopic.png';
                                            $item  = '';
                                          }

                                          $html = ' <span id="ew-Item-Info" data-id="'.$model->items->id.'" ew-item-no="'.$item.'" class="btn hidden-xs">'.Html::img($model->items->getPicture(),['style'=>'width:50px;']).'</span>';


                                          if($model->description=='') $model->description = $model->items->description_th;

                                          if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          {
                                            $unitPrice = number_format($model->unit_price,2);
                                          }else  {    // Exclude Vat.
                                            $unitPrice = number_format($model->unit_price,2);

                                          }
                                          if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          {
                                            $sumline = number_format($model->quantity * $model->unit_price,2);
                                          }else  {    // Exclude Vat.
                                            $sumline = number_format($model->quantity * $model->unit_price,2);                                             
                                          }
                                          $html.= '<div class="row hidden-sm hidden-md hidden-lg">
                                                    <div class="col-xs-3">
                                                      <span id="ew-Item-Info" ew-item-no="'.$item.'" class="btn">'.Html::img($model->items->getPicture(),['style'=>'width:50px;']).'</span>
                                                    </div>
                                                    <div class="col-xs-9">
                                                      <p class="text-aqua break-word ">'.$model->description.'</p>
                                                      <p>
                                                        <span class="col-xs-8">'.number_format($model->quantity).' x '.$unitPrice.'</span>
                                                        <span class="col-xs-4 text-primary text-right">'.$sumline.'</span>
                                                      </p>
                                                      <p></p>
                                                    </div>
                                                  </div>';                                      
                                         return $html;
                                      }
                                  ],

                                  [
                                    'attribute' => 'item_no',
                                    'format' => 'html',
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      //return $model->items['master_code'];
                                      return $model->crossreference->no;
                                    },
                                    'footer' => '<div></div>',
                                  ],
                                    //'items.Description',
                                  [
                                    'label' => Yii::t('common','Description'),
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){

                                      if($model->description=='') $model->description = $model->items->description_th;

                                      return $model->description;
                                    }
                                  ],
                                  [
                                     'attribute' => 'quantity',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'hidden-xs text-right'],
                                     'contentOptions' => ['class' => 'hidden-xs text-right'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        return number_format($model->quantity,2);
                                     }
                                  ],
                                  [
                                    'label' => Yii::t('common','Unit Of Measure'),
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){                                      
                                      return $model->items->UnitOfMeasure;
                                    }
                                  ],
                                    //'quantity',
                                   // 'items.UnitOfMeasure',
                                    //'unit_measure',
                                    //'unit_price',
                                  [
                                     'attribute' => 'unit_price',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'hidden-xs text-right'],
                                     'contentOptions' => ['class' => 'hidden-xs text-right'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        if(Yii::$app->session->get('vat')==1) // Include Vat.
                                        {
                                          return number_format($model->unit_price,2);
                                        }else  {    // Exclude Vat.
                                          return number_format($model->unit_price,2);

                                        }
                                        //return number_format($model->unit_price,2);
                                     },
                                     'footer' => 'รวม',
                                  ],
                                    //'line_amount',
                                  [
                                      'attribute' => 'line_amount',

                                      'format' => 'html',
                                      'label' => Yii::t('common', 'Total Amount'),
                                      'headerOptions' => ['class' => 'hidden-xs text-right'],
                                      'contentOptions' => ['class' => 'hidden-xs text-right'],
                                      'footerOptions' => ['class' => 'hidden-xs'],
                                      'value' => function($model){

                                          if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          {
                                            return number_format($model->quantity * $model->unit_price,2);
                                          }else  {    // Exclude Vat.
                                            return number_format($model->quantity * $model->unit_price,2);
                                            //$total += $item['quantity'] * $item['unit_price_exvat'] ;
                                          }
                                          //return number_format($model->quantity * $model->unit_price,2);


                                      },
                                      'footer' => number_format($Fnc->getTotalSaleOrder($dataProvider->models),2),
                                  ],



                              ];
              ?>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,
                  //'filterModel' => $searchModel,
                  'showFooter' => true,
                  'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                  'columns' => $gridColumns,
                  //'responsive'=>true,
                  //'hover'=>true,
                  'summary' => false,
              ]);
              ?>
                    </div>
                    <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<?php
  // $vat          = $model->vat_percent;
  // $BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);
  // $Discount     = $model->discount;
  // // หักส่วนลด (ก่อน vat)
  // $subtotal     = $BeforeDisc - $Discount ;
  // if($model->include_vat == 1){
  //   // Vat นอก
  //   $InCVat   = ($subtotal * $vat )/ 100;
  //   $total    = ($InCVat + $subtotal);
  // }else {
  //   // Vat ใน
  //   $InCVat   = $subtotal - ($subtotal / 1.07);
  //   $total    = $subtotal;
  // }
?>
 
<div class="row">

  <div class="col-md-7">
    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
        <?php echo  $Bahttext->ThaiBaht($model->sumtotal->total) ?>
    </p>

     <div class="hidden">
          <p class="lead"><?=Yii::t('common','Payment Methods') ?>:</p>
          <img src="images/credit/visa.png" alt="Visa">
          <img src="images/credit/mastercard.png" alt="Mastercard">
          <img src="images/credit/american-express.png" alt="American Express">
          <img src="images/credit/paypal2.png" alt="Paypal">


    </div>
  </div>
  <div class="col-md-5">
    <div class="panel panel-default">

        <?php
          if($model->vat_type==1)
          {
            $vat_color = 'background-color:green; color:#FFF;';
          }else {
            $vat_color = 'background-color:#f39c12; color:#FFF;';
          }
        ?>
        <div class="table-responsive" style="">
            <table class="table">
              <?php if($model->discount != 0): ?>
                <tr>
                  <th><?=Yii::t('common','Before Discount')?>:</th>
                  <td align="right"><?=number_format($model->sumtotal->sumline,2) ?></td>
                </tr>

                <tr>
                  <th><?=Yii::t('common','Discount')?>:</th>
                  <td align="right"><?=number_format($model->sumtotal->discount,2) ?></td>
                </tr>
              <?php endif; ?>


              <tr>
                <th style="width:50%"><?=Yii::t('common','Subtotal')?>:</th>
                <td align="right"><?= number_format($model->sumtotal->subtotal,2) ?></td>
              </tr>


              <?php if($model->include_vat == 0): // Vat ใน ?>
                <tr>
                  <th> ยอดก่อนภาษี </th>
                  <td align="right"><?= number_format($model->sumtotal->subtotal - $model->sumtotal->incvat,2) ?></td>
                </tr>
              <?php endif; ?>

              <?php if($model->vat_percent > 0):?>
              <tr>
                <th style="<?=$vat_color?>"><?=Yii::t('common','Vat')?> (<?=$model->sumtotal->vat?>%)</th>
                <td style="<?=$vat_color?>" align="right"><?= number_format($model->sumtotal->incvat,2) ?></td>
              </tr>
              <?php endif; ?>




              <tr style="background-color:#00c0ef; color:#FFF;">
                <th><?=Yii::t('common','Grand total')?>:</th>
                <td align="right" ><?= number_format($model->sumtotal->total,2) ?> </td>
              </tr>

            </table>
          </div>


    </div>
  </div>
</div>

 

<div class="content-footer " style="
      position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 10px 15px 10px;
      right: 0px;
      text-align: right;
      z-index:1000;
    " >
    <div class="row">
        <div class="col-xs-4 col-sm-4 ">
          <?= Html::a('<i class="fas fa-home"></i> '.Yii::t('common', 'Back'), ['/SaleOrders/quotation'], ['class' => 'btn btn-default-ew hidden']) ?>   
        </div>
        <div class="col-xs-8 col-sm-8 text-right">            
              <?= Html::a('<i class="fas fa-print"></i> '.Yii::t('common', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info-ew ','target' => '_blank']) ?>
              <?= Html::a('<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-success-ew hidden-xs']) ?>                
              <?= Html::a('<i class="fas fa-sign-in-alt"></i> '.Yii::t('common', 'Convert to SO'), ['convert', 'id' => $model->id], [
                  'class' => 'btn btn-primary-ew  ',
                  'data' => [
                      'confirm' => Yii::t('common', 'Confirm create "Sale Order"?'),
                      'method' => 'post',
                  ],
              ]) ?>         
        </div>
    </div>
</div>


<?php 
$btn=<<<JS

  $(document).ready(function(){
      var footer = $('div.content-footer').html();
      $('footer').html(footer).find('div.content-footer').fadeIn('slow');
  
  })

JS;
$this->registerJS($btn,\yii\web\View::POS_END,'yiiOptions');
 
?>


<?php
$Yii = 'Yii';
$js =<<<JS

var langText = {

  add         : "{$Yii::t('common','Add')}",
  back        : "{$Yii::t('common','Back')}",
  ship        : "{$Yii::t('common','Ship-packing')}",
  error_zero  : "{$Yii::t('common','[Error!]  Quantity to ship must not be \"0\".')}",
  undonot_zero: "{$Yii::t('common','[Error!]  The undo quantity  must not be \"0\".')}",
  confirmship : "{$Yii::t('common','[Confirm] Do you want to post this shipment?')}",
  overship    : "{$Yii::t('common','Over quantity')}",
  lessthan    : "{$Yii::t('common','You can not enter values less than 0!')}",
  deliticket  : "{$Yii::t('common','Delivery ticket')}",
  print       : "{$Yii::t('common','Print')}",
  undo        : "{$Yii::t('common','Undo')}",
  save        : "{$Yii::t('common','Save')}",
  confirmdel  : "{$Yii::t('common','Do you want to delete?')}",
  shipment    : "{$Yii::t('common','Shipment')}",
  confirmundo : "{$Yii::t('common','Do you want to undo this document?')}",
  confirm     : "{$Yii::t('common','Confirm')}",
};

JS;


$this->registerJS($js,\yii\web\View::POS_HEAD);
?>
<?php echo  $this->render('../modal/__modal_approve',['model' => $model]) ?>
<?php echo  $this->render('../modal/__modal_item_info',['model' => $model]) ?>
<?php $this->registerJsFile('js/item-picker.js?v=3.03.23');?> 
<?php $this->registerJsFile('js/saleorders/salequote-form.js?v=3.03.31'); ?>
 
