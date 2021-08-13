<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PrintPage */

$this->title = Yii::t('common', 'Create Print Page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Print Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="print-page-create" ng-init="Title='<?=$this->title?>'">

     

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
