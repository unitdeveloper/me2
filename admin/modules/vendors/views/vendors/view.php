<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Vendors */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="vendors-view" ng-init="Title='<?=$this->title?>'">
<?php if(Yii::$app->session->hasFlash('alert')):?>
    <?= \yii\bootstrap\Alert::widget([
    'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
    'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
    ])?>
<?php endif; ?>




    <div class="row">
        <div class="col-xs-2">
            <div class="row ">
                <div class="col-xs-12"><?php echo Html::img($model->getPhotoViewer('logo'),['class' => 'img-responsive img-rounded img-thumbnail','style' => 'min-width:100px;']);?></div>
                <div class="col-xs-12"><?php echo Html::img($model->getPhotoViewer('photo'),[
                    'class' => 'img-responsive img-rounded img-thumbnail',
                    'style' => 'min-width:100px;margin:20px 0 20px 0;',
                    'id'=>"zoom_01",
                    'data-zoom-image'=> $model->getPhotoViewer('photo') == '/uploads/kitbom/img.png' ? ' ' : $model->getPhotoViewer('photo') ]);?></div>
            </div>
            <?= Html::a('<i class="fa fa-edit" ></i> '.Yii::t('common', 'Edit'), ['update', 'id' => $model->id], [
                'class' => 'btn btn-warning-ew  mr-5',                 
            ]) ?>
             <?= Html::a('<i class="fa fa-trash-o" aria-hidden="true"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger-ew pull-right',
                'data' => [
                    'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>

        <div class="col-xs-10" style="font-family: saraban;">



            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'code',
                    'name',
                    'address',
                    'address2',
                    'vat_regis',
                    [
                        'attribute' => 'payment_term',
                        'value' => function($model){
                            return $model->payment_term .' '. Yii::t('common','Day');
                        }
                    ],
                    'phone',
                    'fax',
                    'contact',
                    'district',
                    'city',
                    'province',
                    'postcode',
                    'country',                    
                   
                    'vendor_posting_group',
                    'vatbus_posting_group',
                    'email:ntext',
                    'homepage:ntext',
                    'headoffice',
                    'branch',
                    'branch_name',
                ],
            ]) ?>
        </div>
    </div>
</div>
