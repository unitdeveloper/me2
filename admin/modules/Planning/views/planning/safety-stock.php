<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMystore */

$this->title = Yii::t('common', 'Safety Stock');

?>
<div class="row" style="background-color:#e1feff; height:50px; padding-top:10px; margin-top:-15px; border-bottom:1px solid #ccc; margin-bottom:15px;">
    <div class="col-xs-8">
        <div class="input-group hidden-sm hidden-md hidden-lg">
            <input id="search" class="form-control" type="text" placeholder="<?=Yii::t('common','Filter')?>..." />
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
        </div> 
    </div>
    <div class="col-xs-4  text-right"><?=Html::a('<i class="fas fa-wrench"></i> '.Yii::t('common','Setup'),['index'],['class' => 'btn btn-warning'])?></div>
</div>
<div class="item-mystore-update" ng-init="Title='<?=$this->title;?>'">

    <div style="position:fixed; top:40%; right:50%; z-index:2010; display:none;" id="loading"><i class="fa fa-refresh fa-spin fa-3x text-red"></i></div>

    <div id="render-table" class="table-responsive"></div>

</div>
<?php 
 
$Yii = 'Yii';
 
$js=<<<JS
 


const filterBox = `<div class="row search-box">                    
                    <div class="col-md-4 col-sm-6 col-xs-12 pull-right">
                        <div class="input-group hidden-xs">
                            <input id="search" class="form-control" type="text" placeholder="{$Yii::t('common','Filter')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div>                 
                </div>`;


const getDataAPI = (callback) => {
    fetch("?r=Planning%2Fplanning%2Freorder-ajax", {
        method: "POST",
        body: JSON.stringify(),
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

const renderTable = (data, callback) => {
    let body =``;
    let newData = data.raws;

    function compare( a, b ) {
        if ( a.stock < b.stock ){
            return -1;
        }
        return 0;
    }

    newData.sort( compare );


    newData.map((model, i) => {
        let stock = model.stock * 1;
        body+=`
            <tr data-key=` + model.id + `>
                <td class="bg-gray">` + (i + 1) + `</td>
                <td class="item-info"><img class="td-item-img pointer" style="width:20px;" src="` + model.img + `"/></td>
                <td class="item-info"><a href="?r=items%2Fitems%2Fview&id=` + model.id + `" target="_blank">` + model.code + `</a></td>
                <td>` + model.name + `</td>
                <td class="text-right bg-yellow ">` + number_format(stock.toFixed(0)) + `</td>
                <td class="text-right stock-change" style="background-color: #f2ffc0;">` + number_format(model.safety_stock) + `</td>
                <td class="text-right stock-reorder" style="background-color: #c0f0ff;">` + number_format(model.reorder_point) + `</td>
                <td class="text-right stock-minimum" style="background-color: #edc0ff;">` + number_format(model.minimum_stock) + `</td>
                
            </tr>
        `;
    })

    let table = `
            <table class="table table-bordered font-roboto" id="export_table">
                <thead>
                    <tr class="mt-5 ">
                        <th colspan="8" class="text-right">
                            ` + filterBox + ` 
                        </th>
                    </tr>
                    <tr>
                        <th class="bg-primary" style="width:10px;">#</th>
                        <th class="bg-dark thead-item-img" style="width:30px;">{$Yii::t('common','Images')}</th>   
                        <th class="bg-dark" style="min-width:130px;">{$Yii::t('common','Code')}</th>
                        <th class="bg-dark" style="min-width: 220px;">{$Yii::t('common','Items')}</th>
                        <th class="bg-dark text-right" style="width:80px;"><span style="margin-right:10px;">{$Yii::t('common','Stock')}</span></th>
                        <th class="bg-dark text-right" style="width:80px;"><span style="margin-right:10px;">{$Yii::t('common','Safety Stock')}</span></th>
                        <th class="bg-dark text-right" style="width:80px;"><span style="margin-right:10px;">{$Yii::t('common','Reorder Point')}</span></th>
                        <th class="bg-dark text-right" style="width:80px;"><span style="margin-right:10px;">{$Yii::t('common','Minimum Stock')}</span></th>
                         
                    </tr>
                </thead>
                <tbody>` + body + `</tbody>
            </table>
    `;

    callback(table);
}


const filterTable  = (search) => {
    $("#export_table  tbody tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
    });

    $('#export_table tbody tr').each((key,value) => {
        $(value).find('.key').html(key + 1);
    });

    $('#export_table').attr('style','width:100%');
}

$("body").on("keyup", '#search', function() {
    var value = $(this).val().toLowerCase();
    filterTable(value);
 
});

 
$(document).ready(function() {
    $('#loading').show();
    getDataAPI(res => {
        setTimeout(() => {
            renderTable(res, html => {
                $('#render-table').html(html);
                var table = $('#export_table').DataTable({
                                "paging": true,
                                'pageLength' : 1000,
                                "searching": false
                            });
                
                var data = table
                    .column( 1 )
                    .data()
                    .sort();

                $('#loading').hide();

                $("#export_table").tableExport({
                    headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
                    footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
                    formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
                    fileName: "{$this->title}",         // (id, String), filename for the downloaded file
                    bootstrap: true,                    // (Boolean), style buttons using bootstrap
                    position: "bottom" ,                // (top, bottom), position of the caption element relative to table
                    ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
                    ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
                    ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file          
                });

            });
        }, 100);
    });

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