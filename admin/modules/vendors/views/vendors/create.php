<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Vendors */

$this->title = Yii::t('app', 'Create Vendors');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Vendors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="vendors-create"  ng-init="Title='<?=$this->title?>'">
 

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
