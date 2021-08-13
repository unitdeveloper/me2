<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use kartik\widgets\ActiveForm;


?>
<style media="screen">
  .redactor-toolbar{
    background-color: rgba(0, 90, 209, 0.65);
  }
</style>
<div class="print-page-form">
  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-sm-9">
      <div class="col-sm-offset-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

      </div>
      <div class="row">
          <div class="col-xs-12"><span class="pointer pull-right" data-toggle="collapse" data-target="#css-editor">
            <i class="fa fa-css3 text-aqua" aria-hidden="true"></i> <?=Yii::t('common','CSS')?></span>
          </div>
          <div class="col-xs-12 collapse" id="css-editor">
              <div class="">
                <i class="fa fa-css3 text-aqua" aria-hidden="true"></i> <?=Yii::t('common','Style Sheet')?>
              </div>
              <div class="" style="height:30px; background-color: rgba(0, 90, 209, 0.65); padding:8px; margin-bottom:-1px;">
                <a href="#" class="re-icon re-html" rel="html" tabindex="-1" style="color:#000;" data-toggle="collapse" data-target="#css-editor"></a>
              </div>

              <?= $form->field($model, 'style')->textarea(['rows' => 15,
              'style' => 'font-size:16px; background-color:#111010; color:#b3b3b3; padding:20px;'])
              ->label(false) ?>
              <?php //$form->field($model, 'style')->widget(\yii\redactor\widgets\Redactor::className(), []) ?>

          </div>

      </div>


      <?= $form->field($model, 'header')->widget(\yii\redactor\widgets\Redactor::className(), [
        'clientOptions' => [
          'imageManagerJson' => ['/images/upload/image-json'],
          //'imageUpload' => ['/images/upload'],
          'imageUpload' => \yii\helpers\Url::to(['/images/upload/image']),
          'fileUpload' => ['/images/upload/file'],
          'minHeight' => 300,
          'plugins' => ['clips', 'fontcolor','imagemanager','table','fontfamily','fullscreen'],
          //'imageUploadErrorCallback' => new \yii\web\JsExpression('function(json){ alert(json.error); }')
        ]
      ])?>

      <?= $form->field($model, 'footer')->widget(\yii\redactor\widgets\Redactor::className(), [
      'clientOptions' => [
          'imageManagerJson' => ['/images/upload/image-json'],
          'imageUpload' => ['/images/upload/'],
          'fileUpload' => ['/images/upload/file'],
          //'lang' => 'th',
          'plugins' => ['clips', 'fontcolor','imagemanager','table','fullscreen']
      ]
      ])?>

        <?= $form->field($model, 'signature')->widget(\yii\redactor\widgets\Redactor::className(), [
        'clientOptions' => [
            //'imageManagerJson' => ['/redactor/upload/image-json'],
            //'imageUpload' => ['/images/upload/'],
            //'fileUpload' => ['/images/upload/file'],
            //'lang' => 'th',
            'plugins' => ['clips', 'fontcolor','imagemanager','table','fullscreen']
        ]
        ]) ?>
    </div>
    <div class="col-sm-3">

      <?php // $form->field($model, 'logo')->fileInput() ?>
      <?= $form->field($model, 'margin_top',[
          'feedbackIcon' => ['default' => 'text-height']
      ])->textInput(['maxlength' => true,'placeholder' => '25'])->label('Top page Height') ?>

      <?= $form->field($model, 'header_height',[
          'feedbackIcon' => ['default' => 'text-height']
      ])->textInput(['maxlength' => true,'placeholder' => '110'])->label('Top page Height') ?>

      <?= $form->field($model, 'body_height',[
          'feedbackIcon' => ['default' => 'align-center']
      ])->textInput(['maxlength' => true,'placeholder' => '124mm']) ?>

      <?= $form->field($model, 'footer_height',[
          'feedbackIcon' => ['default' => 'text-width']
      ])->textInput(['maxlength' => true]) ?>



      <hr />

      <?= $form->field($model, 'pagination',
       ['addon' =>
         ['append' =>
           [
            'content'=>'<i class="fa fa-angle-down" aria-hidden="true"></i>',
           ]
         ]
       ])->textInput(['placeholder' => '15']) ?>

      <?= $form->field($model, 'paper_size')->dropDownList([
        'A3' => 'A3',
        'A4' => 'A4',
        'LETTER' => 'LETTER',
        ]) ?>

      <?= $form->field($model, 'comp_id')->hiddenInput(['value' => Yii::$app->session->get('Rules')['comp_id']])->label(false) ?>
      <div class="form-group">
          <?= Html::submitButton('<i class="fa fa-floppy-o" aria-hidden="true"></i> '.Yii::t('common', 'Save') , ['class' => 'btn btn-success']) ?>
      </div>
    </div>
  </div>










    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">

  // ใส่ tab ใน textarea
  $(document).delegate('#printpage-style', 'keydown', function(e) {
  var keyCode = e.keyCode || e.which;

  if (keyCode == 9) {
    e.preventDefault();
    var start = $(this).get(0).selectionStart;
    var end = $(this).get(0).selectionEnd;

    // set textarea value to: text before caret + tab + text after caret
    $(this).val($(this).val().substring(0, start)
                + "\t"
                + $(this).val().substring(end));

    // put caret at right position again
    $(this).get(0).selectionStart =
    $(this).get(0).selectionEnd = start + 1;
  }
  });

  // ใส่สีให้ข้อความ

</script>
