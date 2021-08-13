<?php

use yii\helpers\Html;
use kartik\grid\GridView;
 
  
$this->title = Yii::t('app', 'Sale Order');
$this->params['breadcrumbs'][] = $this->title;
 
?>
<style>
    
@media (max-width: 425px) {
    #modal-pdr iframe{
        width:200px !important;
        height:200px !important;
    }
}
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>



<?=$this->render('_script_inv_list')?>


<?=$this->render('_header',['model' => $searchModel])?>
<div class="row" ng-init="Title='<?= Html::encode($this->title) ?>'">

    <?php 
        $columns = [
            [
                'headerOptions' => ['class' => 'bg-primary'],
                'contentOptions'=> ['class' => 'bg-gray'],
                'class' => 'yii\grid\SerialColumn'
            ],
            // [
            //     'attribute' => 'order_date',
            //     'label'     => Yii::t('common','Date'),
            //     'contentOptions' => ['style' => 'font-family:roboto; width:100px;'],
            //     'value' => function($model){
            //         return date('d/m/Y',strtotime($model->order_date));
            //     },
            // ],

            [
                'label'     => Yii::t('common','Customer'),
                //'attribute' => 'customer_name',
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-dark', 'style' => 'min-width:120px;'],   
                'contentOptions' => ['class' => 'bg-white'],
                'value' => function($model){

                    $dell =  Html::a('<i class="far fa-trash-alt"></i> ',['delete','order' => $model->id],[
                                'class' => 'btn btn-danger-ew btn-sm',
                                'data' => [
                                    'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]);

                    $name = $model->customer->nick_name ?: $model->customer->name;

                    $html = '<div class="mb-10">'. Yii::t('common','Date').' : '. date('d/m/Y',strtotime($model->order_date)). '</div>';
                    $html.= '<div>'. Html::a($name,['/customers/customer/view', 'id' => $model->customer_id],['target' => '_blank', 'title' => $model->customer->name]) . '</div>';
                    $html.= '<div ><small>PO : <span class="text-danger">'.$model->ext_document.'</span></small></div>';
                    $html.= '<div style="width:30px;">';
                    if($model->transportShip){
                        $html.= '<div class="pointer mt-10 add-to-ship" data-type="1"><i class="fas fa-truck text-info" style="min-width:15px;"></i></div>';
                    }else{
                        $html.= '<div class="pointer mt-10 add-to-ship" data-type="0"><i class="fas fa-truck text-gray" style="min-width:15px;"></i></div>'; 
                    }
                    $html.= '</div>';
                    
                    $html.= '<div style="margin-top:10px;">'.$dell.'</div>';
                    
                   return $html;
                }
            ],

            [
                'attribute' => 'no',
                'format' => 'raw',
                'headerOptions' => ['class' => 'bg-dark', 'style' => 'min-width:210px;'],
                'contentOptions' => ['class' => 'bg-white', 'style' => 'font-family:roboto;'],
                'value' => function($model){
                    $html = '<div class="row" data-key="'.$model->id.'">';
                    $html.= '   <div class="col-xs-10">'.Html::a('<i class="fa fa-print"></i> '.$model->no,'#',['class' => 'show-print']).'</div>';
                    $html.= '   <div class="col-xs-2">'.Html::a('<i class="fas fa-edit text-yellow"></i> ',['/SaleOrders/reserve/update', 'id' => $model->id],['target' => '_blank', 'class' => 'mx-1']).'</div>';
                    $html.= '</div>';  
   
                    $no = '<div class="well" style="background:#bff;">
                                <div>'.Yii::t('common','Sale Order').'</div>
                                <div>'.$html.'</div>
                            </div>';  

                    $pdr  = '';   
                                      
                    foreach ($model->production as $key => $pd) {
                        $pdr.= '<div class="row">';
                        $pdr.= '    <div class="col-xs-10 mb-10 pdr-detail pointer mb-3 text-info" data-key="'.$pd->id.'">'.(
                                        Html::a(' <i class="far fa-file-alt text-yellow"></i> ' .$pd->no,'#',[
                                                'class' => 'print-pdr',
                                                'data-key' => $pd->id
                                                //'target' => "_blank"
                                        ])).'
                                    </div>';
                        $pdr.= '    <div class="col-xs-2 mb-10 text-right">'.(
                                            Html::a('<i class="fa fa-times"></i> ','#',[
                                                    'class'     => 'text-red delete-pdr',
                                                    'data-key'  => $pd->id,
                                                    'data-no' => $pd->no
                                            ])).'
                                    </div>';
                        $pdr.= '</div>';
                    }
                    
                    if(count($model->production) > 0){ 
                        $no.= '<div class="well" style="background:rgb(242 255 248);">
                                <div>'.Yii::t('common','Production Order').'</div>
                                <div>'.$pdr.'</div>
                            </div>';   
                    }
                    return $no;
                }
            ],

            /*
            [
                'label' => Yii::t('common','Production Order'),
                'format'        => 'raw', 
                'headerOptions' => ['class' => 'bg-dark', 'style' => 'min-width:210px;'],
                'contentOptions'=> ['class' => 'bg-white production-list'],
                'value' => function($model){
                    $pdr  = '';   
                    //$pdr = Html::a('<i class="fas fa-file-invoice"></i> '.Yii::t('common','Create PDR.'), '#', ['class' => 'btn btn-warning-ew btn-sm create-pdr-btn']);                            
                    foreach ($model->production as $key => $pd) {
                        $pdr.= '<div class="row">';
                        $pdr.= '    <div class="col-xs-10 mb-10 pdr-detail pointer mb-3 text-info" data-key="'.$pd->id.'">'.(
                                        Html::a(' <i class="far fa-file-alt text-yellow"></i> ' .$pd->no,'#',[
                                                'class' => 'print-pdr',
                                                'data-key' => $pd->id
                                                //'target' => "_blank"
                                        ])).'
                                    </div>';
                        $pdr.= '    <div class="col-xs-2 mb-10 text-right">'.(
                                            Html::a('<i class="fa fa-times"></i> ','#',[
                                                    'class'     => 'text-red delete-pdr',
                                                    'data-key'  => $pd->id,
                                                    'data-no' => $pd->no
                                            ])).'
                                    </div>';
                        $pdr.= '</div>';
                    }
                    $html = '<div class="well">'.$pdr.'</div>';                        
                    return $html;           
                }
            ],
*/
            [
                'label'         => Yii::t('common','Tax Invoice'),   
                'format'        => 'raw', 
                'headerOptions' => ['class' => 'bg-dark', 'style' => 'min-width:210px;'],  
                'contentOptions'=> ['class' => 'tax-invoice'],               
                'value' => function($model){                        
                    
                    $inv  = '';     
                    if($model->confirm > 0){
                    
                        if($model->invoicing){                                                   
                            foreach ($model->invoicing as $key => $iv) {  
                                if($iv->ship_all == 0){
                                    $inv.= '<div style="position:absolute; top:0px; left:0px; width:40px; background:blue; height:100%;"></div>';
                                }  

                                $inv.= '<div class="row invoice-div" data-key="'.base64_encode($iv->id).'" data-id="'.$iv->id.'" data-status="'.$iv->status.'" data-locked="'.$iv->locked.'">';
                                $inv.= '   <div class="col-xs-8">'. Html::a('<i class="fas fa-print text-red"></i> '.$iv->no_,'#',['class' => 'show-print-inv']). 
                                            '</div>';
                                $inv.= '   <div class="col-xs-4 text-right">
                                            <a href="#" type="button" class="delete-line-line-moderntrade pull-left"><i class="fas fa-times text-red"></i></a>
                                            '.Html::a('<i class="fas fa-edit text-yellow"></i> ',['/accounting/posted/posted-invoice', 'id' => base64_encode($iv->id)],[
                                                'target' => '_blank', 
                                                'class' => ($iv->doc_type == 'Credit-Note' ? 'text-yellow pull-right' : 'text-info pull-right') ]).'</div>';
                                $inv.= '</div>';       
                                
                                
                            }
                            $html = '<div class=" ">'.$inv.'</div>';
                        }else{
                            $html = Html::a('<i class="fas fa-file-invoice"></i> '.Yii::t('common','Create Invoice.'), '#', ['class' => 'btn btn-danger-ew btn-sm create-invoice-btn']);
                            if($model->reserve_inv_no!=''){
                                $html.= '<div><small> จอง : '.$model->reserve_inv_no.'</small></div>';   
                            }
                            
                        }

                    }else{
                        $html = '<div class="text-yellow">'.Yii::t('common','Waiting Confirm').'</div>';
                    }
                    
                    return '<div class="well" style="position:relative;">'.$html.'</div>';  
                    
                },
            ],

            /*
            $AutoCutStock ?
            [
                'label'         => '',
                'contentOptions' => ['class' => 'hidden'],
                'headerOptions' => ['class' => 'hidden']
            ] : */ 
            [
                'label'         => Yii::t('common','Cut off stock'),   
                'format'        => 'raw', 
                'headerOptions' => ['class' => 'bg-dark ', 'style' => 'min-width:150px;'],
                'contentOptions'=> ['class' => 'stock-list'],
                'value' => function($model) use ($AutoCutStock){
                    $html   = '';
                     

                    if($model->confirm > 0){
                        if(Yii::$app->user->identity->id==1){
                            $html.= Html::a('<i class="fas fa-cubes"></i> '.Yii::t('common','Cut off stock'), '#', ['class' => 'btn btn-success-ew btn-sm create-custoffstock-btn']);
                        }else{
                            if(!$AutoCutStock){
                                $html.= Html::a('<i class="fas fa-cubes"></i> '.Yii::t('common','Cut off stock'), '#', ['class' => 'btn btn-success-ew btn-sm create-custoffstock-btn']);   
                            }
                        }
                        

                        if($model->shipment!=null){
                            foreach ($model->shiped as $key => $sh) {
                                $html.=  '<div><a href="#" class="ship-detail mb-3 '.(in_array($sh->status,['Undo','Undo-Shiped']) ? 'text-gray' : 'text-info').'" data-status="'.$sh->status.'" data-key="'.$sh->id.'">'.($key + 1 . ') '.$sh->DocumentNo).'</a></div>';
                            }
                        }
                    }else{
                        $html = '<div class="text-yellow">'.Yii::t('common','Waiting Confirm').'</div>';
                    }

                        
                    $stock = '<div class="well">
                                <div>'.Yii::t('common','Cut off stock').'</div>
                                <div>'.$html.'</div>
                            </div>';
                    

                    $pdr  = '';                            
                    foreach ($model->produce as $key => $pd) {
                        $pdr.= '<div class="pdo-detail pointer mb-3 '.($pd->status == 'Produce' ? 'text-info' : 'text-gray').'" data-status="'.$pd->status.'" data-key="'.$pd->id.'">'.($key + 1 . ') ' .$pd->DocumentNo).'</div>';
                    }

                    
                    $html = '<div class="well" >
                                <div>'.Yii::t('common','Production').'</div>
                                <div>'.$pdr.'</div>
                            </div>';                        
                     
                    return $stock.$html;         

                },
            ] ,

            // [
            //     'label' => Yii::t('common','Production'),
            //     //'attribute' => 'no',
            //     'format' => 'raw',
            //     'headerOptions' => ['style' => 'min-width:170px;'],
            //     'contentOptions' => ['style' => 'font-family:roboto;'],
            //     'value' => function($model){
            //         $pdr  = '';                            
            //         foreach ($model->produce as $key => $pd) {
            //             $pdr.= '<div class="pdo-detail pointer mb-3 '.($pd->status == 'Produce' ? 'text-info' : 'text-gray').'" data-key="'.$pd->id.'">'.($key + 1 . ') ' .$pd->DocumentNo).'</div>';
            //         }
            //         $html = '<div class="well">'.$pdr.'</div>';                        
            //         return $html;                                           
            //     }
            // ],

            // [
            //     'attribute' => 'balance',    
            //     'headerOptions' => ['class' => 'text-right'],  
            //     'contentOptions' => ['class' => 'text-right font-roboto'],               
            //     'value' => function($model){
            //         return number_format($model->balance,2);
            //     },
            // ], 
            /*          
            [

                'class' => 'yii\grid\ActionColumn',
                'buttonOptions' => ['class'=>'btn btn-default'],
                'headerOptions' => ['class' => 'bg-dark'],
                'contentOptions'=> ['class' => 'text-right'],
                'template'      => '<div class="btn-group btn-group text-center" role="group">{delete}   </div>',
                'options'       => ['style'=>'width:50px;'],
                'buttons'       => [
                    'invoice' => function($url,$model,$key){

                        return Html::a('<i class="fas fa-arrow-right"></i> ',['invoice','order' => $model->id],[
                            'class' => 'btn btn-info-ew btn-sm',
                            'data' => [
                                'confirm' => Yii::t('common', 'Next to invoice?'),
                                'method' => 'post',
                            ],
                        ]);
                    },
                    
                    'delete' => function($url,$model,$key){

                        return Html::a('<i class="far fa-trash-alt"></i> ',['delete','order' => $model->id],[
                            'class' => 'btn btn-danger-ew btn-sm',
                            'data' => [
                                'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]);
                    },

                ]
            ],*/
        ];
    ?>
    <?php /* if($confirmed->getTotalCount() > 0) { ?>
        <div class="col-xs-12 font-roboto" style="margin-bottom:150px;">            
            <h3 class="text-success"><?=Yii::t('common','Confirmed')?></h3>
            <hr class="style19" />
            <?= GridView::widget([
                'dataProvider'  => $confirmed,
                'columns'       => $columns,
                'pager'         => [
                    'options'           => ['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel'     => '«',   // Set the label for the "previous" page button
                    'nextPageLabel'     => '»',   // Set the label for the "next" page button
                    'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
                    'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
                    'nextPageCssClass'  => 'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass'  => 'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass'  => 'last',    // Set CSS class for the "last" page button
                    'maxButtonCount'    => 10,    // Set maximum number of page buttons that can be displayed
                ],
                'options' => [
                    'class' => 'table ',                
                ],            
                //'pjax'=>true,   
                'responsive' => true, 
                'responsiveWrap' => false, // Disable Mobile responsive                    
            ]); ?>        
        </div>
    <?php } */ ?>

    <?php /* if($checking->getTotalCount() > 0) { ?>
        <div class="col-xs-12 font-roboto" style="margin-bottom:150px;">
            <h3 class="text-yellow"><?=Yii::t('common','Waiting Confirm')?></h3>
            <hr class="style19" />
            <?= GridView::widget([
                'dataProvider'  => $checking,
                'columns'       => $columns,
                'pager'         => [
                    'options'           => ['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel'     => '«',   // Set the label for the "previous" page button
                    'nextPageLabel'     => '»',   // Set the label for the "next" page button
                    'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
                    'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
                    'nextPageCssClass'  => 'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass'  => 'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass'  => 'last',    // Set CSS class for the "last" page button
                    'maxButtonCount'    => 10,    // Set maximum number of page buttons that can be displayed
                ],
                'options' => [
                    'class' => 'table ',                
                ],            
                //'pjax'=>true,   
                'responsive' => true, 
                'responsiveWrap' => false, // Disable Mobile responsive    
                    
            ]); ?>
        </div>
    <?php } */ ?>

    

        <div class="col-xs-12 font-roboto">
             
            <?= GridView::widget([
                'dataProvider'  => $dataProvider,
                'columns'       => $columns,
                'pager'         => [
                    'options'           => ['class'=>'pagination'],   // set clas name used in ui list of pagination
                    'prevPageLabel'     => '«',   // Set the label for the "previous" page button
                    'nextPageLabel'     => '»',   // Set the label for the "next" page button
                    'firstPageLabel'    => Yii::t('common','First'),   // Set the label for the "first" page button
                    'lastPageLabel'     => Yii::t('common','Last'),    // Set the label for the "last" page button
                    'nextPageCssClass'  => 'next',    // Set CSS class for the "next" page button
                    'prevPageCssClass'  => 'prev',    // Set CSS class for the "previous" page button
                    'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                    'lastPageCssClass'  => 'last',    // Set CSS class for the "last" page button
                    'maxButtonCount'    => 10,    // Set maximum number of page buttons that can be displayed
                ],
                'options' => [
                    'class' => 'table renders-data',                
                ],            
                //'pjax'=>true,   
                'responsive' => true, 
                'responsiveWrap' => false, // Disable Mobile responsive    
                    
            ]); ?>
        </div>
 
</div>


<div class="modal fade modal-full" id="modal-create-actions">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Create Invoice')?></h4>
            </div>
            <div class="modal-body">
                <div class="row document-info">
                    <div class="col-xs-12 col-lg-8 col-sm-7 mb-10">                        
                    </div>
                    <div class="col-xs-12 col-lg-4 col-sm-5 mb-10">
                        <label><?=Yii::t('common','Document No')?></label>
                        <input type="text" class="form-control mb-10" name="document-no"/>
                        <label><?=Yii::t('common','Order Date')?></label>
                        <?php 
                            echo \kartik\widgets\DatePicker::widget([
                                'name'      => 'order-date',
                                'value'     => date('Y-m-d'),        
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format'    => 'yyyy-mm-dd'
                                ],
                                'options'   => ['autocomplete' => 'off']                            
                            ]);
                        ?> 

                        <label class="mt-5"><?=Yii::t('common','External Document')?></label>
                        <input type="text" class="form-control" name="ext-document"/>
                    </div>
                </div>
                <div id="document-rows"></div>
            </div>
            <div class="modal-footer bg-gray">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-danger confirm-create-btn"><i class="far fa-save"></i> <?=Yii::t('common','Confirm')?></button>
            </div>
        </div>
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

<div class="text-center loading-div" style="display:none; position:fixed; top:40%; left:50%; z-index:2000;">
    <i class="fa fa-refresh fa-spin fa-4x fa-fw text-green" aria-hidden="true"></i>
    <div class="blink text-green"><h3> Loading... </h3></div>          
</div>


<?php
$LABEL_CREATE_INVOICE   = Yii::t('common','Create Invoice');
$LABEL_CUT_OFF_STOCK    = Yii::t('common','Cut off stock');
$LABEL_CONFIRM_CAREATE  = Yii::t('common','Are you sure to create invoice?');
$LABEL_CONFIRM_STOCK    = Yii::t('common','Are you sure?');
$CURRENT_DATE           = date('Y-m-d');
$LABEL_IMG              = Yii::t('common','Image');
$LABEL_CODE             = Yii::t('common','Code');
$LABEL_NAME             = Yii::t('common','Name');
$LABEL_QTY              = Yii::t('common','Quantity');
$LABEL_STOCK            = Yii::t('common','Stock');
$LABEL_ALL              = Yii::t('common','Check All');
$LABEL_TYPE             = Yii::t('common','Type');
$LABEL_QUANTITY         = Yii::t('common','Quantity');
$LABEL_CANCEL           = Yii::t('common','Undo');
$LABEL_REMAIN           = Yii::t('common','Remain');
$LABEL_PD_TITLE         = Yii::t('common','Production Order');
$LABEL_SH_TITLE         = Yii::t('common','Shipment');
$LABEL_QTY_TO_CONFIRM   = Yii::t('common','Confirmed');
$LABEL_ALERT_SELECT     = Yii::t('common','You have not selected any items.');


$js=<<<JS

 
let createInvoice = (obj,callback) => {
    $('body').find('.loading-div').show();
    fetch("?r=SaleOrders/reserve/create-invoice", { method: "POST", body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        callback(response); 
        $('body').find('.loading-div').hide();
    })
    .catch(error => { console.log(error); });
};


let createStock = (obj,callback) => {
    //$('body').find('.loading-div').show();
    $('#modal-create-actions').modal('hide');
    $('body').find('tr[data-key="' + obj.id + '"]').find('td.stock-list .well').append('<i class="fa fa-refresh fa-spin" ></i>');

    fetch("?r=SaleOrders/reserve/create-stock", { method: "POST", body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        $('body').find('tr[data-key="' + obj.id + '"]').find('td.stock-list .well i.fa-refresh').remove();       
        callback(response);
        //$('body').find('.loading-div').hide();
    })
    .catch(error => { console.log(error); });
};

let prepareInvoice = (obj,callback) => {   
    $('body').find('button.confirm-create-btn').hide();     
    fetch("?r=SaleOrders/reserve/prepare-invoice", { method: "POST", body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { callback(response); })
    .catch(error => { console.log(error); });
};

let prepareStock = (obj,callback) => {
    fetch("?r=SaleOrders/reserve/prepare-stock", { method: "POST", body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        callback(response); 
        $('body').find('button[data-action="STOCK"]').addClass('confirm-create-btn').attr('disabled', false);
    })
    .catch(error => { console.log(error); });
};


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

let undoPdr = (obj, callback) => {
    //$('body').find('.loading-div').show();
    fetch("?r=Manufacturing/default/undo-transaction", {
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
        //$('body').find('.loading-div').hide();
    })
    .catch(error => {
        console.log(error);
    });
}

let renderTableOnModal = (obj, callback) => {
    let body    = '';
    let i       = 0;
    let data    = obj.raws;

    data.length > 0
    ? data.map(model => {
        i++;
        body += `<tr data-key="` + model.id + `" class="rows ` + (model.stock >= model.qty ? '' : 'bg-danger' ) + `">
                    <td class=" " style="font-family:roboto;">` + i + `</td>
                    <td class="item-code" style="font-family:roboto;"><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">`+ model.code +`</a></td>
                    <td class="item-desc"><label class="pointer" for="check-stock-` + model.id + `">`+ model.name +`</label></td>
                    <td class="item-desc text-center">`+ (model.confirm > 0 ? '<i class="far fa-check-square text-green"></i>' : '<i class="far fa-square text-dark"></i>') +`</label></td>
                    <td class="text-right ` + (model.stock >= model.qty ? 'text-green' : 'text-red' ) + `" style="font-family:roboto;">`+ number_format(model.stock) +`</td>                    
                    <td class="bg-yellow text-right" style="font-family:roboto;"><input type="text" class="form-control text-right" name="qty" value="`+ model.qty +`" /></td>                    
                    <td class="text-center ` + (obj.type==='inv' ? 'hidden' : ' ' )+ `">
                        <label style="width:100%; height:30px;" class="pointer" for="check-stock-` + model.id + `">
                            <input type="checkbox" ` + (model.stock >= model.qty ? 'checked' : (model.confirm > 0 ? 'checked' : '') ) + ` id="check-stock-` + model.id + `" data-key="` + model.id + `" name="check-item"/> 
                        </label>
                    </td>
                </tr> \r\n`;
      })
    : (body += `<tr><td colspan="7" class="text-center" ><h2 style="margin-top:200px;"><i class="fas fa-exclamation-triangle"></i> No Data</h2></td></tr>`);

    let html = `<table class="table table-bordered" id="data-items-inv" data-key="` + obj.id + `">
                    <thead>
                        <tr class="` + (obj.type==='inv' ? 'hidden' : ' ' )+ `">
                            <th class="bg-gray" colspan="5" > </th>                            
                            <th class="bg-gray text-center text-red" colspan="2" >` + (obj.type==='inv' ? '{$LABEL_CREATE_INVOICE}' : '{$LABEL_CUT_OFF_STOCK}' )+ `</th>
                        </tr>
                        <tr>
                            <th class="bg-dark" width="10">#</th>
                            <th class="bg-dark" width="150">{$LABEL_CODE}</th>
                            <th class="bg-dark">{$LABEL_NAME}</th>
                            <th class="bg-dark text-center" width="80">{$LABEL_QTY_TO_CONFIRM}</th>
                            <th class="bg-dark text-right" width="80">{$LABEL_STOCK}</th>                            
                            <th class="bg-yellow text-right" width="100">{$LABEL_QTY}</th>                            
                            <th class="bg-dark text-center ` + (obj.type==='inv' ? 'hidden' : ' ' )+ `" width="100" >
                                <input type="checkbox" id="check-all"  name="checked" />
                                <label class="pointer" for="check-all">  {$LABEL_ALL} </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        `+ body +`
                    </tbody>
                </table>`;
    callback({
        html:html
    });
}



let renderTableOnInvoice = (obj, callback) => {
    let body    = '';
    let i       = 0;
    let data    = obj.raws;

    

    

    data.length > 0
    ? data.map(model => {

        let shiped  = model.shipment;
        let shipment= `<table style="width: 100%; margin: -8px -8px -6px 0px;" border="1">`;
            shipment+= `<tr>`
            shiped.map((model, key) => {
                let color = model.qty > 0 ? 'rgba(189, 255, 215, 0.6)' : 'rgb(255 209 204 / 60%)';
                shipment+= `<td style="padding: 15px; min-width: 50px; text-align: center; position:relative; background: ` +color+ `;">
                                <span style="position:absolute; left:2px; top:2px; opacity: 0.3;"><small>`+ (key + 1) +`</small></span>
                                <span style="font-size: 18px; ">`+ (model.qty) +`</span>
                            </td>`;
            });
            shipment+= `<tr>`
            shipment+= `</table>`;

        i++;
        body += `<tr data-key="` + model.id + `" class="rows ` + (model.stock >= model.qty ? '' : 'bg-danger' ) + `" data-item="`+ model.item +`">
                    <td class=" " style="font-family:roboto;">` + i + `</td>
                    <td class="item-code" style="font-family:roboto;">
                        <a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">`+ model.code +`</a>
                    </td>
                    <td class="item-desc">
                        <label class="pointer" for="check-stock-` + model.id + `">`+ model.name +`</label>
                        <span class="pull-right">` + shipment + `</span>
                    </td>
                    <td class="text-right ` + (model.stock >= model.qty ? 'text-green' : 'text-red' ) + `" style="font-family:roboto; opacity: 0.2;">
                        <i class="pull-left re-calculate pointer fas fa-refresh"></i> <span class="text-stock">`+ number_format(model.stock) +`<span>
                    </td>
                    <td class=" text-right" style="font-family:roboto;">
                        <input type="text" class="text-right no-border" readonly name="qty" value="`+ model.qty +`" style="width: 100%;" />
                    </td>
                    <td class="text-center">
                        <label style="width:100%; height:30px;" class="pointer" for="check-stock-` + model.id + `">
                            <input type="checkbox" ` + (model.stock >= model.qty ? 'checked' : (model.confirm > 0 ? 'checked' : '') ) + ` id="check-stock-` + model.id + `" data-key="` + model.id + `" name="check-item"/> 
                        </label>
                    </td>
                </tr> \r\n`;
      })
    : (body += `<tr><td colspan="7" class="text-center" ><h2 style="margin-top:200px;"><i class="fas fa-exclamation-triangle"></i> No Data</h2></td></tr>`);

    let html = `<table class="table table-bordered" id="data-items-inv" data-key="` + obj.id + `">
                    <thead>
                        <tr class=" ">
                            <th class="bg-gray" colspan="3" > </th>                            
                            <th class="bg-aqua text-center " colspan="3" >{$LABEL_CUT_OFF_STOCK}</th>
                        </tr>
                        <tr>
                            <th class="bg-dark" width="10">#</th>
                            <th class="bg-dark" width="150">{$LABEL_CODE}</th>
                            <th class="bg-dark">{$LABEL_NAME}</th>                             
                            <th class="bg-dark text-right" width="80">{$LABEL_REMAIN}</th> 
                            <th class="bg-dark text-right" width="100">{$LABEL_QTY}</th>
                            <th class="bg-dark text-center" width="100">
                                <input type="checkbox" id="check-all"  name="checked" checked />
                                <label class="pointer" for="check-all">  {$LABEL_ALL} </label>
                            </th>                                
                        </tr>
                    </thead>
                    <tbody>
                        `+ body +`
                    </tbody>
                </table>`;
    callback({
        html:html
    });
}

const renderTable = (obj) => {
    let data = obj.raws;
    let html  = `<div><h4><span class="pdr-no">` + obj.no + `</span></h4></div>`;
        html += `<div><h5><span class="pdr-desc" style="font-family: saraban;">` + obj.desc + `</span></h5></div>`;
        html += `<table class="table table-bordered">
                    <thead>
                        <tr class="bg-gray" style="font-family: saraban; font-size:14px;">
                            <th style="width:10px;">#</th>
                            <th style="width:150px;">${LABEL_CODE}</th>
                            <th>${LABEL_NAME}</th>
                            <th>${LABEL_TYPE}</th>
                            <th class="text-right"  style="width:150px;">${LABEL_QUANTITY}</th>
                            <th class="text-right" >${LABEL_REMAIN}</th>
                        </tr>
                    </thead>`;
        html += "<tbody>";

    data.length > 0
        ? data.map((model, key) => {
            html += `<tr data-key="` + model.id + `" class="` + (model.qty < 0 ? 'bg-warning' : 'bg-success') + `" data-id="` + model.item + `">
                        <td style=" ">` + (key + 1) + `</td>
                        <td class="code font-roboto"><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">` + model.code + `</a></td>
                        <td style="font-family: saraban; font-size:14px;">` + model.name + `</td>
                        <td style="font-family: saraban; font-size:14px;" class="type">` + model.type + `</td>
                        <td class="text-right font-roboto">
                            <a href="?WarehouseSearch[ItemId]=` + btoa(model.item) + `&r=warehousemoving%2Fwarehouse" 
                            target="_blank"
                            class="` + (model.qty < 0 ? 'text-red' : 'text-green') + `">` + model.qty + `</a>
                        </td>
                        <td class="text-right font-roboto ">
                            <a href="?r=warehousemoving%2Fwarehouse&WarehouseSearch[ItemId]=` + btoa(model.item) + `&WarehouseSearch[rowid]=` + model.id + `" target="_blank">
                            ` + number_format(model.remain) + `</a>
                        </td>
                    </tr>`;
                }
            )
        : null;

    html += "</tbody>";
    html += "</table>";
    html += `<div class="text-right">
                <div class="undo-pdr">
                    <a href="#" class="btn btn-app btn-danger  ew-undo-pdr"
                    data-type="` + (obj.type==='Shiped' ? 'Shiped' : 'Produce') + `" 
                    data-key="` + obj.id + `"><i class="fa fa-undo"></i>${LABEL_CANCEL}</a>
                </div>
            </div>`;
    $("body").find('#modal-pdr .modal-body').html(html);        
}

const checkShiped = (el, id, cond, callback) =>{
    let shiped = $(el).closest('tr').find('.stock-list '+cond+'').html();
    callback(shiped);
}

const allowInvoice = (that, id, callback) => {
    let allow   = false;
    let message = '';
    checkShiped(that, id, '.well:first  .ship-detail[data-status="Shiped"]' , res =>{ // Shipment
        //console.log(res)
        if(res === undefined){
            message = 'ไม่มี shipment ทำต่อได้';
            allow   = true;
        }else{
            
            checkShiped(that, id, '.well:last .pdo-detail[data-status="Produce"]' , response =>{ // Consumption
                //console.log(response)
                if(response === undefined){ // ไม่มีใบสั่งผลิต แต่มี Shipment
                    message = 'มี shipment ต้องยกเลิกยอดก่อน';
                    allow   = false;                  
                }else{
                    message = 'มี ใบสั่งผลิต ต้องยกเลิกก่อน';
                    allow   = false;
                }
            })
        }  
        
        callback({allow:allow, message:message});
    })
}

$('body').on('click', 'a.create-invoice-btn', function(){
    let that    = this;
    let id      = parseInt($(this).closest('tr').attr('data-key')); 
    
    $('#modal-create-actions .modal-title').html('{$LABEL_CREATE_INVOICE}');
    $('#modal-create-actions .confirm-create-btn').attr('data-action', 'INVOICE').attr('data-id', id).html('<i class="far fa-file-alt"></i> {$LABEL_CREATE_INVOICE}');

    $('#document-rows').html('<div class="text-center"><i class="fas fa-sync-alt fa-3x fa-spin"></i></div>');
 
    allowInvoice(that, id,  check =>{
        //console.log(check.allow)
        if(check.allow){
            $('#modal-create-actions').modal('show');
        }else{
            alert(check.message);
        }
        
    });

    prepareInvoice({id:id}, res => {
        
        if(res.status===200){
            $('body').find('input[name="document-no"]').val(res.no);
            $('body').find('input[name="ext-document"]').val(res.ext);
            $('body').find('input[name="order-date"]').val(res.date);
            
            if(parseInt("${AutoCutStock}") == 1){ 
                renderTableOnInvoice(res, response => {
                    $('body').find('button.confirm-create-btn').show();
                    setTimeout(() => {
                        $('#document-rows').html(response.html);
                        $('input[name="document-no"]').focus().select();
                    }, 500);                
                })
            }else{
                renderTableOnModal(res, response => {
                    $('body').find('button.confirm-create-btn').show();
                    setTimeout(() => {
                        $('#document-rows').html(response.html);
                        $('input[name="document-no"]').focus().select();
                    }, 500);                
                })
            }
            
        } 
    });
});

$('body').on('click', 'a.create-custoffstock-btn', function(){
    let id      = parseInt($(this).closest('tr').attr('data-key'));
    $('#modal-create-actions').modal('show');
    $('#modal-create-actions .modal-title').html('{$LABEL_CUT_OFF_STOCK}');
    $('#modal-create-actions .confirm-create-btn').attr('data-action', 'STOCK').attr('data-id', id).html('<i class="fas fa-cubes"></i> {$LABEL_CUT_OFF_STOCK}');
    $('body').find('input[name="document-no"]').val(' ');
    $('body').find('input[name="ext-document"]').val(' ');
    $('body').find('input[name="order-date"]').val('{$CURRENT_DATE}');
    $('#document-rows').html('<div class="text-center"><i class="fas fa-sync-alt fa-3x fa-spin"></i></div>');
    prepareStock({id:id}, res => {
        if(res.status===200){
            $('body').find('input[name="document-no"]').val(res.no);
            $('body').find('input[name="ext-document"]').val(res.ext);
            $('body').find('input[name="order-date"]').val(res.date);
            renderTableOnModal(res, response => {
                setTimeout(() => {
                    $('#document-rows').html(response.html);
                }, 500);
            })
        } 
    });
});

const checkNumber = (obj, callback) => {
    fetch("?r=accounting/ajax/no-exists", { 
        method: "POST", 
        body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        callback(response);         
    })
    .catch(error => { console.log(error); });
}

const cutStockAuto = (obj, el, callback) => {
    //let id = el.attr('data-key');
    let id = obj.id;
    let no = obj.no;

    // Check bill number
    checkNumber({no:no}, res =>{

        if(res.status==404){
            // close modal
            $('#modal-create-actions').modal('hide');

            createStock(obj, res => {
                let status = true;
                if(res.status===200){
                    $.notify({
                        // options
                        icon: "fas fa-shopping-basket",
                        message: res.message
                    },{
                        // settings
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: "success",
                        delay: 3000,
                        z_index: 3000
                    });  
                    
                    setTimeout(() => {
                        $('body').find('tr[data-key="'+id+'"]').find('.stock-list .well:first div:first').append('<div><a href="#" class="ship-detail mb-3 text-info" data-key="' + res.id + '">'+res.no+'</a></div>');
                        el.show();
                    }, 1000);    

                    
                            
                }else{
                    el.show();
                    $.notify({
                        // options
                        icon: "fas fa-box-open",
                        message: res.message + ' ' + res.suggestion
                    },{
                        // settings
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: "warning",
                        delay: 3000,
                        z_index: 3000
                    });
                    status = false;

                    $('body').find('tr[data-key="'+id+'"]').find('.tax-invoice .well').html('<a class="btn btn-danger-ew btn-sm create-invoice-btn" href="#"><i class="fas fa-file-invoice"></i> ใบกำกับภาษี</a>');
                                
                }

                el.addClass('confirm-create-btn').attr('disabled', false);
                
                callback(status)
            });
        }else{
            alert('เอกสารไม่มีอยู่แล้ว');
            $('body').find('.confirm-create-btn').show();
            return false;
        }
    });


}

$('body').on('click', 'button.confirm-create-btn', function(){
    let actions = $(this).attr('data-action');
    let id      = parseInt($(this).attr('data-id'));
    let no      = $('input[name="document-no"]').val();
    let ext     = $('input[name="ext-document"]').val();
    let oDate   = $('input[name="order-date"]').val();
    let el      = $(this);
    
    

    if(actions=='INVOICE'){
        
        if(confirm('{$LABEL_CONFIRM_CAREATE}')){
            el.hide();
            let so      = parseInt($('#data-items-inv').attr('data-key'));
            let raws    = [];
            $('#data-items-inv tr.rows').each(function(){
                let row = $(this).find('input[name="check-item"]');
                let id  = row.attr('data-key');
                let val = row.closest('tr').find('input[name="qty"]').val();
                let code= row.closest('tr').find('.item-code').text();
                
                
                raws.push({
                    id: parseInt(id),
                    code: code.trim(),
                    qty: val,
                    stock: $(this).find('input[name="check-item"]').is(":checked") ? true : false
                });
                    
            });
            
            

           

            if(parseInt("${AutoCutStock}") == 1){  
                // Loading
                $('body').find('tr[data-key="'+id+'"]').find('.tax-invoice .well').html('<i class="fa fa-refresh fa-spin mt-10 mb-10"></i>');
                                   
                // ตัดสต๊อก
                cutStockAuto({id:id, no:no, ext:ext, date:oDate, source:raws}, el, response => {
                    if(response){

                        //เปิดบิล
                        createInvoice({id:id, no:no, ext:ext, date:oDate, source:raws}, res => {
                            el.removeClass('confirm-create-btn').attr('disabled', true);
                            if(res.status===200){  
                                
                                $.notify({
                                    // options
                                    icon: "fas fa-shopping-basket",
                                    message: res.message
                                },{
                                    // settings
                                    placement: {
                                        from: "top",
                                        align: "center"
                                    },
                                    type: "success",
                                    delay: 3000,
                                    z_index: 3000
                                });  
                                //$('#modal-create-actions').modal('hide');
                                setTimeout(() => {
                                    $('body').find('tr[data-key="'+id+'"]').find('.tax-invoice .well').html('<a href="?r=accounting%2Fposted%2Fposted-invoice&id='+btoa(res.inv)+'" target="_blank"><i class="fas fa-print text-red"></i> '+res.no+'</a>');
                                    el.show();
                                }, 1000);                    
                            }else{
                                el.show();
                                $.notify({
                                // options
                                icon: "fas fa-box-open",
                                message: res.suggestion
                                },{
                                    // settings
                                    placement: {
                                        from: "top",
                                        align: "center"
                                    },
                                    type: "warning",
                                    delay: 3000,
                                    z_index: 3000
                                });    
                                setTimeout(() => {
                                    $('input[name="document-no"]').focus();
                                }, 1000); 
                            }


                            el.addClass('confirm-create-btn').attr('disabled', false);
                        })
                    }else{
                        alert('ไม่สามารถสร้างบิลได้ เนื่องจากสินค้าไม่เพียงพอ')
                    }
                });   
            }


        }else{
            el.addClass('confirm-create-btn').attr('disabled', false);
            return false;
        }
    }else{
 
        if(confirm('{$LABEL_CONFIRM_STOCK}')){
            el.hide();
            let order   = parseInt($('#data-items-inv').attr('data-key'));
            let source  = [];
            let count   = 0;

            $('#data-items-inv tr.rows').each(function(){
                let row = $(this).find('input[name="check-item"]');
                let id  = row.attr('data-key');
                let val = row.closest('tr').find('input[name="qty"]').val();
                let code= row.closest('tr').find('.item-code').text();                
                
                source.push({
                    id: parseInt(id),
                    code: code.trim(),
                    qty: val,
                    stock: $(this).find('input[name="check-item"]').is(":checked") ? true : false
                });

                count+= $(this).find('input[name="check-item"]').is(":checked") ? 1 : 0;
                    
            })

            if(count > 0){

                createStock({id:id, no:no, ext:ext, date:oDate, source:source}, res => {
                    if(res.status===200){                    
                        $.notify({
                            // options
                            icon: "fas fa-shopping-basket",
                            message: res.message
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: "center"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });  
                        $('#modal-create-actions').modal('hide');
                        setTimeout(() => {
                            $('body').find('tr[data-key="'+id+'"]').find('.stock-list div:first').append('<div><a href="#" class="ship-detail mb-3 text-info" data-key="' + res.id + '">'+res.no+'</a></div>');
                            el.show();
                        }, 1000);                    
                    }else{
                        el.show();
                        $.notify({
                            // options
                            icon: "fas fa-box-open",
                            message: res.message + ' ' + res.suggestion
                        },{
                            // settings
                            placement: {
                                from: "top",
                                align: "center"
                            },
                            type: "warning",
                            delay: 3000,
                            z_index: 3000
                        });    
                    }

                    el.addClass('confirm-create-btn').attr('disabled', false);
                    
                })

            }else{
                alert('${LABEL_ALERT_SELECT}');
                return false;
            }
            
        }else{
            el.addClass('confirm-create-btn').attr('disabled', false);
            return false;
        } // end confirm
        
    }// end if
})


$('body').on('click', '.pdo-detail' , function(){
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
            //$('.ew-undo-pdr').attr('class', 'btn btn-app btn-danger  ew-undo-ship');
            $('.ew-undo-pdr').attr('data-type', 'Shiped');
            $("body").find('#modal-pdr .modal-body').append(`<div class="mt-10"> ` + (res.remark ? res.remark : ' ') + ` </div>`);
            $("body").find('#modal-pdr .modal-title').html(`${LABEL_SH_TITLE}`);
        }
    })
})

$("body").on('click', 'a.ew-undo-pdr', function(){
    if(confirm("Confirm Undo ?")){
        let id = parseInt($(this).attr('data-key'));
        let type = $(this).attr('data-type');

        // Hide
        //$('.ew-undo-pdr').hide();
        $("#modal-pdr").modal("hide");

        // Loading
        if(type=='Produce'){
            $('body').find('.pdo-detail[data-key="'+ id +'"]').append(' <i class="fa fa-refresh fa-spin"></i>');
        }else{
            $('body').find('.ship-detail[data-key="'+ id +'"]').append(' <i class="fa fa-refresh fa-spin"></i>');
        }
       

        undoPdr({id:id, name:type}, res => {
            if(res.status===200){
                renderTable(res);
                
                // Replase Color
                if(type=='Produce'){
                    $('body').find('.pdo-detail[data-key="'+ id +'"]').attr('class', 'pdo-detail pointer mb-3 text-gray');
                    $('body').find('.pdo-detail[data-key="'+ id +'"] i.fa').attr('class', 'fas fa-check text-green')    // remove loading
                }else{
                    $('body').find('.ship-detail[data-key="'+ id +'"]').attr('class', 'ship-detail pointer mb-3 text-gray');
                    $('body').find('.ship-detail[data-key="'+ id +'"] i.fa').attr('class', 'fas fa-check text-green')   // remove loading
                }
                $("body").attr("style", "overflow:auto;"); //  
            
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

$('body').on('click', '#check-all', function(){
    $('input[name="check-item"]').not(this).prop('checked', this.checked);
})

$('body').on('click', 'a.print-pdr', function(){
    let id = $(this).attr('data-key');
    $("#modal-pdr").modal("show");
    $('.loading-div').show();
    $("body").find('#modal-pdr .modal-title').html('Production Order');
    $("body").find('#modal-pdr .modal-body').html('').attr('style','margin-top:-5px;     margin-bottom: 0px;');
    setTimeout(() => {
        $("body").find('#modal-pdr .modal-body').append('<div class="row" style="height:100%; margin-top: -6px;"></div>');
        $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" />').attr('src', "index.php?r=Manufacturing%2Fproduction%2Fprint&id="+id).appendTo('#modal-pdr .modal-body .row'); // append to modal body or wherever you want
        setTimeout(() => {
            $('.loading-div').hide();
        }, 1000);
    }, 500);
 
});

$('body').on('click', 'a.show-print', function(){
    let id = $(this).closest('div.row').attr('data-key');
    $("#modal-pdr").modal("show");
    $('.loading-div').show();
    $("body").find('#modal-pdr .modal-title').html('Sale Order');
    $("body").find('#modal-pdr .modal-body').html('').attr('style','margin-top:-5px;     margin-bottom: 0px;');
    setTimeout(() => {
        $("body").find('#modal-pdr .modal-body').append('<div class="row" style="height:100%; margin-top: -6px;"></div>');
        $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" />').attr('src', "index.php?r=SaleOrders%2Fsaleorder%2Fprint&id="+id+"&footer=1").appendTo('#modal-pdr .modal-body .row'); // append to modal body or wherever you want
        setTimeout(() => {
            $('.loading-div').hide();
        }, 1000);
    }, 500);
})

$('body').on('click', 'a.show-print-inv', function(){
    let id = $(this).closest('div.row').attr('data-key');
    $("#modal-pdr").modal("show");
    $('.loading-div').show();
    $("body").find('#modal-pdr .modal-title').html('Invoice');
    $("body").find('#modal-pdr .modal-body').html('').attr('style','margin-top:-5px;     margin-bottom: 0px;');
    setTimeout(() => {
        $("body").find('#modal-pdr .modal-body').append('<div class="row" style="height:100%; margin-top: -6px;"></div>');
        $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" />').attr('src', "index.php?r=accounting%2Fposted%2Fprint-inv&id=" + id + "==&footer=1").appendTo('#modal-pdr .modal-body .row'); // append to modal body or wherever you want
        setTimeout(() => {
            $('.loading-div').hide();
        }, 1000);
    }, 500);
})



const deletePdr = (obj, callback) => {
    fetch("?r=Manufacturing/production/delete-ajax", { 
        method: "POST", 
        body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        callback(response);         
    })
    .catch(error => { console.log(error); });
}

$('body').on('click', 'a.delete-pdr', function(){
    let id      = $(this).attr('data-key');
    let code    = $(this).attr('data-no');
    let tr      = $(this).closest('.row');

    if (confirm('ต้องการลบรายการ "' + code + '" ?')) {  
        
        deletePdr({id:id}, res => {
            if(res.status===200){
                tr.css("background-color", "#aaf7ff");
                tr.fadeOut(300, function() {
                    tr.remove();                    
                });     
            }else{
                alert('error');
            }
        })
        
          
    }
});

const addToTransportShip = (obj, callback) => {
    fetch("?r=SaleOrders/reserve/transport-list-update", { 
        method: "POST", 
        body: JSON.stringify(obj),
        headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
    })
    .then(res => res.json())
    .then(response => { 
        callback(response);         
    })
    .catch(error => { console.log(error); });
}


$('body').on('click', '.add-to-ship', function(){
    let el      = $(this);
    let id      = $(this).closest('tr').attr('data-key');
    let type    = $(this).attr('data-type');
    
    addToTransportShip({id:id}, res => {
        if(res.status===200){
            el.html('<i class="fas fa-truck text-' + (res.action===1 ? 'info' : 'gray')+ '" style="min-width:15px;"></i>');
        }else{
            $.notify({
            // options
            icon: "fas fa-box-open",
            message: res.message
            },{
                // settings
                placement: {
                    from: "top",
                    align: "center"
                },
                type: "warning",
                delay: 3000,
                z_index: 3000
            });    
        }
    });
});


    const reCalculate = (obj, callback) =>{
        fetch("?r=items/ajax/recalculate", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.re-calculate', function(){
        let el  = $(this);
        let id  = el.closest('tr').attr('data-item');
                  el.closest('tr').find('i.fas').addClass('fa-spin');

        reCalculate({id:id}, res =>{            
            let stock = res.raws.stock;
                        el.closest('tr').addClass('text-green').find('.text-stock').html(number_format(stock))
                        el.closest('tr').find('i.fas').removeClass('fa-spin');

                if(stock > 0){
                    el.closest('tr').removeClass('bg-danger');
                    el.closest('td').removeClass('text-red');
                }else{
                    el.closest('tr').addClass('bg-danger').addClass('text-red');
                    el.closest('td').addClass('text-red');
                }
        });
    });

    $("#modal-create-actions").on("show.bs.modal", function() {
        //$('body').find('button.confirm-create-btn').hide();
    });

    $("#modal-create-actions").on("hidden.bs.modal", function() {
        $('.loading-div').hide();
    });




JS;


$this->registerJs($js,Yii\web\View::POS_END);
$this->render('_reseive_script');

?>

 