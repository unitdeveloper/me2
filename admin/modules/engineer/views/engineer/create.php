<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Engineer */

$this->title = Yii::t('app', 'Create Engineer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Engineers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="engineer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
