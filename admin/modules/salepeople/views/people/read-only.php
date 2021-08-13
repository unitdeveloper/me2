<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\salepeople\models\SearchPeople */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Sales Peoples');
$this->params['breadcrumbs'][] = $this->title;
?>
 
<div class="sales-people-index" ng-init="Title='<?=$this->title;?>'">

 


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'code',
            [
                'attribute' => 'code',
                'format' => 'raw',
                'value' => function ($model) { 
                    return $model->code;
                },
            ],
            //'name',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) { 
                    return $model->name.' '.$model->surname;
                },
            ],
            'nickname',
            //'sale_group',
            // 'user_id',
            // 'comp_id',
             'tax_id',
             //'position',
             'address',
 
        ],
    ]); ?>
</div>
 