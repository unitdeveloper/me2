<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel admin\models\ProvinceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Provinces');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="province-index" ng-init="Title='<?=$this->title;?>'">
 
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'PROVINCE_ID',
            //'PROVINCE_CODE',
            'PROVINCE_NAME',
            //'zone.name',
            //'GEO_SUB',
            [
                'attribute' => 'GEO_ID',
                'format' => 'raw',
                'value' => function($model){
                    return $model->zone->name;
                }
            ],
            [
                'attribute' => 'GEO_SUB',
                'format' => 'raw',
                'value' => function($model)
                {
                     
                    if($model->GEO_SUB==0)
                    {
                        return Yii::t('common','None');
                    }else if($model->GEO_SUB==1){
                        return Yii::t('common','Upper _');
                    }else if($model->GEO_SUB==2){
                        return Yii::t('common','Central _');
                    }else if($model->GEO_SUB==3){
                        return Yii::t('common','Lower _');
                    }
                },
                'filter' => Html::activeDropDownList($searchModel,'GEO_SUB',
                    [
                        '0' => Yii::t('common','None'),
                        '1' => Yii::t('common','Upper _'),
                        '2' => Yii::t('common','Central _'),
                        '3' => Yii::t('common','Lower _'),
                    ],
                    [                        
                        'class' => 'form-control',
                        'prompt' => Yii::t('common','Show All'),
                    ]),
            ],
            // 'latitude',
            // 'longitude',
            'countries.country_name',
            //['class' => 'yii\grid\ActionColumn'],
             [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:150px;'],
              'buttonOptions'=>['class'=>'btn btn-default'],
              'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} {delete} </div>'
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
