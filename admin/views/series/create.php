<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\NumberSeries */

$this->title = Yii::t('common', 'Create Number Series');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Number Series'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="number-series-create" ng-init="Title='<?=$this->title?>'">

   

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
