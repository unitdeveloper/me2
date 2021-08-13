<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Property */

$this->title = Yii::t('app', 'Create Property');
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="property-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
