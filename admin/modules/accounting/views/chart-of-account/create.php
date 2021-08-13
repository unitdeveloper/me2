<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ChartOfAccount */

$this->title = Yii::t('common', 'Create Chart Of Account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Chart Of Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="chart-of-account-create" ng-init="Title='<?=$this->title?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
