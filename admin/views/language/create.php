<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SourceMessage */

$this->title = Yii::t('common', 'Create Source Message');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Source Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="source-message-create"  ng-init="Title='<?= Html::encode($this->title) ?>'">

     
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
