<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model common\models\PromotionsItemGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Promotions Item Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
<div class="promotions-item-group-view" ng-init="Title='<?= Html::encode($this->title) ?>'">
<div class="col-md-2 col-sm-4">
        <h4><?=$model->name?></h4>        
        <p>
            <?= Html::a(Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    <div class="col-md-10 col-sm-8">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'description',
                'items.description_th',
                'comp_id',
            ],
        ]) ?>
    </div> 

</div>
