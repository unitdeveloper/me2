<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\models\SalesPeople;
use common\models\RcInvoiceLine;
use common\models\Items;
//use kartik\detail\DetailView;
 
// Basic default usage without any options. When <code>ip</code> is not set, the IP address of 
// the user session will be used to determine the IP info. Click the flag icon to get details.

/* @var $this yii\web\View */
/* @var $model common\models\customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$comp   = Yii::$app->session->get('Rules')['comp_id'];
$keys   = 'customers%2Fcustomer%2Fview&id:'.$model->id;


if(!Yii::$app->cache->get($keys)){
    $item       = RcInvoiceLine::find()->select('item')->where(['customer_no_' => $model->id])->groupBy(['item']);
    $itemList   = Items::find()->where(['IN','id',$item])->orderBy(['description_th' => SORT_ASC])->all();
    $items      = [];
    foreach ($itemList as $key => $item) {
        $items[] = (Object)[
            'id'    => $item->id,
            'name'  => $item->description_th,
            'code'  => $item->master_code,
            'qty'   => RcInvoiceLine::find()->where(['item' => $item->id])->andWhere(['customer_no_' => $model->id])->sum('quantity'),
            'img'   => $item->picture
        ];
    }
    $rawData = (Object)[
        'payin'         => $model->getCredit()->PayIn,
        'available'     => $model->getCredit()->CreditAvailable,
        'posted'        => $model->getCredit()->SumPostedInv,
        'total'         => $model->getCredit()->TotalRcApp,
        'outstanding'   => $model->getCredit()->OutstandingBalance,
        'remaining'     => $model->getCredit()->CreditRemaining,
        'clearing'      => $model->getCredit()->Clearing,
        'items'         => $items
    ];
    Yii::$app->cache->set($keys, $rawData, 60);
}

$CreditInfo = Yii::$app->cache->get($keys);

?>
<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>




<div class="row"  ng-init="Title='<?= Html::encode($this->title) ?>'">
    <div class="col-sm-2">
        <div class="row ">
            <div class="col-sm-12 col-xs-6"><?php echo Html::img($model->getPhotoViewer('logo'),[
                'class' => 'img-responsive img-rounded img-thumbnail',
                'style' => 'margin-bottom:20px;'
                ]);?>
            </div>
            <div class="col-sm-12 col-xs-6"><?php echo Html::img($model->getPhotothumb('photo'),[
                'class' => 'img-responsive img-rounded img-thumbnail',
                'style' => 'margin-bottom:20px;',
                'id'=>"zoom_01",
                'data-zoom-image'=> $model->getPhotoViewer('photo') == '/uploads/kitbom/img.png' ? ' ' : $model->getPhotoViewer('photo') 
                ]);?>
            </div>

            <div class="col-sm-12 text-right mb-10">
                <?= Html::a('<i class="far fa-trash-alt"></i> '.Yii::t('common', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger-ew',
                    'data' => [
                        'confirm' => Yii::t('common', 'Are you sure you want to delete this customer?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        </div>
                                                                                       
                                                                               
                                                                   
                 
    </div>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-xs-12 mb-10">
                <a class="btn btn-primary" data-toggle="collapse" href="#item-sale-info" aria-expanded="false" aria-controls="item-sale-info">
                    <i class="fa fa-list"></i> <?= Yii::t('common','Customer item sale')?>
                </a>
            </div>                  
       </div>  
             
        <div class="collapse" id="item-sale-info">
            <?php             
                $table = '';
                if(count($CreditInfo->items) > 0 ):
                foreach ($CreditInfo->items as $key => $item) {
                    $table.= '<tr data-key="' . $item->id . '" data-name="'.$item->name.'">';
                    $table.= '  <td class="text-center font-roboto  hidden-xs">'.($key + 1).'</td>';
                    $table.= '  <td  class="item-info pointer text-center" style="width:100px;">
                                    <div>'.Html::img($item->img,['class' => 'img-responsive img-thumbnail', 'style' => 'width:100px;']).'</div>
                                    <div style="font-size:9px;">'.$item->code.'</div>
                                </td>';
                    $table.= '  <td class="font-roboto">'.$item->name.'</td>';
                    $table.= '  <td class="text-right font-roboto item-info pointer hidden-xs">'.number_format($item->qty).'</td>';
                    $table.= '</tr>';
                }
            ?>
            <table class="table table-hover" data-cust="<?=$model->id?>" id="export_table">
                <thead>
                    <tr class="bg-black">
                        <th class="text-center  hidden-xs" style="width:30px;"><?=Yii::t('common','#')?></th>
                        <th class="text-center"><?=Yii::t('common','Image')?></th>
                        <th ><?=Yii::t('common','Product Name')?></th>                         
                        <th class="text-right  hidden-xs"><?=Yii::t('common','Quantity')?></th>
                    </tr>
                </thead>   
                <tbody>
                    <?=$table ;?>
                </tbody>            
            </table>
            <?php else: echo Yii::t('common','No data found'); ?>
            <?php endif; ?>
        </div>  
          
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="customer-view">
                <h3 class="pull-left"><?= Html::encode($model->name) ?> </h3>         
                    
                         

                    <?= DetailView::widget([
                        'model' => $model,                        
                        'attributes' => [
                            'code',
                            'name',
                            'name_en',
                            [
                                'attribute' => 'address',
                                'format' => 'raw',
                                'value' => function($model){
                                    
                                    $html = '<div>'.($model->latitude ? $model->getMaps(250) : null).'</div>';
                                     
                                    $html.= '<div>'.$model->fullAddress['address'].'</div>';
                                    $html.= '<div>'.$model->fullAddress['address_en'].'</div>';
                                    return $html;
                                }  
                            ],
                            // 'address',
                            // 'address2',
                            // 'address_en',                            
                            'vat_regis', 
                            
                            [
                                'attribute' => 'genbus_postinggroup',
                                'format' => 'raw',
                                'value' => function($model){

                                    if($model->genbus_postinggroup == '01') $model->genbus_postinggroup = Yii::t('common','General');
                                    if($model->genbus_postinggroup == '02') $model->genbus_postinggroup = Yii::t('common','Modern Trade');

                                    return $model->genbus_postinggroup;
                                }  
                            ],
                            
                            //'headoffice',
                            [
                                'attribute' => 'headoffice',
                                'format' => 'raw',
                                //'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                                'value' => function ($model) { 
                                    if($model->headoffice == '1')
                                    {
                                        return Yii::t('common','Head Office');
                                    }else {
                                        return Yii::t('common','Branch');
                                    }
                                    
                                },
                            ],
                            'districttb.DISTRICT_NAME',
                            'citytb.AMPHUR_NAME',
                            
                            'provincetb.PROVINCE_NAME',
                            'postcode',
                            //'country',
                            //'owner_sales',
                            [
                                'attribute' => 'country',
                                'format' => 'raw',
                                'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                                'value' => function ($model) { 
                                    
                                     $country = \common\models\Countries::findOne($model->country);
                                     return $country->country_name;
                                },
                            ],
                            //'transport',
                            [
                                'attribute' => 'transport',
                                'format' => 'raw',
                                'value' => function($model){
                                    /*
                                    return ' <a href="index.php?r=customers/customer/print-ship&id=<?=$model->id?>" target="_blank" class="pull-right btn btn-success"><i class="fa fa-print" aria-hidden="true"></i> Print</a>';
                                    */
                                    $print = $model->transport;
                                    $print.= Html::a('<i class="fa fa-print" aria-hidden="true"></i> Print',
                                        ['/customers/customer/print-ship','id' => $model->id],
                                        ['target' =>"_blank",'class' => 'pull-right btn btn-info-ew btn-flat']);
                                    return $print;
                                }
                            ],
                            //'owner_sales',
                            [
                                'attribute' => 'owner_sales',
                                'format' => 'raw',
                                'visible' => (Yii::$app->session->get('Rules')['rules_id']!=3),
                                'value' => function ($model) { 
                                    
                                    // if(SalesPeople::find()->where(['code' => explode(',',$model->owner_sales)])->count()>0)
                                    // {
                                    //     $sales = SalesPeople::find()
                                    //     ->where(['code' => explode(',',$model->owner_sales),'comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                    //     ->all();
                                    //     $salpeople = '';
                                    //     foreach ($sales as $people) {
                                    //         $salpeople.= '<div>['.$people->code.'] '.$people->name.'</div>'; 
                                    //     }
                                    //     return $salpeople;

                                    // }else {
                                    //     return '-';
                                    // }
                                    $data = '';
                                    foreach ($model->salesHasCustomer as $key => $value) {
                                        $data.= '<div>['.$value->salespeople->code.'] '.$value->salespeople->name.'</div>'; 
                                    }
                                    
                                    return $data;
                                },
                            ],
                            //'vatbus_postinggroup',
                            //'genbus_postinggroup',

                            //'blance',
                            //'status',
                                                   
                            'create_date',
                            //'credit_limit:decimal',
                            [
                                'label' => Yii::t('common','Credit Limit'),
                                'value' => function($model){
                                    if($model->credit_limit > 0){
                                        return number_format($model->credit_limit,2);
                                    }else {
                                        return 0;
                                    }
                                }
                            ],
                            [
                                'label' => Yii::t('common','Credit'),
                                'value' => function($model){
                                    if($model->payment_term > 0){
                                        return $model->payment_term.' '.Yii::t('common','Day');
                                    }else {
                                        return Yii::t('common','Cash');
                                    }
                                }
                            ],
                            [
                                //'label' => Yii::t('common','Credit'),
                                'attribute' => 'payment_due',
                                'value' => function($model){
                                    return Yii::t('common','Every date'). ' ' .$model->payment_due. ' '.Yii::t('common','of month');
                                }
                            ],
                         
                            // [
                            //     'attribute' => 'status',
                            //     'format' => 'raw',
                            //     'value' => function($model){
                            //         if($model->status == 1)
                            //         {
                            //             $status = 'checked';
                            //         }else {
                            //             $status = NULL;
                            //         }
                            //         $data = '<input id="ew-customer-status" type="checkbox" '.$status.' data-toggle="toggle" data-style="android" data-onstyle="info" value="'.$model->status.'" ew-id="'.$model->id.'" disabled>';

                            //         return $data;
                            //     },
                            // ],

                            // [
                            //     //'attribute' => 'address2',
                            //     'label' => 'Zone',
                            //     'format' => 'html',
                            //     'value' => function($model){
                            //         return IpInfo::widget();
                            //     },
                            // ],

                             
                        ],
                    ]) ?>

                </div>
            </div>
        </div>
        <!-- ./ panel -->


        
        <div class="panel panel-info">
                <div class="panel-heading">
                <h5><i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Credit')?></h5></div>
                <div class="panel-body">
                
                    <fieldset>                         
                        <div class="row">                            
                            <div class="col-xs-5 my-5"><?=Yii::t('common','Limit')?></div>
                            <div class="col-xs-7 text-right my-5"><?=number_format($model->credit_limit,2);?></div>
                        </div> 
                        <div class="row">                            
                            <div class="col-xs-7 my-5"><?=Yii::t('common','Usage')?></div>
                            <div class="col-xs-5 text-right my-5">
                                <a href="index.php?r=SaleOrders/saleorder&SaleListSearch[customer_id]=<?=$model->id?>" target="_blank">
                                    <?=number_format($CreditInfo->payin, 2);?>
                                </a>
                            </div>
                        </div> 

                        <div class="row">                             
                            <div class="col-xs-5 my-5"><?=Yii::t('common','Available')?></div>
                            <div class="col-xs-7 text-right my-5"> 
                                <div class="<?=$CreditInfo->available < 0 ? 'text-red' : 'text-success'; ?>">
                                    <?=number_format($CreditInfo->available, 2);?>
                                </div>
                            </div>
                        </div>                      
                    </fieldset>
                    
                        <div class="row"><hr></div>
                        <h5><i class="fa fa-credit-card-alt" aria-hidden="true"></i> <?=Yii::t('common','Payment')?></h5>
                        <div class="row"><hr></div>
                        <div class="row">                            
                            <div class="col-xs-7"><div><i class="fa fa-leanpub" aria-hidden="true"></i> ยอดหนี้ </div><?=Yii::t('common','Debt balance')?> </div>
                            <div class="col-xs-5 text-right"> 
                                <div class="alert bg-danger"> 
                                    <a href="index.php?r=accounting/posted/index&RcinvheaderSearch[cust_no_]=<?=$model->id?>" target="_blank">
                                        <?=number_format($CreditInfo->posted, 2);?>
                                    </a>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
                             
                            <div class="col-xs-7"><div><i class="fa fa-paypal" aria-hidden="true"></i> ยอดชำระ </div>Paid</div>
                            <div class="col-xs-5 text-right ">
                                <div class="alert bg-info"> 
                                    <a href="index.php?ChequeSearch[cust_no_]=<?=$model->id?>&r=Management/report/pass-cheque" target="_blank">
                                        <?=number_format($CreditInfo->total, 2);?>
                                    </a>  
                                </div>  
                            </div>
                        </div>  
                        
                        <div class="row">
                            
                            <div class="col-xs-7"><div><i class="fa fa-cc-paypal" aria-hidden="true"></i> ยอดค้างชำระ </div><?=Yii::t('common','Outstanding Balance')?></div>
                            <div class="col-xs-5 text-right"> 
                                <div class="alert <?=$CreditInfo->outstanding < 0 ? 'bg-danger' : 'bg-warning'; ?>">

                                    <?=number_format($CreditInfo->outstanding, 2);?>
                                        
                                </div>
                            </div> 
                        </div>

                        <div class="row">
                            
                            <div class="col-xs-7"><div><i class="fa fa-shopping-bag" aria-hidden="true"></i> เครดิต คงเหลือ </div><?=Yii::t('common','Credit Remaining')?></div>
                            <div class="col-xs-5 text-right "> 
                                <div class="alert <?=$CreditInfo->remaining < 0 ? 'bg-danger' : 'bg-success'; ?> ">
                                    <?=number_format($CreditInfo->remaining,2);?>
                                </div>
                            </div> 
                        </div>
                     

                    

                </div>
                <div class="panel-footer">
                    
                    
                         
                        <div class="row">
                             
                            <div class="col-xs-7"><div><i class="fa fa-hourglass-start" aria-hidden="true"></i> เคลียร์ริ่ง </div> <?=Yii::t('common','Clearing')?></div>
                            <div class="col-xs-5 text-right">
                                <div class="col-xs-12">
                                <a href="index.php?ChequeSearch[cust_no_]=<?=$model->id?>&r=Management/approve/clearing" target="_blank">
                                    <?=number_format($CreditInfo->clearing,2);?>
                                </a>    
                                </div>
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

    


    </div>
</div>

 
<?php 
 
$Yii = 'Yii';
 
$js=<<<JS

let state = {
    progress : false,
    data : []
};

$(document).ready(function(){
    $("#zoom_01").elevateZoom({scrollZoom : true});
    $('body').find('.btn-app-print').remove();

    var table = $('#export_table').DataTable({
                        "paging": true,
                        "searching": true
                    });
    // Export to excel
    setTimeout(() => {
        $("#export_table").tableExport({
            headings: true,                    // (Boolean), display table headings (th/td elements) in the <thead>
            footers: true,                     // (Boolean), display table footers (th/td elements) in the <tfoot>
            formats: ["xlsx"],    // (String[]), filetypes for the export ["xls", "csv", "txt"]
            fileName: "{$this->title}",         // (id, String), filename for the downloaded file
            bootstrap: true,                   // (Boolean), style buttons using bootstrap
            position: "bottom" ,                // (top, bottom), position of the caption element relative to table
            ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
            ignoreCols: null,                 // (Number, Number[]), column indices to exclude from the exported file
            ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
        });   
    }, 500);
    
})
const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>
            <h4 class="count-time"></h4>
        </div>`;


let getApi = (id, cust, callback) => {
    fetch("?r=items/ajax/get-item-in-inv", {
        method: "POST",
        body: JSON.stringify({id:id, cust:cust}),
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

let renderTable = (data, callback) => {
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
    let cust    = $(this).closest('table').attr('data-cust');
    $('#modal-item-info .modal-body').html(loadingDiv);
    getApi(id, cust, res => {        
        renderTable(res, render => {    
            $('#modal-item-info .modal-title').html(name);                         
            $('#modal-item-info .modal-body').html(render.html);
            var table = $('#export_table_detail').DataTable({
                        "paging": false,
                        "searching": false
                    }); 
        });
    })
    $('#modal-item-info').modal('show');
})

$('#modal-item-info').on('hidden.bs.modal',function(){
    $('body').addClass('modal-open').attr('style','overflow: auto; margin-right: 0px; padding-right: 0px;');
    $('#modal-item-info .modal-body').html(loadingDiv);
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

<?php $this->registerJsFile('@web/js/jquery.elevatezoom.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
