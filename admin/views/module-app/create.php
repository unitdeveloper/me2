<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ModuleApp */

$this->title = Yii::t('common', 'Create Module App');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Module Apps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="module-app-create" ng-init="Title='<?=$this->title?>'">

     

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
