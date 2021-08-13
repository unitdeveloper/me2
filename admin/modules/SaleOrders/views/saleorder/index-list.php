<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;
use yii\db\Expression;
use kartik\daterange\DateRangePicker;

use admin\modules\SaleOrders\models\FunctionSaleOrder;
use admin\modules\apps_rules\models\SysRuleModels;

 
 
$this->title = Yii::t('common', 'Data Sale Order');
$this->params['breadcrumbs'][] = $this->title;

$IV_NO = \admin\models\Series::invoiceNo('view_rc_invoice', 'no_', 'all', 'Sale', 'IVCT');
 
$this->registerCssFile('css/sale-order.css?v=3.6.01',['rel' => 'stylesheet','type' => 'text/css']);
$this->registerCssFile('css/sales_order/index.css?v=4',['rel' => 'stylesheet','type' => 'text/css']);
 

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

?>
<style>
.btn-app>.far, 
.btn-app>.fas{
    font-size: 20px;
    display: block;
}

.box{
    border: 1px solid; 
    padding-top: 20px;
}
.dataTables_filter{
    padding-right: 7px;
}
.dataTables_wrapper .dataTables_filter {
    float: right;
    text-align: left;
    margin-left:7px;
}
input[type="search"]{
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
}
.my-cart{
    position:fixed;
    right: 55px;
    top: 2px;
    z-index:3000;
    border:1px solid #ff6500;
    background: #fff;
    border-radius: 30px;
    height: 40px;
    width: 60px;
    padding: 10px 0px 0px 0px;
    display:none;
}
.loading{
    position:fixed;
    top:50%;
    left:48%;
    z-index:3001;
    display:none;
}
</style>
<div class="loading"><i class="fas fa-sync-alt fa-3x fa-spin text-info"></i></div>
<div class="sale-header-index" ng-init="Title='<?=$this->title;?>'">
    <div class="my-cart btn show-my-cart">
        <i class="fas fa-cart-arrow-down text-aqua"></i>
        <span id="count-item-cart"></span>
    </div>
    <div class="row text-center">
        <div class="col-xs-4">
            <?php //$startDate = date('Y').'-'.(date('m') - 3).'-'.date('t'); ?>
            <?=DatePicker::widget([
                'type'      => DatePicker::TYPE_RANGE,
                'name'      => 'fdate',
                'value'     => Yii::$app->request->get('fdate') 
                                ? date('Y-m-01',strtotime(Yii::$app->request->get('fdate'))) 
                                : date('Y-m-01'),					
                'name2'     => 'tdate',
                'value2'    => Yii::$app->request->get('tdate') 
                                ? date('Y-m-d',strtotime(Yii::$app->request->get('tdate'))) 
                                : date('Y-m-t'),                  
                'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                'layout'    => $layout,
                'options'   => ['autocomplete'=>'off'],
                'options2'  => ['autocomplete'=>'off'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'yyyy-mm-dd'
                ],
            ]);?>
        </div>
        <div class="col-xs-2">
                <select name="order_status" id="order-status" class="form-control">
                    <option value="0" selected><?=Yii::t('common','New Order')?></option>
                    <option value="2"><?=Yii::t('common','Created invoiced')?></option>
                    <option value="1"><?=Yii::t('common','Closed Order')?></option>
                </select>
        </div>
        <div class="col-xs-6">
            <div class="input-group">                
                <?= Html::dropDownList('company_id', null,
                    arrayHelper::map(\common\models\Company::find()->where(['IN','id',[1,45,67,68]])->all(),'id', 'name'),[
                        'class' => 'form-control',   
                        //'prompt'=>'เลือกลูกค้า',
                        'options' => [                        
                            1 => ['selected' => 'selected']
                        ],
                ]); ?>
                <span class="input-group-addon btn btn-refresh-data" id="basic-addon1"><i class="fas fa-download"></i></span>
             </div>
             
        </div>
    
    </div>

    
    <div class="row text-center hidden">
        
        <div class="col-xs-3 text-center">
        <div class="box-body box">
            <a class="btn btn-app text-aqua">
                <i class="fas fa-file-signature "></i> <?=Yii::t('common','Sale Order');?>
            </a>               
        </div>
        </div>
        <div class="col-xs-1 text-center" style="padding-top: 45px;">
            <i class="fas fa-arrow-right"></i>
        </div>
        <div class="col-xs-3 text-center">
            <div class="box-body box">
                <a class="btn btn-app text-yellow">
                    <i class="fas fa-dolly-flatbed"></i> <?=Yii::t('common','Stock');?>
                </a>               
            </div>
        </div>
        <div class="col-xs-1 text-center" style="padding-top: 45px;">
            <i class="fas fa-arrow-right"></i>
        </div>
        <div class="col-xs-3 text-center">
            <div class="box-body box">
                <a class="btn btn-app text-red">
                    <i class="fas fa-file-invoice"></i> <?=Yii::t('common','Tax Invoice');?>
                </a>               
            </div>
        </div>
    </div>
    <a href="#demo" class="mt-10" data-toggle="collapse" title="<?=Yii::t('common','help')?>"><i class="fas fa-question-circle"></i></a>

    <div id="demo" class="collapse">
        ระบบดึงข้อมูลจากบริษัทอื่นๆ 
        <ul>
            <li>ดึงข้อมูลใบสั่งขายจากบริษัทอื่น เพื่อนำไปเปิดเป็นใบสั่งขาย ของบริษัทเราอีกที </li>
        </ul>
    </div>           
    <div class="renders"></div>
    
</div>




 
<div class="modal fade modal-full" id="modal-order-detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Sale Order')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <a href="#" target="_blank" class="btn btn-primary-ew print-label"><i class="fas fa-print"></i> <?=Yii::t('common','Label')?></a>
                <button type="button" class="btn btn-primary add-to-cart"><i class="fas fa-cart-arrow-down"></i> <?=Yii::t('common','Add to cart')?></button>
            </div>
        </div>
    </div>
</div>

 
<div class="modal fade modal-full" id="modal-show-my-cart">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#2ea4dc;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Show My Cart</h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer" style="background:#2ea4dc;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-warning confirm-create-bill"><i class="fas fa-tasks"></i> <?=Yii::t('common','Create Invoice')?></button>
                <button type="button" class="btn btn-success confirm-create-sale-order"><i class="fas fa-dollar-sign"></i> <?=Yii::t('common','Create Sale Order')?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-confirm-create">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Confirm')?></h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-top: -15px; margin-bottom:-15px; padding:20px 0px 20px 0px; background:#fff; color:#000;">
                    <div class="col-xs-6"></div>
                    <div class="col-xs-6">
                        <label for="inv-no"><?=Yii::t('common','Tax Invoice')?></label>
                        <input type="text" value="<?=$IV_NO?>" name="no" id="inv-no" class="form-control"/>

                        
                    </div>
                </div>
                <div class="row" style="margin-top: -15px; margin-bottom:-15px; padding:20px 0px 20px 0px; background:#fff; color:#000;">

                    <div class="col-sm-6"> 
                        <label for="inv-no"><?=Yii::t('common','Customer')?></label>
                        <?= Html::dropDownList('customer_id', null,
                            arrayHelper::map(\common\models\Customer::find()
                            ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                            ->orderBy(['code' => SORT_ASC])
                            ->all(),
                            'id', function($model){
                                return '['.$model->code.'] '.$model->name;
                            }),[
                                'class' => 'form-control',   
                                //'prompt'=>'เลือกลูกค้า',
                                'options' => [                        
                                    1469 => ['selected' => 'selected']
                                ],
                        ]); ?>
                    </div>
                    <div class="col-xs-6">
                        <div class="">
                            <label for="order_date"><?=Yii::t('common','Order Date')?></label>
                            <?=DatePicker::widget([
                                'type'          => DatePicker::TYPE_COMPONENT_APPEND,
                                'name'          => 'order_date',
                                'value'         => Yii::$app->request->get('odate') 
                                                    ? date('Y-m-d',strtotime(Yii::$app->request->get('odate')))
                                                    : date('Y-m-d',strtotime('this week', time())),
                                'options'       => ['autocomplete'=>'off', 'id' => 'order_date'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format'    => 'yyyy-mm-dd'
                                ],
                            ]);?>
                        </div>
                        
                        
                    </div>
                   
                </div>

                <div class="row" style="margin-top: -15px; margin-bottom:-15px; padding:20px 0px 20px 0px; background:#fff; color:#000;">
                    <div class="col-xs-6"></div>
                    <div class="col-xs-6">
                         <input type="text" name="sale_id" data-key="" value="" class="form-control hidden" readonly/>
                         <input type="text" name="sale_code" data-key="" value="" class="form-control" readonly/>
                    </div>
                </div>


                <div class="row" style="margin-top: -15px; margin-bottom:-15px; padding:20px 0px 20px 0px; background:#fff; color:#000;">
                    <div class="col-xs-12 mt-5">
                        <label for="remark"><?=Yii::t('common','Remark')?></label>
                        <textarea name="remark" id="remark" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="row" style="margin-top: -15px; margin-bottom:-15px; padding:20px 0px 20px 0px; background:#fff; color:#000;">
                    <div class="col-xs-6"></div>
                    <div class="col-xs-6">
                        <div class="mt-5">
                            <?php 
                                $dataVat = \common\models\VatType::find()->all();
                            ?>
                            <label for="vat-percent"><?=Yii::t('common','Vat')?></label>
                            <?= Html::dropDownList('vat_percent', null,
                                arrayHelper::map($dataVat,'vat_value', 'name'),[
                                    'class' => 'form-control',  
                                    'options' => [                        
                                        0 => ['selected' => 'selected']
                                    ],
                            ]); ?>
                             
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Cancel')?></button>
                <button type="button" class="btn btn-success click-create-invoice"><i class="fas fa-tasks"></i> <?=Yii::t('common','Confirm')?></button>
                <button type="button" class="btn btn-success click-create-sale-order"><i class="fas fa-dollar-sign"></i> <?=Yii::t('common','Confirm')?></button>
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
        
    const getSaleorderFromAPI = (obj,callback) => {

        $('#export_table > tbody').html(`<tr><td colspan="7">` + loadingDiv + `</td></tr>`);
        fetch("?r=SaleOrders%2Fsaleorder/index-list-ajax", {
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
                swal('{$Yii::t("common","Warning")} : ' + res.message, res.count, "warning");
            }
            
        })
        .catch(error => {
            console.log(error);
        });
    }

    const getSaleLineFromAPI = (obj,callback) => {

         fetch("?r=SaleOrders/saleorder/index-line-ajax", {
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
                swal('{$Yii::t("common","Warning")} : ' + res.message, res.count, "warning");
            }
            
        })
        .catch(error => {
            console.log(error);
        });
    }
    

    const showDetail = (obj, no, el) => {
        $('#modal-order-detail').modal('show');
        $('#modal-order-detail').find('.modal-body').html(loadingDiv);
        $('#modal-order-detail').attr('data-no',no);

        getSaleLineFromAPI(obj, res => {
            let body = ``;
            let i = 0;

            const childBom = (data,i) => {
                let body = ``;
                if(data.detail && data.detail.length  > 0){
                 
                    data.detail.map((model,keys) => {
                        let sub = keys + 1;
                        let qty = data.qty * model.qty;
                        body+= `
                            <tr 
                                data-key="` + model.id + `" 
                                data-source_id="` + data.source_id + `" 
                                data-item="` + model.item + `" 
                                data-qty="` + qty + `" 
                                data-cost="` + model.cost + `"  
                                data-price="`+ (model.price *1) + `"
                                data-sale="` + (el.closest('tr').find('td.sales').attr('data-id')) + `"
                                data-saleCode="` + (el.closest('tr').find('td.sales').attr('data-saleCode')) + `"
                                data-cust="` + (el.closest('tr').find('td.cust').attr('data-id')) + `"
                                data-measure="` + model.measure + `"
                                class="pointer" 
                                style="background:#f9f9f9;">

                                <td class="text-right">`+(i + `.` + sub )+`</td>
                                <td class="item-code"><a href="?r=items/items/view&id=`+model.item+`" target="_blank" >`+model.code+`</a></td>
                                <td class="item-name">`+model.name+`</td>
                                <td class="text-right">`+number_format(qty)+`</td>
                                <td class="text-center"><input type="checkbox" name="select_item" /></td>
                            </tr>`;
                        body+= childBom(model,i+'.'+sub);
                    })
                }

               return body;
            }

            res.raws.map((model,key) => {
                i++;
                body+= `
                    <tr 
                    data-key="` + model.id + `" 
                    data-source_id="` + model.source_id + `" 
                    data-item="` + model.item + `" 
                    data-qty="` + model.qty + `" 
                    data-cost="` + model.cost + `" 
                    data-price="`+ (model.price *1) + `"
                    data-sale="` + (el.closest('tr').find('td.sales').attr('data-id')) + `"
                    data-saleCode="` + (el.closest('tr').find('td.sales').attr('data-saleCode')) + `"
                    data-cust="` + (el.closest('tr').find('td.cust').attr('data-id')) + `"
                    data-measure="` + model.measure + `"
                    class="bg-info pointer">
                        <td class="text-left">`+i+`</td>
                        <td class="item-code"><a href="?r=items/items/view&id=`+model.item+`" target="_blank" >`+model.code+`</a></td>
                        <td class="item-name">`+model.name+`</td>
                        <td class="text-right">`+number_format(model.qty)+`</td>
                        <td class="text-center">
                            `+ (model.detail && model.detail.length ? `` :  `<input type="checkbox" name="select_item" />`) +`
                        </td>
                    </tr>`;

                body+= childBom(model,i);
                
              
            })
           
            let table = `
                    <div> `+ no +`</div>
                    <table class="table table-bordered font-roboto" id="export_list">
                        <thead>
                            <tr >
                                <th style="width:50px;">#</th>
                                <th>{$Yii::t('common','Item Code')}</th>
                                <th>{$Yii::t('common','Item Name')}</th>
                                <th class="text-right">{$Yii::t('common','Quantity')}</th>
                                <th style="width:50px;"> </th>
                            </tr>
                        </thead>
                        <tbody>` + body + `</tbody>
                    </table>
                `;

            $('#modal-order-detail').find('.modal-body').html(table);

            if(res.header.shipment > 0){
                $('#modal-order-detail').find('a.print-label').show();
                $('#modal-order-detail').find('a.print-label').attr('href', '?r=warehousemoving/shipment/print-ship&id='+ res.header.shipment +'&footer=1');
            }else{
                $('#modal-order-detail').find('a.print-label').hide();
            }
            
        });
    }

    const renderSaleOrder = () => {
        let data    = state.data.raws;
         
        let body    = ``;

        
        data.map((model, key) => {
            body+= `
                <tr data-key="` + model.id + `" data-no="` + model.no + `">
                    <td class="text-center">`+(key + 1)+`</td>
                    <td>`+ model.order_date + `</td>
                    <td><b>`+ model.no + `</b></td>
                    <td>`+ model.shipdate + `</td>
                    <td class="cust" data-id="` + model.cust_id + `">`+ model.cust_code + `</td>
                    <td class="sales" data-id="` + model.sale_id + `" data-saleCode="`+ model.sale_code + `">`+ model.sale_code + `</td>
                    <td style="width:100px;">
                        <button type="button" class="btn btn-info show-order-detail"><i class="fas fa-list"></i></button>
                        <button type="button" class="btn btn-default hide-order" data-set="` + model.order_status + `"><i class="fas fa-times"></i></button>
                    </td>
                </tr>
            `;
        })

        let orderStatus = parseInt($('body').find('select[name="order_status"]').val());
        let bg      = orderStatus == 0 
                            ? 'background:orange; color:#fff;' 
                            : (orderStatus == 1 
                                ? 'background:gray; color:#fff;'
                                : 'background:green; color:#fff;'        
                            )
        let table = `
                    <div id="docx" style="margin-top:50px;">
                        <table class="table font-roboto table-bordered table-hover" id="export_table">
                            <thead>
                                <tr style="` + bg +`">
                                    <th class="" style="` + bg +` width:20px;">#</th>
                                    <th class="" style="` + bg +` width:80px;">{$Yii::t('common','Date')}</th>
                                    <th class="" style="` + bg +` ">{$Yii::t('common','Order No.')}</th>
                                    <th class="" style="` + bg +` ">{$Yii::t('common','Delivery Date')}</th>
                                    <th class="" style="` + bg +` ">{$Yii::t('common','Customer')}</th>
                                    <th class="" style="` + bg +` ">{$Yii::t('common','Sale People')}</th>
                                    <th class=""> </th>
                                </tr>
                            </thead>
                            <tbody>` + body + `</tbody>
                        </table>
                    </div>`;
    
        $('.renders').html(table);

       
        
          $('#export_table').DataTable({
                            "paging": false, // true จะ export ได้แค่หน้าแรก
                            'pageLength' : 50,
                            "searching": true,
                        });
        setTimeout(() => {
            let tds = $('#export_table').tableExport({
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
        }, 1000);    
        
    }
    

    $('body').on('click', '.show-order-detail', function(){
        let id = $(this).closest('tr').attr('data-key');
        let no = $(this).closest('tr').attr('data-no');
        let el = $(this);
        showDetail({id:id}, no, el);
    })

    

    $('body').on('click', '.btn-refresh-data', function(){
        let comp            = $('body').find('select[name="company_id"]').val();
        let fdate           = $('body').find('input[name="fdate"]').val();
        let tdate           = $('body').find('input[name="tdate"]').val();
        let order_status    = $('body').find('select[name="order_status"]').val();
        $('#export_table > tbody').html('');
        $('#export_table > caption').remove();
        getSaleorderFromAPI({comp:comp, fdate:fdate, tdate:tdate, order_status:order_status}, res => {
            renderSaleOrder(response => {
                
            });
        })
    })

    const checkCart = () => {
        let itemCart = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        if(itemCart && itemCart.length > 0){
            $('.my-cart').fadeIn();
            $('#count-item-cart').html(itemCart.length);
        }else{
            $('.my-cart').fadeOut();
            $('#count-item-cart').html('');
        }
    }

    $(document).ready(function(){
        let comp            = $('body').find('select[name="company_id"]').val();
        let fdate           = $('body').find('input[name="fdate"]').val();
        let tdate           = $('body').find('input[name="tdate"]').val();
        let order_status    = $('body').find('select[name="order_status"]').val();
        getSaleorderFromAPI({comp:comp, fdate:fdate, tdate:tdate, order_status:order_status}, res => {
            renderSaleOrder(response => {
                
            });
        });

        checkCart();
    })

    $('body').on('click', '.add-to-cart', function(){
       
        let itemCart = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        $('input[name="select_item"]:checked').map((key,el) => {
            itemCart.push({
                id: parseInt($(el).closest('tr').attr('data-key')),
                item: parseInt($(el).closest('tr').attr('data-item')),
                code: $(el).closest('tr').find('td.item-code').text(),
                name: $(el).closest('tr').find('td.item-name').text(),
                qty: parseInt($(el).closest('tr').attr('data-qty')),
                price: parseFloat($(el).closest('tr').attr('data-price')),
                no : $('#modal-order-detail').attr('data-no'),
                source_id: parseInt($(el).closest('tr').attr('data-source_id')),
                sale: parseInt($(el).closest('tr').attr('data-sale')),
                saleCode: $(el).closest('tr').attr('data-saleCode'),
                cust: parseInt($(el).closest('tr').attr('data-cust')),
                measure: parseInt($(el).closest('tr').attr('data-measure'))
            })
        })
         
        localStorage.setItem('item-cart', JSON.stringify(itemCart));
        checkCart();
    });



    $('body').on('click', '.show-my-cart', function(){
        $('#modal-show-my-cart').modal('show');
        let body = ``;
        let itemCart = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        itemCart.map((model, key) => {
            body+= `
                <tr data-key="`+model.item+`">
                    <td> ` +(key+1)+ ` </td>
                    <td> ` +model.no+ ` </td>
                    <td> ` +model.code+ ` </td>
                    <td> ` +model.name+ ` </td>                     
                    <td><input type="text" value="` +model.qty+ `" name="qty" class="form-control text-right update-state" /> </td>
                    <td><input type="text" value="` +model.price+ `" name="price" class="form-control text-right update-state" /> </td>
                    <td class="text-center"><button type="button" class="btn btn-danger-ew remove-form-cart"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
        });

        let table = `
            <div>My Cart</div>
             
            <table class="table table-bordered font-roboto">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{$Yii::t('common','Order')}</th>
                        <th>{$Yii::t('common','No')}</th>                        
                        <th>{$Yii::t('common','Name')}</th>
                        <th class="text-right" style="width:100px;">{$Yii::t('common','Quantity')}</th>
                        <th class="text-right" style="width:100px;">{$Yii::t('common','Price')}</th>
                        <th class="text-center" style="width:50px;">{$Yii::t('common','Delete')}</th>
                    </tr>
                </thead>
                <tbody>
                    `+body+`
                </tbody>
            </table>
        `;

        $('#modal-show-my-cart').find('.modal-body').html(table);
    });

    $('body').on('click','.remove-form-cart', function(){
        let el          = $(this);
        let index       = el.closest('tr').index();
         
        let itemCart    = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
                    
        let update      = itemCart.filter((model,key) => (key !== index ? model : null));

            localStorage.setItem('item-cart', JSON.stringify(update));
            checkCart();

            el.closest('tr').fadeOut('slow');
            setTimeout(() => {
                el.closest('tr').remove();
            }, 500);
    })


    $('body').on('click', '#export_list > tbody > tr', function(){
        
        if($(this).find('input[name="select_item"]').is(':checked')){
            $(this).find('input[name="select_item"]').prop('checked', false);
            $(this).closest('tr').attr('style','');
        }else{
            $(this).find('input[name="select_item"]').prop('checked', true);
            if($(this).find('input[name="select_item"]').is(':checked')){
                $(this).closest('tr').attr('style','background:#a9ffc4;');
            }
            
        }
       
    });

    const createSaleInvoie  = (obj, callback) => {
        
    
        fetch("?r=SaleOrders/saleorder/create-invoice-from-item", {
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
                callback(res);
            }else{
                swal('{$Yii::t("common","Warning")} : ' + res.suggestion, res.count, "warning");
                // remove loading
                loading('Hide');                    
            }
            
        })
        .catch(error => {
            console.log(error);
        });
        
    }

    $('body').on('click','.confirm-create-bill',function(){
        
        $('#modal-confirm-create').modal('show');
        $('#modal-confirm-create').find('.modal-header').removeClass('bg-green').addClass('bg-yellow');
        $('#modal-confirm-create').find('.modal-footer').removeClass('bg-success').addClass('bg-warning');
        $('#modal-confirm-create').find('.click-create-invoice').show();
        $('#modal-confirm-create').find('.click-create-sale-order').hide();
        $('#modal-confirm-create').find('input#inv-no').closest('div.row').show();

        let items       = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        let model       = items[0];
        $('#modal-confirm-create').find('input[name="sale_id"]').val(model.sale);
        $('#modal-confirm-create').find('input[name="sale_code"]').val(model.saleCode);
        loading('Hide');
    });


    const loading = (action) => {
        if(action=='Show'){
            $('.loading').show();
            $('.click-create-invoice').attr('disabled', true);
        }else{
            $('.loading').hide();
            $('.click-create-invoice').attr('disabled', false);
        }
    }
    

    $('body').on('click','.click-create-invoice', function(){
        //var inv = prompt("{$Yii::t('common','Tax Invoice No.')}", "{$IV_NO}");
        var inv         = $('body').find('input[name="no"]').val();
        let items       = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        let order_date  = $('body').find('input[name="order_date"]').val();
        let custId      = $('body').find('select[name="customer_id"]').val();
        let vat         = $('body').find('select[name="vat_percent"]').val();
        let remark      = $('body').find('textarea[name="remark"]').val();
        let saleId      = $('body').find('input[name="sale_id"]').val();
        
        if (inv != null) {
            if(items && items.length > 0){
                // show loading
                loading('Show');

                // Remove Order
                
                createSaleInvoie({items:items, no:inv, order_date:order_date, custId:custId, vat:vat, remark:remark, saleId:saleId}, res => {
                    // remove loading
                    loading('Hide');
                    
                    if(res.status==200){

                        $.notify({
                            // options
                            icon: "fas fa-shopping-basket",
                            message: res.message
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });  

                        setTimeout(() => {
                            if(confirm("{$Yii::t('common','Clear Data')} ?")){
                            
                                
                                localStorage.removeItem("item-cart");
                                checkCart();
                                window.open("index.php?r=accounting%2Fposted%2Fposted-invoice&id="+btoa(res.inv));
                                $('.modal').modal('hide');
                            }else{
                                window.open("index.php?r=accounting%2Fposted%2Fposted-invoice&id="+btoa(res.inv));
                                checkCart();
                                $('.modal').modal('hide');
                            }
                        }, 800);
                        
                        
                    }else{
                        
                    }
                })
            }
        }
    })


    $('body').on('keyup', '.update-state', function(){
        let index       = $(this).closest('tr').index();
        let qty         = $(this).closest('tr').find('input[name="qty"]').val();
        let price       = $(this).closest('tr').find('input[name="price"]').val();
        let items       = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        let update      = items.map((model, key) => {
                            return index === key ? Object.assign({}, model, { qty:qty, price: price }) : model;
                        });

                        localStorage.setItem('item-cart', JSON.stringify(update));
    })


    const hiddenOrder  = (obj, callback) => {
        
    
        fetch("?r=SaleOrders/saleorder/hide-order", {
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
                callback(res);
            }else{
                swal('{$Yii::t("common","Warning")} : ' + res.suggestion, res.count, "warning");
            }
            
        })
        .catch(error => {
            console.log(error);
        });
        
    }

    $('body').on('click', '.hide-order', function(){
        // 0 = show 1=hide
        let id  = $(this).closest('tr').attr('data-key');
        let set = parseInt($(this).attr('data-set'));
        let el  = $(this);
        if(confirm("{$Yii::t('common','Confirm')} ?")){
            el.closest('tr').fadeOut();
            hiddenOrder({id:id,set:set == 0 ? 1 : 0}, res => {
                
                if(res.status == 200){
                    setTimeout(() => {
                        el.closest('tr').remove();
                    }, 500);
                    
                }else{
                    el.closest('tr').fadeIn();
                }
            });
        }
        
    });

    
    const createOrder  = (obj, callback) => {
        
    
        fetch("?r=SaleOrders/saleorder/create-order-ajax", {
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
                callback(res);
            }else{
                swal('{$Yii::t("common","Warning")} : ' + res.suggestion, res.count, "warning");
            }
            
        })
        .catch(error => {
            console.log(error);
        });
        
    }

    $('body').on('click', '.confirm-create-sale-order', function(){
        $('#modal-confirm-create').modal('show');
        $('#modal-confirm-create').find('.modal-header').removeClass('bg-yellow').addClass('bg-green');
        $('#modal-confirm-create').find('.modal-footer').removeClass('bg-warning').addClass('bg-success');
        $('#modal-confirm-create').find('.click-create-invoice').hide();
        $('#modal-confirm-create').find('.click-create-sale-order').show();
        $('#modal-confirm-create').find('input#inv-no').closest('div.row').hide();

        let items       = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        let model       = items[0];
        $('#modal-confirm-create').find('input[name="sale_id"]').val(model.sale);
        $('#modal-confirm-create').find('input[name="sale_code"]').val(model.saleCode);
    });



    $('body').on('click', '.click-create-sale-order', function(){
 
        var inv         = $('body').find('input[name="no"]').val();
        let items       = localStorage.getItem('item-cart') ? JSON.parse(localStorage.getItem('item-cart')) : [];
        let order_date  = $('body').find('input[name="order_date"]').val();
        let custId      = $('body').find('select[name="customer_id"]').val();
        let vat         = $('body').find('select[name="vat_percent"]').val();
        let remark      = $('body').find('textarea[name="remark"]').val();
        let saleId      = $('body').find('input[name="sale_id"]').val();
        
        if (inv != null) {
            if(items && items.length > 0){
                loading('Show');
                createOrder({items:items, no:inv, order_date:order_date, custId:custId, vat:vat, remark:remark, saleId:saleId}, res =>{
                    // remove loading
                    loading('Hide');
                    
                    if(res.status==200){

                        $.notify({
                            // options
                            icon: "fas fa-shopping-basket",
                            message: res.message
                        },{
                            // settings
                            placement: {
                            from: "top",
                            align: "center"
                            },
                            type: "success",
                            delay: 3000,
                            z_index: 3000
                        });  
                    
                        setTimeout(() => {
                            if(confirm("{$Yii::t('common','Clear Data')} ?")){
                            
                                
                                localStorage.removeItem("item-cart");
                                checkCart();
                               
                                $('.modal').modal('hide');
                            }else{
                                //window.open("?r=SaleOrders/saleorder/view&id="+res.header.id);
                                checkCart();
                                $('.modal').modal('hide');
                            }

                            window.open("?r=SaleOrders/saleorder/index&SaleListSearch[no]="+res.header.no);
                        }, 800);
                        

                    }

                });
            }
        }
    }); 

    $('#modal-order-detail').on('hidden.bs.modal',function(){   
        checkCart();
        $('body').attr('style','');
    });

    $('#modal-show-my-cart').on('show.bs.modal',function(){   
        $('.my-cart').hide();
        $('.click-create-invoice').attr('disabled', false);
    });

    $('#modal-show-my-cart').on('hidden.bs.modal',function(){   
        $('.my-cart').show();
        loading('Hide');
    });

    
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
