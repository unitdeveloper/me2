<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\unitofmeasure */

$this->title = $model->UnitCode;
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="unitofmeasure-view">

     

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'UnitCode',
            'Description',
        ],
    ]) ?>

</div>
