<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('common', 'Create User');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="user-create" ng-init="Title='<?= Html::encode($this->title) ?>'">
 

    <?= $this->render('_form', [
        'model' => $model,
        'profile' => $profile,
        'appRule' => $appRule
    ]) ?>

</div>
