<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BomHeader */

$this->title = Yii::t('common', 'Create Bom');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Bom Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="bom-header-create">

  

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
