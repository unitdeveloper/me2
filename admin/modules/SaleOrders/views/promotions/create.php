<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Promotions */

$this->title = Yii::t('common', 'Create Promotions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Promotions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="promotions-create" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
