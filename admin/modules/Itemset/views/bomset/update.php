<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\Manufacturing\models\KitbomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'BOM');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading"><?=$model->name?>
				<div class="pull-right text-success ew-refresh-btn" style="cursor: pointer;"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i> Refresh</div>
			</div>
			<div class="panel-body">
			<div class="row">
				<div class="col-sm-12">
					<h4 class="text-red"><?=$model->name?></h4>
					 
				</div>
			</div>
			<hr class="style2">
		
			<div class="row">
				<div class="col-sm-5">
					<h4 class="text-orange">สินค้าตัวหลัก (ไม่สามารถเปลี่ยนได้)</h4>
					<?php
					
					$fixed = '<div class="fixed">';
					foreach ($dataProvider->models as $key => $value) {
						$fixed .= $value->id;
					}
					$fixed.= '</div>';
					//echo $fixed;
					?>

					<?= GridView::widget([
			        'dataProvider' => $dataProvider,
			        //'filterModel' => $searchModel,
			        'columns' => [
	 
			            [
			            	'label' => Yii::t('common','Name'),
			            	'format' => 'raw',
			            	'value' => function($model)
			            	{
			            		return Html::a($model->code,['/Manufacturing/bom/view','id' => $model->id]);
			            	}
			            ],
						//'format_gen',
						//'priority',
			            [
			                'label' => Yii::t('common','Code'),
			                'format' => 'raw',
			                'value' => function($model){

			                	$btn = '';

			                	if($model->format_gen!='')
			                    {
			                        $btn.= '<div class="input-group">';
			                        $btn.= '	<input class="form-control text-right" value="'.$model->format_gen.'" style="background-color:#ccc; padding:5px; width:100%;" readonly><span class="input-group-addon"  style="background-color:#a0bbe5; padding:5px;">1234</span>';
			                        $btn.= '</div>';
			                         
			                    } 



			                	$KitbomLine = \common\models\KitbomLine::find()->where(['kitbom_no' => $model->id])->all();
			                	$btn.= '<div class="row"> ';
			                	$i = 0;
			                	foreach ($KitbomLine as  $value) {
			                		$i++;
									 $btn.= '<div class="col-sm-8">'.$i.'. '.$value->items['Description'].'</div>'; 
									 $btn.= '<div class="col-sm-4 text-right"><span class="label label-info">'.$value->quantity.'</span></div>'; 
								 
			                	}
			                	$btn.= '</div>';

			                	return $btn;
			                    




			                    
			                }
			            ],
						'priority',
			            //'name',
			            //'description',
						//'fixed',
						/*
			            [
			                'label' => yii::t('common','Multiple'),
			                'format' => 'raw',
			                'contentOptions' => ['class' => 'text-right'],
			                'value' => function($model)
			                {
			                    if($model->multiple == 1)
			                    {
			                        $status = 'checked';
			                    }else {
			                        $status = NULL;
			                    }
			                    $data = '<input id="ew-multiple" type="checkbox"  '.$status.'   data-toggle="toggle" data-style="android" data-onstyle="info" value="'.$model->multiple.'" ew-id="'.$model->id.'" data-on="'.Yii::t('common','Multiple').'" data-off="'.Yii::t('common','Fix').'">';

			                    return $data;
			                }
			            ],
*/
			            // [
			            //     'attribute' => 'status',
			            //     'format' => 'raw',
			            //     'contentOptions' => ['class' => 'text-right'],
			            //     'value' => function($model)
			            //     {
			            //         if($model->status == 1)
			            //         {
			            //             $status = 'checked';
			            //         }else {
			            //             $status = NULL;
			            //         }
			            //         $data = '<input id="ew-status" type="checkbox" '.$status.'   data-toggle="toggle" data-style="android" data-onstyle="success" value="'.$model->status.'" ew-enable="'.$model->id.'" data-on="'.Yii::t('common','Enable').'" data-off="'.Yii::t('common','Disable').'">';

			            //         return $data;
			            //     }
			            // ],
			            // 'quantity',
			            // 'color_style',
			            // 'comp_id',
			            // 'user_id',
			            //'running_digit',

			            //['class' => 'yii\grid\ActionColumn'],
			        ],
			    ]); ?>
				</div>
				<div class="col-sm-7">
					<h4 class="text-orange">สินค้าตัวเลือก (สามารถเลือกได้ตามต้องการ)</h4>
					<?php
					
					$multi = '<div class="panel">';
					$multi.= '<div class="panel-body">';
					foreach ($dataProvider2->models as $key => $value) {

						$multi .= '<div>'.$value->code.'</div>';
						$multi .= '<div>'.$value->name.'</div>';
						$multi .= '<div>'.$value->description.'</div>';
					}
					$multi.= '</div>';
					$multi.= '</div>';
					//echo $multi;

					?>

					<?= GridView::widget([
			        'dataProvider' => $dataProvider2,
			        //'filterModel' => $searchModel,

			        'columns' => [
			             
			            //'itemset.name',
			            //'code',
			            //'format_gen',
			            // [
			            //     'label' => Yii::t('common','Format'),
			            //     'format' => 'raw',
			            //     'value' => function($model){
			            //         if($model->format_gen!='')
			            //         {
			            //             $btn = '<div class="input-group">';
			            //             $btn.= '<input class="form-control text-right" value="'.$model->format_gen.'" style="background-color:#ccc; padding:5px; width:100%;" readonly><span class="input-group-addon"  style="background-color:#a0bbe5; padding:5px;">1234</span>';
			            //             $btn.= '</div>';
			            //             return $btn;
			            //         }else {
			            //             return '';
			            //         }
			                    
			            //     }
			            // ],
			            //'name',
			            [
			            	'label' => Yii::t('common','Name'),
			            	'format' => 'raw',
			            	'value' => function($model)
			            	{
			            		return Html::a($model->name,['/Manufacturing/bom/view','id' => $model->id]);
			            	}
			            ],

			            [
			            	'label' => Yii::t('common','Description'),
			            	'format' => 'raw',
			            	'value' => function($model)
			            	{

			            		$data = Html::a($model->description,['/Manufacturing/bom/view','id' => $model->id]);

			            		$KitbomLine = \common\models\KitbomLine::find()->where(['kitbom_no' => $model->id])->all();
			                	$data.= '<div class="row">';
			                	$i = 0;
			                	foreach ($KitbomLine as  $value) {
			                		 $i++;
									 $data.= '<div class="col-sm-8">'.$i.'. '.$value->items['Description'].'</div>'; 
									 $data.= '<div class="col-sm-4 text-right">'.$value->quantity.'</div>'; 
			                	}
			                	$data.= '</div>';

			                	
			            		return $data;
			            	}
						],
						'priority'
			            //'description',
						//'fixed',
						/*
			            [
			                'label' => yii::t('common','Multiple'),
			                'format' => 'raw',
			                'contentOptions' => ['class' => 'text-right'],
			                'value' => function($model)
			                {
			                    if($model->multiple == 1)
			                    {
			                        $status = 'checked';
			                    }else {
			                        $status = NULL;
			                    }
			                    $data = '<input id="ew-multiple" type="checkbox"  '.$status.'   data-toggle="toggle" data-style="android" data-onstyle="info" value="'.$model->multiple.'" ew-id="'.$model->id.'" data-on="'.Yii::t('common','Multiple').'" data-off="'.Yii::t('common','Fix').'">';

			                    return $data;
			                }
			            ],
*/
			            // [
			            //     'attribute' => 'status',
			            //     'format' => 'raw',
			            //     'contentOptions' => ['class' => 'text-right'],
			            //     'value' => function($model)
			            //     {




			            //         if($model->status == 1)
			            //         {
			            //             $status = 'checked';
			            //         }else {
			            //             $status = NULL;
			            //         }
			            //         $data = '<input id="ew-status" type="checkbox" '.$status.'   data-toggle="toggle" data-style="android" data-onstyle="success" value="'.$model->status.'" ew-enable="'.$model->id.'" data-on="'.Yii::t('common','Enable').'" data-off="'.Yii::t('common','Disable').'">';

			            //         return $data;
			            //     }
			            // ],
			            // 'quantity',
			            // 'color_style',
			            // 'comp_id',
			            // 'user_id',
			            //'running_digit',

			            //['class' => 'yii\grid\ActionColumn'],
			        ],
			    ]); ?>
				</div>
			</div>
			 
			<div class="kitbom-line-index" ng-init="Title='<?=$this->title;?>'">

			 
			    
			</div>
			</div>
		</div>
		 
	</div>
</div> 

 
<p>
	<a  data-toggle="collapse" href="#contentId" aria-expanded="false" aria-controls="contentId">
	คำอธิบาย <i class="fa fa-info-circle"></i>
	</a>
</p>
<div class="collapse" id="contentId">
	<img src="images/KIT_BOM.jpg" class="img-responsive" />
</div>

<script>   

  $(function() {     
    $('input[id="ew-status"]').change(function() {
      route('index.php?r=Manufacturing/ajax/update-status','POST',{param:{id:$(this).attr('ew-enable'),val:$(this).prop('checked')}},'Navi-Title');
    })
  });


  $(function() {     
    $('input[id="ew-multiple"]').change(function() {
        //alert('test');
     route('index.php?r=Manufacturing/ajax/update-multiple','POST',{param:{id:$(this).attr('ew-id'),val:$(this).prop('checked')}},'Navi-Title');
    })
  });


  $('body').on('click','.ew-refresh-btn',function(){
  	 route('index.php?r=Itemset%2Fbomset%2Fupdate&id=<?=$_GET['id']?>','GET',{data:1},'content');
  });
</script>
