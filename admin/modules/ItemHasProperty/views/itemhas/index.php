<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ItemHasProperty\models\SearchItemhas */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Items Has Properties');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="items-has-property-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Items Has Property'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            //'Items_No',
            //'itemsNo.itemset',
            [
                'attribute' => 'itemset',
                'format' => 'raw',
                'value' => 'itemsNo.itemset'
            ],
            'itemsNo.itemSet.name',
            'itemsNo.master_code',
            'Items_No',
            'property_id',
            'values',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:200px;'],
                'headerOptions' => ['class' => 'hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'template'=>'<div class="btn-group btn-group text-center" role="group">{delete-line} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                     
                    'delete-line' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    'update' => function($url,$model,$key){
                        return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-primary']);
                    }
        
                  ]
              ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
