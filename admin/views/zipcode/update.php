<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Zipcode */

$this->title = Yii::t('common', 'Update {modelClass}: ', [
    'modelClass' => 'Zipcode',
]) . $model->amphur->AMPHUR_NAME;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Zipcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ZIPCODE_ID, 'url' => ['view', 'id' => $model->ZIPCODE_ID]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Update');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="zipcode-update" ng-init="Title='<?=$this->title;?>'">
 
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
