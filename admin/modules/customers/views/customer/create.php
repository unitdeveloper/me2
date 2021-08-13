<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\customer */

$this->title = Yii::t('app', 'Create Customer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="customer-create"  ng-init="Title='Create :: Customers'">

     

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
