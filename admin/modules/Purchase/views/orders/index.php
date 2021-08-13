<?= $this->render('@admin/themes/adminlte/views/layouts/_menu_apps') ?>

<div class="row">
    <div class="col-xs-12 col-sm-6"></div>
    <div class="col-xs-12 col-sm-6">
        <div class="form-group highlight-addon field-ordersearch-search">
            <div class="input-group">
                <input type="text" id="ordersearch-search" class="form-control text-search" name="search-order" value="" placeholder="ค้นหา">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-search">ค้นหา</button>
                </span>
            </div>
            <div class="help-block"></div>
        </div>
    </div>
</div>

<div class="row mb-10">
    <div class="col-xs-6 pull-right">
        <a class="btn btn-default-ew " href="?r=Purchase%2Fpurchase-line"><i class="fas fa-list-ul"></i> รายการสั่งซื้อ</a>
    </div>
</div>

<div class="row">
   
    <div class="col-sm-4">
        <h3 class="box-title">เอกสารใหม่</h3>
        <div class="new-render"></div>
    </div>
    <div class="col-sm-4">
        <h3 class="box-title">PO ค้างรับ</h3>
        <div class="outstanding-render"></div>
    </div>
    <div class="col-sm-4">
        <h3 class="box-title">รับสินค้าแล้ว</h3>
        <div class="received-render"></div>
    </div>

</div>
<div>
    <h4><?=Yii::t('common','Purchase Orders')?></h4>
</div>
<div>
    
</div>
<?php 
$js=<<<JS
let state = [
    {
        data:[]
    }
];

 

let getApi = (obj, callback) => {
    fetch("?r=Purchase%2Forders%2Findex-ajax", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => { state.data = response; callback(response); })
    .catch(e => { swal("Fail!", "Something Wrong. "+ e.responseText +' '+ new Date().toTimeString().slice(0, 8), "error"); });
}

let renderTable = (data, callback) => {
    let newData = data.data.raws;
    let html = '';
    let search = $('input.text-search').val();

    if(search != ''){
        newData =  newData.filter(model => (
                                                model.no.toLowerCase().indexOf(search) > -1 || 
                                                model.vendor_name.toLowerCase().indexOf(search) > -1 || 
                                                (model.ref ? model.ref : '' ).toLowerCase().indexOf(search) > -1 || 
                                                (model.detail ? model.detail : '').toLowerCase().indexOf(search) > -1
                                            ) 
                    ? model 
                    : null
                );
    } 

    newData = newData.filter(model => model.status === data.status ? model : null);
    


    newData.map((model, keys) => {
        let balance = parseFloat(model.balance);

        let Received = '';
            if(model.status==0){
                Received =  '';                    
            }else if(model.status==1){ // Release
                Received =  '<li class="change-order-status line" data-key="10"> <a href="#" title="Product Receiv"><i class="fas fa-hand-holding text-green"></i> Received</a></li>';
            }else if(model.status==10){ // Received
                Received =  '<li class="line" data-key="10"><a href="#" title="Product Receiv"><i class="fa fa-eye text-info"></i> Detail</li>';
            }else{
                Received =  '<li class="change-order-status line" data-key="10"><a href="#" title="Product Receiv"><i class="fa fa-eye text-info"></i> Detail </li>';
            }

            html+= `<tr data-key=`+model.id+`>
                        
                        <td style="padding:30px 10px 30px 10px;"> 
                            <div><h4><b>` + (model.vat > 0 
                                    ? (model.incVat == 1 
                                        ? '<i class="far fa-check-square text-green"></i>'
                                        : '<i class="far fa-check-square text-success"></i>')
                                    : '<i class="far fa-square"></i>') + ` 
                                    ` + model.no + `</b></h4> 
                            </div>
                        <div>` + model.vendor_name + ` </div>
                        <div>` + (model.ref ? model.ref : '' ) + ` </div>
                        <div>` + (model.detail ? model.detail : '') + ` </div>
                        <div class="text-right"><h4>` + number_format(balance.toFixed(2)) + `</h4> </div>
                        <div style="margin-top:30px;">
                            
                            <div class="btn-group btn-group text-center dropup" role="group">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-warning-ew" href="?r=Purchase%2Forders%2F%23" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-pencil"></i> แก้ไข  <span class="caret"></span></a>
                                        
                                    <ul class="dropdown-menu  ">
                                        <li class=" "><a class="" href="?r=Purchase%2Forders%2Freceive&id=`+model.id+`" title="รับสินค้าเข้า"><i class="fas fa-hand-holding text-green mr-5"></i> รับสินค้าเข้า</a></li>                                            
                                        <li class=" "><a class="" href="?r=Purchase%2Forders%2Fview&id=`+model.id+`" target="_blank"><i class="fa fa-eye text-info"></i> ดูข้อมูล</a></li>
                                        <li class=" "><a class="" href="?r=Purchase%2Forders%2Fupdate&id=`+model.id+`" target="_blank"><i class="fa fa-pencil text-warning"></i> แก้ไข</a></li>
                                        <li class=" "><a class="text-red" href="?r=Purchase%2Forders%2Fdelete&id=`+model.id+`" data-confirm="คุณแน่ใจ! ว่าต้องการลบรายการนี้หรือไม่?" data-method="post"><i class="far fa-trash-alt mr-10"></i> ลบ</a></li>
                                    </ul>
                                </div>
                                <div class="btn-group" role="group">
                                        <button type="button" class="btn  dropdown-toggle `+(model.status == 10 ? 'bg-teal' : 'btn-default-ew' )+` " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:80px">
                                        ` + (model.status == 0 
                                            ? '<i class="fas fa-lock-open"></i> Open'
                                            : ( model.status == 1 
                                                ? '<i class="fas fa-lock text-red"></i> Release'
                                                : '<i class="fas fa-check"></i> Received'                                           
                                            )) + `
                                        <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu option-list-menu">
                                            <li class="change-order-status line" data-key="0"><a href="#" style="color: #616161 !important;"><i class="fas fa-lock-open"></i> Open</a></li>
                                            <li class="change-order-status line" data-key="1"><a href="#"><i class="fas fa-lock text-red mr-5"></i> Release</a></li>                                        
                                            ` + Received +`
                                        </ul>
                                </div>
                                <a class="btn btn-info-ew" href="?r=Purchase%2Forders%2Fprint&id=`+model.id+`" target="_blank" ><i class="fas fa-print"></i> </a>
                            </div>
                        </td>
                    </tr>`;
    })

    let table = `
                <div class="table-responsive">
                    <table class="table table-bordered font-roboto" id="table-purchase-order">
                        <thead> 
                            <tr class="` +(data.status === 0 ? 'bg-warning' :  (data.status === 1 ? 'bg-info' : 'bg-success'))+ `">
                                 
                              
                                <th > </th>
                            </tr>
                        </thead>
                        <tbody>
                            `+html+`
                        </tbody>

                    </table>
                </div>
    `;
    

    callback({html:table});
}

const renderAllTable = (data) => {
    renderTable({data:data, status:0},  res =>{       
            $('body').find('.new-render').html(res.html)
    }),

    renderTable({data:data, status:1},  res =>{       
        $('body').find('.outstanding-render').html(res.html)
    }),

    renderTable({data:data, status:10},  res =>{       
        $('body').find('.received-render').html(res.html)
    })
}

$(document).ready(function(){
    var footer = $('div.content-footer').html();
    $('footer').html(footer).find('div.content-footer').fadeIn('slow');

    getApi({type:'empty'}, data => {
        renderAllTable(data);

        // renderTable({data:data, status:0},  res =>{       
        //     $('body').find('.new-render').html(res.html)
        // }),

        // renderTable({data:data, status:1},  res =>{       
        //     $('body').find('.outstanding-render').html(res.html)
        // }),

        // renderTable({data:data, status:10},  res =>{       
        //     $('body').find('.received-render').html(res.html)
        // })
    })

});


const changeStatus = (obj, callback) => {
    fetch("?r=Purchase%2Forders%2Fupdate-field", {
        method: "POST",
        body: JSON.stringify(obj),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
        },
    })
    .then(res => res.json())
    .then(response => { callback(response); })
    .catch(e => { swal("Fail!", "Something Wrong. "+ e.responseText +' '+ new Date().toTimeString().slice(0, 8), "error"); });
};

$('body').on('click', '.change-order-status', function(){
    let id      = $(this).closest('tr').attr('data-key');
    let value   = parseInt($(this).attr('data-key'));
    
    let doChange = (id,value) => {    
        changeStatus({id:id, field:'status', value:value}, res => {
            if(res.status===200){
                getApi({type:'empty'}, data => {
                    renderAllTable(data);
                })
               // window.location = "?r=Purchase%2Forders%2Findex"; 
            }else{
                swal("Fail!", res.message, "error");
            }
        })
    }

    if(value===10){
        if(confirm('ต้องการเปลี่ยนสถาณะ (โดยไม่รับสินค้า) ใช่หรือไม่ ?')){
            doChange(id,value);
        }else{
            return false;
        }
    }else{
        if(confirm('ต้องการเปลี่ยนสถาณะ ใช่หรือไม่ ?')){
            doChange(id,value);
        }else{
            return false;
        }
    }

});


$('body').on('keyup', 'input.text-search', function(){
    renderAllTable(state.data);
})

JS;
$this->registerJS($js,\yii\web\View::POS_END,'yiiOptions');