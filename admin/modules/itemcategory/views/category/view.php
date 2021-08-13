<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = $model->name;
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="item-category-view">

     

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'discription',
            'child',
            'status',
            'date_added',
            'comp_id',
        ],
    ]) ?>

</div>
