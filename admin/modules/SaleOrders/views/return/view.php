<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\SaleReturnHeader */

$this->title = $model->no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sale Return Headers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sale-return-header-index" ng-init="Title='<?=$this->title?>'">
   
    <div class="row">
        
        <div class="col-xs-12">
            <h3><?=$model->return_type == 1 ? Yii::t('common', 'Return to customer') : Yii::t('common', 'Return from customer'); ?></h3>
        </div>
        <div class="row">
            
            <div class="col-xs-6 mt-10 mb-10">
                <div class="row">
                    <div class="col-xs-12 img-return" style="<?=$model->return_type==1 ? ' ' : 'display: none;';?>">
                        <div class="col-xs-4"><i class="fas fa-store-alt fa-4x text-green"></i>  </div>
                        <div class="col-xs-4 text-center"><i class="fas fa-arrow-left fa-4x text-yellow"></i></div>
                        <div class="col-xs-4 text-right"><i class="fas fa-cube fa-4x "></i></div> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 img-receive" style="<?=$model->return_type==2 ? ' ' : 'display: none;';?>">
                        <div class="col-xs-4"><i class="fas fa-cube fa-4x  "></i></div>
                        <div class="col-xs-4 text-center"><i class="fas fa-arrow-right fa-4x text-yellow"></i></div>
                        <div class="col-xs-4 text-right"><i class="fas fa-industry fa-4x text-green"></i></div> 
                    </div>
                </div>                            
            </div>
        </div>
        <div class="col-sm-12"><h4><?=$this->title?></h4></div>
        <div class="col-sm-12"><?=($model->customers ? $model->customers->name : '')?></div>
    </div>
    

    <div class="SaleLine">
        <?php $gridColumns = [
            [
                'class' => 'yii\grid\SerialColumn',                                    
                'headerOptions' => ['class' => 'bg-info text-right','style' => 'width:30px;'],
                'contentOptions'  => ['class' => 'bg-info','style' => 'vertical-align: middle;'],
            ],

            [
                'label' => Yii::t('common','Items'),
                'format' => 'raw',
                'contentOptions' => ['class' => 'font-roboto','style' => 'vertical-align: middle;'],
                'headerOptions' => ['class' => ' ','style' => 'width:150px;'],
                'value' => function($model){
                    return Html::a($model->crossreference->no, ['/items/items/view', 'id' => $model->items->id], ['target' => '_blank']);
                }
            ],

            [
                'label' => Yii::t('common','Description'),
                'format' => 'raw',
                'headerOptions' => ['class' => ' ','style' => 'min-width:200px;'],
                'contentOptions' => ['class' => 'font-roboto','style' => 'vertical-align: middle;'],
                'value' => function($model){                     
                    return $model->description;
                }               
            ],

            [
                'label' => Yii::t('common','Stock'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right hidden-xs','style' => 'width:100px;'],
                'contentOptions' => ['class' => 'text-right   hidden-xs font-roboto text-gray'],
                'value' => function($model){
                    return number_format($model->items->invenByCache);
                }                 
            ],

            [
                'label' => Yii::t('common','Quantity'),
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right ','style' => 'max-width:100px;'],
                'contentOptions' => ['class' => 'text-right bg-yellow font-roboto'],
                'value' => function($model){
                    return number_format($model->quantity);
                }                 
            ],

            [
                'label' => Yii::t('common','Measure'),
                'headerOptions' => ['class' => 'hidden-xs' ,'style' => 'max-width:80px;'],
                'contentOptions' => ['class' => 'hidden-xs','style' => 'vertical-align: middle;'],
                'value' => 'items.UnitOfMeasure'
            ],     
        ];
        ?>
        
        <?=GridView::widget([
                'dataProvider'=> $dataProvider,              
                'showFooter' => false,
                'headerRowOptions'=>['class'=> ($model->return_type == 1 ? 'bg-green' : 'bg-dark')],
                'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
                'columns' => $gridColumns,
                'summary' => false,
                'tableOptions' => [
                    'class' => 'table table-bordered '.($model->status=='Posted' ? 'table-warning' : '')
                ]
            ]);
        ?>
    </div>
    <div class="row ">
        <div class="col-sm-6">
            <div class="well"><?=$model->remark?></div>
        </div>
    </div>
    <p>
        <div class="row">
            <div class="col-xs-6 <?=($model->status != 'Open' ? 'hidden' : '')?>">
             
                    <?= Html::a('<i class="fa fa-trash"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger-ew',
                        'data' => [
                            'confirm'   => Yii::t('common', 'Are you sure you want to delete this item?'),
                            'method'    => 'post',
                        ],
                    ]) ?>
            </div>
            <div class="col-xs-6 text-right <?=($model->status != 'Open' ? 'hidden' : '')?>">
                
                <div class="btn-group line-group <?=($model->status=='Posted' 
                                                        ? 'hidden' 
                                                        : ($model->countLine > 0 ? '' : 'hidden')
                                                    )?>" data-status="<?=$model->status?>" role="group">
                    <a href="#" class="post-adjust btn btn-danger" data-key="Adjust"><i class="fa fa-server "></i> <?=Yii::t('common','Post Adjust')?></a>
                </div>
            </div>
        </div>
    </p>
</div>

<?php if($model->status != 'Open') { ?>
<div><h3>รายละเอียดการตัดสต๊อก</h3></div>
<?php 
    function fetTable($model){
        $Html = '';
        foreach ($model->moving as $key => $line) {
             
            $Html.= '<div class="row '.($line->TypeOfDocument=='Output' ? 'bg-success' : '').'">
                        <div class="col-xs-3">'.($key + 1).'). '.(Html::a($line->items->master_code, ['/items/items/view', 'id' => $line->item], ['target' => '_blank'])).'</div>
                        <div class="col-xs-7">'.$line->Description.'</div>
                        <div class="col-xs-2 text-right ">'.(Html::a(($line->Quantity * 1), ['/warehousemoving/warehouse', 'WarehouseSearch[ItemId]' => base64_encode($line->item)], ['target' => '_blank'])).'</div>
                    </div>';
        }
                
        return $Html;
    }
?>
<?=GridView::widget([
        'dataProvider'=> $produce,              
        'showFooter' => false,
        'headerRowOptions'=>['class'=>'bg-gray'],
        'footerRowOptions'=>['style'=>'font-weight:bold; text-align:right;'],
        'columns' => [
            [
                'label' => Yii::t('common','Date'),
                'attribute' => 'DocumentNo', 
                'value' => function($model){
                    return date('Y-m-d', strtotime($model->PostingDate));
                }
            ],
            [
                'label' => Yii::t('common','Type'),
                'attribute' => 'DocumentNo', 
                'value' => function($model){
                    return Yii::t('common', $model->TypeOfDocument);
                }
            ],
            [
                //'label' => Yii::t('common','Items'),
                'attribute' => 'DocumentNo', 
                'value' => 'DocumentNo'
            ],
            [
                'label' => Yii::t('common','Items'),
                'attribute' => 'item',
                'format' => 'raw',
                'value' => function($model){                     
                    return fetTable($model);
                }
            ],
            
            
        ],
        'summary' => false,
        'tableOptions' => [
            'class' => 'table table-bordered font-roboto' 
        ]
    ]);
?>
<?php } ?>

<div id="errors"></div>

<?php
$Yii    = 'Yii';
$id     = $model->id;
$status = $model->status;
$js =<<<JS

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
 
$('body').on('click', 'a.post-adjust', function(){

    let oldVal  = $(this).closest('.line-group').attr('data-status');

    if('{$status}'=='Posted'){
        alert('Posted');
    }else{
        if(confirm('Are you sure, Do you want to post?')){
            postStock({id:'{$id}'}, res => {
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
                    setTimeout(function() {window.location.href ="index.php?r=SaleOrders%2Freturn%2Fview&&id={$id}";}, 1000);
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
                     
                    $('#errors').html(res.produce[0][0].error);
                }
            })
        }
    }
})



JS;

$this->registerJS($js,\Yii\web\View::POS_END);