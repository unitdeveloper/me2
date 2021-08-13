<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
//use common\models\RcInvoiceLine;

//use admin\modules\accounting\models\FunctionAccounting;
/* @var $this yii\web\View */
/* @var $searchModel admin\modules\accounting\models\SaleinvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<style type="text/css">
    .ew-table tr{
        cursor: pointer;
    }
</style>
<?php 
/*
<div class="row" >
    <div class="col-xs-12">
       
        <div class="col-xs-2 pull-right text-center">
           <?=Yii::t('common','รับเกิน')?> <div class="btn bg-info btn-xs"><span class="text-danger">⌃</span> </div>
        </div>
        <div class="col-xs-2 pull-right text-center">
           <?=Yii::t('common','รับครบ')?> <div class="btn bg-success btn-xs"><i class="fa fa-check text-success" aria-hidden="true"></i> </div>
        </div>
        <div class="col-xs-2 pull-right text-center">
           <?=Yii::t('common','รับไม่ครบ')?> <div class="btn bg-warning btn-xs"><i class="fa fa-question text-info" aria-hidden="true"></i> </div>
        </div>

         <div class="col-xs-2 pull-right text-center">
           <?=Yii::t('common','ยังไม่รับ')?> <div class="btn btn-default btn-xs"><i class="fa fa-btc" aria-hidden="true"></i> </div>
        </div>
    </div>
    <div class="col-xs-12 ">
        <?=Yii::t('common','From Date')?> : <span class="text-red"><?=date('Y-m-d',strtotime($fdate))?> </span>
        <?=Yii::t('common','To Date')?> :  <span class="text-red"><?=date('Y-m-d',strtotime($tdate))?></span>
    </div>
</div>
*/
?>
<h3>เฉพาะที่ <span class="text-orange">โพส (Posted)</span> แล้วเท่านั้น</h3>
<small>หากไม่เจอรายการ ให้ไปดู <a href="?r=accounting%2Frcreport%2Findex" target="_blank">ใบกำกับภาษี</a> ว่ายังไม่โพสหรือไม่ </small>
<div class="sale-invoice-header-index">
    <div class="table-responsive">
    <?php //Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'ew-table table table-hover'],
        'pjax' => true,
        'rowOptions' => function($model){

                    //$query    = \common\models\Cheque::find()->where(['apply_to' => $model->id]);

                    // if($model->status=='Posted'){

                    //     $sumLine  = FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');

                    // }else{
                      
                    //     $sumLine  = $model->getTotalBalance($model,'All','Equal');

                    // } 

                    // if($query->exists()){

                    //     $Cheque = $query->all();


                    //     $sumBalance = 0;
                        
                    //     foreach ($Cheque as $key => $models) {

                    //         $sumBalance += $models->balance;

                    //     }

                    //     if($sumBalance > $sumLine){

                    //         return ['class' => 'info'];

                    //     }else if($sumBalance == $sumLine){

                    //         return ['class' => 'success'];
                    //     }else if($sumBalance < $sumLine){

                    //         return ['class' => 'warning'];
                    //     }


                    // }

                },
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['class' => 'text-center bg-dark', 'style' => 'width:50px;'],
                'contentOptions' => function($model){
                    if($model->status == 'Open'){
                      return ['class' => 'alert-warning text-center'];
                    }else {
                      return ['class' => 'alert-info text-center'];
                    }
                    
                },
            ],

            //'id',
            //'no_',
            //'posting_date',
            [
                //'attribute' => 'posting_date',
                'label' => Yii::t('common','Posting date'),
                'headerOptions' => ['class' => 'bg-gray', 'style' => 'width:110px;'],
                //'format' => 'raw',
                'value' => function($model){
                    return date('d/m/Y',strtotime($model->posting_date));

                }

            ],
            
            [
                //'attribute' => 'no_',
                'label' => Yii::t('common','Document No'),
                'headerOptions' => ['class' => 'bg-gray'],
                'contentOptions' => ['class' => 'bg-gray'],
                'format' => 'raw',
                'value' => function($model){
                    //return Html::a($model->no_,['posted-invoice','id' => base64_encode($model->id)]);
                    return $model->no_;
                }

            ],

            [
                //'attribute' => 'no_',
                'label' => Yii::t('common','Reference'),
                'headerOptions' => ['class' => 'bg-gray'],
                'format' => 'raw',
                'value' => function($model){                    
                    //return $model->field->ext_document;
                    return $model->ext_document;
                }

            ],

            
            //'cust_no_',
            [
                //'attribute' => 'cust_no_',
                'label' => Yii::t('common','Customer'),
                'headerOptions' => ['class' => 'bg-gray'],
                'format' => 'raw',
                'value' => function($model){
                    // if($model->status == 'Posted'){
                    //     return Html::a($model->cust_name_,['posted-invoice','id' => base64_encode($model->id)],['target' => '_blank', 'data-pjax' => "0"]);
                    // }else{
                    //     return $model->cust_name_;
                    // }
                    return $model->cust_name_;

                }

            ],
            /*
            [
                'label' => Yii::t('common','Status'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center bg-gray'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    $query    = \common\models\Cheque::find()->where(['apply_to' => $model->id]);

                    if($model->status=='Posted'){

                        $sumLine  = FunctionAccounting::getTotalBalance($model,'RcInvoiceLine');

                    }else{
                      
                        $sumLine  = $model->getTotalBalance($model,'All','Equal');

                    } 
                    

                    $status = '<i class="fa fa-btc" aria-hidden="true"></i> ';
                    if($query->exists()){

                        $Cheque = $query->all();


                        $sumBalance = '0';

                        foreach ($Cheque as $key => $models) {

                            $sumBalance += $models->balance;

                        }

                        if($sumBalance > $sumLine){

                            $status = '<span class="text-danger">⌃</span> ';

                        }else if($sumBalance == $sumLine){
                            // ⌄
                            $status =  '<i class="fa fa-check text-success" aria-hidden="true"></i> ';
                        }else if($sumBalance < $sumLine){
                            // ⌄
                            $status =  '<i class="fa fa-question text-info" aria-hidden="true"></i> ';
                        }


                    }
                    return $status. $model->doc_type;
                }
            ],
            */
            [
                
                'label' => Yii::t('common','Balance'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right bg-gray'],
                'contentOptions' => ['class' => 'text-right bg-gray'],
                'value' => function($model){
                    // $invLine = RcInvoiceLine::find()->where(['source_id' => $model->id]);
                    // $sumLine = $invLine->sum('quantity * unit_price');
                   

                    // $sumLine = $model->getTotalBalance($model,'All','Equal');

                    // return number_format($sumLine,2);
                    return number_format($model->sumtotals->total,2);

                }

            ],

            [

                'attribute' => 'Get',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center bg-gray'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function($model){
                    //$sumLine = $model->getTotalBalance($model,'All','Equal');
                    $sumLine = $model->sumTotals->total;
                    return "<input type='checkbox' class='ew-checked' name='inv[]' data='{$model->cust_no_}' row-data='{$model->id}' bal='{$sumLine}' status='{$model->status}'>";
                }
            ],
            

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php //Pjax::end(); ?>
    </div>
</div>
<script type="text/javascript">
    // $(document).ready(function() {
      
    //   $('.ew-table tr').click(function(event) {
    //     if (event.target.type !== 'checkbox') {

    //       $(':checkbox', this).trigger('click');
    //       $(this).removeClass('bg-yellow');
           
    //     }else{
    //         $(this).addClass('bg-yellow');
    //     }

    //     console.log($(this).find('input[name="inv[]"]'));
         
    //   });
    // });


     
</script>
