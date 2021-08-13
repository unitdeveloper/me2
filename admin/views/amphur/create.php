<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Amphur */

$this->title = Yii::t('common', 'Create Amphur');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Amphurs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="amphur-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
