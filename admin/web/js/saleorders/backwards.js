var soundAlert = new Audio("media/alert.wav");
var soundClick = new Audio("media/click.wav");
var soundClick2 = new Audio("media/click-2.wav");
var soundEffect = new Audio("media/effect.wav");
var soundCuckoo = new Audio("media/Cuckoo.wav");
var soundFade = new Audio("media/fade.mp3");
var soundCamera = new Audio("media/camera.flac");
var soundEnterkey = new Audio("media/enter-key.wav");
var soundError = new Audio("media/error.wav");
var soundError2 = new Audio("media/computer-error.wav");
var soundBikeHorn = new Audio("media/mud__bike-horn-1.wav.wav");


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
  fetch("?r=SaleOrders/backwards/find-customers", {
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

let makeSession = length => {
  var text = "";
  var possible =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < length; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  var today = new Date().getTime();

  return today + text + "auto";
};

let setHeader = () => {
  let header = localStorage.getItem("sale-header")
    ? JSON.parse(localStorage.getItem("sale-header"))
    : [];

  $("#saleheader-vat_percent").val(header.vat);
  $("#saleheader-include_vat").val(header.incvat);
  $("body")
    .find("#saleheader-order_date")
    .val(header.date);
  $("body")
    .find("#saleheader-invoice_no")
    .val(header.inv)
    .attr("data-id", header.invid);
  $("body")
    .find("#saleheader-ext_document")
    .val(header.po);
  $("body")
    .find("#saleheader-remark")
    .val(header.remark);
  $("body")
    .find(".file-of-company")
    .html(header.name);
};

$(document).ready(function() {
  $("body")
    .addClass("sidebar-collapse")
    .find(".user-panel")
    .hide();
  renderCustomer(
    localStorage.getItem("customer")
      ? JSON.parse(localStorage.getItem("customer"))
      : []
  );
  setHeader();

  // สร้าง session เพิ่มตรวจสอบและลบในภายหลัง
  let session = localStorage.getItem("session")
    ? JSON.parse(localStorage.getItem("session"))
    : [];
  if (!session.id) {
    localStorage.setItem("session", JSON.stringify({ id: makeSession(15) }));
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
      $("body")
        .find(".next-to-upload")
        .addClass("next-step text-success btn-success-ew");
    }, 700);
  } else {
    // ถ้ายังไม่มีลูกค้าบังคับให้เลือกก่อน
    $("body")
      .find(".next-to-upload")
      .removeClass("next-step")
      .addClass("btn-warning-ew text-warning");
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
        html += `<tr data-key="${model.id}" data-address="${
          model.address.address
        }" class="${model.head === 1 ? "bg-info" : ""}" data-term="${
          model.term
        }">
                    <td class="code" style="font-family:roboto;">${
                      model.code
                    }</td>
                    <td class="name">${model.name} ${
          model.head === 1 ? '<i class="far fa-star text-orange"></i>' : ""
        }</td>
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
  let words = $('input[name="search"]').val();
  search(words);
});

$("body").on("keypress", 'input[name="search"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let words = $('input[name="search"]').val();
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
    header: JSON.parse(localStorage.getItem("sale-header")),
    customer: localStorage.getItem("customer")
      ? JSON.parse(localStorage.getItem("customer"))
      : []
  };
  fetch("?r=SaleOrders/backwards/load-data", {
    method: "POST",
    body: JSON.stringify({
      line: JSON.parse(sessionStorage.getItem("data")),
      headers: headers
    }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      // RENDER TABLE
      renderTable(response.item);
      localStorage.setItem("new-sale-line", JSON.stringify(response.item));
    })
    .catch(error => {
      console.log(error);
    });
});

// Make to invoice
$("body").on("click", "button.next-to-finish", function() {
  soundClick2.play();
  let headers = localStorage.getItem("create-order")
    ? JSON.parse(localStorage.getItem("create-order"))
    : [];
  //Finish  
  fetch("?r=SaleOrders/backwards/finished", {
    method: "POST",
    body: JSON.stringify({ inv_id: headers.inv_id, order_id: headers.order_id }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
    .then(res => res.json())
    .then(response => {
      if (response.order[0].status === 200 && response.inv[0].status === 200) {
        $("body")
          .find(".wizard .nav-tabs")
          .attr("style", "visibility: hidden;");
        $("body")
          .find(".ORDER_TOTAL")
          .html(number_format(response.order[0].message.total.toFixed(2), 2));
        $("body")
          .find(".INV_TOTAL")
          .html(number_format(response.inv[0].message.total.toFixed(2), 2));
        // ถ้ายอดไม่ตรง ให้ตรวจสอบใหม่
        if (
          Number(response.order[0].message.total.toFixed(2)) ===
          Number(response.inv[0].message.total.toFixed(2))
        ) {
          localStorage.removeItem("create-order");
          localStorage.removeItem("customer");
          localStorage.removeItem("new-sale-line");
          localStorage.removeItem("sale-header");
          localStorage.removeItem("session");
          sessionStorage.removeItem("data");

          $("body")
            .find(".TOTAL_CONFLICT")
            .html(``);
        } else {
          $("body")
            .find(".TOTAL_CONFLICT")
            .html(
              `<div class="blink text-red">เกิดความผิดพลาด ยอดไม่ตรงกัน !</div>`
            );
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

$("body").on("click", "button.create-sale-line", function() {
  let data = localStorage.getItem("new-sale-line")
    ? JSON.parse(localStorage.getItem("new-sale-line"))
    : [];
  let headers = {
    header: localStorage.getItem("sale-header")
      ? JSON.parse(localStorage.getItem("sale-header"))
      : [],
    customer: localStorage.getItem("customer")
      ? JSON.parse(localStorage.getItem("customer"))
      : [],
    session: localStorage.getItem("session")
      ? JSON.parse(localStorage.getItem("session"))
      : []
  };

  let active = $(".wizard .nav-tabs li.active");
  if ((headers.header.date === "") || (!headers.customer.term)) {
    // ใส่วันที่ก่อน
    alert("Please input Date");
    setTimeout(function() {
      $("input#saleheader-order_date").focus();
    }, 500);

    active.next().addClass("disabled");
    prevTab(active);
    return false;
  } else if (data.length > 0 && headers.customer) {
    soundClick2.play();
    localStorage.removeItem("create-order");

   

    fetch("?r=SaleOrders/backwards/create-sale-line", {
      method: "POST",
      body: JSON.stringify({ line: data, headers: headers }),
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
      }
    })
      .then(res => res.json())
      .then(response => {
        if (response.status === 200) {

          // ปิด(10) ไม่อนุญาตการส่งข้อมูลเมื่อคลิก next
          $("body")
          .find("button#btn-create-sale-line")
          .removeClass("create-sale-line")
          .removeClass("btn-warning-ew text-warning")
          .addClass("btn-success-ew text-success");


          renderTableReadonly(response.item);
          $("body")
            .find("#saleheader-invoice_no")
            .val(response.invoice.no)
            .attr("data-id", response.invoice.id);

          $("body")
            .find(".INVOICE-NUMBER")
            .html(response.invoice.no)
            .attr(
              "href",
              "?r=accounting%2Fposted%2Fprint-inv&id=" +
                btoa(response.invoice.id) +
                "&footer=1"
            );

          $("body")
            .find(".SALEORDER-NUMBER")
            .html(response.order.no)
            .attr("data-id", response.order.id)
            .attr(
              "href",
              "?r=SaleOrders%2Fsaleorder%2Fprint&id=" +
                response.order.id +
                "&footer=1"
            );

            if(response.invoice.id==''){
              alert('Error Invoice');
            }
            if(response.order.id==''){
              alert('Error Sale Order');
            }

          localStorage.setItem(
            "create-order",
            JSON.stringify({
              line: response.item,
              order_id: response.order.id,
              order_no: response.order.no,
              inv_id: response.invoice.id,
              inv_no: response.invoice.no
            })
          );
        } else {
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
  } else {
    soundError2.play();
    swal("No data", "data not found", "warning");
    active.next().addClass("disabled");
    prevTab(active);
    return false;
  }
});

$("body").on("change", 'input[type="file"]', function(e) {
  $("body").prepend(
    '<div style="position: absolute; left:50%; top:40%; z-index:1100;"> <i class="fas fa-spinner fa-4x fa-spin"></i> </div>'
  );
  $("body")
    .find('label[id="file-input"]')
    .slideUp();
  soundCamera.play();
  localStorage.removeItem("new-sale-line");
  localStorage.removeItem("create-order");
  sessionStorage.removeItem("data");
  setTimeout(() => {
    $("form#import-file").submit();
  }, 1000);
});

var data = [];
let store = 0;

let findCompany = callback => {
  let store = 1;
  let endpage = 1;
  let storename = "";
  let po = "";
  let total = 0;

  $("#pdf-content")
    .children("div")
    .map((key, el) => {
      $(el)
        .find("p")
        .map((i, p) => {
          // List all <p> tag
          let str = $(p).html();
          if (str.search("60004-CRC") > -1) {
            store = 1;
            storename = "CRC Thai Watsadu Limited";
            $(p).css("background", "gray");
          } else if (str.search("สยามโกลบอลเฮ้าส์") > -1) {
            store = 2;
            storename = "บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่";
            $(p).css("background", "gray");
          } else if (str.search("0115545007325") > -1) {
            // // ฮาร์ดแวร์เฮาส์
            store = 3;
            storename = "บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)";
            $(p).css("background", "gray");
          } else if (str.search("โฮมฮับ") > -1) {
            store = 4;
            storename = "บริษัท โฮมฮับ จํากัด";
            $(p).css("background", "transparence");
          } else if (str.search("501383") > -1) {
            store = 5;
            storename = "บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)";
            $(p).css("background", "gray");
          } else if (str.search("บริษัท เมกา โฮม") > -1) {
            store = 6;
            storename = "บริษัท เมกา โฮม เซ็นเตอร์ จํากัด";
            $(p).css("background", "gray");
          }
          // หาจำนวนหน้า
          if (str.search("єѬјзҕѥѝѧьзҖѥ") > -1) {
            // CRC Thai Watsadu Limited
            $(p)
              .prevAll()
              .slice(0, 1)
              .css({
                "background-color": "rgb(0, 224, 247)",
                padding: "5px !important"
              });
            total = Number(
              $(p)
                .prevAll()
                .slice(0, 1)
                .text()
                .replace(/[^0-9\.-]+/g, "")
            );
            endpage =
              $(p)
                .closest("div")
                .index();
            if(endpage > 2){
              endpage = endpage - 1
            }
          } else if (str.search("มูลค่ารวมทั้งสิ้น") > -1) {
            // บจก. ฮาร์ดแวร์เฮาส์ (สำนักงานใหญ่)
            $(p)
              .next()
              .css({
                "background-color": "rgb(0, 224, 247)",
                padding: "5px !important"
              });
            total = Number($(p).next().text().replace(/[^0-9\.-]+/g, ""));
            endpage = $(p).closest("div").index();
            if(endpage > 2){ endpage = endpage - 1 }
          } else if (
            str.search(
              "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7%"
            ) > -1
          ) {
            // บริษัท สยามโกลบอลเฮ้าส์ จํากัด (มหาชน) สํานักงานใหญ่
            $(p)
              .nextAll()
              .slice(2, 3)
              .css({
                "background-color": "rgb(0, 224, 247)",
                padding: "5px !important"
              });
            total = Number(
              $(p)
                .nextAll()
                .slice(2, 3)
                .text()
                .replace(/[^0-9\.-]+/g, "")
            );
            endpage = $(p).closest("div").index();
            if(endpage > 2){ endpage = endpage - 1 }
          } else if (str.search("จํานวนเงินทังสิน") > -1) {
            // บริษัท โฮมฮับ จํากัด
            $(p).nextAll().slice(3, 4).css({
                "background-color": "rgb(0, 224, 247)",
                "font-size": "20px"
              });
            total = Number($(p).nextAll().slice(3, 4).text().replace(/[^0-9\.-]+/g, ""));
            endpage = $(p).closest("div").index();
            if(endpage > 2){ endpage = endpage - 1 }
          } else if (str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1) {
            // บริษัท เมกา โฮม เซ็นเตอร์ จํากัด
            $(p)
              .prevAll()
              .slice(0, 1)
              .css({
                "background-color": "rgb(0, 224, 247)",
                padding: "5px !important"
              });
            total = Number(
              $(p)
                .prevAll()
                .slice(0, 1)
                .text()
                .replace(/[^0-9\.-]+/g, "")
            );
            endpage =
              $(p)
                .closest("div")
                .index();
            if(endpage > 2){ endpage = endpage - 1 }
          } else if (str.search("รวมราคาสินค้าไม่รวมภาษีมูลค่าเพิ�ม") > -1) {
            // บริษัท โฮมโปรดักส์ เซ็นเตอร์ จํากัด (มหาชน)
            $(p)
              .prevAll()
              .slice(0, 1)
              .css({
                "background-color": "rgb(0, 224, 247)",
                padding: "5px !important"
              });
            total = Number(
              $(p)
                .prevAll()
                .slice(0, 1)
                .text()
                .replace(/[^0-9\.-]+/g, "")
            );
            endpage = $(p).closest("div").index();
            if(endpage > 2){ endpage = endpage - 1 }
          }
        });
    });
  callback({
    store: store,
    page: endpage <= 0 ? 1 : endpage,
    name: storename,
    po: po,
    total: total,
    vat: "7",
    incvat: "1",
    date: $("body")
      .find("#saleheader-order_date")
      .val(),
    inv: $("body")
      .find("#saleheader-invoice_no")
      .val(),
    remark: $("body")
      .find("#saleheader-remark")
      .val()
  });
};

$(document).ready(function() {
  let data = localStorage.getItem("new-sale-line")
    ? JSON.parse(localStorage.getItem("new-sale-line"))
    : [];
  if (data.length > 0) {
    // ถ้ามีรายการ Import file มาแล้วให้แสดงในตาราง
    renderTable(data);

    let orders = localStorage.getItem("create-order")
      ? JSON.parse(localStorage.getItem("create-order"))
      : [];
    if (orders.line ? orders.line.length > 0 : false) {
      // ถ้า สร้าง invoice แล้วให้ข้ามขั้นตอนได้
      // ไปยังหน้าถัดไป (แก้ไขรายการ)
      setTimeout(() => {
        let active = $(".wizard .nav-tabs li.active");
        active.next().removeClass("disabled");
        nextTab(active);
      }, 1500);
      // ปิด(10) ไม่อนุญาตการส่งข้อมูลเมื่อคลิก next
      $("body")
        .find("button#btn-create-sale-line")
        .removeClass("create-sale-line")
        .removeClass("btn-warning-ew text-warning")
        .addClass("btn-success-ew text-success");
      renderTableReadonly(orders.line);

      $("body")
        .find("#saleheader-invoice_no")
        .val(orders.inv_no)
        .attr("data-id", orders.inv_id);
      $("body")
        .find(".SALEORDER-NUMBER")
        .html(orders.order_no)
        .attr("data-id", orders.order_id)
        .attr(
          "href",
          "?r=SaleOrders%2Fsaleorder%2Fprint&id=" +
            orders.order_id +
            "&footer=1"
        );

      $("body")
        .find(".INVOICE-NUMBER")
        .html(orders.inv_no)
        .attr(
          "href",
          "?r=accounting%2Fposted%2Fprint-inv&id=" +
            btoa(orders.inv_id) +
            "&footer=1"
        );
    }
  } else {
    findCompany(res => {
      //console.log(res);
      store = res.store;
      $("#pdf-content")
        .children("div")
        .map((key, el) => {
          if (key < res.page) {
             
            $(el)
              .find("p")
              .map((i, p) => {
                // List all <p> tag
                let str = $(p).html();
                switch (store) {
                  case 1: // Home work ไทวัสดุ
                    // ค้นหา PO
                    if (str.search("P/O Number") > -1) {
                      // CRC Thai Watsadu Limited
                      $(p)
                        .next()
                        .next()
                        .css("background", "pink");
                      res = Object.assign({}, res, {
                        po: $(p)
                          .next()
                          .next()
                          .text()
                      });
                    }

                    if (str.search("8859042") > -1) {
                      // find string 885 from p
                      $(p).css("background", "#51ff00");
                      $(p)
                        .nextAll()
                        .slice(4, 5)
                        .css("background", "red");
                      $(p)
                        .nextAll()
                        .slice(10, 11)
                        .css("background", "orange");

                      let qty = Number(
                        $(p)
                          .nextAll()
                          .slice(4, 5)
                          .text()
                          .replace(/[^0-9\.-]+/g, "")
                      );
                      let sumline = Number(
                        $(p)
                          .nextAll()
                          .slice(10, 11)
                          .text()
                          .replace(/[^0-9\.-]+/g, "")
                      );
                      let price = sumline / qty;

                      data.push({
                        item: $(p).text(),
                        qty: qty,
                        price: Number(price.toFixed(4)),
                        sumline: sumline,
                        discount: 0
                      });
                    }
                    break;

                  case 2: // GOBAL HOUSE
                    if (str.search("PODC") > -1) {
                      // ค้นหา PO
                      $(p)
                        .first()
                        .css("background", "pink");
                      res = Object.assign({}, res, {
                        po: $(p)
                          .first()
                          .text()
                      });
                    }

                    if (str.search("8859042") > -1) {
                      // find string 885 from p
                      $(p).css("background", "#51ff00");
                      $(p).nextAll().slice(3, 4).css("background", "red");
                      $(p).nextAll().slice(6, 7).css("background", "orange");

                      let qty     = Number($(p).nextAll().slice(3, 4).text().replace(/[^0-9\.-]+/g, ""));
                      let sumline = Number($(p).nextAll().slice(6, 7).text().replace(/[^0-9\.-]+/g, ""));
                      let price   = sumline / qty;

                      data.push({
                        item: $(p).text(),
                        qty: qty,
                        price: Number(price.toFixed(4)),
                        sumline: sumline,
                        discount: 0
                      });
                    }
                    break;

                  case 3: // HARDWARE HOUSE
                    if (str.search("เลขที่เอกสาร :") > -1) {
                      // ค้นหา PO
                      $(p).next().css("background", "pink");
                      res = Object.assign({}, res, {
                        po: $(p).next().text()
                      });
                    }

                    if (str.search("8859042") > -1) {
                      // find string 885 from p
                      $(p).css("background", "#51ff00");
                      $(p).nextAll().slice(2, 3).css("background", "red");
                      $(p).nextAll().slice(4, 5).css("background", "orange");

                      data.push({
                        item: $(p).text(),
                        qty: Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g, "")),
                        price:Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g, "")) / Number($(p).nextAll().slice(2, 3).text().replace(/[^0-9\.-]+/g, "")),
                        sumline: Number($(p).nextAll().slice(4, 5).text().replace(/[^0-9\.-]+/g, "")),
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
                    if (str.search("PO #") > -1) {
                      // ค้นหา PO
                      $(p).first().css("background", "green");
                      $(p).prev().css("background", "pink");

                      res = Object.assign({}, res, {
                        po: $(p).prev().text(),
                        id: 251
                      });
                    }

                    if (str.search("/1 EA") > -1) {
                      // find string 885 from p
                      let price   = 0;
                      let qty     = 0;
                      let sumline = 0;
                      let rows    = $(p).nextAll().slice(1, 2).text();
                      // ถ้าเจอเครื่องหมาย % ในตัวเลขแสดงว่าเป็นเปอร์เซ็นส่วนลด
                      console.log(rows);
                      if(rows.indexOf('%')){

                        $(p).prevAll().slice(1, 2).css("background", "#51ff00");
                        $(p).next().css("background", "red");
                        $(p).nextAll().slice(0, 1).css("background", "orange");

                        qty     = Number($(p).next().text().replace(/[^0-9\.-]+/g, ""));                      
                        sumline = Number($(p).nextAll().slice(0, 1).text().replace(/[^0-9\.-]+/g, ""));
                        price   = sumline / qty;

                      }else {
                      
                        $(p).prevAll().slice(1, 2).css("background", "#51ff00");
                        $(p).next().css("background", "red");
                        $(p).nextAll().slice(1, 2).css("background", "orange");

                        qty     = Number($(p).next().text().replace(/[^0-9\.-]+/g, ""));                      
                        sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g, ""));
                        price   = sumline / qty;
                      
                      }

                      data.push({
                        item: $(p).prevAll().slice(1, 2).text(),
                        qty: qty,
                        price: Number(price.toFixed(4)),
                        sumline: sumline,
                        discount: 0
                      });
                    }
                    break;

                  case 6: // MAGA HOME
                    if (str.search("PO #") > -1) {
                      // ค้นหา PO
                      $(p).prev().css("background", "pink");
                      res = Object.assign({}, res, {
                        po: $(p).prev().text()
                      });
                    }

                    if (str.search("/1 EA") > -1) {
                      $(p).prevAll().slice(1, 2).css("background", "#51ff00");
                      $(p).next().css("background", "red");
                      $(p).nextAll().slice(1, 2).css("background", "orange");
                      let qty = Number($(p).next().text().replace(/[^0-9\.-]+/g, ""));
                      let sumline = Number($(p).nextAll().slice(1, 2).text().replace(/[^0-9\.-]+/g, ""));
                      let price = sumline / qty;

                      data.push({
                        item: $(p).prevAll().slice(1, 2).text(),
                        qty: qty,
                        price: Number(price.toFixed(4)),
                        sumline: sumline,
                        discount: 0
                      });
                    }
                    break;

                  default:
                    break;
                }
              });
          } else {
            $(el)
              .closest("div")
              .remove();
          }
        });
      // เก็บข้อมูลไว้ตรวจสอบในภายหลัง
      localStorage.setItem("sale-header", JSON.stringify(res));
      sessionStorage.setItem("data", JSON.stringify(data));

      let headers = {
        header: res,
        customer: localStorage.getItem("customer")
          ? JSON.parse(localStorage.getItem("customer"))
          : []
      };

      if (headers.customer.id) {
        fetch("?r=SaleOrders/backwards/load-data", {
          method: "POST",
          body: JSON.stringify({ line: data, headers: headers }),
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
          }
        })
          .then(res => res.json())
          .then(response => {
            // RENDER TABLE
            renderTable(response.item);
            localStorage.setItem(
              "new-sale-line",
              JSON.stringify(response.item)
            );
          })
          .catch(error => {
            console.log(error);
          });
      }
    }); // findCompany
  }
});

let renderTable = data => {
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
  data.length > 0
    ? data.map((model, key) => {
        i++;
        let lineDiscount = model.discount ? (model.discount * 1) : 0;
        let totals  = (model.qty * model.price) - ((lineDiscount /100) * (model.qty * model.price));
        let total = Number((totals).toFixed(2));
        let price   = model.price > 0 ? Number(model.price.toFixed(2)) : 0;
        
        sum += total;

        if (model.status) {
          html += `
                    <tr data-key="${model.id}" data-row="${key}">
                        <td>${i}</td>
                        <td class="text-left"><input type="text" class="form-control"  name="qty" readonly value="${model.code}" autocomplete="off"/></td>
                        <td><input type="text" class="form-control"  name="name" readonly value="${model.name}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="qty" value="${model.qty}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="price" value="${price}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="discount" value="${lineDiscount}" autocomplete="off"/></td>
                        <td class="text-right">${number_format(total.toFixed(2),2)}</td>
                        <th class="text-right" style="width:50px;"><button type="button" class="btn btn-danger btn-xs delete-line"><i class="far fa-trash-alt"></i></button></th>
                    </tr>
            `;
        } else {
          html += `
                    <tr class="bg-danger" data-row="${key}">
                        <td>${i}</td>
                        <td class="text-left"><input type="text" class="form-control text-red" readonly  name="qty" value="${model.code}" autocomplete="off"/></td>
                        <td><input type="text" class="form-control text-red"  name="name" readonly value="${model.name}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="qty" value="${model.qty}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="price" value="${price}" autocomplete="off"/></td>
                        <td><input type="number" class="form-control text-right"  name="discount" value="${lineDiscount}" autocomplete="off"/></td>
                        <td class="text-right">${number_format(total.toFixed(2),2)}</td>
                        <th class="text-right" style="width:50px;"><button type="button" class="btn btn-danger btn-xs delete-line"><i class="far fa-trash-alt"></i></button></th>
                    </tr>
            `;
        }
      })
    : null;

  html += `
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

  html += `</tbody>
            </table>`;

  setHeader();

  let totals = (sum, html, callback) => {
    let header = JSON.parse(localStorage.getItem("sale-header"));
    let total, vat, vatTotal, textTotal, vat_revert, sumTotal;
    if (header.incvat === "0") {
      //vat ใน
      vat_revert = header.vat / 100 + 1;
      vat = sum - sum / vat_revert;
      sumTotal = number_format((sum - vat).toFixed(2));
      textTotal = number_format(sum.toFixed(2));
      let diff = Number(sum.toFixed(2)) - header.total;
      total =
        diff === 0
          ? '<span class="text-success">' + textTotal + "</span>"
          : '<span class="text-red blink">' + textTotal + "</span>";
    } else {
      // vat นอก
      vat = (sum * header.vat) / 100;
      vatTotal = vat + sum;
      textTotal = number_format(vatTotal.toFixed(2));
      // ถ้าผลรวมไม่เท่ากัน ให้แสดง text สีแดง
      let diff = Number(vatTotal.toFixed(2)) - header.total;
      total =
        diff === 0
          ? '<span class="text-success">' + textTotal + "</span>"
          : '<span class="text-red blink">' + textTotal + "</span>";
      // ถ้ายอดไม่ตรง อาจจะเป็น vat ใน (ลองตรวจกับยอด sum)
      let textSum = number_format(sum.toFixed(2));
      // ถ้าตรงกันอยู่แล้ว ไม่ต้องไปเช็คต่อ
      if (diff === 0) {
        sumTotal = textSum;
      } else {
        let diff_vat = Number(sum.toFixed(2)) - header.total;
        sumTotal =
          diff_vat === 0
            ? '<span class="text-success">' + textSum + "</span>"
            : '<span class="text-red blink">' + textSum + "</span>";
        total = textTotal; // ไม่ต้องแสดงความผิดพลาด (vat ใน ยอดนี้ไม่ใช่อยู่แล้ว)
      }
    }

    // ไม่ให้กด tab หลักจากแก้รายการ(คลิก next เท่านั้น)
    $(".wizard .nav-tabs li.active")
      .next()
      .addClass("disabled");
    // เปิด(10) อนุญาตการส่งข้อมูลเมื่อคลิก next
    $("body")
      .find("button#btn-create-sale-line")
      .addClass("create-sale-line")
      .addClass("text-warning btn-warning-ew");

    callback({
      total: sumTotal,
      vat: number_format(vat.toFixed(2)),
      grand: total,
      html: html,
      _vat: header.vat,
      _incvat: header.incvat
    });
  };

  totals(sum, html, res => {
    $("body")
      .find("#saleheader-include_vat")
      .val(res._incvat);
    $("body")
      .find("#saleheader-vat_percent")
      .val(res._vat);

    $("body")
      .find("#get-sum-total")
      .html(res.total);
    $("body")
      .find("#get-sum-vat")
      .html(res.vat);
    $("body")
      .find("#get-grand-total")
      .html(res.grand);

    $("body")
      .find("div.renders")
      .html(res.html);
  });
};

let renderTableReadonly = data => {
  let html = `
        <table class="table table-bordered" id="sale-line-table" style="font-family:roboto;">
            <thead class="bg-gray">
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:200px;">item</th>
                    <th>name</th>
                    <th class="text-right" style="width:150px;">Quantity</th>
                    <th class="text-right" style="width:150px;">Unit Price</th>
                    <th class="text-right" style="width:150px;">Discount</th>
                    <th class="text-right" style="width:150px;">Total</th>
                </tr>
            </thead>
            <tbody>
    `;

  let i = 0;
  let sum = 0;
  data.length > 0
    ? data.map(model => {
        i++;
        let lineDiscount = model.discount ? (model.discount * 1) : 0;
        let totals  = (model.qty * model.price) - ((lineDiscount /100) * (model.qty * model.price));
        let total = Number((totals).toFixed(2));
       // let total = Number((model.qty * model.price).toFixed(2));
        sum += total;
        html += `
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
      })
    : null;

  html += `</tbody>
            </table>`;

  let tableTotals = (sum, html, callback) => {
    let header = JSON.parse(localStorage.getItem("sale-header"));
    let total, vat, vat_revert, sumTotal;

    if (header.incvat === "0") {
      //vat ใน
      vat_revert = header.vat / 100 + 1;
      vat = sum - sum / vat_revert;
      sumTotal = number_format((sum - vat).toFixed(2));
      total = number_format(sum.toFixed(2));
    } else {
      // vat นอก
      vat = (sum * header.vat) / 100;
      sumTotal = number_format(sum.toFixed(2));
      total = number_format((vat + sum).toFixed(2));
    }

    callback({
      total: sumTotal,
      vat: number_format(vat.toFixed(2)),
      grand: total,
      html: html,
      _vat: header.vat,
      _incvat: header.incvat
    });
  };

  tableTotals(sum, html, res => {
    $("body")
      .find("#sum-total")
      .html(res.total);
    $("body")
      .find("#sum-vat")
      .html(res.vat);
    $("body")
      .find("#grand-total")
      .html(res.grand);

    $("body")
      .find("#renders-editable")
      .html(res.html);
  });
};

let findItems = (search, callback) => {
  let customer = JSON.parse(localStorage.getItem("customer"));

  fetch("?r=SaleOrders/backwards/find-items", {
    method: "POST",
    body: JSON.stringify({ search: search, customer: customer.id }),
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

$("body").on("keypress", 'input[type="text"], input[type="number"]', function(
  e
) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});

$("body").on("keydown", 'input[name="add-code"]', function(e) {
  let search = $(this).val();

  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;

  if (keyCode === 13 || keyCode === 9) {
    e.preventDefault();
    if (search.trim()) {
      findItems(search, res => {
        if (res.status === 200) {
          soundClick.play();
          $("body")
            .find('input[name="add-code"]')
            .val(res.items[0].code)
            .attr("data-id", res.items[0].id)
            .attr("data-barcode", res.items[0].barcode)
            .css({ background: "#fff", color: "#555" });

          $("body")
            .find('input[name="add-name"]')
            .val(res.items[0].name)
            .attr("data-desc", res.items[0].name_en)
            .css({ background: "#fff", color: "#555" })
            .focus();

          if (res.items[0].lastprice !== "") {
            let price = Number(res.items[0].lastprice);
            $("body")
              .find('input[name="add-price"]')
              .val(price);
          }
        } else {
          $("body")
            .find('input[name="add-code"]')
            .css({ background: "#000", color: "#fff" });
          soundError.play();
          $("body")
            .find('input[name="add-name"]')
            .val(res.message)
            .css({ background: "#000", color: "#fff" });
        }
      });
      return false;
    } else {
      soundError2.play();
    }
  }
});

$("body").on("keypress", 'input[name="add-name"]', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    $("body")
      .find('input[name="add-qty"]')
      .focus();
    return false;
  }
});

$("body").on("keypress", 'input[name="add-qty"]', function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    $("body")
      .find('input[name="add-price"]')
      .select()
      .focus();
    return false;
  }
});

// Add to Line and show new input
$("body").on("keypress", 'input[name="add-price"]', function(e) {
  // let data = JSON.parse(localStorage.getItem("new-sale-line"));
  // let newData = {
  //   id: $('input[name="add-code"]').attr("data-id")
  //     ? Number($('input[name="add-code"]').attr("data-id"))
  //     : 1414,
  //   barcode: $('input[name="add-code"]').attr("data-barcode"),
  //   code: $('input[name="add-code"]').val(),
  //   name: $('input[name="add-name"]').val(),
  //   name_en: $('input[name="add-name"]').attr("data-desc"),
  //   price: Number($('input[name="add-price"]').val()),
  //   qty: Number($('input[name="add-qty"]').val()),
  //   status: true,
  //   unit: $('input[name="add-name"]').attr("data-unit")
  // };
  // let update = data.concat(newData);

  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    //soundEnterkey.play();
    //renderTable(update);
    // $("body")
    //   .find('input[name="add-code"]')
    //   .focus();
    let total = $('input[name="add-qty"]').val() * $('input[name="add-price"]').val();
    $("body")
      .find('input[name="add-totalprice"]')
      .val(total)
      .select()
      .focus();
    //localStorage.setItem("new-sale-line", JSON.stringify(update));
    return false;
  }
});


// Add to Line callculate price from sumline
$("body").on("keypress", 'input[name="add-totalprice"]', function(e) {
  let data = JSON.parse(localStorage.getItem("new-sale-line"));
  let discount = $('input[name="add-discount"]').val();
  let newData = {
    id: $('input[name="add-code"]').attr("data-id")
      ? Number($('input[name="add-code"]').attr("data-id"))
      : 1414,
    barcode: $('input[name="add-code"]').attr("data-barcode"),
    code: $('input[name="add-code"]').val(),
    discount: discount != 0 ? discount : 0,
    name: $('input[name="add-name"]').val(),
    name_en: $('input[name="add-name"]').attr("data-desc"),
    price: Number(Number($('input[name="add-totalprice"]').val()) / Number($('input[name="add-qty"]').val())),
    qty: Number($('input[name="add-qty"]').val()),
    status: true,
    unit: $('input[name="add-name"]').attr("data-unit")
  };

  let update = data.concat(newData);

  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    soundEnterkey.play();
    renderTable(update);
    $("body")
      .find('input[name="add-code"]')
      .focus();
    localStorage.setItem("new-sale-line", JSON.stringify(update));
    return false;
  }

});

// Delete Line
$("body").on("click", "button.delete-line", function() {
  let id = Number(
    $(this)
      .closest("tr")
      .index()
  );
  let tr = $(this).closest("tr");
  let code = $(this)
    .closest("tr")
    .find('input[name="name"]')
    .val();

  let deleteLine = id => {
    let data = JSON.parse(localStorage.getItem("new-sale-line"));
    let line = data.filter((model, key) => (key !== id ? model : null));
    localStorage.setItem("new-sale-line", JSON.stringify(line));
    renderTable(line);
  };

  if (confirm('ต้องการลบรายการ "' + code + '" ?')) {
    tr.css("background-color", "#aaf7ff");
    soundClick2.play();
    tr.fadeOut(300, function() {
      tr.remove();
      deleteLine(id);
    });
  }
});

$("body").on("change", 'input[name="qty"]', function() {
  let el      = $(this).closest("tr").index();
  let row     = $(this).closest("tr").attr("data-row");
  let qty     = $(this).val();
  let data    = JSON.parse(localStorage.getItem("new-sale-line"));
  let update  = data.map((model, key) => {
                return key === el ? Object.assign({}, model, { qty: Number(qty) }) : model;
              });
  localStorage.setItem("new-sale-line", JSON.stringify(update));
  renderTable(update);
  setTimeout(() => {
    $("body").find('tr[data-row="' + row + '"]').find('input[name="price"]').select().focus();
  }, 100);
});

$("body").on("keypress", 'input[name="qty"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let el    = $(this).closest("tr").index();
    let row   = $(this).closest("tr").attr("data-row");
    let qty   = $(this).val();
    let data  = JSON.parse(localStorage.getItem("new-sale-line"));
    let update = data.map((model, key) => {
      return key === el
        ? Object.assign({}, model, { qty: Number(qty) })
        : model;
    });
    localStorage.setItem("new-sale-line", JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      $("body").find('tr[data-row="' + row + '"]').find('input[name="price"]').select().focus();
    }, 100);
  }
});

$("body").on("change", 'input[name="price"]', function() {
  let el = $(this)
    .closest("tr")
    .index();
  let price = $(this).val();
  let data = JSON.parse(localStorage.getItem("new-sale-line"));
  let update = data.map((model, key) => {
    return key === el
      ? Object.assign({}, model, { price: Number(price) })
      : model;
  });
  localStorage.setItem("new-sale-line", JSON.stringify(update));
  renderTable(update);
});

$("body").on("keypress", 'input[name="price"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let el    = $(this).closest("tr").index();
    let row   = Number($(this).closest("tr").attr("data-row")); 
    let price = $(this).val();
    let data  = JSON.parse(localStorage.getItem("new-sale-line"));
    let update = data.map((model, key) => {
      return key === el
        ? Object.assign({}, model, { price: Number(price) })
        : model;
    });
    localStorage.setItem("new-sale-line", JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      // ถ้าแถวสุดท้าย ให้ไป focus ที่บรรทัดใหม่
      if (row === update.length) {
        $("body").find('input[name="add-code"]').select().focus();
      } else {
        // ถ้าไม่ใช่บรรทัดสุดท้าย ให้ไป input ถัดไป
        $("body").find('tr[data-row="' + row + '"]').find('input[name="discount"]').select().focus();
      }
    }, 100);
  }
});


$("body").on("change", 'input[name="discount"]', function() {
  let el  = $(this).closest("tr").index();
  let discount = $(this).val();
  let data = JSON.parse(localStorage.getItem("new-sale-line"));
  let update = data.map((model, key) => {
    return key === el
      ? Object.assign({}, model, { discount: Number(discount) })
      : model;
  });
  localStorage.setItem("new-sale-line", JSON.stringify(update));
  renderTable(update);
});

$("body").on("keypress", 'input[name="discount"]', function(e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    let el    = $(this).closest("tr").index();
    let row   = Number($(this).closest("tr").attr("data-row")) + 1; // next row
    let discount = $(this).val();
    let data  = JSON.parse(localStorage.getItem("new-sale-line"));
    let update = data.map((model, key) => {
      return key === el
        ? Object.assign({}, model, { discount: Number(discount) })
        : model;
    });
    localStorage.setItem("new-sale-line", JSON.stringify(update));
    renderTable(update);
    setTimeout(() => {
      // ถ้าแถวสุดท้าย ให้ไป focus ที่บรรทัดใหม่
      if (row === update.length) {
        $("body").find('input[name="add-code"]').select().focus();
      } else {
        // ถ้าไม่ใช่บรรทัดสุดท้าย ให้ไป input ถัดไป
        $("body").find('tr[data-row="' + row + '"]').find('input[name="qty"]').select().focus();
      }
    }, 100);
  }
});

$("body").on("click", 'input[name="qty"], input[name="price"]', function() {
  $(this)
    .select()
    .focus();
});

// Header Change
$("body").on(
  "change",
  "#saleheader-invoice_no, #saleheader-ext_document, #saleheader-order_date, #saleheader-vat_percent, #saleheader-remark, #saleheader-include_vat",
  function() {
    let header = JSON.parse(localStorage.getItem("sale-header"));
    header = Object.assign({}, header, {
      vat: $("#saleheader-vat_percent").val(),
      incvat: $("#saleheader-include_vat").val(),
      date: $("body")
        .find("#saleheader-order_date")
        .val(),
      inv: $("body")
        .find("#saleheader-invoice_no")
        .val(),
      po: $("body")
        .find("#saleheader-ext_document")
        .val(),
      remark: $("body")
        .find("#saleheader-remark")
        .val()
    });
    localStorage.setItem("sale-header", JSON.stringify(header));
    let data = localStorage.getItem("new-sale-line")
      ? JSON.parse(localStorage.getItem("new-sale-line"))
      : [];
    renderTable(data);
    // **** Register อีกที่ ใน DatePicker (php)
  }
);

// Payment Term change
$("body").on("change", 'select[name="payment_term"]', function() {
  console.log($(this).val());
  let customer = JSON.parse(localStorage.getItem("customer"));
  customer = Object.assign({}, customer, { term: Number($(this).val()) });
  localStorage.setItem("customer", JSON.stringify(customer));
  $("body")
    .find(".next-to-upload")
    .addClass("next-step text-success btn-success-ew");
});






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

const loadData = () => {

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