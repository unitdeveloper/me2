<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\icons\Icon;

use yii\helpers\Url;
use kartik\widgets\DepDrop;

use common\models\Province;
use common\models\Amphur;
use common\models\District;
use common\models\Zipcode;

use yii\grid\GridView;
//use kartik\grid\GridView;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use kartik\widgets\DatePicker;

$Fnc = new FunctionSaleOrder();
?>

 
 
<div class="row" >
  <div class="col-sm-12">
  <div class="box box-info" >
    <div style="margin-top: 15px;">

  <?php $form = ActiveForm::begin([
      'id' => 'Form-SaleOrder',
      'options' => ['class' => 'SaleHeader'],
      'action' => ['saleorder/update-header','id' => $model->id], 
      'method'=> 'post'
      ]); ?>



   <?php if(Yii::$app->session->get('Rules')['rules_id']==9):   ?>      

        <div class="col-xs-9">

          <div class="row">
            <div class="col-sm-6">
              <?= $form->field($model,'transport')->textInput(['placeholder' => Yii::t('common','Transport By')])->label(Yii::t('common','Transport By')) ?>
            </div>
            <div class="col-sm-6">
              <?php //$form->field($model,'ship_date')->textInput()->label(Yii::t('common','Ship Date')) ?>


              <?php echo $form->field($model, 'ship_date')->widget(DatePicker::classname(), [
                                            'options' => ['placeholder' => 'Shipment Date'],
                                            'value' => $model->ship_date,  
                                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                            'pluginOptions' => [
                                                //'format' => 'dd/mm/yyyy',
                                                'format' => 'yyyy-mm-dd',
                                                'autoclose'=>true
                                            ]
                                        ]); 
              ?>
            </div>
          </div>

         
          
          <div class="row">
            <div class="col-sm-8">
              <?= $form->field($model,'ship_address')->textInput()->label(Yii::t('common','Ship Address'))  ?>
            </div>
            <div class="col-sm-4">
             <?php

              if(isset($model->customer->province))
              {
                $model->province = $model->customer->province;
              }

              if(isset($model->customer->city))
              {
                $model->city = $model->customer->city;
              }

              if(isset($model->customer->district))
              {
                $model->district = $model->customer->district;
              }


              echo $form->field($model, 'province')->dropDownList(
                    ArrayHelper::map(Province::find()->orderBy(['PROVINCE_NAME' => SORT_ASC])->all(),
                                                'PROVINCE_ID',
                                                'PROVINCE_NAME'),[

                                                'data-live-search'=> "true",
                                                'class' => 'selectpicker',
                                                'id'=>'ddl-province',
                                                'prompt'=>'เลือกจังหวัด'
                                                 
                                            ] 
                ) ?>
                </div>

          </div>
          <div class="row">
            <div class=" ">
              
                <div class="col-sm-4">
                  <?= $form->field($model, 'city')->widget(DepDrop::classname(), [
                                                'options'=>['id'=>'ddl-amphur'],
                                                'data'=> [
                                                    ArrayHelper::map(Amphur::find()
                                                    ->where(['PROVINCE_ID' => $model->province])
                                                    ->all(),'AMPHUR_ID','AMPHUR_NAME')
                                                ],
                                                'pluginOptions'=>[
                                                    'depends'=>['ddl-province'],
                                                    'placeholder'=>'เลือกอำเภอ...',
                                                    'url'=>Url::to(['/ajax/get-amphur'])
                                                ]
                                            ]); ?>
                </div>
                <div class="col-sm-4">
                 <?= $form->field($model, 'district')->widget(DepDrop::classname(), [
                                           'options'=>['id'=>'ddl-district'],
                                           'data' =>[
                                           ArrayHelper::map(District::find()
                                            ->where(['AMPHUR_ID' => $model->city])
                                            ->all(),'DISTRICT_ID','DISTRICT_NAME')
                                           ],
                                           'pluginOptions'=>[
                                               'depends'=>['ddl-province', 'ddl-amphur'],
                                               'placeholder'=>'เลือกตำบล...',
                                               'url'=>Url::to(['/ajax/get-district'])
                                           ]
                                ]); ?>
                </div>
              
            </div>
            <div class="col-sm-4">

             
              
              <?php //echo  $form->field($model,'zipcode')->textInput(['value' => $model->customer->postcode])->label(Yii::t('common','Zip Code')) 



              ?>

              <?= $form->field($model, 'zipcode')->widget(DepDrop::classname(), [
                                                 
                                                'data'=> [
                                                    ArrayHelper::map(Zipcode::find()
                                                    ->where(['PROVINCE_ID' => $model->customer->province])
                                                    ->andwhere(['AMPHUR_ID' => $model->city])
                                                    ->andwhere(['DISTRICT_ID' => $model->district])
                                                    ->all(),'ZIPCODE','ZIPCODE')
                                                ],
                                                'pluginOptions'=>[
                                                    'depends'=>['ddl-district'],
                                                    'placeholder'=>'รหัสไปรษณีย์...',
                                                    'url'=>Url::to(['/ajax/get-zipcode'])
                                                ]
                                            ]); ?>


               
            </div>
          </div>
        </div>


        <div class="col-xs-3">
           
          <!-- <div><label>Save</label></div>
           <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-lg']) ?>
           -->
        </div>
    <?php else: ?>

      <div class="col-sm-8"><?= $model->ship_address ?></div>
      <div class="col-sm-4"><?= $model->zipcode ?></div>
      <div class="row">
        <div class="col-sm-12" style="text-align: center;">
          <img src="images/icon/ewin-truck.png" height="300px" alt="" >
        </div>
      </div>
    <?php endif; ?>


  <?php ActiveForm::end(); ?> 
      </div>
    </div>
  </div>
</div>





