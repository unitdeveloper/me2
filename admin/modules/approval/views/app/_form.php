<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use common\models\Cheque;
/* @var $this yii\web\View */
/* @var $model common\models\Approval */
/* @var $form yii\widgets\ActiveForm */



?>

<div class="approval-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-approve-cheque',
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>


    <?php
        if(isset($_GET['key'])){
            $cheque = Cheque::findOne($_GET['key']); 
            echo $this->render('_approve',['cheque' => $cheque]);

            Yii::$app->session->set('source',$cheque->id);
        }




    ?>

    
    <?= $form->field($model, 'source_id')->hiddenInput()->label(false) ?>

    
    <div class="form-group pull-right <?=Yii::$app->request->isAjax ? 'hidden' : ''?>">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Approve') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    
    $('#form-approve-cheque').on('beforeSubmit', function(e) {
        Ajaxsubmit($(this))
    }).on('submit', function(e){
        e.preventDefault();
    });


    function Ajaxsubmit(form){

        var formData = form.serialize();
        var action = form.attr('action');
        $.ajax({
          url: action,
          type: form.attr("method"),
          data: formData,
          success: function (getData) {

              $('div.ew-dialog-body').html(getData);
              
          },
          error: function () {
              alert("Something went wrong");
          }
        });

    }

</script>