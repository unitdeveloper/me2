<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model admin\modules\Manufacturing\models\KitbomLine */

$this->title = Yii::t('common', 'Create Kitbom');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Kitbom'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="kitbom-line-create"  ng-init="Title='<?=$this->title;?>'">

    

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
