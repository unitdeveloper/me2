<?php
use common\models\Itemgroup;
use admin\models\FunctionCenter;

$Fn = new FunctionCenter();
?>
<style type="text/css">
 
	 
	.ew-menubox   {
		background-color: #fff;
		cursor: pointer;
		border:1px solid #ccc;		
		/*margin:2px 4px 2px 0;*/
		padding: 10px;

	}

	.ew-menubox:hover {
	    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}

	.menu-explorer-bar{
		position:relative;
	}

	.ew-itemgroup {
		margin-top:20px;
	}
	 

	.menu-itemgroup{
		position:absolute;
		width:130px;
		height:30px;
		background-color:#fdfdfd;
		z-index:50;
		top:-17px;
		right:15px;
		padding:5px 5px 5px 10px;
		border-left:1px solid #ccc;
		border-right:1px solid #ccc;
		border-bottom:1px solid #ccc;
		box-shadow: 2px 3px 2px rgba(0,0,0,.1);
	}
</style>

<div class="row menu-explorer-bar <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>">
	<a href="javascript:void(0)" class="menu-itemgroup" data-toggle="collapse" data-target="#itemgrous-list"><i class="fas fa-caret-square-down"></i> <?=Yii::t('common','Item Group')?></a>
	<div class="col-md-12 ew-itemgroup collapse " id="itemgrous-list">
		<?php
		$ItemGroup = Itemgroup::find()
					->where(['Child' => 0])
					->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
					->andWhere(['Status' => 1])
					->all();
		$div = null;
		if($ItemGroup != null){
			foreach ($ItemGroup as $value) {
				$div.= '<a href="#'.Yii::t('common',$value->Description_th).'" class="ew-href col-sm-2 no-padding" ew-data="'.$value->GroupID.'" style="float:left;">';
					$div.= '<div class="ew-menubox ">';
					$div.= '<img src="'.Yii::getAlias('@web').'/'.$value->photo.'"  width="50px"  >';
					$div.= Yii::t('common',$value->Description_th);
					$div.= '</div>';
				$div.= '</a>';
			}
		}
		echo $div;
		?>
	</div>
</div>


<div class="ew-menu-itemgroup <?=(Yii::$app->user->identity->id ==1 ? ' ' : ($model->id == 1414 ? 'hidden' : ''))?>">
	<?= $Fn->ItemGroupChild();?>
</div>