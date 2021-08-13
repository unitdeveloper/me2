<?php 
    $this->title = Yii::t('common', 'Withholding tax');
?> 
    
<div class="row" ng-init="Title='<?=$this->title?>'">
    <div class="col-sm-6 col-xs-12">
        <div class="print-tw-modify mt-5" style="position:absolute; z-index: 5; top:32px; height:1300px; width:100%; display: none; color: #fff; background: rgb(82 86 89);">
            <?=$this->render('_form')?>        
        </div>
    </div>
    <div class="col-sm-6 col-xs-12"><div class="print-tw text-center mt-5" style="position:absolute; z-index: 5; top:32px; height:1300px; width:100%; display: none; background: rgb(82 86 89);"></div></div>
</div>
<div class="row" style="min-height:1350px;">
    <div class="col-xs-12">
        <div class="btn-group">     
            <a href="?r=accounting%2Freport%2Fmain"  class="btn btn-default-ew "><i class="fa fa-home"></i> <?=Yii::t('common','Home')?></a>       
            <a href="#"  class="btn btn-info-ew   refresh-wht-form"><i class="fa fa-refresh"></i> <?=Yii::t('common','Refresh')?></a>
            <a href="#"  class="btn btn-primary-ew  new-wht-form"><i class="fa fa-plus"></i> <?=Yii::t('common','Create')?></a>
        </div>
        <div class="pointer btn btn-default-ew pull-right close-pdf" style="font-size:17px; margin-right:-13px; margin-top: -3px;"><i class="fa fa-power-off text-red"></i></div>
        <div class="wht-render-table mt-5"></div>
        
        
    </div>
    
</div>

<div class="modal fade modal-full" id="modal-create-vendor">
    <div class="modal-dialog  ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Create Vendor')?></h4>
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



    
     
<div class="modal fade" id="modal-other-choice">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Other')?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice0"  value="0">
                                รางวัล
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice1"  value="1">
                                ส่งเสริมการขาย
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice2"  value="2">
                                ค่าโฆษณา
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice3"  value="3">
                                ค่าเช่า
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice4"  value="4">
                                ค่าขนส่ง
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice5"  value="5">
                                ค่าบริการ
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="other_choice" id="other_choice6"  value="6">
                                ค่าเบี้ยประกันวินาศภัย ฯลฯ
                            </label>
                        </div>

                         
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-primary save-form" data-render="edit"><i class="fa fa-save"></i> <?=Yii::t('common','Save')?></button>
            </div>
        </div>
    </div>
</div>


<style>
.list-inline {
    position: fixed;
    bottom: -10px;
    border-top: 1px solid #ccc;
    width: 100%;
    background-color: rgba(239, 239, 239, 0.9);
    padding: 10px 10px 15px 10px;
    right: 0px;
    text-align: right;
    z-index: 100;
    display: none;
}
</style>
<ul class="list-inline pull-right text-right">       
    <li><button type="button" class="btn btn-danger-ew close-pdf"><i class="fa fa-power-off" aria-hidden="true"></i> <?=Yii::t('common','Close')?></button> </li>
    <li><button type="button" class="btn btn-success save-form"><i class="fa fa-floppy-o" aria-hidden="true"></i> <?=Yii::t('common','Save')?></button></li>
</ul>


<div class="loading" style="position:absolute; top:30%; left:50%; z-index:3;"><i class="fa fa-refresh fa-spin text-center fa-2x"></i></div>
<?php
$Yii                = 'Yii';
 
$jsx=<<<JS

    let state = {
        name : ''
    };
    
    const iframe = `<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-50TW" name="iPrint-50TW" />`;

    const renderTable = (obj) => {
        let body = ``;

        obj.raws.map((model, key) => {
            /*<button type="button" class="btn btn-warning-ew btn-sm edit-wht hidden"><i class="fas fa-edit"></i></button>*/
            body+= `<tr data-key="`+ model.id +`">
                        <td>`+ (key+1) +`</td>
                        <td class="text-center">`+ model.date +`</td>
                        <td class="text-center">`+ model.book_id +`</td>
                        <td class="text-center">`+ model.book_no +`</td>
                        <td class="text-center">`+ model.no +`</td>
                        <td>`+ model.name +`</td>
                        <td align="center">
                            <div class="btn-group">
                                
                                <button type="button" class="btn btn-warning-ew btn-sm print-wht-preview" data-type="edit"><i class="fa fa-edit"></i></button>
                                <button type="button" class="btn btn-info-ew btn-sm print-wht"  data-type="print"><i class="fa fa-print "></i></button>
                                <button type="button" class="btn btn-danger-ew btn-sm delete-wht"><i class="fa fa-trash "></i></button>
                            </div>
                        </td>
                    </tr>`;
        })

        let table = `
            <table class="table table-bordered" id="export_table">
                <thead>
                    <tr class="bg-gray">
                        <th class="text-center" width="20">#</th>
                        <th class="text-center" width="150">วันที่</th>
                        <th class="text-center" width="150">เล่มที่</th>
                        <th class="text-center" width="150">เลขที่</th>
                        <th class="text-center" width="150">ลำดับที่</th>
                        <th>ผู้ถูกหัก ฯ</th>
                        <th width="150"> </th>
                    </tr>
                </thead>
                <tbody>
                 `+body+`
                </tbody>
            </table>            
        `;
        $('body').find('.close-pdf').hide();
        $('body').find('.wht-render-table').html(table);

        var tables = $('#export_table').DataTable({
            "paging": true,
            'pageLength' : 50,
            "searching": true
        });
    }

    const listWithholdingTax = (callback) => {
        fetch("?r=accounting/default/list-withholding-tax", {
            method: "POST",
            body: JSON.stringify({id:1}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            $('.loading').hide();
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const getWithholdingTax = (obj, callback) => {
        fetch("?r=accounting/default/get-withholding-tax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            $('.loading').hide();
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }




    $(document).ready(function(){
        
        listWithholdingTax(res => {
            renderTable(res);
        })
        
    });

    $('body').on('click', 'a.refresh-wht-form', function(){
        $('.loading').show();
        let el = $(this);
        el.find('.fa').addClass('fa-spin');
        $('body').find('.wht-render-table').html('');
        setTimeout(() => {
            listWithholdingTax(res => {
                renderTable(res);
                el.find('.fa').removeClass('fa-spin');
            })
        }, 500);
        
    });

    const showZone = () =>{
        $('.loading').hide();
        $('body').find('.close-pdf').show();
        $('body').find('.print-tw').show();
        $('body').find('.print-tw-modify').show();
    }

    const clearZone = () =>{
        $('body').find('input[name="doc-type"]').prop('checked',false);
        $('body').find('input[name="choice_payer"]').prop('checked',false);
        $('body').find('.print-tw-modify').attr('data-key','');

        $('body').find('input[name="book_id"]').val('');
        $('body').find('input[name="book_no"]').val('');
        
        $('body').find('input[name="other"]').val('');       
        $('body').find('input[name="amount"]').val('');
        $('body').find('input[name="vat"]').val('');   
        $('body').find('input[name="owner-name"]').val('');  
        
        $('body').find('input[name="vendor-code"]').val('').removeAttr('data-key');
        $('body').find('input[name="vendor_name"]').val('');
        $('body').find('input[name="vendor_address"]').val('');
        $('body').find('input[name="vendor_vat_regis"]').val('');

        $('body').find('.help-create-vendor').hide();
    }

    $('body').on('click', '.edit-wht, .print-wht-preview, .print-wht', function(){
        let id      = $(this).closest('tr').attr('data-key');  
        let btnType = $(this).attr('data-type');

        $('.loading').show();
        $('.print-tw').html('');
        clearZone();

        getWithholdingTax({id: parseInt(id)}, res => {
 
            $('body').find('.list-inline').show(); 
            if(res.status==200){
                
                $('body').find('input[name="book_id"]').val(res.raws.header.book_id);
                $('body').find('input[name="book_no"]').val(res.raws.header.book_no);

                $('body').find('input[name="owner-name"]').val(res.raws.header.user_name);
                $('body').find('.print-tw-modify').attr('data-key',res.raws.header.id);
                $('body').find('input[name="vendor-code"]').val(res.raws.header.code).attr('data-key', res.raws.header.vendId);         
                $('body').find('input[name="vendor_name"]').val(res.raws.header.name);
                $('body').find('input[name="vendor_address"]').val(res.raws.header.address);
                $('body').find('input[name="vendor_vat_regis"]').val(res.raws.header.vat_regis);
                $('body').find('input[name="no"]').val(res.raws.header.no);

                docType = res.raws.header.docType;                
                docType.map(el => {  
                    let ids = 'doc-type'+el;            
                    $('body').find('input[id="'+ids+'"]').trigger('click');
                });

                choicePayer = res.raws.header.payer;
                choicePayer.map(el => {  
                    let idChoice = 'choice_payer'+el;            
                    $('body').find('input[id="'+idChoice+'"]').trigger('click');
                });

                
                otherChoice = res.raws.header.other;
                otherChoice.map(el => {  
                    let idOtherChoice = 'other_choice'+el;            
                    $('body').find('input[id="'+idOtherChoice+'"]').trigger('click');
                });


                line  = res.raws.line;

                line.map(model => {
                     
                    if(model.id==13){
                        $('body').find('tr[data-key="'+model.id+'"]').find('input[name="other"]').val(model.other);
                    }
                    $('body').find('tr[data-key="'+model.id+'"]').find('input[name="amount"]').val(model.amount);
                    $('body').find('tr[data-key="'+model.id+'"]').find('input[name="vat"]').val(model.vat);
                   
                });
                
                state = {
                    name: res.raws.header.user_name 
                }
                
            }else{
                $('body').find('input[name="vendor-code"]').val('').attr('data-key','');
                $('body').find('input[name="vendor_name"]').html('ยังไม่มีลูกค้านี้');
                $('body').find('input[name="vendor_address"]').html('-');
                $('body').find('input[name="vendor_vat_regis"]').html('-');
                clearZone();
            }

            if(btnType == 'edit'){
                $('body').find('.list-inline .save-form').attr('data-render', 'edit');
                $('body').find('.save-form').attr('data-render', 'edit'); 
            }else{
                $('body').find('.list-inline .save-form').attr('data-render', 'print');
                $('body').find('.save-form').attr('data-render', 'print');
            }

            showZone();   
        });
    })


    $('body').on('click', '.print-wht-preview', function(){
        let id = $(this).closest('tr').attr('data-key');  
        $('.loading').show();
        $('.print-tw').html('');
        setTimeout(() => {
            setTimeout(() => {
              showZone();
            }, 1000);             
            $(iframe).attr('src', '?r=accounting/default/print50tw&id=' + id).appendTo('.print-tw');        
        }, 1000);
    })

    $('body').on('click', '.print-wht', function(){
        let id = $(this).closest('tr').attr('data-key');       
        $('.loading').show();
        $('.print-tw').html('');
        setTimeout(() => {
            setTimeout(() => {
                showZone();
            }, 1000);             
            $(iframe).attr('src', '?r=accounting/default/print50tw&nobg=true&id=' + id).appendTo('.print-tw');        
        }, 1000);
    })

    
    $('body').on('click', '.close-pdf', function(){
        $('.print-tw').html('');
        $('body').find('.print-tw').hide();
        $('body').find('.print-tw-modify').hide();
        $('body').find('.close-pdf').hide();
        $('body').find('.list-inline').hide();
        $('body').find('.print-tw-modify').removeAttr('data-key');
        $('body').find('input[name="vendor-code"]').removeAttr('data-key');
        
    });


    const createLine = (obj,callback) => {
        $('.loading').show();
        fetch("?r=accounting/default/create-withholding-line", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            $('.loading').hide();
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const validateField = (obj, callback) => {
         
        if(isNaN(obj.vendor)){
            $('body').find('input[name=".vendor-code"]').addClass('text-red').focus();
            $('body').find('.help-create-vendor').show().attr('style','position: absolute; left: 10px; top: -20px; z-index: 2;');
            callback({
                message: 'ใส่รหัสผู้เสียภาษี หากไม่มี ต้องสร้างรายชื่อก่อน',
                status: false
            });
        }else if(obj.lengthVat != 13){
            $('body').find('input[name="vendor_vat_regis"]').focus().addClass('text-red');
            callback({
                message: 'เลขผู้เสียภาษี ไม่ถูกต้อง = ' + obj.lengthVat + '/13 หลัก',
                status: false
            })
        }else {
            callback({
                message: '',
                status: true
            }) 
        }
         
    }

    $('body').on('click', '.save-form', function(){
        let data                = [];
        let line                = [];
        let payer               = []; 
        let otherChoice         = [];
        let docType             = [];
        let source              = parseInt($('body').find('.print-tw-modify').attr('data-key'));
        let vendor              = parseInt($('body').find('input[name="vendor-code"]').attr('data-key'));
        let vendor_name         = $('body').find('input[name="vendor_name"]').val();
        let vendor_address      = $('body').find('input[name="vendor_address"]').val();
        let vendor_vat_regis    = $('body').find('input[name="vendor_vat_regis"]').val();
        let book_id             = $('body').find('input[name="book_id"]').val();
        let book_no             = $('body').find('input[name="book_no"]').val();

        let renderType          = $(this).attr('data-render');
      
        let lengthVat           = vendor_vat_regis.length;

        validateField({lengthVat:lengthVat, vendor:vendor}, res => {
            if(!res.status){
                alert(res.message);
                return false;
            }else{

                $('body').find('input[name="vendor_vat_regis"]').removeClass('text-red');
                $('input[name="amount"]').map((keys, model) =>{             
                    
                // if($(model).val() != ""){
                        let id      = $(model).closest('tr').attr('data-key');
                        let other   = id == 13 ? $('body').find('input[name="other"]').val() : null;
                        let vat     = $(model).closest('tr').find('input[name="vat"]').val();
                        line.push({
                            id: parseInt(id),
                            value: $(model).val() * 1,
                            vat: vat * 1,
                            other: other
                        }); 
                    //}
                });

                $('input[name="doc-type"]:checked').map((keys, model) =>{             
                    docType.push({
                        value: $(model).val(),
                    });             
                });

                $('input[name="choice_payer"]:checked').map((keys, model) =>{             
                    payer.push({
                        value: $(model).val(),
                    });             
                })

                $('input[name="other_choice"]:checked').map((keys, model) =>{             
                    otherChoice.push({
                        value: $(model).val(),
                    });             
                })


                
                $('.print-tw').html('');

            
                createLine({
                    date: $('body').find('input[id="start_date"]').val(),
                    source : source,
                    vendor: vendor,
                    vendor_name:vendor_name,
                    vendor_address:vendor_address,
                    vendor_vat_regis:vendor_vat_regis,
                    no: $('input[name="no"]').val(),
                    line: line,
                    type: docType,
                    payer: payer,
                    user_name: state.name,
                    book_id: book_id,
                    book_no: book_no,
                    other: otherChoice
                }, res => {
                    if(res.status==200){
                        $('body').find('.print-tw-modify').attr('data-key',res.source);
                        if(renderType=='edit'){
                            $(iframe).attr('src', '?r=accounting/default/print50tw&id=' + res.source).appendTo('.print-tw'); 
                        }else{
                            $(iframe).attr('src', '?r=accounting/default/print50tw&nobg=true&id=' + res.source).appendTo('.print-tw'); 
                        }
                        
                    }
                    
                });
                 
            }
        });
    });


    $('body').on('change', 'input[name="owner-name"]', function(){
        state.name = $(this).val();

        console.log(state);
    });

    $('#modal-create-vendor').on('show.bs.modal',function(){   
        // let frame = `<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" />`;

        // $(frame).attr('src', '?r=vendors%2Fvendors%2Fcreate').appendTo('#modal-create-vendor .modal-body'); 
    });

    $('body').on('click', '.new-wht-form', function(){
        showZone();
        clearZone();
        $('body').find('.list-inline').show(); 
        //$('body').find('input[name="vendor-code"]').val('').removeAttr('data-key');
    });

    const deleteDoc = (obj, callback) =>{
        fetch("?r=accounting/default/delete-withholding-tax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            $('.loading').hide();
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    $('body').on('click', '.delete-wht', function(){
        let el = $(this);
        let id = el.closest('tr').attr('data-key');
        if(confirm("Delete ?")){
            deleteDoc({id:id}, res =>{
                if(res.status==200){
                    el.closest('tr').remove();
                }
               
            })
        }
    });


        
    const filterTable  = (search) => {

        Table = $('#export_table').DataTable();
        
        Table.search(search).draw() ;
    
    }
 
JS;

$this->registerJS($jsx,\yii\web\View::POS_END);
?>
 
<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 