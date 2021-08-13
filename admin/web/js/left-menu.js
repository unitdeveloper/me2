// ##### Left-Menu #####
$(document).ready(function() {
  // Create Div and set data

  var divSo =
    '<div class="ew-data-saleorder pointer"><i class="far fa-circle fa-spin" aria-hidden="true"></i></div>';
  var divApp =
    '<div class="ew-data-approve pointer"><i class="far fa-circle fa-spin " aria-hidden="true"></i></div>';

  // Set CSS relative
  $("#ew-alert-saleorder").css("position", "relative");
  $("#ew-alert-management-approve").css("position", "relative");

  // Create Div
  $("#ew-alert-salemain").append('<div class="ew-main-menu hidden-xs"></div>');
  $("#ew-alert-saleorder").append('<div class="ew-sub-menu"></div>');

  $("#ew-alert-management").append(
    '<div class="ew-main-menu-app hidden-xs"></div>'
  );
  $("#ew-alert-management-approve").append(
    '<div class="ew-sub-menu-app"></div>'
  );

  // append div
  $(".ew-main-menu").html(divSo);
  $(".ew-sub-menu").html(divSo);

  $(".ew-main-menu-app").html(divApp);
  $(".ew-sub-menu-app").html(divApp);

  if ($("#ew-alert-salemain").attr("class") === "active") {
    $(".ew-main-menu").fadeOut("fast");
  }

  if ($("#ew-alert-management").attr("class") === "active") {
    $(".ew-main-menu-app").fadeOut("fast");
  }

  // Generate Data to div
  //GenerateData("count-menu");
  //GenerateDataApp('count-menu-approve');
});

// On Click Zone
$("#ew-alert-salemain").click(function() {
  divActions();
});

$("#ew-alert-management").click(function() {
  divActionsApp();
});

//  On Click another menu
$(".sidebar-menu li a").click(function() {

  $(".ew-main-menu").fadeIn("fast");
  $(".ew-main-menu-app").fadeIn("fast");

});

$("body").on("click", '#ew-alert-salemain li a[href="#"]', function() {
  $(".ew-main-menu").fadeOut();
  $(".ew-sub-menu").fadeIn();
});

$("body").on("click", '#ew-alert-management li a[href="#"]', function() {
  $(".ew-main-menu-app").fadeOut();
  $(".ew-sub-menu-app").fadeIn();
});

/* Function Zone */

setInterval(function() {
  GenerateData("count-menu");
}, 20000);

var myImg = "images/icon/andriod/ic_launcher.png";
var jobs = 0;
function GenerateData(module) {
  $.ajax({
    url: "index.php?r=ajax/" + module,
    type: "POST",
    data: { param: { menu: "saleorder" } },
    async: true,
    dataType: "json",
    //contentType: "application/json; charset=utf-8",
    success: function(getData) {
      //var obj = jQuery.parseJSON(getData);
      $(".ew-data-saleorder").html(getData.saleorder);
      $(".ew-data-approve").html(getData.approve);

      $(".warning-amount").html(getData.alert);
      $(".warning-task").html(getData.task);
      $(".warning-message").html(getData.message);

      if (getData.newjob > 0) {
        if (jobs > 0) {
          if (jobs != getData.newjob) {
            $("#easyNotify").easyNotify({
              title: "New",
              options: {
                body: getData.newjob + " Jobs",
                icon: myImg,
                lang: "en-US",
                onClick: myFunction
              }
            });
            // Auto Refresh

            location.reload();
          }
        }
        jobs = getData.newjob;
      }
    },
    error: function(request, error) {
      //alert(error+' [Menu]');
    }
  });
  //route('index.php?r=ajax/'+ module,'GET',{param:{menu:'saleorder'}},'ew-data-saleorder');
}

var myFunction = function() {
  window.location.href = "index.php?r=SaleOrders%2Fsaleorder";
};

function divActions() {
  if ($("#ew-alert-salemain").attr("class") != "active") {
    $(".ew-main-menu").fadeOut("slow");
    $(".ew-sub-menu").fadeIn("slow");
  } else {
    $(".ew-main-menu").fadeIn("slow");
    $(".ew-sub-menu").fadeOut("slow");
  }
}

function divActionsApp() {
  if ($("#ew-alert-management").attr("class") != "active") {
    $(".ew-main-menu-app").fadeOut("slow");
    $(".ew-sub-menu-app").fadeIn("slow");
  } else {
    $(".ew-main-menu-app").fadeIn("slow");
    $(".ew-sub-menu-app").fadeOut("slow");
  }
}

function divActionsRevert() {
  if ($("#ew-alert-salemain").attr("class") === "active") {
    $(".ew-main-menu").fadeOut("fast");
    $(".ew-sub-menu").fadeIn("slow");
  } else {
    //alert($('#ew-alert-salemain').attr('class'));
    $(".ew-main-menu").fadeIn("slow");
    $(".ew-sub-menu").fadeOut("slow");
  }
}

$("body").on("click", "div.ew-data-approve", function() {
  var $url = $(this)
    .closest("li")
    .find("a")
    .attr("href");
  if ($url != null) {
    window.location.href = $url;
  }
});

$(document).click(function(e) {
  var menuRightOpen = $(e.target)
    .closest("div")
    .attr("class");
  var menuRight = $(e.target)
    .closest("aside")
    .attr("class");

  if (
    menuRight == "control-sidebar control-sidebar-dark control-sidebar-open" ||
    menuRightOpen == "navbar-custom-menu" ||
    menuRightOpen == "control-sidebar-bg"
  ) {
    //$('aside').collapse('hide');
  } else {
    $("aside.control-sidebar").removeClass("control-sidebar-open");
  }
});

// ##### Left-Menu #####
