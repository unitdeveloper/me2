<?php $this->title = Yii::t('common','Stock').' GINOLR'; ?>
<style>
@media (max-width: 767px) {
    td.item-detail{
        padding-top:20px !important;
        padding-bottom:20px !important;
    }

}
</style>
<div class="font-roboto">
    <h3>สต๊อกสินค้าที่ GINOLR</h3>
    <table class="table table-hover table-bordered" id="export_table">
        <thead>
            <tr>
                <th class="hidden-xs">#</th>
                <th class="hidden-xs"><?=Yii::t('common','Code')?></th>
                <th><?=Yii::t('common','Product')?></th>
                <th class="text-right hidden"><?=Yii::t('common','Quantity')?></th>
                <th class="text-right hidden-xs"><span style="margin-right:10px;"><?=Yii::t('common','Stock')?> GINOLR</span></th>
            </tr>
        </thead>
        <tbody>
            <td colspan="5" class="text-center"  style="padding-top:50px; padding-bottom:50px;"><i class="fab fa-react fa-spin fa-5x text-aqua"></i></td>
        </tbody>
    </table>

</div>
<?php 
 
$Yii = 'Yii';
$LABEL_STOCK = Yii::t('common','Stock');
 
$js=<<<JS

  
const getDataFromAPI = (obj,callback) => {    
    fetch("?r=items/stock/my-stock", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {callback(res); })
    .catch(error => { console.log(error); });
}
 
$(document).ready(function(){
    getDataFromAPI({id:68},res => {   
        htmlRender(res);
    }); 
});

const htmlRender = (obj) => {

    let body = ``;
    let html = ``;

    obj.raws.map((model, key) => {
        body+= `<tr>
                    <td class="hidden-xs">` + (key + 1) +`</td>
                    <td class="hidden-xs">
                        <img src="` + model.img + `" width="20" class="img-responsive hidden-xs pull-left" style="margin-right:5px;" /> ` + model.code + `
                    </td>
                    <td class="item-detail">                     
                        <div>
                            <div class="pull-left"> 
                                <img src="` + model.img + `" width="50" class="img-responsive hidden-sm hidden-md hidden-lg" />
                                ` + model.name + `
                            </div>
                            <div class="stock-hidden pull-right hidden-sm hidden-md hidden-lg text-right"> ${LABEL_STOCK} <br />` + number_format(model.stock_customer) + `</div>
                        </div>
                    </td>
                    <td class="text-right hidden">` + number_format(model.stock) + `</td>
                    <td class="text-right hidden-xs">` + number_format(model.stock_customer) + `</td>
                </tr>`;
    })

    $('#export_table tbody').html(body);    
    var table = $('#export_table').DataTable({
                    "paging": false,
                    "searching": true
                });
        
    var data = table.column(1).data().sort();
    //Export to excel
    $("#export_table").tableExport({
        headings: true,                    // (Boolean), display table headings (th/td elements) in the <thead>
        footers: true,                     // (Boolean), display table footers (th/td elements) in the <tfoot>
        formats: ["csv"],    // (String[]), filetypes for the export ["xls", "csv", "txt"]
        fileName: "{$this->title}",         // (id, String), filename for the downloaded file
        bootstrap: true,                   // (Boolean), style buttons using bootstrap
        position: "bottom" ,                // (top, bottom), position of the caption element relative to table
        ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
        ignoreCols: null,                 // (Number, Number[]), column indices to exclude from the exported file
        ignoreCSS: "div.stock-hidden"   // (selector, selector[]), selector(s) to exclude from the exported file
    });   

}
 



      

JS;
$this->registerJS($js,\yii\web\View::POS_END);
 
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 