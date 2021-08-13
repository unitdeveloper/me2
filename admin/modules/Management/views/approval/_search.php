<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model admin\modules\Management\models\ApprovalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="approval-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
 
    <div class="row">
        <div class="col-xs-12 col-sm-6 pull-right">
            <?php 
                echo $form->field($model, 'search', [
                    'addon' => ['append' => ['content'=>'<i class="fas fa-search"></i>']]
                ])->label(false);            
            ?>
        </div>
    </div>
    

    <?php ActiveForm::end(); ?>

</div>
