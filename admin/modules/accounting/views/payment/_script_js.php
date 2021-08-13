<?php
$Yii    = 'Yii';
$id     = $model->isNewRecord ? 'null' : $model->id;
$today  = date('Y-m-d');
$js     =<<<JS

let state = {
    key: null
};

const totalSummary = (total) => {

  
    let before_discount     = total * 1;
    let percent_discount    = $('#percent_discount').val();
    let discount            = $('#discount').val() * 1;
    let subtotal            = total - (percent_discount ? ((before_discount * percent_discount)/ 100) : discount);
    let vat_percent         = $('#vat_percent').val() * 1;
    let include_vat         = $('#include_vat').val();
    let vat                 = (include_vat == 2) ? 0 : vat_percent;      
    let withholdingTax      = $('select#withholdtax').val(); 
    let inv_no              = $('input[name="no"]').val();
    let inv_date            = $('input[name="inv_date"]').val();
    let ext_doc             = $('input[name="ext_document"]').val();
 

    if(vat_percent >= 7){
        $('.show-vat-type').show('fast');
    }else{
        $('.show-vat-type').hide('fast');
        include_vat  = '1';
    }

     
    

    if(percent_discount){
        let disc    = (before_discount * percent_discount)/ 100;   
        $('body').find('input#discount').val(disc.toFixed(2));
    }
                      


    if(include_vat == 1){
        // Vat นอก
        var InCVat       = (subtotal * vat )/ 100;
        var beforeVat    = 0;
        var total        = (InCVat + subtotal);
        $('body').find('.before-vat-row').fadeOut('slow');
    }else {
        // Vat ใน
        // 1.07 = 7%
        var InCVat       = subtotal - (subtotal / 1.07);
        var beforeVat    = subtotal - InCVat;
        var total        = subtotal;
        $('body').find('.before-vat-row').fadeIn('slow');
    }

    
    $('body').find('.before-discount').html(number_format(before_discount.toFixed(2))).attr('value',before_discount);
    $('body').find('.after-discount').html(number_format(subtotal.toFixed(2)))
    $('body').find('#ew-after-vat').html(number_format(InCVat.toFixed(2)));
    $('body').find('.subtotal').html(number_format(total.toFixed(2)));
    $('body').find('#ew-before-vat').html(number_format(beforeVat.toFixed(2)));
    
    

    let witholdingValue     = (subtotal * withholdingTax) / 100;
    let grandTotalPayment = 0;

    if($('input#withholdtaxswitch').is(':checked')){
        $('.tax-toggle').fadeIn();
        afterWithholdtax        = (subtotal * withholdingTax)/100;
        grandTotalPayment       = total - ((subtotal * withholdingTax)/100);
        $('body').find('.grandTotalPayment').html(number_format(grandTotalPayment.toFixed(2))); 
        $('body').find('.after_withholdtax').html(number_format(afterWithholdtax.toFixed(2)));
    }else{
        $('.tax-toggle').fadeOut();
        withholdingTax      = 0;
        grandTotalPayment   = total - ((subtotal * withholdingTax)/100);
        $('body').find('.grandTotalPayment').html(number_format(grandTotalPayment.toFixed(2))); 
        $('body').find('.after_withholdtax').html(0)
    }
    
    localStorage.setItem('payment-header',JSON.stringify({
        inv_no: inv_no,
        inv_date: inv_date ? inv_date : $today,
        total: total,
        before_discount: before_discount,
        percent_discount: percent_discount,
        discount: discount,
        beforeVat: beforeVat,
        incVat: InCVat,
        vat: vat_percent,
        include_vat: include_vat * 1,
        inVat: include_vat == 1 ? false : true,  // vat ใน
        grandTotalPayment: grandTotalPayment,
        withholdingTax: withholdingTax * 1,
        remark : $('#remark').val(),
        ext_doc: ext_doc
    }));   

    // set to textbox
    $('input[name="amount"]').val(grandTotalPayment.toFixed(2)).attr('data-val', grandTotalPayment);
}


const inputForm = () => {
    let html = `<tr>
                    <td class="text-center"><i class="fas fa-hand-point-right fa-2x"></i></td>
                    <td colspan="2" class="has-success"><input type="text" name="search-code" class="form-control" placeholder="{$Yii::t('common','Search Item')}"/></td>                     
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>`;
    return html;
}

const renderLineTable = (data) => {
    let rows    = '';
    let total   = 0;

    data.map((model, key) => {
        total+= model.qty * model.price;
        rows+= `<tr data-key="` + key + `" data-id="` + model.id + `">
                    <td> ` + (key + 1) + ` </td>
                    <td class="font-roboto" > ` + model.code + ` </td>
                    <td class="text-left"> ` + model.name + ` </td>
                    <td class="text-right font-roboto"> ` + model.qty + ` </td>
                    <td class="text-center font-roboto"> ` + model.unit + ` </td>
                    <td class="text-right font-roboto"> ` + model.price + ` </td>
                    <td class="text-right font-roboto"> ` + number_format((model.qty * model.price).toFixed(2)) + ` </td>
                    <td class="text-center delete-row text-red pointer"><i class="fas fa-times"></i></td>
                </tr>`;
    })
    let html = `<table class="table table-bordered">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:50px;">#</th>
                            <th style="width:150px;">{$Yii::t('common','Code')}</th>
                            <th class="text-center" style="min-width:200px;">{$Yii::t('common','List')}</th>
                            <th class="text-right font-roboto" style="width:100px;">{$Yii::t('common','Quantity')}</th>
                            <th class="text-center" style="width:150px;">{$Yii::t('common','Unit')}</th>
                            <th class="text-right font-roboto" style="width:100px;">{$Yii::t('common','Price')}</th>
                            <th class="text-right font-roboto" style="width:150px;">{$Yii::t('common','Total')}</th>
                            <th style="width:50px;"> </th>
                        </tr>
                    </thead>
                    <tbody>
                        ` + rows + `
                        ` + inputForm() + `
                    </tbody>
                </table>`;

    totalSummary(total);
    if(data.length > 0){
        $('.footer-zone').show();
    }else{
        $('.footer-zone').hide();
    }
    return html;
}




const getApi = (obj, callback) => {

    fetch("?r=accounting/payment/line-ajax", {
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

 
$(document).ready(function(){
    setTimeout(() => {
        $('body').attr('style', ' ');         
    }, 800);
    
    if($id){
        getApi({
            id: $id 
        }, res => {
            $('body').find('#Payment-Line').html(renderLineTable(res.raw));
            localStorage.setItem('payment-line',JSON.stringify(res.raw));
            setTimeout(() => {
                $('body').find('input[name="search-code"]').select().focus();
            }, 500);
        })
    }else{
        let data = localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
        $('body').find('#Payment-Line').html(renderLineTable(data));
        setTimeout(() => {
            $('body').find('input[name="search-code"]').select().focus();
        }, 500);
    }


    if(localStorage.getItem("vendors")){
        let vendor = JSON.parse(localStorage.getItem("vendors"));
        $('.vendor-name').html(vendor.name);
        $('.vendor-code').html(vendor.code);
        $('a.vendor-code').attr('href', `index.php?r=vendors%2Fvendors%2Fview&id=` +vendor.id);

        $('.content-step-vendor').hide();
        $('.content-step-edit').show();
        $('.next-to-create-line').show();
    }else{
        $('.content-step-vendor').show();
        $('.content-step-edit').hide();
        $('.next-to-create-line').hide();
    }
});


$('body').on('click', 'td.delete-row', function(){
    if(confirm("{$Yii::t('common','Do you want to delete ?')}")){    
        let thisKey = $(this).closest('tr').attr('data-key');
        let data    = localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
            data    = data.filter((model, key) => key != thisKey ? model : null);
            localStorage.setItem('payment-line',JSON.stringify(data));
            $('body').find('#Payment-Line').html(renderLineTable(data));
            setTimeout(() => {
                $('body').find('input[name="search-code"]').select().focus();
            }, 500);
    }else{
        setTimeout(() => {
            $('body').find('input[name="search-code"]').select().focus();
        }, 500);
    }
})

  
$('body').on("keypress",'input[type="text"], input[type="number"]', function(e) {
    // Disable form submit on enter.
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
      e.preventDefault();
      return false;
    }
});

 

const createLine = (obj) => {
    $('#modal-get-source').modal('hide');
    let data = localStorage.getItem('payment-line') ? JSON.parse(localStorage.getItem('payment-line')) : [];
    // Put data to line for editable    
    
    let name    = prompt("{$Yii::t('common','Name')}", obj.name);
    let qty     = prompt("{$Yii::t('common','Quantity')}", 1);   
    let price   = prompt("{$Yii::t('common','Price')}", obj.price);
 
    
    
    if(price != null){ 
            data.push({
                id: obj.id,
                code: obj.code,
                name: name,
                qty: qty,
                price: price,
                unit: obj.unit,
                measure: obj.measure
            });

        localStorage.setItem('payment-line',JSON.stringify(data));
        
        $('body').find('#Payment-Line').html(renderLineTable(data));
        setTimeout(() => {
            $('body').find('input[name="search-code"]').select().focus();             
        }, 500);
    }
}


const popupShowMultiItem = (data) => {
    let rows    = ``;
    
    data.length > 0
        ? data.map((model, key) => {

            let measure = ``;
            model.unit.map(u => {
                measure+= `<option value="` + u.id + `">` + u.name + `</option>`;
            })

            rows += `<tr data-key="` + key +`" data-id="` + model.id + `" data-price="` + model.lastprice + `">
                        <td class="image"><img src="` + model.pic  +`" style="max-width:50px;" /></td>
                        <td class="code" style="font-family:roboto;"><a href="index.php?r=items%2Fitems%2Fview&&id=` + model.id + `" target="_blank"> ` + model.code  +`</a></td>
                        <td class="name">` + model.name  +`</td>
                        <td><select class="form-control" name="unit_of_measure">` + measure + `</select></td>
                        <td class="text-center"><button type="button" class="selected-item btn btn-primary btn-flat"><i class="fas fa-check"></i> {$Yii::t('common','Select')}</button></td>
                    </tr>`;
            })
        : rows += `<tr><td colspan="3" class="text-center">{$Yii::t('common','No data')}</td></tr>`;

    let html = `<table class="table table-bordered">
                    <thead>
                        <tr class="bg-gray">
                            <th style="width:50px;"> </th>
                            <th>{$Yii::t('common','Code')}</th>
                            <th>{$Yii::t('common','Name')}</th>
                            <th style="width:100px;" >{$Yii::t('common','Unit')}</th>
                            <th style="width:95px;" class="text-center">{$Yii::t('common','Select')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        ` + rows + `
                    </tbody>
                </table>`;
            
    $('#modal-get-source').modal('show');
    $('#modal-get-source .modal-title').html(`{$Yii::t('common','Pick Items')}`);
    $('#modal-get-source .modal-body').html(html);
    setTimeout(() => {
        $('body').find('button.selected-item:first').focus();
    }, 500);


    $('#modal-get-source').on('hidden.bs.modal',function(){   
        setTimeout(() => {
            $('body').find('input[name="search-code"]').focus().select();
        }, 500);
        $('#modal-get-source .modal-body').html(' '); 
    })

}

$('body').on('click','button.selected-item', function(){
    createLine({
        id: $(this).closest('tr').attr('data-id'),
        name: $(this).closest('tr').find('td.name').text(),
        code: $(this).closest('tr').find('td.code').text(),
        price: $(this).closest('tr').attr('data-price'),
        unit: $(this).closest('tr').find('select[name="unit_of_measure"]').text(),
        measure: $(this).closest('tr').find('select[name="unit_of_measure"]').val(),
        qty: 1
    });
})



$('body').on('keyup', 'input[name="search-code"]', function(e){
    let text    = $(this).val();
    let vendor  = JSON.parse(localStorage.getItem('vendors'));
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        fetch("?r=accounting/payment/find-items", {
            method: "POST",
            body: JSON.stringify({text:text, vendor:vendor.id}),
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
            }
        })
        .then(res => res.json())
        .then(response => {
            console.log(response);
            if(response.items.length > 1){
                popupShowMultiItem(response.items);
            }else{
                createLine({
                    id: response.items[0].id,
                    name: response.items[0].name,
                    code: response.items[0].code,
                    price: response.items[0].lastprice,
                    unit: response.items[0].unit[0].name,
                    measure: response.items[0].unit[0].id,
                    qty: 1
                });
            }
        })
        .catch(error => {
            console.log(error);
        });
    }
})




$('body').on('click','.back-to-pick-vendor', function(){
  $('.content-step-vendor').show();
  $('.content-step-edit').hide();  
  $('.content-step-success').hide();
});

$('body').on('click','.next-to-create-line', function(){
  $('.content-step-vendor').hide();
  $('.content-step-edit').show();
  $('.content-step-success').hide();
});


$('body').on('click','.next-to-finish', function(){
//   $('.content-step-vendor').hide();
//   $('.content-step-edit').hide();
//   $('.content-step-success').show();

  finishProcess();
});




JS;
$this->registerJs($js,\yii\web\View::POS_END);
?>
  