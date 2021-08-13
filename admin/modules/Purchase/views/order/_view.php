<div class="row">
    <div class="col-xs-12 text-right">
        <i class="far fa-arrow-alt-circle-up fa-2x  pointer text-primary"  data-toggle="collapse" href="#view-detail" aria-expanded="false" aria-controls="view-detail"></i>
    </div>
</div>
<div class="row">
    <div class="col-xs-3">
        <h4><?=$model->doc_no;?></h4>        
        <div class="panel panel-info">
            <div class="panel-body">
            <h5><?=$model->vendor_name;?></h5>
            </div>
        </div>        
    </div>
    <div class="col-xs-9">
        <h4><a class="btn btn-default-ew btn-xs" data-toggle="modal" href='#modal-pay-invoice'><i class="fas fa-caret-up"></i></a> รับสินค้า</h4>       
        <div class="panel panel-warning rc-list"></div>
        <br />
        <br />
        <h4><a class="btn btn-default-ew btn-xs" data-toggle="modal" href='#modal-pay-invoice'><i class="fas fa-caret-up"></i></a> ใบตั้งหนี้</h4>        
        <div class="panel panel-danger pay-inv-list"></div>
        <br />
        <br />
        <h4><a class="btn btn-default-ew btn-xs" data-toggle="modal" href='#modal-pay-invoice'><i class="fas fa-caret-up"></i></a> จ่ายชำระ</h4>        
        <div class="panel panel-success payment-list"></div>
    </div>
</div>
<div class="row">
    <hr class="mb-10" />
</div>
<div class="modal fade modal-full" id="modal-pay-invoice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #f5f5f5 !important; color:#3b3b3b;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">ใบตั้งหนี้</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer" style="background: #f5f5f5 !important; color:#3b3b3b;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fas fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-primary"><i class="far fa-save"></i> <?= Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>

<?php 
 
$Yii = 'Yii';
$id  = $model->id;
$js=<<<JS

let state = {
    data : []
};


const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <img src="images/icon/loader2.gif" height="122"/>
        </div>`;
 

const getDataFromAPI = (obj,callback) => {
    fetch(`?r=Purchase/order/view-ajax`, {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
        state.data = res.data;                      
        callback(res);
    })
    .catch(error => {
        console.log(error);
    });
}

const renderRcTable = (data, render) => {
    let html    = '';
    let body    = '';
    console.log(data.id);
    data.rc.length > 0 
        ? data.rc.map((model, key) => {
                body+= `<tr>
                            <td>` + (key + 1) + `</td>
                            <td>
                                <a href="index.php?r=warehousemoving%2Freceive%2Fview&id=` + model.id + `&po=`+ data.id +`"  target="_blank">
                                    ` + model.no + `  
                                </a>
                            </td>
                            <td>
                                ` + model.ext_doc + `
                            </td>
                        </tr>`;
            })
        : body+= `<tr><td colspan="2"> {$Yii::t('common','No data')} </td></tr>`;

    html+= `<table class="table ">
                    <thead>
                        <tr class="bg-warning">
                            <th style="width:50px">#</th>
                            <th style="width:150px">{$Yii::t('common','Document No')}</th>
                            <th>{$Yii::t('common','External Document')}</th>
                        </tr>
                    </thead>
                    <tbody>` + body + `</tbody>
                </table>`; 

    $('body').find('.rc-list').html(html);
}


const renderInvListTable = (data, render) => {
    let html    = '';
    let body    = '';
    data.length > 0 
        ? data.map((model, key) => {
                body+= `<tr>
                            <td>` + (key + 1) + `</td>
                            <td>` + model.no + `</td>
                        </tr>`;
            })
        : body+= `<tr><td colspan="2"> {$Yii::t('common','No data')} </td></tr>`;

    html+= `<table class="table">
                    <thead>
                        <tr class="bg-danger">
                            <th style="width:50px">#</th>
                            <th>{$Yii::t('common','Document No')}</th>
                        </tr>
                    </thead>
                    <tbody>` + body + `</tbody>
                </table>`; 

    $('body').find('.pay-inv-list').html(html);
}

const renderPaymentTable = (data, render) => {
    let html    = '';
    let body    = '';
    data.length > 0 
        ? data.map((model, key) => {
                body+= `<tr>
                            <td>` + (key + 1) + `</td>
                            <td>` + model.no + `</td>
                        </tr>`;
            })
        : body+= `<tr><td colspan="2"> {$Yii::t('common','No data')} </td></tr>`;

    html+= `<table class="table">
                    <thead>
                        <tr class="bg-success">
                            <th style="width:50px">#</th>
                            <th>{$Yii::t('common','Document No')}</th>
                        </tr>
                    </thead>
                    <tbody>` + body + `</tbody>
                </table>`; 

    $('body').find('.payment-list').html(html);
}


$(document).ready(function(){
    setTimeout(() => {
        getDataFromAPI({
            id: $id
        }, res => {
            renderRcTable(res);
            renderInvListTable(res.pay_inv);
            renderPaymentTable(res.payment);
        })
    }, 500);
});



$('#modal-pay-invoice').on('show.bs.modal', function(){
    $('body').attr('style', ' ');
});

$('#modal-pay-invoice').on('hide.bs.modal', function(){
     console.log('hide');
});

JS;
$this->registerJS($js,\yii\web\View::POS_END);
 
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 