<?php

//use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

use common\models\SetupSysMenu;
use admin\modules\apps_rules\models\SysRuleModels;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use kartik\helpers\Html;

use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

//use yii\grid\GridView;
//use kartik\grid\GridView;


use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;

$Bahttext     = new FunctionBahttext();

$Fnc          = new FunctionSaleOrder();

$myCompany    = Yii::$app->session->get('Rules')['comp_id'];
$myRule       = Yii::$app->session->get('Rules');
$SalePeople   = $myRule['sale_id'];

$this->title  = $model->no;



// Sales = 3
// ไม่อนุญาตให้ดูใบงานที่ไม่ใช่ของตัวเอง
// Policy 1

//$Policy = SetupSysMenu::findOne(1);
//$myPolicy = explode(',',$Policy->rules_id);

//if(in_array($myRule['rules_id'],['3','9'])){
if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','SalehearderSearch','view'))){
  if($model->sale_id!=$SalePeople) return Yii::$app->response->redirect(Url::to(['/SaleOrders/saleorder/index']));
}



if($model->vat_percent==0){
  $alert = 'alert-warning';
}else {
  $alert = 'alert-success hidden-xs hidden-sm hidden-md hidden-lg';
}


app\assets\SweetalertAsset::register($this);
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
 

<div class=" " ng-init="Title='<?=$this->title?>'" id="export_to_excel">
    <div class=" ">
        <div class="col-xs-offset-6 text-right ">
            <div class="col-xs-12 text-info">
                <div><?=$model->no?></div>
                <div>[<?=$model->sales_people?>] <?=$model->sales ? $model->sales->name : ''; ?></div>
            </div>
        </div>
    </div>
    <div class="panel-heading" style="position: relative;" id="SaleOrder" ew-so-id="<?=$_GET['id']?>" ew-status="<?=$model->status ?>">
      <a data-toggle="collapse"  href="#collapseOne" >
        
        <?php
          if($model->customer_id==''){
              echo '<i class="far fa-address-card"></i> '.Yii::t('common','Customer'); 
          }else{
              echo '<i class="far fa-address-card fa-2x text-green"></i> <span >'.$model->customer->name.' ('. $model->customer->locations->province.')'.'</span>';
          }
        ?> 
      </a>
      <div class="print" style="position: absolute; right: 30px; top: 10px;">
        

      </div>

      

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
                                  <div class="col-sm-3">  </div>
                                  <div class="col-lg-9"><?= $model->customer->locations->address ?></div>
                                  <div class="col-sm-3 hidden-xs">  </div>
                                  <div class="col-lg-9">
                                  <?= Yii::t('common','Phone') ?> : <?= $model->customer->phone ?>
                                  <?= Yii::t('common','Fax') ?> : <?= $model->customer->fax ?></div>
                                </div>                                                           
                                <div class="row credit-zone">
                                  <div class="col-sm-12">                                        
                                      <div class="panel panel-info">                                                
                                          <div class="panel-body">
                                              <div class="row">
                                                  <div class="col-xs-6"><?=Yii::t('common','Credit Limit')?> :</div>
                                                  <div class="col-xs-6 text-right"><span class="credit-limit"><?=$model->customer ? number_format($model->customer->credit_limit)  : 0 ?></span></div>
                                              </div>
                                              <div class="row">
                                                  <div class="col-xs-6"><?=Yii::t('common','Credit Available')?> :</div>
                                                  <div class="col-xs-6 text-right"><span class="credit-available"><?=$model->customer ? number_format($model->customer->credit->CreditAvailable) : 0 ?></span></div>
                                              </div>  
                                          </div>
                                      </div>                                       
                                  </div>
                                </div> 
                              </div>
                            </div>

                            <div class="col-md-6 col-md-5">
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order No') ?> : </div><div class="col-lg-8"><b><?= $model->no ?> </b></div>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Order Date') ?> : </div><div class="col-lg-8"><?= $model->order_date ?> </div>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Due Date') ?> : </div><div class="col-lg-8"><?= $model->paymentdue ?> .</div>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Vat') ?> : </div><div class="col-lg-8"><?php // $model->vattb->name ?> </div>
                                <?php
                                 if($model->include_vat == '0')
                                  {
                                    $vats = Yii::t('common','Include Vat');
                                  }else {
                                    $vats = Yii::t('common','Exclude Vat');
                                  }
                                ?>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','Vat') ?> : </div><div class="col-lg-8"><?= $vats ?> </div>
                                <div class="col-sm-4 col-xs-5"> <?=Yii::t('common','PO.') ?> : </div><div class="col-lg-8"><?=$model->ext_document ?> .</div>
                                <div class="col-sm-4 col-xs-5 "> <?=Yii::t('common','Status')?> : </div>
                                <div class="col-lg-8 ew-text-status">
                                  <div id="ew-tr-modal" data="<?=base64_encode($model->id)?>" style="cursor: pointer;"><?=$Fnc->OrderStatus($model) ?> </div>
                                </div>
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
                                    'class' => 'kartik\grid\SerialColumn',
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
                                            //$photo = 'images/product/'.$model->items->ItemGroup.'/'.$model->items->Photo;
                                            $item  = $model->items->No;

                                          }else {
                                            //$photo = 'images/nopic.png';
                                            $item  = '';
                                          }

                                          $html = ' <span id="ew-Item-Info" ew-item-no="'.$item.'" class="btn hidden-xs">'.Html::a(Html::img($model->items->picture,['style'=>'width:50px;']), ['/items/items/view-only', 'id' => $model->item],['target' => '_blank']).'</span>';


                                          if($model->description=='') $model->description = $model->items->description_th;

                                          // if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          // {
                                          //   $unitPrice = number_format($model->unit_price,2);
                                          // }else  {    // Exclude Vat.
                                          //   $unitPrice = number_format($model->unit_price,2);

                                          // }
                                          // if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          // {
                                          //   $sumline = number_format($model->quantity * $model->unit_price,2);
                                          // }else  {    // Exclude Vat.
                                          //   $sumline = number_format($model->quantity * $model->unit_price,2);                                             
                                          // }
                                          $discount = ($model->line_discount > 0 
                                                        ? '<i class="fas fa-sort-down text-green"></i>'.number_format($model->line_discount).'%' 
                                                        : '');

                                          $html.= '<div class="row hidden-sm hidden-md hidden-lg">
                                                    <div class="col-xs-2 text-left">
                                                      <span id="ew-Item-Info" ew-item-no="'.$item.'" class="btn">
                                                        '.Html::a(Html::img($model->items->picture,['style'=>'max-width:50px;', 'class' => 'img-responsive']), 
                                                        ['/items/items/view-only', 'id' => $model->item],['target' => '_blank']).'
                                                      </span>
                                                    </div>
                                                    <div class="col-xs-10 text-left" style="padding-left:22px;">
                                                      <p class="text-primary break-word" style="font-size: 10pt;">'.$model->description.'</p>
                                                      
                                                      <div class="row">
                                                        <div class="col-xs-8 text-left " title="'.$model->unit_price.'">
                                                          <span class="text-yellow">'.number_format($model->quantity).'</span> x <span class="text-red">'.number_format($model->unit_price,2).'</span> '.$discount.'</div>
                                                        <div class="col-xs-4 text-primary text-right">'.number_format($model->sumLine,2).'</div>
                                                      </div>
                                                      <p></p>
                                                    </div>
                                                  </div>';                                      
                                         return $html;
                                      }
                                  ],

                                  [
                                    'attribute' => 'item_no',
                                    'format' => 'raw',
                                    'contentOptions' => ['class' => 'hidden-xs'],
                                    'headerOptions' => ['class' => 'hidden-xs'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                      //return $model->itemstb['master_code'];
                                      return Html::a($model->crossreference->no, ['/items/items/view-only', 'id' => $model->item],['target' => '_blank']);
                                    },
                                    'footer' => '<div></div>',
                                  ],
                                    //'itemstb.Description',
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
                                    'label' => Yii::t('common','Stock'),
                                    'contentOptions' => ['class' => 'hidden-xs text-right font-roboto text-gray'],
                                    'headerOptions' => ['class' => 'hidden-xs text-right font-roboto'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){                                      
                                        return ($model->items->ProductionBom != '') 
                                                ? number_format($model->items->last_possible) 
                                                : number_format($model->items->last_stock);
                                        //return number_format($model->items->qtyAfter);
                                    }
                                  ],

                                  [
                                     'attribute' => 'quantity',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'hidden-xs text-right'],
                                     'contentOptions' => ['class' => 'hidden-xs text-right  bg-yellow'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        // $stock  = $model->items->ProductionBom != '' 
                                        //           ? $model->items->last_possible 
                                        //           : $model->items->last_stock;
                                        // $html   = '<div class="'.($stock >= $model->quantity ? 'hidden' : '').' text-aqua blink pull-left">!</div>';
                                        $html = number_format($model->quantity,2);

                                        return $html;
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
                                   // 'itemstb.UnitOfMeasure',
                                    //'unit_measure',
                                    //'unit_price',
                                  [
                                     'attribute' => 'unit_price',
                                     'format' => 'html',
                                     'headerOptions' => ['class' => 'hidden-xs text-right'],
                                     'contentOptions' => ['class' => 'hidden-xs text-right '],
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
                                    // 'footer' => 'รวม',
                                  ],

                                  [
                                    'label' => Yii::t('common','% Discount'),
                                     'format' => 'raw',
                                     'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'width:100px;'],
                                     'contentOptions' => ['class' => 'text-right hidden-xs'],
                                     'footerOptions' => ['class' => 'hidden-xs'],
                                     'value' => function($model){
                                        return number_format($model->line_discount);     
                                    },
                                     //'footer' => 'รวม',
                                      
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

                                          return number_format($model->sumLine,2);
                                          // if(Yii::$app->session->get('vat')==1) // Include Vat.
                                          // {
                                          //   return number_format($model->quantity * $model->unit_price,2);
                                          // }else  {    // Exclude Vat.
                                          //   return number_format($model->quantity * $model->unit_price,2);
                                          //   //$total += $item['quantity'] * $item['unit_price_exvat'] ;
                                          // }
                                          //return number_format($model->quantity * $model->unit_price,2);


                                      },
                                      //'footer' => number_format($Fnc->getTotalSaleOrder($dataProvider->models),2),
                                  ],

                                   /*
                                  [
                                    'label' => Yii::t('common','Can produce'),                                    
                                    'contentOptions' => ['class' => 'hidden-xs text-right font-roboto bg-purple'],
                                    'headerOptions' => ['class' => 'hidden-xs text-right font-roboto', 'style' => 'width:120px;'],
                                    'footerOptions' => ['class' => 'hidden-xs'],
                                    'value' => function($model){
                                        return number_format($model->items->invenByBom);
                                    }
                                  ]
*/
                              ];
              ?>
<div class="text-right">
        <div style="margin-bottom:5px;">
          <?php 
            echo  ExportMenu::widget([
                      'dataProvider'      => $dataProvider,
                      'columns'           => $gridColumns,
                      'filename'          => Yii::t('app', 'SaleOrder'),
                      'hiddenColumns'     => [0,1],   // SerialColumn & ActionColumn
                      'disabledColumns'   => [0,1], // ID & Name
                      'columnSelectorOptions'=>[
                          'label' => 'Columns',
                          'class' => 'btn btn-success-ew ',
                      ],
                      'showColumnSelector'=> false,
                      'target'            => ExportMenu::TARGET_BLANK,
                      
                      'fontAwesome'       => true,
                      'dropdownOptions'   => [
                          'label' => 'Export All',
                          'class' => 'btn btn-primary-ew'
                      ],
                      'exportConfig'      => [
                          ExportMenu::FORMAT_HTML => false,
                          ExportMenu::FORMAT_PDF  => false,
                      ]
                  ]); 
            ?>
        </div>
        </div>
              <?=  GridView::widget([
                  'dataProvider'=> $dataProvider,
                  //'filterModel' => $searchModel,
                  'showFooter' => false,
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
 
 
<div class="row">

  <div class="col-md-7">
    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
        <?= $model->sumtotal->total == 0 ? 'ศูนย์บาทถ้วน' : $Bahttext->ThaiBaht($model->sumtotal->total) ?>
    </p>
    <div><?= Yii::t('common','Remark') ?> : <span  class="text-red"><?= $model->remark ?></span></div>
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
            $vat_culor = 'background-color:green; color:#FFF;';
          }else {
            $vat_culor = 'background-color:#f39c12; color:#FFF;';
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
                <th style="<?=$vat_culor?>"><?=Yii::t('common','Vat')?> (<?=$model->sumtotal->vat?>%)</th>
                <td style="<?=$vat_culor?>" align="right"><?= number_format($model->sumtotal->incvat,2) ?></td>
              </tr>
              <?php endif; ?>




              <tr style="background-color:#00c0ef; color:#FFF;">
                <th><?=Yii::t('common','Grand total')?>:</th>
                <td align="right" ><?= number_format($model->sumtotal->total,2) ?> </td>
              </tr>

            </table>
          </div>


    </div>

    <?php //$this->render('__chat',['model' => $model]); ?>
  </div>
</div>


 
<div class="modal fade" id="modal-transport">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close close-transport"  aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=Yii::t('common','Transport')?></h4>
      </div>
      <div class="modal-body">
            <div class="row">
              <div class="my-5 col-xs-12">
                <label for="transport-name"><?=Yii::t('common','Transport Name')?></label> 
                <input type="text" class="form-control" name="name" id="transport-name" autocomplete="off"/>
              </div>

              <div class="my-10 col-xs-12">
                <label for="transport-address"><?=Yii::t('common','Address')?></label> 
                <input type="text" class="form-control" name="address" id="transport-address" autocomplete="off" />
              </div>

              <div class="my-5 col-xs-6">
                <label for="transport-contact"><?=Yii::t('common','Contact')?></label> 
                <input type="text" class="form-control" name="contact" id="transport-contact" autocomplete="off" />       
              </div>   

              <div class="my-5 col-xs-6">
                <label for="transport-phone"><?=Yii::t('common','Phone')?></label> 
                <input type="text" class="form-control" name="phone" id="transport-phone" autocomplete="off" />       
              </div>   
            </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default close-transport pull-left" ><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
        <button type="button" class="btn btn-success transport-save"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
      </div>
    </div>
  </div>
</div>

<?php
 
//if(Yii::$app->session->get('Rules')['rules_id'] == 4 || Yii::$app->session->get('Rules')['rules_id'] == 2) {
if(in_array($myRule['rules_id'],SysRuleModels::getPolicy('Main Function','SaleOrders','saleorder','actionCreate','ew-app-zone'))){
  if($model->status == 'Pre-Cancel')
  {
    // เมื่อกด Confirm จะเปลี่ยนสถานะเป็น Cancel
    $ConfCon    = 'Confirm-Cancel';

    // เมื่อกด Reject จะเปลี่ยนสถานะเป็น Checking
    $RejCon     = 'Checking';
  }else {

    $ConfCon  = 'Confirm';

    $RejCon     = 'Reject';
  }

  $btnApprove = '
  <div class="row ew-app-zone">
    <div class="col-md-offset-7">
    <div class="col-sm-12">
      <div class="row">
        <div class="col-xs-6">
        <button class="btn btn-danger btn-lg ew-btn-app-click" id="ew-reject" ew-data="'.$RejCon.'" data-rippleria>
          <i class="fa fa-eject" aria-hidden="true"></i> '.Yii::t('common','Reject').'<span class="hidden-xs"> (F7)</span></button>
        </div>
        <div class="col-xs-6 text-right">
        <button class="btn btn-success  btn-lg ew-btn-app-click" id="ew-confirm" ew-data="'.$ConfCon.'" data-rippleria>
          <i class="fa fa-check-square-o" aria-hidden="true"></i> '.Yii::t('common','Accept').'<span class="hidden-xs"> (F10)</span></button>
        </div>
      </div>
    </div>
    </div>
  </div>';
  if($model->status=='Release' || $model->status=='Pre-Cancel')
  {
    echo $btnApprove;
  }
}


?>

 



<?php
 $this->registerJS("
  var langText = {

        'add'         : '".Yii::t('common','Add')."',
        'back'        : '".Yii::t('common','Back')."',
        'ship'        : '".Yii::t('common','Ship-packing')."',
        'error_zero'  : '".Yii::t('common','[Error!]  Quantity to ship must not be "0".')."',
        'undonot_zero': '".Yii::t('common','[Error!]  The undo quantity  must not be "0".')."',
        'confirmship' : '".Yii::t('common','[Confirm] Do you want to post this shipment?')."',
        'overship'    : '".Yii::t('common','Over quantity')."',
        'lessthan'    : '".Yii::t('common','You can not enter values less than 0!')."',
        'deliticket'  : '".Yii::t('common','Delivery ticket')."',
        'print'       : '".Yii::t('common','Print')."',
        'undo'        : '".Yii::t('common','Undo')."',
        'save'        : '".Yii::t('common','Save')."',
        'confirmdel'  : '".Yii::t('common','Do you want to delete?')."',
        'shipment'    : '".Yii::t('common','Shipment')."',
        'confirmundo' : '".Yii::t('common','Do you want to undo this document?')."',
        'confirm'     : '".Yii::t('common','Confirm')."',
        'notAllow'    : '".Yii::t('common','Not allowed to delete documents with status')."'
      };

",\yii\web\View::POS_HEAD) ;
?>
<?php echo  $this->render('../modal/_tracking') ?>
<?php echo  $this->render('../modal/__modal_approve') ?> 

 
<?php //$this->registerJsFile('js/item-picker.js?v=3.03.23');?>
<?php //$this->registerJsFile('js/action_sale_invoice.js?v=3.12.26'); ?>
<?php $this->registerJsFile('js/saleorders/views.js?v=5.04.23'); ?>
<?php //$this->registerJsFile('js/saleorders/saleorder-form.js?v=3.09.05'); ?>
<?php $this->registerJsFile('js/saleorders/saleorder_index.js?v=3.06.21'); ?>
<?php $this->registerJsFile('js/warehouse/shipment.js?v=5.01.15');?>
