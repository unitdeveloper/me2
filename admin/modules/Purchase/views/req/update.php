<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\PurchaseHeader */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Purchase Order',
]) . $model->doc_no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Purchase Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->doc_no, 'url' => ['view', 'id' => $model->doc_no]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-update" ng-init="Title='<?=$this->title?>'">

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
