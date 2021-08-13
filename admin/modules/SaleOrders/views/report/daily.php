<?php 
$this->title = Yii::t('common','Daily Report');
?>
<div ng-init="Title='<?=$this->title?>'">

  <h3>รายงานการขาย ประจำปี : <?=date('Y')?></h3>
  <div class="row">
    <div class="col-xs-3 col-sm-2 col-lg-1 hidden"  >
      <button type="button" class="btn btn-default-ew new-calculate" >คำนวนใหม่</button>
    </div>
    <div class="col-xs-12">
      <div>
        <small class="credit-note-section" style="display:none;"><label for="credit-note"><input type="checkbox" name="credit-note" value="1" checked id="credit-note"/> รวมใบลดหนี้แล้ว</label></small>
      </div>
      <div class="hidden">
        <small><label for="modern-trade"><input type="checkbox"  name="modern-trade" value="1" checked id="modern-trade"/> รวมรายการขายห้างสรรพสินค้า</label></small>
      </div>
    </div>
  </div>

  <div class="mt-5"></div>

  <div id="render-date" class="font-roboto"></div>
  
  <div class="modal modal-full fade" id="modal-show-inv-list">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?=Yii::t('common','Invoice List')?></h4>
        </div>
        <div class="modal-body">
          <div class="row">
              <div class="col-sm-12" id="renderInvoice" style="padding-bottom:10px;"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button> 
        </div>
      </div>
    </div>
  </div>

</div>
<?php

$years  = date('Y');
$Yii    = 'Yii';
$js=<<<JS

const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> Loading... </div>
            <img src="images/icon/loader2.gif" height="122"/>            
        </div>`;


function m(key){
    let m = '';
    switch (key) {
        case 1:
          m = 'มกราคม / Jan';
          break;

        case 2:
          m = 'กุมภาพันธ์  / Feb';
          break;

        case 3:
          m = 'มีนาคม / Mar';
          break;

        case 4:
          m = 'เมษายน / Apr';
          break;

        case 5:
          m = 'พฤษภาคม / May';
          break;

        case 6:
          m = 'มิถุนายน / Jun';
          break;

        case 7:
          m = 'กรกฏาคม / Jul';
          break;

        case 8:
          m = 'สิงหาคม / Aug';
          break;

        case 9:
          m = 'กันยายน / Sep';
          break;

        case 10:
          m = 'ตุลาคม / Oct';
          break;

        case 11:
          m = 'พฤศจิกายน / Nov';
          break;

        case 12:
          m = 'ธันวาคม / Dec';
          break;
        
        default:
          m = 'Unknow';
          break;
      }

      return m;
  }


  const monthlyReportApi =  async (obj, callback) => {
    let api = await fetch("?r=accounting/report/monthly&m="+obj.m, {
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


const  everyDay = async (callback) => {
  
  const month = [{1:31},{2:29},{3:31},{4:30},{5:31},{6:30},{7:31},{8:31},{9:30},{10:31},{11:30},{12:31}];
  let body    = '';

  
  body+= `<div class="row" >`;
  month.map((val, key) => {
    body+= `<div class=" col-xs-12" style="margin-bottom: 150px;">`;
    body+= `<table class="table table-bordered table-hover" id="m-`+(key+1)+`" data-key="`+(key+1)+`">
              <thead>
                  <tr class="bg-dark">
                    <th colspan=3><h3>` + m(key+1) + `</h3></th>
                  </tr>
                  <tr class="bg-gray">
                    <th class="text-center" style="width:110px;"><h4> {$Yii::t('common','Date')} </h4></th>
                    <th class="text-right"><h4> {$Yii::t('common','Balance')} </h4></th>
                    <th class="text-center" style="width:300px;"><h4>{$Yii::t('common','Total')}</h4></th>
                  </tr>
                </thead> 
                <tbody>
              `;

              for (let index = 1; index <= val[key+1] ; index++) {
          
                body+= `<tr class="row-data" data-key="`+index+`">
                          <td class="text-center"><h3>` + ('0' + index).slice(-2)  + `</h3></td>
                          <td class="text-right balance pointer"><i class="fa fa-spinner fa-spin" > </td>
                          <td ` +(index === 1 ? 'rowspan=' +(val[key+1])+ ' class="totals text-center bg-gray" ' : 'class="totals hidden"')+ `>
                             <i class="fa fa-spinner fa-spin" ></i> 
                          </td>
                        </tr>
                `;
              }

    body+= `  </tbody>
            </table>`;
    body+= `</div>`;
 
  });
  body+= `</div>`;


 
  callback({
    table:body
  });
}

const fetchDays = () => {
  let cr = $('#credit-note').is(":checked") ? 1 : 0; // Credit Note
  let mo = $('#modern-trade').is(":checked") ? 1 : 0; // Modern Trade

  let doing = everyDay(res => {
              $('#render-date').html(res.table);
            })
            
      doing.then(() => {
         
        for (let index = 1; index <= 12; index++) {

          let api = monthlyReportApi({m:index, cr:cr, mo:mo}, res =>{
            
            let tables = $('body').find('table#m-'+(index)+' > tbody > tr.row-data');
                tables.map((i, el) => {
                  let dKey    = parseInt($(el).attr('data-key'));
                  let resDay  = res.raw.filter((d) => {  return d.days == i+1; });
                  //console.log(dKey)
                  let resDate = resDay[0] ? resDay[0].days : null;

                  //console.log(resDate)
                  if(dKey===resDate){
                    let balance = resDay[0].balance;
                    $('body').find('table#m-'+(index)+' > tbody > tr.row-data[data-key=' +dKey+ ']').addClass('bg-info').find('td.balance').attr('data-val',balance).html(`<h3>`+number_format(balance.toFixed(2))+`</h3>`);
                  }else{
                    $('body').find('table#m-'+(index)+' > tbody > tr.row-data[data-key=' +dKey+ ']').find('td.balance').attr('data-val',0).html(`<h3>0</h3>`);
                  }

                });


                let total = 0;                 
                tables.map((i, el) => {
                  total+=  $(el).find('td.balance').attr('data-val') * 1;
                   
                });
                 
                $('body').find('table#m-'+(index)+' > tbody > tr.row-data > td.totals').attr('data-val',total).html(`<h2>` + number_format(total.toFixed(2))+ `</h2>`);

                
          });

        }

      })
    setTimeout(() => {
      $('body').find('.credit-note-section').fadeIn('slow');
    }, 1000);
     
}

$(document).ready(function(){
  fetchDays();
});



let getInvoiceFromApi = (obj, callback) => {
    fetch("?r=accounting/ajax/invoice-all", {
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




const renderTableInvList = (data, calback) => {
let tbvat = 0;
let tvat  = 0;
let tgrand= 0;
 
let rows  = ''
if(data.length > 0){

  data.map((model, keys) => {

      let sumLine     = model.balance.invat == 0 ? (model.balance.exvat * 1) : (model.balance.subtotal * 1);
      let incVat      = model.balance.incvat * 1;
      let balance     = model.balance.total * 1;
      let countDoc    = model.no.trim().length;
    
      tbvat+=sumLine;
      tvat+=incVat;
      tgrand+=balance;

      let url   = model.status != 'Posted' 
                  ? `?r=accounting/saleinvoice/print-inv-page&id=` + model.id + `&footer=1`
                  : `?r=accounting/posted/print-inv&id=` + btoa(model.id) + `&footer=1`;

      rows+= `<tr 
                  data-key="` + model.id +`" 
                  data-status="` + model.status + `" 
                  data-locked="` + model.locked + `"
                  data-so="` + model.orderId + `"
                  class="" 
                  style="` + (model.bg ? `background:` + model.bg + `;` : ' ') + `">    
                  
                  <td class="text-center">` + (keys + 1) +`</td>    
                  <td>` + model.date + `</td>    
                  <td>` + model.orderNo + `</td>          
                  <td class="doc-no ` + (model.status==='Open' 
                              ? 'new-line pointer text-success' 
                              : (model.new == true  
                                  ? `new-line pointer`
                                  : ` `) 
                              ) + `  ">
                              <a href="` + url +`" class=" " target="_blank">` + model.no + `</a>
                  </td>       
                  <td class="sales">
                    <span title="`+(model.sale_code + ' : ' + model.sale_name)+`">`  + model.sale_name + ` </span>
                  </td>                
                  <td>` + model.custCode + `</td>
                  <td>` + model.custName + `</td>
                  <td class="text-right">` + number_format(sumLine.toFixed(2)) + `</td>
                  <td class="text-right">` + number_format(incVat.toFixed(2)) + `</td>
                  <td class="text-right">` + number_format(balance.toFixed(2)) + `</td>
                  
              </tr>`;
  });

}else{
  rows+= `<tr > <td colspan="8">{$Yii::t('common','No Data')}</td>  </tr>`;
}

let html = `<table class="table table-bordered table-hover font-roboto" id="export_table">
            
            <thead>
              <tr class="bg-primary">                    
                <th class="text-center" style="width:10px;">#</th>  
                <th style="width:80px;">วันที่</th>     
                <th style="width:100px;">ใบสั่งขาย</th>                          
                <th style="width:120px;" title="ใบกำกับภาษี">ใบกำกับภาษี</th>   
                <th style="width:150px;">ผู้ขาย</th>                   
                <th style="width:95px;">รหัสลูกค้า</th>
                <th>ลูกค้า</th>
                <th class="text-right" style="width:80px; padding-right: 16px;">ก่อน Vat</th>
                <th class="text-right" style="width:80px; padding-right: 16px;">Vat</th>
                <th class="text-right" style="width:80px; padding-right: 16px;">ยอดเงิน</th>
                 
              </tr>
            </thead>
            
            <tbody>
              ` + rows + `
            </tbody>

            <tfoot>
              <tr class="bg-gray">
                <th class="text-right" colspan="7">{$Yii::t('common','Total')}</th>
                <th class="text-right"> ` + number_format(tbvat.toFixed(2)) + ` </th>
                <th class="text-right"> ` + number_format(tvat.toFixed(2)) + ` </th>
                <th class="text-right ` + ((tbvat + tvat).toFixed(2) == tgrand.toFixed(2) ?  'text-dark' : 'text-red') + `"> ` + number_format(tgrand.toFixed(2)) + ` </th>
              </tr>
            </tfoot>
            
          </table>`;



calback({ 
  html: html,
  count: data.length
});
}



$('body').on('click', 'td.balance', function(){
  $("#modal-show-inv-list").modal("show");
  let days  = $(this).closest('tr').attr('data-key');
  let m     = $(this).closest('table').attr('data-key');
  let cr    = $('#credit-note').is(":checked") ? 1 : 0; // Credit Note
  let mo    = $('#modern-trade').is(":checked") ? 1 : 0; // Modern Trade

  $('body').find('#renderInvoice').html(loadingDiv);

  getInvoiceFromApi({
    fdate: '{$years}-'+m+'-'+days,
    tdate: '{$years}-'+m+'-'+days,
    vat: 0,
    cr:cr,
    mo:mo
  }, res => { 
 
    renderTableInvList(res.data.raw, res => {
      $('body').find('#renderInvoice').html(res.html);
      var table = $('#export_table').DataTable({
            "paging": false,
            "searching": false,
            "info" : false,
            "order": [[ 2, "desc" ]]
        });


    })    
  });

});

$('body').on('change', 'input[name="credit-note"]', function(){
  fetchDays();
})
 
 
JS;

$this->registerJS($js);
?>
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 