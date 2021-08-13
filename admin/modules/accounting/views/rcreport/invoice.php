<?= $this->render('_tax-invoice_header'); ?>

<div class="render-table"></div>
 
<div class="modal fade" id="modal-invoice-detail">
    <div class="modal-dialog modal-primary" style="min-width: 830px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>  
            <div class="modal-body">
                <div class="row">                    
                    <div class="col-xs-12 renders">                     
                        <h4 ><?= Yii::t("common","Date") ?> : <span class="date-inv-modal"></span></h4>              
                        <table class="table table-bordered table-warning">
                            <thead>
                                <tr class="bg-gray">
                                    <th>#</th>
                                    <th><?= Yii::t("common","Items") ?></th>
                                    <th><?= Yii::t("common","Name") ?></th>
                                    <th class="text-right"><?= Yii::t("common","Quantity") ?></th>
                                </tr>
                            </thead>
                            <tbody class="tbody-modal"></tbody>
                        </table> 
                    </div> 
                    <div class="col-xs-12">
                        <div class="loading" style="height:50px;"></div>
                    </div>
                </div>
            </div>         
            <div class="modal-footer ">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <a  href="#" class="btn btn-primary btn-modal-footer" target="_blank"><i class="fa fa-print"></i> <?= Yii::t('common','Print invoice')?></a>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" id="payment-detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-green">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
 
            </div>
        </div>
    </div>
</div>

<?php 
$Yii = 'Yii';
$today = date('Y-m-d');
$JS=<<<JS

let state = {
        data: [],
        search: '',
        vat: ''
    };

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
            <h4 class="years-callulate"></h4>
            <img src="images/icon/loader2.gif" height="122"/>             
        </div>`;

const filterBox = `<div class="row search-box">                    
                    <div class="col-md-4 col-sm-6 col-xs-12 pull-right">
                        <div class="input-group">
                            <input id="text-search" class="form-control" type="text" placeholder="{$Yii::t('common','Filter')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div>                 
                </div>`;

const getDataFromAPI = (obj,callback) => {
    fetch("?r=accounting/rcreport/tax-invoice-ajax", {
        method: "POST",
        body: JSON.stringify({tdate:obj.tdate, fdate:obj.fdate, cust:obj.cust, sale:obj.sale, vat:obj.vat}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {   
        if(res.status===200){
            state.data = res;
            callback(res);
        }else{
            swal('{$Yii::t("common","Warning")} : ' + res.message, res.count, "warning");
        }
        
    })
    .catch(error => {
        console.log(error);
    });
}


const renderTable = (res) => {
    let newData = res.data.raw;

    let TotalDis    = 0;
    let TotalSum    = 0;
    let TotalVat    = 0;
    let grandTotal  = 0;
    let TotalReceipt= 0;
    
    let html = `<table class="table table-bordered" id="export_table">
                    <thead>
                        <tr class="mt-5">
                            <th colspan="12">
                                <div class="row">  
                                    <div class="col-sm-12">
                                        <div><h5 class="text-company-name pull-left">` + res.data.compName +`</h5> <span class="pull-right">` + (newData.length) + ` {$Yii::t('common','items')}</span></div>
                                    </div>  
                                    <div class="col-sm-12"><h4>{$Yii::t('common','Invoice report')}</h4></div>
                                    <div class="col-sm-12">
                                        <div class="pull-right">{$Yii::t('common','Date')} : <span class="show-date">{$today}</span></div>    
                                    </div>        
                                </div>
                                <div class="row"> 
                                    <div class="col-md-2 col-sm-6">
                                        <div>{$Yii::t('common','From date')} <span class="from-date ml-10">` + res.data.fdate +`</span></div>
                                        <div>{$Yii::t('common','Customer code')}</div>
                                        <div>{$Yii::t('common','Sale People')}</div>
                                    </div>         
                                    <div class="col-md-10 col-sm-6">
                                        <div>{$Yii::t('common','To Date')} <span class="to-date ml-10">` + res.data.tdate +`</span></div>
                                        <div class="text-customer-code">` + res.data.custName +`</div>
                                        <div class="text-sale-code">` + res.data.saleName +`</div>
                                    </div>              
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-info">  
                            <th colspan="5" class="text-right"><h4>{$Yii::t('common','Sum')}</h4></th>
                            <th class="text-right text-orange"><h4 class="total-discount"></h4></th>
                            <th class="text-right text-blue"><h4 class="total-amount"></h4></th>
                            <th class="text-right text-red"><h4 class="total-vat"></h4></th>
                            <th class="text-right text-green"><h4 class="total-balance"></h4></th>                             
                            <th colspan="2"></th>
                            <th class="text-right text-orange"><h4 class="total-payment"></h4></th>
                        </tr>
                        <tr class="bg-gray">
                            <th>#</th>
                            <th class="hidden">{$Yii::t('common','Vat')}</th>
                            <th>{$Yii::t('common','dd/mm/yy')}</th>
                            <th>{$Yii::t('common','No.')}</th>
                            <th>{$Yii::t('common','Customer')}</th>
                            <th>{$Yii::t('common','Sale People')}</th>
                            <th class="text-right">{$Yii::t('common','Discount')}</th>
                            <th class="text-right">{$Yii::t('common','Amount')}</th>
                            <th class="text-right">{$Yii::t('common','Vat')}</th>
                            <th class="text-right">{$Yii::t('common','Total')}</th>
                            <th >{$Yii::t('common','Due date')}</th>
                            <th>{$Yii::t('common','Sale Order')}</th>
                            <th class="text-right">{$Yii::t('common','Payment')}</th>
                        </tr>
                    </thead>`;
 
       

        if(state.search != ''){
            newData =  newData.filter(model => 
            (model.no.toLowerCase().indexOf(state.search) > -1 || model.custName.toLowerCase().indexOf(state.search) > -1) 
            ? model : null);
        }

        if(state.vat === 'Vat'){
            newData = newData.filter(model => (model.vat * 1) > 0 ? model : null);
        }else if(state.vat === 'No'){
            newData = newData.filter(model => (model.vat * 1) === 0 ? model : null);
        }

        
        
        
        function compare( a, b ) {
            if ( a.no < b.no ){
                return -1;
            }
            return 0;
        }

        newData.sort( compare );
        
        let i = 0;
        html+= '<tbody  class="table-renders">';
        newData.map(model => {
            i++;
            let payment = 0; model.pay.map(el => { payment+= parseFloat(el.balance); });
            let sumline = model.invat == 1 ? model.sumline * 1 : model.before;
            TotalDis    += model.revenue == 0 ? model.discount : 0;
            TotalSum    += model.revenue == 0 ? sumline : 0;
            TotalVat    += model.revenue == 0 ? model.incvat : 0;
            grandTotal  += model.revenue == 0 ? model.balance : 0;
            TotalReceipt+= model.revenue == 0 ? payment : 0;

            html+= '<tr data-key="'+ model.id +'" style="'+(model.revenue == 1 ? 'text-decoration: line-through; text-decoration-color: #ff6565; background: #7d7d7d !important; color:#fff;' : '')+'" >';
            html+= '    <td class="text-center">'+ i +'</td>';
            html+= '    <td class="hidden">' + model.vat + '</td>';
            html+= '    <td>' + model.date + '</td>';
            html+= '    <td class="pointer invoice-detail text-info" data-id="'+ model.id +'" data-val="'+ model.no +'">' + model.no + '</td>';
            html+= '    <td>' + model.custName + '</td>';
            html+= '    <td>' + model.saleCode + '</td>';
            html+= '    <td class="text-right">' + model.discount + '</td>';
            html+= '    <td class="text-right">' + number_format(sumline.toFixed(4)) + '</td>';
            html+= '    <td class="text-right">' + number_format(model.incvat.toFixed(4)) + '</td>';
            html+= '    <td class="text-right">' + number_format(model.balance.toFixed(4)) + '</td>';
            html+= '    <td>' + model.due + '</td>';
            html+= '    <td><a href="?r=SaleOrders%2Fsaleorder%2Fview&id=' + model.orderId + '" target="_blank">' + model.orderNo + '</a></td>';
            html+= '    <td class="' + (payment <= 0 ? 'text-right' : 'bg-orange text-right payment-detail-modal pointer' )+ '" data-id="' + model.id + '" data-val="' + payment + '" data-no="'+ model.no + '">' + number_format(payment.toFixed(2)) + '</td>';
            html+= '</tr>';
        })
        html+= '</tbody>';        
        html+= `<tfoot>
                    <tr class="bg-primary">  
                        <th colspan="5" class="text-right">{$Yii::t('common','Sum')}</th>
                        <th class="text-right">` + number_format(TotalDis.toFixed(2)) + `</th>
                        <th class="text-right">` + number_format(TotalSum.toFixed(2)) + `</th>
                        <th class="text-right">` + number_format(TotalVat.toFixed(2)) + `</th>
                        <th class="text-right">` + number_format(grandTotal.toFixed(2)) + `</th>                             
                        <th colspan="2"></th>
                        <th class="text-right">` + number_format(TotalReceipt.toFixed(2)) + `</th>
                    </tr>
                </tfoot>`;
        html+= `</table>`;

        
        $('.text-headoffice').html(res.data.headOffice ? '{$Yii::t("common","Head Office")}' : '{$Yii::t("common","Branch")}' )

    if(res.data.raw.length > 0){
        $('.render-table').html(html);

        $('body').find('.total-discount').html(number_format(TotalDis.toFixed(2)));
        $('body').find('.total-amount').html(number_format(TotalSum.toFixed(2)));
        $('body').find('.total-vat').html(number_format(TotalVat.toFixed(2)));
        $('body').find('.total-balance').html(number_format(grandTotal.toFixed(2)));
        $('body').find('.total-payment').html(number_format(TotalReceipt.toFixed(2)));
        
        $("#export_table").tableExport({
            headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
            footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
            formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
            fileName: "{$this->title}",         // (id, String), filename for the downloaded file
            bootstrap: true,                    // (Boolean), style buttons using bootstrap
            position: "top" ,                   // (top, bottom), position of the caption element relative to table
            ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
            ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
            ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file          
        }); 

        $('body').find('caption').append(filterBox);
        $('body').find('caption button').text('{$Yii::t("common","Export to Excel")}')         
        $('body').find('#text-search').val(state.search).focus();
        //$('body').find('#vat-change').val(state.vat ? state.vat : '0');        
        $('body').find('.vat-status').html('{$Yii::t("common","Vat")} : ' + $('select[id="vat-change"] option:selected').text());
 
        
       

    }else{
        $('.render-table').html('<div class="row"><div class="col-xs-12 text-center"><h4>{$Yii::t("common","No data found")}</h4></div></div>');
    }
    
    var table = $('#export_table').DataTable({
                        "paging": false,
                        "searching": false
                    });
        
}



$(document).ready(function(){
    $('.render-table').html(loadingDiv);
    getDataFromAPI({
        sale: '',
        cust: '',
        fdate: $('body').find('input[name="fdate"]').val(),
        tdate: $('body').find('input[name="tdate"]').val(),
        vat: '0'
    },res => {

        renderTable(res);
    })
});


$('body').on('submit',"#search-item", function(event) {
    event.preventDefault();
    $('.render-table').html(loadingDiv);
 
    getDataFromAPI({
        sale: $('body').find('select[name="search-from-sale"]').val(),
        cust: $('body').find('select[name="customer"]').val(),
        fdate: $('body').find('input[name="fdate"]').val(),
        tdate: $('body').find('input[name="tdate"]').val(),
        vat:   $('body').find('select[name="vat-filter"]').val(),
    },res => {

        renderTable(res);
    })

})

 
 
$('body').on('click','.payment-detail-modal',function(){
    let id  = $(this).data('id');
    let val = $(this).data('val');
    let data= state.data.data.raw.filter(model => model.id === id ? model : null);
    $('#payment-detail .modal-title').html($(this).attr('data-no'));
    $('#payment-detail .modal-body').html('');

    console.log(data);
    if(val > 0){
        let html = '<table class="table table-bordered">';
        html+= `<thead>
                    <tr>
                        <th style="width:150px;">{$Yii::t('common','Date')}</th>
                        <th>{$Yii::t('common','From')}</th>
                        <th>{$Yii::t('common','To account')}</th>
                        <th>{$Yii::t('common','Remark')}</th>
                        <th class="text-right"  style="width:100px;" >{$Yii::t('common','Amount')}</th>
                    </tr>
                </thead>`;
        html+= `<tbody>`;
        data.map(el => {
            el.pay.map(model => {

            
            html+= `<tr data-key="`+ model.id +`">
                        <td>` + model.datetime + `</td>
                        <td><a href="?r=accounting%2Fcheque%2Fview&id=`+ model.id +`" target="_blank" >` + model.from + ` ` + (model.type != 'Cash' ? model.bank : ``) + `</a></td>    
                        <td>` + model.toNo + ` (` + model.to +`)</td>    
                        <td>` + model.remark + `</td>                    
                        <td class="text-right bg-orange">` + number_format(parseFloat(model.balance).toFixed(2)) + `</td>                        
                    </tr>`;

            })
        })
        html+= `</tbody>`;
        html+= '</table>';
        $('#payment-detail .modal-body').html(html);
        $('#payment-detail').modal('show');
    }
    
})



 
$('body').on('click','.invoice-detail',function(){
    let id  = $(this).data('id');
    let val = $(this).data('val');
    $('#modal-invoice-detail .modal-body .renders').html('');
    $('#modal-invoice-detail .modal-body .loading').html('loading <i class="fa fa-refresh fa-spin fa-2x fa-fw"></i>').fadeIn();
    $('#modal-invoice-detail').modal('show');
    $('#modal-invoice-detail .modal-title').html(val);
    let hrefLink= '';

    const child = (detail,qty) => {
        let childTable = ' ';
        let x = 0;
        detail.map(el => {
            x++;
            childTable+= el.detail.length >= 1 
                        ? child(el.detail, qty) 
                        : `  <tr class="bg-green">
                                <td></td>
                                <td><img src="` + el.img + `" width="35"/></td>
                                <td><a href="?r=items%2Fitems%2Fview&id=`+ el.item +`" target="_blank" class="text-white" >`+ el.code +`</td>
                                <td>`+ el.name + `</td>  
                                <td class="text-right" >`+ parseInt(el.qty) + `</td>
                                <td class="text-right" style="min-width: 40px;">`+ parseInt(el.qty * qty) +`</td>
                                <td> </td>
                            </tr>`;
        });
        childTable+= ' ';
        return childTable;
    }

    fetch("?r=accounting/ajax/invoice-by-bom", {
        method: "POST",
        body: JSON.stringify({id:id}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {   

        if(res.status===200){                       
            $('#modal-invoice-detail .modal-body .renders').slideUp('fast');

            hrefLink= '?r=accounting%2Fposted%2Fprint-inv&id='+ btoa(id) + '&footer=1';             
        
            let html = '<table class="table table-bordered">';
                html+= `<thead>
                            <tr class="bg-gray">
                                <th style="width:50px;">#</th>
                                <th colspan="2">{$Yii::t('common','Code')}</th>
                                <th>{$Yii::t('common','Name')}</th>
                                <th colspan="2" class="text-right"  style="width:100px;">{$Yii::t('common','Quantity')}</th>
                                <th class="text-right"  style="width:100px;" >{$Yii::t('common','Price')}</th>
                            </tr>
                        </thead>`;
                html+= `<tbody>`;
            let i = 0;
            res.data.raw.map(model => {
                i++;
                html+= `<tr data-key="` + model.item + `">
                            <td><div style="position:absolute;">` + i + `</div> <img src="` + model.img + `" width="35"/></td>
                            <td colspan="2"><a href="?r=items%2Fitems%2Fview&id=`+ model.item +`" target="_blank" class="text-white" >` + model.code + `</a></td>    
                            <td>` + model.name + `</td>    
                            <td colspan="2" class="text-right">` + number_format(parseFloat(model.qty)) + `</td>                    
                            <td class="text-right">` + number_format(parseFloat(model.price).toFixed(2)) + `</td>                        
                        </tr>`;
                html+= model.detail.length >= 1 ? child(model.detail,model.qty) : '';
            })
                html+= `</tbody>`;
                html+= '</table>';
                let body = `
                            <div class="row">
                                <div class="col-xs-12">` + res.data.custName + `</div>
                            </div>`;

                $('#modal-invoice-detail .modal-body .renders').html(body + " " + html);

                setTimeout(() => {
                    $('#modal-invoice-detail .modal-body .loading').slideUp();
                    $('#modal-invoice-detail .modal-body .renders').slideDown('slow');
                }, 1000);
                
                $('#modal-invoice-detail .btn-modal-footer').attr('href',hrefLink);

        }else{
            swal('{$Yii::t("common","Warning")} : ' + res.message, res.count, "warning");
        }
        
    })
    .catch(error => {
        console.log(error);
        $('#modal-invoice-detail .modal-body').html(error);
    });

    
    
})
 

$('#modal-invoice-detail').on('hidden.bs.modal',function(){
    $('#modal-invoice-detail .modal-body .renders').html('');
})

 
JS;

$this->registerJS($JS,\yii\web\View::POS_END);
?>
<?php $this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
