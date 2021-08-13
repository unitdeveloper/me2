<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;


use common\models\Customer;
use common\models\Company;
use common\models\SalesPeople;

use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $model admin\modules\accounting\models\BillingNoteSearch */
/* @var $form yii\widgets\ActiveForm */
$Actions        = Yii::$app->controller->action->actionMethod; 
$Cust 			= ['address' => NULL,'code' => NULL,'name' => NULL,'no_' => NULL];



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
	 
	$DocumentNo = substr($NoSeries, 0,-3).'XXX';


}

 

$LastDay    = date('t',strtotime(date('Y-m-d')));
$startDate  = date('Y-').'01-01';
$endDate    = date('Y-m-').$LastDay;
?>


<?php if(!Yii::$app->request->isAjax) : ?>
 
 <div class="row btn-print" style=" margin-top: -15px;">
     <div class="col-md-12  col-xs-12" style="margin-bottom: 10px; padding-top: 15px;">
  
         
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
                                                                 ->orderBy(['code' => SORT_ASC])
                                                                 ->all(),
                                                                     'id',function($model){ return '['.$model->code.'] '.$model->name; }
                                                                 ),
                                     'options' => [
                                         'placeholder' => Yii::t('common','Customer'),
                                         'multiple' => false,
                                         'class'=>'form-control  col-xs-12',
                                          
 
                                     ],
                                     'value' => @$_GET['customer']
                                 ]);
                             }
                         }else {
                             echo Select2::widget([
                                     'name' => 'customer',
                                     'data' => ArrayHelper::map(
                                                                 Customer::find()
                                                                 ->where(['id' => $custIncase])
                                                                 ->orderBy(['code' => SORT_ASC])
                                                                 ->all(),
                                                                     'id',function($model){ return '['.$model->code.'] '.$model->name; }
                                                                 ),
                                     'options' => [
                                         'placeholder' => Yii::t('common','Customer'),
                                         'multiple' => false,
                                         'class'=>'form-control  col-xs-12',
                                         
                                          
                                     ],
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