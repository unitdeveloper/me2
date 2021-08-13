<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dosamigos\multiselect\MultiSelect;
use \yii\web\View;
use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
use common\models\BankAccount;
use kartik\widgets\DatePicker;
$this->title = Yii::t('common', 'Item low stock');
$this->params['breadcrumbs'][] = $this->title;

use kartik\widgets\Select2;
use yii\web\JsExpression;

$escape = new JsExpression("function(m) { return m; }");

?>
<style>
.select2-container--krajee .select2-results > .select2-results__options {
    max-height: 500px !important;
    overflow-y: auto;
}
@media print{
    @page {
        margin-top:21px !important;
        /* size: A4 portrait;  */
    }
    body{
        font-family: 'saraban', 'roboto', sans-serif; 
        font-size:10px !important;
    }

    body table{
        font-size: 10px !important;
        width: 700px  !important;
        height: auto  !important;
        overflow-y: hidden !important;
        overflow: -moz-scrollbars-none;
        -ms-overflow-style: none; 
    } 
    
    .filter,
    .search-box{
      display: none;
    }
    
    a[href]:after {
      content: none !important;
    }
}
</style>

 
<div class="row filter"  ng-init="Title='<?=$this->title;?>'">
    <div class="col-xs-6">
    <?php

$startDate  = date('Y-m-').'01';
$endDate    = date('Y-m-d');

 

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
        'type'      => DatePicker::TYPE_RANGE,
        'name'      => 'ChequeSearch[fdate]',
        'value'     => Yii::$app->request->get('fdate') ? Yii::$app->request->get('fdate') : date('Y-m-').'01',
        'name2'     => 'ChequeSearch[tdate]',
        'value2'    => Yii::$app->request->get('tdate') ? Yii::$app->request->get('tdate') : date('Y-m-t'),
        'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
        'layout'    => $layout,
        'options'   => [ 'autocomplete' => 'off' ],
        'options2'  => [ 'autocomplete' => 'off' ],
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd'
        ],
        'pluginEvents' => [
            "hide" => "function(e) { 
                //$('body').find('.totals').html('xxx'); 
            }",
        ],
    ]);

?>
    
    </div>       
    <div class="col-sm-6 col-xs-6 pull-right">     
        <?= Select2::widget([
                'name' => 'bank-list',
                'data' => ArrayHelper::map(
                    BankAccount::find()->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])->all(), 
                    'id', 
                    function($model){
                        return '<img src="uploads/' .$model->banklist->imageFile .'" style="height:25px;" class="mr-10" /> '. $model->bank_no. ' '. $model->name. ' ' .$model->branch;
                    } 
                ),
                'options' => ['placeholder' => Yii::t('common','Select'). ' '. Yii::t('common','Bank')],
                'pluginOptions' => [
                    'escapeMarkup' => $escape,
                    'allowClear' => true
                ],
                'pluginEvents' => [
                    "change" => "function() { 
                                        getDataFromAPI({
                                            id: $(this).val(),
                                            fdate: $('input[name=\"ChequeSearch[fdate]\"]').val(),
                                            tdate: $('input[name=\"ChequeSearch[tdate]\"]').val()
                                        }, res => {       
                                        renderTable(res);                                       
                                    }); 
                                }",
                ]
            ]);
        ?>  
    </div>
 
    
</div>
<div class="row search-box" style="display:none;">
    <div class="col-xs-6 col-sm-3 pull-right mt-10 div-filter"></div>
    <div class="col-xs-6 col-sm-3 pull-right mt-10"> 
        <!-- <select class="form-control  mb-10 " name="vat-filter" >
            <option value="0"><?= Yii::t('common','Show All') ?></option>
            <option value="Vat">Vat</option>
            <option value="No">No Vat</option>
        </select>  -->
    </div>   
</div>

<div id="print-area">
    <div class="row" >
        <div class="col-xs-12"> 
            <div class="totals" ></div>
        </div>
    </div> 
    <div class="row" >
        <div class="col-xs-12">               
            <div id="export_wrapper" ><div class="mt-10 text-center" style="margin-top:50px;"><h3  > เลือกวันที่ และธนาคาร ที่ต้องการดูรายงาน </h3></div></div>
        </div>        
    </div>
</div>
 
 

<?php 
$company = \common\models\Company::findOne(Yii::$app->session->get('Rules')['comp_id']);
$Yii = 'Yii'; 
$jsOnHead =<<<JS

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>            
        </div>`;

const filterBox = `<div class="row ">                    
                    <div class="col-xs-12 pull-right">
                        <div class="input-group">
                            <input id="text-search" class="form-control" type="text" placeholder="{$Yii::t('common','Filter')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div>                 
                </div>`;

const getDataFromAPI = (obj,callback) => {
    $('body').find('#export_wrapper').html(loadingDiv)
    fetch("?r=accounting/report/bank-list-ajax", {
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

const renderTable = (raw) => {
    let total = 0;
    let vat = $('body').find('select[name="vat-filter"]').val();
    newData = raw.data;
    // if(vat === 'Vat'){
    //     newData = raw.data.filter(model => model.vat > 0 ? model : null);
    // }else if(vat === 'No'){
    //     newData = raw.data.filter(model => model.vat === 0 ? model : null);
    // }
    
    function compare( a, b ) {
        if ( a.date < b.date ){
            return -1;
        }
        return 0;
    }

    newData.sort( compare );       

    let html = `<table class="table table-hover" id="export_table">
                    <thead>
                        <tr class="mt-5">
                            <th colspan="6">
                                <div class="row">  
                                    <div class="col-sm-12">
                                        <div><h5 class="text-company-name pull-left">{$company->name} </h5> <span class="pull-right"> </span></div>
                                    </div>  
                                </div>
                                <div class="row">  
                                    <div class="col-sm-12"><h4>{$Yii::t('common','Cash receipt report')}</h4></div>                                          
                                </div>                                 
                            </th>
                        </tr>
                        <tr class="bg-gray">
                            <th>#</th>
                            <th>{$Yii::t('common','Cheque date')}</th>
                            <th>{$Yii::t('common','Invoice Date')}</th>                           
                            <th>{$Yii::t('common','No')}</th>
                            <th>{$Yii::t('common','Customer')}</th>
                            <th class="text-right font-roboto">{$Yii::t('common','Debit')}</th>
                            <th class="text-right font-roboto">{$Yii::t('common','Credit')}</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
    newData.map((model, key) => {
        html+= `<tr data-key="` + model.id + `">
                    <td>` + (key + 1) + `</td>
                    <td class="font-roboto">` + model.chequedate + `</td>
                    <td class="font-roboto">` + model.date + `</td>                    
                    <td class="font-roboto"><a href="?r=accounting%2Fcheque%2Fview&id=` + model.id + `" target="_blank">` + model.inv_no + `</a></td>
                    <td class="font-roboto">` + model.cust + `</td>
                    <td class="text-right font-roboto"> </td>
                    <td class="text-right font-roboto">` + number_format(model.balance.toFixed(2)) + `</td>
                </tr>`;
        total+= model.balance;
    })
    html+= '</tbody>';
    html+= `<tfoot>
                <tr>
                    <th colspan="5"></th>
                    <th class="text-right font-roboto bg-gray">{$Yii::t('common','Sum')}</th>
                    <th class="text-right font-roboto bg-gray">` + number_format(total.toFixed(2)) + `</th>
                </tr>
                <tr>
                    <th colspan="7">` + (newData.length) + ` {$Yii::t('common','items')}</th>
                </tr>
            </tfoot>`;
    html+= '</table>';
    $('body').find('#export_wrapper').html(html);
    $('body').find('.search-box').show();
    setTimeout(() => {        
        var table = $('#export_table').DataTable({
                        "paging": false,
                        "searching": false,
                        "info": false
                    });        
        var data = table
            .column( 1 )
            .data()
            .sort();
        
        // Export to excel
        
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

}

JS;
$this->registerJS($jsOnHead,\yii\web\View::POS_HEAD);


$js=<<<JS


const filterTable  = (search) => {
    $("#export_table  tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
    });

    $('#export_table tbody tr').each((key,value) => {
        $(value).find('.key').html(key + 1);
    });
}


$(document).ready(function(){    
    $('body').find('.div-filter').html(filterBox);
})


$("body").on("keyup", '#text-search', function() {
    var text = $(this).val().toLowerCase();
    filterTable(text);
    topFunction();
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
  
 