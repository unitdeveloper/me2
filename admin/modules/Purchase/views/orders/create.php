<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PurchaseHeader */

$this->title = Yii::t('common', 'Create Purchase Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Purchase Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="purchase-header-create" ng-init="Title='<?=$this->title?>'">

    

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'doc_no'        => $doc_no
    ]) ?>

</div>
