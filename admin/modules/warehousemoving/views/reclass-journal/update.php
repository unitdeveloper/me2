<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\WarehouseHeader */

$this->title = Yii::t('common', 'Items {modelClass}: ', [
    'modelClass' => 'Reclass Journal',
]) . $model->DocumentNo;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Warehouse Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('common', 'Adjust');
?>

<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="warehouse-header-update" ng-init="Title='<?= Html::encode($this->title) ?>'">

 <div class="next-previous hidden" style="position: absolute; margin-left: 20px;">
 	<?php
 		 
		// ยกเลิกการกด next, Previous
 	 
 		$Prev = common\models\ItemJournal::find()->where(['<','id',$model->id])->orderBy(['id' => SORT_ASC])->all();
 		$Previous = 0;
 		$PreID = 0;
 		foreach ($Prev as $value) {
 			$Previous = 'index.php?r=warehousemoving/adjust/update&id='.$value->id;
 			$PreID = $value->id;
 			
 		}
 		if($PreID==0) $Previous = '#';

 		$Next = common\models\ItemJournal::find()->where(['>','id',$model->id])->orderBy(['id' => SORT_DESC])->all();
 		$NextBt = 0;
 		$NexId = 0;
 		foreach ($Next as $value) {
 			$NextBt = 'index.php?r=warehousemoving/adjust/update&id='.$value->id;
 			$NexId = $value->id;
 			
 		}
 		if($NexId==0) $NextBt = '#';
 	?>
    <ul class="pager">
      <li><a href="<?=$Previous;?>">Previous</a></li>
      <li><a href="<?=$NextBt;?>">Next</a></li>
    </ul> 
</div>


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
