<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMystore */

$this->title = Yii::t('common', 'Create Item Mystore');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item Mystores'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-mystore-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
