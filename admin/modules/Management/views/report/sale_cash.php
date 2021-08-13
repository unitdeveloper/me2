<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;

use common\models\Customer;
use common\models\Company;
use common\models\SalesPeople;
use common\models\SaleHeader;
use common\models\RcInvoiceLine;
use admin\modules\accounting\models\FunctionAccounting;
use admin\modules\Management\models\FunctionManagement;
use common\models\Cheque;

use kartik\grid\GridView;
use kartik\export\ExportMenu;

use kartik\widgets\Select2;

$comp 		= Company::find()->where(['id' => Yii::$app->session->get('Rules')['comp_id']])->one();
$sales 		= SalesPeople::find()->where(['code' => isset($_GET['search-from-sale']) ? $_GET['search-from-sale'] : 0])->one();
$Customer 	= Customer::findOne(isset($_GET['customer']) ? $_GET['customer'] : 0);
 

$subStr         = 100;

// Set line amount per page.
$PerPage        = 15;

if(isset($_GET['substr']))      $subStr     = $_GET['substr'];
 
$gridColumns = [ 

];



function DateThai($strDate)
{
	$strYear = date("Y",strtotime($strDate))+543;
	$strMonth= date("n",strtotime($strDate));
	$strDay= date("j",strtotime($strDate));
	$strHour= date("H",strtotime($strDate));
	$strMinute= date("i",strtotime($strDate));
	$strSeconds= date("s",strtotime($strDate));
	$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$strMonthThai=$strMonthCut[$strMonth];
	//return "$strDay $strMonthThai $strYear, $strHour:$strMinute";
	return "$strDay $strMonthThai $strYear";
}
 



?>
<style type="text/css">
		@media print {
			.btn-print{
				display: none;
			}
			.body-table .body-print{

				padding:0px !important;
				margin:0px !important;

				background-color: #000;

				width: 29.7cm;

				font-size: 11px;
				
        	}
        	 

		}

		.main-header{
			background-color: #eeefef;
		}


         
        .body-table{
                background-color: rgb(100,100,100);
                
                position: relative;
				
                z-index: 5 !important;
        }

        .body-table .overflow{
        	  
        	overflow: auto;
			 
        }

        .body-table .body-print{

				margin:30px 30px 30px 30px;

				background-color: #fff;

				box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);

				padding:5px;

				width: 29.7cm; 

				font-size: 11px;

				min-height: 580px;
				/* width:794px; height:1122px , width:21cm; height:29.7cm A4*/

				
        }

		.profile-header {

			padding-left: 5px;
			 
		}

        .profile-header div div{
        	height: 20px;
        	
        }






        /* ################## CONTENT #################### */
		.table-header{
			padding:5px 0 5px 0;
			border-top: 1px dashed #000;
            border-bottom: 1px  dashed  #000;
            height: 50px;
		}
 
        .tb-head tr td {
        	 
        }

		.footer-value{
            

        }
        .footer-value div{
            
            border-top: 1px dashed blue;
            border-bottom: 1px  dashed blue;

            padding: 3px;
            width: 90%;

        }
        .footer-value p{
             
            border-bottom: 1px  dashed blue;
            margin:0 -3px 0 -3px;

            padding: 6px 0 6px 0;

            text-align: right;

        }

        .select2-selection{
        	height: 34px !important;

        }
        .select2-container--krajee .select2-selection--single .select2-selection__placeholder {
		    color: #999;
		     
		}

		.select2-container .select2-selection--single .select2-selection__rendered {
		     
 
		    padding-top: 5px;
		     
		}

		.F3{ /* Total */
			color: blue;
			font-weight: 900;
		}

		.F4{ /* Over Pay */
			color: red;
			font-weight: 900;
		}

		.input-group-addon{
			background-color: rgb(249,249,249) !important;
			border: 1px solid #999 !important;

		}
    </style>
 
<?php if(!Yii::$app->request->isAjax) : ?>
<div class="row btn-print">
	<div class="col-md-9  col-xs-12">
 
        
        <?php $form = ActiveForm::begin(['id' => 'invline-search','method' => 'GET']); ?>
        <div class="row" style="margin-bottom: 10px;">

           

            

            <div class="col-sm-6 col-xs-12">  
				<label><?=Yii::t('common','Date Filter')?></label>

              <?php

              $startDate  = date('Y-m-').'01';
              $endDate    = date('Y-m-d');

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

            <div class="col-md-2  col-xs-6"> 

            	
	            <div class="input-group" >
					<label><?=Yii::t('common','Sales')?></label>
	                <?php
	                echo Html::dropDownList('search-from-sale', null,
	                    					ArrayHelper::map(
				                    						SalesPeople::find()->orderBy(['name'=> SORT_ASC])->all(),
				                                            	'code',function($model){
				                                                return '['.$model->code.'] '.$model->name. ' '.$model->surname;
				                                            	}
				                                            ),
					                    					[
					                    						'class'=>'form-control  col-xs-12',
					                    						'prompt' => Yii::t('common','Every one'),
					                    						
					                    					]
					                    					 
			                                             
	                						) 
	                ?>

	                <?php 

	    //             echo Select2::widget([
					//     'name' => 'customer',
					//     'data' => ArrayHelper::map(
					//                     						SalesPeople::find()->orderBy(['name' => SORT_ASC])->all(),
					//                                             	'code',function($model){ return '['.$model->code.'] '.$model->name. ' '.$model->surname; }
					//                                             ),
					//     'options' => [
					//         'placeholder' => Yii::t('common','Every one'),
					//         'multiple' => false,
					//         'class'=>'form-control  col-xs-12',
					//     ],
					// ]);

					?>
	               
	            </div>
               
            </div>


            <div class="col-md-3  col-xs-6">
             
					<label><?=Yii::t('common','Customers')?></label>
	                <?php

	                // Html::dropDownList('search-customer', null,
	                //     					ArrayHelper::map(
				             //        						Customer::find()->all(),
				             //                                	'code','name'
				             //                                ),
					            //         					[
					            //         						'data-live-search'=> "true",
					            //         						'class'=>'  form-control ',
					            //         						'prompt' => Yii::t('common','All customers'),
					                    						
					            //         					] 
			                                             
	                // 						) 
	                ?>

	                <?php 

	                echo Select2::widget([
					    'name' => 'customer',
					    'data' => ArrayHelper::map(
					                    						Customer::find()->orderBy(['code' => SORT_ASC])->all(),
					                                            	'id',function($model){ return '['.$model->code.'] '.$model->name; }
					                                            ),
					    'options' => [
					        'placeholder' => Yii::t('common','Customer'),
					        'multiple' => false,
					        'class'=>'form-control  col-xs-12',
					    ],
					]);

					?>
	               
	             
            </div>
             <div class="col-md-1  col-xs-6">  
             	<div class="input-group" >
             	<label  style="color: #fff"> Search </label> <br>
            	<button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            	</div>
            </div>

           

        </div><!-- /.row -->

         <?php ActiveForm::end(); ?>

 


	</div>
	<dir class="col-md-3  col-xs-12" style="margin-top: 0px;">
		<div class="row text-right" >
			
			<div class="col-xs-12 ">
			<label style="color: #fff"> Print </label> <br>
			<?php
				 // echo ExportMenu::widget([
				 //            'dataProvider' => $dataProvider,
				 //            'columns' => $gridColumns,
				 //            'columnSelectorOptions'=>[
				 //                'label' => ' ',
				 //                'class' => 'btn btn-warning'
				 //            ],
				 //            'fontAwesome' => true,
				 //            'dropdownOptions' => [
				 //                'label' => 'Export All',
				 //                'class' => 'btn btn-primary'
				 //            ],
		                 
				 //        ]); 


				?>
				<a href="#" class="btn btn-info-ew " onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
			</div>
		</div>
	</dir>
</div>	
			

<?php endif; ?>


<div class="row body-table">
	<div class="overflow">
	<div class="body-print">
		<div class="row">
	        <div class="profile-header">
	            <div class="col-xs-10">
	            	<div class="row">
	            		<div class="col-xs-12"><?=$comp->name?></div>
	            	</div>
	                
	                <div class="row">
	                	<div class="col-xs-12"><span class="h5">รายงานขายเงินสด เรียงตามเลขที่</span></div>
	                </div>
	                
	                 

	            	<div class="row">
	                    <div class="col-xs-12">
	                    	<div class="row">
		                        <div class="col-xs-2">วันที่จาก</div>
		                        <div class="col-xs-2"><?=DateThai(isset($_GET['fdate']) ? $_GET['fdate'] : date('Y-m-').'01') ?></div>
		                        <div class="col-xs-1">ถึง</div>
		                        <div class="col-xs-7"><?=DateThai(isset($_GET['tdate'])? $_GET['tdate'] : date('Y-m-d'))?></div>
	                        </div>
	                    </div>
	                </div>
	                 
					 
	                 
					<div class="row"> 
		                <div class="col-xs-12">
		                	<div class="row">
			                    <div class="col-xs-2">เลขที่จาก</div>
			                    <div class="col-xs-2" style="color: #fff;">- </div>
			                    <div class="col-xs-1">ถึง</div>
			                    <div class="col-xs-7">๙๙๙๙๙๙๙๙๙</div>
		                    </div>
		                </div>
	                </div>
	                 
	                
	            	<div class="row"> 
		                <div class="col-xs-12">
		                	<div class="row">
			                    <div class="col-xs-2">รหัสลูกค้า</div>

			                    <div class="col-xs-2" >
				                    <?php if(isset($_GET['customer'])) { 
				                    	if($_GET['customer']!=''){ 
				                    		echo $Customer->code; 
				                    		}   
				                    	}  
				                    ?>
			                    	
			                    </div>

			                    <div class="col-xs-1">:</div>
			                    <div class="col-xs-7">
			                    	<?php if(isset($_GET['customer'])) { 
			                    		if($_GET['customer']!=''){ 
			                    			echo $Customer->code; 
			                    			} 
			                    		} 
			                    	?>
			                    		
			                    </div>
		                    </div>
		                </div>
	                </div>

	                 
	            	<div class="row"> 
		                <div class="col-xs-12">
		                	<div class="row">
			                    <div class="col-xs-2">พนักงานขาย</div>
			                    <div class="col-xs-2"><?=isset($_GET['search-from-sale']) ? $_GET['search-from-sale'] : ' ' ?></div>
			                    <div class="col-xs-1">:</div>
			                    <div class="col-xs-7">
			                    <?php
			                    if(isset($_GET['search-from-sale'])){

			                    	 if($_GET['search-from-sale']!=''){

				                    	 echo $sales->name;

				                    	}	
			                    }
			                     ?>
			                     </div>
		                    </div>
		                </div>
	                </div>
	                 
	            </div>

	            <div class="col-xs-2 text-right">
	                <div class="col-xs-12" style="margin-bottom: 3mm;">
	                    <div class="col-xs-6"> </div>
	                    <div class="col-xs-6"> </div>
	                </div>
	                <div class="col-xs-12">
	                    <div class="col-xs-6"><?=Yii::t('common','Date')?> : </div>
	                    <div class="col-xs-6">  <?=date('d/m/y',strtotime(date('Y-m-d').'+543 Years'))?> </div>
	                </div>
	            </div>

	        </div>
		</div>

        <div class="table-header">
  
  		<table class="tb-head"  border="0" cellpadding="0" cellspacing="0"  width="100%">
			<tr >
				<td class=' ' width="8%" style="padding-left: 20px;">เลขที่</td>
				<td class='text-center' width="7%" >วันที่</td>
				<td class='text-center' width="5%" >รหัสลูกค้า</td>
				<td class='text-left' style="padding-left: 25px;">ชื่อลูกค้า</td>
				<td class='text-center' width="9%" >รหัสพนักงานขาย</td>
				<td class='text-center' width="5%" >V</td>
				<td class='text-right' width="6%" >ส่วนลด</td>
				<td class='text-right' width="7%" >มูลค่าสินค้า</td>
				<td class='text-right' width="6%" >VAT.</td>
				<td class='text-right' width="7%" >รวมทั้งสิ้น</td>
				<td class='text-right' width="7%" >ยอดรับเกิน</td>
				<td class='text-right' width="7%" >รับด้วย ง/ส</td>
				<td class='text-right' width="7%" >รับด้วยเช็ค</td>
				<td class='text-right' width="7%" >ภาษี ณ ที่จ่าย</td>
			</tr>
			<tr>
				<td colspan="10" style="padding-left: 1px;">
					<table  border="0" cellpadding="0" cellspacing="0"  width="100%">
						<tr>
							<td width="45%" style="padding-left: 25px;">รายละเอียด</td>
							<td class='text-right' width="11%">จำนวน</td>
							<td class='text-right' width="10%">ราคาต่อหน่วย</td>
							<td class='text-right' width="10%">จำนวนเงิน</td>
							<td class='text-right' width="10%">จากใบสั่งขาย</td>
							<td > </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		  

	     


		<?php 


		    
 
			$sumF1 	= 0;
	        $sumF2 	= 0;
	        $sumF3 	= 0;
	        $sumF4 	= 0;
	        $sumF5 	= 0;
	        $sumF6 	= 0;
	        $sumF7 	= 0;



	 

		    $AllData = $dataProvider->getTotalCount();
		    

		    $data = $dataProvider->models;
		    $data = array_chunk($data, $PerPage);

		 

		    $ix = 0;

		    for ($i=0; $i < count($data); $i++) { 
		            
		        
 

		        
		        $td = '<table class="table">';
		        $td.= '<tbody>';

		        

		       

		        foreach ($data[$i] as $model) {
		            
		            $ix++;
		            
		          	$Dotted 	= '';

		          	if(strlen($model->customer->name) > $subStr) $Dotted = '...';


					$invLine 	= RcInvoiceLine::find()->where(['source_id' => $model->id]);
					$sumLine 	= $invLine->sum('quantity * unit_price');

		          	$Total 		= FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');
 					 
 					$vatTotal 	= $sumLine * $model->vat_percent/100;



 					// ###################################
 					// ###################################
 					// ###################################
 					// ###################################

 					$RcCheque 	= FunctionManagement::validateCheque($model,'Cheque');
 					$RcCash 	= FunctionManagement::validateCheque($model,'Cash');
 					
 					$RcOver 		= ($RcCheque + $RcCash) - $Total;

 					if($RcOver <= 0){

 						$RcOver 	= 0; 
 						$OverColor  = '';

 					}else {
 						$OverColor  = 'text-danger';
 					}





		            $td.= '<tr style="background-color: #eeefef;">';
		                 
		            	/* R1 */ 	$td.= "<td class='item' width='9%'>{$model->no_}</td>";
		            	/* R2 */    $td.= "<td class='item' width='7%'>".date('d/m/y',strtotime($model->posting_date."+543 Years"))."</td>";
		            	/* R3 */    $td.= "<td class='item' width='5%'>{$model->cust_code} </td>";
		            	/* R4 */    $td.= "<td class='item'  >".mb_substr($model->customer->name,0,$subStr).$Dotted."</td>";
		            	/* R5 */    $td.= "<td class='item' width='5%'>{$model->sales_people}</td>";
		            	/* R6 */    $td.= "<td class='item text-right' width='4%' >0</td>";
		            	/* R7 */    $td.= "<td class='item text-right' width='8%' >{$model->discount}</td>";
		/* $sumF1 */	/* R8 */    $td.= "<td class='item text-right' width='7%'>".number_format($sumLine,2)."</td>";
		/* $sumF2 */	/* R9 */    $td.= "<td class='item text-right' width='6%'>{$model->vat_percent}</td>";
		/* $sumF3 */	/* R10 */ 	$td.= "<td class='item text-right' width='7%' >".number_format($Total,2)."</td>";
		/* $sumF4 */	/* R11 */ 	$td.= "<td class='item text-right {$OverColor}' width='7%' >".number_format($RcOver,2)."</td>";
		/* $sumF5 */	/* R12 */ 	$td.= "<td class='item text-right' width='7%' >".number_format($RcCash,2)."</td>";
		/* $sumF6 */	/* R13 */ 	$td.= "<td class='item text-right' width='7%' >".number_format($RcCheque,2)."</td>";
		/* $sumF7 */	/* R14 */ 	$td.= "<td class='item text-right' width='7%' >".number_format($vatTotal,2)."</td>";


		            $td.= '</tr>';


		            /// Sum Zone 
		            $sumF1 += $sumLine;
		            $sumF2 += $model->vat_percent;
		            $sumF3 += $Total;
		            $sumF4 += $RcOver;
		            $sumF5 += $RcCash;
		            $sumF6 += $RcCheque;
		            $sumF7 += $vatTotal;


		            $x = 0;


		            $td.= '<tr>';
			            	$td.= "<td class='tbDetail' colspan='10' style='padding-left: 0.5cm;padding-bottom:0.5cm;'>";

			            	$td.= '<table border="0" cellpadding="0" cellspacing="0"  width="100%">';

				            foreach ($invLine->all() as $key => $line) {
				            	$x++;

				            	$ix++;

				            	$SO 		= '0000000';
				            	

				            	if(isset($line->orderNo->order_id))
			            		{
			            			$saleOrder 	= SaleHeader::findOne($line->orderNo->order_id);
			            			$SO = $saleOrder->no;

			            		}

				            
					            
					            		$td.= '<tr>';
					            			$td.= "<td  width='3%' >{$x}.</td>";
					            			$td.= "<td  width='13%' >{$line->itemstb->master_code}</td>";
					            			$td.= "<td  width='30%' >{$line->code_desc_}</td>";
					            			$td.= "<td  width='10%' align='right'>".number_format($line->quantity,2)." {$line->itemstb->UnitOfMeasure}</td>";
					            			$td.= "<td  width='10%' align='right'>".number_format($line->unit_price,2)."</td>";
					            			$td.= "<td  width='10%' align='right'>".number_format($line->quantity * $line->unit_price,2)."</td>";
					            			$td.= "<td  width='15%' align='center' >{$SO}</td>";
					            			$td.= "<td  > </td>";
					            			 
					            		$td.= '</tr>';
					            	

				            }

		            		$td.= '</table>';

		            		$td.= "</td>";
		            		$td.= "<td colspan='6'>";
		            		$td.= "</td>";

		            $td.= '</tr>';
		            
		        }

		        $td.= '</tbody>';
		        $td.= '</table>';
		        //$td.= '</div>';
		         
		        echo $td;


		    }
		 

		?>
		 
	<table class="tb-head"  border="0" cellpadding="0" cellspacing="0"  width="100%">
			<tr>
				<td width="8%" > </td>
				<td width="7%"> </td>
				<td  > </td>
				<td width="13%" > </td>
				<td width="8%" > </td>
				<td width="11%"  colspan="2">รวม <?=$AllData?> ใบ</td>
			 
				<td class="footer-value text-right" width="7%" ><div><p><?=number_format($sumF1,2)?></p></div></td>
				<td class="footer-value text-right" width="6%" ><div><p><?=number_format($sumF2,2)?></p></div></td>
				<td class="footer-value text-right F3" width="7%" ><div><p><?=number_format($sumF3,2)?></p></div></td>
				<td class="footer-value text-right" width="7%"><div><p><?=number_format($sumF4,2)?></p></div></td>
				<td class="footer-value text-right" width="7%"><div><p><?=number_format($sumF5,2)?></p></div></td>
				<td class="footer-value text-right" width="7%"><div><p><?=number_format($sumF6,2)?></p></div></td>
				<td class="footer-value text-right" width="7%"><div><p><?=number_format($sumF7,2)?></p></div></td>
			</tr>
	</table>	
	 


	</div>
	</div>
</div>



 




 

 