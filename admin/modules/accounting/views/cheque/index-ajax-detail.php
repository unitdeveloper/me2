<?php
 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use kartik\widgets\DatePicker;
use common\models\Customer;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$Yii = 'Yii';
 
$this->title = Yii::t('common','Receiving money');
?>
<div class="loading" style="position:fixed; top:40%; left:50%; z-index: 10;"><i class="fa fa-refresh fa-spin fa-3x"></i></div>
<div class="row filter"  ng-init="Title='<?=$this->title;?>'" >
    <div class="col-xs-4">
        <?php

        $startDate  = date('Y-m-').'01';
        $endDate    = date('Y-m-d');

        

        $FromDate   = Yii::t('common','From Date');
        $ToDate     = Yii::t('common','To Date');
// With Range
$layout = <<< HTML
    <span class="input-group-addon">$FromDate</span>
    {input1}
    {separator} 
    <span class="input-group-addon">$ToDate</span>
    {input2}
    <span class="input-group-addon kv-date-remove">
    <i class="glyphicon glyphicon-remove"></i>
    </span>
HTML;

    echo DatePicker::widget([
        'type'      => DatePicker::TYPE_RANGE,
        'name'      => 'fdate',
        'value'     => Yii::$app->request->get('fdate') ? Yii::$app->request->get('fdate') : date('Y-m-').'01',
        'name2'     => 'tdate',
        'value2'    => Yii::$app->request->get('tdate') ? Yii::$app->request->get('tdate') : date('Y-m-t'),
        'separator' => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
        'layout'    => $layout,
        'options'   => [ 'autocomplete' => 'off' ],
        'options2'  => [ 'autocomplete' => 'off' ],
        'pluginOptions' => [
            'autoclose' => true,
            'format'    => 'yyyy-mm-dd'
        ],
        'pluginEvents' => [
            "hide" => "function(e) { 
                //$('body').find('.totals').html('xxx'); 
            }",
        ],
    ]);

?>
    
    </div>  
    <div class="col-xs-6">

        <div class="input-group pull-left" >           
            <?= Html::dropDownList('bank-list', null,
                ArrayHelper::map(
                    \common\models\BankAccount::find()                    
                    ->where(['comp_id' => Yii::$app->session->get('Rules')['comp_id']])
                    ->orderBy(['name' => SORT_ASC])
                    ->all(),
                            'id',function($model){
                                return $model->name .' ' .$model->branch .' [ '.$model->bank_no.' ]';
                            }
                        ),
                    [
                        'class'=>'form-control',
                        'prompt' => Yii::t('common','All'),
                        'options' => [                        
                            @$_GET['bank-list'] => ['selected' => 'selected']
                        ],
                    ]                                     
                ) 
            ?>          
        </div>

        
    </div> 

    <div class="col-xs-2  ">  
        <button class="btn btn-info-ew btn-create pull-right hidden"><i class="fa fa-plus"></i> <?=Yii::t('common','Create New')?></button>
    </div>
</div>

<div class="row mt-5">
    <div class="col-xs-4">        
    <?php 
                      $keys = 'customers&comp:'.Yii::$app->session->get('Rules')['comp_id'];
                      $customerList = Yii::$app->cache->get($keys);                  
                      if($customerList){
                        $customer = $customerList;
                      }else{
                        $customer = ArrayHelper::map(
                          Customer::find()
                          ->where(['or', 
                            ['id'       => 909], 
                            ['comp_id'  =>  Yii::$app->session->get('Rules')['comp_id']]
                          ])
                          ->andWhere(['status'=>'1'])
                          ->andWhere(['headoffice' => 1])
                          ->orderBy(['code' => SORT_ASC])
                          ->all(),
                                  'id',
                                  function($model){ 
                                    return '['.$model->code.'] '.$model->name; 
                                  }
                          );
                    
                        Yii::$app->cache->set($keys,$customer, 1);
                      }
                    ?>
                    <?= Select2::widget([
                        'name'  => 'customer',
                        'id'    => 'customer',
                        'data'  => $customer,
                        'options' => [
                            'placeholder' => Yii::t('common','Customer'),
                            'multiple' => false,
                            'class' => 'form-control',
                        ],
                        'pluginOptions' => [
                          'allowClear' => true
                        ],
                        'value' => Yii::$app->request->get('customer') ?  Yii::$app->request->get('customer') : ''
                    ]);
                  ?>
    </div>
    <div class="col-xs-6"> 
       
        <select class="form-control  mb-10 " name="branch-filter" >
            <option value="1"><?=Yii::t('common','Head Office')?></option>
            <option value="2"><?=Yii::t('common','Head Office')?> <?=Yii::t('common','And')?> <?=Yii::t('common','Branch')?></option>
        </select> 
        <small class="show-branch" style="display:none;">
            <a href="#"><i class="fas fa-sitemap"></i> <?=Yii::t('common','Show brach')?></a>
        </small>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid box-info" id="branch-list" style="display:none;">
                    <div class="box-header with-border ">
                        <i class="fa fa-text-width"></i> 
                        <h3 class="box-title"> </h3>
                    </div> 
                    <div class="box-body"> </div> 
                </div>
            </div>
        </div>

    </div>
    <div class="col-xs-2 ">
        

        <button class="btn btn-default btn-search ml-5 pull-right"><i class="fa fa-search"></i> <?=Yii::t('common','Search')?></button>
          
        <select id="round-digit" class="form-control pull-right hidden" style="max-width:80px;">
            <option value="100">2 Digit</option>
            <option value="1000">3 Digit</option>
        </select>

        
    </div>  
</div>


<?=$this->render('_create_receipt')?>


<div class="row">
    <div class="col-xs-12" style="margin-top:10px;">
        <div class="table-responsive">
            <div class="table-renders" ></div>
        </div>
    </div>
</div>
<?php


$js =<<<JS

    $(document).ready(function(){  
        $('.loading').hide();
    });


    const renderTable = (obj) => {
        let body    = '';
        let data    = obj.raws;
        
        function compare( a, b ) {
            if ( a.source_id < b.id ){
                return -1;
            }
            return 0;
        }

        function compareDate( a, b ) {
            if ( a.cheque_date < b.cheque_date ){
                return -1;
            }
            return 0;
        }

        data.sort( compare );

        data.map((model, key) => {
            let inv_total   = model.inv_total * 1;
            let receive     = model.balance * 1;
            let invList     = '';
            let newData     = model.inv_list;


            newData.length > 0 
                ? newData.map(e => { invList+= `<div>` + e.no + `</div>`})
                : null;
            body+= `
                <tr data-key="`+model.id+`" data-source="`+model.source_id+`">
                    <td>`+(key+1)+`</td>
                    <td class="text-center">`+model.source_id+`</td>
                    <td>`+model.cheque_date+`</td>
                    <td>`+model.no+`</td>
                    <td>`+invList+`</td>  
                    <td class="text-right ` + (inv_total < 0 ? 'text-red' : 'text-green') + `">`+number_format(inv_total.toFixed(2))+`</td>
                    <td class="text-right ` + (receive < 0 ? 'text-red' : 'text-green') + `">`+number_format(receive.toFixed(2))+`</td>
                    <td>`+model.bankType+`</td> 
                    <td>`+model.bankFrom+`</td>      
                    <td>`+model.bankTo+`</td>                     
                    <td class="">`+model.cust_name+`</td>     
                    <td class="">`+model.remark+`</td>                       
                    <td class="text-right">
                         
                        <a href="?r=accounting%2Fcheque%2Fprint&id=`+model.source_id+`" target="_blank" class="btn btn-info-ew btn-flat btn-sm"><i class="fa fa-print"></i> Print</a>
                        <button class="btn btn-danger-ew btn-delete-receipt-line btn-flat btn-sm" ><i class="far fa-trash-alt"></i></button>
                    </td>
                    <td class="hidden">`+model.owner+`</td>      
                </tr>
            `;
        })

        let table = `
            <table class="table font-roboto table-bordered table-hover" id="export_table">
                <thead>
                    <tr class="bg-gray">
                        <th style="width: 50px;">#</th>
                        <th style="width: 90px;" class="text-center" title="เลขที่ใบรับ">ID</th>
                        <th style="width: 90px;">วันที่</th>
                        <th style="width: 125px;">เลขที่</th>
                        <th style="width: 125px;">บิล</th>
                        <th style="width: 100px;">ยอดบิล</th>
                        <th style="width: 100px;">ยอดรับ</th>
                        <th style="width: 125px;">ประเภท</th>
                        <th style="width: 125px;">From</th>
                        <th style="width: 125px;">To</th>
                        <th>ลูกค้า</th>          
                        <th>หมายเหตุ</th>                     
                        <th style="width: 170px;" class=""></th>
                        <th class="hidden">สร้างโดย</th>
                    </tr>
                </thead>
                <tbody>
                    `+body+`
                </tbody>
            </table>
        `;

        $('body').find('.table-renders').html(table);
        $('.loading').hide();

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

        let tables = $('#export_table').DataTable({
                        "paging": true,
                        "searching": true
                    });
    }

    $('body').on('click','.btn-search', function(){
        let fdate   = $('body').find('input[name="fdate"]').val();
        let tdate   = $('body').find('input[name="tdate"]').val();
        let bank    = $('body').find('select[name="bank-list"]').val();
        let cust    = $('body').find('select#customer').val();

        let custList= [];
        let branch  = $('select[name="branch-filter"]').val();
        let getBom  = 0;

        $('input:checkbox[name="customer[]"]:checked').each(function() {
            custList.push(parseInt($(this).val()));
        });

        $('.loading').show();
        getData({fdate:fdate, tdate:tdate, bank:bank, cust:cust, custList: custList, branch:branch,}, res => {            
            renderTable(res);
        })
    })

    const getData = (obj, callback) => {
        fetch("?r=accounting/cheque/get-data-detail", {
            method: "POST",
            body: JSON.stringify(obj),
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

    const updateField = (obj, callback) => {
        fetch("?r=accounting/cheque/update-field", {
            method: "POST",
            body: JSON.stringify(obj),
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


    const deleteReceiptLine = (obj, callback) =>{

        if(confirm("Delete ?")){
            fetch("?r=accounting/cheque/delete-row", {
                method: "POST",
                body: JSON.stringify(obj),
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
    }

    $('body').on('change', 'input[name="no"]', function(){
        let el      = $(this);
        let id      = $(this).closest('tr').attr('data-source');
        let value   = $(this).val();
        
        if(id==null){
            id = $('body').find('#modal-receipt').attr('data-source');
        }
       
        updateField({id:id, field:'no', value:value}, res =>{
            if(res.status!=200){
                el.val('').attr('placeholder', value);
                el.addClass('text-yellow');
                alert(res.message);
            }else{
                $('body').find('tr[data-source="'+id+'"] input[name="no"]').val(value);
                $('body').find('input.add-new-invoice-to-receipt-line').focus();
                el.addClass('text-green');
            }
        })
    });


    $('body').on('click', '.btn-delete-receipt-line', function(){
        let el = $(this).closest('tr');
        let id = $(this).closest('tr').attr('data-key')
        deleteReceiptLine({id:id}, res =>{
            if(res.status==200){
                el.hide('show');
                setTimeout(() => {
                    el.remove();
                }, 500);
                
            }
        });
    });


    
const renderBranchList = (data, callback) => {
    let body = ``
    data.raws.map((model, keys) => {
        body+= `
            <tr>
                <td>` +(keys + 1) + `</td>
                <td><a href="?r=customers%2Fcustomer%2Fview&id=`+model.id+`" target="_blank">` + model.branch + `</a> </td>
                <td>` + model.name + ` ` + (model.head == 1 ? '<span class="text-yellow"><i class="fas fa-star"></i></span>' : '') + ` </td>
                <td class="text-center"><input type="checkbox" checked  name="customer[]" value="` + model.id + `"/> </td>
            </tr>
        `;
    })

    let table = `
        <table class="table table-bordered">
            <thead>
                <tr class="bg-gray">
                    <th>#</th>
                    <th>{$Yii::t('common','Branch')}</th>
                    <th>{$Yii::t('common','Name')}</th>
                    <th class="check-all-branch">
                        <label for="check-all-branch">
                            <input type="checkbox" id="check-all-branch" checked/> 
                            {$Yii::t('common','All')} 
                        </label>
                    </th>
                </tr>
            </thead>
            <tbody>` + body + `</tbody>
        </table>
    `;

    callback(table);
}
    
const changeBranch = (cust) => {
    fetch("?r=customers%2Fcustomer%2Fbranch-list", {
        method: "POST",
        body: JSON.stringify({cust:cust}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(res => {
                
        if(res.status===403){
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
            $.notify(
                {
                    message: res.calculating.years + ' : ' + parseInt(res.percent) + '%'
                },
                {
                    type: 'info',
                    delay: 10000,
                    z_index:3000
                }
            );            
        }else{
            renderBranchList(res, html => {
                $('#branch-list .box-title').html(res.name);
                $('#branch-list .box-body').html(html);
            })
            
        }
    })
    .catch(error => {
        console.log(error);
    });
}


$('body').on('change', 'select[name="branch-filter"]', function(){
    let val = parseInt($(this).val());
    if(val===1){
        $('.show-branch').hide();
        $('#branch-list').hide();
        $('#branch-list .box-title').html('');
        $('#branch-list .box-body').html('');
    }else{
        $('.show-branch').show();        
        let cust    = $('select[name="customer"]').val();
        changeBranch(cust);
    }
    
});


$('body').on('change', 'select[name="customer"]', function(){
    let cust = $(this).val();
    if(cust!=''){
        changeBranch(cust);
    }
});

$('body').on('click', '.show-branch', function(){
    $('#branch-list').toggle();
});


$('body').on('click', '#check-all-branch', function(){
    $('input:checkbox[name="customer[]"]').not(this).prop('checked', this.checked);
});

 

    
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>

<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php $this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css');?>
<?php $this->registerJsFile('@web/js/js-xlsx-master/xlsx.core.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/Blob.js-master/Blob.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('@web/js/FileSaver.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.3.5/js/tableexport.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); ?>  
  