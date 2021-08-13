<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\customer */

$this->title = Yii::t('app', $model->name);
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="customer-update" ng-init="Title='<?= Html::encode($this->title) ?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
