<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BankList */

$this->title = Yii::t('common', 'Create Bank List');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bank Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bank-list-create" ng-init="Title='<?=$this->title?>'">


 
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
