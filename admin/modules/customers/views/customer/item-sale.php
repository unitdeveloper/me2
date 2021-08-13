<?= $this->render('_item-sale_header'); ?>
<div class="row">    
    <div class="col-sm-12">
        <div><?= Yii::t('common','Customer')?> : <span class="customer-name"></span></div>
        <div><?= Yii::t('common','Sales')?> : <span class="sale-name"></span></div>
        <div><?= Yii::t('common','From Date')?> : <span class="from-date"></span>,  <?= Yii::t('common','To Date')?> : <span class="to-date"></span></div>
        <div class="vat-status"></div>
        <div class="count-item"></div>
    </div>
</div>
<div class="table-renders"></div>


<div class="modal fade" id="modal-invoice-detail">
    <div class="modal-dialog modal-primary" style="min-width: 830px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>  
            <div class="modal-body">
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
            <div class="modal-footer ">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?= Yii::t('common','Close')?></button>
                <a  href="#" class="btn btn-primary btn-modal-footer" target="_blank"><i class="fa fa-print"></i> <?= Yii::t('common','Print invoice')?></a>
            </div>
        </div>
    </div>
</div>

<?php 
$Yii = 'Yii';
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
                            <input id="text-search" class="form-control" type="text" placeholder="{$Yii::t('common','Search items')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12 pull-right">
                        <select class="form-control" id="vat-change" >
                            <option value="0">{$Yii::t('common','All')}</option>
                            <option value="Vat">Vat</option>
                            <option value="No">No Vat</option>
                        </select> 
                    </div>
                </div>`;

const getDataFromAPI = (obj,callback) => {
    fetch("?r=customers%2Fcustomer%2Fitem-sale-ajax", {
        method: "POST",
        body: JSON.stringify({fdate:obj.fdate, tdate:obj.tdate, cust:parseInt(obj.cust), sale:obj.sale}),
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
    let newData = [];
    let html = `<table class="table table-bordered font-roboto" id="export_table">
                <tbody>
                <tr  class="bg-dark">
                    <td class="text-center">#</td>
                    <td colspan="3">{$Yii::t("common","Items")}</td>
                    <td colspan="3" class="text-right">{$Yii::t("common","Code")}</td>
                    <td class="text-right">{$Yii::t("common","Total Price")}</td>
                </tr>`;
        

        if(state.vat !== '0'){
            res.data.raw.map(model => {
                if (newData.some(e => ((e.item === model.item) && (e.vat === model.vat)) )) {
                        // ถ้ามีอยู่แล้ว                    
                        newData = newData.map(el => el.item === model.item
                                        ?   Object.assign({}, el, {
                                                qty : parseFloat(el.qty) + parseFloat(model.qty),                                    
                                            })
                                        :   el
                                    )
                    }else{
                        // ถ้ายังไม่มี                
                        newData.push(model)  ;              
                    }
            });
        }else {
            res.data.raw.map(model => {
                if (newData.some(e => e.item === model.item)) {
                    // ถ้ามีอยู่แล้ว                    
                    newData = newData.map(el => el.item === model.item
                                    ?   Object.assign({}, el, {
                                            qty : parseFloat(el.qty) + parseFloat(model.qty),                                    
                                        })
                                    :   el
                                )
                }else{
                    // ถ้ายังไม่มี                
                    newData.push(model)  ;              
                }
            });
        }
  
       
        if(state.search != ''){
            newData =  newData.filter(model => 
                (model.name.toLowerCase().indexOf(state.search) > -1 || model.code.toLowerCase().indexOf(state.search) > -1) 
                    ? model 
                    : null
                );
        }

        if(state.vat === 'Vat'){
            newData = newData.filter(model => model.vat > 0 ? model : null);
        }else if(state.vat === 'No'){
            newData = newData.filter(model => model.vat === 0 ? model : null);
        }
        
        
        function compare( a, b ) {
            if ( a.name < b.name ){
                return -1;
            }
            return 0;
        }

        newData.sort( compare );
        let i = 0;
        newData.map(model => {
            i++;
            let sumQty      = 0;
            let sumTotal    = 0;
            let detail      = res.data.raw.filter(el => (parseInt(el.item) === parseInt(model.item)) ? el : null);

            if(state.vat === 'Vat'){
                detail = detail.filter(model => (model.vat * 1) > 0 ? model : null);
            }else if(state.vat === 'No'){
                detail = detail.filter(model => (model.vat * 1) <= 0 ? model : null);
            }

            let childTable = `<tr class="bg-warning">
                                    <td style="width:100px;">{$Yii::t("common","Date")}</td>
                                    <td style="width:150px;">{$Yii::t("common","Bill")}</td>
                                    <td class="text-right" style="min-width: 40px;">{$Yii::t("common","Quantity")}</td>
                                    <td>{$Yii::t("common","Unit")}</td>
                                    <td class="text-right">{$Yii::t("common","Cost")}</td>
                                    <td class="text-right">{$Yii::t("common","Unit Price")}</td>
                                    <td class="text-right">{$Yii::t("common","Total")}</td>
                                    <td class="text-right"></td>
                                </tr>`;
                

                detail.map(el => {
                    sumQty+= parseInt(el.qty);
                    sumTotal+= (el.qty * el.price);

                    childTable+= `<tr data-key="`+ model.item +`">
                                        <td>` + el.date + `</td>
                                        <td><a href="javascript:void(0)" class="invoice-detail" data-id="` + el.parent + `">` + el.no + `</a></td>
                                        <td class="text-right" data-tableexport-msonumberformat="\@">` + number_format(parseInt(el.qty)) + `</td>
                                        <td>` + el.unit + `</td>
                                        <td class="text-right" data-tableexport-msonumberformat="\@">` + number_format(parseFloat(el.cost).toFixed(2)) + `</td>
                                        <td class="text-right" data-tableexport-msonumberformat="\@">` + number_format(parseFloat(el.price).toFixed(2)) + `</td>
                                        <td class="text-right" data-tableexport-msonumberformat="\@">` + number_format((el.qty * el.price).toFixed(2)) + `</td>
                                        <td class="text-right" data-tableexport-msonumberformat="\@">` + (el.qty * el.price).toFixed(2) + `</td>
                                    </tr>`;

                })
                childTable+= `<tr class="bg-gray">
                                    <td class="text-right" colspan="2">{$Yii::t("common","Sum of bill items")}</td>
                                    <td class="text-right">` + sumQty + `</td>
                                    <td data-tableexport-msonumberformat="\@">` + model.unit + `</td>
                                    <td> </td>
                                    <td> </td>
                                    <td class="text-right" data-tableexport-msonumberformat="\@">` + number_format(sumTotal.toFixed(2)) + `</td>
                                    <td> </td>
                                </tr>`;


            html+= `<tr data-key="` + model.item + ` " class="bg-green">
                        <td class="text-center"><h5>` + i + `</h5></td>
                        <td colspan="3"><h5><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank" class="text-white">` + model.name + `</a></h5></td>
                        <td colspan="3" class="text-right"><h5><a href="?r=items%2Fitems%2Fview&id=`+ model.item +`" target="_blank" class="text-white">` + model.code + `</a></h5></td>
                        <td class=" "> </td>
                    </tr>`;

            html+= (detail.length > 0 ? childTable : ' ');
            
        })

        html+= '</tbody>';
        html+= '</table>';

        $('.customer-name').html(res.data.custName);
        $('.sale-name').html(res.data.saleName);
        $('.from-date').html(res.data.fdate);
        $('.to-date').html(res.data.tdate);
        $('.count-item').html('{$Yii::t("common","Product")} : '+ newData.length + ' {$Yii::t("common","items")}');


    if(res.data.raw.length > 0){
        $('.table-renders').html(html);

        
        
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
        $('body').find('#vat-change').val(state.vat ? state.vat : '0');        
        $('body').find('.vat-status').html('{$Yii::t("common","Vat")} : ' + $('select[id="vat-change"] option:selected').text());
        
    }else{
        $('.table-renders').html('<div class="row"><div class="col-xs-12 text-center"><h4>{$Yii::t("common","No data found")}</h4></div></div>');
    }
    
}



$(document).ready(function(){
    $('.table-renders').html(loadingDiv);
    getDataFromAPI({
        fdate:$('input[name="fdate"]').val(),
        tdate:$('input[name="tdate"]').val(),
        cust: null,
        sale: ''
    },res => {

        renderTable(res);
    })
});


$('body').on('submit',"#search-item", function(event) {
    
    $('.table-renders').html(loadingDiv);
 
    getDataFromAPI({
        fdate:$('input[name="fdate"]').val(),
        tdate:$('input[name="tdate"]').val(),
        cust: $('#customer').val(),
        sale: $('select[name="search-from-sale"]').val()
    },res => {

        renderTable(res);
    })
    event.preventDefault();
})

 
 
$('body').on("change",'#text-search, #vat-change', function() {
    var search  = $('#text-search').val().toLowerCase().trim();
    var vat     = $('#vat-change').val()
    state.search = search;
    state.vat = vat;
    setTimeout(() => {
        $('.table-renders').html(loadingDiv); 
        setTimeout(() => {
            renderTable(state.data);
        }, 100);
    }, 100);
    
    
});
 



$('body').on('click','.invoice-detail',function(){
    
    let id      = $(this).data('id');
    let dataLink= $(this).closest('tr').data('key');
    let html    = '';
    let i       = 0;
    let date    = '';
    let invNo   = '';
    let Title   = '';
    let hrefLink= '';
    let data    = state.data.data.raw.filter(model => model.parent === id ? model : null);
 
    let newData = [];
    data.map(model => {
        if (newData.some(e => e.item === model.item)) {
            // ถ้ามีอยู่แล้ว
            newData = newData.map(el => el.item === model.item
                            ?   Object.assign({}, el, { })
                            :   el
                        )
        }else{
            // ถ้ายังไม่มี
            newData.push(model);
        }        
    });

 
    newData.map(model => {
        i++;
        html+= '    <tr class="'+(model.item === parseInt(dataLink) ? 'bg-yellow' : ' ')+'">';
        html+= '        <td>' + i + '</td>';           
        html+= '        <td class="font-roboto" style="min-width:125px;">' + model.code + '</td>';
        html+= '        <td class="font-roboto">' + model.name + '</td>';
        html+= '        <td class="font-roboto text-right">' + number_format(parseInt(model.qty).toFixed(0)) + '</td>'; 
        html+= '    </tr>';

        invNo   = model.no;
        date    = model.date;
        Title   = model.no;
        hrefLink= '?r=accounting%2Fposted%2Fprint-inv&id='+ btoa(model.parent) + '&footer=1'; 
    });

    $('body').find('#modal-invoice-detail .no-inv-modal').html(invNo);
    $('body').find('#modal-invoice-detail .date-inv-modal').html(date);
    $('body').find('#modal-invoice-detail .btn-modal-footer').attr('href',hrefLink);
    $('body').find('#modal-invoice-detail .tbody-modal').html(html);
    $('body').find('#modal-invoice-detail .modal-title').html(Title);
    $('#modal-invoice-detail').modal('show');
})

// debug modal 
// แก้ไข modal ทับกันแล้วทำให้ scrolling ไม่ได้
$('#modal-invoice-detail').on('hidden.bs.modal',function(){
    $('body').addClass('modal-open').attr('style','overflow: auto; margin-right: 0px; padding-right: 0px;');
})

$('#modal-invoice-detail').on('show.bs.modal',function(){
    setTimeout(() => {
        $('body').attr('style','overflow: auto; margin-right: 0px; padding-right: 0px;');
    }, 500);    
})

JS;

$this->registerJS($JS,\yii\web\View::POS_END);
?>
<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
 