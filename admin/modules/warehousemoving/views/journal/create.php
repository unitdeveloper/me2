<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = Yii::t('common', 'Item Journal');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="warehouse-header-create" ng-init="Title='<?= Html::encode($this->title) ?>'">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<script type="text/javascript">
	
$(document).ready(function(){
	
	setTimeout(function(){ 
		
		ValidateSeries('Adjust','5','item_journal',$(form+'typeofdocument').val(),$(form+'adjusttype').val(),'#itemjournal-documentno',true);

         
	}, 300);

});
</script>