<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
 

use kartik\widgets\Select2;
use common\models\Customer;
?>
<style type="text/css">
  @media print{
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination{
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
</style>
<?php $form = ActiveForm::begin(['action' => ['index'],'id' => 'cheque-search','method' => 'GET']); ?>
<div class="row" style="margin-bottom: 10px;">
    <div class=" ">
        <div class="col-sm-6">  
        <label><?=Yii::t('common','Date Filter')?></label>
            <?php

            $startDate  = date('Y-m-').'01';
            $endDate    = date('Y-m-d');

            if($model->fdate!='') $startDate   = date('Y-m-d',strtotime($model->fdate));

            if($model->tdate!='') $endDate     = date('Y-m-d',strtotime($model->tdate));

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
                'name' => 'ChequeSearch[fdate]',
                'value' => $startDate,					
                'name2' => 'ChequeSearch[tdate]',
                'value2' => $endDate,                  
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

        
        <div class="col-md-3  col-xs-8">
        
        <?= $form->field($model, 'cust_no_')->widget(Select2::classname(),[
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
                    //'value' => @$_GET['customer']
                ]) ?>
             
        </div>

        <div class="col-sm-1  col-xs-12" style="padding-top: 25px;">             
            <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i> '.Yii::t('common', 'Search'), ['class' => 'btn btn-info']) ?>
        </div>

    </div><!-- /.col-sm-offset-6 -->

</div><!-- /.row -->


<div class="row">
    <div class="col-xs-6" >
        <div class="col-xs-12 panel panel-info" style="padding: 5px;">
            <div class="col-xs-12">การแสดงผล</div>
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
</div>

<?php ActiveForm::end(); ?>