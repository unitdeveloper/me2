
<?php 
$id     = Yii::$app->request->get('id') ? Yii::$app->request->get('id') : 0;
$status = Yii::$app->request->get('status') ? Yii::$app->request->get('status') : 'Open';
$Yii    = 'Yii'; 
$js=<<<JS
 
     

 const renderTableInvoice = (obj) => {
     let body = ``;
     obj.raws.map((model, key) => {
         let total = model.total;
        body+=`
            <tr >              
                <td align="center"  style="border-left:0.05em solid #000; border-right:0.05em solid #000;">` + (key+1) + `</td>
                <td align="left"    style="border-left:0.05em solid #000;">` + model.code + `</td>
                <td align="left"    style="border-right:0.05em solid #000;">` + number_format(model.name) + `</td>
                <td align="right"   style="border-left:0.05em solid #000; border-right:0.05em solid #000;">` + number_format(model.qty) + `</td>
                <td                 style="border-right:0.05em solid #000;">` + number_format(model.measure) + `</td>
                <td align="right"   style="border-right:0.05em solid #000;">` + number_format(model.price) + `</td>
                <td align="right"   style="border-right:0.05em solid #000;">` + number_format(model.discount) + `</td>
                <td align="right"   style="border-right:0.05em solid #000;">` + number_format(total.toFixed(2)) + `</td>
            </tr>
        `;
     });

     $('#export_table tbody.render').html(body);

     setTimeout(() => {
        let name = $('body').find('a.ew-print-btn').attr('data-no');
        tableToExcel('export_table', 'IV', name+'.xls');
    }, 500);
 }

 const getInvoice = (obj, callback) => {
    fetch("?r=accounting/print/get-invoice", {
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

 $(document).ready(function(){

    
    let id      = "{$id}";
    let status  = "{$status}";

    getInvoice({id:id, status:status}, res => {
        renderTableInvoice(res);

        $('a.ew-print-btn').attr('data-no',res.header.no);
        $('.export-to-word').attr('data-no',res.header.no);

        //set Head
        let header = res.header.cust_vat + ' ' + res.header.cust_head;
        $('.cust-code').html(res.header.cust_code);
        $('.cust-name').html(res.header.cust_name);
        $('.cust-address').html(res.header.cust_address);
        $('.cust-tell').html(res.header.cust_tell);
        $('.cust-fax').html(res.header.cust_fax);
        $('.cust-transport').html(res.header.transport_by);
        $('.vat-regis').html(header);

        // set Head right
        let dueDate     =  res.header.payment_term == 0 ? '' : res.header.payment_due;
        let salePeople  =  '['+ res.header.sale_code +'] ' + res.header.sale_name + ' ' + res.header.sale_surname;
        $('.inv-no').html(res.header.no);
        $('.order-date').html(res.header.posting_date);
        $('.payment-due').html(res.header.payment_date);
        $('.due-date').html(dueDate);
        $('.so-no').html(res.header.ext_doc);
        $('.sale-name').html(salePeople);


        // set footer
        let sumline     = res.header.total.sumline * 1;
        let discount    = res.header.total.discount * 1;
        let percentdis  = res.header.total.percentdis * 1;
        let afterDis    = res.header.total.subtotal * 1;
        let percentVat  = res.header.total.vat * 1;
        let afterVat    = res.header.total.incvat * 1;
        let grandTotal  = res.header.total.total * 1;
        $('.total').html(number_format(sumline.toFixed(2)));
        $('.percent-discount').html(percentdis+'%');
        $('.discount').html(number_format(discount.toFixed(2)));
        $('.after-discount').html(number_format(afterDis.toFixed(2)));
        $('.vat-percent').html(percentVat+'%');
        $('.after-vat').html(number_format(afterVat.toFixed(2)));
        $('.grand-total').html(number_format(grandTotal.toFixed(2)));
        $('.remark-text').html(res.header.remark);
        $('.text-baht').html(res.header.thaibaht);


       
    })
 });

 $('body').on('click', 'a.ew-print-btn', function(){
    let name = $(this).attr('data-no');
    tableToExcel('export_table', 'IV', name+'.xls');
 })

 $('body').on('click','#export_word', function(){
    let name = $(this).attr('data-no');
    downloadWord(name);
 })
 
JS;
$this->registerJS($js,\yii\web\View::POS_END);
 
?>
 