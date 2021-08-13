<?php

use yii\helpers\Html;
 

/* @var $this yii\web\View */
/* @var $model common\models\SaleInvoiceHeader */

$this->title = Yii::t('common', 'Create Sale Invoice Header');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Invoice Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
 
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-invoice-header-create">

   

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>


<script type="text/javascript">
	$('.ew-print-preview').attr('disabled','disabled').attr('class','btn btn-default-ew').attr('href','#').attr('target','');
</script>