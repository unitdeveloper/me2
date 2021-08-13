
<?php 
$Yii    = 'Yii';
$today  = date('Y-m-d');
$JS=<<<JS

let state = {
        data: [],
        search: '',
        vat: '',
        modal:[]
    };

const loadingDiv = `
        <div class="text-center" style="position: fixed; top:50%; left:50%; z-index:2000;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink" style="margin-top:10px;"> {$Yii::t("common","Calculating data please wait a minute")} .... </div>         
        </div>`;

const filterBox = `<div class="row search-box">                    
                    <div class="col-md-4 col-sm-6 col-xs-12 pull-right">
                        <div class="input-group hidden-xs">
                            <input id="search" class="form-control" type="text" placeholder="{$Yii::t('common','Filter')}..." />
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>        
                    </div>                 
                </div>`;

const getDataFromAPI = (obj,callback) => {
    fetch("?r=Planning/planning/safety-stock-ajax", {
        method: "POST",
        body: JSON.stringify(obj),
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
            swal('{$Yii::t("common","Warning")} : ' + res.message, res.status, "warning");
        }
        
    })
    .catch(error => {
        console.log(error);
    });
}


const renderTable = (res) => {
    let newData = res.raws;

    let TotalDis    = 0;
    let TotalSum    = 0;
    let TotalVat    = 0;
    let grandTotal  = 0;
    let TotalReceipt= 0;

    let html = `<table class="table table-bordered table-hover font-roboto" id="export_table">
                    <thead>
                        <tr class="mt-5 ">
                            <th colspan="7" class="text-right">
                                ` + filterBox + ` 
                            </th>
                        </tr>
                        <tr class="bg-gray">
                            <th class="bg-primary">#</th>
                            <th class="bg-dark" width="110">{$Yii::t('common','No.')}</th>
                            <th class="bg-dark">{$Yii::t('common','Name')}</th>
                            <th class="bg-dark text-right bg-yellow"><span style="margin-right:10px;">{$Yii::t('common','Stock')}</span></th>
                            <th class="bg-dark text-right"><span style="margin-right:10px;">{$Yii::t('common','Safety Stock')}</span></th>
                            <th class="bg-dark text-right"><span style="margin-right:10px;">{$Yii::t('common','Reorder Point')}</span></th>
                            <th class="bg-dark text-right"><span style="margin-right:10px;">{$Yii::t('common','Minimum Stock')}</span></th>                      
                        </tr>
                    </thead>`;
 
       

        if(state.search != ''){
            newData =  newData.filter(model => 
            (model.no.toLowerCase().indexOf(state.search) > -1 || model.name.toLowerCase().indexOf(state.search) > -1) 
            ? model : null);
        }

        
        
        
        function compare( a, b ) {
            if ( a.code < b.code ){
                return -1;
            }
            return 0;
        }

        newData.sort( compare );
        let i = 0;
        html+= '<tbody  class="table-renders">';
        newData.map((model, key) => {
            i++;
             
            html+= `<tr data-key="`+ model.id +`" data-img="`+ model.img +`">
                        <td class="bg-gray">` + (key + 1) + `</td>
                        <td class="item-info pointer item-code" style="min-width: 130px;">` + model.code + `</td>
                        <td class="item-info pointer item-name" style="min-width: 220px;">` + model.name + `</td>
                        <td class="text-right bg-yellow action-stock" data-val="` + model.stock + `">` + number_format(model.stock) + `</td>
                        <td class="text-right action-safety_stock" data-val="` + model.safety_stock + `" style="background-color: #f2ffc0;">
                            <input type="text" name="line_safety" value="` + model.safety_stock + `"  class="line-change form-control text-right pull-right no-border" autocomplete="off" style="background-color: #f2ffc0; width:100px;" /></td>
                        <td class="text-right action-reorder_point" data-val="` + model.reorder_point + `" style="background-color: #c0f0ff;">
                            <input type="text" name="line_reorder" value="` + model.reorder_point + `" class="line-change form-control  text-right pull-right no-border"  autocomplete="off" style="background-color: #c0f0ff; width:100px;"/></td>
                        <td class="text-right action-minimum_stock" data-val="` + model.minimum_stock + `" style="background-color: #edc0ff;">
                            <input type="text" name="line_minimum" value="` + model.minimum_stock + `" class="line-change form-control  text-right pull-right no-border"  autocomplete="off" style="background-color: #edc0ff; width:100px;" /></td>
                    </tr>`;
        })
        html+= '</tbody>';
        
        html+= `</table>`;

    

    if(newData.length > 0){
        $('.render-table').html(html);
        
        $("#export_table").tableExport({
            headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
            footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
            formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
            fileName: "{$this->title}",         // (id, String), filename for the downloaded file
            bootstrap: true,                    // (Boolean), style buttons using bootstrap
            position: "bottom" ,                   // (top, bottom), position of the caption element relative to table
            ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file
            ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
            ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file          
        }); 
        
        
    }else{
        $('.render-table').html('<div class="row"><div class="col-xs-12 text-center"><h4>{$Yii::t("common","No data found")}</h4></div></div>');
    }
    
    var table = $('#export_table').DataTable({
                        "paging": true,
                        'pageLength' : 1000,
                        "searching": false
                    });
    
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



$(document).ready(function(){
    $('.render-table').html(loadingDiv);
    getDataFromAPI({page:'all'},res => {
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
    let id      = $(this).data('id');
    let val     = $(this).data('val');
    let so      = $(this).attr('data-so');
    let soid    = $(this).attr('data-soid');
    let date    = $(this).attr('data-date');
    let status  = $(this).attr('data-status');

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
                                <td><a href="?r=items%2Fitems%2Fview&id=`+ el.item +`" target="_blank" class="text-white" >`+ el.code +`</a></td>
                                <td>`+ el.name + `</td>  
                                <td class="text-right" >`+ parseInt(el.qty) + `</td>
                                <td class="text-right" style="min-width: 40px;"><a href="?WarehouseSearch[ItemId]=`+ btoa(el.item) +`&WarehouseSearch[SourceDoc]=`+soid+`&r=warehousemoving%2Fwarehouse" target="_blank" class="text-white" >`+ parseInt(el.qty * qty) +`</a></td>
                                <td> </td>
                            </tr>`;
        });
        childTable+= ' ';
        return childTable;
    }

    fetch("?r=accounting/ajax/invoice-by-inven", {
        method: "POST",
        body: JSON.stringify({id:id, status: status}),
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
            if(res.data.status=='Open'){
                hrefLink= '?r=accounting%2Fsaleinvoice%2Fprint-inv-page&id='+ id + '&footer=1';           
            }
            
        
            let html = `<table class="table table-bordered">`;
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
                            <td class="item-name">` + model.name + `</td>    
                            <td colspan="2" class="text-right item-qty"><a href="?WarehouseSearch[ItemId]=`+ btoa(model.item) +`&WarehouseSearch[SourceDocNo]=`+so+`&r=warehousemoving%2Fwarehouse" target="_blank" class="text-white" >` + number_format(parseFloat(model.qty)) + `</a></td>                    
                            <td class="text-right">` + number_format(parseFloat(model.price).toFixed(2)) + `</td>                        
                        </tr>`;
                html+= model.detail.length >= 1 ? child(model.detail,model.qty) : '';
            })
                html+= `</tbody>`;
                html+= '</table>';
                let body = `
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 >` + res.data.custName + `</h4>
                                    <h5 >{$Yii::t("common","Date")} : <span class="date-inv-modal">`+ date +`</span> <span class="pull-right">` + so + `</span></h5>     
                                </div>
                                                      
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


$('body').on('click', '.action-safety_stock', function(){
    // let name    = prompt("safety_stock", '');
    // console.log(name);
})


$('body').on('click', '.item-info', function(){
    $('body').find('#modal-customize').modal('show'); 
    let row     = $(this).closest('tr');
    let id      = row.attr('data-key');
    let name    = row.find('.item-name').text();
    let code    = row.find('.item-code').text();
    let stock   = row.find('.action-stock').text();
    let safety  = row.find('.action-safety_stock').attr('data-val');
    let reorder = row.find('.action-reorder_point').attr('data-val');
    let minimum = row.find('.action-minimum_stock').attr('data-val');
    let img     = row.attr('data-img');


    setTimeout(() => {
        $('body').find('#modal-customize').find('input[name="name"]').val(name);
        $('body').find('#modal-customize').find('input[name="stock"]').val(stock);
        $('body').find('#modal-customize').find('input[name="safety"]').val(safety);
        $('body').find('#modal-customize').find('input[name="reorder"]').val(reorder);
        $('body').find('#modal-customize').find('input[name="minimum"]').val(minimum);
        $('body').find('#modal-customize').find('.modal-title').html(`<a href="?r=items%2Fitems%2Fview&id=` + id + `" target="_blank">` + code + `</a>`);
        $('body').find('#modal-customize').find('.modal-body .img').html('<img src="'+img+'" height="150px;" />');
        $('body').find('#modal-customize').attr('data-key',id);

        
    }, 100);

    setTimeout(() => {
        $('body').find('#modal-customize').find('input[name="safety"]').select().focus();
    }, 500);
    //console.log($(this).closest('tr').attr('data-key'));
});

const updateItem = (obj, callback) => {
    fetch("?r=Planning/planning/update-item-field-ajax", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {   
       callback(res)
        
    })
    .catch(error => {
        console.log(error);
    });

}

$('body').on('click', '#modal-customize .save-change', function(){
    let id      = $('body').find('#modal-customize').attr('data-key');
    let safety  = $('body').find('#modal-customize').find('input[name="safety"]').val();
    let reorder = $('body').find('#modal-customize').find('input[name="reorder"]').val();
    let minimum = $('body').find('#modal-customize').find('input[name="minimum"]').val();

    updateItem({id:id, safety:safety, reorder:reorder, minimum:minimum}, res => {
        if(res.status===200){
            $.notify({
                // options
                icon: 'fas fa-clock',
                message: res.message
            },{
                // settings
                type: 'success',
                delay: 10000,
                z_index:3000,
            });    
            setTimeout(() => {
                $('body').find('#modal-customize').modal('hide'); 

                // set value
                // $('body').find('tr[data-key="'+id+'"]').find('.action-safety_stock').attr('data-val',safety).html((safety * 1));
                // $('body').find('tr[data-key="'+id+'"]').find('.action-reorder_point').attr('data-val',reorder).html((reorder * 1));
                // $('body').find('tr[data-key="'+id+'"]').find('.action-minimum_stock').attr('data-val',minimum).html((minimum * 1));
                function inputBox(val, name, color) {
                    return `<input type="text" name="` + name + `" value="` + val + `"  class="line-change form-control text-right pull-right no-border" autocomplete="off" style="background-color: #bbffbb; width:100px;" />`;
                }
                
                $('body').find('tr[data-key="'+id+'"]').find('.action-safety_stock').attr('data-val',safety).html(inputBox((safety * 1), 'line_safety', '#f2ffc0'));
                $('body').find('tr[data-key="'+id+'"]').find('.action-reorder_point').attr('data-val',reorder).html(inputBox((reorder * 1), 'line_reorder', '#c0f0ff'));
                $('body').find('tr[data-key="'+id+'"]').find('.action-minimum_stock').attr('data-val',minimum).html(inputBox((minimum * 1), 'line_minimum', '#edc0ff'));
                
            }, 200);
            
        }else{
            $.notify({
                // options
                icon: 'fas fa-clock',
                message: res.message
            },{
                // settings
                type: 'warning',
                delay: 10000,
                z_index:3000,
            });    
        }
    })
})

// Next Field

$("body").on("keypress", 'input[name="safety"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $('body').find('#modal-customize').find('input[name="reorder"]').select().focus();
  }
});

$("body").on("keypress", 'input[name="reorder"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $('body').find('#modal-customize').find('input[name="minimum"]').select().focus();
  }
});

$("body").on("keypress", 'input[name="minimum"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $('body').find('#modal-customize').find('button.save-change').focus();
  }
});



$("body").on("keypress", 'input[name="line_safety"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $(this).closest('tr').find('input[name="line_reorder"]').select().focus();
  }
});


$("body").on("keypress", 'input[name="line_reorder"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $(this).closest('tr').find('input[name="line_minimum"]').select().focus();
  }
});

$("body").on("keypress", 'input[name="line_minimum"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    $(this).closest('tr').next("tr").find('input[name="line_safety"]').select().focus();
  }
});

$('body').on('click', '.line-change', function(){
    $(this).select();
});

$('body').on('change', '.line-change', function(){
    let row     = $(this).closest('tr');
    let id      = row.attr('data-key');
    let safety  = row.find('input[name="line_safety"]').val();
    let reorder = row.find('input[name="line_reorder"]').val();
    let minimum = row.find('input[name="line_minimum"]').val();
    let me      = $(this);

    updateItem({id:id, safety:safety, reorder:reorder, minimum:minimum}, res => {
        if(res.status===200){
            // $.notify({
            //     // options
            //     icon: 'fas fa-clock',
            //     message: res.message
            // },{
            //     // settings
            //     type: 'success',
            //     delay: 10000,
            //     z_index:3000,
            // });    
            me.attr('style','background-color: #bbffbb; width:100px;');
            me.closest('td').attr('data-val',me.val());
            
        }else{
            let message = res.message;
            
            $.notify({
                // options
                icon: 'fas fa-exclamation',
                message: Object.values(message)[0]
            },{
                // settings
                type: 'danger',
                delay: 10000,
                z_index:3000,
            });    

            me.select().focus();
        }
    }) 
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
