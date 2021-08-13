<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\BankListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Bank Lists');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-list-index" ng-init="Title='<?=$this->title?>'">
    <div class="row">
        <div class="col-sm-7">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="fa fa-book" aria-hidden="true"></i> <?=$this->title?></h4></div>
                <div class="panel-body">
                    
                    <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                //'id',
                                [
                                    'attribute' => 'name',
                                    'format' => 'raw',
                                    'value' => function($model){
                                        return "<img src='uploads/{$model->imageFile}' class='img-responsive pull-left' style='width:25px; margin-right:5px;'> {$model->name}";
                                    }
                                ],
                                //'name',
                                'description',
                                //'country',
                                //'comp_id',

                                [
                                    'contentOptions' => ['class' => 'text-right'],
                                    'class' => 'yii\grid\ActionColumn',
                                    'options'=>['style'=>'width:150px;'],
                                    'buttonOptions'=>['class'=>'btn btn-default'],
                                    'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
                                ],
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>    
</div>
