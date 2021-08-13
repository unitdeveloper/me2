<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Company;
use common\models\AppsRulesSetup;

/* @var $this yii\web\View */
/* @var $model common\models\SetupSysMenu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setup-sys-menu-form">

    <?php $form = ActiveForm::begin(); ?>

 
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
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
                                    ) ?>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'function_group_type')->dropDownList([
                                                                        'Data Access' => 'Data Access',
                                                                        'Main Function' => 'Main Function',
                                                                    ]);?> 
           
        </div>
         
        <div class="col-sm-6">
            <?php $model->rules_id = explode(',',$model->rules_id); ?>

            <?= $form->field($model, 'rules_id')->dropDownList(
                                        ArrayHelper::map(AppsRulesSetup::find()->orderBy(['name' => SORT_ASC])->all(),'id','name'),
                                        [
                                            'data-live-search'=> "true",
                                            'class' => 'selectpicker form-control',
                                            'multiple'=>"multiple",
                                        ] 
                                    ) ?>
        </div>
        
    </div>






    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'function_modules')->dropDownList([
                                                                        'accounting' => 'Accounting',
                                                                        'Customer' => 'Customer',
                                                                        'Finance' => 'Finance',
                                                                        'Items' => 'Items',
                                                                        'Main Menu' => 'Main Menu',
                                                                        'Management' => 'Management',
                                                                        'SaleOrders' => 'Sale Orders',
                                                                        'warehousemoving' => 'Warehouse'                                                                        
                                                                        
                                                                    ]);?> 
           
        </div>
         
        <div class="col-sm-3">

            <?= $form->field($model, 'function_controllers')->dropDownList([
                                                                        'Approve' => 'Approve',
                                                                        'customer' => 'Customer',
                                                                        'Invoice' => 'Invoice',
                                                                        'Items' => 'Items',                                                                        
                                                                        'order' => 'Order',
                                                                        'report' => 'Report',
                                                                        'saleorder' => 'Sale Orders',                 
                                                                    ]);?> 
          
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'function_models')->dropDownList([
                                                                        'Cheque' => 'Cheque',
                                                                        'actionCreate' => 'Create',
                                                                        'Customer' => 'Customer',
                                                                        'common' => 'common',                                                                        
                                                                        'actionIndex' => 'Index',
                                                                        'manage' => 'Manage',                                                                    
                                                                        'SaleAdmin' => 'Sale Admin', 
                                                                        'SalesDirector' => 'Sales Director',                                                                        
                                                                        'SalehearderSearch' => 'SalehearderSearch',    
                                                                    ]);?> 

           
        </div>


        <div class="col-sm-3">
            <?= $form->field($model, 'function_name')->textInput();?> 

           
        </div>
        
    </div>
	



    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'function_code')->textarea(['rows' => 9]) ?>
        </div>
         
        <div class="col-sm-6">

            <?= $form->field($model, 'detail')->textarea(['rows' => 9]) ?>
        </div>

       
        
    </div>

	
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
