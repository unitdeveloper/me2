<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use common\models\NumberSeries;
use common\models\ModuleApp;
/* @var $this yii\web\View */
/* @var $model common\models\SetupNoSeries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setup-no-series-form">

    <?php $form = ActiveForm::begin(); ?>

     
    <?php 
    // $form->field($model,'form_id')->dropDownList([
    //                     'SaleOrder' => 'Sale Order' , 
    //                     'PurchaseOrder' => 'Purchase Order',
    //                     'SaleInvoice' => 'Sale Invoice',
    //                 ],
    //                     ['options' => ['1' => ['Selected'=>'selected']
    //                 ]
    //     , 'prompt' => ' -- Select Module --']) 
        ?>

    <?= $form->field($model, 'form_id')->dropDownList(
        arrayHelper::map(ModuleApp::find()->all(),
                        'name','description'))?>

    <?= $form->field($model, 'form_name')->textInput() ?>

    
    <?= $form->field($model, 'no_series')->dropDownList(
    	arrayHelper::map(NumberSeries::find()
									    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
									    ->all(),
						'id',function($model){
                            return $model->name.' '.$model->description;
                            }))?>

 

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

