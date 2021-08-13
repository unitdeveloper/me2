<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
 
?>


                    
<?php yii\widgets\Pjax::begin(['id' => 'grid-invoice-pjax',
            'timeout'=>false,
            'enablePushState' => false,
            'enableReplaceState' => false,
            'clientOptions' => ['method' => 'POST']
            ]) ?>   
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
                'contentOptions' => ['class' => 'font-roboto'],
                'value' => function($model){ return $model->no_; }
            ],
            
            'customer.name',
            [
                'attribute' => 'total',
                'label' => Yii::t('common','Amount'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'font-roboto text-right'],
                'value' => function($model){ return number_format($model->sumtotals->total,2); }
            ],

            [
                //'attribute' => 'owner_sales',
                'label' => Yii::t('common','Select'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    $html = '<a href="#" class="btn btn-primary btn-flat" id="ew-pick-invoice">
                                 '.Yii::t('common','Select').'
                            </a>';
                    return $html;
                },
            ],
                
        ],
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=>Yii::t('common','First'),   // Set the label for the "first" page button
            'lastPageLabel'=>Yii::t('common','Last'),    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
<?php Pjax::end(); ?>
                