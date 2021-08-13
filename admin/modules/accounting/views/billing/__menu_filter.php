<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;


use common\models\Customer;
use common\models\Company;
use common\models\SalesPeople;

use kartik\widgets\Select2;

$Actions = Yii::$app->controller->action->actionMethod; 

//$GenSeries		= new \admin\models\Generater();
//$NoSeries		= $GenSeries->NextRuning('billing_note','vat_type','0',false); 
$NoSeries 		= date('Ym');

$DocumentNo 	= Yii::$app->session->get('billingNo');

$Cust 			= ['address' => NULL,'code' => NULL,'name' => NULL,'no_' => NULL];

$custId 		= 0; 

$delId 			= '';

$comp       	= \common\models\Company::find()
				->where(['id' => Yii::$app->session->get('Rules')['comp_id']])
				->one();

$TEXT_COMMENT 	= 'ได้รับบิลเงินเชื่อหรือเงินสดไว้ เพื่อตรวจสอบและพร้อมจะชำระเงินให้ตามบิลดังต่อไปนี';
$TEXT_REMARK 	= '';
$TEXT_LECTURE 	= 'ในนาม '.$comp->name;


if(isset($_GET['customer'])){
	if($_GET['customer'] != '') $custId = $_GET['customer'];
}


if($Actions!='actionCreate'){
	 
	foreach ($dataProvider->models as $key => $model) {
		 
		// Update 
		$DocumentNo 	= $model->no_;

		$custId 		= $model->cust_no_;

		$TEXT_COMMENT 	= $model->text_comment; 
		$TEXT_REMARK 	= $model->text_remark;
		$TEXT_LECTURE 	= $model->text_lecture;
		
		if(!isset($_GET['fdate'])){
			$delId = base64_encode($model->no_);
			// Search in update page.
		 	if(@$_GET['getView'] == 'false'){
				$DocumentNo = substr($NoSeries, 0,-3).'XXX';
			}
		} 


	}

	

	$Cust = Customer::findOne($custId);

}else {
	 
	$DocumentNo = substr($NoSeries, 0,-3).'vvv';


}

 

//$LastDay    = date('t',strtotime(date('Y-m-d')));
$startDate  = date('Y-m-d');
$endDate    = date('Y-m-d');
?>
<style type="text/css">
	    .select2-selection{
        	height: 34px !important;

        }
        .select2-container--krajee .select2-selection--single .select2-selection__placeholder {
		    color: #999;
		     
		}

		.select2-container .select2-selection--single .select2-selection__rendered {
		     
 
		    padding-top: 5px;
		     
		}

	 

		.input-group-addon{
			background-color: rgb(249,249,249) !important;
			border: 1px solid #999 !important;

		}


		.menu-right-slide{
			position: absolute;
			right: 0px;
			border:1px solid #ccc;
			background-color: rgb(253,253,253);
			border-bottom-left-radius: 5px;
			/*box-shadow: 5px 5px 5px #ccc;*/
			z-index: 60;
			display: none;
			padding: 10px;
			min-width:20%;
			
			/*outline: 1px solid #ccc;*/
		}
		.menu-right-slide .custom-menu{
			min-width:200px;
			height: 100%;

		}

		.menu-left-click{
			position: absolute;
			margin:5px 5px 0 0;
			right: 0px;
			z-index: 50;
			display: none;
		} 

		.btn-post{
			
			
		}
</style>

 

<?php if(!Yii::$app->request->isAjax) : ?>
 
<div class="row btn-print bg-gray" style=" margin-top: -15px;">
	<div class="col-md-12 <?=$Actions=='actionUpdate' ? 'hidden' : ''?> col-xs-12" style="margin-bottom: 10px; padding-top: 15px;">
 
        
        <?php $form = ActiveForm::begin(['id' => 'invline-search','method' => 'GET']); ?>
        <div class=" " >

           

            

            <div class="col-sm-4 col-xs-12">  
				<label><?=Yii::t('common','Date Filter')?></label>

              <?php              

              if(isset($_GET['fdate']))    
              {
                if($_GET['fdate']!='') $startDate   = date('Y-m-d',strtotime($_GET['fdate']));
              }


              if(isset($_GET['tdate'])){      
                if($_GET['tdate']!='') $endDate     = date('Y-m-d',strtotime($_GET['tdate']));

              }


$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
	<span class="input-group-addon">$FromDate</span>
	{input1}
	{separator} 
	<span class="input-group-addon">$ToDate</span>
	{input2}
	<span class="input-group-addon kv-date-remove">
	    <i class="glyphicon glyphicon-remove"></i>
	</span>
HTML;


              echo DatePicker::widget([
              		'type' => DatePicker::TYPE_RANGE,
					'name' => 'fdate',
					'value' => $startDate,					
					'name2' => 'tdate',
					'value2' => $endDate,                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
					'layout' => $layout,
					'pluginOptions' => [
						'autoclose'=>true,
						'format' => 'yyyy-mm-dd'
						//'format' => 'dd-mm-yyyy'
					],
					 
              ]);

              ?>
            </div>
			<div class="col-sm-2 col-xs-4"> 
               
	            <div class="input-group" >
                <label><?=Yii::t('common','Sales')?></label>
	                <?= Html::dropDownList('search-from-sale', null,
	                    					ArrayHelper::map(
															SalesPeople::find()
															->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id'],'status'=> 1])
															->orderBy(['code' => SORT_ASC])
															->all(),
				                                            	'id',function($model){
				                                                return '['.$model->code.'] '.$model->name. ' '.$model->surname;
				                                            	}
				                                            ),
					                    					[
					                    						'class'=>'form-control',
					                    						'prompt' => Yii::t('common','Every one'),
					                    						'options' => [                        
																	@$_GET['search-from-sale'] => ['selected' => 'selected']
																  ],
					                    					] 
			                                             
	                						) 
	                ?>
	               
	            </div>
               
            </div>

            <div class="col-md-2  col-xs-6"> 

            	
	            <div class="input-group" >
					<label><?=Yii::t('common','Vat Type')?></label>

					 
	                <?php
	                echo Html::dropDownList('searchVat', null,
	                    					ArrayHelper::map(
				                    						\common\models\VatType::find()->orderBy(['name'=> SORT_ASC])->all(),
				                                            	'vat_value','name'
				                                            ),
					                    					[
					                    						'class'=>'form-control  col-xs-12',
					                    						'prompt' => Yii::t('common','All'),
					                    						'options' => [                        
																	@$_GET['searchVat'] => ['selected' => 'selected']
																  ],
					                    					] 
					                    					 
			                                             
	                						)  
	                ?>
						

					 
	            </div>
               
            </div>


            <div class="col-md-3  col-xs-6">
             
					<label><?=Yii::t('common','Customers')?></label>
	                 

	                <?php 

	                $search_cust = '';

	                if(isset($_GET['customer'])){
	                	if($_GET['customer'] != '') $search_cust = $_GET['customer'];
	                }

	                $query = \common\models\RcInvoiceHeader::find()->select('cust_no_')->indexBy('cust_no_')->all();
	                $custIncase = array();
	                foreach ($query as $key => $value) {
	                	$custIncase[] = $value->cust_no_;
	                }

	               



	                if($Actions=='actionUpdate'){


	                	 
	                	echo '<select name="customer" class="form-control" >
	                				<option value="'.$Cust->id.'">'.$Cust->name.'</option>
	                			</select>';

	                }else {

	                	if(isset($_GET['getView'])){

	                		if($_GET['getView']=='true'){

	                			$getCust = Customer::findOne($_GET['customer']);

	                			echo '<select name="customer" class="form-control" >
										<option value="'.$getCust->id.'">'.$getCust->name.'</option>
									</select>';

	                		}else {

			                	echo Select2::widget([
								    'name' => 'customer',
								    'data' => ArrayHelper::map(
					                    						Customer::find()
																->where(['id' => $custIncase])
																->andWhere(['or',['id' => 909],['comp_id' => Yii::$app->session->get('Rules')['comp_id']]])
					                    						->orderBy(['code' => SORT_ASC])
					                    						->all(),
					                                            	'id',function($model){ return '['.$model->code.'] '.$model->name; }
					                                            ),
								    'options' => [
								        'placeholder' => Yii::t('common','Customer'),
								        'multiple' => false,
										'class'=>'form-control  col-xs-12',
									],
									'pluginOptions' => ['allowClear' => true],
									'value' => @$_GET['customer']
								]);
							}
		                }else {
		                	echo Select2::widget([
								    'name' => 'customer',
								    'data' => ArrayHelper::map(
					                    						Customer::find()
																->where(['id' => $custIncase])
																->andWhere(['or',['id' => 909],['comp_id' => Yii::$app->session->get('Rules')['comp_id']]])
					                    						->orderBy(['code' => SORT_ASC])
					                    						->all(),
					                                            	'id',function($model){ return '['.$model->code.'] '.$model->name; }
					                                            ),
								    'options' => [
								        'placeholder' => Yii::t('common','Customer'),
								        'multiple' => false,
										'class'=>'form-control  col-xs-12',
									],
									'pluginOptions' => ['allowClear' => true],
									'value' => @$_GET['customer']
								]);
		                }
		            }

		                
	                

					?>
	               
	             
            </div>
            <div class="col-md-1  col-xs-6">  
             	<div class="input-group" >
	             	<label  style="color: #fff"> Search <input type="hidden" name="getView" value="false"> </label> <br>

	            	<button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> Search</button>

            	</div>
            </div>

           

        </div><!-- /.row -->

         <?php ActiveForm::end(); ?>

 


	</div>
	
</div>
 		

<?php endif; ?>


<div class="menu-left-click">
	<div class="menu-buttun-click"><button class="btn btn-info btn-flat menu-buttun-click-on"><i class="fa fa-arrow-left"></i></button></div>
</div>





<div class="menu-right-slide">
	<div class="custom-menu">
		 

		<div class="row" style="margin-bottom: 15px;">
			<div class="col-xs-12">
				<button class="btn btn-default-ew menu-right-keep"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
				<span class="custom-menu-print">
					<button type="button" id="print-billing" class="btn btn-info-ew pull-right"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
				</span>

				<!-- <span class="custom-menu-new">
					<a href="index.php?r=accounting/billing/create" class="btn btn-warning-ew"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
				</span> -->

				<?php if($Actions!='actionUpdate') : ?>			 
					<button type="button" class="btn btn-warning   cancel-billing"><i class="fa fa-ban"></i> <?=Yii::t('common','Cancel');?></button>	 
					<button  type="button" class="btn btn-success btn-post pull-right save-billing"><i class="fa fa-save blink"></i> <?=Yii::t('common','Confirm');?></button>				 
						
				<?php endif; ?>
			</div>
		</div>

		


		<div class="custom-menu-content">
		 
			<div class="row">
				<div class="col-xs-4">
					<h5><b>เลขที่ใบวางบิล</b></h5>
				</div>
				
				<div class="col-xs-8 ">
					<div class="input-group margin-bottom" style="max-width: 150px;">
						<span class="btn btn-info input-group-addon">
							<i class="fa fa-file-o" aria-hidden="true"></i></span>
						<input type="text" id="no" class="form-control  text-right bill_no" value="<?=$DocumentNo?>"    >
						
					</div>
					
				</div>
			</div>

			<!-- Text -->
			<div class="row">
				<div class="col-xs-12 ">
					<span class="link"  data-toggle="collapse" data-target="#Text"><i class="fa fa-text-width" aria-hidden="true"></i> Text Detail</span>
				</div>
			</div>

			<div id="Text" class="collapse in">
				<div class="row">
					 
					<div class="col-xs-12">
						<label><?=Yii::t('common','Comment')?> : </label> 
						<textarea class="form-control print-comment" rows="4" placeholder="<?=Yii::t('common','Comment')?> ..."><?=$TEXT_COMMENT?></textarea>
						 
						
					</div>

					
				</div>
				<div class="row" style="margin-top:5px;">
					<div class="col-xs-12">
						<label><?=Yii::t('common','Remark')?> : </label>
						<textarea class="form-control print-remark" placeholder="<?=Yii::t('common','Remark')?> ..." ><?=$TEXT_REMARK?></textarea>
						 
						
					</div>
				</div>
				<div class="row" style="margin-top:5px;">
					<div class="col-xs-12">
						<label><?=Yii::t('common','Lecture')?> : </label>
						<textarea class="form-control print-lecture" placeholder="<?=Yii::t('common','Lecture')?> ..." ><?=$TEXT_LECTURE?></textarea>
						 
						
					</div>
				</div>



			</div>
			<hr>


			<div class="row">
				<div class="col-xs-12 ">
					<span class="link" data-toggle="collapse" data-target="#Papers"><i class="fa fa-print" aria-hidden="true"></i> Paper</span>
				</div>
				
			</div>
			
			<div id="Papers" class="collapse">

				<!-- Paper Options -->
				<div class="row margin-bottom">
					<div class="col-xs-4">
						Font
					</div>
					
					<div class="col-xs-3" style="padding: 0px;">
						<select class="form-control" id="font-size">
							<option value="10">10px</option>
							<option value="11">11px</option>
							<option value="12">12px</option>
							<option value="13">13px</option>
							<option value="14" selected="selected">14px</option>
							<option value="15">15px</option>
							<option value="16">16px</option>
							<option value="17">17px</option>
							<option value="18">18px</option>
							<option value="19">19px</option>
							<option value="20">20px</option>
							<option value="21">21px</option>
							<option value="22">22px</option>
						</select>
					</div>	
					<div class="col-xs-5">
						<select class="form-control" id="font-style">
							<option value="saraban" selected="selected">Thai Saraban</option>
							<option value="freesiaupc">Freesia UPC</option>
							<option value="thaimono" >Thai Mono</option>
							<option value="tahoma">Tahoma</option>

						</select>
					</div>		
				</div>

				<div class="row">
					<div class="col-xs-4">
						Paper
					</div>
					<div class="col-xs-3" style="padding: 0px;" >
						<select class="form-control" id="paper-size">
							<option value="A4">A4</option>
							<option value="9x11" selected="selected">9x11</option>							
						</select>
					</div>
					<div class="col-xs-5">
						<select class="form-control alternate-paper">
							<option value="L"  >Landscape</option>
							<option value="P" selected="selected">Portrait</option>
						</select>
					</div>
				</div>

				<?php if($Actions=='actionUpdate') : ?>

				<div class="row margin-top">
					 
					<div class="col-xs-12 text-right">
						<?php 

						
					
						
						echo Html::a('<i class="fa fa-trash" aria-hidden="true"></i> '.Yii::t('common', 'Delete'), ['delete', 'no' => $delId], [
					            'class' => 'btn btn-danger',
					            'data' => [
					                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
					                'method' => 'post',
					            ],
					        ]); 
					 
					    ?>
					      
					</div>					
				</div>
					
				<?php endif; ?>


				<!-- /.Paper Options -->
			</div>
			<hr>


			<?php if($Actions=='actionUpdate') : ?>
				
				<div class="chat-tracking" data-text="BillingNote" data="<?=$delId?>"></div>
				<hr>
			<?php endif; ?>

			<style>
			.menu-remark  ul li{
				list-style-type: none; 
				margin-left: -40px; 
				min-height: 25px;
			}
			.menu-remark  ul li .text{
				border:1px solid #ccc;
			}
			.menu-remark  .remark-img{				
				width:250px;
				height:166px;
				background-image:url('images/manual/manual-bill.png');
				background-size:250px 166px;
			}
			</style>
			
			<div class="row">
				<div class="col-xs-12 ">
					<span class="link" data-toggle="collapse" data-target="#Explanation"><i class="far fa-question-circle"></i> <?=Yii::t('common','Explanation')?></span>
				</div>
				
			</div>
			
			<div id="Explanation" class="collapse in">
				<div class="row" >
					<div class="col-sm-12">
						<div class="" style="margin-top: 15px;">
							<div class="menu-remark" style="padding: 5px;">							
								<div class="remark-img"></div>								
								<ul style="margin-top:5px;">
									<li><span class="col-xs-4  text text-center" > <?=Yii::t('common','Wite')?> </span><span class="col-xs-8"> <?=Yii::t('common','Not yet paid')?> </span></li>		
									<li><span class="bg-red col-xs-4  text text-center "> <?=Yii::t('common','Red')?> </span><span class="col-xs-8"> <?=Yii::t('common','Billed create')?> </span></li>						
									<li><span class="bg-orange col-xs-4  text text-center "> <?=Yii::t('common','Orange')?> </span><span class="col-xs-8"> <?=Yii::t('common','Payment notice')?> </span></li>
									<li><span class="bg-success col-xs-4  text text-center"> <?=Yii::t('common','Green')?> </span><span class="col-xs-8"> <?=Yii::t('common','Approved payment')?> </span></li> 
								</ul>
							</div>
						</div>			
					</div>
				</div>
			</div>
			
			 
								
			

		</div>
		<!-- /.custom-menu-content -->
 
	</div>
	<!-- /.custom-menu -->
</div>
<!-- /.menu-right-slide -->
<?php $this->registerJsFile('js/_chat_module.js?v=6.07.02');?>

<?php $this->registerJsFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);?>

<?php

$js =<<<JS
	$(document).click(function(e){
		if(e.target.id != 'edit-comment') {
			$('.text-comment').html($('.text-comment').data('text'));			 
			$('.text-comment').addClass('edit-comment'); 
			$('#print-billing').attr('disabled',false);
		}
		
	})


	$('body').on('change paste','.print-comment',function(){
		
		updatePosition('text_comment',$(this).val(),'.text-comment');
		$('.text-comment').addClass('edit-comment'); 
		
	});

	$('body').on('change paste','.print-remark',function(){

		updatePosition('text_remark',$(this).val(),'.text-remark');
		 
	});


	$('body').on('change paste','.print-lecture',function(){

		updatePosition('text_lecture',$(this).val(),'.text-lecture');
		 
	});


	function updatePosition(field,data,destination){
		$.ajax({
			url:'index.php?r=accounting/billing/ajax-update&id='+$('.chat-tracking').attr('data')+'&field='+field+'&data='+data,
			type:'POST',
			async:true,
			success:function(getData){

				$(destination).html(getData).data('text',getData);
				$('#print-billing').attr('disabled',false);

			}
		})
	}

	$("#font-size").change(function() {
		 
		 $('.body-print').css("font-size", $(this).val() + "px");
 
	 });

	$("#font-style").change(function() {
		 
		$('.body-print').css("font-family", $(this).val());

	});

	$("#paper-size").change(function() {
		 if($(this).val()=='9x11'){
			$('.body-print').css("width", '203mm');
			$('.body-print').css("height", '270mm');
		 }else if($(this).val()=='A4'){
			$('.body-print').css("width", '203mm');
			$('.body-print').css("height", '279mm');
		 }
		 
 
	});



	$('body').on('click','.cancel-billing',function(){
				
		var url = 'index.php?r=accounting%2Fbilling%2Fcreate';
		var data = '&fdate={$startDate}&tdate={$endDate}&search-from-sale=&searchVat=&customer='+$('.customer-billing').attr('data')+'&getView=false';
		window.location.href = url+data;
	});


	$('body').on('change', '.alternate-paper', function(){
		var portrait = (window.orientation % 180 == 0);
		if($(this).val()=='L'){			
			$("body .body-print").css("-webkit-transform", !portrait ? "rotate(-90deg)" : "");
		}else{
			$("body .body-print").css("-webkit-transform", !portrait ? "rotate(0deg)" : "");
		}
		
	});
	
JS;
//$this->registerJS($js);
$this->registerJs($js,\yii\web\View::POS_END);
?>
 
 
