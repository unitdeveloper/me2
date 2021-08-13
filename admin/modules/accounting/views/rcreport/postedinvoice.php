<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\models\FunctionBahttext;
use common\models\Company;

use kartik\grid\GridView;
//use yii\grid\GridView;

use common\models\Items;

$Bahttext = new FunctionBahttext();
$Fnc = new FunctionSaleOrder();
/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = $model->no_;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_no_, 'url' => ['view', 'id' => $model->document_no_]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<style type="text/css">
    footer {
    position: fixed;
    height: 100px;
    bottom: 0;
    width: 100%;
}
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>


<div class="sale-invoice-header-update" ng-init="Title='<?=$model->no_?>'">

    <h1><?= Html::encode($this->title) ?></h1>

     
<div class="panel panel-info">

    <!-- Invoice Header  -->
    <div class="panel-heading">
        <div class="row">
                <div class="col-xs-12" >
                <div class="pull-right">
                <?php if($model->no_ == ''): ?>
                    <a href="#" id="ew-get-source" class="btn btn-primary"  data-toggle="modal" data-target="#ew-modal-source"><i class="fa fa-search-plus" aria-hidden="true"></i> <?=Yii::t('common','Get source document') ?></a>
                <?php else:  ?>
                    <h4> <?=$model->no_?></h4>
                <?php endif; ?>
                </div>
                <div><h4><i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i> สร้าง ใบวางบิล/ใบแจ้งหนี้</h4></div>
            </div>

        </div>
    </div>
    <div class="panel-body">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4">
             
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-4">
                    <?= $form->field($model, 'cust_code')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                </div>
                <div class="col-sm-8">
                    <?= $form->field($model, 'cust_name_')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
                </div>
            </div>
            

           

            <?= $form->field($model, 'cust_address')->textInput(['readonly' => 'readonly']) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'district')->textInput(['readonly' => 'readonly']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'city')->textInput(['readonly' => 'readonly']) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'province')->textInput(['readonly' => 'readonly']) ?>
                </div>

                <div class="col-sm-6">
                    <?= $form->field($model, 'postcode')->textInput(['readonly' => 'readonly']) ?>
                </div>

            </div>
 

        </div>   
        <div class="col-sm-4">
            
            
            <?= $form->field($model, 'sales_people')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
            <?= $form->field($model, 'discount')->textInput(['readonly' => 'readonly']) ?>

        </div> 

        <div class="col-sm-2">
            <?= $form->field($model, 'posting_date')->textInput(['readonly' => 'readonly']) ?>

            <?= $form->field($model, 'order_date')->textInput(['readonly' => 'readonly']) ?>

            <?= $form->field($model, 'ship_date')->textInput(['readonly' => 'readonly']) ?>
        </div>
        <div class="col-sm-2">
              <?= $form->field($model, 'document_no_')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>

            <?= $form->field($model, 'doc_type')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
        </div>
    </div>

    <!-- /. Invoice Header  -->

    <!--  Invoice Line  -->
    <div class="row">
        <hr class="style2">  
    </div> 

    <div class="table">

        <?php 
        $gridColumns = [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'doc_no_',
                //'line_no_',
                //'customer_no_',
                //'code_no_',
                [
                    'label' => Yii::t('common','Item'),
                    'value' => function($model){

                        if($model->type=='Item')
                        {
                            $item = Items::find()->where(['No' => $model->code_no_])->one();
                            $code = $item->master_code;
                        }else {

                            // ค้นจาก GL Account 
                            // 
                            $code = 'G/L Number';
                        }
                        

                        return $code;
                    },
                    
                ],

                [
                    'label' => Yii::t('common','Name'),
                    'value' => function($model){

                        return $model->code_desc_;
                    },
                     
                ],

                // 'code_desc_',
                [
                    'label' => Yii::t('common','Quantity'),
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){

                        return number_format($model->quantity);
                    },
                     
                ],
                 //'quantity',
                 //'unit_price',
                [
                    'label' => Yii::t('common','Unit Price'),
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){

                        return number_format($model->unit_price);
                    },
                     
                ], 

                [
                    'label' => Yii::t('common','Discount'),
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){

                        return number_format($model->line_discount);
                    },
                     
                ], 

                [
                    'label' => Yii::t('common','Amount'),
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model){

                        return number_format(($model->quantity * $model->unit_price)- $model->line_discount);
                    },
                     
                ], 
                // 'vat_percent',
                // 'line_discount',

                //['class' => 'yii\grid\ActionColumn'],
            ];

        ?>


        <?=  GridView::widget([
              'dataProvider'=> $dataProvider,
              //'filterModel' => $searchModel,
              'summary' => false,
              //'showFooter' => true,
              'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
              'columns' => $gridColumns,
              //'responsive'=>true,
              //'hover'=>true,
              //'summary' => false,
          ]);
        ?>
         
    </div>


    
    <!--  /. Invoice Line  -->

    <div class="row">
<hr class="style14">  

<?php

  $vat          = $model->vat_percent; 
  $BeforeDisc   = $Fnc->getTotalSaleOrder($dataProvider->models);

  $Discount     = $model->discount;

  // หักส่วนลด (ก่อน vat)
  $subtotal     = $BeforeDisc - $Discount ;


  if($model->include_vat == 1){ 

  // Vat นอก


  $InCVat   = ($subtotal * $vat )/ 100;

    $total    = ($InCVat + $subtotal);
  }else {

  // Vat ใน

   

  $InCVat   = $subtotal - ($subtotal / 1.07);

    $total    = $subtotal;
  }
 
 


?> 
  <div class="col-md-8">
    <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
        <?php echo  $Bahttext->ThaiBaht($total) ?>   </p>
     <div class="hidden">
          <p class="lead">ช่องทางการชำระเงิน:</p>
          <img src="images/credit/visa.png" alt="Visa">
          <img src="images/credit/mastercard.png" alt="Mastercard">
          <img src="images/credit/american-express.png" alt="American Express">
          <img src="images/credit/paypal2.png" alt="Paypal">

         
    </div>
  </div>
   
  <div class="col-md-4">
    <div class="panel panel-default">
        
           
        <div class="table-responsive" style="">
            <table class="table">


              

              <?php if($model->discount != 0): ?>
                <tr>
                  <th><?=Yii::t('common','Before Discount')?>:</th>
                  <td align="right"><?=number_format($BeforeDisc,2) ?></td>
                </tr>

                <tr>
                  <th><?=Yii::t('common','Discount')?>:</th>
                  <td align="right"><?=$Discount ?></td>
                </tr>
              <?php endif; ?>


              <tr>
                <th style="width:50%"><?=Yii::t('common','Subtotal')?>:</th>
                <td align="right"><?= number_format($subtotal,2) ?></td>
              </tr>


              <?php if($model->include_vat == 0): // Vat ใน ?>
                <tr>
                  <th> ก่อนรวมภาษี </th> 
                  <td align="right"><?= number_format($subtotal - $InCVat,2) ?></td>
                </tr>
              <?php endif; ?>

              <tr>
                <th ><?=Yii::t('common','Vat')?> (<?=$vat?>%)</th> 
                <td  align="right"><?= number_format($InCVat,2) ?></td>
              </tr>


              


              <tr >
                <th><?=Yii::t('common','Grand total')?>:</th>
                <td align="right" ><?= number_format($total,2) ?> </td>
              </tr>

            </table>
          </div> 
      
       
    </div>
  </div>
</div>

    <div class="form-group pull-right">

        <?=Html::a('<i class="fa fa-print" aria-hidden="true"></i> '.Yii::t('common','Print'),['print-inv', 'id' => $model->id],
            [
                'class' => 'btn btn-info',
                'target' => '_blank',
            ]) ?>
  
         
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
 

</div>


 