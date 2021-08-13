<?php

use yii\helpers\Html;
use yii\db\Expression;
use yii\widgets\DetailView;

use common\models\Items;
use common\models\KitbomHeader;
use common\models\KitbomLine;
use common\models\WarehouseMoving;


/* @var $this yii\web\View */
/* @var $model common\models\Itemset */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Itemsets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="Picking row" >
	
	<div class="col-lg-12">
		<div class="row tabpanel">
			<!-- ->where(['item_set' => $model->id]) -->
			<?php
				$BomHeader = KitbomHeader::find()
							->where(new Expression('FIND_IN_SET(:item_set, item_set)'))
							->addParams([':item_set' => $model->id])
							->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
							->orderBy(['priority' => SORT_ASC])
							->all();

				$div 	= ' ';
				$i 		= 0;
				foreach ($BomHeader as  $value) {
					$i ++ ;
					if($value->multiple==0)
					{
						// AngularJS 
						$div.= '<div class="col-xs-12 nav nav-tabs bg-primary" role="tablist"  id="ew-Head" ew-detail="'.$value->code.'">
									<a data-toggle="collapse" href="#tab'.$i.'"  class="text-aqua collapsed" data-parent="#accordion" aria-expanded="false">
										<i class="fas fa-bars"></i> '.Yii::t('common',$value->description).'
									</a>
								</div>';

						$div.= '<div id="ew-gen-code" ew-id="'.$value->id.'" ew-Max="'.$value->max_val.'" ng-init="ID='.$value->id.'; Max='.$value->max_val.';" ew-set-code="'.$value->code.'"></div>';
						$div.= '<div class="fixed-item panel-collapse collapse" id="tab'.$i.'" >'.getChild($value->id,$i,$value->name, 'disabled').'</div>';  // ตู้เปล่า
						$div.= '<div id="ew-render-itemno" data-key=""></div>';
					}else {
						$div.= '<div class="col-xs-12 nav nav-tabs bg-dark" role="tablist"  id="ew-Head" ew-detail="'.$value->code.'">
									<a data-toggle="collapse" href="#tab'.$i.'"  class="text-aqua collapsed" data-parent="#accordion" aria-expanded="false">
										<i class="fas fa-bars"></i> '.Yii::t('common',$value->description).'
									</a>
								</div>';
						$div.= '<div id="tab'.$i.'" class="panel-collapse collapse in" aria-expanded="false" >';
						$div.= getChild($value->id,$i,$value->name, NULL);
						$div.= "</div>";
						$div.= '<input type="hidden" class="form-control" name="'.$value->name.'-Char" id="ewGenChar">';
						
						// $div.= '<div class="row">
						// 			<div class="col-xs-12">
						// 				<hr class="style10" >
						// 			</div>
						// 		</div>';
					}
				}

				echo $div;

 


				function getChild($bom,$i,$name, $disabled)
				{
					$BomLine = KitbomLine::find()
					->where(['kitbom_no' => $bom])
					->orderBy(['name' => SORT_ASC])
					->all();
					$div = '';
					foreach ($BomLine as $line) {
						//$items 		= Items::find()->where(['No' =>$line->item_no])->one();
						//$Query 		= WarehouseMoving::find()->where(['ItemNo' => $items->No]);
						//$RealInven 	= $Query->sum('Quantity');
						//$Remaining 	= $items->Inventory + $RealInven;	 
						$Yii        = 'Yii';
						//$Remaining  = number_format($Remaining);
						$cost 		= $line->items 
										? $line->items->lastPrice * 1
										: 0;

						$Remaining  = $line->items 
										? ($line->items->ProductionBom > 0 //$line->items->invenByBom
											? $line->items->invenByBom
											: $line->items->last_stock) //$line->items->invenByBom
										: 0;

						$Remaining 	= $Remaining > 0 
										? $Remaining
										: 0;

						$showRemain = number_format($Remaining);
$div.=<<<HTML
						<div class="col-md-2 col-sm-3 col-xs-6">
							<div id="{$line->id}" class="small-box  ew-box-click" style="padding:5px; margin-left: -18px;margin-right: -8px; border:1px solid #f9f1f1; background-color: #fff;">
								<i class="fas fa-power-off" style="color: {$line->color_style}; "></i> 	
								<label style="font-size:19px;" class="text-info">{$line->name}</label>								 
								
								
								<div class="input-group" >
									<span class="input-group-btn">
										<button {$disabled} type="button" class="btn btn-number btn-default" data-type="minus" data-field="{$name}[]"  data-rippleria>
											<span class="glyphicon glyphicon-minus"></span>
										</button>
									</span>
									<input type="number" step=any class="form-control input-number text-center ewInput-txt"
										ew-count="{$i}"
										ew-name="{$line->name}"
										id="ewInput-txt"
										name="{$name}[]"
										value="{$line->quantity}"
										ew-xcode="{$line->item_no}"  
										data-key="{$line->item}"
										min="0" 
										max="10"
										{$disabled}
										>
									<span class="input-group-btn">
										<button {$disabled} type="button" class="btn btn-number btn-default" data-type="plus" data-field="{$name}[]"  data-rippleria>
											<span class="glyphicon glyphicon-plus"></span>
										</button>
									</span>
								</div>
								<div style="font-size:16px; margin-bottom:7px;" class="hidden">{$Yii::t('common','Price')} : {$cost}</div>
								<br />
								<div class="text-small">	{$Yii::t('common','Remain')} :	<span class="remain" data-val="{$Remaining}">{$showRemain}</span></div>
							</div>
						</div>

HTML;
					}

					return $div;
				}

			?>
		
			
		</div>
		<div class="col-lg-12">
			<div class="itemset-view">
				<br>
				<div class="text-left text-orange">
					<div class="text-left ew-set-name hidden"><?=$model->name?> </div>
					<?php if($model->photo=='') $model->photo = 'images/icon/production-.png'; ?>
					<img class="img-thumbnail ew-main-photo hidden" src="<?=$model->photo?>" alt="">
					<div class="text-left ew-Validate-" id="ew-real-desc" style="font-size: 20px;"></div>
					<div class="text-left ew-Validate text-danger" data-key="" data-id=""></div>
				</div>
				<br>
				<div class="text-left ew-Code"></div>
			</div>
		</div>
		<div class="hidden">
			<div class="text-center ew-Validate-" style="font-size: 16px; font-weight: bold;"></div>
			<div class="text-center ew-Remaining" data='0'> </div>
		</div>

		<div class="row"><hr class="style10"></div>
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-xs-6">
				<label><?=Yii::t('common','Quantity') ?></label>
				<div class="input-group">
					<input type="number" step=any class="form-control text-right ew-to-line" id="ew-to-line" name="Quantity" value="1" />
					<span class="input-group-addon"><?=Yii::t('common','Set')?></span>
				</div>
			</div>
			<div class="col-xs-6">
				<label><?=Yii::t('common','Price') ?></label>
				<div class="input-group">
					<input type="number" step=any class="form-control text-right ew-to-line-price" id="ew-to-line-price" name="Price" />
					<span class="input-group-addon"><?=Yii::t('common','Baht')?></span>
				</div>
			</div>
			<!-- <div class="col-xs-4">
				<label><?=Yii::t('common','Discount') ?></label>
				<div class="input-group">
					<input type="text" class="form-control" id="ew-to-line" name="Discount">
					<span class="input-group-addon"><?=Yii::t('common','Baht')?></span>
				</div>

			</div> -->
		</div>


		<div class="row" style="margin-bottom: 20px;">

		<div class="ew-create-bom"></div>
		</div>
	</div>
</div>
<div class="row">

<?php //if(!Yii::$app->request->isAjax)  $this->registerJsFile('js/manufacturing/item_set.js?v=3.03.21');?>
