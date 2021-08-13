<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use \yii\helpers\ArrayHelper;

use common\models\FormMapNumberSeries;

// common\models\FormMapNumberSeries::find()->asArray()->all();
function seriesGroup() {
	$options = [];	 
	// $parents = FormMapNumberSeries::find()->where(['child' => 0])->all();
	// foreach($parents as $id => $p) {
	// 	$children = FormMapNumberSeries::find()->where(['child' => $p->id])->all();
	// 	$child_options = [];
	// 	foreach($children as $child) {
	// 		$child_options[$child->id] = $child->name;
	// 	}
	// 	$options[$p->name] = $child_options;
	// }
	return $options;
}
 
?>
 <style>
 	.adv{
 		background-color: #fff;
	    border-radius: 3px;
	    color: #006621;
	    display: inline-block;
	    font-size: 11px;
	    border: 1px solid #006621;
	    padding: 1px 3px 0 2px;
	    line-height: 11px;
	    vertical-align: baseline;
	    margin:5px 5px 0 0;
 	}
 	/*.tb-series thead, tbody { display: block; }
 	.tb-series tbody {
	    height: 400px;        
	    overflow-y: auto;     
	    overflow-x: hidden;  
	}*/
	#last_no{

	}

 </style>



<div class="">
	<div class="row hidden" style="padding:10px;"> 
		<div class="col-xs-5" style="margin:5px 0px 10px 0px;">Form Name : 
			<input type="text" name="form_name" disabled value="Credit Note" class="form-control" />
		</div>
		<div class="col-xs-5" style="margin:5px 0px 10px 0px;">Number Series : 
			<?= Html::dropDownList('number_series_code',['value' => $group],seriesGroup(),
			['class' => 'form-control','id' => 'number-series-code']
			);?>
		</div>	 
		<div class="col-xs-2">
			<label>Save</label>
			<div><?= Html::button('<i class="far fa-save"></i> ' .Yii::t('common','Save & Close'),['class' => 'btn btn-primary-ew btn-flat save-close'])?></div>
		</div>
	</div>
<?php

	$bgTr 	= ' ';
	$i 		= 0;

	$table = '<table class="table tb-series">'; 
	$table.= '<thead class="bg-dark"><tr>';
	$table.= '	<th class="text-center">'.Yii::t('common','Month').'</th>';
	$table.= '	<th>'.Yii::t('common','Start Date').'</th>';
	$table.= '	<th>'.Yii::t('common','No Series Format').'</th>';
	$table.= '	<th>'.Yii::t('common','Last Series').'</th>';
	$table.= '</tr></thead>';

	$table.= '<tbody>';
	foreach ($model as $value) {
		
		$i++;

	 
			if(date('m',strtotime($value->start_date)) == date('m') ):
				$bgTr = 'bg-success';
			else:
				$bgTr = ' ';
			endif;
			 

		$table.= '<tr class="'.$bgTr.'">';
		$table.= '	<td class="text-center bg-success">'.$i.' '.(
						(date('m',strtotime($value->start_date)) == date('m') ) 
							? '<i class="fas fa-caret-right pull-right text-red blink" style="font-size: 22px; margin: 4px 0px 0px -7px;"></i> ' 
							: '').'
					</td>';
		$table.= '	<td><input class="no-se form-control font-roboto" type="date" value="'.$value->start_date .'" name="start_date" id="start_date" data="'.$value->id .'"></td>';
		$table.= '	<td><input class="no-se form-control font-roboto" type="text" value="'.$value->start_no .'"  name="start_no" id="start_no" data="'.$value->id .'"></td>';
		$table.= '	<td><input class="no-se form-control font-roboto" type="text" value="'.$value->last_no .'"  name="last_no" id="last_no" data="'.$value->id .'" autocomplete="off"></td>';
		$table.= '</tr>'; 
	}
	$table.= '<tr>
				<td colspan="4" class="text-right">
					<button type="button" data="'.$id.'" name="CLEAR" class="CLEAR-DATA btn btn-danger"><i class="fas fa-brush"></i> '.Yii::t('common','​Clean').'</button>
				</td>
			</tr>';
	$table.= '</tbody>';
	$table.= '</table>';
	 


	if(!empty($model)) { 

		echo $table;
		echo "<script>$('.resource').hide();</script>";

	}else {

		echo '<div class="col-md-12 text-center" >
				 <button type="button" name="GenSeries" class="GenSeries btn btn-info"><i class="fa fa-play" aria-hidden="true"></i> '.Yii::t('common','Auto Generate').'</button>
			  </div><br>';

	}


?>
 
</div>

<?php

	$NoSeries   = \common\models\NumberSeries::findOne($id);

	function getSeprate($model){
		$data = '';

		switch ($model->format_type) {
			case '12M':

				for ($i=1; $i < 13; $i++) { 
					$data .= '<div>'.Separator($model,sprintf("%02d",$i)).'</div>';
				}
				break;
			
			case 'ONCE':
				$data .= '<div>'.Separator($model,'').'</div>';
				break;

			default:
				$data .= '<div>'.Separator($model,'').'</div>';
				break;
		}
		 

		return $data;
	}
	 
	function Separator($model,$runing){

		switch ($model->separate) {

			case 'YY':
				return $model->starting_char.date('y').$model->format_gen;
				break;

			case 'YY-':
				return $model->starting_char.date('y').'-'.$model->format_gen;
				break;

			case 'YYTH':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).$model->format_gen;
				break;

			case 'YYTH-':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$model->format_gen;
				break;

			case 'YYMM':
				return $model->starting_char.date('y').$runing.$model->format_gen;
				break;
			
			case 'YYMM-TH':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.$model->format_gen;
				break;

			case 'YY-MM-TH':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$runing.$model->format_gen;
				break;

			case 'YY-MM-TH-':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).'-'.$runing.'-'.$model->format_gen;
				break;

			case 'YYMM-':
				return $model->starting_char.date('y').$runing.'-'.$model->format_gen;
				break;

			case 'YY-MM':
				return $model->starting_char.date('y').'-'.$runing.$model->format_gen;
				break;	

			case 'YY-MM-':
				return $model->starting_char.date('y').'-'.$runing.'-'.$model->format_gen;
				break;	
			
			case 'YYMM-TH-':
				return $model->starting_char.date('y',strtotime(date('Y')+543)).$runing.'-'.$model->format_gen;
				break;

			default:
				return $model->starting_char.$model->separate.$model->format_gen;
				break;
		}

	}
?> 

<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10">
		<div class="resource">
			<div class="row text-center" style="margin-top: 20px;">
				<div class="row">
					<div style="border: 1px solid #ccc; height: 100%; width: 100%;" >
						<div class="text-left margin">
							<p id="digit" data="<?=$NoSeries->format_gen?>" char="<?=$code;?>" no-series="<?=$id;?>" >
								RUNING : <?=Html::a('<i class="far fa-edit"></i> '. Yii::t('common','Edit'),['/series/update','id'=>$id],['class' => ' ','target' => '_blank'])?> 
								<div class="well font-roboto"> <?=getSeprate($NoSeries)?> </div>
							</p>
							<p> LOOP : <?=$NoSeries->format_type;?></p>
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-1"></div>
</div>