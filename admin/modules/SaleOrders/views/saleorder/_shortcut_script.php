<div class="loading" style="position:fixed; top:50%; left:48%; z-index:2010; display:none;"><i class="fas fa-spinner fa-spin fa-4x"></i></div>
<div class="modal fade modal-full" id="modal-pdr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?=Yii::t('common','Production Order')?></h4>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-ew pull-left" data-dismiss="modal"><i class="fa fa-power-off"></i> <?=Yii::t('common','Close')?></button>                 
            </div>
        </div>
    </div>
</div>

<?php
$Yii                = 'Yii';
$LABEL_CODE         = Yii::t('common','Code');
$LABEL_NAME         = Yii::t('common','Name');
$LABEL_TYPE         = Yii::t('common','Type');
$LABEL_QUANTITY     = Yii::t('common','Quantity');
$LABEL_CANCEL       = Yii::t('common','Undo');
$LABEL_REMAIN       = Yii::t('common','Remain');
$LABEL_PD_TITLE     = Yii::t('common','Production Order');
$LABEL_SH_TITLE     = Yii::t('common','Shipment');

$jsx=<<<JS

    var flashInterval;

    const iframeTR      = `<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-TR" name="iPrint-TR" />`;
    const iframeSHIP    = `<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-SHIP" name="iPrint-SHIP" />`;

    const getProductionOrder = (obj, callback) => {
        $('.loading').show();
        fetch("?r=Manufacturing/default/bom-detail", {
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
            setTimeout(() => {
                $('.loading').hide();
            }, 1000);
           
        })
        .catch(error => {
            console.log(error);
        });
    }

    const renderTableStock = (obj) => {
        let data = obj.raws;
        let html  = `<div><h4><span class="pdr-no">` + obj.no + `</span></h4></div>`;
            html += `<div><h5><span class="pdr-desc" style="font-family: saraban;">` + obj.desc + `</span></h5></div>`;
            html += `<table class="table table-bordered">
                        <thead>
                            <tr class="bg-gray" style="font-family: saraban; font-size:14px;">
                                <th style="width:150px;">${LABEL_CODE}</th>
                                <th>${LABEL_NAME}</th>
                                <th>${LABEL_TYPE}</th>
                                <th class="text-right"  style="width:150px;">${LABEL_QUANTITY}</th>
                                <th class="text-right" >${LABEL_REMAIN}</th>
                            </tr>
                        </thead>`;
            html += "<tbody>";

        data.length > 0
            ? data.map(model => {
                html += `<tr data-key="` + model.id + `" class="` + (model.qty < 0 ? 'bg-warning' : 'bg-success') + `" data-id="` + model.item + `">
                            <td class="code font-roboto"><a href="?r=items%2Fitems%2Fview&id=` + model.item + `" target="_blank">` + model.code + `</a></td>
                            <td style="font-family: saraban; font-size:14px;">` + model.name + `</td>
                            <td style="font-family: saraban; font-size:14px;" class="type">` + model.type + `</td>
                            <td class="text-right font-roboto">
                                <a href="?WarehouseSearch[ItemId]=` + btoa(model.item) + `&r=warehousemoving%2Fwarehouse" 
                                target="_blank"
                                class="` + (model.qty < 0 ? 'text-red' : 'text-green') + `">` + model.qty + `</a>
                            </td>
                            <td class="text-right font-roboto ">` + number_format(model.remain > 0 ? model.remain : '') + `</td>
                        </tr>`;
                    }
                )
            : null;

        html += "</tbody>";
        html += "</table>";
        html += `<div>` + obj.remark + `</div>`;
        html += `<div class="text-right">
                    <div class="undo-pdr">
                        <a href="#" class="btn btn-app btn-danger  ew-undo-pdr" data-key="` + obj.id + `"><i class="fa fa-undo"></i>${LABEL_CANCEL}</a>
                    </div>
                    <div class="undo-ship">
                        <a href="#" class="btn btn-app btn-danger  ew-undo-ship" data-key="` + obj.id + `"><i class="fa fa-undo"></i>${LABEL_CANCEL}</a>
                    </div>
                </div>`;
        
        $("body").find('#modal-pdr .modal-body').html(html);        
    }

    const getSaleOrder = (obj, callback) => {
        fetch("?r=SaleOrders%2Fsaleorder%2Findex-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
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

    const getSaleOrderDetail = (obj, callback) => {
        $('#modal-print-all .modal-body .print-so-section').html('');
        $('body').find('a#btn-create-invoice').hide();

        if(obj.status==='Confirmed'){
            $('a#btn-create-invoice').show();
        }else{
            $('a#btn-create-invoice').hide();
        }

        let loadingIcon = '<i class="fas fa-spinner fa-spin"></i>';
        $('body').find('.show-shipment-table').html(loadingIcon);
        $('body').find('.show-production-table').html(loadingIcon);
        $('body').find('.show-invoice-table').html(loadingIcon); 

        setTimeout(() => {
            
            fetch("?r=SaleOrders%2Fsaleorder%2Fdetail-ajax", {
                method: "POST",
                body: JSON.stringify(obj),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
            })
            .then(res => res.json())
            .then(response => {
                callback(response);
                
                $('#so-no').html(`<a href="?r=SaleOrders%2Fsaleorder%2Fview&id=&id=` + response.header.id + `" target="_blank" >` + response.header.no + `</a>`);
                $('a#so-print').attr('href', '?r=SaleOrders/saleorder/print&id=' + response.header.id + '&footer=1');
                $('#cust-name').html(`<a href="?r=customers/customer/view&id=` + response.header.custId + `" target="_blank" >` + response.header.custName + `</a>`);
                $('#ship_address').val(response.header.ship_address);
                $('#ship_name').val(response.header.custName);
                $('#ship_phone').val(response.header.ship_phone);

                 
                setTimeout(() => {
                    let transport = response.header.transport;
                   
                    $('#transport').val(transport).trigger('change');
            
                }, 1000);

                $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-SO" name="iPrint-SO" />')
                .attr('src', "?r=SaleOrders/saleorder/print&id=" + response.header.id + "&footer=1")
                .appendTo('#modal-print-all .modal-body .print-so-section'); // append to modal body or wherever you want
            })
            .catch(error => {
                console.log(error);
            });

        }, 500);
    }

    const getTransportAjax = (obj, callback) => {
        fetch("?r=warehousemoving/shipment/print-transport-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => {
            return res.json();
        })
        .then(response => {
           
            if(response.status==200){
             
            }
            
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }
    const getSaleOrderDetailShipment = (obj, callback) => {
        $('#modal-print-all .modal-body .print-ship-section').html('');
        $('#modal-print-all .modal-body .print-ship-section-pack').html('');

        $('body').find('.print-ship-section').html('<i class="fa fa-refresh fa-spin" ></i>')

        fetch("?r=SaleOrders/saleorder/detail-shipment-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => {
            return res.json();
        })
        .then(response => {
           
            let id = 0;

            if(response.data.length > 0){

                if(response.data[0].type != 'Production'){

                
                    id = response.data[0].id;
                    $(iframeSHIP).attr('src', "?r=warehousemoving/shipment/print-ship&id="+id+"&footer=1").appendTo('#modal-print-all .modal-body .print-ship-section');
                    $(iframeTR).attr('src', "?r=warehousemoving/shipment/print-transport&id="+id+"&footer=1").appendTo('#modal-print-all .modal-body .print-ship-section-pack');
            
                    /*
                    $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-SHIP" name="iPrint-SHIP" />')
                    .attr('src', "?r=warehousemoving/shipment/print-ship&id="+id+"&footer=1")
                    .appendTo('#modal-print-all .modal-body .print-ship-section'); // append to modal body or wherever you want
                    */
                    $('body').find('.print-delivery').attr('href', '?r=warehousemoving/shipment/print-transport&id='+id);
                    /*
                    $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-TR" name="iPrint-TR" />')
                    .attr('src', "?r=warehousemoving/shipment/print-transport&id="+id+"&footer=1")
                    .appendTo('#modal-print-all .modal-body .print-ship-section-pack'); // append to modal body or wherever you want
                    */
                    // getTransportAjax({id:response.data[0].id}, res =>{
                    //     $('#modal-print-all .modal-body .print-ship-section-pack').html(res.pdf);
                    // })
                }
                
            }
            if(response.status==200){
                $('body').find('.print-ship-section i.fa-refresh').remove();
            }
            
            callback(response);
        })
        .catch(error => {
            console.log(error);
        });
    }

    const getSaleOrderGetInvoice = (obj, callback) => {

        $('#modal-print-all .modal-body .print-inv-section').html('');

        fetch("?r=SaleOrders/saleorder/detail-invoice-ajax", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => {
            return res.json();
        })
        .then(response => {
            callback(response);
            
            if(response.data.length > 0){
                $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-INV" name="iPrint-INV" />')
                .attr('src', "?r=accounting%2Fposted%2Fprint-inv&id="+btoa(response.data[0].id)+"&footer=1")
                .appendTo('#modal-print-all .modal-body .print-inv-section'); // append to modal body or wherever you want
            }
            
            //const contentType = response.headers.get("content-type");
            //console.log(contentType)
        })
        .catch(error => {
            console.log(error);
        });
    }


    const renderTable = (obj, callback) => {
        let body = ``;
        //console.log(obj);
        let keys = 0; 
        obj.data.raws.map((model, i) => {
            let balance = model.balance * 1;
            let open    = model.status === 'Release' 
                            ? `<button class="approve-saleorder  btn btn-default-ew btn-xs">{$Yii::t('common','Approve')}</button>`
                            : `<button class="click-modal-` + obj.status + `  btn btn-default-ew btn-sm"><i class="fas fa-columns"></i> {$Yii::t('common','Description')}</button>`;
                    
            if(model.status === obj.status){
                keys+=1;
                body+= `
                    <tbody>
                        <tr data-key="` +model.id+ `">
                            <td class="font-roboto ">
                                <div class=" pointer pull-left">
                                    <div style="margin-right: 2px; width: 25px; float: left; background: #e8e8e8; text-align: center;">` + keys + `.</div>  <a href="?r=SaleOrders%2Fsaleorder%2Fview&id=` + model.id + `" target="_blank">` + model.no + `</a></div>
                                <div class="pull-right">
                                ` + open + `
                                </div>
                            </td>
                            <td class="font-roboto text-right" style="max-width:70px;">` + number_format(balance.toFixed(2)) + `</td>
                        </tr>
                    </tbody>
                `;
            }
        });

        let empty = `<tbody>
                        <tr>
                            <td colspan="2" style="padding: 7px !important;>ไม่มีข้อมูล</td>
                        </tr>
                    </tbody>`;

        let table = `
            <table class="table table-bordered font-roboto">
                ` + (body==`` ? empty : body) + `
            </table>
        `;
        
        callback(table);
    }

    const renderTableOnModal = (obj, callback) => {
        let body = ``;
        //console.log(obj);
        obj.data.map((model, i) => {
            let status  = model.type === 'Production'
                            ? (model.status === 'Undo-Produce' 
                                        ? 'text-gray pdr-detail' 
                                        : 'text-info pdr-detail')
                            : (model.status === 'Undo' 
                                        ? 'text-gray ship-detail' 
                                        : (model.type === 'Invoice' 
                                            ? 'text-info '
                                            : (model.status=='Undo-Shiped' 
                                                ? 'text-gray ship-detail' 
                                                : 'text-info ship-detail' )
                                            )
                                );

            let icon    = model.type === 'Production'
                            ? (model.status=='Undo-Produce' ? '<i class="fab fa-steam-symbol"></i>' : '<i class="fas fa-truck-loading"></i>')
                            : (model.type === 'Invoice'
                                ? '' 
                                : (model.status=='Undo-Shiped' ? '<i class="fas fa-box-open"></i>' : '<i class="fas fa-cube"></i>'));


            let link    = model.type === 'Invoice'
                            ? (model.status === 'Open'
                                ? `<a href="?r=accounting%2Fsaleinvoice%2Fupdate&id=` + model.id + `" target="_blank"><i class="far fa-file-pdf text-red"></i> ` + (icon + ` ` + model.no)  + `</a><button type="button" class="btn btn-danger  btn-xs btn-flat delete-line-line pull-right"><i class="fas fa-trash"></i> ลบ</button>`
                                : `<a href="?r=accounting%2Fposted%2Fposted-invoice&id=` + btoa(model.id) + `" target="_blank"><i class="far fa-file-pdf text-red"></i> ` + (icon + ` ` + model.no)  + `</a><button type="button" class="btn btn-danger  btn-xs btn-flat delete-line-line pull-right"><i class="fas fa-trash"></i> ลบ</button>`
                                )
                            : (model.status === 'Shiped' || model.status === 'Invoiced'
                                ? (`<span class="pointer ` + status + `">` + icon + ` ` + model.no + `</span><span class="pull-right">
                                                                <a class="btn btn-warning-ew btn-sm edit-ship-address" href="#" ><i class="fas fa-pencil-alt"></i> Edit</a>
                                                                <a class="btn btn-primary-ew btn-sm" href="?r=warehousemoving/shipment/print-ship&id=` + model.id + `&footer=1" target="_blank">
                                                                    <i class="fas fa-cube"></i>
                                                                </a>
                                                            </span>`)
                                : (`<span class="pointer ` + status + `" >`+ icon + ` ` + model.no + `</span>`)
                                )
             

            if(model.type === obj.type){
               
                body+= `
                    
                        <tr data-key="` +model.id+ `" class="` + obj.type + `" data-status="`+model.status+`">
                            <td class="font-roboto  ">
                               ` + link + `
                            </td>
                        </tr>
                    
                `;
            }
        });

        let empty = ` 
                        <tr class="` + obj.type + `">
                            <td >ไม่มีข้อมูล</td>
                        </tr>
                     `;

        let table = `
            <table class="table table-bordered table-renders" style="width:100%;">
                <tbody >` + (body==`` ? empty : body) + `</tbody>
            </table>
        `;
        
        callback(table);
    }

    const loadPage = () => {

        $('body').find('.show-release-table').html('<i class="fas fa-spinner fa-spin"></i>');
        $('body').find('.show-waiting-table').html('<i class="fas fa-spinner fa-spin"></i>');
        $('body').find('.show-confirm-table').html('<i class="fas fa-spinner fa-spin"></i>');
        $('body').find('.show-invoiced-table').html('<i class="fas fa-spinner fa-spin"></i>');

        getSaleOrder({'status' : 'Open', 'limit' : 100}, res => {
            
            renderTable({data:res, status:'Release'}, html => {
                $('body').find('.show-release-table').html(html);
            });

            renderTable({data:res, status:'Checking'}, html => {
                $('body').find('.show-waiting-table').html(html);
            });

            renderTable({data:res, status:'Confirmed'}, html => {
                $('body').find('.show-confirm-table').html(html);
            });

            renderTable({data:res, status:'Invoiced'}, html => {
                $('body').find('.show-invoiced-table').html(html);
            });
        });
    }


    $('body').on('click', '.pdr-detail' , function(){
        let id = parseInt($(this).closest('tr').attr('data-key'));
        getProductionOrder({id:id}, res => {
            if(res.status === 200){
                renderTableStock(res);
                $("body").attr("style", "overflow:hidden; margin-right:0px;"); // Modal on top
                $("#modal-pdr").modal("show");
                $('.ew-undo-pdr').show();
                $('.ew-undo-ship').hide();
                $("body").find('#modal-pdr .modal-title').html(`${LABEL_PD_TITLE}`);
            }
        })
    })

    $('body').on('click', '.ship-detail' , function(){
        let id = parseInt($(this).closest('tr').attr('data-key'));
        getProductionOrder({id:id}, res => {
            if(res.status === 200){
                renderTableStock(res);
                $("body").attr("style", "overflow:hidden; margin-right:0px;"); // Modal on top
                $("#modal-pdr").modal("show");
                $('.ew-undo-pdr').hide();
                $('.ew-undo-ship').show();
                //$("body").find('#modal-pdr .modal-body').append(`<div class="mt-10"> ` + (res.remark ? res.remark : ' ') + ` </div>`);
                $("body").find('#modal-pdr .modal-title').html(`${LABEL_SH_TITLE}`);
            }
        })
    })

    const UndoShip = (obj, callback) => {
         
        fetch("?r=Manufacturing/default/undo-transaction", {
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
    
    let undoPdr = (obj, callback) => {
        fetch("?r=Manufacturing/default/bom-revert", {
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

    $("body").on('click', 'a.ew-undo-pdr', function(){
        
        if(confirm("ยืนยันการยกเลิกรายการ ?")){
            let remark  = prompt("{$Yii::t('common','Remark')}", '');
            let id      = parseInt($(this).attr('data-key'));
            undoPdr({id:id, remark:remark, name:'Produce'}, res => {
                if(res.status===200){
                    renderTableStock(res);
                    $('.ew-undo-pdr').hide();
                    $('.ew-undo-ship').hide();
                }else{
                    $.notify({
                        // options
                        icon: 'fas fa-exclamation',
                        message: res.message
                    },{
                        // settings
                        type: 'warning',
                        delay: 1000,
                        z_index:3000,
                    });
                }
            })
        }
    })

    $("body").on('click', 'a.ew-undo-ship', function(){
        
        if(confirm("ยืนยันการยกเลิกรายการ ?")){
            let remark  = prompt("{$Yii::t('common','Remark')}", '');
            let id      = parseInt($(this).attr('data-key'));
            UndoShip({id:id, remark:remark, name:'Shiped'}, res => {
                if(res.status===200){
                    renderTableStock(res);
                    $('.ew-undo-pdr').hide();
                    $('.ew-undo-ship').hide();
                }else{
                    $.notify({
                        // options
                        icon: 'fas fa-exclamation',
                        message: res.message
                    },{
                        // settings
                        type: 'warning',
                        delay: 1000,
                        z_index:3000,
                    });
                }
            })
        }
    })


    $('body').on('click', '.click-modal-Release', function(){
        let id = $(this).closest('tr').attr('data-key');
        $('#modal-sale-order-action').modal('show').attr('data-key',id);
        getSaleOrderDetail({id:parseInt(id), status: 'Release'}, res=>{
            

            getSaleOrderDetailShipment({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Sale'}, html => {
                    $('body').find('.show-shipment-table').html(html);
                });

                renderTableOnModal({data:res.data, type:'Production'}, html => {
                    $('body').find('.show-production-table').html(html);
                });
            });

            getSaleOrderGetInvoice({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Invoice'}, html => {
                    $('body').find('.show-invoice-table').html(html);
                    if(res.data.length <= 0){
                        $('body').find('input[name="pre_inv_no"]').val(res.no);
                    }else{
                        $('body').find('input[name="pre_inv_no"]').attr('placeholder',res.no);
                    }
                });
            });
        })

    });

    $('body').on('click', '.click-modal-Checking', function(){
        let id = $(this).closest('tr').attr('data-key');
        $('#modal-sale-order-action').modal('show').attr('data-key',id);
        getSaleOrderDetail({id:parseInt(id), status: 'Checking'}, res=>{
             
            getSaleOrderDetailShipment({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Sale'}, html => {
                    $('body').find('.show-shipment-table').html(html);
                });

                renderTableOnModal({data:res.data, type:'Production'}, html => {
                    $('body').find('.show-production-table').html(html);
                });
            });

            getSaleOrderGetInvoice({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Invoice'}, html => {
                    $('body').find('.show-invoice-table').html(html);
                    if(res.data.length <= 0){
                        $('body').find('input[name="pre_inv_no"]').val(res.no);
                    }else{
                        $('body').find('input[name="pre_inv_no"]').attr('placeholder',res.no);
                    }
                });
            });

        })
    });

    $('body').on('click', '.click-modal-Confirmed', function(){
        let id = $(this).closest('tr').attr('data-key');
        $('#modal-sale-order-action').modal('show').attr('data-key',id);
        //$('#modal-sale-order-action .modal-title').html("{$Yii::t('common','Description')}")
        getSaleOrderDetail({id:parseInt(id), status: 'Confirmed'}, res=>{
            
            getSaleOrderDetailShipment({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Sale'}, html => {
                    $('body').find('.show-shipment-table').html(html);
                });

                renderTableOnModal({data:res.data, type:'Production'}, html => {
                    $('body').find('.show-production-table').html(html);
                });
            });

            getSaleOrderGetInvoice({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Invoice'}, html => {
                    $('body').find('.show-invoice-table').html(html);
                    if(res.data.length <= 0){
                        $('body').find('input[name="pre_inv_no"]').val(res.no);
                    }else{
                        $('body').find('input[name="pre_inv_no"]').attr('placeholder',res.no);
                    }
                });
            });
        })
    });

    $('body').on('click', '.click-modal-Invoiced', function(){
        let id = $(this).closest('tr').attr('data-key');
        $('#modal-sale-order-action').modal('show').attr('data-key',id);
        getSaleOrderDetail({id:parseInt(id), status: 'Invoiced'}, res=>{
            
            getSaleOrderDetailShipment({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Sale'}, html => {
                    $('body').find('.show-shipment-table').html(html);
                });

                renderTableOnModal({data:res.data, type:'Production'}, html => {
                    $('body').find('.show-production-table').html(html);
                });
            })

            getSaleOrderGetInvoice({id:parseInt(id)}, res => {
                renderTableOnModal({data:res.data, type:'Invoice'}, html => {
                    $('body').find('.show-invoice-table').html(html);
                    if(res.data.length <= 0){
                        $('body').find('input[name="pre_inv_no"]').val(res.no);
                    }else{
                        $('body').find('input[name="pre_inv_no"]').attr('placeholder',res.no);
                    }
                });
            })
        })
    });

    $('body').on('click', '.modal-production-detail', function(){
        let id  = $(this).closest('tr').attr('data-key');
        fetch("?r=Manufacturing/default/bom-detail", {
            method: "POST",
            body: JSON.stringify({id:id}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
             
        })
        .catch(error => {
            console.log(error);
        });
    })

    $('body').on('click', 'button.approve-saleorder', function(){
        let id  = $(this).closest('tr').attr('data-key');
        if(confirm(`{$Yii::t('common','Confirm')}`)){
            fetch("?r=SaleOrders%2Fsaleorder%2Fconfirm-order", {
                method: "POST",
                body: JSON.stringify({id:id}),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                },
            })
            .then(res => res.json())
            .then(response => {
                if(response.status===200){
                    loadPage();
                }
            })
            .catch(error => {
                console.log(error);
            });
        }else{
            return false;
        }
    });


    const cutStock = (obj, callback) => {
        $('#modal-print-all .modal-body .print-ship-section').html('');
        $('#modal-print-all .modal-body .print-ship-section-pack').html('');
        // ตัดสต๊อก        
        fetch("?r=SaleOrders/saleorder/shipment", {
            method: "POST",
            body: JSON.stringify(obj),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
                      
            
            $(iframeSHIP).attr('src', "?r=warehousemoving/shipment/print-ship&id="+response.id+"&footer=1").appendTo('#modal-print-all .modal-body .print-ship-section');
            $(iframeTR).attr('src', "?r=warehousemoving/shipment/print-transport&id="+response.id+"&footer=1").appendTo('#modal-print-all .modal-body .print-ship-section-pack');
            /*
            $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-SHIP" name="iPrint-SHIP" />')
            .attr('src', "?r=warehousemoving/shipment/print-ship&id="+response.id+"&footer=1")
            .appendTo('#modal-print-all .modal-body .print-ship-section'); // append to modal body or wherever you want

            */
            $('body').find('.print-delivery').attr('href', '?r=warehousemoving/shipment/print-transport&id='+response.id);


            
            /*
            $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-TR" name="iPrint-TR" />')
            .attr('src', "?r=warehousemoving/shipment/print-transport&id="+response.id+"&footer=1")
            .appendTo('#modal-print-all .modal-body .print-ship-section-pack');
            */

            callback(response);  
        })
        .catch(error => {
            console.log(error);
        });
        
    }


    const createInvoice = (obj, callback) => {
        // สร้างใบกำกับภาษี
        let id              = $('#modal-sale-order-action').attr('data-key');
        let posting_date    = $('#posting_date').val();
        let no              = $('#pre_inv_no').val().trim();
        $('#modal-print-all .modal-body .print-inv-section').html('');
        fetch("?r=SaleOrders/saleorder/create-invoice", {
            method: "POST",
            body: JSON.stringify({id:id,date:posting_date, no:no}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            callback(response);
            $('<iframe style="width: 100%; height: 100%;" frameBorder="0" width="auto" height="auto" id="iPrint-INV" name="iPrint-INV" />')
                .attr('src', "?r=accounting%2Fposted%2Fprint-inv&id="+btoa(response.inv)+"&footer=1")
                .appendTo('#modal-print-all .modal-body .print-inv-section'); // append to modal body or wherever you want
        })
        .catch(error => {
            console.log(error);
        });
    }

    

    $('body').on('click', 'a#btn-create-invoice', function(){
        // alert('ขออภัย! ยังไม่เปิดให้ใช้งาน');

        if($('#pre_inv_no').val() == ''){
            alert('ต้องใส่เลขที่ใบกำกับภาษี');
            $('#pre_inv_no').focus();
            return false;
        }else{

            if(confirm("Confirm ?")){

                $('body').find('a#btn-create-invoice').hide();

                // ตัดสต๊อก
                let id          = $('#modal-sale-order-action').attr('data-key');
                let transport   = $('#transport').val();
                let shipDate    = $('#ship_date').val();
                let ship_name   = $('#ship_name').val();
                let ship_address= $('#ship_address').val();
                let ship_phone  = $('#ship_phone').val();

                cutStock({id:id, transport:transport, ship_date:shipDate, ship_name:ship_name, ship_address:ship_address, ship_phone:ship_phone}, res =>{
                    if(res.status==200){

                        // Loading Produce
                        $('body').find('.table-renders .Production td').html('<div class="loading-production" style="height:35px; width:0%; background-color: rgb(138, 227, 249); margin:-8px; "></div>')
                        setTimeout(() => {
                            $('body').find('.table-renders .loading-production').css('width', '100%');
                        }, 1000);
                        // END -Loading

                        // Loading Store
                        $('body').find('.table-renders .Sale td').html('<div class="loading-sale" style="height:35px; width:0%; background-color: #5891cc; margin:-8px; "></div>')
                        setTimeout(() => {
                            $('body').find('.table-renders .loading-sale').css('width', '100%');
                        }, 1000);
                        // END -Loading                
                        
                        // เปิดบิล
                        createInvoice({id:res.id}, response =>{
                            if(response.status==200){

                                let invId = parseInt(response.inv);

                                // Loading  Invoice
                                $('body').find('.table-renders .Invoice td').html('<div class="loading-invoice" style="height:35px; width:0%; background-color: rgb(255, 158, 129); margin:-8px; "></div>')
                                setTimeout(() => {
                                    $('body').find('.table-renders .loading-invoice').css('width', '100%');
                                    setTimeout(() => {
                                        let invHtml = `<a href="?r=accounting/posted/posted-invoice&id=` + btoa(invId) + `" target="_blank">
                                                            <i class="far fa-file-pdf text-red"></i> ` + (response.no) + `
                                                        </a>`;
                                        $('body').find('.table-renders .Invoice td').html(invHtml);

                                        // Redirect to Invoice     
                                        //window.open('?r=accounting%2Fposted%2Fprint-inv&id=' + btoa(invId) +'&footer=1');

                                        // Redirect to Shipment
                                        // newwindow   = window.open('?r=warehousemoving%2Fshipment%2Fprint-ship&id=' + res.id +'&footer=1','Shipment');
                                        // if (window.focus) {newwindow.focus()} // Focus new window
                                        $('#modal-print-all').modal('show');
                                        loadPage(); 
                                    }, 500);
                                }, 1500);
                                // END -Loading                      


                            }else{
                                $.notify({
                                    // options
                                    icon: 'fas fa-exclamation',
                                    message: response.suggestion
                                },{
                                    // settings
                                    type: 'warning',
                                    delay: 5000,
                                    z_index:3000,
                                });

                                UndoShip({id:res.id, name:'Shiped'}, undo => {
                                    $.notify({
                                        // options
                                        icon: 'fas fa-exclamation',
                                        message: 'กำลังยกเลิกการตัดสต๊อก'
                                    },{
                                        // settings
                                        type: 'info',
                                        delay: 5000,
                                        z_index:3000,
                                    });

                                    if(undo.status===200){
                                        $('.ew-undo-pdr').hide();
                                        $('.ew-undo-ship').hide();
                                        setTimeout(() => {
                                            $('body').find('.table-renders .loading-sale').css('width', '0%');
                                        },1500);
                                    }else{
                                        $.notify({
                                            // options
                                            icon: 'fas fa-exclamation',
                                            message: undo.message
                                        },{
                                            // settings
                                            type: 'warning',
                                            delay: 5000,
                                            z_index:3000,
                                        });
                                    }
                                })
                            }
                            
                        }); 

                    }else{

                        if(res.status==403){
                            $.notify({
                                // options
                                icon: 'fas fa-exclamation',
                                message: res.message
                            },{
                                // settings
                                type: 'warning',
                                delay: 10000,
                                z_index:3000,
                            });
                            $('body').find('.table-renders div.loading-sale').css('width', '0%');
                        }else{
                        
                            UndoShip({id:res.id, name:'Shiped'}, undo => {
                                $.notify({
                                    // options
                                    icon: 'fas fa-exclamation',
                                    message: 'กำลังยกเลิกการตัดสต๊อก'
                                },{
                                    // settings
                                    type: 'info',
                                    delay: 5000,
                                    z_index:3000,
                                });
                                if(undo.status===200){
                                    $('.ew-undo-pdr').hide();
                                    $('.ew-undo-ship').hide();
                                    setTimeout(() => {
                                        $('body').find('.table-renders .loading-sale').css('width', '0%');
                                    },1500);
                                }else{
                                    $.notify({
                                        // options
                                        icon: 'fas fa-exclamation',
                                        message: undo.message
                                    },{
                                        // settings
                                        type: 'warning',
                                        delay: 5000,
                                        z_index:3000,
                                    });
                                }
                            });
                        }
                    }
                })
            }

        }

    });

    $('body').on('click', '.type-docment-click', function(){
        let el = $(this);
        let no = $(this).attr('data-text');
        fetch("?r=series/new-series", {
            method: "POST",
            body: JSON.stringify({no:no}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
            },
        })
        .then(res => res.json())
        .then(response => {
            if(response.status==200){
                $('#pre_inv_no').val(response.no);
                if(el.attr('data-text')=='GN'){
                    el.attr('data-text', 'IV').text('IV');
                }else if(el.attr('data-text')=='IV'){
                    el.attr('data-text', 'CT').text('CT');
                }else{
                    el.attr('data-text', 'GN').text('GN');
                }       
            }
        })
        .catch(error => {
            console.log(error);
        });
    });

    const getShipment   = (obj, callback) => {
        fetch("?r=warehousemoving/shipment/get-shipment", {
            method: "POST",
            body: JSON.stringify(obj),
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
    const modifyShipment = (obj, callback) => {
        fetch("?r=warehousemoving/shipment/modify-shipment", {
            method: "POST",
            body: JSON.stringify(obj),
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

    $('body').on('click', '.edit-ship-address', function(){
        let id          = $(this).closest('tr').attr('data-key');
 
        $('body').find('.panel-shipment').attr('class', 'panel panel-danger panel-shipment');

        getShipment({id:id}, res => {
            $('body').find('.save-modify').show();
            $('#transport').val(res.transport_id).trigger('change');
            $('#ship_date').val(res.ship_date).trigger('change').addClass('text-yellow');
            $('#ship_name').val(res.ship_name).addClass('text-yellow');
            $('#ship_address').val(res.ship_address).addClass('text-yellow');
            $('#ship_phone').val(res.phone).addClass('text-yellow');
            $('body').find('.save-modify').attr('data-id', res.id);
        })
    })

    $('body').on('click', '.save-modify', function(){
        let id          = $(this).attr('data-id');
        let transport   = $('#transport').val();
        let shipDate    = $('#ship_date').val();
        let ship_name   = $('#ship_name').val();
        let ship_address= $('#ship_address').val();
        let ship_phone  = $('#ship_phone').val();

        $('#ship_date').trigger('change').removeClass('text-yellow');
        $('#ship_name').removeClass('text-yellow');
        $('#ship_address').removeClass('text-yellow');
        $('#ship_phone').removeClass('text-yellow');

        modifyShipment({
            id:id, 
            transport:transport, 
            shipDate:shipDate, 
            ship_name:ship_name, 
            ship_address:ship_address, 
            ship_phone:ship_phone
        }, res => {
            if(res.status==200){
                $('body').find('.save-modify').hide();
                let btn = `<a class="btn btn-primary-ew " href="?r=warehousemoving/shipment/print-ship&id=` + id + `&footer=1" target="_blank">
                                                                    <i class="fas fa-cube"></i>
                                                                </a>`;

                //$('body').find('.save-modify').closest('div').html(btn);
                $('body').find('.panel-shipment').attr('class', 'panel panel-info panel-shipment');

                $.notify({
                        // options
                        icon: 'fas fa-check',
                        message: 'Saved'
                    },{
                        // settings
                        type: 'success',
                        delay: 1000,
                        z_index:3000,
                    });
            }else{
                $.notify({
                        // options
                        icon: 'fas fa-exclamation',
                        message: res.message
                    },{
                        // settings
                        type: 'warning',
                        delay: 3000,
                        z_index:3000,
                    });
            }
        })
       
    })

    $('body').on('click', '.create-only-bill', function(){

        let id          = $('body').find('.show-shipment-table tbody tr[data-status="Shiped"]').first().attr('data-key');
        let invId       = $('body').find('.show-invoice-table tbody tr[data-status="Posted"]').first().attr('data-key');
        let invNo       = $('#pre_inv_no').val();

        const createBillOnly = () => {
            // เปิดบิล
            createInvoice({id:id}, response =>{
                if(response.status==200){

                    let inv = parseInt(response.inv);

                    // Loading  Invoice
                    $('body').find('.show-invoice-table tbody').prepend(`<tr class="loading-invoice" >
                                                                            <td class="font-roboto loading-invoice">
                                                                                <div class="loading-invoice" style="height:35px; width:0%; background-color: rgb(255, 158, 129); margin:-8px; "></div>
                                                                            </td>
                                                                        </tr>`)
                    setTimeout(() => {
                        $('body').find('.table-renders div.loading-invoice').css('width', '100%');
                        setTimeout(() => {
                            let invHtml = `<tr data-key="`+inv+`" class="Invoice" data-status="Posted">
                                                <td class="font-roboto">
                                                    <a href="?r=accounting/posted/posted-invoice&id=` + btoa(inv) + `" target="_blank">
                                                        <i class="far fa-file-pdf text-red"></i> ` + (response.no) + `
                                                    </a>
                                                    <button type="button" class="btn btn-danger  btn-xs btn-flat delete-line-line pull-right"><i class="fas fa-trash"></i> {$Yii::t('common','Delete')}</button>
                                                </td>
                                            </tr>`;
                            $('body').find('.show-invoice-table tbody').prepend(invHtml);
                            $('#modal-print-all').modal('show');
                            $('tr.loading-invoice').remove();
                            loadPage(); 
                        }, 500);
                    }, 1500);
                    // END -Loading                  


                }else{
                    $.notify({
                        // options
                        icon: 'fas fa-exclamation',
                        message: response.suggestion
                    },{
                        // settings
                        type: 'danger',
                        delay: 5000,
                        z_index:3000,
                    });
                    $('#pre_inv_no').focus();
                }            
            });  
        }

        if(confirm("ระบบจะสร้างบิลขึ้นมาใหม่ โดยใช้ข้อมูลการจัดของ  คุณต้องการสร้างบิล ใช่หรือไม่?")){
            
            if (typeof id === "undefined") {
                $.notify({
                    // options
                    icon: 'fas fa-exclamation',
                    message: 'ยังไม่มี ใบจัดของ'
                },{
                    // settings
                    type: 'danger',
                    delay: 5000,
                    z_index:3000,
                });

                
                if(invNo== ''){
                    $('#pre_inv_no').val($('#pre_inv_no').attr('placeholder'));
                }
                
                
                flashInterval = setInterval(function () {
                                    $('.packing-box').toggleClass('box-danger');
                                }, 1000, function(){
                                    clearInterval(flashInterval);
                                    $('.packing-box').removeClass('box-danger');
                                });
                
                   
                 
                 
            }else{

                if (typeof invId !== "undefined") { // ถ้ามีบิลอยู่แล้ว
                    if(confirm("กำลังจะเปิดบิลซ้ำ ?")){
                        createBillOnly();
                    }else{
                        return false;
                    }               
                }else{
                    createBillOnly();
                }
            }

        }else{
            return false;
        }

    });


    $('body').on('click', '.create-only-shipment', function(){

        // ตัดสต๊อก
        if(confirm("ต้องการตัดสต๊อกหรือไม่ ?")){
        
            let data = {
                id: $('#modal-sale-order-action').attr('data-key'), 
                transport: $('#transport').val(), 
                ship_date: $('#ship_date').val(), 
                ship_name: $('#ship_name').val(), 
                ship_address: $('#ship_address').val(), 
                ship_phone: $('#ship_phone').val()
            }

            // Loading Store
            $('body').find('.show-shipment-table tbody').prepend(`<tr class="loading-sale">
                                                                    <td>
                                                                        <div class="loading-sale" style="height:35px; width:0%; background-color: #5891cc; margin:-8px;"></div>
                                                                    </td>
                                                                </tr>`);
            $('body').find('.table-renders div.loading-sale').css('width', '50%');

            cutStock(data, res =>{
                if(res.status==200){                
    
                    $('body').find('.table-renders div.loading-sale').css('width', '100%');                
                    // END -Loading       
                    setTimeout(() => {
                        $('body').find('.show-shipment-table tr.loading-sale').remove();
                        let invHtml = `<tr data-key="`+ res.id + `" class="Sale" data-status="Shiped">
                                            <td class="font-roboto">
                                                <span class="pointer text-info ship-detail">
                                                    <i class="fas fa-cube"></i> `+ res.no + `</span>
                                                    <span class="pull-right">
                                                        <a class="btn btn-warning-ew btn-sm edit-ship-address" href="#"><i class="fas fa-pencil-alt"></i> Edit</a>
                                                        <a class="btn btn-primary-ew btn-sm" href="?r=warehousemoving/shipment/print-ship&id=`+ res.id + `&footer=1" target="_blank">
                                                        <i class="fas fa-cube"></i>
                                                    </a>
                                                </span>
                                            </td>
                                        </tr>`;
                        $('body').find('.show-shipment-table tbody').prepend(invHtml);
                    }, 3000);  
                    
                    
                }else{
                    
                    if(res.status==403){
                        $.notify({
                            // options
                            icon: 'fas fa-exclamation',
                            message: res.message
                        },{
                            // settings
                            type: 'warning',
                            delay: 10000,
                            z_index:3000,
                        });
                        $('body').find('.table-renders div.loading-sale').css('width', '0%');
                    }else{
                        
                        UndoShip({id:res.id, name:'Shiped'}, undo => {
                            $.notify({
                                // options
                                icon: 'fas fa-exclamation',
                                message: 'กำลังยกเลิกการตัดสต๊อก'
                            },{
                                // settings
                                type: 'info',
                                delay: 10000,
                                z_index:3000,
                            });
                            if(undo.status===200){
                                $('.ew-undo-pdr').hide();
                                $('.ew-undo-ship').hide();
                                setTimeout(() => {
                                    $('body').find('.table-renders .loading-sale').css('width', '0%');
                                },1500);
                            }else{
                                $.notify({
                                    // options
                                    icon: 'fas fa-exclamation',
                                    message: undo.message
                                },{
                                    // settings
                                    type: 'warning',
                                    delay: 10000,
                                    z_index:3000,
                                });
                            }
                        })
                    }
                }
            });
        }
    });


    $('#modal-sale-order-action').on('show.bs.modal', function(){
        clearInterval(flashInterval);
        $('.packing-box').removeClass('box-danger');
    });

    $('#modal-sale-order-action').on('hidden.bs.modal', function(){
        $('body').find('input[name="pre_inv_no"]').attr('placeholder', '').val('');
        
        
        $('#modal-print-all .modal-body .print-so-section').html('');
        $('#modal-print-all .modal-body .print-ship-section').html('');
        $('#modal-print-all .modal-body .print-ship-section-pack').html('');
        $('#modal-print-all .modal-body .print-inv-section').html('');
        
    });
 
JS;

$this->registerJS($jsx,\yii\web\View::POS_END);
?>
 