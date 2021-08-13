<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\unitofmeasure */

$this->title = Yii::t('app', 'Create Unitofmeasure');
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="unitofmeasure-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
