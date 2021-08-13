<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use kartik\widgets\ActiveForm;
use common\models\SalesPeople;
use common\models\Customer;
use kartik\widgets\Select2;

use yii\web\JsExpression;
 

$this->title = Yii::t('common','Payment Receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Payment Receipt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>

 
    	   
<?php $form = ActiveForm::begin(['id' => 'invlice-search','method' => 'GET']); ?>
    <div class="row" style="margin-bottom: 10px;">              
        <div class="col-sm-6">  
            <label><?=Yii::t('common','Date Filter')?></label>
            <?php

            $startDate  = date('Y-m-').'01';
            $endDate    = date('Y-m-d');

            if(isset($_GET['fdate'])){
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
          'type'      => DatePicker::TYPE_RANGE,
          'name'      => 'fdate',
          'value'     => $startDate,					
          'name2'     => 'tdate',
          'value2'    => $endDate,                  
          'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
          'layout'    => $layout,
          'options'   => ['autocomplete'=>'off', 'id' => 'fdate'],
          'options2'  => ['autocomplete'=>'off', 'id' => 'tdate'],
          'pluginOptions' => [
            'autoclose'   => true,
            'format'      => 'yyyy-mm-dd'
          ], 
          'pluginEvents' => [
                            "changeDate" => "function(e) { console.log($('#fdate').val()); console.log($('#tdate').val()); }",
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

        <div class="col-md-3  col-xs-8">        
            <label><?=Yii::t('common','Customers')?></label>
            <?= Select2::widget([
                'name'  => 'customer',
                'id'    => 'customer',
                'options' => [
                    'placeholder' => Yii::t('common','Customer'),
                    'multiple'    => false,
                    'class'       => 'form-control  col-xs-12',
                ],
                'pluginOptions' => [
                    'allowClear'    => true,
                    'minimumInputLength'  => 2,
                    'ajax' => [
                        'url'       => \yii\helpers\Url::to(['/customers/customer/customer-list-ajax']),
                        'dataType'  => 'json',
                        'data'      => new JsExpression('function(params) { return {q:params.term}; }'),
                        'cache'     => true
                    ],
                ],                    
                'value' => @$_GET['customer'],
                'initValueText' => @$_GET['customer'] 
                                        ? '['.Customer::findOne(@$_GET['customer'])->code.'] '.Customer::findOne(@$_GET['customer'])->name
                                        : ''
            ]);
            ?>
        </div>
      
        <div class="col-sm-1 col-xs-12" style="padding-top: 25px;">               
            <button type="submit" class="btn btn-info submit-form-search"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
        </div>
    </div>
          
       

    <div class="row" >  
        <div class="col-sm-6" >
            <?php 
            $payments     = NULL;
            $notpayment   = NULL;            
            if(isset($_GET['payment'])) {
                if($_GET['payment'] == 'payment')     $payments = 'checked="checked"';
                if($_GET['payment'] == 'not_payment') $notpayment = 'checked="checked"';
            };
            ?>
            <div class="col-xs-12 panel panel-info" style="padding: 5px;">
                <div class="col-xs-12">Bill Option</div>
                <div class="col-xs-4">
                    <label> 
                        <input type="radio" name="payment" value="all" checked="checked" > <?=Yii::t('common','ทั้งหมด')?> 
                    </label>
                </div>
                <div class="col-xs-4">
                    <label>
                        <div class="box-color bg-aqua"></div> 
                        <input type="radio" name="payment" value="payment" <?=$payments?> > <?=Yii::t('common','Posted')?> 
                    </label>
                </div>
                <div class="col-xs-4">
                    <label>
                        <div class="box-color bg-orange"></div> 
                        <input type="radio" name="payment" value="not_payment" <?=$notpayment?> > <?=Yii::t('common','Not Post')?> 
                    </label>
                </div>                    
            </div>            
        </div>

        <div class="col-xs-6 " >
            <div class="panel panel-info" style="padding: 5px;">
                <div class="row">
                    <div class="col-xs-12">การแสดงผล (แสดงผลรวม)</div>
                   
                    <div class="col-xs-4">
                        <select name="vat" >
                            <option value="0" <?php if(!@$_GET['vat']) echo 'selected'; ?>>All</option>
                            <option value="Vat" <?php if(@$_GET['vat']=='Vat') echo 'selected'; ?>>Vat</option>
                            <option value="No" <?php if(@$_GET['vat']=='No') echo 'selected'; ?> >No Vat</option>
                        </select>
                    </div>   
                    <div class="col-xs-4">
                        <select name="show-cn" >
                            <option value="false" <?php if(@$_GET['show-cn']=='false') echo 'selected'; ?>><?=Yii::t('common','Hide Credit Note')?></option>
                            <option value="true" <?php if(@$_GET['show-cn']=='true') echo 'selected'; ?> ><?=Yii::t('common','Show Credit Note')?></option>
                        </select>
                    </div>   
                    <div class="col-xs-4">
                        <select name="total-summary" id="">
                            <option value="all" selected="selected"><?=Yii::t('common','ทั้งหมด')?></option>
                            <option value="20" <?php if(@$_GET['total-summary']=='20') echo 'selected'; ?> ><?=Yii::t('common','แสดง 20 รายการ/หน้า')?></option>
                            <option value="50" <?php if(@$_GET['total-summary']=='50') echo 'selected'; ?>><?=Yii::t('common','แสดง 50 รายการ/หน้า')?></option>
                            <option value="100" <?php if(@$_GET['total-summary']=='100') echo 'selected'; ?>><?=Yii::t('common','แสดง 100 รายการ/หน้า')?></option>
                        </select>
                    </div>
                </div>             
            </div>
        </div>

        
    </div>

<?php ActiveForm::end(); ?>
 


