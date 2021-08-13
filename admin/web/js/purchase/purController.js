app.controller('purchaseOrder', ['$scope', '$filter', '$http', '$log',
function($scope,$filter,$http,$attrs) {


    var items = [];
    $scope.finddata = 0;
    
    $http.get('index.php?r=Purchase/order/ajax-total&id='+$('form#form-purchase-order').data('key'))
    .then(function (response) {
        
        $scope.total              = response.data.total;
        $scope.discount           = response.data.discount;
        $scope.percentDiscount    = response.data.percentDiscount;
        $scope.vat_percent        = response.data.vat;
        $scope.vat_type           = response.data.vat_type;
        $scope.include_vat        = response.data.include_vat;
        $scope.transport          = response.data.transport;

        $scope.subtotal           = response.data.subtotal;
        $scope.grandTotal         = response.data.grandtotal;
        $scope.withholdingTax     = response.data.withholdTax;
        //$scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.witholdingValue    = ($scope.subtotal * $scope.withholdingTax) / 100;
        $scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.beforvat           = response.data.beforeVat;
        $scope.aftervat           = response.data.vatValue;
        //$scope.payment_term       = response.data.terms;

        //console.log(response);
        if(response.data.vat >= 7) // Vat
        {
            $('.show-vat-type').show('fast');    
        }else {
            $('.show-vat-type').hide('fast');
        }

        
    });


 

    // $http.get('?r=measure%2Fmeasure%2Fget-measure')
    // $scope.unitofmeasure = function(){
    //     $http.get('?r=items%2Fajax%2Fitem-of-measure&id='+$('input[name="item"]').attr('data-id'))
    //     .then(function (response) {    
    //         $scope.unitofmeasure    = response.data.list;
    //         console.log($scope.unitofmeasure);          
    //     });
    // }

    $scope.filterItems  = {};
    $scope.testFilter = function (source) {   
        return $scope.filterItems[source.po];
    };  

 
    
    $scope.Sources      = '';

    $scope.getSourceRequest = function(){
        
        $http.get('index.php?r=Purchase/req/source-request')
                .then(function (response) {
                $scope.Sources   = response.data;     
        });                   
        
    }

    $scope.toggleObjSelection = function($event, id) {
        $event.stopPropagation();               
    }
    
    $scope.rowClicked = function(source) {                 
        //source.selected = !source.selected;                
        $scope.filterItems[source.id] = !$scope.filterItems[source.id];               
    };

    $scope.checkAllSource = function () {              
        if ($scope.checkSource) {
            $scope.checkSource = true;
        } else {
            $scope.checkSource = false;
        }
        angular.forEach($scope.Sources, function(source) {
            //source.selected = $scope.checkSource;
            $scope.filterItems[source.id] = $scope.checkSource; 
        });
    };
           
             
    $scope.SourceList = '';
    
    $scope.selectPurchaseLine = function() {
        
        if($scope.SourceList){
            // ถ้ามีข้อมูลแล้วไม่ต้องเรียกซ้ำ
        }else{

       
            var reqlist = [
                {id:2}
            ];

            //รายละเอียดในใบ PR 
            $http.post('index.php?r=Purchase/req/source-request-list',{key:reqlist})
                    .then(function (response) {
                        $scope.SourceList       = response.data; 
                        
                        $scope.SourceList      = $filter('orderBy')($scope.SourceList, 'po', false) 

                        $scope.checkList      = true;
                        $scope.checkAllList();
                    });
        }
    }    
    
    $scope.rowClickedList = function(source) {                 
        source.selected = !source.selected;                                              
    };
                    
    $scope.checkAllList = function () {              
        if ($scope.checkList) {
            $scope.checkList = true;
        } else {
            $scope.checkList = false;
        }
        
        angular.forEach($scope.SourceList, function(source) {
            
            source.selected = $scope.checkList;

        });
    };

    $scope.calTermOfPayment = function() {
        //alert('test');
    }
                   
    $scope.openModalConfirmReq = function(){
        var keys = Object.keys($scope.filterItems);                        
        if(keys.length >= 1){
            $('#confirm-req').modal('show');
        }else{
            
            $.notify({
                // options
                icon: 'far fa-check-square',
                message: 'กรุณา เลือกรายการ' 
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
    
        }
        
    }

    $scope.confirmRequisition = function(SourceList){
        var keyId = [];
        angular.forEach(SourceList, function(source) {   
            
            // ส่งเฉพาะ ID ที่อยู่ใน PO
            // ค้นหาว่ามี po id อะไรบ้าง
            angular.forEach($scope.filterItems, function(selected,poId) {
                
                if(selected){                                     
                    if(source.po==poId){
                        if(source.selected){
                            keyId.push(source.id); 
                        }
                    }
                }
            });

            $scope.prId = source.po;
        });
        
        //if(confirm('Are you sure?')){                      
        
            $http.post('index.php?r=Purchase/order/get-source&id=' + $('form#form-purchase-order').data('key') + '&pr=' + $scope.prId,{id:keyId}) 
                .then(function (response) {
                   

                    if(response.data.status==200){
                        $http.get('index.php?r=Purchase/purchase-line/angular-get&id='+$('form#form-purchase-order').data('key'))
                        .then(function (res) {
                            $scope.PurchaseLine   = res.data;
                        });

                        $scope.getTotalSummary();

                        $('#confirm-req').modal('hide');
                        $('#ew-modal-source').modal('hide');
                    }
                    
                });
        //};
    }





    $scope.PurchaseLine = '';
    $http.get('index.php?r=Purchase/purchase-line/purchase-line&id='+$('form#form-purchase-order').data('key'))
            .then(function (response) {
              $scope.PurchaseLine   = response.data;
            });

            

    $scope.getTotal = function(){
        var total = 0;
        for(var i = 0; i < $scope.PurchaseLine.length; i++){
            var getSum = $scope.PurchaseLine[i];
            total += (getSum.quantity * getSum.unitcost);            
        }        
        return total;
    }

    $scope.addNew = function($event,PurchaseLine){
        var param ={
                item:angular.element($event.currentTarget).attr('data-no'),
                itemId:angular.element($event.currentTarget).attr('data-id'),
                desc:angular.element($event.currentTarget).attr('data-desc'),
                qty:1,
                price:angular.element($event.currentTarget).attr('data-cost'),
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchaseheader-doc_no').val(),
                type:'item',
                detail: '',
                size: ''
            };
             
        $http({
            url:'index.php?r=Purchase/order/json-create-item-line',
            method:"POST",
            data:param,
        })
        .then(function (response) {
            $scope.PurchaseLine.push({
                'id'        : response.data.id,
                'item'      : response.data.item,
                'item_no'   : response.data.item_no,
                'description': response.data.desc,
                'quantity'  : response.data.qty,
                'unitcost'  : response.data.price,
                'measure'   : response.data.measure,
                'unitofmeasure' : response.data.unitofmeasure,     
                'qty_per_unit' :  response.data.qty_per_unit,            
                //'unitcost': angular.element($event.currentTarget).attr('data-cost'),
            });
            
            $scope.getTotalSummary();
            setTimeout(() => {
                $('body').find('input[name="description"]:last').select().focus();
            }, 500);
            
        });
    };

    $scope.remove = function(){
        if (confirm('Are you sure you want to delete this?')) {
            var newDataList=[];
            $scope.selectedAll = false;
            angular.forEach($scope.PurchaseLine, function(selected){
                if(!selected.selected){
                    newDataList.push(selected);
                }
                // Delete
                if(selected.selected){
                    var data = {data:selected.id,pur:$('form#form-purchase-order').attr('data-key')};
                    $http.post('index.php?r=Purchase/order/ajax-delete-pur-line',data)
                    .then(function(response){
                         
                        if(response.data.status==200){
                            $scope.PurchaseLine = newDataList;
                            $scope.getTotalSummary();
                        }else{
                            $.notify({
                                // options
                                icon: 'fas fa-exclamation-triangle',
                                message: response.data.message
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
                        }
                    })
                }
            });
            
        }
    };

    $scope.checkAll = function () {
        if ($scope.selectedAll) {
            $scope.selectedAll = true;
        } else {
            $scope.selectedAll = false;
        }
        angular.forEach($scope.PurchaseLine, function(model) {
            model.selected = $scope.selectedAll;
        });
    };


    


    $scope.calMeasure = function(){
        //console.log(obj.model);
       var qty_per_unit = 1;
        if(this.model!=null){
            var thisMeasure = this.model.measure;        
            angular.forEach(this.model.unitofmeasure, function(measure){
                
                if(measure.id ===thisMeasure){
                    
                    qty_per_unit = measure.qty_per;
                }
            });
        }        

        this.model.qty_per_unit = qty_per_unit;
        
        $scope.getTotalSummary();
        
    }

 
    $scope.getTotalSummary = function(){
   
        if($scope.vat_percent >= 7){
            $('.show-vat-type').show('fast');
        }else{
            $('.show-vat-type').hide('fast');
            $scope.include_vat  = '1';
        }

        
        var vat             = ($scope.vat_percent == 0) ? 1 : $scope.vat_percent;       
        var BeforeDisc      = $scope.getTotal();
        $scope.total        = BeforeDisc;
        //console.log(BeforeDisc);
        //if($getTotal==null) BeforeDisc = $('#ew-purline-total').attr('data');
        var $Discount     = (BeforeDisc * $scope.percentDiscount)/ 100;
            
        // หักส่วนลด (ก่อน vat)
        var $subtotal     = BeforeDisc - $Discount;
        if($scope.include_vat == 1){
            // Vat นอก
            var $InCVat       = ($subtotal * vat )/ 100;
            var $beforeVat    = 0;
            var $total        = ($InCVat + $subtotal);
        }else {
            // Vat ใน
            // 1.07 = 7%
            //var $vat_revert   = ($scope.vat_type/100) + 1;
            var $InCVat       = $subtotal - ($subtotal / 1.07);
            var $beforeVat    = $subtotal - $InCVat;
            var $total        = $subtotal;
        }

        $scope.discount           = (BeforeDisc * $scope.percentDiscount)/ 100;
        $scope.subtotal           = $subtotal;       
        $scope.beforvat           = $beforeVat;
        $scope.aftervat           = $scope.vat_percent > 0 ? $InCVat : 0;
        $scope.grandTotal         = $scope.vat_percent > 0 ? (parseInt($total) + parseInt($scope.transport)) : $scope.total;
        $scope.witholdingValue    = ($scope.subtotal * $scope.withholdingTax) / 100;
        $scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.withholdingTax     = $scope.withholdingTax;

     
        //console.log($scope.total);
        
        // if($scope.vat_type > 0)
        // {
        //     //$('#purchaseheader-vat_type').fadeIn('in');
        // }else {
        //     //$('#purchaseheader-vat_type').fadeOut();
        //     $scope.include_vat  = '1';
        // }


    }

  $scope.$watch("searchProduct",function(newValue,oldValue){

    if(newValue)
    if(newValue.length > 2){

      $http({
        method:'GET',
        url:'index.php?r=items/ajax/find-items-my-store-json&limit=20&word='+newValue,
        data:{word:newValue},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function (response) {

        items = []; //CLEAR         
        items.push(response);


        $scope.searchdata = response.data;

        if(response.data.length > 1){
            $('.searchItem').slideDown('slow');
        }
        
      });

    }else {
      $scope.searchdata = [];
    }



  });


  $(function() {
    $('input[name=\"PurchaseHeader[withholdTaxSwitch]\"]').on('switchChange.bootstrapSwitch', function(event, state) {
      //console.log(this); // DOM element
      //console.log(event); // jQuery event
      //console.log(state); // true | false
      if(state){
        console.log('on');
        $('.tax-toggle').slideDown();
        $scope.withholdingTax     = '0';
        $scope.getTotalSummary();
      }else {
        console.log('off');
        $('.tax-toggle').slideUp();
        $scope.withholdingTax     = '0';
        $scope.getTotalSummary();
        
      }
    });
  });


  $scope.findItem = function ($event) {
    //console.log(angular.element($event.currentTarget).val());
    
    if($scope.searchProduct !== undefined){
        // ถ้าไม่มี ให้ขึ้นสินค้า ...(ข้อความ) และบันทึกรหัสนั้นไว้ในบรรทัดการซื้อ (ไม่ต้องสร้างสินค้า)
        // ถ้ามี 
        if(items[0] && items[0].data.length <= 0){
            
            var param ={
                item:'1^x',
                desc:lang('common','Text'),
                qty:1,
                price:0,
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchaseheader-doc_no').val(),
                type:'item',
                code:'1414',
                item_no:$scope.searchProduct,
                size: '',
                detail: ''
            };
        
            
            
            if($scope.finddata==0){  // ถ้าไม่มีการค้นหา, เริ่มค้นหาได้

                
                $scope.finddata = 1; // กำหนดสถาณะ -> กำลังค้นหา
                $http({
                    url:'index.php?r=Purchase/order/json-create-item-line',
                    method:"POST",
                    data:param,
                })
                .then(function (response) {

                    
                    $scope.PurchaseLine.push({
                        'id'        : response.data.id,
                        'item'      : response.data.item,
                        'item_no'   : $scope.searchProduct,
                        'description': response.data.desc + ' ' + response.data.detail + ' ' + response.data.size,
                        'quantity'  : response.data.qty,
                        'unitcost'  : response.data.price,
                        'measure'   : response.data.measure,
                        'unitofmeasure' : response.data.unitofmeasure,     
                        'qty_per_unit' :  response.data.qty_per_unit
                    });
                    
                    $scope.getTotalSummary();
                    setTimeout(() => {
                        $('body').find('input[name="description"]:last').select().focus();
                        $scope.finddata = 0; // ค้นหาเสร็จแล้ว
                    }, 500);
                });
            }
        }else if(items[0] && items[0].data.length == 1){
            var thisItem = items[0].data;
            var param ={
                item:thisItem[0].id,
                desc:thisItem[0].desc_th,
                qty:1,
                price:thisItem[0].price,
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchaseheader-doc_no').val(),
                type:'item',
                code:thisItem[0].id,
                item_no:$scope.searchProduct,
                size: thisItem[0].size ? thisItem[0].size : ' ',
                detail: thisItem[0].detail ? thisItem[0].detail : ' '
            };
            
            $http({
                url:'index.php?r=Purchase/order/json-create-item-line',
                method:"POST",
                data:param,
            })
            .then(function (response) {
                $scope.PurchaseLine.push({
                    'id'        : response.data.id,
                    'item'      : response.data.item,
                    'item_no'   : response.data.item_no,
                    'description': response.data.desc,
                    'quantity'  : response.data.qty,
                    'unitcost'  : response.data.price,
                    'measure'   : response.data.measure,
                    'unitofmeasure' : response.data.unitofmeasure,     
                    'qty_per_unit' :  response.data.qty_per_unit
                });
                
                $scope.getTotalSummary();
                setTimeout(() => {
                    $('body').find('input[name="description"]:last').select().focus();
                }, 500);
                
            });
        }
    }
};


$scope.nextField = function(event, id, nextIdx, last) {
   
    if(event.keyCode == 13){
        if(last===true){
            angular.element(document.querySelector('#searchProduct'))[0].select();
        }else{
            angular.element(document.querySelector('#'+id+nextIdx))[0].select();
        }
    }
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

app.run(function($rootScope) {
    $rootScope.typeOf = function(value) {
        return typeof value;
    };
})
app.directive('stringToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(value) {
          return '' + value;
        });
        ngModel.$formatters.push(function(value) {
          return parseFloat(value);
        });
      }
    };
  });

 