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
use common\models\BankList;
 
 

use admin\modules\Management\models\FunctionManagement;

use common\models\ViewRcInvoice;
 
$this->title = Yii::t('common','Payment Receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Payment Receipt'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

function itemGroup($data){
    $GroupID = FunctionManagement::findItemInGroup($data);
    $GroupID = explode(',', $GroupID);
    $GroupID = array_merge($data,$GroupID);
    foreach ($GroupID as $key => $var) {
      $GroupID[$key] = (int)$var;
    }
    return $GroupID;
}

$model = $searchModel;


?>


<div class="sales-report" ng-init="Title='<?=$this->title?>'">

<div class="row btn-print ">
    <div class="col-md-9  col-xs-12" >
        <?=$this->render('_top_menu',['model' => $model])?>
    </div>
      <div class="col-sm-3 col-xs-12 text-right" style="padding-top: 25px;">
        

        <?php
           echo ExportMenu::widget([
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
        
            
                      ],
                      'columnSelectorOptions'=>[
                          'label' => ' ',
                          'class' => 'btn btn-warning'
                      ],
                      'fontAwesome' => true,
                      'selectedColumns'=> [0,1,2,5,6,7,8],
                      'dropdownOptions' => [
                          'label' => Yii::t('common','Export All'),
                          'class' => 'btn btn-primary'
                      ],
                      'target' => ExportMenu::TARGET_BLANK,
                      'filename' => 'Payment-'.date('ymd_H_i_s')  
                       
                  ]); 
          ?>
        <div style="margin-top: 10px; margin-bottom: 10px;">
        <button class="btn btn-success-ew" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
        </div>
      </div>
</div>
 



<div class="row">
    <div class="col-sm-12">รายงานแยกประเทภทั่วไป </div>
    <div class="col-sm-6  " style="font-size: 16px;">

    
    <div>
        <?php

        if(isset($model->fdate)){
            $fdate = date('m',strtotime($model->fdate));
            $tdate = date('m',strtotime($model->tdate));

            if($fdate==$tdate){
                echo date('d',strtotime($model->fdate)).' '.Yii::t('common',date('M',strtotime($model->fdate)));
            }else {
                echo date('d',strtotime($model->fdate)).' '.Yii::t('common',date('M',strtotime($model->fdate))).' ถึง '.date('d',strtotime($model->tdate)).' '.Yii::t('common',date('M',strtotime($model->tdate)));
            }
            
        }

        ?> 

        ปี 

        <?php

        if(isset($model->fdate)){
            $fYear = date('y',strtotime($model->fdate));
            $tYear = date('y',strtotime($model->tdate));

            if($fYear==$tYear){
            echo date('Y',strtotime($model->fdate));
            }else {
            echo date('Y',strtotime($model->fdate)).' ถึง '.date('Y',strtotime($model->tdate));
            }
            
        }

        ?> 
    </div>
    
  </div>
 
</div>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showFooter' => true,
        //'tableOptions' => ['class' => 'table table-bordered table-striped','id'=>'tb-cheque'],
        //'pjax' => true, 
        'responsiveWrap' => false,
        'columns' => [
            //['class' => 'kartik\grid\SerialColumn'],
            'posting_date',
            [
                'label' => Yii::t('common','Receive'),
                'value' => function($model){
                    return Yii::t('common','Receive');
                }
            ],
            'no',
            [
                'label' => Yii::t('common','Payment'),
                'value' => function($model){
                    return Yii::t('common','Payment');
                }
            ],
            //'banklist.name',
            //'customer.code',
            [
                'attribute' => 'customer.name',
                'value' => function($model){
                    return $model->customer->name;
                }
            ],
            [
                'label' => Yii::t('common','Invoiced'),     
                'contentOptions' => ['class' => 'text-right'],     
                'headerOptions' => ['class' => 'text-right'],         
                'value' => function($model){
                    return $model->invoice ? number_format($model->invoice->total,2)  : 0;
                }
            ],
            [
                'label' => Yii::t('common','Debit'),
                'attribute' => 'balance_cheque',
                'contentOptions' => ['class' => 'text-right'],   
                'headerOptions' => ['class' => 'text-right'],           
                'value' => function($model){
                    return number_format($model->balance_cheque,2);
                }
            ],
            
            [
                'label' => Yii::t('common','Diff'), 
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],        
                'headerOptions' => ['class' => 'text-right'],               
                'value' => function($model){
                    $total = $model->balance_cheque - ($model->invoice ? $model->invoice->total : 0);
                    $color = '';
                    if($total > 0){
                        $color = 'text-success';
                    }else if($total < 0){
                        $color = 'text-red';
                    }
                    return '<div class="'.$color.'">'.number_format(($total),2).'</div>';
                }
            ],
            
            

        ],
        'pager' => [
          'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
          'prevPageLabel' => '«',   // Set the label for the "previous" page button
          'nextPageLabel' => '»',   // Set the label for the "next" page button
          'firstPageLabel'=> Yii::t('common','First'),   // Set the label for the "first" page button
          'lastPageLabel'=> Yii::t('common','Last'),    // Set the label for the "last" page button
          'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
          'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
          'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
          'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
          'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
          ],
         
    ]); ?>

</div>


<?php
$Yii = 'Yii';
$js =<<<JS
 
JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>







