<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model common\models\PromotionsItemGroup */

$this->title = Yii::t('common', 'View');

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="hidden-xs">
	<?=Breadcrumbs::widget([
		'itemTemplate' => "<i class=\"fas fa-home\"></i> <li><i>{link}</i></li>\n", // template for all links
		'links' => [
            [
                'label' => Yii::t('common','Promotions'),
                'url' => ['promotions/index', 'id' => 10],
                'template' => "<li><b>{link}</b></li>\n", // template for this link only
            ],
            [
                'label' => Yii::t('common', 'Promotions Item Groups'), 
                'url' => ['promotions-item-group/index'],
                'template' => "<li><b>{link}</b></li>\n",
            ],
            $this->title,
            
		],
	]);?>
</div>
<div class="promotions-item-group-view-name" ng-init="Title='<?= Html::encode($this->title) ?>'">

<div class="row">
    <div class="col-md-2 col-sm-4">
        <h4><?=$model->name?></h4>
        <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="col-md-10 col-sm-8">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                
                'items.master_code',
                'items.description_th',
                
                //'comp_id',

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>

 
</div>
