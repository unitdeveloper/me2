<?php
$Yii    = 'Yii';
$js     =<<<JS

const renderPoList = (data) => {
    let rows = '';
    data.map((model, key) => {
        rows+= `<tr data-key="` + key + `" data-id="` + model.id + `" class="tb-rows">
                    <td class="text-center"> ` + (key + 1) + ` </td>
                    <td class="font-roboto" ><a href="index.php?r=Purchase/order/view&id=` + model.id + `" target="_blank"> ` + model.no + ` </a></td>
                    <td class="text-left"> ` + model.date + ` </td>
                    <td class="text-left"> ` + model.due + ` </td>
                    <td class="text-right"> ` + number_format(model.amount.toFixed(2)) + ` </td>
                    <td class="text-center">
                        <button type="button" class="selected-po-list btn btn-primary btn-flat"><i class="fas fa-download"></i> {$Yii::t('common','Select')}</button>
                    </td>
                </tr>`;
       if(model.received.length > 0){
            rows+= `<tr class="bg-gray">
                        <th> </th>
                        <th class="text-center"> </th>
                        <th> {$Yii::t('common','Receipt No.')} </th>
                        <th class="text-left"> {$Yii::t('common','Receipt Date')} </th>
                        <th class="text-right font-roboto"> {$Yii::t('common','Quantity to receive')} </th>
                        <th style="width:95px;" class="text-center"> </th>
                    </tr>`;
            model.received.map((rc, key) => {  
                      
                rows+= `<tr  data-key="` + key + `" data-id="` + rc.id + `" data-po="` + model.id + `">
                            <td> </td>
                            <td class="text-center">` + (key + 1) + ` </td>
                            <td class="text-info"><a href="index.php?r=warehousemoving/header/view&id=` + rc.id + `"  target="_blank">` + rc.no + `</a></td>
                            <td class="text-left">` + rc.date + `</td>
                            <td class="text-right">` + rc.count + `</td>
                            <td class="text-center"><button type="button" class="selected-rc-list btn btn-info-ew btn-flat">
                                <i class="fas fa-download"></i> {$Yii::t('common','Select')}</button>
                            </td>
                        </tr>`;
            })
       }         
    })
    let html = `<table class="table table-bordered">
                    <thead>
                        <tr class="bg-dark">
                            <th class="text-center">#</th>
                            <th style="width:140px;">{$Yii::t('common','No')}</th>
                            <th class=" ">{$Yii::t('common','Date')}</th>
                            <th class=" ">{$Yii::t('common','Payment Due.')}</th>
                            <th class="text-right font-roboto">{$Yii::t('common','Amount')}</th>
                            <th style="width:95px;" class="text-center">{$Yii::t('common','Select')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        ` + rows + `                        
                    </tbody>
                </table>`;
    return html;
}

const showGetSource = () => {
    let vendor = JSON.parse(localStorage.getItem("vendors"));
    fetch("?r=accounting/payment/get-purchase-list", {
        method: "POST",
        body: JSON.stringify({vendor:vendor.id}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {
     
        
        $('#modal-get-source .modal-body').html(renderPoList(response.raw));
        $('#modal-get-source').modal('show');
        $('#modal-get-source .modal-title').html('{$Yii::t("common","Purchase Order")}');
        setTimeout(() => {
            $('body').find('button.selected-po-list:first').select().focus();
        }, 500);

    })
    .catch(error => {
        console.log(error);
    });
}

$('body').on('click', '.open-modal-get-source', function(){  
    showGetSource();
});   

$('#modal-get-source').on('show.bs.modal',function(){   
    setTimeout(() => {
        $('body').attr('style',' ');
    }, 500);    
})




$('body').on('click','button.selected-po-list', function(){
    let id = $(this).closest('tr').attr('data-id');
    fetch("?r=accounting/payment/push-purchase-line", {
        method: "POST",
        body: JSON.stringify({id:id}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {     
        
        $('input[name="ext_document"]').val(response.po.no).attr('data-id', response.po.id);
        let data = localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
        // Put data to line for editable
        response.raw.map(model => {
            let unit = model.measure.filter(u => u.selected ? u.name : null);
            console.log(unit);
            data.push({
                id: model.item,
                code: model.code,
                name: model.name,
                qty: model.qty,
                price: model.price,
                unit: unit[0].name,
                measure: unit[0].id,
            });
        })

        localStorage.setItem('payment-line',JSON.stringify(data));
        $('#modal-get-source').modal('hide');
        
        $('body').find('#Payment-Line').html(renderLineTable(data));
        setTimeout(() => {
            $('body').find('input[name="search-code"]').select().focus();
        }, 500);
         

    })
    .catch(error => {
        console.log(error);
    });
});

$(document).ready(function(){
    
})


$('body').on('keydown',function(e){
    var keyCode = e.keyCode || e.which;
    
    if(keyCode == 18){
        state.key  = keyCode;     // ลงทะเบียนไว้ว่ากด Alt ไปแล้ว   
    }

    // Alt + G
    // 18 = Alt
    // 71 = G 
    if(state.key == 18){ 
        if(keyCode == 71){  
            console.log(keyCode);
            // Do something... 
            showGetSource();
            state.key  = null;   // Clrear Alt   
        }       
    }
})



$('body').on('click','button.selected-rc-list', function(){
    let id = $(this).closest('tr').attr('data-id');
    let po = $(this).closest('tr').attr('data-po');
    fetch("?r=accounting/payment/push-received-line", {
        method: "POST",
        body: JSON.stringify({id:id, po:po}),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
        }
    })
    .then(res => res.json())
    .then(response => {

        $('input[name="ext_document"]').val(response.po.no).attr('data-id', response.po.id);
        let data = localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
        // Put data to line for editable    
        response.raw.map(model => {        
            let unit = model.measure.filter(u => u.selected ? u.name : null);            
                data.push({
                    id: model.item,
                    code: model.code,
                    name: model.name,
                    qty: model.qty,
                    price: model.price,
                    unit: unit[0].name,
                    measure: unit[0].id,
                });
        })

        localStorage.setItem('payment-line',JSON.stringify(data));
        $('#modal-get-source').modal('hide');
        
        $('body').find('#Payment-Line').html(renderLineTable(data));
        setTimeout(() => {
            $('body').find('input[name="search-code"]').select().focus();
        }, 500);
         

    })
    .catch(error => {
        console.log(error);
    });
});

JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  