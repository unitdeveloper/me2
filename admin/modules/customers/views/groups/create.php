<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CustomerGroups */

$this->title = Yii::t('common', 'Create Customer Groups');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Customer Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="customer-groups-create" ng-init="Title='<?= Html::encode($this->title) ?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
