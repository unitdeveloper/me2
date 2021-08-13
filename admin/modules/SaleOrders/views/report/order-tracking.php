<?php

use yii\helpers\Html;
use kartik\grid\GridView;
  
$this->title = Yii::t('app', 'Sale Order Tracking');
$this->params['breadcrumbs'][] = $this->title;
 

?>
 
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">
    <div class="col-xs-12">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table  table-bordered', 'id' => 'export_table'],
            'columns' => [

                [
                    'headerOptions' => ['style' => 'font-family:roboto; width:10px;'],  
                    'class' => 'yii\grid\SerialColumn'
                ],

                [
                    'attribute' => 'order_date',
                    'label'     => Yii::t('common','Date'),
                    'contentOptions' => ['style' => 'font-family:roboto; width:100px;'],
                    'value' => function($model){
                        //return date('d/m/Y',strtotime($model->order_date));
                        return $model->order_date;
                    },
                ],
                [
                    'attribute' => 'customer_name', 
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'font-family:saraban; font-size:13px;'],                    
                    'value' => function($model){
                        $html = '<div>';
                        $html.= Html::a($model->customer->name,['/customers/customer/view', 'id' => $model->customer->id],['target' => '_blank', 'class' => 'mx-1']);
                        $html.= '</div>';
                               
                        return $html;       
                    },
                ],
                [
                    'label' => Yii::t('common','Sale Order'),
                    'attribute' => 'no',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width:130px;'],
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        $html = '<div>';
                        $html.= Html::a($model->no,['/SaleOrders/saleorder/view', 'id' => $model->id],['target' => '_blank', 'class' => 'mx-1']);   
                        $html.= '</div>';                     
                        $html.= '<div class="label label-primary">' . number_format($model->balance, 2) . '</div>';            
                        return $html;
                    }
                ],

                [
                    'label' => Yii::t('common','Production'),
                    //'attribute' => 'no',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width:180px;'],
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        $pdr  = '';                            
                        foreach ($model->produce as $key => $pd) {
                            $pdr.= '<div class="pdr-detail pointer mb-3 '.($pd->status == 'Produce' ? 'text-green' : 'text-red').'" data-key="'.$pd->id.'">'.$pd->DocumentNo.'</div>';
                        }
                        $html = '<div class="well">'.$pdr.'</div>';                        
                        return $html;                                           
                    }
                ],

                [
                    'label' => Yii::t('common','Shipment'),
                    //'attribute' => 'no',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'width:150px;'],
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                        $ship  = '';                                
                        foreach ($model->shiped as $key => $sh) {
                            $ship.= '<div><a href="#" class="ship-detail mb-3 '.($sh->status == 'Undo' ? 'text-gray' : 'text-info').'" data-key="'.$sh->id.'">'.$sh->DocumentNo.'</a></div>';
                        }
                        $html = '<div class="well">'.$ship.'</div>';
                        return $html;                                            
                    }
                ],

                [
                    'label' => Yii::t('common','Bill'),
                    //'attribute' => 'no',
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'font-family:roboto;'],
                    'value' => function($model){
                         
                        
                        $href   = '#';
                        $hrefId = '';

                        if($model->hasInvoice){
                            if($model->hasInvoice->status == 'Posted'){
                                $hrefId = base64_encode($model->hasInvoice ? $model->hasInvoice->id : '');
                                
                                $href   = '/accounting/posted/posted-invoice';
                            }else{
                                $hrefId = ($model->hasInvoice ? $model->hasInvoice->id : '');
                              
                                $href   = '/accounting/saleinvoice/update';
                            }                            
                        }                    

                        //return Html::a($model->hasInvoice ? $model->hasInvoice->no_ : '',[$href, 'id' => $hrefId],['target' => '_blank', 'class' => 'mx-1 '.$status]);                       
                         
                        $ship  = '';     
                        if($model->invoicing){                                                   
                            foreach ($model->invoicing as $key => $iv) {
                                //$ship.= '<div><a href="#" class="inv-detail mb-3 '.($iv->doc_type == 'Credit-Note' ? 'text-yellow' : 'text-info').'" data-key="'.$iv->id.'">'.$iv->no_.'</a></div>';
                                $ship.= '<div class="inv-detail mb-3 ">'. Html::a($iv->no_,[$href, 'id' => $hrefId],[
                                            'target' => '_blank', 
                                            'class' => ($iv->doc_type == 'Credit-Note' ? 'text-yellow' : 'text-info') ]). 
                                        '</div>';
                            }
                        }
                        $html = '<div class="well">'.$ship.'</div>';
                        return $html;    
                        
                    }
                ],

                // [
                //     'attribute' => 'balance',    
                //     'headerOptions' => ['class' => 'text-right'],  
                //     'contentOptions' => ['class' => 'text-right font-roboto'],               
                //     'value' => function($model){
                //         return number_format($model->balance,2);
                //     },
                // ],      
            
            ],
            'pager' => [
                'options'=>['class'=>'pagination'],             // set clas name used in ui list of pagination
                'prevPageLabel' => '«',                         // Set the label for the "previous" page button
                'nextPageLabel' => '»',                         // Set the label for the "next" page button
                'firstPageLabel'=> Yii::t('common','First'),    // Set the label for the "first" page button
                'lastPageLabel'=> Yii::t('common','Last'),      // Set the label for the "last" page button
                'nextPageCssClass'=>'next',                     // Set CSS class for the "next" page button
                'prevPageCssClass'=>'prev',                     // Set CSS class for the "previous" page button
                'firstPageCssClass'=>'first',                   // Set CSS class for the "first" page button
                'lastPageCssClass'=>'last',                     // Set CSS class for the "last" page button
                'maxButtonCount'=>10,                           // Set maximum number of page buttons that can be displayed
            ],
            'options' => [
                'class' => 'table ',
            ],
            'responsiveWrap' => false // Disable Mobile responsive    
        ]); ?>
    </div>
</div>


<div class="modal fade modal-full" id="modal-pdr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Production Order')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>                 
            </div>
        </div>
    </div>
</div>





<?php
$Yii                = 'Yii';
$LABEL_CODE         = Yii::t('common','Code');
$LABEL_NAME         = Yii::t('common','Name');
$LABEL_TYPE         = Yii::t('common','Type');
$LABEL_QUANTITY     = Yii::t('common','Quantity');
$LABEL_CANCEL       = Yii::t('common','Undo');
$LABEL_REMAIN       = Yii::t('common','Remain');
$LABEL_PD_TITLE     = Yii::t('common','Production Order');
$LABEL_SH_TITLE     = Yii::t('common','Shipment');

$js=<<<JS

    const getProductionOrder = (obj, callback) => {
        fetch("?r=Manufacturing/default/bom-detail", {
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

    const renderTable = (obj) => {
        let data = obj.raws;
        let html  = `<div><h4><span class="pdr-no">` + obj.no + `</span></h4></div>`;
            html += `<div><h5><span class="pdr-desc" style="font-family: saraban;">` + obj.desc + `</span></h5></div>`;
            html += `<table class="table table-bordered">
                        <thead>
                            <tr class="bg-gray" style="font-family: saraban; font-size:14px;">
                                <th style="width:150px;">${LABEL_CODE}</th>
                                <th>${LABEL_NAME}</th>
                                <th>${LABEL_TYPE}</th>
                                <th class="text-right"  style="width:150px;">${LABEL_QUANTITY}</th>
                                <th class="text-right" >${LABEL_REMAIN}</th>
                            </tr>
                        </thead>`;
            html += "<tbody>";

        data.length > 0
            ? data.map(model => {
                html += `<tr data-key="` + model.id + `" class="` + (model.qty < 0 ? 'bg-warning' : 'bg-success') + `" data-id="` + model.item + `">
                            <td class="code font-roboto"><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">` + model.code + `</a></td>
                            <td style="font-family: saraban; font-size:14px;">` + model.name + `</td>
                            <td style="font-family: saraban; font-size:14px;" class="type">` + model.type + `</td>
                            <td class="text-right font-roboto">
                                <a href="?WarehouseSearch[ItemId]=` + btoa(model.item) + `&r=warehousemoving%2Fwarehouse" 
                                target="_blank"
                                class="` + (model.qty < 0 ? 'text-red' : 'text-green') + `">` + model.qty + `</a>
                            </td>
                            <td class="text-right font-roboto ">` + number_format(model.remain > 0 ? model.remain : '') + `</td>
                        </tr>`;
                    }
                )
            : null;

        html += "</tbody>";
        html += "</table>";
        html += `<div class="text-right">
                    <div class="undo-pdr">
                        <a href="#" class="btn btn-app btn-danger  ew-undo-pdr" data-key="` + obj.id + `"><i class="fa fa-undo"></i>${LABEL_CANCEL}</a>
                    </div>
                </div>`;
        $("body").find('#modal-pdr .modal-body').html(html);        
    }

    $('body').on('click', '.pdr-detail' , function(){
        let id = parseInt($(this).attr('data-key'));
        getProductionOrder({id:id}, res => {
            if(res.status === 200){
                renderTable(res);
                $("body").attr("style", "overflow:hidden; margin-right:0px;"); // Modal on top
                $("#modal-pdr").modal("show");
                $("body").find('#modal-pdr .modal-title').html(`${LABEL_PD_TITLE}`);
            }
        })
    })

    $('body').on('click', '.ship-detail' , function(){
        let id = parseInt($(this).attr('data-key'));
        getProductionOrder({id:id}, res => {
            if(res.status === 200){
                renderTable(res);
                $("body").attr("style", "overflow:hidden; margin-right:0px;"); // Modal on top
                $("#modal-pdr").modal("show");
                $('.ew-undo-pdr').hide();
                $("body").find('#modal-pdr .modal-body').append(`<div class="mt-10"> ` + (res.remark ? res.remark : ' ') + ` </div>`);
                $("body").find('#modal-pdr .modal-title').html(`${LABEL_SH_TITLE}`);
            }
        })
    })


    let undoPdr = (obj, callback) => {
        fetch("?r=Manufacturing/default/bom-revert", {
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

    $("body").on('click', 'a.ew-undo-pdr', function(){
        if(confirm("OK?")){
            let id = parseInt($(this).attr('data-key'));
            undoPdr({id:id}, res => {
                if(res.status===200){
                    renderTable(res);
                }else{
                    $.notify({
                        // options
                        icon: 'fas fa-exclamation',
                        message: res.message
                    },{
                        // settings
                        type: 'warning',
                        delay: 1000,
                        z_index:3000,
                    });
                }
            })
        }
    })
 
JS;

$this->registerJS($js,\yii\web\View::POS_END);
?>
 
 