<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;

use yii\db\Expression;
 

use yii\helpers\ArrayHelper;
use kartik\icons\Icon;

use common\models\Itemset;

/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\KitbomLine */
/* @var $form yii\widgets\ActiveForm */
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?> 
<div class="kitbom-line-form">

    <?php $form = ActiveForm::begin(); ?>

 

    
    <div class="row">
        <div class="col-xs-6">
        <?php
        	if($model->multiple==NULL) $model->multiple = 1;
            if($model->multiple==1)
            {
                $multiple = 'multiple';
            }else {
                $multiple = false;
            }

            $model->item_set = explode(',',$model->item_set);
        ?>
        <?= $form->field($model, 'item_set')->dropDownList(arrayHelper::map(Itemset::find()->all(),'id','name','detail'),[
                                        'data-live-search'=> "true",
                                        'class' => 'selectpicker form-control ',
                                        'prompt' => Yii::t('common','Item Set'),
                                        'multiple' => $multiple,
        ]) ?>

        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-6">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'max_val')->textInput() ?>
        </div>
    </div>
 
    

    <div class="row">
        <div class="col-xs-6">
        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-xs-3"><?= $form->field($model,'priority')->textInput();?></div>
        <div class="col-xs-3">
        <label> <?=Yii::t('common','ON-OFF') ?></label>
        <?= $form->field($model, 'multiple')->checkBox([
        'data-toggle'=>"toggle", 
        'data-style'=>"android", 
        'data-onstyle'=>"info",
        'data-on'=> Yii::t('common','Multiple') ,
        'data-off'=> Yii::t('common','Fix'),
        'label' => ' ',
        ]) ?>   

         

        </div>
    </div>
    <div class="row">
        
        <div class="col-xs-6">
            <?php $List = [
                'CODE%RUNNING%' => 'CODE%RUNNING% เช่น AAA1234',
                'CODE%RUNNING%ENDCODE' => 'CODE%RUNNING%ENDCODE เช่น AAA1234BBB',
            ]; ?>
            <?= $form->field($model,'format_type')->dropDownList($List,['disabled' => 'disabled']) ?>
        </div>
        <div class="col-xs-4 format_gen" ew-format-gen="<?=$model->format_gen;?>">
            
            <?= $form->field($model,'format_gen',[
                                    'addon' => ['append' => ['content'=> Yii::t('common','1234')]]
                                    ])->textInput(['class' => 'form-control text-right']) ?>
        </div>
        <div class="col-xs-2 running_digit" ew-digit="<?=$model->running_digit;?>">
            
            <?= $form->field($model,'running_digit')->textInput(['type' => 'number']) ?>
        </div>

    </div>
    <div class="row">
        
        <div class="col-xs-offset-6"> 
            <div class="col-xs-12">
                <label><?=Yii::t('common','For example') ?></label>
                <div style="border: 1px solid #ccc; height: 50px; background-color: #000; color: #FFF; text-align: center; font-size: 30px; padding-top: 2px;" class="ew-gen-text"></div>
            </div>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">

    $( document ).ready(function() {
         


        var str = $('.format_gen').attr('ew-format-gen') + '1234';
        var word = str.replace("%RUNNING%", "");

        $('.ew-gen-text').text(word);
        $('#kitbomheader-format_gen').val($('.format_gen').attr('ew-format-gen').replace("%RUNNING%", ""));


    });

    $('body').on('change','#kitbomheader-format_type',function(){

        //var genCode = $('.format_gen').attr('ew-format-gen');
        var genCode = $('#kitbomheader-format_gen').val();

        if($('#kitbomheader-format_type').val()==='CODE%RUNNING%ENDCODE')
        {
            $('#kitbomheader-format_gen').val(genCode + '%RUNNING%-ABC' );
            $('.ew-gen-text').text(genCode + '1234-ABC')
            //$('#kitbomheader-format_gen').val(genCode.replace("%RUNNING%", ""));

        }else  if($('#kitbomheader-format_type').val()==='CODE%RUNNING%')
        {
            $('#kitbomheader-format_gen').val(genCode + '%RUNNING%' );
            $('.ew-gen-text').text(genCode + '1234')
            $('#kitbomheader-format_gen').val(genCode.replace("%RUNNING%", ""));

        }


    });



    

</script>

<script>   

  $(function() {     
    $('input[id="kitbomheader-multiple"]').change(function() {
      
      if($(this).prop('checked')===true)
      {
        $('select[id="kitbomheader-item_set"]').attr('multiple','multiple');  
        $('select[id="kitbomheader-item_set"]').attr('name','KitbomHeader[item_set][]'); 
      }else{
        $('select[id="kitbomheader-item_set"]').removeAttr('multiple');
        $('select[id="kitbomheader-item_set"]').attr('name','KitbomHeader[item_set]'); 
      }
    })
  });
   
</script>

