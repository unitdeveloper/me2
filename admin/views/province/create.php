<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = Yii::t('common', 'Create Province');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Provinces'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="province-create" ng-init="Title='<?=$this->title;?>'">
 

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
