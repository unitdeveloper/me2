/*!
 * EWIN
 * v3.12.19 - 2018
 * (c) Assawin.Thkch; MIT License
 */

$("body").on("click", ".ew-address-click", function() {
  pickAddress($(this));
});

$("body").on("click", "#ADD-ADDRESS", function() {
  $(".address-form").slideToggle("slow");
  addNewAddress();
  //$(this).hide('fast');
});
function pickAddress(data) {
  $('ew[id="commonAddress"]').attr("data", data.attr("ew-id-click"));

  setTimeout(() => {
    $("a.ew-form-address").html(data.attr("ew-text"));
    $("a.ew-transport-show").html(data.attr("ew-transport"));
  }, 500);
  renderShipment();

  // Create Ship button.
  var icon_refresh = '<i class="fa fa-cubes"></i>';
  $(".ew-refresh-ship").html(icon_refresh + langText.ship);
  $(".ew-refresh-ship").attr(
    "class",
    "btn btn-app btn-danger pull-right ew-gen-ship"
  );

  if (data.attr("source") == "shipped-tab") {
    activaTab("shippedList");
  }
}

function addNewAddress() {
  var customer = $('ew[id="customerid"]').attr("data");
  $("#address-source_id").val(customer);

  $.ajax({
    url: "index.php?r=customers/ajax/json-get-customer&id=" + customer,
    type: "POST",
    data: { id: customer },
    async: true,
    success: function(getData) {
      var obj = jQuery.parseJSON(getData);

      $("#address-source_name").val(obj.name);
      $("#address-address").val(obj.address);
      $("#address-address2").val(obj.address2);

      //$('#address-district').val(obj.district);

      //$('#address-city').val(obj.city);
      $("#address-transport").val(obj.transport);

      $("#address-postcode").val(obj.postcode);

      getProvince(obj.postcode);

      getCityDefault(obj.city, obj.province);
      getDistrictFromCity(obj.city, obj.district);
    }
  });

  var icon = '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
  $("a.ew-submit-edit").html(icon + langText.add);
  $("a.ew-submit-edit").attr("class", "btn btn-success ew-submit");
}
function getValueIntext(id) {
  var qtyToShip = Number(0);

  $("input[id='" + id + "']").each(function() {
    qtyToShip = Number($(this).val()) + qtyToShip;
  });
  return qtyToShip;
}
$("body").on("click", ".ew-gen-ship", function() {
  let el = $(this);
  //loading
  let loading =
    '<div class="loading" style="position: absolute;z-index: 3330;left: 40%; top: 20%;"><img src="images/loader-128x/Preloader_1.gif" class="img-fluid" style="max-width: 350px;"></div>';
  $(".SaleLine").append(loading);

  el.attr("disabled", true);
  el.removeClass("ew-gen-ship");
   

  if (getValueIntext("qtyToShip") === 0) {
    alert(langText.error_zero);
    setTimeout(() => {
      el.attr("disabled", false);
      $(".loading").remove();
      el.addClass("ew-gen-ship");
    }, 1000);
  } else {
    if (confirm(langText.confirmship)) {
      //var saleheader  = $('form.SaleHeader').serialize();
      var qtytoship = $("form.Shipment input[type=number]").serializeArray();
      var appdata = {
        param: {
          //apk:'Shiped',
          apk: "ShipNow",
          id: $(".ew-ship-renders").data("key"),
          no: $(this).attr("ew-order-no"),
          cur: $(".ew-ship-renders").data("status"),
          reson: "",
          qtytoship: qtytoship,
          //saleheader:saleheader,
          addrid: $('ew[id="commonAddress"]').attr("data"),
          custid: $('ew[id="customerid"]').attr("data"),
          shipdate: $('form.Shipment input[id="saleheader-ship_date"]').val(),
          transport: $('form.Shipment select[id="saleheader-transport"]').val()
        }
      };

      if ($(this).attr("ew-order-no") == "") {
        alert('Error! "Order Number"');
        $(".loading").remove();
        return false;
      } else if (qtytoship == "") {
        alert('Error! "Quantity"');
        console.log(appdata);
        $(".loading").remove();
        return false;
      } else if ($('ew[id="commonAddress"]').attr("data") == "") {
        alert('Error! "Address"');
        $(".loading").remove();
        return false;
      } else if ($('ew[id="customerid"]').attr("data") == "") {
        alert('Error! "Customer"');
        $(".loading").remove();
        return false;
      } else if ($('form.Shipment input[id="saleheader-ship_date"]').val() == "") {
        alert('Error! "Ship Date"');
        $(".loading").remove();
        return false;
      }

      //route('index.php?r=approval/approve/sale-order','POST',appdata,'ew-ship-renders');
      $.ajax({
        url: "index.php?r=approval/approve/sale-order",
        type: "POST",
        data: appdata,
        dataType: "JSON",
        success: function(response) {

          //console.log(response);

          if(response.status===200){
            //renderShipment();
            let data = {param: {
                          no: $('ew[id="orderid"]').attr("data"),
                          id: $(".ew-ship-renders").data("key")
                        }
                      };
            $.ajax({
              url: "index.php?r=warehousemoving/shipment/shipline",
              type: "POST",
              data: data,
              success: function(response) {  
                $(".ew-ship-pagerender").html(response);
                                        
                setTimeout(() => {
                  $(".loading").remove();
                  el.attr("disabled", false);
                  el.addClass("ew-gen-ship"); 
                  activaTab("shippedList"); 
                }, 1500);                  
              }
            });
          }else{
            $(".loading").remove();
            el.attr("disabled", false);
            el.addClass("ew-gen-ship");
            swal(response.message, response.suggestion, "warning");
             
          }

        }
      });

      //$('form[id="Form-SaleOrder"]').submit();
    }else{
      setTimeout(() => {
        el.attr("disabled", false);
        $(".loading").remove();
        el.addClass("ew-gen-ship");
      }, 1000);
    }
    return false;
  }
});

function activaTab(tab) {
  $('.nav-tabs a[href="#' + tab + '"]').tab("show");
}

$(".ewSaleShipModal").click(function() {
  $("body").attr("style", "overflow:hidden; margin-right:1px;");
  $(".ew-ship-renders").html("");
  renderShipment();
});

$("body").on("change", "#qtyToShip", function() {
  var line = $(this).attr("line");
  var oldval = Number($(this).attr("ew-old-data"));
  var newval = Number($(this).val());
  //alert($(this).attr('line'));

  if (newval > oldval) {
    alert(langText.overship);
    $(this).val($(this).attr("ew-old-data"));
  } else {
    return true;
  }
});

$("body").on("change", "#qtyShipped", function() {
  var line = $(this).attr("line");
  var oldval = Number($(this).attr("ew-old-data"));
  var newval = Number($(this).val());
  //alert($(this).attr('line'));

  if (newval > oldval) {
    alert(langText.overship);
    $(this).val($(this).attr("ew-old-data"));
  } else if (newval < 0) {
    alert(langText.lessthan);
    $(this).val($(this).attr("ew-old-data"));
  } else {
    return true;
  }
});

function renderShipment() {
  var data = {
    param: {
      no: $('ew[id="orderid"]').attr("data"),
      id: $(".ew-ship-renders").data("key")
    }
  };
  //route('index.php?r=warehousemoving/shipment/shipline','POST',data,'ew-ship-pagerender');
  $.ajax({
    url: "index.php?r=warehousemoving/shipment/shipline",
    type: "POST",
    data: data,
    success: function(response) {
      $(".ew-ship-pagerender").html(response);

    }
  });
}

$("body").on("click", ".ew-shipment", function() {
  //alert($(this).val());
  renderShippedList($(this).attr("ew-shipped-id"));

  // Create Refresh Button
  CreateBackButton();
  // var icon_refresh = '<i class="fa fa-refresh"></i>';
  // $('.ew-gen-ship').html(icon_refresh +'<?=Yii::t('common','Refresh')?>');
  // $('.ew-gen-ship').attr('class','btn btn-app btn-danger pull-right ew-refresh-ship');

  // Create Undu Button
  var icon = '<i class="fa fa-undo"></i>';
  var alink =
    '<a href="#" class="btn btn-app btn-danger  ew-undo-ship" ew-shipped-id="' +
    $(this).attr("ew-shipped-id") +
    '"">' +
    icon +
    langText.undo +
    "</a>";

  //$('.ew-actions').html('');
  $(".undo-btn").html(alink);
  createPrintButton($(this).attr("ew-shipped-id"));
});

function createPrintButton(id) {
  //var icon_refresh = '<i class="fa fa-print"></i>';
  //var div = icon_refresh +'<?=Yii::t('common','Print')?>';

  var alink =
    '<div class="pull-right"><a href="index.php?r=warehousemoving/shipment/print-ship&id=' +
    id +
    '" target="_blank" class="btn btn-app btn-info text-aqua">' +
    '<i class="fa fa-print"></i>' +
    langText.print +
    " " +
    langText.deliticket +
    "</a> ";
  alink +=
    '<a href="index.php?r=warehousemoving/shipment/print-transport&id=' +
    id +
    '" target="_blank" class="btn btn-app btn-info text-blue" >' +
    '<i class="fa fa-print"></i>' +
    langText.print +
    " " +
    langText.shipment +
    "</a></div>";

  //$('.ew-actions').append(alink);
  $(".ew-actions").html(alink);
}

function renderShippedList(id) {
  var data = {
    param: {
      no: $('ew[id="orderid"]').attr("data"),
      id: id
    }
  };
  route(
    "index.php?r=warehousemoving/shipment/shipped-line",
    "POST",
    data,
    "Shipped"
  );
}

undoShip = (id, callback) => {
  var qtyshipped = $("form.ShippedLine").serializeArray();
  var data = {
    param: {
      id: id,
      qtyshipped: qtyshipped
    }
  };
  $.ajax({
    url: "index.php?r=warehousemoving/shipment/undo-ship",
    type: "POST",
    data: data,
    dataType: "JSON",
    success: function(response) {
      if (response.status == 200) {
        renderShipment();
        CreateShipButton();
      } else {
        console.log(response);
        swal(response.message, response.suggestion, "warning");
      }

      callback({
        status: true
      })
    }
  });
  //route('index.php?r=warehousemoving/shipment/undo-ship','POST',data,'Shipped');
}
function CreateShipButton() {
  // Create Ship button.
  var icon_refresh = '<i class="fa fa-cubes"></i>';
  $(".ew-refresh-ship").html(icon_refresh + langText.ship);
  $(".ew-refresh-ship").attr(
    "class",
    "btn btn-app btn-danger pull-right ew-gen-ship"
  );
}

function CreateBackButton() {
  // Create Refresh Button
  var icon_refresh = '<i class="fa fa-caret-square-o-left"></i>';
  $(".ew-gen-ship").html(icon_refresh + langText.back);
  $(".ew-gen-ship").attr(
    "class",
    "btn btn-app btn-danger pull-right ew-refresh-ship"
  );
}
// When Refersh button click.
// Return all.
$("body").on("click", ".ew-refresh-ship", function() {
  // Clear content.
  $(".ew-ship-renders").html("");

  // Create Ship button.
  CreateShipButton();
  // var icon_refresh = '<i class="fa fa-truck"></i>';
  // $('.ew-refresh-ship').html(icon_refresh +'<?=Yii::t('common','แจ้งส่ง')?>');
  // $('.ew-refresh-ship').attr('class','btn btn-app btn-danger pull-right ew-gen-ship');

  $(".ew-actions").html("");
  renderShipment();
});

$("body").on("click", "#CANCEL-ADDRESS", function() {
  renderShipment();
  CreateShipButton();
});

$("body").on("click", ".ew-undo-ship", function() {
  // Undo Ship
  let el = $(this);
  //loading
  let loading =
    '<div class="loading" style="position: absolute;z-index: 3330;left: 40%; top: 20%;"><img src="images/loader-128x/Preloader_1.gif" class="img-fluid" style="max-width: 350px;"></div>';
  $(".SaleLine").append(loading);

  el.removeClass("ew-undo-ship");

  el.attr("disabled", true);

  if (getValueIntext("qtyShipped") === 0) {
    alert(langText.undonot_zero);
    setTimeout(() => {
      el.attr("disabled", false);
      $(".loading").remove();
      el.addClass("ew-undo-ship");
    }, 3000);
  } else {
    if (confirm(langText.confirmundo)) {
      undoShip($(this).attr("ew-shipped-id"),res => {
        if(res.status){
          setTimeout(() => {
            el.attr("disabled", false);
            $(".loading").remove();
            el.addClass("ew-undo-ship");
          }, 3000);
        }        
      });
      
      //renderShippedList($(this).attr('ew-shipped-id'));
    }else{
      setTimeout(() => {
        el.attr("disabled", false);
        $(".loading").remove();
        el.addClass("ew-undo-ship");
      }, 1000);
    }

    
    return false;
  }
});

$("body").on("click", "a[href=#JSON]", function() {
  $(".Shipment").text("");
  route(
    "index.php?r=warehousemoving/address/create",
    "POST",
    {
      id: $('ew[id="customerid"]').attr("data"),
      source: $(this).attr("source")
    },
    "Shipment"
  );

  // Create Refresh Button
  CreateBackButton();
  activaTab("notshipment");
});

$("body").on("click", ".addr-edit", function() {
  $(".ew-show-address").hide("nomal");

  $(".address-form").show("slow");

  var customer = $('ew[id="customerid"]').attr("data");
  $("#address-source_id").val(customer);

  $.ajax({
    url:
      "index.php?r=customers/ajax/json-get-address&id=" +
      $(this).attr("addr-id"),
    type: "POST",
    data: { id: customer },
    async: true,
    success: function(getData) {
      var obj = jQuery.parseJSON(getData);

      //alert( obj.name === "John" );
      $("#address-source_name").val(obj.name);
      $("#address-address").val(obj.address);
      $("#address-address2").val(obj.address2);

      //$('#address-district').val(obj.district);

      //$('#address-city').val(obj.city);
      $("#address-transport").val(obj.transport);

      $("#address-postcode").val(obj.postcode);

      $("#address-remark").val(obj.remark);
      $("#address-comment").val(obj.comment);

      getProvince(obj.postcode);
      getCityDefault(obj.city, obj.province);
      getDistrictFromCity(obj.city, obj.district);
    }
  });
  var icon = '<i class="fa fa-floppy-o" aria-hidden="true"></i>';
  $("a.ew-submit").html(icon + langText.save);
  $("a.ew-submit").attr("class", "btn btn-warning ew-submit-edit");

  $('ew[id="commonAddress"]').attr("data", $(this).attr("addr-id"));
});

$("body").on("click", ".addr-delete", function() {
  if (confirm(langText.confirmdel)) {
    route(
      "index.php?r=warehousemoving/address/ajax-delete",
      "POST",
      {
        id: $(this).attr("addr-id"),
        source_id: $(this).attr("addr-source")
      },
      "Shipment"
    );
  }

  route(
    "index.php?r=warehousemoving/address/create",
    "POST",
    { id: $('ew[id="customerid"]').attr("data") },
    "Shipment"
  );
});

$("body").on("click", ".ew-submit", function() {
  //alert('test');
  var data = {
    Address: {
      source_id: $("#address-source_id").val(),
      source_name: $("#address-source_name").val(),
      transport: $("#address-transport").val(),
      address: $("#address-address").val(),
      address2: $("#address-address2").val(),
      district: $("#address-district").val(),
      city: $("#address-city").val(),
      province: $("#address-province").val(),
      postcode: $("#address-postcode").val()
    }
  };

  route(
    "index.php?r=warehousemoving/address/ajax-create",
    "POST",
    data,
    "Shipment"
  );
});

$("body").on("click", "a.ew-submit-edit", function() {
  var commonAddress = $('ew[id="commonAddress"]').attr("data");
  var data = {
    Address: {
      source_id: $("#address-source_id").val(),
      source_name: $("#address-source_name").val(),
      transport: $("#address-transport").val(),
      address: $("#address-address").val(),
      address2: $("#address-address2").val(),
      district: $("#address-district").val(),
      city: $("#address-city").val(),
      province: $("#address-province").val(),
      postcode: $("#address-postcode").val(),
      remark: $("#address-remark").val(),
      comment: $("#address-comment").val()
    }
  };

  route(
    "index.php?r=warehousemoving/address/ajax-update&id=" + commonAddress,
    "POST",
    data,
    "Shipment"
  );
});

$("body").on("click", 'a[href="#notshipment"]', function() {
  CreateShipButton();
});

$("body").on("click", 'a[href="#shippedList"]', function() {
  CreateBackButton();
});

$("body").on("click", ".open-modal-editheader", function() {
  //alert('test');
  var id = $(this).attr("data");
  $("#ew-modal-WarehouseHeader").modal("show");
  $("#ew-modal-WarehouseHeader .modal-body").hide();

  setTimeout(function(e) {
    $.ajax({
      url: "index.php?r=warehousemoving/header/update&id=" + id,
      type: "GET",
      success: function(getData) {
        $(".ew-body-wh").html(getData);

        $("#ew-modal-WarehouseHeader .modal-body").slideDown("slow");
      }
    });
  }, 300);
});

// /.Right Click

$(function() {
  var $contextMenu = $("#contextMenu");

  $("body").on("contextmenu", ".ew-gen-ship", function(e) {
    $contextMenu.css({
      display: "block",
      left: e.pageX - 350,
      top: e.pageY - 50
    });
    return false;
  });

  $contextMenu.on("click", "a", function() {
    $contextMenu.hide();
  });
});

$(document).click(function(e) {
  // check that your clicked
  // element has no id=info

  if (e.target.id != "contextMenu") {
    $("#contextMenu").hide();
  }
});

$("body").on("click", "button.EditTransport", function() {
  if (confirm(langText.confirm + " ?")) {
    $(".close-load").show();
    $('form[id="form-warehouse-shipment-info"]').submit();
  }
});


$('body').on('click', '.add-transport', function(){
  $('#modal-transport').modal('show');
})

$('body').on('click', '.close-transport', function(){
  $('#modal-transport').modal('hide');
})


$('body').on('click', '.transport-save', function(){
  let data = {
    name:$('#transport-name').val(),
    addr:$('#transport-address').val(),
    contact: $('#transport-contact').val(),
    phone: $('#transport-phone').val()
  };
  if(data.name==''){
    $('#transport-name').focus();
  }else if(data.addr==''){
    $('#transport-address').focus();
  }else{
  
    fetch("?r=transport/create-ajax", {
      method: "POST",
      body: JSON.stringify(data),
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
      }
    })
    .then(res => res.json())
    .then(response => {

      if(response.status===200){  
        
        renderShipment();
        $('#modal-transport').modal('hide');
        
        $("#ew-modal-WarehouseHeader .modal-body").slideUp();
        setTimeout(function(e) {
          $.ajax({
            url: "index.php?r=warehousemoving/header/update&id=" + $('.open-modal-editheader').attr("data"),
            type: "GET",
            success: function(getData) {
              $(".ew-body-wh").html(getData);
              $("#ew-modal-WarehouseHeader .modal-body").slideDown("slow");
            }
          });
        }, 400);

        $('#modal-transport').modal('hide');
        
      } 

    })
    .catch(error => {
      console.log(error);
    });

      
  }

})


const updateBox = (obj, callback) => {
  fetch("?r=warehousemoving/header/update-box", {
      method: "POST",
      body: JSON.stringify({id:obj.id, boxs:obj.boxs}),
      headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token": $('meta[name="csrf-token"]').attr('content'),
      },
  })
  .then(res => res.json())
  .then(response => {        
      callback(response);
  })
  .catch(error => {
      console.log(error);
  });
}
 
$('body').on('change', 'input#box-change', function(){  
  let obj = {
    id: $(this).attr('data-key'),
    boxs: $(this).val()
  };
  updateBox(obj, res => {
    console.log(res);
  })
})