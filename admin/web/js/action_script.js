$(document).ready(function() {
  // Create Submit button link
  //var formID = $('button[type=submit]').closest("form").attr('id');
  var formID = $("form")
    .find('input[name="eWin-backend"]')
    .closest("form")
    .attr("id");
  $(".ew-save-common")
    .attr("onclick", "$('#" + formID + "').submit()")
    .attr("href", "javascript:void(0)");
  //alert(formID);

  $("div.date").hover(function() {
    $(this)
      .children("span")
      .attr("title", " ");
  });
  $("body").on("mouseover", 'button[data-toggle="dropdown"]', function() {
    $(this)
      .removeAttr("title", " ")
      .removeAttr("aria-describedby", " ");
  });
  // Set mini-bar when window size = tablet
  /*
    var windowWidth = $(window).width();

    $( window ).resize(function() {

        
        
        

        if ($(window).width() != windowWidth) {

            // Update the window width for next time
            //alert(window.innerWidth);
            if(window.innerWidth < 768 ){

                $('body').attr('class','skin-green sidebar-mini');
                $('.user-panel').show();
                
            }else if(window.innerWidth <= 1024 )
            {        
                $('body').addClass('sidebar-collapse');
                $('.user-panel').hide();
            }else {
                // $('body').removeClass('sidebar-collapse');
                // $('.user-panel').show();
            }
            

            
            windowWidth = $(window).width();

            // Do stuff here

        }
        
        
        
         

    });*/

  if ($("body").hasClass("sidebar-collapse")) {
    //console.log('Open');
    $(".user-panel").hide();
  } else {
    //console.log('Close');
    setTimeout(() => {
      $(".user-panel").slideDown('slow');
    },1500)
    
  }

  $("body").on("click", "#offcanvas", function() {
    //console.log(window.innerWidth);

    // Mini
    if (window.innerWidth < 768) {
      if ($("body").hasClass("sidebar-open")) {
        //console.log('Close');
        //$('.user-panel').hide();
      } else {
        //console.log('Open');
        $(".user-panel").show();
      }
    } else {
      route("index.php?r=ajax/switch_menu", "GET", "", "Navi-Title");
      if ($("body").hasClass("sidebar-collapse")) {
        //console.log('Open');
        $(".user-panel").show();
      } else {
        //console.log('Close');
        $(".user-panel").hide();
      }
    }
  });

  $("body").on("change", ".language-picker", function() {
    //$('.language-picker').on('change', function(){
    var selected = $(this)
      .find("option:selected")
      .val();
    window.location.href = addParameterToURL("language=" + selected);
  });
});


let tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
      , template = `<html xmlns:o="urn:schemas-microsoft-com:office:office" 
                          xmlns:x="urn:schemas-microsoft-com:office:excel" 
                          xmlns="http://www.w3.org/TR/REC-html40">
                          <meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">
                          <head>
                              <!--[if gte mso 9]>
                                  <xml>
                                      <x:ExcelWorkbook><x:ExcelWorksheets>
                                      <x:ExcelWorksheet><x:Name>
                                      {worksheet}</x:Name><x:WorksheetOptions>
                                      <x:DisplayGridlines/>
                                      </x:WorksheetOptions>
                                      </x:ExcelWorksheet>
                                      </x:ExcelWorksheets>
                                      </x:ExcelWorkbook>
                                  </xml>
                              <![endif]-->                                
                          </head>
                          <body style="width:216mm; font-family: 'TH SarabunPSK'; font-size:16px;">
                              <table style="color:#000;">{table}</table>
                          </body>
                      </html>`
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
      return function(table, name, fileName) {
        if (!table.nodeType) table = document.getElementById(table)
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
        
        var link = document.createElement("A");
        link.href = uri + base64(format(template, ctx));
        link.download = fileName || 'Workbook.xls';
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
  })()

  const downloadWord = (name) => {

    if (!window.Blob) {
        alert('Your legacy browser does not support this action.');
        return;
    }

    var html, link, blob, url, css;
    
    // EU A4 use: size: 841.95pt 595.35pt;
    // US Letter use: size:11.0in 8.5in;
    
    css = (
        `<style>
            @page table-responsive{
                size:11.0in 8.5in;
                mso-page-orientation: portrait;
            }
            table#export_table {
                page: table-responsive;
            } 
        </style>`
    );
    
    html = window.docx.innerHTML;
    blob = new Blob(['\ufeff', css + html], {
    type: 'application/msword'
    });
    url = URL.createObjectURL(blob);
    link = document.createElement('A');
    link.href = url;
    // Set default file name. 
    // Word will append file extension - do not add an extension here.
    link.download = name;   
    document.body.appendChild(link);
    if (navigator.msSaveOrOpenBlob ) navigator.msSaveOrOpenBlob( blob, name+'.doc'); // IE10-11
            else link.click();  // other browsers
    document.body.removeChild(link);
  }


  function addParameterToURL(param) {
    _url = location.href;
    _url += (_url.split("?")[1] ? "&" : "?") + param;
    return _url;
  }

//---ซ่อน Javascript---
//$(document).ready(function () {$('script').remove();});
// $(document).ready(function () {$('script[type="text/javascript"]').remove();});
// $(document).ready(function () {$('script[type="text/jsx"]').remove();});
// $(document).ready(function () {$('script[type="text/babel"]').remove();});
// $(document).ready(function () {$('script[src="/\.(yii).*$/"]').remove();});
//--- /.ซ่อน Javascript---

function route(url, type, data, div) {
  var newUrl = "index.php?r=" + url;

  if (~url.indexOf("index.php?r=")) {
    newUrl = url;
  }

  $.ajax({
    url: newUrl,
    type: type,
    data: data,
    async: false,
    success: function(getData) {
      $("." + div)
        .hide()
        .html(getData)
        .fadeIn("slow");
    },
    error: function(xhr, resp, text) {
      console.log(xhr, resp, text);
    }
  });
}

function routes(url, type, data, div) {
  var newUrl = "index.php?r=" + url;

  if (~url.indexOf("index.php?r=")) {
    newUrl = url;
  }

  $(div).fadeOut();

  $.ajax({
    url: newUrl,
    type: type,
    data: data,
    async: true,
    success: function(getData) {
      $(div).html(getData);
      setTimeout(function() {
        $(div).fadeIn("slow");
      }, 200);
    },
    error: function(xhr, resp, text) {
      console.log(xhr, resp, text);
    }
  });
}

function number_format(nStr) {
  nStr += "";
  x = nStr.split(".");
  x1 = x[0];
  x2 = x.length > 1 ? "." + x[1] : "";
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, "$1" + "," + "$2");
  }
  return x1 + x2;
}

$("body").on("click", 'button[data-dismiss="modal"]', function() {
  $("body").attr("style", "overflow:auto; margin-right:0px;");
});

function getUrlVars($param) {
  var vars = [],
    hash;
  var hashes = window.location.href
    .slice(window.location.href.indexOf("?") + 1)
    .split("&");
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split("=");
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars[$param];
}

function disableSubmit($form) {
  $($form)
    .on("beforeSubmit", function(e) {})
    .on("submit", function(e) {
      e.preventDefault();
    });
}

function toolTip(e) {
  $(e).hover(
    function() {
      if ($(this).attr("data-tooltip") != "") {
        $(this).css("cursor", "pointer");

        $(this).append('<div class="ew-tooltip"></div>');

        $(this)
          .closest("tr")
          .find(".ew-tooltip")
          .html($(this).attr("data-tooltip"));

        $(this)
          .closest("tr")
          .find(".ew-tooltip")
          .fadeIn("slow");
      }
    },
    function() {
      $(this)
        .closest("tr")
        .find(".ew-tooltip")
        .fadeOut("fast", function() {
          $(this)
            .closest("tr")
            .find(".ew-tooltip")
            .remove();
        });
    }
  );
}

$("body").on("click", "div.ew-data-saleorder", function() {
  var $url = $(this)
    .closest("li")
    .find("a")
    .attr("href");
  if ($url != null) {
    window.location.href = $url;
  }
});

function autoRefresh(setTimmer) {
  var timmer = setTimmer; //second
  var timer;
  $(document).on("mousemove", function(e) {
    clearInterval(timer);

    timer = setInterval(function() {
      location.reload();
    }, timmer * 1000);
  });

  $(document).ready(function() {
    timer = setInterval(function() {
      location.reload();
    }, timmer * 1000);
  });
}

function ActiveUpdateField($Object) {
  var tr = $Object.data.closest("tr");
  var form = $($Object.form);
  var $data = {
    ajax: true,
    key: tr.attr("data-key"),
    name: tr.find($Object.data).attr("name"),
    data: tr.find($Object.data).val()
  };
  var action = form.attr("action");
  var res = [];
  $.ajax({
    url: action + "&_pjax=%23p0",
    type: form.attr("method"),
    data: $data,
    dataType: "JSON",
    success: function(response) {
      //$('div.throw-status').html(getData);
      res.push(response);
    },
    error: function() {
      alert("Something went wrong");
    }
  });
  return res;
}

$("body").on("keydown", "input.money", function(e) {
  // Allow: backspace, delete, tab, escape, enter and .
  if (
    $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
    // Allow: Ctrl+A, Command+A
    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: home, end, left, right, down, up
    (e.keyCode >= 35 && e.keyCode <= 40)
  ) {
    var valid = /^\d{0,4}(\.\d{0,2})?$/.test(this.value),
      val = this.value;

    if (!valid) {
      this.value = val.substring(0, val.length - 1);
    }
    // let it happen, don't do anything
    return;
  }
  // Ensure that it is a number and stop the keypress
  if (
    (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
    (e.keyCode < 96 || e.keyCode > 105)
  ) {
    e.preventDefault();
  }
});

//----CHANGE THEMES SALEORDER----
$("body").on("click", "a#change-theme", function() {
  var theme = $(this).data("id");
  $.ajax({
    url: "index.php?r=ajax/change-theme",
    type: "POST",
    data: { theme: theme },
    dataType: "JSON",
    success: function(response) {
      if (response.status == 200) {
        location.reload();
      }
    }
  });
});
$("body").on("click", ".demis-theme-change", function() {
  $.ajax({
    url: "index.php?r=ajax/change-theme-demis",
    type: "GET",
    data: { demis: "true" },
    dataType: "JSON",
    success: function(response) {
      if (response.status == 200) {
        location.reload();
      }
    }
  });
});
//---- /.CHANGE THEMES SALEORDER----

// Date Picker
if ($("body").find('input[type="date"]').length > 0) {
  //console.log('Yes');
  // ถ้ามี input type='date'
  // chrome(มี Datepicker อยู่แล้ว) ไม่แสดง date picker
  // safari(ไม่มี Datepicker) แสดง datepicker
  if ($('input[type="date"]')[0].type != "date")
    $('input[type="date"]').datepicker({ dateFormat: "yy-mm-dd" });
}

// Page full screen
$("body").on("click", ".nav-link-expand", function(e) {
  // if (typeof screenfull != "undefined") {
  //   if (screenfull.enabled) {
  //     screenfull.toggle();
  //   }
  // }
});
// if (typeof screenfull != 'undefined'){
//     if (screenfull.enabled) {
//         $(document).on(screenfull.raw.fullscreenchange, function(){
//             if(screenfull.isFullscreen){
//                 $('.nav-link-expand').find('i').toggleClass('icon-contract icon-expand2');
//             }
//             else{
//                 $('.nav-link-expand').find('i').toggleClass('icon-expand2 icon-contract');
//             }
//         });
//     }
// }

$('body').on('click','a.set-favorite-menu',function(){
  let el      = $(this);
  let url     = el.attr('data-url');
  let status  = el.attr('data-status'); 
  let name    = $('title').html();
  fetch("?r=ajax/set-favorite-menu", {
    method: "POST",
    body: JSON.stringify({ url:url, status:status, name:name }),
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content")
    }
  })
  .then(res => res.json())
  .then(res => {
     if(res.status === 200){
       if(res.message === 'on'){
          el.find('i').attr('class','fas fa-star text-yellow');
          el.attr('data-status','on');
       }else{
          el.find('i').attr('class','far fa-star');
          el.attr('data-status','off');
       }
       
     }
  })
  .catch(error => {
    console.log(error);
  });
})