<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\warehousemoving\models\HeaderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Report').' : '.Yii::t('common', 'RECEIVE/SHIPMENT');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="warehouse-header-index" ng-init="Title='<?=$this->title?>'">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

     
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'line_no',
            'PostingDate',
            //'DocumentDate',
            //'TypeOfDocument',
            'SourceDoc',
            //'SourceDocNo',
            //'DocumentNo',
            [
                'attribute' => 'DocumentNo',
                'format' => 'html',
                'label' => Yii::t('common','Customer'),
                'value' => function($model){
                    return Html::a($model->DocumentNo,['/warehousemoving/header/view','id'=>$model->id]);
                }
            ],
            //'customer_id',
            //'customer.name',
            [
                'attribute' => 'customer_id',
                'label' => Yii::t('common','Customer'),
                'value' => function($model){
                    return ($model->customer_id)? 
                    ($model->customer)? $model->customer->name : ''
                    : '-';
                }
            ],
            // 'SourceDoc',
            //'Description',
            [
                'attribute' => 'Description',
                'label' => Yii::t('common','Transport'),
                'value' => function($model){
                    return $model->Description;
                }
            ],
            // 'Quantity',
            // 'address',
            // 'address2',
            // 'district',
            // 'city',
            // 'province',
            // 'postcode',
            // 'contact',
            // 'phone',
            // 'gps:ntext',
            // 'update_date',
            // 'status',
            // 'user_id',
            // 'comp_id',
            // 'ship_to',
            // 'ship_date',
            // 'AdjustType',
            //  [
            //   'class' => 'yii\grid\ActionColumn',
            //   'options'=>['style'=>'width:150px;'],
            //   'contentOptions' => ['class' => 'text-right'],
            //   'buttonOptions'=>['class'=>'btn btn-default'],
            //   'template'=>'<div class="btn-group btn-group-sm text-center" role="group"> {view} {update} </div>'
            // ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options'=>['class' => 'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel'     => '«',         // Set the label for the "previous" page button
            'nextPageLabel'     => '»',         // Set the label for the "next" page button
            'firstPageLabel'    => Yii::t('common','page-first'),     // Set the label for the "first" page button
            'lastPageLabel'     => Yii::t('common','page-last'),      // Set the label for the "last" page button
            'nextPageCssClass'  => 'next',      // Set CSS class for the "next" page button
            'prevPageCssClass'  => 'prev',      // Set CSS class for the "previous" page button
            'firstPageCssClass' => 'first',     // Set CSS class for the "first" page button
            'lastPageCssClass'  => 'last',      // Set CSS class for the "last" page button
            'maxButtonCount'    => 5,           // Set maximum number of page buttons that can be displayed
            ],
    ]); ?>
<?php Pjax::end(); ?></div>
