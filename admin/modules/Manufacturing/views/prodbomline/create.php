<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BomLine */

$this->title = Yii::t('common', 'Create Bom Line');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Lines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bom-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
