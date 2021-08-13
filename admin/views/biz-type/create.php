<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CommonBusinessType */

$this->title = Yii::t('common', 'Create Common Business Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Common Business Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="common-business-type-create" ng-init="Title='<?=$this->title?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
