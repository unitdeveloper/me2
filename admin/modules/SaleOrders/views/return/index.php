<?php

use yii\helpers\Html;
use kartik\grid\GridView;
//use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel admin\modules\SaleOrders\models\SaleReturnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('common', 'Return/Receive');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-return-header-index font-roboto" ng-init="Title='<?=$this->title?>'">


   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'columns' => [
            [
                'headerOptions' => ['class' => 'bg-dark'],
                'class' => 'yii\grid\SerialColumn'
            ],

            [
                'label' => Yii::t('common','Type'),
                'headerOptions' => ['class' => 'bg-gray'],
                'format' => 'raw',
                'value' => function($model){
                    return  $model->return_type == '1' ? ('<i class="fas fa-arrow-left text-red"></i> '.Yii::t('common', 'Send goods')) : ('<i class="fas fa-arrow-right text-green"></i> '.Yii::t('common', 'Receive goods'));
                }
            ],
            
            [
                
                'attribute'     => 'no',
                'headerOptions' => ['class' => 'bg-gray'],
                'value'         => function($model){
                    return $model->no;
                }
            ],
            //'customers.name',
            [
                'label'         => Yii::t('common','Customer'),
                'headerOptions' => ['class' => 'bg-gray'],
                'value'         => 'customers.name'
            ],
            [
                'label' => Yii::t('common','Sales'),
                'headerOptions' => ['class' => 'bg-gray'],
                'value' => 'sales.name'
            ],
            //'sale_address',
            //'bill_address',
            //'ship_address',
            //'order_date',
            //'ship_date',
            //'balance',
            //'balance_befor_vat',
            //'discount',
            //'percent_discount',
            //'update_status_date',
            //'create_date',
            //'paymentdue',
            //'sale_id',
            //'sales_people',
            //'vat_percent',
            //'ext_document',
            //'payment_term',
            //'vat_type',
            //'remark:ntext',
            //'transport',
            //'update_by',
            //'update_date',
            //'include_vat',
            //'confirm',
            //'confirm_by',
            //'release_date',
            //'confirm_date',
            //'shiped_date',
            //'comments:ntext',
            //'status',
            //'session_id',
            //'user_id',
            //'comp_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions'=>['class'=>'btn btn-default'],
                'headerOptions' => ['class' => 'hidden-xs bg-gray'],
                'filterOptions' => ['class' => 'hidden-xs'],
                'contentOptions' => ['class' => 'text-right','style'=>'min-width:280px;'],
                'template'=>'<div class="btn-group btn-group text-center" role="group"> {status} {view} {print}  {update}  {delete} </div>',
                'options'=> ['style'=>'width:300px;'],
                'buttons'=>[
                    'print' => function($url,$model,$key){     
                        if($model->customer_id==''){
                            return Html::button('<i class="fas fa-print"></i> ',['class'=>'btn btn-info-ew','disabled'=>true]);
                        }else{
                            return Html::a('<i class="fas fa-print"></i> ',$url,['class'=>'btn btn-info-ew','target'=>'_blank']);
                        }              
                        
                    },

                    'status' => function($url,$model,$key){  
                        switch ($model->status) {
                            case 'Release':
                                $status = '<i class="fas fa-lock text-yellow"></i> Release</a>';
                                break;
                            
                            case 'Open':
                                $status = '<i class="fas fa-lock-open text-green"></i> Open</a>';
                                break;

                            case 'Posted':
                                $status = '<i class="fa fa-server text-red"></i> Posted</a>';
                                break; 
                                    
                            default:
                                $status = ' ';
                                break;
                        }
                       

                        return '<div class="btn-group line-group" data-status="'.$model->status.'" role="group">
                                        <button type="button" class="btn  dropdown-toggle btn-default-ew '.($model->status == 'Release' ? 'text-yellow' : '').'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:105px">
                                        '.$status.'
                                        <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu option-list-menu">
                                            <li class="change-order-status line"><a href="#" class="status-open" data-key="Open"><i class="fas fa-lock-open text-green"></i> Open</a></li>
                                            <li class="change-order-status line"><a href="#" class="status-release" data-key="Release"><i class="fas fa-lock text-yellow"></i> Release</a></li>
                                            <li class=" "><a href="#" class="post-adjust" data-key="Adjust"><i class="fa fa-server text-red"></i>'.Yii::t('common','Post Adjust').'</a></li>
                                        </ul>
                                </div>';
                    },

                    'view' => function($url,$model,$key){
                        return Html::a('<i class="fas fa-eye"></i> ',$url,['class'=>'btn btn-default-ew']);
                    },
                    'delete' => function($url,$model,$key){
                        return Html::a('<i class="far fa-trash-alt"></i> ',$url,[
                            'class' => 'btn btn-danger-ew',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },

                    'update' => function($url,$model,$key){
                        if($model->status != 'Open'){
                            return Html::button('<i class="far fa-edit text-black"></i> ',['class'=>'btn btn-success-ew', 'disabled' => true]);
                        }else{
                            return Html::a('<i class="far fa-edit"></i> ',$url,['class'=>'btn btn-success-ew']);
                        }
                        
                    }

                ]
            ],
        ],
    ]); ?>
</div>


<?php
$Yii = 'Yii';
$js =<<<JS

 
const UpdateOrderStatus = (obj,callback) => {
    fetch("?r=SaleOrders/return/update-status", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
}

const postStock = (obj, callback) => {
    fetch("?r=SaleOrders/return/post-stock", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);     
    })
    .catch(error => {
        console.log(error);
    });
}

$('body').on('click','.change-order-status > a', function(){
        let id      = $(this).closest('tr').attr('data-key');
        let status  = $(this).attr('data-key');
        let that    = $(this);
        let text    = $(this).html();
        let caret   = ' <span class="caret"></span>';
        let oldVal  = $(this).closest('.line-group').attr('data-status');

        if(oldVal!==status){
            if(confirm("Are you sure?")){
                UpdateOrderStatus({id:id,status:status}, res =>{
                    console.log(text);
                    if(res.status===200){
                        that.closest('.btn-group').find('button').html(text + caret);
                        $.notify({
                            // options
                            icon: "fas fa-check-circle",
                            message: res.message
                            },{
                            // settings
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });  
                        that.closest('.line-group').attr('data-status', status);
                    }else{
                        
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: res.status===301 ? "right" : "center"
                            },
                            type: res.status===301 ? "info" : "error",
                            delay: 3000,
                            z_index: 3000
                        }); 
                    }
                    
                })
            }
        }
    })


    $('body').on('click', 'a.post-adjust', function(){
        let el      = $(this);
        let id      = $(this).closest('tr').attr('data-key');
        let oldVal  = $(this).closest('.line-group').attr('data-status');

        if(oldVal=='Posted'){
            alert('Posted');
        }else{
            if(confirm('Are you sure, Do you want to post?')){
                postStock({id:id}, res => {
                    if(res.status===200){
                        $.notify({
                            // options
                            icon: "fas fa-check-circle",
                            message: res.message
                            },{
                            // settings
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });   

                        el.closest('.line-group').find('button').html(' <i class="fa fa-server text-red"></i> Posted <span class="caret"></span>');
                        el.closest('.line-group').attr('data-status','Posted');
                    }else{
                        
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: res.status===301 ? "right" : "center"
                            },
                            type: res.status===301 ? "info" : "error",
                            delay: 3000,
                            z_index: 3000
                        }); 
                    }
                })
            }
        }
    })
JS;

$this->registerJS($js,\Yii\web\View::POS_END);