<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Itemgroup;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\Itemgroup */
/* @var $form yii\widgets\ActiveForm */
$class = null;


if(isset($_GET['childof'])){
    $model->Child   = $_GET['childof'];
    $model->sequent = $model->getLastChild($model->GroupID)->sequent + 1;
    $class          = 'hidden';

    $dataList = ArrayHelper::map(Itemgroup::find()
    ->Where(['Child' => getParent($model->Child)])    
    ->all(),'GroupID','Description');

}else{
    if($model->Child==null) $model->Child = '00';
    if($model->isNewRecord){
        $model->Status  = 1;
        $model->Child   = '00'; // Main
        $model->sequent = 1;

        $dataList = ArrayHelper::map(Itemgroup::find()
        ->where(['Child' => '00'])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all(),'GroupID','Description');
    }else{
        // Update
         
        $dataList = ArrayHelper::map(Itemgroup::find()
        ->where(['Child' => getParent($model->Child)])
        ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
        ->all(),'GroupID','Description');
    }
    
    
    
}
 

function getParent($id){
    $g = Itemgroup::find()->where(['GroupID' => $id])->one();
    if($g!==null){
        return $g->Child;
    }else{
        return '00';
    }    
}

function getParentGroup($id){
    $gg = Itemgroup::find()->where(['GroupID' => $id])->one();
    if($gg!==null){
    
        $ggg = Itemgroup::find()->where(['GroupID' => $gg->Child])->one();
        if($ggg!==null){
            return $ggg->Description;
        }else{
            
            return Yii::t('common','MAIN GROUP');
        }
    }else{
        return Yii::t('common','MAIN GROUP'); 
    }
    

    
     
}
?>

<div class="itemgroup-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-2 <?=$class?>">
            <?= $form->field($model, 'photo')->fileInput() ?>
            <?= Html::img($model->picture,['id' => 'img-preview-photo','class'=> 'img-responsive'])?>

            <?= $form->field($model, 'Status')->dropDownList([ '0' => 'Disable', '1'=> 'Enable' ],['prompt' => '', 'value' => '1']) ?>      
        </div>
        <div class="col-sm-10">
        
        
            

            <?= $form->field($model, 'Description_th')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'Description')->textInput(['maxlength' => true]) ?>

            <div class="row">                
                <div class="col-sm-6 hidden"><?= $form->field($model, 'sequent')->textInput(['maxlength' => true]) ?></div>
                <div class="col-sm-6">
                    <?php # $form->field($model, 'Child')->textInput(['maxlength' => true,'readonly' => (isset($_GET['childof']))? true : false])->label(Yii::t('common','Child Of')) ?>
                    <?=$form->field($model, 'Child')->dropDownList([
                        '00'=>Yii::t('common','MAIN GROUP'),
                        Yii::t('common',getParentGroup($model->Child))=>$dataList
                        ])->label(Yii::t('common','Child Of')) ?>
                </div>
            </div>

            

           
           

            

            <div class="form-group">
                  
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-save"></i> '.Yii::t('common', 'Save') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                 
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php 
$js =<<<JS
    function readURL(input,div) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $(div)
                .fadeOut(400, function() {
                    $(div).attr('src', e.target.result);
                })
                .fadeIn(400);

                //$('#img-preview').attr('src', e.target.result).fadeIn('slow');

            }

            reader.readAsDataURL(input.files[0]);
        }
    }


    $("#itemgroup-photo").change(function(){
        readURL(this,'#img-preview-photo');
    });
JS;

$this->registerJS($js);