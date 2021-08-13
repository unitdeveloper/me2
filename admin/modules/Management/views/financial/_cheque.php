<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
 
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use common\models\SaleHeader;
use common\models\SalesPeople;
use common\models\Customer;

use kartik\widgets\Select2;

use admin\modules\Management\models\FunctionManagement;

use common\models\ViewRcInvoice;
 
$this->title = Yii::t('common','Payment receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Payment receipt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

function itemGroup($data)
{
    $GroupID = FunctionManagement::findItemInGroup($data);

    $GroupID = explode(',', $GroupID);

    return $GroupID;
}

$this->registerCssFile('css/management-cheque.css');
?>
 

<div class="sales-report" ng-init="Title='<?=Yii::t('common','Payment')?>'">
<?php 
        $gridColumns = [
            [
              'class' => 'kartik\grid\SerialColumn',
              'contentOptions' => function($model){
                if($model->status == 'Open'){
                  return ['class' => 'alert-warning'];
                }else {
                  return ['class' => 'alert-info'];
                }
                
              },
            ],
            //'update_date',
            [
              //'attribute' => 'posting_date',
            	'label' => Yii::t('common','Date'),
              'format' => 'raw',
              'contentOptions' => ['style' => 'font-family: tahoma,Arial, Helvetica, sans-serif;'],
            	'value' => function($model)
            	{
            		// wordwrap($model->ship_address, 150, "<br/>\r\n") 
                    return date('d/m/Y',strtotime($model->posting_date));

            	}

            ],

            [
              'label' => Yii::t('common','Customer'),
              'attribute' => 'cust_no_',
            	'format' => 'raw',
            	'contentOptions' => [
                    				        'style'=>'max-width:180px; min-height:100px; overflow: auto; word-wrap: break-word;'
                    				      ],
            	'value' => function($model){
            		
                    //return wordwrap($model->customer->name, 100, "<br/>\r\n"); 
                    
                    $html =  "<a href='#modal/{$model->cust_no_}' class='open-modal' data='{$model->cust_no_}' row-data='{$model->id}'>{$model->customer['name']} ({$model->customer->getAddress()['province']})</a>";
                    
                    return $html;
                    //return $model->customer['name'];

            	}
            ],
            //'customer.name',
            //'no_',

            [
            	'attribute' => 'no_',
              'label' => Yii::t('common','Document No'),
              'format' => 'html',
              'footerOptions' => ['class' => 'text-right'],
              'contentOptions' => ['style' => 'font-family: tahoma,Arial, Helvetica, sans-serif;'],
            	'value' => function($model){

                return $model->no_;
            		// return Html::a($model->no_,['/accounting/posted/posted-invoice','id' => base64_encode($model->id)],['target' => '_blank','data-pjax'=>"0"]);
              },
              'footer' => (@$_GET['total-summary']=='all') ? '
                  <div style="padding-top:92px;" >
                    <input type="text" style="width:150px;"  class="textComment  text-right"  value="'.Yii::$app->session->get('textComment').'"> 
                  </div>' : ''
            ],


            [
            	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Accessories'),
            	'format' => 'raw',
              'headerOptions' => ['class' => 'text-right'],
            	'contentOptions' => ['class' => 'text-right'],
              'footerOptions' => ['class' => 'text-right'],
            	'value' => function($model)
            	{

                //[34,35,36,37,38,39,40,42,43,44,45,71]

            		$Total = $model->getTotalBalance($model,itemGroup([4]),'Excepted');
            		return number_format($Total,2);
            	},
              'footer' => (@$_GET['total-summary']=='all') ? '<div class="sum-footer">'.number_format(ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4]),'Excepted'),2).'</div>
              <div class="text-sum">รวมทั้งสิ้น</div>
              <div class="text-sum">                
                <input type="text" class="dataCalc text-right" style="width:60px;"> %
              </div>
              ' : '',
               
            ],


            [
            	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','LED'),
            	'format' => 'raw',
              'headerOptions' => ['class' => 'text-right'],
            	'contentOptions' => ['class' => 'text-right'],
              'footerOptions' => ['class' => 'text-right'],
            	'value' => function($model)
            	{
                 

            		$Total = $model->getTotalBalance($model,itemGroup([4]),'Equal');
            		return number_format($Total,2);
            	},
              'footer' => (@$_GET['total-summary']=='all') ? '<div class="sum-footer">'.number_format(ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4]),'Equal'),2).'</div>
                            <div class="text-sumVal" data="'.ViewRcInvoice::getSumFooter($dataProvider->models).'">'.number_format(ViewRcInvoice::getSumFooter($dataProvider->models),2).'</div>
                            <div style="margin-top:15px;">
                              
                              <input type="text" class="dataTotal no-border text-right" >
                            </div>' : '',
            ],


            [
            	'label' => Yii::t('common','การชำระเงิน'),
            	'format' => 'raw',
            	'contentOptions' => [
				        'style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;'
				      ],
            	'value' => function($model){
                // return $model->id;
                $Cheque = \common\models\Cheque::find()
                ->joinwith('banklist')
                ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
                ->addParams([':apply_to' => $model->id]);

                if($Cheque->exists()){

                    $query = $Cheque->all();

                    $data = array();

                    foreach ($query as $key => $models) {

                      $dateCheque   = date('d/m/y',strtotime($models->posting_date));
                       
                      //#######
                      $models->bank_id = '#'.$models->bank_id;

                      if($models->type=='Cash') $models->bank_id = NULL;
                      if($models->type=='ATM')  $models->bank_id = NULL;

                      //$models->banklist->name   = $models->banklist->name;
                      if($models->type=='ATM') $models->banklist->name = Yii::t('common','Transfer');

                      $models->balance = number_format($models->balance,2);





                      // ถ้า approve แล้วให้มีสีเขียว
                      $bgStatus = NULL;
                      $ApproveCheck  = \common\models\Approval::find()->where(['source_id' => $models->id]);
                      if($ApproveCheck->exists()) $bgStatus = 'bg-green';

                      $data[]       = "<a href='#{$models->id}' class='view-receipt {$bgStatus}' data='{$models->id}'>
                                        {$models->banklist->name} 
                                        #{$dateCheque} {$models->bank_id} 
                                        #{$models->balance}
                                      </a>";




                    }
                    //$dateCheque   = date('d/m/y',strtotime($models->posting_date.'+543 Year'));
                    

                    return implode("<br>\r\n",$data);

                }else {

                  return '';

                }
            		
              },
             
            ],


            // [
            // 	'label' => Yii::t('common','Amount') .'/'. Yii::t('common','สวิทซ์ปลั๊ก'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            
            
            // [
            // 	'label' => Yii::t('common','V-SAFE 2'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            // [
            // 	'label' => Yii::t('common','SPD-R'),
            // 	'format' => 'raw',
            // 	'value' => function($model)
            // 	{
            // 		return '';
            // 	}
            // ],
            [
            	'label' => Yii::t('common','Remark'),
            	'format' => 'raw',
            	'contentOptions' => [
				        'style'=>'max-width:150px; min-height:100px; overflow: auto; word-wrap: break-word;',
                'class' => 'remark',
				      ],
            	'value' => function($model)
            	{
                  $Cheque = \common\models\Cheque::find()
                  ->joinwith('banklist')
                  ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                  ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
                  ->addParams([':apply_to' => $model->id]);

                  if($Cheque->exists()){

                      $query = $Cheque->all();

                      $data = array();

                      foreach ($query as $key => $models) {
                        
                        $data[]       = "<span style='color:red'>*  {$models->remark}</span>";
                      }
                      //$dateCheque   = date('d/m/y',strtotime($models->posting_date.'+543 Year'));
                      

                      return implode("<br>\r\n",$data);

                      //$models = $Cheque->one();
                      
                      //return "<p style='color:red'>{$models->remark}</p>";

                  }else {

                    return '';

                  }
            		
            	}
            ],
            

            // [
            // 	'label' => Yii::t('common','Amount'),
            // 	'format' => 'raw',
            // 	'contentOptions' => ['class' => 'text-right'],
            // 	'value' => function($model)
            // 	{
            // 		//return number_format(($model->sumLine - $model->discount) * $model->vat_percent);
            // 		$Total = FunctionManagement::getTotalBalance($model,'All','Equal');
            // 		return $Total;
            // 	}
            // ],


            
        ]; ?>
























<?php $form = ActiveForm::begin(['id' => 'invlice-search','method' => 'GET']); ?>

<div class="row btn-print box-shadow">
  <div class="col-md-12  col-xs-12" >
        	
        
        
        <div class="row" style="margin-bottom: 10px;">

          <div class=" ">

            

            <div class="col-sm-6">  
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

            <div class="col-sm-2 col-xs-4"> 
               
	            <div class="input-group" >
                <label><?=Yii::t('common','Sales')?></label>
	                <?= Html::dropDownList('search-from-sale', null,
	                    					ArrayHelper::map(
				                    						SalesPeople::find()->where(['status'=> 1])->all(),
				                                            	'code',function($model){
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
             <div class="col-md-3  col-xs-8">
             
                  <label><?=Yii::t('common','Customers')?></label>
                        <?php

                        // Html::dropDownList('search-customer', null,
                        //              ArrayHelper::map(
                           //                   Customer::find()->all(),
                           //                                 'code','name'
                           //                                ),
                            //                  [
                            //                    'data-live-search'=> "true",
                            //                    'class'=>'  form-control ',
                            //                    'prompt' => Yii::t('common','All customers'),
                                                
                            //                  ] 
                                                         
                        //            ) 
                        ?>

                        <?php 

                        echo Select2::widget([
                    'name' => 'customer',
                    'data' => ArrayHelper::map(
                                                Customer::find()
                                                ->where(['comp_id'=>Yii::$app->session->get('Rules')['comp_id'],'status'=>'1'])
                                                ->orderBy(['code' => SORT_ASC])->all(),
                                                              'id',function($model){ 
                                                                return '['.$model->code.'] '.$model->name.' ('.$model->getAddress()['province'].')'; 
                                                              }
                                                            ),
                    'options' => [
                        'placeholder' => Yii::t('common','Customer'),
                        'multiple' => false,
                        'class'=>'form-control  col-xs-12',
                    ],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                    'value' => @$_GET['customer']
                ]);

              ?>
                 
               
            </div>

            <div class="col-sm-1  col-xs-12" style="padding: 25px 10px 0px 0px;">
               
            	<button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            </div>
            
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->

          
        <div class="row">
          
      </div>
      

  
      </div>
      

  <div class=" ">
    <div class="col-sm-4" >
        <?php 
        $payments     = NULL;
        $notpayment   = NULL;
        

        if(isset($_GET['payment'])) {
          if($_GET['payment'] == 'payment')     $payments = 'checked="checked"';
          if($_GET['payment'] == 'not_payment') $notpayment = 'checked="checked"';


        }; 

        
        ?>
        <div class="col-xs-12 panel panel-info" style="padding: 5px;">
          <div class="col-xs-12">Bill Option </div>
        <div class="col-xs-4">
          <label> <input type="radio" name="payment" value="all" checked="checked" > <?=Yii::t('common','ทั้งหมด')?> </label>
        </div>

        <div class="col-xs-4">

          <label><div class="box-color bg-aqua"></div> <input type="radio" name="payment" value="payment" <?=$payments?> > <?=Yii::t('common','Posted')?> </label>
        </div>

        <div class="col-xs-4">
          <label><div class="box-color bg-orange"></div> <input type="radio" name="payment" value="not_payment" <?=$notpayment?> > <?=Yii::t('common','Not Post')?> </label>
        </div>
        
      </div>
    </div>
    <div class="col-sm-4">
      <div class="col-xs-12 panel panel-info" style="padding: 5px;">
        <div class="col-xs-12">การแสดงผล (แสดงผลรวม)</div>
        <div class="col-xs-12">
          <select name="total-summary" id="">
            <option value="all" selected="selected"><?=Yii::t('common','ทั้งหมด')?></option>
            <option value="20" <?php if(@$_GET['total-summary']=='20') echo 'selected'; ?> ><?=Yii::t('common','แสดง 20 รายการ/หน้า')?></option>
            <option value="50" <?php if(@$_GET['total-summary']=='50') echo 'selected'; ?>><?=Yii::t('common','แสดง 50 รายการ/หน้า')?></option>
            <option value="100" <?php if(@$_GET['total-summary']=='100') echo 'selected'; ?>><?=Yii::t('common','แสดง 100 รายการ/หน้า')?></option>
            
          </select>
          
        </div>
          
      </div>
    </div>
    <div class="col-sm-4">
      <div class="pull-left" style="margin-top: 22px;">
        <?php
           $fullExportMenu = ExportMenu::widget([
                      'dataProvider' => $dataProvider,
                      'columns' => [
                                               
                        [
                           
                          'label' => Yii::t('common','Date'),
                          'format' => 'raw',
                          'value' => function($model)
                          {                               
                              return date('d/m/Y',strtotime($model->posting_date));
            
                          }
            
                        ],
            
                        [
                          'label' => Yii::t('common','Customer'),
                          'format' => 'raw',                          
                          'value' => function($model){
                            
                                
                                return $model->customer['name'].' ('.$model->customer->getAddress()['province'].')';
                                
                              
            
                          }
                        ],
                        
                        [
                          //'attribute' => 'no_',
                          'label' => Yii::t('common','Document No'),
                          'format' => 'html',                          
                          'value' => function($model){
            
                            return $model->no_;
                             
                          },
                          'footer' => Yii::t('common','รวม'),
                        ],
            
            
                        [
                          'label' => Yii::t('common','Amount') .'/'. Yii::t('common','Accessories'),
                          'format' => 'raw',                      
                          'value' => function($model)
                          {
          
            
                            $Total = $model->getTotalBalance($model,itemGroup([4]),'Excepted');
                            return $Total;
                          },
                          'footer' => (@$_GET['total-summary']=='all') ? ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4]),'Excepted') : '',
                          
                           
                        ],
            
            
                        [
                          'label' => Yii::t('common','Amount') .'/'. Yii::t('common','LED'),
                          'format' => 'raw',
                          'headerOptions' => ['class' => 'text-right'],
                          'contentOptions' => ['class' => 'text-right'],
                          'footerOptions' => ['class' => 'text-right'],
                          'value' => function($model)
                          {
                             
            
                            $Total = $model->getTotalBalance($model,itemGroup([4]),'Equal');
                            return $Total;
                          },
                          'footer' => (@$_GET['total-summary']=='all') ? ViewRcInvoice::getFooterRowTotal($dataProvider->models,itemGroup([4]),'Equal') : '',
                           
                        ],
            
            
                        [
                          'label' => Yii::t('common','Payment'),
                          'format' => 'raw',                           
                          'value' => function($model){
                            // return $model->id;
                            $Cheque = \common\models\Cheque::find()
                            ->joinwith('banklist')
                            ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->andWhere(new Expression('FIND_IN_SET(:apply_to, cheque.apply_to)'))
                            ->addParams([':apply_to' => $model->id]);
            
                            if($Cheque->exists()){            
                                $query = $Cheque->all();
                                $data = [];
                                foreach ($query as $key => $models) {
                                  $dateCheque   = date('d/m/y',strtotime($models->posting_date));
                                  //#######
                                  $models->bank_id = '#'.$models->bank_id;
                                  if($models->type=='Cash') $models->bank_id = NULL;
                                  if($models->type=='ATM')  $models->bank_id = NULL;
                                  //$models->banklist->name   = $models->banklist->name;
                                  if($models->type=='ATM') $models->banklist->name = Yii::t('common','Transfer');
                                  $models->balance = number_format($models->balance,2);
                                  // ถ้า approve แล้วให้มีสีเขียว
                                  $bgStatus = NULL;
                                  $ApproveCheck  = \common\models\Approval::find()->where(['source_id' => $models->id]);
                                  if($ApproveCheck->exists()) $bgStatus = 'bg-green';
                                  $data[]       = "{$models->banklist->name} #{$dateCheque} {$models->bank_id}  #{$models->balance} ";
                                }                                 
            
                                return implode("<br>\r\n",$data);

                            }else {
                              return '';
                            }
                            
                          },
                         
                        ],
            
                        [
                          'label' => Yii::t('common','Remark'),
                          'format' => 'raw',
                          'contentOptions' => [
                            'style'=>'max-width:150px; min-height:100px; overflow: auto; word-wrap: break-word;',
                            'class' => 'remark',
                          ],
                          'value' => function($model)
                          {
                              $Cheque = \common\models\Cheque::find()
                              ->joinwith('banklist')
                              ->where(['cheque.comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                              ->andWhere(new Expression('FIND_IN_SET(:apply_to, apply_to)'))
                              ->addParams([':apply_to' => $model->id]);
            
                              if($Cheque->exists()){
            
                                  $query = $Cheque->all();
            
                                  $data = array();
            
                                  foreach ($query as $key => $models) {
                                    
                                    $data[]       = " {$models->remark} ";
                                  }
                                  
            
                                  return implode("<br>\r\n",$data);
            
                          
            
                              }else {
            
                                return '';
            
                              }
                            
                          }
                        ],
                        
            
                      ],
                      'columnSelectorOptions'=>[
                          'label' => ' ',
                          'class' => 'btn btn-warning'
                      ],
                      'fontAwesome' => true,
                      'dropdownOptions' => [
                          'label' => 'Export All',
                          'class' => 'btn btn-primary'
                      ],
                       
                  ]); 
          ?>
      </div>
      <div class="pull-right" style="margin-top: 22px; margin-bottom: 10px;">
        <button class="btn btn-success-ew" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
      </div>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>







<div class="row">
	 
  <div class="col-sm-6  " style="font-size: 16px;">

    ยอดขายประจำเดือน 
    <?php

      if(isset($_GET['fdate'])){
        $fdate = date('m',strtotime($_GET['fdate']));
        $tdate = date('m',strtotime($_GET['tdate']));

        if($fdate==$tdate){
          echo Yii::t('common',date('M',strtotime($_GET['fdate'])));
        }else {
          echo Yii::t('common',date('M',strtotime($_GET['fdate']))).' ถึง '.Yii::t('common',date('M',strtotime($_GET['tdate'])));
        }
        
      }

    ?> 

    ปี 

    <?php

      if(isset($_GET['fdate'])){
        $fYear = date('y',strtotime($_GET['fdate']));
        $tYear = date('y',strtotime($_GET['tdate']));

        if($fYear==$tYear){
          echo date('Y',strtotime($_GET['fdate']));
        }else {
          echo date('Y',strtotime($_GET['fdate'])).' ถึง '.date('Y',strtotime($_GET['tdate']));
        }
        
      }

    ?> 

    
  </div>

  <div class="col-sm-6">
    Sale : <?php

    if(isset($_GET['search-from-sale'])){
      $SaleMan = \common\models\SalesPeople::find()->where(['code' => $_GET['search-from-sale']]);

      if($SaleMan->exists()){
        $models = $SaleMan->one();
        echo $models->code. ' '.$models->name;
      }
      
    }

    ?>
  </div>
</div>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        //'tableOptions' => ['class' => 'table table-bordered table-striped','id'=>'tb-cheque'],
        'pjax' => true, 
        'responsiveWrap' => false,
        'columns' => $gridColumns,
        'pager' => [
            'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
            'prevPageLabel' => '«',   // Set the label for the "previous" page button
            'nextPageLabel' => '»',   // Set the label for the "next" page button
            'firstPageLabel'=> '<i class="fa fa-fast-backward" aria-hidden="true"></i>',   // Set the label for the "first" page button
            'lastPageLabel'=>'<i class="fa fa-fast-forward" aria-hidden="true"></i>',    // Set the label for the "last" page button
            'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
            'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
            'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
            'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
            'maxButtonCount'=>15,    // Set maximum number of page buttons that can be displayed
            ],
          'panel' => [
              'type' => GridView::TYPE_PRIMARY,
              'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Library</h3>',
          ],
          // set a label for default menu
          'export' => [
              'label' => 'Page',
              'fontAwesome' => true,
          ],
          // your toolbar can include the additional full export menu
          'toolbar' => [
              '{export}',
              $fullExportMenu,
              // ['content'=>
              //     Html::button('<i class="glyphicon glyphicon-plus"></i>', [
              //         'type'=>'button', 
              //         'title'=>Yii::t('kvgrid', 'Add Book'), 
              //         'class'=>'btn btn-success'
              //     ]) . ' '.
              //     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], [
              //         'data-pjax'=>0, 
              //         'class' => 'btn btn-default', 
              //         'title'=>Yii::t('kvgrid', 'Reset Grid')
              //     ])
              // ],
          ]
         
    ]); ?>

</div>




<!-- Modal -->
<div id="chequeModal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header bg-green" style="cursor: move;">
        <button type="button" class="close " data-dismiss="modal">&times;</button>
        <h4 class="modal-title">การรับเช็ค/วางบิล</h4>
      </div>
      <div class="modal-body">
		<div class="ew-body-cheque">
			 <br><br><br><br>

		</div>	
      </div>
      <div class="text-center loading-content" style="position: absolute; top: 40%; right: 45%;  display: none;">
			<i class="fa fa-spinner fa-spin fa-2x fa-fw text-info" aria-hidden="true"></i>
			<div class="blink"> Loading </div>
	  </div>

      <div class="modal-footer">
      

        <button type="button" class="btn btn-default  pull-left" data-dismiss="modal">
        <i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button>    
      
     	<button type="button" name="Select" class="btn btn-success-ew getInv">
	    <i class="fa fa-check" aria-hidden="true"></i> <?=Yii::t('common','Select')?></button>
       

      </div>
    </div>

  </div>
</div>
 


<?php $this->registerJsFile('https://code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]);?>

<?php $this->render('__script');?>

