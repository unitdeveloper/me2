<?php

use yii\helpers\Html;
 

use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\SaleLine */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>
 
<div class="nav-tabs-custom" ng-init="noseries='<?= $SaleHeader->no ?>'">
    <ul class="nav nav-tabs">
        <li class="active">
        <a href="#General" data-toggle="tab" aria-expanded="true"> 
            <?= Icon::show('user', [], Icon::BSG) ?>
            General</a>
        </li>
        <li class=""><a href="#Invoicing" data-toggle="tab" aria-expanded="false">
            <?= Icon::show('barcode', [], Icon::BSG) ?>
            Invoicing</a></li>
        <li class=""><a href="#Shipping" data-toggle="tab" aria-expanded="false">
            <?= Icon::show('shopping-cart', [], Icon::BSG) ?>
        Shipping</a></li>
    </ul>
    <div class="tab-content">
    <div class="tab-pane  active" id="General">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($SaleHeader, 'no')->textInput(['ng-model'=> 'noseries']) ?>

                

            </div>
            <div class="col-md-6">
                a
            </div>
        </div>
    </div>
    <!-- /.tab-pane -->
    <div class="tab-pane fade" id="Invoicing">
            <!-- The timeline -->
            <div class="row">
            <div class="col-md-6">
                 asdf

            
            </div>
            <div class="col-md-6">
                 

               aaa
            </div>
            </div>

    </div>
    <!-- /.tab-pane -->

    <div class="tab-pane fade" id="Shipping">
        
           sss
    </div>
     <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>
 
<div class="SaleLine">
<?php 
 $gridColumns = [ 
                    'order_no',
                    'type',
                    'item_no',
                    'description',
                ];
?>
<?=  GridView::widget([
    'dataProvider'=> $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'responsive'=>true,
    'hover'=>true
]);
?>
   

</div>
  
<?= $form->field($model, 'order_no')->textInput(['ng-model'=> 'noseries']) ?>

                <?= $form->field($model, 'type')->textInput() ?>

                <?= $form->field($model, 'item_no')->textInput() ?>
  <?= $form->field($model, 'description')->textInput() ?>

                <?= $form->field($model, 'quantity')->textInput() ?>

                <?= $form->field($model, 'unit_measure')->textInput() ?>

                <?= $form->field($model, 'unit_price')->textInput() ?>  
                 <?= $form->field($model, 'line_amount')->textInput() ?>

                <?= $form->field($model, 'line_discount')->textInput() ?>

                <?= $form->field($model, 'need_ship_date')->textInput() ?>

                <?= $form->field($model, 'quantity_to_ship')->textInput() ?>

                <?= $form->field($model, 'quantity_shipped')->textInput() ?>

                <?= $form->field($model, 'quantity_to_invoice')->textInput() ?>

                <?= $form->field($model, 'quantity_invoiced')->textInput() ?>
                 <?= $form->field($model, 'create_date')->textInput() ?>

            <?= $form->field($model, 'user_id')->textInput() ?>

            <?= $form->field($model, 'comp_id')->textInput() ?>

            <?= $form->field($model, 'api_key')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
 
