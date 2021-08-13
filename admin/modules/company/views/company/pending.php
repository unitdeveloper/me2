<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\company\models\SearchCompany */
/* @var $dataProvider yii\data\ActiveDataProvider */
use yii\widgets\ActiveForm;
 
use common\models\Register;
   
$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
<div class="panel-heading"><i class="<?= $model->regis->icon ?>"></i> <b><?= $model->regis_name ?></b> ::  <?= $model->regis->name ?></div>
<div class="panel-body">
	<div class="company-index">

	   <?= DetailView::widget([
	                'model' => $model,
	                'attributes' => [
	                  
	                    'user.username',
	                    'date_regis',
	                    //'branch', 
	                    [
	                            'attribute' => 'branch',
	                            'format' => 'html',
	                            
	                            'label' => Yii::t('common','Branch'),
	                            'value' => function($model){

	                               if($model->branch ==='1'){
	                                    return 'สำนักงานใหญ่';                            
	                                     
	                                }else if($model->branch ==='2'){
	                                    return 'สาขาย่อย';
	                                } 
	                               },

	                        ],
	                    'regis_name',
	                    'regis_address',
	                     [
	                            'attribute' => 'regis.name',
	                            'format' => 'html',
	                            
	                            'label' => Yii::t('common','Regis Type'),
	                            'value' => function($model){

	                               
	                                return Html::a($model->regis->name, ['register/view','id'=>$model->id]);
	                            },

	                        ],
	                         
	                         
	                         
	                        [
	                            'attribute' => 'status',
	                            'format' => 'html',
	                            
	                            'label' => Yii::t('common','Status'),
	                            'value' => function($model){

	                               $div = '<div>'. Register::statusregis($model) .'</div>';

		                            if($model->status === 'reject')
		                             {
		                                $div.= '<div class="well">'. $model->reject_reason .'</div>';
		                             } 

		                             return $div;
	                               },

	                        ],
	                ],
	            ]) ?>
	            <div class="col-sm-4"></div>
	            <div class="col-sm-4">
	            <?= Html::a('Approve',['/approval/approve/approved','id'=>$model->id , 'code' => $model->gen_code], [
			        'class' => 'btn btn-info pull-right',
			        'data' => [
			            'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
			            'method' => 'post',
			        ],
			        ]) ?>
			    </div>
			    <div class="col-sm-4">
			    <!-- <?= Html::a('Reject',['/approval/approve/reject','id'=>$model->id , 'code' => $model->gen_code], [
			        'class' => 'btn btn-warning pull-right',
			        'data' => [
			            'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
			            'method' => 'post',
			        ],
			        ]) ?> -->
			     <button type="button" class="btn btn-warning pull-right" data-toggle="modal" data-target="#myComment">Reject</button>   
			    </div>
	</div>
</div>
</div>
 
 

<!-- Modal -->
<div id="myComment" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<?php $form = ActiveForm::begin([
	'action' => ['/approval/approve/reject','id' => $model->id, 'code' => $model->gen_code],
	'method' => 'POST',
	]); ?>
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= Yii::t('common','Reason') ?></h4>
      </div>
      <div class="modal-body">
        <?= $form->field($model, 'reject_reason')->textarea(['rows' => '6']) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <?= Html::submitButton(Yii::t('common', 'Reject Now'), ['class' => 'btn btn-danger']) ?>
        
      </div>
     <?php ActiveForm::end(); ?>
    </div>

  </div>
</div>