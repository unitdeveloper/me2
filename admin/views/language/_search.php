<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model admin\models\MessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-message-search pull-right">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

        <div class="input-group">
        
        <?= $form->field($model, 'message',['addon' => ['append' => ['content'=> Html::submitButton('<i class="fas fa-search"></i>',['class' => 'no-border'])]]])
        ->textInput(['class' => 'form-control','placeholder'=>Yii::t('common','Search')])
        ->label(false) ?>
            
        </div>

    <?php ActiveForm::end(); ?>

</div>
 