<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\medels\Message;
/* @var $this yii\web\View */
/* @var $model common\models\SourceMessage */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="source-message-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-xs-2">
        <?= $form->field($model, 'category')->dropDownList(['common' => 'common','app' => 'app']) ?>
      </div>
      <div class="col-xs-2">
        <i class="fa fa-text-width" aria-hidden="true"></i>
        <label>Message</label>
        <?= $form->field($model, 'message')->textInput()->label(false) ?>
      </div>
      <div class="col-xs-2">
        <span class="flag-icon flag-icon-gb"></span>
        <label>English</label>
        <input type="text" name="language[en]"  class="form-control language_en" value="<?=$model->getLanguage('en')->text?>"/>
      </div>
      <div class="col-xs-2">
        <span class="flag-icon flag-icon-th"></span>
        <label>ไทย</label>
        <input type="text" name="language[th]"  class="form-control" value="<?=$model->getLanguage('th')->text?>"/>
      </div>
      <div class="col-xs-2">
        <span class="flag-icon flag-icon-cn"></span>
        <label>中文</label>
        <input type="text" name="language[zh]"  class="form-control"  value="<?=$model->getLanguage('zh')->text?>"/>
      </div>
      <div class="col-xs-2">
        <span class="flag-icon flag-icon-la"></span>
        <label>ພາສາລາວ</label>
        <input type="text" name="language[la]"  class="form-control"  value="<?=$model->getLanguage('la')->text?>"/>
      </div>
      <div class="col-xs-12 text-right">

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Create') : '<i class="fa fa-floppy-o"></i> '.Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
      </div>
    </div>






    <?php ActiveForm::end(); ?>

</div>
<?php
$js=<<<JS

$('body').on('change','#sourcemessage-message',function(){
  duplicate($(this).val());
})

function duplicate(val){
  $('input.language_en').val(val);
  console.log(val);
}
JS;
$this->registerJS($js);
?>