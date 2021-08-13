<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
use common\models\SalesPeople;
use common\models\Customer;

use kartik\widgets\Select2;


$this->title = Yii::t('common','Customer item sale');
?>
<style type="text/css">
    
 
    .text-page{
        counter-increment: page;        
        content: counter(page);
    }
    
  @media print{
    @page {
        margin-top:21px !important;
        size: A4 portrait; 
    }
    body{
        font-family: 'saraban', 'roboto', sans-serif; 
        font-size:10px !important;
    }

    body table{
        font-size:9px !important;
    }

    .text-page{
        counter-increment: page !important;        
        content: counter(page) !important;
    }

    .text-page:after{
        content: "Page " counter(page) " of " counter(pages); 
        /* content: counter(page);*/
    }

    
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

  .payment-detail-modal:hover,
  .invoice-detail:hover {
    background: #3fbbea !important;
  }

  .select2-results{
    font-family: 'tahoma';
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
            <div class="col-lg-4 col-md-6 col-sm-4">  
            <label><?=Yii::t('common','Date Filter')?></label>
              <?php

$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
	<span class="input-group-addon">$FromDate</span>
	{input1}
 
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

            <div class="col-sm-5 col-xs-12">
              <div class="row">
                <div class="col-xs-8">
                    <label><?=Yii::t('common','Customers').' ('.Yii::t('common','Head Office').')'?> </label>
                    <?php 
                      $keys = 'customers&comp:'.Yii::$app->session->get('Rules')['comp_id'];
                      $customerList = Yii::$app->cache->get($keys);                  
                      if($customerList){
                        $customer = $customerList;
                      }else{
                        $customer = ArrayHelper::map(
                          Customer::find()
                          ->where(['or', 
                            ['id'       => 909], 
                            ['comp_id'  =>  Yii::$app->session->get('Rules')['comp_id']]
                          ])
                          ->andWhere(['status'=>'1'])
                          ->andWhere(['headoffice' => 1])
                          ->orderBy(['code' => SORT_ASC])
                          ->all(),
                                  'id',
                                  function($model){ 
                                    return '['.$model->code.'] '.$model->name; 
                                  }
                          );
                    
                        Yii::$app->cache->set($keys,$customer, 1);
                      }
                    ?>
                    <?= Select2::widget([
                        'name'  => 'customer',
                        'id'    => 'customer',
                        'data'  => $customer,
                        'options' => [
                            'placeholder' => Yii::t('common','Customer'),
                            'multiple' => false,
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                          'allowClear' => true
                        ],
                        'value' => Yii::$app->request->get('customer') ?  Yii::$app->request->get('customer') : ''
                    ]);
                  ?>
                </div>
                <div class="col-xs-4"> 
                  <label><?=Yii::t('common','Branch')?></label>
                  <select class="form-control  mb-10" name="branch-filter" >
                      <option value="1"><?=Yii::t('common','Head Office')?></option>
                      <option value="2"><?=Yii::t('common','Head Office')?> <?=Yii::t('common','And')?> <?=Yii::t('common','Branch')?></option>
                  </select> 
                  <small class="show-branch" style="display:none;">
                    <a href="#"><i class="fas fa-sitemap"></i> <?=Yii::t('common','Show brach')?></a>
                  </small>
                </div>
              </div>
              
              <div class="row">
                <div class="col-xs-12">
                  <div class="box box-solid box-info" id="branch-list" style="display:none;">
                    <div class="box-header with-border ">
                      <i class="fa fa-text-width"></i> 
                      <h3 class="box-title"> </h3>
                    </div> 
                    <div class="box-body"> </div> 
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-2 col-xs-12">
              <label><?=Yii::t('common','Vat')?></label>
              <select class="form-control  mb-10" name="vat-filter" >
                  <option value="0"><?= Yii::t('common','All') ?></option>
                  <option value="Vat">Vat</option>
                  <option value="No">No Vat</option>
              </select> 
              
            </div>     
            <div class="col-sm-1  col-xs-12 text-right" style="padding-top: 25px;">
            	<button type="button" class="btn btn-info btn-refresh" id="btn-refresh" data-key="all"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('common','Search')?></button>              
            </div>
            
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->
      <?php ActiveForm::end(); ?>
    </div>
     
</div>