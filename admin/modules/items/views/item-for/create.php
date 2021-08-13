<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemForCompany */

$this->title = Yii::t('common', 'รายการสต๊อก');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Item For Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-for-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'models' => $models,
        'duplicate' => $duplicate
    ]) ?>

</div>
