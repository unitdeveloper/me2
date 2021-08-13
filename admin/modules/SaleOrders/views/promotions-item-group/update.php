<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model common\models\PromotionsItemGroup */

$this->title = Yii::t('common', 'Update Promotions Item Group: ' . $model->name, [
    'nameAttribute' => '' . $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Promotions Item Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
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
<div class="promotions-item-group-update" ng-init="Title='<?= Html::encode($this->title) ?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
