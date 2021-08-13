<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppsRulesSetup */

$this->title = Yii::t('common', 'Create Department');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Department'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="apps-rules-setup-create"  ng-init="Title='<?= Html::encode($this->title) ?>'">
 
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
