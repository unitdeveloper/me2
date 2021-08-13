<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dosamigos\multiselect\MultiSelect;

use common\models\ItemsHasGroups;
use common\models\ItemgroupCommon;
 
$this->title = Yii::t('common', 'Item low stock');
$this->params['breadcrumbs'][] = $this->title;
 
?>

<div class="row search-box">
    <div class="col-sm-6 text-right"></div>
    <div class="col-sm-6 col-xs-12 pull-right mb-10">
        <div class="row">
            <div class="col-sm-9 col-xs-6">
                <input id="myInput" class="form-control mb-10" type="text" placeholder="<?=Yii::t('common','Search')?>...">
            </div>
            <div class="col-sm-3 col-xs-6">                 
                <button type="button" class="btn pull-right btn-default-ew" id="btn-refresh"><i class="fa fa-refresh"></i> <?= Yii::t('common','ReCalculate')?></button>
            </div>
        </div>        
    </div>
</div>

<div ng-init="Title='<?=$this->title;?>'">
    <div class="row" >
        <div class="col-xs-12 ">    
            <div class="wmd-view-topscroll">
                <div class="scroll-div1" style="display: none;">
                </div>
            </div>     
            <div id="export_wrapper" class="table-responsive wmd-view"></div>
        </div>
    </div>
</div>
 
 

<?php 
 
$Yii = 'Yii'; 
$js=<<<JS

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>            
        </div>`;

const getDataFromAPI = (obj,callback) => {
    $('body').find('#export_wrapper').html(loadingDiv)
    fetch("?r=items/report/low-ajax", {
        method: "POST",
        body: JSON.stringify({obj}),
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
const renderTable = (data) => {

    function compare( a, b ) {
        if ( a.stock < b.stock ){
            return -1;
        }
        return 0;
    }

    data.sort( compare );   

    let html = `<table class="table table-hover" id="export_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Barcode</th>
                            <th>Name</th>
                            <th class="text-right">Inven</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
    data.map((model, key) => {
        html+= `<tr data-key="` + model.id + `">
                    <td>` + (key + 1) + `</td>
                    <td class="font-roboto"><a href="?r=items%2Fitems%2Fview&id=` + model.id + `" target="_blank">` + model.code + `</a></td>
                    <td class="font-roboto">` + model.barcode + `</td>
                    <td>` + model.name + `</td>
                    <td class="text-right font-roboto">` + number_format(model.stock) + `</td>
                </tr>`;
    })
    html+= '</tbody>';
    html+= '</table>';
    $('body').find('#export_wrapper').html(html);
    setTimeout(() => {        
        var table = $('#export_table').DataTable({
                        "paging": false,
                        "searching": false
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
            position: "top" ,                // (top, bottom), position of the caption element relative to table
            ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
            ignoreCols: null,                 // (Number, Number[]), column indices to exclude from the exported file
            ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
        });

    }, 500);
    
}

const filterTable  = (search) => {
    $("#export_table  tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
    });

    $('#export_table tbody tr').each((key,value) => {
        $(value).find('.key').html(key + 1);
    });
}


$(document).ready(function(){    
    getDataFromAPI({id:1}, res => {       
        renderTable(res);
        console.log(res);
    });
})


$("#myInput").on("keyup", function() {
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
  
 