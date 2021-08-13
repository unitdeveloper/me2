<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PropertyHasGroup */

$this->title = Yii::t('common', 'Create Property Has Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Property Has Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="property-has-group-create">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
