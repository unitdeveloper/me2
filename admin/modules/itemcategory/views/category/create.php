<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ItemCategory */

$this->title = Yii::t('common', 'Create Item Category');
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="item-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
