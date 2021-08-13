/*!
* EWIN
* v4.06.07-rebuild - 2019
* (c) Assawin.Thkch; MIT License
*/

$( document ).ready(function() {



    var random = 0;
    route('index.php?r=SaleOrders/ajax/menu-random','GET',{param:{data:random}},'ResourceItemSearch');
    LoadAjax();



    if($('#salequoteheader-vat_percent').val() > 0 ) // Vat
    {
        $('#Exc-vat').show('fast');

    }else {
        $('#Exc-vat').hide('fast');

    }


    if(Number($('#ew-line-total').attr('data'))<=0)
    {
        $('.ew-sum-line').hide();
    }else {
        $('.ew-sum-line').show();
    }





});



(function($, window, document, undefined){
    $("#salequoteheader-payment_term").on("change", function(){

       var today = new Date();
       var date = new Date(today),
           days = parseInt($("#salequoteheader-payment_term").val(), 10);

        if(!isNaN(date.getTime())){
            date.setDate(date.getDate() + days);

            $('input[id="salequoteheader-paymentdue"]').val(date.toInputFormat());
        } else {
            alert("Invalid Date");
        }
    });


    //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]); // padding
    };
})(jQuery, this, document);




function LoadAjax(){

    //$('.ew-save-common').attr('onclick','$(\'#Form-Quotation\').submit()'); // ย้ายไป J action_script.js แล้ว

    // $('.InsertItem').focus();
    // $('.InsertItem').hide();
    // $('.ew-desc').hide();
    // $('.ew-qty').hide();
     $('.ew-add').hide();
    // $('.ew-price').hide();
    // $('.ew-type').hide();

    // js/item-picker.js
    loadFindBox('.render-search-item');

}


$('body').on('change','#salequoteheader-sales_people',function(){

//$('form#Form-Quotation').change('select#saleheader-status',function(e){


    var $status = $('#salequoteheader-status').val();


    if($('#salequoteheader-customer_id').val()==''){

        if($.inArray($status,['Open','Cancel']) < 0){
            $('#salequoteheader-status').val('Open');
            swal(
              'ดูเหมือนว่า "ยังไม่ได้เลือกลูกค้า"',
              'กรุณาเลือกลูกค้า',
              'warning'
            );

            return false;
        }


        //console.log($.inArray($(this).val(),['Open','Cancel']));

    }
});





$('#Form-Quotation').on('keypress', function(e) {

// Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});

 

$('body').on('click','#ewSelect',function(){

    var that = this;
    $(that).find('i').attr('class','fas fa-sync-alt fa-spin');
    $(that).attr('disabled',true); 


    CreateBom($('.ew-Code').attr('ew-post-param'));



    var itemno = $('.ew-Validate').attr('data-key');
    var unit_price = $('input[name=Price]').val();

    if(unit_price==0)
    {
        //alert('ไม่มีราคา');

    }

    var data = {param:{
        itemno:itemno,
        orderno:$('#salequoteheader-no').val(),
        itemset:$('#itemset').val(),
        soid:$('#SaleOrder').attr('ew-so-id'),
        amount:$('input[name=Quantity]').val(),
        price:$('input[name=Price]').val(),
        discount:$('input[name=Discount]').val(),
     }};
    
     $.ajax({
        url:'index.php?r=SaleOrders/quotation/create_saleline',
        type:'POST',
        data:data,
        success:function(res){
            $('.SaleLine').html(res);  

            //Close Modal
            $('#PickItem-Modal').modal('hide');

            $('body').attr('style','overflow:auto;');
            $("#ewSelect" ).hide();

            getSumLine($('#ew-discount-amount').val(),'discount');

            $('html, body').animate({ scrollTop: $('.grid-view').offset().top - 80 },500);
            LoadAjax();

            $(that).find('i').attr('class','fa fa-check');
            $(that).attr('disabled',false);
        }
    }); 
    //alert(itemno);
    //route("index.php?r=SaleOrders/quotation/create_saleline",'POST',data,'SaleLine');

   
    



});



$('body').on('click','.ew-PickItem',function(){
//$('.PicItem').click(function(){

    $('body').find('.loading-absolute').fadeIn(); 
    var data = { param:{
                        itemid: $(this).closest('div.item-groups').attr('data-key'),
                        itemno:$(this).attr("itemno"),
                        orderno:$('#salequoteheader-no').val(),
                        pset:$(this).attr("itemset"),
                        itemset:$(this).attr("itemset")
                }};

    //$('#Smooth-Ajax').hide('fast');


    if($(this).attr('ew-bom')==='enabled'){

       $.ajax({
            url:"index.php?r=Itemset/bomset/view&id="+$(this).attr("itemset"),
            type: "POST",
            data: data,
            async:true,
            success:function(getData){
                $('.ew-create-item').html(getData);
                $("#PickToSaleLine" ).hide();
                setTimeout(() => {
                    $('body').find('.loading-absolute').fadeOut('slow');
                    $('#PickItem-Modal').modal('show');
                }, 500); 
            }
        });

 
    }else {
        //route("index.php?r=SaleOrders/quotation/viewitem",'POST',data,'ew-create-item'); // render _modal_pickitem
        $.ajax({
            url:"index.php?r=SaleOrders/saleorder/viewitem&from=sq",
            type: "POST",
            data: data,
            async:true,
            success:function(getData){
                $('.ew-create-item').html(getData);

                let itemno = $("#itemno").val();
                let id    = data.param.itemid;

                loadItem({no:itemno, id:id});
                $("#PickToSaleLine" ).hide();
                setTimeout(() => {
                    $('body').find('.loading-absolute').fadeOut('slow');
                    $('#PickItem-Modal').modal('show');
                }, 500); 
            }
        });        
    }
 


    $('.modal-title').html($(this).attr('ew-set-name'));

    $('body').attr('style','overflow:hidden; margin-right:0px;');


});



$('body').on('mouseout','.ItemGrid',function(){

    $(".btn-detail-group").css("visibility", "hidden");
});

$('body').on('change','#salequoteheader-vat_percent',function(){
    if($(this).val() > 0) // Vat
    {
        $('#Exc-vat').show('fast');

    }else {
        $('#Exc-vat').hide('fast');
    }
    //console.log($(this).val());
    getSumLine(Number($('input[id="ew-discount-amount"]').val()),'discount');

});

$('body').on('change','#salequoteheader-include_vat',function(){

    getSumLine(Number($('input[id="ew-discount-amount"]').val()),'discount');

});



$('body').on('click','.RemoveSaleLine,a.delete-btn',function(){

    var that    = this;
    var itemno  = $(this).attr('href');
    var id      = itemno.substring(1);
    var orderno = $('#SaleOrder').attr('ew-so-id');
    var alt     = $(this).attr('alt');

    var data    = { param:{
        lineno:id,
        orderno:orderno
    }};

    //var tr = $(this).closest('tr');
    var tr = $('tr[data-key="'+itemno.substring(1)+'"]');



    // ----- Do confirm delete.-----
    if (confirm('ต้องการลบรายการ "' + alt + '" ?')) {


        $.ajax({
            url:'index.php?r=SaleOrders/quotation/delete_line',
            type:'POST',
            data:data,
            async:true,
            success:function(getData){
                //$('.Navi-Title').html(getData);
                if($(that).attr('class') !== 'RemoveSaleLine'){
                    $("body").css({ overflow: 'inherit' });
                    $('.item-detail').toggle("slide", { direction: "right" }, 500);
                }
                

                tr.css("background-color","#aaf7ff");
                tr.fadeOut(500, function(){
                    tr.remove();
                });
        
                LoadAjax();
        
                getSumLine($('#ew-discount-amount').val(),'discount');
        
                if(Number($('#ew-line-total').attr('data'))<=0)
                {
                    $('.ew-sum-line').hide();
                }else {
                    $('.ew-sum-line').show();
                }
            }
        });


        

    }
    // ----- /. Do confirm delete.-----

        
    


  return false;

});


$('body').on('change','#ew-text-editor',function(i,el){

        var $div    = $.trim($(this).parent('div').parent('td').attr('class'));
        var input   = $(this);

        if($div === 'ew-sl-qty text-right')
        {

            var pre     = $(this).parent('div').parent('td').parent('tr').find('a.RemoveSaleLine').attr('qty');
            var revert  = '<div id="ew-qty-edit" ew-line-no="'+input.attr('ew-lineno')+'">'+pre+'</div>';

        }else {

            var pre     = $(this).parent('div').parent('td').parent('tr').find('a.RemoveSaleLine').attr('price');
            var revert  = '<div id="ew-price-edit" ew-line-no="'+input.attr('ew-lineno')+'">'+pre+'</div>';

        }




       // alert('กำลังทำการเปลี่ยนแปลงข้อมูล');


        var data    = {param:{
                     orderno:$('#salequoteheader-no').val(),
                     lineno:$(this).attr('ew-lineno'),
                     updatefield:$(this).attr('name'),
                     edit:$(this).val(),
                 }};
         
        // ----- Do change number.-----


        route('index.php?r=SaleOrders/quotation/update-sale-line','POST',data,'SaleLine');
        LoadAjax();
        getSumLine($('#ew-discount-amount').val(),'discount');
        // ----- /. Do change number.-----

            


    });

/* Sale Line */
$('body').on('click','.ew-sl-qty',function(){

    var lineNumber  = $(this).children("div").attr('ew-line-no');
    var value_txt   = $(this).text().replace(',', '');

    if(value_txt==='')
    {
        value_txt   = $(this).children("div").children("input").val();
    }

    var text_qty    = '<div class="pull-right" ew-line-no="'+lineNumber+'"><input type="number" name="qty" id="ew-text-editor" value="' + value_txt + '" ew-lineno="' + lineNumber + '" class="form-control text-right" style="width:80px;"></div>';

    //$('#ew-qty-edit').html(text_qty);
    $(this).html(text_qty);
    $(this).children("div").children("input").focus();
    $(this).children("div").children("input").select();

});




$('body').on('click','.ew-sl-price',function(){
    var lineNumber = $(this).children("div").attr('ew-line-no');
    var value_txt   = $(this).text().replace(',', '');

    if(value_txt==='')
    {
        value_txt   = $(this).children("div").children("input").val();
    }
    var text_qty = '<div class="pull-right" ew-line-no="'+lineNumber+'"><input type="number" name="price" id="ew-text-editor" value="'+value_txt+'" ew-lineno="' + lineNumber + '" class="form-control text-right" style="width:100px;"></div>';

    //$('#ew-qty-edit').html(text_qty);
    $(this).html(text_qty);
    $(this).children("div").children("input").focus();
    $(this).children("div").children("input").select();




});



$('body').on('click','.ew-filter-onclick',function(){
    var href = $(this).attr('href').slice(1);
    var data = { param:
                    {
                        href:href
                    }
                };
    $('.FilterResource').hide('fast');

    route('index.php?r=SaleOrders/ajax/items','POST',data,'ResourceItemSearch');

    $('.FilterResource').show('normal');





});

$('body').on('click','.ew-fsize span',function(e){

    $('.ew-type').toggle();
    $('.InsertItem').toggle();
    $('.InsertItem').focus();
    //var inputItem = '<input type="text" name="InsertItem" class="form-control InsertItem">';
    //$('.ew-item-insert').html(inputItem);
    //$('.InsertItem').focus();

});

$('body').on('keyup','.InsertItem',function(e){
//$('.InsertItem').keydown(function (e) {

    var len = $.trim($(this).val()).length;
     

    if(len >= 3){
        
        if (e.which === 32 || e.which === 13) { // 32 Space bar
            //findItemTable($(this));
            FindItemsJson($(this));
        }



        if (e.which == 9) {

                if($('#InsertDesc').attr('ew-item-code') != 'eWinl')
                    {

                        $('#InsertDesc').first().focus();
                    }

            var inputItem = $.trim($('.InsertItem').val());
            $('.InsertItem').val(inputItem);


             $.ajax({

                url:"index.php?r=SaleOrders/ajax/json-find-item",
                type: "POST",
                data: {param:{item:inputItem}},
                async:true,
                success:function(getData){


                    var obj = jQuery.parseJSON(getData);
                    //alert( obj.name === "John" );
                    $('.ew-desc').show();
                    $('#InsertDesc').val(obj.desc);

                    $('#InsertDesc').attr('ew-item-code',obj.item);

                    $('.ew-qty').show();
                    $('#InsertQty').val(1);

                    $('.ew-price').show();
                    $('#InsertPrice').val(obj.std);
                    $('#item-id').val(obj.id);

                    if(obj.code != 'eWinl')
                    {
                        $('.ew-add').show();
                    }else {
                        $('.ew-add').hide();
                    }


                }
            });

            //alert(inputItem);
            getSumLine($('#ew-discount-amount').val(),'discount');

        }


    }else{
        $('.find-item').slideUp();
    }



});

$('body').on('keydown','#InsertDesc',function(e){

    if (e.which == 13) {

        $('#InsertQty').first().focus();

    }
});

$('body').on('keydown','#InsertQty',function(e){

    if (e.which == 13) {

        $('#InsertPrice').first().focus();

    }
});


// Add to Sale Line.
// ---------->
$('body').on('click','.ew-add',function(e){
    CreateSaleLine();

});

$('body').on('keydown','.ew-add',function(e){

    if (e.which == 13) {
        CreateSaleLine();

    }
});


$('body').on('keydown','#InsertPrice',function(e){

    if (e.which == 13) {
        CreateSaleLine();
    }
});

function CreateSaleLine() {


    if($('#InsertDesc').attr('ew-item-code') === 'eWinl')
    {
        if($('#InsertType').val()=='G/L')
        {
            alert('ขณะนี้ ยังไม่เปิดให้ใช้งาน G/L' );
        }else {
            alert('ไม่มี Item "'+ $('.InsertItem').val() +'"' );
        }

    }else {

        if($('#InsertPrice').val()==='' || $('#InsertPrice').val()==='0'){ alert('คุณกำลังพยายามใส่ "ราคา 0 บาท"'); }

        var data = {param:{
            itemid:$('#item-id').val(),
            itemno:$('#InsertDesc').attr('ew-item-code'),
            orderno:$('#salequoteheader-no').val(),
            itemset:0,
            soid:$('#SaleOrder').attr('ew-so-id'),
            amount:$('#InsertQty').val(),
            price:$('#InsertPrice').val(),
            desc:$('#InsertDesc').val(),
        }};

        route("index.php?r=SaleOrders/quotation/create_saleline",'POST',data,'SaleLine');
        //LoadAjax();
    }
    getSumLine($('#ew-discount-amount').val(),'discount');



}


$('body').on('click','.pick-item-to-createline',function(){
    PickToSaleLine($(this));
});

function PickToSaleLine($this){

    var $data = {param:{
            itemid:$this.attr('data-id'),
            itemno:$this.attr('itemno'),
            orderno:$('#salequoteheader-no').val(),
            itemset:0,
            soid:$('#SaleOrder').attr('ew-so-id'),
            amount:1,
            price:$this.attr('price'),
            desc:$this.attr('desc'),
        }};
    //console.log($data);

    route("index.php?r=SaleOrders/quotation/create_saleline",'POST',$data,'SaleLine');

    LoadAjax();

    getSumLine($('#ew-discount-amount').val(),'discount');

}
// <--------
// End add to Sale Line




$("body").keydown(function(event) {
    if(event.which == 27) { // ESC

        $('#ew-modal-Approve').modal('hide');

    }else if(event.which == 112) { // F1
        //alert('F1');
        $('.reject-reason #reason-text').focus();

    }else if(event.which == 113) { //F2

        //alert('F2');
    }
    else if(event.which == 114) { //F3
        if (confirm('Create New Document?')) {

        window.location.replace("index.php?r=SaleOrders/quotation/create");


        }
        return false;

    }
    else if(event.which == 116) { //F5

        alert('F5');
    }else if(event.which == 118) { //F7
        //$('#ew-modal-Approve').modal('toggle');
        BtnApprove($('#ew-reject'));
    }
    else if(event.which == 121) { //F10
        //$('#ew-modal-Approve').modal('toggle');
        BtnApprove($('#ew-confirm'));

    }
    else if(event.which == 13) { // Enter
        // If Model Open
        if($('.ew-confirm').is(':visible')){

            //alert('Confirm');

            Approve('ew-approve-body',$('#ew-data-text').text());


        }




    }



});














$('body').on('click','#ew-Item-Info',function(){

    $('#ewItemInfoModal').modal('show');
    $("#PickToSaleLine" ).hide();

    var items = $(this).attr('data-id');
    $('.ew-item-info-body').show();

    $('.ew-render-item-info').html('<div style="position:absolute; left:45%;">'+
        '<i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>'+
        '<div class="blink" > Loading .... </div></div>');


    setTimeout(function(e){
        $('.ew-render-item-info').slideUp();
        $.ajax({

            url:"index.php?r=items/items/view-modal",
            type: "GET",
            data: {id:items},
            async:true,
            success:function(getData){

                 $('.ew-render-item-info').html(getData).slideDown( "slow" );
                 //$('.ew-render-item-info').slideDown( "slow" );

            }
        });


        // route("index.php?r=items/items/view-modal",'GET',{id:items},'ew-render-item-info').slideToggle( "slow" );
    }, 1000);



});

$('body').on('click','.close-ewItemInfoModal',function(){

    $('#ewSelect').hide();
    $('#PickToSaleLine').hide();

    $('.ew-item-info-body').slideToggle();
    $('.ew-render-item-info').html('<div style="position:absolute; left:45%;">'+
        '<i class="fa fa-refresh fa-spin fa-2x fa-fw text-success" aria-hidden="true"></i>'+
        '<div class="blink"> Loading .... </div></div>');


    setTimeout(function(e){
        $('#ewItemInfoModal').modal('hide');
    }, 350);

});



$('body').on('click','#ew-modal-pick-cust',function(){
    //$('#ewPickCustomer').modal('show');
    route("index.php?r=customers/customer/pick-customer",'GET',{search:'',id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Customer');
});

//------ Select Customer ---------
$('body').on('change','#ew-search-text',function(){
    route("index.php?r=customers/customer/pick-customer",'GET',{search:$(this).val(),id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Customer');

});

$('body').on('click','#ew-search-btn',function(){
    route("index.php?r=customers/customer/pick-customer",'GET',{search:$('#ew-search-text').val(),id:$('#SaleOrder').attr('ew-so-id')},'ew-Pick-Customer');

});
//------/. Select Customer -------



$('body').on('click','#ew-pick-customer',function(){
   
    if (confirm('ยืนยันการเลือกลูกค้า ! ')) {
        // $('#salequoteheader-customer_id').val($(this).attr('ew-val'));
        // route("index.php?r=SaleOrders/quotation/updatecus&id=" + $('a#SaleOrder').attr('ew-so-id'),'POST',{param:{ cust:$(this).attr('ew-val')}},'ew-title-pic-cust');
        var tr = $(this).closest('tr');
        $('#salequoteheader-customer_id').val($(this).attr('ew-val'));
        $('#ewPickCustomer').modal('hide');
        var index = $(this).index()+1;
        $('#ew-modal-pick-cust').html(tr.find('a#ew-pick-customer').eq(2).text());
        //console.log($(this).attr('ew-val'));
    }else {
      return false;
    }

});
















function getSumLine(discount,key)
{

    var data = {
        id:$('a[id=SaleOrder]').attr('ew-so-id'),
        discount:discount,
        vat_percent:$('#salequoteheader-vat_percent').val(),
        inc_vat:$('#salequoteheader-include_vat').val(),
        percent:$('#ew-discount-percent').val(),
        credit:$('#salequoteheader-payment_term').val(),
        due:$('#salequoteheader-paymentdue').val(),
    };

    route('index.php?r=SaleOrders/quotation/percent-discount&id=' + data.id + '&key=' + key,'POST',data,'ew-sum-line');
}

$('body').on('change','#salequoteheader-payment_term',function(){

    var field = 'payment_term';
    var data = $(this).val();

    $.ajax({
        url:'?r=SaleOrders/quotation/update-some-field&id=' + $('a[id=SaleOrder]').attr('ew-so-id'),
        type:'POST',
        data: {field:field,data:data},
        dataType:'JSON',
        success:function(res){
            //console.log(res);
        }
    })
});

$('body').on('change','input[id="ew-discount-percent"]',function(){

    var percent_disc = $(this).val();
    var subtotal = Number($('#ew-line-total').attr('data'));
    var discount = (subtotal * percent_disc)/ 100;
    //var discount = $('#ew-discount-amount').val();
    if(percent_disc === '' ){
        discount = $('#ew-discount-amount').attr('data');
    }


    getSumLine(discount,'percent');


    $('input[id="ew-discount-amount"]').val(discount);
});

$('body').on('change','input[id="ew-discount-amount"]',function(){
    var discount = $(this).val(); // Baht
    var subtotal = Number($('#ew-line-total').attr('data'));
    var percent_disc = discount/subtotal*100;

    getSumLine(discount,'discount');

    $('input[id="ew-discount-percent"]').val(Math.round(percent_disc * 100) / 100);
});









$('body').on('change','.customer_id',function(){

    var $cust = $(this).val();
    var $order = $('a#SaleOrder').attr('ew-so-id');
    $('#ew-modal-pick-cust').text($('#salequoteheader-customer_id option:selected').text());
    //$('#saleheader-customer_id').val($(this).val());
    //route("index.php?r=SaleOrders/quotation/updatecus&id=" + $('a#SaleOrder').attr('ew-so-id'),'POST',{param:{ cust:$(this).val()}},'Navi-Title');

    if($cust!==''){
        $.ajax({
            url:"index.php?r=customers/ajax/json-get-customer&id=" + $cust,
            type: 'POST',
            data:{cust:$cust},
            dataType:'JSON',
            success:function(response){
                 $('#salequoteheader-payment_term').val(response.payment_term);
                 $('#salequoteheader-sale_address').val(response.fulladdress);
                 $('#salequoteheader-bill_address').val(response.fulladdress);
                 $('#salequoteheader-ship_address').val(response.fulladdress);
                 //$('#saleheader-sale_id').val(response.owner_sales);
                 $('#customer-payment').addClass('in');
                 

            }

        });
    }


})




$('body').on('click','.ew-btn-app-click',function(e){

    BtnApprove(this);

});


$('body').one('click','.ew-confirm',function(e){
    Approve('ew-approve-body',$('#ew-data-text').text());
    $('.modal-footer').hide();

});

$('body').on('click','.ew-cancel-job',function(e){

  BtnApprove(this);

  // if (confirm('ต้องการยกเลิกใบงาน ?')) {

  //   Approve('ew-text-status','Cancel');

  // }
  // return false;


});




function Approve(div,type)
{
    var appdata = { param:{
            apk:type,
            id:$('#SaleOrder').attr('ew-so-id'),
            cur:$('#SaleOrder').attr('ew-status'),
            reson:$('#reason-text').text(),

    }};
    route('index.php?r=approval/approve/sale-order','POST',appdata,div);
}



function BtnApprove(e)
{
    var text = $(e).attr('ew-data');
    var input = '<label for="reason-text">เหตุผล : </label><textarea class="form-control" id="reason-text" rows="3">ตรวจสอบรายการ</textarea>';
    var showText = ShowText(text);

    $('#ew-modal-Approve').modal('toggle');
    $('#ew-data-text').html(text);
    $('#ew-showText').html(showText);


    if(text === 'Reject' || text ==='Cancel' || text ==='Checking')
    {
        $('.reject-reason').html(input);

        if(text ==='Cancel')
        {
            $('#reason-text').text('สั่งผิด');
        }



    }else {
        $('.reject-reason').html('');
    }
}


function ShowText($text)
{
    if($text=='Checking'){
        return 'ยืนยัน!';
    }else if($text=='Confirm-Cancel'){
        return 'อนุมัติคำขอยกเลิก';
    }else if($text=='Confirm'){
        return 'อนุมัติ';
    }else if($text=='Reject'){
        return 'ปฏิเสธ';
    }


    return 'ยืนยัน';
}









// 05/10/17


$( document ).ready(function() {
    // Modal load PickItem-Modal
    $('#PickItem-Modal').on('shown.bs.modal', function () {


        // loadItem($('#itemno').val());

        // $("#PickToSaleLine" ).css("visibility", "hidden");

    });
});

$('body').on('click','input[id="ew-price"],input[id="ew-amount"]',function(){

    $(this).val('');

});

function loadItem(item){



    // ดึงรายการแรกออกมา เพื่อแสดงภาพ และกำหนดราคา
    $.ajax({

            url:"index.php?r=SaleOrders/ajax/item-getdata&checked=true",
            type: "POST",
            data: {param:{item:item.no , itemid: item.id}},
            async:true,
            success:function(getData){


                var obj = jQuery.parseJSON(getData);

                $('#ew-price').val(0);
                $("#ew-price").prop('disabled', true);
                $("#ew-amount").prop('disabled', true);
                $('.ew-render-item').html(obj.desc);

                if(parseInt(obj.inven) > 0){
                    $(".text-amount").html(number_format(obj.inven))
                    .addClass('text-green blink');
                }else{
                    $(".text-amount").html(obj.message).addClass('text-danger blink').css('background-color','yellow');
                }

                // Change Photo
                $(".ew-itemset-pic").attr("src", obj.Photo);
                //$('.ew-itemset-pic').attr('src','//assets.ewinl.com/images/product/' +obj.ig +'/' + obj.Photo);

            }
        });


}



 $('body').on('click','#selector ._radio',function(){

        $(this).addClass('btn-info').siblings().removeClass('btn-info');
        itemno =  $(this).attr('data');


        $('#price').val($(this).attr('price'));
        $('#ItemName').val($(this).attr('item_desc'));

        // TODO: insert whatever you want to do with $(this) here
    });



$('body').on('click','#PickToSaleLine',function(){
//$('#PickToSaleLine').click(function(){

        var that = this;
        $(that).find('i').attr('class','fas fa-sync-alt fa-spin');
        $(that).attr('disabled',true);            

        var data = {param:{
            itemid:$('#ew-render-itemno').data('key'),
            itemno:$('#itemno').val(),
            orderno:$('#salequoteheader-no').val(),
            itemset:$('#itemset').val(),
            soid:$('#SaleOrder').attr('ew-so-id'),
            amount:$('#ew-amount').val(),
            price:$('#ew-price').val(),
         }};

         $.ajax({
            url:'index.php?r=SaleOrders/quotation/create_saleline',
            type:'POST',
            data:data,
            success:function(res){
                
                $('.SaleLine').html(res);                

                $.notify({
                    // options
                    icon: 'fas fa-shopping-basket',
                    message: 'เพิ่มรายการสินค้าแล้ว' 
                },{
                    // settings
                    placement: {
                        from: "top",
                        align: "center"
                    },
                    type: 'warning',
                    delay: 3000,
                    z_index:3000,
                });

                $('html, body').animate({ 
                    scrollTop: $('.grid-view').offset().top - 80 
                },500);
        
                LoadAjax();
                getSumLine($('#ew-discount-amount').val(),'discount');

                setTimeout(function(){
                    $(that).find('i').attr('class','fa fa-check');
                    $(that).attr('disabled',false);
                },1000)


                var unit_price = $('#ew-price').val();
                if(unit_price==0)
                {
                    setTimeout(function(){
                        alert('ไม่มีราคา');
                    },1000);           
                }
            }
        })

        //route("index.php?r=SaleOrders/quotation/create_saleline",'POST',data,'SaleLine');


        // swal({
        //       title: 'บันทึกรายการแล้ว',
        //       text: 'Success',
        //       timer: 700
        //     }).then(
        //       function () {},
        //       // handling the promise rejection
        //       function (dismiss) {
        //         if (dismiss === 'timer') {
        //           console.log('สามารถเลือกสินค้าต่อได้')
        //         }
        //       }
        //     );
        // $.notify({
        //     // options
        //     icon: 'fas fa-shopping-basket',
        //     message: 'เพิ่มรายการสินค้าแล้ว' 
        //   },{
        //     // settings
        //     placement: {
        //         from: "top",
        //         align: "center"
        //     },
        //     type: 'warning',
        //     delay: 3000,
        //     z_index:3000,
        //   });



        // $('html, body').animate(
        //     { scrollTop: $('.grid-view').offset().top -80 },
        //     500);

        // LoadAjax();
        // getSumLine($('#ew-discount-amount').val(),'discount');

    });


$('body').on('click','.ew-action-my-item',function(){

        //ItemValidate
        var data = { param:{
            pid:$(this).attr('ew-radio-id'),
            pval:$(this).attr('ew-radio-val'),
            pset:$('#itemset').val(),

        }};
        //route("index.php?r=SaleOrders/ajax/item-validate",'POST',data,'ew-render-item');
        //$('.ew-getItem-Set').hide('slow');
        route("index.php?r=SaleOrders/ajax/item-validate",'POST',data,'ew-getItem-Set');
        //$('.ew-getItem-Set').show('fast');

        $('#ew-price').val(0);
        $('.ew-render-itemno').html('');
        $('.ew-render-item').html('');
        $('.text-amount').hide('')


        $("#ew-price").prop('disabled', true);
        $("#ew-amount").prop('disabled', true);
        $("#PickToSaleLine" ).hide();

    });


$('body').on('click','.ew-action-item',function(){

         //alert($(this).attr('ew-radio-item'));
         //console.log($(this).attr('ew-radio-item'));
         //$('.ew-render-item').html($(this).attr('ew-radio-item'));



         // Change Item No.
         $('#itemno').val($(this).attr('ew-radio-item'));


        let id = $(this).attr('data-key');

         $.ajax({

                url:"index.php?r=SaleOrders/ajax/item-getdata",
                type: "POST",
                data: {param:{item:$(this).attr('ew-radio-item'), itemid: id}},
                async:true,
                success:function(getData){


                    var obj = jQuery.parseJSON(getData);
                    //alert( obj.name === "John" );
                    //.hide().html(obj.inven).fadeIn('slow');

                    $('#ew-render-itemno').attr('data-key',obj.id);
                    $('#ew-price').val(obj.std);
                    $('.ew-render-itemno').hide().html(obj.code).fadeIn('slow');
                    $('.ew-render-item').hide().html(obj.desc).fadeIn('slow');
                    $('.text-amount').hide().html(number_format(obj.inven)).fadeIn('slow');

                    $("#ew-price").prop('disabled', false);
                    $("#ew-amount").prop('disabled', false);

                    $("#PickToSaleLine" ).show();

                    // Change Photo
                    //$('.ew-itemset-pic').attr('src','//assets.ewinl.com/images/product/' +obj.ig +'/' + obj.Photo).fadeIn('slow');

                    if(parseInt(obj.inven) > 0){
                        $(".text-amount").html(number_format(obj.inven))
                        .addClass('text-green blink');
                    }else{
                        $(".text-amount").html(obj.message).addClass('text-danger blink').css('background-color','yellow');
                    }
 
            
                    // Change Photo
                    $(".ew-itemset-pic").attr("src", obj.Photo).show();
            
                    //$('.ew-getItem-Set').html(obj.html);
                    if (obj.status == 200) {
                        $(".renders-box").show();
                        $(".renders-box").find('input[type="number"]:first');
                    }
                }
            });







    });

    $('body').on('click','.ew-render-itemno',function(){

        SelectText('ew-render-itemno');
    });

    $('body').on('dblclick','.ew-render-itemno',function(){

        //SelectText('ew-render-itemno');
        var url = "index.php?r=items/items/view&id="+ $(this).attr('data-key');
        // window.location = "https://admin.ewinl.com/index.php?r=items/items/view&id="+ $('#itemno').val();
        window.open(url, '_blank');
    });

    function SelectText(element) {
        var doc = document
            , text = doc.getElementById(element)
            , range, selection
        ;
        if (doc.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    }


    function FindItemsJson($this){

        let words = $this.val();
       
        $('.find-load').fadeIn('fast');
        $('.find-item-render').html('');
        $.ajax({
            url:"index.php?r=items/ajax/find-items-json-limit",
            type:'GET',
            data:{word:words,limit:20},
            async:true,
            success:function(getData){
                var obj = $.parseJSON(getData);
                if(obj[0].count==1){
                    if(obj[0].id===1414){
                        var name = prompt("กรุณาใส่ชื่อ/รายการสินค้า", " ");
                        if (name != null) {
                          obj[0].desc_th = name;              
                          createLine(obj[0]);
                          $(".find-item").hide("fast");
                          $(".find-load").fadeOut("fast");
                          // FOCUS First Text box
                          $.each($(".SaleLine").find("tr:last"), function(key, model) {
                            $(model)
                              .find("input:first")
                              .focus()
                              .select();
                          });
                        }
                      }else{
                        createLine(obj[0]);
                        $('.find-item').hide('fast');
                        $('.find-load').fadeOut('fast'); 
                      }
                    
                }else {
                    var html = '';
                    $('.find-item').show('fast');
                    $.each(obj,function(key,model){

                        if(model.count!=0){

                            html += '<a href="#true" data-id="'+model.id+'" itemno="'+model.no+'" desc="'+model.desc_th+' '+ model.detail +' '+model.size+'" price="'+model.cost+'"  class="pick-item-to-createline" >'+
                                    '<div class="panel panel-info">'+
                                        '<div class="panel-body">'+
                                            '<div class="row">'+
                                                '<div class="col-md-1 col-sm-2"><img src="'+model.img+'" class="img-responsive" style="min-width:50px; margin-bottom:20px;"></div>'+
                                                '<div class="col-md-11 col-sm-10">'+
                                                    '<div class="row">'+
                                                    '<div class="col-md-10 col-xs-8">'+model.desc_th+' '+ model.detail +' '+model.size+'</div>'+
                                                    '<div class="col-md-2 col-xs-4 text-right">'+
                                                        '<span class="find-price"><p class="price">Price</p>'+model.cost+'</span>'+
                                                    '</div>'+
                                                    '</div>'+
                                                    '<div class="row">'+
                                                    '<div class="col-xs-12"><span class="text-sm text-gray">'+model.desc_en+'</span></div>'+
                                                    '<div class="col-xs-12"><label class="text-black">Code : '+model.item+'</label></div>'+
                                                    '</div>'+
                                                    '<div class="row">'+
                                                    '<div class="col-xs-8"><label>Stock</label></div>'+
                                                    '<div class="col-xs-4 text-right"><span class="text-gray">'+model.inven+'</span></div>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '</a>\r\n';
                        }else {                           
                            html += '<div class="col-md-3">'+
                                        '<div class="col-xs-2 text-center"><i class="fas fa-search fa-3x"></i></div>'+
                                        '<div class="col-xs-10 text-center">NO DATA FOUND<br/> ไม่พบข้อมูล</div>'+
                                    '</div>';
                        }
                        
                    });

                    $('.find-item-render').html(html);
                        setTimeout(function(e){
                            $('.find-item').slideDown('slow');
                            $('.find-load').fadeOut();
                        },100);
                }
 
            }
    
        });
        
    
    }
    

    function createLine(itemno){
        var data = {param:{
            itemid:itemno.id,
            itemno:itemno.no,
            orderno:$('#salequoteheader-no').val(),
            itemset:0,
            soid:$('#SaleOrder').attr('ew-so-id'),
            amount:1,
            price:itemno.cost,
            desc:itemno.desc_th + itemno.detail + itemno.size,
        }};

        route("index.php?r=SaleOrders/quotation/create_saleline",'POST',data,'SaleLine');
        //LoadAjax();
     
        getSumLine($('#ew-discount-amount').val(),'discount');
    }
 
 
    $('body').on('click',"input[type='number']",function () {
        $(this).select();
     });
    $('body').on('keyup','input.update-desc',function(event){
        var keyCode   = event.keyCode || event.which;
        if (keyCode === 13) {
            var index = $('.text-line').index(this) + 1;
            $('.text-line').eq(index).focus().select();
         }  
    });
    $('body').on('change','input.update-desc',function(event){       
        var $this     = $(this);         
        var tr        = $this.closest('tr');  
        var form      = $('form#Form-Quotation');
        var $data = {
          ajax:true,
          key:tr.data('key'),
          name:tr.find($this).attr('name'),
          data:tr.find($this).val(),
        };  
        var action  = form.attr('action');  
        $.ajax({
            url: action+'&_pjax=%23p0',
            type: form.attr("method"),
            data: $data,
            dataType:'JSON',
            success: function (response) {    
                if(response.status==200){
                    getSumLine($('#ew-discount-amount').val(),'discount');                   
                }
            }        
        });        
    });
    $('body').on('keyup','input.update-quantity',function(event){
        var keyCode   = event.keyCode || event.which;         
        if (keyCode === 13) {
           var index = $('.text-line').index(this) + 1;
           $('.text-line').eq(index).focus().select();
        }  
    });
    $('body').on('change','input.update-quantity',function(event){
        var $this     = $(this);  
        var tr        = $this.closest('tr');  
        var form      = $('form#Form-Quotation');
        var $data = {
          ajax:true,
          key:tr.data('key'),
          name:tr.find($this).attr('name'),
          data:tr.find($this).val(),
        };  
        var action  = form.attr('action');  
        $.ajax({
            url: action+'&_pjax=%23p0',
            type: form.attr("method"),
            data: $data,
            dataType:'JSON',
            success: function (response) {    
                if(response.status==200){
                    getSumLine($('#ew-discount-amount').val(),'discount');
                    tr.find('.line-amount').text(number_format(response.value.total.toFixed(2)));
                }
            }
        });
        
    });

    
    $('body').on('keyup','input.update-unit_price',function(event){
        var keyCode   = event.keyCode || event.which;         
        if (keyCode === 13) {
            // ถ้าเป็นบรรทัดสุดท้ายเมื่อกด ENTER ให้ไป text search
            var index = $('.text-line').index(this) + 1;
            if(index==$('.text-line').length){                
                $('.InsertItem').focus().select();
            }else{
                $('.text-line').eq(index).focus().select();
            }         
        }  
    });

    $('body').on('change','input.update-unit_price',function(event){
        var $this     = $(this);          
        var tr        = $this.closest('tr');  
        var form      = $('form#Form-Quotation');
        var $data = {
          ajax:true,
          key:tr.data('key'),
          name:tr.find($this).attr('name'),
          data:tr.find($this).val(),
        };  
        var action  = form.attr('action');  
        $.ajax({
            url: action+'&_pjax=%23p0',
            type: form.attr("method"),
            data: $data,
            dataType:'JSON',
            success: function (response) {    
                if(response.status==200){
                    getSumLine($('#ew-discount-amount').val(),'discount');
                    tr.find('.line-amount').text(number_format(response.value.total.toFixed(2)));
                }
            }        
        });
        
    });
  
 
$('body').on('click', '.go-detail', function () {
    $('body').find('.loading-absolute').fadeIn(); 
    $('body').find('.item-detail').toggle("slide", { direction: "right" }, 500);  
    //$('.item-detail').toggle("slide", { direction: "right" }, 500);
    // $("html, body").animate({ scrollTop: 0 }, "slow");

    var tr = $(this).closest('tr');
    $("body").css({ overflow: 'hidden' });
    $('.item-box').slideUp();
    $.ajax({
        url: 'index.php?r=SaleOrders%2Fquotation%2Fget-saleline&id=' + btoa(tr.data('key')),
        type: 'GET',
        data: '',
        dataType: 'JSON',
        success: function (response) {
            setTimeout(() => {                    
                $('body').find('input[name="quantity"]').focus();
                setTimeout(() => {
                    $('body').find('input[name="quantity"]').select();
                    $('body').find('.loading-absolute').fadeOut('slow');
                }, 500); 
            }, 800);

            $('.item-name').html(response.value.name);
            $('.item-desc').html(response.value.detail);

            $('.item-price').val(response.value.price).attr('data-key', response.value.id);
            $('.item-qty').val(response.value.qty).attr('data-key', response.value.id);
            $('.item-line-amount').val(number_format(response.value.sumline.toFixed(2)));

            $('a.delete-btn').attr('href', '#' + response.value.id).attr('alt', response.value.name).attr('qty', response.value.qty).attr('price', response.value.price);
        }
    })

})



//---PICK CUSTOMER---
$('body').on('change', '#salequoteheader-customer_id', function () {
    //if($(this).val()!=''){
    $('#SaleOrder').html('<i class="far fa-address-card  fa-2x text-green"></i> ' + $('#salequoteheader-customer_id option:selected').text());
    $('#collapseOne').collapse();
    //$('#collapseOne').removeClass('in')
    //}
})

$('body').on('click', '#ew-pick-customer', function () {
    var tr = $(this).closest('tr');
    $('#SaleOrder').html('<i class="far fa-address-card fa-2x  text-green"></i> ' + tr.find('a#ew-pick-customer').eq(2).text());
    $('#collapseOne').collapse();

})
//--- /.PICK CUSTOMER---

 