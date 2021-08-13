<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EngineerType */

$this->title = Yii::t('app', 'Create Engineer Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Engineer Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="engineer-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
