<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMystore */

$this->title = Yii::t('common', 'Reorder Point');

?>
<div class="item-mystore-update" ng-init="Title='<?=$this->title;?>'">

    <h3><?= Html::encode($this->title) ?></h3>

    <div style="position:fixed; top:40%; right:50%; z-index:2010; display:none;" id="loading"><i class="fa fa-refresh fa-spin fa-3x text-red"></i></div>

    <div id="render-table"></div>

</div>
<?php 
 
$Yii = 'Yii';
 
$js=<<<JS
 
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

    data.raws.map((model, i) => {
        let stock = model.stock * 1;
        body+=`
            <tr>
                <td class="bg-gray">` + (i + 1) + `</td>
                <td><a href="?r=items%2Fitems%2Fview&id=` + model.id + `" target="_blank">` + model.code + `</a></td>
                <td>` + model.name + `</td>
                <td class="text-right">` + number_format(stock.toFixed(0)) + `</td>
                <td>` + model.safety_stock + `</td>
                <td>` + model.reorder_point + `</td>
                <td>` + model.minimum_stock + `</td>

            </tr>
        `;
    })

    let table = `
            <table class="table table-bordered font-roboto">
                <thead>
                    <tr>
                        <th class="bg-gray">#</th>
                        <th class="bg-gray">{$Yii::t('common','Code')}</th>
                        <th class="bg-gray">{$Yii::t('common','Name')}</th>
                        <th class="bg-gray">{$Yii::t('common','Stock')}</th>
                        <th class="bg-gray">{$Yii::t('common','Safety Stock')}</th>
                        <th class="bg-gray">{$Yii::t('common','Reorder Point')}</th>
                        <th class="bg-gray">{$Yii::t('common','Minimum Stock')}</th>
                    </tr>
                </thead>
                <tbody>` + body + `</tbody>
            </table>
    `;

    callback(table);
}

 
$(document).ready(function() {
    $('#loading').show();
    getDataAPI(res => {
        setTimeout(() => {
            renderTable(res, html => {
                $('#render-table').html(html);
                $('#loading').hide();
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