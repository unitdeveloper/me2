<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Company;
use common\models\AppsRulesSetup;
use common\models\SalesPeople;


use yii\widgets\MaskedInput;

/* @var $this yii\web\View */
/* @var $model common\models\AppsRules */
/* @var $form yii\widgets\ActiveForm */

?>
<script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<div class="apps-rules-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation'      => $model->isNewRecord ? true : false,//เปิดการใช้งาน AjaxValidation
        'enableClientValidation'    => false,
        'validateOnChange'          => true,//กำหนดให้ตรวจสอบเมื่อมีการเปลี่ยนแปลงค่า
        'validateOnSubmit'          => true,//กำหนดให้ตรวจสอบเมื่อส่งข้อมูล
        'validateOnBlur'            => false,
    ]); ?> 

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><?= Html::encode($this->title) ?></h4></div>
            <div class="panel-body">

                

                
                <div class="col-md-6">

                <?php if($model->sprit_code!='') $model->users = $model->sprit_code.'-'.$model->user_id; ?>
                <?=$form->field($model, 'users')->textInput([
                        'readonly' => $model->isNewRecord ? false : 'readonly'
                    ])?>
                 

               
    
                </div>
                <div class="col-md-6">
                 
                
                <?= $form->field($model, 'name')->textInput() ?>


                </div>


                

            </div>
        
         
        </div>
    </div>
    <div class="col-md-6">
         <div class="panel panel-default">
            <div class="panel-heading"><h4><?=Yii::t('common','Company')?></h4></div>
            <div class="panel-body">
                

                <div class="row">
                
                    
                    <div class="col-sm-6">
                        <?php 

                            if(Yii::$app->user->identity->id==1)
                            {
                                $CompanyList = Company::find()
                                            ->orderBy(['name' => SORT_ASC])
                                            ->all();

                            }else {
                                $CompanyList = Company::find()
                                                                ->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
                                                                ->orderBy(['name' => SORT_ASC])
                                                                ->all();
                            }
                        

                        ?>
                        <?= $form->field($model, 'comp_id')->dropDownList(
                                        ArrayHelper::map($CompanyList,'id','name'),
                                        [

                                            'data-live-search'=> "true",
                                            'class' => 'selectpicker form-control',
                                            
                                            
                                            
                                        ] 
                                )->label(Yii::t('common','Company')) ?>
                                
                    </div>
                
                    <div class="col-sm-3">
                        <label><?=Yii::t('common','Status')?></label> 
                        <?= $form->field($model, 'status')->checkBox(['class'=>'input-lg','data-toggle'=>"toggle", 
                                    'data-style'=>"android", 'data-onstyle'=>"info", 'label' => null])?>    
                        
                    </div>
                    <div class="col-sm-3">
                        <?= $model->sales_id?>
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-6">

                        <?= $form->field($model, 'rules_id')->dropDownList(
                            ArrayHelper::map(AppsRulesSetup::find()->orderBy(['name'=>SORT_ASC])->all(),'id','name'),
                            [

                                'data-live-search'=> "true",
                                'class' => 'selectpicker form-control',
                                
                                    
                                    
                            ] 
                        )->label(Yii::t('common','Department')) ?>

                    </div>
                    <div class="col-md-6">
                        <?php 

                         
                        $SaleList = SalesPeople::find()
                                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                    ->andWhere(['status'=> 1])
                                    ->orderBy(['code'=> SORT_ASC])
                                    ->all();
                             
                        ?>
                        <?= $form->field($model, 'sale_id')->dropDownList(
                                                ArrayHelper::map($SaleList,'id',function($model){
                                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname;
                                                }),
                                            [

                                                'data-live-search'=> "true",
                                                'class' => 'selectpicker form-control',
                                                'prompt' => Yii::t('common','Sales People'),
                                                
                                                
                                                
                                            ] 
                                        ) ?>
                    </div>
                </div>


            </div>
        </div>
    
    <div class="form-group text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
 