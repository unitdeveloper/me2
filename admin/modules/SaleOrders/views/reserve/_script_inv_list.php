<?php 

use kartik\widgets\DatePicker;

$INV = 'CT'.date('y',strtotime(date('Y')+543)).date('m').'000';
$today = date('Y-m-d');

$userName = Yii::$app->user->identity->username;
?>
<div class="row">
    <div class="col-sm-12">
        <a href="#" class="show-series-list btn btn-warning pull-right"><i class="fas fa-list text-info"></i> <?=Yii::t('common','INVOICE LIST')?></a>
    </div>
</div>

<div class="modal fade modal-full " id="modal-show-inv-list" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog  " >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fas fa-list"></i> <?=Yii::t('common','Invoice List')?></h4>
            </div>
           
            <div class="modal-body">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-4 pull-right">                       
                        <form name="search">
                            <div class="input-group"  >
                                <input type="text" name="search-inv" class="form-control" autocomplate="off" placeholder="<?=Yii::t('common','Search')?>" />                 
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-search-inv"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-2 pull-right">
                        <select class="form-control  mb-10" id="vat-change" >
                            <option value="0"><?=Yii::t('common','All')?></option>
                            <option value="Vat" selected>Vat</option>
                            <option value="No">No Vat</option>
                        </select> 
                    </div>
                    <div class="col-sm-6 pull-right">
                    <?php

$FromDate   = Yii::t('common','From Date');
$ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
	<span class="input-group-addon">$FromDate</span>
	{input1}
 
	<span class="input-group-addon">$ToDate</span>
	{input2}
	<span class="input-group-addon kv-date-remove">
	    <i class="glyphicon glyphicon-remove"></i>
	</span>
HTML;

              echo DatePicker::widget([
              		'type'      => DatePicker::TYPE_RANGE,
					'name'      => 'fdate',
					'value'     => Yii::$app->request->get('fdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('fdate'))) : date('Y-m-d',strtotime('this week', time())),					
					'name2'     => 'tdate',
					'value2'    => Yii::$app->request->get('tdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('tdate'))) : date('Y-m-d'),                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'layout'    => $layout,
                    'options'   => ['autocomplete'=>'off'],
                    'options2'  => ['autocomplete'=>'off'],
					'pluginOptions' => [
						'autoclose' => true,
                        'format'    => 'yyyy-mm-dd'
                    ],
                    // 'pluginEvents'  => [
                    //     'changeDate'=> 'function(e) {
                    //         let val = $(e.target).val();
                    //         let name = $(e.target).attr("name");
                    //         console.log(name);
                    //     }'
                    // ]
              ]);
              ?>
                    </div>
                </div>
                <div class="row" id="docx">
                    <div class="col-sm-12" id="renderInvoice" style="padding-bottom:10px;"></div>
                </div>
            </div>
             
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <i class="fas fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                   
                <div class="pull-right">
                    <div class="btn-group dropup ">
                        <button id=" " type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download"></i> <?=Yii::t('common','Download')?>
                        </button>
                        <ul class="dropdown-menu  pull-right" role="menu">
                            <li><a href="#" class="export-to-excel  text-green"><i class="fas fa-file-excel"></i> <?=Yii::t('common','Microsoft Excel')?></a></li>
                            <li><a href="#" class="export-to-word  text-aqua" id="export_word" ><i class="fas fa-file-word"></i> <?=Yii::t('common','Microsoft Word')?></a>  </li>
                        </ul>
                    </div>
                </div>
                    
                

            </div>
        </div>
    </div>
</div>


<?php
$Yii    = 'Yii';
$current= date('Y') + 543;
$years  = substr($current,2);
$month  = date('m');

$jsInv=<<<JS


let state = {
  data:[]
};

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> Loading... </div>
            <img src="images/icon/loader2.gif" height="122"/>            
        </div>`;
 
//  NEW

let getInvoiceFromApi = (obj, callback) => {
    fetch("?r=accounting/ajax/invoice-by-no", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);
    })
    .catch(error => {
        console.log(error);
    });
};


const renderTableInvList = (datas, calback) => {
    
    let data = localStorage.getItem('iv') ? JSON.parse(localStorage.getItem('iv')) : []; 
    
    let vat = $('body').find('#vat-change').val();
    if(vat === 'Vat'){
        data = data.filter(model => model.vat > 0 ? model : null);
    }else if(vat === 'No'){
        data = data.filter(model => model.vat === 0 ? model : null);
    }
    
    
    let rows  = ''
    data.map((model, keys) => {

        let balance     = model.balance.total;
        let countDoc    = model.no.trim().length;
        let digit       = model.vat === 7 ? 9 : 10;

        let url     = model.status != 'Posted' 
                        ? `?r=accounting/saleinvoice/print-inv-page&id=` + model.id + `&footer=1`
                        : `?r=accounting/posted/print-inv&id=` + btoa(model.id) + `&footer=1`;

        let urlEdit = model.status != 'Posted' 
                        ? `?r=accounting%2Fsaleinvoice%2Fupdate&id=` + model.id
                        : `?r=accounting%2Fposted%2Fposted-invoice&id=` + btoa(model.id);

        rows+= `<tr 
                    data-key="` + model.id +`" 
                    data-status="` + model.status + `" 
                    data-locked="` + model.locked + `"
                    data-so="` + model.orderId + `"
                    class="" 
                    style="` + (model.bg ? `background:` + model.bg + `;` : ' ') + ` `+(model.revenue==1 ? 'text-decoration: line-through; text-decoration-color: #ff6565;' : '')+`">    
                    
                    <td class="text-center">` + (keys + 1) +`</td>     
                    <td>
                        <button class="btn btn-sm btn-default-ew transport-list-update">
                            `+(model.transport > 0 
                                ? '<i class="fas fa-truck text-info" style="min-width:15px;"></i>' 
                                : '<i class="fas fa-truck text-gray" style="min-width:15px;"></i>')+`
                        </button>
                    </td>                     
                    <td class="doc-no ` + (model.status==='Open' 
                                ? 'new-line pointer text-success' 
                                : (model.new == true  
                                    ? `new-line pointer`
                                    : ` `) 
                                ) + ` ` + ( countDoc != digit  ? 'text-red blink': ' ')  + `" 
                        >` + model.no + `</td>     
                    <td>
                        <div class="btn-group" role="group" aria-label="Basic example"> 
                            <a href="` + urlEdit +`" class="btn btn-xs btn-warning-ew btn-flat" title="View" target="_blank"><i class="fas fa-pen text-yellow"></i></a>
                            <a href="` + url +`" class="btn btn-xs btn-danger-ew btn-flat" target="_blank" title="PDF" ><i class="fas fa-file-pdf text-red"></i></a>
                            <a href="#" data-as="?r=accounting/print/export&id=` + model.id +`&status=` + model.status +`&vat=` + model.vat +`" class="btn btn-xs btn-success-ew btn-flat download-excel" target="_blank" title="Excel" ><i class="fas fa-file-excel text-green"></i></a>
                        </div>
                    </td>            
                    <td>` + model.date + `</td>
                    <td>` + model.custCode + `</td>
                    <td>` + model.custName + `</td>
                    <td class="` +( model.status != 'Posted' ? 'ref' : 'ref-not-change')+ `" data-val="` + model.ref + `">` + (model.new == true  ? `<input type="text" class="form-control" name="update-ref" value="" />` : model.ref) + ` </td>
                    <td>` + model.orderNo + `</td>
                    <td class="text-right">` + number_format(balance.toFixed(2)) + `</td>
                    <td class="text-center">`+(model.locked == 1 
                                    ? '<i class="fas fa-lock text-red ' + (model.status === 'Posted' ? 'pointer click-for-unlock' : ' ') + '"></i>'
                                    : '<i class="fas fa-unlock-alt text-green ' + (model.status === 'Posted' ? 'pointer click-for-locked' : ' ') + ' "></i>') +`</td> 
                    <td class="text-right action-btn">
                        ` + (
                            model.new == true 
                                ? `<button type="button" class="btn btn-primary btn-xs btn-flat confirm-new-line"><i class="fas fa-check"></i> ยืนยัน</button>`
                                : `<button type="button" class="btn ` + (model.status==='Posted' ? 'btn-danger' : 'btn-warning') + `  btn-xs btn-flat delete-line-line"><i class="fas fa-trash"></i> ลบ</button>`
                        ) + `                        
                    </td>
                </tr>`;
    });
 
    let html = `<table class="table table-bordered table-hover font-roboto" id="export_table">
                
                <thead>
                  <tr class="bg-primary">                    
                    <th class="text-center" style="width:10px;">#</th>  
                    <th style="width:20px;">T</th>                               
                    <th style="width:95px;">เลขที่</th>  
                    <th style="width:80px;"></th> 
                    <th style="width:80px;">วันที่</th>                    
                    <th style="width:96px;">รหัสลูกค้า</th>
                    <th>ลูกค้า</th>
                    <th style="width:150px;">อ้างอิง</th>
                    <th style="width:100px;">ใบสั่งขาย</th>
                    <th class="text-right" style="width:80px; padding-right: 16px;">ยอดเงิน</th>
                    <th style="width:50px;">Lock</th> 
                    <th class="text-right" style="width:50px; padding-right: 16px;"> </th>
                  </tr>
                </thead>
                <thead>
                    <tr class="bg-gray">
                        <th></th>
                        <th><a class="btn btn-primary-ew btn-truck" href="?r=SaleOrders%2Freport%2Fsale-order-modern-trade" target="_blank"><i class="fas fa-truck"></i> {$Yii::t('common','Transport')}</a></th>
                        <th colspan="8"></th>
                        <td class="text-right">
                            <small  class="btn btn-warning btn-sm btn-flat click-for-lock-all">
                                <i class="fas fa-lock text-red pointer"></i>
                                {$Yii::t('common','Lock All')}
                            </small>
                        </td>
                        <td class="text-right add-zone"><button type="button" class="btn btn-info  btn-sm btn-flat">+ {$Yii::t('common','Create Now')}</button></td>
                    </tr>
                </thead>
                <tbody>
                  ` + rows + `
                </tbody>
                
              </table>`;


    setTimeout(() => {
        $('body').find('td.add-zone button').addClass('add-new-invoice-row');
    }, 500);

    calback({ 
      html: html ,
      count: data.length
    });
}

const loadData = () => {

  $('body').find('#renderInvoice').html(loadingDiv);
  getInvoiceFromApi({
    fdate: $('body').find('input[name="fdate"]').val(),
    tdate: $('body').find('input[name="tdate"]').val(),
    vat: $('body').find('#vat-change').val()
  }, res => { 
    state.data = res.data.raw;
    localStorage.setItem('iv', JSON.stringify(res.data.raw));
    renderTableInvList(res.data.raw, res => {
      $('body').find('#renderInvoice').html(res.html);
      var table = $('#export_table').DataTable({
            "paging": false,
            "searching": false,
            "info" : false,
            "order": [[ 2, "desc" ]]
        });

    //   table
    //   .column( 0 )
    //   .data()
    //   .sort();

        setTimeout(() => {
            $('body').find('input[name="search-inv"]').trigger('keyup');
        }, 1000);

    })    
  });
}


const filterTable  = (search) => {
  $("#export_table  tbody tr").filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
  });

  $('#export_table tbody tr').each((key,value) => {
      $(value).find('.key').html(key + 1);
  });
}

function filterGlobal (search) {
    $('#export_table').DataTable().search(
        search,
        $('body').find('input[name="search-inv"]').val().toLowerCase(),
        $('body').find('#vat-change').val()
    ).draw();
}


$('body').on('click','.show-series-list', function(){
  $("#modal-show-inv-list").modal("show"); 
  $('body').find('#renderInvoice').html(loadingDiv); 
  setTimeout(() => {
    $('body').find('#inv-search-box').select().focus();
    $('body').find('.loading-div').hide();
    loadData()
  }, 800);  
  
});



$('body').on('keyup', '#modal-show-inv-list input[name="search-inv"]', function(e){
  let words = $('#modal-show-inv-list input[name="search-inv"]').val().toLowerCase();
  filterTable(words);
});

$('body').on('change', 'input[name="fdate"]', function(){
    $('input[name="tdate"]').val($(this).val());
    loadData();
    
})

$('body').on('change', 'input[name="tdate"]', function(){
    loadData();
})


$('body').on('change', ' #vat-change', function(){
  renderTableInvList(state.data, res => {
    $('body').find('#renderInvoice').html(res.html);
    var table = $('#export_table').DataTable({
          "paging": false,
          "searching": false,
          "info" : false,
          "order": [[ 2, "desc" ]]
      });

    table
    .column( 0 )
    .data()
    .sort();

  });
})



$('body').on('keyup', '#saleheader-ext_document', function(e){
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
     $('#saleheader-invoice_no').select().focus();
  }
})
$('body').on('keyup', '#saleheader-invoice_no', function(e){
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
     $('#saleheader-order_date').select().focus();
  }
})

$('body').on('click', '.insert-new-line', function(){

    let newRow = {
        id: 0,
        custId: 909,
        custCode: "999",
        custName: "จอง : ${userName}",
        no: "Disabled",
        ref: " ",
        orderId: 0,
        orderNo: " ",
        due: " ",
        date: "${today}",
        status: "Open",
        vat: 7,
        balance: {
            total:0
        },
        bg: '#d2ffd2',
        new: true,
        locked: 0
    }

    state.data.push(newRow);

    let data    = localStorage.getItem('iv') ? JSON.parse(localStorage.getItem('iv')) : []; 
    let update  = data.push(newRow);

    localStorage.setItem('iv', JSON.stringify(update));

 
    renderTableInvList(state.data, res => {

      $('body').find('#renderInvoice').html(res.html);
      var table = $('#export_table').DataTable({
            "paging": false,
            "searching": false,
            "info" : false,
            "order": [[ 2, "desc" ]]
        });

        
    })   
})

const newDoc = (str) => {

    if(str){
        var TEXT = str.substring(0, 6);
        let last = str.substring(6, 9);
        let Next = parseInt(last) + 1;

        // console.log(str);
        // console.log(TEXT);
        // console.log(last);
        // console.log(Next);

        return TEXT+('000' + Next).slice(-3);
    }else{
        return 'CT'+ "{$years}{$month}" + "001";
    }
}

$('body').on('dbclick', '.add-new-invoice-row', function(){
    alert('คลิกครั้งเดียวก็ได้');
    return false;
})

$('body').on('click', '.add-new-invoice-row', function(){
    
    
    
    let lastDoc = $(this).closest('table').find('tbody > tr:first').find('td.doc-no').text();

    let No      = newDoc(lastDoc.trim());

    let newRow = {
        id: 0,
        custId: 909,
        custCode: " ",
        custName: "จอง : ${userName}",
        no: No,
        ref: " ",
        orderId: 0,
        orderNo: " ",
        due: " ",
        date: "${today}",
        status: "Open",
        vat: 7,
        balance: {
            total:0
        },
        bg: '#d2ffd2',
        new: true,
        locked: 0
    }
    //console.log(state.data);
   
 
    let datas       = localStorage.getItem('iv') 
                        ? JSON.parse(localStorage.getItem('iv')) 
                        : []; 

        datas.push(newRow);
        localStorage.setItem('iv', JSON.stringify(datas));
     
        state.data.push(newRow);
 // 
 
    renderTableInvList(state.data, res => {
      $('body').find('#renderInvoice').html(res.html);
      var table = $('#export_table').DataTable({
            "paging": false,
            "searching": false,
            "info" : false,
            "order": [[ 2, "desc" ]]
        });

        
        
    });


});

const modifyDocumentNo = (obj, callback) => {
    fetch("?r=accounting/ajax/invoice-update-no", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        if(response.status===200){
            callback(response);
        }else{
            // Error
            $.notify({
                // options
                icon: "fas fa-exclamation-circle",
                message: response.message
            },{
                // settings
                placement: {
                from: "top",
                align: "center"
                },
                type: "error",
                delay: 4000,
                z_index: 3000
            });
        }
        
    })
    .catch(error => {
        console.log(error);
    });
}

$('body').on('click', '.new-line', function(){
    let el      = $(this);
    let current = $(this).text();
    let inv     = prompt("{$Yii::t('common','Please enter your invoice number.')}", current);


    if(inv.length != 9){
        $(this).closest('td').addClass('text-red');
        setTimeout(() => {
            alert('โปรดตรวจสอบจำนวนตัวอักษร');
        }, 1000);        
    }else{
        $(this).closest('td').removeClass('text-red');
        // Update Document no
        // ถ้าสถาณะ Open แก้ไขได้
        let id = el.closest('tr').attr('data-key');
        if(current!=inv){

            if(id > 0){
                modifyDocumentNo({id:id,no:inv}, res => {

                });
            }
        }
       
    }

    $(this).text(inv);

    let index   = $(this).closest("tr").index();
    let update  = state.data.map((model, key) => {
                    return key === index ? Object.assign({}, model, { no: inv.trim() }) : model;
                });

    state.data  = update;
 
 
});


let createEmptyInvoice = (obj, callback) => {
    fetch("?r=accounting/ajax/create-empty-inv", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);
    })
    .catch(error => {
        console.log(error);
    });
};


$('body').on('click', '.confirm-new-line', function(){
    let el  = $(this);
    let no  = $(this).closest('tr').find('.new-line').text().trim();
    let ref = $(this).closest('tr').find('input[name="update-ref"]').val().trim();
    if (confirm('ยืนยัน "' + no + '" ?')) {   
        createEmptyInvoice({no:no, ref:ref},res => {
            if(res.status===200){
                el.closest('tr').attr('data-key', res.id).attr('data-status', 'Open');                 
                el.closest('tr').attr('style', ' ');            // Remove row green
                el.closest('tr').find('input[name="update-ref"]').val(ref.trim());  
                el.closest('tr').find('td.action-btn').html(`<button type="button" class="btn btn-success  btn-xs btn-flat delete-line-line"><i class="fas fa-trash"></i> ลบ</button>`);  
                //el.closest('tr').find('.ref').html(ref.trim()); // Remove Input


                let data = localStorage.getItem('iv') ? JSON.parse(localStorage.getItem('iv')) : []; 
                
                // Update State
                let idx     = el.closest("tr").index();
                let update  = data.map((model, key) => {
                    return no === model.no ? Object.assign({}, model, { id: res.id,new: false }) : model;
                });

                state.data  = update;
                localStorage.setItem('iv', JSON.stringify(update));
                //console.log(update);

            }else{

                // Error
                $.notify({
                    // options
                    icon: "fas fa-exclamation-circle",
                    message: res.message
                },{
                    // settings
                    placement: {
                    from: "top",
                    align: "center"
                    },
                    type: "error",
                    delay: 4000,
                    z_index: 3000
                });

                if(res.status===403){
                    $('#modal-show-inv-list input[name="search-inv"]').val(no.trim());
                    filterTable(no.trim().toLowerCase());
                }

            }
        });
    }
});


$('body').on('change', 'input[name="ref[]"]', function(){
    let el      = $(this).closest("tr").index();
    let ref     = $(this).val();
    let update  = state.data.map((model, key) => {
                    return key === el ? Object.assign({}, model, { ref: ref }) : model;
                });

        state.data = update;
        //console.log(state.data);
});




let deleteInvoice = (obj, callback) => {
    fetch("?r=accounting/ajax/delete", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        callback(response);
    })
    .catch(error => {
        console.log(error);
    });
};

$('body').on('click', '.delete-line-line', function(){
    var el          = $(this);
    var id          = parseInt($(this).closest('tr').attr('data-key'));
    var ivStatus    = $(this).closest('tr').attr('data-status');
    var locked      = parseInt($(this).closest('tr').attr('data-locked'));

    if(locked===1){
        alert("เอกสารถูกล็อค!  โปรดติดต่อฝ่ายบัญชีเพื่อทำการปลดล๊อค");
    }else{

        let reason = prompt("เหตุผลการลบ", '');

        if (reason) {   
            if(reason.length < 5){
                alert('เหตุผลไม่เพียงพอ');
                return false;
            }else{

            
                deleteInvoice({id:id, status:ivStatus, remark:reason}, res => {
                    if(res.status===200){

                        let newData = state.data.filter(model => ((model.id !== id || model.id == 0) ? model : null));
                        state.data = newData;
                        localStorage.setItem('iv', JSON.stringify(newData));
                        
                        el.closest('tr').attr('style','background: #add4f5;');
                        setTimeout(() => {                   
                            el.closest('tr').remove();
                            renderTableInvList(newData, res => {
                                $('body').find('#renderInvoice').html(res.html);
                                var table = $('#export_table').DataTable({
                                    "paging": false,
                                    "searching": false,
                                    "info" : false,
                                    "order": [[ 2, "desc" ]]
                                });

                                table
                                .column( 0 )
                                .data()
                                .sort();

                            });
                        }, 300);
                        
                    }else{

                        // Error
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.suggestion
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "error",
                            delay: 4000,
                            z_index: 3000
                        });

                    }
                    //console.log(res);
                })
            }
        };
    }
});


$('body').on('click', '.delete-line-line-moderntrade', function(){
    var el          = $(this);
    var id          = parseInt($(this).closest('div.invoice-div').attr('data-id'));
    var ivStatus    = $(this).closest('div.invoice-div').attr('data-status');
    var locked      = parseInt($(this).closest('div.invoice-div').attr('data-locked'));

    if(locked===1){
        alert("เอกสารถูกล็อค!  โปรดติดต่อฝ่ายบัญชีเพื่อทำการปลดล๊อค");
    }else{

        let reason = prompt("เหตุผลการลบ", '');

        if (reason) {   
            if(reason.length < 5){
                alert('เหตุผลไม่เพียงพอ');
                return false;
            }else{

            
                deleteInvoice({id:id, status:ivStatus, remark:reason}, res => {
                    if(res.status===200){
                         
                        setTimeout(() => {                   
                            el.closest('div.invoice-div').html(`<a class="btn btn-danger-ew btn-sm create-invoice-btn" href="#"><i class="fas fa-file-invoice"></i> ใบกำกับภาษี</a>`);                             
                        }, 300);
                        
                    }else{

                        // Error
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.suggestion
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "error",
                            delay: 4000,
                            z_index: 3000
                        });

                    }
                    //console.log(res);
                })
            }
        };
    }
});

const updateLocke = (obj, callback) => {

    fetch("?r=accounting/ajax/invoice-locked", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        if(response.status===200){
            callback(response);
        }else{
            // Error
            $.notify({
                // options
                icon: "fas fa-exclamation-circle",
                message: response.message
            },{
                // settings
                placement: {
                from: "top",
                align: "center"
                },
                type: "error",
                delay: 4000,
                z_index: 3000
            });
        }
        
    })
    .catch(error => {
        console.log(error);
    });
}

$('body').on('click', '.click-for-unlock', function(){
    let el      = $(this);
    let id      = $(this).closest('tr').attr('data-key');
    let status  = $(this).closest('tr').attr('data-status');

    if(status==='Posted'){
        if (confirm('ต้องการ "ปลดล็อค" รายการนี้หรือไม่ ?')) {   
            updateLocke({id:id,status:0}, res => {
                if(res.locked===0){ // Unlocked
                    el.closest('tr').attr('data-locked', 0);
                    el.closest('td').html('<i class="fas fa-unlock-alt text-green  pointer click-for-locked"></i>');
                }
            });
        }
    }
});

$('body').on('click', '.click-for-locked', function(){ 
    let el      = $(this);
    let id      = $(this).closest('tr').attr('data-key');
    let status  = $(this).closest('tr').attr('data-status');
    if(status==='Posted'){
        if (confirm('ต้องการ "ล็อค" รายการนี้หรือไม่ ?')) {   
            updateLocke({id:id,status:1}, res => {
                if(res.locked===1){ // Locked
                    el.closest('tr').attr('data-locked', 1);
                    el.closest('td').html('<i class="fas fa-lock text-red pointer click-for-unlock"></i>');                    
                }
            });
        }
    }
    
});


$('body').on('click', '.transport-list-update', function(){
    let thisBtn = $(this);
    let id      = $(this).closest('tr').attr('data-so');

    fetch("?r=SaleOrders/reserve/transport-list-update", {
        method: "POST",
        body: JSON.stringify({ id: parseInt(id) }),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
        //console.log(response);
        if(response.status===200){
            if(response.action===1){
                thisBtn.find('i').attr('class','fas fa-truck text-info');
            }else{
                thisBtn.find('i').attr('class','fas fa-truck text-gray');
            }
            
        }else{
            if(response.status===202){ 
                alert(response.message);
            }else{
                alert('Error! Something wrong');
            }
        }            
    })
    .catch(error => {
        console.log(error);
    });
});


$('body').on('click', '.minus-inv-from-ship', function(){
        let thisBtn = $(this);
        let rows    = $(this).closest('tr').attr('data-so');

        fetch("?r=SaleOrders/reserve/transport-list-update", {
            method: "POST",
            body: JSON.stringify({ id: rows }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            //console.log(response);
            if(response.status===200){
                thisBtn.attr('class','btn btn-default-ew btn-sm plus-inv-to-ship');
                thisBtn.find('i').attr('class','fas fa-truck text-gray');
            }else{
                thisBtn.attr('class','btn btn-success-ew btn-sm minus-inv-from-ship');
                alert('Error! Something wrong');
            }            
        })
        .catch(error => {
            console.log(error);
        });
    })

    $('body').on('click','.ref',function(){
        let val     = $(this).removeClass('ref').attr('data-val');
        let html = `<input type="text" class="form-control" name="update-ref" value="` + val +`" />`;
        $(this).html(html);
    });

    $('body').on('change','input[name="update-ref"]', function(){
        let el = $(this).closest('td');
        let id = $(this).closest('tr').attr('data-key');
        let val = $(this).val();
        fetch("?r=accounting/ajax/invoice-update-ref", {
            method: "POST",
            body: JSON.stringify({id:id, val:val}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            if(response.status===200){
                el.addClass('ref').attr('data-val',val);
                el.text(val);
            }else{
                // Error
                // $.notify({
                //     // options
                //     icon: "fas fa-exclamation-circle",
                //     message: response.message
                // },{
                //     // settings
                //     placement: {
                //     from: "top",
                //     align: "center"
                //     },
                //     type: "error",
                //     delay: 4000,
                //     z_index: 3000
                // });
            }
            
        })
        .catch(error => {
            console.log(error);
        });
    });


    const lockAllDocument = (obj, callback) => {
        fetch("?r=accounting/ajax/locked-all", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.click-for-lock-all', function(){
        let raws = [];
        $('#export_table tbody tr i.click-for-locked').each((key,el) => {
            raws.push({
                id:$(el).closest('tr').attr('data-key'),
                no:$(el).closest('tr').find('td.doc-no').text()
            }); 
        });
        
        if(raws.length > 0){
            if(confirm("{$Yii::t('common','Confirm Locked')}")){
                lockAllDocument(raws, res =>{
                    if(res.status==200){
                        $.notify({
                            // options
                            icon: "fas fa-check text-green",
                            message: "{$Yii::t('common','Success')}"
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "info",
                            delay: 5000,
                            z_index: 3000
                        });

                        
                        $('#export_table tbody tr i.click-for-locked').each((key,el) => {                            
                            $(el).closest('td').html('<i class="fas fa-lock text-red pointer click-for-unlock"></i>');
                        });


                    }else{
                        $.notify({
                            // options
                            icon: "fas fa-exclamation-circle",
                            message: res.message
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "error",
                            delay: 4000,
                            z_index: 3000
                        });
                    }
                })
            }
        }else{
            alert('ไม่มีรายการ');
        }
        
    });

    $('body').on('click', 'a.export-to-excel', function(){
         
        tableToExcel('export_table', 'CT', 'Invoice-All.xls');
    })

    $('body').on('click', 'a.export-to-word', function(){
        
        try {
            downloadWord('Invoice-All');
        } catch (error) {
            console.log(error);
        }
        
    })

    $('body').on('click', 'a.download-excel', function(){
        var imgURL = $(this).attr('data-as');
        //var imgWindow = window.open(imgURL,"MsgWindow", "width=200,height=100");
        var imgWindow = window.open(imgURL,"MsgWindow");
        imgWindow.onload = function(){
            setTimeout(() => {
                //imgWindow.close();
            }, 2000);
            
        };
    })
 

JS;


$this->registerJs($jsInv,Yii\web\View::POS_END);

?>

<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 