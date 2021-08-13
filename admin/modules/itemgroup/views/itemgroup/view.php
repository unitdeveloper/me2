<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Itemgroup */

$this->title = $model->Description;

?>
<style>
    li.child-group{
        list-style-type: none;
        padding-bottom:30px;
    }
    li.child-group:hover{
        list-style-type: none;
		color:red;
		/* border-bottom:1px solid #ccc; */
    }
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row">
	<div class="col-sm-6">
		<div class="itemgroup-view box box-primary" data-key="<?=$model->GroupID;?>">

			<div class="row">
				<div class="col-xs-3">
					<img src="<?=$model->picture;?>" class="img-responsive">
				</div>
				<div class="col-xs-9">
					<?= DetailView::widget([
						'model' => $model,
					//'options' => ['class' => 'table table-striped table-bordered'],
						'attributes' => [
							'GroupID',
							'Description',
							'Description_th',
							'Child',
							'Status',
						],
					]) ?>
					
				</div>
				
			</div>	
			<div class="box-footer">
				<div class="text-right">
					<?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->GroupID], [
						'class' => 'btn btn-danger-ew',
						'data' => [
							'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
							'method' => 'post',
						],
					]) ?>
				</div>				
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="row">
			<div class="col-md-12 ">
				<div class=" ">
					<div class="panel panel-default fixed">
					<div class="panel-heading">Property Template
						<div class="right pull-right"><?= Html::a('Add',['property/create','GroupID' => $model->GroupID]) ?></div>
					</div>
					<div class="panel-body">
						<?= $this->render('_actionPropertyasGroup',['searchModel' => $searchModel,'dataProvider'=> $dataProvider]) ?>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
echo $model->GroupID .' » '.$model->Description_th.' '.Html::a('<i class="far fa-plus-square"></i> '.Yii::t('common','Add Child Group'),['create','childof' => $model->GroupID]); ?>

<?=$model->getChildLoop($model->GroupID)?>
 
