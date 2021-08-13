<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
use common\models\SalesPeople;
use common\models\Customer;

//use kartik\widgets\Select2;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('common','Customer item sale');
?>
<style type="text/css">
  @media print{
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination,
    .search-box,
    caption{
      display: none;
    }
    .dataCalc{
      border:0px;
    }
    .textComment{
      border:0px;
    }
    a[href]:after {
      content: none !important;
    }
  }
  .btn-print{
      background-color: rgb(253,253,253);
      border-bottom: 1px solid #ccc;
      margin-bottom: 20px;
  }


	.input-group-addon{
		background-color: rgb(249,249,249) !important;
		border: 1px solid #999 !important;

	}

  a.view-receipt{
    padding: 0 5px 0 5px;
     
  }

  a.view-receipt:hover{
    color: red;
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

  .text-sum{
    margin:20px 0 0 0;
  }

  .text-sumVal{
    margin:20px 0 0 0;
    border-bottom: 5px double #ccc;
  }

  .sum-footer{
    margin-top: 10px;
    border-bottom: 1px solid #ccc;
  }

  .modal
  {
    overflow: hidden;
    background:none !important;

  }


  .modal-dialog{
     box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
  }

  .box-color{
    width:20px !important;
    height:20px;
    border:1px solid #ccc;
    position:absolute;
    margin-left:-25px;
  }

  table{
    font-family:  Arial, Helvetica, sans-serif;
  }
  @media (max-width: 767px) {
        .search-box{
           margin-top:50px;
        }

        #vat-change {
            margin-top: 10px;
        }

  }
</style>
<div class="row btn-print ">
  <div class="col-xs-12" >
        <?php $form = ActiveForm::begin(['id' => 'search-item','method' => 'POST']); ?>
        <div class="row" style="margin-bottom: 10px;">
          <div class=" ">
            <div class="col-sm-5">  
            <label><?=Yii::t('common','Date Filter')?></label>
              <?php

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
					'value' => Yii::$app->request->get('fdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('fdate'))) : date('Y-m').'-01',					
					'name2' => 'tdate',
					'value2' => Yii::$app->request->get('tdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('tdate'))) : date('Y-m-t'),                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'layout' => $layout,
                    'options' => ['autocomplete'=>'off'],
                    'options2' => ['autocomplete'=>'off'],
					'pluginOptions' => [
						'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',
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
                                                ->where(['status'=> 1])
                                                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                ->orderBy(['code' => SORT_ASC])
                                                ->all(),'id',function($model){
				                                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname;
				                                            	}
				                                            ),[
					                    						'class'=>'form-control',
					                    						'prompt' => Yii::t('common','Every one'),
					                    						'options' => [                        
                                                                    Yii::$app->request->get('search-from-sale')  => ['selected' => 'selected']
                                                                ],
					                    					] 
	                						) 
	                ?>
	               
	            </div>
               
            </div>
             <div class="col-md-3  col-xs-8">
                <label><?=Yii::t('common','Customers')?></label>
                <?= Select2::widget([
                    'name' => 'customer',
                    'id' => 'customer',
                    'options' => [
                        'placeholder' => Yii::t('common','Customer'),
                        'multiple' => false,
                        'class'=>'form-control  col-xs-12',
                    ],
                    'pluginOptions' => [
                      'allowClear' => true,
                      'minimumInputLength' => 2,
                      'ajax' => [
                          'url' => \yii\helpers\Url::to(['customer-list-ajax']),
                          'dataType' => 'json',
                          'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                          'cache' => true
                      ],
                    ],

                    //'value' => 909
                ]);
              ?>
            </div>

            <div class="col-sm-2  col-xs-12 text-right" style="padding-top: 25px;">
            	<button type="submit" class="btn btn-info"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('common','Search')?></button>
            </div>
            
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->
      <?php ActiveForm::end(); ?>
    </div>
</div>
<?php 
 