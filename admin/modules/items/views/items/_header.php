<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\items\models\SearchItems */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
        'action' => ['stock'],
        'method' => 'get',
    ]); ?>
 
  

<div class="row main-head">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-sm-8">
                <h3><?=Yii::t('common','Product')?></h3>
            </div>
            <div class="col-sm-4">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary hidden']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <input type="text" class="form-control bg-blue-sky hidden-xs" />
            </div>
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-sm-3">
                        <input type="text" class="form-control bg-blue-sky hidden-xs" />
                    </div>
                    <div class="col-sm-3">
                        <input type="text" class="form-control bg-blue-sky hidden-xs" />
                    </div>
                    <div class="col-sm-6">                       
                        <?=$form->field($model, 'description_th',[
                                            'addon' => ['append' => ['content'=>'<i class="fa fa-search"></i>']]
                                            ])->label(Yii::t('common','Search'))->label(false) ?>
                                            
                    </div>
                </div>     
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<style>
    .main-head{
        /* background: rgb(253,253,253); */
        margin-top: -15px;
        padding-bottom: 15px;
        border-bottom:1px solid rgba(204, 204, 204, 0.13);
    }
    .bg-blue-sky{
        background-color: rgba(251, 254, 255, 0.32);
    }
</style>