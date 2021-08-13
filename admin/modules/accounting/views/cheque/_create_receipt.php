<?php 

use yii\helpers\Html;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
$Yii = 'Yii';
$now = date('Y-m-d');
?>
<style>

.fixed-receipt{
    border:1px solid #ccc; 
    padding:5px; 
    position: fixed;
    top: 110px;
    right: 20px;
    min-width: 350px;
}

.total-receipt{
    border:1px solid #ccc; 
    padding:5px; 
    color:green; 
    text-align: right; 
    font-size:40px; 
    position: fixed;
    bottom: 50px;
    right: 32px;
    min-width: 200px;
    z-index:1020;
    background: #fff;
}


@media (min-width: 1024px) {
        
    .fixed-receipt{
        border:1px solid #ccc; 
        padding:5px; 
        position: fixed;
        top: 110px;
        right: 20px;
        min-width: 300px;
    }
}

@media (min-width: 992px) {
    .full-height .col-xs-8 {
        overflow: hidden;
    }

    .full-height .col-xs-8 {
        padding-bottom: 100%;
        margin-bottom: -100%;
    }
}

@media (max-width: 426px) {
    .total-receipt{
        border: 0px;
        color: green;
        text-align: right;
        font-size: 26px;
        position: fixed;
        bottom: -2px;
        right: 85px;
        min-width: 200px;
        z-index: 1020;
    }
}
</style>


<div class="modal fade modal-full" id="modal-receipt">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#37a6da;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title pull-left"> </h4>
                <div class="ID" style="margin-left: 52px; margin-top: 3px;"></div>
                <div class="pull-right" style="position:absolute; top: 5px; right: 45px;">
                    <input type="text" name="no" class="form-control" id="receipt-no" style="width: 250px;" placeholder="<?=Yii::t('common','No')?>" /> 
                </div>
            </div>
            <div class="modal-body" style="margin-top: -5px;">
                <div class="row">
                    <div class="col-xs-12" style="border-bottom:1px solid #ccc;">
                        <div class="row" style="background:#f1f1f1; padding-bottom: 10px;">
                            <div class="col-xs-8 ">
                               <div class="mr-5 mt-5 text-info"> <i class="fas fa-info-circle text-yellow"></i> เพิ่มรายการใบกำกับภาษี / ใบลดหนี้ / รายการอื่นๆที่ต้องหัก ลงในช่อง(เลขที่บิล / เลขที่ใบลดหนี้)</div>
                            </div>
                            <div class="col-xs-4 ">
                                                          
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row full-height" style="margin-bottom:50px;">
                    <div class="col-sm-8" >
                        <div class="mt-10">
                            
                            <div class="render-receipt"></div>
                        </div>
                    </div>
                    <div class="col-sm-4" style="background:#f1f1f1; padding-bottom:50px;" >
                        <div class="mt-10">

                            <div class="row">
                                <div class="col-xs-4" >  
                                    <label for="cheque-posting_date">รับด้วย</label> 
                                    <div class="fixed-receipt-x">
                                        <?= Html::dropDownList('bank-list',[ ], [
                                                                    'Cash' => Yii::t('common','Cash'),
                                                                    'Cheque' => Yii::t('common','Cheque'),
                                                                    'ATM' => Yii::t('common','Transfer money').' ('.Yii::t('common','ATM').')',
                                                                    ],['class' => 'form-control cash-receipt', 'disabled' => false]);

                                        ?> 
                                    </div>                             
                                    <div class="total-receipt" >00.00</div>
                                </div>
                                <div class="col-xs-8">   
                                    <label for="cheque-posting_date">ธนาคาร</label>                             
                                    <img src="uploads/BangkokBank.png" id="img-bank" height="50px" 
                                    style="margin: 10px; position: absolute; right: 35px; height: 22px; top: 22px;">
                                    <?= Html::dropDownList('select-bank','',
                                        ArrayHelper::map(\common\models\BankList::find()
                                        ->orderBy(['name' => SORT_ASC])
                                        ->all(),'id','name'),['class' => 'form-control select-bank' , 'disabled' => false])
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-10">
                                <div class="col-sm-4">
                                    <label for="cheque-posting_date">วันที่รับชำระ</label>
                                    <?=DatePicker::widget([
                                                'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                                'name'      => 'posting_date',
                                                'options'   => ['id'    => 'cheque-posting_date' , 'disabled' => false],                                            
                                                'value'     => date('Y-m-d'),  
                                                'removeButton' => false,     
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd'
                                                ]                                            
                                        ]);
                                    ?>
                                </div>
                                <div class="col-sm-8">
                                    <label for="cheque-bank_id">เลขที่เช็ค</label>
                                    <input type="text" name="cheque-bank_id" class="form-control" placeholder="000-0-00000-0"/>
                                </div>
                            </div>

                            <div class="row " style="margin-top:20px;">
                                <div class="col-sm-12" style="margin-top:30px; ">
                                    <div class="text-green">เข้าบัญชี <i class="fas fa-level-down-alt"></i></div>
                                    <div class="bank-name text-red hidden" style="padding: 10px; background: #fff;"></div>
                                    <?= Html::dropDownList('transfer-to', null,
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
                                                'style' => 'background: #dbfbff;',
                                                //'prompt' => Yii::t('common','All'),
                                                'options' => [                        
                                                   // @$_GET['transfer-to'] => ['selected' => 'selected']
                                                ],
                                            ]                                     
                                        ) 
                                    ?> <i class="far fa-hand-point-left text-yellow fa-2x show-hand blink" style="display:none;"></i>    
                                </div>
                                <div class="col-sm-4">
                                <label for="cheque-posting_date">กำหนดวันที่เงินเข้า</label>
                                <?= Html::dropDownList('cheque-know_date','',
                                    [
                                        '1' => Yii::t('common','Define date'),
                                        '0' => Yii::t('common','Not sure')
                                    ],['class' => 'form-control know-date' , 'disabled' => false]) ?>

                                </div>
                                <div class="col-xs-8">
                                    <label for="cheque-posting_date">วันที่ เงินเข้า</label>
                                    <?=DatePicker::widget([
                                                'type'      => DatePicker::TYPE_COMPONENT_APPEND,
                                                'name'      => 'post_date_cheque',
                                                'options'   => ['id'    => 'post_date_cheque' , 'disabled' => false],                                            
                                                'value'     => date('Y-m-d'),  
                                                'removeButton' => false,     
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'yyyy-mm-dd'
                                                ]                                            
                                        ]);
                                    ?>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-xs-12">
                                    <label><?=Yii::t('common','Remark')?></label>
                                    <textarea id="cheque-remark" class="form-control" name="remark" aria-invalid="false" ></textarea>
                                </div>
                                <div class="col-xs-12 " style="margin-top:50px;">
                                    <div ><?=Yii::t('common','Create By')?> : [<span id="user_id"></span>] <span id="user_name"></span></div>          
                                    <div ><?=Yii::t('common','Update By')?> : [<span id="update_user_id"></span>] <span id="update_user_name"></span></div>                                
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background:#54c8ff;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                
                <button type="button" class="btn btn-success btn-save-cheque" data-action="save"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
                <button type="button" class="btn btn-default btn-save-cheque" data-action="save-close" style="margin-right:50px;"><i class="fa fa-save"></i> <?=Yii::t('common','Save & Close')?></button>
                <a href="#" target="_blank" class="btn btn-info print-cheque"><i class="fa fa-print"></i> <?=Yii::t('common','Print')?></a>
            </div>
        </div>
    </div>
</div>

 
<div class="modal fade" id="modal-select-bank">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fas fa-university"></i> <?=Yii::t('common','Bank')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php
$today = date('Y-m-d');

$js =<<<JS

    let state = { 
            bank : null,
            header: {
                id: null
            }
        };

    $(document).ready(function(){  
       
    });

    const changeProirity = () => {
        let raws = [];
        $('body').find('table#drag_table input[name="priority"]').each((key,el) => {
            raws.push({
                id: $(el).closest('tr').attr('data-key'),
                priority: $(el).val()
            })
        });

        fetch("?r=accounting/cheque/change-priority", {
            method: "POST",
            body: JSON.stringify({raws:raws}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {   
           
            res.raws.map(model => {
                $('body').find('table#drag_table tr[data-key="'+model.id+'"]').find('input[name="priority"]').val(model.priority)
            });
            
        })
        .catch(error => {
            console.log(error);
        });
    }

    const getReceiptLine = (obj, callback) => {
        localStorage.removeItem("receipt");

        fetch("?r=accounting/cheque/get-line", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {   
           
            $('#modal-receipt').attr('data-source',obj.id);
            $('body').find('.ID').html((obj.id > 0 ? obj.id : ''));      
            $('#modal-receipt').find('input[name="no"]').val(obj.no);
            $('body').find('input.add-new-invoice-to-receipt-line').focus();  
            $('body').find('select[name="bank-list"]').val(res.header.type);     
            $('body').find('input[name="cheque-bank_id"]').val(res.header.bank_id);    
            $('body').find('input#cheque-posting_date').val(res.header.posting_date); 
            $('body').find('select[name="transfer-to"]').val(res.header.tranferId);
            $('body').find('select[name="select-bank"]').val(res.header.bankId);
            $('body').find('img#img-bank').attr('src',res.header.bankImg);
            $('body').find('select[name="cheque-know_date"]').val(res.header.know_date);
            $('body').find('input#post_date_cheque').val(res.header.post_date_cheque);
            $('body').find('textarea[name="remark"]').val(res.header.remark);

            $('body').find('#user_id').html(res.header.owner);
            $('body').find('#user_name').html(res.header.owner_name);

            $('body').find('#update_user_id').html(res.header.update_by_id);
            $('body').find('#update_user_name').html(res.header.update_by_name);
            
            callback(res);
        })
        .catch(error => {
            console.log(error);
        });
    }


    const renderTableReceipt = () =>{
        
        let obj         = localStorage.getItem("receipt")
                            ? JSON.parse(localStorage.getItem("receipt"))
                            : [];
        let body        = '';

        let roundDigit  = parseInt($('body').find('#round-digit').val());
        let decimal     = roundDigit == 1000 ? 3 : 2;

        function compare( a, b ) {
            if ( a.priority < b.priority ){
                return -1;
            }
            return 0;
        }

        obj.sort( compare ); // ไม่ต้องเรียงตาม priority
        
        
        let totalBalance = 0;
        obj.map((model, key) => {
            let balance     = Math.round( model.balance * roundDigit )/roundDigit;
            let invTotal    = Math.round( model.invTotal * roundDigit )/roundDigit;
                totalBalance+= balance ;
            
            let iv          = model.invId;
            let url         = `<a href="?r=accounting/posted/posted-print&id=`+btoa(iv)+`&no=`+model.inv+`" target="_blank">`+model.inv+`</a>`;
             
            body+= `
                <tr id="`+key+`" key="`+key+`" data-key="`+model.id+`" data-source="`+model.source_id+`" class="`+ (model.id==0 ? 'bg-warning' : '') +` " data-new="`+model.rand+`">
                    <td class="text-center">`+(key+1)+`</td>
                    <td>`+model.posting_date+`</td>
                    <td>`+(iv > 0 ? url : model.inv)+`</a></td>
                    <td class="text-right">`+number_format(invTotal.toFixed(decimal))+`</td>
                    <td class="text-right ` + (balance < 0 ? 'text-red' : 'text-green') + `">`+number_format(balance.toFixed(decimal))+`</td>
                    <td class="text-center">
                     <button class="btn btn-flat btn-sm btn-danger-ew btn-delete-recepit "><i class="far fa-trash-alt"></i> </button>
                     <input type="number" class="hidden" name="priority" value="`+model.priority+`"/>
                    </td>
                </tr>
            `;
        })

        let table = `
            <table class="table font-roboto table-bordered table-hover" id="drag_table">
                <thead>
                    <tr class="bg-gray">
                        <th style="width: 20px;">#</th>
                        <th style="width: 100px;">วันที่</th>
                        <th>เลขที่</th>
                        <th class="text-right" style="width: 150px;"><i class="pull-left fa fa-refresh load-calculate pointer" style="font-size: 17px;"></i> ยอดตามบิล</th>     
                        <th class="text-right" style="width: 150px;">ยอดที่รับเงิน</th>     
                        <th style="width: 30px;" class="text-center"></th>                                       
                    </tr>
                </thead>
                <tbody>
                    `+body+`
                </tbody>
                <tfoot>
                    <tr>
                        <th> </th>
                        <th class="text-center"> </th>
                        <th><input type="text" class="form-control add-new-invoice-to-receipt-line" placeholder="เลขที่บิล / เลขที่ใบลดหนี้" /></th>
                        <th><input type="text" class="form-control text-right" readonly name="inv-total" /> </th> 
                        <th><input type="number" class="form-control text-right" name="balance" /></th>        
                        <th> </th>                 
                    </tr>
                </tfoot>
            </table>
        `;

        $('body').find('.render-receipt').html(table);
        $('.loading').hide();
        $('body').find("#drag_table").tableDnD({
            onDrop: function(table, row) {
                
                changeProirity();
            }
            // onDragClass: "myDragClass",
            // onDrop: function(table, row) {
            //     var rows = table.tBodies[0].rows;
            //     var debugStr = "Row dropped was "+row.id+". New order: ";
            //     for (var i=0; i<rows.length; i++) {
            //         debugStr += rows[i].id+" ";
            //     }
            //     $('#debugArea').html(debugStr);
            // },
            // onDragStart: function(table, row) {
            //     $('#debugArea').html("Started dragging row "+row.id);
            // }
        });

        $("#drag_table").tableExport({
            headings: true,                     // (Boolean), display table headings (th/td elements) in the <thead>
            footers: true,                      // (Boolean), display table footers (th/td elements) in the <tfoot>
            formats: ["xlsx"],                  // (String[]), filetypes for the export ["xls", "csv", "txt"]
            fileName: "{$this->title}",         // (id, String), filename for the downloaded file
            bootstrap: true,                    // (Boolean), style buttons using bootstrap
            position: "bottom" ,            	// (top, bottom), position of the caption element relative to table
            ignoreRows: null,     			// (Number, Number[]), row indices to exclude from the exported file
            ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file
            ignoreCSS: ".tableexport-ignore",   // (selector, selector[]), selector(s) to exclude from the exported file         
            footers: false 
        });
        $('body').find('.total-receipt').attr('data-val',totalBalance).html(number_format(totalBalance.toFixed(decimal)));
        setTimeout(() => {
            $('body').find('input.add-new-invoice-to-receipt-line').focus();
        }, 500);
        
    }

    $('body').on('click', '.btn-create', function(){

        let bank        = $('body').find('select[name="transfer-to"]').val();
        let bankName    = $('body').find('select[name="transfer-to"] :selected').text();

        $('#modal-receipt').modal('show');
        // Select Bank 
        /*if(bank <= 0){
           
            //alert('{$Yii::t("common","Please select bank first")}');
            
            
            //$('body').find('select[name="bank-list"]').simulate('mousedown');
            setTimeout(() => {
                $('.show-hand').show();
                $('body').find('select[name="transfer-to"]').focus();
            }, 500);
           
        }else{*/
            $('.show-hand').hide();
            $('#modal-receipt').modal('show');
            let el = $(this).closest('tr');
            let id = 0;
            let no = el.find('input[name="no"]').val();
            

            //$('#modal-receipt').find('.btn-save-cheque').show(); 
            $('#modal-receipt').find('.bank-name').html(bankName);
            
            

            getReceiptLine({id:id, no:no}, res => {    
                $('body').find('.ID').html((id > 0 ? id : ''));         
                $('#modal-receipt .modal-title').html('{$Yii::t("common","Create")}');       
                // $('body').find('input#post_date_cheque').val("{$today}");
                
                state.header = res.header;
                 

                localStorage.setItem("receipt", JSON.stringify(res.raws));
                renderTableReceipt();            
            });
       /* }*/
    });


    $('body').on('click', '.btn-modify-receipt', function(){
        $('#modal-receipt').modal('show');
        let el = $(this).closest('tr');
        let id = el.attr('data-source');
        let no = el.find('input[name="no"]').val();
        $('body').find('.render-receipt').html('<i class="fa fa-refresh fa-spin text-center"></i>');
        $('body').find('.total-receipt').html('<i class="fa fa-refresh fa-spin text-center"></i>');
        $('body').find('.bank-name').html('<i class="fa fa-refresh fa-spin text-center"></i>');
        //$('#modal-receipt').find('.btn-save-cheque').hide(); 
        setTimeout(() => {
            
            getReceiptLine({id:id, no:no}, res => {             
                $('#modal-receipt .modal-title').html('{$Yii::t("common","Edit")}');   
                $('body').find('.ID').html((id > 0 ? id : ''));  
                
                state.header = res.header;
                setTimeout(() => {
                    $('.bank-name').html(res.header.tranferName);   
                }, 1000);

                //console.log(state);
                
                localStorage.setItem("receipt", JSON.stringify(res.raws));
                renderTableReceipt();            
            });
        }, 500);

    });

    const createNewLine = (obj, callback) =>{
        // After Create 
        // Update id in tr[data-key]

        fetch("?r=accounting/cheque/create-line", {
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

    const addNewRow = (obj) =>{
        let receipt     = localStorage.getItem("receipt")
                            ? JSON.parse(localStorage.getItem("receipt"))
                            : [];

            receipt.push(obj);
            localStorage.setItem("receipt", JSON.stringify(receipt));
            

            renderTableReceipt();

            //try to create line hare !!
            createNewLine(obj, res =>{
                if(res.status==200){
                    $('body').find('table#drag_table tr[data-new="'+res.rand+'"]').attr('data-key', res.id).removeClass("bg-warning");
                    $('body').find('table#drag_table tr[data-new="'+res.rand+'"]').attr('data-source', res.source_id).removeClass("bg-warning");
                    $('body').find('#modal-receipt').attr('data-source', res.source_id);
                    $('body').find('#modal-receipt').find('div.ID').html(res.source_id);
                    //$('body').find('.ID').html((obj.source_id > 0 ? obj.source_id : '')); 
                    let update  = receipt.map((model, key) => {
                                        return model.rand === res.rand ? Object.assign({}, model, { id: res.id }) : model;
                                    });
                        localStorage.setItem("receipt", JSON.stringify(update));
                }else{
                    $('body').find('table#drag_table tr[data-new="'+res.rand+'"]').attr('data-key', res.id).removeClass("bg-danger");


                    setTimeout(() => {
                        $('body').find('table#drag_table tr[data-new="'+res.rand+'"]').remove();
                    }, 1500);

                    $.notify({
                    // options
                    icon: "fas fa-box-open",
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
            });
    }

    const checkExists = (obj, callback) =>{
        fetch("?r=accounting/posted/no-exists", {
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

    const deleteRow = (obj, callback) =>{
        let el = $('body').find('tr[data-key="'+obj.id+'"]');
        if(confirm("Delete ?")){

            el.hide('slow');
            setTimeout(() => {                
                el.remove();
            }, 1000);

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

    $('body').on('keydown', 'input.add-new-invoice-to-receipt-line', function(e){

        let el      = $(this);
        let val     = $(this).val().trim();
        let balance = $(this).closest('tr').find('input[name="balance"]').val();
        
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 || keyCode === 9) {
            e.preventDefault();

            if(val !== ''){
                checkExists({no:val}, res =>{

                    let sourceId        = 0;
                    let no              = val;
                    let returnBalance   = balance;

                    if(res.status==200){     
                        sourceId        = res.data.id;
                        no              = res.data.no;
                        returnBalance   = res.data.balance;
                    }

                    el.closest('tr').attr('data-apply', sourceId);
                    el.val(no);     
                    $('body').find('input[name="inv-total"]').val(returnBalance);        
                    $('body').find('input[name="balance"]').focus().val(returnBalance);                    
                });
            }

            return false;
        }
    })

    $('body').on('keyup', 'input[name="balance"]', function(e){
        let val         = $(this).closest('tr').find('input.add-new-invoice-to-receipt-line').val();
        let balance     = $(this).closest('tr').find('input[name="balance"]').val();
        let cheque_date = $('body').find('input#cheque-posting_date').val();
        let source      = $('body').find('#modal-receipt').attr('data-source');
        let randomId    = 1 + Math.random() * 6;
        let bankType    = $('body').find('select[name="bank-list"]').val();
        let bankList    = $('body').find('select[name="select-bank"]').val();
        let bank_id     = $('body').find('input[name="cheque-bank_id"]').val();
        let tranfer_to  = $('body').find('select[name="transfer-to"]').val();
        let knowDate    = $('body').find('select[name="cheque-know_date"]').val();
        let pdc         = $('body').find('input[name="post_date_cheque"]').val();
        let remark      = $('body').find('textarea[name="remark"]').val();

        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
                if(balance == 0){
                    alert('ต้องใส่ราคาก่อน');
                }else{

                
                    addNewRow({
                        balance: parseFloat(balance),
                        bank: source <= 0 ? bankList : 0, // ถ้าไม่ได้เลือกธนาคาร
                        cheque_date: cheque_date,
                        id: '',
                        inv: val,
                        no: $('body').find('input[id="receipt-no"]').val(),
                        posting_date: cheque_date,
                        remark: "",
                        source_id: source,
                        invTotal: $('body').find('input[name="inv-total"]').val(),
                        rand: randomId,
                        type: bankType,
                        bank_id: bank_id,
                        tranfer_to:tranfer_to,
                        knowDate:knowDate,
                        pdc: pdc,
                        remark: remark
                    });

                    $('body').find('input.add-new-invoice-to-receipt-line').focus();    
                }      
        }         
    })

    $('body').on('click', '.btn-delete-recepit', function(){
        let el = $(this).closest('tr');
        let id = $(this).closest('tr').attr('data-key');

        if(id > 0){
            
            
            deleteRow({id:id}, res=>{
                if(res.status==200){
                    
                    let newData     = [];
                    let receipt     = localStorage.getItem("receipt")
                                    ? JSON.parse(localStorage.getItem("receipt"))
                                    : [];

                        newData     = receipt.filter(model => model.id != id ? model : null);

                    localStorage.setItem("receipt", JSON.stringify(newData));
                    

                    renderTableReceipt();

                }
            })
            
        }
    });

    $('body').on('click', 'a.print-cheque', function(){
        let id  = $('body').find('#modal-receipt').attr('data-source');
        let url = `?r=accounting/cheque/print&id=`+id;

        $(this).attr('href', url);
    });


    $('body').on('click', '.load-calculate', function(){
        let el = $(this);
        let id = $('body').find('#modal-receipt').attr('data-source');
        el.addClass('fa-spin');
        fetch("?r=accounting/cheque/calculate-row", {
            method: "POST",
            body: JSON.stringify({id:id}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
            localStorage.setItem("receipt", JSON.stringify(res.raws));
            renderTableReceipt();
            
            setTimeout(() => {
                el.removeClass('fa-spin');
            }, 1000);
            
        })
        .catch(error => {
            console.log(error);
        });
    });


    $('body').on('change', '.select-bank', function(){
        let bank = $(this).val();
        if(bank==0){
            $('.bank-row').slideUp('slow');
            $('#cheque-type').val('Cash');
        }

        $.ajax({
            url: "index.php?r=accounting/bank-list/ajax-view",
            data: {id:bank},
            success: function(getData){
                var obj = jQuery.parseJSON(getData);
                $('img#img-bank').attr('src','uploads/'+obj.img);

            }
        })
        //$('#modal-select-bank').modal('show');
    });
    

    const saveHeader = () =>{
       
        let cheque_date = $('body').find('input[name="posting_date"]').val();
        let source      = $('body').find('#modal-receipt').attr('data-source');
        let bankType    = $('body').find('select[name="bank-list"]').val();
        let bankList    = $('body').find('select[name="select-bank"]').val();
        let bank_id     = $('body').find('input[name="cheque-bank_id"]').val();
        
        let tranfer_to  = $('body').find('select[name="transfer-to"]').val();
        let know_date   = $('body').find('select[name="cheque-know_date"]').val();
        let pdc         = $('body').find('input[name="post_date_cheque"]').val();
        let remark      = $('body').find('textarea[name="remark"]').val();

        let obj = {
            cheque_date: cheque_date,
            source:source,
            bankType:bankType,
            bankList:bankList,
            bank_id:bank_id,
            tranfer_to:tranfer_to,
            know_date:know_date,
            pdc:pdc,
            remark:remark
        };

        fetch("?r=accounting/cheque/update-header", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(res => {          
             
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.btn-save-cheque', function(){
        let actions = $(this).attr('data-action');
        if(actions == 'save-close'){
            setTimeout(() => {
                $("#modal-receipt").modal('hide');
            }, 500);           
            saveHeader();
        }else{
            saveHeader();
        }
    })

    $('body').on('change','select[name="transfer-to"]', function(){
        if($(this).val() != null){
            $('.show-hand').hide();
        }
    });

    $('body').on('click', '.select-customer', function(){
        $('#modal-select-customer').modal('show');
    });

    $('#modal-receipt').on('show.bs.modal', function() {        
        
        
    });   

    $('#modal-receipt').on('hidden.bs.modal', function() {
      $('.bank-name').html('');
      $('#modal-receipt input').val('');
      $('#modal-receipt textarea').val('');
      $('#modal-receipt select').val(null);
      //$('#modal-receipt select[name="transfer-to"]').val(11);
      //$('select[name="bank-list"]').val(null);
    });    
JS;

$this->registerJs($js,Yii\web\View::POS_END);
?>