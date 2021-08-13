<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cheque */

$this->title = Yii::t('common', 'Create Cheque');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Cheques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cheque-create">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
