<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SetupNoSeries */

$this->title = $model->id;
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="setup-no-series-view">
 

    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'form_id',
            'form_name',
            'no_series',
            'comp_id',
        ],
    ]) ?>

</div>
