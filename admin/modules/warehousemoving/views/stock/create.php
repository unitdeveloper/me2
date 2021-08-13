<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemJournal */

$this->title = Yii::t('common', 'Create Item Journal');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item Journals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-journal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
