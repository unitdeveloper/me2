
<?php
$js=<<<JS


let findItems = (search,callback) => {

  let customer = JSON.parse(localStorage.getItem('customer'));
  
  fetch("?r=SaleOrders/wizard/find-items", {
    method: "POST",
    body: JSON.stringify({ search: search,customer:customer.id }),
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
};


$('body').on("keypress",'input[type="text"], input[type="number"]', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});
 
$('body').on("keydown",'input[name="add-code"]', function(e) {
    let search  = $(this).val();
 
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  
    if ((keyCode === 13)||(keyCode === 9)) {
        e.preventDefault();
        if(search.trim()){
            findItems(search,res => {
                if(res.status===200){
                    soundClick.play();
                    $('body').find('input[name="add-code"]')
                    .val(res.items[0].code)
                    .attr('data-id',res.items[0].id)
                    .attr('data-barcode', res.items[0].barcode)
                    .css({'background':'#fff','color':'#555'});

                    $('body').find('input[name="add-name"]')
                    .val(res.items[0].name)
                    .attr('data-desc',res.items[0].name_en)
                    .css({'background':'#fff','color':'#555'})
                    .focus();

                    if(res.items[0].lastprice!==""){
                        let price  = Number((res.items[0].lastprice));
                        $('body').find('input[name="add-price"]').val(price.toFixed(2));
                    }

                }else{
                    $('body').find('input[name="add-code"]').css({'background':'#000','color':'#fff'})
                    soundError.play();
                    $('body').find('input[name="add-name"]')
                    .val(res.message)
                    .css({'background':'#000','color':'#fff'});
                }
            });
            return false;
        }else{
            soundError2.play();
        }
    }
});

$('body').on("keypress",'input[name="add-name"]', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    $('body').find('input[name="add-qty"]').focus();
    return false;
  }
});


$('body').on("keypress",'input[name="add-qty"]', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    $('body').find('input[name="add-price"]').select().focus();
    return false;
  }
});

// Add to Line and show new input
$('body').on("keypress",'input[name="add-price"]', function(e) {
 
    let data = JSON.parse(localStorage.getItem('new-sale-line'));
    let newData = {
                    id: $('input[name="add-code"]').attr('data-id') ? Number($('input[name="add-code"]').attr('data-id')) : 1414,
                    barcode: $('input[name="add-code"]').attr('data-barcode'),
                    code: $('input[name="add-code"]').val(),                    
                    name: $('input[name="add-name"]').val(),
                    name_en: $('input[name="add-name"]').attr('data-desc'),
                    price: Number($('input[name="add-price"]').val()),
                    qty:  Number($('input[name="add-qty"]').val()),
                    status: true,
                    unit: $('input[name="add-name"]').attr('data-unit')
                }

    let update = data.concat(newData);
    
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    // ถ้าไม่มี id ไม่ให้เพิ่ม
    //if($('input[name="add-code"]').attr('data-id')){
        e.preventDefault();
        soundEnterkey.play();
        console.log('update');
        console.log(update);
        renderTable(update);
        $('body').find('input[name="add-code"]').focus();
        localStorage.setItem('new-sale-line',JSON.stringify(update));
    // }else{
    //     soundError2.play();
    //     $('body').find('input[name="add-code"]').focus();
    // }

    return false;
  }
});



// Delete Line
$('body').on('click','button.delete-line',function(){
    let id = Number($(this).closest('tr').index());
    let tr = $(this).closest('tr');
    let code = $(this).closest('tr').find('input[name="name"]').val();

    let deleteLine = (id) => {
        let data = JSON.parse(localStorage.getItem('new-sale-line'));
        
        let line = data.filter((model,key) => key!==id? model: null);
        localStorage.setItem('new-sale-line',JSON.stringify(line));
        renderTable(line);
    }

    if (confirm('ต้องการลบรายการ "' + code + '" ?')) {
        tr.css("background-color", "#aaf7ff");
        soundClick2.play();
        tr.fadeOut(300, function() {
            tr.remove();   
            deleteLine(id);         
        });
    }
})

 
$('body').on('change','input[name="qty"]',function(){
    let el = $(this).closest('tr').index();
    let row = $(this).closest('tr').attr('data-row');
    let qty = $(this).val();
    let data = JSON.parse(localStorage.getItem('new-sale-line'));
    let update = data.map((model,key) => {
      return key===el? 
      Object.assign({},model,{
        qty: Number(qty)
      }) : model
    })
    localStorage.setItem('new-sale-line',JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      $('body').find('tr[data-row="'+row+'"]').find('input[name="price"]').select().focus();
    }, 100);
})

$('body').on('keypress','input[name="qty"]',function(e){
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let el = $(this).closest('tr').index();
    let row = $(this).closest('tr').attr('data-row');
    let qty = $(this).val();
    let data = JSON.parse(localStorage.getItem('new-sale-line'));
    let update = data.map((model,key) => {
      return key===el? 
      Object.assign({},model,{
        qty: Number(qty)
      }) : model
    })
    localStorage.setItem('new-sale-line',JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      $('body').find('tr[data-row="'+row+'"]').find('input[name="price"]').select().focus();
    }, 100);

  }
})


$('body').on('change','input[name="price"]',function(){
    let el = $(this).closest('tr').index();
    let price = $(this).val();
    let data = JSON.parse(localStorage.getItem('new-sale-line'));
    let update = data.map((model,key) => { return key===el? Object.assign({},model,{ price: Number(price) }) : model })
    localStorage.setItem('new-sale-line',JSON.stringify(update));
    renderTable(update);
})


$('body').on('keypress','input[name="price"]',function(e){
    var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let el      = $(this).closest('tr').index();
    let row     = Number($(this).closest('tr').attr('data-row')) + 1; // next row
    let price   = $(this).val();
    let data    = JSON.parse(localStorage.getItem('new-sale-line'));
    let update  = data.map((model,key) => { return key===el? Object.assign({},model,{ price: Number(price) }) : model })
    localStorage.setItem('new-sale-line',JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      // ถ้าแถวสุดท้าย ให้ไป focus ที่บรรทัดใหม่
      if(row===update.length){
        $('body').find('input[name="add-code"]').select().focus();
      }else{
        // ถ้าไม่ใช่บรรทัดสุดท้าย ให้ไป input ถัดไป
        $('body').find('tr[data-row="'+row+'"]').find('input[name="qty"]').select().focus();
      }
      
    }, 100);
  }
})

$('body').on('click','input[name="qty"], input[name="price"]',function(){
  $(this).select().focus();
})

// Header Change
$('body').on('change','#saleheader-invoice_no, #saleheader-ext_document, #saleheader-order_date, #saleheader-vat_percent, #saleheader-include_vat',function(){
  let header = JSON.parse(localStorage.getItem('sale-header'));
      header = Object.assign({},header,{ 
          vat:    $('#saleheader-vat_percent').val(), 
          incvat: $('#saleheader-include_vat').val(),
          date:   $('body').find('#saleheader-order_date').val(), 
          inv:    $('body').find('#saleheader-invoice_no').val(),
          po:     $('body').find('#saleheader-ext_document').val()
      });
  localStorage.setItem('sale-header',JSON.stringify(header));
  let data = localStorage.getItem('new-sale-line')? JSON.parse(localStorage.getItem('new-sale-line')) : [];
  renderTable(data);
})

JS;


$this->registerJs($js,Yii\web\View::POS_END);

?>