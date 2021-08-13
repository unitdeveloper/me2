app.controller('requisitionWorksheet', ['$scope', '$http', '$log',
function($scope,$http,$attrs) {
    
    var items = [];
    $http.get('index.php?r=Purchase/req/ajax-total&id='+$('form#form-purchase-order').data('key'))
    .then(function (response) {
        
        $scope.total              = response.data.total;
        $scope.discount           = response.data.discount;
        $scope.percentDiscount    = response.data.percentDiscount;
        $scope.vat_percent        = response.data.vat;
        $scope.vat_type           = response.data.vat_type;
        $scope.include_vat        = response.data.include_vat;
        

        $scope.subtotal           = response.data.subtotal;
        $scope.grandTotal         = response.data.grandtotal;
        $scope.withholdingTax     = response.data.withholdTax;
        //$scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.witholdingValue    = ($scope.subtotal * $scope.withholdingTax) / 100;
        $scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.beforvat           = response.data.beforeVat;
        $scope.aftervat           = response.data.vat;

        //console.log($scope.grandTotalPayment);
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
    

    $scope.PurchaseLine = '';
    $http.get('index.php?r=Purchase/req/angular-get&id='+$('form#form-purchase-order').data('key'))
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
                item:angular.element($event.currentTarget).attr('data-key'),
                desc:angular.element($event.currentTarget).attr('data-desc'),
                qty:1,
                price:angular.element($event.currentTarget).attr('data-cost'),
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchasereqheader-doc_no').val(),
                type:'item',
            };

        $http({
            url:'index.php?r=Purchase/req/json-create-item-line',
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
                    $http.post('index.php?r=Purchase/req/ajax-delete-pur-line',data).then(function(data){
                       // console.log('Remove');
                    })
                }
            });
            $scope.PurchaseLine = newDataList;
            $scope.getTotalSummary();
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
   
        
        var vat             = ($scope.vat_type == 2) ? 0 : $scope.vat_percent;       
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
        $scope.aftervat           = $InCVat;
        $scope.grandTotal         = $total;
        $scope.witholdingValue    = ($scope.subtotal * $scope.withholdingTax) / 100;
        $scope.grandTotalPayment  = $scope.grandTotal - (($scope.subtotal * $scope.withholdingTax)/100);

        $scope.withholdingTax     = $scope.withholdingTax;

        
        
        if($scope.vat_percent >= 7){
            $('.show-vat-type').show('fast');
        }else{
            $('.show-vat-type').hide('fast');
            $scope.include_vat  = '1';
        }
        // if($scope.vat_type > 0)
        // {
        //     //$('#purchasereqheader-vat_type').fadeIn('in');
        // }else {
        //     //$('#purchasereqheader-vat_type').fadeOut();
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
        $('.searchItem').slideDown('slow');
        
      });

    }else {
      $scope.searchdata = [];
    }



  });


    $(function() {
        $('input[name=\"PurchaseReqHeader[withholdTaxSwitch]\"]').on('switchChange.bootstrapSwitch', function(event, state) {
            //console.log(this); // DOM element
            //console.log(event); // jQuery event
            //console.log(state); // true | false
            if(state){
            //console.log('on');
            $('.tax-toggle').slideDown();
            $scope.withholdingTax     = '0';
            $scope.getTotalSummary();
            }else {
            //console.log('off');
            $('.tax-toggle').slideUp();
            $scope.withholdingTax     = '0';
            $scope.getTotalSummary();
            
            }
        });
    });
 

    $scope.findItem = function ($event) {
        //console.log(angular.element($event.currentTarget).val());
        //console.log($scope.searchProduct);
        // ถ้าไม่มี ให้ขึ้นสินค้า ...(ข้อความ) และบันทึกรหัสนั้นไว้ในบรรทัดการซื้อ (ไม่ต้องสร้างสินค้า)
        // ถ้ามี 
        if(items[0].data.length <= 0){
             
            var param ={
                item:'1^x',
                desc:lang('common','Text'),
                qty:1,
                price:0,
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchasereqheader-doc_no').val(),
                type:'item',
                code:'1414',
                item_no:$scope.searchProduct,
            };
        
            //console.log(param);
            

            $http({
                url:'index.php?r=Purchase/req/json-create-item-line',
                method:"POST",
                data:param,
            })
            .then(function (response) {
                $scope.PurchaseLine.push({
                    'id'        : response.data.id,
                    'item'      : response.data.item,
                    'item_no'   : $scope.searchProduct,
                    'description': response.data.desc,
                    'quantity'  : response.data.qty,
                    'unitcost'  : response.data.price,
                    'measure'   : response.data.measure,
                    'unitofmeasure' : response.data.unitofmeasure,     
                    'qty_per_unit' :  response.data.qty_per_unit
                });
                $scope.getTotalSummary();
            });
        }else if(items[0].data.length == 1){
            var thisItem = items[0].data;
            var param ={
                item:thisItem[0].id,
                desc:thisItem[0].desc_th,
                qty:1,
                price:thisItem[0].price,
                id:$('form#form-purchase-order').attr('data-key'),
                no:$('input#purchasereqheader-doc_no').val(),
                type:'item',
                code:thisItem[0].id,
                item_no:$scope.searchProduct,
            };
 
            $http({
                url:'index.php?r=Purchase/req/json-create-item-line',
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
            });
        }
    };

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

 