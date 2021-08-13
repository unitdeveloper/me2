<?= $this->render('_tax-invoice_header'); ?>
<div class="row">
    <div class="col-xs-12 search-box-render"></div>
</div>
<div class="tax_invoice">
    
    <table class="table table-bordered" id="export_table">
        <thead>
            <tr class="mt-5">
                <th colspan="9">
                    <div class="row">  
                        <div class="col-sm-12">
                            <div >
                                <span class="mr-5"><?= Yii::t('common','Month/Tax years') ?></span> <span class="text-month">เมษายน</span>  <span class="text-years mr-10">2019</span> 
                                (<?= Yii::t('common','From Date')?>  <span class="from-date"></span>  <?= Yii::t('common','To Date')?>  <span class="to-date"></span>)
                            </div>
                            <div class="pull-right"><?= Yii::t('common','Date')?> : <span class="text-date">12/05/2019</span></div>
                        </div>
                        <div class="col-sm-12 text-center"><h4><?= Yii::t('common','Sales tax report')?></h4></div>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-xs-3">
                                    <div class=" "><?= Yii::t('common','Entrepreneur name')?> : </div>
                                    <div class=" "><?= Yii::t('common','Entrepreneur address')?> : </div>
                                    <div class=" "><?= Yii::t('common','Taxid')?> : </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="text-customer-name">-</div>
                                    <div class="text-customer-addr">-</div>
                                    <div><span class="text-customer-head">-</span> <span class="text-headoffice">-</span></div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="pull-right"><?= Yii::t('common','Page')?> : <span class="text-page"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th class="bg-primary">#</th>
                <th class="bg-info"><?= Yii::t('common','dd/mm/yy')?></th>
                <th class="bg-info"><?= Yii::t('common','No.')?></th>
                <th class="bg-info substr-head"><?= Yii::t('common','Buyer/Provider')?></th>
                <th class="bg-info"><?= Yii::t('common','Tax ID')?></th>
                <th class="bg-info text-center">
                    <div><?= Yii::t('common','สถานประกอบการ')?></div>
                    <div><span  class="pull-left"><?= Yii::t('common','สนญ.')?> </span><span  class="pull-right"><?= Yii::t('common','สาขาที่')?></span></div>
                </th>
                <th class="bg-info">
                    <div><?= Yii::t('common','มูลค่าสินค้า')?></div>
                    <div><?= Yii::t('common','หรือบริการ')?></div>
                </th>
                <th class="bg-info">
                    <div><?= Yii::t('common','Amount')?></div>
                    <div><?= Yii::t('common','Vat')?></div>
                </th>
                <th class="bg-info"><?= Yii::t('common','Remark')?></th>
            </tr>
        </thead>
        <tbody  class="table-renders"></tbody>
    </table>
</div>
 
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
        <tr>
            <td colspan="9" class="text-center">
                <div class="text-center" style="margin-top:50px;">
                    <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
                    <div class="blink"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>
                    <h4 class="years-callulate"></h4>
                    <img src="images/icon/loader2.gif" height="122"/>             
                </div>
            </td>
        </tr>`;

const filterBox = `<div class="row search-box">                    
                    <div class="col-md-4 col-sm-6 col-xs-12 pull-right">
                        <div class="input-group">
                            <input id="text-search" class="form-control" type="text" placeholder="{$Yii::t('common','Filter')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div> 
                    <div class="col-md-2 col-sm-6 col-xs-12 pull-right text-right mt-8">
                        <span class="total-length"></span>
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
    let html = ' ';
 
       

        if(state.search != ''){
            newData =  newData.filter(model => 
            (model.no.toLowerCase().indexOf(state.search) > -1 || model.custName.toLowerCase().indexOf(state.search) > -1) 
            ? model : null);
        }

        if(state.vat === 'Vat'){
            newData = newData.filter(model => model.vat > 0 ? model : null);
        }else if(state.vat === 'No'){
            newData = newData.filter(model => model.vat === 0 ? model : null);
        }
        
        
        function compare( a, b ) {
            if ( a.no < b.no ){
                return -1;
            }
            return 0;
        }

        newData.sort( compare );
        let i = 0;

        newData.map(model => {
            i++;
            let amount = (model.sumline * 1);
            let sumline = model.subTotal * 1;

            let urlLink = 'index.php?r=accounting%2Fposted%2Fprint&id=' + btoa(model.id) + '=&footer=1';
            if(model.type==='Credit-Note'){
                urlLink = 'index.php?r=accounting%2Fcredit-note%2Fprint&id=' + btoa(model.id) + '&no=' + model.no;
            }
            html+= '<tr data-key="'+ model.id +'"  >';
            html+= '    <td class="bg-gray text-center">'+ i +'</td>';
            html+= '    <td>' + model.date + '</td>';
            html+= '    <td><a href="' + urlLink +'" target="_blank" class="'+(amount <= 0 ? 'text-red' : ' ')+'">' + model.no + '</a></td>';
            html+= '    <td ><div class="substr"><a href="index.php?r=customers%2Fcustomer%2Fview&id=' + model.custId + '" target="_blank" >' + model.custName + '</a></div></td>';
            html+= '    <td>' + model.tax + '</td>';
            html+= '    <td><div>'+ (model.headoffice ? 'X' : ' ') +'</div><div class="pull-right">'+ (model.headoffice ? ' ' : 'X') +'</div></td>';
            html+= '    <td class="text-right ' + (model.invat===1 ? '' : 'text-yellow') + '" title="' + amount + '">' + number_format(sumline.toFixed(2)) + '</td>';
            html+= '    <td class="text-right">' + number_format(model.incvat.toFixed(2)) + '</td>';
            html+= '    <td> </td>';
            html+= '</tr>';
        })

 
        $('.text-customer-name').html(res.data.compName);
        $('.text-customer-addr').html(res.data.compAddr);
        $('.text-customer-head').html(res.data.compTax);
        $('.text-headoffice').html(res.data.headOffice ? '{$Yii::t("common","Head Office")}' : '{$Yii::t("common","Branch")}' );        
        $('body').find('.search-box-render').html(filterBox);

    if(newData.length > 0){
        $('.table-renders').html(html);

        // $('body').find('caption').remove();        
        // $("#export_table").tableExport({
        //     headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
        //     footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
        //     formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
        //     fileName: "{$this->title}",         // (id, String), filename for the downloaded file
        //     bootstrap: true,                    // (Boolean), style buttons using bootstrap
        //     position: "top" ,                   // (top, bottom), position of the caption element relative to table
        //     ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
        //     ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
        //     ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file          
        // }); 

        // $('body').find('caption').append(filterBox);        
        // $('body').find('caption button').text('{$Yii::t("common","Export to Excel")}')
         
        $('body').find('#text-search').val(state.search).focus();
        //$('body').find('#vat-change').val(state.vat ? state.vat : '0');        
        $('body').find('.vat-status').html('{$Yii::t("common","Vat")} : ' + $('select[id="vat-change"] option:selected').text());
        $('body').find('.total-length').html(newData.length + ' {$Yii::t("common","items")}');
        
    }else{
        $('.table-renders').html('<tr><td colspan="9" class="text-center"><h4>{$Yii::t("common","No data found")}</h4></td></tr>');
    }
    
}



$(document).ready(function(){
    $('.table-renders').html(loadingDiv);
    getDataFromAPI({
        sale: '',
        cust: '',
        fdate: $('body').find('input[name="fdate"]').val(),
        tdate: $('body').find('input[name="tdate"]').val(),
        vat: 0
    },res => {

        renderTable(res);
    })
});


$('body').on('submit',"#search-item", function(event) {
    event.preventDefault();
    $('.table-renders').html(loadingDiv);
 
    getDataFromAPI({
        sale: $('body').find('select[name="search-from-sale"]').val(),
        cust: $('body').find('select[name="customer"]').val(),
        fdate: $('body').find('input[name="fdate"]').val(),
        tdate: $('body').find('input[name="tdate"]').val(),
        vat: $('body').find('select[name="vat-filter"]').val()
    },res => {

        renderTable(res);
    })

})

 
 
$('body').on("change",'#text-search, #vat-change', function() {
    var search  = $('#text-search').val().toLowerCase().trim();
    var vat     = $('#vat-change').val()
    state.search = search;
    state.vat = vat;
    renderTable(state.data);
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
<?php $this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  
<?php //https://printjs.crabbly.com/ ?>
<?php //$this->registerCssFile('//printjs-4de6.kxcdn.com/print.min.css');?>
<?php //$this->registerJsFile('//printjs-4de6.kxcdn.com/print.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
