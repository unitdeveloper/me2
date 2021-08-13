/*!
 * EWIN
 * v4.04.14 - 2019
 * (c) Assawin.Thkch; MIT License
 */
let switch_promo = 0;
 
 

let LoadAjax = () => {
    $(".ew-add").hide();
    loadFindBox(".render-search-item");
}

const NewSaleLine = (data,that) => {
  $.ajax({
    url: "index.php?r=SaleOrders/return/create-saleline",
    type: "POST",
    data: data,
    success: function(res) {
      let obj = $.parseJSON(res);  
      if(obj.data.status===200){
        
        //Close Modal
        //$("#PickItem-Modal").modal("hide");
        $("body").attr("style", "overflow:auto;");
        //$("#ewSelect").hide();  
        
        LoadAjax();
        $(".SaleLine").html(obj.html);        
        getSumLine($("#ew-discount-amount").attr("data"), "discount");
        $("html, body").animate({ scrollTop: $(".grid-view").offset().top - 80 },500);
        setTimeout(() => {
          $('body').find('input.InsertItem').focus();
        }, 100);
        
      }else if(obj.data.status===403){
        $.notify({
          // options
          icon: "fas fa-box-open",
          message: obj.data.message
        },{
            // settings
            placement: {
              from: "top",
              align: "right"
            },
            type: "warning",
            delay: 3000,
            z_index: 3000
        });
        
      }else{
        $.notify({
          // options
          icon: "fas fa-box-open",
          message: obj.data.message
        },{
            // settings
            placement: {
              from: "top",
              align: "center"
            },
            type: "error",
            delay: 4000,
            z_index: 3000
        }); 

        if(obj.data.status===404){
          $.notify({
            // options
            icon: "fas fa-luggage-cart",
            message: obj.data.suggestion + ' : ' + number_format(obj.data.reserve)
          },{
              // settings
              placement: {
                from: "top",
                align: "center"
              },
              type: "warning",
              delay: 8000,
              z_index: 3000
          });    
        }

      }
        
      $(that).find("i").attr("class", "fa fa-check");
      $(that).attr("disabled", false);

    }
  });
}

$(document).ready(function() {
   
  LoadAjax();

  if ($("#salereturnheader-vat_percent").val() > 0) {
    // Vat
    $("#Exc-vat").show("fast");
  } else {
    $("#Exc-vat").hide("fast");
  }

  if (Number($("#ew-line-total").attr("data")) <= 0) {
    $(".ew-sum-line").hide();
  } else {
    $(".ew-sum-line").show();
  }

  getSumLine($("#ew-discount-amount").attr("data"), "discount");

  $("body").on("click", "a.get-promotions-switch", function() {
    switch_promo = 1;
  });

  $("body").on("click", "#click-label-discount", function() {
    $("#ew-discount-amount")
      .focus()
      .select();
  });

  // Modal load PickItem-Modal
  $("body")
    .find("#PickItem-Modal")
    .on("shown.bs.modal", function() {
      $("html").addClass("freezePage");
      $("body").addClass("freezePage");
 
    });

  $("body")
    .find("#PickItem-Modal")
    .on("hidden.bs.modal", function() {
      $("html").removeClass("freezePage");
      $("body").removeClass("freezePage");
      setTimeout(() => {
        $('body').find('.loading-absolute').fadeOut('slow');
      }, 500); 
    });

    setTimeout(() => {
      $.ajax({
        url:"index.php?r=SaleOrders/return/get-line",
        type: "POST",
        data: {id: $('body').find("#SaleOrder").attr("ew-so-id")},
        async:false,
        success:function(response){
          $('body').find('.SaleLine').html(response);
        }
      });
      //ajaxRoute( "index.php?r=SaleOrders/return/get-line","POST",{id: $('body').find("#SaleOrder").attr("ew-so-id")},"SaleLine");
    }, 10);
    
});

(function($, window, document, undefined) {
  $("#salereturnheader-payment_term").on("change", function() {
    var today = new Date($("#salereturnheader-order_date").val());
    var date = new Date(today),
      days = parseInt($("#salereturnheader-payment_term").val(), 10);

    if (!isNaN(date.getTime())) {
      date.setDate(date.getDate() + days);

      $('input[id="salereturnheader-paymentdue"]').val(date.toInputFormat());
    } else {
      alert("Invalid Date");
    }
  });

  //From: http://stackoverflow.com/questions/3066586/get-string-in-yyyymmdd-format-from-js-date-object
  Date.prototype.toInputFormat = function() {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return (
      yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0])
    ); // padding
  };
})(jQuery, this, document);


$("body").on("change", "#salereturnheader-sales_people", function() {
  var $status = $("#salereturnheader-status").val();

  if ($("#salereturnheader-customer_id").val() == "") {
    if ($.inArray($status, ["Open", "Cancel"]) < 0) {
      $("#salereturnheader-status").val("Open");
      swal('ดูเหมือนว่า "ยังไม่ได้เลือกลูกค้า"', "กรุณาเลือกลูกค้า", "warning");

      return false;
    }
  }
});

let CreateItemAndBom = (post, callback) => {

    	$.ajax({
          url:"index.php?r=Manufacturing/ajax/create-bom",
          type: "POST",
          data: {
            param:{
                  post: post,
                  desc: $.trim($('#ew-real-desc').text()),
                  group: $('#ew-gen-code').attr('ew-id'),
                  item: $('.ew-Validate').attr('data-key'),
                  id: $('#ew-gen-code').attr('ew-id'),
                  price: $('input[name="Price"]').val(),
            }
          },
          async:false,
          success:function(response){
            callback($.parseJSON(response));
          }
        });
    }


$("#Form-SaleReturnOrder").on("keypress", function(e) {
  // Disable form submit on enter.
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});

$("body").on("click", "#ewSelect", function() {
  var that = this;
  $(that).find("i").attr("class", "fas fa-sync-alt fa-spin");
  $(that).attr("disabled", true);

  CreateItemAndBom($(".ew-Code").attr("ew-post-param"), res => {
    let obj = res.message;    

    let itemid      = obj.message==='create' 
                        ? obj.id     
                        : ($('body').find('#ew-render-itemno').attr('data-key') 
                          ? $('body').find('#ew-render-itemno').attr('data-key') 
                          : '');
    var itemno      = obj.message==='create' ? obj.value  : $(".ew-Validate").attr("data-key");
    var unit_price  = $("input[name=Price]").val();
    


    if(obj.message==='create'){
      // itemid = obj.id;
      // itemno = obj.value;
      $('body').find('.ew-Validate').text(obj.code);
      $('body').find('.ew-Validate').attr('data-key',obj.id);
    }

    if(obj.message==='exists'){
      itemid = obj.id;      
    }
    

    // if (unit_price == 0) {
    //   alert('ไม่มีราคา');
    // }


    var data = {
      param: {
        itemid: itemid,
        itemno: itemno.toString(),
        itemset: $("#itemset").val(),
        soid: $("#Form-SaleReturnOrder").attr('data-key'),
        amount: $("input[name=Quantity]").val(),
        price: unit_price,
        discount: $("input[name=Discount]").val()
      }
    };
    NewSaleLine(data, that);
     
    
  });

});

$("body").on("click", ".ew-PickItem", function() {
  
  $('body').find('.loading-absolute').fadeIn(); 
  var data = {
    param: {
      itemno: $(this).attr("itemno"),
      orderno: $("#salereturnheader-no").val(),
      pset: $(this).attr("itemset"),
      itemset: $(this).attr("itemset")
    }
  };  

  setTimeout(() => {        
    if ($(this).attr("ew-bom") === "enabled") {
      $.ajax({
        url: "index.php?r=Itemset/bomset/view&id=" + $(this).attr("itemset"),
        type: "POST",
        data: data,
        async: true,
        success: function(getData) {
          $(".ew-create-item").html(getData);           
          $("#PickToSaleLine").hide();
          setTimeout(() => {
              $('body').find('.loading-absolute').fadeOut('slow');
              $('#PickItem-Modal').modal('show');
          }, 500); 
        }
      });
    } else {
      // render _modal_pickitem
      $.ajax({
        url: "index.php?r=SaleOrders/return/viewitem&test=1",
        type: "POST",
        data: data,
        async: false,
        success: function(response) {
         
          $("body")
            .find("div.ew-create-item")
            .html(response);
            setTimeout(() => {
                $('body').find('.loading-absolute').fadeOut('slow');   
                $('#PickItem-Modal').modal('show');             
            }, 500); 

          loadItem($("#itemno").val());
          $("#PickToSaleLine").hide();
        }
      });

      
    }

    $(".modal-title").html($(this).attr("ew-set-name"));
    $("body").attr("style", "overflow:hidden; margin-right:0px;");

  }, 500);
});

$("body").on("mouseout", ".ItemGrid", function() {
  $(".btn-detail-group").css("visibility", "hidden");
});

$("body").on("change", "#salereturnheader-vat_percent", function() {
  var thiscond = $(this).val();
  if ($(this).val() > 0) {
    // Vat
    $("#Exc-vat").show("fast");
  } else {
    $("#Exc-vat").hide("fast");
  }

  getSumLine($("#ew-discount-amount").attr("data"), "discount");
});

$("body").on("change", "#salereturnheader-include_vat", function() {
  getSumLine($("#ew-discount-amount").attr("data"), "discount");
});

$("body").on("click", ".RemoveSaleLine", function() {
  var itemno = $(this).attr("href");
  var id = itemno.substring(1);
  var orderno = $("#SaleOrder").attr("ew-so-id");
  var alt = $(this).attr("alt");

  var data = {
    param: {
      lineno: id,
      orderno: orderno
    }
  };

  var tr = $('tr[data-key="' + itemno.substring(1) + '"]');

   
  // ----- Do confirm delete.-----
  if (confirm('ต้องการลบรายการ "' + alt + '" ?')) {
    $.ajax({
      url: "index.php?r=SaleOrders/return/delete_line&id="+orderno,
      type: "POST",
      data: data,
      async: true,
      success: function(res) {

        tr.css("background-color", "#aaf7ff");
        tr.fadeOut(500, function() {
          tr.remove();
        });

        LoadAjax();

        getSumLine($("#ew-discount-amount").attr("data"), "discount");

        if (Number($("#ew-line-total").attr("data")) <= 0) {
          $(".ew-sum-line").hide();
        } else {
          $(".ew-sum-line").show();
        }
      }
    });
  }
  // ----- /. Do confirm delete.-----

});

$("body").on("change", "#ew-text-editor", function(i, el) {
  var $div = $.trim(
    $(this)
      .parent("div")
      .parent("td")
      .attr("class")
  );
  var input = $(this);

  if ($div === "ew-sl-qty text-right") {
    var pre = $(this)
      .parent("div")
      .parent("td")
      .parent("tr")
      .find("a.RemoveSaleLine")
      .attr("qty");
    var revert =
      '<div id="ew-qty-edit" ew-line-no="' +
      input.attr("ew-lineno") +
      '">' +
      pre +
      "</div>";
  } else {
    var pre = $(this)
      .parent("div")
      .parent("td")
      .parent("tr")
      .find("a.RemoveSaleLine")
      .attr("price");
    var revert =
      '<div id="ew-price-edit" ew-line-no="' +
      input.attr("ew-lineno") +
      '">' +
      pre +
      "</div>";
  }

  var data = {
    param: {
      orderno: $("#salereturnheader-no").val(),
      lineno: $(this).attr("ew-lineno"),
      updatefield: $(this).attr("name"),
      edit: $(this).val()
    }
  };
  // Validate shipment before delete this line.
  $.get(
    "index.php?r=SaleOrders/ajax/has-ship",
    { source: $(this).attr("ew-lineno") },
    function(getData) {
      var obj = jQuery.parseJSON(getData);

      if (obj.id == "Pass") {
        // ----- Do change number.-----

        //route("index.php?r=SaleOrders/saleorder/update-sale-line","POST",data,"SaleLine");
        LoadAjax();
        getSumLine($("#ew-discount-amount").attr("data"), "discount");
        // ----- /. Do change number.-----
      } else {
        //Already Exists
        //------ Shiped this line.--------

        swal(
          "สินค้าถูกบรรจุแล้ว",
          "ต้อง 'ยกเลิก' รายการ \"" + obj.doc + '"  ก่อนทำการแก้ไข"',
          "warning"
        );

        input.parent("div").html(revert);

        //alert(pre);
        //------/. Shiped this line.--------
      }
    }
  );
});

/* Sale Line */
$("body").on("click", ".ew-sl-qty", function() {
  var lineNumber = $(this)
    .children("div")
    .attr("ew-line-no");
  var value_txt = $(this)
    .text()
    .replace(",", "");

  if (value_txt === "") {
    value_txt = $(this)
      .children("div")
      .children("input")
      .val();
  }

  var text_qty =
    '<div class="pull-right" ew-line-no="' +
    lineNumber +
    '"><input type="number" name="qty" id="ew-text-editor" value="' +
    value_txt +
    '" ew-lineno="' +
    lineNumber +
    '" class="form-control text-right" style="width:80px;"></div>';

  $(this).html(text_qty);
  $(this)
    .children("div")
    .children("input")
    .focus();
  $(this)
    .children("div")
    .children("input")
    .select();
});

$("body").on("click", ".ew-sl-price", function() {
  var lineNumber = $(this)
    .children("div")
    .attr("ew-line-no");
  var value_txt = $(this)
    .text()
    .replace(",", "");

  if (value_txt === "") {
    value_txt = $(this)
      .children("div")
      .children("input")
      .val();
  }
  var text_qty =
    '<div class="pull-right" ew-line-no="' +
    lineNumber +
    '"><input type="number" name="price" id="ew-text-editor" value="' +
    value_txt +
    '" ew-lineno="' +
    lineNumber +
    '" class="form-control text-right" style="width:100px;"></div>';

  $(this).html(text_qty);
  $(this)
    .children("div")
    .children("input")
    .focus();
  $(this)
    .children("div")
    .children("input")
    .select();
});

$("body").on("click", ".ew-filter-onclick", function(el) {
  var href = $(this).attr("href").slice(1);
  var $this= $(this);
  var data = {
                param: {
                  href: href
                }
              };
              
  var template = '<div class="ccc" style="width: 100%;height: 100%;background: #00000078;position: absolute;z-index: 10;right: 0; top:0px;"></div>';
  $(".FilterResource").prepend(template);
  $(".widget-user-image").addClass("text-right").html('<i class="fas fa-sync fa-spin fa-3x " style="margin-left: 29px;color:#fff"></i>');

  $(this).attr("style", "background-color: #ccc;");

  $.ajax({
    url: "index.php?r=SaleOrders/ajax/items",
    data: data,
    type: "POST",
    async: true,
    success: function(response) {
      $("body")
        .find(".ResourceItemSearch")
        .html(response);

      $("body")
        .find(".ccc")
        .remove();
      $($this).attr("style", " ");
    }
  });
});

$("body").on("click", ".ew-fsize span", function(e) {
  $(".ew-type").toggle();
  $(".InsertItem").toggle();
  $(".InsertItem").focus();
});

$("body").on("keyup", ".InsertItem", function(e) {
  var len = $.trim($(this).val()).length;

  if (len >= 3) {
    if (e.which === 32 || e.which === 13) {
      // 32 Space bar
      FindItemsJson($(this));
    }

    if (e.which == 9) {
      if ($("#InsertDesc").attr("ew-item-code") != "eWinl") {
        $("#InsertDesc").first().focus();
      }

      var inputItem = $.trim($(".InsertItem").val());
      $(".InsertItem").val(inputItem);

      $.ajax({
        url: "index.php?r=SaleOrders/ajax/json-find-item",
        type: "POST",
        data: { param: { item: inputItem } },
        async: true,
        success: function(getData) {
          var obj = jQuery.parseJSON(getData);

          $(".ew-desc").show();
          $("#InsertDesc").val(obj.desc);
          $("#InsertDesc").attr("ew-item-code", obj.item);
          $(".ew-qty").show();
          $("#InsertQty").val(1);
          $(".ew-price").show();
          $("#InsertPrice").val(obj.std);
          $("#item-id").val(obj.id);

          if (obj.code != "eWinl") {
            $(".ew-add").show();
          } else {
            $(".ew-add").hide();
          }
        }
      });

      getSumLine($("#ew-discount-amount").attr("data"), "discount");
    }
  } else {
    $(".find-item").slideUp();
  }
});

$("body").on("keydown", "#InsertDesc", function(e) {
  if (e.which == 13) {
    $("#InsertQty")
      .first()
      .focus();
  }
});

$("body").on("keydown", "#InsertQty", function(e) {
  if (e.which == 13) {
    $("#InsertPrice")
      .first()
      .focus();
  }
});

// Add to Sale Line.
// ---------->
$("body").on("click", ".ew-add", function(e) {
  CreateSaleLine();
});

$("body").on("keydown", ".ew-add", function(e) {
  if (e.which == 13) {
    CreateSaleLine();
  }
});

$("body").on("keydown", "#InsertPrice", function(e) {
  if (e.which == 13) {
    CreateSaleLine();
  }
});

function CreateSaleLine() {
  if ($("#InsertDesc").attr("ew-item-code") === "eWinl") {
    if ($("#InsertType").val() == "G/L") {
      alert("ขณะนี้ ยังไม่เปิดให้ใช้งาน G/L");
    } else {
      alert('ไม่มี Item "' + $(".InsertItem").val() + '"');
    }
  } else {
    if ($("#InsertPrice").val() === "" || $("#InsertPrice").val() === "0") {
      alert('คุณกำลังพยายามใส่ "ราคา 0 บาท"');
    }

    var data = {
      param: {
        itemid: $("#item-id").val(),
        itemno: $("#InsertDesc").attr("ew-item-code"),
        itemset: 0,
        soid: $("#Form-SaleReturnOrder").attr('data-key'),
        amount: $("#InsertQty").val(),
        price: $("#InsertPrice").val(),
        desc: $("#InsertDesc").val()
      }
    };
    NewSaleLine(data, $(this));

  }
  getSumLine($("#ew-discount-amount").attr("data"), "discount");
}

$("body").on("click", ".pick-item-to-createline", function() {
  //PickToSaleLine($(this));
  var that = this;
  var data = {
    param: {
      itemid: $(this).attr("data-id"),
      itemno: $(this).attr("itemno"),
      itemset: 0,
      soid: $("#Form-SaleReturnOrder").attr('data-key'),
      amount: 1,
      price: $(this).attr("price"),
      desc: $(this).attr("desc")
    }
  };
  NewSaleLine(data, that);
});

function PickToSaleLine($this) {
  var $data = {
    param: {
      itemid: $this.attr("data-id"),
      itemno: $this.attr("itemno"),
      itemset: 0,
      soid: $("#Form-SaleReturnOrder").attr('data-key'),
      amount: 1,
      price: $this.attr("price"),
      desc: $this.attr("desc")
    }
  };
  //console.log($data);
  NewSaleLine($data, $this);


  LoadAjax();

  getSumLine($("#ew-discount-amount").attr("data"), "discount");
}
// <--------
// End add to Sale Line

$("body").keydown(function(event) {
  if (event.which == 27) {
    // ESC

    $("#ew-modal-Approve").modal("hide");
  } else if (event.which == 112) {
    // F1
    //alert('F1');
    $(".reject-reason #reason-text").focus();
  } else if (event.which == 113) {
    //F2
    //alert('F2');
  } else if (event.which == 114) {
    //F3
    if (confirm("Create New Document?")) {
      window.location.replace("index.php?r=SaleOrders/return/create");
    }
    return false;
  } else if (event.which == 116) {
    //F5

    alert("F5");
  } else if (event.which == 118) {
    //F7
    //$('#ew-modal-Approve').modal('toggle');
    BtnApprove($("#ew-reject"));
  } else if (event.which == 121) {
    //F10
    //$('#ew-modal-Approve').modal('toggle');
    BtnApprove($("#ew-confirm"));
  } else if (event.which == 13) {
    // Enter
    // If Model Open
    if ($(".ew-confirm").is(":visible")) {
      //alert('Confirm');

      Approve("ew-approve-body", $("#ew-data-text").text());
    }
  }
});

$("body").on("click", "#ew-Item-Info", function() {
  $("#ewItemInfoModal").modal("show");
  $("#PickToSaleLine").hide();

  var items = $(this).attr("ew-item-no");
  $(".ew-item-info-body").show();

  $(".ew-render-item-info").html(
    '<div style="position:absolute; left:45%;">' +
      '<i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>' +
      '<div class="blink" > Loading .... </div></div>'
  );

  setTimeout(function(e) {
    $(".ew-render-item-info").slideUp();
    $.ajax({
      url: "index.php?r=items/items/view-modal",
      type: "GET",
      data: { id: items },
      async: true,
      success: function(getData) {
        $(".ew-render-item-info")
          .html(getData)
          .slideDown("slow");
        //$('.ew-render-item-info').slideDown( "slow" );
      }
    });

  }, 1000);
});

$("body").on("click", ".close-ewItemInfoModal", function() {
  $("#ewSelect").hide();
  $("#PickToSaleLine").hide();

  $(".ew-item-info-body").slideToggle();
  $(".ew-render-item-info").html(
    '<div style="position:absolute; left:45%;">' +
      '<i class="fa fa-refresh fa-spin fa-2x fa-fw text-success" aria-hidden="true"></i>' +
      '<div class="blink"> Loading .... </div></div>'
  );

  setTimeout(function(e) {
    $("#ewItemInfoModal").modal("hide");
    $('#ewSelect').attr('disabled', false).find('i').attr('class', 'fa fa-power-off');
  }, 350);
});

$("body").on("click", "#ew-modal-pick-cust", function() {
  //$('#ewPickCustomer').modal('show');
  $.ajax({
    url:"index.php?r=customers/customer/pick-customer",
    type: "GET",
    data: { search: "", id: $("#Form-SaleReturnOrder").attr("data-key") },
    async:false,
    success:function(response){
      $('body').find('.ew-Pick-Customer').html(response);
    }
  });
  //ajaxRoute("index.php?r=customers/customer/pick-customer","GET",{ search: "", id: $("#SaleOrder").attr("ew-so-id") },"ew-Pick-Customer");
});

//------ Select Customer ---------
$("body").on("change", "#ew-search-text", function() {
  $.ajax({
    url:"index.php?r=customers/customer/pick-customer",
    type: "GET",
    data: { search: $(this).val(), id: $("#Form-SaleReturnOrder").attr("data-key") },
    async:false,
    success:function(response){
      $('body').find('.ew-Pick-Customer').html(response);
    }
  });
  //ajaxRoute("index.php?r=customers/customer/pick-customer","GET",{ search: $(this).val(), id: $("#SaleOrder").attr("ew-so-id") },"ew-Pick-Customer");
});

$("body").on("click", "#ew-search-btn", function() {
  $.ajax({
    url:"index.php?r=customers/customer/pick-customer",
    type: "GET",
    data: { search: "", id: $("#Form-SaleReturnOrder").attr("data-key") },
    async:false,
    success:function(response){
      $('body').find('.ew-Pick-Customer').html(response);
    }
  });
  //ajaxRoute("index.php?r=customers/customer/pick-customer","GET",{search: $("#ew-search-text").val(),id: $("#SaleOrder").attr("ew-so-id")},"ew-Pick-Customer");
});
//------/. Select Customer -------

const updateField = (obj, callback) => {
  fetch("?r=SaleOrders%2Freturn%2Fupdate-field", {
    method: "POST",
    body: JSON.stringify(obj),
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
    },
  })
  .then(res => res.json())
  .then(response => { callback(response); })
  .catch(e => { swal("Fail!", "Something Wrong. "+ e.responseText + new Date().toTimeString().slice(0, 8), "error"); });
}

$("body").on("click", "#ew-pick-customer", function() {
  if (confirm("ยืนยันการเลือกลูกค้า ! ")) {
    let id        = $('#Form-SaleReturnOrder').attr('data-key');
    let cust      = $(this).attr('ew-val');
    var tr = $(this).closest("tr");
    $("#salereturnheader-customer_id").val($(this).attr("ew-val"));
    $("#ewPickCustomer").modal("hide");
    var index     = $(this).index() + 1;
    let province  = tr.find("td").eq(3).text();
    let customer  = tr.find("a#ew-pick-customer").eq(2).text() +" (" +province.trim() +")";

    $("#ew-modal-pick-cust").html(customer);
    $("#salereturnheader-payment_term").val($(this).attr("data-payment"));
    $("#customer-payment").addClass("in");

    $("#SaleOrder").html('<i class="far fa-address-card fa-2x  text-green"></i> ' + customer);
    $("#collapseOne").collapse();
    $('a[data-target="#customer-infomation"]').hide();

    var today = new Date();
    var date  = new Date(today),
        days  = parseInt($("#salereturnheader-payment_term").val(), 10);

    if (!isNaN(date.getTime())) {
      date.setDate(date.getDate() + days);
      $("#salereturnheader-paymentdue").val(date.toInputFormat());
    }
    
    updateField({id:id,field:'customer_id', value:cust}, res => {
      if(res.status===200){
        console.log(res);
      }else{
        swal("Fail!",response.message,"error");
      }
    });

  } else {
    return false;
  }
});

function getSumLine(discount, key) {


  $("body").find(".recommend-discount").fadeOut(300, function() {
      $(this).remove();
  });

  let data = {
      id: $("a[id=SaleOrder]").attr("ew-so-id"),
      discount: discount,
      vat_percent: $("#salereturnheader-vat_percent").val(),
      inc_vat: $("#salereturnheader-include_vat").val(),
      percent: $("#ew-discount-percent").val(),
      credit: $("#salereturnheader-payment_term").val(),
      due: $("#salereturnheader-paymentdue").val(),
      key: key
  };
/*
  $.ajax({
    url: "index.php?r=SaleOrders/return/percent-discount",
    type: "POST",
    data: data,
    dataType: "JSON",
    success: function(response) {
      $("body").find("div.ew-sum-line").html(response.html).show();

      let totalDiscount = 0;
      model             = response.promo.data;
      let html = '<div class="row recommend-discount" >';
      html += '    <div class="col-sm-6 col-xs-12 pull-right">';
      html += '        <a href="#promotions" data-toggle="collapse" data-target="#get-promotions" class="collapsed text-orange get-promotions-switch" aria-expanded="false">';
      html += '        <i class="far fa-newspaper text-orange blink"></i> ' + response.promo.text.label_promotion + "</a>";
      html += '        <div id="get-promotions" class="' + (switch_promo === 1 ? "collapse fade  in" : "collapse fade") + '" >';
      html += '        <table class="table table-bordered">';
      html += "            <thead>";
      html += '                <tr class="bg-orange">';
      html += '                    <th class="">' + response.promo.text.label_promotion + "</th>";
      html += '                    <th class="text-right">' + response.promo.text.label_buy + "</th>";
      html += '                    <th class="text-right">' + response.promo.text.label_getdiscount + "</th>";
      html += "                </tr>";
      html += "            </thead>";
      html += "            <tbody>";

      for (i = 0; i < model.length; i++) {
        totalDiscount += model[i].sum_discount;
        if (model[i].sum_discount > 0) {
          let promotion = parseInt(model[i].promotion);
              discount  = parseInt(model[i].sum_discount);
              curBuy    = parseInt(model[i].current_total);
              disPer    = parseInt(model[i].discount_perunit);
              title     = "Buy : " + promotion + ", Get : " + disPer;

          html += "            <tr>";
          html += '                <td class="info" alt="' + title + '" title="' + title + '">' +model[i].name +"</td>";
          html += '                <td class="text-right">' + number_format(curBuy.toFixed(2)) + "</td>";
          html += '                <td class="text-right">' + number_format(discount.toFixed(2)) + "</td>";
          html += "            </tr>";
        }
      }

      html += "                <tr>";
      html += '                    <td colspan="2" class="text-right">' + response.promo.text.label_totaldiscount + "</td>";
      html += '                    <td class="text-right bg-gray">' + number_format(totalDiscount.toFixed(2)) + "</td>";
      html += "                </tr>";
      html += "                <tr>";
      html += '                    <td colspan="3"><small>หมายเหตุ: ส่วนลดนี้ เป็นเพียงส่วนลดที่แนะนำ(เท่านั้น)</small><br /><small>ให้นำไปใส่ในช่อง <u class="text-info" id="click-label-discount">ส่วนลด</u> เพื่อคำนวนอีกครั้ง</small></td>';
      html += "                </tr>";
      html += "            </tbody>";
      html += "        </table>";
      html += "    </div>";
      html += "    </div>";
      html += "</div>";

      if (totalDiscount > 0) {
        $("body")
          .find("div.SaleLine")
          .append(
            $(html)
              .hide()
              .fadeIn(1000)
          );
        //$('body').find('input#ew-discount-amount').val(totalDiscount).change();
        //$('body').find('#ew-discount-percent').attr('disabled',true);
      } else {
        $("body")
          .find(".recommend-discount")
          .fadeOut(300, function() {
            $(this).remove();
          });
        //$('body').find('#ew-discount-percent').attr('disabled',false);
      }

      $("body")
        .find("input#ew-discount-amount")
        .attr("placeholder", totalDiscount)
        .change();
    }
  });*/
}

$("body").on("change", "#salereturnheader-payment_term", function() {
  var field = "payment_term";
  var data = $(this).val();

  $.ajax({
    url:
      "?r=SaleOrders/return/update-some-field&id=" +
      $("a[id=SaleOrder]").attr("ew-so-id"),
    type: "POST",
    data: { field: field, data: data },
    dataType: "JSON",
    success: function(res) {
      //console.log(res);
    }
  });

  if ($(this).val() === "0") {
    // var percent_disc = 3;
    // var subtotal = Number($('#ew-line-total').attr('data'));
    // var discount = (subtotal * percent_disc)/ 100;
    // $('input[id="ew-discount-amount"]').val(discount);
    // $('input[id="ew-discount-percent"]').val(percent_disc);
    // getSumLine(discount);
  } else {
    //getSumLine($('#ew-discount-amount').attr('data'));
  }
});

$("body").on("change", 'input[id="ew-discount-percent"]', function() {
  var percent_disc = $(this).val() * 1;
  var subtotal    = Number($("#ew-line-total").attr("data"));
  var discount    = (subtotal * percent_disc) / 100;

  if (percent_disc === "") {
    discount = $("#ew-discount-amount").attr("data");
  }

  getSumLine(discount, "percent");

  $('input[id="ew-discount-amount"]').val(discount);
});

$("body").on("change", 'input[id="ew-discount-amount"]', function() {
  var discount      = $(this).val() * 1;
  var subtotal      = Number($("#ew-line-total").attr("data"));
  var percent_disc  = (discount / subtotal) * 100;

  getSumLine(discount, "discount");

  $('input[id="ew-discount-percent"]').val(
    Math.round(percent_disc * 100) / 100
  );
});

$("body").on("change", ".customer_id", function() {
  var $cust = $(this).val();
  var $order = $("a#SaleOrder").attr("ew-so-id");
  $("#ew-modal-pick-cust").text(
    $("#salereturnheader-customer_id option:selected").text()
  );

  if ($cust !== "") {
    $.ajax({
      url: "index.php?r=customers/ajax/json-get-customer&id=" + $cust,
      type: "POST",
      data: { cust: $cust },
      dataType: "JSON",
      success: function(response) {
        $("#salereturnheader-payment_term").val(response.payment_term);
        $("#salereturnheader-sale_address").val(response.fulladdress);
        $("#salereturnheader-bill_address").val(response.fulladdress);
        $("#salereturnheader-ship_address").val(response.fulladdress);
        //$('#salereturnheader-sale_id').val(response.owner_sales);
        $("#customer-payment").addClass("in");
        $('a[data-target="#customer-infomation"]').hide();

        var today = new Date();
        var date = new Date(today),
          days = parseInt($("#salereturnheader-payment_term").val(), 10);

        if (!isNaN(date.getTime())) {
          date.setDate(date.getDate() + days);
          $("#salereturnheader-paymentdue").val(date.toInputFormat());
        }

         
        $('body').find('.credit-limit').html(number_format(response.credit_limit.toFixed(0)));
        $('body').find('.credit-available').html(number_format(response.credit_available.toFixed(0)));

      }
    });
  }
});

$("body").on("click", ".ew-btn-app-click", function(e) {
  BtnApprove(this);
});

$("body").one("click", ".ew-confirm", function(e) {
  Approve("ew-approve-body", $("#ew-data-text").text());
  $(".modal-footer").hide();
});

$("body").on("click", ".ew-cancel-job", function(e) {
  BtnApprove(this);

  // if (confirm('ต้องการยกเลิกใบงาน ?')) {

  //   Approve('ew-text-status','Cancel');

  // }
  // return false;
});

function Approve(div, type) {
  var appdata = {
    param: {
      apk: type,
      id: $("#SaleOrder").attr("ew-so-id"),
      cur: $("#SaleOrder").attr("ew-status"),
      reson: $("#reason-text").text()
    }
  };
  $.ajax({
    url:"index.php?r=approval/approve/sale-order",
    type: "POST",
    data: appdata,
    async:false,
    success:function(response){
      $('body').find(div).html(response);
    }
  });
  //ajaxRoute("index.php?r=approval/approve/sale-order", "POST", appdata, div);
}

function BtnApprove(e) {
  var text = $(e).attr("ew-data");
  var input =
    '<label for="reason-text">เหตุผล : </label><textarea class="form-control" id="reason-text" rows="3">ตรวจสอบรายการ</textarea>';
  var showText = ShowText(text);

  $("#ew-modal-Approve").modal("toggle");
  $("#ew-data-text").html(text);
  $("#ew-showText").html(showText);

  if (text === "Reject" || text === "Cancel" || text === "Checking") {
    $(".reject-reason").html(input);

    if (text === "Cancel") {
      $("#reason-text").text("สั่งผิด");
    }
  } else {
    $(".reject-reason").html("");
  }
}

function ShowText($text) {
  if ($text == "Checking") {
    return "ยืนยัน!";
  } else if ($text == "Confirm-Cancel") {
    return "อนุมัติคำขอยกเลิก";
  } else if ($text == "Confirm") {
    return "อนุมัติ";
  } else if ($text == "Reject") {
    return "ปฏิเสธ";
  }

  return "ยืนยัน";
}

// 05/10/17

$("body").on("keyup", "input#ew-amount", function() {
  let val = parseInt($(this).val());
  let qty = parseInt($('body').find('.text-amount').attr('data-val'));
  if(qty > 0 ){
    let total = qty - val;
    $('body').find('.text-amount').html(total < 0 ? 0 : total);
  }
});

$("body").on("keydown", "input#ew-amount", function(event) {
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {
    $("input#ew-price")
      .focus()
      .select();
  }
});
$("body").on("keydown", "input#ew-price", function(event) {
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {
    $("#PickToSaleLine").click();
  }
});

$("body").on("click", 'input[id="ew-price"],input[id="ew-amount"]', function() {
  var value = $(this).val();
  $(this).attr("placeholder", value);
  $(this).val("");
  $(this)
    .focus()
    .select();
});

function loadItem($item) {
  // ดึงรายการแรกออกมา เพื่อแสดงภาพ และกำหนดราคา
  $.ajax({
    url: "index.php?r=SaleOrders/ajax/item-getdata",
    type: "POST",
    data: { param: { item: $item } },
    async: false,
    success: function(getData) {
      //console.log("Show");
      var obj = jQuery.parseJSON(getData);

      $("#ew-price").val(0);
      $("#ew-price").prop("disabled", true);
      $("#ew-amount").prop("disabled", true);
      $(".ew-render-item").html(obj.desc);

 
      if(parseInt(obj.inven) > 0){
        $(".text-amount").html(number_format(obj.inven))
        .attr('data-val', obj.inven)
        .attr('class', 'text-amount text-green blink').css('background-color','#fff');
      }else{
        $(".text-amount").html(obj.message).attr('class', 'text-amount text-danger blink').css('background-color','yellow');
      }
      
      // Change Photo
      //$('.ew-itemset-pic').attr('src','//assets.ewinl.com/images/product/' +obj.ig +'/' + obj.Photo);
      $(".ew-itemset-pic").attr("src", obj.Photo);
    }
  });
}

$("body").on("click", "#selector ._radio", function() {
  $(this)
    .addClass("btn-info")
    .siblings()
    .removeClass("btn-info");
  itemno = $(this).attr("data");

  $("#price").val($(this).attr("price"));
  $("#ItemName").val($(this).attr("item_desc"));

  // TODO: insert whatever you want to do with $(this) here
});

$("body").on("click", "#PickToSaleLine", function() {
 
  //$('#wrapper').hide('fast');
  var that = this;
  $(that)
    .find("i")
    .attr("class", "fas fa-sync-alt fa-spin");
  $(that).attr("disabled", true);

  var data = {
    param: {
      itemid: $("#ew-render-itemno").attr("data-key"),
      itemno: $("#itemno").val(),
      itemset: $("#itemset").val(),
      soid: $("#Form-SaleReturnOrder").attr('data-key'),
      amount: $("#ew-amount").val(),
      price: $("#ew-price").val()
    }
  };
  NewSaleLine(data, that);
   
});

$("body").on("click", ".ew-action-my-item", function() {
  //ItemValidate
  var data = {
    param: {
      pid: $(this).attr("ew-radio-id"),
      pval: $(this).attr("ew-radio-val"),
      pset: $("#itemset").val()
    }
  };
  $.ajax({
    url:"index.php?r=SaleOrders/ajax/item-validate",
    type: "POST",
    data: data,
    async:false,
    success:function(response){
      $('body').find('.ew-getItem-Set').html(response);
    }
  });
  //ajaxRoute("index.php?r=SaleOrders/ajax/item-validate","POST",data,"ew-getItem-Set");

  $("#ew-price").val(0);
  $(".ew-render-itemno").html("");
  $(".ew-render-item").html("");
 

  $("#ew-price").prop("disabled", true);
  $("#ew-amount").prop("disabled", true);
  $("#PickToSaleLine").hide();

  $(".renders-box").slideUp();
});

$("body").on("click", ".ew-action-item", function() {
  var that = this;
  // Change Item No.
  $("#itemno").val($(this).attr("ew-radio-item"));

  $.ajax({
    url: "index.php?r=SaleOrders/ajax/item-getdata",
    type: "POST",
    data: { param: { item: $(this).attr("ew-radio-item") } },
    async: false,
    success: function(getData) {
      //console.log("click");
      var obj = jQuery.parseJSON(getData);

      $.each(
        $(that)
          .closest("div")
          .find("a"),
        function(index, el) {
          if (el == that) {
            $(el)
              .find("i")
              .attr("class", "far fa-check-square");
          } else {
            $(el)
              .find("i")
              .attr("class", "far far fa-square");
            //$(el).eq(index).find('i').attr('class','far fa-square');
          }
        }
      );

      $("#ew-render-itemno").attr("data-key", obj.id);
      $("#ew-price").val(obj.std);
      $(".ew-render-itemno").html(obj.code);
      $(".ew-render-item").html(obj.desc);

      if(parseInt(obj.inven) > 0){
        $(".text-amount").html(number_format(obj.inven))
        .attr('data-val', obj.inven)
        .attr('class', 'text-amount text-green blink').css('background-color','#fff');
      }else{
        $(".text-amount").html(obj.message).attr('class', 'text-amount text-danger blink').css('background-color','yellow');
      }
      

      $("#ew-price").prop("disabled", false);
      $("#ew-amount").prop("disabled", false);

      $("#PickToSaleLine").show();

      // Change Photo
      $(".ew-itemset-pic")
        .attr("src", obj.Photo)
        .show();

      //$('.ew-getItem-Set').html(obj.html);
      if (obj.status == 200) {
        $(".renders-box").show();
        $(".renders-box").find('input[type="number"]:first');
      }
    }
  });
});

$("body").on("click", ".ew-render-itemno", function() {
  SelectText("ew-render-itemno");
});

$("body").on("dblclick", ".ew-render-itemno", function() {
  var url = "index.php?r=items/items/view&id=" + $(this).attr("data-key");
  window.open(url, "_blank");
});

function SelectText(element) {
  var doc = document,
    text = doc.getElementById(element),
    range,
    selection;
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

function FindItemsJson($this) {
  $(".find-load").fadeIn("fast");
  $(".find-item-render").html("");
  var word = $.trim($this.val());
  $.ajax({
    url: "index.php?r=items/ajax/find-items-json-limit",
    type: "GET",
    data: { word:word , limit: 20 },
    async: true,
    success: function(getData) {
      var obj = $.parseJSON(getData);
      //console.log(obj);
      if (obj[0].count===1) {        

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
        
      } else {
        var html = "";
        $(".find-item").show("fast");
        $.each(obj, function(key, model) {
          if (model.count != 0) {
            html +=
              '<a href="#true" data-id="' +
              model.id +
              '" itemno="' +
              model.no +
              '" desc="' +
              model.desc_th +
              '" price="' +
              model.cost +
              '"  class="pick-item-to-createline" >' +
              '<div class="panel panel-info">' +
              '<div class="panel-body">' +
              '<div class="row">' +
              '<div class="col-md-1 col-sm-2"><img src="' +
              model.img +
              '" class="img-responsive" style="min-width:50px; margin-bottom:20px;"></div>' +
              '<div class="col-md-11 col-sm-10">' +
              '<div class="row">' +
              '<div class="col-md-10 col-xs-8">' +
              model.desc_th +
              "</div>" +
              '<div class="col-md-2 col-xs-4 text-right">' +
              '<span class="find-price"><p class="price">Price</p>' +
              model.cost +
              "</span>" +
              "</div>" +
              "</div>" +
              '<div class="row">' +
              '<div class="col-xs-12"><span class="text-sm text-gray">' +
              model.desc_en +
              "</span></div>" +
              '<div class="col-xs-12"><label class="text-black">Code : ' +
              model.item +
              "</label></div>" +
              "</div>" +
              '<div class="row">' +
              '<div class="col-xs-8"><label>Stock</label></div>' +
              '<div class="col-xs-4 text-right"><span class="text-gray">' +
              model.inven +
              "</span></div>" +
              "</div>" +
              "</div>" +
              "</div>" +
              "</div>" +
              "</div>" +
              "</a>\r\n";
          } else {
            html +=
              '<div class="col-md-3">' +
              '<div class="col-xs-2 text-center"><i class="fas fa-search fa-3x"></i></div>' +
              '<div class="col-xs-10 text-center">NO DATA FOUND<br/> ไม่พบข้อมูล</div>' +
              "</div>";
          }
        });

        $(".find-item-render").html(html);
        setTimeout(function(e) {
          $(".find-item").slideDown("slow");
          $(".find-load").fadeOut();
        }, 100);
      }
    }
  });
}

function createLine(itemno) {
  var data = {
    param: {
      itemid: itemno.id,
      itemno: itemno.no,
      itemset: 0,
      soid: $("#Form-SaleReturnOrder").attr('data-key'),
      amount: 1,
      price: itemno.cost,
      desc: itemno.desc_th
    }
  };

  NewSaleLine(data, $(this));


  getSumLine($("#ew-discount-amount").attr("data"), "discount");
}

// EDITABLE

$("body").on("click", "input[type='number']", function() {
  $(this).select();
});
$("body").on("keyup", "input.update-desc", function(event) {
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {
    var index = $(".text-line").index(this) + 1;
    $(".text-line")
      .eq(index)
      .focus()
      .select();
  }
});
$("body").on("change", "input.update-desc", function(event) {
  var $this = $(this);
  var tr = $this.closest("tr");
  var form = $("form#Form-SaleReturnOrder");
  var $data = {
    ajax: true,
    key: tr.data("key"),
    name: tr.find($this).attr("name"),
    data: tr.find($this).val()
  };
  var action = form.attr("action");
  $.ajax({
    url: action + "&_pjax=%23p0",
    type: form.attr("method"),
    data: $data,
    dataType: "JSON",
    success: function(response) {
      if (response.status == 200) {
        getSumLine($("#ew-discount-amount").attr("data"), "discount");
      }
    }
  });
});

$("body").on("keyup", "input.update-quantity", function(event) {
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {
    var index = $(".text-line").index(this) + 1;
    $(".text-line")
      .eq(index)
      .focus()
      .select();
  }
});

$("body").on("change", "input.update-quantity", function(event) {
  var $this = $(this);
  var tr    = $this.closest("tr");
  var form  = $("form#Form-SaleReturnOrder");
  var $data = {
              ajax: true,
              key: tr.data("key"),
              name: tr.find($this).attr("name"),
              data: tr.find($this).val()
            };
  var action = form.attr("action");
  $.ajax({
    url: action + "&_pjax=%23p0",
    type: form.attr("method"),
    data: $data,
    dataType: "JSON",
    success: function(response) {
       
      if(response.data.status===200){


          // $.notify({
          //     // options
          //     icon: "fas fa-shopping-basket",
          //     message: response.data.message
          //   },{
          //     // settings
          //     placement: {
          //       from: "right",
          //       align: "center"
          //     },
          //     type: "success",
          //     delay: 3000,
          //     z_index: 3000
          // });  
          
           
          
        
        }else if(response.data.status===403){
          $.notify({
            // options
            icon: "fas fa-box-open",
            message: response.data.message
          },{
              // settings
              placement: {
                from: "top",
                align: "center"
              },
              type: "warning",
              delay: 3000,
              z_index: 3000
          });    
          $this.focus().select();
        }else{

          $.notify({
            // options
            icon: "fas fa-box-open",
            message: response.data.message
          },{
              // settings
              placement: {
                from: "top",
                align: "center"
              },
              type: "error",
              delay: 4000,
              z_index: 3000
          }); 
  
          if(response.data.status===404){

            $.notify({
              // options
              icon: "fas fa-box-open",
              message: response.data.text + ' : ' + number_format(response.data.inven)
            },{
                // settings
                placement: {
                  from: "top",
                  align: "center"
                },
                type: "info",
                delay: 8000,
                z_index: 3000
            }); 
            
            $.notify({
              // options
              icon: "fas fa-luggage-cart",
              message: response.data.suggestion + ' : ' + number_format(response.data.reserve)
            },{
                // settings
                placement: {
                  from: "top",
                  align: "center"
                },
                type: "warning",
                delay: 8000,
                z_index: 3000
            });

          }

          $this.focus().select();
        }

        $this.val(response.value.val);

        getSumLine($("#ew-discount-amount").attr("data"), "discount");
        tr.find(".line-amount").text(number_format(response.value.total.toFixed(2)));
        tr.find(".line-stock").text(number_format(response.value.remain));      
    }
  });
});

$("body").on("keyup", "input.update-unit_price", function(event) {
  var keyCode = event.keyCode || event.which;
  if (keyCode === 13) {
    // ถ้าเป็นบรรทัดสุดท้ายเมื่อกด ENTER ให้ไป text search
    var index = $(".text-line").index(this) + 1;
    if (index == $(".text-line").length) {
      $(".InsertItem")
        .focus()
        .select();
    } else {
      $(".text-line")
        .eq(index)
        .focus()
        .select();
    }
  }
});

$("body").on("change", "input.update-unit_price", function(event) {
  var $this = $(this);
  var tr = $this.closest("tr");
  var form = $("form#Form-SaleReturnOrder");
  var $data = {
    ajax: true,
    key: tr.data("key"),
    name: tr.find($this).attr("name"),
    data: tr.find($this).val()
  };
  var action = form.attr("action");
  $.ajax({
    url: action + "&_pjax=%23p0",
    type: form.attr("method"),
    data: $data,
    dataType: "JSON",
    success: function(response) {
      if (response.status == 200) {
        getSumLine($("#ew-discount-amount").attr("data"), "discount");
        tr.find(".line-amount").text(
          number_format(response.value.total.toFixed(2))
        );
        tr.find(".line-stock").text(number_format(response.value.remain));
      }
    }
  });
});
