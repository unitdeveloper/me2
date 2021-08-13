<?php

use yii\helpers\Html;

$this->title = Yii::t('common', 'Rejected');
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="approval-reject" ng-init="Title='<?= Html::encode($this->title) ?>'">
    Rejected
</div>