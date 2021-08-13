<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\form\ActiveForm;
//use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
use common\models\TransportList;
/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */
/* @var $form yii\widgets\ActiveForm */

$actions = $model->isNewRecord ? 'create' : 'update';

?>
<style type="text/css">
  .close-load{
    position: absolute;
    top: 45%;
    left: 45%;
    text-align: center;
    display: none;
  }
</style>
<div class="close-load">
 <i class="fa fa-refresh fa-spin fa-2x" aria-hidden="true"></i>
 <p> Loading </p>
</div>
<div class="warehouse-header-form" >

    <?php $form = ActiveForm::begin([
        'id' => 'form-warehouse-shipment-info',

        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data','data-action' => $actions,'data-key' => $model->id]
    ]); ?>


  <div class="row">

    <div class="col-sm-8">
        <?= $form->field($model, 'DocumentNo')->textInput(['maxlength' => true,'readonly'=>'readonly']) ?>
        <?php
          $model->gps     = $model->customer ? $model->customer->default_transport : '';        
          $TransportList = TransportList::find()
          ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
          ->orderBy(['name' => SORT_ASC])
          ->all();
    
          echo $form->field($model, 'gps',[
              'addon' => ['append' => ['content'=> Html::Button('<i class="fas fa-plus pointer add-transport"></i>',['class' => 'no-border'])]]
            ])->dropDownList(
              arrayHelper::map($TransportList,'id', 'name'),
              [
                  'class' => 'form-control',
                  //'prompt'=>'- เลือก Sales -',
              ]
          )->label(Yii::t('common','Transport By'));
        ?>
    </div>
    <div class="col-sm-4">
      <?php
          if($model->ship_date=='') $model->ship_date = date('Y-m-d',strtotime($model->ship_date));

          echo $form->field($model, 'ship_date')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => Yii::t('common','Ship Date').'...'],
            'value' => date('Y-m-d',strtotime($model->ship_date)),
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'removeButton' => false,
            'pluginOptions' => [
                //'format' => 'dd/mm/yyyy',
                'format' => 'yyyy-mm-dd',
                'autoclose'=>true
            ]
          ])->label(Yii::t('common','Ship Date'));

            ?>
    </div>
  </div>
    
    <p class="text-right">
      <a class=" " data-toggle="collapse" href="#contentId" aria-expanded="false" aria-controls="contentId"><i class="fas fa-plus"></i> More</a>
    </p>
    <div class="collapse" id="contentId">
      <div class="row">
          <div class="col-sm-6">
              <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
              <?= $form->field($model, 'address')->textarea(['maxlength' => true]) ?>  
          </div>
          <div class="col-sm-6">
              <?php if(!Yii::$app->request->isAjax) : ?>
                <?= $form->field($model, 'status')->dropDownList(['Shiped' => Yii::t('common','Shiped'),'Undo' => Yii::t('common','Undo')]) ?>
              <?php endif; ?>
              <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>
          </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">        
        <?= $form->field($model, 'comment')->textarea(['maxlength' => true,'placeholder' => Yii::t('common','Comment')])->label(Yii::t('common','Comment')) ?>
      </div>
      <div class="col-sm-6">
        <?php if($model->remark=='') $model->remark = '(ระวังสินค้าแตกง่าย)'; ?>
        <?= $form->field($model, 'remark')->textarea(['maxlength' => true,'style' => 'color:red;'])->label(Yii::t('common','Remark')) ?>       
      </div>
    </div>

    <div class="form-group <?=Yii::$app->request->isAjax ? 'hidden' : ''?>">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>





<?php 



$this->registerJS("
     
      
    $('#form-warehouse-shipment-info').on('beforeSubmit', function(e) {

        var form        = $(this);
        var formData    = form.serialize();
        var action      = form.data('action');
        var id          = form.data('key');

        $.ajax({
            url: 'index.php?r=warehousemoving/header/'+action+'&id='+id,
            type: form.attr('method'),
            data: formData,
            success: function (getData) {

                var obj = jQuery.parseJSON(getData);

                $('.Shipped').html('<i class=\"fa fa-refresh fa-spin fa-2x text-center\" aria-hidden=\"true\"></i>');

                renderShippedList(id);

                var icon = '<i class=\"fa fa-undo\"></i>';
                var alink = '<a href=\"#\" class=\"btn btn-app btn-danger  ew-undo-ship\" ew-shipped-id=id>'
                    + icon +'".Yii::t('common','Undo')."</a>';
                $('.undo-btn').html(alink);

                    
                if(obj.status == 200){

                    createPrintButton(id);

                    $('#ew-modal-WarehouseHeader .modal-body').slideUp();

                    $('#ew-modal-WarehouseHeader').modal('hide');

                }else {

                    alert('".Yii::t('common','Something went wrong')."'); 
                    
                    createPrintButton(id);                   
                    $('#ew-modal-WarehouseHeader .modal-body').slideUp();
                    $('#ew-modal-WarehouseHeader').modal('hide');
                }

            },
            error: function () {
                alert('".Yii::t('common','Something went wrong')."');
            }
        });


    }).on('submit', function(e){
        e.preventDefault();
    });

");
?>