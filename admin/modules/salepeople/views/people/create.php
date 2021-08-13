<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */

$this->title = Yii::t('common', 'Create Sales People');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Peoples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-create" ng-init="Title='<?= Html::encode($this->title) ?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
