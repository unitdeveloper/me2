<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Items */

$this->title = Yii::t('common', 'Create Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
 
<div class="row" ng-init="Title='<?=Yii::t('common','Product')?>'">	
	<div class="col-xs-12">
		<?= $this->render('_explorer_bar',['model' => $model]) ?>
	</div> 
	<div class="col-sm-12">
		<div class="items-create">		     
		    <?= $this->render('_form', [
		        'model' => $model,
		        'Proper' => $Proper,
		    ]) ?>
		</div>
	</div>
</div>
<?= $this->render('_script_js') ?>
<?php

$Yii 		= 'Yii';
$Country 	= '213';
$js=<<<JS

	$(document).ready(function(){
		createItemcode($('.ew-selected').attr('ew-radio-data'));
	})
	
	$('body').on('click','.ew-href',function(){
		var company = $('input[name="company"]').val();
		$('input#master_code').val({$Country}+'-'+company+'-'+$(this).attr('ew-data')).attr('data','885-'+company+'-'+$(this).attr('ew-data'));
	})

	$('body').on('click','.ew-selected',function(){
		createItemcode($(this).attr('ew-radio-data'));
	});

	function createItemcode(curCode){
		var oldCode = $('input#master_code').attr('data');

		if(oldCode==null) oldCode = {$Country}+'-'+$('#company').val()+'-73';
		if(curCode==null) curCode = '79';
 
		var newCode = oldCode+'-'+curCode;
		var code 	= $('#company').val()+'73'+curCode;
		$.ajax({
			url: '?r=items/ajax/auto-master-code&code='+newCode,
			data:{code:newCode,data:code},
			type: 'POST',
			dataType:'JSON',
			success:function(response){
				$('input#items-barcode').val(response.value.barcode);
				$('input#master_code').val(response.value.newcode).attr('data-org',response.value.newcode);
				$('input#items-description_th').select();
			}
		});
	}
JS;

$this->registerJS($js,\yii\web\View::POS_END);
