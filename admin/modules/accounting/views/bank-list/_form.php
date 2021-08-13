<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model common\models\BankList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bank-list-form">

    <?php $form = ActiveForm::begin([
    	'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

	<div class="row">
		<div class="col-sm-4"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
		<div class="col-sm-4"><?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?></div>
		<div class="col-sm-4">
			<?php
				if($model->country=='') $model->country = '213';

				echo $form->field($model, 'country')->dropDownList(
					ArrayHelper::map(\common\models\Countries::find()->orderBy(['country_name' => SORT_ASC])->all(),
				                            'id',
				                            'country_name'),[

				                            'data-live-search'=> "true",
				                            'class' => 'selectpicker form-control',
				                            'prompt'=>Yii::t('common','Select'). ' ' .Yii::t('common','country')
				                             
				                        ] 
				) 
			?>
		</div>
	</div>
    
    <hr>
	<div class="row">
		<div class="col-sm-4"><?= $form->field($model, 'imageFile')->fileInput(['class' => 'form-control']) ?></div>
	</div>
	
    
 
	

    <div class="form-group pull-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
