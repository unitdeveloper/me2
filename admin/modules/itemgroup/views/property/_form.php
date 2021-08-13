<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use common\models\Itemgroup; 
use common\models\Property;


$Itemgroup = new Itemgroup();
/* @var $this yii\web\View */
/* @var $model common\models\PropertyHasGroup */
/* @var $form yii\widgets\ActiveForm */
if(isset($_GET['GroupID']))
$model->itemgroup = $_GET['GroupID'];
?>
<div class="row">
<div class="col-sm-6">
	
		<div class="property-has-group-form">
			<div class="box box-default">
		        <div class="box-header with-border">
		          <h3 class="box-title"><?= Html::encode($this->title) ?></h3>

		          <div class="box-tools pull-right">
		            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
		          </div>
		        </div>
		        <div class="box-body">
				    <?php $form = ActiveForm::begin(); ?>

				     
				    <?= $form->field($model, 'itemgroup')->dropDownList(arrayHelper::map($Itemgroup->find()->all(),'GroupID','Description'))?>

				    <div class="row">
					 	<div class="col-sm-6">
					    <?= $form->field($model, 'property')->dropDownList(arrayHelper::map(Property::find()->all(),'id','description'),[

					                                                'data-live-search'=> "true",
					                                                'class' => 'selectpicker',
					                                                 
					                                            ])?>
					    </div>
					    <div class="col-sm-6">
					    	<label>ADD</label><p><?= Html::a('Add Property',['/property/property/create']) ?></p>
					    </div> 	
				    </div>
				    <?= $form->field($model, 'status')->dropDownList(['Enable' => 'Enable','Disable' => 'Disable']) ?>

				    <div class="form-group">
				        <?= Html::submitButton($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
				    </div>

				    <?php ActiveForm::end(); ?>

				</div>
			</div>
		</div>
	</div>
</div>
</div>