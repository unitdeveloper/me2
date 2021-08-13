<?php
use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

use kartik\widgets\ActiveForm;
use common\models\SalesPeople;
use common\models\Customer;

use kartik\widgets\Select2;


$this->title = Yii::t('common','Customer item sale');
?>
<style type="text/css">
    
 
    .text-page{
        counter-increment: page;        
        content: counter(page);
    }
    
  @media print{
    @page {
        margin-top:21px !important;
        size: A4 portrait; 
    }
    body{
        font-family: 'saraban', 'roboto', sans-serif; 
        font-size:10px !important;
    }

    body table{
        font-size:9px !important;
    }

    .text-page{
        counter-increment: page !important;        
        content: counter(page) !important;
    }

    .text-page:after{
        content: "Page " counter(page) " of " counter(pages); 
        /* content: counter(page);*/
    }

    
    .btn-print{
      display: none;
    }
    .remark span{
      color: red;
    }
    .pagination,
    .search-box,
    caption{
      display: none;
    }
    .dataCalc{
      border:0px;
    }
    .textComment{
      border:0px;
    }
    a[href]:after {
      content: none !important;
    }

  }

   
    



  .btn-print{
      background-color: rgb(253,253,253);
      border-bottom: 1px solid #ccc;
      margin-bottom: 20px;
  }


	.input-group-addon{
		background-color: rgb(249,249,249) !important;
		border: 1px solid #999 !important;

	}

  a.view-receipt{
    padding: 0 5px 0 5px;
     
  }

  a.view-receipt:hover{
    color: red;
  }
  .select2-selection{
    height: 34px !important;

  }
  .select2-container--krajee .select2-selection--single .select2-selection__placeholder {
    color: #999;
     
  }

  .select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 5px;   
  }

  .text-sum{
    margin:20px 0 0 0;
  }

  .text-sumVal{
    margin:20px 0 0 0;
    border-bottom: 5px double #ccc;
  }

  .sum-footer{
    margin-top: 10px;
    border-bottom: 1px solid #ccc;
  }

  .modal
  {
    overflow: hidden;
    background:none !important;

  }


  .modal-dialog{
     box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
  }

  .box-color{
    width:20px !important;
    height:20px;
    border:1px solid #ccc;
    position:absolute;
    margin-left:-25px;
  }

  table{
    font-family:  Arial, Helvetica, sans-serif;
  }

  .payment-detail-modal:hover,
  .invoice-detail:hover {
    background: #3fbbea !important;
  }

  @media (max-width: 767px) {
        .search-box{
           margin-top:50px;
        }

        #vat-change {
            margin-top: 10px;
        }

  }
</style>
<div class="row btn-print ">
  <div class="col-xs-12" >
        <?php $form = ActiveForm::begin(['id' => 'search-item','method' => 'POST']); ?>
        <div class="row" style="margin-bottom: 10px;">
          <div class=" ">
            <div class="col-lg-4 col-md-6 col-sm-4">  
            <label><?=Yii::t('common','Date Filter')?></label>
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
              		'type' => DatePicker::TYPE_RANGE,
					'name' => 'fdate',
					'value' => Yii::$app->request->get('fdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('fdate'))) : date('Y-m-d'),					
					'name2' => 'tdate',
					'value2' => Yii::$app->request->get('tdate') ? date('Y-m-d',strtotime(Yii::$app->request->get('tdate'))) : date('Y-m-d'),                  
					'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'layout' => $layout,
                    'options' => ['autocomplete'=>'off'],
                    'options2' => ['autocomplete'=>'off'],
					'pluginOptions' => [
						'autoclose'=>true,
                        'format' => 'yyyy-mm-dd',
						//'format' => 'dd-mm-yyyy'
					],
              ]);
              ?>
            </div>

            <div class="col-sm-2 col-xs-4 hidden-xs"> 
               
	            <div class="input-group " >
                <label><?=Yii::t('common','Sales')?></label>
	                <?= Html::dropDownList('search-from-sale', null,
	                    					ArrayHelper::map(
                                                SalesPeople::find()
                                                ->where(['status'=> 1])
                                                ->andWhere(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                                                ->orderBy(['code' => SORT_ASC])
                                                ->all(),'id',function($model){
				                                                    return '['.$model->code.'] '.$model->name. ' '.$model->surname;
				                                            	}
				                                            ),[
					                    						'class'=>'form-control',
					                    						'prompt' => Yii::t('common','Every one'),
					                    						'options' => [                        
                                                                    Yii::$app->request->get('search-from-sale')  => ['selected' => 'selected']
                                                                ],
					                    					] 
	                						) 
	                ?>
	               
	            </div>
               
            </div>
             <div class="col-sm-3  col-xs-8  hidden-xs">
                <label><?=Yii::t('common','Customers')?></label>
                <?php 
                  $keys = 'customer&comp:'.Yii::$app->session->get('Rules')['comp_id'];
                  $customerList = Yii::$app->cache->get($keys);                  
                  if($customerList){
                    $customer = $customerList;
                  }else{
                    $customer = ArrayHelper::map(Customer::find()->where(['comp_id'=>Yii::$app->session->get('Rules')['comp_id'],'status'=>'1'])->orderBy(['code' => SORT_ASC])->all(),
                                  'id',
                                  function($model){ 
                                    return '['.$model->code.'] '.$model->name.' ('.$model->getAddress()['province'].')'; 
                                  }
                                );
                
                    Yii::$app->cache->set($keys,$customer, 3600);
                  }
                ?>
                <?= Select2::widget([
                    'name' => 'customer',
                    'id' => 'customer',
                    'data' => $customer,
                    'options' => [
                        'placeholder' => Yii::t('common','Customer'),
                        'multiple' => false,
                        'class'=>'form-control ',
                    ],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                    'value' => Yii::$app->request->get('customer') ?  Yii::$app->request->get('customer') : ''
                ]);
              ?>
            </div>
            <div class="col-sm-2 col-xs-12 hidden-xs">
              <label><?=Yii::t('common','Vat')?></label>
              <select class="form-control  mb-10" name="vat-filter" >
                  <option value="0"><?= Yii::t('common','All') ?></option>
                  <option value="Vat">Vat</option>
                  <option value="No">No Vat</option>
              </select> 
            </div>     
            <div class="col-sm-1  col-xs-12 text-right" style="padding-top: 25px;">
            	<button type="submit" class="btn btn-info-ew"><i class="fa fa-search" aria-hidden="true"></i> <?= Yii::t('common','Enter')?></button>              
            </div>
            
          </div><!-- /.col-sm-offset-6 -->
          
        </div><!-- /.row -->
      <?php ActiveForm::end(); ?>
    </div>
</div>
 
<div class="render-table"></div>
 
<div class="modal fade" id="modal-invoice-detail">
    <div class="modal-dialog   modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>  
            <div class="modal-body bg-gray">
                <div class="row">                    
                    <div class="col-xs-12 renders">                     
                         
                    </div> 
                    <div class="col-xs-12">
                        <div class="loading" style="height:50px;"></div>
                    </div>
                </div>
            </div>         
            <div class="modal-footer bg-primary">
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
    fetch("?r=accounting/rcreport/all-invoice-mobile-ajax", {
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
                                    <div class="col-md-2 col-sm-6">
                                        <div>
                                        {$Yii::t('common','From date')} <span class="from-date ml-10">` + res.data.fdate +`</span>
                                        {$Yii::t('common','To Date')} <span class="to-date ml-10">` + res.data.tdate +`</span>
                                        </div>
                                    </div>             
                                </div>
                            </th>
                        </tr>
                        <tr class="bg-gray">
                            <th>#</th>
                            <th>{$Yii::t('common','dd/mm/yy')}</th>
                            <th>{$Yii::t('common','No.')}</th>
                            <th class="hidden-xs">{$Yii::t('common','Customer')}</th>                     
                            <th>{$Yii::t('common','Sale Order')}</th>
                        </tr>
                    </thead>`;
 
       

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
            if ( a.date < b.date ){
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

            TotalDis    += model.discount;
            TotalSum    += model.sumline;
            TotalVat    += model.incvat;
            grandTotal  += model.balance;
            TotalReceipt+= payment;

            html+= `<tr data-key="`+ model.id +`" >
                        <td class="text-center">`+ i +`</td>
                        <td>` + model.date + ` </td>
                        <td class="pointer invoice-detail text-info `+ ( model.posted == 'Posted' ? '' : 'text-orange')+`" 
                            data-id="`+ model.id +`" 
                            data-val="`+ model.no +`" 
                            data-status="`+ model.posted +`"  
                            data-so="` + model.orderNo + `" 
                            data-date="` + model.date + `" 
                            data-soid="`+model.orderId+`">` + model.no + ` ` + (model.modern ===2 ? '<i class="fas fa-star text-green"></i>' : '') + `
                        </td>
                        <td class="hidden-xs">` + model.custName + `</td>
                        <td><a href="?r=SaleOrders%2Fsaleorder%2Fprint&id=` + model.orderId + `" target="_blank">` + model.orderNo + `</a></td>
                    </tr>`;
        })
        html+= '</tbody>';
        
        html+= `</table>`;

        
        $('.text-headoffice').html(res.data.headOffice ? '{$Yii::t("common","Head Office")}' : '{$Yii::t("common","Branch")}' )

    if(res.data.raw.length > 0){
        $('.render-table').html(html);
        
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
 
        $('body').find('caption button').text('{$Yii::t("common","Export to Excel")}')         
        $('body').find('#text-search').val(state.search).focus();   
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

    const child = (detail, model) => {
        let childTable = ' ';
        let x = 0;
        detail.map(el => {
            x++;
            let qty = model.return
                            ? `<div class="text-red"> + ` + model.return + `</div>`
                            : parseInt(el.qty * model.qty);

            childTable += el.detail.length >= 1 
                ? child(el.detail, model) 
                : ` <tr class="` + (model.return ? 'bg-green' : 'bg-yellow' ) + `">
                        <td></td>
                        <td><img src="` + el.img + `" width="35"/></td>
                        <td><a href="?r=items%2Fitems%2Fview&id=`+ el.item +`" target="_blank" class="text-white" >`+ el.code +`</a></td>
                        <td>`+ el.name + `</td>  
                        <td class="text-right" >`+ parseInt(el.qty) + `</td>
                        <td class="text-right" style="min-width: 40px;"><a href="?WarehouseSearch[ItemId]=`+ btoa(el.item) +`&WarehouseSearch[SourceDoc]=`+soid+`&r=warehousemoving%2Fwarehouse" target="_blank" class="text-white" >`+ qty +`</a></td>
                    </tr>`;
        });
        childTable += ' ';
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
            
        
            let html = `<div class="table-responsive">
                        <table class="table table-bordered">`;
                html+= `<thead>
                            <tr class="bg-dark">
                                <th style="width:50px;">#</th>
                                <th colspan="2">{$Yii::t('common','Code')}</th>
                                <th>{$Yii::t('common','Name')}</th>
                                <th colspan="2" class="text-right"  style="width:100px;">{$Yii::t('common','Quantity')}</th>
                            </tr>
                        </thead>`;
                html+= `<tbody>`;
            let i = 0;
            res.data.raw.map(model => {
                i++;
                let qty = model.return
                            ? `<div class=""> <span class="blink text-green" style="font-size:20px;">+</span> ` + model.return + `</div>`
                            : number_format(parseFloat(model.qty));

                html+= `<tr data-key="` + model.item + `" class="` + (model.return ? 'bg-red' : ' ') + `" >
                            <td><div style="position:absolute;">` + i + `</div> <img src="` + model.img + `" width="35"/></td>
                            <td colspan="2"><a href="?r=items%2Fitems%2Fview&id=`+ model.item +`" target="_blank" class="text-white" >` + model.code + `</a></td>    
                            <td>` + model.name + `</td>    
                            <td colspan="2" class="text-right"><a href="?WarehouseSearch[ItemId]=`+ btoa(model.item) +`&WarehouseSearch[SourceDocNo]=`+so+`&r=warehousemoving%2Fwarehouse" target="_blank" class="text-white" >` + qty + `</a></td>                     
                        </tr>`;
                html+= model.detail.length >= 1 ? child(model.detail,model) : '';
            })
                html+= `</tbody>`;
                html+= `</table>
                        </div>`;
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
