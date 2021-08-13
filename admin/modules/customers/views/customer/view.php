<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\models\SalesPeople;
use common\models\RcInvoiceLine;
use common\models\Items;
use kartik\widgets\DatePicker;

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
    <div class="col-md-1 col-sm-6">
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

    <div class="col-md-6 col-sm-12">
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
                                'attribute' => 'ship_name',
                                'label' => Yii::t('common','Shipment Information'),
                                'format' => 'raw',
                                'value' => function($model){
                                    $html = '<div class="panel panel-default">                                    
                                                <div class="panel-body">    
                                                    <div><b>'.$model->ship_name.'</b></div>
                                                    <div class="mt-5">'.$model->ship_address.'</div>
                                                </div>
                                            </div>';

                                    return $html;
                                }
                            ],

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
                                                   
                            'create_date:datetime',
                            'user_id',
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

                        ],
                    ]) ?>

                </div>
            </div>
        </div>
        <!-- ./ panel -->
    </div>



    <div class="col-md-5 col-sm-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <i class="fas fa-truck-moving"></i>
                <h3 class="box-title"><?= Yii::t('common','History Transport')?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <ol>
                <?php
                    $myTranSport = $model->myTransport;
                    foreach ($myTranSport as $key => $list) {
                        echo "<li>$list->name</li>";
                    }
                ?>
                </ol>
            </div>
            <!-- /.box-body -->
        </div>

        <div class="panel panel-info">
            <div class="panel-heading">
            <h5><i class="fa fa-credit-card" aria-hidden="true"></i> <?=Yii::t('common','Credit')?></h5></div>
            <div class="panel-body">
            
                <fieldset>                         
                    <div class="row">                            
                        <div class="col-xs-5 my-5"><?=Yii::t('common','Credit')?></div>
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
            </div>
            <div class="panel-footer"> </div>
        </div> 


        <div class="panel panel-danger">
            <div class="panel-heading">
            <a href="#" class="btn btn-default-ew pull-right add-new-promotion"><i class="fas fa-plus"></i></a>
            <h5><i class="fas fa-tags"></i> <?=Yii::t('common','Promotion')?></h5> 
            </div>
            <div class="panel-body">
            
                <fieldset>                         
                                             
                        <?PHP 

                            $promotion = $model->attachData;

                            foreach ($promotion as $key => $value) {
                                $count = $key + 1;
                                $file   = 'uploads/'.$value->data_file;
                                $hidden = $value->data_file == '' ? 'hidden' : null;
                                $delete = (Yii::$app->session->get('Rules')['name'] == 'Administrator' || Yii::$app->session->get('Rules')['name'] == 'Accounting') 
                                            ? "<a href='#' class='text-red pull-right delete-attach'><i class='far fa-trash-alt'></i></a>"
                                            : NULL;


                                echo "
                                    <div class='this-row font-roboto' data-key='{$value->id}'>   
                                        <div class='row' >   
                                            <div class='col-xs-1'>{$count}</div>
                                            <div class='col-xs-3'>{$value->create_date}</div>
                                            <div class='col-xs-4'><b>{$value->title}</b></div>
                                            <div class='col-xs-4 '>
                                                <a href='{$file}' target='_blank' class='show-file {$hidden}'>
                                                    <i class='far fa-file-pdf'></i> Attach
                                                </a>
                                                {$delete}
                                            </div>
                                        </div>
                                        <div class='row'>   
                                            <div class='col-xs-1 mb-5'></div>
                                            <div class='col-xs-10 mb-5' style='border: 1px solid #ccc; padding: 25px 5px 25px 5px;'>{$value->remark}</div>                                            
                                            <div class='col-xs-1 mb-5'></div>
                                        </div>
                                        <div class='row'>
                                            <div class='col-xs-12'><hr class='style19'/></div>
                                        </div>
                                    </div>
                                ";
                            }
                        ?>
                     
                    

                                     
                </fieldset>
            </div>
            <div class="panel-footer"> </div>
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



 
<div class="modal fade" id="modal-add-promotion" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">New Promotion</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                   
                    <div class="col-xs-4">
                        <label for="title"><?=Yii::t('common','Name')?></label>
                        <input type="text" id="title" name="title" class="form-control" />
                    </div> 
                    
                    <div class="col-xs-4">
                        <label for="start_date"><?=Yii::t('common','Start Date')?></label> 
                        <?=DatePicker::widget([
                                    'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                    'name'      => 'start_date',
                                    'options'   => ['id'    => 'start_date'],                                            
                                    'value'     => date('Y-m-d'),  
                                    'removeButton' => false,     
                                    'pluginOptions' => [
                                        'autoclose'=>true,
                                        'format' => 'yyyy-mm-dd'
                                    ]                                            
                            ]);
                        ?>  
                    </div>  

                    <div class="col-xs-4">
                        <label for="file"><?=Yii::t('common','File')?></label>
                        <input type="file" id="attach-file" class="form-control"/>
                    </div>                
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label for="remark" style="margin-top:20px;"><?=Yii::t('common','Description')?></label>
                        <textarea name="remark" id="remark" class="form-control" rows="3" required="required"></textarea>
                        <div id="img-preview-logo"></div>
                        
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-success save-promotion"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
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




    const addPromotion =(obj, callback) => {
        fetch("?r=customers/ajax/save-promotion", {
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

    const readURL = (input,div) => {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            let typeFile= input.files[0].name.split('.').pop().toLowerCase();
      
            let file     = typeFile == 'pdf' 
                            ? '<iframe src="" style="width: 100%; height: 300px;" frameborder="0" id="img-preview-render" type="application/pdf"></iframe>'
                            : '<img class="img-responsive img-rounded img-thumbnail item-img " style="max-height: 300px;" src="" id="img-preview-render">';
        
            $('#'+div).html(file);
            reader.onload = function (e) {
                $('#img-preview-render').fadeOut(100, function() { 
                    $('#img-preview-render').attr('src', e.target.result);                     
                }).fadeIn(400); 
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#attach-file").change(function(){
        readURL(this,'img-preview-logo');
        state.imgchange = true;
    });

    $('body').on('click', 'a.add-new-promotion', function(){
        $('#modal-add-promotion').modal('show');
        $('.loading').remove();
    })

    $('body').on('click', '.save-promotion', function(){

        let data = {
            id: parseInt("{$model->id}"),
            file: $('#img-preview-render').attr('src'),
            title: $('#title').val(),
            date: $('#start_date').val(),
            remark: $('#remark').val()
        } 

        $('#modal-add-promotion .modal-body').append(`<div class="loading" style="
        position: absolute; 
        width: 100%; 
        z-index: 100; 
        background: #040404cc;
        height: 100%;
        padding: 17% 0px 0px 50%;
        top: 0px;
        left: 0px;
        "> 
        <i class="fas fa-spinner fa-spin fa-2x text-green"></i> Loading ... 
        </div>`);
        
        addPromotion(data, res => {
            if(res.status==200){
                $('#modal-add-promotion').modal('hide');
                location.reload();
            }            
        })
    });

    const deleteAttach = (obj, callback) => {
        fetch("?r=customers/ajax/delete-promotion", {
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

    $('body').on('click','.delete-attach', function(){
        let el = $(this);
        let id = $(this).closest('.this-row').attr('data-key');
        if(confirm("Delete ?")){
            deleteAttach({id:id}, res =>{
                if(res.status==200){
                    el.closest('.this-row').remove();                    
                }
            });
        }
    });

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
  
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
