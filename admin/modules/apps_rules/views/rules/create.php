<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppsRules */

$this->title = Yii::t('common', 'Create Apps Rules');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Apps Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="apps-rules-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
