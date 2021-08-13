<?php

use yii\helpers\Html;
 

?>
<style>
.vertical-middle{
    vertical-align: middle !important;
}

td.border-bottom,
th.border-bottom {
    border-bottom: 1px solid #000 !important;
}

.boxs:hover{
    background:#ebffff;
}

.check-text-footer{
    font-size:15px;
}

.change-field{
    background: #f3f9f7;
}


@media print {
    @page{
        size: A4;
        margin: 0;
    }
    html, body {
        /* width: 210mm;
        height: 297mm; */        
        font-size:11px;
    }
    
    .no-print{
        display:none;
    } 

    .minus-from-ship,
    .without-print {
        display:none;
    }
    
    .img-logo {
        width:50px;
    }

    h3 {
        font-size:18px;
    }

    td.balance{
        background: #fcf8e3 !important;
    }

    td.document-no a {
        color: #000 !important;
    }

    .check-text-footer{
        font-size:10px;
    }

    .ship-date{
        border:none;
        width:60px;
    }
}
    
</style>
<div class="row without-print">
    <div class="col-xs-12 text-right mb-10">
        <button class="btn btn-info-ew" id="print-page"><i class="fa fa-print"></i> <?=Yii::t('common','Print')?></button>
    </div>
</div>
<div class="row" style="font-family: 'saraban'; font-weight: bold; margin-bottom:200px;">
    <div class="col-xs-12 table-responsive">
        <table class="table table-bordered " id="transport-table">
            <thead>
                <tr >
                    <th colspan="7" class="vertical-middle"> 
                        <div class="row">
                            <div class="col-xs-3 text-left">
                                <?= Html::img($company->logoViewer,['class' => 'img-logo', 'style' => 'max-width:50px;']) ?>
                            </div>
                            <div class="col-xs-9 text-left">
                                <h3>ใบแจ้งส่งสินค้าผลิตภัณฑ์สำเร็จรูปประจำวัน</h3>
                            </div>
                        </div>
                    </th>
                    <th  class="text-right" colspan="2">วันที่จัดส่ง <span ><input type="text"  class="ship-date text-right" id="datepicker" value="<?=date('d/m/Y')?>"></span></th>
                </tr>
                <tr class="bg-gray">
                    <th rowspan="2" class="text-center vertical-middle border-bottom" style="width:20px;">ลำดับ</th>
                    <th rowspan="2" class="text-center vertical-middle border-bottom" style="min-width:80px;">บริษัท/ห้างร้าน</th>
                    <th rowspan="2" class="text-center vertical-middle border-bottom" style="min-width:80px;">รายชื่อเซลล์</th>
                    <th colspan="4" class="text-center" >รายการชิ้นงานที่ส่งไป</th>
                    <th rowspan="2" colspan="2" class="text-center vertical-middle border-bottom" >หมายเหตุ</th>
                </tr>
                <tr class="bg-gray">
                    <th class="border-bottom text-center" style="min-width:80px;">จำนวน/กล่อง</th>
                    <th class="border-bottom" style="min-width:90px;">ขนส่ง</th>
                    <th class="border-bottom" width="40px">กทม.</th>
                    <th class="border-bottom" width="40px">ห้าง</th>
                </tr>
            </thead>
            <tbody>
               <tr>
                <td colspan="9" class="text-center">
                <div style="margin-top:50px; margin-bottom:150px;">
                    <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i> Loading...
                </div>
                </td>
               </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" rowspan="4">
                        <div class="row">
                            <div class="col-xs-12 text-center"><h5><u> เวลาออกเดินทาง </u></h5></div>
                        </div>
                        <div class="row check-text-footer">                            
                            <div class="col-xs-4 mt-10"> 
                                <div class="mb-10"><i class="far fa-square fa-2x"></i> <span >เต็มรถ</span></div>
                                <div class="mt-10"><i class="far fa-square fa-2x"></i> <span >ไม่เต็มรถ</span></div>
                            </div>
                            <div class="col-xs-8 mt-10"> 
                                <div class="mb-10"><i class="far fa-square fa-2x"></i> <span > ออกเดินทาง  ...................................................................................................</span></div>
                                <div class="mt-10"><i class="far fa-square fa-2x"></i> <span > กลับถึงบริษัท  ................................................................................................</span></div>
                            </div>                            
                        </div>    
                        <div class="row">
                            <div class="col-xs-12" style="margin-top:30px;">ทะเบียนรถ .......................................................................................................</div>
                        </div>                
                    </th>
                </tr>
                <tr>
                    <th colspan="2" class="text-center vertical-middle">ผู้อนุมัติ</th>
                    <th class="text-center vertical-middle" >ผู้ตรวจสอบ</th>
                    <th class="text-center vertical-middle" style="min-width:60px;">ผู้แจ้ง</th>
                </tr>
                <tr>
                    <th colspan="2" style="height:60px;"> </th>
                    <th> </th>
                    <th> </th>
                </tr>
                <tr>
                    <th colspan="4" class="vertical-middle" style="height:50px;"> วันที่แจ้ง : </th> 
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row no-print" >
    <div class=" " style="position: fixed;
      bottom: -10px;
      border-top: 1px solid #ccc;
      width: 100%;
      background-color: rgba(239, 239, 239, 0.9);
      padding: 10px 0px 15px 0px;
      right: 0px;
      z-index:2000;">
    <div class="col-sm-6 text-left"><button type="button" class=" btn btn-danger-ew erase-all"><i class="fas fa-eraser" title="<?=Yii::t('common','Delete All')?>" alt="<?=Yii::t('common','Delete All')?>"></i> <?=Yii::t('common','Clear All')?></button></div>
    <div class="col-sm-6 text-right"><button class="btn btn-info" id="print-page"><i class="fa fa-print"></i> <?=Yii::t('common','Print')?></button></div>
    </div>
</div>
 


 
<div class="modal fade" id="modal-customer-modify">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Customer')?></h4>
            </div>
            <div class="modal-body">
                
                <input type="text" name="customer-name" class="form-control" />
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>
                <button type="button" class="btn btn-primary-ew save-change"><i class="fa fa-save"></i> <?=Yii::t('common','Save changes')?></button>
            </div>
        </div>
    </div>
</div>

<?php
$thisTime = date('d/m/Y');
$thisYears= date('Y');
$js=<<<JS

const state = {
    date: `{$thisTime}`
}


    const getSaleOrderTransport = (callback) => {
        fetch("?r=SaleOrders/report/load-data-transport", {
            method: "POST",
            body: JSON.stringify({years:2020}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {        
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }


    const renderTableTranport = (res) => {
        let data = res.data;
        let html = ``;   

        data.length > 0 ? data.map((model, key) => {    
 
            html+= `
                    <tr data-key="` + model.id + `">
                        <td class="text-center">` + (key + 1) + `</td>
                        <td>
                            <span class="text-left"> ` + model.date + ` </span>
                            <span class="pull-right"> 
                                <a href="#" data-id=` + model.custId + `" class="text-black modify-name">` + model.custName + ` </a></span>
                        </td>
                        <td class="text-center">
                            ` + model.saleName + `                    
                        </td>
                        <td class="text-center bg-warning balance">
                        ` + number_format(parseInt(model.balance)) + `                       
                        </td>
                        <td class="transport-name-change change-field pointer" data-name="transport" data-ship="` + model.shipId + `" > ` + model.transport + `</td>
                        <td class="change-field pointer text-center " data-name="area"> ` + model.area + ` </td>
                        <td class="change-field pointer text-center " data-name="area_2"> ` + model.area_2 + ` </td>
                    
                        <td colspan="2"  class="change-field pointer" data-name="remark"><span > ` + model.remark + `</span> </td>
                    </tr>
                    <tr data-key="` + model.id + `" >
                        <td class="border-bottom"> </td>
                        <td class="text-right border-bottom document-no"> 
                            <a href="?r=SaleOrders%2Fsaleorder%2Fview&id=` + model.orderId + `" target="_blank" class=" ">` + model.no + ` </a>  
                            <div><small> (` + model.create + `)</small></div>
                        </td>
                        <td class="text-center border-bottom">
                            [` + model.saleCode + `]
                        </td>
                        <td class="text-center border-bottom change-field pointer" data-name="boxs" data-ship="` + model.shipId + `"> ` + model.boxs + ` </td>
                        <td class="border-bottom">
                        ` +(model.invoice.no ? model.invoice.no : '')+ ` 
                        <div> ` +(model.invoice.date ? ` <small>(` + model.invoice.date + `)</small>` : '')+ ` </div>
                        </td>
                        <td class="border-bottom "  > </td>
                        <td class="border-bottom "  >  </td>
                        <td class="border-bottom " colspan="2" > <button class="pull-right btn btn-danger-ew minus-from-ship btn-xs" ><i class="fa fa-times"></i></button>  </td>
                    </tr>
            `;
    
        }): null;
        
        $('body').find('#transport-table tbody').html(html);
        
    }


    $(document).ready(function(){
        getSaleOrderTransport(res => {
            renderTableTranport(res);
        })
        //$('body').find('span.ship-date').html(state.date);
        $('body').find(".ship-date").datepicker(
            { dateFormat: 'dd/mm/yy' }
        );
    })


    $('body').on('click', '.minus-from-ship', function(){
        let thisBtn = $(this);
        let rows    = $(this).closest('tr').attr('data-key');

        fetch("?r=SaleOrders/report/remove-bill-from-transport-ship", {
            method: "POST",
            body: JSON.stringify({ id: rows }),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            if(response.status===200){
                getSaleOrderTransport(res => {
                    renderTableTranport(res);
                })
            }else{
                thisBtn.attr('class','btn btn-success btn-xs minus-from-ship');
                alert('Error! Something wrong');
            }            
        })
        .catch(error => {
            console.log(error);
        });
    })



    const updateBox = (obj, callback) => {
        fetch("?r=warehousemoving/header/update-box", {
            method: "POST",
            body: JSON.stringify({id:obj.id, boxs:obj.boxs}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {        
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const updateTransport = (obj, callback) => {
        fetch("?r=warehousemoving/header/update-transport", {
            method: "POST",
            body: JSON.stringify({id:obj.id, boxs:obj.transport}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {        
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }


    $('body').on('click', 'td.boxs', function(){
        let boxs = $(this);  
        if(boxs.attr('data-ship')){             
            var box = prompt("กรุณาใส่จำนวนกล่อง", " ");
            if (box != null) {
                let obj = {
                        id: boxs.attr('data-ship'),
                        boxs: box
                    };
                if(obj.id != 0){
                    console.log(box);
                    updateBox(obj, res => {
                        boxs.html(res.boxs)
                    }); 
                }       
            }
        }else{
            alert('ยังไม่จัดของ')
        }
    });


    $('body').on('click', 'td.transport-name', function(){
        let boxs = $(this);  
        
        
        let obj = {
                id: boxs.attr('data-ship'),
                boxs: box
            };

        if(obj.id != 0){
            updateBox(obj, res => {
                boxs.html(res.boxs)
            }); 
        }       
        
    });


    $('body').on('click', 'button#print-page', function(){
        window.print();
    });




    const changeTransportShip = (obj, callback) => {
        fetch("?r=SaleOrders/report/update-transport-ship", { 
            method: "POST", 
            body: JSON.stringify(obj),
            headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
        })
        .then(res => res.json())
        .then(response => { 
            if(response.status!==200){
                $.notify({
                // options
                icon: "fas fa-box-open",
                message: response.message
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: "warning",
                    delay: 3000,
                    z_index: 3000
                });    
            }
            callback(response);         
        })
        .catch(error => { console.log(error); });
    }


    $('body').on('click', '.change-field', function(){
        let el      = $(this);
        let id      = $(this).closest('tr').attr('data-key');
        let field   = $(this).attr('data-name');
        let value   = $(this).text();

        let boxs    = $(this);  
            
        var box     = prompt("กรุณาใส่ข้อมูลที่ต้องการ", value.trim());
        if (box != null) {
             
             
            changeTransportShip({id:id, field:field, value:box}, res =>{
                el.html(box);
            })
            // updateBox(obj, res => {
            //     boxs.html(res.boxs)
            // }); 
            
        }
    
        
    })

    $('body').on('click', '.erase-all', function(){
        if(confirm("Clear ?")){
            fetch("?r=SaleOrders/report/delete-transport-ship", { 
                method: "POST", 
                body: JSON.stringify({clear:'all'}),
                headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
            })
            .then(res => res.json())
            .then(response => { 
                if(response.status===200){
                    setTimeout(() => {
                        $('body').find('#transport-table tbody').fadeOut();
                        setTimeout(() => {
                            $('body').find('#transport-table tbody').html('');
                            setTimeout(() => {
                                $('body').find('#transport-table tbody').fadeIn();
                            }, 1000);
                        }, 500);
                    }, 800);
                
                }else {
                    $.notify({
                    // options
                    icon: "fas fa-box-open",
                    message: response.message
                    },{
                        // settings
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: "warning",
                        delay: 3000,
                        z_index: 3000
                    });    
                }
                        
            })
            .catch(error => { console.log(error); });
        }
    });

    $('body').on('click', 'a.modify-name', function(){
        $('#modal-customer-modify').modal('show');
        let name    = $(this).html();
        let id      = $(this).attr('data-id');

        
        setTimeout(() => {
            $('body').find('input[name="customer-name"]').attr('data-id',id).val(name).focus();
        }, 500);
    });

    const updateCustomerName = () => {
        let name    = $('body').find('input[name="customer-name"]').val();
        let id      = $('body').find('input[name="customer-name"]').attr('data-id');
        
        fetch("?r=customers/ajax/update-nick-name", { 
                method: "POST", 
                body: JSON.stringify({id:id, name:name}),
                headers: {"Content-Type": "application/json","X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")}
            })
            .then(res => res.json())
            .then(response => { 
                if(response.status===200){
                    $('#modal-customer-modify').modal('hide');
                    $('body').find("a.modify-name[data-id='" + id + "']").html(name);
                }else {
                    $.notify({
                    // options
                    icon: "fas fa-box-open",
                    message: response.message
                    },{
                        // settings
                        placement: {
                            from: "top",
                            align: "center"
                        },
                        type: "warning",
                        delay: 3000,
                        z_index: 3000
                    });    
                }
                        
            })
            .catch(error => { console.log(error); });
    }

    $("body").on("keypress", 'input[name="customer-name"]', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            updateCustomerName()
        }
    });


    $('body').on('click', '.save-change', function(){
        updateCustomerName();
    })

JS;


$this->registerJs($js,Yii\web\View::POS_END);
 
?>

<?php $this->registerCssFile('//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');?>
<?php $this->registerJsFile('//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<?php $this->registerJsFile('//code.jquery.com/ui/1.12.1/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

 

 