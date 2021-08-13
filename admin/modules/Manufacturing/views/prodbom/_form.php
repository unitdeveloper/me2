<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
 
use common\models\Items;
use common\models\ItemMystore;
use common\models\BomHeader;

use kartik\widgets\Select2;

$model->item = Yii::$app->request->get('BomHeader')['item'] ? Yii::$app->request->get('BomHeader')['item'] : $model->item;
        
//$model->code = Yii::$app->request->get('code');
/* @var $this yii\web\View */
/* @var $model common\models\BomHeader */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bom-header-form">

    <?php $form = ActiveForm::begin(); ?>
    <h4><?=Yii::t('common','Select Item')?></h4>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'item')->widget(Select2::classname(),[
                    'name' => 'Items',
                    'data' => ArrayHelper::map(
                        $model->isNewRecord ? 

                            Yii::$app->request->get('BomHeader') ? 
                                Items::find()->where(['id' => Yii::$app->request->get('BomHeader')['item']])->all() 
                            : 
                            Items::find()
                                ->joinWith('itemmystore')
                                ->where(['not in','item_mystore.master_code',BomHeader::find()->select('code')]) 
                                ->andWhere(['item_mystore.comp_id'=>Yii::$app->session->get('Rules')['comp_id']])
                                ->andWhere(['items.status' => 1])
                                ->andWhere(['<>','items.id',1414])
                                ->orderBy(['items.master_code' => SORT_ASC])
                                ->all() 
                        : 
                            Items::find()
                            ->joinWith('itemmystore')
                            ->where(['item_mystore.comp_id'=>Yii::$app->session->get('Rules')['comp_id']])
                            ->andWhere(['items.status' => 1])
                            ->andWhere(['<>','items.id',1414])
                            ->orderBy(['items.master_code' => SORT_ASC])
                            ->all()
                            ,
                        'id','description_th'
                    ),
                    'options' => [
                        'placeholder' => Yii::t('common','Search'),
                        'multiple' => false,
                        'class'=>'form-control  col-xs-12',
                    ],
                    'pluginOptions' => [
                        'allowClear' => Yii::$app->request->get('BomHeader') ? false : true
                    ],
                    //'value' => Yii::$app->request->get('BomHeader')['item']
                ])->label(false) ?>

            <?= $model->isNewRecord ? '' : $form->field($model,'name')?>
            <?= $model->isNewRecord ? '' : $form->field($model,'description')?>
        </div>
        <?= Html::submitButton($model->isNewRecord ? 
        '<i class="far fa-save"></i> '.Yii::t('common', 'Save') 
        : 
        '<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
