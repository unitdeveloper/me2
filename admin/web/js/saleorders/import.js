

$('body').on('change','input[type="file"]',function(e){
    $('body').prepend('<div style="position: absolute; left:50%; top:40%; z-index:1100;"> <i class="fas fa-spinner fa-4x fa-spin"></i> </div>');
    $('body').find('label[id="file-input"]').slideUp();
    soundCamera.play();
    localStorage.removeItem('new-sale-line');
    localStorage.removeItem('create-order-wiz');
    sessionStorage.removeItem('data');
    setTimeout(() => { $('form#import-file').submit(); },1000 );
})


var data = [];
let store   = 0;
 

let findCompany = (callback) =>{

    let store       = 1;
    let endpage     = 2;
    let storename   = '';
    let po          = '';
    let total       = 0;

    $('#pdf-content').children('div').map((key,el) => {
           $(el).find('p').map((i,p) => { // List all <p> tag 
               let str = $(p).html();
                if(str.search("60004-CRC") > -1){
                    store       = 1;
                    storename   = 'CRC Thai Watsadu Limited';
                    $(p).css('background','gray');                       
                }else if(str.search("สยามโกลบอลเฮ้าส์") > -1){
                    store       = 2;                  
                    storename   = 'บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่';
                    $(p).css('background','gray');  
                }else if(str.search("0115545007325") > -1){ // // ฮาร์ดแวร์เฮาส์  
                    store       = 3;    
                    storename   = 'บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)';
                    $(p).css('background','gray');                
                }else if(str.search("โฮมฮับ") > -1){
                    store       = 4;
                    storename   = 'บริษัท โฮมฮับ จํากัด';
                    $(p).css('background','transparence');  
                }else if(str.search("501383") > -1){
                    store       = 5;
                    storename   = 'บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)';
                    $(p).css('background','gray');  
                }else if(str.search("บริษัท เมกา โฮม") > -1){
                    store       = 6;
                    storename   = 'บริษัท เมกา โฮม เซ็นเตอร์ จํากัด';
                    $(p).css('background','gray');  
                } 
                // หาจำนวนหน้า
                if(str.search("єѬјзҕѥѝѧьзҖѥ") > -1){
                    // CRC Thai Watsadu Limited
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                        total   = Number($(p).prevAll().slice(0, 1).text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }else if(str.search("มูลค่ารวมทั้งสิ้น") > -1){ 
                    // บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)
                    $(p).next().css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                        total   = Number($(p).next().text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }else if(str.search("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7%") > -1){
                    // บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่
                    $(p).nextAll().slice(2, 3).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                        total   = Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }else if(str.search("จํานวนเงินทังสิน") > -1){
                    // บริษัท โฮมฮับ จํากัด
                    $(p).nextAll().slice(3, 4).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'font-size': '20px'
                        });
                        total   = Number($(p).nextAll().slice(3, 4).text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }else if(str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1){
                    // บริษัท เมกา โฮม เซ็นเตอร์ จํากัด
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                        total   = Number($(p).prevAll().slice(0, 1).text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }else if(str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1){
                    // บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)
                    $(p).prevAll().slice(0, 1).css({
                        'background-color': 'rgb(0, 224, 247)',
                        'padding': '5px !important'
                        });
                        total   = Number($(p).prevAll().slice(0, 1).text().replace(/[^0-9\.-]+/g,""));
                        endpage = $(p).closest('div').index();
                        if(endpage > 2){ endpage = endpage - 1 }
                }
            })
    })
    callback({
        store:  store,
        page:   endpage <=0 ? 1 : endpage,
        name:   storename,
        po:     po,
        total:  total,
        vat:    "7",
        incvat: "1",
        date:   $("body").find("#saleheader-order_date").val(), 
        inv:    $("body").find("#saleheader-invoice_no").val(),
        remark: $("body").find("#saleheader-remark").val()       
    });
}

$(document).ready(function(){
    let data = localStorage.getItem('new-sale-line')? JSON.parse(localStorage.getItem('new-sale-line')) : [];
    if(data.length > 0){
        // ถ้ามีรายการ Import file มาแล้วให้แสดงในตาราง
        renderTable(data);
        
        let orders = localStorage.getItem('create-order-wiz')? JSON.parse(localStorage.getItem('create-order-wiz')) : [];
        if(orders.line ? orders.line.length > 0 : false){
            // ถ้า สร้าง invoice แล้วให้ข้ามขั้นตอนได้
            // ไปยังหน้าถัดไป (แก้ไขรายการ)
            setTimeout(() => {
                let active = $(".wizard .nav-tabs li.active");
                    active.next().removeClass("disabled");
                nextTab(active);
            }, 1500);
            // ปิด(10) ไม่อนุญาตการส่งข้อมูลเมื่อคลิก next
            $('body').find('button#btn-create-sale-line')
            .removeClass('create-sale-line')
            .removeClass('btn-warning-ew text-warning')
            .addClass('btn-success-ew text-success');
            //renderTableReadonly(orders.line);
            explodeBom(orders.stock);

            $("body").find('#saleheader-invoice_no').val(orders.inv_no).attr('data-id',orders.inv_id);
            $("body").find('.SALEORDER-NUMBER').html(orders.order_no)
            .attr('data-id',orders.order_id)
            .attr('href', '?r=SaleOrders%2Fsaleorder%2Fprint&id='+orders.order_id+'&footer=1');

            $("body").find('.INVOICE-NUMBER')
            .html(orders.inv_no)
            .attr('href', '?r=accounting%2Fposted%2Fprint-inv&id='+btoa(orders.inv_id)+'&footer=1');

             
            

        }
    }else {
        findCompany(res => {    
            store = res.store;
            $('#pdf-content').children('div').map((key,el) => {
            
                if(key < (res.page)){ 
                    $(el).find('p').map((i,p) => { // List all <p> tag 
                        let str = $(p).html(); 
                            switch (store) {
                                case 1: // Home work ไทวัสดุ

                                    // ค้นหา PO
                                    if(str.search("P/O Number") > -1){ 
                                        // CRC Thai Watsadu Limited
                                        $(p).next().next().css('background','pink');
                                        res = Object.assign({}, res, { po: $(p).next().next().text() });
                                    }
                                    
                                    if(str.search("8859042") > -1){ // find string 885 from p
                                        $(p).css('background','#51ff00');
                                        $(p).nextAll().slice(4, 5).css('background','red');
                                        $(p).nextAll().slice(10, 11).css('background','orange');

                                        let qty     = Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,""));
                                        let sumline = Number($(p).nextAll().slice(10, 11).text().replace(/[^0-9\.-]+/g,""));
                                        let price   = sumline / qty;

                                        data.push({
                                            item:     $(p).text(),
                                            qty:      qty,
                                            price:    Number(price.toFixed(4)),
                                            sumline:  sumline,
                                            discount: 0
                                        });
                                    }
                                break;

                                case 2: // GOBAL HOUSE
                                    
                                    if(str.search("PODC") > -1){ // ค้นหา PO
                                        $(p).first().css('background','pink');
                                        res = Object.assign({}, res, { po: $(p).first().text() });
                                    }

                                    if(str.search("8859042") > -1){ // find string 885 from p
                                        $(p).css('background','#51ff00');
                                        $(p).nextAll().slice(3, 4).css('background','red');
                                        $(p).nextAll().slice(6, 7).css('background','orange');

                                        let qty     = Number($(p).nextAll().slice(3, 4).text().replace(/[^0-9\.-]+/g,""));
                                        let sumline = Number($(p).nextAll().slice(6, 7).text().replace(/[^0-9\.-]+/g,""));
                                        let price   = sumline / qty;

                                        data.push({
                                            item:     $(p).text(),
                                            qty:      qty,
                                            price:    Number(price.toFixed(4)),
                                            sumline:  sumline,
                                            discount: 0
                                        });
                                    }
                                break;

                                case 3: // HARDWARE HOUSE
                                    
                                    if(str.search("เลขที่เอกสาร :") > -1){ // ค้นหา PO
                                        $(p).next().css('background','pink');
                                        res = Object.assign({}, res, { po: $(p).next().text() });
                                    }

                                    if(str.search("8859042") > -1){ // find string 885 from p
                                        $(p).css('background','#51ff00');
                                        $(p).nextAll().slice(2, 3).css('background','red');
                                        $(p).nextAll().slice(4, 5).css('background','orange');

                                        data.push({
                                            item:     $(p).text(),
                                            qty:      Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,"")),
                                            price:    Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,"")) / Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g,"")),
                                            sumline:  Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g,"")),
                                            discount: 0
                                        });
                                    }
                                break;

                                case 4: // HOME HUB
                                    
                                    if (str.search("วันทีกําหนดส่ง") > -1) {
                                        // ค้นหา PO
                                        $(p).prevAll().slice(2, 3).css("background", "pink");
                                        let text  = $(p).html().split('</b>');                      
                                        let text2 = $(p).prevAll().slice(1, 2).text();
                                        let text3 = $(p).prevAll().slice(0, 1).text();
                
                                        let remark = text[1].replace(/&nbsp;/g,' ').replace('(สาขาที )','(สาขาที 3)').trim() 
                                        + "\r\n" +  text2.replace('      ',' ').replace('หมู่ที','111 หมู่ที 12').trim() 
                                        + " " + text3.trim();
                                        
                    
                                        let po      = $(p).prevAll().slice(2, 3).html(); // เอา html ออกมาแยก <br>
                                        let onlyPo  = po.split("<br>"); // แยกอักษรออกจากวันที่ (ขึ้นด้วย <br>)
                                    
                                        res = Object.assign({}, res, { po: onlyPo[0], remark:remark });
                                    }
                
                                    if (str.search("8859042") > -1) {
                                        // find string 885 from p
                
                                        let price   = 0;
                                        let qty     = 0;
                                        let sumline = 0;
                                        let discount= 0;
                                        let total   = 0;
                                        let dis     = 0;
                                        let rows    = $(p).prevAll().slice(4, 5).text();
                                        //console.log(rows);
                                        $(p).css("background", "#51ff00");
                                        // ถ้าเจอเครื่องหมาย % ในตัวเลขแสดงว่าเป็นเปอร์เซ็นส่วนลด
                                        if(rows.search('%') > -1){
                                        
                                        qty     = Number($(p).prevAll().slice(2, 3).text().replace(/[^0-9\.-]+/g, ""));
                                        price   = Number($(p).prevAll().slice(5, 6).text().replace(/[^0-9\.-]+/g, ""));
                
                                        // ถ้าไม่เจอมูลค่าให้ถอยกลับไป 1 p
                                        // เนื่องจากหน่วย มีตัวอักษรยาว tag p จะถูกรวมกัน ทำให้ tag p หายในในบรรทัดนั้น
                                        if(price > 0){
                                            $(p).prevAll().slice(2, 3).css("background", "red");
                                            $(p).prevAll().slice(3, 4).css("background", "orange");
                                            $(p).prevAll().slice(4, 5).css("background", "green");
                                            price   = Number($(p).prevAll().slice(3, 4).text().replace(/[^0-9\.-]+/g, ""));
                                            discount= Number($(p).prevAll().slice(4, 5).text().replace(/[^0-9\.-]+/g, ""));
                                        }else{
                                            qty     = Number($(p).prevAll().slice(1, 2).text().replace(/[^0-9\.-]+/g, ""));
                                            price   = Number($(p).prevAll().slice(2, 3).text().replace(/[^0-9\.-]+/g, ""));
                                            discount= Number($(p).prevAll().slice(3, 4).text().replace(/[^0-9\.-]+/g, ""));
                                            $(p).prevAll().slice(1, 2).css("background", "red");
                                            $(p).prevAll().slice(2, 3).css("background", "orange");
                                            $(p).prevAll().slice(3, 4).css("background", "green");
                                        }
                
                                        total       = qty * price;
                                        dis         = (discount / 100) * total;
                                        sumline     = (total) - dis;
                
                                        }else {                                                 
                
                                            $(p).nextAll().slice(2, 3).css("background", "blue");
                                            $(p).nextAll().slice(4, 5).css("background", "orange"); // Quantity
                                            $(p).nextAll().slice(3, 4).css("background", "green");
                                            $(p).nextAll().slice(0, 1).css("background", "red"); 
                    
                                            qty     = Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g, ""));
                                            sumline = Number($(p).nextAll().slice(0, 1).text().replace(/[^0-9\.-]+/g, ""));
                                            discount= 0;                        
                                            price   = sumline / qty;
                                            
                                        }
                
                                        data.push({
                                        item: $(p).text(),
                                        qty: qty,
                                        price: price,
                                        sumline: sumline,
                                        discount: discount
                                        });
                                    }
                                break;


                                case 5: // HOME PRO
                                    
                                    if(str.search("PO #") > -1){ // ค้นหา PO
                                        $(p).first().css('background','green');    
                                        $(p).prev().css('background','pink');  
                                        res = Object.assign({}, res, { po: $(p).prev().text(), id:251 });       
                                                        
                                    }

                                    if(str.search("/1 EA") > -1){ // find string 885 from p

                                        $(p).prevAll().slice(1, 2).css('background','#51ff00');
                                        $(p).next().css('background','red');                            
                                        $(p).nextAll().slice(1, 2).css('background','orange');

                                        let qty     = Number($(p).next().text().replace(/[^0-9\.-]+/g,""));
                                        let sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g,""));
                                        let price   = sumline / qty;

                                        data.push({
                                            item:     $(p).prevAll().slice(1, 2).text(),
                                            qty:      qty,
                                            price:    Number(price.toFixed(4)),
                                            sumline:  sumline,
                                            discount: 0
                                        });
                                    }
                                break;

                                case 6: // MAGA HOME
                                    
                                    if(str.search("PO #") > -1){ // ค้นหา PO 
                                        $(p).prev().css('background','pink');   
                                        res = Object.assign({}, res, { po: $(p).prev().text() });                        
                                    }

                                    if(str.search("/1 EA") > -1){  

                                        $(p).prevAll().slice(1, 2).css('background','#51ff00');
                                        $(p).next().css('background','red');                            
                                        $(p).nextAll().slice(1, 2).css('background','orange');

                                        let qty     = Number($(p).next().text().replace(/[^0-9\.-]+/g,""));
                                        let sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g,""));
                                        let price   = sumline / qty;

                                        data.push({
                                            item:     $(p).prevAll().slice(1, 2).text(),
                                            qty:      qty,
                                            price:    Number(price.toFixed(4)),
                                            sumline:  sumline,
                                            discount: 0
                                        });
                                    }
                                break;

                                default:
                                    break;
                            }
                        
                    })
                }else{
                    $(el).closest('div').remove();
                }
            })
            // เก็บข้อมูลไว้ตรวจสอบในภายหลัง
            localStorage.setItem('sale-header',JSON.stringify(res));    
            sessionStorage.setItem('data',JSON.stringify(data));         
    
            let headers = {
                    header: res,
                    customer: localStorage.getItem('customer') ? JSON.parse(localStorage.getItem('customer')) : []
                };
                
            if(headers.customer.id){
                fetch("?r=SaleOrders/wizard/load-data", {
                        method: "POST",
                        body: JSON.stringify({line:data,headers:headers}),
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
                        },
                    })
                    .then(res => res.json())
                    .then(response => {        
                        // RENDER TABLE
                        renderTable(response.item);
                        localStorage.setItem('new-sale-line',JSON.stringify(response.item));   
                        if(response.po.exists){
                            swal(response.po.message, response.po.inv_no, "warning");
                        }                 
                    })
                    .catch(error => {
                        console.log(error);
                    });
            }
        }); // findCompany

    }
});


let renderTable = (data) => {
    let html = `
        <table class="table table-bordered" id="sale-line-table" style="font-family:roboto;">
            <thead class="bg-gray">
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:200px;">item</th>
                    <th>name</th>
                    <th class="text-right" style="width:150px;">Quantity</th>
                    <th class="text-right" style="width:150px;">Unit Price</th>
                    <th class="text-right" style="width:150px;">Discount(%)</th>
                    <th class="text-right" style="width:150px;">Total</th>
                    <th class="text-center" style="width:50px;"> - </th>
                </tr>
            </thead>
            <tbody>
    `;

    let i = 0;
    let sum = 0;
    data.length > 0 ? data.map((model,key) => {
        i++;
        let lineDiscount    = model.discount ? (model.discount * 1) : 0;
        let totals          = (model.qty * model.price) - ((lineDiscount /100) * (model.qty * model.price));
        let total           = Number((totals).toFixed(2));
        let price           = model.price;
            sum+= total;
        
        if(model.status===true){ 

            
            
            html+= `
                    <tr data-key="${model.id}" data-row="${key}">
                        <td>${i}</td>
                        <td class="text-left"><input type="text" class="form-control"  name="qty" readonly value="${model.code}" autocomplete="off"/></td>
                        <td><input type="text" class="form-control"  name="name" readonly value="${model.name}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="qty" value="${model.qty}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="price" value="${Number(price > 0? price.toFixed(2) : 0)}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="discount" value="${lineDiscount}" autocomplete="off"/></td>
                        <td class="text-right">${number_format(total.toFixed(2),2)}</td>
                        <th class="text-right" style="width:50px;"><button type="button" class="btn btn-danger btn-xs delete-line"><i class="far fa-trash-alt"></i></button></th>
                    </tr>
            `;
        }else{
            
            html+= `
                    <tr class="bg-danger" data-row="${key}">
                        <td>${i}</td>
                        <td class="text-left"><input type="text" class="form-control text-red" readonly  name="qty" value="${model.code}" autocomplete="off"/></td>
                        <td><input type="text" class="form-control text-red"  name="name" readonly value="${model.name}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="qty" value="${model.qty}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="price" value="${Number(price > 0? price.toFixed(2) : 0)}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="discount" value="${lineDiscount}" autocomplete="off"/></td>
                        <td class="text-right">${number_format(total.toFixed(2),2)}</td>
                        <th class="text-right" style="width:50px;"><button type="button" class="btn btn-danger btn-xs delete-line"><i class="far fa-trash-alt"></i></button></th>
                    </tr>
            `;
        }

    }): null ;
    
    html+= `
                <tr class="bg-gray" data-row="0">
                    <td class="text-right"><i class="fas fa-arrow-right"></i></td>
                    <td><input type="text" class="form-control" name="add-code" /></td>
                    <td><input type="text" class="form-control" name="add-name" /></td>
                    <td><input type="number" class="form-control text-right"  name="add-qty" autocomplete="off" /></td>
                    <td><input type="number" class="form-control text-right"  name="add-price" autocomplete="off" /></td>
                    <td><input type="number" class="form-control text-right"  name="add-discount" autocomplete="off" /></td>
                    <td><input type="number" class="form-control text-right"  name="add-totalprice" autocomplete="off" /></td>
                    <td class="text-right"><button type="button" class="btn btn-info btn-xs hidden enter-line"><i class="far fa-arrow-alt-circle-right"></i></button></td>
                </tr>
            `;


    html+= `</tbody>
            </table>`;

    setHeader();


    let totals      = (sum, html, callback) => {
        let header      = JSON.parse(localStorage.getItem('sale-header'));
        let total, vat, vatTotal, textTotal, vat_revert, sumTotal;
        if(header.incvat==="0"){
            //vat ใน
            vat_revert  = (header.vat/100) +1;        
            vat         = sum - (sum / vat_revert);
            sumTotal    = number_format((sum - vat).toFixed(2));
            textTotal   = number_format(sum.toFixed(2));
            let diff    = Number(sum.toFixed(2)) - header.total;
            total       = diff ===0 ? '<span class="text-success">'+textTotal+'</span>' : '<span class="text-red blink">'+textTotal+'</span>';
        }else{
            // vat นอก
            vat         = (sum * header.vat) /100;
            vatTotal    = vat + sum;
            textTotal   = number_format(vatTotal.toFixed(2));
            // ถ้าผลรวมไม่เท่ากัน ให้แสดง text สีแดง
            let diff        = Number(vatTotal.toFixed(2)) - header.total;
                total       = diff ===0 ? '<span class="text-success">'+textTotal+'</span>' : '<span class="text-red blink">'+textTotal+'</span>';
            // ถ้ายอดไม่ตรง อาจจะเป็น vat ใน (ลองตรวจกับยอด sum)
            let textSum     = number_format(sum.toFixed(2));
            // ถ้าตรงกันอยู่แล้ว ไม่ต้องไปเช็คต่อ
            if(diff===0){
                sumTotal    = textSum;
            }else{
                let diff_vat= Number(sum.toFixed(2)) - header.total;
                sumTotal    = diff_vat ===0 ? '<span class="text-success">'+textSum+'</span>' : '<span class="text-red blink">'+textSum+'</span>';
                total       = textTotal; // ไม่ต้องแสดงความผิดพลาด (vat ใน ยอดนี้ไม่ใช่อยู่แล้ว)
            }
    }

// ไม่ให้กด tab หลักจากแก้รายการ(คลิก next เท่านั้น)
$(".wizard .nav-tabs li.active").next().addClass("disabled");
// เปิด(10) อนุญาตการส่งข้อมูลเมื่อคลิก next
$('body').find('button#btn-create-sale-line').addClass('create-sale-line').addClass('text-warning btn-warning-ew');

callback({
    total:  sumTotal,
    vat:    number_format(vat.toFixed(2)),
    grand:  total,
    html:   html,
    _vat:   header.vat,
    _incvat: header.incvat
});

}
    
    totals(sum, html, res => {
        $('body').find('#saleheader-include_vat').val(res._incvat);
        $('body').find('#saleheader-vat_percent').val(res._vat);
        
        $('body').find('#get-sum-total').html(res.total);
        $('body').find('#get-sum-vat').html(res.vat);
        $('body').find('#get-grand-total').html(res.grand);

        $('body').find('div.renders').html(res.html);
    })
}


let tableTotals = (sum, html, callback) => {
    let header      = JSON.parse(localStorage.getItem('sale-header'));
    let total, vat, vat_revert, sumTotal;

    if(header.incvat==="0"){
        //vat ใน
        vat_revert  = (header.vat/100) +1;        
        vat         = sum - (sum / vat_revert);
        sumTotal    = number_format((sum - vat).toFixed(2));
        total       = number_format(sum.toFixed(2));
    
    }else{
        // vat นอก
        vat         = (sum * header.vat) /100;
        sumTotal    = number_format(sum.toFixed(2));
        total       = number_format((vat + sum).toFixed(2));
    }

    callback({
        total: sumTotal,
        vat: number_format(vat.toFixed(2)),
        grand: total,
        html: html,
        _vat: header.vat,
        _incvat: header.incvat
    });

}


let renderTableReadonly = (data) => {

    let html = `
        <table class="table table-bordered" id="sale-line-table" style="font-family:roboto;">
            <thead class="bg-gray">
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:200px;">item</th>
                    <th>name</th>
                    <th class="text-right" style="width:150px;">Quantity</th>
                    <th class="text-right" style="width:150px;">Unit Price</th>
                    <th class="text-right" style="width:150px;">Discount(%)</th>
                    <th class="text-right" style="width:150px;">Total</th>
                </tr>
            </thead>
            <tbody>
    `;

    let i = 0;
    let sum = 0;
    data.length > 0 ? data.map(model => {

        i++;
        let lineDiscount = model.discount ? (model.discount * 1) : 0;
        let totals  = (model.qty * model.price) - ((lineDiscount /100) * (model.qty * model.price));
        let total = Number((totals).toFixed(2));
        //let total = Number((model.qty * model.price).toFixed(2));
        sum+= total;
        html+= `
                <tr class="">
                    <td>${i}</td>
                    <td class="text-left">${model.code}</td>
                    <td>${model.name}</td>
                    <td class="text-right">${model.qty}</td>
                    <td class="text-right">${Number(model.price.toFixed(2))}</td>
                    <td class="text-right">${lineDiscount}</td>
                    <td class="text-right">${total}</td>
                </tr>
        `;
 
    }): null;
    
    html+= `</tbody>
            </table>`;




    tableTotals(sum, html, res => {
        $('body').find('#sum-total').html(res.total);
        $('body').find('#sum-vat').html(res.vat);
        $('body').find('#grand-total').html(res.grand);

        $('body').find('#renders-editable').html(res.html);
    })
}




 


let explodeFetch =  (data) => {
    let html = '';

    function compare( a, b ) {
        if ( a.line < b.line ){
            return -1;
        }
        return 0;
    }


    data.sort( compare );

    data.length > 0 ?  data.map(model => {
            if(model.length > 0 ) html+= explodeFetch(model);
            html+= `
                <tr class="bg-warning ${(model.message =='Output' ? 'text-success' : 'text-danger')}">
                    <td> </td>
                    <td class="text-left"><div class="${(model.message =='Output' ? ' ' : 'ml-10')}">${model.code}</div></td>
                    <td><div class="${(model.message =='Output' ? ' ' : 'ml-10')}">${model.name}</div></td>
                    <td class="text-right">${number_format(Number(model.qty),2)}</td>                    
                    <td class="text-right">${number_format(model.cost,2)}</td>
                    <td></td>
                    <td class="text-right">${number_format(Math.abs(model.qty * model.cost).toFixed(2),2)}</td>
                </tr>
            `;
            
    }): null;
                
    return html;
}
   

let explodeBom = (data) => {

    let html = `
        <table class="table table-bordered" id="sale-line-table" style="font-family:roboto;">
            <thead class="bg-gray">
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:200px;">item</th>
                    <th>name</th>
                    <th class="text-right" style="width:150px;">Quantity</th>
                    <th class="text-right" style="width:150px;">Unit Price</th>
                    <th class="text-right" style="width:150px;">Discount(%)</th>
                    <th class="text-right" style="width:150px;">Total</th>
                </tr>
            </thead>
            <tbody>
    `;
    

    let i = 0;
    let sum = 0;
    data.length > 0 ? data.map(model => {

        i++;
        let lineDiscount    = model.discount ? (model.discount * 1) : 0;
        let totals          = (model.qty * model.price) - ((lineDiscount /100) * (model.qty * model.price));
        let total           = Number((totals).toFixed(2));
        //let total   = Number((model.qty * model.price).toFixed(2));
        let price           = Number(model.price);
        let qty             = model.qty ? Number(model.qty) : 0;

        sum+= total;
        html+= `
                <tr class="bg-dark">
                    <td>${i}</td>
                    <td class="text-left">${model.show_code}</td>
                    <td>${model.name}</td>
                    <td class="text-right">${number_format(qty * -1,2)}</td>
                    <td class="text-right">${price}</td>
                    <td class="text-right">${lineDiscount}</td>
                    <td class="text-right">${number_format(total,2)}</td>
                </tr>
        `;

        function compare( a, b ) {
            if ( a.line < b.line ){
                return -1;
            }
            return 0;
        }
        
        model.production.sort( compare );

        model.production.length > 0  ? model.production.map(el => {
              
            if(el.length > 0 ) html+=  explodeFetch(el);

            if(el.code){
                html+= `
                    <tr class="bg-info ${(el.message =='Output' ? 'text-success' : 'text-danger')}">
                        <td> </td>
                        <td class="text-left"><div class="${(el.message =='Output' ? ' ' : 'ml-10')}">${el.code}</div></td>
                        <td><div class="${(el.message =='Output' ? ' ' : 'ml-10')}">${el.name}</div></td>
                        <td class="text-right">${Number(el.qty)}</td>
                        <td class="text-right">${el.cost}</td>
                        <td></td>
                        <td class="text-right">${number_format(Math.abs(el.qty * el.cost).toFixed(2),2)}</td>
                    </tr>
                `; 
            }
            
        }) : null;
 
    }): null;
    
    html+= `</tbody>
            </table>`;

    tableTotals(sum, html, res => {
        $('body').find('#sum-total').html(res.total);
        $('body').find('#sum-vat').html(res.vat);
        $('body').find('#grand-total').html(res.grand);

        $('body').find('#renders-editable').html(res.html);
    })
}
