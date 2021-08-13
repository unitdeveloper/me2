app.controller('pointOfSale', ['$scope', '$http', '$compile','$sce', '$filter',
function ($scope, $http, $compile,$sce,$filter) {

  var OrderID = $('form#form-event-order').data('key');


  $scope.renderLine = function(){
    $http.get('index.php?r=SaleOrders/event/load-sale-line&id='+$('form#form-event-order').data('key'))
              .then(function (response) {

                $scope.saleEventline    = response.data;

                var total     = 0;
                var discount  = 0;
                var subtotal  = 0;
                for(var i = 0; i < $scope.saleEventline.length; i++){
                    var getSum = $scope.saleEventline[i];
                    total     += (getSum.quantity * getSum.price);
                    subtotal  += (getSum.quantity * (getSum.price - getSum.discount));
                    discount  += (getSum.quantity * getSum.discount);

                }
                $scope.total          = total;
                $scope.sumdiscount    = discount;
                $scope.subtotal       = subtotal;
                $scope.discount       = 0;
                $scope.percdiscount   = 0;

              });
  }
  $scope.renderLine();



  // Scan Barcode
  $scope.qtyperunit = 1;
  $scope.searchProduct = function($event,sce){

    if($scope.search==''){
      $scope.finance();
      return false;
    }
    if($scope.search!=''){

      if($scope.qtyperunit<=0){ $scope.qtyperunit = 1; }
      if($scope.discount==null){ $scope.discount = 0; }
      if($scope.percdiscount==null){ $scope.percdiscount = 0; }

      var param = {
        //no        : angular.element($event.currentTarget).attr('data-key'),
        code      : $scope.search,
        orderno   : $('form#form-event-order').data('key'),
        qtyperunit: $scope.qtyperunit,
        discount  : $scope.discount,
        percdis   : $scope.percdiscount,
        
      };
      //console.log($event);

      // clear text      
      $scope.qtyperunit     = 1;



      $http.post('index.php?r=SaleOrders/event/find-barcode',param)
                .then(function (response) {

                  

                  if(response.data.current.validate==false){

                    // Barcode Not Found.
                    //-> Save new barcode
                    /*  
                      $('#findItems').modal('show');
                      $scope.modal();
                    */
                   
                   // Open product tab for chose it.
                   $scope.search         = '';
                   $('.nav-tabs a[href="#product"]').tab('show');



                  }else {
                    // clear text
                    $scope.search         = '';
                    $scope.description      = $sce.trustAsHtml(response.data.current.desc_th);
                  }



                  // Sum Total
                  $scope.saleEventline    = response.data.newData;
                  var total     = 0;
                  var discount  = 0;
                  var subtotal  = 0;
                  for(var i = 0; i < $scope.saleEventline.length; i++){
                      var getSum = $scope.saleEventline[i];
                      total     += (getSum.quantity * getSum.price);
                      subtotal  += (getSum.quantity * (getSum.price - getSum.discount));
                      discount  += (getSum.quantity * getSum.discount);

                  }
                  $scope.total          = total;
                  $scope.sumdiscount    = discount;
                  $scope.subtotal       = subtotal;
                  $scope.discount       = 0;
                  $scope.percdiscount   = 0;

                });






    }

  };

  // Pick from item list
  $scope.pickProduct = function($event,sce){

    if($scope.qtyperunit<=0){ $scope.qtyperunit = 1; }
    if($scope.discount==null){ $scope.discount = 0; }
    if($scope.percdiscount==null){ $scope.percdiscount = 0; }

      var param = {
        no        : angular.element($event.currentTarget).attr('data-key'),
        code      : angular.element($event.currentTarget).attr('data-code'),
        orderno   : $('form#form-event-order').data('key'),
        qtyperunit: $scope.qtyperunit,
        discount  : $scope.discount,
        percdis   : $scope.percdiscount,
        
      };
    
      // clear text
      $scope.search         = '';
      $scope.qtyperunit     = 1;

      $http.post('index.php?r=SaleOrders/event/find-barcode',param)
      .then(function (response) {

        

        if(response.data.current.validate==false){

          // Barcode Not Found.
          // $('#findItems').modal('show');
          // $scope.modal();

          // Open product tab for chose it.
          $scope.search         = '';
          $('.nav-tabs a[href="#product"]').tab('show');
          

        }else {
          $scope.description      = $sce.trustAsHtml(response.data.current.desc_th);
          $.notify({
            // options
            icon: 'fas fa-shopping-basket',
            message: 'เพิ่มสินค้าในตะกร้าแล้ว' 
          },{
            // settings
            type: 'warning',
            delay: 1000,
            z_index:3000,
          });
        }



        // Sum Total
        $scope.saleEventline    = response.data.newData;
        var total     = 0;
        var discount  = 0;
        var subtotal  = 0;
        for(var i = 0; i < $scope.saleEventline.length; i++){
            var getSum = $scope.saleEventline[i];
            total     += (getSum.quantity * getSum.price);
            subtotal  += (getSum.quantity * (getSum.price - getSum.discount));
            discount  += (getSum.quantity * getSum.discount);

        }
        $scope.total          = total;
        $scope.sumdiscount    = discount;
        $scope.subtotal       = subtotal;
        $scope.discount       = 0;
        $scope.percdiscount   = 0;

      });

  };




  // $scope.qtyperunit = 1;
  // $scope.$watch("searchProduct",function(newValue,oldValue){
  //
  //     var param = {
  //       code:newValue,
  //       orderno:$('form#form-event-order').data('key'),
  //       qtyperunit:$scope.qtyperunit,
  //       discount:$scope.discount,
  //     };
  //     $http.post('index.php?r=SaleOrders/event/find-barcode',param)
  //     .then(function (response) {
  //       $scope.saleline       = response.data;
  //       $scope.description    = response.data.desc_th;
  //       $scope.renderLine();
  //
  //       // clear text
  //       $scope.searchProduct  = '';
  //       $scope.qtyperunit     = 1;
  //       $scope.discount       = 0;
  //     });
  //     //console.log(newValue);
  //
  //
  // });
  $scope.$watch("percdiscount",function(newValue,oldValue){
    if(newValue > 0){
      $scope.discount     = '';
      $('input[ng-model="discount"]').attr('style','background-color:#ccc !important;');
      $('input[ng-model="percdiscount"]').attr('style','background-color:#00a65a !important;');
    }else {
      $('input[ng-model="discount"]').attr('style','background-color:#3c8dbc !important;');
    }

  });
  $scope.$watch("discount",function(newValue,oldValue){
    if(newValue > 0){
      $scope.percdiscount = '';
      $('input[ng-model="percdiscount"]').attr('style','background-color:#ccc !important;');
      $('input[ng-model="discount"]').attr('style','background-color:#3c8dbc !important;');
    }else {
      $('input[ng-model="percdiscount"]').attr('style','background-color:#00a65a !important;');
    }
  });




  $scope.menuHover  = function($event,compile){

    var dataKey = angular.element($event.currentTarget).attr('data-key');
    $scope.lineno = dataKey;

    $('div.menu-list').remove();
    $scope.text = 'DELETE';
    var html = '<div class="menu-list" ng-click="deleline($event)" data="{{lineno}}" data-rippleria><i class="fa fa-trash-o" aria-hidden="true"></i> {{text}}</div>';

    var compileHtml = $compile(html)($scope);

    angular.element($event.currentTarget).append(compileHtml);


  }

  $scope.menuSlide  = function($event,compile){
    var $this = angular.element($event.currentTarget);
    //var key = angular.element($event.currentTarget).attr('data-key');
    var key = $this.parent('tr').parent('td').attr('data-key');
    $scope.itemno = $this.parent('tr').parent('td').attr('data-no');
    $scope.lineno = key;
    console.log(key);
    $('.actions').remove();
    var template = '<div class="actions" id="actions'+key+'" data-key="'+ key +'" data-no="{{itemno}}">'+                        
                        '<a href="javascript:void(0);" class="more"   ng-click="moreBtn($event)"><i class="fas fa-ellipsis-h"></i>  <p> More</p>    </a>'+
                        '<a href="javascript:void(0);" class="delete" ng-click="deleline($event)" data="{{lineno}}"><i class="fa fa-trash-o"></i><p>   Delete</p>      </a>'+
                        '<a href="javascript:void(0);" class="cancel" ng-click="closeBtn($event)"><i class="fa fa-power-off"></i><p> Close</p>       </a>'+                        
                    '</div>';
    var compileHtml = $compile(template)($scope);
    $this.parent('td').prepend(compileHtml);
    //$this.html('<i class="fas fa-bars"></i>');                                 
    $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);
    $('#actions'+key+' a').rippleria();
  }
  $scope.menuDbSlide  = function($event,compile){
    var $this     = angular.element($event.currentTarget);
    var key       = $this.parent('tr').attr('data-key');
    $scope.itemno = $this.parent('tr').attr('data-no');
    $scope.lineno = key;
    $('.actions').remove();
    var template = '<div class="actions" id="actions'+key+'" data-key="'+ key +'" data-no="{{itemno}}">'+                        
                        '<a href="javascript:void(0);" class="more"   ng-click="moreBtn($event)"><i class="fas fa-ellipsis-h"></i>  <p> More</p>    </a>'+
                        '<a href="javascript:void(0);" class="delete" ng-click="deleline($event)" data="{{lineno}}"><i class="fa fa-trash-o"></i><p>   Delete</p>      </a>'+
                        '<a href="javascript:void(0);" class="cancel" ng-click="closeBtn($event)"><i class="fa fa-power-off"></i><p> Close</p>       </a>'+                        
                    '</div>';
    var compileHtml = $compile(template)($scope);
    $this.parent('tr').children('td.actions-render').prepend(compileHtml);                       
    $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);
    $('#actions'+key+' a').rippleria();
  }
  
  $scope.moreBtn = function($event){
    var itemno = angular.element($event.currentTarget).parent('div').attr('data-no');
    if(itemno==undefined){ itemno = angular.element($event.currentTarget).parent('li').attr('data-no');}
        setTimeout(function(){
            //window.location.href = 'index.php?r=items%2Fitems%2Fview&id='+itemno;
            window.open('index.php?r=items%2Fitems%2Fview&id='+itemno,'_blank');
        }, 450); 
  }

  $scope.closeBtn = function($event){
    var key    = angular.element($event.currentTarget).parent('div').attr('data-key');
        setTimeout(function(){
            $('#actions'+key+'').toggle("slide", { direction: "right" }, 500);
            
        }, 450); 
        
  }

  $scope.deleline = function($event){
    var dataKey = angular.element($event.currentTarget).attr('data');
    setTimeout(function(e){    
      if (confirm('Are you sure you want to delete this?')) {
        $http.post('index.php?r=SaleOrders/event/delete-line',{lineno:dataKey})
        .then(function (response) {
          console.log('Delete');
          $scope.renderLine();
        });
      }else {
        $('.menu-list').fadeOut('fast');
      }
    },500);
  }






  $scope.modal = function(sec){

     
    //When barcode not found.
    $scope.modalHeader  = 'เลือกรายการสินค้าที่ต้องการบันทึก Barcode นี้ : ' + $scope.search;
    $scope.findItem     = [];
    
    $http.get('index.php?r=items/ajax/find-items-json-limit&word=')
      .then(function (response) {
        $scope.findItem       = response.data;
        //$scope.itemSearch   = $scope.search;
      });

    $scope.bodyModal    = $sce.trustAsHtml('<div class="text-danger">Top 10</div>');
  }

  $scope.closeModal = function(){
    $('#findItems').modal('hide');
    $('input[ng-model=\"search\"]').focus().val('');
  }

  $scope.$watch("itemSearch",function(newValue,oldValue){

      $http.get('index.php?r=items/ajax/find-items-json-limit&word='+newValue)
      .then(function (response) {
        $scope.findItem       = response.data;

      });



  });

  $scope.productSearch = function($event,compile){
    var $this = angular.element($event.currentTarget);
    var search = $('#item-search').val();
    $http.get('index.php?r=SaleOrders%2Fevent%2Ffilter-product&search='+search)
      .then(function (response) {     

        var compileHtml = $compile(response.data.value.html)($scope);
        $('.render-item-list').html(compileHtml);
      });
  }

  $scope.changeQty = function(obj,$event){
    var id      = obj.model.id;
    var qty     = obj.model.quantity;

    if(qty<=0){
      // ถ้าต้ำกว่า 0 ให้จำนวนเป็น 1
      qty = 1;
      obj.model.quantity = 1;
    }
    
    $http.post('index.php?r=SaleOrders/event/update-qty&id='+id,{qty:qty})
      .then(function (response) {
        
        if(response.data.status===200){
          // Sum Total
                  
          var total     = 0;
          var discount  = 0;
          var subtotal  = 0;
          for(var i = 0; i < $scope.saleEventline.length; i++){
              var getSum = $scope.saleEventline[i];
              total     += (getSum.quantity * getSum.price);
              subtotal  += (getSum.quantity * (getSum.price - getSum.discount));
              discount  += (getSum.quantity * getSum.discount);

          }
          $scope.total          = total;
          $scope.sumdiscount    = discount;
          $scope.subtotal       = subtotal;
          $scope.discount       = 0;
          $scope.percdiscount   = 0;
        }else {
          console.log(response.data.message);
          $scope.total          = 0;
        }
       
        
      });

     


};



  $scope.updateItem = function($event){
    var param = {
      'id'      : angular.element($event.currentTarget).attr('data-key'),
    };
    $http.post('index.php?r=items/ajax/items-update',param)
              .then(function (response) {
                if(response.data == true){
                  alert('บันทึก บาร์โค๊ด แล้ว \r\nโปรดทำการ สแกนบาร์โค้ด ใหม่อีกครั้ง');
                  $('#findItems').modal('hide');
                }else {
                  alert(response.data.barcode);
                }
              });
  }
  $scope.defineCode = function($event){
    var itemNo = angular.element($event.currentTarget).attr('data-key');
    $http.get('index.php?r=items/ajax/find-items-info&No='+itemNo)
    .then(function (response) {
      $scope.findItem       = response.data;
      $scope.modalHeader  = 'ยืนยันการบันทึก Barcode โดย คลิกที่ ปุ่ม (ยืนย้น,Confirm)';
    });
  }



  $scope.finance = function(sec){
    $('#financeModal').modal('show');
    $scope.fnHeader = $sce.trustAsHtml('<i class="fa fa-credit-card" aria-hidden="true"></i> รับชำระเงิน');
    $scope.rcMoney = 0;
    $('input[ng-model="rcMoney"]').focus();


  }
  $scope.closeModalFn = function(){
    $('#financeModal').modal('hide');
  }

  $scope.receiveMoney = function($event){

    if($event.keyCode == 112){
      $scope.rcMoney = $scope.subtotal;
    }
    if($event.keyCode == 13){

      // # ถ้าช่องรับเงินว่าง
      // # ให้ focus ที่ช่องรับเงิน
      // # ให้ ช่องรับเงินว่าง
      if($scope.rcMoney == 0){
        $('input[ng-model="rcMoney"]').focus();
        $scope.rcMoney = $scope.subtotal;
      }else {
        if (confirm('Confirm ?')) {

            if($scope.rcMoney - $scope.subtotal >= 0){
              $('#financeModal').modal('hide');
              var param = {
                status:'closed',
                amount:$scope.subtotal,
                pay:$scope.rcMoney,
                rcchange:$scope.rcMoney - $scope.subtotal,
              };
              $http.post('index.php?r=SaleOrders/event/update&id='+OrderID,param)
              .then(function(response){

                if(response.data == true){
                  window.print();
                  if (confirm('บันทึกเรียบร้อย \r\nต้องการสร้างใบงานใหมต่อหรือไม่ ?')) {
                    window.location.href = "index.php?r=SaleOrders/event/create";
                  }else{
                    window.location.href = "index.php?r=SaleOrders/event/index";
                  }
                }else{
                  console.log(response.data);
                }


              })

            }else {
              // ถ้าเงินทอน น้อยกว่า 0
              alert('ยอดชำระไม่ถูกต้อง');
            }
        }

      }

    }
    if($event.keyCode == 32){
      $scope.rcMoney = '';
    }
  }


  $scope.resetFilter = function() {
    $scope.search = null;
  };

  $scope.confirmPayment = function($event){
     
    if($scope.rcMoney<=0){
      
      $.notify({
        // options
        icon: 'fas fa-money-bill-alt',
        message: 'กรุณาใส่จำนวนเงินที่ลุกค้าชำระ' 
      },{
        // settings
        type: 'danger',
        delay: 3000,
        z_index:3000,
      });

      $('input[ng-model="rcMoney"]').focus();
       
      $scope.rcMoney = '';
      //return false;
    }else{
      
        if (confirm('Confirm ?')) {

          if($scope.rcMoney - $scope.subtotal >= 0){
            $('#financeModal').modal('hide');
            setTimeout(function(){
                var param = {
                  status:'closed',
                  amount:$scope.subtotal,
                  pay:$scope.rcMoney,
                  rcchange:$scope.rcMoney - $scope.subtotal,
                };
                $http.post('index.php?r=SaleOrders/event/update&id='+OrderID,param)
                .then(function(response){

                  if(response.data == true){
                    window.print();
                    if (confirm('บันทึกเรียบร้อย \r\nต้องการสร้างใบงานใหมต่อหรือไม่ ?')) {
                      window.location.href = "index.php?r=SaleOrders/event/create";
                    }else{
                      window.location.href = "index.php?r=SaleOrders/event/index";
                    }
                  }else{
                    console.log(response.data);
                  }
                })
            }, 450);

          }else {
            // ถ้าเงินทอน น้อยกว่า 0
            alert('ยอดชำระไม่ถูกต้อง');
          }      
      }else{
        $('input[ng-model="rcMoney"]').select();
      }

    }
  }

  $scope.banknote = function(el){
    $scope.rcMoney = el + $scope.rcMoney;
  }

  $scope.Rightmenu = function($event,compile){
    var $this     = angular.element($event.currentTarget);
    var key       = $this.attr('data-key');
    
    $scope.itemno = $this.attr('data-no');
    $scope.lineno = key;
    var y         = $event.y - 70;
    var x         = $event.x - 30;

    
    $('.contextMenu').remove();
    var template = '<div id="contextMenu'+key+'" data-key="'+ key +'"  class="contextMenu" style="position: absolute;z-index: 500; top:'+y+'px; left:'+x+'px; " >'+
                    '<div  class="dropdown clearfix ">'+
                      '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px; box-shadow: 5px 5px 5px rgba(0,0,0,.2);">'+
                        '<li data-no="{{itemno}}">'+
                          '<a href="javascript:void(0);" class="more"   ng-click="moreBtn($event)"><i class="fas fa-ellipsis-h"></i> Edit Item</a>'+
                        '</li>'+                  
                        '<li class="divider"></li>'+                  
                        '<li>'+
                          '<a href="javascript:void(0);" class="delete" ng-click="deleline($event)" data="{{lineno}}"><i class="fa fa-trash-o"></i> Delete</a>'+
                        '</li>'+                         
                      '</ul>'+
                    '</div>'+
                  '</div>';
    var compileHtml = $compile(template)($scope);
    $this.parent('td').prepend(compileHtml);                       
  }
  
}]);

app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.replace(/[^0-9|.]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$setViewValue(transformedInput);
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('escKey', function () {
  return function (scope, element, attrs) {
    element.bind('keydown keypress', function (event) {
      if(event.which === 27) { // 27 = esc key
        scope.$apply(function (){
          scope.$eval(attrs.escKey);
        });

        event.preventDefault();
      }
    });
  };
})

app.directive('ngRightClick', function($parse) {
  return function(scope, element, attrs) {
      var fn = $parse(attrs.ngRightClick);
      element.bind('contextmenu', function(event) {
          scope.$apply(function() {
              event.preventDefault();
              fn(scope, {$event:event});
          });
      });
  };
});

app.controller('indexController',['$scope', '$http', '$compile','$sce', '$filter',
function ($scope, $http, $compile,$sce,$filter) {

  $scope.countDocument  = chartData.jobs;
  $scope.sumTotal       = chartData.total;

  $scope.items          = chartData.list;

  $scope.detailAmount  =  function ($event) {
    window.location.href = "index.php?r=SaleOrders/event/sale-line";
  }

  $scope.brandText     = $('.mode.Active').attr('data-key');

  $scope.formDate        = new Date(new Date().getFullYear(), 0, 1).getTime();              // 2017-01-01;
  $scope.toDate          = new Date(new Date().getFullYear(), 11, 31,23,59,59).getTime();   // 2017-31-12

   

}])
 