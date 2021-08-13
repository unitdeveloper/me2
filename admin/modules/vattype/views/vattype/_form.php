<?php

use yii\helpers\Html;
use yii\helpers\arrayHelper;
use yii\widgets\ActiveForm;

use common\models\Company;
/* @var $this yii\web\View */
/* @var $model common\models\VatType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vat-type-form">
<div class="col-md-12  box box-info">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'vat_value')->textInput() ?>

    

    <?php 
            $data = Company::find()->all();
            
            $List = arrayHelper::map($data,'id', function ($element) {
                        return $element['name'];
                         
                    }); 
             
            
    ?>  
    
    <?= $form->field($model, 'comp_id')->dropDownList($List,[
                        'data-live-search'=> "true",
                        'class' => 'selectpicker customer_code',
                        'prompt'=>'- Select Company -',
                    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>