app.controller('itemController', ['$scope', '$http', '$compile','$sce', '$filter',
function ($scope, $http, $compile,$sce,$filter) {

    
    $http.get('?r=measure%2Fmeasure%2Fget-measure')
    .then(function (response) {    
        $scope.unitofmeasure    = response.data.list;             
    });

    $http.get('?r=items%2Fajax%2Fitem-of-measure&id='+$('#items-form').attr('data-id'))
    .then(function (response) {
        $scope.mesurelist       = response.data.list;
         
    });
    
    $scope.addMeasure = function(){       
        var param = {             
            measure : 2,
            qty     : 1,
            item    : $('#items-form').attr('data-id'),
            session : $('#items-form').attr('data-session') 
          };
        if($scope.mesurelist.length==null){ // ถ้ายังไม่มีหน่วย ให้สร้างตัวแปร Array มารอรับ (ถ้่าไม่มี จะไม่สามารถ push ได้)
            $scope.mesurelist = [];
            param = {             
                measure : 1,
                qty     : 1,
                item    : $('#items-form').attr('data-id'),  
                session : $('#items-form').attr('data-session') 
              };
        }
         
        $http.post('?r=items%2Fajax%2Fcreate-item-of-measure&key='+$('input[name="eWin-backend"]').val(),param)
        .then(function (response) {                
            
            $scope.mesurelist.push({'id':response.data.id,'qty':response.data.qty,'measure':response.data.measure});     
        }); 
    }

    $scope.removeRow = function(index,list){
        if(confirm('Confirm ?')){
            $http.post('?r=items%2Fajax%2Fdelete-item-of-measure&id='+list.id,{id:list.id})
            .then(function (response) {    
                if(response.data.status===200){
                    $scope.mesurelist.splice( index, 1);
                    if ($scope.mesurelist.length === 0){
                        $scope.mesurelist = [];
                    }
                }else{
                    alert(response.data.message);
                }
            }); 
        }
    }
 

    $scope.changeMeasure = function(measure,list){        
        var param = {
            line    : list.id,
            measure : measure,
            item    : list.item,       
          };
         
        $http.post('?r=items%2Fajax%2Fupdate-item-of-measure&id='+list.id,param)
        .then(function (response) {    
            //$scope.mesurelist       = response.data.list; 
            console.log(response);
        }); 
    }

    $scope.changeMeasureQty = function(obj){     
        
       
        var param = {
            line    : obj.list.id,
            qty     : obj.list.qty,
            item    : obj.list.item,       
        };         
          
        $http.post('?r=items%2Fajax%2Fupdate-item-of-measure&id='+obj.list.id,param)
        .then(function (response) {    
            if(response.data.status==200){
                obj.list.qty = response.data.value.qty;
            }else{
                $.notify({
                    // options
                    icon: 'far fa-times-circle',
                    message: response.data.message 
                },{
                    // settings
                    type: 'danger',
                    delay: 2000,
                    z_index:3000,
                });
                
                $scope.mesurelist       = [];
                $scope.mesurelist       = response.data.value.list;

            }
        }); 
        
    }

    $scope.setDefault = function(obj){

        $('.btn-delete-measure').html('<i class="fa fa-sync fa-spin text-success"></i>');
        var param = {
            line    : obj.list.id,     
        };         
        
        $http.post('?r=items%2Fajax%2Fset-default-of-measure&id='+obj.list.id,param)
        .then(function (response) {    
            if(response.data.status==200){
                $scope.mesurelist       = [];
                $scope.mesurelist       = response.data.value.list;
                $('.btn-delete-measure').html('<i class="far fa-times-circle  text-red "></i>');
            } 
        }); 
    }

    //$scope.$watch("description_th",function(newValue,oldValue){       
        $scope.changeDiscriptionth = function(obj){
            
            if(obj.description_th !=''){
    
                $http.get('?r=items%2Fajax%2Ffind-items-for-clone&word='+obj.description_th)
                .then(function (response) {     
    
                    if(response.data[0].count > 0){
                        $('.product-name-popup').slideDown('fast');
                        $scope.itemList         = response.data;  
    
                        var desc_   = newValue,
                            res     = response.data[0];
    
                        if(res.desc_en.toLowerCase() == desc_.toLowerCase()){
                            $scope.description_th   = res.desc_th;
                            
                            //$('#master_code').val(res.item);    // Disabled 19/05/2020
                            $('#items-standardcost').val(res.cost);
                            $('#items-costgp').val(res.price);
                        }else{
                            //$scope.description_th   = null;
                            //$('#master_code').val($('#master_code').attr('data-org'));      // Disabled 22/07/2020 
                        }
                    }else{
                        $('.product-name-popup').slideUp('fast'); 
    
                        //$scope.description_th   = null;
                        //$('#master_code').val($('#master_code').attr('data-org'));       // Disabled 19/05/2020
                    }
                    
                });
            }else{
                $('.product-name-popup').slideUp('fast');    
            }
    
        };
  
    //$scope.$watch("description",function(newValue,oldValue){       
    $scope.changeDiscription = function(obj){
        
        if(obj.description !=''){

            $http.get('?r=items%2Fajax%2Ffind-items-for-clone&word='+obj.description)
            .then(function (response) {     

                if(response.data[0].count > 0){
                    $('.product-name-popup').slideDown('fast');
                    $scope.itemList         = response.data;  

                    var desc_   = newValue,
                        res     = response.data[0];

                    if(res.desc_en.toLowerCase() == desc_.toLowerCase()){
                        $scope.description_th   = res.desc_th;
                        
                        //$('#master_code').val(res.item);   // Disabled 22/07/2020
                        $('#items-standardcost').val(res.cost);
                        $('#items-costgp').val(res.price);
                    }else{
                        //$scope.description_th   = null;
                        //$('#master_code').val($('#master_code').attr('data-org'));  // Disabled 22/07/2020 
                    }
                }else{
                    $('.product-name-popup').slideUp('fast'); 

                    //$scope.description_th   = null;
                    //$('#master_code').val($('#master_code').attr('data-org'));      // Disabled 22/07/2020 
                }
                
            });
        }else{
            $('.product-name-popup').slideUp('fast');    
        }

    };

    $scope.choseItem = function(obj){
        $scope.description      = obj.model.desc_en;
        $scope.description_th   = obj.model.desc_th;
        $scope.barcode          = obj.model.barcode;
        $('.product-name-popup').slideUp('fast');  
    }

    $scope.cloneItem = function(obj){
        if(confirm('ต้องการดึงรายการมาใช้หรือไม่ ?')){        
            $http.post('?r=items%2Fitems%2Fclone-item-json&id='+obj.model.id)
            .then(function (response) {                
                if(response.data.status==200){
                    // Clone success                 
                    window.location.href = 'index.php?r=items/items/view&id='+response.data.value.id;                    
                }else if(response.data.status===201){
                    // Already exists
                    $.notify({
                        // options
                        icon: 'fab fa-slack',
                        message: response.data.message 
                    },{
                        // settings
                        type: 'info',
                        delay: 2000,
                        z_index:3000,
                    });
                }else{
                    $.notify({
                        // options
                        icon: 'far fa-times-circle',
                        message: response.data.message 
                    },{
                        // settings
                        type: 'danger',
                        delay: 2000,
                        z_index:3000,
                    });
                }
            }); 
        }
    }


}]);



$('body').on('click','button.use-barcode',function(){
    
    if($('#master_code').val()===''){
        $('#master_code').select();
    }else{
        $('#items-description_th').select();
    }
    
});