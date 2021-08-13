<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemCraft */

$this->title = Yii::t('common', 'Create Item Craft');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item Crafts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-craft-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
