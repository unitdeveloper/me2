<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Zipcode */

$this->title = Yii::t('common', 'Create Zipcode');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Zipcodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="zipcode-create" ng-init="Title='<?=$this->title;?>'">
 

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
