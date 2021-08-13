<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\SaleReturnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Return/Receive');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-return-header-index font-roboto" ng-init="Title='<?=$this->title?>'">


   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'Photo',
                'format' => 'raw',
                'value' => function($model){
                    return Html::img($model->picture,
                            [
                                'style'=>'width:50px;',
                                'class' => 'img-responsive'
                            ]);
                   // return '<img src="'.$model->picture.'" class="img-responsive" width="50"/>';
                }
            ],
            [
                'attribute' => 'item',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a($model->item,  ['/items/items/view','id'=>$model->item],['target' => '_blank']);
                }
            ],
            
            'barcode',
            'master_code',
            'name',
            ['class' => 'yii\grid\ActionColumn'],
            //'customers.name',
            
            //'sale_address',
           

             
        ],
    ]); ?>
</div>

 