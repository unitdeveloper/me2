<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $model common\models\SalesPeople */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('common', 'Sales Peoples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .item-info:hover {
        background: #3fbbea !important;
        color: #fff !important;
    }
    @media (max-width: 767px) {
    
    .content-wrapper{
        background-color: #ecf0f5 !important;
    }
}
</style>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>
<div class="sales-people-view" ng-init="Title='<?= Html::encode($this->title) ?>'">

    <div class="row">
        <div class="col-sm-4">
            <!-- Widget: user widget style 1 -->
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-yellow">
              <div class="widget-user-image">
                    <?=Html::img($model->picture,['class' => 'img-circle'])?>
              </div>
              <!-- /.widget-user-image -->
              <h3 class="widget-user-username"><?= $model->name.' '.$model->surname ?></h3>
              <h5 class="widget-user-desc"><?= $model->position?></h5>
            </div>
            <div class="box-footer no-padding">
              <ul class="nav nav-stacked">
                <li><a href="#"><?=Yii::t('common','ID')?> <span class="pull-right badge bg-blue"><?=$model->id ?></span></a></li>
                <li><a href="#"><?=Yii::t('common','Code')?> <span class="pull-right badge bg-aqua"><?=$model->code ?></span></a></li>
                <!-- <li><a href="#"><?=Yii::t('common','Projects')?> <span class="pull-right badge bg-green">12</span></a></li>
                <li><a href="#"><?=Yii::t('common','Completed Projects')?> <span class="pull-right badge bg-green">12</span></a></li> -->
                <li><a href="#"><?=Yii::t('common','Address')?><span class="pull-right   "><?=$model->address ?></span></a></li>
                <li><a href="#"><?=Yii::t('common','Vat Registration')?> <span class="pull-right  "><?=$model->tax_id ?></span></a></li>
                <li><a href="#"><?=Yii::t('common','Phone')?> <span class="pull-right  "><?=$model->mobile_phone ?></span></a></li>
              </ul>
            </div>
          </div>
          <!-- /.widget-user -->
             
            
        </div>
        <div class="col-sm-8">
             
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><?=Yii::t('common','Customers List')?></h3>
                </div>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,        
                    'responsiveWrap' => false,             
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' => 'group',
                            'label' => Yii::t('common','Customer Group'),
                            'value' => function($model){
                                return $model['group'];
                            },
                            'group'=>true,
                        ],
                        [
                            'attribute' => 'code',
                            'label' => Yii::t('common','Code'),
                            'value' => 'code'
                        ],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('common','Name'),
                            'value' => 'name'
                        ],
                        // 'code',
                        // 'name'
                        // [
                        //     'attribute' => 'customer.code',
                        //     'value' => 'customer.code',
                        // ],
                        // [
                        //     'attribute' => 'customer.name',
                        //     'value' => 'customer.name',
                        // ],
                        // [
                        //     'attribute' => 'customer.locations.province',
                        //     'value' => 'customer.locations.province'
                        // ],
                        
                    ],
                ]); ?> 
             
            </div>
            <p class="mb-10 text-right">
                <a class="btn btn-info" data-toggle="collapse" href="#item-sales" aria-expanded="false" aria-controls="item-sales">
                    <i class="fa fa-list"></i> <?=Yii::t('common','My item sales')?>
                </a>
                <?= Html::a('<i class="far fa-edit"></i> '.Yii::t('common', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
           
            </p>
            <div class="collapse" id="item-sales">
                <div id="table-item-sales" class="mt-10"></div>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="modal-item-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Item Info</h4>
            </div>
            <div class="modal-body table-responsive">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
            </div>
        </div>
    </div>
</div>

<?php 
 
$Yii = 'Yii';
$id  = $model->id;
 
$js=<<<JS

let state = {
    progress : false,
    data : []
};

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>             
            <h4 class="count-time"></h4>
        </div>`;

$('body').on('click','#myBtn',function(){
    topFunction();
});



const getApi  = (id, callback) => {
    fetch("?r=customers/responsible/get-sale-items", {
        method: "POST",
        body: JSON.stringify({id:id}),
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
 
 
const renderTable = () => {
    let id = '{$id}';
    
    getApi(id, res => {
        let html = `<table class="table" id="export_table">
                        <thead>
                            <tr class="bg-black">
                                <th class="text-center  hidden-xs" style="width:30px;">{$Yii::t('common','#')}</th>
                                <th class="text-center">{$Yii::t('common','Image')}</th>
                                <th >{$Yii::t('common','Product Name')}</th>                         
                                <th class="text-right  hidden-xs">{$Yii::t('common','Quantity')}</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;
            res.items.map((model, key) => {
                html+= `<tr data-key="` + model.id + `" data-name="` + model.name + `">
                            <td class="hidden-xs">  ` + (key + 1) + ` </td>
                            <td  class="item-info pointer text-center" style="width:100px;">
                                    <div><img src="` + model.img + `" class="img-responsive" /></div>
                                    <div style="font-size:9px;">` + model.code + `</div>
                            </td>
                            <td>
                                <div class="mb-10">` + model.name + `</div>
                                <div class="hidden-sm hidden-md hidden-lg pull-right mt-10"> {$Yii::t('common','Quantity')} : ` + model.qty + `</div> 
                            </td>
                            <td class="text-right hidden-xs"> ` + model.qty + ` </td>
                        </tr>`;
            })

        html+= '</tbody></table>';       
        $('#table-item-sales').html(html);
        var table = $('body').find('#export_table').DataTable({
                        "paging": true,
                        "searching": true
                    }); 
    });
}

$(document).ready(function(){
    renderTable();
    $('.btn-app-print').remove();
})

let getInvApi = (id, callback) => {
    fetch("?r=items/ajax/get-item-in-inv-by-sale", {
        method: "POST",
        body: JSON.stringify({id:id, sale:'{$id}'}),
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


let renderTableInv = (data, callback) => {
    let html = `<table class="table table-bordered font-roboto" id="export_table_detail">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="min-width:75px;">{$Yii::t('common','Date')}</th>
                            <th style="min-width:90px;">{$Yii::t('common','Sale Order')}</th>
                            <th style="min-width:90px;">{$Yii::t('common','Tax Invoice')}</th>
                            <th class="text-right">{$Yii::t('common','Quantity')}</th>
                            <th class="text-right">{$Yii::t('common','Price')}</th>
                            <th class="text-right" style="min-width:70px;">{$Yii::t('common','Total')}</th>
                        </tr>
                    </thead>
    
    `;
    function compare( a, b ) {
        if ( a.no > b.no ){
            return -1;
        }
        return 0;
    }

    data.sort( compare );   
    data.map((model, key) => {
        html+= `<tr>
                    <td>` + (key + 1) + `</td>
                    <td>` + model.date + `</td>
                    <td><a href="?r=SaleOrders%2Fsaleorder%2Fview&id=` + model.soId + `" target="_blank">` + model.so + `</a></td>
                    <td><a href="?r=accounting%2Fposted%2Fread-only&id=` + model.id + `" target="_blank">` + model.no + `</td>
                    <td class="text-right bg-yellow">` + number_format((model.qty).toFixed(0)) + `</td>
                    <td class="text-right">` + number_format((model.price).toFixed(0)) + `</td>
                    <td class="text-right">` + number_format((model.qty * model.price).toFixed(0)) + `</td>
                </tr>`;
    })
        html+= '</table>';

    callback({html:html});
}

$('body').on('click','.item-info',function(){
    let id      = $(this).closest('tr').data('key');
    let name    = $(this).closest('tr').attr('data-name');
    $('#modal-item-info .modal-body').html(loadingDiv);
    getInvApi(id, res => {        
        renderTableInv(res, render => {     
            $('#modal-item-info .modal-title').html(name);                      
            $('#modal-item-info .modal-body').html(render.html);
            var table = $('body').find('#export_table_detail').DataTable({
                        "paging": false,
                        "searching": false
                }); 
        });
    })
    $('#modal-item-info').modal('show');
})


JS;
$this->registerJS($js,\yii\web\View::POS_END);
$this->registerJsFile('@web/js/jquery.animateNumber.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 