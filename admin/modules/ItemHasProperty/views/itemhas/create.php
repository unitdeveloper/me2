<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ItemsHasProperty */

$this->title = Yii::t('app', 'Create Items Has Property');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Items Has Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="items-has-property-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
