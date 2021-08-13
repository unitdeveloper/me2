<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\AuthAssignment;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
//$model->password = $model->password_hash;
?>

<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">   

                <div class="row">

                    <div class="col-sm-8">
                        <?= $form->field($profile, 'name')->textInput(['maxlength' => true])->label((Yii::t('common','Name').'-'.Yii::t('common','Surname'))) ?>
                        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                        
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>  
        
                        <?= $model->isNewRecord ? $form->field($model, 'password')->textInput(['required' => true ]) : null ?>  
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($profile, 'gender')->dropDownList(['Man' => Yii::t('common','Man'),'Woman' => Yii::t('common','Woman')]) ?>
                        <div >
                            <label><?=Yii::t('common','Status')?></label> 
                                
                            <?= $form->field($appRule, 'status')->checkBox([
                                'class' =>'input-lg',
                                'data-toggle'   =>"toggle", 
                                'data-style'    =>"android", 
                                'data-onstyle'  =>"info", 
                                'label' => null])?>    
                        </div>
                        <label><?php //Yii::t('common','Permission')?></label>
                        <?php /* $model->isNewRecord ? Html::dropDownList('rules_name', null,
                            ArrayHelper::map(
                            AuthAssignment::find()->select('item_name')->groupBy('item_name')->all(),
                                'item_name','item_name'
                            ),
                            [
                                'class'=>'form-control',
                                //'prompt' => Yii::t('common','Every one'),
                                'options' => ['Owner' => ['selected' => 'selected']]
                            ] 
                                        
                        )  : null */
                        ?>
                    </div>
                    
                </div>            
                 
            
            </div>
            <div class="col-md-6">
               

                <div class="panel panel-default" style="margin-top: 22px;">
                    <div class="panel-heading"><h4><?=Yii::t('common','Company')?></h4></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <?php 

                                    if(Yii::$app->user->identity->id==1){
                                        $CompanyList = \common\models\Company::find()
                                                        ->where(['status' => 1])
                                                        ->orderBy(['name' => SORT_ASC])
                                                        ->all();
                                    }else {
                                        $CompanyList = \common\models\Company::find()
                                                        ->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
                                                        ->orderBy(['name' => SORT_ASC])
                                                        ->all();
                                    }
                                ?>
                                <?= $form->field($appRule, 'comp_id')->dropDownList(
                                        ArrayHelper::map($CompanyList,'id','name'),
                                        [
                                            'data-live-search'=> "true",
                                            'class' => 'selectpicker form-control',
                                            'options' => $model->isNewRecord 
                                                        ? [ 1                   => ['selected' => 'selected']]
                                                        : [ $appRule->comp_id   => ['selected' => 'selected']],
                                        ])->label(Yii::t('common','Company')) ?>
                            </div>
                           
                             
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($appRule, 'rules_id')->dropDownList(
                                    ArrayHelper::map(\common\models\AppsRulesSetup::find()->orderBy(['name'=>SORT_ASC])->all(),'id','name'),
                                    [
                                        'data-live-search'=> "true",
                                        'class' => 'selectpicker form-control', 
                                       
                                    ] 
                                )->label(Yii::t('common','Department')) ?>
                            </div>
                            <div class="col-md-6">
                                <?php 
                                $SaleList = \common\models\SalesPeople::find()
                                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                            ->andWhere(['status'=> 1])
                                            ->orderBy(['code'=> SORT_ASC])
                                            ->all();
                                    
                                ?>
                                <?= $form->field($appRule, 'sale_id')->dropDownList(
                                                        ArrayHelper::map($SaleList,'id',function($model){
                                                            return '['.$model->code.'] '.$model->name. ' '.$model->surname;
                                                        }),
                                                    [

                                                        'data-live-search'=> "true",
                                                        'class' => 'selectpicker form-control',
                                                        'prompt' => Yii::t('common','Sales People'),
                                                        'options' => [                        
                                                            7 => ['selected' => 'selected']
                                                        ],
                                                    ] 
                                                )->label(Yii::t('common','Sale People')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            
                                
            </div>
        </div>
        <div class="form-group pull-right">
            <?= Html::submitButton($model->isNewRecord ? '<i class="far fa-save"></i> '.Yii::t('common', 'Create') : '<i class="far fa-save"></i> '.Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
        
    <?php ActiveForm::end(); ?>
</div>
