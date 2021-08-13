<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\BankAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-account-form panel panel-default box-shadow" style="padding: 20px;">

    <?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-sm-3">
			<?php
				if($model->bank=='') $model->bank = '1';

				echo $form->field($model, 'bank')->dropDownList(
					ArrayHelper::map(\common\models\BankList::find()->orderBy(['name' => SORT_ASC])->all(),
				                            'id',
				                            'name'),[

				                            'data-live-search'=> "true",
				                            'class' => 'selectpicker form-control',
				                            'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')
				                             
				                        ] 
				) 
			?>


		</div>
		<div class="col-sm-3"><?= $form->field($model, 'bank_no')->textInput(['maxlength' => true]) ?></div>
	</div>
    <div class="row">
    	<div class="col-sm-6"><?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('ชื่อบัญชี') ?></div>
    	<div class="col-sm-6"><?= $form->field($model, 'branch')->textInput(['maxlength' => true]) ?></div>
    </div>
    

    <div class="row">
    	<div class="col-sm-6"><?= $form->field($model, 'bank_type')->dropDownList(['1' => 'บัญชีออมทรัพย์','2' => 'กระแสรายวัน']) ?></div>
    	
    </div>

    
 

 

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
