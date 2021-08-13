
var soundAlert      = new Audio ("media/alert.wav");
var soundClick      = new Audio ("media/click.wav");
var soundClick2     = new Audio ("media/click-2.wav");
var soundEffect     = new Audio ("media/effect.wav");
var soundCuckoo     = new Audio ("media/Cuckoo.wav");
var soundFade       = new Audio ("media/fade.mp3");
var soundCamera     = new Audio ("media/camera.flac");
var soundEnterkey   = new Audio ("media/enter-key.wav");
var soundError      = new Audio ("media/error.wav");
var soundError2     = new Audio ("media/computer-error.wav");
var soundBikeHorn   = new Audio ("media/mud__bike-horn-1.wav.wav");
 


const loadingDiv = `
        <div class="text-center" style="margin-top:50px;">
            <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
            <div class="blink"> Loading... </div>
            <img src="images/icon/loader2.gif" height="122"/>            
        </div>`;


const filterTable  = (search) => {
  $("#export_table  tbody tr").filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(search) > -1)
  });

  $('#export_table tbody tr').each((key,value) => {
      $(value).find('.key').html(key + 1);
  });
}

let state = {
  data:[]
};

let search = search => {
  fetch("?r=SaleOrders/wizard/find-customers", {
    method: "POST",
    body: JSON.stringify({ search: search }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      renders(response.data, "#renderCustomer");
    })
    .catch(error => {
      console.log(error);
    });
};

let customer = [];


let makeSession = (length) => {
  var text      = "";
  var possible  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < length; i++) text += possible.charAt(Math.floor(Math.random() * possible.length));

  var today = new Date().getTime();

  return today+text+'auto';
}
 

let setHeader = () => {
  let header = localStorage.getItem('sale-header') ? JSON.parse(localStorage.getItem('sale-header')) : [];
      
      $('#saleheader-vat_percent').val(header.vat);
      $('#saleheader-include_vat').val(header.incvat);
      $('body').find('#saleheader-order_date').val(header.date);
      $('body').find('#saleheader-invoice_no').val(header.inv).attr('data-id',header.invid);
      $('body').find('#saleheader-ext_document').val(header.po);
      $('body').find('#saleheader-remark').val(header.remark);
      $('body').find('.file-of-company').html(header.name);
      
}

$(document).ready(function() {
  $("body").addClass("sidebar-collapse").find(".user-panel").hide();
  renderCustomer(localStorage.getItem("customer")? JSON.parse(localStorage.getItem("customer")): []);
  setHeader();

  // สร้าง session เพิ่มตรวจสอบและลบในภายหลัง
  let session = localStorage.getItem('session') ? JSON.parse(localStorage.getItem('session')) : [];
  if(!session.id){
    localStorage.setItem('session',JSON.stringify({id:makeSession(15)}));
  }
});

let renderCustomer = customer => {
  if (customer.id) {
    // ถ้ามีลูกค้าแล้ว ให้แทนที่ข้อมูลใน element ได้เลย
    $(".cust-code")
      .html(customer.code)
      .attr(
        "href",
        "?r=customers%2Fcustomer%2Fview-only&id=" + customer.id + "#!#Invoicing"
      );
    $(".cust-name").html(customer.name);
    $(".cust-address").html(customer.address);
    $('select[name="payment_term"]').val(customer.term);
    $('a[href="#modal-pick-customer-wizard"]')
      .removeClass("btn-warning")
      .addClass("btn-success");


    // ไปยังหน้าถัดไป (แก้ไขรายการ)
    setTimeout(() => {
      var $active = $(".wizard .nav-tabs li.active");
      $active.next().removeClass("disabled");
      nextTab($active);
      $('body').find('.next-to-upload').addClass('next-step text-success btn-success-ew');
    }, 700);
  } else {
    // ถ้ายังไม่มีลูกค้าบังคับให้เลือกก่อน
    $('body').find('.next-to-upload').removeClass('next-step').addClass('btn-warning-ew text-warning');
    setTimeout(() => {
      $("#modal-pick-customer-wizard").modal("show");
      setTimeout(() => {
        search("ขายสด");
      }, 500);
    }, 1000);
  }
};

// ----------- Tabs ----------->

let nextTab = elem => {
  $(elem)
    .next()
    .find('a[data-toggle="tab"]')
    .click();
};
let prevTab = elem => {
  $(elem)
    .prev()
    .find('a[data-toggle="tab"]')
    .click();
};

$(document).ready(function() {
  //Initialize tooltips
  $(".nav-tabs > li a[title]").tooltip();

  //Wizard
  $('a[data-toggle="tab"]').on("show.bs.tab", function(e) {
    var $target = $(e.target);
    if ($target.parent().hasClass("disabled")) {
      return false;
    }
  });

  $(".next-step").click(function(e) {
    var $active = $(".wizard .nav-tabs li.active");
    $active.next().removeClass("disabled");
    nextTab($active);
  });

  $(".prev-step").click(function(e) {
    var $active = $(".wizard .nav-tabs li.active");
    prevTab($active);
  });
});

// <---------- Tabs -----------

let renders = (data, div) => {
  let html = `<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th class="text-right">Select</th>
                        </tr>
                    </thead>`;
  html += "<tbody>";

  data.length > 0
    ? data.map(model => {
        html += `<tr data-key="${model.id}" data-address="${model.address.address}" class="${model.head === 1 ? "bg-info" : ""}" data-term="${model.term}">
                    <td class="code" style="font-family:roboto;">${model.code}</td>
                    <td class="name">${model.name} ${model.head === 1 ? '<i class="far fa-star text-orange"></i>' : ''}</td>
                    <td class="text-right"><button type="button" class="selected-customer btn btn-primary btn-flat">Select</button></td>
                </tr>`;
      })
    : (html += "");

  html += "</tbody>";
  html += "</table>";
  html += `<div class="row"><div class="col-sm-12"> หมายเหตุ : <i class="far fa-star text-orange"></i> = สำนักงานใหญ่</div></div>`;

  $("body")
    .find(div)
    .html(html);
};

$("body").on("submit", 'form[name="search"]', function() {
  let words = $('#modal-pick-customer-wizard input[name="search"]').val();
  search(words);
});

$("body").on("keypress", '#modal-pick-customer-wizard input[name="search"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let words = $('#modal-pick-customer-wizard input[name="search"]').val();
    search(words);
  }
});

// Select customer
$("body").on("click", "button.selected-customer", function() {
  let id = parseInt(
    $(this)
      .closest("tr")
      .attr("data-key")
  );
  let name = $(this)
    .closest("tr")
    .find("td.name")
    .text();
  let code = $(this)
    .closest("tr")
    .find("td.code")
    .text();

  let address = $(this)
    .closest("tr")
    .attr("data-address");

  let term = $(this)
    .closest("tr")
    .attr("data-term");

  let customer = {
    id: id,
    name: name,
    code: code,
    address: address,
    term: term ? term : 0
  };

  localStorage.setItem("customer", JSON.stringify(customer));
  $("#modal-pick-customer-wizard").modal("hide");
  renderCustomer(customer);
  
 
 
  // ถ้าเลือกลูกค้าใหม่ ให้ส่งข้อมูลไปตรวจสอบใหม่อีกครั้ง
  let headers = {
          header: JSON.parse(localStorage.getItem('sale-header')),
          customer: localStorage.getItem('customer') ? JSON.parse(localStorage.getItem('customer')) : []
      };
  fetch("?r=SaleOrders/wizard/load-data", {
        method: "POST",
      body: JSON.stringify({line:JSON.parse(sessionStorage.getItem('data')),headers:headers}),
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
  })
  .catch(error => {
      console.log(error);
  });

});

// Make to invoice
$("body").on("click", "button.next-to-finish", function() {
  soundClick2.play();
  let headers = localStorage.getItem("create-order-wiz")
    ? JSON.parse(localStorage.getItem("create-order-wiz"))
    : [];
  //Finish
  fetch("?r=SaleOrders/wizard/finished", {
    method: "POST",
    body: JSON.stringify({ inv_id: headers.inv_id, order_id: headers.order_id }),
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
    },
  })
  .then(res => res.json())
  .then(response => {    
    if(response.order[0].status===200 && response.inv[0].status===200){
      
      $('body').find('.wizard .nav-tabs').attr('style','visibility: hidden;');
      $('body').find('.ORDER_TOTAL').html(number_format(response.order[0].message.total.toFixed(2),2));
      $('body').find('.INV_TOTAL').html(number_format(response.inv[0].message.total.toFixed(2),2));
      // ถ้ายอดไม่ตรง ให้ตรวจสอบใหม่
      if(Number(response.order[0].message.total.toFixed(2)) === Number(response.inv[0].message.total.toFixed(2))){
        localStorage.removeItem('create-order-wiz');
        localStorage.removeItem('customer');
        localStorage.removeItem('new-sale-line');
        localStorage.removeItem('sale-header');
        localStorage.removeItem('session');    
        sessionStorage.removeItem('data');    
        
        $('body').find('.TOTAL_CONFLICT').html(``);
      }else{
        $('body').find('.TOTAL_CONFLICT').html(`<div class="blink text-red">เกิดความผิดพลาด ยอดไม่ตรงกัน !</div>`);
      }
      
       

      
    } else {
      console.log(response.order[0].message);
      console.log(response.inv[0].message);
    } 
   
          
  })
  .catch(error => {
    console.log(error);
  });


});





$('body').on('click','button.create-sale-line',function(){
  
  let data = localStorage.getItem('new-sale-line') ? JSON.parse(localStorage.getItem('new-sale-line')) : [];
  let headers = {
          header: localStorage.getItem('sale-header') ? JSON.parse(localStorage.getItem('sale-header')) : [],
          customer: localStorage.getItem('customer') ? JSON.parse(localStorage.getItem('customer')) : [],
          session: localStorage.getItem('session') ? JSON.parse(localStorage.getItem('session')) : []
      };

  let active = $(".wizard .nav-tabs li.active");
  if(headers.header.date===""){
      // ใส่วันที่ก่อน
      alert('Please input Date');
      setTimeout(function() { $('input#saleheader-order_date').focus() }, 500);
      
      active.next().addClass("disabled");
      prevTab(active);
      return false;
  }else if((data.length > 0) && (headers.customer)){
      soundClick2.play();
      localStorage.removeItem('create-order-wiz');
      $('body').find('#renders-editable').html('<i class="fas fa-spinner fa-spin fa-2x"></i>');
      
      fetch("?r=SaleOrders/wizard/create-sale-line", {
              method: "POST",
              body: JSON.stringify({line:data,headers:headers}),
              headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
              },
          })
          .then(res => res.json())
          .then(response => {
              
              if(response.status===200){

                // ปิด(10) ไม่อนุญาตการส่งข้อมูลเมื่อคลิก next
                $('body').find('button#btn-create-sale-line')
                .removeClass('create-sale-line')
                .removeClass('btn-warning-ew text-warning')
                .addClass('btn-success-ew text-success');


                  localStorage.setItem('create-order-wiz',JSON.stringify({
                      line:       response.item, 
                      order_id:   response.order.id,
                      order_no:   response.order.no,
                      inv_id:     response.invoice.id,
                      inv_no:     response.invoice.no,
                      stock:      response.stock
                  }));

                  explodeBom(response.stock);

                  $("body").find('#saleheader-invoice_no').val(response.invoice.no)
                  .attr('data-id',response.invoice.id);
                  if(response.invoice.id==''){
                    alert('Error Invoice');
                  }
                  if(response.order.id==''){
                    alert('Error Sale Order');
                  }
                  
                  $("body").find('.INVOICE-NUMBER')
                  .html(response.invoice.no)
                  .attr('href', '?r=accounting%2Fposted%2Fprint-inv&id='+btoa(response.invoice.id)+'&footer=1');

                  $("body").find('.SALEORDER-NUMBER').html(response.order.no)
                  .attr('data-id',response.order.id)
                  .attr('href', '?r=SaleOrders%2Fsaleorder%2Fprint&id='+response.order.id+'&footer=1');
                   

                  
              }else{
                  swal(response.message, response.suggestion, "warning");
                  soundError2.play();
                  active.next().addClass("disabled");
                  prevTab(active);
                  return false;
              }
          })
          .catch(error => {
              console.log(error);
          });
  }else{
      soundError2.play();
      swal('No data','data not found','warning');
      active.next().addClass("disabled");
      prevTab(active);
      return false;
  }

  
})





let getInvoiceFromApi = (obj, callback) => {
  fetch("?r=accounting/ajax/invoice-by-no", {
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
};


const renderTableInvList = (data, calback) => {

  let vat = $('body').find('#vat-change').val();
  if(vat === 'Vat'){
      data = data.filter(model => model.vat > 0 ? model : null);
  }else if(vat === 'No'){
      data = data.filter(model => model.vat === 0 ? model : null);
  }
  
  let rows  = ''
  data.map(model => {
    let url   = model.status != 'Posted' 
                  ? `?r=accounting/saleinvoice/print-inv-page&id=${model.id}&footer=1`
                  : `?r=accounting/posted/print-inv&id=${btoa(model.id)}&footer=1`;

      rows+= `<tr data-key="${model.id}" class="${model.status != 'Posted' ? 'text-warning' : ''}">               
                <td class="bg-gray"><a href="${url}" target="_blank">${model.no}</a></td>
                <td>${model.custCode}</td>
                <td>${model.date}</td>
                <td>${model.due}</td>
                <td>${model.custName}</td>
                <td>${model.ref}</td>
                <td>${model.orderNo}</td>
              </tr>`;
      })
 
  let html = `<table class="table table-bordered table-hover font-roboto" id="export_table">
                <thead>
                  <tr class="bg-primary">
                    <th>เลขที่</th>
                    <th>รหัสลูกค้า</th>
                    <th>วันที่</th>
                    <th>ครบกำหนด</th>
                    <th>ลูกค้า</th>
                    <th>อ้างอิง</th>
                    <th>ใบสั่งขาย</th>
                  </tr>
                </thead>
                <tbody>
                  ${rows}
                </tbody>
              </table>`;
    calback({ 
      html: data && data.length > 0 ? html : '',
      count: data.length
    });
}

const loadData = (id) => {
  $('body').find('#renderInvoice').html(loadingDiv);
  getInvoiceFromApi({
    fdate: $('body').find('input[name="fdate"]').val(),
    tdate: $('body').find('input[name="tdate"]').val(),
    vat: 'all'
  }, res => { 
    state.data = res.data.raw;
    renderTableInvList(res.data.raw, res => {
      $('body').find('#renderInvoice').html(res.html);
      var table = $('#export_table').DataTable({
            "paging": false,
            "searching": false,
            "info" : false
        });

      table
      .column( 0 )
      .data()
      .sort();

    })    
  });
}

$('body').on('click','.show-series-list', function(){
  $("#modal-show-inv-list").modal("show");    
  setTimeout(() => {
    $('body').find('#inv-search-box').select().focus();
  }, 800);
  
  loadData()
});


$('body').on('keyup', '#modal-show-inv-list input[name="search-inv"]', function(e){
  let words = $('#modal-show-inv-list input[name="search-inv"]').val().toLowerCase();
  filterTable(words);
});


$('body').on('change', 'input[name="fdate"], input[name="tdate"]', function(){
  loadData();
})


$('body').on('change', ' #vat-change', function(){
  renderTableInvList(state.data, res => {
    $('body').find('#renderInvoice').html(res.html);
    var table = $('#export_table').DataTable({
          "paging": false,
          "searching": false,
          "info" : false
      });

    table
    .column( 0 )
    .data()
    .sort();

  });
})



$('body').on('keyup', '#saleheader-ext_document', function(e){
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
     $('#saleheader-invoice_no').select().focus();
  }
})
$('body').on('keyup', '#saleheader-invoice_no', function(e){
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
     $('#saleheader-order_date').select().focus();
  }
})