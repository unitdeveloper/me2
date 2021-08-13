<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Itemgroup */

$this->title = Yii::t('app', 'Create Itemgroup');
if(isset($_GET['childof'])){
    $this->title = 'Child of » '.$model->getParent($_GET['childof'])->Description;
}
 
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="itemgroup-create" ng-init="Title='Add Group'">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
